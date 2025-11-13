<?php

namespace Httpful;

class Httpful {
    public const VERSION = '1.0.0';

    private static $mimeRegistrar = [];
    private static $default = null;

    /**
     * @param string $mimeType
     * @param \Httpful\Handlers\MimeHandlerAdapter $handler
     */
    public static function register($mimeType, \Httpful\Handlers\MimeHandlerAdapter $handler)
    {
        self::$mimeRegistrar[$mimeType] = $handler;
    }

    /**
     * @param string $mimeType defaults to MimeHandlerAdapter
     * @return \Httpful\Handlers\MimeHandlerAdapter
     */
    public static function get($mimeType = null)
    {
        if (isset(self::$mimeRegistrar[$mimeType])) {
            return self::$mimeRegistrar[$mimeType];
        }

        if (empty(self::$default)) {
            self::$default = new \Httpful\Handlers\MimeHandlerAdapter();
        }

        return self::$default;
    }

    /**
     * Does this particular Mime Type have a parser registered
     * for it?
     * @param string $mimeType
     * @return bool
     */
    public static function hasParserRegistered($mimeType): bool
    {
        return isset(self::$mimeRegistrar[$mimeType]);
    }
}
