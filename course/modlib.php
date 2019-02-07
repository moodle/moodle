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
 * Library of functions specific to course/modedit.php and course API functions.
 * The course API function calling them are course/lib.php:create_module() and update_module().
 * This file has been created has an alternative solution to a full refactor of course/modedit.php
 * in order to create the course API functions.
 *
 * @copyright 2013 Jerome Mouneyrac
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_course
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/lib.php');

/**
 * Add course module.
 *
 * The function does not check user capabilities.
 * The function creates course module, module instance, add the module to the correct section.
 * It also trigger common action that need to be done after adding/updating a module.
 *
 * @param object $moduleinfo the moudle data
 * @param object $course the course of the module
 * @param object $mform this is required by an existing hack to deal with files during MODULENAME_add_instance()
 * @return object the updated module info
 */
function add_moduleinfo($moduleinfo, $course, $mform = null) {
    global $DB, $CFG;

    // Attempt to include module library before we make any changes to DB.
    include_modulelib($moduleinfo->modulename);

    $moduleinfo->course = $course->id;
    $moduleinfo = set_moduleinfo_defaults($moduleinfo);

    if (!empty($course->groupmodeforce) or !isset($moduleinfo->groupmode)) {
        $moduleinfo->groupmode = 0; // Do not set groupmode.
    }

    // First add course_module record because we need the context.
    $newcm = new stdClass();
    $newcm->course           = $course->id;
    $newcm->module           = $moduleinfo->module;
    $newcm->instance         = 0; // Not known yet, will be updated later (this is similar to restore code).
    $newcm->visible          = $moduleinfo->visible;
    $newcm->visibleold       = $moduleinfo->visible;
    $newcm->visibleoncoursepage = $moduleinfo->visibleoncoursepage;
    if (isset($moduleinfo->cmidnumber)) {
        $newcm->idnumber         = $moduleinfo->cmidnumber;
    }
    $newcm->groupmode        = $moduleinfo->groupmode;
    $newcm->groupingid       = $moduleinfo->groupingid;
    $completion = new completion_info($course);
    if ($completion->is_enabled()) {
        $newcm->completion                = $moduleinfo->completion;
        $newcm->completiongradeitemnumber = $moduleinfo->completiongradeitemnumber;
        $newcm->completionview            = $moduleinfo->completionview;
        $newcm->completionexpected        = $moduleinfo->completionexpected;
    }
    if(!empty($CFG->enableavailability)) {
        // This code is used both when submitting the form, which uses a long
        // name to avoid clashes, and by unit test code which uses the real
        // name in the table.
        $newcm->availability = null;
        if (property_exists($moduleinfo, 'availabilityconditionsjson')) {
            if ($moduleinfo->availabilityconditionsjson !== '') {
                $newcm->availability = $moduleinfo->availabilityconditionsjson;
            }
        } else if (property_exists($moduleinfo, 'availability')) {
            $newcm->availability = $moduleinfo->availability;
        }
        // If there is any availability data, verify it.
        if ($newcm->availability) {
            $tree = new \core_availability\tree(json_decode($newcm->availability));
            // Save time and database space by setting null if the only data
            // is an empty tree.
            if ($tree->is_empty()) {
                $newcm->availability = null;
            }
        }
    }
    if (isset($moduleinfo->showdescription)) {
        $newcm->showdescription = $moduleinfo->showdescription;
    } else {
        $newcm->showdescription = 0;
    }

    // From this point we make database changes, so start transaction.
    $transaction = $DB->start_delegated_transaction();

    if (!$moduleinfo->coursemodule = add_course_module($newcm)) {
        print_error('cannotaddcoursemodule');
    }

    if (plugin_supports('mod', $moduleinfo->modulename, FEATURE_MOD_INTRO, true) &&
            isset($moduleinfo->introeditor)) {
        $introeditor = $moduleinfo->introeditor;
        unset($moduleinfo->introeditor);
        $moduleinfo->intro       = $introeditor['text'];
        $moduleinfo->introformat = $introeditor['format'];
    }

    $addinstancefunction    = $moduleinfo->modulename."_add_instance";
    try {
        $returnfromfunc = $addinstancefunction($moduleinfo, $mform);
    } catch (moodle_exception $e) {
        $returnfromfunc = $e;
    }
    if (!$returnfromfunc or !is_number($returnfromfunc)) {
        // Undo everything we can. This is not necessary for databases which
        // support transactions, but improves consistency for other databases.
        context_helper::delete_instance(CONTEXT_MODULE, $moduleinfo->coursemodule);
        $DB->delete_records('course_modules', array('id'=>$moduleinfo->coursemodule));

        if ($returnfromfunc instanceof moodle_exception) {
            throw $returnfromfunc;
        } else if (!is_number($returnfromfunc)) {
            print_error('invalidfunction', '', course_get_url($course, $moduleinfo->section));
        } else {
            print_error('cannotaddnewmodule', '', course_get_url($course, $moduleinfo->section), $moduleinfo->modulename);
        }
    }

    $moduleinfo->instance = $returnfromfunc;

    $DB->set_field('course_modules', 'instance', $returnfromfunc, array('id'=>$moduleinfo->coursemodule));

    // Update embedded links and save files.
    $modcontext = context_module::instance($moduleinfo->coursemodule);
    if (!empty($introeditor)) {
        // This will respect a module that has set a value for intro in it's modname_add_instance() function.
        $introeditor['text'] = $moduleinfo->intro;

        $moduleinfo->intro = file_save_draft_area_files($introeditor['itemid'], $modcontext->id,
                                                      'mod_'.$moduleinfo->modulename, 'intro', 0,
                                                      array('subdirs'=>true), $introeditor['text']);
        $DB->set_field($moduleinfo->modulename, 'intro', $moduleinfo->intro, array('id'=>$moduleinfo->instance));
    }

    // Add module tags.
    if (core_tag_tag::is_enabled('core', 'course_modules') && isset($moduleinfo->tags)) {
        core_tag_tag::set_item_tags('core', 'course_modules', $moduleinfo->coursemodule, $modcontext, $moduleinfo->tags);
    }

    // Course_modules and course_sections each contain a reference to each other.
    // So we have to update one of them twice.
    $sectionid = course_add_cm_to_section($course, $moduleinfo->coursemodule, $moduleinfo->section);

    // Trigger event based on the action we did.
    // Api create_from_cm expects modname and id property, and we don't want to modify $moduleinfo since we are returning it.
    $eventdata = clone $moduleinfo;
    $eventdata->modname = $eventdata->modulename;
    $eventdata->id = $eventdata->coursemodule;
    $event = \core\event\course_module_created::create_from_cm($eventdata, $modcontext);
    $event->trigger();

    $moduleinfo = edit_module_post_actions($moduleinfo, $course);
    $transaction->allow_commit();

    return $moduleinfo;
}

