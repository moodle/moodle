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
 * This filter provides automatic support for MathJax
 *
 * @package    filter_mathjaxloader
 * @copyright  2013 Damyon Wiese (damyon@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Mathjax filtering
 */
class filter_mathjaxloader extends moodle_text_filter {

    /*
     * Perform a mapping of the moodle language code to the equivalent for MathJax.
     *
     * @param string $moodlelangcode - The moodle language code - e.g. en_pirate
     * @return string The MathJax language code.
     */
    public function map_language_code($moodlelangcode) {
        $mathjaxlangcodes = array('br',
                                  'cdo',
                                  'cs',
                                  'da',
                                  'de',
                                  'en',
                                  'eo',
                                  'es',
                                  'fa',
                                  'fi',
                                  'fr',
                                  'gl',
                                  'he',
                                  'ia',
                                  'it',
                                  'ja',
                                  'ko',
                                  'lb',
                                  'mk',
                                  'nl',
                                  'oc',
                                  'pl',
                                  'pt',
                                  'pt-br',
                                  'ru',
                                  'sl',
                                  'sv',
                                  'tr',
                                  'uk',
                                  'zh-hans');
        $exceptions = array('cz' => 'cs');

        // First see if this is an exception.
        if (isset($exceptions[$moodlelangcode])) {
            $moodlelangcode = $exceptions[$moodlelangcode];
        }

        // Now look for an exact lang string match.
        if (in_array($moodlelangcode, $mathjaxlangcodes)) {
            return $moodlelangcode;
        }

        // Now try shortening the moodle lang string.
        $moodlelangcode = preg_replace('/-.*/', '', $moodlelangcode);
        // Look for a match on the shortened string.
        if (in_array($moodlelangcode, $mathjaxlangcodes)) {
            return $moodlelangcode;
        }
        // All failed - use english.
        return 'en';
    }

    /*
     * Add the javascript to enable mathjax processing on this page.
     *
     * @param moodle_page $page The current page.
     * @param context $context The current context.
     */
    public function setup($page, $context) {
        // This only requires execution once per request.
        static $jsinitialised = false;

        if (empty($jsinitialised)) {
            if (is_https()) {
                $url = get_config('filter_mathjaxloader', 'httpsurl');
            } else {
                $url = get_config('filter_mathjaxloader', 'httpurl');
            }
            $lang = $this->map_language_code(current_language());
            $url = new moodle_url($url, array('delayStartupUntil' => 'configured'));

            $moduleconfig = array(
                'name' => 'mathjax',
                'fullpath' => $url
            );

            $page->requires->js_module($moduleconfig);

            $config = get_config('filter_mathjaxloader', 'mathjaxconfig');

            $params = array('mathjaxconfig' => $config, 'lang' => $lang);

            $page->requires->yui_module('moodle-filter_mathjaxloader-loader', 'M.filter_mathjaxloader.configure', array($params));

            $jsinitialised = true;
        }
    }

    /*
     * This function wraps the filtered text in a span, that mathjaxloader is configured to process.
     *
     * @param string $text The text to filter.
     * @param array $options The filter options.
     */
    public function filter($text, array $options = array()) {
        global $PAGE;

        $legacy = get_config('filter_mathjaxloader', 'texfiltercompatibility');
        $extradelimiters = explode(',', get_config('filter_mathjaxloader', 'additionaldelimiters'));
        if ($legacy) {
            // This replaces any of the tex filter maths delimiters with the default for inline maths in MathJAX "\( blah \)".
            // E.g. "<tex.*> blah </tex>".
            $text = preg_replace('|<(/?) *tex( [^>]*)?>|u', '[\1tex]', $text);
            // E.g. "[tex.*] blah [/tex]".
            $text = str_replace('[tex]', '\\(', $text);
            $text = str_replace('[/tex]', '\\)', $text);
            // E.g. "$$ blah $$".
            $text = preg_replace('|\$\$([\S\s]*?)\$\$|u', '\\(\1\\)', $text);
            // E.g. "\[ blah \]".
            $text = str_replace('\\[', '\\(', $text);
            $text = str_replace('\\]', '\\)', $text);
        }

        $hasinline = strpos($text, '\\(') !== false && strpos($text, '\\)') !== false;
        $hasdisplay = (strpos($text, '$$') !== false) ||
                      (strpos($text, '\\[') !== false && strpos($text, '\\]') !== false);

        $hasextra = false;

        foreach ($extradelimiters as $extra) {
            if ($extra && strpos($text, $extra) !== false) {
                $hasextra = true;
                break;
            }
        }
        if ($hasinline || $hasdisplay || $hasextra) {
            $PAGE->requires->yui_module('moodle-filter_mathjaxloader-loader', 'M.filter_mathjaxloader.typeset');
            return '<span class="nolink"><span class="filter_mathjaxloader_equation">' . $text . '</span></span>';
        }
        return $text;
    }
}
