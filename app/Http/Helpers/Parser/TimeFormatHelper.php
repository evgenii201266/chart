<?php

declare(strict_types=1);

namespace App\Http\Helpers\Parser;

use Carbon\Carbon;

trait TimeFormatHelper
{
    public function getFormatTime(string $time)
    {
        $data = $this->searchMatch($time);
        $timeForamt = $this->formatTime($data);
        return $timeForamt;
    }

    private function searchMatch($time): array
    {
        preg_match_all("/\d{2}/", $time, $data);

        return $data;
    }

    private function formatTime(array $data)
    {
        $year = $data[0][0] . $data[0][1];
        $month = $data[0][2];
        $day = $data[0][3];
        $hour = $data[0][4];
        $minute = $data[0][5];
        $second = $data[0][6];

        return Carbon::create($year, $month, $day, $hour, $minute, $second);
    }


}
