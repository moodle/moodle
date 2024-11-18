<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Used to list KV objects in Riak
 *
 * Not recommended for production use.
 * This command traverses all the keys stored in the cluster and should not be used in production.
 *
 * <code>
 * $command = (new Command\Builder\ListObjects($riak))
 *   ->buildBucket('users', 'default')
 *   ->acknowledgeRisk(true)
 *   ->build();
 *
 * $response = $command->execute();
 *
 * $data = $response->getKeys();
 * </code>
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class ListObjects extends Command\Builder implements Command\BuilderInterface
{
    use BucketTrait;
    use ObjectTrait;

    /**
     * @var bool
     */
    protected $decodeAsAssociative = false;
    protected $acknowledgedRisk = null;

    public function __construct(Riak $riak)
    {
        parent::__construct($riak);
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\KVObject\Keys\Fetch
     */
    public function build()
    {
        $this->validate();

        return new Command\KVObject\Keys\Fetch($this);
    }

    /**
     * ListKeys operations are dangerous in production environments and are highly discouraged.
     * This method is required in order to complete the operation.
     *
     * @return $this
     */
    public function acknowledgeRisk($acknowledgedRisk = false)
    {
        if ($acknowledgedRisk) {
            $this->acknowledgedRisk = $acknowledgedRisk;
        }
        return $this;
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
     * Fetch the setting for acknowledgedRisk.
     *
     * @return bool
     */
    public function getAcknowledgedRisk()
    {
        return $this->acknowledgedRisk;
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Bucket');
        $this->required('AcknowledgedRisk');
    }
}
