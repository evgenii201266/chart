<?php

declare(strict_types=1);

namespace App\Http\Helpers\Parser;

use Carbon\Carbon;
use App\Models\Test;
use function GuzzleHttp\json_encode;

trait ParseHelper
{
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
        $dateArray = [];
        $objectIdArray = [];


        $count = count($responseDecode);
        for($i = 0; $i < $count; $i = $i + 1)
        {
            if(isset($responseDecode[$i]) && $responseDecode[$i]->params->{'Parameters.timestamp'} != '' && isset($responseDecode[$i]->params->{'RFBEACON.pamp_temp'})) {
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
                    $dateArray[] = $test->getFormatTime($time)->format('H:i:s');
                    $objectIdArray[] = $objectId;
// Отобразить min max temp
// id первого и последнего в интервале
// время первого и последнего
// 254type расширенна телеметри вывысти отдельным графиком
// экспорт базы в csv


// Обзор - выбор яхыкав,технологий, баз, фрэйврки, среда разработки  15стр
// Практическая часть - блок схема работы,   25стр
// Приложение- код, 


                    if($timeAfter->diffInHours($timeBefore) != 0) {
                        $pampTemp = array_sum($pampTempArray)/count($pampTempArray);
                        $pampVoltage = array_sum($pampVoltageArray)/count($pampVoltageArray);
                        $systemVoltage = array_sum($systemVoltageArray)/count($systemVoltageArray);
                        $batteryVoltage = array_sum($batteryVoltageArray)/count($batteryVoltageArray);

                        $jsonOption = $this->jsonOption($pampTempArray, $pampVoltageArray, 
                                                    $systemVoltageArray, $batteryVoltageArray, $dateArray, $objectIdArray);

                        $test->object_id = $objectId;
                        $test->pamp_temp = $pampTemp;
                        $test->pamp_voltage = $pampVoltage;
                        $test->system_voltage = $systemVoltage;
                        $test->battery_voltage = $batteryVoltage;
                        $test->options = $jsonOption;
        
                        $test->date = $test->getFormatTime($time);
                        $test->save();

                        $pampTempArray = [];
                        $pampVoltageArray = [];
                        $systemVoltageArray = [];
                        $batteryVoltageArray = [];
                
                    } else continue;
                
                }
            }
        }
    }
    
    private function jsonOption($pampTempArray, $pampVoltageArray, 
                                    $systemVoltageArray, $batteryVoltageArray, $dateArray, $objectIdArray)
    {
        return json_encode([
            'minTemp' => min($pampTempArray),
            'maxTemp' => max($pampTempArray),
            'idBegin' => min($objectIdArray),
            'idEnd' => max($objectIdArray),
            'minPampVoltage' => min($pampVoltageArray),
            'maxPampVoltage' => max($pampVoltageArray),
            'minSystemVoltage' => min($systemVoltageArray),
            'maxSystemVoltage' => max($systemVoltageArray),
            'minBatteryVoltage' => min($batteryVoltageArray),
            'maxBatteryVoltage' => max($batteryVoltageArray),
            'dateBegin' => reset($dateArray),
            'dateEnd' => array_pop($dateArray),
        ]);
    }
}
