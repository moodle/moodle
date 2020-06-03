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
 * Display H5P filter
 *
 * @package    filter_displayh5p
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

use core_h5p\local\library\autoloader;

/**
 * Display H5P filter
 *
 * This filter will replace any occurrence of H5P URLs with the corresponding H5P content embed code
 *
 * @package    filter_displayh5p
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_displayh5p extends moodle_text_filter {

    /**
     * @var boolean $loadresizerjs This is whether to request the resize.js script.
     */
    private static $loadresizerjs = true;

    /**
     * Function filter replaces any h5p-sources.
     *
     * @param  string $text    HTML content to process
     * @param  array  $options options passed to the filters
     * @return string
     */
    public function filter($text, array $options = array()) {
        global $CFG;

        if (!is_string($text) or empty($text)) {
            // Non string data can not be filtered anyway.
            return $text;
        }

        // We are trying to minimize performance impact checking there's some H5P related URL.
        $h5purl = '(http[^ &<]*h5p)';
        if (!preg_match($h5purl, $text)) {
            return $text;
        }

        $allowedsources = get_config('filter_displayh5p', 'allowedsources');
        $allowedsources = array_filter(array_map('trim', explode("\n", $allowedsources)));

        $localsource = '('.preg_quote($CFG->wwwroot, '~').'/[^ &\#"\'<]*\.h5p([?][^ "\'<]*)?[^ \#"\'<]*)';
        $allowedsources[] = $localsource;

        $params = array(
            'tagbegin' => '<iframe src="',
            'tagend' => '</iframe>'
        );

        $specialchars = ['?', '&'];
        $escapedspecialchars = ['\?', '&amp;'];
        $h5pcontents = array();
        $h5plinks = array();

        // Check all allowed sources.
        foreach ($allowedsources as $source) {
            // It is needed to add "/embed" at the end of URLs like https:://*.h5p.com/content/12345 (H5P.com).
            $params['urlmodifier'] = '';

            if (($source == $localsource)) {
                $params['tagbegin'] = '<iframe src="'.$CFG->wwwroot.'/h5p/embed.php?url=';
                $escapechars = $source;
                $ultimatepattern = $source;
            } else {
                if (!stripos($source, 'embed')) {
                    $params['urlmodifier'] = '/embed';
                }
                // Convert special chars.
                $sourceid = str_replace('[id]', '[0-9]+', $source);
                $escapechars = str_replace($specialchars, $escapedspecialchars, $sourceid);
                $ultimatepattern = '(' . $escapechars . ')';
            }

            // Improve performance creating filterobjects only when needed.
            if (!preg_match($ultimatepattern, $text)) {
                continue;
            }

            $h5pcontenturl = new filterobject($source, null, null, false,
                false, null, [$this, 'filterobject_prepare_replacement_callback'], $params);

            $h5pcontenturl->workregexp = '#'.$ultimatepattern.'#';
            $h5pcontents[] = $h5pcontenturl;

            // Regex to find h5p extensions in an <a> tag.
            $linkregexp = '~<a [^>]*href=["\']('.$escapechars.'[^"\']*)["\'][^>]*>([^<]*)</a>~is';

            $h5plinkurl = new filterobject($linkregexp, null, null, false,
                false, null, [$this, 'filterobject_prepare_replacement_callback'], $params);
            $h5plinkurl->workregexp = $linkregexp;
            $h5plinks[] = $h5plinkurl;
        }

        if (empty($h5pcontents) && empty($h5links)) {
            // No matches to deal with.
            return $text;
        }

        // Apply filter inside <a> tag href attribute.
        // We can not use filter_phrase function because it removes all tags and can not be applied in tag attributes.
        foreach ($h5plinks as $h5plink) {
            $text = preg_replace_callback($h5plink->workregexp,
                function ($matches) use ($h5plink) {
                    if ($matches[1] == $matches[2]) {
                        filter_prepare_phrase_for_replacement($h5plink);

                        return str_replace('$1', $matches[1], $h5plink->workreplacementphrase);
                    } else {
                        return $matches[0];
                    }
                }, $text);

        }

        $result = filter_phrases($text, $h5pcontents, null, null, false, true);

        // Encoding H5P file URLs.
        // embed.php page is requesting a PARAM_LOCALURL url parameter, so for files/directories use non-alphanumeric
        // characters, we need to encode the parameter. Fetch url parameter added to embed.php and encode the whole url.
        $localurl = '#\?url=([^" <]*[\/]+[^" <]*\.h5p)([?][^"]*)?#';
        $result = preg_replace_callback($localurl,
            function ($matches) {
                $baseurl = rawurlencode($matches[1]);
                // Deal with possible parameters in the url link.
                if (!empty($matches[2])) {
                    $match = explode('?', $matches[2]);
                    if (!empty($match[1])) {
                        $baseurl = $baseurl."&".$match[1];
                    }
                }
                return "?url=".$baseurl;
            }, $result);

        return $result;
    }

    /**
     * Callback used by filterobject / filter_phrases.
     *
     * @param string $tagbegin HTML to insert before any match
     * @param string $tagend HTML to insert after any match
     * @param string $urlmodifier string to add to the match URL
     * @return array [$hreftagbegin, $hreftagend, $replacementphrase] for filterobject.
     */
    public function filterobject_prepare_replacement_callback($tagbegin, $tagend, $urlmodifier) {
        $sourceurl = "$1";
        if ($urlmodifier !== "") {
            $sourceurl .= $urlmodifier;
        }

        $h5piframesrc = $sourceurl . '" class="h5p-iframe" name="h5pcontent"' .
            ' style="height:230px; width: 100%; border: 0;" allowfullscreen="allowfullscreen">';

        // We want to request the resizing script only once.
        if (self::$loadresizerjs) {
            $resizerurl = autoloader::get_h5p_core_library_url('js/h5p-resizer.js');
            $tagend .= '<script src="' . $resizerurl->out() . '"></script>';
            self::$loadresizerjs = false;
        }

        return [$tagbegin, $tagend, $h5piframesrc];
    }
}
