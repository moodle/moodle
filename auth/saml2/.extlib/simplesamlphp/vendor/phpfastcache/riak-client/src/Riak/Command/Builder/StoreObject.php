<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Used to increment counter objects in Riak by the provided positive / negative integer
 *
 * <code>
 * $command = (new Command\Builder\StoreObject($riak))
 *   ->buildObject('{"firstName":"John","lastName":"Doe","email":"johndoe@gmail.com"}')
 *   ->buildBucket('users')
 *   ->build();
 *
 * $response = $command->execute();
 *
 * $user_location = $response->getLocation();
 * </code>
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class StoreObject extends Command\Builder implements Command\BuilderInterface
{
    use ObjectTrait;
    use LocationTrait;

    /**
     * @var bool
     */
    protected $decodeAsAssociative = false;

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
     *
     * @return Command\KVObject\Store
     */
    public function build()
    {
        $this->validate();

        return new Command\KVObject\Store($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Bucket');
        $this->required('DataObject');
    }
}
