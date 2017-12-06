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
                $device = new Device($data['name'], $data['id'], $key, $data['last_connection']);
                array_push($this->sites[$data['site_id']]['zones'][$data['zone_id']]['devices'], $device);
                array_push($this->sensors, $device);
            }
        }
        // TODO: Go into sensor, pick an id, change it, find it in sites and see if its changed

        // Initialize data arrays
        $fidelity = '10minute';
        foreach($this->sensors as $device) {
            $data = $this->refreshRawSensorData($device->getID(), $fidelity);
            switch($device->getType()) {
                case 'gas':
                    $device->setTimestamps(collect($data['gas_values'])->pluck(0));
                    $device->setReadings(collect($data['gas_values'])->pluck(1));
                    $device->setScale($data['gas_scale']);
                    $device->setFidelity($fidelity);
                    break;
                case 'solar':
                    $device->setTimestamps(collect($data['solar_value'])->pluck(0));
                    $device->setReadings(collect($data['solar_value'])->pluck(1));
                    $device->setScale($data['solar_scale']);
                    $device->setFidelity($fidelity);
                    break;
                case 'hydrometer':
                    $device->setTimestamps(collect($data['moisture_value'])->pluck(0));
                    $device->setReadings(collect($data['moisture_value'])->pluck(1));
                    $device->setScale($data['moisture_scale']);
                    $device->setFidelity($fidelity);
                    break;
                case 'tempHumid':
                    $device->setTimestamps(collect($data['temperature_value'])->pluck(0));
                    $device->setReadings(collect($data['temperature_value'])->pluck(1));

                    $device->setSecondaryTimestamps(collect($data['humidity_value'])->pluck(0));
                    $device->setSecondaryReadings(collect($data['humidity_value'])->pluck(1));

                    $device->setScale($data['temp_scale']);
                    $device->setSecondaryScale($data['humidity_scale']);
                    $device->setFidelity($fidelity);
                    break;
                case 'lumosity':
                    $device->setTimestamps(collect($data['light_value'])->pluck(0));
                    $device->setReadings(collect($data['light_value'])->pluck(1));
                    $device->setScale($data['light_scale']);
                    $device->setFidelity($fidelity);
                    break;
            }
        }
        $devices = collect($this->sensors)->keyBy('id');

        return view('index', ['sites' => $this->sites, 'devices' => $devices]);
    }

    public function getData($path)
    {
        return json_decode(file_get_contents("http://shed.kent.ac.uk/$path"), true);
    }

    public function refreshRawSensorData($sensorID, $rate) {
    	return $this->getData("device/$sensorID/$rate");
    }
}