/**
 * Hook for plugins to take action when a module is created or updated.
 *
 * @param stdClass $moduleinfo the module info
 * @param stdClass $course the course of the module
 *
 * @return stdClass moduleinfo updated by plugins.
 */
function plugin_extend_coursemodule_edit_post_actions($moduleinfo, $course) {
    $callbacks = get_plugins_with_function('coursemodule_edit_post_actions', 'lib.php');
    foreach ($callbacks as $type => $plugins) {
        foreach ($plugins as $plugin => $pluginfunction) {
            $moduleinfo = $pluginfunction($moduleinfo, $course);
        }
    }
    return $moduleinfo;
}

/**
 * Common create/update module module actions that need to be processed as soon as a module is created/updaded.
 * For example:create grade parent category, add outcomes, rebuild caches, regrade, save plagiarism settings...
 * Please note this api does not trigger events as of MOODLE 2.6. Please trigger events before calling this api.
 *
 * @param object $moduleinfo the module info
 * @param object $course the course of the module
 *
 * @return object moduleinfo update with grading management info
 */
function edit_module_post_actions($moduleinfo, $course) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $modcontext = context_module::instance($moduleinfo->coursemodule);
    $hasgrades = plugin_supports('mod', $moduleinfo->modulename, FEATURE_GRADE_HAS_GRADE, false);
    $hasoutcomes = plugin_supports('mod', $moduleinfo->modulename, FEATURE_GRADE_OUTCOMES, true);

    // Sync idnumber with grade_item.
    if ($hasgrades && $grade_item = grade_item::fetch(array('itemtype'=>'mod', 'itemmodule'=>$moduleinfo->modulename,
                 'iteminstance'=>$moduleinfo->instance, 'itemnumber'=>0, 'courseid'=>$course->id))) {
        $gradeupdate = false;
        if ($grade_item->idnumber != $moduleinfo->cmidnumber) {
            $grade_item->idnumber = $moduleinfo->cmidnumber;
            $gradeupdate = true;
        }
        if (isset($moduleinfo->gradepass) && $grade_item->gradepass != $moduleinfo->gradepass) {
            $grade_item->gradepass = $moduleinfo->gradepass;
            $gradeupdate = true;
        }
        if ($gradeupdate) {
            $grade_item->update();
        }
    }

    if ($hasgrades) {
        $items = grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$moduleinfo->modulename,
                                         'iteminstance'=>$moduleinfo->instance, 'courseid'=>$course->id));
    } else {
        $items = array();
    }

    // Create parent category if requested and move to correct parent category.
    if ($items and isset($moduleinfo->gradecat)) {
        if ($moduleinfo->gradecat == -1) {
            $grade_category = new grade_category();
            $grade_category->courseid = $course->id;
            $grade_category->fullname = $moduleinfo->name;
            $grade_category->insert();
            if ($grade_item) {
                $parent = $grade_item->get_parent_category();
                $grade_category->set_parent($parent->id);
            }
            $moduleinfo->gradecat = $grade_category->id;
        }

        foreach ($items as $itemid=>$unused) {
            $items[$itemid]->set_parent($moduleinfo->gradecat);
            if ($itemid == $grade_item->id) {
                // Use updated grade_item.
                $grade_item = $items[$itemid];
            }
        }
    }

    require_once($CFG->libdir.'/grade/grade_outcome.php');
    // Add outcomes if requested.
    if ($hasoutcomes && $outcomes = grade_outcome::fetch_all_available($course->id)) {
        $grade_items = array();

        // Outcome grade_item.itemnumber start at 1000, there is nothing above outcomes.
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

            if (property_exists($moduleinfo, $elname) and $moduleinfo->$elname) {
                // So we have a request for new outcome grade item?
                if ($items) {
                    $outcomeexists = false;
                    foreach($items as $item) {
                        if ($item->outcomeid == $outcome->id) {
                            $outcomeexists = true;
                            break;
                        }
                    }
                    if ($outcomeexists) {
                        continue;
                    }
                }

                $max_itemnumber++;

                $outcome_item = new grade_item();
                $outcome_item->courseid     = $course->id;
                $outcome_item->itemtype     = 'mod';
                $outcome_item->itemmodule   = $moduleinfo->modulename;
                $outcome_item->iteminstance = $moduleinfo->instance;
                $outcome_item->itemnumber   = $max_itemnumber;
                $outcome_item->itemname     = $outcome->fullname;
                $outcome_item->outcomeid    = $outcome->id;
                $outcome_item->gradetype    = GRADE_TYPE_SCALE;
                $outcome_item->scaleid      = $outcome->scaleid;
                $outcome_item->insert();

                // Move the new outcome into correct category and fix sortorder if needed.
                if ($grade_item) {
                    $outcome_item->set_parent($grade_item->categoryid);
                    $outcome_item->move_after_sortorder($grade_item->sortorder);

                } else if (isset($moduleinfo->gradecat)) {
                    $outcome_item->set_parent($moduleinfo->gradecat);
                }
            }
        }
    }

    if (plugin_supports('mod', $moduleinfo->modulename, FEATURE_ADVANCED_GRADING, false)
            and has_capability('moodle/grade:managegradingforms', $modcontext)) {
        require_once($CFG->dirroot.'/grade/grading/lib.php');
        $gradingman = get_grading_manager($modcontext, 'mod_'.$moduleinfo->modulename);
        $showgradingmanagement = false;
        foreach ($gradingman->get_available_areas() as $areaname => $aretitle) {
            $formfield = 'advancedgradingmethod_'.$areaname;
            if (isset($moduleinfo->{$formfield})) {
                $gradingman->set_area($areaname);
                $methodchanged = $gradingman->set_active_method($moduleinfo->{$formfield});
                if (empty($moduleinfo->{$formfield})) {
                    // Going back to the simple direct grading is not a reason to open the management screen.
                    $methodchanged = false;
                }
                $showgradingmanagement = $showgradingmanagement || $methodchanged;
            }
        }
        // Update grading management information.
        $moduleinfo->gradingman = $gradingman;
        $moduleinfo->showgradingmanagement = $showgradingmanagement;
    }

    rebuild_course_cache($course->id, true);
    if ($hasgrades) {
        grade_regrade_final_grades($course->id);
    }
    require_once($CFG->libdir.'/plagiarismlib.php');
    plagiarism_save_form_elements($moduleinfo);

    // Allow plugins to extend the course module form.
    $moduleinfo = plugin_extend_coursemodule_edit_post_actions($moduleinfo, $course);

    return $moduleinfo;
}


