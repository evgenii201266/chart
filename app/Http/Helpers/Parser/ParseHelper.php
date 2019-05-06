<?php

declare(strict_types=1);

namespace App\Http\Helpers\Parser;

use Carbon\Carbon;
use App\Models\Test;

trait ParseHelper
{
    // User::chunk(200, function($users)
    // {
    //     foreach ($users as $user)
    //     {
    //         //
    //     }
    // });

    public function getParse($response)
    {
        $responseDecode = json_decode($response);
        // dd($responseDecode[0]);
        
        usort($responseDecode, function($first,$second){
            return ($first->id < $second->id) ? -1 : 1;
        });
        

        $pampTempArray = [];
        $pampVoltageArray = [];
        $systemVoltageArray = [];
        $batteryVoltageArray = [];


        for($i = 0; $i<count($responseDecode); $i = $i + 1)
        {
            if(isset($responseDecode[$i]) && $responseDecode[$i]->params->{'Parameters.timestamp'} != '' && isset($responseDecode[$i]->params->{'RFBEACON.pamp_temp'})) {
            // Test::truncate();
                $pampTemp  = $responseDecode[$i]->params->{'RFBEACON.pamp_temp'};
                
                $pampVoltage = $responseDecode[$i]->params->{'RFBEACON.pamp_voltage'};
                $systemVoltage = $responseDecode[$i]->params->{'RFBEACON.system_voltage'};
                $batteryVoltage = $responseDecode[$i]->params->{'RFBEACON.battery_voltage'};

                $time = $responseDecode[$i]->params->{'Parameters.timestamp'};
                $objectId = $responseDecode[$i]->id;

                $test = new Test();


                if(isset($responseDecode[$i+1])) {
                    $timeBefore = $test->getFormatTime($responseDecode[$i]->params->{'Parameters.timestamp'});
                    $timeAfter = $test->getFormatTime($responseDecode[$i+1]->params->{'Parameters.timestamp'});
                    
                    $pampTempArray[] = $pampTemp;
                    $pampVoltageArray[] = $pampVoltage;
                    $systemVoltageArray[] = $systemVoltage;
                    $batteryVoltageArray[] = $batteryVoltage;


                    if($timeAfter->diffInHours($timeBefore) != 0) {
                        $pampTemp = array_sum($pampTempArray)/count($pampTempArray);
                        $pampVoltage = array_sum($pampVoltageArray)/count($pampVoltageArray);
                        $systemVoltage = array_sum($systemVoltageArray)/count($systemVoltageArray);
                        $batteryVoltage = array_sum($batteryVoltageArray)/count($batteryVoltageArray);

                        $test->object_id = $objectId;
                        $test->pamp_temp = $pampTemp;
                        $test->pamp_voltage = $pampVoltage;
                        $test->system_voltage = $systemVoltage;
                        $test->battery_voltage = $batteryVoltage;
        
                        $test->date = $test->getFormatTime($time);
                        $test->save();

                        $pampTempArray = [];
                        $pampVoltageArray = [];
                        $systemVoltageArray = [];
                        $batteryVoltageArray = [];
                
                    } else continue;
                
                }
                

                // $test->object_id = $objectId;
                // $test->pamp_temp = $pampTemp;
                // $test->pamp_voltage = $pampVoltage;
                // $test->system_voltage = $systemVoltage;
                // $test->battery_voltage = $batteryVoltage;

                // $test->date = $test->getFormatTime($time);
                // $test->save();
            }
        }
        // Test::truncate();
    }

}
