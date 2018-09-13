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
 * Stores fields that define the status of the checklist output
 *
 * @package   mod_checklist
 * @copyright 2016 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_checklist\local;

defined('MOODLE_INTERNAL') || die();

class output_status {
    // All output.
    protected $additemafter = 0;

    // View items only.
    protected $viewother = false;
    protected $userreport = false;
    protected $teachercomments = false;
    protected $editcomments = false;
    protected $teachermarklocked = false;
    protected $showcompletiondates = false;
    protected $canupdateown = false;
    protected $canaddown = false;
    protected $addown = false;
    protected $showprogressbar = false;
    protected $showteachermark = false;
    protected $showcheckbox = false;
    protected $overrideauto = false;
    protected $checkgroupings = false;
    protected $updateform = false;

    // Edit items only.
    protected $editdates = false;
    protected $editlinks = false;
    protected $allowcourselinks = false;
    protected $itemid = null;
    protected $autopopulate = false;
    protected $autoupdatewarning = null;
    protected $editgrouping = false;
    protected $courseid = null;

    /**
     * Viewing another user (i.e. teacher report about a single user)
     * @return boolean
     */
    public function is_viewother() {
        return $this->viewother;
    }

    /**
     * @param boolean $viewother
     */
    public function set_viewother($viewother) {
        $this->viewother = $viewother;
    }

    /**
     * Viewing complete user report (so no updating of checkmarks)
     * @return boolean
     */
    public function is_userreport() {
        return $this->userreport;
    }

    /**
     * @param boolean $userreport
     */
    public function set_userreport($userreport) {
        $this->userreport = $userreport;
    }

    /**
     * Are teacher comments enabled for this instance?
     * @return boolean
     */
    public function is_teachercomments() {
        return $this->teachercomments;
    }

    /**
     * @param boolean $teachercomments
     */
    public function set_teachercomments($teachercomments) {
        $this->teachercomments = $teachercomments;
    }

    /**
     * Is the user editing comments at the moment?
     * @return boolean
     */
    public function is_editcomments() {
        return $this->editcomments;
    }

    /**
     * @param boolean $editcomments
     */
    public function set_editcomments($editcomments) {
        $this->editcomments = $editcomments;
    }

    /**
     * Are completed teacher marks locked (so the current user can't update them)?
     * @return boolean
     */
    public function is_teachermarklocked() {
        return $this->teachermarklocked;
    }

    /**
     * @param boolean $teachermarklocked
     */
    public function set_teachermarklocked($teachermarklocked) {
        $this->teachermarklocked = $teachermarklocked;
    }

    /**
     * Should the completion dates be output?
     * @return boolean
     */
    public function is_showcompletiondates() {
        return $this->showcompletiondates;
    }

    /**
     * @param boolean $showcompletiondates
     */
    public function set_showcompletiondates($showcompletiondates) {
        $this->showcompletiondates = $showcompletiondates;
    }

    /**
     * Can the user update their own checkmarks (students).
     * @return boolean
     */
    public function is_canupdateown() {
        return $this->canupdateown;
    }

    /**
     * @param boolean $canupdateown
     */
    public function set_canupdateown($canupdateown) {
        $this->canupdateown = $canupdateown;
    }

    /**
     * Should the progress bar be shown?
     * @return boolean
     */
    public function is_showprogressbar() {
        return $this->showprogressbar;
    }

    /**
     * @param boolean $showprogressbar
     */
    public function set_showprogressbar($showprogressbar) {
        $this->showprogressbar = $showprogressbar;
    }

    /**
     * Should the teacher mark be shown?
     * @return boolean
     */
    public function is_showteachermark() {
        return $this->showteachermark;
    }

    /**
     * @param boolean $showteachermark
     */
    public function set_showteachermark($showteachermark) {
        $this->showteachermark = $showteachermark;
    }

    /**
     * Should the student mark be shown?
     * @return boolean
     */
    public function is_showcheckbox() {
        return $this->showcheckbox;
    }

    /**
     * @param boolean $showcheckbox
     */
    public function set_showcheckbox($showcheckbox) {
        $this->showcheckbox = $showcheckbox;
    }

