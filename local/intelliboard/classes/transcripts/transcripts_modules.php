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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\transcripts;

class transcripts_modules extends \local_intelliboard\transcripts\transcripts {

    const STATUS_INPROGRESS     = 0;
    const STATUS_COMPLETED      = 1;
    const STATUS_CLOSED         = 2;
    const STATUS_PASSED         = 3;
    const STATUS_FAILED         = 4;

    public $table = 'local_intelliboard_trns_m';

    public function get_statuses() {
        return [
            self::STATUS_INPROGRESS     => get_string('inprogress', 'local_intelliboard'),
            self::STATUS_COMPLETED      => get_string('completed', 'local_intelliboard'),
            self::STATUS_CLOSED         => get_string('closed', 'local_intelliboard'),
            self::STATUS_PASSED         => get_string('passed', 'local_intelliboard'),
            self::STATUS_FAILED         => get_string('failed', 'local_intelliboard'),
        ];
    }

}
