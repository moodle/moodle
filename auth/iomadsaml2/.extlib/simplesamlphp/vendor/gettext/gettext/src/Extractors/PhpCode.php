<?php

namespace Gettext\Extractors;

use Exception;
use Gettext\Translations;
use Gettext\Utils\FunctionsScanner;

/**
 * Class to get gettext strings from php files returning arrays.
 */
class PhpCode extends Extractor implements ExtractorInterface, ExtractorMultiInterface
{
    public static $options = [
        // - false: to not extract comments
        // - empty string: to extract all comments
        // - non-empty string: to extract comments that start with that string
        // - array with strings to extract comments format.
        'extractComments' => false,

        'constants' => [],

        'functions' => [
            'gettext' => 'gettext',
            '__' => 'gettext',
            'ngettext' => 'ngettext',
            'n__' => 'ngettext',
            'pgettext' => 'pgettext',
            'p__' => 'pgettext',
            'dgettext' => 'dgettext',
            'd__' => 'dgettext',
            'dngettext' => 'dngettext',
            'dn__' => 'dngettext',
            'dpgettext' => 'dpgettext',
            'dp__' => 'dpgettext',
            'npgettext' => 'npgettext',
            'np__' => 'npgettext',
            'dnpgettext' => 'dnpgettext',
            'dnp__' => 'dnpgettext',
            'noop' => 'noop',
            'noop__' => 'noop',
        ],
    ];

    protected static $functionsScannerClass = 'Gettext\Utils\PhpFunctionsScanner';

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        static::fromStringMultiple($string, [$translations], $options);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function fromStringMultiple($string, array $translations, array $options = [])
    {
        $options += static::$options;

        /** @var FunctionsScanner $functions */
        $functions = new static::$functionsScannerClass($string);

        if ($options['extractComments'] !== false) {
            $functions->enableCommentsExtraction($options['extractComments']);
        }

        $functions->saveGettextFunctions($translations, $options);
    }

    /**
     * @inheritDoc
     */
    public static function fromFileMultiple($file, array $translations, array $options = [])
    {
        foreach (static::getFiles($file) as $file) {
            $options['file'] = $file;
            static::fromStringMultiple(static::readFile($file), $translations, $options);
        }
    }


    /**
     * Decodes a T_CONSTANT_ENCAPSED_STRING string.
     *
     * @param string $value
     *
     * @return string
     */
    public static function convertString($value)
    {
        if (strpos($value, '\\') === false) {
            return substr($value, 1, -1);
        }

        if ($value[0] === "'") {
            return strtr(substr($value, 1, -1), ['\\\\' => '\\', '\\\'' => '\'']);
        }

        $value = substr($value, 1, -1);

        return preg_replace_callback(
            '/\\\(n|r|t|v|e|f|\$|"|\\\|x[0-9A-Fa-f]{1,2}|u{[0-9a-f]{1,6}}|[0-7]{1,3})/',
            function ($match) {
                switch ($match[1][0]) {
                    case 'n':
                        return "\n";
                    case 'r':
                        return "\r";
                    case 't':
                        return "\t";
                    case 'v':
                        return "\v";
                    case 'e':
                        return "\e";
                    case 'f':
                        return "\f";
                    case '$':
                        return '$';
                    case '"':
                        return '"';
                    case '\\':
                        return '\\';
                    case 'x':
                        return chr(hexdec(substr($match[1], 1)));
                    case 'u':
                        return static::unicodeChar(hexdec(substr($match[1], 1)));
                    default:
                        return chr(octdec($match[1]));
                }
            },
            $value
        );
    }

    /**
     * @param $dec
     * @return string|null
     * @see http://php.net/manual/en/function.chr.php#118804
     */
    protected static function unicodeChar($dec)
    {
        if ($dec < 0x80) {
            return chr($dec);
        }

        if ($dec < 0x0800) {
            return chr(0xC0 + ($dec >> 6))
                . chr(0x80 + ($dec & 0x3f));
        }

        if ($dec < 0x010000) {
            return chr(0xE0 + ($dec >> 12))
                . chr(0x80 + (($dec >> 6) & 0x3f))
                . chr(0x80 + ($dec & 0x3f));
        }

        if ($dec < 0x200000) {
            return chr(0xF0 + ($dec >> 18))
                . chr(0x80 + (($dec >> 12) & 0x3f))
                . chr(0x80 + (($dec >> 6) & 0x3f))
                . chr(0x80 + ($dec & 0x3f));
        }

        return null;
    }
}
