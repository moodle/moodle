<?php

namespace Gettext\Generators;

use Gettext\Translations;

class Po extends Generator implements GeneratorInterface
{
    public static $options = [
        'noLocation' => false,
    ];

    /**
     * {@parentDoc}.
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $options += static::$options;

        $pluralForm = $translations->getPluralForms();
        $pluralSize = is_array($pluralForm) ? ($pluralForm[0] - 1) : null;
        $lines = ['msgid ""', 'msgstr ""'];

        foreach ($translations->getHeaders() as $name => $value) {
            $lines[] = sprintf('"%s: %s\\n"', $name, $value);
        }

        $lines[] = '';

        //Translations
        foreach ($translations as $translation) {
            if ($translation->hasComments()) {
                foreach ($translation->getComments() as $comment) {
                    $lines[] = '# '.$comment;
                }
            }

            if ($translation->hasExtractedComments()) {
                foreach ($translation->getExtractedComments() as $comment) {
                    $lines[] = '#. '.$comment;
                }
            }

            if (!$options['noLocation'] && $translation->hasReferences()) {
                foreach ($translation->getReferences() as $reference) {
                    $lines[] = '#: '.$reference[0].(!is_null($reference[1]) ? ':'.$reference[1] : null);
                }
            }

            if ($translation->hasFlags()) {
                $lines[] = '#, '.implode(',', $translation->getFlags());
            }

            $prefix = $translation->isDisabled() ? '#~ ' : '';

            if ($translation->hasContext()) {
                $lines[] = $prefix.'msgctxt '.static::convertString($translation->getContext());
            }

            static::addLines($lines, $prefix.'msgid', $translation->getOriginal());

            if ($translation->hasPlural()) {
                static::addLines($lines, $prefix.'msgid_plural', $translation->getPlural());
                static::addLines($lines, $prefix.'msgstr[0]', $translation->getTranslation());

                foreach ($translation->getPluralTranslations($pluralSize) as $k => $v) {
                    static::addLines($lines, $prefix.'msgstr['.($k + 1).']', $v);
                }
            } else {
                static::addLines($lines, $prefix.'msgstr', $translation->getTranslation());
            }

            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    /**
     * Escapes and adds double quotes to a string.
     *
     * @param string $string
     *
     * @return string
     */
    protected static function multilineQuote($string)
    {
        $lines = explode("\n", $string);
        $last = count($lines) - 1;

        foreach ($lines as $k => $line) {
            if ($k === $last) {
                $lines[$k] = static::convertString($line);
            } else {
                $lines[$k] = static::convertString($line."\n");
            }
        }

        return $lines;
    }

    /**
     * Add one or more lines depending whether the string is multiline or not.
     *
     * @param array  &$lines
     * @param string $name
     * @param string $value
     */
    protected static function addLines(array &$lines, $name, $value)
    {
        $newLines = static::multilineQuote($value);

        if (count($newLines) === 1) {
            $lines[] = $name.' '.$newLines[0];
        } else {
            $lines[] = $name.' ""';

            foreach ($newLines as $line) {
                $lines[] = $line;
            }
        }
    }

    /**
     * Convert a string to its PO representation.
     *
     * @param string $value
     *
     * @return string
     */
    public static function convertString($value)
    {
        return '"'.strtr(
            $value,
            [
                "\x00" => '',
                '\\' => '\\\\',
                "\t" => '\t',
                "\r" => '\r',
                "\n" => '\n',
                '"' => '\\"',
            ]
        ).'"';
    }
}
