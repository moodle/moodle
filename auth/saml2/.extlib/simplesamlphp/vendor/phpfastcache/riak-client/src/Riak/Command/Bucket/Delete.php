<?php

namespace Basho\Riak\Command\Bucket;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to remove a bucket property from a Riak bucket
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Delete extends Command\KVObject implements CommandInterface
{
    protected $method = 'DELETE';

    public function __construct(Command\Builder\DeleteObject $builder)
    {
        parent::__construct($builder);

        $this->bucket = $builder->getBucket();
        $this->location = $builder->getLocation();
    }
}
