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
 * Assignment upgrade tool library functions
 *
 * @package    tool_assignmentupgrade
 * @copyright  2012 NetSpot
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get the URL of a script within this plugin.
 * @param string $script the script name, without .php. E.g. 'index'
 * @param array $params URL parameters (optional)
 * @return moodle_url
 */
function tool_assignmentupgrade_url($script, $params = array()) {
    return new moodle_url('/admin/tool/assignmentupgrade/' . $script . '.php', $params);
}

/**
 * Class to encapsulate the continue / cancel for batch operations
 *
 * @package    tool_assignmentupgrade
 * @copyright  2012 NetSpot
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_assignmentupgrade_batchoperationconfirm implements renderable {
    /** @var string $continuemessage The message to show above the continue cancel buttons */
    public $continuemessage = '';
    /** @var string $continueurl The url to load if the user clicks continue */
    public $continueurl;

    /**
     * Constructor for this class
     * @param stdClass $data - The data from the previous batch form
     */
    function __construct($data) {
        if (isset($data->upgradeselected)) {
            $this->continuemessage = get_string('upgradeselectedcount', 'tool_assignmentupgrade', count(explode(',', $data->selectedassignments)));
            $this->continueurl = new moodle_url('/admin/tool/assignmentupgrade/batchupgrade.php', array('upgradeselected'=>'1', 'confirm'=>'1', 'sesskey'=>sesskey(), 'selected'=>$data->selectedassignments));
        } else if (isset($data->upgradeall)) {
            if (!tool_assignmentupgrade_any_upgradable_assignments()) {
                $this->continuemessage = get_string('noassignmentstoupgrade', 'tool_assignmentupgrade');
                $this->continueurl = '';
            } else {
                $this->continuemessage = get_string('upgradeallconfirm', 'tool_assignmentupgrade');
                $this->continueurl = new moodle_url('/admin/tool/assignmentupgrade/batchupgrade.php', array('upgradeall'=>'1', 'confirm'=>'1', 'sesskey'=>sesskey()));
            }
        }
    }
}


/**
 * Class to encapsulate one of the functionalities that this plugin offers.
 *
 * @package    tool_assignmentupgrade
 * @copyright  2012 NetSpot
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_assignmentupgrade_action {
    /** @var string the name of this action. */
    public $name;
    /** @var moodle_url the URL to launch this action. */
    public $url;
    /** @var string a description of this aciton. */
    public $description;

    /**
     * Constructor to set the fields.
     *
     * In order to create a new tool_assignmentupgrade_action instance you must use the tool_assignmentupgrade_action::make
     * method.
     *
     * @param string $name the name of this action.
     * @param moodle_url $url the URL to launch this action.
     * @param string $description a description of this aciton.
     */
    protected function __construct($name, moodle_url $url, $description) {
        $this->name = $name;
        $this->url = $url;
        $this->description = $description;
    }

    /**
     * Make an action with standard values.
     * @param string $shortname internal name of the action. Used to get strings and build a URL.
     * @param array $params any URL params required.
     * @return tool_assignmentupgrade_action
     */
    public static function make($shortname, $params = array()) {
        return new self(
                get_string($shortname, 'tool_assignmentupgrade'),
                tool_assignmentupgrade_url($shortname, $params),
                get_string($shortname . '_desc', 'tool_assignmentupgrade'));
    }
}

/**
 * Determine if there are any assignments that can be upgraded
 * @return boolean - Are there any assignments that can be upgraded
 */
function tool_assignmentupgrade_any_upgradable_assignments() {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/mod/assign/locallib.php');
    // first find all the unique assignment types
    $types = $DB->get_records_sql('SELECT plugin AS assignmenttype, value AS version FROM {config_plugins} WHERE name = ? AND plugin LIKE ?', array('version', 'assignment_%'));

    $upgradabletypes = array();

    foreach ($types as $assignment) {
        $shorttype = substr($assignment->assignmenttype, strlen('assignment_'));
        if (assign::can_upgrade_assignment($shorttype, $assignment->version)) {
            $upgradabletypes[] = $shorttype;
        }
    }
    $paramlist = '?';
    foreach ($upgradabletypes as $index => $upgradabletype) {
        if ($index > 0) {
            $paramlist .= ', ?';
        }
    }

    $record = $DB->get_record_sql('SELECT COUNT(id) as count from {assignment} where assignmenttype in (' . $paramlist . ')', $upgradabletypes);

    return $record->count > 0;
}

