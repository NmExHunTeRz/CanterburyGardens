<?php

namespace App;

class Device
{
	public $name;
	public $id;
	public $type;
	public $site_id;
	public $last_connection;
	public $fidelity;

	public $timestamps;
	public $secondaryTimestamps;

	public $rawReadings;
	public $readings;
	public $rawSecondaryreadings;
	public $secondaryReadings;
	public $dataScale;
	public $secondaryDataScale;

	// If this is set to true, the device is encountering problems and needs to be checked.
	public $notify;

	public function __construct($name, $id, $type, $last_connection, $site_id)
	{
		$this->name = $name;
		$this->id = $id;
		$this->type = $type;
		$this->site_id = $site_id;

		$this->timestamps = null;
		$this->secondaryTimestamps = null;
		$this->readings = null;
		$this->rawReadings = null;
		$this->rawSecondaryreadings = null;
		$this->secondaryReadings = null;
		$this->dataScale = null;
		$this->secondaryDataScale = null;
		$this->notify = false;
		$this->fidelity = null;

		$str = str_replace("T", " ", $last_connection);
		$str = substr($str, 0, 19);
		$this->last_connection = $str;
	}

	public function getName() {
		return $this->name;
	}

	public function getID() {
		return $this->id;
	}

	public function getType() {
		return $this->type;
	}

    public function getSiteID()
    {
        return $this->site_id;
	}

	public function getLastConnection() {
		return $this->last_connection;
	}

	public function setTimestamps($timestamps)
	{
		$tmp = [];
		foreach($timestamps as $timestamp) {
			$str = str_replace("T", " ", $timestamp);
			$str = substr($str, 0, 19);
			// dd($str);
			array_push($tmp, $str);
		}
		$this->timestamps = $tmp;
	}

	public function setSecondaryTimestamps($secondaryTimestamps)
	{
		$this->secondaryTimestamps = $secondaryTimestamps;
	}
	public function setReadings($readings)
	{
		$this->rawReadings = $readings;
	}

	public function setProcessedReadings($readings) {
		$this->readings = $readings;
	}

	public function setSecondaryReadings($secondaryReadings)
	{
		$this->secondaryReadings = $secondaryReadings;
	}

	public function setProcessedSecondaryReadings($readings) {
		$this->readings = $readings;
	}

	public function setScale($scale) {
		$this->dataScale = $scale;
	}

	public function setSecondaryScale($scale) {
		$this->secondaryDataScale = $scale;
	}

	public function setFidelity($value) {
		$this->fidelity = $value;
	}

	public function processData() {
		// Fill null values
		$data = $this->rawReadings;
		if (in_array(null, $data, true)) {
			$tmp = $this->fillNullValues($data);
			$this->setProcessedReadings($tmp);
		} else {
			$this->setProcessedReadings($data);
		}

		$data = $this->rawSecondaryreadings;
		if ($data != null) {
			if (in_array(null, $secondaryReadings, true)) {
				$this->fillNullValues($data);
				$this->setSecondaryProcessedReadings($tmp);
			} else {
				$this->setProcessedSecondaryReadings($data);
			}
		}


		// Removing invalid data
		switch($this->getType()) {
			case 'gas':
					$tmp = $this->remove_invalid($this->readings, 0, 10000);
					$this->setProcessedReadings($tmp);
					break;
			case 'solar':
					$tmp = $this->remove_invalid($this->readings, 0, 1000); // Need to verify the upper range of this
					$this->setProcessedReadings($tmp);
					break;
			case 'hydrometer':
					$tmp = $this->remove_invalid($this->readings, 0, 100);
					$this->setProcessedReadings($tmp);
					break;
			case 'tempHumid':
					$tmp = $this->remove_invalid($this->readings, -40, 80);
					$this->setProcessedReadings($tmp);
					$tmp = $this->remove_invalid($this->secondaryReadings, 0, 100);
					$this->setProcessedSecondaryReadings($tmp);
					break;
			case 'lumosity':
					$tmp = $this->remove_invalid($this->readings, 0, 100000);
					$this->setProcessedReadings($tmp);
					break;
		}

	}

	/*
	 *	Fill any null values in our data with the average value over the entire array.
	 *  If array is entirely null values, this will fill every element with 0.
	 */
	public function fillNullValues($array) {
		$tmp = array_filter( $array, 'strlen' );

		if (!empty($tmp)) { // Checks to see if our array has values
            $average = array_sum($tmp)/count($tmp);
            for($i = 0; $i < count($array); $i++) {
                if ($array[$i] === null) {
                    $array[$i] = $average;
                }
            }
            return $array;
        }
	}

	/*
	 *	Remove invalid values (outside acceptable range). Replace each invalid value with the previous value.
	 */
	public function remove_invalid($array, $low, $high) {
		for ($i = 0; $i < count($array); $i++) {
			if ($array[$i] < $low || $array[$i] > $high) {
				if ($i === 0) $array[$i] = $low; 
				else $array[$i] = $array[$i - 1];
			}
		}
		return $array;
	}

	/*
	 *	Checks that the sensor has returned sensible values in the past 100 measurements
	 *	and check that the device has connected in the last 12 hours.
	 */
	public function setNotify() {
		// See if the device has connected in the last 12 hours
		$last_connection = strtotime($this->last_connection);
		$current_time = time();
		$tmp = false;
		if (($current_time - $last_connection) > 43200) $tmp = true;
		// See if the data from the last 100 readings were all null
		else {
			$i = sizeof($this->readings) - 1;
			$n = sizeof($this->readings) - 20;

			for ($i; $i >= $n; $i--) {
				if ($this->readings[$i] === null) {
				} else {
					$tmp = true;
					break;
				}
			}
		}
		$this->notify = $tmp;
	}
}
