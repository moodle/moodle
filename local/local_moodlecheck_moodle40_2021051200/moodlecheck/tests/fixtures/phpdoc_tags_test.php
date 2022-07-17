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
 * A fixture to verify various phpdoc tags in a tests location.
 *
 * @package   local_moodlecheck
 * @copyright 2018 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * A fixture to verify various phpdoc tags in a tests location.
 *
 * @package   local_moodlecheck
 * @copyright 2018 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fixturing_tests {

    /**
     * Some valid tags, to verify they are ok.
     *
     * @license
     * @throws
     * @deprecated
     * @author
     * @todo
     * @codingStandardsIgnoreLine
     */
    public function all_valid_tags() {
        echo "yay!";
    }

    /**
     * Some more valid tags, because we are under tests area.
     *
     * @covers
     * @dataProvider
     * @group
     */
    public function also_all_valid_tags() {
        echo "reyay!";
    }

    /**
     * Some invalid tags.
     *
     * @small
     * @zzzing
     * @inheritdoc
     */
    public function all_invalid_tags() {
        echo "yoy!";
    }
}
