<?php
// This file is part of the Checklist plugin for Moodle - http://moodle.org/
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
 * Stores all the functions for manipulating a checklist
 *
 * @author   David Smith <moodle@davosmith.co.uk>
 * @package  mod/checklist
 */

use mod_checklist\local\checklist_check;
use mod_checklist\local\checklist_comment;
use mod_checklist\local\checklist_item;
use mod_checklist\local\output_status;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot.'/mod/checklist/lib.php');

define("CHECKLIST_TEXT_INPUT_WIDTH", 45);
define("CHECKLIST_OPTIONAL_NO", 0);
define("CHECKLIST_OPTIONAL_YES", 1);
define("CHECKLIST_OPTIONAL_HEADING", 2);

define("CHECKLIST_HIDDEN_NO", 0);
define("CHECKLIST_HIDDEN_MANUAL", 1);
define("CHECKLIST_HIDDEN_BYMODULE", 2);

class checklist_class {
    protected $cm;
    protected $course;
    protected $checklist;
    protected $strchecklists;
    protected $strchecklist;
    protected $context;
    protected $userid;
    /** @var checklist_item[] */
    protected $items;
    /** @var checklist_item[] */
    protected $useritems;
    protected $useredit;
    protected $additemafter;
    protected $editdates;
    /** @var bool|int[] */
    protected $groupings;
    /** @var mod_checklist_renderer */
    protected $output;

    protected $canlinkcourses = null;

    /**
     * @param int|string $cmid optional
     * @param int $userid optional
     * @param object $checklist optional
     * @param object $cm optional
     * @param object $course optional
     */
    public function __construct($cmid = 'staticonly', $userid = 0, $checklist = null, $cm = null, $course = null) {
        global $COURSE, $DB, $CFG;

        if ($cmid == 'staticonly') {
            // Use static functions only!
            return;
        }

        $this->output = self::get_renderer();

        $this->userid = $userid;

        if ($cm) {
            $this->cm = $cm;
        } else {
            $this->cm = get_coursemodule_from_id('checklist', $cmid, 0, false, MUST_EXIST);
        }

        $this->context = context_module::instance($this->cm->id);

        if ($course) {
            $this->course = $course;
        } else if ($this->cm->course == $COURSE->id) {
            $this->course = $COURSE;
        } else {
            $this->course = $DB->get_record('course', array('id' => $this->cm->course), '*', MUST_EXIST);
        }
        checklist_comment::set_courseid($this->course->id);

        if ($checklist) {
            $this->checklist = $checklist;
        } else {
            $this->checklist = $DB->get_record('checklist', array('id' => $this->cm->instance), '*', MUST_EXIST);
        }

        if ($userid) {
            $this->groupings = self::get_user_groupings($userid, $this->course->id);
        } else {
            $this->groupings = false;
        }

        $this->strchecklist = get_string('modulename', 'checklist');
        $this->strchecklists = get_string('modulenameplural', 'checklist');
        $this->pagetitle = strip_tags($this->course->shortname.': '.$this->strchecklist.': '.
                                      format_string($this->checklist->name, true));

        $this->get_items();

        if ($this->checklist->autopopulate) {
            $this->update_items_from_course();
        }
    }

    /**
     * @return mod_checklist_renderer
     */
    private static function get_renderer() {
        global $PAGE;
        return $PAGE->get_renderer('mod_checklist');
    }

    /**
     * Is linking items to courses enabled on the site?
     * @return bool
     */
    protected function can_link_courses() {
        if ($this->canlinkcourses === null) {
            $this->canlinkcourses = (bool)get_config('mod_checklist', 'linkcourses');
        }
        return $this->canlinkcourses;
    }

    /**
     * Force checklist into 'edit dates' mode (really only needed by behat generator).
     * @param bool $edit
     */
    public function set_editing_dates($edit) {
        $this->editdates = (bool)$edit;
    }

    /**
     * Get an array of the items in a checklist
     *
     */
    protected function get_items() {
        global $DB;

        // Load all shared checklist items.
        $this->items = checklist_item::fetch_all(['checklist' => $this->checklist->id, 'userid' => 0], true);

        // Makes sure all items are numbered sequentially, starting at 1.
        $this->update_item_positions();

        // Load student's own checklist items.
        if ($this->userid && $this->canaddown()) {
            $this->useritems = checklist_item::fetch_all(['checklist' => $this->checklist->id, 'userid' => $this->userid], true);
        } else {
            $this->useritems = false;
        }

        // Load the currently checked-off items.
        if ($this->userid) {
            $sql = 'SELECT i.id, c.usertimestamp, c.teachermark, c.teachertimestamp, c.teacherid
                      FROM {checklist_item} i
                 LEFT JOIN {checklist_check} c ';
            $sql .= 'ON (i.id = c.item AND c.userid = ?) WHERE i.checklist = ? ';

            $checks = $DB->get_records_sql($sql, array($this->userid, $this->checklist->id));

            foreach ($checks as $check) {
                $id = $check->id;

                if (isset($this->items[$id])) {
                    $this->items[$id]->store_status($check->usertimestamp, $check->teachermark,
                                                    $check->teachertimestamp, $check->teacherid);
                } else if ($this->useritems && isset($this->useritems[$id])) {
                    $this->useritems[$id]->store_status($check->usertimestamp);
                    // User items never have a teacher mark to go with them.
                }
            }
        }
    }

    /**
     * Loop through all activities / resources in course and check they
     * are in the current checklist (in the right order)
     */
    protected function update_items_from_course() {
        global $DB, $CFG;

        $mods = get_fast_modinfo($this->course);

        $section = 1;
        $nextpos = 1;
        $changes = false;
        reset($this->items);

        $importsection = -1;
        if ($this->checklist->autopopulate == CHECKLIST_AUTOPOPULATE_SECTION) {
            foreach ($mods->get_sections() as $num => $section) {
                if (in_array($this->cm->id, $section)) {
                    $importsection = $num;
                    $section = $importsection;
                    break;
                }
            }
        }

        $groupmembersonly = ((int)$CFG->branch < 28) && (!empty($CFG->enablegroupmembersonly));

        $numsections = 1;
        $courseformat = course_get_format($this->course);
        if (method_exists($courseformat, 'get_last_section_number')) {
            $numsections = $courseformat->get_last_section_number();
        } else {
            $opts = $courseformat->get_format_options();
            if (isset($opts['numsections'])) {
                $numsections = $opts['numsections'];
            }
        }
        $sections = $mods->get_sections();
        while ($section <= $numsections || $section == $importsection) {
            if (!array_key_exists($section, $sections)) {
                $section++;
                continue;
            }

            if ($importsection >= 0 && $importsection != $section) {
                $section++; // Only importing the section with the checklist in it.
                continue;
            }

            $sectionheading = 0;
            while ($item = current($this->items)) {
                // Search from current position.
                if (($item->moduleid == $section) && ($item->itemoptional == CHECKLIST_OPTIONAL_HEADING)) {
                    $sectionheading = $item->id;
                    break;
                }
                next($this->items);
            }

            if (!$sectionheading) {
                // Search again from the start.
                foreach ($this->items as $item) {
                    if (($item->moduleid == $section) && ($item->itemoptional == CHECKLIST_OPTIONAL_HEADING)) {
                        $sectionheading = $item->id;
                        break;
                    }
                }
                reset($this->items);
            }

            $sectionname = $courseformat->get_section_name($section);
            if (trim($sectionname) == '') {
                $sectionname = get_string('section').' '.$section;
            }
            if (!$sectionheading) {
                $sectionheading = $this->additem($sectionname, 0, 0, false, false, $section, CHECKLIST_OPTIONAL_HEADING);
                reset($this->items);
            } else {
                if ($this->items[$sectionheading]->displaytext != $sectionname) {
                    $this->updateitem($sectionheading, $sectionname);
                }
            }

            if ($sectionheading) {
                $this->items[$sectionheading]->stillexists = true;

                if ($this->items[$sectionheading]->position < $nextpos) {
                    $this->moveitemto($sectionheading, $nextpos, true);
                    reset($this->items);
                }
                $nextpos = $this->items[$sectionheading]->position + 1;
            }

            foreach ($sections[$section] as $cmid) {
                if ($this->cm->id == $cmid) {
                    continue; // Do not include this checklist in the list of modules.
                }
                if ($mods->get_cm($cmid)->modname === 'label') {
                    continue; // Ignore any labels.
                }
                if (isset($mods->get_cm($cmid)->deletioninprogress) && $mods->get_cm($cmid)->deletioninprogress) {
                    continue; // M3.2 onwards - if cm is in the recycle bin, being deleted, then skip it.
                }

                $foundit = false;
                while ($item = current($this->items)) {
                    // Search list from current position (will usually be the next item).
                    if (($item->moduleid == $cmid) && ($item->itemoptional != CHECKLIST_OPTIONAL_HEADING)) {
                        $foundit = $item;
                        break;
                    }
                    if (($item->moduleid == 0) && ($item->position == $nextpos)) {
                        // Skip any items that are not linked to modules.
                        $nextpos++;
                    }
                    next($this->items);
                }
                if (!$foundit) {
                    // Search list again from the start (just in case).
                    foreach ($this->items as $item) {
                        if (($item->moduleid == $cmid) && ($item->itemoptional != CHECKLIST_OPTIONAL_HEADING)) {
                            $foundit = $item;
                            break;
                        }
                    }
                    reset($this->items);
                }
                $modname = $mods->get_cm($cmid)->name;
                if ($foundit) {
                    $item->stillexists = true;
                    if ($item->position != $nextpos) {
                        $this->moveitemto($item->id, $nextpos, true);
                        reset($this->items);
                    }
                    if ($item->displaytext != $modname) {
                        $this->updateitem($item->id, $modname);
                    }
                    if (($item->hidden == CHECKLIST_HIDDEN_BYMODULE) && $mods->get_cm($cmid)->visible) {
                        // Course module was hidden and now is not.
                        $item->hidden = CHECKLIST_HIDDEN_NO;
                        $upd = new stdClass;
                        $upd->id = $item->id;
                        $upd->hidden = $item->hidden;
                        $DB->update_record('checklist_item', $upd);
                        $changes = true;

                    } else if (($item->hidden == CHECKLIST_HIDDEN_NO) && !$mods->get_cm($cmid)->visible) {
                        // Course module is now hidden.
                        $item->hidden = CHECKLIST_HIDDEN_BYMODULE;
                        $upd = new stdClass;
                        $upd->id = $item->id;
                        $upd->hidden = $item->hidden;
                        $DB->update_record('checklist_item', $upd);
                        $changes = true;
                    }

                    $groupingid = $mods->get_cm($cmid)->groupingid;
                    if ($groupmembersonly && $groupingid && $mods->get_cm($cmid)->groupmembersonly) {
                        if ($item->grouping != $groupingid) {
                            $item->grouping = $groupingid;
                            $upd = new stdClass;
                            $upd->id = $item->id;
                            $upd->grouping = $groupingid;
                            $DB->update_record('checklist_item', $upd);
                            $changes = true;
                        }
                    } else {
                        if ($item->grouping) {
                            $item->grouping = 0;
                            $upd = new stdClass;
                            $upd->id = $item->id;
                            $upd->grouping = 0;
                            $DB->update_record('checklist_item', $upd);
                            $changes = true;
                        }
                    }
                } else {
                    $hidden = $mods->get_cm($cmid)->visible ? CHECKLIST_HIDDEN_NO : CHECKLIST_HIDDEN_BYMODULE;
                    $itemid = $this->additem($modname, 0, 0, $nextpos, false, $cmid, CHECKLIST_OPTIONAL_NO, $hidden);
                    $changes = true;
                    reset($this->items);
                    $this->items[$itemid]->stillexists = true;
                    $usegrouping = $groupmembersonly && $mods->get_cm($cmid)->groupmembersonly;
                    $this->items[$itemid]->grouping = $usegrouping ? $mods->get_cm($cmid)->groupingid : 0;
                    $item = $this->items[$itemid];
                }
                $item->set_modulelink(new moodle_url('/mod/'.$mods->get_cm($cmid)->modname.'/view.php', array('id' => $cmid)));
                $nextpos++;
            }

            $section++;
        }

        // Delete any items that are related to activities / resources that have been deleted.
        if ($this->items) {
            foreach ($this->items as $item) {
                if ($item->moduleid && !isset($item->stillexists)) {
                    $this->deleteitem($item->id, true);
                    $changes = true;
                }
            }
        }

        if ($changes) {
            $this->update_all_autoupdate_checks(true, false);
        }
    }

