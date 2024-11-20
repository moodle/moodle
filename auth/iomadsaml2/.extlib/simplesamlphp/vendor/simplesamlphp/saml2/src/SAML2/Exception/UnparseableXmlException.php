<?php

declare(strict_types=1);

namespace SAML2\Exception;

use LibXMLError;

final class UnparseableXmlException extends RuntimeException
{
    /** @var array */
    private static $levelMap = [
        LIBXML_ERR_WARNING => 'WARNING',
        LIBXML_ERR_ERROR   => 'ERROR',
        LIBXML_ERR_FATAL   => 'FATAL'
    ];


    /**
     * Constructor for UnparseableXmlException
     *
     * @param \LibXMLError $error
     */
    public function __construct(LibXMLError $error)
    {
        $message = sprintf(
            'Unable to parse XML - "%s[%d]": "%s" in "%s" at line %d on column %d"',
            self::$levelMap[$error->level],
            $error->code,
            $error->message,
            $error->file ?: '(string)',
            $error->line,
            $error->column
        );

        parent::__construct($message);
    }
}
