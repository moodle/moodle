<?php

namespace Basho\Tests;

use Basho\Riak\TimeSeries\Cell;

/**
 * Helps with reusability for timeseries commands
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */

trait TimeSeriesTrait
{
    protected static $table = "WeatherByRegion";
    protected static $tableBlob = "GeoCheckin_Wide_1_5";
    protected static $key = [];
    protected static $now;

    protected static function tableDefinition($table_name = "")
    {
        $table = "
            CREATE TABLE %s (
                region varchar not null,
                state varchar not null,
                time timestamp not null,
                weather varchar not null,
                temperature double,
                uv_index sint64,
                observed boolean not null,
                PRIMARY KEY((region, state, quantum(time, 15, 'm')), region, state, time)
            )";

        return sprintf($table, $table_name ? $table_name : static::$table);
    }

    protected static function populateKey()
    {
        static::$now = new \DateTime("@1443816900");

        static::$key = [
            (new Cell("region"))->setValue("South Atlantic"),
            (new Cell("state"))->setValue("South Carolina"),
            (new Cell("time"))->setTimestampValue(static::$now->getTimestamp()),
        ];
    }

    public static function generateRows()
    {
        $row = static::generateRow();
        $rows = [
            $row,
            [
                $row[0],
                $row[1],
                (new Cell("time"))->setTimestampValue(static::oneHourAgo()),
                (new Cell("weather"))->setValue("windy"),
                (new Cell("temperature"))->setDoubleValue(19.8),
                (new Cell("uv_index"))->setIntValue(10),
                (new Cell("observed"))->setBooleanValue(true),
            ],
            [
                $row[0],
                $row[1],
                (new Cell("time"))->setTimestampValue(static::twoHoursAgo()),
                (new Cell("weather"))->setValue("cloudy"),
                (new Cell("temperature"))->setDoubleValue(19.1),
                (new Cell("uv_index"))->setIntValue(15),
                (new Cell("observed"))->setBooleanValue(false),
            ],
        ];

        return $rows;
    }

    public static function generateRow()
    {
        $row = static::$key;
        $row[] = (new Cell("weather"))->setValue("hot");
        $row[] = (new Cell("temperature"))->setDoubleValue(23.5);
        $row[] = (new Cell("uv_index"))->setIntValue(10);
        $row[] = (new Cell("observed"))->setBooleanValue(true);

        return $row;
    }

    public static function oneHourAgo()
    {
        return static::$now->getTimestamp() - 60 * 60 * 1;
    }

    public static function twoHoursAgo()
    {
        return static::$now->getTimestamp() - 60 * 60 * 2;
    }

    public static function threeHoursAgo()
    {
        return static::$now->getTimestamp() - 60 * 60 * 3;
    }
}
