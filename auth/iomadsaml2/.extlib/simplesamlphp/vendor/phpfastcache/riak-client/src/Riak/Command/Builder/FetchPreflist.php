<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak\Command;

/**
 * Used to fetch map objects from Riak
 *
 * <code>
 * $command = (new Command\Builder\FetchPreflist($riak))
 *   ->buildLocation($order_id, 'online_orders', 'sales')
 *   ->build();
 *
 * $response = $command->execute();
 * </code>
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class FetchPreflist extends Command\Builder implements Command\BuilderInterface
{
    use LocationTrait;

    /**
     * {@inheritdoc}
     *
     * @return Command\KVObject\FetchPreflist;
     */
    public function build()
    {
        $this->validate();

        return new Command\KVObject\FetchPreflist($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Location');
    }
}
