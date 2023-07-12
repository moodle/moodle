<?php

namespace Basho\Riak\Command\Builder\MapReduce;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Builds the command to fetch a collection of objects from Riak using Yokozuna search
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class FetchObjects extends Command\Builder implements Command\BuilderInterface
{
    /**
     * MR inputs used by query phase
     *
     * @var array|string
     */
    protected $inputs = [];

    /**
     * MR Query phases
     *  - options include map, reduce or link
     *
     * @var array
     */
    protected $query = [];

    /**
     * Timeout for MR query
     *
     * @var int
     */
    protected $timeout = 0;

    /**
     * {@inheritdoc}
     *
     * @return Command\MapReduce\Fetch;
     */
    public function build()
    {
        $this->validate();

        if (is_array($this->inputs) && count($this->inputs) == 1) {
            $this->inputs = $this->inputs[0];
        }

        return new Command\MapReduce\Fetch($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Inputs');
        $this->required('Query');
    }

    /**
     * addBucketInput
     *
     * @param Riak\Bucket $bucket
     *
     * @return $this
     */
    public function addBucketInput(Riak\Bucket $bucket)
    {
        // default bucket type cannot be passed to the MR api due to a bug
        if ($bucket->getType() == 'default') {
            $input = $bucket->getName();
        } else {
            $input = [$bucket->getType(), $bucket->getName()];
        }

        $this->inputs[] = $input;

        return $this;
    }

    /**
     * addLocationInput
     *
     * @param Riak\Location $location
     *
     * @return $this
     */
    public function addLocationInput(Riak\Location $location)
    {
        // default bucket type cannot be passed to the MR api due to a bug
        if ($location->getBucket()->getType() == 'default') {
            $input = [$location->getBucket()->getName(), $location->getKey()];
        } else {
            $input = [$location->getBucket()->getName(), $location->getKey(), '', $location->getBucket()->getType()];
        }

        $this->inputs[] = $input;

        return $this;
    }

    /**
     * withInput
     *
     * Sets a single input value as a string. Can be:
     *  - a bucket with the default bucket type
     *  - a 2i index name
     *  - a Search/Yokozuna index name
     *
     * @param $input
     *
     * @return $this
     */
    public function withInput($input)
    {
        $this->inputs = $input;

        return $this;
    }

    /**
     * withInputs
     *
     * Sets an array of inputs for the MR request
     *
     * @param array $inputs
     *
     * @return $this
     */
    public function withInputs(array $inputs)
    {
        $this->inputs = $inputs;

        return $this;
    }

    public function getInputs()
    {
        return $this->inputs;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * buildMapPhase
     *
     * Allows you to define the Map Query Phase by using parameters
     *
     * @param string $bucket
     * @param string $key
     * @param string $source
     * @param string $language
     * @param bool $keep
     *
     * @return $this
     */
    public function buildMapPhase($bucket = '', $key = '', $source = '', $language = 'javascript', $keep = false)
    {
        $this->setPhase('map', $this->assemblePhaseData($bucket, $key, $source, $language), $keep);

        return $this;
    }

    /**
     * setPhase
     *
     * @param $type
     * @param $data
     * @param bool $keep
     */
    protected function setPhase($type, $data, $keep = false)
    {
        $data['keep'] = $keep;
        $object = new \StdClass();
        $object->{$type} = $data;
        $this->query[] = $object;
    }

    /**
     * assemblePhaseData
     *
     * Assembles the parameters into a data structure to define a Query Phase
     *
     * @param string $bucket
     * @param string $key
     * @param string $source
     * @param string $language
     * @param string $tag
     *
     * @return array
     */
    protected function assemblePhaseData($bucket = '', $key = '', $source = '', $language = '', $tag = '')
    {
        $data = [];

        if ($language) {
            $data['language'] = $language;
        }

        if ($bucket) {
            $data['bucket'] = $bucket;
        }

        if ($key) {
            $data['key'] = $key;
        }

        if ($source) {
            $data['source'] = $source;
        }

        if ($tag) {
            $data['tag'] = $tag;
        }

        return $data;
    }

    /**
     * buildReducePhase
     *
     * Allows you to define the Reduce Query Phase by using parameters
     *
     * @param string $bucket
     * @param string $key
     * @param string $source
     * @param string $language
     * @param bool $keep
     *
     * @return $this
     */
    public function buildReducePhase($bucket = '', $key = '', $source = '', $language = 'javascript', $keep = true)
    {
        $this->setPhase('reduce', $this->assemblePhaseData($bucket, $key, $source, $language), $keep);

        return $this;
    }

    /**
     * buildReducePhase
     *
     * Allows you to define the Reduce Query Phase by using parameters
     *
     * @param string $bucket
     * @param string $tag
     * @param bool $keep
     *
     * @internal param string $key
     * @internal param string $source
     * @internal param string $language
     * @return $this
     */
    public function buildLinkPhase($bucket = '', $tag = '', $keep = false)
    {
        $this->setPhase('link', $this->assemblePhaseData($bucket, '', '', '', $tag), $keep);

        return $this;
    }

    /**
     * withQuery
     *
     * Sets the Query Phase/s for the MR request. This value gets converted directly into a JSON array and passed to Riak
     *
     * @param array $query
     *
     * @return $this
     */
    public function withQuery(array $query)
    {
        $this->query = $query;

        return $this;
    }
}
