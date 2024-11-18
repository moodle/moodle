<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class UpdateSet extends Command\Builder implements Command\BuilderInterface
{
    use LocationTrait;

    /**
     * @var array
     */
    protected $add_all = [];

    /**
     * @var array
     */
    protected $remove_all = [];

    /**
     * Similar to Vector Clocks, the context allows us to determine the state of a CRDT Set
     *
     * @var string
     */
    protected $context = '';

    /**
     * @param mixed $element
     *
     * @return $this
     */
    public function add($element)
    {
        settype($element, 'string');
        $this->add_all[] = $element;

        return $this;
    }

    /**
     * @param mixed $element
     *
     * @return $this
     */
    public function remove($element)
    {
        settype($element, 'string');
        $this->remove_all[] = $element;

        return $this;
    }

    /**
     * @param $context
     *
     * @return $this
     */
    public function withContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return array
     */
    public function getAddAll()
    {
        return $this->add_all;
    }

    /**
     * @return array
     */
    public function getRemoveAll()
    {
        return $this->remove_all;
    }

    /**
     * getContext
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\DataType\Set\Store
     */
    public function build()
    {
        $this->validate();

        return new Command\DataType\Set\Store($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Bucket');

        $count_add = count($this->add_all);
        $count_remove = count($this->remove_all);

        if ($count_add + $count_remove < 1) {
            throw new Exception('At least one element to add or remove needs to be defined.');
        }

        // if we are performing a remove, Location and context are required
        if ($count_remove) {
            $this->required('Location');
            $this->required('Context');
        }
    }
}
