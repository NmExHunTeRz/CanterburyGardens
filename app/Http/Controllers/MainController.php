<?php

namespace App\Http\Controllers;

set_time_limit(60);

use App\Condition;
use App\Device;
use Illuminate\Http\Request;

class MainController extends Controller
{
	public $sites;
	public $sensors;
	public $notifications;

	/**
	 * Our initial index function that gets hit on initial page load
	 */
	public function index()
	{
		$conditions = Condition::all(); //

		$this->sites = [];
		$this->sensors = [];
		$this->notifications = [];

		// Initialize sites and relevant metadata
		$data_sites = $this->getData('sites');
		foreach ($data_sites as $data_site) {
			$this->sites[$data_site['id']] = $data_site;
			if ($data_site['id'] == 'house')
				$this->sites[$data_site['id']]['icon'] = "img/farm.png";
			else if ($data_site['id'] == 'outside')
				$this->sites[$data_site['id']]['icon'] = "img/plant.png";
			else
				$this->sites[$data_site['id']]['icon'] = "img/greenhouse.png";
		}
		// Set zones array to associative array
		foreach ($this->sites as $key => $site) {
			foreach ($site['zones'] as $index => $zone) {
				$this->sites[$key]['zones'][$zone['id']] = ['name' => $zone['name'], 'devices' => []];
				unset($this->sites[$key]['zones'][$index]);
			}
		}

		// Initialize all sensor devices
		$types = $this->getData('devices');
		foreach ($types as $key => $type) {
			foreach ($type as $sensor) {
				$data = $this->getData("device/$sensor");
				$device = new Device($data['name'], $data['id'], $key, $data['last_connection'], $data['site_id']);
				array_push($this->sites[$data['site_id']]['zones'][$data['zone_id']]['devices'], $device);
				array_push($this->sensors, $device);
			}
		}

		// Initialize data arrays
		$fidelity = 'minute';
		foreach($this->sensors as $device) {
			$data = $this->refreshRawSensorData($device->getID(), $fidelity);
			switch($device->getType()) {
				case 'gas':
					$timestamps = array_slice(collect($data['gas_values'])->pluck(0)->toArray(), count($data['gas_values']) - 400);
					$readings = array_slice(collect($data['gas_values'])->pluck(1)->toArray(), count($data['gas_values']) - 400);
					$device->setTimestamps($timestamps);
					$device->setReadings($readings);
					$device->setScale($data['gas_scale']);
					$device->setFidelity($fidelity);
					$device->setNotify();
					$device->processData();
                    break;
				case 'solar':
					$timestamps = array_slice(collect($data['solar_value'])->pluck(0)->toArray(), count($data['solar_value']) - 400);
					$readings = array_slice(collect($data['solar_value'])->pluck(1)->toArray(), count($data['solar_value']) - 400);
					$device->setTimestamps($timestamps);
					$device->setReadings($readings);
					$device->setScale($data['solar_scale']);
					$device->setFidelity($fidelity);
					$device->setNotify();
					$device->processData();
					break;
				case 'hydrometer':
					$timestamps = array_slice(collect($data['moisture_value'])->pluck(0)->toArray(), count($data['moisture_value']) - 400);
					$readings = array_slice(collect($data['moisture_value'])->pluck(1)->toArray(), count($data['moisture_value']) - 400);
					$device->setTimestamps($timestamps);
					$device->setReadings($readings);
					$device->setScale($data['moisture_scale']);
					$device->setFidelity($fidelity);
					$device->setNotify();
					$device->processData();
					break;
				case 'tempHumid':
					$timestamps = array_slice(collect($data['temperature_value'])->pluck(0)->toArray(), count($data['temperature_value']) - 400);
					$readings = array_slice(collect($data['temperature_value'])->pluck(1)->toArray(), count($data['temperature_value']) - 400);
					$device->setTimestamps($timestamps);
					$device->setReadings($readings);

					$timestamps = array_slice(collect($data['humidity_value'])->pluck(0)->toArray(), count($data['humidity_value']) - 400);
					$readings = array_slice(collect($data['humidity_value'])->pluck(1)->toArray(), count($data['humidity_value']) - 400);
					$device->setSecondaryTimestamps($timestamps);
					$device->setSecondaryReadings($readings);

					$device->setScale($data['temp_scale']);
					$device->setSecondaryScale($data['humidity_scale']);
					$device->setFidelity($fidelity);
					$device->setNotify();
					$device->processData();
					break;
				case 'lumosity':
					$timestamps = array_slice(collect($data['light_value'])->pluck(0)->toArray(), count($data['light_value']) - 1000);
					$readings = array_slice(collect($data['light_value'])->pluck(1)->toArray(), count($data['light_value']) - 1000);
					$device->setTimestamps($timestamps);
					$device->setReadings($readings);
					$device->setScale($data['light_scale']);
					$device->setFidelity($fidelity);
					$device->setNotify();
					$device->processData();
					break;
			}
		}
		
		$devices = collect($this->sensors)->keyBy('id');
        $conditions = Condition::all()->keyBy('site_id');
		$this->processNotification($devices, $conditions);

		return view('index', ['sites' => $this->sites, 'devices' => $devices, 'conditions' => $conditions]);
	}

	public function getData($path)
	{
		return json_decode(file_get_contents("http://shed.kent.ac.uk/$path"), true);
	}

	public function refreshRawSensorData($sensorID, $rate) {
		return $this->getData("device/$sensorID/$rate");
	}

    public function processNotification($devices, $conditions) // I.e. Look at the last 12 hours of data if any values fall out of the *conditions* then store a notification
    {
        $notifications = [];


        // Implement frontend for customising DB values for conditions
        foreach ($devices as $device) {
            switch ($device->site_id) {
                case 'gh1':
                    if ($device->type == 'tempHumid') {
                        foreach ($device->readings as $reading) {
                            if ($reading < $conditions['gh1']->low_temp || $reading > $conditions['gh1']->high_temp) {
                                dump($reading);
                                dump("GH1 is out of optimal conditions");

                                break;
                            }
                        }
                    }
                    //- Between 7°C and 29°C and in Winter between 8°C and 10°C


                break;
            }
        }
        dd($devices);
	}
}
