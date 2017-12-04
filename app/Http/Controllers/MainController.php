<?php

namespace App\Http\Controllers;

use App\Device;
use Illuminate\Http\Request;

class MainController extends Controller
{
    /**
     * Our initial index function that gets hit on initial page load
     */
    public function index()
    {
        $sites = $sensors = [];

        $data_sites = $this->getData('sites');

        foreach ($data_sites as $data_site) {
            $sites[$data_site['id']] = $data_site;
        }

        $types = $this->getData('devices');

        foreach ($sites as $key => $site) {
            foreach ($site['zones'] as $index => $zone) {
                $sites[$key]['zones'][$zone['id']] = ['name' => $zone['name'], 'devices' => []];
                unset($sites[$key]['zones'][$index]);
            }
        }

        foreach ($types as $key => $type) {
            foreach ($type as $sensor) {
                $data = $this->getData("device/$sensor");

                $device = new Device($data['name'], $data['id'], $key, $data['last_connection']);

                array_push($sites[$data['site_id']]['zones'][$data['zone_id']]['devices'], $device);
                array_push($sensors, $device);

            }
        }
        // Go into sensor, pick an id, change it, find it in sites and see if its changed


        dump($sensors);
        dump($sites);

        return view('home', ['sites' => collect($sites)]);
    }

    public function getData($path)
    {
        return json_decode(file_get_contents("http://shed.kent.ac.uk/$path"), true);
    }
}
