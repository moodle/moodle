<?php

namespace Gettext\Generators;

use Gettext\Translations;
use Gettext\Utils\HeadersGeneratorTrait;

class Mo extends Generator implements GeneratorInterface
{
    use HeadersGeneratorTrait;

    public static $options = [
        'includeHeaders' => true,
    ];

    /**
     * {@parentDoc}.
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $options += static::$options;
        $messages = [];

        if ($options['includeHeaders']) {
            $messages[''] = static::generateHeaders($translations);
        }

        foreach ($translations as $translation) {
            if (!$translation->hasTranslation() || $translation->isDisabled()) {
                continue;
            }

            if ($translation->hasContext()) {
                $originalString = $translation->getContext()."\x04".$translation->getOriginal();
            } else {
                $originalString = $translation->getOriginal();
            }

            $messages[$originalString] = $translation;
        }

        ksort($messages);
        $numEntries = count($messages);
        $originalsTable = '';
        $translationsTable = '';
        $originalsIndex = [];
        $translationsIndex = [];
        $pluralForm = $translations->getPluralForms();
        $pluralSize = is_array($pluralForm) ? ($pluralForm[0] - 1) : null;

        foreach ($messages as $originalString => $translation) {
            if (is_string($translation)) {
                // Headers
                $translationString = $translation;
            } else {
                /* @var $translation \Gettext\Translation */
                if ($translation->hasPlural() && $translation->hasPluralTranslations(true)) {
                    $originalString .= "\x00".$translation->getPlural();
                    $translationString = $translation->getTranslation();
                    $translationString .= "\x00".implode("\x00", $translation->getPluralTranslations($pluralSize));
                } else {
                    $translationString = $translation->getTranslation();
                }
            }

            $originalsIndex[] = [
                'relativeOffset' => strlen($originalsTable),
                'length' => strlen($originalString)
            ];
            $originalsTable .= $originalString."\x00";
            $translationsIndex[] = [
                'relativeOffset' => strlen($translationsTable),
                'length' => strlen($translationString)
            ];
            $translationsTable .= $translationString."\x00";
        }

        // Offset of table with the original strings index: right after the header (which is 7 words)
        $originalsIndexOffset = 7 * 4;

        // Size of table with the original strings index
        $originalsIndexSize = $numEntries * (4 + 4);

        // Offset of table with the translation strings index: right after the original strings index table
        $translationsIndexOffset = $originalsIndexOffset + $originalsIndexSize;

        // Size of table with the translation strings index
        $translationsIndexSize = $numEntries * (4 + 4);

        // Hashing table starts after the header and after the index table
        $originalsStringsOffset = $translationsIndexOffset + $translationsIndexSize;

        // Translations start after the keys
        $translationsStringsOffset = $originalsStringsOffset + strlen($originalsTable);

        // Let's generate the .mo file binary data
        $mo = '';

        // Magic number
        $mo .= pack('L', 0x950412de);

        // File format revision
        $mo .= pack('L', 0);

        // Number of strings
        $mo .= pack('L', $numEntries);

        // Offset of table with original strings
        $mo .= pack('L', $originalsIndexOffset);

        // Offset of table with translation strings
        $mo .= pack('L', $translationsIndexOffset);

        // Size of hashing table: we don't use it.
        $mo .= pack('L', 0);

        // Offset of hashing table: it would start right after the translations index table
        $mo .= pack('L', $translationsIndexOffset + $translationsIndexSize);

        // Write the lengths & offsets of the original strings
        foreach ($originalsIndex as $info) {
            $mo .= pack('L', $info['length']);
            $mo .= pack('L', $originalsStringsOffset + $info['relativeOffset']);
        }

        // Write the lengths & offsets of the translated strings
        foreach ($translationsIndex as $info) {
            $mo .= pack('L', $info['length']);
            $mo .= pack('L', $translationsStringsOffset + $info['relativeOffset']);
        }

        // Write original strings
        $mo .= $originalsTable;

        // Write translation strings
        $mo .= $translationsTable;

        return $mo;
    }
}
