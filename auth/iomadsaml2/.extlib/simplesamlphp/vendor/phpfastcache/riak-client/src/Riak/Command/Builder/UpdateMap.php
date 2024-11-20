<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class UpdateMap extends Command\Builder implements Command\BuilderInterface
{
    use LocationTrait;

    protected $remove = [];

    /**
     * @var array
     */
    protected $registers = [];

    /**
     * @var array
     */
    protected $flags = [];

    /**
     * @var IncrementCounter[]
     */
    protected $counters = [];

    /**
     * @var UpdateSet[]
     */
    protected $sets = [];

    /**
     * @var UpdateMap[]
     */
    protected $maps = [];

    /**
     * Similar to Vector Clocks, the context allows us to determine the state of a CRDT Set
     *
     * @var string
     */
    protected $context = '';

    public function removeRegister($key)
    {
        $this->remove($key, Riak\DataType\Map::REGISTER);

        return $this;
    }

    protected function remove($key, $type)
    {
        $this->remove[] = sprintf('%s_%s', $key, $type);
    }

    public function removeFlag($key)
    {
        $this->remove($key, Riak\DataType\Map::FLAG);

        return $this;
    }

    public function removeCounter($key)
    {
        $this->remove($key, Riak\DataType\Counter::TYPE);

        return $this;
    }

    public function removeSet($key)
    {
        $this->remove($key, Riak\DataType\Set::TYPE);

        return $this;
    }

    public function removeMap($key)
    {
        $this->remove($key, Riak\DataType\Map::TYPE);

        return $this;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function updateRegister($key, $value)
    {
        $this->update($key, Riak\DataType\Map::REGISTER, $value);

        return $this;
    }

    protected function update($key, $type, $value)
    {
        $property = "{$type}s";
        $this->{$property}[sprintf('%s_%s', $key, $type)] = $value;
    }

    /**
     * @param $key
     * @param bool $state
     *
     * @return $this
     */
    public function updateFlag($key, $state = TRUE)
    {
        $this->update($key, Riak\DataType\Map::FLAG, $state);

        return $this;
    }

    /**
     * @param $key
     * @param IncrementCounter $builder
     *
     * @return $this
     */
    public function updateCounter($key, IncrementCounter $builder)
    {
        $this->update($key, Riak\DataType\Counter::TYPE, $builder);

        return $this;
    }

    /**
     * @param $key
     * @param UpdateSet $builder
     *
     * @return $this
     */
    public function updateSet($key, UpdateSet $builder)
    {
        $this->update($key, Riak\DataType\Set::TYPE, $builder);

        return $this;
    }

    /**
     * @param $key
     * @param UpdateMap $builder
     *
     * @return $this
     */
    public function updateMap($key, UpdateMap $builder)
    {
        $this->update($key, Riak\DataType\Map::TYPE, $builder);

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
     * {@inheritdoc}
     *
     * @return Command\DataType\Map\Store
     */
    public function build()
    {
        $this->validate();

        return new Command\DataType\Map\Store($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Bucket');

        $count_remove = count($this->remove);
        $count_registers = count($this->registers);
        $count_flags = count($this->flags);
        $count_counters = count($this->counters);
        $count_sets = count($this->sets);
        $count_maps = count($this->maps);

        if ($count_remove + $count_registers + $count_flags + $count_counters + $count_sets + $count_maps < 1) {
            throw new Exception('At least one add, remove, or update operation needs to be defined.');
        }

        if ($count_remove) {
            $this->required('Location');
            $this->required('Context');
        }

        // if we are performing a remove on a nested set, Location and context are required
        if ($count_sets) {
            foreach ($this->sets as $set) {
                if (count($set->getRemoveAll())) {
                    $this->required('Location');
                    $this->required('Context');
                    break;
                }
            }
        }


        // if we are performing a remove, Location and context are required
        if ($count_remove) {
            $this->required('Location');
            $this->required('Context');
        }
    }

    public function getRemove()
    {
        return $this->remove;
    }

    /**
     * @return array
     */
    public function getRegisters()
    {
        return $this->registers;
    }

    /**
     * @return array
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * @return IncrementCounter[]
     */
    public function getCounters()
    {
        return $this->counters;
    }

    /**
     * @return UpdateSet[]
     */
    public function getSets()
    {
        return $this->sets;
    }

    /**
     * @return UpdateMap[]
     */
    public function getMaps()
    {
        return $this->maps;
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
}
