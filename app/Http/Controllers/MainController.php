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
        dd("hit2");
    }
}
