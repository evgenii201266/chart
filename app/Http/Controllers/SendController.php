<?php

namespace App\Http\Controllers;
use App\Models\Test;


use Illuminate\Http\Request;


class SendController extends Controller
{


    public function send(Request $request)
    {
        $date = [];
        $pampTemp = [];
        $pampVoltage = [];
        $systemVoltage = [];
        $batteryVoltage = [];

        $count = Test::count();
        $lastData = 100;
        $test = Test::skip($count-$lastData)->take($lastData)->get();

        foreach ($test as $t)
        {
            $date[] = $t->date;
            $pampTemp[] = $t->pamp_temp;
            $pampVoltage[] = $t->pamp_voltage;
            $systemVoltage[] = $t->system_voltage;
            $batteryVoltage[] = $t->battery_voltage;

        }

        return response(['date' => $date,
                         'pampTemp' =>$pampTemp,
                         'pampVoltage' =>$pampVoltage,
                         'systemVoltage' =>$systemVoltage,
                         'batteryVoltage' =>$batteryVoltage
                         ]);
    }

    public function search(Request $request)
    {
        $date = [];
        $pampTemp = [];
        $pampVoltage = [];
        $systemVoltage = [];
        $batteryVoltage = [];
        $test = Test::where('date', 'LIKE', '%'. $request->catalogId .'%')->get();

        foreach ($test as $t)
        {
            $date[] = $t->date;
            $pampTemp[] = $t->pamp_temp;
            $pampVoltage[] = $t->pamp_voltage;
            $systemVoltage[] = $t->system_voltage;
            $batteryVoltage[] = $t->battery_voltage;

        }

        return response(['date' => $date,
                         'pampTemp' =>$pampTemp,
                         'pampVoltage' =>$pampVoltage,
                         'systemVoltage' =>$systemVoltage,
                         'batteryVoltage' =>$batteryVoltage
                         ]);
    }
  

}
