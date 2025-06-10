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
 * Plugin strings are defined here.
 *
 * @package     tool_migratehvp2h5p
 * @category    string
 * @copyright   2020 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['attempted'] = 'Attempted users';
$string['cannot_migrate'] = 'Cannot migrate the activity';
$string['contenttype'] = 'Content-type';
$string['copy2cb'] = 'Should these contents be added to the content bank?';
$string['copy2cb_yeswithlink'] = 'Yes, and a link to these files should be used in the activity';
$string['copy2cb_yeswithoutlink'] = 'Yes, but a copy will be used in the activity (changes in the content bank won\'t be reflected in the activity)';
$string['copy2cb_no'] = 'No, they should be only created in the activity.';
$string['error_contenttypeh5p_disabled'] = "H5P contentbank type is disabled. It must be enabled to migrate activities from mod_hvp
and add them to the content bank too. You can enable this contentytype from 'Site administration | Plugins | Content bank | Manage
content types' or run again the migration tool and select 'No, they should be only created in the activity.' (or 'copy2cb=0' if
you're running CLI) to avoid creating files in content bank.";
$string['error_modh5pactivity_disabled'] = 'H5P activity is disabled. It must be enabled to migrate activities from mod_hvp';
$string['event_hvp_migrated'] = 'mod_hvp migrated to mod_h5pactivity';
$string['graded'] = 'Graded users';
$string['hvpactivities'] = 'Pending mod_hvp activities';
$string['id'] = 'Id';
$string['migrate'] = 'Migrate';
$string['migrate_success'] = 'Hvp activity with id {$a} migrated successfully';
$string['migrate_fail'] = 'Error migrating hvp activity with id {$a}';
$string['migrate_gradesoverridden'] = 'Original mod_hvp activity "{$a->name}", with id {$a->id}, migrated successfully. However,
    it has some grading information overridden, such as feedback, which hasn\'t been migrated because the original activity is
    configured with an invalid maximum grade (it has to be higher than 0 in order to be migrated to the gradebook).';
$string['migrate_gradesoverridden_notdelete'] = 'Original mod_hvp activity "{$a->name}", with id {$a->id}, migrated successfully.
    However, it has some grading information overridden, such as feedback, which hasn\'t been migrated because the original activity
    is configured with an invalid maximum grade (it has to be higher than 0 in order to be migrated to the gradebook).
    The original activity has been hidden instead of removing it.';
$string['nohvpactivities'] = 'There are no mod_hvp activities to migrate to the mod_h5pactivity.';
$string['pluginname'] = 'Migrate content from mod_hvp to mod_h5pactivity';
$string['keeporiginal'] = 'Select what to do with the original activity once migrated';
$string['keeporiginal_hide'] = 'Hide the original activity';
$string['keeporiginal_delete'] = 'Delete the original activity';
$string['keeporiginal_nothing'] = 'Leave the original activity as it is';
$string['privacy:metadata'] = 'Migrate content from mod_hvp to mod_h5pactivity does not store any personal data';
$string['savedstate'] = 'Saved state';
$string['selecthvpactivity'] = 'Select {$a} mod_hvp activity';
$string['settings'] = 'Migration settings';