    protected function removeauto() {
        if ($this->checklist->autopopulate) {
            return; // Still automatically populating the checklist, so don't remove the items.
        }

        if (!$this->canedit()) {
            return;
        }

        if ($this->items) {
            foreach ($this->items as $item) {
                if ($item->moduleid) {
                    $this->deleteitem($item->id);
                }
            }
        }
    }

    /**
     * Check all items are numbered sequentially from 1
     * then, move any items between $start and $end
     * the number of places indicated by $move
     *
     * @param int $move (optional) - how far to offset the current positions
     * @param int $start (optional) - where to start offsetting positions
     * @param bool $end (optional) - where to stop offsetting positions
     */
    protected function update_item_positions($move = 0, $start = 1, $end = false) {
        $pos = 1;

        if (!$this->items) {
            return;
        }
        foreach ($this->items as $item) {
            if ($pos == $start) {
                $pos += $move;
                $start = -1;
            }
            if ($item->position != $pos) {
                $oldpos = $item->position;
                $item->position = $pos;
                $item->update();

                if ($oldpos == $end) {
                    break;
                }
            }
            $pos++;
        }
    }

    /**
     * @param int $position
     * @return bool|object
     */
    protected function get_item_at_position($position) {
        if (!$this->items) {
            return false;
        }
        foreach ($this->items as $item) {
            if ($item->position == $position) {
                return $item;
            }
        }
        return false;
    }

    protected function canupdateown() {
        global $USER;
        return (!$this->userid || ($this->userid == $USER->id)) && has_capability('mod/checklist:updateown', $this->context);
    }

    protected function canaddown() {
        global $USER;
        return $this->checklist->useritemsallowed
        && (!$this->userid || ($this->userid == $USER->id)) && has_capability('mod/checklist:updateown', $this->context);
    }

    protected function canpreview() {
        return has_capability('mod/checklist:preview', $this->context);
    }

    protected function canedit() {
        return has_capability('mod/checklist:edit', $this->context);
    }

    protected function caneditother() {
        return has_capability('mod/checklist:updateother', $this->context);
    }

    protected function canviewreports() {
        return has_capability('mod/checklist:viewreports', $this->context)
        || has_capability('mod/checklist:viewmenteereports', $this->context);
    }

    protected function only_view_mentee_reports() {
        return has_capability('mod/checklist:viewmenteereports', $this->context)
        && !has_capability('mod/checklist:viewreports', $this->context);
    }

    /**
     * Test if the current user is a mentor of the passed in user id.
     *
     * @param int $userid
     * @return bool
     */
    public static function is_mentor($userid) {
        global $USER, $DB;

        $sql = 'SELECT c.instanceid
                  FROM {role_assignments} ra
                  JOIN {context} c ON ra.contextid = c.id
                 WHERE c.contextlevel = '.CONTEXT_USER.'
                   AND ra.userid = ?
                   AND c.instanceid = ?';
        return $DB->record_exists_sql($sql, array($USER->id, $userid));
    }

    /**
     * Takes a list of userids and returns only those that the current user
     * is a mentor for (ones where the current user is assigned a role in their
     * user context)
     *
     * @param int[] $userids
     * @return int[]
     */
    public static function filter_mentee_users($userids) {
        global $DB, $USER;

        list($usql, $uparams) = $DB->get_in_or_equal($userids);
        $sql = 'SELECT c.instanceid
                  FROM {role_assignments} ra
                  JOIN {context} c ON ra.contextid = c.id
                 WHERE c.contextlevel = '.CONTEXT_USER.'
                   AND ra.userid = ?
                   AND c.instanceid '.$usql;
        $params = array_merge(array($USER->id), $uparams);
        return $DB->get_fieldset_sql($sql, $params);
    }

    public function view() {
        global $OUTPUT, $CFG;

        if ((!$this->items) && $this->canedit()) {
            redirect(new moodle_url('/mod/checklist/edit.php', array('id' => $this->cm->id)));
        }

        if ($this->canupdateown()) {
            $currenttab = 'view';
        } else if ($this->canpreview()) {
            $currenttab = 'preview';
        } else {
            if ($this->canviewreports()) { // No editing, but can view reports.
                redirect(new moodle_url('/mod/checklist/report.php', array('id' => $this->cm->id)));
            } else {
                $this->view_header();

                if ($CFG->branch >= 30) {
                    $ref = get_local_referer(false);
                } else {
                    $ref = get_referer(false);
                }

                echo $OUTPUT->heading(format_string($this->checklist->name));
                echo $OUTPUT->confirm('<p>'.get_string('guestsno', 'checklist')."</p>\n\n<p>".
                                      get_string('liketologin')."</p>\n", get_login_url(), $ref);
                echo $OUTPUT->footer();
                die;
            }
            $currenttab = '';
        }

        $this->view_header();

        echo $OUTPUT->heading(format_string($this->checklist->name));

        $this->view_tabs($currenttab);

        $params = array(
            'contextid' => $this->context->id,
            'objectid' => $this->checklist->id,
        );
        $event = \mod_checklist\event\course_module_viewed::create($params);
        $event->trigger();

        if ($this->canupdateown()) {
            $this->process_view_actions();
        }

        $this->view_items();

        $this->view_footer();
    }

    public function edit() {
        global $OUTPUT;

        if (!$this->canedit()) {
            redirect(new moodle_url('/mod/checklist/view.php', array('id' => $this->cm->id)));
        }

        $params = array(
            'contextid' => $this->context->id,
            'objectid' => $this->checklist->id,
        );
        $event = \mod_checklist\event\edit_page_viewed::create($params);
        $event->trigger();

        $this->view_header();

        echo $OUTPUT->heading(format_string($this->checklist->name));

        $this->view_tabs('edit');

        $this->process_edit_actions();

        if ($this->checklist->autopopulate) {
            // Needs to be done again, just in case the edit actions have changed something.
            $this->update_items_from_course();
        }

        $this->view_import_export();

        $this->view_edit_items();

        $this->view_footer();
    }

    public function report() {
        global $OUTPUT;

        if ((!$this->items) && $this->canedit()) {
            redirect(new moodle_url('/mod/checklist/edit.php', array('id' => $this->cm->id)));
        }

        if (!$this->canviewreports()) {
            redirect(new moodle_url('/mod/checklist/view.php', array('id' => $this->cm->id)));
        }

        if ($this->userid && $this->only_view_mentee_reports()) {
            // Check this user is a mentee of the logged in user.
            if (!self::is_mentor($this->userid)) {
                $this->userid = false;
            }

        } else if (!$this->caneditother()) {
            $this->userid = false;
        }

        checklist_item::add_grouping_names($this->items, $this->course->id);

        $this->view_header();

        echo $OUTPUT->heading(format_string($this->checklist->name));

        $this->view_tabs('report');

        $this->process_report_actions();

        $params = array(
            'contextid' => $this->context->id,
            'objectid' => $this->checklist->id,
        );
        if ($this->userid) {
            $params['relateduserid'] = $this->userid;
        }
        $event = \mod_checklist\event\report_viewed::create($params);
        $event->trigger();

        if ($this->userid) {
            $this->view_items(true);
        } else {
            $this->view_report();
        }

        $this->view_footer();
    }

    public function user_complete() {
        $this->view_items(false, true);
    }

    protected function view_header() {
        global $PAGE, $OUTPUT;

        $PAGE->set_title($this->pagetitle);
        $PAGE->set_heading($this->course->fullname);

        echo $OUTPUT->header();
    }

    protected function view_tabs($currenttab) {
        $tabs = array();
        $row = array();
        $inactive = array();
        $activated = array();

        if ($this->canupdateown()) {
            $row[] = new tabobject('view', new moodle_url('/mod/checklist/view.php', array('id' => $this->cm->id)),
                                   get_string('view', 'checklist'));
        } else if ($this->canpreview()) {
            $row[] = new tabobject('preview', new moodle_url('/mod/checklist/view.php', array('id' => $this->cm->id)),
                                   get_string('preview', 'checklist'));
        }
        if ($this->canviewreports()) {
            $row[] = new tabobject('report', new moodle_url('/mod/checklist/report.php', array('id' => $this->cm->id)),
                                   get_string('report', 'checklist'));
        }
        if ($this->canedit()) {
            $row[] = new tabobject('edit', new moodle_url('/mod/checklist/edit.php', array('id' => $this->cm->id)),
                                   get_string('edit', 'checklist'));
        }

        if (count($row) > 1) { // No tabs for students.
            $tabs[] = $row;
        }

        if ($currenttab == 'report') {
            $activated[] = 'report';
        }

        if ($currenttab == 'edit') {
            $activated[] = 'edit';

            if (!$this->items) {
                $inactive = array('view', 'report', 'preview');
            }
        }

        if ($currenttab == 'preview') {
            $activated[] = 'preview';
        }

        print_tabs($tabs, $currenttab, $inactive, $activated);
    }

    protected function get_progress() {
        if (!$this->items) {
            return null;
        }

        $teacherprogress = ($this->checklist->teacheredit != CHECKLIST_MARKING_STUDENT);

        $totalitems = 0;
        $requireditems = 0;
        $completeitems = 0;
        $allcompleteitems = 0;
        $checkgroupings = ($this->groupings !== false);
        foreach ($this->items as $item) {
            if (($item->is_heading()) || ($item->hidden)) {
                continue;
            }
            if ($checkgroupings && !empty($item->grouping)) {
                if (!in_array($item->grouping, $this->groupings)) {
                    continue; // Current user is not a member of this item's grouping.
                }
            }
            if ($item->is_required()) {
                $requireditems++;
                if ($item->is_checked($teacherprogress)) {
                    $completeitems++;
                    $allcompleteitems++;
                }
            } else {
                if ($item->is_checked($teacherprogress)) {
                    $allcompleteitems++;
                }
            }
            $totalitems++;
        }
        if (!$teacherprogress) {
            if ($this->useritems) {
                foreach ($this->useritems as $item) {
                    if ($item->is_checked_student()) {
                        $allcompleteitems++;
                    }
                    $totalitems++;
                }
            }
        }
        if ($totalitems == 0) {
            return null;
        }

        return new \mod_checklist\local\progress_info($totalitems, $requireditems, $allcompleteitems, $completeitems);
    }