    /**
     * Can the user override automatically-calculated checkbox items (linked to activity completion)?
     * @return boolean
     */
    public function is_overrideauto() {
        return $this->overrideauto;
    }

    /**
     * @param boolean $overrideauto
     */
    public function set_overrideauto($overrideauto) {
        $this->overrideauto = $overrideauto;
    }

    /**
     * Should items be checked against groupings, for visibility purposes?
     * @return boolean
     */
    public function is_checkgroupings() {
        return $this->checkgroupings;
    }

    /**
     * @param boolean $checkgroupings
     */
    public function set_checkgroupings($checkgroupings) {
        $this->checkgroupings = $checkgroupings;
    }

    /**
     * Can the student add their own items?
     * @return boolean
     */
    public function is_canaddown() {
        return $this->canaddown;
    }

    /**
     * @param boolean $canaddown
     */
    public function set_canaddown($canaddown) {
        $this->canaddown = $canaddown;
    }

    /**
     * Is the user currently adding/editing their own items?
     * @return boolean
     */
    public function is_addown() {
        return $this->addown;
    }

    /**
     * @param boolean $addown
     */
    public function set_addown($addown) {
        $this->addown = $addown;
    }

    /**
     * Output 'add item' fields after this item.
     * @return int
     */
    public function get_additemafter() {
        return $this->additemafter;
    }

    /**
     * @param int $additemafter
     */
    public function set_additemafter($additemafter) {
        $this->additemafter = $additemafter;
    }

    /**
     * Should an update form be output?
     * @return boolean
     */
    public function is_updateform() {
        return $this->updateform;
    }

    /**
     * @param boolean $updateform
     */
    public function set_updateform($updateform) {
        $this->updateform = $updateform;
    }

    /**
     * Is date editing enabled?
     * @return boolean
     */
    public function is_editdates() {
        return $this->editdates;
    }

    /**
     * @param boolean $editdates
     */
    public function set_editdates($editdates) {
        $this->editdates = $editdates;
    }

    /**
     * The ID of the item being edited (to generate the correct URLs).
     * @return int|null
     */
    public function get_itemid() {
        return $this->itemid;
    }

    /**
     * @param null $itemid
     */
    public function set_itemid($itemid) {
        $this->itemid = $itemid;
    }

    /**
     * @return boolean
     */
    public function is_autopopulate() {
        return $this->autopopulate;
    }

    /**
     * @param boolean $autopopulate
     */
    public function set_autopopulate($autopopulate) {
        $this->autopopulate = $autopopulate;
    }

    /**
     * Should the autoupdate warning be shown and, if so, what type?
     * @return int|null
     */
    public function get_autoupdatewarning() {
        return $this->autoupdatewarning;
    }

    public function is_autoupdatewarning() {
        return ($this->autoupdatewarning !== null);
    }

    /**
     * @param boolean $autoupdatewarning
     */
    public function set_autoupdatewarning($autoupdatewarning) {
        $this->autoupdatewarning = $autoupdatewarning;
    }

    /**
     * @return boolean
     */
    public function is_editlinks() {
        return $this->editlinks;
    }

    /**
     * @param boolean $editlinks
     */
    public function set_editlinks($editlinks) {
        $this->editlinks = $editlinks;
    }

    /**
     * @return boolean
     */
    public function is_allowcourselinks() {
        return $this->allowcourselinks;
    }

    /**
     * @param boolean $allowcourselinks
     */
    public function set_allowcourselinks($allowcourselinks) {
        $this->allowcourselinks = $allowcourselinks;
    }

    /**
     * @return boolean
     */
    public function is_editgrouping() {
        return $this->editgrouping;
    }

    /**
     * @param boolean $editgrouping
     */
    public function set_editgrouping($editgrouping) {
        $this->editgrouping = $editgrouping;
    }

    /**
     * @return int
     */
    public function get_courseid() {
        if (!$this->courseid) {
            throw new \coding_exception('No courseid set');
        }
        return $this->courseid;
    }

    /**
     * @param int $courseid
     */
    public function set_courseid($courseid) {
        $this->courseid = $courseid;
    }
}