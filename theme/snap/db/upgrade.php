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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/theme/snap/lib.php');

/**
 * Theme upgrade
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_theme_snap_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2014080400) {
        if (get_config('core', 'theme') == 'snap') {
            set_config('deadlinestoggle', 0, 'theme_snap');
            set_config('messagestoggle', 0, 'theme_snap');
        }
        upgrade_plugin_savepoint(true, 2014080400, 'theme', 'snap');
    }

    if ($oldversion < 2014090900) {
        if (get_config('core', 'theme') == 'snap') {
            set_config('coursefootertoggle', 0, 'theme_snap');
        }
        upgrade_plugin_savepoint(true, 2014090900, 'theme', 'snap');
    }

    if ($oldversion < 2014110404) {
        theme_snap_process_site_coverimage();
        upgrade_plugin_savepoint(true, 2014110404, 'theme', 'snap');
    }

    if ($oldversion < 2016042900) {
        // Set default value for showing personal menu on login.
        if (get_config('core', 'theme') == 'snap') {
            set_config('personalmenulogintoggle', 0, 'theme_snap');
        }

        // Snap savepoint reached.
        upgrade_plugin_savepoint(true, 2016042900, 'theme', 'snap');
    }

    if ($oldversion < 2016042904) {
        // Define table theme_snap_course_favorites to be created.
        $table = new xmldb_table('theme_snap_course_favorites');

        // Adding fields to table theme_snap_course_favorites.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timefavorited', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table theme_snap_course_favorites.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table theme_snap_course_favorites.
        $table->add_index('userid-courseid', XMLDB_INDEX_UNIQUE, array('userid', 'courseid'));

        // Conditionally launch create table for theme_snap_course_favorites.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Snap savepoint reached.
        upgrade_plugin_savepoint(true, 2016042904, 'theme', 'snap');
    }

    if ($oldversion < 2016121309) {
        if (get_config('core', 'theme') === 'snap') {
            set_config('showcoursegradepersonalmenu', 0, 'theme_snap');
        }
        upgrade_plugin_savepoint(true, 2016121309, 'theme', 'snap');
    }

    if ($oldversion < 2017122801) {
        if (!is_null(get_config('theme_snap', 'hidenavblock'))) {
            unset_config('hidenavblock', 'theme_snap');
        }
        upgrade_plugin_savepoint(true, 2017122801, 'theme', 'snap');
    }

    if ($oldversion < 2019051501) {
        $favourites = $DB->get_records('theme_snap_course_favorites');
        foreach ($favourites as $key => $favourite) {
            $userid = $favourite->userid;
            $usercontext = \context_user::instance($userid, IGNORE_MISSING);
            $courseid = $favourite->courseid;
            $coursecontext = \context_course::instance($courseid, IGNORE_MISSING);
            if ($usercontext !== false && $coursecontext !== false) {
                $conditions = ['component' => 'core_course',
                    'itemtype' => 'courses',
                    'itemid' => $courseid,
                    'userid' => $userid,
                ];
                // Checks if the user already has marked as favourite that course via dashboard.
                if (!$DB->record_exists('favourite', $conditions)) {
                    $ufservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);
                    $ufservice->create_favourite('core_course', 'courses', $courseid, $coursecontext);
                }
            }
        }
        upgrade_plugin_savepoint(true, 2019051501, 'theme', 'snap');
    }

    if ($oldversion < 2022042800) {
        unset_config('design_activity_chooser', 'theme_snap');
        upgrade_plugin_savepoint(true, 2022042800, 'theme', 'snap');
    }

    if ($oldversion < 2024020100) {
        if (!is_null(get_config('theme_snap', 'design_mod_page'))) {
            $previous = get_config('theme_snap', 'design_mod_page');
            set_config('behavior_mod_page', $previous, 'theme_snap');
            unset_config('design_mod_page', 'theme_snap');
        }
        upgrade_plugin_savepoint(true, 2024020100, 'theme', 'snap');
    }

    if ($oldversion < 2024030700) {
        unset_config('behavior_mod_page', 'theme_snap');
        upgrade_plugin_savepoint(true, 2024030700, 'theme', 'snap');
    }

    // Snap Personal Menu is now deprecated and will be completely removed in Moodle 4.5.
    if ($oldversion < 2025011002) {
        set_config('personalmenuenablepersonalmenu', 0, 'theme_snap');
        set_config('personalmenulogintoggle', 0, 'theme_snap');
        set_config('showcoursegradepersonalmenu', 0, 'theme_snap');
        upgrade_plugin_savepoint(true, 2025011002, 'theme', 'snap');
    }

        // BEGIN LSU Extra Course Tabs.
    if ($oldversion < 2025011003 && $oldversion > 2020061109) {

        // Define table theme_snap_remotes to be created.
        $table = new xmldb_table('theme_snap_remotes');

        // Adding fields to table theme_snap_remotes.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('rcjson', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('lastupdated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table theme_snap_remotes.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table theme_snap_remotes.
        $table->add_index('userid', XMLDB_INDEX_UNIQUE, ['userid']);
        $table->add_index('lastupdated', XMLDB_INDEX_NOTUNIQUE, ['lastupdated']);

        // Conditionally launch create table for theme_snap_remotes.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Snap savepoint reached.
        upgrade_plugin_savepoint(true, 2025011003, 'theme', 'snap');
    }
    // END LSU Extra Course Tabs.
    if ($oldversion < 2025042100) {
        // Rename Personal menu settings.
        if (!is_null(get_config('theme_snap', 'personalmenuadvancedfeedsenable'))) {
            $previous = get_config('theme_snap', 'personalmenuadvancedfeedsenable');
            set_config('advancedfeedsenable', $previous, 'theme_snap');
            unset_config('personalmenuadvancedfeedsenable', 'theme_snap');
        }
        if (!is_null(get_config('theme_snap', 'personalmenuadvancedfeedsperpage'))) {
            $previous = get_config('theme_snap', 'personalmenuadvancedfeedsperpage');
            set_config('advancedfeedsperpage', $previous, 'theme_snap');
            unset_config('personalmenuadvancedfeedsperpage', 'theme_snap');
        }
        if (!is_null(get_config('theme_snap', 'personalmenuadvancedfeedslifetime'))) {
            $previous = get_config('theme_snap', 'personalmenuadvancedfeedslifetime');
            set_config('advancedfeedslifetime', $previous, 'theme_snap');
            unset_config('personalmenuadvancedfeedslifetime', 'theme_snap');
        }
        if (!is_null(get_config('theme_snap', 'personalmenurefreshdeadlines'))) {
            $previous = get_config('theme_snap', 'personalmenurefreshdeadlines');
            set_config('refreshdeadlines', $previous, 'theme_snap');
            unset_config('personalmenurefreshdeadlines', 'theme_snap');
        }
        upgrade_plugin_savepoint(true, 2025042100, 'theme', 'snap');
    }

    return true;
}
