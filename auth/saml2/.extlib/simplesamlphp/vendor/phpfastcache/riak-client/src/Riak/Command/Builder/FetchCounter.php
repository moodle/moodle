<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak\Command;

/**
 * Used to fetch counter objects from Riak
 *
 * <code>
 * $command = (new Command\Builder\FetchCounter($riak))
 *   ->buildLocation($user_name, 'user_visit_count', 'visit_counters')
 *   ->build();
 *
 * $response = $command->execute();
 *
 * $counter = $response->getCounter();
 * </code>
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class FetchCounter extends Command\Builder implements Command\BuilderInterface
{
    use LocationTrait;

    /**
     * {@inheritdoc}
     *
     * @return Command\DataType\Counter\Fetch;
     */
    public function build()
    {
        $this->validate();

        return new Command\DataType\Counter\Fetch($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Location');
    }
}
