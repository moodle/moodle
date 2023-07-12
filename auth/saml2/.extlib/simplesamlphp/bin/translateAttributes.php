#!/usr/bin/env php
<?php

/**
 * This script loads attribute names from the attributemap/ directory, and dumps them into an attributes.po
 * translation file for each supported language.
 */

$base = __DIR__ . '/../';

include_once($base . 'vendor/autoload.php');

include_once($base . 'attributemap/name2urn.php');
$names = $attributemap;

include_once($base . 'attributemap/urn2oid.php');
$urns = $attributemap;

include_once($base . 'attributemap/newSchacNS.php');
$schac = $attributemap;

/*
 * We are still using the old JSON dictionaries as authoritative source here. It is actually convenient to keep
 * something equivalent to this, in order to automate propagation of attribute translations to the PO files. We should
 * probably consider moving the "dictionaries/attributes.definition.json" file somewhere else, and keep using it as
 * the authoritative source of known attributes for this script.
 */
$defs = json_decode(file_get_contents($base . 'dictionaries/attributes.definition.json'), true);
$trans = json_decode(file_get_contents($base . 'dictionaries/attributes.translation.json'), true);

$attributes = [];

$languages = SimpleSAML\Locale\Language::$language_names;
$languages['nb'] = $languages['no'];
unset($languages['no']);


// build the list of attributes with their corresponding aliases
foreach ($names as $name => $urn) {
    $lower = str_replace([':', '-'], '_', strtolower($name));
    if (!array_key_exists('attribute_'  . $lower, $defs)) {
        $defs['attribute_' . $lower] = [];
    }
    if (!array_key_exists('attribute_' . $lower, $trans)) {
        $trans['attribute_' . $lower] = [];
    }
    if (array_key_exists('no', $trans['attribute_' . $lower])) {
        // fix the locale code
        $trans['attribute_' . $lower]['nb'] = $trans['attribute_' . $lower]['no'];
        unset($trans['attribute_' . $lower]['no']);
    }
    $names = [$name, $urn, $urns[$urn]];
    if (array_key_exists($urn, $schac)) {
        $names[] = $schac[$urn];
    }
    $attributes[$name] = [
        'names' => $names,
        'translations' => array_merge(
            [
                'en' => $defs['attribute_' . $lower]['en'],
            ],
            $trans['attribute_' . $lower]
        ),
    ];
}

// process other sets of attributes
foreach (['facebook', 'linkedin', 'openid', 'twitter', 'windowslive'] as $set) {
    include_once($base . 'attributemap/' . $set . '2name.php');
    foreach ($attributemap as $alias => $attr) {
        if (array_key_exists($attr, $attributes)) {
            $attributes[$attr]['names'][] = $alias;
        }
    }
}

// build the dictionaries per language
foreach (array_keys($languages) as $language) {
    $strings = new Gettext\Translations();

    // load existing translations in the PO files
    $strings->addFromPoFile($base . 'locales/' . $language . "/LC_MESSAGES/attributes.po");

    foreach ($attributes as $attribute) {
        foreach ($attribute['names'] as $name) {
            if (empty($name)) {
                continue;
            }
            $translation = new Gettext\Translation('', $name);
            if (
                array_key_exists($language, $attribute['translations'])
                && !is_null($attribute['translations'][$language])
            ) {
                $t = $strings->find($translation);
                if ($t) {
                    if ($t->getOriginal() === $t->getTranslation()) {
                        $t->setTranslation($attribute['translations'][$language]);
                        $translation = $t;
                    }
                }
            }
            if (!is_null($attribute['translations']['en']) && $language !== 'en') {
                $translation->addComment('English string: ' . $attribute['translations']['en']);
            }
            $strings[] = $translation;
        }
    }

    foreach ($strings as $entry) {
        if ($entry->getTranslation() === '') {
            // ensure that all entries contain a translation string
            $entry->setTranslation($entry->getOriginal());
        }
    }

    // remove headers that only cause unnecessary changes in our commits
    $strings->deleteHeader('POT-Creation-Date');
    $strings->deleteHeader('PO-Revision-Date');

    $strings->setLanguage($language);
    echo "Saving translations to " . $base . "locales/" . $language . "/LC_MESSAGES/attributes.po\n";
    Gettext\Generators\Po::toFile($strings, $base . 'locales/' . $language . '/LC_MESSAGES/attributes.po');
}
