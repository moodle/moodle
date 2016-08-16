<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2010-2015 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mustache Cache in-memory implementation.
 *
 * The in-memory cache is used for uncached lambda section templates. It's also useful during development, but is not
 * recommended for production use.
 */
class Mustache_Cache_NoopCache extends Mustache_Cache_AbstractCache
{
    /**
     * Loads nothing. Move along.
     *
     * @param string $key
     *
     * @return bool
     */
    public function load($key)
    {
        return false;
    }

    /**
     * Loads the compiled Mustache Template class without caching.
     *
     * @param string $key
     * @param string $value
     */
    public function cache($key, $value)
    {
        $this->log(
            Mustache_Logger::WARNING,
            'Template cache disabled, evaluating "{className}" class at runtime',
            array('className' => $key)
        );
        eval('?>' . $value);
    }
}