/**
 * Set module info default values for the unset module attributs.
 *
 * @param object $moduleinfo the current known data of the module
 * @return object the completed module info
 */
function set_moduleinfo_defaults($moduleinfo) {

    if (empty($moduleinfo->coursemodule)) {
        // Add.
        $cm = null;
        $moduleinfo->instance     = '';
        $moduleinfo->coursemodule = '';
    } else {
        // Update.
        $cm = get_coursemodule_from_id('', $moduleinfo->coursemodule, 0, false, MUST_EXIST);
        $moduleinfo->instance     = $cm->instance;
        $moduleinfo->coursemodule = $cm->id;
    }
    // For safety.
    $moduleinfo->modulename = clean_param($moduleinfo->modulename, PARAM_PLUGIN);

    if (!isset($moduleinfo->groupingid)) {
        $moduleinfo->groupingid = 0;
    }

    if (!isset($moduleinfo->name)) { // Label.
        $moduleinfo->name = $moduleinfo->modulename;
    }

    if (!isset($moduleinfo->completion)) {
        $moduleinfo->completion = COMPLETION_DISABLED;
    }
    if (!isset($moduleinfo->completionview)) {
        $moduleinfo->completionview = COMPLETION_VIEW_NOT_REQUIRED;
    }
    if (!isset($moduleinfo->completionexpected)) {
        $moduleinfo->completionexpected = 0;
    }

    // Convert the 'use grade' checkbox into a grade-item number: 0 if checked, null if not.
    if (isset($moduleinfo->completionusegrade) && $moduleinfo->completionusegrade) {
        $moduleinfo->completiongradeitemnumber = 0;
    } else {
        $moduleinfo->completiongradeitemnumber = null;
    }

    if (!isset($moduleinfo->conditiongradegroup)) {
        $moduleinfo->conditiongradegroup = array();
    }
    if (!isset($moduleinfo->conditionfieldgroup)) {
        $moduleinfo->conditionfieldgroup = array();
    }
    if (!isset($moduleinfo->visibleoncoursepage)) {
        $moduleinfo->visibleoncoursepage = 1;
    }

    return $moduleinfo;
}

