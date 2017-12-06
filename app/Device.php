<?php

namespace App;

class Device
{
	public $name;
	public $id;
	public $type;
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

	public function __construct($name, $id, $type, $last_connection)
	{
		$this->name = $name;
		$this->id = $id;
		$this->type = $type;

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

	public function getLastConnection() {
		return $this->last_connection;
	}

	public function setTimestamps($timestamps)
	{
		$this->timestamps = $timestamps;
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

	public function setProcessedSecondaryReadings($reading) {
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
		}

		$data = $this->rawSecondaryreadings;
		if ($data != null) {
			if (in_array(null, $secondaryReadings, true)) {
				$this->fillNullValues($data);
				$this->setSecondaryProcessedReadings($tmp);
			}
		}

		// Data smoothing
		$data = $this->readings;
		switch($this->getType()) {
			case 'gas':
					$tmp = $this->median_filter($this->readings);
					break;
			case 'solar':
					$tmp = $this->median_filter($this->readings);
					break;
			case 'hydrometer':
					$tmp = $this->median_filter($this->readings);
					break;
			case 'tempHumid':
					$tmp = $this->median_filter($this->readings);
					break;
			case 'lumosity':
					$tmp = $this->median_filter($this->readings);
					break;
			}

	}

	public function fillNullValues($array) {
		$tmp = array_filter( $array, 'strlen' );            
		$average = array_sum($tmp)/count($tmp);
		for($i = 0; $i < count($array); $i++) {
			if ($array[$i] === null) {
				$array[$i] = $average;
				// dump("Fixed!");
			}
		}
		return $array;
	}

	public function median_filter($array) {
		$length = count($array);
		$new_array = [];
		$sum = 0;
		for ($i = 0; $i < $length; $i+=3) {
			for ($j = $i; $j < ($i + 3); $j++) {
				$new_array[$i] = $array[$i];
				$sum += $array[$i];
			}
			$new_array[$i+1] = floor($sum/4);
			$sum = 0;
		}

		return $new_array;
	}

	public function movingAverage($array) {

	}

	public function setNotify() {
		// See if the device has connected in the last 12 hours
		$last_connection = strtotime($this->last_connection);
		$current_time = time();
		$tmp = true;
		if (($current_time - $last_connection) > 43200) {} //nothing to do here
		// See if the data from the last 20 readings were all null
		else {
			$i = sizeof($this->readings) - 1;
			$n = sizeof($this->readings) - 20;

			for ($i; $i >= $n; $i--) {
				if ($this->readings[$i] !== null) {
					$tmp = false;
				}
			}
		}
		$this->notify = $tmp;
	}
}