    /**
     * @param bool $viewother
     * @param bool $userreport
     */
    protected function view_items($viewother = false, $userreport = false) {
        global $DB, $PAGE;

        // Configure the status of the checklist output.
        $status = new output_status();
        $status->set_viewother($viewother);
        $status->set_userreport($userreport);
        $status->set_teachercomments($this->checklist->teachercomments);
        $status->set_canupdateown($this->canupdateown());
        $status->set_canaddown($this->canaddown());

        if ($status->is_teachercomments()) {
            if ($status->is_viewother()) {
                $status->set_editcomments(optional_param('editcomments', false, PARAM_BOOL));
            }
            $comments = checklist_comment::fetch_by_userid_itemids($this->userid, array_keys($this->items));
            checklist_comment::add_commentby_names($comments);
            checklist_item::add_comments($this->items, $comments);
        }
        if ($status->is_canupdateown() || $status->is_viewother() || $status->is_userreport()) {
            $status->set_showprogressbar(true);
            $showteachermark = in_array($this->checklist->teacheredit, [CHECKLIST_MARKING_TEACHER, CHECKLIST_MARKING_BOTH]);
            $status->set_showteachermark($showteachermark);
            $showcheckbox = in_array($this->checklist->teacheredit, [CHECKLIST_MARKING_STUDENT, CHECKLIST_MARKING_BOTH]);
            $status->set_showcheckbox($showcheckbox);
        }
        if ($status->is_showteachermark() && $this->checklist->lockteachermarks) {
            $status->set_teachermarklocked(!has_capability('mod/checklist:updatelocked', $this->context));
        }
        if ($status->is_viewother()) {
            $reportsettings = $this->get_report_settings();
            $status->set_showcompletiondates($reportsettings->showcompletiondates);
            if ($status->is_showcompletiondates()) {
                checklist_item::add_teacher_names($this->items);
            }
        }
        $status->set_overrideauto($this->checklist->autoupdate != CHECKLIST_AUTOUPDATE_YES);
        if ($status->is_canaddown()) {
            if ($this->useredit) {
                $status->set_addown(true);
                $status->set_additemafter($this->additemafter);
            }
        }
        $status->set_checkgroupings($this->groupings !== false);
        if ($status->is_showcheckbox() && $status->is_canupdateown()) {
            if (!$status->is_viewother() && !$status->is_userreport()) {
                // Student checklist + can update + not viewing another user or overview report.
                $status->set_updateform(true);
            }
        }
        if ($status->is_viewother()) {
            if ($status->is_showteachermark() || $status->is_editcomments()) {
                // Viewing another user + teacher checklist or editing comments.
                $status->set_updateform(true);
            }
        }

        // Gather some extra details needed in the output.
        $intro = $this->formatted_intro();
        $progress = null;
        if ($status->is_showprogressbar()) {
            $progress = $this->get_progress();
        }
        $student = null;
        if ($status->is_viewother()) {
            $student = $DB->get_record('user', ['id' => $this->userid], '*', MUST_EXIST);
        }

        // Add the javascript, if needed.
        if (!$status->is_viewother()) {
            // Load the Javascript required to send changes back to the server (without clicking 'save').
            $jsmodule = array(
                'name' => 'mod_checklist',
                'fullpath' => new moodle_url('/mod/checklist/updatechecks24.js')
            );
            $updatechecksurl = new moodle_url('/mod/checklist/updatechecks.php');
            // Progress bars should be updated on 'student only' checklists.
            $updateprogress = $status->is_showteachermark() ? 0 : 1;
            $PAGE->requires->js_init_call('M.mod_checklist.init', array(
                $updatechecksurl->out(), sesskey(), $this->cm->id, $updateprogress
            ), true, $jsmodule);
        }

        $this->output->checklist_items($this->items, $this->useritems, $this->groupings, $intro, $status, $progress, $student);
    }

    protected function formatted_intro() {
        global $CFG;
        $intro = file_rewrite_pluginfile_urls($this->checklist->intro, 'pluginfile.php', $this->context->id,
                                              'mod_checklist', 'intro', null);
        $opts = array('trusted' => $CFG->enabletrusttext);
        return format_text($intro, $this->checklist->introformat, $opts);
    }

    protected function view_import_export() {
        $importurl = new moodle_url('/mod/checklist/import.php', array('id' => $this->cm->id));
        $exporturl = new moodle_url('/mod/checklist/export.php', array('id' => $this->cm->id));

        $importstr = get_string('import', 'checklist');
        $exportstr = get_string('export', 'checklist');

        echo "<div class='checklistimportexport'>";
        echo "<a href='$importurl'>$importstr</a>&nbsp;&nbsp;&nbsp;<a href='$exporturl'>$exportstr</a>";
        echo "</div>";
    }

    protected function view_edit_items() {
        global $PAGE;

        $status = new output_status();
        $status->set_additemafter($this->additemafter);
        $status->set_editdates($this->editdates);
        $status->set_itemid(optional_param('itemid', null, PARAM_INT));
        $status->set_autopopulate($this->checklist->autopopulate);
        if ($this->checklist->autopopulate && $this->checklist->autoupdate) {
            $status->set_autoupdatewarning($this->checklist->teacheredit);
        }
        $status->set_editlinks(true);
        $status->set_allowcourselinks($this->can_link_courses());
        $status->set_editgrouping((bool)self::get_course_groupings($this->course->id));
        $status->set_courseid($this->course->id);

        checklist_item::add_grouping_names($this->items, $this->course->id);

        if ($status->is_allowcourselinks()) {
            $PAGE->requires->yui_module('moodle-mod_checklist-linkselect', 'M.mod_checklist.linkselect.init');
        }

        $this->output->checklist_edit_items($this->items, $status);
    }

