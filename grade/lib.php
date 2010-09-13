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

require_once $CFG->libdir.'/gradelib.php';

/**
 * This class iterates over all users that are graded in a course.
 * Returns detailed info about users and their grades.
 */
class graded_users_iterator {
    var $course;
    var $grade_items;
    var $groupid;
    var $users_rs;
    var $grades_rs;
    var $gradestack;
    var $sortfield1;
    var $sortorder1;
    var $sortfield2;
    var $sortorder2;

    /**
     * Constructor
     * @param $course object
     * @param array grade_items array of grade items, if not specified only user info returned
     * @param int $groupid iterate only group users if present
     * @param string $sortfield1 The first field of the users table by which the array of users will be sorted
     * @param string $sortorder1 The order in which the first sorting field will be sorted (ASC or DESC)
     * @param string $sortfield2 The second field of the users table by which the array of users will be sorted
     * @param string $sortorder2 The order in which the second sorting field will be sorted (ASC or DESC)
     */
    function graded_users_iterator($course, $grade_items=null, $groupid=0, $sortfield1='lastname', $sortorder1='ASC', $sortfield2='firstname', $sortorder2='ASC') {
        $this->course      = $course;
        $this->grade_items = $grade_items;
        $this->groupid     = $groupid;
        $this->sortfield1  = $sortfield1;
        $this->sortorder1  = $sortorder1;
        $this->sortfield2  = $sortfield2;
        $this->sortorder2  = $sortorder2;

        $this->gradestack  = array();
    }

    /**
     * Initialise the iterator
     * @return boolean success
     */
    function init() {
        global $CFG;

        $this->close();

        grade_regrade_final_grades($this->course->id);
        $course_item = grade_item::fetch_course_item($this->course->id);
        if ($course_item->needsupdate) {
            // can not calculate all final grades - sorry
            return false;
        }

        if (strpos($CFG->gradebookroles, ',') === false) {
            $gradebookroles = " = {$CFG->gradebookroles}";
        } else {
            $gradebookroles = " IN ({$CFG->gradebookroles})";
        }

        $relatedcontexts = get_related_contexts_string(get_context_instance(CONTEXT_COURSE, $this->course->id));

        if ($this->groupid) {
            $groupsql = "INNER JOIN {$CFG->prefix}groups_members gm ON gm.userid = u.id";
            $groupwheresql = "AND gm.groupid = {$this->groupid}";
        } else {
            $groupsql = "";
            $groupwheresql = "";
        }

        if (empty($this->sortfield1)) {
            // we must do some sorting even if not specified
            $ofields = ", u.id AS usrt";
            $order   = "usrt ASC";

        } else {
            $ofields = ", u.$this->sortfield1 AS usrt1";
            $order   = "usrt1 $this->sortorder1";
            if (!empty($this->sortfield2)) {
                $ofields .= ", u.$this->sortfield2 AS usrt2";
                $order   .= ", usrt2 $this->sortorder2";
            }
            if ($this->sortfield1 != 'id' and $this->sortfield2 != 'id') {
                // user order MUST be the same in both queries, must include the only unique user->id if not already present
                $ofields .= ", u.id AS usrt";
                $order   .= ", usrt ASC";
            }
        }

        $users_sql = "SELECT u.* $ofields
                        FROM {$CFG->prefix}user u
                             INNER JOIN {$CFG->prefix}role_assignments ra ON u.id = ra.userid
                             $groupsql
                       WHERE ra.roleid $gradebookroles
                             AND ra.contextid $relatedcontexts
                             $groupwheresql
                    ORDER BY $order";

        $this->users_rs = get_recordset_sql($users_sql);

        if (!empty($this->grade_items)) {
            $itemids = array_keys($this->grade_items);
            $itemids = implode(',', $itemids);

            $grades_sql = "SELECT g.* $ofields
                             FROM {$CFG->prefix}grade_grades g
                                  INNER JOIN {$CFG->prefix}user u ON g.userid = u.id
                                  INNER JOIN {$CFG->prefix}role_assignments ra ON u.id = ra.userid
                                  $groupsql
                            WHERE ra.roleid $gradebookroles
                                  AND ra.contextid $relatedcontexts
                                  AND g.itemid IN ($itemids)
                                  $groupwheresql
                         ORDER BY $order, g.itemid ASC";
            $this->grades_rs = get_recordset_sql($grades_sql);
        } else {
            $this->grades_rs = false;
        }

        return true;
    }

    /**
     * Returns information about the next user
     * @return mixed array of user info, all grades and feedback or null when no more users found
     */
    function next_user() {
        if (!$this->users_rs) {
            return false; // no users present
        }

        if (!$user = rs_fetch_next_record($this->users_rs)) {
            if ($current = $this->_pop()) {
                // this is not good - user or grades updated between the two reads above :-(
            }

            return false; // no more users
        }

        // find grades of this user
        $grade_records = array();
        while (true) {
            if (!$current = $this->_pop()) {
                break; // no more grades
            }

            if ($current->userid != $user->id) {
                // grade of the next user, we have all for this user
                $this->_push($current);
                break;
            }

            $grade_records[$current->itemid] = $current;
        }

        $grades = array();
        $feedbacks = array();

        if (!empty($this->grade_items)) {
            foreach ($this->grade_items as $grade_item) {
                if (array_key_exists($grade_item->id, $grade_records)) {
                    $feedbacks[$grade_item->id]->feedback       = $grade_records[$grade_item->id]->feedback;
                    $feedbacks[$grade_item->id]->feedbackformat = $grade_records[$grade_item->id]->feedbackformat;
                    unset($grade_records[$grade_item->id]->feedback);
                    unset($grade_records[$grade_item->id]->feedbackformat);
                    $grades[$grade_item->id] = new grade_grade($grade_records[$grade_item->id], false);
                } else {
                    $feedbacks[$grade_item->id]->feedback       = '';
                    $feedbacks[$grade_item->id]->feedbackformat = FORMAT_MOODLE;
                    $grades[$grade_item->id] = new grade_grade(array('userid'=>$user->id, 'itemid'=>$grade_item->id), false);
                }
            }
        }

        $result = new object();
        $result->user      = $user;
        $result->grades    = $grades;
        $result->feedbacks = $feedbacks;

        return $result;
    }

    /**
     * Close the iterator, do not forget to call this function.
     * @return void
     */
    function close() {
        if ($this->users_rs) {
            rs_close($this->users_rs);
            $this->users_rs = null;
        }
        if ($this->grades_rs) {
            rs_close($this->grades_rs);
            $this->grades_rs = null;
        }
        $this->gradestack = array();
    }

    /**
     * Internal function
     */
    function _push($grade) {
        array_push($this->gradestack, $grade);
    }

    /**
     * Internal function
     */
    function _pop() {
        if (empty($this->gradestack)) {
            if (!$this->grades_rs) {
                return NULL; // no grades present
            }

            if (!$grade = rs_fetch_next_record($this->grades_rs)) {
                return NULL; // no more grades
            }

            return $grade;
        } else {
            return array_pop($this->gradestack);
        }
    }
}

/**
 * Print a selection popup form of the graded users in a course.
 *
 * @param int $courseid id of the course
 * @param string $actionpage The page receiving the data from the popoup form
 * @param int $userid   id of the currently selected user (or 'all' if they are all selected)
 * @param int $groupid id of requested group, 0 means all
 * @param int $includeall bool include all option
 * @param bool $return If true, will return the HTML, otherwise, will print directly
 * @return null
 */
