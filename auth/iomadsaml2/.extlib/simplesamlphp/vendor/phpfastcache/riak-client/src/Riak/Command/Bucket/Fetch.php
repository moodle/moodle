<?php

namespace Basho\Riak\Command\Bucket;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Fetches properties for a Riak Bucket
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Fetch extends Command implements CommandInterface
{
    /**
     * @var Command\Bucket\Response|null
     */
    protected $response = null;

    public function __construct(Command\Builder\FetchBucketProperties $builder)
    {
        parent::__construct($builder);

        $this->bucket = $builder->getBucket();
    }

    public function getData()
    {
        return '';
    }

    public function getEncodedData()
    {
        return '';
    }

    /**
     * @return Command\Bucket\Response
     */
    public function execute()
    {
        return parent::execute();
    }
}
