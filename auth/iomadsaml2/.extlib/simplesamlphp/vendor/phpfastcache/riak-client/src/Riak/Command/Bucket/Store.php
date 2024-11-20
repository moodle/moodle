<?php

namespace Basho\Riak\Command\Bucket;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to set a bucket property on a bucket
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Store extends Command implements CommandInterface
{
    protected $method = 'PUT';

    protected $properties = [];

    /**
     * @var Command\Bucket\Response|null
     */
    protected $response = null;

    public function __construct(Command\Builder\SetBucketProperties $builder)
    {
        parent::__construct($builder);

        $this->bucket = $builder->getBucket();
        $this->properties = $builder->getProperties();
    }

    public function getEncodedData()
    {
        return json_encode($this->getData());
    }

    public function getData()
    {
        return ['props' => $this->properties];
    }

    /**
     * @return Command\Bucket\Response
     */
    public function execute()
    {
        return parent::execute();
    }
}