function print_graded_users_selector($course, $actionpage, $userid=0, $groupid=0, $includeall=true, $return=false) {
    global $CFG, $USER;

    if (is_null($userid)) {
        $userid = $USER->id;
    }

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    $menu = array(); // Will be a list of userid => user name

    $gui = new graded_users_iterator($course, null, $groupid);
    $gui->init();
    

    $label = get_string('selectauser', 'grades');
    if ($includeall) {
        $menu[0] = get_string('allusers', 'grades');
        $label = get_string('selectalloroneuser', 'grades');
    }

    while ($userdata = $gui->next_user()) {
        $user = $userdata->user;
        $menu[$user->id] = fullname($user);
    }

    $gui->close();

    if ($includeall) {
        $menu[0] .= " (" . (count($menu) - 1) . ")";
    }

    return popup_form($CFG->wwwroot.'/grade/' . $actionpage . '&amp;userid=', $menu, 'choosegradeduser', $userid, 'choose', '', '',
                        $return, 'self', $label);
}

/**
 * Print grading plugin selection popup form.
 *
 * @param int $courseid id of course
 * @param string $active_type type of plugin on current page - import, export, report or edit
 * @param string $active_plugin active plugin type - grader, user, cvs, ...
 * @param boolean $return return as string
 * @return nothing or string if $return true
 */
function print_grade_plugin_selector($plugin_info, $return=false) {
    global $CFG;

    $menu = array();
    $count = 0;
    $active = '';

    foreach ($plugin_info as $plugin_type => $plugins) {
        if ($plugin_type == 'strings') {
            continue;
        }

        $first_plugin = reset($plugins);

        $menu[$first_plugin['link'].'&amp;'] = '--'.$plugin_info['strings'][$plugin_type];

        if (empty($plugins['id'])) {
            foreach ($plugins as $plugin) {
                $menu[$plugin['link']] = $plugin['string'];
                $count++;
            }
        }
    }

/// finally print/return the popup form
    if ($count > 1) {
        $select = popup_form('', $menu, 'choosepluginreport', '', get_string('chooseaction', 'grades'), '', '', true, 'self');
        if ($return) {
            return $select;
        } else {
            echo $select;
        }
    } else {
        // only one option - no plugin selector needed
        return '';
    }
}

/**
 * Print grading plugin selection tab-based navigation.
 *
 * @param int $courseid id of course
 * @param string $active_type type of plugin on current page - import, export, report or edit
 * @param string $active_plugin active plugin type - grader, user, cvs, ...
 * @param boolean $return return as string
 * @return nothing or string if $return true
 */
function grade_print_tabs($active_type, $active_plugin, $plugin_info, $return=false) {
    global $CFG, $COURSE;

    if (!isset($currenttab)) {
        $currenttab = '';
    }

    $tabs = array();
    $top_row  = array();
    $bottom_row = array();
    $inactive = array($active_plugin);
    $activated = array();

    $count = 0;
    $active = '';

    foreach ($plugin_info as $plugin_type => $plugins) {
        if ($plugin_type == 'strings') {
            continue;
        }

        // If $plugins is actually the definition of a child-less parent link:
        if (!empty($plugins['id'])) {
            $string = $plugins['string'];
            if (!empty($plugin_info[$active_type]['parent'])) {
                $string = $plugin_info[$active_type]['parent']['string'];
            }

            $top_row[] = new tabobject($plugin_type, $plugins['link'], $string);
            continue;
        }

        $first_plugin = reset($plugins);
        $url = $first_plugin['link'];

        if ($plugin_type == 'report') {
            $url = $CFG->wwwroot.'/grade/report/index.php?id='.$COURSE->id;
        }

        $top_row[] = new tabobject($plugin_type, $url, $plugin_info['strings'][$plugin_type]);

        if ($active_type == $plugin_type) {
            foreach ($plugins as $plugin) {
                $bottom_row[] = new tabobject($plugin['id'], $plugin['link'], $plugin['string']);
                if ($plugin['id'] == $active_plugin) {
                    $inactive = array($plugin['id']);
                }
            }
        }
    }

    $tabs[] = $top_row;
    $tabs[] = $bottom_row;

    if ($return) {
        return print_tabs($tabs, $active_type, $inactive, $activated, true);
    } else {
        print_tabs($tabs, $active_type, $inactive, $activated);
    }
}