    protected function view_report() {
        global $DB, $OUTPUT, $CFG;

        $reportsettings = $this->get_report_settings();

        $editchecks = $this->caneditother() && optional_param('editchecks', false, PARAM_BOOL);

        $page = optional_param('page', 0, PARAM_INT);
        $perpage = optional_param('perpage', 30, PARAM_INT);

        $thisurl = new moodle_url('/mod/checklist/report.php', array('id' => $this->cm->id, 'sesskey' => sesskey()));
        if ($editchecks) {
            $thisurl->param('editchecks', 'on');
        }

        if ($this->checklist->autoupdate && $this->checklist->autopopulate) {
            if ($this->checklist->teacheredit == CHECKLIST_MARKING_TEACHER) {
                echo '<p>'.get_string('autoupdatewarning_teacher', 'checklist').'</p>';
            } else if ($this->checklist->teacheredit == CHECKLIST_MARKING_BOTH) {
                echo '<p class="checklistwarning">'.get_string('autoupdatewarning_both', 'checklist').'</p>';
            }
        }

        groups_print_activity_menu($this->cm, $thisurl);
        $activegroup = groups_get_activity_group($this->cm, true);
        if ($activegroup == 0) {
            if (groups_get_activity_groupmode($this->cm) == SEPARATEGROUPS) {
                if (!has_capability('moodle/site:accessallgroups', $this->context)) {
                    $activegroup = -1; // Not allowed to access any groups.
                }
            }
        }

        echo '&nbsp;&nbsp;<form style="display: inline;" action="'.$thisurl->out_omit_querystring().'" method="get" />';
        echo html_writer::input_hidden_params($thisurl, array('action'));
        if ($reportsettings->showoptional) {
            echo '<input type="hidden" name="action" value="hideoptional" />';
            echo '<input type="submit" name="submit" value="'.get_string('optionalhide', 'checklist').'" />';
        } else {
            echo '<input type="hidden" name="action" value="showoptional" />';
            echo '<input type="submit" name="submit" value="'.get_string('optionalshow', 'checklist').'" />';
        }
        echo '</form>';

        echo '&nbsp;&nbsp;<form style="display: inline;" action="'.$thisurl->out_omit_querystring().'" method="get" />';
        echo html_writer::input_hidden_params($thisurl);
        if ($reportsettings->showprogressbars) {
            $editchecks = false;
            echo '<input type="hidden" name="action" value="hideprogressbars" />';
            echo '<input type="submit" name="submit" value="'.get_string('showfulldetails', 'checklist').'" />';
        } else {
            echo '<input type="hidden" name="action" value="showprogressbars" />';
            echo '<input type="submit" name="submit" value="'.get_string('showprogressbars', 'checklist').'" />';
        }
        echo '</form>';

        if ($editchecks) {
            echo '&nbsp;&nbsp;<form style="display: inline;" action="'.$thisurl->out_omit_querystring().'" method="post" />';
            echo html_writer::input_hidden_params($thisurl);
            echo '<input type="hidden" name="action" value="updateallchecks"/>';
            echo '<input type="submit" name="submit" value="'.get_string('savechecks', 'checklist').'" />';
        } else if (!$reportsettings->showprogressbars && $this->caneditother()
            && $this->checklist->teacheredit != CHECKLIST_MARKING_STUDENT
        ) {
            echo '&nbsp;&nbsp;<form style="display: inline;" action="'.$thisurl->out_omit_querystring().'" method="get" />';
            echo html_writer::input_hidden_params($thisurl);
            echo '<input type="hidden" name="editchecks" value="on" />';
            echo '<input type="submit" name="submit" value="'.get_string('editchecks', 'checklist').'" />';
            echo '</form>';
        }

        echo '<br style="clear:both"/>';

        switch ($reportsettings->sortby) {
            case 'firstdesc':
                $orderby = 'u.firstname DESC';
                break;

            case 'lastasc':
                $orderby = 'u.lastname';
                break;

            case 'lastdesc':
                $orderby = 'u.lastname DESC';
                break;

            default:
                $orderby = 'u.firstname';
                break;
        }

        $ausers = false;
        if ($activegroup == -1) {
            $users = array();
        } else {
            if (get_config('mod_checklist', 'onlyenrolled')) {
                $users = get_enrolled_users($this->context, 'mod/checklist:updateown', $activegroup, 'u.id', $orderby, 0, 0, true);
            } else {
                $users = get_users_by_capability($this->context, 'mod/checklist:updateown', 'u.id', $orderby, '', '',
                                                 $activegroup, '', false);
            }
            if ($users) {
                $users = array_keys($users);
                if ($this->only_view_mentee_reports()) {
                    // Filter to only show reports for users who this user mentors
                    // (ie they have been assigned to them in a context).
                    $users = static::filter_mentee_users($users);
                }
            }
        }
        if ($users && !empty($users)) {
            if (count($users) < $page * $perpage) {
                $page = 0;
            }
            echo $OUTPUT->paging_bar(count($users), $page, $perpage, new moodle_url($thisurl, array('perpage' => $perpage)));
            $users = array_slice($users, $page * $perpage, $perpage);

            list($usql, $uparams) = $DB->get_in_or_equal($users);
            $fields = get_all_user_name_fields(true, 'u');
            $ausers = $DB->get_records_sql("SELECT u.id, $fields FROM {user} u WHERE u.id ".$usql.' ORDER BY '.$orderby, $uparams);
        }

        if ($reportsettings->showprogressbars) {
            if ($ausers) {
                // Show just progress bars.
                if ($reportsettings->showoptional) {
                    $itemstocount = array();
                    foreach ($this->items as $item) {
                        if (!$item->hidden) {
                            if (($item->itemoptional == CHECKLIST_OPTIONAL_YES) || ($item->itemoptional == CHECKLIST_OPTIONAL_NO)) {
                                $itemstocount[] = $item->id;
                            }
                        }
                    }
                } else {
                    $itemstocount = array();
                    foreach ($this->items as $item) {
                        if (!$item->hidden) {
                            if ($item->itemoptional == CHECKLIST_OPTIONAL_NO) {
                                $itemstocount[] = $item->id;
                            }
                        }
                    }
                }
                $totalitems = count($itemstocount);

                $sql = '';
                if ($totalitems) {
                    list($isql, $iparams) = $DB->get_in_or_equal($itemstocount, SQL_PARAMS_NAMED);
                    if ($this->checklist->teacheredit == CHECKLIST_MARKING_STUDENT) {
                        $sql = 'usertimestamp > 0 AND item '.$isql.' AND userid = :user ';
                    } else {
                        $sql = 'teachermark = '.CHECKLIST_TEACHERMARK_YES.' AND item '.$isql.' AND userid = :user ';
                    }
                }
                echo '<div>';
                foreach ($ausers as $auser) {
                    if ($totalitems) {
                        $iparams['user'] = $auser->id;
                        $tickeditems = $DB->count_records_select('checklist_check', $sql, $iparams);
                        $percentcomplete = ($tickeditems * 100) / $totalitems;
                    } else {
                        $percentcomplete = 0;
                        $tickeditems = 0;
                    }

                    if ($this->caneditother()) {
                        $vslink = ' <a href="'.$thisurl->out(true, array('studentid' => $auser->id)).'" ';
                        $vslink .= 'alt="'.get_string('viewsinglereport', 'checklist').'" title="'.
                            get_string('viewsinglereport', 'checklist').'">';
                        $vslink .= $OUTPUT->pix_icon('t/preview', '').'</a>';
                    } else {
                        $vslink = '';
                    }
                    $userurl = new moodle_url('/user/view.php', array('id' => $auser->id, 'course' => $this->course->id));
                    $userlink = '<a href="'.$userurl.'">'.fullname($auser).'</a>';
                    echo '<div style="float: left; width: 30%; text-align: right; margin-right: 8px; ">'.$userlink.$vslink.'</div>';

                    echo '<div class="checklist_progress_outer">';
                    echo '<div class="checklist_progress_inner" style="width:'.$percentcomplete.'%;">&nbsp;</div>';
                    echo '</div>';
                    echo '<div class="checklist_percentcomplete" style="float:left; width: 3em;">&nbsp;'.
                        sprintf('%0d%%', $percentcomplete).'</div>';
                    echo '<div style="float:left;">&nbsp;('.$tickeditems.'/'.$totalitems.')</div>';
                    echo '<br style="clear:both;" />';
                }
                echo '</div>';
            }

        } else {

            // Show full table.
            $firstlink = 'firstasc';
            $lastlink = 'lastasc';
            $firstarrow = '';
            $lastarrow = '';
            if ($reportsettings->sortby == 'firstasc') {
                $firstlink = 'firstdesc';
                $firstarrow = $OUTPUT->pix_icon('t/down', get_string('asc'));
            } else if ($reportsettings->sortby == 'lastasc') {
                $lastlink = 'lastdesc';
                $lastarrow = $OUTPUT->pix_icon('t/down', get_string('asc'));
            } else if ($reportsettings->sortby == 'firstdesc') {
                $firstarrow = $OUTPUT->pix_icon('t/up', get_string('desc'));
            } else if ($reportsettings->sortby == 'lastdesc') {
                $lastarrow = $OUTPUT->pix_icon('t/up', get_string('desc'));
            }
            $firstlink = new moodle_url($thisurl, array('sortby' => $firstlink));
            $lastlink = new moodle_url($thisurl, array('sortby' => $lastlink));
            $nameheading = ' <a href="'.$firstlink.'" >'.get_string('firstname').'</a> '.$firstarrow;
            $nameheading .= ' / <a href="'.$lastlink.'" >'.get_string('lastname').'</a> '.$lastarrow;

            $table = new stdClass;
            $table->head = array($nameheading);
            $table->level = array(-1);
            $table->size = array('100px');
            $table->skip = array(false);
            foreach ($this->items as $item) {
                if ($item->hidden) {
                    continue;
                }

                $table->head[] = format_string($item->displaytext).$this->output->item_grouping($item);
                $table->level[] = ($item->indent < 3) ? $item->indent : 2;
                $table->size[] = '80px';
                $table->skip[] = (!$reportsettings->showoptional) && ($item->itemoptional == CHECKLIST_OPTIONAL_YES);
            }

            $disableditems = $this->get_teacher_disabled_items();

            $table->data = array();
            if ($ausers) {
                foreach ($ausers as $auser) {
                    $row = array();

                    $vslink = ' <a href="'.$thisurl->out(true, array('studentid' => $auser->id)).'" ';
                    $vslink .= 'alt="'.get_string('viewsinglereport', 'checklist').'" title="'.
                        get_string('viewsinglereport', 'checklist').'">';
                    $vslink .= $OUTPUT->pix_icon('t/preview', '').'</a>';
                    $userurl = new moodle_url('/user/view.php', array('id' => $auser->id, 'course' => $this->course->id));
                    $userlink = '<a href="'.$userurl.'">'.fullname($auser).'</a>';

                    $row[] = $userlink.$vslink;

                    $sql = 'SELECT i.id, i.itemoptional, i.hidden, c.usertimestamp, c.teachermark
                              FROM {checklist_item} i
                         LEFT JOIN {checklist_check} c ';
                    $sql .= 'ON (i.id = c.item AND c.userid = ? ) WHERE i.checklist = ? AND i.userid=0 ORDER BY i.position';
                    $checks = $DB->get_records_sql($sql, array($auser->id, $this->checklist->id));

                    foreach ($checks as $check) {
                        if ($check->hidden) {
                            continue;
                        }

                        if ($check->itemoptional == CHECKLIST_OPTIONAL_HEADING) {
                            $row[] = array(false, false, true, 0, 0);
                        } else {
                            if ($check->usertimestamp > 0) {
                                $row[] = array($check->teachermark, true, false, $auser->id, $check->id);
                            } else {
                                $row[] = array($check->teachermark, false, false, $auser->id, $check->id);
                            }
                        }
                    }

                    $table->data[] = $row;

                    if ($editchecks) {
                        echo '<input type="hidden" name="userids[]" value="'.$auser->id.'" />';
                    }
                }
            }

            echo '<div style="overflow:auto">';
            $this->print_report_table($table, $editchecks, $disableditems);
            echo '</div>';

            if ($editchecks) {
                echo '<input type="submit" name="submit" value="'.get_string('savechecks', 'checklist').'" />';
                echo '</form>';
            }
        }
    }

    /**
     * This function gets called when we are in editing mode
     * adding the button the the row
     *
     * @table object object being parsed
     * @param $table
     * @return string Return ammended code to output
     */
    protected function report_add_toggle_button_row($table) {
        global $PAGE;

        if (!$table->data) {
            return '';
        }

        $PAGE->requires->yui_module('moodle-mod_checklist-buttons', 'M.mod_checklist.buttons.init');
        $passedrow = $table->data;
        $output = '';
        $output .= '<tr class="r1">';
        foreach ($passedrow[0] as $key => $item) {
            if ($key == 0) {
                // Left align + colspan of 2 (overlapping the button column).
                $output .= '<td colspan="2" style=" text-align: left; width: '.$table->size[0].';" class="cell c0"></td>';
            } else {
                $size = $table->size[$key];
                $cellclass = 'cell c'.$key.' level'.$table->level[$key];
                list($teachermark, $studentmark, $heading, $userid, $checkid) = $item;
                if ($heading) {
                    // Heading items have no buttons.
                    $output .= '<td style=" text-align: center; width: '.$size.';" class="cell c0">&nbsp;</td>';
                } else {
                    // Not a heading item => add a button.
                    $output .= '<td style=" text-align: center; width: '.$size.';" class="'.$cellclass.'">';
                    $output .= html_writer::tag('button', get_string('togglecolumn', 'checklist'),
                                                array(
                                                    'class' => 'make_col_c',
                                                    'id' => $checkid,
                                                    'type' => 'button'
                                                ));
                    $output .= '</td>';
                }
            }
        }
        $output .= '</tr>';
        return $output;
    }

    protected function print_report_table($table, $editchecks, $disableditems) {
        global $OUTPUT;

        $output = '';

        $output .= '<table summary="'.get_string('reporttablesummary', 'checklist').'"';
        $output .= ' cellpadding="5" cellspacing="1" class="generaltable boxaligncenter checklistreport">';

        $showteachermark = !($this->checklist->teacheredit == CHECKLIST_MARKING_STUDENT);
        $showstudentmark = !($this->checklist->teacheredit == CHECKLIST_MARKING_TEACHER);
        $teachermarklocked = $this->checklist->lockteachermarks && !has_capability('mod/checklist:updatelocked', $this->context);

        // Sort out the heading row.
        $output .= '<tr>';
        $keys = array_keys($table->head);
        $lastkey = end($keys);
        foreach ($table->head as $key => $heading) {
            if ($table->skip[$key]) {
                continue;
            }
            $size = $table->size[$key];
            $levelclass = ' head'.$table->level[$key];
            if ($key == $lastkey) {
                $levelclass .= ' lastcol';
            }
            // If statement to judge if the header is the first cell in the row, if so the <th> needs colspan=2 added
            // to cover the extra column added (containing the toggle button) to retain the correct table structure.
            $colspan = '';
            if ($key == 0 && $editchecks) {
                $colspan = 'colspan="2"';
            }
            $output .= '<th '.$colspan.' style="vertical-align:top; text-align: center; width:'.$size.
                '" class="header c'.$key.$levelclass.'" scope="col">';
            $output .= $heading.'</th>';
        }
        $output .= '</tr>';

        // If we are in editing mode, run the add_row function that adds the button and necessary code to the document.
        if ($editchecks) {
            $output .= $this->report_add_toggle_button_row($table);
        }
        // Output the data.
        $tickimg = $OUTPUT->pix_icon('i/grade_correct', get_string('itemcomplete', 'checklist'));
        $teacherimg = array(
            CHECKLIST_TEACHERMARK_UNDECIDED => $OUTPUT->pix_icon('empty_box',
                                                                 get_string('teachermarkundecided', 'checklist'), 'checklist'),
            CHECKLIST_TEACHERMARK_YES => $OUTPUT->pix_icon('tick_box', get_string('teachermarkyes', 'checklist'), 'checklist'),
            CHECKLIST_TEACHERMARK_NO => $OUTPUT->pix_icon('cross_box', get_string('teachermarkno', 'checklist'), 'checklist'),
        );
        $oddeven = 1;
        $keys = array_keys($table->data);
        $lastrowkey = end($keys);
        foreach ($table->data as $key => $row) {
            $oddeven = $oddeven ? 0 : 1;
            $class = '';
            if ($key == $lastrowkey) {
                $class = ' lastrow';
            }

            $output .= '<tr class="r'.$oddeven.$class.'">';
            $keys2 = array_keys($row);
            $lastkey = end($keys2);
            foreach ($row as $colkey => $item) {
                if ($table->skip[$colkey]) {
                    continue;
                }
                if ($colkey == 0) {
                    // First item is the name.
                    $output .= '<td style=" text-align: left; width: '.$table->size[0].';" class="cell c0">'.$item.'</td>';
                } else {
                    $size = $table->size[$colkey];
                    $img = '&nbsp;';
                    $cellclass = 'level'.$table->level[$colkey];
                    list($teachermark, $studentmark, $heading, $userid, $checkid) = $item;
                    // If statement to add button at beginning of row in edting mode.
                    if ($colkey == 1 && $editchecks) {
                        $output .= '<td style=" text-align: center; width: '.$size.';" class="'.$cellclass.'">';
                        $output .= html_writer::tag('button', get_string('togglerow', 'checklist'),
                                                    array(
                                                        'class' => 'make_c',
                                                        'id' => $this->find_userid($row),
                                                        'type' => 'button'
                                                    ));
                        $output .= '</td>';
                    }
                    if ($heading) {
                        $output .= '<td style=" text-align: center; width: '.$size.
                            ';" class="cell c'.$colkey.' reportheading">&nbsp;</td>';
                    } else {
                        if ($showteachermark) {
                            if ($teachermark == CHECKLIST_TEACHERMARK_YES) {
                                $cellclass .= '-checked';
                                $img = $teacherimg[$teachermark];
                            } else if ($teachermark == CHECKLIST_TEACHERMARK_NO) {
                                $cellclass .= '-unchecked';
                                $img = $teacherimg[$teachermark];
                            } else {
                                $img = $teacherimg[CHECKLIST_TEACHERMARK_UNDECIDED];
                            }

                            if ($editchecks) {
                                $lock = $teachermarklocked && $teachermark == CHECKLIST_TEACHERMARK_YES;
                                $lock = $lock || in_array($checkid, $disableditems);
                                $disabled = $lock ? 'disabled="disabled" ' : '';

                                $selu = ($teachermark == CHECKLIST_TEACHERMARK_UNDECIDED) ? 'selected="selected" ' : '';
                                $sely = ($teachermark == CHECKLIST_TEACHERMARK_YES) ? 'selected="selected" ' : '';
                                $seln = ($teachermark == CHECKLIST_TEACHERMARK_NO) ? 'selected="selected" ' : '';

                                $img = '<select name="items_'.$userid.'['.$checkid.']" '.$disabled.'>';
                                $img .= '<option value="'.CHECKLIST_TEACHERMARK_UNDECIDED.'" '.$selu.'></option>';
                                $img .= '<option value="'.CHECKLIST_TEACHERMARK_YES.'" '.$sely.'>'.get_string('yes').'</option>';
                                $img .= '<option value="'.CHECKLIST_TEACHERMARK_NO.'" '.$seln.'>'.get_string('no').'</option>';
                                $img .= '</select>';
                            }
                        }
                        if ($showstudentmark) {
                            if ($studentmark) {
                                if (!$showteachermark) {
                                    $cellclass .= '-checked';
                                }
                                $img .= $tickimg;
                            }
                        }

                        $cellclass .= ' cell c'.$colkey;

                        if ($colkey == $lastkey) {
                            $cellclass .= ' lastcol';
                        }

                        $output .= '<td style=" text-align: center; width: '.$size.';" class="'.$cellclass.'">'.$img.'</td>';
                    }
                }
            }
            $output .= '</tr>';
        }

        $output .= '</table>';

        echo $output;
    }

