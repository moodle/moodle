<?php

declare(strict_types=1);

namespace Phpml\Exception;

class SerializeException extends \Exception
{
    /**
     * @param string $filepath
     *
     * @return SerializeException
     */
    public static function cantUnserialize(string $filepath)
    {
        return new self(sprintf('"%s" can not be unserialized.', $filepath));
    }

    /**
     * @param string $classname
     *
     * @return SerializeException
     */
    public static function cantSerialize(string $classname)
    {
        return new self(sprintf('Class "%s" can not be serialized.', $classname));
    }
}
