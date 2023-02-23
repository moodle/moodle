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

declare(strict_types=1);

use core_badges\badge;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/badgeslib.php");

/**
 * Badges test generator
 *
 * @package     core_badges
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_badges_generator extends component_generator_base {

    /**
     * Create badge
     *
     * TODO: MDL-73648 Use from Behat too
     *
     * @param array|stdClass $record
     */
    public function create_badge($record): badge {
        global $DB, $USER;

        $record = (object) array_merge([
            'name' => 'Test badge',
            'description' => 'Testing badges',
            'timecreated' => time(),
            'timemodified' => time(),
            'usercreated' => $USER->id,
            'usermodified' => $USER->id,
            'issuername' => 'Test issuer',
            'issuerurl' => 'http://issuer-url.domain.co.nz',
            'issuercontact' => 'issuer@example.com',
            'expiredate' => null,
            'expireperiod' => null,
            'type' => BADGE_TYPE_SITE,
            'courseid' => null,
            'messagesubject' => 'Test message subject',
            'message' => 'Test message body',
            'attachment' => 1,
            'notification' => 0,
            'status' => BADGE_STATUS_ACTIVE,
            'version' => OPEN_BADGES_V2,
            'language' => 'en',
            'imageauthorname' => 'Image author',
            'imageauthoremail' => 'author@example.com',
            'imageauthorurl' => 'http://image.example.com/',
            'imagecaption' => 'Image caption'
        ], (array) $record);

        $record->id = $DB->insert_record('badge', $record);

        return new badge($record->id);
    }
}
