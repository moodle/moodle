<?php

declare(strict_types=1);

namespace DI\Attribute;

use Attribute;

/**
 * "Injectable" attribute.
 *
 * Marks a class as injectable
 *
 * @api
 *
 * @author Domenic Muskulus <domenic@muskulus.eu>
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Injectable
{
    /**
     * @param bool|null $lazy Should the object be lazy-loaded.
     */
    public function __construct(
        private ?bool $lazy = null,
    ) {
    }

    public function isLazy() : ?bool
    {
        return $this->lazy;
    }
}