    private function find_userid($row) {
        foreach ($row as $colkey => $item) {
            if ($colkey == 0) {
                continue;
            }
            list($teachermark, $studentmark, $heading, $userid, $checkid) = $item;
            if ($userid) {
                return $userid;
            }
        }
        return null;
    }

    protected function view_footer() {
        global $OUTPUT;
        echo $OUTPUT->footer();
    }

    protected function process_view_actions() {
        $this->useredit = optional_param('useredit', false, PARAM_BOOL);

        $action = optional_param('action', false, PARAM_TEXT);
        if (!$action) {
            return;
        }

        require_sesskey();

        $itemid = optional_param('itemid', 0, PARAM_INT);

        switch ($action) {
            case 'updatechecks':
                $newchecks = optional_param_array('items', array(), PARAM_INT);
                $this->updatechecks($newchecks);
                break;

            case 'startadditem':
                $this->additemafter = $itemid;
                break;

            case 'edititem':
                if ($this->useritems && isset($this->useritems[$itemid])) {
                    $this->useritems[$itemid]->set_editme();
                }
                break;

            case 'additem':
                $displaytext = optional_param('displaytext', '', PARAM_TEXT);
                $displaytext .= "\n".optional_param('displaytextnote', '', PARAM_TEXT);
                $position = optional_param('position', false, PARAM_INT);
                $this->additem($displaytext, $this->userid, 0, $position);
                $item = $this->get_item_at_position($position);
                if ($item) {
                    $this->additemafter = $item->id;
                }
                break;

            case 'deleteitem':
                $this->deleteitem($itemid);
                break;

            case 'updateitem':
                $displaytext = optional_param('displaytext', '', PARAM_TEXT);
                $displaytext .= "\n".optional_param('displaytextnote', '', PARAM_TEXT);
                $this->updateitem($itemid, $displaytext);
                break;

            default:
                throw new moodle_exception('invalidaction', 'mod_checklist', '', $action);
        }

        if ($action != 'updatechecks') {
            $this->useredit = true;
        }
    }

    protected function process_edit_actions() {
        $this->editdates = optional_param('editdates', false, PARAM_BOOL);
        $additemafter = optional_param('additemafter', false, PARAM_INT);
        $removeauto = optional_param('removeauto', false, PARAM_TEXT);

        if ($removeauto) {
            // Remove any automatically generated items from the list
            // (if no longer using automatic items).
            require_sesskey();
            $this->removeauto();
            return;
        }

        $action = optional_param('action', false, PARAM_TEXT);
        if (!$action) {
            if (optional_param('additem', false, PARAM_BOOL)) {
                $action = 'additem';
            } else if (optional_param('updateitem', false, PARAM_BOOL)) {
                $action = 'updateitem';
            } else if (optional_param('showhideitems', false, PARAM_BOOL)) {
                $action = 'showhideitems';
            } else if (optional_param('canceledititem', false, PARAM_BOOL)) {
                $additemafter = false;
            }
        }
        if (!$action) {
            $this->additemafter = $additemafter;
            return;
        }

        require_sesskey();

        $itemid = optional_param('itemid', 0, PARAM_INT);

        switch ($action) {
            case 'additem':
                $displaytext = optional_param('displaytext', '', PARAM_TEXT);
                $indent = optional_param('indent', 0, PARAM_INT);
                $position = optional_param('position', false, PARAM_INT);
                $linkcourseid = optional_param('linkcourseid', null, PARAM_INT);
                $linkurl = optional_param('linkurl', null, PARAM_URL);
                $grouping = optional_param('grouping', 0, PARAM_INT);
                if (optional_param('duetimedisable', false, PARAM_BOOL)) {
                    $duetime = false;
                } else {
                    $duetime = optional_param_array('duetime', false, PARAM_INT);
                }
                $this->additem($displaytext, 0, $indent, $position, $duetime, 0, CHECKLIST_OPTIONAL_NO, CHECKLIST_HIDDEN_NO,
                               $linkcourseid, $linkurl, $grouping);
                if ($position) {
                    $additemafter = false;
                }
                break;
            case 'startadditem':
                $additemafter = $itemid;
                break;
            case 'edititem':
                if (isset($this->items[$itemid])) {
                    $this->items[$itemid]->set_editme();
                }
                $additemafter = false;
                break;
            case 'updateitem':
                $displaytext = optional_param('displaytext', '', PARAM_TEXT);
                $linkcourseid = optional_param('linkcourseid', null, PARAM_INT);
                $linkurl = optional_param('linkurl', null, PARAM_URL);
                $grouping = optional_param('grouping', 0, PARAM_INT);
                if (optional_param('duetimedisable', false, PARAM_BOOL)) {
                    $duetime = false;
                } else {
                    $duetime = optional_param_array('duetime', false, PARAM_INT);
                }
                $this->updateitem($itemid, $displaytext, $duetime, $linkcourseid, $linkurl, $grouping);
                break;
            case 'deleteitem':
                if (($this->checklist->autopopulate) && (isset($this->items[$itemid])) && ($this->items[$itemid]->moduleid)) {
                    $this->toggledisableitem($itemid);
                } else {
                    $this->deleteitem($itemid);
                }
                break;
            case 'moveitemup':
                $this->moveitemup($itemid);
                break;
            case 'moveitemdown':
                $this->moveitemdown($itemid);
                break;
            case 'indentitem':
                $this->indentitem($itemid);
                break;
            case 'unindentitem':
                $this->unindentitem($itemid);
                break;
            case 'makeoptional':
                $this->makeoptional($itemid, true);
                break;
            case 'makerequired':
                $this->makeoptional($itemid, false);
                break;
            case 'makeheading':
                $this->makeoptional($itemid, true, true);
                break;
            case 'nextcolour':
                $this->nextcolour($itemid);
                break;

            case 'showhideitems':
                $itemids = optional_param_array('items', array(), PARAM_INT);
                foreach ($itemids as $itemid) {
                    $this->toggledisableitem($itemid);
                }
                break;

            default:
                throw new moodle_exception('invalidaction', 'mod_checklist', '', $action);
        }

        if ($additemafter) {
            $this->additemafter = $additemafter;
        }
    }

    protected function get_report_settings() {
        global $SESSION;

        if (!isset($SESSION->checklist_report)) {
            $settings = new stdClass;
            $settings->showcompletiondates = false;
            $settings->showoptional = true;
            $settings->showprogressbars = false;
            $settings->sortby = 'firstasc';
            $SESSION->checklist_report = $settings;
        }
        return clone $SESSION->checklist_report; // We want changes to settings to be explicit.
    }

    protected function set_report_settings($settings) {
        global $SESSION, $CFG;

        $currsettings = $this->get_report_settings();
        foreach ($currsettings as $key => $currval) {
            if (isset($settings->$key)) {
                $currsettings->$key = $settings->$key; // Only set values if they already exist.
            }
        }
        if ($CFG->debug == DEBUG_DEVELOPER) { // Show dev error if attempting to set non-existent setting.
            foreach ($settings as $key => $val) {
                if (!isset($currsettings->$key)) {
                    debugging("Attempting to set invalid setting '$key'", DEBUG_DEVELOPER);
                }
            }
        }

        $SESSION->checklist_report = $currsettings;
    }

    protected function process_report_actions() {
        $settings = $this->get_report_settings();

        if ($sortby = optional_param('sortby', false, PARAM_TEXT)) {
            $settings->sortby = $sortby;
            $this->set_report_settings($settings);
        }

        $savenext = optional_param('savenext', false, PARAM_TEXT);
        $viewnext = optional_param('viewnext', false, PARAM_TEXT);
        $action = optional_param('action', false, PARAM_TEXT);
        if (!$action) {
            return;
        }

        if (!confirm_sesskey()) {
            error('Invalid sesskey');
        }

        switch ($action) {
            case 'showprogressbars':
                $settings->showprogressbars = true;
                break;
            case 'hideprogressbars':
                $settings->showprogressbars = false;
                break;
            case 'showoptional':
                $settings->showoptional = true;
                break;
            case 'hideoptional':
                $settings->showoptional = false;
                break;
            case 'updatechecks':
                if ($this->caneditother() && !$viewnext) {
                    $this->updateteachermarks();
                }
                break;
            case 'updateallchecks':
                if ($this->caneditother()) {
                    $this->updateallteachermarks();
                }
                break;
            case 'toggledates':
                $settings->showcompletiondates = !$settings->showcompletiondates;
                break;
        }

        $this->set_report_settings($settings);

        if ($viewnext || $savenext) {
            $this->getnextuserid();
            $this->get_items();
        }
    }

