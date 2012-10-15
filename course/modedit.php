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
* Adds or updates modules in a course using new formslib
*
* @package    moodlecore
* @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once("../config.php");
require_once("lib.php");
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/conditionlib.php');
require_once($CFG->libdir.'/plagiarismlib.php');

$add    = optional_param('add', '', PARAM_ALPHA);     // module name
$update = optional_param('update', 0, PARAM_INT);
$return = optional_param('return', 0, PARAM_BOOL);    //return to course/view.php if false or mod/modname/view.php if true
$type   = optional_param('type', '', PARAM_ALPHANUM); //TODO: hopefully will be removed in 2.0
$sectionreturn = optional_param('sr', null, PARAM_INT);

$url = new moodle_url('/course/modedit.php');
$url->param('sr', $sectionreturn);
if (!empty($return)) {
    $url->param('return', $return);
}

if (!empty($add)) {
    $section = required_param('section', PARAM_INT);
    $course  = required_param('course', PARAM_INT);

    $url->param('add', $add);
    $url->param('section', $section);
    $url->param('course', $course);
    $PAGE->set_url($url);

    $course = $DB->get_record('course', array('id'=>$course), '*', MUST_EXIST);
    $module = $DB->get_record('modules', array('name'=>$add), '*', MUST_EXIST);

    require_login($course);
    $context = context_course::instance($course->id);
    require_capability('moodle/course:manageactivities', $context);

    course_create_sections_if_missing($course, $section);
    $cw = get_fast_modinfo($course)->get_section_info($section);

    if (!course_allowed_module($course, $module->name)) {
        print_error('moduledisable');
    }

    $cm = null;

    $data = new stdClass();
    $data->section          = $section;  // The section number itself - relative!!! (section column in course_sections)
    $data->visible          = $cw->visible;
    $data->course           = $course->id;
    $data->module           = $module->id;
    $data->modulename       = $module->name;
    $data->groupmode        = $course->groupmode;
    $data->groupingid       = $course->defaultgroupingid;
    $data->groupmembersonly = 0;
    $data->id               = '';
    $data->instance         = '';
    $data->coursemodule     = '';
    $data->add              = $add;
    $data->return           = 0; //must be false if this is an add, go back to course view on cancel
    $data->sr               = $sectionreturn;

    if (plugin_supports('mod', $data->modulename, FEATURE_MOD_INTRO, true)) {
        $draftid_editor = file_get_submitted_draft_itemid('introeditor');
        file_prepare_draft_area($draftid_editor, null, null, null, null);
        $data->introeditor = array('text'=>'', 'format'=>FORMAT_HTML, 'itemid'=>$draftid_editor); // TODO: add better default
    }

    if (plugin_supports('mod', $data->modulename, FEATURE_ADVANCED_GRADING, false)
            and has_capability('moodle/grade:managegradingforms', $context)) {
        require_once($CFG->dirroot.'/grade/grading/lib.php');

        $data->_advancedgradingdata['methods'] = grading_manager::available_methods();
        $areas = grading_manager::available_areas('mod_'.$module->name);

        foreach ($areas as $areaname => $areatitle) {
            $data->_advancedgradingdata['areas'][$areaname] = array(
                'title'  => $areatitle,
                'method' => '',
            );
            $formfield = 'advancedgradingmethod_'.$areaname;
            $data->{$formfield} = '';
        }
    }

    if (!empty($type)) { //TODO: hopefully will be removed in 2.0
        $data->type = $type;
    }

    $sectionname = get_section_name($course, $cw);
    $fullmodulename = get_string('modulename', $module->name);

    if ($data->section && $course->format != 'site') {
        $heading = new stdClass();
        $heading->what = $fullmodulename;
        $heading->to   = $sectionname;
        $pageheading = get_string('addinganewto', 'moodle', $heading);
    } else {
        $pageheading = get_string('addinganew', 'moodle', $fullmodulename);
    }

} else if (!empty($update)) {

    $url->param('update', $update);
    $PAGE->set_url($url);

    $cm = get_coursemodule_from_id('', $update, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

    require_login($course, false, $cm); // needed to setup proper $COURSE
    $context = context_module::instance($cm->id);
    require_capability('moodle/course:manageactivities', $context);

    $module = $DB->get_record('modules', array('id'=>$cm->module), '*', MUST_EXIST);
    $data = $data = $DB->get_record($module->name, array('id'=>$cm->instance), '*', MUST_EXIST);
    $cw = $DB->get_record('course_sections', array('id'=>$cm->section), '*', MUST_EXIST);

    $data->coursemodule       = $cm->id;
    $data->section            = $cw->section;  // The section number itself - relative!!! (section column in course_sections)
    $data->visible            = $cm->visible; //??  $cw->visible ? $cm->visible : 0; // section hiding overrides
    $data->cmidnumber         = $cm->idnumber;          // The cm IDnumber
    $data->groupmode          = groups_get_activity_groupmode($cm); // locked later if forced
    $data->groupingid         = $cm->groupingid;
    $data->groupmembersonly   = $cm->groupmembersonly;
    $data->course             = $course->id;
    $data->module             = $module->id;
    $data->modulename         = $module->name;
    $data->instance           = $cm->instance;
    $data->return             = $return;
    $data->sr                 = $sectionreturn;
    $data->update             = $update;
    $data->completion         = $cm->completion;
    $data->completionview     = $cm->completionview;
    $data->completionexpected = $cm->completionexpected;
    $data->completionusegrade = is_null($cm->completiongradeitemnumber) ? 0 : 1;
    $data->showdescription    = $cm->showdescription;
    if (!empty($CFG->enableavailability)) {
        $data->availablefrom      = $cm->availablefrom;
        $data->availableuntil     = $cm->availableuntil;
        $data->showavailability   = $cm->showavailability;
    }

    if (plugin_supports('mod', $data->modulename, FEATURE_MOD_INTRO, true)) {
        $draftid_editor = file_get_submitted_draft_itemid('introeditor');
        $currentintro = file_prepare_draft_area($draftid_editor, $context->id, 'mod_'.$data->modulename, 'intro', 0, array('subdirs'=>true), $data->intro);
        $data->introeditor = array('text'=>$currentintro, 'format'=>$data->introformat, 'itemid'=>$draftid_editor);
    }

    if (plugin_supports('mod', $data->modulename, FEATURE_ADVANCED_GRADING, false)
            and has_capability('moodle/grade:managegradingforms', $context)) {
        require_once($CFG->dirroot.'/grade/grading/lib.php');
        $gradingman = get_grading_manager($context, 'mod_'.$data->modulename);
        $data->_advancedgradingdata['methods'] = $gradingman->get_available_methods();
        $areas = $gradingman->get_available_areas();

        foreach ($areas as $areaname => $areatitle) {
            $gradingman->set_area($areaname);
            $method = $gradingman->get_active_method();
            $data->_advancedgradingdata['areas'][$areaname] = array(
                'title'  => $areatitle,
                'method' => $method,
            );
            $formfield = 'advancedgradingmethod_'.$areaname;
            $data->{$formfield} = $method;
        }
    }

    if ($items = grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$data->modulename,
                                             'iteminstance'=>$data->instance, 'courseid'=>$course->id))) {
        // add existing outcomes
        foreach ($items as $item) {
            if (!empty($item->outcomeid)) {
                $data->{'outcome_'.$item->outcomeid} = 1;
            }
        }

        // set category if present
        $gradecat = false;
        foreach ($items as $item) {
            if ($gradecat === false) {
                $gradecat = $item->categoryid;
                continue;
            }
            if ($gradecat != $item->categoryid) {
                //mixed categories
                $gradecat = false;
                break;
            }
        }
        if ($gradecat !== false) {
            // do not set if mixed categories present
            $data->gradecat = $gradecat;
        }
    }

    $sectionname = get_section_name($course, $cw);
    $fullmodulename = get_string('modulename', $module->name);

    if ($data->section && $course->format != 'site') {
        $heading = new stdClass();
        $heading->what = $fullmodulename;
        $heading->in   = $sectionname;
        $pageheading = get_string('updatingain', 'moodle', $heading);
    } else {
        $pageheading = get_string('updatinga', 'moodle', $fullmodulename);
    }

} else {
    require_login();
    print_error('invalidaction');
}

