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
 * Resource module upgrade code
 *
 * This file keeps track of upgrades to
 * the resource module
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package    mod
 * @subpackage resource
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function xmldb_resource_upgrade($oldversion) {
    global $CFG, $DB;
    require_once("$CFG->dirroot/mod/resource/db/upgradelib.php");

    $dbman = $DB->get_manager();

//===== 1.9.0 upgrade line ======//

    if ($oldversion < 2009041900) {
        resource_20_prepare_migration();
        // resource savepoint reached
        upgrade_mod_savepoint(true, 2009041900, 'resource');
    }

    if ($oldversion < 2009042000) {
       // Rename field summary on table resource to intro
        $table = new xmldb_table('resource');
        $field = new xmldb_field('summary', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'reference');

        // Launch rename field summary
        $dbman->rename_field($table, $field, 'intro');

        // resource savepoint reached
        upgrade_mod_savepoint(true, 2009042000, 'resource');
    }

    if ($oldversion < 2009042001) {
        // Define field introformat to be added to resource
        $table = new xmldb_table('resource');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

        // Launch add field introformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // conditionally migrate to html format in intro
        if ($CFG->texteditors !== 'textarea') {
            $rs = $DB->get_recordset('resource', array('introformat'=>FORMAT_MOODLE), '', 'id,intro,introformat');
            foreach ($rs as $r) {
                $r->intro       = text_to_html($r->intro, false, false, true);
                $r->introformat = FORMAT_HTML;
                $DB->update_record('resource', $r);
                upgrade_set_timeout();
            }
            $rs->close();
        }

        // resource savepoint reached
        upgrade_mod_savepoint(true, 2009042001, 'resource');
    }

    if ($oldversion < 2009062600) {
        $res_count = $DB->count_records('resource');
        $old_count = $DB->count_records('resource_old', array('migrated'=>0));
        if ($res_count != $old_count) {
            //we can not continue, something is very wrong!!
            upgrade_log(UPGRADE_LOG_ERROR, null, 'Resource migration failed.');
            upgrade_mod_savepoint(false, 2009062600, 'resource');
        }

        // Drop obsoleted fields from resource table
        $table = new xmldb_table('resource');
        $fields = array('type', 'reference', 'alltext', 'popup', 'options');
        foreach ($fields as $fname) {
            $field = new xmldb_field($fname);
            $dbman->drop_field($table, $field);
        }

        // resource savepoint reached
        upgrade_mod_savepoint(true, 2009062600, 'resource');
    }

    if ($oldversion < 2009062601) {
        $table = new xmldb_table('resource');
        // Define field tobemigrated to be added to resource
        $field = new xmldb_field('tobemigrated', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'introformat');
        // Conditionally launch add field tobemigrated
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field mainfile to be added to resource
        $field = new xmldb_field('mainfile', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'tobemigrated');
        // Conditionally launch add field mainfile
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field legacyfiles to be added to resource
        $field = new xmldb_field('legacyfiles', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'mainfile');
        // Conditionally launch add field legacyfiles
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field legacyfileslast to be added to resource
        $field = new xmldb_field('legacyfileslast', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'legacyfiles');
        // Conditionally launch add field legacyfileslast
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field display to be added to resource
        $field = new xmldb_field('display', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'legacyfileslast');
        // Conditionally launch add field display
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field displayoptions to be added to resource
        $field = new xmldb_field('displayoptions', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'display');
        // Conditionally launch add field displayoptions
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field filterfiles to be added to resource
        $field = new xmldb_field('filterfiles', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'displayoptions');
        // Conditionally launch add field filterfiles
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field revision to be added to resource
        $field = new xmldb_field('revision', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'filterfiles');
        // Conditionally launch add field revision
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        //mark all records as awaiting migration
        $DB->set_field('resource', 'tobemigrated', 1, array());

        // resource savepoint reached
        upgrade_mod_savepoint(true, 2009062601, 'resource');
    }

    if ($oldversion < 2009062603) {
        resource_20_migrate();
        upgrade_mod_savepoint(true, 2009062603, 'resource');
    }

    if ($oldversion < 2009063000) {
        //migrate and prune old settings - admins need to review and set up all module settings anyway
        if (!empty($CFG->resource_framesize)) {
            set_config('framesize', $CFG->resource_framesize, 'resource');
        }
        if (!empty($CFG->resource_popupheight)) {
            set_config('popupheight', $CFG->resource_popupheight, 'resource');
        }
        if (!empty($CFG->resource_popupwidth)) {
            set_config('popupwidth', $CFG->resource_popupwidth, 'resource');
        }

        $cleanupsettings = array(
            // migrated settings
            'resource_framesize', 'resource_popupheight', 'resource_popupwidth', 'resource_popupmenubar',

            // obsoleted settings
            'resource_websearch', 'resource_defaulturl', 'resource_allowlocalfiles',
            'resource_popup', 'resource_popupresizable', 'resource_popupscrollbars',
            'resource_popupdirectories', 'resource_popuplocation',
            'resource_popuptoolbar', 'resource_popupstatus',

            //waiting for some other modules or plugins to pick these up
            /*
            'resource_secretphrase',
            */
        );
        foreach ($cleanupsettings as $setting) {
            unset_config($setting);
        }

        upgrade_mod_savepoint(true, 2009063000, 'resource');
    }

    if ($oldversion < 2009080501) {
        require_once("$CFG->libdir/filelib.php");

        $sql = "SELECT r.id,
                       r.mainfile,
                       cm.id AS cmid
                  FROM {resource} r
                  JOIN {modules} m ON m.name='resource'
                  JOIN {course_modules} cm ON (cm.module = m.id AND cm.instance = r.id)";

        $instances = $DB->get_recordset_sql($sql);
        foreach ($instances as $instance) {
            if (empty($instance->mainfile)) {
                // weird
                continue;
            }
            $context   = get_context_instance(CONTEXT_MODULE, $instance->cmid, MUST_EXIST);
            $parts     = explode('/', $instance->mainfile);
            $filename  = array_pop($parts);
            $filepath  = implode('/', $parts);
            file_set_sortorder($context->id, 'mod_resource', 'content', 0, $filepath, $filename, 1);
        }
        $instances->close();

     /// Define field mainfile to be dropped from resource
        $table = new xmldb_table('resource');
        $field = new xmldb_field('mainfile');
        $dbman->drop_field($table, $field);

    /// resource savepoint reached
        upgrade_mod_savepoint(true, 2009080501, 'resource');
    }

    // MDL-10906. Removing resource_allowlocalfiles setting.
    if ($oldversion < 2010083000) {
        unset_config('resource_allowlocalfiles');
        upgrade_mod_savepoint(true, 2010083000, 'resource');
    }

    if ($oldversion < 2011022700) {
        // refresh resource links breakage caused by invalid sortorder
        require_once($CFG->dirroot . '/course/lib.php');
        rebuild_course_cache(0, true);
        upgrade_mod_savepoint(true, 2011022700, 'resource');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}
