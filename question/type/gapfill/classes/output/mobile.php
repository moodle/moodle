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
 * Mobile output class for qtype_gapfill
 *
 * @package    qtype_gapfill
 * @copyright  2018 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_gapfill\output;

/**
 * Mobile output class for gapfill question type
 *
 * @package    qtype_gapfill
 * @copyright  2018 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mobile {

    /**
     * Returns the gapfill question type for the quiz in the mobile app.
     * @param array $args
     * @return array
     */
    public static function mobile_get_gapfill($args) {
        global $CFG;
        $args = (object) $args;
        $folder = $args->appversioncode >= 3950 ? 'latest' : 'ionic3';
        $templatepath = $CFG->dirroot."/question/type/gapfill/mobile/$folder/addon-qtype-gapfill.html";
        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => file_get_contents($templatepath)
                    ]
            ],
            'javascript' => file_get_contents($CFG->dirroot . '/question/type/gapfill/mobile/mobile.js')
        ];
    }
}
