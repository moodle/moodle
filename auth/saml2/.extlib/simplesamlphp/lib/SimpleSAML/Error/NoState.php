<?php

declare(strict_types=1);

namespace SimpleSAML\Error;

/**
 * Exception which will show a page telling the user
 * that we don't know what to do.
 *
 * @package SimpleSAMLphp
 */

class NoState extends Error
{
    /**
     * Create the error
     */
    public function __construct()
    {
        $this->includeTemplate = 'core:no_state.tpl.php';
        parent::__construct('NOSTATE');
    }
}
