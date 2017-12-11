<?php

namespace App\Http\Controllers;

use App\Condition;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Requires that our user is authenticated in order to access any functions within this controller
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Returns the preferences view with all of the set conditions
     */
    public function preferences()
    {
        return view ('preferences', ['conditions' => Condition::all()]);
    }


    /**
     * Updates each database entry for conditions with the new values
     */
    public function updatePreferences(Request $request)
    {
        foreach ($request->all() as $id => $value) {
            if (!is_string($value)) {
                $condition = Condition::find($id);
                $condition->high_humidity = $value['high_humidity'];
                $condition->low_humidity = $value['low_humidity'];
                $condition->high_moisture = $value['high_moisture'];
                $condition->low_moisture = $value['low_moisture'];
                $condition->high_lux = $value['high_lux'];
                $condition->low_lux = $value['low_lux'];
                $condition->high_gas = $value['high_gas'];
                $condition->low_gas = $value['low_gas'];
                $condition->high_temp = $value['high_temp'];
                $condition->low_temp = $value['low_temp'];
                $condition->winter_high_temp = $value['winter_high_temp'];
                $condition->winter_low_temp = $value['winter_low_temp'];
                $condition->save();
            }
        }

        $request->session()->flash('success', 'Successfully updated your preferences.');

        return view('preferences', ['conditions' => Condition::all()]);
    }
}
