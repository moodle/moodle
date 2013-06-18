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
 * Bulk course registration functions
 *
 * @package    tool_uploadcourse
 * @subpackage uploadcourse
 * @copyright  2004 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('CC_COURSE_ADDNEW', 0);
define('CC_COURSE_ADDINC', 1);
define('CC_COURSE_ADD_UPDATE', 2);
define('CC_COURSE_UPDATE', 3);

define('CC_UPDATE_NOCHANGES', 0);
define('CC_UPDATE_FILEOVERRIDE', 1);
define('CC_UPDATE_ALLOVERRIDE', 2);
define('CC_UPDATE_MISSING', 3);

define('CC_BULK_NEW', 1);
define('CC_BULK_UPDATED', 2);
define('CC_BULK_ALL', 3);

define('CC_PWRESET_NONE', 0);
define('CC_PWRESET_WEAK', 1);
define('CC_PWRESET_ALL', 2);
define('CC_BULK_NONE', 0);


/**
 * Return the list of stad fields the course upload processes
 */
function tool_uploadcourse_std_fields() {
    // Array of all valid fields for validation.
    return $std_fields = array('fullname', 'shortname', 'category', 'idnumber', 'summary',
                    'format', 'showgrades', 'newsitems', 'teacher', 'editingteacher', 'student', 'modinfo',
                    'manager', 'coursecreator', 'guest', 'user', 'startdate', 'numsections',
                    'maxbytes', 'visible', 'groupmode', 'restrictmodules',
                    'enablecompletion', 'completionstartonenrol', 'completionnotify',
                    'hiddensections', 'groupmodeforce', 'lang', 'theme',
                    'cost', 'showreports', 'notifystudents', 'expirynotify', 'expirythreshold', 'requested',
                    'deleted',     // 1 means delete course.
                    'oldshortname', // For renaming.
                    'backupfile', // For restoring a course template after creation.
                    'templatename', // Course to use as a template - the shortname.
                    'reset',
                    // There are also the enrolment fields but these are free form as they vary on enrolment type
                    // eg: enrolmethod_1,status_1,enrolmethod_2,name_2,password_2,customtext1_2
                    //     manual,       1,       self,         self1, letmein,   this is a custom message 1.
    );
}

/**
 * process the upload
 *
 * @param object $formdata - object of the form data
 * @param object $cir - object of the CSV importer
 * @param array $filecolumns - file column definitions
 * @param string $restorefile - file to restore from
 * @param boolean $plain - plain text output
 */
