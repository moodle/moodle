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

namespace filter_tex;

use core\context\system as context_system;
use core\exception\coding_exception;
use core\output\actions\popup_action;
use core\url;
use core_useragent;
use stdClass;

/**
 * Moodle - Filter for converting TeX expressions to cached gif images
 *
 * This Moodle text filter converts TeX expressions delimited
 * by either $$...$$ or by <tex...>...</tex> tags to gif images using
 * mimetex.cgi obtained from http: *www.forkosh.com/mimetex.html authored by
 * John Forkosh john@forkosh.com.  Several binaries of this areincluded with
 * this distribution.
 * Note that there may be patent restrictions on the production of gif images
 * in Canada and some parts of Western Europe and Japan until July 2004.
 *
 * @package    filter_tex
 * @subpackage tex
 * @copyright  2004 Zbigniew Fiedorowicz fiedorow@math.ohio-state.edu
 *             Originally based on code provided by Bruno Vernier bruno@vsbeducation.ca
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_filter extends \core_filters\text_filter {
    #[\Override]
    public function filter($text, array $options = []) {
        global $CFG, $DB;

        // Do a quick check using stripos to avoid unnecessary work.
        if (
            (!preg_match('/<tex/i', $text)) &&
                (strpos($text, '$$') === false) &&
                (strpos($text, '\\[') === false) &&
                (strpos($text, '\\(') === false) &&
                (!preg_match('/\[tex/i', $text))
        ) {
            return $text;
        }

        $text .= ' ';
        preg_match_all('/\$(\$\$+?)([^\$])/s', $text, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $replacement = str_replace('$', '&#x00024;', $matches[1][$i]) . $matches[2][$i];
            $text = str_replace($matches[0][$i], $replacement, $text);
        }

        // The following regular expression matches TeX expressions delimited by:
        // <tex> TeX expression </tex>
        // or <tex alt="My alternative text to be used instead of the TeX form"> TeX expression </tex>
        // or $$ TeX expression $$
        // or \[ TeX expression \]          // original tag of MathType and TeXaide
        // or [tex] TeX expression [/tex]   // somtime it's more comfortable than <tex>.
        $rules = [
            '<tex(?:\s+alt=["\'](.*?)["\'])?>(.+?)<\/tex>',
            '\$\$(.+?)\$\$',
            '\\\\\[(.+?)\\\\\]',
            '\\\\\((.+?)\\\\\)',
            '\\[tex\\](.+?)\\[\/tex\\]',
        ];
        $megarule = '/' . implode('|', $rules) . '/is';
        preg_match_all($megarule, $text, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $texexp = '';
            for ($j = 0; $j < count($rules); $j++) {
                $texexp .= $matches[$j + 2][$i];
            }
            $alt = $matches[1][$i];
            $texexp = str_replace('<nolink>', '', $texexp);
            $texexp = str_replace('</nolink>', '', $texexp);
            $texexp = str_replace('<span class="nolink">', '', $texexp);
            $texexp = str_replace('</span>', '', $texexp);
            $texexp = preg_replace("/<br[[:space:]]*\/?>/i", '', $texexp);
            $align = "middle";
            if (preg_match('/^align=bottom /', $texexp)) {
                $align = "text-bottom";
                $texexp = preg_replace('/^align=bottom /', '', $texexp);
            } else if (preg_match('/^align=top /', $texexp)) {
                $align = "text-top";
                $texexp = preg_replace('/^align=top /', '', $texexp);
            }

            // Decode entities encoded by editor, luckily there is very little chance of double decoding.
            $texexp = html_entity_decode($texexp, ENT_QUOTES, 'UTF-8');

            if ($texexp === '') {
                continue;
            }

            // Sanitize the decoded string, because $this->get_image_markup() injects the final string between script tags.
            $texexp = clean_param($texexp, PARAM_TEXT);

            $md5 = md5($texexp);
            if (!$DB->record_exists("cache_filters", ["filter" => "tex", "md5key" => $md5])) {
                $texcache = new stdClass();
                $texcache->filter = 'tex';
                $texcache->version = 1;
                $texcache->md5key = $md5;
                $texcache->rawtext = $texexp;
                $texcache->timemodified = time();
                $DB->insert_record("cache_filters", $texcache, false);
            }
            $convertformat = get_config('filter_tex', 'convertformat');
            if ($convertformat == 'svg' && !core_useragent::supports_svg()) {
                $convertformat = 'png';
            }
            $filename = $md5 . ".{$convertformat}";
            $text = str_replace($matches[0][$i], $this->get_image_markup($filename, $texexp, 0, 0, $align, $alt), $text);
        }
        return $text;
    }

    /**
     * Create image link.
     *
     * @param string $imagefile name of file
     * @param string $tex TeX notation (html entities already decoded)
     * @param int $height O means automatic
     * @param int $width O means automatic
     * @param string $align
     * @param string $alt
     * @return string HTML markup
     */
    protected function get_image_markup(
        string $imagefile,
        string $tex,
        int $height,
        int $width,
        string $align,
        string $alt,
    ): string {
        global $CFG, $OUTPUT;

        if (!$imagefile) {
            throw new coding_exception('Image file argument empty in get_image_markup()');
        }

        // Work out any necessary inline style.
        $rules = [];
        if ($align !== 'middle') {
            $rules[] = 'vertical-align:' . $align . ';';
        }
        if ($height) {
            $rules[] = 'height:' . $height . 'px;';
        }
        if ($width) {
            $rules[] = 'width:' . $width . 'px;';
        }
        if (!empty($rules)) {
            $style = ' style="' . implode('', $rules) . '" ';
        } else {
            $style = '';
        }

        // Prepare the title attribute.
        // Note that we retain the title tag as TeX format rather than using
        // the alt text, even if supplied. The alt text is intended for blind
        // users (to provide a text equivalent to the equation) while the title
        // is there as a convenience for sighted users who want to see the TeX
        // code.
        $title = 'title="' . s($tex) . '"';

        if ($alt === '') {
            $alt = s($tex);
        } else {
            $alt = s(html_entity_decode($tex, ENT_QUOTES, 'UTF-8'));
        }

        // Build the output.
        $anchorcontents = "<img class=\"texrender\" $title alt=\"$alt\" src=\"";
        if ($CFG->slasharguments) {
            // Use this method if possible for better client-side caching.
            $anchorcontents .= "$CFG->wwwroot/filter/tex/pix.php/$imagefile";
        } else {
            $anchorcontents .= "$CFG->wwwroot/filter/tex/pix.php?file=$imagefile";
        }
        $anchorcontents .= "\" $style/>";

        $imagefound = file_exists("$CFG->dataroot/filter/tex/$imagefile");
        if (!$imagefound && has_capability('moodle/site:config', context_system::instance())) {
            $link = '/filter/tex/texdebug.php';
            $action = null;
        } else {
            $link = new url('/filter/tex/displaytex.php', ['texexp' => $tex]);
            $action = new popup_action('click', $link, 'popup', ['width' => 320, 'height' => 240]);
        }
        // TODO: the popups do not work when text caching is enabled.
        $output = $OUTPUT->action_link($link, $anchorcontents, $action, ['title' => 'TeX']);
        $output = "<span class=\"MathJax_Preview\">$output</span><script type=\"math/tex\">$tex</script>";

        return $output;
    }
}
