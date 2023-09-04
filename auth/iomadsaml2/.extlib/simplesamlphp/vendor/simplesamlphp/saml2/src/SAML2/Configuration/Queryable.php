<?php

declare(strict_types=1);

namespace SAML2\Configuration;

/**
 * Interface \SAML2\Configuration\Queryable
 */
interface Queryable
{
    /**
     * Query for whether or not the configuration has a value for the key
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool;


    /**
     * Query to get the value in the configuration for the given key. If no value is present the default value is
     * returned
     *
     * @param string     $key
     * @param null|mixed $defaultValue
     *
     * @return mixed
     */
    public function get(string $key, $defaultValue = null);
}
