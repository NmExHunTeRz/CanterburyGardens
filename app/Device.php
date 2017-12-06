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

    public $readings;
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
        $this->secondaryReadings = null;
        $this->dataScale = null;
        $this->secondaryDataScale = null;
        $this->notify = false;
        $this->fidelity = null;

        $str = str_replace("T", " ", $last_connection);
        $str = substr($str, 0, 19);
        $this->last_connection = $str;

        $this->setNotify();
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
        $this->readings = $readings;
    }

    public function setSecondaryReadings($secondaryReadings)
    {
        $this->secondaryReadings = $secondaryReadings;
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
        $data = $this->readings;
        $secondaryData = $this->secondaryReadings;

        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i] == null) { 
                dump($this->id);
                dump($data[$i]);
            }
        }
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
                if ($this->readings[$i] != "null") { 
                    $tmp = false;
                }
            }
        }
        $this->notify = $tmp;
    }
}