    protected function validate_links(&$linkcourseid, &$linkurl, &$grouping) {
        if ($linkcourseid && $this->can_link_courses()) {
            $courses = self::get_linkable_courses();
            if (!array_key_exists($linkcourseid, $courses)) {
                $linkcourseid = null;
            } else {
                $linkurl = null; // If the courseid is valid, then the url should be blank.
            }
        } else {
            $linkcourseid = null;
        }

        if ($linkurl) {
            $scheme = parse_url($linkurl, PHP_URL_SCHEME);
            if (!$scheme) {
                $linkurl = 'http://'.$linkurl;
            }
        }

        if ($grouping !== null) {
            if (!$grouping || !array_key_exists($grouping, self::get_course_groupings($this->course->id))) {
                $grouping = 0;
            }
        }
    }

    public function additem($displaytext, $userid = 0, $indent = 0, $position = false, $duetime = false, $moduleid = 0,
                            $optional = CHECKLIST_OPTIONAL_NO, $hidden = CHECKLIST_HIDDEN_NO, $linkcourseid = null,
                            $linkurl = null, $grouping = 0) {
        $displaytext = trim($displaytext);
        if ($displaytext == '') {
            return false;
        }

        if ($userid) {
            if (!$this->canaddown()) {
                return false;
            }
        } else {
            if (!$moduleid && !$this->canedit()) {
                // Moduleid entries are added automatically, if the activity exists; ignore canedit check.
                return false;
            }
        }

        $this->validate_links($linkcourseid, $linkurl, $grouping);

        $item = new checklist_item();
        $item->checklist = $this->checklist->id;
        $item->displaytext = $displaytext;
        if ($position) {
            $item->position = $position;
        } else {
            $item->position = count($this->items) + 1;
        }
        $item->indent = $indent;
        $item->userid = $userid;
        $item->itemoptional = $optional;
        $item->hidden = $hidden;
        $item->duetime = 0;
        if ($this->editdates && $duetime) {
            $item->duetime = make_timestamp($duetime['year'], $duetime['month'], $duetime['day']);
        }
        $item->eventid = 0;
        $item->colour = 'black';
        $item->moduleid = $moduleid;
        $item->linkcourseid = $linkcourseid;
        $item->linkurl = $linkurl;
        $item->grouping = $grouping;

        $item->insert();
        if ($item->id) {
            if ($userid) {
                $this->useritems[$item->id] = $item;
                if ($position) {
                    checklist_item::sort_items($this->useritems);
                }
            } else {
                if ($position) {
                    $this->additemafter = $item->id;
                    $this->update_item_positions(1, $position);
                }
                $this->items[$item->id] = $item;
                checklist_item::sort_items($this->items);
                if ($this->checklist->duedatesoncalendar) {
                    $this->setevent($item, true);
                }
            }

            if ($item->linkcourseid) {
                $this->update_course_completion_for_item($item);
            }
        }

        return $item->id;
    }

    protected function setevent(checklist_item $item, $add) {
        global $CFG;
        require_once($CFG->dirroot.'/calendar/lib.php');

        if ((!$add) || ($item->duetime == 0)) {  // Remove the event (if any).
            if (!$item->eventid) {
                return; // No event to remove.
            }

            try {
                $event = calendar_event::load($item->eventid);
                $event->delete();
            } catch (dml_missing_record_exception $e) {
                // Just ignore this error - the event is missing, so does not need deleting.
                $event = null; // Do something here to stop codechecker complaining.
            }
            $item->eventid = 0;
            if ($add) {
                // Don't bother updating the record if we are deleting.
                $item->update();
            }

        } else {  // Add/update event.
            $eventdata = new stdClass();
            $eventdata->name = $item->displaytext;
            $eventdata->description = get_string('calendardescription', 'checklist', $this->checklist->name);
            $eventdata->courseid = $this->course->id;
            $eventdata->modulename = 'checklist';
            $eventdata->instance = $this->checklist->id;
            $eventdata->eventtype = 'due';
            $eventdata->timestart = $item->duetime;

            if ($item->eventid) {
                try {
                    $event = calendar_event::load($item->eventid);
                    $event->update($eventdata);
                } catch (dml_missing_record_exception $e) {
                    $item->eventid = 0; // Event missing, so create a new event.
                }
            }
            if (!$item->eventid) {
                $event = calendar_event::create($eventdata, false);
                $item->eventid = $event->id;
                $item->update();
            }
        }
    }

    public function setallevents() {
        if (!$this->items) {
            return;
        }

        $add = $this->checklist->duedatesoncalendar;
        foreach ($this->items as $item) {
            $this->setevent($item, $add);
        }
    }

    protected function updateitem($itemid, $displaytext, $duetime = false, $linkcourseid = null, $linkurl = null,
                                  $grouping = null) {
        $displaytext = trim($displaytext);
        if ($displaytext == '') {
            return;
        }

        if (isset($this->items[$itemid])) {
            if ($this->canedit()) {
                $this->validate_links($linkcourseid, $linkurl, $grouping);

                $item = $this->items[$itemid];
                $oldlinkcourseid = $item->linkcourseid;
                $item->displaytext = $displaytext;
                if ($this->editdates) {
                    $item->duetime = 0;
                    if ($duetime) {
                        $item->duetime = make_timestamp($duetime['year'], $duetime['month'], $duetime['day']);
                    }
                }
                $item->linkcourseid = $linkcourseid;
                $item->linkurl = $linkurl;
                if ($grouping !== null) {
                    $item->grouping = $grouping;
                }
                $item->update();
                if ($this->checklist->duedatesoncalendar) {
                    $this->setevent($item, true);
                }

                if ($item->linkcourseid != $oldlinkcourseid) {
                    if ($this->checklist->autoupdate == CHECKLIST_AUTOUPDATE_YES) {
                        // If autoupdate is yes, cannot override, reset all checks for this item, before recalculating status
                        // (if the student *can* override, or if there is no autoupdate, then leave them as they are).
                        $item->clear_all_student_checks();
                    }
                    $this->update_course_completion_for_item($item);
                }
            }
        } else if (isset($this->useritems[$itemid])) {
            if ($this->canaddown()) {
                $item = $this->useritems[$itemid];
                $item->displaytext = $displaytext;
                $item->update();
            }
        }
    }

    protected function toggledisableitem($itemid) {
        if (isset($this->items[$itemid])) {
            if (!$this->canedit()) {
                return;
            }

            $item = $this->items[$itemid];
            $item->toggle_hidden();

            // If the item is a section heading, then show/hide all items in that section.
            if ($item->is_heading()) {
                foreach ($this->items as $it) {
                    if ($it->position <= $item->position) {
                        continue; // Loop until we find the current item.
                    }
                    if ($it->is_heading()) {
                        break; // Stop at the next heading.
                    }
                    if ($item->hidden) {
                        $it->hide_item();
                    } else {
                        $it->show_item();
                    }
                }
            }
            checklist_update_grades($this->checklist);
        }
    }

    protected function deleteitem($itemid, $forcedelete = false) {
        global $DB;

        if (isset($this->items[$itemid])) {
            if (!$forcedelete && !$this->canedit()) {
                return;
            }
            $item = $this->items[$itemid];
            $this->setevent($item, false); // Remove any calendar events.
            unset($this->items[$itemid]);
        } else if (isset($this->useritems[$itemid])) {
            if (!$this->canaddown()) {
                return;
            }
            $item = $this->useritems[$itemid];
            unset($this->useritems[$itemid]);
        } else {
            // Item for deletion is not currently available.
            return;
        }

        $item->delete();
        $DB->delete_records('checklist_check', array('item' => $itemid));

        $this->update_item_positions();
    }

    protected function moveitemto($itemid, $newposition, $forceupdate = false) {
        global $DB;

        if (!isset($this->items[$itemid])) {
            if (isset($this->useritems[$itemid])) {
                if ($this->canupdateown()) {
                    $item = $this->useritems[$itemid];
                    $item->position = $newposition;
                    $item->update();
                }
            }
            return;
        }

        if (!$forceupdate && !$this->canedit()) {
            return;
        }

        $itemcount = count($this->items);
        if ($newposition < 1) {
            $newposition = 1;
        } else if ($newposition > $itemcount) {
            $newposition = $itemcount;
        }

        $oldposition = $this->items[$itemid]->position;
        if ($oldposition == $newposition) {
            return;
        }

        if ($newposition < $oldposition) {
            $this->update_item_positions(1, $newposition, $oldposition); // Move items down.
        } else {
            $this->update_item_positions(-1, $oldposition, $newposition); // Move items up (including this one).
        }

        $item = $this->items[$itemid];
        $item->position = $newposition; // Move item to new position.
        $item->update();
        checklist_item::sort_items($this->items);
    }

    protected function moveitemup($itemid) {
        // TODO If indented, only allow move if suitable space for 'reparenting'.

        if (!isset($this->items[$itemid])) {
            if (isset($this->useritems[$itemid])) {
                $this->moveitemto($itemid, $this->useritems[$itemid]->position - 1);
            }
            return;
        }
        $this->moveitemto($itemid, $this->items[$itemid]->position - 1);
    }

    protected function moveitemdown($itemid) {
        // TODO If indented, only allow move if suitable space for 'reparenting'.

        if (!isset($this->items[$itemid])) {
            if (isset($this->useritems[$itemid])) {
                $this->moveitemto($itemid, $this->useritems[$itemid]->position + 1);
            }
            return;
        }
        $this->moveitemto($itemid, $this->items[$itemid]->position + 1);
    }

    protected function indentitemto($itemid, $indent) {
        if (!isset($this->items[$itemid])) {
            // Not able to indent useritems, as they are always parent + 1.
            return;
        }
        $item = $this->items[$itemid];

        if ($item->position == 1) {
            $indent = 0;
        }

        if ($indent < 0) {
            $indent = 0;
        } else if ($indent > CHECKLIST_MAX_INDENT) {
            $indent = CHECKLIST_MAX_INDENT;
        }

        $oldindent = $item->indent;
        $adjust = $indent - $oldindent;
        if ($adjust == 0) {
            return;
        }
        $item->indent = $indent;
        $item->update();

        // Update all 'children' of this item to new indent.
        foreach ($this->items as $it) {
            if ($it->position > $item->position) {
                if ($it->indent > $oldindent) {
                    $it->indent += $adjust;
                    $it->update();
                } else {
                    break;
                }
            }
        }
    }

    protected function indentitem($itemid) {
        if (!isset($this->items[$itemid])) {
            // Not able to indent useritems, as they are always parent + 1.
            return;
        }
        $this->indentitemto($itemid, $this->items[$itemid]->indent + 1);
    }

    protected function unindentitem($itemid) {
        if (!isset($this->items[$itemid])) {
            // Not able to indent useritems, as they are always parent + 1.
            return;
        }
        $this->indentitemto($itemid, $this->items[$itemid]->indent - 1);
    }

