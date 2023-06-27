<?php

require __DIR__ . '/../vendor/autoload.php';

use Basho\Riak;
use Basho\Riak\Command;
use Basho\Riak\Node;

$node = (new Node\Builder)
    ->atHost('riak-test')
    ->onPort(8098)
    ->build();

$riak = new Riak([$node]);

$user = new \StdClass();
$user->name = 'John Doe';
$user->email = 'jdoe@example.com';

// store a new value
$command = (new Command\Builder\StoreObject($riak))
    ->buildJsonObject($user)
    ->buildBucket('users')
    ->build();

$response = $command->execute();

$location = $response->getLocation();

$command = (new Command\Builder\FetchObject($riak))
    ->atLocation($location)
    ->build();

$response = $command->execute();

$object = $response->getObject();

$object->getData()->country = 'USA';

$command = (new Command\Builder\StoreObject($riak))
    ->withObject($object)
    ->atLocation($location)
    ->build();

$response = $command->execute();

echo $response->getStatusCode() . PHP_EOL;
