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
 * Resource module upgrade related helper functions
 *
 * @package    mod
 * @subpackage resource
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Migrate resource module data from 1.9 resource_old table to new resource table
 * @return void
 */
function resource_20_migrate() {
    global $CFG, $DB;

    require_once("$CFG->libdir/filelib.php");
    require_once("$CFG->libdir/resourcelib.php");
    require_once("$CFG->dirroot/course/lib.php");

    $fs = get_file_storage();

    $withrelativelinks = array('text/html', 'text/xml', 'application/xhtml+xml', 'application/x-shockwave-flash');
    // note: pdf doc and other types may contain links too, but we do not support relative links there

    $candidates = $DB->get_recordset('resource_old', array('type'=>'file', 'migrated'=>0));
    if (!$candidates->valid()) {
        $candidates->close(); // Not going to iterate (but exit), close rs
        return;
    }

    foreach ($candidates as $candidate) {
        upgrade_set_timeout();

        $path = $candidate->reference;
        $siteid = get_site()->id;
        $fs = get_file_storage();

        if (empty($candidate->cmid)) {
            // skip borked records
            continue;

        } else if (strpos($path, 'LOCALPATH') === 0) {
            // ignore not maintained local files - sorry
            continue;

        } else if (preg_match("|$CFG->wwwroot/file.php(\?file=)?/$siteid(/[^\s'\"&\?#]+)|", $path, $matches)) {
            // public site files
            $path = $matches[2];

            $resource = new stdClass();
            $resource->id           = $candidate->oldid;
            $resource->tobemigrated = 0;
            $resource->mainfile     = $path;
            $resource->filterfiles  = $CFG->filteruploadedfiles;
            $resource->legacyfiles  = RESOURCELIB_LEGACYFILES_NO; // on-demand-migration not possible for site files, sorry

            $context     = get_context_instance(CONTEXT_MODULE, $candidate->cmid);
            $sitecontext = get_context_instance(CONTEXT_COURSE, $siteid);
            $file_record = array('contextid'=>$context->id, 'component'=>'mod_resourse', 'filearea'=>'content', 'itemid'=>0);
            if ($file = $fs->get_file_by_hash(sha1("/$sitecontext->id/course/legacy/content/0".$path))) {
                try {
                    $fs->create_file_from_storedfile($file_record, $file);
                } catch (Exception $x) {
                }
                $resource->mainfile = $file->get_filepath().$file->get_filename();
            }

        } else if (preg_match("|$CFG->wwwroot/file.php(\?file=)?/$candidate->course(/[^\s'\"&\?#]+)|", $path, $matches)) {
            // current course files
            $path = $matches[2];

            $resource = new stdClass();
            $resource->id           = $candidate->oldid;
            $resource->tobemigrated = 0;
            $resource->mainfile     = $path;
            $resource->filterfiles  = $CFG->filteruploadedfiles;
            $mimetype = mimeinfo('type', $resource->mainfile);
            if (in_array($mimetype, $withrelativelinks)) {
                $resource->legacyfiles = RESOURCELIB_LEGACYFILES_ACTIVE;
            } else {
                $resource->legacyfiles = RESOURCELIB_LEGACYFILES_NO;
            }

            // try migration of main file - ignore if does not exist
            if ($file = resourcelib_try_file_migration($resource->mainfile, $candidate->cmid, $candidate->course, 'mod_resource', 'content', 0)) {
                $resource->mainfile = $file->get_filepath().$file->get_filename();
            }

        } else if (strpos($path, '://') or strpos($path, '/') === 0) {
            // http:// https:// ftp:// OR starts with slash - to be converted to link resource
            continue;

        } else {
            // current course files
            // cleanup old path first
            $path = '/'.trim(trim($path), '/');
            if (strpos($path, '?forcedownload=1') !== false) {
                // eliminate old force download tricks
                $candidate->options = 'forcedownload';
                $path = str_replace('?forcedownload=1', '', $path);
            }
            // get rid of any extra url parameters, sorry we can not support these
            preg_match("/^[^?#]+/", $path, $matches);
            $parts = $matches[0];

            $resource = new stdClass();
            $resource->id           = $candidate->oldid;
            $resource->tobemigrated = 0;
            $resource->mainfile     = $path;
            $resource->filterfiles  = $CFG->filteruploadedfiles;
            $mimetype = mimeinfo('type', $resource->mainfile);
            if (in_array($mimetype, $withrelativelinks)) {
                $resource->legacyfiles = RESOURCELIB_LEGACYFILES_ACTIVE;
            } else {
                $resource->legacyfiles = RESOURCELIB_LEGACYFILES_NO;
            }

            // try migration of main file - ignore if does not exist
            if ($file = resourcelib_try_file_migration($resource->mainfile, $candidate->cmid, $candidate->course, 'mod_resource', 'content', 0)) {
                $resource->mainfile = $file->get_filepath().$file->get_filename();
            }
        }

        $options = array('printheading'=>0, 'printintro'=>1);
        if ($candidate->options == 'frame') {
            $resource->display = RESOURCELIB_DISPLAY_FRAME;

        } else if ($candidate->options == 'objectframe') {
            $resource->display = RESOURCELIB_DISPLAY_EMBED;

        } else if ($candidate->options == 'forcedownload') {
            $resource->display = RESOURCELIB_DISPLAY_DOWNLOAD;

        } else if ($candidate->popup) {
            $resource->display = RESOURCELIB_DISPLAY_POPUP;
            if ($candidate->popup) {
                $rawoptions = explode(',', $candidate->popup);
                foreach ($rawoptions as $rawoption) {
                    list($name, $value) = explode('=', trim($rawoption), 2);
                    if ($value > 0 and ($name == 'width' or $name == 'height')) {
                        $options['popup'.$name] = $value;
                        continue;
                    }
                }
            }

        } else {
            $resource->display = RESOURCELIB_DISPLAY_AUTO;
        }
        $resource->displayoptions = serialize($options);

        // update resource instance and mark as migrated
        $DB->update_record('resource', $resource);
        $candidate->newmodule = 'resource';
        $candidate->newid     = $candidate->oldid;
        $candidate->migrated  = time();
        $DB->update_record('resource_old', $candidate);
    }

    $candidates->close();

    // clear all course modinfo caches
    rebuild_course_cache(0, true);
}