function grade_get_plugin_info($courseid, $active_type, $active_plugin) {
    global $CFG;

    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    $plugin_info = array();
    $count = 0;
    $active = '';
    $url_prefix = $CFG->wwwroot . '/grade/';

    // Language strings
    $plugin_info['strings'] = array(
        'report' => get_string('view'),
        'edittree' => get_string('edittree', 'grades'),
        'scale' => get_string('scales'),
        'outcome' => get_string('outcomes', 'grades'),
        'letter' => get_string('letters', 'grades'),
        'export' => get_string('export', 'grades'),
        'import' => get_string('import'),
        'preferences' => get_string('mypreferences', 'grades'),
        'settings' => get_string('settings'));

    // Settings tab first
    if (has_capability('moodle/course:update', $context)) {
        $url = $url_prefix.'edit/settings/index.php?id='.$courseid;

        if ($active_type == 'settings' and $active_plugin == 'course') {
            $active = $url;
        }

        $plugin_info['settings'] = array();
        $plugin_info['settings']['course'] = array('id' => 'coursesettings', 'link' => $url, 'string' => get_string('course'));
        $count++;
    }


/// report plugins with its special structure
    if ($reports = get_list_of_plugins('grade/report', 'CVS')) {         // Get all installed reports
        foreach ($reports as $key => $plugin) {                      // Remove ones we can't see
            // Outcomes are a special core plugin depending on $CFG->enableoutcomes
            if ($plugin == 'outcomes' && empty($CFG->enableoutcomes)) {
                unset($reports[$key]);
            }
            if (!has_capability('gradereport/'.$plugin.':view', $context)) {
                unset($reports[$key]);
            }
        }
    }

    $reportnames = array();

    if (!empty($reports)) {
        foreach ($reports as $plugin) {
            $url = $url_prefix.'report/'.$plugin.'/index.php?id='.$courseid;
            if ($active_type == 'report' and $active_plugin == $plugin ) {
                $active = $url;
            }
            $reportnames[$plugin] = array('id' => $plugin, 'link' => $url, 'string' => get_string('modulename', 'gradereport_'.$plugin));

            // Add link to preferences tab if such a page exists
            if (file_exists($CFG->dirroot . '/grade/report/'.$plugin.'/preferences.php')) {
                $pref_url = $url_prefix.'report/'.$plugin.'/preferences.php?id='.$courseid;
                $plugin_info['preferences'][$plugin] = array('id' => $plugin, 'link' => $pref_url, 'string' => get_string('modulename', 'gradereport_'.$plugin));
            }

            $count++;
        }
        asort($reportnames);
    }
    if (!empty($reportnames)) {
        $plugin_info['report']=$reportnames;
    }

/// editing scripts - not real plugins
    if (has_capability('moodle/grade:manage', $context)
      or has_capability('moodle/grade:manageletters', $context)
      or has_capability('moodle/course:managescales', $context)
      or has_capability('moodle/course:update', $context)) {

        if (has_capability('moodle/grade:manage', $context)) {
            $url = $url_prefix.'edit/tree/index.php?sesskey='.sesskey().'&amp;showadvanced=0&amp;id='.$courseid;
            $url_adv = $url_prefix.'edit/tree/index.php?sesskey='.sesskey().'&amp;showadvanced=1&amp;id='.$courseid;

            if ($active_type == 'edittree' and $active_plugin == 'simpleview') {
                $active = $url;
            } elseif ($active_type == 'edittree' and $active_plugin == 'fullview') {
                $active = $url_adv;
            }

            $plugin_info['edittree'] = array();
            $plugin_info['edittree']['simpleview'] = array('id' => 'simpleview', 'link' => $url, 'string' => get_string('simpleview', 'grades'));
            $plugin_info['edittree']['fullview'] = array('id' => 'fullview', 'link' => $url_adv, 'string' => get_string('fullview', 'grades'));
            $count++;
        }

        if (has_capability('moodle/course:managescales', $context)) {
            $url = $url_prefix.'edit/scale/index.php?id='.$courseid;

            if ($active_type == 'scale' and is_null($active_plugin)) {
                $active = $url;
            }

            $plugin_info['scale'] = array();

            if ($active_type == 'scale' and $active_plugin == 'edit') {
                $edit_url = $url_prefix.'edit/scale/edit.php?courseid='.$courseid.'&amp;id='.optional_param('id', 0, PARAM_INT);
                $active = $edit_url;
                $plugin_info['scale']['view'] = array('id' => 'edit', 'link' => $edit_url, 'string' => get_string('edit'),
                    'parent' => array('id' => 'scale', 'link' => $url, 'string' => get_string('scales')));
            } else {
                $plugin_info['scale']['view'] = array('id' => 'scale', 'link' => $url, 'string' => get_string('view'));
            }

            $count++;
        }

        if (!empty($CFG->enableoutcomes) && (has_capability('moodle/grade:manage', $context) or
                                             has_capability('moodle/course:update', $context))) {

            $url_course = $url_prefix.'edit/outcome/course.php?id='.$courseid;
            $url_edit = $url_prefix.'edit/outcome/index.php?id='.$courseid;

            $plugin_info['outcome'] = array();

            if (has_capability('moodle/course:update', $context)) {  // Default to course assignment
                $plugin_info['outcome']['course'] = array('id' => 'course', 'link' => $url_course, 'string' => get_string('outcomescourse', 'grades'));
                $plugin_info['outcome']['edit'] = array('id' => 'edit', 'link' => $url_edit, 'string' => get_string('editoutcomes', 'grades'));
            } else {
                $plugin_info['outcome'] = array('id' => 'edit', 'link' => $url_course, 'string' => get_string('outcomescourse', 'grades'));
            }

            if ($active_type == 'outcome' and is_null($active_plugin)) {
                $active = $url_edit;
            } elseif ($active_type == 'outcome' and $active_plugin == 'course' ) {
                $active = $url_course;
            } elseif ($active_type == 'outcome' and $active_plugin == 'edit' ) {
                $active = $url_edit;
            } elseif ($active_type == 'outcome' and $active_plugin == 'import') {
                $plugin_info['outcome']['import'] = array('id' => 'import', 'link' => null, 'string' => get_string('importoutcomes', 'grades'));
            }

            $count++;
        }

        if (has_capability('moodle/grade:manage', $context) or has_capability('moodle/grade:manageletters', $context)) {
            $course_context = get_context_instance(CONTEXT_COURSE, $courseid);
            $url = $url_prefix.'edit/letter/index.php?id='.$courseid;
            $url_edit = $url_prefix.'edit/letter/edit.php?id='.$course_context->id;

            if ($active_type == 'letter' and $active_plugin == 'view' ) {
                $active = $url;
            } elseif ($active_type == 'letter' and $active_plugin == 'edit' ) {
                $active = $url_edit;
            }

            $plugin_info['letter'] = array();
            $plugin_info['letter']['view'] = array('id' => 'view', 'link' => $url, 'string' => get_string('view'));
            $plugin_info['letter']['edit'] = array('id' => 'edit', 'link' => $url_edit, 'string' => get_string('edit'));
            $count++;
        }
    }

/// standard import plugins
    if ($imports = get_list_of_plugins('grade/import', 'CVS')) {         // Get all installed import plugins
        foreach ($imports as $key => $plugin) {                      // Remove ones we can't see
            if (!has_capability('gradeimport/'.$plugin.':view', $context)) {
                unset($imports[$key]);
            }
        }
    }
    $importnames = array();
    if (!empty($imports)) {
        foreach ($imports as $plugin) {
            $url = $url_prefix.'import/'.$plugin.'/index.php?id='.$courseid;
            if ($active_type == 'import' and $active_plugin == $plugin ) {
                $active = $url;
            }
            $importnames[$plugin] = array('id' => $plugin, 'link' => $url, 'string' => get_string('modulename', 'gradeimport_'.$plugin));
            $count++;
        }
        asort($importnames);
    }
    if (!empty($importnames)) {
        $plugin_info['import']=$importnames;
    }

/// standard export plugins
    if ($exports = get_list_of_plugins('grade/export', 'CVS')) {         // Get all installed export plugins
        foreach ($exports as $key => $plugin) {                      // Remove ones we can't see
            if (!has_capability('gradeexport/'.$plugin.':view', $context)) {
                unset($exports[$key]);
            }
        }
    }
    $exportnames = array();
    if (!empty($exports)) {
        foreach ($exports as $plugin) {
            $url = $url_prefix.'export/'.$plugin.'/index.php?id='.$courseid;
            if ($active_type == 'export' and $active_plugin == $plugin ) {
                $active = $url;
            }
            $exportnames[$plugin] = array('id' => $plugin, 'link' => $url, 'string' => get_string('modulename', 'gradeexport_'.$plugin));
            $count++;
        }
        asort($exportnames);
    }

    if (!empty($exportnames)) {
        $plugin_info['export']=$exportnames;
    }

    // Key managers
    if ($CFG->gradepublishing) {
        $keymanager_url = $url_prefix.'export/keymanager.php?id='.$courseid;
        $plugin_info['export']['keymanager'] = array('id' => 'keymanager', 'link' => $keymanager_url, 'string' => get_string('keymanager', 'grades'));
        if ($active_type == 'export' and $active_plugin == 'keymanager' ) {
            $active = $keymanager_url;
        }
        $count++;

        $keymanager_url = $url_prefix.'import/keymanager.php?id='.$courseid;
        $plugin_info['import']['keymanager'] = array('id' => 'keymanager', 'link' => $keymanager_url, 'string' => get_string('keymanager', 'grades'));
        if ($active_type == 'import' and $active_plugin == 'keymanager' ) {
            $active = $keymanager_url;
        }
        $count++;
    }


    foreach ($plugin_info as $plugin_type => $plugins) {
        if (!empty($plugins['id']) && $active_plugin == $plugins['id']) {
            $plugin_info['strings']['active_plugin_str'] = $plugins['string'];
            break;
        }
        foreach ($plugins as $plugin) {
            if ($active_plugin == $plugin['id']) {
                $plugin_info['strings']['active_plugin_str'] = $plugin['string'];
            }
        }
    }

    // Put settings last
    if (!empty($plugin_info['settings'])) {
        $settings = $plugin_info['settings'];
        unset($plugin_info['settings']);
        $plugin_info['settings'] = $settings;
    }

    // Put preferences last
    if (!empty($plugin_info['preferences'])) {
        $prefs = $plugin_info['preferences'];
        unset($plugin_info['preferences']);
        $plugin_info['preferences'] = $prefs;
    }

    // Check import and export caps
    if (!has_capability('moodle/grade:export', $context)) {
        unset($plugin_info['export']);
    }
    if (!has_capability('moodle/grade:import', $context)) {
        unset($plugin_info['import']);
    }
    return $plugin_info;
}

/**
 * Prints the page headers, breadcrumb trail, page heading, (optional) dropdown navigation menu and
 * (optional) navigation tabs for any gradebook page. All gradebook pages MUST use these functions
 * in favour of the usual print_header(), print_header_simple(), print_heading() etc.
 * !IMPORTANT! Use of tabs.php file in gradebook pages is forbidden unless tabs are switched off at
 * the site level for the gradebook ($CFG->grade_navmethod = GRADE_NAVMETHOD_DROPDOWN).
 *
 * @param int $courseid
 * @param string $active_type The type of the current page (report, settings, import, export, scales, outcomes, letters)
 * @param string $active_plugin The plugin of the current page (grader, fullview etc...)
 * @param string $heading The heading of the page. Tries to guess if none is given
 * @param boolean $return Whether to return (true) or echo (false) the HTML generated by this function
 * @param string $bodytags Additional attributes that will be added to the <body> tag
 * @param string $buttons Additional buttons to display on the page
 *
 * @return string HTML code or nothing if $return == false
 */
