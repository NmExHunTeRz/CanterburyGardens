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
	public $notifications_last = [];

	public function huy()
	{
        //dd($sites);

        return view('huy', []);
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

        $this->processNotification($devices, $conditions);
        
        $rainfall24 = $this->rainData();
        $weatherData = $this->weatherData();


		return view('index', ['sites' => $this->sites, 'devices' => $devices, 'conditions' => $conditions, 'notifications' => $this->notifications, 'notifications_last' => $this->notifications_last, 'rainfall' => $rainfall24, 'weather' => $weatherData]);
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
		// dd($types);
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
		$tmp_notifications = [];
		$tmp_notifications_last = [];
		$condition_site_id = null;
		$winter_months = [12, 1, 2];
		$current_month = Carbon::now()->format('m');

		if (in_array($current_month, $winter_months)) {
			$winter = true;
		} else {
			$winter = false;
		}

		// dd($conditions);

		// Implement frontend for customising DB values for conditions
		foreach ($devices as $device) {
			if ($device->id == 'outside_field_temp') { // Root Crop
				$condition_site_id = 'field';
			} else if ($device->id == 'outside_heap_temp') { // Heap
				$condition_site_id = 'muck_heap';
			} else if ($device->site_id == "house") { // Store Room
				$condition_site_id = 'shed';
			} else if (isset($conditions[$device->site_id])) {
				$condition_site_id = $device->site_id;
			}

			$total = count($device->readings);
			$count = 0;
			$last_notification = null;

			if ($device->type == 'tempHumid') {
				// Temperature
				foreach ($device->readings as $reading) {
					if ($condition_site_id) {
						if ($winter) { // Set the optimal range depending on winter temperatures
							$low = $conditions[$condition_site_id]->winter_low_temp;
							$high = $conditions[$condition_site_id]->winter_high_temp;
						} else {
							$low = $conditions[$condition_site_id]->low_temp;
							$high = $conditions[$condition_site_id]->high_temp;
						}
						if ($reading && $low && $high) { //Check that nothing is null
							if ($reading < $low) {
								$count++;
								$last_notification = "$condition_site_id:$device->id|Currently too cold: {$reading}. Optimal: {$low}-{$high}°C";
							} else if ($reading > $high) {
								$count++;
								$last_notification = "$condition_site_id:$device->id|Currently too hot: {$reading}. Optimal: {$low}-{$high}°C";
							}
							$percentage = round($count/$total*100);
							if ($percentage > 0) $tmp_notifications[$condition_site_id]['temp'] = "$condition_site_id - $device->id|Temperature outside optimal range ({$low}-{$high}°C) for {$percentage}% of readings";
							if ($last_notification) $tmp_notifications_last[$condition_site_id]['temp'] = $last_notification;
						}
					}
				}
				$count = 0;
				//Humidity
				foreach ($device->secondaryReadings as $secondaryReading) {
					if ($condition_site_id) {
						$low = $conditions[$condition_site_id]->low_humidity;
						$high = $conditions[$condition_site_id]->high_humidity;
						if ($reading && $low && $high) { //Check that nothing is null
							if ($reading < $low) {
								$count++;
								$last_notification = "$condition_site_id:$device->id|Currently humidity too low: {$reading}%. Optimal: {$low}-{$high}%";
							} else if ($reading > $high) {
								$count++;
								$last_notification = "$condition_site_id:$device->id|Currently humidity too high: {$reading}%. Optimal: {$low}-{$high}%";
							}
							$percentage = round($count/$total*100);
							if ($percentage > 0) $tmp_notifications[$condition_site_id]['humidity'] = "$condition_site_id - $device->id|Humidity outside optimal range ({$low}-{$high}%) for {$percentage}% of readings";
							if ($last_notification) $tmp_notifications_last[$condition_site_id]['humidity'] = $last_notification;
						}
					}
				}

			} else if ($device->type == 'lumosity') {
				// Light 
				foreach ($device->readings as $reading) {
					if ($condition_site_id) {
						$low = $conditions[$condition_site_id]->low_lux;
						$high = $conditions[$condition_site_id]->high_lux;
						if ($reading && $low && $high) { //Check that nothing is null
							if ($reading < $low) {
								$count++;
								$last_notification = "$condition_site_id:$device->id|Currently too dark: {$reading} lux. Optimal: {$low}-{$high} lux";
							} else if ($reading > $high) {
								$count++;
								$last_notification = "[$condition_site_id:$device->id|Currently too bright: {$reading} lux. Optimal: {$low}-{$high} lux";
							}
							$percentage = round($count/$total*100);
							if ($percentage > 0) $tmp_notifications[$condition_site_id]['lux'] = "$condition_site_id - $device->id|Brightness outside optimal range ({$low}-{$high} lux) for {$percentage}% of readings";
							if ($last_notification) $tmp_notifications_last[$condition_site_id]['lux'] = $last_notification;
						}
					}
				}

			} else if ($device->type == 'hydrometer') {
				// Moisture
				foreach ($device->readings as $reading) {
					if ($condition_site_id) {
						$low = $conditions[$condition_site_id]->low_moisture;
						$high = $conditions[$condition_site_id]->high_moisture;
						if ($reading && $low && $high) { //Check that nothing is null
							if ($reading < $low) {
								$count++;
								$last_notification = "$condition_site_id:$device->id|Currently soil too dry: {$reading}%. Optimal: {$low}-{$high}%";
							} else if ($reading > $high) {
								$count++;
								$last_notification = "$condition_site_id:$device->id|Currently soil too wet: {$reading}%. Optimal: {$low}-{$high}%";
							}
							$percentage = round($count/$total*100);
							if ($percentage > 0) $tmp_notifications[$condition_site_id]['moisture'] = "$condition_site_id - $device->id|Soil moisture outside optimal range ({$low}-{$high}%) for {$percentage}% of readings";
							if ($last_notification) $tmp_notifications_last[$condition_site_id]['moisture'] = $last_notification;
						}
					}
				}

			} else if ($device->type == 'gas') {
				// CO Sensor
				foreach ($device->readings as $reading) {
					if ($condition_site_id) {
						$low = $conditions[$condition_site_id]->low_gas;
						$high = $conditions[$condition_site_id]->high_gas;
						if ($reading && $low && $high) { //Check that nothing is null
							if ($reading > $high) {
								$count++;
								$last_notification = "$condition_site_id:$device->id|DANGER Carbon Monoxide level too high: {$reading}ppm. Optimal: {$low}-{$high}ppm";
							}
							$percentage = round($count/$total*100);
							if ($percentage > 0) $tmp_notifications[$condition_site_id]['gas'] = "$condition_site_id - $device->id|DANGER Carbon Monoxide level dangerous for {$percentage}% of readings";
							if ($last_notification) $tmp_notifications_last[$condition_site_id]['gas'] = $last_notification;
						}
					}
				}
			}
		}

		$this->notifications = $tmp_notifications;
		$this->notifications_last = $tmp_notifications_last;
	}

    /**
     * Gets the rainfall from the past 24 hours, totals it and formats it to inches
     */
    public function rainData()
    {
        $data_rain = $rainData = $this -> getRainData();

        $temp = 0;
        
        foreach ($data_rain['items'] as $time) {
            $temp += $time['value'];
        }

        return round(($temp/25.4), 3);
    }

    public function weatherData()
    {
        $fullData = $this->getWeatherData();
        $daydata = $fullData['SiteRep']['DV']['Location']['Period'][0]['Rep'];

        switch ($daydata[0]["W"]) {
            case '1':
                $returnArray['tomorrowWeather'] = "Sunny Day";
                break;
            case '3':
                $returnArray['tomorrowWeather'] = "Partly Cloudy";
                break;
            case '5':
                $returnArray['tomorrowWeather'] = "Misty";
                break;
            case '6':
                $returnArray['tomorrowWeather'] = "Foggy";
                break;
            case '7':
                $returnArray['tomorrowWeather'] = "Cloudy";
                break;
            case '8':
                $returnArray['tomorrowWeather'] = "Overcast";
                break;
            case '10':
                $returnArray['tomorrowWeather'] = "Light Rain Showers";
                break;
            case '11':
                $returnArray['tomorrowWeather'] = "Drizzle";
                break;
            case '12':
                $returnArray['tomorrowWeather'] = "Light Rain";
                break;
            case '14':
                $returnArray['tomorrowWeather'] = "Heavy Rain Showers";
                break;
            case '15':
                $returnArray['tomorrowWeather'] = "Heavy Rain";
                break;
            case '17':
                $returnArray['tomorrowWeather'] = "Sleet Shower";
                break;
            case '18':
                $returnArray['tomorrowWeather'] = "Sleet";
                break;
            case '20':
                $returnArray['tomorrowWeather'] = "Hail Showers";
                break;
            case '21':
                $returnArray['tomorrowWeather'] = "Hail";
                break;
            case '23':
                $returnArray['tomorrowWeather'] = "Light Snow Showers";
                break;
            case '24':
                $returnArray['tomorrowWeather'] = "Light Snow";
                break;
            case '26':
                $returnArray['tomorrowWeather'] = "Heavy Snow Showers";
                break;
            case '27':
                $returnArray['tomorrowWeather'] = "Heavy Snow";
                break;
            case '29':
                $returnArray['tomorrowWeather'] = "Thunder Shower";
                break;
            case '30':
                $returnArray['tomorrowWeather'] = "Thunderstormss";
                break;
            default:
                $returnArray['tomorrowWeather'] = "No weather data availible";
                break;
        }

        $returnArray['dayTemp'] = $daydata[0]['T'];
        $returnArray['nightTemp'] = $daydata[1]['T'];

        return $returnArray;
    }

	public function getData($path)
	{
		return json_decode(file_get_contents("http://shed.kent.ac.uk/$path"), true);
	}

	public function refreshRawSensorData($sensorID, $rate) {
		return $this->getData("device/$sensorID/$rate");
    }
    
    /**
     * Gets weather data for the next 24 hours
     */
    public function getWeatherData()
    {
        return json_decode(file_get_contents("http://datapoint.metoffice.gov.uk/public/data/val/wxfcs/all/json/322089?res=daily&key=e46d1123-7ccf-4a53-bd16-115c36761e23"), true);
    }

    /**
     * Gets rain readings for the past 24 hours
     */
    public function getRainData()
    {
        return json_decode(file_get_contents("http://environment.data.gov.uk/flood-monitoring/id/stations/E4090/readings.json?_limit=360&_sorted&parameter=rainfall"), true);
    }
}