<?php

namespace Gettext\Languages\Exporter;

class Xml extends Exporter
{
    /**
     * {@inheritdoc}
     *
     * @see \Gettext\Languages\Exporter\Exporter::getDescription()
     */
    public static function getDescription()
    {
        return 'Build an XML file - schema available at http://mlocati.github.io/cldr-to-gettext-plural-rules/GettextLanguages.xsd';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Gettext\Languages\Exporter\Exporter::toStringDo()
     */
    protected static function toStringDo($languages)
    {
        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->loadXML('<languages
            xmlns="https://github.com/mlocati/cldr-to-gettext-plural-rules"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="https://github.com/mlocati/cldr-to-gettext-plural-rules http://mlocati.github.io/cldr-to-gettext-plural-rules/GettextLanguages.xsd"
        />');
        $xLanguages = $xml->firstChild;
        foreach ($languages as $language) {
            $xLanguage = $xml->createElement('language');
            $xLanguage->setAttribute('id', $language->id);
            $xLanguage->setAttribute('name', $language->name);
            if (isset($language->supersededBy)) {
                $xLanguage->setAttribute('supersededBy', $language->supersededBy);
            }
            if (isset($language->script)) {
                $xLanguage->setAttribute('script', $language->script);
            }
            if (isset($language->territory)) {
                $xLanguage->setAttribute('territory', $language->territory);
            }
            if (isset($language->baseLanguage)) {
                $xLanguage->setAttribute('baseLanguage', $language->baseLanguage);
            }
            $xLanguage->setAttribute('formula', $language->formula);
            foreach ($language->categories as $category) {
                $xCategory = $xml->createElement('category');
                $xCategory->setAttribute('id', $category->id);
                $xCategory->setAttribute('examples', $category->examples);
                $xLanguage->appendChild($xCategory);
            }
            $xLanguages->appendChild($xLanguage);
        }
        $xml->formatOutput = true;

        return $xml->saveXML();
    }
}
