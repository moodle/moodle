<?php

declare(strict_types=1);

namespace SAML2\Certificate;

use SAML2\Utilities\ArrayCollection;

use Webmozart\Assert\Assert;

/**
 * Simple collection object for transporting keys
 */
class KeyCollection extends ArrayCollection
{
    /**
     * Add a key to the collection
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     * @param \SAML2\Certificate\Key $key
     * @return void
     *
     * Type hint not possible due to upstream method signature
     */
    public function add($key) : void
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        Assert::isInstanceOf($key, Key::class);
        parent::add($key);
    }
}
