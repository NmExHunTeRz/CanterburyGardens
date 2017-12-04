<?php

namespace App;

class Device
{
    public $name;
    public $id;
    public $type;
    public $last_connection;

    public function __construct($name, $id, $type, $last_connection)
    {
        $this->name = $name;
        $this->id = $id;
        $this->type = $type;
        $this->last_connection = $last_connection;
    }
}
