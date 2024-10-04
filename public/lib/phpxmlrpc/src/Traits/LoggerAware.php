<?php

namespace PhpXmlRpc\Traits;

use PhpXmlRpc\Helper\Logger;

trait LoggerAware
{
    protected static $logger;

    public function getLogger()
    {
        if (self::$logger === null) {
            self::$logger = Logger::instance();
        }
        return self::$logger;
    }

    /**
     * @param $logger
     * @return void
     */
    public static function setLogger($logger)
    {
        self::$logger = $logger;
    }
}
