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
 * Fixture for testing the functionality of core_media_player_native.
 *
 * @package     core
 * @subpackage  fixtures
 * @category    test
 * @copyright   2019 Ruslan Kabalin
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Native media player stub for testing purposes.
 *
 * @copyright   2019 Ruslan Kabalin
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_test_native_plugin extends core_media_player_native {
    /** @var int Player rank */
    public $rank;
    /** @var int Arbitrary number */
    public $num;

    /**
     * Constructor is used for tuning the fixture.
     *
     * @param int $num Number (used in output)
     * @param int $rank Player rank
     */
    public function __construct($num = 1, $rank = 13) {
        $this->rank = $rank;
        $this->num = $num;
    }

    /**
     * Generates code required to embed the player.
     *
     * @param array $urls URLs of media files
     * @param string $name Display name; '' to use default
     * @param int $width Optional width; 0 to use default
     * @param int $height Optional height; 0 to use default
     * @param array $options Options array
     * @return string HTML code for embed
     */
    public function embed($urls, $name, $width, $height, $options) {
        $sources = array();
        foreach ($urls as $url) {
            $params = ['src' => $url];
            $sources[] = html_writer::empty_tag('source', $params);
        }

        $sources = implode("\n", $sources);
        $title = $this->get_name($name, $urls);
        // Escape title but prevent double escaping.
        $title = s(preg_replace(['/&amp;/', '/&gt;/', '/&lt;/'], ['&', '>', '<'], $title));

        return <<<OET
<video class="mediaplugin mediaplugin_test" title="$title">
    $sources
</video>
OET;
    }

    /**
     * Gets the ranking of this player.
     *
     * @return int Rank
     */
    public function get_rank() {
        return 10;
    }
}