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
 * Unknown template exception.
 */
class UnknownTemplateException extends InvalidArgumentException implements Exception
{
    protected $templateName;

    /**
     * @param string    $templateName
     * @param Exception $previous
     */
    public function __construct($templateName, $previous = null)
    {
        $this->templateName = $templateName;
        $message = sprintf('Unknown template: %s', $templateName);
        parent::__construct($message, 0, $previous);
    }

    public function getTemplateName()
    {
        return $this->templateName;
    }
}
