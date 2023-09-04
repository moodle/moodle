<?php

namespace Gettext\Languages;

use Exception;

/**
 * Holds the CLDR data.
 */
class CldrData
{
    /**
     * Super-special plural category: this should always be present for any language.
     *
     * @var string
     */
    const OTHER_CATEGORY = 'other';

    /**
     * The list of the plural categories, sorted from 'zero' to 'other'.
     *
     * @var string[]
     */
    public static $categories = array('zero', 'one', 'two', 'few', 'many', self::OTHER_CATEGORY);

    /**
     * The loaded CLDR data.
     *
     * @var array
     */
    private static $data;

    /**
     * @var array
     */
    private static $plurals;

    /**
     * Returns a dictionary containing the language names.
     * The keys are the language identifiers.
     * The values are the language names in US English.
     *
     * @return string[]
     */
    public static function getLanguageNames()
    {
        return self::getData('languages');
    }

    /**
     * Return a dictionary containing the territory names (in US English).
     * The keys are the territory identifiers.
     * The values are the territory names in US English.
     *
     * @return string[]
     */
    public static function getTerritoryNames()
    {
        return self::getData('territories');
    }

    /**
     * Return a dictionary containing the script names (in US English).
     * The keys are the script identifiers.
     * The values are the script names in US English.
     *
     * @param bool $standAlone set to true to retrieve the stand-alone script names, false otherwise
     *
     * @return string[]
     */
    public static function getScriptNames($standAlone)
    {
        return self::getData($standAlone ? 'standAloneScripts' : 'scripts');
    }

    /**
     * A dictionary containing the plural rules.
     * The keys are the language identifiers.
     * The values are arrays whose keys are the CLDR category names and the values are the CLDR category definition.
     *
     * @example The English key-value pair is somethink like this:
     * <code><pre>
     * "en": {
     *     "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
     *     "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
     * }
     * </pre></code>
     *
     * @return array
     */
    public static function getPlurals()
    {
        return self::getData('plurals');
    }

    /**
     * Return a list of superseded language codes.
     *
     * @return array keys are the former language codes, values are the new language/locale codes
     */
    public static function getSupersededLanguages()
    {
        return self::getData('supersededLanguages');
    }

