<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak\Command;

/**
 * Used to fetch set objects from Riak
 *
 * <code>
 * $command = (new Command\Builder\FetchSet($riak))
 *   ->buildLocation($user_id, 'email_subscriptions', 'user_preferences')
 *   ->build();
 *
 * $response = $command->execute();
 *
 * $set = $response->getSet();
 * </code>
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class FetchSet extends Command\Builder implements Command\BuilderInterface
{
    use LocationTrait;

    /**
     * {@inheritdoc}
     *
     * @return Command\DataType\Set\Fetch;
     */
    public function build()
    {
        $this->validate();

        return new Command\DataType\Set\Fetch($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Location');
    }
}
