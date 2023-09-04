<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * @author Luke Bakken <lbakken@basho.com>
 */
class UpdateGSet extends Command\Builder implements Command\BuilderInterface
{
    use LocationTrait;

    /**
     * @var array
     */
    protected $add_all = [];

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
     * @return array
     */
    public function getAddAll()
    {
        return $this->add_all;
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\DataType\GSet\Store
     */
    public function build()
    {
        $this->validate();

        return new Command\DataType\GSet\Store($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Bucket');

        $count_add = count($this->add_all);

        if ($count_add < 1) {
            throw new Exception('At least one element to add needs to be defined.');
        }
    }
}
