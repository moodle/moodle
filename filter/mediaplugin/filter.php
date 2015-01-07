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
 *  Media plugin filtering
 *
 *  This filter will replace any links to a media file with
 *  a media plugin that plays that media inline
 *
 * @package    filter
 * @subpackage mediaplugin
 * @copyright  2004 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Automatic media embedding filter class.
 *
 * It is highly recommended to configure servers to be compatible with our slasharguments,
 * otherwise the "?d=600x400" may not work.
 *
 * @package    filter
 * @subpackage mediaplugin
 * @copyright  2004 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_mediaplugin extends moodle_text_filter {
    /** @var bool True if currently filtering trusted text */
    private $trusted;
    /** @var core_media_renderer Media renderer */
    private $mediarenderer;
    /** @var string Partial regex pattern indicating possible embeddable content */
    private $embedmarkers;

    public function filter($text, array $options = array()) {
        global $CFG, $PAGE;

        if (!is_string($text) or empty($text)) {
            // non string data can not be filtered anyway
            return $text;
        }

        if (stripos($text, '</a>') === false) {
            // Performance shortcut - if not </a> tag, nothing can match.
            return $text;
        }

        if (!$this->mediarenderer) {
            $this->mediarenderer = $PAGE->get_renderer('core', 'media');
            $this->embedmarkers = $this->mediarenderer->get_embeddable_markers();
        }

        // Check SWF permissions.
        $this->trusted = !empty($options['noclean']) or !empty($CFG->allowobjectembed);

        // Looking for tags.
        $matches = preg_split('/(<[^>]*>)/i', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        if (!$matches) {
            return $text;
        }

        // Regex to find media extensions in an <a> tag.
        $re = '~<a\s[^>]*href="([^"]*(?:' .  $this->embedmarkers . ')[^"]*)"[^>]*>([^>]*)</a>~is';

        $newtext = '';
        $validtag = '';
        $sizeofmatches = count($matches);

        // We iterate through the given string to find valid <a> tags
        // and build them so that the callback function can check it for
        // embedded content. Then we rebuild the string.
        foreach ($matches as $idx => $tag) {
            if (preg_match('|</a>|', $tag) && !empty($validtag)) {
                $validtag .= $tag;

                // Given we now have a valid <a> tag to process it's time for
                // ReDoS protection. Stop processing if a word is too large.
                if (strlen($validtag) < 4096) {
                    $processed = preg_replace_callback($re, array($this, 'callback'), $validtag);
                }
                // Rebuilding the string with our new processed text.
                $newtext .= !empty($processed) ? $processed : $validtag;
                // Wipe it so we can catch any more instances to filter.
                $validtag = '';
                $processed = '';
            } else if (preg_match('/<a\s[^>]*/', $tag) && $sizeofmatches > 1) {
                // Looking for a starting <a> tag.
                $validtag = $tag;
            } else {
                // If we have a validtag add to that to process later,
                // else add straight onto our newtext string.
                if (!empty($validtag)) {
                    $validtag .= $tag;
                } else {
                    $newtext .= $tag;
                }
            }
        }

        // Return the same string except processed by the above.
        return $newtext;
    }

    /**
     * Replace link with embedded content, if supported.
     *
     * @param array $matches
     * @return string
     */
    private function callback(array $matches) {
        global $CFG, $PAGE;
        // Check if we ignore it.
        if (preg_match('/class="[^"]*nomediaplugin/i', $matches[0])) {
            return $matches[0];
        }

        // Get name.
        $name = trim($matches[2]);
        if (empty($name) or strpos($name, 'http') === 0) {
            $name = ''; // Use default name.
        }

        // Split provided URL into alternatives.
        $urls = core_media::split_alternatives($matches[1], $width, $height);

        $options = array();

        // Allow SWF (or not).
        if ($this->trusted) {
            $options[core_media::OPTION_TRUSTED] = true;
        }

        // We could test whether embed is possible using can_embed, but to save
        // time, let's just embed it with the 'fallback to blank' option which
        // does most of the same stuff anyhow.
        $options[core_media::OPTION_FALLBACK_TO_BLANK] = true;

        // NOTE: Options are not passed through from filter because the 'embed'
        // code does not recognise filter options (it's a different kind of
        // option-space) as it can be used in non-filter situations.
        $result = $this->mediarenderer->embed_alternatives($urls, $name, $width, $height, $options);

        // If something was embedded, return it, otherwise return original.
        if ($result !== '') {
            return $result;
        } else {
            return $matches[0];
        }
    }
}