function tool_uploadcourse_process_course_upload($formdata, $cir, $filecolumns, $restorefile=null, $plain=false) {
    global $CFG, $USER, $OUTPUT, $SESSION, $DB;

    $std_fields = tool_uploadcourse_std_fields();

    @set_time_limit(60*60); // 1 hour should be enough.
    raise_memory_limit(MEMORY_HUGE);

    require_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM));
    require_capability('moodle/course:update', get_context_instance(CONTEXT_SYSTEM));
    require_capability('moodle/course:delete', get_context_instance(CONTEXT_SYSTEM));

    $strcourserenamed             = get_string('courserenamed', 'tool_uploadcourse');
    $strcoursenotrenamedexists    = get_string('coursenotrenamedexists', 'tool_uploadcourse');
    $strcoursenotrenamedmissing   = get_string('coursenotrenamedmissing', 'tool_uploadcourse');
    $strcoursenotrenamedoff       = get_string('coursenotrenamedoff', 'tool_uploadcourse');

    $strcourseupdated             = get_string('courseupdated', 'tool_uploadcourse');
    $strcoursenotupdated          = get_string('coursenotupdatederror', 'tool_uploadcourse');
    $strcoursenotupdatednotexists = get_string('coursenotupdatednotexists', 'tool_uploadcourse');

    $strcourseuptodate            = get_string('courseuptodate', 'tool_uploadcourse');

    $strcourseadded               = get_string('newcourse');
    $strcoursenotadded            = get_string('coursenotadded', 'tool_uploadcourse');
    $strcoursenotaddederror       = get_string('coursenotaddederror', 'tool_uploadcourse');

    $strcoursedeleted             = get_string('coursedeleted', 'tool_uploadcourse');
    $strcoursenotdeletederror     = get_string('coursenotdeletederror', 'tool_uploadcourse');
    $strcoursenotdeletedmissing   = get_string('coursenotdeletedmissing', 'tool_uploadcourse');
    $strcoursenotdeletedoff       = get_string('coursenotdeletedoff', 'tool_uploadcourse');
    $errorstr                     = get_string('error');

    $returnurl = new moodle_url('/admin/tool/uploadcourse/index.php');
    $bulknurl  = new moodle_url('/admin/tool/uploadcourse/index.php');

    $today = time();
    $today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

    $optype = $formdata->cctype;

    $updatetype        = isset($formdata->ccupdatetype) ? $formdata->ccupdatetype : 0;
    $allowrenames      = (!empty($formdata->ccallowrenames) and $optype != CC_COURSE_ADDNEW and $optype != CC_COURSE_ADDINC);
    $allowdeletes      = (!empty($formdata->ccallowdeletes) and $optype != CC_COURSE_ADDNEW and $optype != CC_COURSE_ADDINC);
    $bulk              = isset($formdata->ccbulk) ? $formdata->ccbulk : 0;
    $standardshortnames = $formdata->ccstandardshortnames;

    // Check for the template.
    $templatepathname = null;
    if (!empty($formdata->templatename) && $formdata->templatename != 'none') {
        $template = $DB->get_record('course', array('shortname' => $formdata->templatename));

        // Backup the course template.
        $bc = new backup_controller(backup::TYPE_1COURSE, $template->id, backup::FORMAT_MOODLE,
                        backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id);
        $backupid       = $bc->get_backupid();
        $backupbasepath = $bc->get_plan()->get_basepath();
        $bc->execute_plan();
        $bc->destroy();
        $packer = get_file_packer('application/zip');
        // Check if tmp dir exists.
        $tmpdir = $CFG->tempdir . '/backup';
        if (!check_dir_exists($tmpdir, true, true)) {
            throw new restore_controller_exception('cannot_create_backup_temp_dir');
        }
        $filename = restore_controller::get_tempdir_name(SITEID, $USER->id);
        $templatepathname = $tmpdir . '/' . $filename;
        // Get the list of files in directory.
        $filestemp = get_directory_list($backupbasepath, '', false, true, true);
        $files = array();
        foreach ($filestemp as $file) {
            // Add zip paths and fs paths to all them.
            $files[$file] = $backupbasepath . '/' . $file;
        }
        $zippacker = get_file_packer('application/zip');
        $zippacker->archive_to_pathname($files, $templatepathname);
        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete($backupbasepath);
        }
    }

    // Check the uploaded backup file.
    if (!empty($formdata->restorefile)) {
        // Check if tmp dir exists.
        if ($restorefile) {
            $filepath = restore_controller::get_tempdir_name(SITEID, $USER->id);
            $packer = get_file_packer('application/zip');
            $restorepathname = "$CFG->tempdir/backup/$filepath/";
            $result = $packer->extract_to_pathname($restorefile, $restorepathname);
            // If not a backup zip file.
            if (!$result) {
                if (empty($CFG->keeptempdirectoriesonbackup)) {
                    fulldelete($restorepathname);
                    fulldelete($restorefile);
                }
                throw new moodle_exception('invalidbackupfile', 'tool_uploadcourse');
            }
            if (empty($CFG->keeptempdirectoriesonbackup)) {
                fulldelete($restorepathname);
            }
        } else {
            $restorefile = null;
        }
    }

    // Verification moved to two places: after upload and into form2.
    $coursesnew      = 0;
    $coursesupdated  = 0;
    $coursesuptodate = 0; // Not printed yet anywhere.
    $courseserrors   = 0;
    $deletes       = 0;
    $deleteerrors  = 0;
    $renames       = 0;
    $renameerrors  = 0;
    $coursesskipped  = 0;
    $enrolmentplugins = enrol_get_plugins(false);
    $courseformats = array_keys(get_plugin_list('format'));

    // Clear bulk selection.
    if ($bulk) {
        $SESSION->bulk_courses = array();
    }

    // Init csv import helper.
    $cir->init();
    $linenum = 1; // Column header is first line.

    // Init upload progress tracker.
    $upt = new tool_uploadcourse_progress_tracker($plain);
    $upt->start(); // Start table.

    while ($line = $cir->next()) {
        $upt->flush();
        $linenum++;

        $upt->track('line', $linenum);

        $course = new stdClass();

        // Add fields to course object.
        foreach ($line as $keynum => $value) {
            if (!isset($filecolumns[$keynum])) {
                // This should not happen.
                continue;
            }
            $key = $filecolumns[$keynum];
            $course->$key = $value;

            if (in_array($key, $upt->columns)) {
                // Default value in progress tracking table, can be changed later.
                $upt->track($key, s($value), 'normal');
            }
        }
        // Validate category.
        $error = false;
        if (!empty($course->category)) {
            $split = preg_split('|(?<!\\\)/|', $course->category);
            $categories = array();
            foreach ($split as $cat) {
                $cat = preg_replace('/\\\/', '', $cat);
                $categories[]= $cat;
            }
            $course->category = 0;
            foreach ($categories as $cat) {
                // Does the category exist - does the category hierachy make sense.
                $category = $DB->get_record('course_categories', array('name'=>trim($cat), 'parent' => $course->category));
                if (empty($category)) {
                    $upt->track('status', get_string('invalidvalue', 'tool_uploadcourse', 'category').' ('.$cat.' '.get_string('missing', 'tool_uploadcourse').')', 'error');
                    $upt->track('category', $errorstr, 'error');
                    $error = true;
                    break;
                }
                $course->category = $category->id;
            }
        }
        // Check for category errors.
        if ($error) {
            $courseserrors++;
            continue;
        }

        if (!isset($course->shortname)) {
            // Prevent warnings bellow.
            $course->shortname = '';
        }
        if (!empty($course->startdate) && $course->startdate != 0) {
            $course->startdate = strtotime($course->startdate);
        }
        if (!empty($course->enrolstartdate) && $course->enrolstartdate != 0) {
            $course->enrolstartdate = strtotime($course->enrolstartdate);
        }

        // Check for enrolment methods.
        $line_fields = (array) $course;
        $enrolmethods = array();
        $enrolments = array();
        $error = false;
        foreach ($line_fields as $k => $v) {
            if (preg_match('/^(\w+)\_(\d+)$/', $k, $matches)) {
                if (!isset($enrolments[$matches[2]])) {
                    $enrolments[$matches[2]] = array();
                }
                if ($matches[1] == 'enrolmethod') {
                    if (!isset($enrolmentplugins[$v])) {
                        $upt->track('status', get_string('invalidenrolmethod', 'tool_uploadcourse', 'category'), 'error');
                        $upt->track($k, $errorstr, 'error');
                        $error = true;
                    }
                    $enrolmethods[$v] = $matches[2];
                }
                $enrolments[$matches[2]][$matches[1]] = $v;
            }
        }
        if ($error) {
            continue;
        }
        foreach ($enrolmethods as $k => $v) {
            $enrolmethods[$k] = $enrolments[$v];
        }

        // Roles.
        $roles = get_all_roles();
        foreach ($roles as $role) {
            if (isset($course->{$role->shortname})) {
                if (in_array($role->shortname, array('teacher', 'editingteacher', 'student',
                                                     'manager', 'coursecreator', 'guest', 'user'))) {
                    $course->{'role_'.$role->id} = $course->{$role->shortname};
                }
            }
        }

        // What type of operation is this ?
        if ($optype == CC_COURSE_ADDNEW or $optype == CC_COURSE_ADDINC) {
            // Course creation is a special case - the shortname may be constructed from templates using firstname and lastname
            // better never try this in mixed update types.
            $error = false;
            if (!isset($course->fullname) or $course->fullname === '') {
                $upt->track('status', get_string('missingfield', 'error', 'fullname'), 'error');
                $upt->track('fullname', $errorstr, 'error');
                $error = true;
            }
            if (!isset($course->summary) or $course->summary === '') {
                $upt->track('status', get_string('missingfield', 'error', 'summary'), 'error');
                $upt->track('summary', $errorstr, 'error');
                $error = true;
            }
            if ($error) {
                $courseserrors++;
                continue;
            }
            // We require shortname too - we might use template for it though.
            if (empty($course->shortname) and !empty($formdata->ccshortname)) {
                $course->shortname = tool_uploadcourse_process_template($formdata->ccshortname, $course);
                $upt->track('shortname', s($course->shortname));
            }
        }

        // Normalize shortname.
        $originalshortname = $course->shortname;
        if ($standardshortnames) {
            $course->shortname = clean_param($course->shortname, PARAM_MULTILANG);
        }

        // Make sure we really have shortname.
        if (empty($course->shortname)) {
            $upt->track('status', get_string('missingfield', 'error', 'shortname'), 'error');
            $upt->track('shortname', $errorstr, 'error');
            $courseserrors++;
            continue;
        }

        if ($existingcourse = $DB->get_record('course', array('shortname' => $course->shortname))) {
            $upt->track('id', $existingcourse->id, 'normal', false);
        }

        // Find out in shortname incrementing required.
        if ($existingcourse and $optype == CC_COURSE_ADDINC) {
            $course->shortname = tool_uploadcourse_increment_shortname($course->shortname);
            if (!empty($course->idnumber)) {
                $oldidnumber = $course->idnumber;
                $course->idnumber = tool_uploadcourse_increment_idnumber($course->idnumber);
                if ($course->idnumber !== $oldidnumber) {
                    $upt->track('idnumber', s($oldidnumber).'-->'.s($course->idnumber), 'info');
                }
            }
            $existingcourse = false;
        }

        // Check duplicate idnumber.
        if (!$existingcourse and !empty($course->idnumber)) {
            if ($DB->record_exists('course', array('idnumber' => $course->idnumber))) {
                $upt->track('status', get_string('idnumbernotunique', 'tool_uploadcourse'), 'error');
                $upt->track('idnumber', $errorstr, 'error');
                $error = true;
            }
        }

        // Notify about nay shortname changes.
        if ($originalshortname !== $course->shortname) {
            $upt->track('shortname', '', 'normal', false); // Clear previous.
            $upt->track('shortname', s($originalshortname).'-->'.s($course->shortname), 'info');
        } else {
            $upt->track('shortname', s($course->shortname), 'normal', false);
        }

        // Add default values for remaining fields.
        $formdefaults = array();
        foreach ($std_fields as $field) {
            if (isset($course->$field)) {
                continue;
            }
            // All validation moved to form2.
            if (isset($formdata->$field)) {
                $course->$field = $formdata->$field;
                $formdefaults[$field] = true;
                if (in_array($field, $upt->columns)) {
                    $upt->track($field, s($course->$field), 'normal');
                }
            } else {
                // Process templates.
                if (isset($formdata->{"cc".$field}) && !empty($formdata->{"cc".$field}) && empty($course->$field)) {
                    $course->$field = tool_uploadcourse_process_template($formdata->{"cc".$field}, $course);
                }
            }
        }
        // Do we run the reset ?
        $resetcourse = false;
        if ($course->reset) {
            $resetcourse = true;
            unset($course->reset);
        }

        // Proof visible flag.
        $course->visible = (int) $course->visible;

        if (empty($course->category)) {
            $course->category = $formdata->cccategory;
        }

        // Delete course.
        if (!empty($course->deleted)) {
            if (!$allowdeletes) {
                $coursesskipped++;
                $upt->track('status', $strcoursenotdeletedoff, 'warning');
                continue;
            }
            if ($existingcourse) {
                if (delete_course($existingcourse->id, false)) {
                    $upt->track('status', $strcoursedeleted);
                    $deletes++;
                } else {
                    $upt->track('status', $strcoursenotdeletederror, 'error');
                    $deleteerrors++;
                }
            } else {
                $upt->track('status', $strcoursenotdeletedmissing, 'error');
                $deleteerrors++;
            }
            continue;
        }
        // We do not need the deleted flag anymore.
        unset($course->deleted);

        // Renaming requested?
        if (!empty($course->oldshortname) ) {
            if (!$allowrenames) {
                $coursesskipped++;
                $upt->track('status', $strcoursenotrenamedoff, 'warning');
                continue;
            }

            if ($existingcourse) {
                $upt->track('status', $strcoursenotrenamedexists, 'error');
                $renameerrors++;
                continue;
            }

            if ($standardshortnames) {
                $oldshortname = clean_param($course->oldshortname, PARAM_MULTILANG);
            } else {
                $oldshortname = $course->oldshortname;
            }

            // No guessing when looking for old shortname, it must be exact match.
            if ($oldcourse = $DB->get_record('course', array('shortname'=>$oldshortname))) {
                $upt->track('id', $oldcourse->id, 'normal', false);
                $DB->set_field('course', 'shortname', $course->shortname, array('id'=>$oldcourse->id));
                $upt->track('shortname', '', 'normal', false); // Clear previous.
                $upt->track('shortname', s($oldshortname).'-->'.s($course->shortname), 'info');
                $upt->track('status', $strcourserenamed);
                $renames++;
            } else {
                $upt->track('status', $strcoursenotrenamedmissing, 'error');
                $renameerrors++;
                continue;
            }
            $existingcourse = $oldcourse;
            $existingcourse->shortname = $course->shortname;
        }

        // Can we process with update or insert?
        $skip = false;
        switch ($optype) {
            case CC_COURSE_ADDNEW:
                if ($existingcourse) {
                    $coursesskipped++;
                    $upt->track('status', $strcoursenotadded, 'warning');
                    $skip = true;
                }
                break;

            case CC_COURSE_ADDINC:
                if ($existingcourse) {
                    // This should not happen!
                    $upt->track('status', $strcoursenotaddederror, 'error');
                    $courseserrors++;
                    $skip = true;
                }
                break;

            case CC_COURSE_ADD_UPDATE:
                break;

            case CC_COURSE_UPDATE:
                if (!$existingcourse) {
                    $coursesskipped++;
                    $upt->track('status', $strcoursenotupdatednotexists, 'warning');
                    $skip = true;
                }
                break;

            default:
                // Unknown type.
                $skip = true;
        }

        // Check for the backup file as template.
        $backupfile = null;
        if (!empty($course->backupfile)) {
            if (!is_readable($course->backupfile) || !preg_match('/(\.mbz|\.zip)$/i', $course->backupfile)) {
                $upt->track('status', get_string('incorrecttemplatefile', 'tool_uploadcourse'), 'error');
                $courseserrors++;
                $skip = true;
            } else {
                $backupfile = $course->backupfile;
            }
        }

        if ($skip) {
            continue;
        }

        // check the format
        if (!empty($course->format) && !in_array($course->format, $courseformats)) {
            $upt->track('status', get_string('incorrectformat', 'tool_uploadcourse'), 'error');
            $courseserrors++;
            continue;
        }

        $templatename = null;
        if ($existingcourse) {
            $course->id = $existingcourse->id;

            $upt->track('shortname', html_writer::link(new moodle_url('/course/view.php',
                                                                      array('id '=> $existingcourse->id)),
                                                                      s($existingcourse->shortname)),
                                                                      'normal', false);

            $existingcourse->timemodified = time();
            // Do NOT mess with timecreated or firstaccess here!
            $doupdate = false;

            if ($updatetype != CC_UPDATE_NOCHANGES) {
                foreach ($std_fields as $column) {
                    if ($column === 'shortname') {
                        // These can not be changed here.
                        continue;
                    }
                    if (!property_exists($course, $column) or !property_exists($existingcourse, $column)) {
                        // This should never happen.
                        continue;
                    }
                    // In the case $updatetype == CC_UPDATE_ALLOVERRIDE we override everything.
                    if ($updatetype == CC_UPDATE_MISSING) {
                        if (!is_null($existingcourse->$column) and $existingcourse->$column !== '') {
                            continue;
                        }

                    } else if ($updatetype == CC_UPDATE_FILEOVERRIDE) {
                        if (!empty($formdefaults[$column])) {
                            // Do not override with form defaults.
                            continue;
                        }
                    }
                    if ($existingcourse->$column !== $course->$column) {
                        if (in_array($column, $upt->columns)) {
                            $upt->track($column, s($existingcourse->$column).'-->'.s($course->$column), 'info', false);
                        }
                        $existingcourse->$column = $course->$column;
                        $doupdate = true;
                    }
                }
            }

            if ($doupdate) {
                // We want only courses that were really updated.
                update_course($existingcourse);
                $upt->track('status', $strcourseupdated);
                $coursesupdated++;

                events_trigger('course_updated', $existingcourse);

                if ($bulk == CC_BULK_UPDATED or $bulk == CC_BULK_ALL) {
                    if (!in_array($course->id, $SESSION->bulk_courses)) {
                        $SESSION->bulk_courses[] = $course->id;
                    }
                }

            } else {
                // No course information changed.
                $upt->track('status', $strcourseuptodate);
                $coursesuptodate++;

                if ($bulk == CC_BULK_ALL) {
                    if (!in_array($course->id, $SESSION->bulk_courses)) {
                        $SESSION->bulk_courses[] = $course->id;
                    }
                }
            }

        } else {
            // Save the new course to the database.
            $course->timemodified = time();
            $course->timecreated  = time();

            // Create course - insert_record ignores any extra properties.
            if (isset($course->templatename) && $course->templatename != 'none') {
                $templatename = $course->templatename;
            } else {
                $templatename = null;
            }
            try {
                $course = create_course($course);
            } catch (moodle_exception $e) {
                $upt->track('status', $e->getMessage(), 'error');
                $courseserrors++;
                $skip = true;
                continue;
            }
            $upt->track('shortname', html_writer::link(new moodle_url('/course/view.php',
                                                                      array('id' => $course->id)),
                                                                      s($course->shortname)),
                                                                      'normal', false);

            $upt->track('status', $strcourseadded);
            $upt->track('id', $course->id, 'normal', false);
            $coursesnew++;

            // Make sure course context exists.
            get_context_instance(CONTEXT_COURSE, $course->id);

            events_trigger('course_created', $course);

            if ($bulk == CC_BULK_NEW or $bulk == CC_BULK_ALL) {
                if (!in_array($course->id, $SESSION->bulk_courses)) {
                    $SESSION->bulk_courses[] = $course->id;
                }
            }
        }

        // After creation/update, do we need to copy from template nominated in the CSV file?
        if (!empty($templatename)) {
            $coursetemplate = $DB->get_record('course', array('shortname' => $templatename));
            if (empty($coursetemplate)) {
                $upt->track('status', get_string('incorrecttemplatefile', 'tool_uploadcourse'), 'error');
                $courseserrors++;
                continue;
            }

            // Backup the course template.
            $bc = new backup_controller(backup::TYPE_1COURSE, $coursetemplate->id, backup::FORMAT_MOODLE,
                            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id);
            $backupid       = $bc->get_backupid();
            $backupbasepath = $bc->get_plan()->get_basepath();
            $bc->execute_plan();
            $bc->destroy();
            $packer = get_file_packer('application/zip');
            // Check if tmp dir exists.
            $tmpdir = $CFG->tempdir . '/backup';
            if (!check_dir_exists($tmpdir, true, true)) {
                throw new restore_controller_exception('cannot_create_backup_temp_dir');
            }
            $filename = restore_controller::get_tempdir_name(SITEID, $USER->id);
            $temppathname = $tmpdir . '/' . $filename;
            // Get the list of files in directory.
            $filestemp = get_directory_list($backupbasepath, '', false, true, true);
            $files = array();
            foreach ($filestemp as $file) {
                // Add zip paths and fs paths to all them.
                $files[$file] = $backupbasepath . '/' . $file;
            }
            $zippacker = get_file_packer('application/zip');
            $zippacker->archive_to_pathname($files, $temppathname);
            if (empty($CFG->keeptempdirectoriesonbackup)) {
                fulldelete($backupbasepath);
            }

            // Check if tmp dir exists.
            $tmpdir = $CFG->tempdir . '/backup';
            $filename = restore_controller::get_tempdir_name($course->id, $USER->id);
            $pathname = $tmpdir . '/' . $filename;
            $packer = get_file_packer('application/zip');
            $packer->extract_to_pathname($temppathname, $pathname);

            // Restore the backup immediately.
            $rc = new restore_controller($filename, $course->id,
                            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id, backup::TARGET_CURRENT_ADDING);
            // Check if the format conversion must happen first.
            if ($rc->get_status() == backup::STATUS_REQUIRE_CONV) {
                $rc->convert();
            }
            if (!$rc->execute_precheck()) {
                $precheckresults = $rc->get_precheck_results();
                if (is_array($precheckresults) && !empty($precheckresults['errors'])) {
                    if (empty($CFG->keeptempdirectoriesonbackup)) {
                        fulldelete($pathname);
                    }
                    echo $output->precheck_notices($precheckresults);
                    if (!$plain) {
                        echo $output->continue_button(new moodle_url('/course/view.php', array('id' => $course->id)));
                        echo $output->footer();
                    }
                    die();
                }
            }
            $rc->execute_plan();
            $rc->destroy();
            if (empty($CFG->keeptempdirectoriesonbackup)) {
                fulldelete($pathname);
            }
        }

        // After creation/update, do we need to copy from template?
        if (!empty($templatepathname)) {
            // Check if tmp dir exists.
            $tmpdir = $CFG->tempdir . '/backup';
            $filename = restore_controller::get_tempdir_name($course->id, $USER->id);
            $pathname = $tmpdir . '/' . $filename;
            $packer = get_file_packer('application/zip');
            $packer->extract_to_pathname($templatepathname, $pathname);

            // Restore the backup immediately.
            $rc = new restore_controller($filename, $course->id,
                            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id, backup::TARGET_CURRENT_ADDING);
            // Check if the format conversion must happen first.
            if ($rc->get_status() == backup::STATUS_REQUIRE_CONV) {
                $rc->convert();
            }
            if (!$rc->execute_precheck()) {
                $precheckresults = $rc->get_precheck_results();
                if (is_array($precheckresults) && !empty($precheckresults['errors'])) {
                    if (empty($CFG->keeptempdirectoriesonbackup)) {
                        fulldelete($pathname);
                    }
                    echo $output->precheck_notices($precheckresults);
                    if (!$plain) {
                        echo $output->continue_button(new moodle_url('/course/view.php', array('id' => $course->id)));
                        echo $output->footer();
                    }
                    die();
                }
            }
            $rc->execute_plan();
            $rc->destroy();
            if (empty($CFG->keeptempdirectoriesonbackup)) {
                fulldelete($pathname);
            }
        }

        // After creation/update, do we need to copy from template backup file?
        if (!empty($restorefile)) {
            // Check if tmp dir exists.
            $tmpdir = $CFG->tempdir . '/backup';
            $filename = restore_controller::get_tempdir_name($course->id, $USER->id);
            $pathname = $tmpdir . '/' . $filename;
            $packer = get_file_packer('application/zip');
            $packer->extract_to_pathname($restorefile, $pathname);

            // Restore the backup immediately.
            $rc = new restore_controller($filename, $course->id,
                            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id, backup::TARGET_CURRENT_ADDING);
            // Check if the format conversion must happen first.
            if ($rc->get_status() == backup::STATUS_REQUIRE_CONV) {
                $rc->convert();
            }
            if (!$rc->execute_precheck()) {
                $precheckresults = $rc->get_precheck_results();
                if (is_array($precheckresults) && !empty($precheckresults['errors'])) {
                    if (empty($CFG->keeptempdirectoriesonbackup)) {
                        fulldelete($pathname);
                    }
                    echo $output->precheck_notices($precheckresults);
                    if (!$plain) {
                        echo $output->continue_button(new moodle_url('/course/view.php', array('id' => $course->id)));
                        echo $output->footer();
                    }
                    die();
                }
            }
            $rc->execute_plan();
            $rc->destroy();
            if (empty($CFG->keeptempdirectoriesonbackup)) {
                fulldelete($pathname);
            }
        }

        // After creation/update, do we need to import a Moodle backup?
        if (!empty($backupfile)) {
            // Check if tmp dir exists.
            $tmpdir = $CFG->tempdir . '/backup';
            if (!check_dir_exists($tmpdir, true, true)) {
                throw new restore_controller_exception('cannot_create_backup_temp_dir');
            }
            $filename = restore_controller::get_tempdir_name($course->id, $USER->id);
            $pathname = $tmpdir . '/' . $filename;
            $packer = get_file_packer('application/zip');
            $packer->extract_to_pathname($backupfile, $pathname);

            // Restore the backup immediately.
            $rc = new restore_controller($filename, $course->id,
                            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id, backup::TARGET_CURRENT_ADDING);
            // Check if the format conversion must happen first.
            if ($rc->get_status() == backup::STATUS_REQUIRE_CONV) {
                $rc->convert();
            }
            if (!$rc->execute_precheck()) {
                $precheckresults = $rc->get_precheck_results();
                if (is_array($precheckresults) && !empty($precheckresults['errors'])) {
                    if (empty($CFG->keeptempdirectoriesonbackup)) {
                        fulldelete($pathname);
                    }
                    echo $output->precheck_notices($precheckresults);
                    if (!$plain) {
                        echo $output->continue_button(new moodle_url('/course/view.php', array('id' => $course->id)));
                        echo $output->footer();
                    }
                    die();
                }
            }
            $rc->execute_plan();
            $rc->destroy();
            if (empty($CFG->keeptempdirectoriesonbackup)) {
                fulldelete($pathname);
            }
        }

        // Handle enrolment methods.
        $enrol_updated = false;
        $instances = enrol_get_instances($course->id, false);
        foreach ($enrolments as $method) {
            if (isset($method['delete']) && $method['delete']) {
                // Remove the enrolment method.
                foreach ($instances as $instance) {
                    if ($instance->enrol == $method['enrolmethod']) {
                        $plugin = $enrolmentplugins[$instance->enrol];
                        $plugin->delete_instance($instance);
                        $enrol_updated = true;
                        break;
                    }
                }
            } else if (isset($method['disable']) && $method['disable']) {
                // Disable the enrolment.
                foreach ($instances as $instance) {
                    if ($instance->enrol == $method['enrolmethod']) {
                        $plugin = $enrolmentplugins[$instance->enrol];
                        $plugin->update_status($instance, ENROL_INSTANCE_DISABLED);
                        $enrol_updated = true;
                        break;
                    }
                }
            } else {
                // We should have this enrolment method.
                $instance = null;
                foreach ($instances as $i) {
                    if ($i->enrol == $method['enrolmethod']) {
                        $instance = $i;
                        break;
                    }
                }
                $plugin = null;
                if (empty($instance)) {
                    $plugin = $enrolmentplugins[$method['enrolmethod']];
                    $instance = new stdClass();
                    $instance->id = $plugin->add_default_instance($course);
                    $instance->roleid = $plugin->get_config('roleid');
                } else {
                    $plugin = $enrolmentplugins[$instance->enrol];
                    $plugin->update_status($instance, ENROL_INSTANCE_ENABLED);
                }
                // Now update values.
                foreach ($method as $k => $v) {
                    $instance->{$k} = $v;
                }

                // Sort out the start, end and date.
                $instance->enrolstartdate = (isset($method['startdate']) ? strtotime($method['startdate']) : 0);
                $instance->enrolenddate = (isset($method['enddate']) ? strtotime($method['enddate']) : 0);

                // Is the enrolment period set?
                if (isset($method['enrolperiod']) && ! empty($method['enrolperiod'])) {
                    if (preg_match('/^\d+$/', $method['enrolperiod'])) {
                        $method['enrolperiod'] = (int) $method['enrolperiod'];
                    } else {
                        // Try and convert period to seconds.
                        $method['enrolperiod'] = strtotime('1970-01-01 GMT + ' . $method['enrolperiod']);
                    }
                    $instance->enrolperiod = $method['enrolperiod'];
                }
                if ($instance->enrolstartdate > 0 && isset($method['enrolperiod'])) {
                    $instance->enrolenddate = $instance->enrolstartdate + $method['enrolperiod'];
                }
                if ($instance->enrolenddate > 0) {
                    $instance->enrolperiod = $instance->enrolenddate - $instance->enrolstartdate;
                }
                if ($instance->enrolenddate < $instance->enrolstartdate) {
                    $instance->enrolenddate = $instance->enrolstartdate;
                }
                // Sort out the given Role.
                if (isset($method['role'])) {
                    $context = context_course::instance($course->id);
                    $roles = get_default_enrol_roles($context, $plugin->get_config('roleid'));
                    if (!empty($roles)) {
                        $roles = array_flip($roles);
                    }
                    if (isset($roles[$method['role']])) {
                        $instance->roleid = $roles[$method['role']];
                    }
                }
                $instance->status = ENROL_INSTANCE_ENABLED;
                $instance->timemodified = time();
                $DB->update_record('enrol', $instance);
                $enrol_updated = true;
            }
        }

        // Do the course reset.
        if ($resetcourse) {
            $resetdata = new stdClass();
            $resetdata->reset_start_date = time();
            $resetdata->id = $course->id;
            $resetdata->reset_events = true;
            $resetdata->reset_logs = true;
            $resetdata->reset_notes = true;
            $resetdata->reset_comments = true;
            $resetdata->reset_completion = true;
            $resetdata->delete_blog_associations = true;
            $roles = get_assignable_roles(context_course::instance($course->id));
            $roles[0] = get_string('noroles', 'role');
            $roles = array_reverse($roles, true);
            $resetdata->reset_roles_local = true;
            $resetdata->reset_gradebook_items = true;
            $resetdata->reset_gradebook_grades = true;
            $resetdata->reset_gradebook_items = true;
            $resetdata->reset_groups_remove = true;
            $resetdata->reset_groups_members = true;
            $resetdata->reset_groupings_remove = true;
            $resetdata->reset_groupings_members = true;
            $resetdata->reset_groups_remove = true;
            $resetdata->reset_groups_remove = true;
            $resetdata->reset_start_date_old = $course->startdate;
            $status = reset_course_userdata($resetdata);
        }

        if ($enrol_updated) {
            $coursesupdated++;
        }
        // Invalidate all enrol caches.
        $context = context_course::instance($course->id);
        $context->mark_dirty();
    }

    // Clean up backup files.
    if (!empty($template)) {
        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete($backupbasepath);
        }
    }
    if (!empty($restorefile)) {
        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete($restorefile);
        }
    }

    $upt->close(); // Close table.

    $cir->close();
    $cir->cleanup(true);
    $systemcontext = context_system::instance();
    mark_context_dirty($systemcontext->path);

    if (!$plain) {
        echo $OUTPUT->box_start('boxwidthnarrow boxaligncenter generalbox', 'uploadresults');
        echo '<p>';
        if ($optype != CC_COURSE_UPDATE) {
            echo get_string('coursescreated', 'tool_uploadcourse').': '.$coursesnew.'<br />';
        }
        if ($optype == CC_COURSE_UPDATE or $optype == CC_COURSE_ADD_UPDATE) {
            echo get_string('coursesupdated', 'tool_uploadcourse').': '.$coursesupdated.'<br />';
        }
        if ($allowdeletes) {
            echo get_string('coursesdeleted', 'tool_uploadcourse').': '.$deletes.'<br />';
            echo get_string('deleteerrors', 'tool_uploadcourse').': '.$deleteerrors.'<br />';
        }
        if ($allowrenames) {
            echo get_string('coursesrenamed', 'tool_uploadcourse').': '.$renames.'<br />';
            echo get_string('renameerrors', 'tool_uploadcourse').': '.$renameerrors.'<br />';
        }
        if ($coursesskipped) {
            echo get_string('coursesskipped', 'tool_uploadcourse').': '.$coursesskipped.'<br />';
        }
        echo get_string('errors', 'tool_uploadcourse').': '.$courseserrors.'</p>';
    } else {
        if ($optype != CC_COURSE_UPDATE) {
            echo get_string('coursescreated', 'tool_uploadcourse').': '.$coursesnew."\n";
        }
        if ($optype == CC_COURSE_UPDATE or $optype == CC_COURSE_ADD_UPDATE) {
            echo get_string('coursesupdated', 'tool_uploadcourse').': '.$coursesupdated."\n";
        }
        if ($allowdeletes) {
            echo get_string('coursesdeleted', 'tool_uploadcourse').': '.$deletes."\n";
            echo get_string('deleteerrors', 'tool_uploadcourse').': '.$deleteerrors."\n";
        }
        if ($allowrenames) {
            echo get_string('coursesrenamed', 'tool_uploadcourse').': '.$renames."\n";
            echo get_string('renameerrors', 'tool_uploadcourse').': '.$renameerrors."\n";
        }
        if ($coursesskipped) {
            echo get_string('coursesskipped', 'tool_uploadcourse').': '.$coursesskipped."\n";
        }
        echo get_string('errors', 'tool_uploadcourse').': '.$courseserrors."\n";
        echo "The End.\n";
    }
}


