<?php

namespace Gettext\Generators;

use Gettext\Translation;
use Gettext\Translations;
use DOMDocument;

class Xliff extends Generator implements GeneratorInterface
{
    const UNIT_ID_REGEXP = '/^XLIFF_UNIT_ID: (.*)$/';

    /**
     * {@inheritdoc}
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $xliff = $dom->appendChild($dom->createElement('xliff'));
        $xliff->setAttribute('xmlns', 'urn:oasis:names:tc:xliff:document:2.0');
        $xliff->setAttribute('version', '2.0');
        $xliff->setAttribute('srcLang', $translations->getLanguage());
        $xliff->setAttribute('trgLang', $translations->getLanguage());
        $file = $xliff->appendChild($dom->createElement('file'));
        $file->setAttribute('id', $translations->getDomain().'.'.$translations->getLanguage());

        //Save headers as notes
        $notes = $dom->createElement('notes');

        foreach ($translations->getHeaders() as $name => $value) {
            $notes->appendChild(static::createTextNode($dom, 'note', $value))->setAttribute('id', $name);
        }

        if ($notes->hasChildNodes()) {
            $file->appendChild($notes);
        }

        foreach ($translations as $translation) {
            //Find an XLIFF unit ID, if one is available; otherwise generate
            $unitId = static::getUnitID($translation)?:md5($translation->getContext().$translation->getOriginal());

            $unit = $dom->createElement('unit');
            $unit->setAttribute('id', $unitId);

            //Save comments as notes
            $notes = $dom->createElement('notes');

            $notes->appendChild(static::createTextNode($dom, 'note', $translation->getContext()))
                ->setAttribute('category', 'context');

            foreach ($translation->getComments() as $comment) {
                //Skip XLIFF unit ID comments.
                if (preg_match(static::UNIT_ID_REGEXP, $comment)) {
                    continue;
                }

                $notes->appendChild(static::createTextNode($dom, 'note', $comment))
                    ->setAttribute('category', 'comment');
            }

            foreach ($translation->getExtractedComments() as $comment) {
                $notes->appendChild(static::createTextNode($dom, 'note', $comment))
                    ->setAttribute('category', 'extracted-comment');
            }

            foreach ($translation->getFlags() as $flag) {
                $notes->appendChild(static::createTextNode($dom, 'note', $flag))
                    ->setAttribute('category', 'flag');
            }

            foreach ($translation->getReferences() as $reference) {
                $notes->appendChild(static::createTextNode($dom, 'note', $reference[0].':'.$reference[1]))
                    ->setAttribute('category', 'reference');
            }

            $unit->appendChild($notes);

            $segment = $unit->appendChild($dom->createElement('segment'));
            $segment->appendChild(static::createTextNode($dom, 'source', $translation->getOriginal()));
            $segment->appendChild(static::createTextNode($dom, 'target', $translation->getTranslation()));

            foreach ($translation->getPluralTranslations() as $plural) {
                if ($plural !== '') {
                    $segment->appendChild(static::createTextNode($dom, 'target', $plural));
                }
            }

            $file->appendChild($unit);
        }

        return $dom->saveXML();
    }

    protected static function createTextNode(DOMDocument $dom, $name, $string)
    {
        $node = $dom->createElement($name);
        $text = (preg_match('/[&<>]/', $string) === 1)
             ? $dom->createCDATASection($string)
             : $dom->createTextNode($string);
        $node->appendChild($text);

        return $node;
    }

    /**
     * Gets the translation's unit ID, if one is available.
     *
     * @param Translation $translation
     *
     * @return string|null
     */
    public static function getUnitID(Translation $translation)
    {
        foreach ($translation->getComments() as $comment) {
            if (preg_match(static::UNIT_ID_REGEXP, $comment, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}
