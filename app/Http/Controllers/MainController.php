<?php

namespace App\Http\Controllers;

set_time_limit(60);

use App\Condition;
use App\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MainController extends Controller
{
	public $sites = [];
	public $sensors = [];
	public $notifications = [];

    public function huy()
    {
        dd($sites);
        return view('huy');
	}

	/**
	 * Our initial index function that gets hit on initial page load
	 */
	public function index()
	{
		$conditions = Condition::all(); //

		$this->initData('minute', 750);
		
		$devices = collect($this->sensors)->keyBy('id');
        $conditions = Condition::all()->keyBy('site_id');

		return view('index', ['sites' => $this->sites, 'devices' => $devices, 'conditions' => $conditions, 'notifications' => $this->processNotification($devices, $conditions)]);
	}

	/**
	 *	Initialize data arrays via the Swagger API
	 *  Takes two parameters: what data collection period to use and what data length to keep (null to keep all values)
	 */
	public function initData($period, $data_length) {
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
		$fidelity = $period;
		foreach($this->sensors as $device) {
			$data = $this->refreshRawSensorData($device->getID(), $fidelity);
			switch($device->getType()) {
				case 'gas':
					if ($data_length === null) {
						$timestamps = collect($data['gas_values'])->pluck(0)->toArray();
						$readings = collect($data['gas_values'])->pluck(1)->toArray();
					} else {
						$timestamps = array_slice(collect($data['gas_values'])->pluck(0)->toArray(), count($data['gas_values']) - $data_length);
						$readings = array_slice(collect($data['gas_values'])->pluck(1)->toArray(), count($data['gas_values']) - $data_length);
					}
					$device->setTimestamps($timestamps);
					$device->setReadings($readings);
					$device->setScale($data['gas_scale']);
					$device->setFidelity($fidelity);
					$device->setNotify();
					$device->processData();
                    break;
				case 'solar':
					if ($data_length === null) {
						$timestamps = collect($data['solar_value'])->pluck(0)->toArray();
						$readings = collect($data['solar_value'])->pluck(1)->toArray();
					} else {
						$timestamps = array_slice(collect($data['solar_value'])->pluck(0)->toArray(), count($data['solar_value']) - $data_length);
						$readings = array_slice(collect($data['solar_value'])->pluck(1)->toArray(), count($data['solar_value']) - $data_length);
					}
					$device->setTimestamps($timestamps);
					$device->setReadings($readings);
					$device->setScale($data['solar_scale']);
					$device->setFidelity($fidelity);
					$device->setNotify();
					$device->processData();
					break;
				case 'hydrometer':
					if ($data_length === null) {
						$timestamps = collect($data['moisture_value'])->pluck(0)->toArray();
						$readings = collect($data['moisture_value'])->pluck(1)->toArray();
					} else {
						$timestamps = array_slice(collect($data['moisture_value'])->pluck(0)->toArray(), count($data['moisture_value']) - $data_length);
						$readings = array_slice(collect($data['moisture_value'])->pluck(1)->toArray(), count($data['moisture_value']) - $data_length);
					}
					$device->setTimestamps($timestamps);
					$device->setReadings($readings);
					$device->setScale($data['moisture_scale']);
					$device->setFidelity($fidelity);
					$device->setNotify();
					$device->processData();
					break;
				case 'tempHumid':
					if ($data_length === null) {
						$timestamps = collect($data['temperature_value'])->pluck(0)->toArray();
						$readings = collect($data['temperature_value'])->pluck(1)->toArray();
						$secondtimestamps = collect($data['humidity_value'])->pluck(0)->toArray();
						$secondreadings = collect($data['humidity_value'])->pluck(1)->toArray();
					} else {
						$timestamps = array_slice(collect($data['temperature_value'])->pluck(0)->toArray(), count($data['temperature_value']) - $data_length);
						$readings = array_slice(collect($data['temperature_value'])->pluck(1)->toArray(), count($data['temperature_value']) - $data_length);
						$secondtimestamps = array_slice(collect($data['humidity_value'])->pluck(0)->toArray(), count($data['humidity_value']) - $data_length);
						$secondreadings = array_slice(collect($data['humidity_value'])->pluck(1)->toArray(), count($data['humidity_value']) - $data_length);
					}
					$device->setTimestamps($timestamps);
					$device->setReadings($readings);
					$device->setSecondaryTimestamps($secondtimestamps);
					$device->setSecondaryReadings($secondreadings);
					$device->setScale($data['temp_scale']);
					$device->setSecondaryScale($data['humidity_scale']);
					$device->setFidelity($fidelity);
					$device->setNotify();
					$device->processData();
					break;
				case 'lumosity':
					if ($data_length === null) {
						$timestamps = collect($data['light_value'])->pluck(0)->toArray();
						$readings = collect($data['light_value'])->pluck(1)->toArray();
					} else {
						$timestamps = array_slice(collect($data['light_value'])->pluck(0)->toArray(), count($data['light_value']) - $data_length);
						$readings = array_slice(collect($data['light_value'])->pluck(1)->toArray(), count($data['light_value']) - $data_length);
					}
					$device->setTimestamps($timestamps);
					$device->setReadings($readings);
					$device->setScale($data['light_scale']);
					$device->setFidelity($fidelity);
					$device->setNotify();
					$device->processData();
					break;
			}
		}
	}

	/**
	 *	Generate notification messages to pass to view. only used on dashboard view.
	 */
	public function processNotification($devices, $conditions) // I.e. Look at the last 12 hours of data if any values fall out of the *conditions* then store a notification
    {
        $notifications = [];
        $condition_site_id = null;
        $winter_months = [12, 1, 2];
        $current_month = Carbon::now()->format('m');

        if (in_array($current_month, $winter_months)) {
            $winter = true;
        } else {
            $winter = false;
        }

        // Implement frontend for customising DB values for conditions
        foreach ($devices as $device) {
            if ($device->id == 'outside_field_temp') { // Root Crop
                $condition_site_id = 'field';
            } else if ($device->id == 'outside_heap_temp') { // Heap
                $condition_site_id = 'heap';
            } else if ($device->site_id == "house") { // Store Room
                $condition_site_id = 'shed';
            } else if (isset($conditions[$device->site_id])) {
                $condition_site_id = $device->site_id;
            }

            if ($device->type == 'tempHumid') {
                foreach ($device->readings as $reading) {
                    if ($condition_site_id) {
                        if ($conditions[$condition_site_id]->winter_low_temp && $conditions[$condition_site_id]->winter_high_temp && $reading && $winter) { // Check to see if all conditions are set
                            if ($reading < $conditions[$condition_site_id]->low_temp || $reading > $conditions[$condition_site_id]->high_temp) { // Check reading against condition
                                $notifications[$condition_site_id]['temp'] = "[$condition_site_id] Reading is {$reading}°C and the optimal winter range is {$conditions[$condition_site_id]->winter_low_temp}°C - {$conditions[$condition_site_id]->winter_high_temp}°C";
                                break;
                            }
                        } else if ($conditions[$condition_site_id]->low_temp && $conditions[$condition_site_id]->high_temp && $reading) { // Check to see if all conditions are set
                            if ($reading < $conditions[$condition_site_id]->low_temp || $reading > $conditions[$condition_site_id]->high_temp) { // Check reading against condition
                                $notifications[$condition_site_id]['temp'] = "[$condition_site_id] Reading is {$reading}°C and the optimal range is {$conditions[$condition_site_id]->low_temp}°C - {$conditions[$condition_site_id]->high_temp}°C";
                                break;
                            }
                        }
                    }
                }

                foreach ($device->secondaryReadings as $secondaryReading) {
                    if ($conditions[$condition_site_id]->low_humidity && $conditions[$condition_site_id]->high_humidity && $secondaryReading) { // Check to see if all conditions are set
                        if ($secondaryReading < $conditions[$condition_site_id]->low_humidity || $secondaryReading > $conditions[$condition_site_id]->high_humidity) { // Check reading against condition
                            $notifications[$condition_site_id]['humidity'] = "[$condition_site_id] Reading is {$secondaryReading}% and the optimal range is {$conditions[$condition_site_id]->low_humidity}% - {$conditions[$condition_site_id]->high_humidity}%";
                            break;
                        }
                    }
                }
            } else if ($device->type == 'lumosity') {
                foreach ($device->readings as $reading) {
                    if ($condition_site_id) {
                        if ($conditions[$condition_site_id]->low_lux && $conditions[$condition_site_id]->high_lux && $reading) { // Check to see if all conditions are set
                            if ($reading < $conditions[$condition_site_id]->low_lux || $reading > $conditions[$condition_site_id]->high_lux) { // Check reading against condition
                                $notifications[$condition_site_id]['lux'] = "[$condition_site_id] Reading is ".round($reading)."lux and the optimal range is {$conditions[$condition_site_id]->low_lux}lux - {$conditions[$condition_site_id]->high_lux}lux";
                                break;
                            }
                        }
                    }
                }
            } else if ($device->type == 'hydrometer') {
                foreach ($device->readings as $reading) {
                    if ($condition_site_id) {
                        if ($conditions[$condition_site_id]->low_moisture && $conditions[$condition_site_id]->high_moisture && $reading) { // Check to see if all conditions are set
                            if ($reading < $conditions[$condition_site_id]->low_moisture || $reading > $conditions[$condition_site_id]->high_moisture) { // Check reading against condition
                                $notifications[$condition_site_id]['moisture'] = "[$condition_site_id] Reading is ".round($reading)."%vwc and the optimal range is {$conditions[$condition_site_id]->low_moisture}%vwc - {$conditions[$condition_site_id]->high_moisture}%vwc";
                                break;
                            }
                        }
                    }
                }
            } else if ($device->type == 'gas') {
                foreach ($device->readings as $reading) {
                    if ($condition_site_id) {
                        if ($conditions[$condition_site_id]->low_gas && $conditions[$condition_site_id]->high_gas && $reading) { // Check to see if all conditions are set
                            if ($reading > $conditions[$condition_site_id]->low_gas && $reading < $conditions[$condition_site_id]->high_gas) { // Check reading against condition
                                $notifications[$condition_site_id]['gas'] = "[$condition_site_id] Warning! Carbon monoxide level is {$reading}ppm and the danger range is {$conditions[$condition_site_id]->low_gas}ppm - {$conditions[$condition_site_id]->high_gas}ppm";
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $notifications;
	}


	public function getData($path)
	{
		return json_decode(file_get_contents("http://shed.kent.ac.uk/$path"), true);
	}

	public function refreshRawSensorData($sensorID, $rate) {
		return $this->getData("device/$sensorID/$rate");
	}
}
