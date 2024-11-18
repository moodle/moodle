<?php

namespace Gettext\Languages\Exporter;

use Exception;

class Prettyjson extends Json
{
    /**
     * {@inheritdoc}
     *
     * @see \Gettext\Languages\Exporter\Exporter::getDescription()
     */
    public static function getDescription()
    {
        return 'Build an uncompressed JSON-encoded file (PHP 5.4 or later is needed)';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Gettext\Languages\Exporter\Json::getEncodeOptions()
     */
    protected static function getEncodeOptions()
    {
        if (!(defined('\JSON_PRETTY_PRINT') && defined('\JSON_UNESCAPED_SLASHES') && defined('\JSON_UNESCAPED_UNICODE'))) {
            throw new Exception('PHP 5.4 or later is required to export uncompressed JSON');
        }

        return \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE;
    }
}
