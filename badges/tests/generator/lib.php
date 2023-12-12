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
     * @param array|stdClass $record
     * @return badge
     */
    public function create_badge($record): badge {
        global $CFG, $DB, $USER;

        $record = (array) $record;

        // Save badge image/tags for later.
        $badgeimage = $record['image'] ?? '';
        $badgetags = $record['tags'] ?? '';
        unset($record['image'], $record['tags']);

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
        ], $record);

        $record->id = $DB->insert_record('badge', $record);
        $badge = new badge($record->id);

        // Process badge image (if supplied).
        if ($badgeimage !== '') {
            $file = get_file_storage()->create_file_from_pathname([
                'contextid' => context_user::instance($USER->id)->id,
                'userid' => $USER->id,
                'component' => 'user',
                'filearea' => 'draft',
                'itemid' => file_get_unused_draft_itemid(),
                'filepath' => '/',
                'filename' => basename($badgeimage),
            ], "{$CFG->dirroot}/$badgeimage");

            // Copy image to temp file, as it'll be deleted by the following call.
            badges_process_badge_image($badge, $file->copy_content_to_temp());
        }

        // Process badge tags (if supplied).
        if ($badgetags !== '') {
            if (!is_array($badgetags)) {
                $badgetags = preg_split('/\s*,\s*/', $badgetags, -1, PREG_SPLIT_NO_EMPTY);
            }
            core_tag_tag::set_item_tags('core_badges', 'badge', $badge->id, $badge->get_context(), $badgetags);
        }

        return $badge;
    }

    /**
     * Create badge criteria
     *
     * Note that only manual criteria issues by role is currently supported
     *
     * @param array|stdClass $record
     * @throws coding_exception
     */
    public function create_criteria($record): void {
        $record = (array) $record;

        if (!array_key_exists('badgeid', $record)) {
            throw new coding_exception('Record must contain \'badgeid\' property');
        }
        if (!array_key_exists('roleid', $record)) {
            throw new coding_exception('Record must contain \'roleid\' property');
        }

        $badge = new badge($record['badgeid']);

        // Create the overall criteria.
        if (count($badge->criteria) === 0) {
            award_criteria::build([
                'badgeid' => $badge->id,
                'criteriatype' => BADGE_CRITERIA_TYPE_OVERALL,
            ])->save([
                'agg' => BADGE_CRITERIA_AGGREGATION_ALL,
            ]);
        }

        // Create the manual criteria.
        award_criteria::build([
            'badgeid' => $badge->id,
            'criteriatype' => BADGE_CRITERIA_TYPE_MANUAL,
        ])->save([
            'role_' . $record['roleid'] => $record['roleid'],
            'description' => $record['description'] ?? '',
        ]);
    }

    /**
     * Create issued badge to a user
     *
     * @param array|stdClass $record
     * @throws coding_exception
     */
    public function create_issued_badge($record): void {
        $record = (array) $record;

        if (!array_key_exists('badgeid', $record)) {
            throw new coding_exception('Record must contain \'badgeid\' property');
        }
        if (!array_key_exists('userid', $record)) {
            throw new coding_exception('Record must contain \'userid\' property');
        }

        $badge = new badge($record['badgeid']);
        $badge->issue($record['userid'], true);
    }
}
