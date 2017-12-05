<?php

namespace App;

class Device
{
    public $name;
    public $id;
    public $type;
    public $last_connection;

    public $data;
    public $timestamp;
    public $reading;
    public $secondaryData;
    public $dataScale;
    public $secondaryDataScale;

    public function __construct($name, $id, $type, $last_connection)
    {
        $this->name = $name;
        $this->id = $id;
        $this->type = $type;
        $this->last_connection = $last_connection;
        $this->data = null;
        $this->timestamp = null;
        $this->reading = null;
        $this->secondaryData = null;
        $this->dataScale = null;
        $this->secondaryDataScale = null;
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

    public function setData($data) {
        $this->data = $data;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function setReading($reading)
    {
        $this->reading = $reading;
    }

    public function setSecondaryData($data) {
        $this->secondaryData = $data;
    }

    public function setScale($scale) {
        $this->dataScale = $scale;
    }

    public function setSecondaryScale($scale) {
        $this->secondaryDataScale = $scale;
    }
}