/**
 * Tracking of processed courses.
 *
 * This class prints course information into a html table.
 *
 * @copyright  2007 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_uploadcourse_progress_tracker {
    /** @var int $_row - row marker */
    private $_row;
    /** @var array $columns - output columns */
    public $columns = array('status', 'line', 'id', 'fullname', 'shortname', 'category', 'idnumber', 'summary', 'deleted');
    /** @var boolean $_plain - output is text mode */
    private $_plain;

    /**
     * Constructor
     *
     * @param boolean $type - is this plain text output
     */
    public function __construct($type = false) {
        $this->_plain = $type;
    }


    /**
     * Print table header.
     * @return void
     */
    public function start() {
        $ci = 0;
        if ($this->_plain) {
            echo "\n\t".get_string('status')."\t".get_string('cccsvline', 'tool_uploadcourse')."\tID\t".
                 get_string('fullname')."\t".get_string('shortname')."\t".get_string('category')."\t".
                 get_string('idnumber')."\t".get_string('summary')."\t".get_string('delete')."\n";
        } else {
            echo '<table id="ccresults" class="generaltable boxaligncenter flexible-wrap" summary="'.
                  get_string('uploadcoursesresult', 'tool_uploadcourse').'">';
            echo '<tr class="heading r0">';
            echo '<th class="header c'.$ci++.'" scope="col">'.get_string('status').'</th>';
            echo '<th class="header c'.$ci++.'" scope="col">'.get_string('cccsvline', 'tool_uploadcourse').'</th>';
            echo '<th class="header c'.$ci++.'" scope="col">ID</th>';
            echo '<th class="header c'.$ci++.'" scope="col">'.get_string('fullname').'</th>';
            echo '<th class="header c'.$ci++.'" scope="col">'.get_string('shortname').'</th>';
            echo '<th class="header c'.$ci++.'" scope="col">'.get_string('category').'</th>';
            echo '<th class="header c'.$ci++.'" scope="col">'.get_string('idnumber').'</th>';
            echo '<th class="header c'.$ci++.'" scope="col">'.get_string('summary').'</th>';
            echo '<th class="header c'.$ci++.'" scope="col">'.get_string('delete').'</th>';
            echo '</tr>';
        }
        $this->_row = null;
    }

    /**
     * Flush previous line and start a new one.
     * @return void
     */
    public function flush() {
        if (empty($this->_row) or empty($this->_row['line']['normal'])) {
            // Nothing to print - each line has to have at least number.
            $this->_row = array();
            foreach ($this->columns as $col) {
                $this->_row[$col] = array('normal'=>'', 'info'=>'', 'warning'=>'', 'error'=>'');
            }
            return;
        }
        $ci = 0;
        $ri = 1;
        echo $this->_plain ? "" : '<tr class="r'.$ri.'">';
        foreach ($this->_row as $key => $field) {
            foreach ($field as $type => $content) {
                if ($field[$type] !== '') {
                    $field[$type] = $this->_plain ? $field[$type] : '<span class="cc'.$type.'">'.$field[$type].'</span>';
                } else {
                    unset($field[$type]);
                }
            }
            echo $this->_plain ? "\t" : '<td class="cell c'.$ci++.'">';
            if (!empty($field)) {
                echo implode(($this->_plain ? "|" : '<br />'), $field);
            } else {
                echo $this->_plain ? '' : '&nbsp;';
            }
            echo $this->_plain ? '' : '</td>';
        }
        echo $this->_plain ? "\n" : '</tr>';
        foreach ($this->columns as $col) {
            $this->_row[$col] = array('normal'=>'', 'info'=>'', 'warning'=>'', 'error'=>'');
        }
    }

    /**
     * Add tracking info
     * @param string $col name of column
     * @param string $msg message
     * @param string $level 'normal', 'warning' or 'error'
     * @param bool $merge true means add as new line, false means override all previous text of the same type
     * @return void
     */
    public function track($col, $msg, $level = 'normal', $merge = true) {
        if (empty($this->_row)) {
            $this->flush(); // Init arrays.
        }
        if (!in_array($col, $this->columns)) {
            debugging('Incorrect column:'.$col);
            return;
        }
        if ($merge) {
            if ($this->_row[$col][$level] != '') {
                $this->_row[$col][$level] .= $this->_plain ? '' : '<br />';
            }
            $this->_row[$col][$level] .= $msg;
        } else {
            $this->_row[$col][$level] = $msg;
        }
    }

    /**
     * Print the table end
     * @return void
     */
    public function close() {
        $this->flush();
        echo $this->_plain ? "\n" : '</table>';
    }
}

