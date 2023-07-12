<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Used to fetch KV objects from Riak
 *
 * <code>
 * $command = (new Command\Builder\FetchObject($riak))
 *   ->buildLocation($user_id, 'users', 'default')
 *   ->build();
 *
 * $response = $command->execute();
 *
 * $user = $response->getObject();
 * </code>
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class FetchObject extends Command\Builder implements Command\BuilderInterface
{
    use ObjectTrait;
    use LocationTrait;

    /**
     * @var bool
     */
    protected $decodeAsAssociative = false;

    public function __construct(Riak $riak)
    {
        parent::__construct($riak);
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\KVObject\Fetch;
     */
    public function build()
    {
        $this->validate();

        return new Command\KVObject\Fetch($this);
    }

    /**
     * Tells the client to decode the data as an associative array instead of a PHP stdClass object.
     * Only works if the fetched object type is JSON.
     *
     * @return $this
     */
    public function withDecodeAsAssociative()
    {
        $this->decodeAsAssociative = true;
        return $this;
    }

    /**
     * Fetch the setting for decodeAsAssociative.
     *
     * @return bool
     */
    public function getDecodeAsAssociative()
    {
        return $this->decodeAsAssociative;
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Location');
    }
}