function print_grade_page_head($courseid, $active_type, $active_plugin=null, $heading = false, $return=false, $bodytags='', $buttons=false, $extracss=array()) {
    global $CFG, $COURSE;
    $strgrades = get_string('grades');
    $plugin_info = grade_get_plugin_info($courseid, $active_type, $active_plugin);

    // Determine the string of the active plugin
    $stractive_plugin = ($active_plugin) ? $plugin_info['strings']['active_plugin_str'] : $heading;
    $stractive_type = $plugin_info['strings'][$active_type];

    $navlinks = array();
    $first_link = '';

    if ($active_type == 'settings' && $active_plugin != 'coursesettings') {
        $first_link = $plugin_info['report'][$active_plugin]['link'];
    } elseif ($active_type != 'report') {
        $first_link = $CFG->wwwroot.'/grade/index.php?id='.$COURSE->id;
    }

    if ($active_type == 'preferences') {
        $CFG->stylesheets[] = $CFG->wwwroot . '/grade/report/styles.css';
    }

    foreach ($extracss as $css_url) {
        $CFG->stylesheets[] = $css_url;
    }

    $navlinks[] = array('name' => $strgrades,
                        'link' => $first_link,
                        'type' => 'misc');

    $active_type_link = '';

    if (!empty($plugin_info[$active_type]['link']) && $plugin_info[$active_type]['link'] != qualified_me()) {
        $active_type_link = $plugin_info[$active_type]['link'];
    }

    if (!empty($plugin_info[$active_type]['parent']['link'])) {
        $active_type_link = $plugin_info[$active_type]['parent']['link'];
        $navlinks[] = array('name' => $stractive_type, 'link' => $active_type_link, 'type' => 'misc');
    }

    if (empty($plugin_info[$active_type]['id'])) {
        $navlinks[] = array('name' => $stractive_type, 'link' => $active_type_link, 'type' => 'misc');
    }

    $navlinks[] = array('name' => $stractive_plugin, 'link' => null, 'type' => 'misc');

    $navigation = build_navigation($navlinks);

    $title = ': ' . $stractive_plugin;
    if (empty($plugin_info[$active_type]['id']) || !empty($plugin_info[$active_type]['parent'])) {
        $title = ': ' . $stractive_type . ': ' . $stractive_plugin;
    }

    $returnval = print_header_simple($strgrades . ': ' . $stractive_type, $title, $navigation, '',
            $bodytags, true, $buttons, navmenu($COURSE), false, '', $return);

    // Guess heading if not given explicitly
    if (!$heading) {
        $heading = $stractive_plugin;
    }

    if ($CFG->grade_navmethod == GRADE_NAVMETHOD_COMBO || $CFG->grade_navmethod == GRADE_NAVMETHOD_DROPDOWN) {
        $returnval .= print_grade_plugin_selector($plugin_info, $return);
    }
    $returnval .= print_heading($heading);

    if ($CFG->grade_navmethod == GRADE_NAVMETHOD_COMBO || $CFG->grade_navmethod == GRADE_NAVMETHOD_TABS) {
        $returnval .= grade_print_tabs($active_type, $active_plugin, $plugin_info, $return);
    }

    if ($return) {
        return $returnval;
    }
}

/**
 * Utility class used for return tracking when using edit and other forms in grade plugins
 */
class grade_plugin_return {
    var $type;
    var $plugin;
    var $courseid;
    var $userid;
    var $page;

