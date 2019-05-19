<?php

namespace App\Http\Controllers;
use App\Models\Test;

use Illuminate\Http\Request;
use App\User;

class SendController extends Controller
{
    public function send(Request $request)
    {
        $count = Test::count();
        $lastDataCount = 100;

        $test = Test::skip($count-$lastDataCount)->take($lastDataCount)
                        ->select('date', 'pamp_temp','pamp_voltage','system_voltage', 'battery_voltage')
                        ->getQuery()
                        ->get();

        return response(['data' => $test]);
    }

    public function search(Request $request)
    {
        $test = Test::where('date', 'LIKE', $request->catalogId .'%')
                        ->getQuery()
                        ->get();


        return response(['data' => $test]);
    }
}
