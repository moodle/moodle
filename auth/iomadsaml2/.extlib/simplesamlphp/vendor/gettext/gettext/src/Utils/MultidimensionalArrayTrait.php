<?php

namespace Gettext\Utils;

use Gettext\Translations;

/**
 * Trait used by all generators that exports the translations to multidimensional arrays
 * (context => [original => [translation, plural1, pluraln...]]).
 */
trait MultidimensionalArrayTrait
{
    use HeadersGeneratorTrait;
    use HeadersExtractorTrait;

    /**
     * Returns a multidimensional array.
     *
     * @param Translations $translations
     * @param bool         $includeHeaders
     * @param bool         $forceArray
     *
     * @return array
     */
    protected static function toArray(Translations $translations, $includeHeaders, $forceArray = false)
    {
        $pluralForm = $translations->getPluralForms();
        $pluralSize = is_array($pluralForm) ? ($pluralForm[0] - 1) : null;
        $messages = [];

        if ($includeHeaders) {
            $messages[''] = [
                '' => [static::generateHeaders($translations)],
            ];
        }

        foreach ($translations as $translation) {
            if ($translation->isDisabled()) {
                continue;
            }

            $context = $translation->getContext();
            $original = $translation->getOriginal();

            if (!isset($messages[$context])) {
                $messages[$context] = [];
            }

            if ($translation->hasPluralTranslations(true)) {
                $messages[$context][$original] = $translation->getPluralTranslations($pluralSize);
                array_unshift($messages[$context][$original], $translation->getTranslation());
            } elseif ($forceArray) {
                $messages[$context][$original] = [$translation->getTranslation()];
            } else {
                $messages[$context][$original] = $translation->getTranslation();
            }
        }

        return [
            'domain' => $translations->getDomain(),
            'plural-forms' => $translations->getHeader('Plural-Forms'),
            'messages' => $messages,
        ];
    }

    /**
     * Extract the entries from a multidimensional array.
     *
     * @param array        $messages
     * @param Translations $translations
     */
    protected static function fromArray(array $messages, Translations $translations)
    {
        if (!empty($messages['domain'])) {
            $translations->setDomain($messages['domain']);
        }

        if (!empty($messages['plural-forms'])) {
            $translations->setHeader(Translations::HEADER_PLURAL, $messages['plural-forms']);
        }

        foreach ($messages['messages'] as $context => $contextTranslations) {
            foreach ($contextTranslations as $original => $value) {
                if ($context === '' && $original === '') {
                    static::extractHeaders(is_array($value) ? array_shift($value) : $value, $translations);
                    continue;
                }

                $translation = $translations->insert($context, $original);

                if (is_array($value)) {
                    $translation->setTranslation(array_shift($value));
                    $translation->setPluralTranslations($value);
                } else {
                    $translation->setTranslation($value);
                }
            }
        }
    }
}
