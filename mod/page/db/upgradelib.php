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
 * Folder module upgrade related helper functions
 *
 * @package    mod
 * @subpackage page
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Migrate page module data from 1.9 resource_old table to new page table
 * @return void
 */
function page_20_migrate() {
    global $CFG, $DB;
    require_once("$CFG->libdir/filelib.php");
    require_once("$CFG->libdir/resourcelib.php");
    require_once("$CFG->dirroot/course/lib.php");

    if (!file_exists("$CFG->dirroot/mod/resource/db/upgradelib.php")) {
        // bad luck, somebody deleted resource module
        return;
    }

    require_once("$CFG->dirroot/mod/resource/db/upgradelib.php");

    // create resource_old table and copy resource table there if needed
    if (!resource_20_prepare_migration()) {
        // no modules or fresh install
        return;
    }

    $fs = get_file_storage();

    $candidates = $DB->get_recordset('resource_old', array('type'=>'html', 'migrated'=>0));
    foreach ($candidates as $candidate) {
        page_20_migrate_candidate($candidate, $fs, FORMAT_HTML);
    }
    $candidates->close();

    $candidates = $DB->get_recordset('resource_old', array('type'=>'text', 'migrated'=>0));
    foreach ($candidates as $candidate) {
        //there might be some rubbish instead of format int value
        $format = (int)$candidate->reference;
        if ($format < 0 or $format > 4) {
            $format = FORMAT_MOODLE;
        }
        page_20_migrate_candidate($candidate, $fs, $format);
    }
    $candidates->close();

    // clear all course modinfo caches
    rebuild_course_cache(0, true);

}

function page_20_migrate_candidate($candidate, $fs, $format) {
    global $CFG, $DB;
    upgrade_set_timeout();

    if ($CFG->texteditors !== 'textarea') {
        $intro       = text_to_html($candidate->intro, false, false, true);
        $introformat = FORMAT_HTML;
    } else {
        $intro       = $candidate->intro;
        $introformat = FORMAT_MOODLE;
    }

    $page = new stdClass();
    $page->course        = $candidate->course;
    $page->name          = $candidate->name;
    $page->intro         = $intro;
    $page->introformat   = $introformat;
    $page->content       = $candidate->alltext;
    $page->contentformat = $format;
    $page->revision      = 1;
    $page->timemodified  = time();

    // convert links to old course files - let the automigration do the actual job
    $usedfiles = array("$CFG->wwwroot/file.php/$page->course/", "$CFG->wwwroot/file.php?file=/$page->course/");
    $page->content = str_ireplace($usedfiles, '@@PLUGINFILE@@/', $page->content);
    if (strpos($page->content, '@@PLUGINFILE@@/') === false) {
        $page->legacyfiles = RESOURCELIB_LEGACYFILES_NO;
    } else {
        $page->legacyfiles = RESOURCELIB_LEGACYFILES_ACTIVE;
    }

    $options = array('printheading'=>0, 'printintro'=>0);
    if ($candidate->popup) {
        $page->display = RESOURCELIB_DISPLAY_POPUP;
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
        $page->display = RESOURCELIB_DISPLAY_OPEN;
    }
    $page->displayoptions = serialize($options);

    $page = resource_migrate_to_module('page', $candidate, $page);

    // now try to migrate files from site files
    // note: this can not work for html pages or files with other relatively linked files :-(
    $siteid = get_site()->id;
    if (preg_match_all("|$CFG->wwwroot/file.php(\?file=)?/$siteid(/[^\s'\"&\?#]+)|", $page->content, $matches)) {
        $context     = get_context_instance(CONTEXT_MODULE, $candidate->cmid);
        $sitecontext = get_context_instance(CONTEXT_COURSE, $siteid);
        $file_record = array('contextid'=>$context->id, 'component'=>'mod_page', 'filearea'=>'content', 'itemid'=>0);
        $fs = get_file_storage();
        foreach ($matches[2] as $i=>$sitefile) {
            if (!$file = $fs->get_file_by_hash(sha1("/$sitecontext->id/course/legacy/0".$sitefile))) {
                continue;
            }
            try {
                $fs->create_file_from_storedfile($file_record, $file);
                $page->content = str_replace($matches[0][$i], '@@PLUGINFILE@@'.$sitefile, $page->content);
            } catch (Exception $x) {
            }
        }
        $DB->update_record('page', $page);
    }
}
