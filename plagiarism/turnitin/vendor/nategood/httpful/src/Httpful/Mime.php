<?php

namespace Httpful;

/**
 * Class to organize the Mime stuff a bit more
 * @author Nate Good <me@nategood.com>
 */
class Mime
{
    const JSON    = 'application/json';
    const XML     = 'application/xml';
    const XHTML   = 'application/html+xml';
    const FORM    = 'application/x-www-form-urlencoded';
    const UPLOAD  = 'multipart/form-data';
    const PLAIN   = 'text/plain';
    const JS      = 'text/javascript';
    const HTML    = 'text/html';
    const YAML    = 'application/x-yaml';
    const CSV     = 'text/csv';

    /**
     * Map short name for a mime type
     * to a full proper mime type
     */
    public static $mimes = array(
        'json'      => self::JSON,
        'xml'       => self::XML,
        'form'      => self::FORM,
        'plain'     => self::PLAIN,
        'text'      => self::PLAIN,
        'upload'      => self::UPLOAD,
        'html'      => self::HTML,
        'xhtml'     => self::XHTML,
        'js'        => self::JS,
        'javascript'=> self::JS,
        'yaml'      => self::YAML,
        'csv'       => self::CSV,
    );

    /**
     * Get the full Mime Type name from a "short name".
     * Returns the short if no mapping was found.
     * @param string $short_name common name for mime type (e.g. json)
     * @return string full mime type (e.g. application/json)
     */
    public static function getFullMime($short_name)
    {
        return array_key_exists($short_name, self::$mimes) ? self::$mimes[$short_name] : $short_name;
    }

    /**
     * @param string $short_name
     * @return bool
     */
    public static function supportsMimeType($short_name)
    {
        return array_key_exists($short_name, self::$mimes);
    }
}