/**
 * Check that the user can add a module. Also returns some information like the module, context and course section info.
 * The fucntion create the course section if it doesn't exist.
 *
 * @param object $course the course of the module
 * @param object $modulename the module name
 * @param object $section the section of the module
 * @return array list containing module, context, course section.
 * @throws moodle_exception if user is not allowed to perform the action or module is not allowed in this course
 */
function can_add_moduleinfo($course, $modulename, $section) {
    global $DB;

    $module = $DB->get_record('modules', array('name'=>$modulename), '*', MUST_EXIST);

    $context = context_course::instance($course->id);
    require_capability('moodle/course:manageactivities', $context);

    course_create_sections_if_missing($course, $section);
    $cw = get_fast_modinfo($course)->get_section_info($section);

    if (!course_allowed_module($course, $module->name)) {
        print_error('moduledisable');
    }

    return array($module, $context, $cw);
}

/**
 * Check if user is allowed to update module info and returns related item/data to the module.
 *
 * @param object $cm course module
 * @return array - list of course module, context, module, moduleinfo, and course section.
 * @throws moodle_exception if user is not allowed to perform the action
 */
function can_update_moduleinfo($cm) {
    global $DB;

    // Check the $USER has the right capability.
    $context = context_module::instance($cm->id);
    require_capability('moodle/course:manageactivities', $context);

    // Check module exists.
    $module = $DB->get_record('modules', array('id'=>$cm->module), '*', MUST_EXIST);

    // Check the moduleinfo exists.
    $data = $DB->get_record($module->name, array('id'=>$cm->instance), '*', MUST_EXIST);

    // Check the course section exists.
    $cw = $DB->get_record('course_sections', array('id'=>$cm->section), '*', MUST_EXIST);

    return array($cm, $context, $module, $data, $cw);
}


