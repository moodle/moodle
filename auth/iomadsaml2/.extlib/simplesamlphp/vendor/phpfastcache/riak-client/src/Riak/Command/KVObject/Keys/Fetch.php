<?php

namespace Basho\Riak\Command\KVObject\Keys;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Lists Riak Kv Object keys
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Fetch extends Command\KVObject implements CommandInterface
{
    public function __construct(Command\Builder\ListObjects $builder)
    {
        parent::__construct($builder);

        $this->parameters['keys'] = 'true';
        $this->bucket = $builder->getBucket();
        $this->decodeAsAssociative = $builder->getDecodeAsAssociative();
    }
}
