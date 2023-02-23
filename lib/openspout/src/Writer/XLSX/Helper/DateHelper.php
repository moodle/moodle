<?php

namespace OpenSpout\Writer\XLSX\Helper;

class DateHelper
{
    /**
     * @see https://github.com/PHPOffice/PhpSpreadsheet/blob/1.22.0/src/PhpSpreadsheet/Shared/Date.php#L296
     *
     * @return float
     */
    public static function toExcel(\DateTimeInterface $dateTime)
    {
        $year = (int) $dateTime->format('Y');
        $month = (int) $dateTime->format('m');
        $day = (int) $dateTime->format('d');
        $hours = (int) $dateTime->format('H');
        $minutes = (int) $dateTime->format('i');
        $seconds = (int) $dateTime->format('s');
        // Fudge factor for the erroneous fact that the year 1900 is treated as a Leap Year in MS Excel
        // This affects every date following 28th February 1900
        $excel1900isLeapYear = true;
        if ((1900 === $year) && ($month <= 2)) {
            $excel1900isLeapYear = false;
        }
        $myexcelBaseDate = 2415020;

        //    Julian base date Adjustment
        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            --$year;
        }

        //    Calculate the Julian Date, then subtract the Excel base date (JD 2415020 = 31-Dec-1899 Giving Excel Date of 0)
        $century = (int) substr((string) $year, 0, 2);
        $decade = (int) substr((string) $year, 2, 2);
        $excelDate = floor((146097 * $century) / 4) + floor((1461 * $decade) / 4) + floor((153 * $month + 2) / 5) + $day + 1721119 - $myexcelBaseDate + $excel1900isLeapYear;

        $excelTime = (($hours * 3600) + ($minutes * 60) + $seconds) / 86400;

        return (float) $excelDate + $excelTime;
    }
}