/**
 * Load a list of all the assignmentids that can be upgraded
 * @return array of assignment ids
 */
function tool_assignmentupgrade_load_all_upgradable_assignmentids() {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/mod/assign/locallib.php');
    // first find all the unique assignment types
    $types = $DB->get_records_sql('SELECT plugin AS assignmenttype, value AS version FROM {config_plugins} WHERE name = ? AND plugin LIKE ?', array('version', 'assignment_%'));

    $upgradabletypes = array();

    foreach ($types as $assignment) {
        $shorttype = substr($assignment->assignmenttype, strlen('assignment_'));
        if (assign::can_upgrade_assignment($shorttype, $assignment->version)) {
            $upgradabletypes[] = $shorttype;
        }
    }
    $paramlist = '?';
    foreach ($upgradabletypes as $index => $upgradabletype) {
        if ($index > 0) {
            $paramlist .= ', ?';
        }
    }

    $records = $DB->get_records_sql('SELECT id from {assignment} where assignmenttype in (' . $paramlist . ')', $upgradabletypes);
    $ids = array();
    foreach ($records as $record) {
        $ids[] = $record->id;
    }

    return $ids;
}


/**
 * Convert a list of assignments from the old format to the new one.
 * @param bool $upgradeall - Upgrade all possible assignments
 * @param array $assignmentids An array of assignment ids to upgrade
 * @return array of $entry['assignmentsummary' => (result from tool_assignmentupgrade_get_assignment)
 *                  $entry['success'] => boolean
 *                  $entry['log'] => string - upgrade log
 */
function tool_assignmentupgrade_upgrade_multiple_assignments($upgradeall, $assignmentids) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/assign/locallib.php');
    require_once($CFG->dirroot . '/mod/assign/upgradelib.php');
    $upgrades = array();

    if ($upgradeall) {
        $assignmentids = tool_assignmentupgrade_load_all_upgradable_assignmentids();
    }

    $assignment_upgrader = new assign_upgrade_manager();
    foreach ($assignmentids as $assignmentid) {
        $info = tool_assignmentupgrade_get_assignment($assignmentid);
        if ($info) {
            $log = '';
            $success = $assignment_upgrader->upgrade_assignment($assignmentid, $log);
        } else {
            $success = false;
            $log = get_string('assignmentnotfound', 'tool_assignmentupgrade', $assignmentid);
            $info = new stdClass();
            $info->name = get_string('unknown', 'tool_assignmentupgrade');
            $info->shortname = get_string('unknown', 'tool_assignmentupgrade');
        }

        $upgrades[] = array('assignmentsummary'=>$info, 'success'=>$success, 'log'=>$log);
    }
    return $upgrades;
}

/**
 * Convert a single assignment from the old format to the new one.
 * @param stdClass $assignmentinfo An object containing information about this class
 * @param string $log This gets appended to with the details of the conversion process
 * @return boolean This is the overall result (true/false)
 */
function tool_assignmentupgrade_upgrade_assignment($assignmentinfo, &$log) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/assign/locallib.php');
    require_once($CFG->dirroot . '/mod/assign/upgradelib.php');
    $assignment_upgrader = new assign_upgrade_manager();
    return $assignment_upgrader->upgrade_assignment($assignmentinfo->id, $log);
}

/**
 * Get the information about a assignment to be upgraded.
 * @param int $assignmentid the assignment id.
 * @return stdClass the information about that assignment.
 */
function tool_assignmentupgrade_get_assignment($assignmentid) {
    global $DB;
    return $DB->get_record_sql("
            SELECT a.id, a.name, c.shortname, c.id AS courseid
            FROM {assignment} a
            JOIN {course} c ON c.id = a.course
            WHERE a.id = ?", array($assignmentid));
}

