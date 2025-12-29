<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2010-2025 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mustache;

/**
 * A class representing a rendered string in Mustache.
 *
 * This is primarily used to prevent re-rendering of strings that have already
 * been processed in higher-order sections.
 *
 * @see LambdaHelper::render()
 * @see LambdaHelper::preventRender()
 */
class RenderedString
{
    private $value;

    /**
     * RenderedString constructor.
     *
     * @param string $value The rendered string value
     */
    public function __construct($value)
    {
        $this->value = (string) $value;
    }

    public function __toString()
    {
        return $this->value;
    }

    /**
     * Get the rendered string value.
     *
     * @return string The rendered string value
     */
    public function getValue()
    {
        return $this->value;
    }
}