    /**
     * Retrieve the name of a language, as well as if a language code is deprecated in favor of another language code.
     *
     * @param string $id the language identifier
     *
     * @return array|null Returns an array with the keys 'id' (normalized), 'name', 'supersededBy' (optional), 'territory' (optional), 'script' (optional), 'baseLanguage' (optional), 'categories'. If $id is not valid returns null.
     */
    public static function getLanguageInfo($id)
    {
        $result = null;
        $matches = array();
        if (preg_match('/^([a-z]{2,3})(?:[_\-]([a-z]{4}))?(?:[_\-]([a-z]{2}|[0-9]{3}))?(?:$|-)/i', $id, $matches)) {
            $languageId = strtolower($matches[1]);
            $scriptId = (isset($matches[2]) && ($matches[2] !== '')) ? ucfirst(strtolower($matches[2])) : null;
            $territoryId = (isset($matches[3]) && ($matches[3] !== '')) ? strtoupper($matches[3]) : null;
            $normalizedId = $languageId;
            if (isset($scriptId)) {
                $normalizedId .= '_' . $scriptId;
            }
            if (isset($territoryId)) {
                $normalizedId .= '_' . $territoryId;
            }
            // Structure precedence: see Likely Subtags - http://www.unicode.org/reports/tr35/tr35-31/tr35.html#Likely_Subtags
            $variants = array();
            $variantsWithScript = array();
            $variantsWithTerritory = array();
            if (isset($scriptId) && isset($territoryId)) {
                $variantsWithTerritory[] = $variantsWithScript[] = $variants[] = "{$languageId}_{$scriptId}_{$territoryId}";
            }
            if (isset($scriptId)) {
                $variantsWithScript[] = $variants[] = "{$languageId}_{$scriptId}";
            }
            if (isset($territoryId)) {
                $variantsWithTerritory[] = $variants[] = "{$languageId}_{$territoryId}";
            }
            $variants[] = $languageId;
            $allGood = true;
            $scriptName = null;
            $scriptStandAloneName = null;
            if (isset($scriptId)) {
                $scriptNames = self::getScriptNames(false);
                if (isset($scriptNames[$scriptId])) {
                    $scriptName = $scriptNames[$scriptId];
                    $scriptStandAloneNames = self::getScriptNames(true);
                    $scriptStandAloneName = $scriptStandAloneNames[$scriptId];
                } else {
                    $allGood = false;
                }
            }
            $territoryName = null;
            if (isset($territoryId)) {
                $territoryNames = self::getTerritoryNames();
                if (isset($territoryNames[$territoryId])) {
                    if ($territoryId !== '001') {
                        $territoryName = $territoryNames[$territoryId];
                    }
                } else {
                    $allGood = false;
                }
            }
            $languageName = null;
            $languageNames = self::getLanguageNames();
            foreach ($variants as $variant) {
                if (isset($languageNames[$variant])) {
                    $languageName = $languageNames[$variant];
                    if (isset($scriptName) && (!in_array($variant, $variantsWithScript))) {
                        $languageName = $scriptName . ' ' . $languageName;
                    }
                    if (isset($territoryName) && (!in_array($variant, $variantsWithTerritory))) {
                        $languageName .= ' (' . $territoryNames[$territoryId] . ')';
                    }
                    break;
                }
            }
            if (!isset($languageName)) {
                $allGood = false;
            }
            $baseLanguage = null;
            if (isset($scriptId) || isset($territoryId)) {
                if (isset($languageNames[$languageId]) && ($languageNames[$languageId] !== $languageName)) {
                    $baseLanguage = $languageNames[$languageId];
                }
            }
            $plural = null;
            $plurals = self::getPlurals();
            foreach ($variants as $variant) {
                if (isset($plurals[$variant])) {
                    $plural = $plurals[$variant];
                    break;
                }
            }
            if (!isset($plural)) {
                $allGood = false;
            }
            $supersededBy = null;
            $supersededBys = self::getSupersededLanguages();
            foreach ($variants as $variant) {
                if (isset($supersededBys[$variant])) {
                    $supersededBy = $supersededBys[$variant];
                    break;
                }
            }
            if ($allGood) {
                $result = array();
                $result['id'] = $normalizedId;
                $result['name'] = $languageName;
                if (isset($supersededBy)) {
                    $result['supersededBy'] = $supersededBy;
                }
                if (isset($scriptStandAloneName)) {
                    $result['script'] = $scriptStandAloneName;
                }
                if (isset($territoryName)) {
                    $result['territory'] = $territoryName;
                }
                if (isset($baseLanguage)) {
                    $result['baseLanguage'] = $baseLanguage;
                }
                $result['categories'] = $plural;
            }
        }

        return $result;
    }