$pagepath = 'mod-' . $module->name . '-';
if (!empty($type)) { //TODO: hopefully will be removed in 2.0
    $pagepath .= $type;
} else {
    $pagepath .= 'mod';
}
$PAGE->set_pagetype($pagepath);
$PAGE->set_pagelayout('admin');

$modmoodleform = "$CFG->dirroot/mod/$module->name/mod_form.php";
if (file_exists($modmoodleform)) {
    require_once($modmoodleform);
} else {
    print_error('noformdesc');
}

$modlib = "$CFG->dirroot/mod/$module->name/lib.php";
if (file_exists($modlib)) {
    include_once($modlib);
} else {
    print_error('modulemissingcode', '', '', $modlib);
}

$mformclassname = 'mod_'.$module->name.'_mod_form';
$mform = new $mformclassname($data, $cw->section, $cm, $course);
$mform->set_data($data);

if ($mform->is_cancelled()) {
    if ($return && !empty($cm->id)) {
        redirect("$CFG->wwwroot/mod/$module->name/view.php?id=$cm->id");
    } else {
        redirect(course_get_url($course, $cw->section, array('sr' => $sectionreturn)));
    }
} else if ($fromform = $mform->get_data()) {
    if (empty($fromform->coursemodule)) {
        // Add
        $cm = null;
        $course = $DB->get_record('course', array('id'=>$fromform->course), '*', MUST_EXIST);
        $fromform->instance     = '';
        $fromform->coursemodule = '';
    } else {
        // Update
        $cm = get_coursemodule_from_id('', $fromform->coursemodule, 0, false, MUST_EXIST);
        $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
        $fromform->instance     = $cm->instance;
        $fromform->coursemodule = $cm->id;
    }

    if (!empty($fromform->coursemodule)) {
        $context = context_module::instance($fromform->coursemodule);
    } else {
        $context = context_course::instance($course->id);
    }

    $fromform->course = $course->id;
    $fromform->modulename = clean_param($fromform->modulename, PARAM_PLUGIN);  // For safety

    $addinstancefunction    = $fromform->modulename."_add_instance";
    $updateinstancefunction = $fromform->modulename."_update_instance";

    if (!isset($fromform->groupingid)) {
        $fromform->groupingid = 0;
    }

    if (!isset($fromform->groupmembersonly)) {
        $fromform->groupmembersonly = 0;
    }

    if (!isset($fromform->name)) { //label
        $fromform->name = $fromform->modulename;
    }

    if (!isset($fromform->completion)) {
        $fromform->completion = COMPLETION_DISABLED;
    }
    if (!isset($fromform->completionview)) {
        $fromform->completionview = COMPLETION_VIEW_NOT_REQUIRED;
    }

    // Convert the 'use grade' checkbox into a grade-item number: 0 if
    // checked, null if not
    if (isset($fromform->completionusegrade) && $fromform->completionusegrade) {
        $fromform->completiongradeitemnumber = 0;
    } else {
        $fromform->completiongradeitemnumber = null;
    }

    // the type of event to trigger (mod_created/mod_updated)
    $eventname = '';

    if (!empty($fromform->update)) {

        if (!empty($course->groupmodeforce) or !isset($fromform->groupmode)) {
            $fromform->groupmode = $cm->groupmode; // keep original
        }

        // update course module first
        $cm->groupmode        = $fromform->groupmode;
        $cm->groupingid       = $fromform->groupingid;
        $cm->groupmembersonly = $fromform->groupmembersonly;

        $completion = new completion_info($course);
        if ($completion->is_enabled()) {
            // Update completion settings
            $cm->completion                = $fromform->completion;
            $cm->completiongradeitemnumber = $fromform->completiongradeitemnumber;
            $cm->completionview            = $fromform->completionview;
            $cm->completionexpected        = $fromform->completionexpected;
        }
        if (!empty($CFG->enableavailability)) {
            $cm->availablefrom             = $fromform->availablefrom;
            $cm->availableuntil            = $fromform->availableuntil;
            $cm->showavailability          = $fromform->showavailability;
            condition_info::update_cm_from_form($cm,$fromform,true);
        }
        if (isset($fromform->showdescription)) {
            $cm->showdescription = $fromform->showdescription;
        } else {
            $cm->showdescription = 0;
        }

        $DB->update_record('course_modules', $cm);

        $modcontext = context_module::instance($fromform->coursemodule);

        // update embedded links and save files
        if (plugin_supports('mod', $fromform->modulename, FEATURE_MOD_INTRO, true)) {
            $fromform->intro = file_save_draft_area_files($fromform->introeditor['itemid'], $modcontext->id,
                                                          'mod_'.$fromform->modulename, 'intro', 0,
                                                          array('subdirs'=>true), $fromform->introeditor['text']);
            $fromform->introformat = $fromform->introeditor['format'];
            unset($fromform->introeditor);
        }

        if (!$updateinstancefunction($fromform, $mform)) {
            print_error('cannotupdatemod', '', course_get_url($course, $cw->section), $fromform->modulename);
        }

        // make sure visibility is set correctly (in particular in calendar)
        if (has_capability('moodle/course:activityvisibility', $modcontext)) {
            set_coursemodule_visible($fromform->coursemodule, $fromform->visible);
        }

        if (isset($fromform->cmidnumber)) { //label
            // set cm idnumber - uniqueness is already verified by form validation
            set_coursemodule_idnumber($fromform->coursemodule, $fromform->cmidnumber);
        }

        // Now that module is fully updated, also update completion data if
        // required (this will wipe all user completion data and recalculate it)
        if ($completion->is_enabled() && !empty($fromform->completionunlocked)) {
            $completion->reset_all_state($cm);
        }

        $eventname = 'mod_updated';

        add_to_log($course->id, "course", "update mod",
                   "../mod/$fromform->modulename/view.php?id=$fromform->coursemodule",
                   "$fromform->modulename $fromform->instance");
        add_to_log($course->id, $fromform->modulename, "update",
                   "view.php?id=$fromform->coursemodule",
                   "$fromform->instance", $fromform->coursemodule);

    } else if (!empty($fromform->add)) {

        if (!empty($course->groupmodeforce) or !isset($fromform->groupmode)) {
            $fromform->groupmode = 0; // do not set groupmode
        }

        if (!course_allowed_module($course, $fromform->modulename)) {
            print_error('moduledisable', '', '', $fromform->modulename);
        }

        // first add course_module record because we need the context
        $newcm = new stdClass();
        $newcm->course           = $course->id;
        $newcm->module           = $fromform->module;
        $newcm->instance         = 0; // not known yet, will be updated later (this is similar to restore code)
        $newcm->visible          = $fromform->visible;
        $newcm->groupmode        = $fromform->groupmode;
        $newcm->groupingid       = $fromform->groupingid;
        $newcm->groupmembersonly = $fromform->groupmembersonly;
        $completion = new completion_info($course);
        if ($completion->is_enabled()) {
            $newcm->completion                = $fromform->completion;
            $newcm->completiongradeitemnumber = $fromform->completiongradeitemnumber;
            $newcm->completionview            = $fromform->completionview;
            $newcm->completionexpected        = $fromform->completionexpected;
        }
        if(!empty($CFG->enableavailability)) {
            $newcm->availablefrom             = $fromform->availablefrom;
            $newcm->availableuntil            = $fromform->availableuntil;
            $newcm->showavailability          = $fromform->showavailability;
        }
        if (isset($fromform->showdescription)) {
            $newcm->showdescription = $fromform->showdescription;
        } else {
            $newcm->showdescription = 0;
        }

        if (!$fromform->coursemodule = add_course_module($newcm)) {
            print_error('cannotaddcoursemodule');
        }

        if (plugin_supports('mod', $fromform->modulename, FEATURE_MOD_INTRO, true)) {
            $introeditor = $fromform->introeditor;
            unset($fromform->introeditor);
            $fromform->intro       = $introeditor['text'];
            $fromform->introformat = $introeditor['format'];
        }

        $returnfromfunc = $addinstancefunction($fromform, $mform);

        if (!$returnfromfunc or !is_number($returnfromfunc)) {
            // undo everything we can
            $modcontext = context_module::instance($fromform->coursemodule);
            delete_context(CONTEXT_MODULE, $fromform->coursemodule);
            $DB->delete_records('course_modules', array('id'=>$fromform->coursemodule));

            if (!is_number($returnfromfunc)) {
                print_error('invalidfunction', '', course_get_url($course, $cw->section));
            } else {
                print_error('cannotaddnewmodule', '', course_get_url($course, $cw->section), $fromform->modulename);
            }
        }

        $fromform->instance = $returnfromfunc;

        $DB->set_field('course_modules', 'instance', $returnfromfunc, array('id'=>$fromform->coursemodule));

        // update embedded links and save files
        $modcontext = context_module::instance($fromform->coursemodule);
        if (!empty($introeditor)) {
            $fromform->intro = file_save_draft_area_files($introeditor['itemid'], $modcontext->id,
                                                          'mod_'.$fromform->modulename, 'intro', 0,
                                                          array('subdirs'=>true), $introeditor['text']);
            $DB->set_field($fromform->modulename, 'intro', $fromform->intro, array('id'=>$fromform->instance));
        }

        // course_modules and course_sections each contain a reference
        // to each other, so we have to update one of them twice.
        $sectionid = course_add_cm_to_section($course, $fromform->coursemodule, $fromform->section);

        // make sure visibility is set correctly (in particular in calendar)
        // note: allow them to set it even without moodle/course:activityvisibility
        set_coursemodule_visible($fromform->coursemodule, $fromform->visible);

        if (isset($fromform->cmidnumber)) { //label
            // set cm idnumber - uniqueness is already verified by form validation
            set_coursemodule_idnumber($fromform->coursemodule, $fromform->cmidnumber);
        }

        // Set up conditions
        if ($CFG->enableavailability) {
            condition_info::update_cm_from_form((object)array('id'=>$fromform->coursemodule), $fromform, false);
        }

        $eventname = 'mod_created';

        add_to_log($course->id, "course", "add mod",
                   "../mod/$fromform->modulename/view.php?id=$fromform->coursemodule",
                   "$fromform->modulename $fromform->instance");
        add_to_log($course->id, $fromform->modulename, "add",
                   "view.php?id=$fromform->coursemodule",
                   "$fromform->instance", $fromform->coursemodule);
    } else {
        print_error('invaliddata');
    }

    // Trigger mod_created/mod_updated event with information about this module.
    $eventdata = new stdClass();
    $eventdata->modulename = $fromform->modulename;
    $eventdata->name       = $fromform->name;
    $eventdata->cmid       = $fromform->coursemodule;
    $eventdata->courseid   = $course->id;
    $eventdata->userid     = $USER->id;
    events_trigger($eventname, $eventdata);

    // sync idnumber with grade_item
    if ($grade_item = grade_item::fetch(array('itemtype'=>'mod', 'itemmodule'=>$fromform->modulename,
                 'iteminstance'=>$fromform->instance, 'itemnumber'=>0, 'courseid'=>$course->id))) {
        if ($grade_item->idnumber != $fromform->cmidnumber) {
            $grade_item->idnumber = $fromform->cmidnumber;
            $grade_item->update();
        }
    }

    $items = grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$fromform->modulename,
                                         'iteminstance'=>$fromform->instance, 'courseid'=>$course->id));

    // create parent category if requested and move to correct parent category
    if ($items and isset($fromform->gradecat)) {
        if ($fromform->gradecat == -1) {
            $grade_category = new grade_category();
            $grade_category->courseid = $course->id;
            $grade_category->fullname = $fromform->name;
            $grade_category->insert();
            if ($grade_item) {
                $parent = $grade_item->get_parent_category();
                $grade_category->set_parent($parent->id);
            }
            $fromform->gradecat = $grade_category->id;
        }
        foreach ($items as $itemid=>$unused) {
            $items[$itemid]->set_parent($fromform->gradecat);
            if ($itemid == $grade_item->id) {
                // use updated grade_item
                $grade_item = $items[$itemid];
            }
        }
    }

    // add outcomes if requested
    if ($outcomes = grade_outcome::fetch_all_available($course->id)) {
        $grade_items = array();

        // Outcome grade_item.itemnumber start at 1000, there is nothing above outcomes
        $max_itemnumber = 999;
        if ($items) {
            foreach($items as $item) {
                if ($item->itemnumber > $max_itemnumber) {
                    $max_itemnumber = $item->itemnumber;
                }
            }
        }

        foreach($outcomes as $outcome) {
            $elname = 'outcome_'.$outcome->id;

            if (property_exists($fromform, $elname) and $fromform->$elname) {
                // so we have a request for new outcome grade item?
                if ($items) {
                    foreach($items as $item) {
                        if ($item->outcomeid == $outcome->id) {
                            //outcome aready exists
                            continue 2;
                        }
                    }
                }

                $max_itemnumber++;

                $outcome_item = new grade_item();
                $outcome_item->courseid     = $course->id;
                $outcome_item->itemtype     = 'mod';
                $outcome_item->itemmodule   = $fromform->modulename;
                $outcome_item->iteminstance = $fromform->instance;
                $outcome_item->itemnumber   = $max_itemnumber;
                $outcome_item->itemname     = $outcome->fullname;
                $outcome_item->outcomeid    = $outcome->id;
                $outcome_item->gradetype    = GRADE_TYPE_SCALE;
                $outcome_item->scaleid      = $outcome->scaleid;
                $outcome_item->insert();

                // move the new outcome into correct category and fix sortorder if needed
                if ($grade_item) {
                    $outcome_item->set_parent($grade_item->categoryid);
                    $outcome_item->move_after_sortorder($grade_item->sortorder);

                } else if (isset($fromform->gradecat)) {
                    $outcome_item->set_parent($fromform->gradecat);
                }
            }
        }
    }

    if (plugin_supports('mod', $fromform->modulename, FEATURE_ADVANCED_GRADING, false)
            and has_capability('moodle/grade:managegradingforms', $modcontext)) {
        require_once($CFG->dirroot.'/grade/grading/lib.php');
        $gradingman = get_grading_manager($modcontext, 'mod_'.$fromform->modulename);
        $showgradingmanagement = false;
        foreach ($gradingman->get_available_areas() as $areaname => $aretitle) {
            $formfield = 'advancedgradingmethod_'.$areaname;
            if (isset($fromform->{$formfield})) {
                $gradingman->set_area($areaname);
                $methodchanged = $gradingman->set_active_method($fromform->{$formfield});
                if (empty($fromform->{$formfield})) {
                    // going back to the simple direct grading is not a reason
                    // to open the management screen
                    $methodchanged = false;
                }
                $showgradingmanagement = $showgradingmanagement || $methodchanged;
            }
        }
    }

    rebuild_course_cache($course->id);
    grade_regrade_final_grades($course->id);
    plagiarism_save_form_elements($fromform); //save plagiarism settings

    if (isset($fromform->submitbutton)) {
        if (empty($showgradingmanagement)) {
            redirect("$CFG->wwwroot/mod/$module->name/view.php?id=$fromform->coursemodule");
        } else {
            $returnurl = new moodle_url("/mod/$module->name/view.php", array('id' => $fromform->coursemodule));
            redirect($gradingman->get_management_url($returnurl));
        }
    } else {
        redirect(course_get_url($course, $cw->section, array('sr' => $sectionreturn)));
    }
    exit;

} else {

    $streditinga = get_string('editinga', 'moodle', $fullmodulename);
    $strmodulenameplural = get_string('modulenameplural', $module->name);

    if (!empty($cm->id)) {
        $context = context_module::instance($cm->id);
    } else {
        $context = context_course::instance($course->id);
    }

    $PAGE->set_heading($course->fullname);
    $PAGE->set_title($streditinga);
    $PAGE->set_cacheable(false);
    echo $OUTPUT->header();

    if (get_string_manager()->string_exists('modulename_help', $module->name)) {
        echo $OUTPUT->heading_with_help($pageheading, 'modulename', $module->name, 'icon');
    } else {
        echo $OUTPUT->heading_with_help($pageheading, '', $module->name, 'icon');
    }

    $mform->display();

    echo $OUTPUT->footer();
}
