<?php

namespace Gettext\Extractors;

use Gettext\Translations;

/**
 * Class to get gettext strings from json files.
 */
class Jed extends Extractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        static::extract(json_decode($string, true), $translations);
    }

    /**
     * Handle an array of translations and append to the Translations instance.
     *
     * @param array        $content
     * @param Translations $translations
     */
    public static function extract(array $content, Translations $translations)
    {
        $messages = current($content);
        $headers = isset($messages['']) ? $messages[''] : null;
        unset($messages['']);

        if (!empty($headers['domain'])) {
            $translations->setDomain($headers['domain']);
        }

        if (!empty($headers['lang'])) {
            $translations->setLanguage($headers['lang']);
        }

        if (!empty($headers['plural-forms'])) {
            $translations->setHeader(Translations::HEADER_PLURAL, $headers['plural-forms']);
        }

        $context_glue = '\u0004';

        foreach ($messages as $key => $translation) {
            $key = explode($context_glue, $key);
            $context = isset($key[1]) ? array_shift($key) : '';

            $translations->insert($context, array_shift($key))
                ->setTranslation(array_shift($translation))
                ->setPluralTranslations($translation);
        }
    }
}
