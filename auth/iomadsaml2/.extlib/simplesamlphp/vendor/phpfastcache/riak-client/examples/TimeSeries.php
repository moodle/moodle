<?php

require __DIR__ . '/../vendor/autoload.php';

use Basho\Riak;
use Basho\Riak\Command;
use Basho\Riak\Node;

$node = (new Node\Builder)
    ->atHost('riak-test')
    ->onPort(8087)
    ->build();

$riak = new Riak([$node], [], new Riak\Api\Pb());


# create table
$table_definition = "
    CREATE TABLE %s (
        family varchar not null,
        series varchar not null,
        time timestamp not null,
        weather varchar not null,
        temperature double,
        PRIMARY KEY((family, series, quantum(time, 15, 'm')), family, series, time)
    )";

$command = (new Command\Builder\TimeSeries\Query($riak))
    ->withQuery(sprintf($table_definition, "GeoCheckins"))
    ->build();

if (!$response->isSuccess()) {
    echo $response->getMessage();
    exit;
}


# describe table
$command = (new Command\Builder\TimeSeries\DescribeTable($riak))
    ->withTable('GeoCheckins')
    ->build();

if (!$response->isSuccess()) {
    echo $response->getMessage();
    exit;
}

foreach ($response->getResults() as $column_index => $column_definition) {
    print_r([$column_index => $column_definition]);
}


# store a row
$response = (new Command\Builder\TimeSeries\StoreRows($riak))
    ->inTable('GeoCheckins')
    ->withRow([
        (new Cell("family"))->setValue("family1"),
        (new Cell("series"))->setValue("series1"),
        (new Cell("time"))->setTimestampValue(1420113600),
        (new Cell("weather"))->setValue("hot"),
        (new Cell("temperature"))->setValue(23.5),
    ])
    ->build()
    ->execute();

if (!$response->isSuccess()) {
    echo $response->getMessage();
    exit;
}


# store rows
$response = (new Command\Builder\TimeSeries\StoreRows($riak))
    ->inTable('GeoCheckins')
    ->withRows([
        [
            (new Cell("family"))->setValue("family1"),
            (new Cell("series"))->setValue("series1"),
            (new Cell("time"))->setTimestampValue(1420115400),
            (new Cell("weather"))->setValue("hot"),
            (new Cell("temperature"))->setValue(22.4),
        ],
        [
            (new Cell("family"))->setValue("family1"),
            (new Cell("series"))->setValue("series1"),
            (new Cell("time"))->setTimestampValue(1420117200),
            (new Cell("weather"))->setValue("warm"),
            (new Cell("temperature"))->setValue(20.5),
        ],
    ])
    ->build()
    ->execute();

if (!$response->isSuccess()) {
    echo $response->getMessage();
    exit;
}


# fetch a row
/** @var Command\TimeSeries\Response $response */
$response = (new Command\Builder\TimeSeries\FetchRow($riak))
    ->atKey([
        (new Cell("family"))->setValue("family1"),
        (new Cell("series"))->setValue("series1"),
        (new Cell("time"))->setTimestampValue(1420113600),
    ])
    ->inTable('GeoCheckins')
    ->build()
    ->execute();

if (!$response->isSuccess()) {
    echo $response->getMessage();
    exit;
}

# output row data
foreach ($response->getRow() as $index => $column) {
    switch ($column->getType()) {
        case Riak\TimeSeries\Cell::INT_TYPE:
            printf("Column %d: %s is an integer equal to %d\n", $index, $column->getName(), $column->getValue());
            break;
        case Riak\TimeSeries\Cell::DOUBLE_TYPE:
            printf("Column %d: %s is a double equal to %d\n", $index, $column->getName(), $column->getValue());
            break;
        case Riak\TimeSeries\Cell::BOOL_TYPE:
            printf("Column %d: %s is a boolean equal to %s\n", $index, $column->getName(), $column->getValue());
            break;
        case Riak\TimeSeries\Cell::TIMESTAMP_TYPE:
            printf("Column %d: %s is a timestamp equal to %d\n", $index, $column->getName(), $column->getValue());
            break;
        default:
            printf("Column %d: %s is a string equal to %s\n", $index, $column->getName(), $column->getValue());
            break;
    }
}


# query for data
$response = (new Command\Builder\TimeSeries\Query($riak))
    ->withQuery("select * from GeoCheckins where family = 'family1' and series = 'myseries1' and (time > 1420113500 and time < 1420116000)")
    ->build()
    ->execute();

# output rows
foreach ($response->getResults() as $row_index => $row) {
    foreach ($row as $column_index => $column) {
        switch ($column->getType()) {
            case Riak\TimeSeries\Cell::INT_TYPE:
                printf("Column %d: %s is an integer equal to %d\n", $index, $column->getName(), $column->getValue());
                break;
            case Riak\TimeSeries\Cell::DOUBLE_TYPE:
                printf("Column %d: %s is a double equal to %d\n", $index, $column->getName(), $column->getValue());
                break;
            case Riak\TimeSeries\Cell::BOOL_TYPE:
                printf("Column %d: %s is a boolean equal to %s\n", $index, $column->getName(), $column->getValue());
                break;
            case Riak\TimeSeries\Cell::TIMESTAMP_TYPE:
                printf("Column %d: %s is a timestamp equal to %d\n", $index, $column->getName(), $column->getValue());
                break;
            default:
                printf("Column %d: %s is a string equal to %s\n", $index, $column->getName(), $column->getValue());
                break;
        }
    }
}


# delete a row
$response = (new Command\Builder\TimeSeries\DeleteRow($riak))
    ->atKey([
        (new Cell("family"))->setValue("family1"),
        (new Cell("series"))->setValue("series1"),
        (new Cell("time"))->setTimestampValue(1420113600),
    ])
    ->inTable('GeoCheckins')
    ->build()
    ->execute();

if (!$response->isSuccess()) {
    echo $response->getMessage();
    exit;
}
