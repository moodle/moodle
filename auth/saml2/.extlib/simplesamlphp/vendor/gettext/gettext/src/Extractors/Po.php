<?php

namespace Gettext\Extractors;

use Gettext\Translations;
use Gettext\Translation;
use Gettext\Utils\HeadersExtractorTrait;

/**
 * Class to get gettext strings from php files returning arrays.
 */
class Po extends Extractor implements ExtractorInterface
{
    use HeadersExtractorTrait;

    /**
     * Parses a .po file and append the translations found in the Translations instance.
     *
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        $lines = explode("\n", $string);
        $i = 0;

        $translation = $translations->createNewTranslation('', '');

        for ($n = count($lines); $i < $n; ++$i) {
            $line = trim($lines[$i]);
            $line = static::fixMultiLines($line, $lines, $i);

            if ($line === '') {
                if ($translation->is('', '')) {
                    static::extractHeaders($translation->getTranslation(), $translations);
                } elseif ($translation->hasOriginal()) {
                    $translations[] = $translation;
                }

                $translation = $translations->createNewTranslation('', '');
                continue;
            }

            $splitLine = preg_split('/\s+/', $line, 2);
            $key = $splitLine[0];
            $data = isset($splitLine[1]) ? $splitLine[1] : '';

            if ($key === '#~') {
                $translation->setDisabled(true);

                $splitLine = preg_split('/\s+/', $data, 2);
                $key = $splitLine[0];
                $data = isset($splitLine[1]) ? $splitLine[1] : '';
            }

            switch ($key) {
                case '#':
                    $translation->addComment($data);
                    $append = null;
                    break;

                case '#.':
                    $translation->addExtractedComment($data);
                    $append = null;
                    break;

                case '#,':
                    foreach (array_map('trim', explode(',', trim($data))) as $value) {
                        $translation->addFlag($value);
                    }
                    $append = null;
                    break;

                case '#:':
                    foreach (preg_split('/\s+/', trim($data)) as $value) {
                        if (preg_match('/^(.+)(:(\d*))?$/U', $value, $matches)) {
                            $translation->addReference($matches[1], isset($matches[3]) ? $matches[3] : null);
                        }
                    }
                    $append = null;
                    break;

                case 'msgctxt':
                    $translation = $translation->getClone(static::convertString($data));
                    $append = 'Context';
                    break;

                case 'msgid':
                    $translation = $translation->getClone(null, static::convertString($data));
                    $append = 'Original';
                    break;

                case 'msgid_plural':
                    $translation->setPlural(static::convertString($data));
                    $append = 'Plural';
                    break;

                case 'msgstr':
                case 'msgstr[0]':
                    $translation->setTranslation(static::convertString($data));
                    $append = 'Translation';
                    break;

                case 'msgstr[1]':
                    $translation->setPluralTranslations([static::convertString($data)]);
                    $append = 'PluralTranslation';
                    break;

                default:
                    if (strpos($key, 'msgstr[') === 0) {
                        $p = $translation->getPluralTranslations();
                        $p[] = static::convertString($data);

                        $translation->setPluralTranslations($p);
                        $append = 'PluralTranslation';
                        break;
                    }

                    if (isset($append)) {
                        if ($append === 'Context') {
                            $translation = $translation->getClone($translation->getContext()
                                ."\n"
                                .static::convertString($data));
                            break;
                        }

                        if ($append === 'Original') {
                            $translation = $translation->getClone(null, $translation->getOriginal()
                                ."\n"
                                .static::convertString($data));
                            break;
                        }

                        if ($append === 'PluralTranslation') {
                            $p = $translation->getPluralTranslations();
                            $p[] = array_pop($p)."\n".static::convertString($data);
                            $translation->setPluralTranslations($p);
                            break;
                        }

                        $getMethod = 'get'.$append;
                        $setMethod = 'set'.$append;
                        $translation->$setMethod($translation->$getMethod()."\n".static::convertString($data));
                    }
                    break;
            }
        }

        if ($translation->hasOriginal() && !in_array($translation, iterator_to_array($translations))) {
            $translations[] = $translation;
        }
    }

    /**
     * Gets one string from multiline strings.
     *
     * @param string $line
     * @param array  $lines
     * @param int    &$i
     *
     * @return string
     */
    protected static function fixMultiLines($line, array $lines, &$i)
    {
        for ($j = $i, $t = count($lines); $j < $t; ++$j) {
            if (substr($line, -1, 1) == '"' && isset($lines[$j + 1])) {
                $nextLine = trim($lines[$j + 1]);
                if (substr($nextLine, 0, 1) == '"') {
                    $line = substr($line, 0, -1).substr($nextLine, 1);
                    continue;
                }
                if (substr($nextLine, 0, 4) == '#~ "') {
                    $line = substr($line, 0, -1).substr($nextLine, 4);
                    continue;
                }
            }
            $i = $j;
            break;
        }

        return $line;
    }

    /**
     * Convert a string from its PO representation.
     *
     * @param string $value
     *
     * @return string
     */
    public static function convertString($value)
    {
        if (!$value) {
            return '';
        }

        if ($value[0] === '"') {
            $value = substr($value, 1, -1);
        }

        return strtr(
            $value,
            [
                '\\\\' => '\\',
                '\\a' => "\x07",
                '\\b' => "\x08",
                '\\t' => "\t",
                '\\n' => "\n",
                '\\v' => "\x0b",
                '\\f' => "\x0c",
                '\\r' => "\r",
                '\\"' => '"',
            ]
        );
    }
}