    /**
     * Constructor
     * @param array $params - associative array with return parameters, if null parameter are taken from _GET or _POST
     */
    function grade_plugin_return ($params=null) {
        if (empty($params)) {
            $this->type     = optional_param('gpr_type', null, PARAM_SAFEDIR);
            $this->plugin   = optional_param('gpr_plugin', null, PARAM_SAFEDIR);
            $this->courseid = optional_param('gpr_courseid', null, PARAM_INT);
            $this->userid   = optional_param('gpr_userid', null, PARAM_INT);
            $this->page     = optional_param('gpr_page', null, PARAM_INT);

        } else {
            foreach ($params as $key=>$value) {
                if (array_key_exists($key, $this)) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * Returns return parameters as options array suitable for buttons.
     * @return array options
     */
    function get_options() {
        if (empty($this->type)) {
            return array();
        }

        $params = array();

        if (!empty($this->plugin)) {
            $params['plugin'] = $this->plugin;
        }

        if (!empty($this->courseid)) {
            $params['id'] = $this->courseid;
        }

        if (!empty($this->userid)) {
            $params['userid'] = $this->userid;
        }

        if (!empty($this->page)) {
            $params['page'] = $this->page;
        }

        return $params;
    }

    /**
     * Returns return url
     * @param string $default default url when params not set
     * @return string url
     */
    function get_return_url($default, $extras=null) {
        global $CFG;

        if (empty($this->type) or empty($this->plugin)) {
            return $default;
        }

        $url = $CFG->wwwroot.'/grade/'.$this->type.'/'.$this->plugin.'/index.php';
        $glue = '?';

        if (!empty($this->courseid)) {
            $url .= $glue.'id='.$this->courseid;
            $glue = '&amp;';
        }

        if (!empty($this->userid)) {
            $url .= $glue.'userid='.$this->userid;
            $glue = '&amp;';
        }

        if (!empty($this->page)) {
            $url .= $glue.'page='.$this->page;
            $glue = '&amp;';
        }

        if (!empty($extras)) {
            foreach($extras as $key=>$value) {
                $url .= $glue.$key.'='.$value;
                $glue = '&amp;';
            }
        }

        return $url;
    }

    /**
     * Returns string with hidden return tracking form elements.
     * @return string
     */
    function get_form_fields() {
        if (empty($this->type)) {
            return '';
        }

        $result  = '<input type="hidden" name="gpr_type" value="'.$this->type.'" />';

        if (!empty($this->plugin)) {
            $result .= '<input type="hidden" name="gpr_plugin" value="'.$this->plugin.'" />';
        }

        if (!empty($this->courseid)) {
            $result .= '<input type="hidden" name="gpr_courseid" value="'.$this->courseid.'" />';
        }

        if (!empty($this->userid)) {
            $result .= '<input type="hidden" name="gpr_userid" value="'.$this->userid.'" />';
        }

        if (!empty($this->page)) {
            $result .= '<input type="hidden" name="gpr_page" value="'.$this->page.'" />';
        }
    }

    /**
     * Add hidden elements into mform
     * @param object $mform moodle form object
     * @return void
     */
    function add_mform_elements(&$mform) {
        if (empty($this->type)) {
            return;
        }

        $mform->addElement('hidden', 'gpr_type', $this->type);
        $mform->setType('gpr_type', PARAM_SAFEDIR);

        if (!empty($this->plugin)) {
            $mform->addElement('hidden', 'gpr_plugin', $this->plugin);
            $mform->setType('gpr_plugin', PARAM_SAFEDIR);
        }

        if (!empty($this->courseid)) {
            $mform->addElement('hidden', 'gpr_courseid', $this->courseid);
            $mform->setType('gpr_courseid', PARAM_INT);
        }

        if (!empty($this->userid)) {
            $mform->addElement('hidden', 'gpr_userid', $this->userid);
            $mform->setType('gpr_userid', PARAM_INT);
        }

        if (!empty($this->page)) {
            $mform->addElement('hidden', 'gpr_page', $this->page);
            $mform->setType('gpr_page', PARAM_INT);
        }
    }

    /**
     * Add return tracking params into url
     * @param string $url
     * @return string $url with erturn tracking params
     */
    function add_url_params($url) {
        if (empty($this->type)) {
            return $url;
        }

        if (strpos($url, '?') === false) {
            $url .= '?gpr_type='.$this->type;
        } else {
            $url .= '&amp;gpr_type='.$this->type;
        }

        if (!empty($this->plugin)) {
            $url .= '&amp;gpr_plugin='.$this->plugin;
        }

        if (!empty($this->courseid)) {
            $url .= '&amp;gpr_courseid='.$this->courseid;
        }

        if (!empty($this->userid)) {
            $url .= '&amp;gpr_userid='.$this->userid;
        }

        if (!empty($this->page)) {
            $url .= '&amp;gpr_page='.$this->page;
        }

        return $url;
    }
}

/**
 * Function central to gradebook for building and printing the navigation (breadcrumb trail).
 * @param string $path The path of the calling script (using __FILE__?)
 * @param string $pagename The language string to use as the last part of the navigation (non-link)
 * @param mixed  $id Either a plain integer (assuming the key is 'id') or an array of keys and values (e.g courseid => $courseid, itemid...)
 * @return string
 */
function grade_build_nav($path, $pagename=null, $id=null) {
    global $CFG, $COURSE;

    $strgrades = get_string('grades', 'grades');

    // Parse the path and build navlinks from its elements
    $dirroot_length = strlen($CFG->dirroot) + 1; // Add 1 for the first slash
    $path = substr($path, $dirroot_length);
    $path = str_replace('\\', '/', $path);

    $path_elements = explode('/', $path);

    $path_elements_count = count($path_elements);

    // First link is always 'grade'
    $navlinks = array();
    $navlinks[] = array('name' => $strgrades,
                        'link' => $CFG->wwwroot.'/grade/index.php?id='.$COURSE->id,
                        'type' => 'misc');

    $link = '';
    $numberofelements = 3;

    // Prepare URL params string
    $id_string = '?';
    if (!is_null($id)) {
        if (is_array($id)) {
            foreach ($id as $idkey => $idvalue) {
                $id_string .= "$idkey=$idvalue&amp;";
            }
        } else {
            $id_string .= "id=$id";
        }
    }

    $navlink4 = null;

    // Remove file extensions from filenames
    foreach ($path_elements as $key => $filename) {
        $path_elements[$key] = str_replace('.php', '', $filename);
    }

    // Second level links
    switch ($path_elements[1]) {
        case 'edit': // No link
            if ($path_elements[3] != 'index.php') {
                $numberofelements = 4;
            }
            break;
        case 'import': // No link
            break;
        case 'export': // No link
            break;
        case 'report':
            // $id is required for this link. Do not print it if $id isn't given
            if (!is_null($id)) {
                $link = $CFG->wwwroot . '/grade/report/index.php' . $id_string;
            }

            if ($path_elements[2] == 'grader') {
                $numberofelements = 4;
            }
            break;

        default:
            // If this element isn't among the ones already listed above, it isn't supported, throw an error.
            debugging("grade_build_nav() doesn't support ". $path_elements[1] . " as the second path element after 'grade'.");
            return false;
    }

    $navlinks[] = array('name' => get_string($path_elements[1], 'grades'), 'link' => $link, 'type' => 'misc');

    // Third level links
    if (empty($pagename)) {
        $pagename = get_string($path_elements[2], 'grades');
    }

    switch ($numberofelements) {
        case 3:
            $navlinks[] = array('name' => $pagename, 'link' => $link, 'type' => 'misc');
            break;
        case 4:

            if ($path_elements[2] == 'grader' AND $path_elements[3] != 'index.php') {
                $navlinks[] = array('name' => get_string('modulename', 'gradereport_grader'),
                                    'link' => "$CFG->wwwroot/grade/report/grader/index.php$id_string",
                                    'type' => 'misc');
            }
            $navlinks[] = array('name' => $pagename, 'link' => '', 'type' => 'misc');
            break;
    }
    $navigation = build_navigation($navlinks);

    return $navigation;
}

/**
 * General structure representing grade items in course
 */
class grade_structure {
    var $context;

    var $courseid;

    /**
     * 1D array of grade items only
     */
    var $items;

    /**
     * Returns icon of element
     * @param object $element
     * @param bool $spacerifnone return spacer if no icon found
     * @return string icon or spacer
     */
    function get_element_icon(&$element, $spacerifnone=false) {
        global $CFG;

        switch ($element['type']) {
            case 'item':
            case 'courseitem':
            case 'categoryitem':
                if ($element['object']->is_calculated()) {
                    $strcalc = get_string('calculatedgrade', 'grades');
                    return '<img src="'.$CFG->pixpath.'/i/calc.gif" class="icon itemicon" title="'.s($strcalc).'" alt="'.s($strcalc).'"/>';

                } else if (($element['object']->is_course_item() or $element['object']->is_category_item())
                  and ($element['object']->gradetype == GRADE_TYPE_SCALE or $element['object']->gradetype == GRADE_TYPE_VALUE)) {
                    if ($category = $element['object']->get_item_category()) {
                        switch ($category->aggregation) {
                            case GRADE_AGGREGATE_MEDIAN:
                            case GRADE_AGGREGATE_MEDIAN:
                                return '<img src="'.$CFG->pixpath.'/i/agg_sum.gif" class="icon itemicon" alt="'.get_string('aggregation', 'grades').'"/>';
                            case GRADE_AGGREGATE_WEIGHTED_MEAN:
                            case GRADE_AGGREGATE_WEIGHTED_MEAN2:
                            case GRADE_AGGREGATE_EXTRACREDIT_MEAN:
                                return '<img src="'.$CFG->pixpath.'/i/agg_mean.gif" class="icon itemicon" alt="'.get_string('aggregation', 'grades').'"/>';
                            case GRADE_AGGREGATE_SUM:
                                return '<img src="'.$CFG->pixpath.'/i/agg_sum.gif" class="icon itemicon" alt="'.get_string('aggregation', 'grades').'"/>';
                        }
                    }

                } else if ($element['object']->itemtype == 'mod') {
                    $strmodname = get_string('modulename', $element['object']->itemmodule);
                    return '<img src="'.$CFG->modpixpath.'/'.$element['object']->itemmodule.'/icon.gif" class="icon itemicon" title="' .s($strmodname).'" alt="' .s($strmodname).'"/>';

                } else if ($element['object']->itemtype == 'manual') {
                    if ($element['object']->is_outcome_item()) {
                        $stroutcome = get_string('outcome', 'grades');
                        return '<img src="'.$CFG->pixpath.'/i/outcomes.gif" class="icon itemicon" title="'.s($stroutcome).'" alt="'.s($stroutcome).'"/>';
                    } else {
                        $strmanual = get_string('manualitem', 'grades');
                        return '<img src="'.$CFG->pixpath.'/t/manual_item.gif" class="icon itemicon" title="'.s($strmanual).'" alt="'.s($strmanual).'"/>';
                    }
                }
                break;

            case 'category':
                $strcat = get_string('category', 'grades');
                return '<img src="'.$CFG->pixpath.'/f/folder.gif" class="icon itemicon" title="'.s($strcat).'" alt="'.s($strcat).'" />';
        }

        if ($spacerifnone) {
            return '<img src="'.$CFG->wwwroot.'/pix/spacer.gif" class="icon itemicon" alt=""/>';
        } else {
            return '';
        }
    }

    /**
     * Returns name of element optionally with icon and link
     * @param object $element
     * @param bool $withlinks
     * @param bool $icons
     * @param bool $spacerifnone return spacer if no icon found
     * @return header string
     */
    function get_element_header(&$element, $withlink=false, $icon=true, $spacerifnone=false) {
        global $CFG;

        $header = '';

        if ($icon) {
            $header .= $this->get_element_icon($element, $spacerifnone);
        }

        $header .= $element['object']->get_name();

        if ($element['type'] != 'item' and $element['type'] != 'categoryitem' and $element['type'] != 'courseitem') {
            return $header;
        }

        $itemtype     = $element['object']->itemtype;
        $itemmodule   = $element['object']->itemmodule;
        $iteminstance = $element['object']->iteminstance;

        if ($withlink and $itemtype=='mod' and $iteminstance and $itemmodule) {
            if ($cm = get_coursemodule_from_instance($itemmodule, $iteminstance, $this->courseid)) {

                $a->name = get_string('modulename', $element['object']->itemmodule);
                $title = get_string('linktoactivity', 'grades', $a);
                $dir = $CFG->dirroot.'/mod/'.$itemmodule;

                if (file_exists($dir.'/grade.php')) {
                    $url = $CFG->wwwroot.'/mod/'.$itemmodule.'/grade.php?id='.$cm->id;
                } else {
                    $url = $CFG->wwwroot.'/mod/'.$itemmodule.'/view.php?id='.$cm->id;
                }

                $header = '<a href="'.$url.'" title="'.s($title).'">'.$header.'</a>';
            }
        }

        return $header;
    }

    /**
     * Returns the grade eid - the grade may not exist yet.
     * @param $grade_grade object
     * @return string eid
     */
    function get_grade_eid($grade_grade) {
        if (empty($grade_grade->id)) {
            return 'n'.$grade_grade->itemid.'u'.$grade_grade->userid;
        } else {
            return 'g'.$grade_grade->id;
        }
    }

    /**
     * Returns the grade_item eid
     * @param $grade_item object
     * @return string eid
     */
    function get_item_eid($grade_item) {
        return 'i'.$grade_item->id;
    }

    function get_params_for_iconstr($element) {
        $strparams = new stdClass();
        $strparams->category = '';
        $strparams->itemname = '';
        $strparams->itemmodule = '';
        if (!method_exists($element['object'], 'get_name')) {
            return $strparams;
        }

        $strparams->itemname = html_to_text($element['object']->get_name());

        // If element name is categorytotal, get the name of the parent category
        if ($strparams->itemname == get_string('categorytotal', 'grades')) {
            $parent = $element['object']->get_parent_category();
            $strparams->category = $parent->get_name() . ' ';
        } else {
            $strparams->category = '';
        }

        $strparams->itemmodule = null;
        if (isset($element['object']->itemmodule)) {
            $strparams->itemmodule = $element['object']->itemmodule;
        }
        return $strparams;
    }

    /**
     * Return edit icon for give element
     * @param object $element
     * @return string
     */
    function get_edit_icon($element, $gpr) {
        global $CFG;

        if (!has_capability('moodle/grade:manage', $this->context)) {
            if ($element['type'] == 'grade' and has_capability('moodle/grade:edit', $this->context)) {
                // oki - let them override grade
            } else {
                return '';
            }
        }

        static $strfeedback   = null;
        static $streditgrade = null;
        if (is_null($streditgrade)) {
            $streditgrade = get_string('editgrade', 'grades');
            $strfeedback  = get_string('feedback');
        }

        $strparams = $this->get_params_for_iconstr($element);
        if ($element['type'] == 'item' or $element['type'] == 'category') {
        }

        $object = $element['object'];

        switch ($element['type']) {
            case 'item':
            case 'categoryitem':
            case 'courseitem':
                $stredit = get_string('editverbose', 'grades', $strparams);
                if (empty($object->outcomeid) || empty($CFG->enableoutcomes)) {
                    $url = $CFG->wwwroot.'/grade/edit/tree/item.php?courseid='.$this->courseid.'&amp;id='.$object->id;
                } else {
                    $url = $CFG->wwwroot.'/grade/edit/tree/outcomeitem.php?courseid='.$this->courseid.'&amp;id='.$object->id;
                }
                $url = $gpr->add_url_params($url);
                break;

            case 'category':
                $stredit = get_string('editverbose', 'grades', $strparams);
                $url = $CFG->wwwroot.'/grade/edit/tree/category.php?courseid='.$this->courseid.'&amp;id='.$object->id;
                $url = $gpr->add_url_params($url);
                break;

            case 'grade':
                $stredit = $streditgrade;
                if (empty($object->id)) {
                    $url = $CFG->wwwroot.'/grade/edit/tree/grade.php?courseid='.$this->courseid.'&amp;itemid='.$object->itemid.'&amp;userid='.$object->userid;
                } else {
                    $url = $CFG->wwwroot.'/grade/edit/tree/grade.php?courseid='.$this->courseid.'&amp;id='.$object->id;
                }
                $url = $gpr->add_url_params($url);
                if (!empty($object->feedback)) {
                    $feedback = addslashes_js(trim(format_string($object->feedback, $object->feedbackformat)));
                }
                break;

            default:
                $url = null;
        }

        if ($url) {
            return '<a href="'.$url.'"><img src="'.$CFG->pixpath.'/t/edit.gif" class="iconsmall" alt="'.s($stredit).'" title="'.s($stredit).'"/></a>';

        } else {
            return '';
        }
    }

    /**
     * Return hiding icon for give element
     * @param object $element
     * @return string
     */
    function get_hiding_icon($element, $gpr) {
        global $CFG;

        if (!has_capability('moodle/grade:manage', $this->context) and !has_capability('moodle/grade:hide', $this->context)) {
            return '';
        }

        $strparams = $this->get_params_for_iconstr($element);
        $strshow = get_string('showverbose', 'grades', $strparams);
        $strhide = get_string('hideverbose', 'grades', $strparams);

        if ($element['object']->is_hidden()) {
            $icon = 'show';
            $tooltip = $strshow;

            if ($element['type'] != 'category' and $element['object']->get_hidden() > 1) { // Change the icon and add a tooltip showing the date
                $icon = 'hiddenuntil';
                $tooltip = get_string('hiddenuntildate', 'grades', userdate($element['object']->get_hidden()));
            }

            $url     = $CFG->wwwroot.'/grade/edit/tree/action.php?id='.$this->courseid.'&amp;action=show&amp;sesskey='.sesskey()
                     . '&amp;eid='.$element['eid'];
            $url     = $gpr->add_url_params($url);
            $action  = '<a href="'.$url.'"><img alt="'.$strshow.'" src="'.$CFG->pixpath.'/t/'.$icon.'.gif" class="iconsmall" title="'.s($tooltip).'"/></a>';

        } else {
            $url     = $CFG->wwwroot.'/grade/edit/tree/action.php?id='.$this->courseid.'&amp;action=hide&amp;sesskey='.sesskey()
                     . '&amp;eid='.$element['eid'];
            $url     = $gpr->add_url_params($url);
            $action  = '<a href="'.$url.'"><img src="'.$CFG->pixpath.'/t/hide.gif" class="iconsmall" alt="'.s($strhide).'" title="'.s($strhide).'"/></a>';
        }
        return $action;
    }

    /**
     * Return locking icon for given element
     * @param object $element
     * @return string
     */
    function get_locking_icon($element, $gpr) {
        global $CFG;

        $strparams = $this->get_params_for_iconstr($element);
        $strunlock = get_string('unlockverbose', 'grades', $strparams);
        $strlock = get_string('lockverbose', 'grades', $strparams);

        // Don't allow an unlocking action for a grade whose grade item is locked: just print a state icon
        if ($element['type'] == 'grade' && $element['object']->grade_item->is_locked()) {
            $strparamobj = new stdClass();
            $strparamobj->itemname = $element['object']->grade_item->itemname;
            $strnonunlockable = get_string('nonunlockableverbose', 'grades', $strparamobj);
            $action  = '<img src="'.$CFG->pixpath.'/t/unlock_gray.gif" alt="'.s($strnonunlockable).'" class="iconsmall" title="'.s($strnonunlockable).'"/>';
        } elseif ($element['object']->is_locked()) {
            $icon = 'unlock';
            $tooltip = $strunlock;

            if ($element['type'] != 'category' and $element['object']->get_locktime() > 1) { // Change the icon and add a tooltip showing the date
                $icon = 'locktime';
                $tooltip = get_string('locktimedate', 'grades', userdate($element['object']->get_locktime()));
            }

            if (!has_capability('moodle/grade:manage', $this->context) and !has_capability('moodle/grade:unlock', $this->context)) {
                return '';
            }
            $url     = $CFG->wwwroot.'/grade/edit/tree/action.php?id='.$this->courseid.'&amp;action=unlock&amp;sesskey='.sesskey()
                     . '&amp;eid='.$element['eid'];
            $url     = $gpr->add_url_params($url);
            $action  = '<a href="'.$url.'"><img src="'.$CFG->pixpath.'/t/'.$icon.'.gif" alt="'.s($strunlock).'" class="iconsmall" title="'.s($tooltip).'"/></a>';

        } else {
            if (!has_capability('moodle/grade:manage', $this->context) and !has_capability('moodle/grade:lock', $this->context)) {
                return '';
            }
            $url     = $CFG->wwwroot.'/grade/edit/tree/action.php?id='.$this->courseid.'&amp;action=lock&amp;sesskey='.sesskey()
                     . '&amp;eid='.$element['eid'];
            $url     = $gpr->add_url_params($url);
            $action  = '<a href="'.$url.'"><img src="'.$CFG->pixpath.'/t/lock.gif" class="iconsmall" alt="'.s($strlock).'" title="'
                     . s($strlock).'"/></a>';
        }
        return $action;
    }

    /**
     * Return calculation icon for given element
     * @param object $element
     * @return string
     */
    function get_calculation_icon($element, $gpr) {
        global $CFG;
        if (!has_capability('moodle/grade:manage', $this->context)) {
            return '';
        }

        $calculation_icon = '';

        $type   = $element['type'];
        $object = $element['object'];


        if ($type == 'item' or $type == 'courseitem' or $type == 'categoryitem') {
            $strparams = $this->get_params_for_iconstr($element);
            $streditcalculation = get_string('editcalculationverbose', 'grades', $strparams);

            // show calculation icon only when calculation possible
            if (!$object->is_external_item() and ($object->gradetype == GRADE_TYPE_SCALE or $object->gradetype == GRADE_TYPE_VALUE)) {
                if ($object->is_calculated()) {
                    $icon = 'calc.gif';
                } else {
                    $icon = 'calc_off.gif';
                }
                $url = $CFG->wwwroot.'/grade/edit/tree/calculation.php?courseid='.$this->courseid.'&amp;id='.$object->id;
                $url = $gpr->add_url_params($url);
                $calculation_icon = '<a href="'. $url.'"><img src="'.$CFG->pixpath.'/t/'.$icon.'" class="iconsmall" alt="'
                                       . s($streditcalculation).'" title="'.s($streditcalculation).'" /></a>'. "\n";
            }
        }

        return $calculation_icon;
    }
}

/**
 * Flat structure similar to grade tree.
 */
class grade_seq extends grade_structure {

    /**
     * A string of GET URL variables, namely courseid and sesskey, used in most URLs built by this class.
     * @var string $commonvars
     */
    var $commonvars;

    /**
     * 1D array of elements
     */
    var $elements;

    /**
     * Constructor, retrieves and stores array of all grade_category and grade_item
     * objects for the given courseid. Full objects are instantiated. Ordering sequence is fixed if needed.
     * @param int $courseid
     * @param boolean $category_grade_last category grade item is the last child
     * @param array $collapsed array of collapsed categories
     */
    function grade_seq($courseid, $category_grade_last=false, $nooutcomes=false) {
        global $USER, $CFG;

        $this->courseid   = $courseid;
        $this->commonvars = "&amp;sesskey=$USER->sesskey&amp;id=$this->courseid";
        $this->context    = get_context_instance(CONTEXT_COURSE, $courseid);

        // get course grade tree
        $top_element = grade_category::fetch_course_tree($courseid, true);

        $this->elements = grade_seq::flatten($top_element, $category_grade_last, $nooutcomes);

        foreach ($this->elements as $key=>$unused) {
            $this->items[$this->elements[$key]['object']->id] =& $this->elements[$key]['object'];
        }
    }

    /**
     * Static recursive helper - makes the grade_item for category the last children
     * @static
     * @param array $element The seed of the recursion
     * @return void
     */
    function flatten(&$element, $category_grade_last, $nooutcomes) {
        if (empty($element['children'])) {
            return array();
        }
        $children = array();

        foreach ($element['children'] as $sortorder=>$unused) {
            if ($nooutcomes and $element['type'] != 'category' and $element['children'][$sortorder]['object']->is_outcome_item()) {
                continue;
            }
            $children[] = $element['children'][$sortorder];
        }
        unset($element['children']);

        if ($category_grade_last and count($children) > 1) {
            $cat_item = array_shift($children);
            array_push($children, $cat_item);
        }

        $result = array();
        foreach ($children as $child) {
            if ($child['type'] == 'category') {
                $result = $result + grade_seq::flatten($child, $category_grade_last, $nooutcomes);
            } else {
                $child['eid'] = 'i'.$child['object']->id;
                $result[$child['object']->id] = $child;
            }
        }

        return $result;
    }

    /**
     * Parses the array in search of a given eid and returns a element object with
     * information about the element it has found.
     * @param int $eid
     * @return object element
     */
    function locate_element($eid) {
        // it is a grade - construct a new object
        if (strpos($eid, 'n') === 0) {
            if (!preg_match('/n(\d+)u(\d+)/', $eid, $matches)) {
                return null;
            }

            $itemid = $matches[1];
            $userid = $matches[2];

            //extra security check - the grade item must be in this tree
            if (!$item_el = $this->locate_element('i'.$itemid)) {
                return null;
            }

            // $gradea->id may be null - means does not exist yet
            $grade = new grade_grade(array('itemid'=>$itemid, 'userid'=>$userid));

            $grade->grade_item =& $item_el['object']; // this may speedup grade_grade methods!
            return array('eid'=>'n'.$itemid.'u'.$userid,'object'=>$grade, 'type'=>'grade');

        } else if (strpos($eid, 'g') === 0) {
            $id = (int)substr($eid, 1);
            if (!$grade = grade_grade::fetch(array('id'=>$id))) {
                return null;
            }
            //extra security check - the grade item must be in this tree
            if (!$item_el = $this->locate_element('i'.$grade->itemid)) {
                return null;
            }
            $grade->grade_item =& $item_el['object']; // this may speedup grade_grade methods!
            return array('eid'=>'g'.$id,'object'=>$grade, 'type'=>'grade');
        }

        // it is a category or item
        foreach ($this->elements as $element) {
            if ($element['eid'] == $eid) {
                return $element;
            }
        }

        return null;
    }
}

/**
 * This class represents a complete tree of categories, grade_items and final grades,
 * organises as an array primarily, but which can also be converted to other formats.
 * It has simple method calls with complex implementations, allowing for easy insertion,
 * deletion and moving of items and categories within the tree.
 */
class grade_tree extends grade_structure {

    /**
     * The basic representation of the tree as a hierarchical, 3-tiered array.
     * @var object $top_element
     */
    var $top_element;

    /**
     * A string of GET URL variables, namely courseid and sesskey, used in most URLs built by this class.
     * @var string $commonvars
     */
    var $commonvars;

    /**
     * 2D array of grade items and categories
     */
    var $levels;

    /**
     * Grade items
     */
    var $items;

    /**
     * Constructor, retrieves and stores a hierarchical array of all grade_category and grade_item
     * objects for the given courseid. Full objects are instantiated. Ordering sequence is fixed if needed.
     * @param int $courseid
     * @param boolean $fillers include fillers and colspans, make the levels var "rectangular"
     * @param boolean $category_grade_last category grade item is the last child
     * @param array $collapsed array of collapsed categories
     */
    function grade_tree($courseid, $fillers=true, $category_grade_last=false, $collapsed=null, $nooutcomes=false) {
        global $USER, $CFG;

        $this->courseid   = $courseid;
        $this->commonvars = "&amp;sesskey=$USER->sesskey&amp;id=$this->courseid";
        $this->levels     = array();
        $this->context    = get_context_instance(CONTEXT_COURSE, $courseid);

        // get course grade tree
        $this->top_element = grade_category::fetch_course_tree($courseid, true);

        // collapse the categories if requested
        if (!empty($collapsed)) {
            grade_tree::category_collapse($this->top_element, $collapsed);
        }

        // no otucomes if requested
        if (!empty($nooutcomes)) {
            grade_tree::no_outcomes($this->top_element);
        }

        // move category item to last position in category
        if ($category_grade_last) {
            grade_tree::category_grade_last($this->top_element);
        }

        if ($fillers) {
            // inject fake categories == fillers
            grade_tree::inject_fillers($this->top_element, 0);
            // add colspans to categories and fillers
            grade_tree::inject_colspans($this->top_element);
        }

        grade_tree::fill_levels($this->levels, $this->top_element, 0);

    }

    /**
     * Static recursive helper - removes items from collapsed categories
     * @static
     * @param array $element The seed of the recursion
     * @param array $collapsed array of collapsed categories
     * @return void
     */
    function category_collapse(&$element, $collapsed) {
        if ($element['type'] != 'category') {
            return;
        }
        if (empty($element['children']) or count($element['children']) < 2) {
            return;
        }

        if (in_array($element['object']->id, $collapsed['aggregatesonly'])) {
            $category_item = reset($element['children']); //keep only category item
            $element['children'] = array(key($element['children'])=>$category_item);

        } else {
            if (in_array($element['object']->id, $collapsed['gradesonly'])) { // Remove category item
                reset($element['children']);
                $first_key = key($element['children']);
                unset($element['children'][$first_key]);
            }
            foreach ($element['children'] as $sortorder=>$child) { // Recurse through the element's children
                grade_tree::category_collapse($element['children'][$sortorder], $collapsed);
            }
        }
    }

    /**
     * Static recursive helper - removes all outcomes
     * @static
     * @param array $element The seed of the recursion
     * @return void
     */
    function no_outcomes(&$element) {
        if ($element['type'] != 'category') {
            return;
        }
        foreach ($element['children'] as $sortorder=>$child) {
            if ($element['children'][$sortorder]['type'] == 'item'
              and $element['children'][$sortorder]['object']->is_outcome_item()) {
                unset($element['children'][$sortorder]);

            } else if ($element['children'][$sortorder]['type'] == 'category') {
                grade_tree::no_outcomes($element['children'][$sortorder]);
            }
        }
    }

    /**
     * Static recursive helper - makes the grade_item for category the last children
     * @static
     * @param array $element The seed of the recursion
     * @return void
     */
    function category_grade_last(&$element) {
        if (empty($element['children'])) {
            return;
        }
        if (count($element['children']) < 2) {
            return;
        }
        $first_item = reset($element['children']);
        if ($first_item['type'] == 'categoryitem' or $first_item['type'] == 'courseitem') {
            // the category item might have been already removed
            $order = key($element['children']);
            unset($element['children'][$order]);
            $element['children'][$order] =& $first_item;
        }
        foreach ($element['children'] as $sortorder => $child) {
            grade_tree::category_grade_last($element['children'][$sortorder]);
        }
    }

    /**
     * Static recursive helper - fills the levels array, useful when accessing tree elements of one level
     * @static
     * @param int $levels
     * @param array $element The seed of the recursion
     * @param int $depth
     * @return void
     */
    function fill_levels(&$levels, &$element, $depth) {
        if (!array_key_exists($depth, $levels)) {
            $levels[$depth] = array();
        }

        // prepare unique identifier
        if ($element['type'] == 'category') {
            $element['eid'] = 'c'.$element['object']->id;
        } else if (in_array($element['type'], array('item', 'courseitem', 'categoryitem'))) {
            $element['eid'] = 'i'.$element['object']->id;
            $this->items[$element['object']->id] =& $element['object'];
        }

        $levels[$depth][] =& $element;
        $depth++;
        if (empty($element['children'])) {
            return;
        }
        $prev = 0;
        foreach ($element['children'] as $sortorder=>$child) {
            grade_tree::fill_levels($levels, $element['children'][$sortorder], $depth);
            $element['children'][$sortorder]['prev'] = $prev;
            $element['children'][$sortorder]['next'] = 0;
            if ($prev) {
                $element['children'][$prev]['next'] = $sortorder;
            }
            $prev = $sortorder;
        }
    }

    /**
     * Static recursive helper - makes full tree (all leafes are at the same level)
     */
    function inject_fillers(&$element, $depth) {
        $depth++;

        if (empty($element['children'])) {
            return $depth;
        }
        $chdepths = array();
        $chids = array_keys($element['children']);
        $last_child  = end($chids);
        $first_child = reset($chids);

        foreach ($chids as $chid) {
            $chdepths[$chid] = grade_tree::inject_fillers($element['children'][$chid], $depth);
        }
        arsort($chdepths);

        $maxdepth = reset($chdepths);
        foreach ($chdepths as $chid=>$chd) {
            if ($chd == $maxdepth) {
                continue;
            }
            for ($i=0; $i < $maxdepth-$chd; $i++) {
                if ($chid == $first_child) {
                    $type = 'fillerfirst';
                } else if ($chid == $last_child) {
                    $type = 'fillerlast';
                } else {
                    $type = 'filler';
                }
                $oldchild =& $element['children'][$chid];
                $element['children'][$chid] = array('object'=>'filler', 'type'=>$type, 'eid'=>'', 'depth'=>$element['object']->depth,'children'=>array($oldchild));
            }
        }

        return $maxdepth;
    }

    /**
     * Static recursive helper - add colspan information into categories
     */
    function inject_colspans(&$element) {
        if (empty($element['children'])) {
            return 1;
        }
        $count = 0;
        foreach ($element['children'] as $key=>$child) {
            $count += grade_tree::inject_colspans($element['children'][$key]);
        }
        $element['colspan'] = $count;
        return $count;
    }

    /**
     * Parses the array in search of a given eid and returns a element object with
     * information about the element it has found.
     * @param int $eid
     * @return object element
     */
    function locate_element($eid) {
        // it is a grade - construct a new object
        if (strpos($eid, 'n') === 0) {
            if (!preg_match('/n(\d+)u(\d+)/', $eid, $matches)) {
                return null;
            }

            $itemid = $matches[1];
            $userid = $matches[2];

            //extra security check - the grade item must be in this tree
            if (!$item_el = $this->locate_element('i'.$itemid)) {
                return null;
            }

            // $gradea->id may be null - means does not exist yet
            $grade = new grade_grade(array('itemid'=>$itemid, 'userid'=>$userid));

            $grade->grade_item =& $item_el['object']; // this may speedup grade_grade methods!
            return array('eid'=>'n'.$itemid.'u'.$userid,'object'=>$grade, 'type'=>'grade');

        } else if (strpos($eid, 'g') === 0) {
            $id = (int)substr($eid, 1);
            if (!$grade = grade_grade::fetch(array('id'=>$id))) {
                return null;
            }
            //extra security check - the grade item must be in this tree
            if (!$item_el = $this->locate_element('i'.$grade->itemid)) {
                return null;
            }
            $grade->grade_item =& $item_el['object']; // this may speedup grade_grade methods!
            return array('eid'=>'g'.$id,'object'=>$grade, 'type'=>'grade');
        }

        // it is a category or item
        foreach ($this->levels as $row) {
            foreach ($row as $element) {
                if ($element['type'] == 'filler') {
                    continue;
                }
                if ($element['eid'] == $eid) {
                    return $element;
                }
            }
        }

        return null;
    }
}

?>