/**
 * Validation callback function - verified the column line of csv file.
 * Converts standard column names to lowercase.
 * @param csv_import_reader $cir
 * @param array $stdfields standard course fields
 * @param moodle_url $returnurl return url in case of any error
 * @return array list of fields
 */
function tool_uploadcourse_validate_course_upload_columns(csv_import_reader $cir, $stdfields, moodle_url $returnurl) {
    $columns = $cir->get_columns();

    if (empty($columns)) {
        $cir->close();
        $cir->cleanup();
        print_error('cannotreadtmpfile', 'error', $returnurl);
    }
    if (count($columns) < 2) {
        $cir->close();
        $cir->cleanup();
        print_error('csvfewcolumns', 'error', $returnurl);
    }

    // Test columns.
    $processed = array();
    foreach ($columns as $key => $unused) {
        $field = $columns[$key];
        $lcfield = textlib::strtolower($field);
        if (in_array($field, $stdfields) or in_array($lcfield, $stdfields)) {
            // Standard fields are only lowercase.
            $newfield = $lcfield;

        } else if (preg_match('/^\w+\_\d+$/', $lcfield)) {
            // Special fields for enrolments.
            $newfield = $lcfield;

        } else {
            $cir->close();
            $cir->cleanup();
            print_error('invalidfieldname', 'error', $returnurl, $field);
        }
        if (in_array($newfield, $processed)) {
            $cir->close();
            $cir->cleanup();
            print_error('duplicatefieldname', 'error', $returnurl, $newfield);
        }
        $processed[$key] = $newfield;
    }

    return $processed;
}

