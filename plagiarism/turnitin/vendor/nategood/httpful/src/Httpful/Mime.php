<?php

namespace Httpful;

/**
 * Class to organize the Mime stuff a bit more
 * @author Nate Good <me@nategood.com>
 */
class Mime
{
    public const JSON    = 'application/json';
    public const XML     = 'application/xml';
    public const XHTML   = 'application/html+xml';
    public const FORM    = 'application/x-www-form-urlencoded';
    public const UPLOAD  = 'multipart/form-data';
    public const PLAIN   = 'text/plain';
    public const JS      = 'text/javascript';
    public const HTML    = 'text/html';
    public const YAML    = 'application/x-yaml';
    public const CSV     = 'text/csv';

    /**
     * Map short name for a mime type
     * to a full proper mime type
     */
    public static $mimes = [
        'json'      => self::JSON,
        'xml'       => self::XML,
        'form'      => self::FORM,
        'plain'     => self::PLAIN,
        'text'      => self::PLAIN,
        'upload'    => self::UPLOAD,
        'html'      => self::HTML,
        'xhtml'     => self::XHTML,
        'js'        => self::JS,
        'javascript'=> self::JS,
        'yaml'      => self::YAML,
        'csv'       => self::CSV,
    ];

    /**
     * Get the full Mime Type name from a "short name".
     * Returns the short if no mapping was found.
     * @param string $short_name common name for mime type (e.g. json)
     * @return string full mime type (e.g. application/json)
     */
    public static function getFullMime(string $short_name): string
    {        
        return self::$mimes[$short_name] ?? $short_name;
    }

    /**
     * @param string $short_name
     * @return bool
     */
    public static function supportsMimeType(string $short_name): bool
    {
        return array_key_exists($short_name, self::$mimes);
    }
}
