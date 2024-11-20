<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak\Command;

/**
 * Used to fetch hll objects from Riak
 *
 * <code>
 * $command = (new Command\Builder\FetchHll($riak))
 *   ->buildLocation($user_id, 'email_subscriptions', 'user_preferences')
 *   ->build();
 *
 * $response = $command->execute();
 *
 * $set = $response->getHll();
 * </code>
 *
 * @author Luke Bakken <lbakken@basho.com>
 */
class FetchHll extends Command\Builder implements Command\BuilderInterface
{
    use LocationTrait;

    /**
     * {@inheritdoc}
     *
     * @return Command\DataType\Hll\Fetch;
     */
    public function build()
    {
        $this->validate();

        return new Command\DataType\Hll\Fetch($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Location');
    }
}
