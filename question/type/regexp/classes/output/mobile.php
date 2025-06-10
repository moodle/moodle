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
 * Mobile output class for question type regexp.
 *
 * @package qtype_regexp
 * @copyright 2018 Joseph Rézeau
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_regexp\output;

/**
 * Mobile output class for question type regexp.
 * @copyright 2018 Joseph Rézeau
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mobile {
    /**
     * Returns the regexp question view for the mobile app.
     *
     * @param mixed $args
     * @return void
     */
    public static function regexp_view($args) {
        global $CFG;

        $args = (object) $args;
        $versionname = $args->appversioncode >= 3950 ? 'latest' : 'ionic3';

        return [
            'templates' => [[
                'id' => 'main',
                'html' => file_get_contents($CFG->dirroot . "/question/type/regexp/mobile/regexp_$versionname.html"),
            ]],
            'javascript' => file_get_contents($CFG->dirroot . '/question/type/regexp/mobile/regexp.js'),
        ];
    }
}