/**
 * Increments shortname - increments trailing number or adds it if not present.
 * Varifies that the new shortname does not exist yet
 * @param string $shortname
 * @return incremented shortname which does not exist yet
 */
function tool_uploadcourse_increment_shortname($shortname) {
    global $DB, $CFG;

    if (!preg_match_all('/(.*?)([0-9]+)$/', $shortname, $matches)) {
        $shortname = $shortname.'2';
    } else {
        $shortname = $matches[1][0].($matches[2][0]+1);
    }

    if ($DB->record_exists('course', array('shortname'=>$shortname))) {
        return tool_uploadcourse_increment_shortname($shortname);
    } else {
        return $shortname;
    }
}

/**
 * Increments idnumber - increments trailing number or adds it if not present.
 * Varifies that the new idnumber does not exist yet
 * @param string $idnumber
 * @return incremented idnumber which does not exist yet
 */
function tool_uploadcourse_increment_idnumber($idnumber) {
    global $DB, $CFG;

    if (!preg_match_all('/(.*?)([0-9]+)$/', $idnumber, $matches)) {
        $idnumber = $idnumber.'2';
    } else {
        $idnumber = $matches[1][0].($matches[2][0]+1);
    }

    if ($DB->record_exists('course', array('idnumber'=>$idnumber))) {
        return tool_uploadcourse_increment_idnumber($idnumber);
    } else {
        return $idnumber;
    }
}