/**
 * Update the module info.
 * This function doesn't check the user capabilities. It updates the course module and the module instance.
 * Then execute common action to create/update module process (trigger event, rebuild cache, save plagiarism settings...).
 *
 * @param object $cm course module
 * @param object $moduleinfo module info
 * @param object $course course of the module
 * @param object $mform - the mform is required by some specific module in the function MODULE_update_instance(). This is due to a hack in this function.
 * @return array list of course module and module info.
 */
function update_moduleinfo($cm, $moduleinfo, $course, $mform = null) {
    global $DB, $CFG;

    $data = new stdClass();
    if ($mform) {
        $data = $mform->get_data();
    }

    // Attempt to include module library before we make any changes to DB.
    include_modulelib($moduleinfo->modulename);

    $moduleinfo->course = $course->id;
    $moduleinfo = set_moduleinfo_defaults($moduleinfo);

    if (!empty($course->groupmodeforce) or !isset($moduleinfo->groupmode)) {
        $moduleinfo->groupmode = $cm->groupmode; // Keep original.
    }

    // Update course module first.
    $cm->groupmode = $moduleinfo->groupmode;
    if (isset($moduleinfo->groupingid)) {
        $cm->groupingid = $moduleinfo->groupingid;
    }

    $completion = new completion_info($course);
    if ($completion->is_enabled()) {
        // Completion settings that would affect users who have already completed
        // the activity may be locked; if so, these should not be updated.
        if (!empty($moduleinfo->completionunlocked)) {
            $cm->completion = $moduleinfo->completion;
            $cm->completiongradeitemnumber = $moduleinfo->completiongradeitemnumber;
            $cm->completionview = $moduleinfo->completionview;
        }
        // The expected date does not affect users who have completed the activity,
        // so it is safe to update it regardless of the lock status.
        $cm->completionexpected = $moduleinfo->completionexpected;
    }
    if (!empty($CFG->enableavailability)) {
        // This code is used both when submitting the form, which uses a long
        // name to avoid clashes, and by unit test code which uses the real
        // name in the table.
        if (property_exists($moduleinfo, 'availabilityconditionsjson')) {
            if ($moduleinfo->availabilityconditionsjson !== '') {
                $cm->availability = $moduleinfo->availabilityconditionsjson;
            } else {
                $cm->availability = null;
            }
        } else if (property_exists($moduleinfo, 'availability')) {
            $cm->availability = $moduleinfo->availability;
        }
        // If there is any availability data, verify it.
        if ($cm->availability) {
            $tree = new \core_availability\tree(json_decode($cm->availability));
            // Save time and database space by setting null if the only data
            // is an empty tree.
            if ($tree->is_empty()) {
                $cm->availability = null;
            }
        }
    }
    if (isset($moduleinfo->showdescription)) {
        $cm->showdescription = $moduleinfo->showdescription;
    } else {
        $cm->showdescription = 0;
    }

    $DB->update_record('course_modules', $cm);

    $modcontext = context_module::instance($moduleinfo->coursemodule);

    // Update embedded links and save files.
    if (plugin_supports('mod', $moduleinfo->modulename, FEATURE_MOD_INTRO, true)) {
        $moduleinfo->intro = file_save_draft_area_files($moduleinfo->introeditor['itemid'], $modcontext->id,
                                                      'mod_'.$moduleinfo->modulename, 'intro', 0,
                                                      array('subdirs'=>true), $moduleinfo->introeditor['text']);
        $moduleinfo->introformat = $moduleinfo->introeditor['format'];
        unset($moduleinfo->introeditor);
    }
    // Get the a copy of the grade_item before it is modified incase we need to scale the grades.
    $oldgradeitem = null;
    $newgradeitem = null;
    if (!empty($data->grade_rescalegrades) && $data->grade_rescalegrades == 'yes') {
        // Fetch the grade item before it is updated.
        $oldgradeitem = grade_item::fetch(array('itemtype' => 'mod',
                                                'itemmodule' => $moduleinfo->modulename,
                                                'iteminstance' => $moduleinfo->instance,
                                                'itemnumber' => 0,
                                                'courseid' => $moduleinfo->course));
    }

    $updateinstancefunction = $moduleinfo->modulename."_update_instance";
    if (!$updateinstancefunction($moduleinfo, $mform)) {
        print_error('cannotupdatemod', '', course_get_url($course, $cm->section), $moduleinfo->modulename);
    }

    // This needs to happen AFTER the grademin/grademax have already been updated.
    if (!empty($data->grade_rescalegrades) && $data->grade_rescalegrades == 'yes') {
        // Get the grade_item after the update call the activity to scale the grades.
        $newgradeitem = grade_item::fetch(array('itemtype' => 'mod',
                                                'itemmodule' => $moduleinfo->modulename,
                                                'iteminstance' => $moduleinfo->instance,
                                                'itemnumber' => 0,
                                                'courseid' => $moduleinfo->course));
        if ($newgradeitem && $oldgradeitem->gradetype == GRADE_TYPE_VALUE && $newgradeitem->gradetype == GRADE_TYPE_VALUE) {
            $params = array(
                $course,
                $cm,
                $oldgradeitem->grademin,
                $oldgradeitem->grademax,
                $newgradeitem->grademin,
                $newgradeitem->grademax
            );
            if (!component_callback('mod_' . $moduleinfo->modulename, 'rescale_activity_grades', $params)) {
                print_error('cannotreprocessgrades', '', course_get_url($course, $cm->section), $moduleinfo->modulename);
            }
        }
    }

    // Make sure visibility is set correctly (in particular in calendar).
    if (has_capability('moodle/course:activityvisibility', $modcontext)) {
        set_coursemodule_visible($moduleinfo->coursemodule, $moduleinfo->visible, $moduleinfo->visibleoncoursepage);
    }

    if (isset($moduleinfo->cmidnumber)) { // Label.
        // Set cm idnumber - uniqueness is already verified by form validation.
        set_coursemodule_idnumber($moduleinfo->coursemodule, $moduleinfo->cmidnumber);
    }

    // Update module tags.
    if (core_tag_tag::is_enabled('core', 'course_modules') && isset($moduleinfo->tags)) {
        core_tag_tag::set_item_tags('core', 'course_modules', $moduleinfo->coursemodule, $modcontext, $moduleinfo->tags);
    }

    // Now that module is fully updated, also update completion data if required.
    // (this will wipe all user completion data and recalculate it)
    if ($completion->is_enabled() && !empty($moduleinfo->completionunlocked)) {
        $completion->reset_all_state($cm);
    }
    $cm->name = $moduleinfo->name;
    \core\event\course_module_updated::create_from_cm($cm, $modcontext)->trigger();

    $moduleinfo = edit_module_post_actions($moduleinfo, $course);

    return array($cm, $moduleinfo);
}

