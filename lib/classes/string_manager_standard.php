<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Standard string manager.
 *
 * @package    core
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Standard string_manager implementation
 *
 * Implements string_manager with getting and printing localised strings
 *
 * @package    core
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_string_manager_standard implements core_string_manager {
    /** @var string location of all packs except 'en' */
    protected $otherroot;
    /** @var string location of all lang pack local modifications */
    protected $localroot;
    /** @var cache lang string cache - it will be optimised more later */
    protected $cache;
    /** @var int get_string() counter */
    protected $countgetstring = 0;
    /** @var bool use disk cache */
    protected $translist;
    /** @var array language aliases to use in the language selector */
    protected $transaliases = [];
    /** @var cache stores list of available translations */
    protected $menucache;
    /** @var array list of cached deprecated strings */
    protected $cacheddeprecated;

    /**
     * Create new instance of string manager
     *
     * @param string $otherroot location of downloaded lang packs - usually $CFG->dataroot/lang
     * @param string $localroot usually the same as $otherroot
     * @param array $translist limit list of visible translations
     * @param array $transaliases aliases to use for the languages in the language selector
     */
    public function __construct($otherroot, $localroot, $translist, $transaliases = []) {
        $this->otherroot    = $otherroot;
        $this->localroot    = $localroot;
        if ($translist) {
            $this->translist = array_combine($translist, $translist);
            $this->transaliases = $transaliases;
        } else {
            $this->translist = array();
        }

        if ($this->get_revision() > 0) {
            // We can use a proper cache, establish the cache using the 'String cache' definition.
            $this->cache = cache::make('core', 'string');
            $this->menucache = cache::make('core', 'langmenu');
        } else {
            // We only want a cache for the length of the request, create a static cache.
            $options = array(
                'simplekeys' => true,
                'simpledata' => true
            );
            $this->cache = cache::make_from_params(cache_store::MODE_REQUEST, 'core', 'string', array(), $options);
            $this->menucache = cache::make_from_params(cache_store::MODE_REQUEST, 'core', 'langmenu', array(), $options);
        }
    }

    /**
     * Returns list of all explicit parent languages for the given language.
     *
     * English (en) is considered as the top implicit parent of all language packs
     * and is not included in the returned list. The language itself is appended to the
     * end of the list. The method is aware of circular dependency risk.
     *
     * @see self::populate_parent_languages()
     * @param string $lang the code of the language
     * @return array all explicit parent languages with the lang itself appended
     */
    public function get_language_dependencies($lang) {
        return $this->populate_parent_languages($lang);
    }

    /**
     * Load all strings for one component
     *
     * @param string $component The module the string is associated with
     * @param string $lang
     * @param bool $disablecache Do not use caches, force fetching the strings from sources
     * @param bool $disablelocal Do not use customized strings in xx_local language packs
     * @return array of all string for given component and lang
     */
    public function load_component_strings($component, $lang, $disablecache = false, $disablelocal = false) {
        global $CFG;

        list($plugintype, $pluginname) = core_component::normalize_component($component);
        if ($plugintype === 'core' and is_null($pluginname)) {
            $component = 'core';
        } else {
            $component = $plugintype . '_' . $pluginname;
        }

        $cachekey = $lang.'_'.$component.'_'.$this->get_key_suffix();

        $cachedstring = $this->cache->get($cachekey);
        if (!$disablecache and !$disablelocal) {
            if ($cachedstring !== false) {
                return $cachedstring;
            }
        }

        // No cache found - let us merge all possible sources of the strings.
        if ($plugintype === 'core') {
            $file = $pluginname;
            if ($file === null) {
                $file = 'moodle';
            }
            $string = array();
            // First load english pack.
            if (!file_exists("$CFG->dirroot/lang/en/$file.php")) {
                return array();
            }
            include("$CFG->dirroot/lang/en/$file.php");
            $enstring = $string;

            // And then corresponding local if present and allowed.
            if (!$disablelocal and file_exists("$this->localroot/en_local/$file.php")) {
                include("$this->localroot/en_local/$file.php");
            }
            // Now loop through all langs in correct order.
            $deps = $this->get_language_dependencies($lang);
            foreach ($deps as $dep) {
                // The main lang string location.
                if (file_exists("$this->otherroot/$dep/$file.php")) {
                    include("$this->otherroot/$dep/$file.php");
                }
                if (!$disablelocal and file_exists("$this->localroot/{$dep}_local/$file.php")) {
                    include("$this->localroot/{$dep}_local/$file.php");
                }
            }

        } else {
            if (!$location = core_component::get_plugin_directory($plugintype, $pluginname) or !is_dir($location)) {
                return array();
            }
            if ($plugintype === 'mod') {
                // Bloody mod hack.
                $file = $pluginname;
            } else {
                $file = $plugintype . '_' . $pluginname;
            }
            $string = array();
            // First load English pack.
            if (!file_exists("$location/lang/en/$file.php")) {
                // English pack does not exist, so do not try to load anything else.
                return array();
            }
            include("$location/lang/en/$file.php");
            $enstring = $string;
            // And then corresponding local english if present.
            if (!$disablelocal and file_exists("$this->localroot/en_local/$file.php")) {
                include("$this->localroot/en_local/$file.php");
            }

            // Now loop through all langs in correct order.
            $deps = $this->get_language_dependencies($lang);
            foreach ($deps as $dep) {
                // Legacy location - used by contrib only.
                if (file_exists("$location/lang/$dep/$file.php")) {
                    include("$location/lang/$dep/$file.php");
                }
                // The main lang string location.
                if (file_exists("$this->otherroot/$dep/$file.php")) {
                    include("$this->otherroot/$dep/$file.php");
                }
                // Local customisations.
                if (!$disablelocal and file_exists("$this->localroot/{$dep}_local/$file.php")) {
                    include("$this->localroot/{$dep}_local/$file.php");
                }
            }
        }

        // We do not want any extra strings from other languages - everything must be in en lang pack.
        $string = array_intersect_key($string, $enstring);

        if (!$disablelocal) {
            // Now we have a list of strings from all possible sources,
            // cache it in MUC cache if not already there.
            if ($cachedstring === false) {
                $this->cache->set($cachekey, $string);
            }
        }
        return $string;
    }

    /**
     * Parses all deprecated.txt in all plugins lang locations and returns the list of deprecated strings.
     *
     * Static variable is used for caching, this function is only called in dev environment.
     *
     * @return array of deprecated strings in the same format they appear in deprecated.txt files: "identifier,component"
     *     where component is a normalised component (i.e. "core_moodle", "mod_assign", etc.)
     */
    protected function load_deprecated_strings() {
        global $CFG;

        if ($this->cacheddeprecated !== null) {
            return $this->cacheddeprecated;
        }

        $this->cacheddeprecated = array();
        $content = '';
        $filename = $CFG->dirroot . '/lang/en/deprecated.txt';
        if (file_exists($filename)) {
            $content .= file_get_contents($filename);
        }
        foreach (core_component::get_plugin_types() as $plugintype => $plugintypedir) {
            foreach (core_component::get_plugin_list($plugintype) as $pluginname => $plugindir) {
                $filename = $plugindir.'/lang/en/deprecated.txt';
                if (file_exists($filename)) {
                    $content .= "\n". file_get_contents($filename);
                }
            }
        }

        $strings = preg_split('/\s*\n\s*/', $content, -1, PREG_SPLIT_NO_EMPTY);
        $this->cacheddeprecated = array_flip($strings);

        return $this->cacheddeprecated;
    }

    /**
     * Has string been deprecated?
     *
     * Usually checked only inside get_string() to display debug warnings.
     *
     * @param string $identifier The identifier of the string to search for
     * @param string $component The module the string is associated with
     * @return bool true if deprecated
     */
    public function string_deprecated($identifier, $component) {
        $deprecated = $this->load_deprecated_strings();
        list($plugintype, $pluginname) = core_component::normalize_component($component);
        $normcomponent = $pluginname ? ($plugintype . '_' . $pluginname) : $plugintype;
        return isset($deprecated[$identifier . ',' . $normcomponent]);
    }

    /**
     * Does the string actually exist?
     *
     * get_string() is throwing debug warnings, sometimes we do not want them
     * or we want to display better explanation of the problem.
     * Note: Use with care!
     *
     * @param string $identifier The identifier of the string to search for
     * @param string $component The module the string is associated with
     * @return boot true if exists
     */
    public function string_exists($identifier, $component) {
        $lang = current_language();
        $string = $this->load_component_strings($component, $lang);
        return isset($string[$identifier]);
    }

    /**
     * Get String returns a requested string
     *
     * @param string $identifier The identifier of the string to search for
     * @param string $component The module the string is associated with
     * @param string|object|array $a An object, string or number that can be used
     *      within translation strings
     * @param string $lang moodle translation language, null means use current
     * @return string The String !
     */
    public function get_string($identifier, $component = '', $a = null, $lang = null) {
        global $CFG;

        $this->countgetstring++;
        // There are very many uses of these time formatting strings without the 'langconfig' component,
        // it would not be reasonable to expect that all of them would be converted during 2.0 migration.
        static $langconfigstrs = array(
            'strftimedate' => 1,
            'strftimedatefullshort' => 1,
            'strftimedateshort' => 1,
            'strftimedatetime' => 1,
            'strftimedatetimeshort' => 1,
            'strftimedaydate' => 1,
            'strftimedaydatetime' => 1,
            'strftimedayshort' => 1,
            'strftimedaytime' => 1,
            'strftimemonthyear' => 1,
            'strftimerecent' => 1,
            'strftimerecentfull' => 1,
            'strftimetime' => 1);

        if (empty($component)) {
            if (isset($langconfigstrs[$identifier])) {
                $component = 'langconfig';
            } else {
                $component = 'moodle';
            }
        }

        if ($lang === null) {
            $lang = current_language();
        }

        $string = $this->load_component_strings($component, $lang);

        if (!isset($string[$identifier])) {
            if ($component === 'pix' or $component === 'core_pix') {
                // This component contains only alt tags for emoticons, not all of them are supposed to be defined.
                return '';
            }
            if ($identifier === 'parentlanguage' and ($component === 'langconfig' or $component === 'core_langconfig')) {
                // Identifier parentlanguage is a special string, undefined means use English if not defined.
                return 'en';
            }
            // Do not rebuild caches here!
            // Devs need to learn to purge all caches after any change or disable $CFG->langstringcache.
            if (!isset($string[$identifier])) {
                // The string is still missing - should be fixed by developer.
                if ($CFG->debugdeveloper) {
                    list($plugintype, $pluginname) = core_component::normalize_component($component);
                    if ($plugintype === 'core') {
                        $file = "lang/en/{$component}.php";
                    } else if ($plugintype == 'mod') {
                        $file = "mod/{$pluginname}/lang/en/{$pluginname}.php";
                    } else {
                        $path = core_component::get_plugin_directory($plugintype, $pluginname);
                        $file = "{$path}/lang/en/{$plugintype}_{$pluginname}.php";
                    }
                    debugging("Invalid get_string() identifier: '{$identifier}' or component '{$component}'. " .
                    "Perhaps you are missing \$string['{$identifier}'] = ''; in {$file}?", DEBUG_DEVELOPER);
                }
                return "[[$identifier]]";
            }
        }

        $string = $string[$identifier];

        if ($a !== null) {
            // Process array's and objects (except lang_strings).
            if (is_array($a) or (is_object($a) && !($a instanceof lang_string))) {
                $a = (array)$a;
                $search = array();
                $replace = array();
                foreach ($a as $key => $value) {
                    if (is_int($key)) {
                        // We do not support numeric keys - sorry!
                        continue;
                    }
                    if (is_array($value) or (is_object($value) && !($value instanceof lang_string))) {
                        // We support just string or lang_string as value.
                        continue;
                    }
                    $search[]  = '{$a->'.$key.'}';
                    $replace[] = (string)$value;
                }
                if ($search) {
                    $string = str_replace($search, $replace, $string);
                }
            } else {
                $string = str_replace('{$a}', (string)$a, $string);
            }
        }

        if ($CFG->debugdeveloper) {
            // Display a debugging message if sting exists but was deprecated.
            if ($this->string_deprecated($identifier, $component)) {
                list($plugintype, $pluginname) = core_component::normalize_component($component);
                $normcomponent = $pluginname ? ($plugintype . '_' . $pluginname) : $plugintype;
                debugging("String [{$identifier},{$normcomponent}] is deprecated. ".
                    'Either you should no longer be using that string, or the string has been incorrectly deprecated, in which case you should report this as a bug. '.
                    'Please refer to https://docs.moodle.org/dev/String_deprecation', DEBUG_DEVELOPER);
            }
        }

        return $string;
    }

    /**
     * Returns information about the core_string_manager performance.
     *
     * @return array
     */
    public function get_performance_summary() {
        return array(array(
            'langcountgetstring' => $this->countgetstring,
        ), array(
            'langcountgetstring' => 'get_string calls',
        ));
    }

    /**
     * Returns a localised list of all country names, sorted by localised name.
     *
     * @param bool $returnall return all or just enabled
     * @param string $lang moodle translation language, null means use current
     * @return array two-letter country code => translated name.
     */
    public function get_list_of_countries($returnall = false, $lang = null) {
        global $CFG;

        if ($lang === null) {
            $lang = current_language();
        }

        $countries = $this->load_component_strings('core_countries', $lang);
        core_collator::asort($countries);

        if (!$returnall and !empty($CFG->allcountrycodes)) {
            $enabled = explode(',', $CFG->allcountrycodes);
            $return = array();
            foreach ($enabled as $c) {
                if (isset($countries[$c])) {
                    $return[$c] = $countries[$c];
                }
            }

            if (!empty($return)) {
                return $return;
            }
        }

        return $countries;
    }

    /**
     * Returns a localised list of languages, sorted by code keys.
     *
     * @param string $lang moodle translation language, null means use current
     * @param string $standard language list standard
     *    - iso6392: three-letter language code (ISO 639-2/T) => translated name
     *    - iso6391: two-letter language code (ISO 639-1) => translated name
     * @return array language code => translated name
     */
    public function get_list_of_languages($lang = null, $standard = 'iso6391') {
        if ($lang === null) {
            $lang = current_language();
        }

        if ($standard === 'iso6392') {
            $langs = $this->load_component_strings('core_iso6392', $lang);
            ksort($langs);
            return $langs;

        } else if ($standard === 'iso6391') {
            $langs2 = $this->load_component_strings('core_iso6392', $lang);
            static $mapping = array('aar' => 'aa', 'abk' => 'ab', 'afr' => 'af', 'aka' => 'ak', 'sqi' => 'sq', 'amh' => 'am', 'ara' => 'ar', 'arg' => 'an', 'hye' => 'hy',
                'asm' => 'as', 'ava' => 'av', 'ave' => 'ae', 'aym' => 'ay', 'aze' => 'az', 'bak' => 'ba', 'bam' => 'bm', 'eus' => 'eu', 'bel' => 'be', 'ben' => 'bn', 'bih' => 'bh',
                'bis' => 'bi', 'bos' => 'bs', 'bre' => 'br', 'bul' => 'bg', 'mya' => 'my', 'cat' => 'ca', 'cha' => 'ch', 'che' => 'ce', 'zho' => 'zh', 'chu' => 'cu', 'chv' => 'cv',
                'cor' => 'kw', 'cos' => 'co', 'cre' => 'cr', 'ces' => 'cs', 'dan' => 'da', 'div' => 'dv', 'nld' => 'nl', 'dzo' => 'dz', 'eng' => 'en', 'epo' => 'eo', 'est' => 'et',
                'ewe' => 'ee', 'fao' => 'fo', 'fij' => 'fj', 'fin' => 'fi', 'fra' => 'fr', 'fry' => 'fy', 'ful' => 'ff', 'kat' => 'ka', 'deu' => 'de', 'gla' => 'gd', 'gle' => 'ga',
                'glg' => 'gl', 'glv' => 'gv', 'ell' => 'el', 'grn' => 'gn', 'guj' => 'gu', 'hat' => 'ht', 'hau' => 'ha', 'heb' => 'he', 'her' => 'hz', 'hin' => 'hi', 'hmo' => 'ho',
                'hrv' => 'hr', 'hun' => 'hu', 'ibo' => 'ig', 'isl' => 'is', 'ido' => 'io', 'iii' => 'ii', 'iku' => 'iu', 'ile' => 'ie', 'ina' => 'ia', 'ind' => 'id', 'ipk' => 'ik',
                'ita' => 'it', 'jav' => 'jv', 'jpn' => 'ja', 'kal' => 'kl', 'kan' => 'kn', 'kas' => 'ks', 'kau' => 'kr', 'kaz' => 'kk', 'khm' => 'km', 'kik' => 'ki', 'kin' => 'rw',
                'kir' => 'ky', 'kom' => 'kv', 'kon' => 'kg', 'kor' => 'ko', 'kua' => 'kj', 'kur' => 'ku', 'lao' => 'lo', 'lat' => 'la', 'lav' => 'lv', 'lim' => 'li', 'lin' => 'ln',
                'lit' => 'lt', 'ltz' => 'lb', 'lub' => 'lu', 'lug' => 'lg', 'mkd' => 'mk', 'mah' => 'mh', 'mal' => 'ml', 'mri' => 'mi', 'mar' => 'mr', 'msa' => 'ms', 'mlg' => 'mg',
                'mlt' => 'mt', 'mon' => 'mn', 'nau' => 'na', 'nav' => 'nv', 'nbl' => 'nr', 'nde' => 'nd', 'ndo' => 'ng', 'nep' => 'ne', 'nno' => 'nn', 'nob' => 'nb', 'nor' => 'no',
                'nya' => 'ny', 'oci' => 'oc', 'oji' => 'oj', 'ori' => 'or', 'orm' => 'om', 'oss' => 'os', 'pan' => 'pa', 'fas' => 'fa', 'pli' => 'pi', 'pol' => 'pl', 'por' => 'pt',
                'pus' => 'ps', 'que' => 'qu', 'roh' => 'rm', 'ron' => 'ro', 'run' => 'rn', 'rus' => 'ru', 'sag' => 'sg', 'san' => 'sa', 'sin' => 'si', 'slk' => 'sk', 'slv' => 'sl',
                'sme' => 'se', 'smo' => 'sm', 'sna' => 'sn', 'snd' => 'sd', 'som' => 'so', 'sot' => 'st', 'spa' => 'es', 'srd' => 'sc', 'srp' => 'sr', 'ssw' => 'ss', 'sun' => 'su',
                'swa' => 'sw', 'swe' => 'sv', 'tah' => 'ty', 'tam' => 'ta', 'tat' => 'tt', 'tel' => 'te', 'tgk' => 'tg', 'tgl' => 'tl', 'tha' => 'th', 'bod' => 'bo', 'tir' => 'ti',
                'ton' => 'to', 'tsn' => 'tn', 'tso' => 'ts', 'tuk' => 'tk', 'tur' => 'tr', 'twi' => 'tw', 'uig' => 'ug', 'ukr' => 'uk', 'urd' => 'ur', 'uzb' => 'uz', 'ven' => 've',
                'vie' => 'vi', 'vol' => 'vo', 'cym' => 'cy', 'wln' => 'wa', 'wol' => 'wo', 'xho' => 'xh', 'yid' => 'yi', 'yor' => 'yo', 'zha' => 'za', 'zul' => 'zu');
            $langs1 = array();
            foreach ($mapping as $c2 => $c1) {
                $langs1[$c1] = $langs2[$c2];
            }
            ksort($langs1);
            return $langs1;

        } else {
            debugging('Unsupported $standard parameter in get_list_of_languages() method: '.$standard);
        }

        return array();
    }

    /**
     * Checks if the translation exists for the language
     *
     * @param string $lang moodle translation language code
     * @param bool $includeall include also disabled translations
     * @return bool true if exists
     */
    public function translation_exists($lang, $includeall = true) {
        $translations = $this->get_list_of_translations($includeall);
        return isset($translations[$lang]);
    }

    /**
     * Returns localised list of installed translations
     *
     * @param bool $returnall return all or just enabled
     * @return array moodle translation code => localised translation name
     */
    public function get_list_of_translations($returnall = false) {
        global $CFG;

        $languages = array();

        $cachekey = 'list_'.$this->get_key_suffix();
        $cachedlist = $this->menucache->get($cachekey);
        if ($cachedlist !== false) {
            // The cache content is invalid.
            if ($returnall or empty($this->translist)) {
                return $cachedlist;
            }
            // Return only enabled translations.
            foreach ($cachedlist as $langcode => $langname) {
                if (array_key_exists($langcode, $this->translist)) {
                    $languages[$langcode] = !empty($this->transaliases[$langcode]) ? $this->transaliases[$langcode] : $langname;
                }
            }

            // If there are no valid enabled translations, then return all languages.
            if (!empty($languages)) {
                return $languages;
            } else {
                return $cachedlist;
            }
        }

        // Get all languages available in system.
        $langdirs = get_list_of_plugins('', 'en', $this->otherroot);
        $langdirs["$CFG->dirroot/lang/en"] = 'en';

        // We use left to right mark to demark the shortcodes contained in LTR brackets, but we need to do
        // this hacky thing to have the utf8 char until we go php7 minimum and can simply put \u200E in
        // a double quoted string.
        $lrm = json_decode('"\u200E"');

        // Loop through all langs and get info.
        foreach ($langdirs as $lang) {
            if (strrpos($lang, '_local') !== false) {
                // Just a local tweak of some other lang pack.
                continue;
            }
            if (strrpos($lang, '_utf8') !== false) {
                // Legacy 1.x lang pack.
                continue;
            }
            if ($lang !== clean_param($lang, PARAM_SAFEDIR)) {
                // Invalid lang pack name!
                continue;
            }
            $string = $this->load_component_strings('langconfig', $lang);
            if (!empty($string['thislanguage'])) {
                $languages[$lang] = $string['thislanguage'].' '.$lrm.'('. $lang .')'.$lrm;
            }
        }

        core_collator::asort($languages);

        // Cache the list so that it can be used next time.
        $this->menucache->set($cachekey, $languages);

        if ($returnall or empty($this->translist)) {
            return $languages;
        }

        $cachedlist = $languages;

        // Return only enabled translations.
        $languages = array();
        foreach ($cachedlist as $langcode => $langname) {
            if (isset($this->translist[$langcode])) {
                $languages[$langcode] = !empty($this->transaliases[$langcode]) ? $this->transaliases[$langcode] : $langname;
            }
        }

        // If there are no valid enabled translations, then return all languages.
        if (!empty($languages)) {
            return $languages;
        } else {
            return $cachedlist;
        }
    }

    /**
     * Returns localised list of currencies.
     *
     * @param string $lang moodle translation language, null means use current
     * @return array currency code => localised currency name
     */
    public function get_list_of_currencies($lang = null) {
        if ($lang === null) {
            $lang = current_language();
        }

        $currencies = $this->load_component_strings('core_currencies', $lang);
        asort($currencies);

        return $currencies;
    }

    /**
     * Clears both in-memory and on-disk caches
     * @param bool $phpunitreset true means called from our PHPUnit integration test reset
     */
    public function reset_caches($phpunitreset = false) {
        // Clear the on-disk disk with aggregated string files.
        $this->cache->purge();
        $this->menucache->purge();

        if (!$phpunitreset) {
            // Increment the revision counter.
            $langrev = get_config('core', 'langrev');
            $next = time();
            if ($langrev !== false and $next <= $langrev and $langrev - $next < 60*60) {
                // This resolves problems when reset is requested repeatedly within 1s,
                // the < 1h condition prevents accidental switching to future dates
                // because we might not recover from it.
                $next = $langrev+1;
            }
            set_config('langrev', $next);
        }

        // Lang packs use PHP files in dataroot, it is better to invalidate opcode caches.
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * Returns cache key suffix, this enables us to store string + lang menu
     * caches in local caches on cluster nodes. We can not use prefix because
     * it would cause problems when creating subdirs in cache file store.
     * @return string
     */
    protected function get_key_suffix() {
        $rev = $this->get_revision();
        if ($rev < 0) {
            // Simple keys do not like minus char.
            $rev = 0;
        }

        return $rev;
    }

    /**
     * Returns string revision counter, this is incremented after any string cache reset.
     * @return int lang string revision counter, -1 if unknown
     */
    public function get_revision() {
        global $CFG;
        if (empty($CFG->langstringcache)) {
            return -1;
        }
        if (isset($CFG->langrev)) {
            return (int)$CFG->langrev;
        } else {
            return -1;
        }
    }

    /**
     * Helper method that recursively loads all parents of the given language.
     *
     * @see self::get_language_dependencies()
     * @param string $lang language code
     * @param array $stack list of parent languages already populated in previous recursive calls
     * @return array list of all parents of the given language with the $lang itself added as the last element
     */
    protected function populate_parent_languages($lang, array $stack = array()) {

        // English does not have a parent language.
        if ($lang === 'en') {
            return $stack;
        }

        // Prevent circular dependency (and thence the infinitive recursion loop).
        if (in_array($lang, $stack)) {
            return $stack;
        }

        // Load language configuration and look for the explicit parent language.
        if (!file_exists("$this->otherroot/$lang/langconfig.php")) {
            return $stack;
        }
        $string = array();
        include("$this->otherroot/$lang/langconfig.php");

        if (empty($string['parentlanguage']) or $string['parentlanguage'] === 'en') {
            return array_merge(array($lang), $stack);

        }

        $parentlang = $string['parentlanguage'];
        return $this->populate_parent_languages($parentlang, array_merge(array($lang), $stack));
    }
}
