<?php

namespace Gettext\Languages\Exporter;

use Exception;

class Po extends Exporter
{
    /**
     * {@inheritdoc}
     *
     * @see \Gettext\Languages\Exporter\Exporter::getDescription()
     */
    public static function getDescription()
    {
        return 'Build a string to be used for gettext .po files';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Gettext\Languages\Exporter\Exporter::toStringDo()
     */
    protected static function toStringDo($languages)
    {
        if (count($languages) !== 1) {
            throw new Exception('The ' . get_called_class() . ' exporter can only export one language');
        }
        $language = $languages[0];
        $lines = array();
        $lines[] = '"Language: ' . $language->id . '\n"';
        $lines[] = '"Plural-Forms: nplurals=' . count($language->categories) . '; plural=' . $language->formula . '\n"';
        $lines[] = '';

        return implode("\n", $lines);
    }
}
