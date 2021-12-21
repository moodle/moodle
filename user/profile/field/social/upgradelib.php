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
 * Contains upgrade and install functions for the social profile fields.
 *
 * @package    profilefield_social
 * @copyright  2020 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/user/profile/definelib.php");

/**
 * Create the default category for custom profile fields if it does not exist yet.
 */
function user_profile_social_create_info_category() {
    global $DB;

    $categories = $DB->get_records('user_info_category', null, 'sortorder ASC');
    // Check that we have at least one category defined.
    if (empty($categories)) {
        $defaultcategory = (object) [
            'name' => get_string('profiledefaultcategory', 'admin'),
            'sortorder' => 1
        ];
        $DB->insert_record('user_info_category', $defaultcategory);
    }
}

/**
 * Called on upgrade to create new profile fields based on the old user table columns
 * for icq, msn, aim, skype and url.
 *
 * @param string $social Social profile field.
 */
function user_profile_social_moveto_profilefield($social) {
    global $DB;

    $users = $DB->get_records_select('user', "$social IS NOT NULL AND $social != ''");
    if (count($users)) {
        $profilefield = user_profile_social_create_profilefield($social);
        foreach ($users as $user) {
            $userinfodata = [
                'userid' => $user->id,
                'fieldid' => $profilefield->id,
                'data' => $user->$social,
                'dataformat' => 0
            ];
            $user->$social = '';
            $DB->update_record('user', $user);
            $DB->insert_record('user_info_data', $userinfodata);
        }
    }
}

/**
 * Create an new custom social profile field if it does not exist
 *
 * @param string $social Social profile field.
 * @return object DB record or social profield field.
 */
function user_profile_social_create_profilefield($social) {
    global $DB, $CFG;

    $hiddenfields = explode(',', $CFG->hiddenuserfields);
    $confignames = [
        'url' => 'webpage',
        'icq' => 'icqnumber',
        'skype' => 'skypeid',
        'yahoo' => 'yahooid',
        'aim' => 'aimid',
        'msn' => 'msnid',
    ];
    $visible = (in_array($confignames[$social], $hiddenfields)) ? 3 : 2;

    $newfield = (object)[
        'shortname' => $social,
        'name' => $social,
        'datatype' => 'social',
        'description' => '',
        'descriptionformat' => 1,
        'categoryid' => 1,
        'required' => 0,
        'locked' => 0,
        'visible' => $visible,
        'forceunique' => 0,
        'signup' => 0,
        'defaultdata' => '',
        'defaultdataformat' => 0,
        'param1' => $social
    ];

    user_profile_social_create_info_category();

    $profilefield = $DB->get_record_sql(
        'SELECT * FROM {user_info_field} WHERE datatype = :datatype AND ' .
        $DB->sql_compare_text('param1') . ' = ' . $DB->sql_compare_text(':social', 40),
        ['datatype' => 'social', 'social' => $social]);

    if (!$profilefield) {

        // Find a new unique shortname.
        $count = 0;
        $shortname = $newfield->shortname;
        while ($field = $DB->get_record('user_info_field', array('shortname' => $shortname))) {
            $count++;
            $shortname = $newfield->shortname . '_' . $count;
        }
        $newfield->shortname = $shortname;

        $profileclass = new profile_define_base();
        $profileclass->define_save($newfield);
        profile_reorder_fields();

        $profilefield = $DB->get_record_sql(
            'SELECT * FROM {user_info_field} WHERE datatype = :datatype AND ' .
            $DB->sql_compare_text('param1') . ' = ' . $DB->sql_compare_text(':social', 40),
            ['datatype' => 'social', 'social' => $social]);
    }
    if (!$profilefield) {
        throw new moodle_exception('upgradeerror', 'admin', 'could not create new social profile field');
    }
    return $profilefield;
}

/**
 * Update the module availability configuration for all course modules
 *
 */
function user_profile_social_update_module_availability() {
    global $DB;
    // Use transaction to improve performance if there are many individual database updates.
    $transaction = $DB->start_delegated_transaction();
    // Query all the course_modules entries that have availability set.
    $rs = $DB->get_recordset_select('course_modules', 'availability IS NOT NULL', [], '', 'id, availability');
    foreach ($rs as $mod) {
        if (isset($mod->availability)) {
            $availability = json_decode($mod->availability);
            if (!is_null($availability)) {
                user_profile_social_update_availability_structure($availability);
                $newavailability = json_encode($availability);
                if ($newavailability !== $mod->availability) {
                    $mod->availability = json_encode($availability);
                    $DB->update_record('course_modules', $mod);
                }
            }
        }
    }
    $rs->close();
    $transaction->allow_commit();
}

/**
 * Loop through the availability info and change all move standard profile
 * fields for icq, skype, aim, yahoo, msn and url to be custom profile fields.
 * @param mixed $structure The availability object.
 */
function user_profile_social_update_availability_structure(&$structure) {
    if (is_array($structure)) {
        foreach ($structure as $st) {
            user_profile_social_update_availability_structure($st);
        }
    }
    foreach ($structure as $key => $value) {
        if ($key === 'c' && is_array($value)) {
            user_profile_social_update_availability_structure($value);
        }
        if ($key === 'type' && $value === 'profile') {
            if (isset($structure->sf) && in_array($structure->sf,
                ['icq', 'skype', 'aim', 'yahoo', 'msn', 'url'])) {
                $structure->cf = $structure->sf;
                unset($structure->sf);
            }
        }
    }
}
