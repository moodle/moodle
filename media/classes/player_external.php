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
 * Base class for players which handle external links
 *
 * @package   core_media
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for players which handle external links (YouTube etc).
 *
 * As opposed to media files.
 *
 * @package   core_media
 * @copyright 2016 Marina Glancy
 * @author    2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class core_media_player_external extends core_media_player {
    /**
     * Array of matches from regular expression - subclass can assume these
     * will be valid when the embed function is called, to save it rerunning
     * the regex.
     * @var array
     */
    protected $matches;

    /**
     * Part of a regular expression, including ending ~ symbol (note: these
     * regexes use ~ instead of / because URLs and HTML code typically include
     * / symbol and makes harder to read if you have to escape it).
     * Matches the end part of a link after you have read the 'important' data
     * including optional #d=400x300 at end of url, plus content of <a> tag,
     * up to </a>.
     * @var string
     */
    const END_LINK_REGEX_PART = '[^#]*(#d=([\d]{1,4})x([\d]{1,4}))?~si';

    public function embed($urls, $name, $width, $height, $options) {
        return $this->embed_external(reset($urls), $name, $width, $height, $options);
    }

    /**
     * Obtains HTML code to embed the link.
     * @param moodle_url $url Single URL to embed
     * @param string $name Display name; '' to use default
     * @param int $width Optional width; 0 to use default
     * @param int $height Optional height; 0 to use default
     * @param array $options Options array
     * @return string HTML code for embed
     */
    protected abstract function embed_external(moodle_url $url, $name, $width, $height, $options);

    public function list_supported_urls(array $urls, array $options = array()) {
        // These only work with a SINGLE url (there is no fallback).
        if (count($urls) != 1) {
            return array();
        }
        $url = reset($urls);

        // Check against regex.
        if (preg_match($this->get_regex(), $url->out(false), $this->matches)) {
            return array($url);
        }

        return array();
    }

    /**
     * Returns regular expression used to match URLs that this player handles
     * @return string PHP regular expression e.g. '~^https?://example.org/~'
     */
    protected function get_regex() {
        return '~^unsupported~';
    }

    /**
     * Annoyingly, preg_match $matches result does not always have the same
     * number of parameters - it leaves out optional ones at the end. WHAT.
     * Anyway, this function can be used to fix it.
     * @param array $matches Array that should be adjusted
     * @param int $count Number of capturing groups (=6 to make $matches[6] work)
     */
    protected static function fix_match_count(&$matches, $count) {
        for ($i = count($matches); $i <= $count; $i++) {
            $matches[$i] = false;
        }
    }
}
