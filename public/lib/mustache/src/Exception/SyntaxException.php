<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2010-2025 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mustache\Exception;

use Mustache\Exception;

/**
 * Mustache syntax exception.
 */
class SyntaxException extends LogicException implements Exception
{
    protected $token;

    /**
     * @param string    $msg
     * @param Exception $previous
     */
    public function __construct($msg, array $token, $previous = null)
    {
        $this->token = $token;
        parent::__construct($msg, 0, $previous);
    }

    /**
     * @return array
     */
    public function getToken()
    {
        return $this->token;
    }
}