    protected function makeoptional($itemid, $optional, $heading = false) {
        global $DB;

        if (!isset($this->items[$itemid])) {
            return;
        }
        $item = $this->items[$itemid];

        if ($heading) {
            $optional = CHECKLIST_OPTIONAL_HEADING;
        } else if ($optional) {
            $optional = CHECKLIST_OPTIONAL_YES;
        } else {
            $optional = CHECKLIST_OPTIONAL_NO;
        }

        if ($item->moduleid) {
            if ($item->is_heading()) {
                return; // Topic headings must stay as headings.
            } else if ($item->itemoptional == CHECKLIST_OPTIONAL_YES) {
                $optional = CHECKLIST_OPTIONAL_NO; // Module links cannot become headings.
            } else {
                $optional = CHECKLIST_OPTIONAL_YES;
            }
        }

        $item->itemoptional = $optional;
        $item->update();
    }

    protected function nextcolour($itemid) {
        if (!isset($this->items[$itemid])) {
            return;
        }
        $item = $this->items[$itemid];

        switch ($item->colour) {
            case 'black':
                $nextcolour = 'red';
                break;
            case 'red':
                $nextcolour = 'orange';
                break;
            case 'orange':
                $nextcolour = 'green';
                break;
            case 'green':
                $nextcolour = 'purple';
                break;
            default:
                $nextcolour = 'black';
        }

        $item->colour = $nextcolour;
        $item->update();
    }

    public function ajaxupdatechecks($changechecks) {
        // Convert array of itemid=>true/false, into array of all 'checked' itemids.
        $newchecks = array();
        foreach ($this->items as $item) {
            if (array_key_exists($item->id, $changechecks)) {
                if ($changechecks[$item->id]) {
                    // Include in array if new status is true.
                    $newchecks[] = $item->id;
                }
            } else {
                // If no new status, include in array if checked.
                if ($item->is_checked_student()) {
                    $newchecks[] = $item->id;
                }
            }
        }
        if ($this->useritems) {
            foreach ($this->useritems as $item) {
                if (array_key_exists($item->id, $changechecks)) {
                    if ($changechecks[$item->id]) {
                        // Include in array if new status is true.
                        $newchecks[] = $item->id;
                    }
                } else {
                    // If no new status, include in array if checked.
                    if ($item->is_checked_student()) {
                        $newchecks[] = $item->id;
                    }
                }
            }
        }

        $this->updatechecks($newchecks);
    }

    protected function updatechecks($newchecks) {
        if (!is_array($newchecks)) {
            // Something has gone wrong, so update nothing.
            return;
        }

        $params = array(
            'contextid' => $this->context->id,
            'objectid' => $this->checklist->id,
        );
        $event = \mod_checklist\event\student_checks_updated::create($params);
        $event->trigger();

        $updategrades = false;
        if ($this->items) {
            foreach ($this->items as $item) {
                if ($this->checklist->autoupdate == CHECKLIST_AUTOUPDATE_YES) {
                    if ($item->moduleid) {
                        continue; // Item linked to course module and autoupdate enabled and cannot override.
                    }
                    if ($item->linkcourseid) {
                        $completion = new completion_info(get_course($item->linkcourseid));
                        if ($completion->is_enabled()) {
                            continue; // Item linked to course and autoupdate enabled and cannot override.
                        }
                    }
                }

                $newval = in_array($item->id, $newchecks);
                if ($item->set_checked_student($this->userid, $newval)) {
                    $updategrades = true;
                }
            }
        }
        if ($updategrades) {
            checklist_update_grades($this->checklist, $this->userid);
        }

        if ($this->useritems) {
            foreach ($this->useritems as $item) {
                $newval = in_array($item->id, $newchecks);
                $item->set_checked_student($this->userid, $newval);
            }
        }
    }

    /**
     * Get a list of items that cannot be updated by teachers (as they are auto updating).
     *
     * @return array
     */
    protected function get_teacher_disabled_items() {
        global $DB;
        $disableitems = [];
        if ($this->checklist->autoupdate == CHECKLIST_AUTOUPDATE_YES &&
            $this->checklist->teacheredit == CHECKLIST_MARKING_TEACHER
        ) {
            // Checklist is auto updating + only showing teacher marks => disable teacher marks for items that auto update.
            $select = "checklist = ? AND userid = 0 AND
                          ((moduleid IS NOT NULL AND moduleid > 0) OR (linkcourseid IS NOT NULL AND linkcourseid > 0))";
            $autoitems = $DB->get_records_select('checklist_item', $select, [$this->checklist->id]);
            foreach ($autoitems as $autoitem) {
                if ($autoitem->moduleid) {
                    $disableitems[] = $autoitem->id;
                } else if ($autoitem->linkcourseid) {
                    $completion = new completion_info(get_course($autoitem->linkcourseid));
                    if ($completion->is_enabled()) {
                        $disableitems[] = $autoitem->id;
                    }
                }
            }
        }
        return $disableitems;
    }

    protected function updateteachermarks() {
        global $USER, $DB;

        $newchecks = optional_param_array('items', array(), PARAM_TEXT);
        if (!is_array($newchecks)) {
            // Something has gone wrong, so update nothing.
            return;
        }

        if ($this->checklist->teacheredit != CHECKLIST_MARKING_STUDENT) {
            if (!$DB->record_exists('user', ['id' => $this->userid])) {
                error('No such user!');
            }
            $params = array(
                'contextid' => $this->context->id,
                'objectid' => $this->checklist->id,
                'relateduserid' => $this->userid,
            );
            $event = \mod_checklist\event\teacher_checks_updated::create($params);
            $event->trigger();

            $teachermarklocked = $this->checklist->lockteachermarks
                && !has_capability('mod/checklist:updatelocked', $this->context);

            $this->update_teachermarks($newchecks, $USER->id, $teachermarklocked);
        }

        $newcomments = optional_param_array('teachercomment', false, PARAM_TEXT);
        if (!$this->checklist->teachercomments || !$newcomments || !is_array($newcomments)) {
            return;
        }

        list($isql, $iparams) = $DB->get_in_or_equal(array_keys($this->items));
        $commentsunsorted = $DB->get_records_select('checklist_comment', "userid = ? AND itemid $isql",
                                                    array_merge(array($this->userid), $iparams));
        $comments = array();
        foreach ($commentsunsorted as $comment) {
            $comments[$comment->itemid] = $comment;
        }
        foreach ($newcomments as $itemid => $newcomment) {
            $newcomment = trim($newcomment);
            if ($newcomment == '') {
                if (array_key_exists($itemid, $comments)) {
                    $DB->delete_records('checklist_comment', array('id' => $comments[$itemid]->id));
                    unset($comments[$itemid]); // Should never be needed, but just in case...
                }
            } else {
                if (array_key_exists($itemid, $comments)) {
                    if ($comments[$itemid]->text != $newcomment) {
                        $updatecomment = new stdClass;
                        $updatecomment->id = $comments[$itemid]->id;
                        $updatecomment->userid = $this->userid;
                        $updatecomment->itemid = $itemid;
                        $updatecomment->commentby = $USER->id;
                        $updatecomment->text = $newcomment;

                        $DB->update_record('checklist_comment', $updatecomment);
                    }
                } else {
                    $addcomment = new stdClass;
                    $addcomment->itemid = $itemid;
                    $addcomment->userid = $this->userid;
                    $addcomment->commentby = $USER->id;
                    $addcomment->text = $newcomment;

                    $DB->insert_record('checklist_comment', $addcomment);
                }
            }
        }
    }

    /**
     * Public to allow use in Behat tests.
     *
     * @param int[] $newchecks maps itemid => teachermark
     * @param int $teacherid userid of the teacher doing the update
     * @param bool $teachermarklocked (optional) set to true to prevent teachers from changing 'yes' to 'no'.
     */
    public function update_teachermarks($newchecks, $teacherid, $teachermarklocked = false) {
        $disableditems = $this->get_teacher_disabled_items();
        $updategrades = false;
        foreach ($newchecks as $itemid => $newval) {
            if (isset($this->items[$itemid])) {
                $item = $this->items[$itemid];

                if ($teachermarklocked && $item->is_checked_teacher()) {
                    continue; // Does not have permission to update marks that are already 'Yes'.
                }

                if (in_array($item->id, $disableditems)) {
                    continue; // Item is auto-updating, so we cannot change it.
                }

                if ($item->set_teachermark($this->userid, $newval, $teacherid)) {
                    $updategrades = true;
                }
            }
        }
        if ($updategrades) {
            checklist_update_grades($this->checklist, $this->userid);
        }
    }

    protected function updateallteachermarks() {
        global $USER;

        if ($this->checklist->teacheredit == CHECKLIST_MARKING_STUDENT) {
            // Student only lists do not have teacher marks to update.
            return;
        }

        $userids = optional_param_array('userids', array(), PARAM_INT);
        if (!is_array($userids)) {
            // Something has gone wrong, so update nothing.
            return;
        }

        $disableditems = $this->get_teacher_disabled_items();

        $userchecks = array();
        foreach ($userids as $userid) {
            $checkdata = optional_param_array('items_'.$userid, array(), PARAM_INT);
            if (!is_array($checkdata)) {
                continue;
            }
            foreach ($checkdata as $itemid => $val) {
                if (!$itemid) {
                    continue;
                }
                if (!array_key_exists($itemid, $this->items)) {
                    continue; // Item is not part of this checklist.
                }
                if (!checklist_check::teachermark_valid($val)) {
                    continue; // Invalid value.
                }
                if (in_array($itemid, $disableditems)) {
                    continue; // Cannot update autoupdate item.
                }
                if (!array_key_exists($userid, $userchecks)) {
                    $userchecks[$userid] = array();
                }
                $userchecks[$userid][$itemid] = $val;
            }
        }

        if (empty($userchecks)) {
            return;
        }

        $teachermarklocked = $this->checklist->lockteachermarks && !has_capability('mod/checklist:updatelocked', $this->context);

        foreach ($userchecks as $userid => $items) {
            $currentchecks = checklist_check::fetch_by_userid_itemids($userid, array_keys($items));
            $updategrades = false;
            foreach ($items as $itemid => $val) {
                if (!array_key_exists($itemid, $currentchecks)) {
                    if ($val == CHECKLIST_TEACHERMARK_UNDECIDED) {
                        continue; // Do not create an entry for blank marks.
                    }

                    // No entry for this item - need to create it.
                    $newcheck = new checklist_check(['item' => $itemid, 'userid' => $userid], false);
                    $newcheck->set_teachermark($val, $USER->id);
                    $newcheck->save();

                    $updategrades = true;

                } else {
                    $current = $currentchecks[$itemid];
                    if ($current->teachermark != $val) {
                        if ($teachermarklocked && $current->teachermark == CHECKLIST_TEACHERMARK_YES) {
                            continue;
                        }

                        // Update the existing item.
                        $current->set_teachermark($val, $USER->id);
                        $current->save();

                        $updategrades = true;
                    }
                }
            }
            if ($updategrades) {
                $params = array(
                    'contextid' => $this->context->id,
                    'objectid' => $this->checklist->id,
                    'relateduserid' => $userid,
                );
                $event = \mod_checklist\event\teacher_checks_updated::create($params);
                $event->trigger();

                checklist_update_grades($this->checklist, $userid);
            }
        }
    }

