<?php

namespace App\Http\Controllers;

use App\Http\Charts\TestChart;
use App\Models\Test;

use Illuminate\Http\Request;


class ChartController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function index()
    {
        $current = [];
        $temperature = [];
        $test = Test::all();
        foreach ($test as $t)
        {
            $current[] = $t->date;
            $temperature[] = $t->temperature;
            // dd($t->created_at);

        }
        $chart = new TestChart;

        $api = url('/api');

        $chart->labels($current)->load($api);
        $chart->options([
            'responsive' => true,
            'scales'=> [
                'yAxes'=>[
                    [
                    'ticks' => [
                        'beginAtZero'=> true,
                        'steps'=> 5,
                        'stepValue'=> 6,
                        'max' => 6000 //max value for the chart is 60
                        ]
                    ]
                ]
            ]
        ]);
        
        return view('welcome', compact('chart'));
    }


    // public function displayLegend(bool $legend)
    // {
    //     return $this->options([
    //         'legend' => [
    //             'display' => $legend,
    //         ],
    //     ]);
    // }

}
