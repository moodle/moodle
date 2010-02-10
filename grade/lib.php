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
 * Functions used by gradebook plugins and reports.
 *
 * @package   moodlecore
 * @copyright 2009 Petr Skoda and Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once $CFG->libdir.'/gradelib.php';

/**
 * This class iterates over all users that are graded in a course.
 * Returns detailed info about users and their grades.
 *
 * @author Petr Skoda <skodak@moodle.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class graded_users_iterator {
    public $course;
    public $grade_items;
    public $groupid;
    public $users_rs;
    public $grades_rs;
    public $gradestack;
    public $sortfield1;
    public $sortorder1;
    public $sortfield2;
    public $sortorder2;

    /**
     * Constructor
     *
     * @param object $course A course object
     * @param array  $grade_items array of grade items, if not specified only user info returned
     * @param int    $groupid iterate only group users if present
     * @param string $sortfield1 The first field of the users table by which the array of users will be sorted
     * @param string $sortorder1 The order in which the first sorting field will be sorted (ASC or DESC)
     * @param string $sortfield2 The second field of the users table by which the array of users will be sorted
     * @param string $sortorder2 The order in which the second sorting field will be sorted (ASC or DESC)
     */
    public function graded_users_iterator($course, $grade_items=null, $groupid=0,
                                          $sortfield1='lastname', $sortorder1='ASC',
                                          $sortfield2='firstname', $sortorder2='ASC') {
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
    public function init() {
        global $CFG, $DB;

        $this->close();

        grade_regrade_final_grades($this->course->id);
        $course_item = grade_item::fetch_course_item($this->course->id);
        if ($course_item->needsupdate) {
            // can not calculate all final grades - sorry
            return false;
        }

        list($gradebookroles_sql, $params) =
            $DB->get_in_or_equal(explode(',', $CFG->gradebookroles), SQL_PARAMS_NAMED, 'grbr0');

        $relatedcontexts = get_related_contexts_string(get_context_instance(CONTEXT_COURSE, $this->course->id));

        if ($this->groupid) {
            $groupsql = "INNER JOIN {groups_members} gm ON gm.userid = u.id";
            $groupwheresql = "AND gm.groupid = :groupid";
            // $params contents: gradebookroles
            $params['groupid'] = $this->groupid;
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
                // user order MUST be the same in both queries,
                // must include the only unique user->id if not already present
                $ofields .= ", u.id AS usrt";
                $order   .= ", usrt ASC";
            }
        }

        // $params contents: gradebookroles and groupid (for $groupwheresql)
        $users_sql = "SELECT u.* $ofields
                        FROM {user} u
                             INNER JOIN {role_assignments} ra ON u.id = ra.userid
                             $groupsql
                       WHERE ra.roleid $gradebookroles_sql
                             AND ra.contextid $relatedcontexts
                             $groupwheresql
                    ORDER BY $order";

        $this->users_rs = $DB->get_recordset_sql($users_sql, $params);

        if (!empty($this->grade_items)) {
            $itemids = array_keys($this->grade_items);
            list($itemidsql, $grades_params) = $DB->get_in_or_equal($itemids, SQL_PARAMS_NAMED, 'items0');
            $params = array_merge($params, $grades_params);

            // $params contents: gradebookroles, groupid (for $groupwheresql) and itemids
            $grades_sql = "SELECT g.* $ofields
                             FROM {grade_grades} g
                                  INNER JOIN {user} u ON g.userid = u.id
                                  INNER JOIN {role_assignments} ra ON u.id = ra.userid
                                  $groupsql
                            WHERE ra.roleid $gradebookroles_sql
                                  AND ra.contextid $relatedcontexts
                                  $groupwheresql
                                  AND g.itemid $itemidsql
                         ORDER BY $order, g.itemid ASC";
            $this->grades_rs = $DB->get_recordset_sql($grades_sql, $params);
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

        if (!$this->users_rs->valid()) {
            if ($current = $this->_pop()) {
                // this is not good - user or grades updated between the two reads above :-(
            }

            return false; // no more users
        } else {
            $user = $this->users_rs->current();
            $this->users_rs->next();
        }

        // find grades of this user
        $grade_records = array();
        while (true) {
            if (!$current = $this->_pop()) {
                break; // no more grades
            }

            if (empty($current->userid)) {
                break;
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
                    $grades[$grade_item->id] =
                        new grade_grade(array('userid'=>$user->id, 'itemid'=>$grade_item->id), false);
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
            $this->users_rs->close();
            $this->users_rs = null;
        }
        if ($this->grades_rs) {
            $this->grades_rs->close();
            $this->grades_rs = null;
        }
        $this->gradestack = array();
    }


    /**
     * _push
     *
     * @param grade_grade $grade Grade object
     *
     * @return void
     */
    function _push($grade) {
        array_push($this->gradestack, $grade);
    }


    /**
     * _pop
     *
     * @return void
     */
    function _pop() {
        global $DB;
        if (empty($this->gradestack)) {
            if (!$this->grades_rs) {
                return null; // no grades present
            }

            if ($this->grades_rs->next()) {
                return null; // no more grades
            }

            return $this->grades_rs->current();
        } else {
            return array_pop($this->gradestack);
        }
    }
}

/**
 * Print a selection popup form of the graded users in a course.
 *
 * @param int    $course id of the course
 * @param string $actionpage The page receiving the data from the popoup form
 * @param int    $userid   id of the currently selected user (or 'all' if they are all selected)
 * @param int    $groupid id of requested group, 0 means all
 * @param int    $includeall bool include all option
 * @param bool   $return If true, will return the HTML, otherwise, will print directly
 * @return null
 */
function print_graded_users_selector($course, $actionpage, $userid=0, $groupid=0, $includeall=true, $return=false) {
    global $CFG, $USER, $OUTPUT;

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
    $select = new single_select(new moodle_url('/grade/'.$actionpage), 'userid', $menu, $userid);
    $select->label = $label;
    $select->formid = 'choosegradeuser';
    return $OUTPUT->render($select);
}

/**
 * Print grading plugin selection popup form.
 *
 * @param array   $plugin_info An array of plugins containing information for the selector
 * @param boolean $return return as string
 *
 * @return nothing or string if $return true
 */
function print_grade_plugin_selector($plugin_info, $return=false) {
    global $CFG, $OUTPUT, $PAGE;

    $menu = array();
    $count = 0;
    $active = '';

    foreach ($plugin_info as $plugin_type => $plugins) {
        if ($plugin_type == 'strings') {
            continue;
        }

        $first_plugin = reset($plugins);

        $menu[$first_plugin->link.'&'] = '--'.$plugin_info['strings'][$plugin_type];

        if (empty($plugins->id)) {
            foreach ($plugins as $plugin) {
                $menu[$plugin->link] = $plugin->string;
                $count++;
            }
        }
    }

    // finally print/return the popup form
    if ($count > 1) {
        $select = html_select::make_popup_form('', '', $menu, 'choosepluginreport', '');
        $select->override_option_values($menu);

        if ($return) {
            return $OUTPUT->select($select);
        } else {
            echo $OUTPUT->select($select);
        }
    } else {
        // only one option - no plugin selector needed
        return '';
    }
}

/**
 * Print grading plugin selection tab-based navigation.
 *
 * @param string  $active_type type of plugin on current page - import, export, report or edit
 * @param string  $active_plugin active plugin type - grader, user, cvs, ...
 * @param array   $plugin_info Array of plugins
 * @param boolean $return return as string
 *
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
        if (!empty($plugins->id)) {
            $string = $plugins->string;
            if (!empty($plugin_info[$active_type]->parent)) {
                $string = $plugin_info[$active_type]->parent->string;
            }

            $top_row[] = new tabobject($plugin_type, $plugins->link, $string);
            continue;
        }

        $first_plugin = reset($plugins);
        $url = $first_plugin->link;

        if ($plugin_type == 'report') {
            $url = $CFG->wwwroot.'/grade/report/index.php?id='.$COURSE->id;
        }

        $top_row[] = new tabobject($plugin_type, $url, $plugin_info['strings'][$plugin_type]);

        if ($active_type == $plugin_type) {
            foreach ($plugins as $plugin) {
                $bottom_row[] = new tabobject($plugin->id, $plugin->link, $plugin->string);
                if ($plugin->id == $active_plugin) {
                    $inactive = array($plugin->id);
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

/**
 * grade_get_plugin_info
 *
 * @param int    $courseid The course id
 * @param string $active_type type of plugin on current page - import, export, report or edit
 * @param string $active_plugin active plugin type - grader, user, cvs, ...
 *
 * @return array
 */
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
        $plugin_info['settings']['course'] =
                new grade_plugin_info('coursesettings', $url, get_string('course'));
        $count++;
    }


    // report plugins with its special structure

    // Get all installed reports
    if ($reports = get_plugin_list('gradereport')) {

        // Remove ones we can't see
        foreach ($reports as $plugin => $unused) {
            if (!has_capability('gradereport/'.$plugin.':view', $context)) {
                unset($reports[$plugin]);
            }
        }
    }

    $reportnames = array();

    if (!empty($reports)) {
        foreach ($reports as $plugin => $plugindir) {
            $pluginstr = get_string('modulename', 'gradereport_'.$plugin);
            $url = $url_prefix.'report/'.$plugin.'/index.php?id='.$courseid;
            if ($active_type == 'report' and $active_plugin == $plugin ) {
                $active = $url;
            }
            $reportnames[$plugin] = new grade_plugin_info($plugin, $url, $pluginstr);

            // Add link to preferences tab if such a page exists
            if (file_exists($plugindir.'/preferences.php')) {
                $pref_url = $url_prefix.'report/'.$plugin.'/preferences.php?id='.$courseid;
                $plugin_info['preferences'][$plugin] = new grade_plugin_info($plugin, $pref_url, $pluginstr);
            }

            $count++;
        }
        asort($reportnames);
    }
    if (!empty($reportnames)) {
        $plugin_info['report']=$reportnames;
    }

    // editing scripts - not real plugins
    if (has_capability('moodle/grade:manage', $context)
      or has_capability('moodle/grade:manageletters', $context)
      or has_capability('moodle/course:managescales', $context)
      or has_capability('moodle/course:update', $context)) {

        if (has_capability('moodle/grade:manage', $context)) {
            $url = $url_prefix.'edit/tree/index.php?sesskey='.sesskey().
                    '&amp;showadvanced=0&amp;id='.$courseid;
            $url_adv = $url_prefix.'edit/tree/index.php?sesskey='.sesskey().
                    '&amp;showadvanced=1&amp;id='.$courseid;

            if ($active_type == 'edittree' and $active_plugin == 'simpleview') {
                $active = $url;
            } else if ($active_type == 'edittree' and $active_plugin == 'fullview') {
                $active = $url_adv;
            }

            $plugin_info['edittree'] = array();
            $plugin_info['edittree']['simpleview'] =
                    new grade_plugin_info('simpleview', $url, get_string('simpleview', 'grades'));
            $plugin_info['edittree']['fullview'] =
                    new grade_plugin_info('fullview', $url_adv, get_string('fullview', 'grades'));
            $count++;
        }

        if (has_capability('moodle/course:managescales', $context)) {
            $url = $url_prefix.'edit/scale/index.php?id='.$courseid;

            if ($active_type == 'scale' and is_null($active_plugin)) {
                $active = $url;
            }

            $plugin_info['scale'] = array();

            if ($active_type == 'scale' and $active_plugin == 'edit') {
                $edit_url = $url_prefix.'edit/scale/edit.php?courseid='.$courseid.
                        '&amp;id='.optional_param('id', 0, PARAM_INT);
                $active = $edit_url;
                $parent = new grade_plugin_info('scale', $url, get_string('scales'));
                $plugin_info['scale']['view'] =
                        new grade_plugin_info('edit', $edit_url, get_string('edit'), $parent);
            } else {
                $plugin_info['scale']['view'] =
                        new grade_plugin_info('scale', $url, get_string('view'));
            }

            $count++;
        }

        if (!empty($CFG->enableoutcomes) && (has_capability('moodle/grade:manage', $context) or
                                             has_capability('moodle/course:update', $context))) {

            $url_course = $url_prefix.'edit/outcome/course.php?id='.$courseid;
            $url_edit = $url_prefix.'edit/outcome/index.php?id='.$courseid;

            $plugin_info['outcome'] = array();

            if (has_capability('moodle/course:update', $context)) {  // Default to course assignment
                $plugin_info['outcome']['course'] =
                        new grade_plugin_info('course', $url_course, get_string('outcomescourse', 'grades'));
                $plugin_info['outcome']['edit'] =
                        new grade_plugin_info('edit', $url_edit, get_string('editoutcomes', 'grades'));
            } else {
                $plugin_info['outcome'] =
                        new grade_plugin_info('edit', $url_course, get_string('outcomescourse', 'grades'));
            }

            if ($active_type == 'outcome' and is_null($active_plugin)) {
                $active = $url_edit;
            } else if ($active_type == 'outcome' and $active_plugin == 'course' ) {
                $active = $url_course;
            } else if ($active_type == 'outcome' and $active_plugin == 'edit' ) {
                $active = $url_edit;
            } else if ($active_type == 'outcome' and $active_plugin == 'import') {
                $plugin_info['outcome']['import'] =
                        new grade_plugin_info('import', null, get_string('importoutcomes', 'grades'));
            }

            $count++;
        }

        if (has_capability('moodle/grade:manage', $context) or
                    has_capability('moodle/grade:manageletters', $context)) {
            $course_context = get_context_instance(CONTEXT_COURSE, $courseid);
            $url = $url_prefix.'edit/letter/index.php?id='.$courseid;
            $url_edit = $url_prefix.'edit/letter/edit.php?id='.$course_context->id;

            if ($active_type == 'letter' and $active_plugin == 'view' ) {
                $active = $url;
            } else if ($active_type == 'letter' and $active_plugin == 'edit' ) {
                $active = $url_edit;
            }

            $plugin_info['letter'] = array();
            $plugin_info['letter']['view'] = new grade_plugin_info('view', $url, get_string('view'));
            $plugin_info['letter']['edit'] = new grade_plugin_info('edit', $url_edit, get_string('edit'));
            $count++;
        }
    }

    // standard import plugins
    if ($imports = get_plugin_list('gradeimport')) { // Get all installed import plugins
        foreach ($imports as $plugin => $plugindir) { // Remove ones we can't see
            if (!has_capability('gradeimport/'.$plugin.':view', $context)) {
                unset($imports[$plugin]);
            }
        }
    }
    $importnames = array();
    if (!empty($imports)) {
        foreach ($imports as $plugin => $plugindir) {
            $pluginstr = get_string('modulename', 'gradeimport_'.$plugin);
            $url = $url_prefix.'import/'.$plugin.'/index.php?id='.$courseid;
            if ($active_type == 'import' and $active_plugin == $plugin ) {
                $active = $url;
            }
            $importnames[$plugin] = new grade_plugin_info($plugin, $url, $pluginstr);
            $count++;
        }
        asort($importnames);
    }
    if (!empty($importnames)) {
        $plugin_info['import']=$importnames;
    }

    // standard export plugins
    if ($exports = get_plugin_list('gradeexport')) { // Get all installed export plugins
        foreach ($exports as $plugin => $plugindir) { // Remove ones we can't see
            if (!has_capability('gradeexport/'.$plugin.':view', $context)) {
                unset($exports[$plugin]);
            }
        }
    }
    $exportnames = array();
    if (!empty($exports)) {
        foreach ($exports as $plugin => $plugindir) {
            $pluginstr = get_string('modulename', 'gradeexport_'.$plugin);
            $url = $url_prefix.'export/'.$plugin.'/index.php?id='.$courseid;
            if ($active_type == 'export' and $active_plugin == $plugin ) {
                $active = $url;
            }
            $exportnames[$plugin] = new grade_plugin_info($plugin, $url, $pluginstr);
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
        $plugin_info['export']['keymanager'] =
                new grade_plugin_info('keymanager', $keymanager_url, get_string('keymanager', 'grades'));
        if ($active_type == 'export' and $active_plugin == 'keymanager' ) {
            $active = $keymanager_url;
        }
        $count++;

        $keymanager_url = $url_prefix.'import/keymanager.php?id='.$courseid;
        $plugin_info['import']['keymanager'] =
                new grade_plugin_info('keymanager', $keymanager_url, get_string('keymanager', 'grades'));
        if ($active_type == 'import' and $active_plugin == 'keymanager' ) {
            $active = $keymanager_url;
        }
        $count++;
    }


    foreach ($plugin_info as $plugin_type => $plugins) {
        if (!empty($plugins->id) && $active_plugin == $plugins->id) {
            $plugin_info['strings']['active_plugin_str'] = $plugins->string;
            break;
        }
        foreach ($plugins as $plugin) {
            if (is_a($plugin, 'grade_plugin_info')) {
                if ($active_plugin == $plugin->id) {
                    $plugin_info['strings']['active_plugin_str'] = $plugin->string;
                }
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
 * A simple class containing info about grade plugins.
 * Can be subclassed for special rules
 *
 * @package moodlecore
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_plugin_info {
    /**
     * A unique id for this plugin
     *
     * @var mixed
     */
    public $id;
    /**
     * A URL to access this plugin
     *
     * @var mixed
     */
    public $link;
    /**
     * The name of this plugin
     *
     * @var mixed
     */
    public $string;
    /**
     * Another grade_plugin_info object, parent of the current one
     *
     * @var mixed
     */
    public $parent;

    /**
     * Constructor
     *
     * @param int $id A unique id for this plugin
     * @param string $link A URL to access this plugin
     * @param string $string The name of this plugin
     * @param object $parent Another grade_plugin_info object, parent of the current one
     *
     * @return void
     */
    public function __construct($id, $link, $string, $parent=null) {
        $this->id = $id;
        $this->link = $link;
        $this->string = $string;
        $this->parent = $parent;
    }
}

/**
 * Prints the page headers, breadcrumb trail, page heading, (optional) dropdown navigation menu and
 * (optional) navigation tabs for any gradebook page. All gradebook pages MUST use these functions
 * in favour of the usual print_header(), print_header_simple(), print_heading() etc.
 * !IMPORTANT! Use of tabs.php file in gradebook pages is forbidden unless tabs are switched off at
 * the site level for the gradebook ($CFG->grade_navmethod = GRADE_NAVMETHOD_DROPDOWN).
 *
 * @param int     $courseid Course id
 * @param string  $active_type The type of the current page (report, settings,
 *                             import, export, scales, outcomes, letters)
 * @param string  $active_plugin The plugin of the current page (grader, fullview etc...)
 * @param string  $heading The heading of the page. Tries to guess if none is given
 * @param boolean $return Whether to return (true) or echo (false) the HTML generated by this function
 * @param string  $bodytags Additional attributes that will be added to the <body> tag
 * @param string  $buttons Additional buttons to display on the page
 *
 * @return string HTML code or nothing if $return == false
 */
function print_grade_page_head($courseid, $active_type, $active_plugin=null,
                               $heading = false, $return=false,
                               $buttons=false) {
    global $CFG, $COURSE, $OUTPUT, $PAGE;
    $strgrades = get_string('grades');
    $plugin_info = grade_get_plugin_info($courseid, $active_type, $active_plugin);

    // Determine the string of the active plugin
    $stractive_plugin = ($active_plugin) ? $plugin_info['strings']['active_plugin_str'] : $heading;
    $stractive_type = $plugin_info['strings'][$active_type];

    $first_link = '';

    if ($active_type == 'settings' && $active_plugin != 'coursesettings') {
        $first_link = $plugin_info['report'][$active_plugin]->link;
    } else if ($active_type != 'report') {
        $first_link = $CFG->wwwroot.'/grade/index.php?id='.$COURSE->id;
    }


    $PAGE->navbar->add($strgrades, $first_link);

    $active_type_link = '';

    if (!empty($plugin_info[$active_type]->link) && $plugin_info[$active_type]->link != qualified_me()) {
        $active_type_link = $plugin_info[$active_type]->link;
    }

    if (!empty($plugin_info[$active_type]->parent->link)) {
        $active_type_link = $plugin_info[$active_type]->parent->link;
        $PAGE->navbar->add($stractive_type, $active_type_link);
    }

    if (empty($plugin_info[$active_type]->id)) {
        $PAGE->navbar->add($stractive_type, $active_type_link);
    }

    $PAGE->navbar->add($stractive_plugin);

    $title = ': ' . $stractive_plugin;
    if (empty($plugin_info[$active_type]->id) || !empty($plugin_info[$active_type]->parent)) {
        $title = ': ' . $stractive_type . ': ' . $stractive_plugin;
    }

    $PAGE->set_title($strgrades . ': ' . $stractive_type);
    $PAGE->set_heading($title);
    $PAGE->set_button($buttons);
    $returnval = $OUTPUT->header();
    if (!$return) {
        echo $returnval;
    }

    // Guess heading if not given explicitly
    if (!$heading) {
        $heading = $stractive_plugin;
    }

    if ($CFG->grade_navmethod == GRADE_NAVMETHOD_COMBO || $CFG->grade_navmethod == GRADE_NAVMETHOD_DROPDOWN) {
        $returnval .= print_grade_plugin_selector($plugin_info, $return);
    }
    $returnval .= $OUTPUT->heading($heading);

    if ($CFG->grade_navmethod == GRADE_NAVMETHOD_COMBO || $CFG->grade_navmethod == GRADE_NAVMETHOD_TABS) {
        $returnval .= grade_print_tabs($active_type, $active_plugin, $plugin_info, $return);
    }

    if ($return) {
        return $returnval;
    }
}

/**
 * Utility class used for return tracking when using edit and other forms in grade plugins
 *
 * @package moodlecore
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_plugin_return {
    public $type;
    public $plugin;
    public $courseid;
    public $userid;
    public $page;

    /**
     * Constructor
     *
     * @param array $params - associative array with return parameters, if null parameter are taken from _GET or _POST
     */
    public function grade_plugin_return($params = null) {
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
    public function get_options() {
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
     *
     * @param string $default default url when params not set
     * @param array  $extras Extra URL parameters
     *
     * @return string url
     */
    public function get_return_url($default, $extras=null) {
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
            foreach ($extras as $key=>$value) {
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
    public function get_form_fields() {
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
     *
     * @param object &$mform moodle form object
     *
     * @return void
     */
    public function add_mform_elements(&$mform) {
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
     *
     * @param moodle_url $url A URL
     *
     * @return string $url with erturn tracking params
     */
    public function add_url_params(moodle_url $url) {
        if (empty($this->type)) {
            return $url;
        }

        $url->param('gpr_type', $this->type);

        if (!empty($this->plugin)) {
            $url->param('gpr_plugin', $this->plugin);
        }

        if (!empty($this->courseid)) {
            $url->param('gpr_courseid' ,$this->courseid);
        }

        if (!empty($this->userid)) {
            $url->param('gpr_userid', $this->userid);
        }

        if (!empty($this->page)) {
            $url->param('gpr_page', $this->page);
        }

        return $url;
    }
}

/**
 * Function central to gradebook for building and printing the navigation (breadcrumb trail).
 *
 * @param string $path The path of the calling script (using __FILE__?)
 * @param string $pagename The language string to use as the last part of the navigation (non-link)
 * @param mixed  $id Either a plain integer (assuming the key is 'id') or
 *                   an array of keys and values (e.g courseid => $courseid, itemid...)
 *
 * @return string
 */
function grade_build_nav($path, $pagename=null, $id=null) {
    global $CFG, $COURSE, $PAGE;

    $strgrades = get_string('grades', 'grades');

    // Parse the path and build navlinks from its elements
    $dirroot_length = strlen($CFG->dirroot) + 1; // Add 1 for the first slash
    $path = substr($path, $dirroot_length);
    $path = str_replace('\\', '/', $path);

    $path_elements = explode('/', $path);

    $path_elements_count = count($path_elements);

    // First link is always 'grade'
    $PAGE->navbar->add($strgrades, new moodle_url('/grade/index.php', array('id'=>$COURSE->id)));

    $link = null;
    $numberofelements = 3;

    // Prepare URL params string
    $linkparams = array();
    if (!is_null($id)) {
        if (is_array($id)) {
            foreach ($id as $idkey => $idvalue) {
                $linkparams[$idkey] = $idvalue;
            }
        } else {
            $linkparams['id'] = $id;
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
                $link = new moodle_url('/grade/report/index.php', $linkparams);
            }

            if ($path_elements[2] == 'grader') {
                $numberofelements = 4;
            }
            break;

        default:
            // If this element isn't among the ones already listed above, it isn't supported, throw an error.
            debugging("grade_build_nav() doesn't support ". $path_elements[1] .
                    " as the second path element after 'grade'.");
            return false;
    }
    $PAGE->navbar->add(get_string($path_elements[1], 'grades'), $link);

    // Third level links
    if (empty($pagename)) {
        $pagename = get_string($path_elements[2], 'grades');
    }

    switch ($numberofelements) {
        case 3:
            $PAGE->navbar->add($pagename, $link);
            break;
        case 4:
            if ($path_elements[2] == 'grader' AND $path_elements[3] != 'index.php') {
                $PAGE->navbar->add(get_string('modulename', 'gradereport_grader'), new moodle_url('/grade/report/grader/index.php', $linkparams));
            }
            $PAGE->navbar->add($pagename);
            break;
    }

    return '';
}

/**
 * General structure representing grade items in course
 *
 * @package moodlecore
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_structure {
    public $context;

    public $courseid;

    /**
     * 1D array of grade items only
     */
    public $items;

    /**
     * Returns icon of element
     *
     * @param array &$element An array representing an element in the grade_tree
     * @param bool  $spacerifnone return spacer if no icon found
     *
     * @return string icon or spacer
     */
    public function get_element_icon(&$element, $spacerifnone=false) {
        global $CFG, $OUTPUT;

        switch ($element['type']) {
            case 'item':
            case 'courseitem':
            case 'categoryitem':
                $is_course   = $element['object']->is_course_item();
                $is_category = $element['object']->is_category_item();
                $is_scale    = $element['object']->gradetype == GRADE_TYPE_SCALE;
                $is_value    = $element['object']->gradetype == GRADE_TYPE_VALUE;

                if ($element['object']->is_calculated()) {
                    $strcalc = get_string('calculatedgrade', 'grades');
                    return '<img src="'.$OUTPUT->pix_url('i/calc') . '" class="icon itemicon" title="'.
                            s($strcalc).'" alt="'.s($strcalc).'"/>';

                } else if (($is_course or $is_category) and ($is_scale or $is_value)) {
                    if ($category = $element['object']->get_item_category()) {
                        switch ($category->aggregation) {
                            case GRADE_AGGREGATE_MEAN:
                            case GRADE_AGGREGATE_MEDIAN:
                            case GRADE_AGGREGATE_WEIGHTED_MEAN:
                            case GRADE_AGGREGATE_WEIGHTED_MEAN2:
                            case GRADE_AGGREGATE_EXTRACREDIT_MEAN:
                                $stragg = get_string('aggregation', 'grades');
                                return '<img src="'.$OUTPUT->pix_url('i/agg_mean') . '" ' .
                                        'class="icon itemicon" title="'.s($stragg).'" alt="'.s($stragg).'"/>';
                            case GRADE_AGGREGATE_SUM:
                                $stragg = get_string('aggregation', 'grades');
                                return '<img src="'.$OUTPUT->pix_url('i/agg_sum') . '" ' .
                                        'class="icon itemicon" title="'.s($stragg).'" alt="'.s($stragg).'"/>';
                        }
                    }

                } else if ($element['object']->itemtype == 'mod') {
                    $strmodname = get_string('modulename', $element['object']->itemmodule);
                    return '<img src="'.$OUTPUT->pix_url('icon',
                            $element['object']->itemmodule) . '" ' .
                            'class="icon itemicon" title="' .s($strmodname).
                            '" alt="' .s($strmodname).'"/>';

                } else if ($element['object']->itemtype == 'manual') {
                    if ($element['object']->is_outcome_item()) {
                        $stroutcome = get_string('outcome', 'grades');
                        return '<img src="'.$OUTPUT->pix_url('i/outcomes') . '" ' .
                                'class="icon itemicon" title="'.s($stroutcome).
                                '" alt="'.s($stroutcome).'"/>';
                    } else {
                        $strmanual = get_string('manualitem', 'grades');
                        return '<img src="'.$OUTPUT->pix_url('t/manual_item') . '" '.
                                'class="icon itemicon" title="'.s($strmanual).
                                '" alt="'.s($strmanual).'"/>';
                    }
                }
                break;

            case 'category':
                $strcat = get_string('category', 'grades');
                return '<img src="'.$OUTPUT->pix_url('f/folder') . '" class="icon itemicon" ' .
                        'title="'.s($strcat).'" alt="'.s($strcat).'" />';
        }

        if ($spacerifnone) {
            return '<img src="'.$CFG->wwwroot.'/pix/spacer.gif" class="icon itemicon" alt=""/>';
        } else {
            return '';
        }
    }

    /**
     * Returns name of element optionally with icon and link
     *
     * @param array &$element An array representing an element in the grade_tree
     * @param bool  $withlink Whether or not this header has a link
     * @param bool  $icon Whether or not to display an icon with this header
     * @param bool  $spacerifnone return spacer if no icon found
     *
     * @return string header
     */
    public function get_element_header(&$element, $withlink=false, $icon=true, $spacerifnone=false) {
        global $CFG;

        $header = '';

        if ($icon) {
            $header .= $this->get_element_icon($element, $spacerifnone);
        }

        $header .= $element['object']->get_name();

        if ($element['type'] != 'item' and $element['type'] != 'categoryitem' and
            $element['type'] != 'courseitem') {
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
     *
     * @param grade_grade $grade_grade A grade_grade object
     *
     * @return string eid
     */
    public function get_grade_eid($grade_grade) {
        if (empty($grade_grade->id)) {
            return 'n'.$grade_grade->itemid.'u'.$grade_grade->userid;
        } else {
            return 'g'.$grade_grade->id;
        }
    }

    /**
     * Returns the grade_item eid
     * @param grade_item $grade_item A grade_item object
     * @return string eid
     */
    public function get_item_eid($grade_item) {
        return 'i'.$grade_item->id;
    }

    /**
     * Given a grade_tree element, returns an array of parameters
     * used to build an icon for that element.
     *
     * @param array $element An array representing an element in the grade_tree
     *
     * @return array
     */
    public function get_params_for_iconstr($element) {
        $strparams = new stdClass();
        $strparams->category = '';
        $strparams->itemname = '';
        $strparams->itemmodule = '';

        if (!method_exists($element['object'], 'get_name')) {
            return $strparams;
        }

        $strparams->itemname = $element['object']->get_name();

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
     *
     * @param array  $element An array representing an element in the grade_tree
     * @param object $gpr A grade_plugin_return object
     *
     * @return string
     */
    public function get_edit_icon($element, $gpr) {
        global $CFG, $OUTPUT;

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

        $object = $element['object'];

        switch ($element['type']) {
            case 'item':
            case 'categoryitem':
            case 'courseitem':
                $stredit = get_string('editverbose', 'grades', $strparams);
                if (empty($object->outcomeid) || empty($CFG->enableoutcomes)) {
                    $url = new moodle_url('/grade/edit/tree/item.php',
                            array('courseid' => $this->courseid, 'id' => $object->id));
                } else {
                    $url = new moodle_url('/grade/edit/tree/outcomeitem.php',
                            array('courseid' => $this->courseid, 'id' => $object->id));
                }
                break;

            case 'category':
                $stredit = get_string('editverbose', 'grades', $strparams);
                $url = new moodle_url('/grade/edit/tree/category.php',
                        array('courseid' => $this->courseid, 'id' => $object->id));
                break;

            case 'grade':
                $stredit = $streditgrade;
                if (empty($object->id)) {
                    $url = new moodle_url('/grade/edit/tree/grade.php',
                            array('courseid' => $this->courseid, 'itemid' => $object->itemid, 'userid' => $object->userid));
                } else {
                    $url = new moodle_url('/grade/edit/tree/grade.php',
                            array('courseid' => $this->courseid, 'id' => $object->id));
                }
                if (!empty($object->feedback)) {
                    $feedback = addslashes_js(trim(format_string($object->feedback, $object->feedbackformat)));
                }
                break;

            default:
                $url = null;
        }

        if ($url) {
            return $OUTPUT->action_icon($gpr->add_url_params($url), $stredit, 't/edit', array('class'=>'iconsmall'));

        } else {
            return '';
        }
    }

    /**
     * Return hiding icon for give element
     *
     * @param array  $element An array representing an element in the grade_tree
     * @param object $gpr A grade_plugin_return object
     *
     * @return string
     */
    public function get_hiding_icon($element, $gpr) {
        global $CFG, $OUTPUT;

        if (!has_capability('moodle/grade:manage', $this->context) and
            !has_capability('moodle/grade:hide', $this->context)) {
            return '';
        }

        $strparams = $this->get_params_for_iconstr($element);
        $strshow = get_string('showverbose', 'grades', $strparams);
        $strhide = get_string('hideverbose', 'grades', $strparams);

        $url = new moodle_url('/grade/edit/tree/action.php', array('id' => $this->courseid, 'sesskey' => sesskey(), 'eid' => $element['eid']));
        $url = $gpr->add_url_params($url);

        if ($element['object']->is_hidden()) {
            $type = 'show';
            $tooltip = $strshow;

            // Change the icon and add a tooltip showing the date
            if ($element['type'] != 'category' and $element['object']->get_hidden() > 1) {
                $type = 'hiddenuntil';
                $tooltip = get_string('hiddenuntildate', 'grades',
                        userdate($element['object']->get_hidden()));
            }

            $url->param('action', 'show');

            $hideicon = $OUTPUT->action_icon($url, $tooltip, 't/'.$type, array('alt'=>$strshow, 'class'=>'iconsmall'));

        } else {
            $url->param('action', 'hide');
            $hideicon = $OUTPUT->action_icon($url, $strhide, 't/hide', array('class'=>'iconsmall'));
        }

        return $hideicon;
    }

    /**
     * Return locking icon for given element
     *
     * @param array  $element An array representing an element in the grade_tree
     * @param object $gpr A grade_plugin_return object
     *
     * @return string
     */
    public function get_locking_icon($element, $gpr) {
        global $CFG, $OUTPUT;

        $strparams = $this->get_params_for_iconstr($element);
        $strunlock = get_string('unlockverbose', 'grades', $strparams);
        $strlock = get_string('lockverbose', 'grades', $strparams);

        $url = new moodle_url('/grade/edit/tree/action.php', array('id' => $this->courseid, 'sesskey' => sesskey(), 'eid' => $element['eid']));
        $url = $gpr->add_url_params($url);

        // Don't allow an unlocking action for a grade whose grade item is locked: just print a state icon
        if ($element['type'] == 'grade' && $element['object']->grade_item->is_locked()) {
            $strparamobj = new stdClass();
            $strparamobj->itemname = $element['object']->grade_item->itemname;
            $strnonunlockable = get_string('nonunlockableverbose', 'grades', $strparamobj);

            $action = $OUTPUT->image('t/unlock_gray', array('alt'=>$strnonunlockable, 'title'=>$strnonunlockable, 'class'=>'iconsmall'));

        } else if ($element['object']->is_locked()) {
            $type = 'unlock';
            $tooltip = $strunlock;

            // Change the icon and add a tooltip showing the date
            if ($element['type'] != 'category' and $element['object']->get_locktime() > 1) {
                $type = 'locktime';
                $tooltip = get_string('locktimedate', 'grades',
                        userdate($element['object']->get_locktime()));
            }

            if (!has_capability('moodle/grade:manage', $this->context) and !has_capability('moodle/grade:unlock', $this->context)) {
                $action = '';
            } else {
                $url->param('action', 'unlock');
                $action = $OUTPUT->action_icon($url, $tooltip, 't/'.$type, array('alt'=>$strunlock, 'class'=>'smallicon'));
            }

        } else {
            if (!has_capability('moodle/grade:manage', $this->context) and !has_capability('moodle/grade:lock', $this->context)) {
                $action = '';
            } else {
                $url->param('action', 'lock');
                $action = $OUTPUT->action_icon($url, $strlock, 't/lock', array('class'=>'smallicon'));
            }
        }

        return $action;
    }

    /**
     * Return calculation icon for given element
     *
     * @param array  $element An array representing an element in the grade_tree
     * @param object $gpr A grade_plugin_return object
     *
     * @return string
     */
    public function get_calculation_icon($element, $gpr) {
        global $CFG, $OUTPUT;
        if (!has_capability('moodle/grade:manage', $this->context)) {
            return '';
        }

        $type   = $element['type'];
        $object = $element['object'];

        if ($type == 'item' or $type == 'courseitem' or $type == 'categoryitem') {
            $strparams = $this->get_params_for_iconstr($element);
            $streditcalculation = get_string('editcalculationverbose', 'grades', $strparams);

            $is_scale = $object->gradetype == GRADE_TYPE_SCALE;
            $is_value = $object->gradetype == GRADE_TYPE_VALUE;

            // show calculation icon only when calculation possible
            if (!$object->is_external_item() and ($is_scale or $is_value)) {
                if ($object->is_calculated()) {
                    $icon = 't/calc';
                } else {
                    $icon = 't/calc_off';
                }

                $url = new moodle_url('/grade/edit/tree/calculation.php', array('courseid' => $this->courseid, 'id' => $object->id));
                $url = $gpr->add_url_params($url);
                return $OUTPUT->action_icon($url, $streditcalculation, $icon, array('class'=>'smallicon')) . "\n";
            }
        }

        return '';
    }
}

/**
 * Flat structure similar to grade tree.
 *
 * @uses grade_structure
 * @package moodlecore
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_seq extends grade_structure {

    /**
     * 1D array of elements
     */
    public $elements;

    /**
     * Constructor, retrieves and stores array of all grade_category and grade_item
     * objects for the given courseid. Full objects are instantiated. Ordering sequence is fixed if needed.
     *
     * @param int  $courseid The course id
     * @param bool $category_grade_last category grade item is the last child
     * @param bool $nooutcomes Whether or not outcomes should be included
     */
    public function grade_seq($courseid, $category_grade_last=false, $nooutcomes=false) {
        global $USER, $CFG;

        $this->courseid   = $courseid;
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
     *
     * @param array &$element The seed of the recursion
     * @param bool $category_grade_last category grade item is the last child
     * @param bool $nooutcomes Whether or not outcomes should be included
     *
     * @return array
     */
    public function flatten(&$element, $category_grade_last, $nooutcomes) {
        if (empty($element['children'])) {
            return array();
        }
        $children = array();

        foreach ($element['children'] as $sortorder=>$unused) {
            if ($nooutcomes and $element['type'] != 'category' and
                $element['children'][$sortorder]['object']->is_outcome_item()) {
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
     *
     * @param int $eid Gradetree Element ID
     *
     * @return object element
     */
    public function locate_element($eid) {
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
            $id = (int) substr($eid, 1);
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
 *
 * @uses grade_structure
 * @package moodlecore
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_tree extends grade_structure {

    /**
     * The basic representation of the tree as a hierarchical, 3-tiered array.
     * @var object $top_element
     */
    public $top_element;

    /**
     * 2D array of grade items and categories
     * @var array $levels
     */
    public $levels;

    /**
     * Grade items
     * @var array $items
     */
    public $items;

    /**
     * Constructor, retrieves and stores a hierarchical array of all grade_category and grade_item
     * objects for the given courseid. Full objects are instantiated. Ordering sequence is fixed if needed.
     *
     * @param int   $courseid The Course ID
     * @param bool  $fillers include fillers and colspans, make the levels var "rectangular"
     * @param bool  $category_grade_last category grade item is the last child
     * @param array $collapsed array of collapsed categories
     * @param bool  $nooutcomes Whether or not outcomes should be included
     */
    public function grade_tree($courseid, $fillers=true, $category_grade_last=false,
                               $collapsed=null, $nooutcomes=false) {
        global $USER, $CFG;

        $this->courseid   = $courseid;
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
     *
     * @param array &$element The seed of the recursion
     * @param array $collapsed array of collapsed categories
     *
     * @return void
     */
    public function category_collapse(&$element, $collapsed) {
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
     *
     * @param array &$element The seed of the recursion
     *
     * @return void
     */
    public function no_outcomes(&$element) {
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
     *
     * @param array &$element The seed of the recursion
     *
     * @return void
     */
    public function category_grade_last(&$element) {
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
     *
     * @param array &$levels The levels of the grade tree through which to recurse
     * @param array &$element The seed of the recursion
     * @param int   $depth How deep are we?
     * @return void
     */
    public function fill_levels(&$levels, &$element, $depth) {
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
     *
     * @param array &$element The seed of the recursion
     * @param int   $depth How deep are we?
     *
     * @return int
     */
    public function inject_fillers(&$element, $depth) {
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
                $element['children'][$chid] = array('object'=>'filler', 'type'=>$type,
                                                    'eid'=>'', 'depth'=>$element['object']->depth,
                                                    'children'=>array($oldchild));
            }
        }

        return $maxdepth;
    }

    /**
     * Static recursive helper - add colspan information into categories
     *
     * @param array &$element The seed of the recursion
     *
     * @return int
     */
    public function inject_colspans(&$element) {
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
     * @param int $eid Gradetree Element ID
     * @return object element
     */
    public function locate_element($eid) {
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
            $id = (int) substr($eid, 1);
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

    /**
     * Returns a well-formed XML representation of the grade-tree using recursion.
     *
     * @param array  $root The current element in the recursion. If null, starts at the top of the tree.
     * @param string $tabs The control character to use for tabs
     *
     * @return string $xml
     */
    public function exporttoxml($root=null, $tabs="\t") {
        $xml = null;
        $first = false;
        if (is_null($root)) {
            $root = $this->top_element;
            $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
            $xml .= "<gradetree>\n";
            $first = true;
        }

        $type = 'undefined';
        if (strpos($root['object']->table, 'grade_categories') !== false) {
            $type = 'category';
        } else if (strpos($root['object']->table, 'grade_items') !== false) {
            $type = 'item';
        } else if (strpos($root['object']->table, 'grade_outcomes') !== false) {
            $type = 'outcome';
        }

        $xml .= "$tabs<element type=\"$type\">\n";
        foreach ($root['object'] as $var => $value) {
            if (!is_object($value) && !is_array($value) && !empty($value)) {
                $xml .= "$tabs\t<$var>$value</$var>\n";
            }
        }

        if (!empty($root['children'])) {
            $xml .= "$tabs\t<children>\n";
            foreach ($root['children'] as $sortorder => $child) {
                $xml .= $this->exportToXML($child, $tabs."\t\t");
            }
            $xml .= "$tabs\t</children>\n";
        }

        $xml .= "$tabs</element>\n";

        if ($first) {
            $xml .= "</gradetree>";
        }

        return $xml;
    }

    /**
     * Returns a JSON representation of the grade-tree using recursion.
     *
     * @param array $root The current element in the recursion. If null, starts at the top of the tree.
     * @param string $tabs Tab characters used to indent the string nicely for humans to enjoy
     *
     * @return string
     */
    public function exporttojson($root=null, $tabs="\t") {
        $json = null;
        $first = false;
        if (is_null($root)) {
            $root = $this->top_element;
            $first = true;
        }

        $name = '';


        if (strpos($root['object']->table, 'grade_categories') !== false) {
            $name = $root['object']->fullname;
            if ($name == '?') {
                $name = $root['object']->get_name();
            }
        } else if (strpos($root['object']->table, 'grade_items') !== false) {
            $name = $root['object']->itemname;
        } else if (strpos($root['object']->table, 'grade_outcomes') !== false) {
            $name = $root['object']->itemname;
        }

        $json .= "$tabs {\n";
        $json .= "$tabs\t \"type\": \"{$root['type']}\",\n";
        $json .= "$tabs\t \"name\": \"$name\",\n";

        foreach ($root['object'] as $var => $value) {
            if (!is_object($value) && !is_array($value) && !empty($value)) {
                $json .= "$tabs\t \"$var\": \"$value\",\n";
            }
        }

        $json = substr($json, 0, strrpos($json, ','));

        if (!empty($root['children'])) {
            $json .= ",\n$tabs\t\"children\": [\n";
            foreach ($root['children'] as $sortorder => $child) {
                $json .= $this->exportToJSON($child, $tabs."\t\t");
            }
            $json = substr($json, 0, strrpos($json, ','));
            $json .= "\n$tabs\t]\n";
        }

        if ($first) {
            $json .= "\n}";
        } else {
            $json .= "\n$tabs},\n";
        }

        return $json;
    }

    /**
     * Returns the array of levels
     *
     * @return array
     */
    public function get_levels() {
        return $this->levels;
    }

    /**
     * Returns the array of grade items
     *
     * @return array
     */
    public function get_items() {
        return $this->items;
    }

    /**
     * Returns a specific Grade Item
     *
     * @param int $itemid The ID of the grade_item object
     *
     * @return grade_item
     */
    public function get_item($itemid) {
        if (array_key_exists($itemid, $this->items)) {
            return $this->items[$itemid];
        } else {
            return false;
        }
    }
}

/**
 * Local shortcut function for creating an edit/delete button for a grade_* object.
 * @param strong $type 'edit' or 'delete'
 * @param int $courseid The Course ID
 * @param grade_* $object The grade_* object
 * @return string html
 */
function grade_button($type, $courseid, $object) {
    global $CFG, $OUTPUT;
    if (preg_match('/grade_(.*)/', get_class($object), $matches)) {
        $objectidstring = $matches[1] . 'id';
    } else {
        throw new coding_exception('grade_button() only accepts grade_* objects as third parameter!');
    }

    $strdelete = get_string('delete');
    $stredit   = get_string('edit');

    if ($type == 'delete') {
        $url = new moodle_url('index.php', array('id' => $courseid, $objectidstring => $object->id, 'action' => 'delete', 'sesskey' => sesskey()));
    } else if ($type == 'edit') {
        $url = new moodle_url('edit.php', array('courseid' => $courseid, 'id' => $object->id));
    }

    return $OUTPUT->action_icon($url, ${'str'.$type}, 't/'.$type, array('class'=>'iconsmall'));

}
