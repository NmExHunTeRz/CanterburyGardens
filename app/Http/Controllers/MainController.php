<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    /**
     * Our initial index function that gets hit on initial page load
     */
    public function index()
    {
        $sites = json_decode(file_get_contents('http://shed.kent.ac.uk/sites'), true);



        return view('home', ['sites' => collect($sites)]);
    }
}
