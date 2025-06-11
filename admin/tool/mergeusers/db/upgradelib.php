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
 * Provides upgrading steps.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Vrigili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

use tool_mergeusers\local\profile_fields;
/**
 * Create or update user profile fields for user metadata once users have been merged.
 *
 * @return void
 */
function tool_mergeusers_define_user_profile_fields(): void {
    global $CFG, $DB;

    require_once $CFG->dirroot . '/user/profile/lib.php';
    require_once $CFG->dirroot . '/user/profile/definelib.php';

    // Create user profile field category.
    $category = $DB->get_record('user_info_category', ['name' => profile_fields::MERGE_CATEGORY_FOR_FIELDS]);

    if (empty($category)) {
        $category = (object)[
            'name' => profile_fields::MERGE_CATEGORY_FOR_FIELDS,
        ];
        profile_save_category($category);
    }


    // Skip if category is not found.
    if (!isset($category->id)) {
        return;
    }

    // Definition of all 4 fields.
    $fields = [
        profile_fields::MERGE_DATE => [
            'name' => 'Merge date',
            'shortname' => profile_fields::MERGE_DATE,
            'datatype' => 'datetime',
            'description' => 'When was this merge completed?',
            'descriptionformat' => FORMAT_HTML,
            'categoryid' => $category->id,
            'required' => false,
            'locked' => true,
            'visible' => false,
            'forceunique' => false,
            'signup' => false,
            'defaultdata' => 0,
            'defaultdataformat' => '0',
            'param1' => '2025',
            'param2' => '2125',
        ],
        profile_fields::MERGE_LOG_ID => [
            'name' => 'Merge log',
            'shortname' => profile_fields::MERGE_LOG_ID,
            'datatype' => 'text',
            'description' => 'Log id for this merge.',
            'descriptionformat' => FORMAT_HTML,
            'categoryid' => $category->id,
            'required' => false,
            'locked' => true,
            'visible' => false,
            'forceunique' => false,
            'signup' => false,
            'defaultdata' => '',
            'defaultdataformat' => '0',
            'param1' => '30',
            'param2' => '2024',
            'param3' => '0',
            'param4' => $CFG->wwwroot . '/admin/tool/mergeusers/log.php?id=$$',
            'param5' => '_blank',
        ],
        profile_fields::MERGE_OLD_USER_ID => [
            'name' => 'Merged old user',
            'shortname' => profile_fields::MERGE_OLD_USER_ID,
            'datatype' => 'text',
            'description' => 'Old user id.',
            'descriptionformat' => FORMAT_HTML,
            'categoryid' => $category->id,
            'required' => false,
            'locked' => true,
            'visible' => false,
            'forceunique' => false,
            'signup' => false,
            'defaultdata' => '',
            'defaultdataformat' => '0',
            'param1' => '30',
            'param2' => '2024',
            'param3' => '0',
            'param4' => $CFG->wwwroot . '/user/profile.php?id=$$',
            'param5' => '_blank',
        ],
        profile_fields::MERGE_NEW_USER_ID => [
            'name' => 'Merged new user',
            'shortname' => profile_fields::MERGE_NEW_USER_ID,
            'datatype' => 'text',
            'description' => 'New user id.',
            'descriptionformat' => FORMAT_HTML,
            'categoryid' => $category->id,
            'required' => false,
            'locked' => true,
            'visible' => false,
            'forceunique' => false,
            'signup' => false,
            'defaultdata' => '',
            'defaultdataformat' => '0',
            'param1' => '30',
            'param2' => '2024',
            'param3' => '0',
            'param4' => $CFG->wwwroot . '/user/profile.php?id=$$',
            'param5' => '_blank',
        ],
    ];

    // Create custom fields if they do not exist.
    foreach ($fields as $field_info) {
        $record = (object)$field_info;

        // Check if it already exists to update it.
        $fieldid = $DB->get_field('user_info_field', 'id', ['shortname' => $record->shortname]);
        if (!empty($fieldid)) {
            $record->id = $fieldid;
        }

        $define_field = new profile_define_base();
        $define_field->define_save($record);
    }
}
