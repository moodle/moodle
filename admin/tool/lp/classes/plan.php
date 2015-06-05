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
 * Class for plans persistence.
 *
 * @package    tool_lp
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;

use stdClass;
use context_user;

/**
 * Class for loading/storing plans from the DB.
 *
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plan extends persistent {

    const STATUS_DRAFT = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_COMPLETE = 2;

    /** @var string $name Name */
    private $name = '';

    /** @var string $description Description for this learning plan */
    private $description = '';

    /** @var int $descriptionformat Format for the description */
    private $descriptionformat = FORMAT_MOODLE;

    /** @var int $userid */
    private $userid = null;

    /** @var bool $templateid */
    private $templateid = null;

    /** @var bool $status The plan status, one of the 3 \tool_lp\plan:STATUS_* constants */
    private $status = null;

    /** @var bool $duedate */
    private $duedate = null;

    /**
     * Method that provides the table name matching this class.
     *
     * @return string
     */
    public function get_table_name() {
        return 'tool_lp_plan';
    }

    public function get_name() {
        return $this->name;
    }

    public function set_name($value) {
        $this->name = $value;
    }

    public function get_description() {
        return $this->description;
    }

    public function set_description($value) {
        $this->description = $value;
    }

    public function get_descriptionformat() {
        return $this->descriptionformat;
    }

    public function set_descriptionformat($value) {
        $this->descriptionformat = $value;
    }

    public function get_userid() {
        return $this->userid;
    }

    public function set_userid($value) {
        $this->userid = $value;
    }

    public function get_templateid() {
        return $this->templateid;
    }

    public function set_templateid($value) {
        $this->templateid = $value;
    }

    public function get_status() {
        if ($this->status === null) {
            return null;
        }

        return (int)$this->status;
    }

    public function set_status($value) {
        $this->status = $value;
    }

    public function get_duedate() {
        return $this->duedate;
    }

    public function set_duedate($value) {
        $this->duedate = $value;
    }

    // Extra methods.


    /**
     * Human readable status name.
     *
     * @return void
     */
    public function get_statusname() {

        $status = $this->get_status();

        switch ($status) {
            case self::STATUS_DRAFT:
                $strname = 'draft';
                break;
            case self::STATUS_ACTIVE:
                $strname = 'active';
                break;
            case self::STATUS_COMPLETE:
                $strname = 'complete';
                break;
            default:
                throw moodle_exception('errorplanstatus', 'tool_lp', '', $status);
                break;
        }

        return get_string('planstatus' . $strname, 'tool_lp');
    }

    /**
     * Whether the current user can update the learning plan.
     *
     * @return void
     */
    public function get_usercanupdate() {
        global $USER;

        // Null if the record has not been filled.
        if (!$userid = $this->get_userid()) {
            return null;
        }

        $context = context_user::instance($userid);

        // Not all users can edit all plans, the template should know about it.
        if (has_capability('tool/lp:planmanageall', $context) ||
                has_capability('tool/lp:planmanageown', $context)) {
            return true;

        }

        // The user that created the template can also edit it if he was the last one that modified it. But
        // can't do it if it is already completed.
        if ($USER->id == $userid && $this->get_usermodified() == $USER->id && $this->get_status() != plan::STATUS_COMPLETE) {
            return true;
        }

        return false;
    }

    /**
     * Converts the object to a standard PHP object.
     *
     * If it is used to insert/update into DB the extra fields like statusname will be
     * ignored, they are useful though when passing the object to templates.
     *
     * @return void
     */
    public function to_record() {

        $record = new stdClass();
        $record->id = $this->get_id();
        $record->name = $this->get_name();
        $record->description = $this->get_description();
        $record->descriptionformat = $this->get_descriptionformat();
        $record->userid = $this->get_userid();
        $record->templateid = $this->get_templateid();
        $record->status = $this->get_status();
        $record->duedate = $this->get_duedate();
        $record->timecreated = $this->get_timecreated();
        $record->timemodified = $this->get_timemodified();
        $record->usermodified = $this->get_usermodified();

        // Extra data.
        $record->statusname = $this->get_statusname();
        $record->usercanupdate = $this->get_usercanupdate();

        return $record;
    }

    public function from_record($record) {
        if (isset($record->id)) {
            $this->set_id($record->id);
        }
        if (isset($record->name)) {
            $this->set_name($record->name);
        }
        if (isset($record->description)) {
            $this->set_description($record->description);
        }
        if (isset($record->descriptionformat)) {
            $this->set_descriptionformat($record->descriptionformat);
        }
        if (isset($record->userid)) {
            $this->set_userid($record->userid);
        }
        if (isset($record->templateid)) {
            $this->set_templateid($record->templateid);
        }
        if (isset($record->status)) {
            $this->set_status($record->status);
        }
        if (isset($record->duedate)) {
            $this->set_duedate($record->duedate);
        }
        if (isset($record->timecreated)) {
            $this->set_timecreated($record->timecreated);
        }
        if (isset($record->timemodified)) {
            $this->set_timemodified($record->timemodified);
        }
        if (isset($record->usermodified)) {
            $this->set_usermodified($record->usermodified);
        }
    }
}
