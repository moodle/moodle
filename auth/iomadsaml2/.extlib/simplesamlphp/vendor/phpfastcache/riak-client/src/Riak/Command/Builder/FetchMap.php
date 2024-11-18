<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak\Command;

/**
 * Used to fetch map objects from Riak
 *
 * <code>
 * $command = (new Command\Builder\FetchMap($riak))
 *   ->buildLocation($order_id, 'online_orders', 'sales_maps')
 *   ->build();
 *
 * $response = $command->execute();
 *
 * $map = $response->getMap();
 * </code>
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class FetchMap extends Command\Builder implements Command\BuilderInterface
{
    use LocationTrait;

    /**
     * {@inheritdoc}
     *
     * @return Command\DataType\Map\Fetch;
     */
    public function build()
    {
        $this->validate();

        return new Command\DataType\Map\Fetch($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Location');
    }
}