    /**
     * Returns the loaded CLDR data.
     *
     * @param string $key Can be 'languages', 'territories', 'plurals', 'supersededLanguages', 'scripts', 'standAloneScripts'
     *
     * @return array
     */
    private static function getData($key)
    {
        if (!isset(self::$data)) {
            $fixKeys = function ($list, &$standAlone = null) {
                $result = array();
                $standAlone = array();
                $match = null;
                foreach ($list as $key => $value) {
                    $variant = '';
                    if (preg_match('/^(.+)-alt-(short|variant|stand-alone|long|menu)$/', $key, $match)) {
                        $key = $match[1];
                        $variant = $match[2];
                    }
                    $key = str_replace('-', '_', $key);
                    switch ($key) {
                        case 'root': // Language: Root
                        case 'und': // Language: Unknown Language
                        case 'zxx': // Language: No linguistic content
                        case 'ZZ': // Territory: Unknown Region
                        case 'Zinh': // Script: Inherited
                        case 'Zmth': // Script: Mathematical Notation
                        case 'Zsym': // Script: Symbols
                        case 'Zxxx': // Script: Unwritten
                        case 'Zyyy': // Script: Common
                        case 'Zzzz': // Script: Unknown Script
                            break;
                        default:
                            switch ($variant) {
                                case 'stand-alone':
                                    $standAlone[$key] = $value;
                                    break;
                                case '':
                                    $result[$key] = $value;
                                    break;
                            }
                            break;
                    }
                }

                return $result;
            };
            $data = array();
            $json = json_decode(file_get_contents(__DIR__ . '/cldr-data/main/en-US/languages.json'), true);
            $data['languages'] = $fixKeys($json['main']['en-US']['localeDisplayNames']['languages']);
            $json = json_decode(file_get_contents(__DIR__ . '/cldr-data/main/en-US/territories.json'), true);
            $data['territories'] = $fixKeys($json['main']['en-US']['localeDisplayNames']['territories']);
            $json = json_decode(file_get_contents(__DIR__ . '/cldr-data/supplemental/plurals.json'), true);
            $data['plurals'] = $fixKeys($json['supplemental']['plurals-type-cardinal']);
            $json = json_decode(file_get_contents(__DIR__ . '/cldr-data/main/en-US/scripts.json'), true);
            $data['scripts'] = $fixKeys($json['main']['en-US']['localeDisplayNames']['scripts'], $data['standAloneScripts']);
            $data['standAloneScripts'] = array_merge($data['scripts'], $data['standAloneScripts']);
            $data['scripts'] = array_merge($data['standAloneScripts'], $data['scripts']);
            $data['supersededLanguages'] = array();
            // Remove the languages for which we don't have plurals
            $m = null;
            foreach (array_keys(array_diff_key($data['languages'], $data['plurals'])) as $missingPlural) {
                if (preg_match('/^([a-z]{2,3})_/', $missingPlural, $m)) {
                    if (!isset($data['plurals'][$m[1]])) {
                        unset($data['languages'][$missingPlural]);
                    }
                } else {
                    unset($data['languages'][$missingPlural]);
                }
            }
            // Fix the languages for which we have plurals
            $formerCodes = array(
                'jw' => 'jv', // former Javanese
                'mo' => 'ro_MD', // former Moldavian
            );
            $knownMissingLanguages = array(
                'guw' => 'Gun',
                'nah' => 'Nahuatl',
                'smi' => 'Sami',
            );
            foreach (array_keys(array_diff_key($data['plurals'], $data['languages'])) as $missingLanguage) {
                if (isset($formerCodes[$missingLanguage]) && isset($data['languages'][$formerCodes[$missingLanguage]])) {
                    $data['languages'][$missingLanguage] = $data['languages'][$formerCodes[$missingLanguage]];
                    $data['supersededLanguages'][$missingLanguage] = $formerCodes[$missingLanguage];
                } else {
                    if (isset($knownMissingLanguages[$missingLanguage])) {
                        $data['languages'][$missingLanguage] = $knownMissingLanguages[$missingLanguage];
                    } else {
                        throw new Exception("We have the plural rule for the language '{$missingLanguage}' but we don't have its language name");
                    }
                }
            }
            ksort($data['languages'], SORT_STRING);
            ksort($data['territories'], SORT_STRING);
            ksort($data['plurals'], SORT_STRING);
            ksort($data['scripts'], SORT_STRING);
            ksort($data['standAloneScripts'], SORT_STRING);
            ksort($data['supersededLanguages'], SORT_STRING);
            self::$data = $data;
        }
        if (!isset(self::$data[$key])) {
            throw new Exception("Invalid CLDR data key: '{$key}'");
        }

        return self::$data[$key];
    }
}
