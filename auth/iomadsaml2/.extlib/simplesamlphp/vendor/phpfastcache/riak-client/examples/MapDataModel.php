<?php

require __DIR__ . '/../vendor/autoload.php';

use Basho\Riak;

class User
{
    /**
     * @var Riak $riak
     */
    private $riak = null;

    /**
     * @var Riak\DataType\Map $data
     */
    private $data = null;

    /**
     * @var Riak\Bucket $bucket
     */
    private $bucket;

    /**
     * @var Riak\Location $location
     */
    private $location;

    /**
     * @var string $first_name
     */
    private $first_name;

    /**
     * @var string $last_name
     */
    private $last_name;

    public function __construct(Riak $riak, $first_name, $last_name)
    {
        $this->riak = $riak;
        $this->bucket = new Riak\Bucket('users', 'maps');
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->location = new Riak\Location(sprintf('%s_%s', $first_name, $last_name), $this->bucket);
    }

    public function __toString()
    {
        return json_encode([
            'first_name'   => $this->first_name,
            'last_name'    => $this->last_name,
            'interests'    => $this->getInterests(),
            'visits'       => $this->getVisitCount(),
            'paid_account' => $this->getPaidAccount()
        ]);
    }

    public function getInterests()
    {
        return $this->getData()->getSet('interests')->getData();
    }

    private function getData()
    {
        $response = (new Riak\Command\Builder\FetchMap($this->riak))
            ->atLocation($this->location)
            ->build()
            ->execute();

        if ($response->isSuccess()) {
            $this->data = $response->getMap();
        } elseif ($response->isNotFound()) {
            $this->data = $this->init();
        } else {
            throw new Exception('Unknown error: ' . $response->getStatusCode());
        }

        return $this->data;
    }

    private function init()
    {
        $response = (new Riak\Command\Builder\UpdateMap($this->riak))
            ->updateRegister('first_name', $this->first_name)
            ->updateRegister('last_name', $this->last_name)
            ->updateFlag('paid_account', false)
            ->atLocation($this->location)
            ->withParameter('returnbody', 'true')
            ->build()
            ->execute();

        return $response->getMap();
    }

    public function getVisitCount()
    {
        return $this->getData()->getCounter('visits')->getData();
    }

    public function getPaidAccount()
    {
        return $this->getData()->getFlag('paid_account');
    }

    public function addInterests(array $interests)
    {
        $updateSetBuilder = (new Riak\Command\Builder\UpdateSet($this->riak));
        foreach ($interests as $interest) {
            $updateSetBuilder->add($interest);
        }

        $response = (new Riak\Command\Builder\UpdateMap($this->riak))
            ->atLocation($this->location)
            ->updateSet('interests', $updateSetBuilder)
            ->withParameter('returnbody', 'true')
            ->build()
            ->execute();

        if ($response->isSuccess()) {
            $this->data = $response->getMap();
        }

        return $this;
    }

    public function addInterest($interest)
    {
        $updateSetBuilder = (new Riak\Command\Builder\UpdateSet($this->riak))->add($interest);

        $response = (new Riak\Command\Builder\UpdateMap($this->riak))
            ->atLocation($this->location)
            ->updateSet('interests', $updateSetBuilder)
            ->withParameter('returnbody', 'true')
            ->build()
            ->execute();

        if ($response->isSuccess()) {
            $this->data = $response->getMap();
        }

        return $this;
    }

    public function removeInterest($interest)
    {
        $updateSetBuilder = (new Riak\Command\Builder\UpdateSet($this->riak))->remove($interest);

        $response = (new Riak\Command\Builder\UpdateMap($this->riak))
            ->atLocation($this->location)
            ->updateSet('interests', $updateSetBuilder)
            ->withParameter('returnbody', 'true')
            ->withContext($this->data->getContext())
            ->build()
            ->execute();

        if ($response->isSuccess()) {
            $this->data = $response->getMap();
        }

        return $this;
    }

    public function recordVisit()
    {
        $updateCounterBuilder = (new Riak\Command\Builder\IncrementCounter($this->riak))
            ->withIncrement(1);

        $response = (new Riak\Command\Builder\UpdateMap($this->riak))
            ->updateCounter('visits', $updateCounterBuilder)
            ->atLocation($this->location)
            ->withParameter('returnbody', 'true')
            ->build()
            ->execute();

        if ($response->isSuccess()) {
            $this->data = $response->getMap();
        }

        return $this;
    }

    public function upgradeAccount()
    {
        $response = (new Riak\Command\Builder\UpdateMap($this->riak))
            ->updateFlag('paid_account', true)
            ->atLocation($this->location)
            ->withParameter('returnbody', 'true')
            ->build()
            ->execute();

        if ($response->isSuccess()) {
            $this->data = $response->getMap();
        }

        return $this;
    }

    public function downgradeAccount()
    {
        $response = (new Riak\Command\Builder\UpdateMap($this->riak))
            ->updateFlag('paid_account', false)
            ->atLocation($this->location)
            ->withParameter('returnbody', 'true')
            ->build()
            ->execute();

        if ($response->isSuccess()) {
            $this->data = $response->getMap();
        }

        return $this;
    }

    public function getFirstName()
    {
        return $this->getData()->getRegister('first_name');
    }

    public function getLastName()
    {
        return $this->getData()->getRegister('last_name');
    }
}

$riak = new Riak((new Riak\Node\Builder())->buildLocalhost([8087]));

$iAmBatman = new User($riak, 'Bruce', 'Wayne');
$iAmBatman->addInterests(['crime fighting', 'climbing stuff']);
// prints json to standard out
echo $iAmBatman;

//-----------------------------------------------------------

$joe = (new User($riak, 'Joe', 'Armstrong'))
    ->addInterests(['distributed systems', 'Erlang'])
    ->recordVisit();

var_dump(
    $joe->getFirstName(),
    $joe->getLastName(),
    $joe->getInterests(),
    $joe->getVisitCount(),
    $joe->recordVisit()->getVisitCount(),
    $joe->getPaidAccount()
);
