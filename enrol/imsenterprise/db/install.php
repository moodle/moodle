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
 * imsenterprise enrolment plugin installation.
 *
 * @package    enrol
 * @subpackage imsenterprise
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_imsenterprise_install() {
    global $CFG, $DB;

    // NOTE: this file is executed during upgrade from 1.9.x!


    // this plugin does not use the new file api - lets undo the migration
    $fs = get_file_storage();

    if ($DB->record_exists('course', array('id'=>1))) { //course 1 is hardcoded here intentionally!
        if ($context = get_context_instance(CONTEXT_COURSE, 1)) {
            if ($file = $fs->get_file($context->id, 'course', 'legacy', 0, '/', 'imsenterprise-enrol.xml')) {
                if (!file_exists("$CFG->dataroot/1/imsenterprise-enrol.xml")) {
                    check_dir_exists($CFG->dataroot.'/');
                    $file->copy_content_to("$CFG->dataroot/1/imsenterprise-enrol.xml");
                }
                $file->delete();
            }
        }
    }

    if (!empty($CFG->enrol_imsfilelocation)) {
        if (strpos($CFG->enrol_imsfilelocation, "$CFG->dataroot/") === 0) {
            $location = str_replace("$CFG->dataroot/", '', $CFG->enrol_imsfilelocation);
            $location = str_replace('\\', '/', $location);
            $parts = explode('/', $location);
            $courseid = array_shift($parts);
            if (is_number($courseid) and $DB->record_exists('course', array('id'=>$courseid))) {
                if ($context = get_context_instance(CONTEXT_COURSE, $courseid)) {
                    $file = array_pop($parts);
                    if ($parts) {
                        $dir = '/'.implode('/', $parts).'/';
                    } else {
                        $dir = '/';
                    }
                    if ($file = $fs->get_file($context->id, 'course', 'legacy', 0, $dir, $file)) {
                        if (!file_exists($CFG->enrol_imsfilelocation)) {
                            check_dir_exists($CFG->dataroot.'/'.$courseid.$dir);
                            $file->copy_content_to($CFG->enrol_imsfilelocation);
                        }
                        $file->delete();
                    }
                }
            }
        }
    }


    // TODO: migrate old config settings

}
