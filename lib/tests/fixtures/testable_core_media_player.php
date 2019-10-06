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
 * Fixture for testing the functionality of core_media_player.
 *
 * @package     core
 * @subpackage  fixtures
 * @category    test
 * @copyright   2012 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Media player stub for testing purposes.
 */
class media_test_plugin extends core_media_player {
    /** @var array Array of supported extensions */
    public $ext;
    /** @var int Player rank */
    public $rank;
    /** @var int Arbitrary number */
    public $num;

    /**
     * @param int $num Number (used in output)
     * @param int $rank Player rank
     * @param array $ext Array of supported extensions
     */
    public function __construct($num = 1, $rank = 13, $ext = array('mp3', 'flv', 'f4v', 'mp4')) {
        $this->ext = $ext;
        $this->rank = $rank;
        $this->num = $num;
    }

    public function embed($urls, $name, $width, $height, $options) {
        self::pick_video_size($width, $height);
        $contents = "\ntestsource=". join("\ntestsource=", $urls) .
            "\ntestname=$name\ntestwidth=$width\ntestheight=$height\n<!--FALLBACK-->\n";
        return html_writer::span($contents, 'mediaplugin mediaplugin_test');
    }

    public function get_supported_extensions() {
        return $this->ext;
    }

    public function get_rank() {
        return 10;
    }
}