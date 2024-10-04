<?php

namespace PhpXmlRpc\Traits;

use PhpXmlRpc\Helper\Charset;

trait CharsetEncoderAware
{
    protected static $charsetEncoder;

    public function getCharsetEncoder()
    {
        if (self::$charsetEncoder === null) {
            self::$charsetEncoder = Charset::instance();
        }
        return self::$charsetEncoder;
    }

    /**
     * @param $charsetEncoder
     * @return void
     */
    public static function setCharsetEncoder($charsetEncoder)
    {
        self::$charsetEncoder = $charsetEncoder;
    }
}
