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

        // List of language codes found in the MathJax/localization/ directory.
        $mathjaxlangcodes = [
            'ar', 'ast', 'bcc', 'bg', 'br', 'ca', 'cdo', 'ce', 'cs', 'cy', 'da', 'de', 'diq', 'en', 'eo', 'es', 'fa',
            'fi', 'fr', 'gl', 'he', 'ia', 'it', 'ja', 'kn', 'ko', 'lb', 'lki', 'lt', 'mk', 'nl', 'oc', 'pl', 'pt',
            'pt-br', 'qqq', 'ru', 'scn', 'sco', 'sk', 'sl', 'sv', 'th', 'tr', 'uk', 'vi', 'zh-hans', 'zh-hant'
        ];

        // List of explicit mappings and known exceptions (moodle => mathjax).
        $explicit = [
            'cz' => 'cs',
            'pt_br' => 'pt-br',
            'zh_tw' => 'zh-hant',
            'zh_cn' => 'zh-hans',
        ];

        // If defined, explicit mapping takes the highest precedence.
        if (isset($explicit[$moodlelangcode])) {
            return $explicit[$moodlelangcode];
        }

        // If there is exact match, it will be probably right.
        if (in_array($moodlelangcode, $mathjaxlangcodes)) {
            return $moodlelangcode;
        }

        // Finally try to find the best matching mathjax pack.
        $parts = explode('_', $moodlelangcode, 2);
        if (in_array($parts[0], $mathjaxlangcodes)) {
            return $parts[0];
        }

        // No more guessing, use English.
        return 'en';
    }

    /*
     * Add the javascript to enable mathjax processing on this page.
     *
     * @param moodle_page $page The current page.
     * @param context $context The current context.
     */
    public function setup($page, $context) {

        if ($page->requires->should_create_one_time_item_now('filter_mathjaxloader-scripts')) {
            $url = get_config('filter_mathjaxloader', 'httpsurl');
            $lang = $this->map_language_code(current_language());
            $url = new moodle_url($url, array('delayStartupUntil' => 'configured'));

            $moduleconfig = array(
                'name' => 'mathjax',
                'fullpath' => $url
            );

            $page->requires->js_module($moduleconfig);

            $config = get_config('filter_mathjaxloader', 'mathjaxconfig');
            $wwwroot = new moodle_url('/');

            $config = str_replace('{wwwroot}', $wwwroot->out(true), $config);

            $params = array('mathjaxconfig' => $config, 'lang' => $lang);

            $page->requires->yui_module('moodle-filter_mathjaxloader-loader', 'M.filter_mathjaxloader.configure', array($params));
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

        $hasextra = false;
        foreach ($extradelimiters as $extra) {
            if ($extra && strpos($text, $extra) !== false) {
                $hasextra = true;
                break;
            }
        }

        $hasdisplayorinline = false;
        if ($hasextra) {
            // If custom dilimeters are used, wrap whole text to prevent autolinking.
            $text = '<span class="nolink">' . $text . '</span>';
        } else if (preg_match('/\\\\[[(]/', $text) || preg_match('/\$\$/', $text)) {
            // Only parse the text if there are mathjax symbols in it. The recognized
            // math environments are \[ \] and $$ $$ for display mathematics and \( \)
            // for inline mathematics.
            // Note: 2 separate regexes seems to perform better here than using a single
            // regex with groupings.

            // Wrap display and inline math environments in nolink spans.
            // Do not wrap nested environments, i.e., if inline math is nested
            // inside display math, only the outer display math is wrapped in
            // a span. The span HTML inside a LaTex math environment would break
            // MathJax. See MDL-61981.
            list($text, $hasdisplayorinline) = $this->wrap_math_in_nolink($text);
        }

        if ($hasdisplayorinline || $hasextra) {
            $PAGE->requires->yui_module('moodle-filter_mathjaxloader-loader', 'M.filter_mathjaxloader.typeset');
            return '<span class="filter_mathjaxloader_equation">' . $text . '</span>';
        }
        return $text;
    }

    /**
     * Find math environments in the $text and wrap them in no link spans
     * (<span class="nolink"></span>). If math environments are nested, only
     * the outer environment is wrapped in the span.
     *
     * The recognized math environments are \[ \] and $$ $$ for display
     * mathematics and \( \) for inline mathematics.
     *
     * @param string $text The text to filter.
     * @return array An array containing the potentially modified text and
     * a boolean that is true if any changes were made to the text.
     */
    protected function wrap_math_in_nolink($text) {
        $i = 1;
        $len = strlen($text);
        $displaystart = -1;
        $displaybracket = false;
        $displaydollar = false;
        $inlinestart = -1;
        $changesdone = false;
        // Loop over the $text once.
        while ($i < $len) {
            if ($displaystart === -1) {
                // No display math has started yet.
                if ($text[$i - 1] === '\\' && $text[$i] === '[') {
                    // Display mode \[ begins.
                    $displaystart = $i - 1;
                    $displaybracket = true;
                } else if ($text[$i - 1] === '$' && $text[$i] === '$') {
                    // Display mode $$ begins.
                    $displaystart = $i - 1;
                    $displaydollar = true;
                } else if ($text[$i - 1] === '\\' && $text[$i] === '(') {
                    // Inline math \( begins, not nested inside display math.
                    $inlinestart = $i - 1;
                } else if ($text[$i - 1] === '\\' && $text[$i] === ')' && $inlinestart > -1) {
                    // Inline math ends, not nested inside display math.
                    // Wrap the span around it.
                    $text = $this->insert_span($text, $inlinestart, $i);

                    $inlinestart = -1; // Reset.
                    $i += 28; // The $text length changed due to the <span>.
                    $len += 28;
                    $changesdone = true;
                }
            } else {
                // Display math open.
                if (($text[$i - 1] === '\\' && $text[$i] === ']' && $displaybracket) ||
                        ($text[$i - 1] === '$' && $text[$i] === '$' && $displaydollar)) {
                    // Display math ends, wrap the span around it.
                    $text = $this->insert_span($text, $displaystart, $i);

                    $displaystart = -1; // Reset.
                    $displaybracket = false;
                    $displaydollar = false;
                    $i += 28; // The $text length changed due to the <span>.
                    $len += 28;
                    $changesdone = true;
                }
            }

            ++$i;
        }
        return array($text, $changesdone);
    }

    /**
     * Wrap a portion of the $text inside a no link span
     * (<span class="nolink"></span>). The whole text is then returned.
     *
     * @param string $text The text to modify.
     * @param int $start The start index of the substring in $text that should
     * be wrapped in the span.
     * @param int $end The end index of the substring in $text that should be
     * wrapped in the span.
     * @return string The whole $text with the span inserted around
     * the defined substring.
     */
    protected function insert_span($text, $start, $end) {
        return substr_replace($text,
                '<span class="nolink">'. substr($text, $start, $end - $start + 1) .'</span>',
                $start,
                $end - $start + 1);
    }
}