/**
 * Check if default field contains templates and apply them.
 * @param string $template - potential tempalte string
 * @param object $course - we need coursename, firstname and lastname
 * @return object $template - course template
 * @return string $result - field value
 */
function tool_uploadcourse_process_template($template, $course) {
    if (is_array($template)) {
        // Hack for for support of text editors with format.
        $t = $template['text'];
    } else {
        $t = $template;
    }
    if (strpos($t, '%') === false) {
        return $template;
    }

    $shortname  = isset($course->shortname) ? $course->shortname  : '';
    $fullname   = isset($course->fullname) ? $course->fullname : '';
    $idnumber   = isset($course->idnumber) ? $course->idnumber  : '';

    $callback = partial('tool_uploadcourse_process_template_callback', $shortname, $fullname, $idnumber);

    $result = preg_replace_callback('/(?<!%)%([+-~])?(\d)*([flu])/', $callback, $t);

    if (is_null($result)) {
        return $template; // Error during regex processing??
    }

    if (is_array($template)) {
        $template['text'] = $result;
        return $t;
    } else {
        return $result;
    }
}

/**
 * Internal callback function.
 * @param string $shortname - course shortname
 * @param string $fullname - course full name
 * @param string $idnumber - course idnumber
 * @param array $block - template parameters
 * @return string $repl - resolved template
 */
function tool_uploadcourse_process_template_callback($shortname, $fullname, $idnumber, $block) {

    switch ($block[3]) {
        case 's':
            $repl = $shortname;
            break;
        case 'f':
            $repl = $fullname;
            break;
        case 'i':
            $repl = $idnumber;
            break;
        default:
            return $block[0];
    }

    switch ($block[1]) {
        case '+':
            $repl = textlib::strtoupper($repl);
            break;
        case '-':
            $repl = textlib::strtolower($repl);
            break;
        case '~':
            $repl = textlib::strtotitle($repl);
            break;
    }

    if (!empty($block[2])) {
        $repl = textlib::substr($repl, 0 , $block[2]);
    }

    return $repl;
}