/**
 * Include once the module lib file.
 *
 * @param string $modulename module name of the lib to include
 * @throws moodle_exception if lib.php file for the module does not exist
 */
function include_modulelib($modulename) {
    global $CFG;
    $modlib = "$CFG->dirroot/mod/$modulename/lib.php";
    if (file_exists($modlib)) {
        include_once($modlib);
    } else {
        throw new moodle_exception('modulemissingcode', '', '', $modlib);
    }
}

/**
 * Get module information data required for updating the module.
 *
 * @param  stdClass $cm     course module object
 * @param  stdClass $course course object
 * @return array required data for updating a module
 * @since  Moodle 3.2
 */
function get_moduleinfo_data($cm, $course) {
    global $CFG;

    list($cm, $context, $module, $data, $cw) = can_update_moduleinfo($cm);

    $data->coursemodule       = $cm->id;
    $data->section            = $cw->section;  // The section number itself - relative!!! (section column in course_sections)
    $data->visible            = $cm->visible; //??  $cw->visible ? $cm->visible : 0; // section hiding overrides
    $data->visibleoncoursepage = $cm->visibleoncoursepage;
    $data->cmidnumber         = $cm->idnumber;          // The cm IDnumber
    $data->groupmode          = groups_get_activity_groupmode($cm); // locked later if forced
    $data->groupingid         = $cm->groupingid;
    $data->course             = $course->id;
    $data->module             = $module->id;
    $data->modulename         = $module->name;
    $data->instance           = $cm->instance;
    $data->completion         = $cm->completion;
    $data->completionview     = $cm->completionview;
    $data->completionexpected = $cm->completionexpected;
    $data->completionusegrade = is_null($cm->completiongradeitemnumber) ? 0 : 1;
    $data->showdescription    = $cm->showdescription;
    $data->tags               = core_tag_tag::get_item_tags_array('core', 'course_modules', $cm->id);
    if (!empty($CFG->enableavailability)) {
        $data->availabilityconditionsjson = $cm->availability;
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
        // Add existing outcomes.
        foreach ($items as $item) {
            if (!empty($item->outcomeid)) {
                $data->{'outcome_' . $item->outcomeid} = 1;
            } else if (isset($item->gradepass)) {
                $decimalpoints = $item->get_decimals();
                $data->gradepass = format_float($item->gradepass, $decimalpoints);
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
    return array($cm, $context, $module, $data, $cw);
}

/**
 * Prepare the standard module information for a new module instance.
 *
 * @param  stdClass $course  course object
 * @param  string $modulename  module name
 * @param  int $section section number
 * @return array module information about other required data
 * @since  Moodle 3.2
 */
function prepare_new_moduleinfo_data($course, $modulename, $section) {
    global $CFG;

    list($module, $context, $cw) = can_add_moduleinfo($course, $modulename, $section);

    $cm = null;

    $data = new stdClass();
    $data->section          = $section;  // The section number itself - relative!!! (section column in course_sections)
    $data->visible          = $cw->visible;
    $data->course           = $course->id;
    $data->module           = $module->id;
    $data->modulename       = $module->name;
    $data->groupmode        = $course->groupmode;
    $data->groupingid       = $course->defaultgroupingid;
    $data->id               = '';
    $data->instance         = '';
    $data->coursemodule     = '';

    // Apply completion defaults.
    $defaults = \core_completion\manager::get_default_completion($course, $module);
    foreach ($defaults as $key => $value) {
        $data->$key = $value;
    }

    if (plugin_supports('mod', $data->modulename, FEATURE_MOD_INTRO, true)) {
        $draftid_editor = file_get_submitted_draft_itemid('introeditor');
        file_prepare_draft_area($draftid_editor, null, null, null, null, array('subdirs'=>true));
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

    return array($module, $context, $cw, $cm, $data);
}