/**
 * This function creates resource_old table and copies all data
 * from resource table, this functions has to be called from
 * all modules that are successors of old resource module types.
 * @return bool true if migration required, false if not
 */
function resource_20_prepare_migration() {
    global $DB;

    $dbman = $DB->get_manager();

    // If the resource not created yet, this is probably a new install
    $table = new xmldb_table('resource');
    if (!$dbman->table_exists($table)) {
        return false;
    }

    // Define table resource_old to be created
    $table = new xmldb_table('resource_old');

    if ($dbman->table_exists($table)) {
        //already executed
        return true;
    }

    // fix invalid NULL popup and options data in old mysql databases
    $sql = "UPDATE {resource} SET popup = ? WHERE popup IS NULL";
    $DB->execute($sql, array($DB->sql_empty()));
    $sql = "UPDATE {resource} SET options = ? WHERE options IS NULL";
    $DB->execute($sql, array($DB->sql_empty()));

    // Adding fields to table resource_old
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('type', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null);
    $table->add_field('reference', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('intro', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
    $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('alltext', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);
    $table->add_field('popup', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
    $table->add_field('options', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
    $table->add_field('oldid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
    $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('newmodule', XMLDB_TYPE_CHAR, '50', null, null, null, null);
    $table->add_field('newid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('migrated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    // Adding keys to table resource_old
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    // Adding indexes to table resource_old
    $table->add_index('oldid', XMLDB_INDEX_UNIQUE, array('oldid'));
    $table->add_index('cmid', XMLDB_INDEX_NOTUNIQUE, array('cmid'));

    // Launch create table for resource_old
    $dbman->create_table($table);

    $module = $DB->get_field('modules', 'id', array('name'=>'resource'));

    if (!$DB->count_records('resource')) {
        // upgrade of fresh new server from 1.9 - no upgrade needed
        return false;
    }

    // copy old data, the intro text format was FORMAT_MOODLE==0
    $sql = "INSERT INTO {resource_old} (oldid, course, name, type, reference, intro, introformat, alltext, popup, options, timemodified, cmid)
            SELECT r.id, r.course, r.name, r.type, r.reference, r.summary, 0, r.alltext, r.popup, r.options, r.timemodified, cm.id
              FROM {resource} r
         LEFT JOIN {course_modules} cm ON (r.id = cm.instance AND cm.module = :module)";

    $DB->execute($sql, array('module'=>$module));

    return true;
}

/**
 * Migrate old resource type to new module, updates all related info, keeps the context id.
 * @param string $modname name of new module
 * @param object $candidate old instance from resource_old
 * @param object $newinstance new module instance to be inserted into db
 * @return mixed false if error, object new instance with id if success
 */
function resource_migrate_to_module($modname, $candidate, $newinstance) {
    global $DB;

    if (!$cm = get_coursemodule_from_id('resource', $candidate->cmid)) {
        return false;
    }

    if (!$module = $DB->get_record('modules', array('name'=>$modname))) {
        return false;
    }

    if (!$resource = $DB->get_record('resource', array('id'=>$candidate->oldid))) {
        return false;
    }

    // insert new instance
    $newinstance->id = $DB->insert_record($modname, $newinstance);

    // update course modules
    $cm->module   = $module->id;
    $cm->instance = $newinstance->id;
    $DB->update_record('course_modules', $cm);

    //delete old record
    $DB->delete_records('resource', array('id'=>$resource->id));

    //mark as migrated
    $candidate->newmodule = $modname;
    $candidate->newid     = $newinstance->id;
    $candidate->migrated  = time();
    $DB->update_record('resource_old', $candidate);

    //no need to upgrade data in logs because resource module is able to redirect to migrated instances
    return $newinstance;
}