    /**
     * Update all automatically-completing checklist items
     *
     * @param bool $updmodules update items related to course modules
     * @param bool $updcourses update items related to courses
     * @throws coding_exception
     */
    public function update_all_autoupdate_checks($updmodules = true, $updcourses = true) {
        global $DB;

        if (!$this->checklist->autoupdate) {
            return;
        }

        if (!$updmodules && !$updcourses) {
            throw new coding_exception('Must specify module update and/or course update');
        }

        if (get_config('mod_checklist', 'onlyenrolled')) {
            $users = get_enrolled_users($this->context, 'mod/checklist:updateown', 0, 'u.id', null, 0, 0, true);
        } else {
            $users = get_users_by_capability($this->context, 'mod/checklist:updateown', 'u.id', '', '', '', '', '', false);
        }
        if (!$users) {
            return;
        }
        $userids = array_keys($users);

        $teachermark = ($this->checklist->teacheredit == CHECKLIST_MARKING_TEACHER);

        // Update all checklist items that are linked to course modules.
        if ($updmodules) {
            // Get a list of all the checklist items with a module linked to them (ignoring headings).
            $sql = "SELECT cm.id AS cmid, m.name AS mod_name, i.id AS itemid, cm.completion AS completion
        FROM {modules} m, {course_modules} cm, {checklist_item} i
        WHERE m.id = cm.module AND cm.id = i.moduleid AND i.moduleid > 0 AND i.checklist = ? AND i.itemoptional != 2";

            $completion = new completion_info($this->course);
            $usingcompletion = $completion->is_enabled();

            $items = $DB->get_records_sql($sql, array($this->checklist->id));
            foreach ($items as $item) {
                if ($usingcompletion && $item->completion) {
                    $fakecm = new stdClass();
                    $fakecm->id = $item->cmid;

                    foreach ($users as $user) {
                        $compdata = $completion->get_data($fakecm, false, $user->id);
                        if ($compdata->completionstate == COMPLETION_COMPLETE
                            || $compdata->completionstate == COMPLETION_COMPLETE_PASS
                        ) {
                            $check = new checklist_check(['item' => $item->itemid, 'userid' => $user->id]);
                            if ($teachermark) {
                                if (!$check->is_checked_teacher()) {
                                    $check->set_teachermark(CHECKLIST_TEACHERMARK_YES, null);
                                    $check->save();
                                }
                            } else {
                                if (!$check->is_checked_student()) {
                                    $check->set_checked_student(true);
                                    $check->save();
                                }
                            }
                        }
                    }
                    continue;
                }

                $loguserids = mod_checklist\local\autoupdate::get_logged_userids($item->mod_name, $item->cmid, $userids);
                if (!$loguserids) {
                    continue;
                }

                foreach ($loguserids as $loguserid) {
                    $check = new checklist_check(['item' => $item->itemid, 'userid' => $loguserid]);
                    if ($teachermark) {
                        if (!$check->is_checked_teacher()) {
                            $check->set_teachermark(CHECKLIST_TEACHERMARK_YES, null);
                            $check->save();
                        }
                    } else {
                        if (!$check->is_checked_student()) {
                            $check->set_checked_student(true);
                            $check->save();
                        }
                    }
                }
            }
        }

        // Update all checklist items that are linked to courses.
        if ($updcourses && $this->can_link_courses()) {
            $sql = "SELECT i.id, i.linkcourseid
                      FROM {checklist_item} i
                      JOIN {course} c ON c.id = i.linkcourseid
                     WHERE i.checklist = :checklistid AND i.itemoptional <> :heading AND c.enablecompletion = 1";
            $params = ['checklistid' => $this->checklist->id, 'heading' => CHECKLIST_OPTIONAL_HEADING];
            $items = $DB->get_records_sql($sql, $params);

            foreach ($items as $item) {
                $this->update_course_completion_for_item($item, $userids);
            }
        }

        // Always update the grades.
        checklist_update_grades($this->checklist);
    }

    /**
     * For the given item (which must contain id + courselinkid), look for any
     * course completions and check-off for those users
     * (Note: does not uncheck items for users who have not completed the course)
     * @param object $item
     * @param int[] $userids (optional) if provided, then the userids do not need to be loaded from the DB
     */
    protected function update_course_completion_for_item($item, $userids = null) {
        global $DB;

        if (!$this->checklist->autoupdate) {
            return; // Automatic updates disabled for this checklist.
        }
        if (!$item->linkcourseid) {
            return; // Not linked to a course, so nothing to do.
        }

        if ($userids === null) {
            // Userids not provided, so load them by capability.
            if (get_config('mod_checklist', 'onlyenrolled')) {
                $users = get_enrolled_users($this->context, 'mod/checklist:updateown', 0, 'u.id', null, 0, 0, true);
            } else {
                $users = get_users_by_capability($this->context, 'mod/checklist:updateown', 'u.id', '', '', '', '', '', false);
            }
            if (!$users) {
                return;
            }
            $userids = array_keys($users);
        }
        $teachermark = ($this->checklist->teacheredit == CHECKLIST_MARKING_TEACHER);

        // Generate a list of users who have completed the given course.
        list($usql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params['courseid'] = $item->linkcourseid;
        $select = "course = :courseid AND userid $usql AND timecompleted > 0";
        $completions = $DB->get_records_select('course_completions', $select, $params, '', 'userid, timecompleted');

        // Mark the checklist item as complete for these users.
        foreach ($completions as $completion) {
            $check = new checklist_check(['item' => $item->id, 'userid' => $completion->userid]);
            if ($teachermark) {
                if (!$check->is_checked_teacher()) {
                    $check->set_teachermark(CHECKLIST_TEACHERMARK_YES, null);
                    $check->save();
                }
            } else {
                if (!$check->is_checked_student()) {
                    $check->set_checked_student(true);
                    $check->save();
                }
            }
        }
    }

    // Update the userid to point to the next user to view.
    protected function getnextuserid() {
        global $DB;

        $activegroup = groups_get_activity_group($this->cm, true);
        $settings = $this->get_report_settings();
        switch ($settings->sortby) {
            case 'firstdesc':
                $orderby = 'ORDER BY u.firstname DESC';
                break;

            case 'lastasc':
                $orderby = 'ORDER BY u.lastname';
                break;

            case 'lastdesc':
                $orderby = 'ORDER BY u.lastname DESC';
                break;

            default:
                $orderby = 'ORDER BY u.firstname';
                break;
        }

        $ausers = false;
        if (get_config('mod_checklist', 'onlyenrolled')) {
            $users = get_enrolled_users($this->context, 'mod/checklist:updateown', $activegroup, 'u.id', null, 0, 0, true);
        } else {
            $users = get_users_by_capability($this->context, 'mod/checklist:updateown', 'u.id', '', '', '',
                                             $activegroup, '', false);
        }
        if (!$users) {
            $users = array_keys($users);
            if ($this->only_view_mentee_reports()) {
                $users = $this->filter_mentee_users($users);
            }
            if (!empty($users)) {
                list($usql, $uparams) = $DB->get_in_or_equal($users);
                $ausers = $DB->get_records_sql('SELECT u.id FROM {user} u WHERE u.id '.$usql.$orderby, $uparams);
            }
        }

        $stoponnext = false;
        foreach ($ausers as $user) {
            if ($stoponnext) {
                $this->userid = $user->id;
                return;
            }
            if ($user->id == $this->userid) {
                $stoponnext = true;
            }
        }
        $this->userid = false;
    }

    public static function print_user_progressbar($checklistid, $userid, $width = '300px', $showpercent = true,
                                                  $return = false, $hidecomplete = false) {
        list($ticked, $total) = self::get_user_progress($checklistid, $userid);
        if (!$total) {
            return '';
        }
        if ($hidecomplete && ($ticked == $total)) {
            return '';
        }

        $output = self::get_renderer();

        $out = $output->progress_bar_external($total, $ticked, $width, $showpercent);
        if ($return) {
            return $out;
        }

        echo $out;
        return '';
    }

    public static function get_user_progress($checklistid, $userid) {
        global $DB;

        $userid = intval($userid); // Just to be on the safe side...

        $checklist = $DB->get_record('checklist', array('id' => $checklistid));
        if (!$checklist) {
            return array(false, false);
        }
        $groupingsql = self::get_grouping_sql($userid, $checklist->course);
        $select = "checklist = ? AND userid = 0 AND itemoptional = ".CHECKLIST_OPTIONAL_NO."
                      AND hidden = ".CHECKLIST_HIDDEN_NO." AND $groupingsql";
        $items = $DB->get_records_select('checklist_item', $select, array($checklist->id),
                                         '', 'id');
        if (empty($items)) {
            return array(false, false);
        }
        $total = count($items);
        list($isql, $iparams) = $DB->get_in_or_equal(array_keys($items));
        $params = array_merge(array($userid), $iparams);

        $sql = "userid = ? AND item $isql AND ";
        if ($checklist->teacheredit == CHECKLIST_MARKING_STUDENT) {
            $sql .= 'usertimestamp > 0';
        } else {
            $sql .= 'teachermark = '.CHECKLIST_TEACHERMARK_YES;
        }
        $ticked = $DB->count_records_select('checklist_check', $sql, $params);

        return array($ticked, $total);
    }

    public static function get_user_groupings($userid, $courseid) {
        global $DB;
        $sql = "SELECT DISTINCT gg.groupingid
                  FROM ({groups} g JOIN {groups_members} gm ON g.id = gm.groupid)
                  JOIN {groupings_groups} gg ON gg.groupid = g.id
                  WHERE gm.userid = ? AND g.courseid = ? ";
        $groupings = $DB->get_records_sql($sql, array($userid, $courseid));
        if ($groupings) {
            return array_keys($groupings);
        }
        return array();
    }

    /**
     * Used to support Behat testing.
     *
     * @param string $itemname
     * @param int $strictness (optional) defaults to throwing an exception if the item is missing
     * @return int|null
     * @throws dml_missing_record_exception
     */
    public function get_itemid_by_name($itemname, $strictness = MUST_EXIST) {
        foreach ($this->items as $item) {
            if ($item->displaytext == $itemname) {
                return $item->id;
            }
        }
        foreach ($this->useritems as $item) {
            if ($item->displaytext == $itemname) {
                return $item->id;
            }
        }
        if ($strictness == MUST_EXIST) {
            // OK - not actually failed to get the record, but if we've not found it then it is missing in the DB.
            throw new dml_missing_record_exception('checklist_item', 'displayname = ?', array($itemname));
        }
        return null;
    }

    /**
     * Get a list of courses that checklist items could be linked to.
     * @return string[] $courseid => $coursename
     */
    public static function get_linkable_courses() {
        global $DB, $SITE;
        $courses = $DB->get_records_select_menu('course', 'id <> ? AND visible = 1', [$SITE->id], 'fullname', 'id, fullname');
        return $courses;
    }

    /**
     * Generate the SQL fragment needed to restrict items to those that are in the
     * same grouping as the current user (always includes items not in any grouping).
     *
     * @param int $userid
     * @param int $courseid
     * @param string $prefix (optional) e.g. 'item.'
     * @return string
     */
    public static function get_grouping_sql($userid, $courseid, $prefix = '') {
        $groupings = self::get_user_groupings($userid, $courseid);
        if ($groupings) {
            $groupings[] = 0;
            $groupingsql = " {$prefix}grouping IN (".implode(',', $groupings).') ';
        } else {
            $groupingsql = " {$prefix}grouping = 0 ";
        }
        return $groupingsql;
    }

    public static function get_course_groupings($courseid) {
        static $allgroupings = [];

        if (!isset($allgroupings[$courseid])) {
            $groupings = groups_get_all_groupings($courseid);
            $ret = [];
            foreach ($groupings as $grouping) {
                $ret[$grouping->id] = $grouping->name;
            }
            $allgroupings[$courseid] = $ret;
        }
        return $allgroupings[$courseid];
    }
}
