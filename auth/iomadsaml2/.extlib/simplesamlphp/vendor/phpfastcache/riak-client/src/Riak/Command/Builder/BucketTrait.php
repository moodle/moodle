<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak\Bucket;

/**
 * Allows easy code sharing for Bucket getters / setters within the Command Builders
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
trait BucketTrait
{
    /**
     * Stores the Bucket object
     *
     * @var Bucket|null
     */
    protected $bucket = NULL;

    /**
     * Gets the Bucket object
     *
     * @return Bucket|null
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * Build a Bucket object to be added to the Command
     *
     * @param $name
     * @param string $type
     *
     * @return $this
     */
    public function buildBucket($name, $type = 'default')
    {
        $this->bucket = new Bucket($name, $type);

        return $this;
    }

    /**
     * Attach the provided Bucket to the Command
     *
     * @param Bucket $bucket
     *
     * @return $this
     */
    public function inBucket(Bucket $bucket)
    {
        $this->bucket = $bucket;

        return $this;
    }
}
