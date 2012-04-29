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
 * Code for the submissions allocation support is defined here
 *
 * @package    mod
 * @subpackage workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Allocators are responsible for assigning submissions to reviewers for assessments
 *
 * The task of the allocator is to assign the correct number of submissions to reviewers
 * for assessment. Several allocation methods are expected and they can be combined. For
 * example, teacher can allocate several submissions manually (by 'manual' allocator) and
 * then let the other submissions being allocated randomly (by 'random' allocator).
 * Allocation is actually done by creating an initial assessment record in the
 * workshop_assessments table.
 */
interface workshop_allocator {

    /**
     * Initialize the allocator and eventually process submitted data
     *
     * This method is called soon after the allocator is constructed and before any output
     * is generated. Therefore it may process any data submitted and do other tasks.
     * It must not produce any output.
     *
     * @throws moodle_exception
     * @return workshop_allocation_result
     */
    public function init();

    /**
     * Print HTML to be displayed as the user interface
     *
     * If a form is part of the UI, the caller should have called $PAGE->set_url(...)
     *
     * @param stdClass $wsoutput workshop module renderer can be used
     * @return string HTML code to be echoed
     */
    public function ui();

    /**
     * Delete all data related to a given workshop module instance
     *
     * This is called from {@link workshop_delete_instance()}.
     *
     * @param int $workshopid id of the workshop module instance being deleted
     * @return void
     */
    public static function delete_instance($workshopid);
}


/**
 * Stores the information about the allocation process
 *
 * Allocator's method init() returns instance of this class.
 */
class workshop_allocation_result implements renderable {

    /** the init() called successfully but no actual allocation was done */
    const STATUS_VOID           = 0;
    /** allocation was successfully executed */
    const STATUS_EXECUTED       = 1;
    /** a serious error has occurred during the allocation (as a hole) */
    const STATUS_FAILED         = 2;
    /** scheduled allocation was configured (to be executed later, for example) */
    const STATUS_CONFIGURED     = 3;

    /** @var workshop_allocator the instance of the allocator that produced this result */
    protected $allocator;
    /** @var null|int the status of the init() call */
    protected $status = null;
    /** @var null|string optional result message to display */
    protected $message = null;
    /** @var int the timestamp of when the allocation process started */
    protected $timestart = null;
    /** @var int the timestamp of when the final status was set */
    protected $timeend = null;
    /** @var array of log message objects, {@see self::log()} */
    protected $logs = array();

    /**
     * Creates new instance of the object
     *
     * @param workshop_allocator $allocator
     */
    public function __construct(workshop_allocator $allocator) {
        $this->allocator = $allocator;
        $this->timestart = time();
    }

    /**
     * Sets the result status of the allocation
     *
     * @param int $status the status code, eg {@link self::STATUS_OK}
     * @param string $message optional status message
     */
    public function set_status($status, $message = null) {
        $this->status = $status;
        $this->message = is_null($message) ? $this->message : $message;
        $this->timeend = time();
    }

    /**
     * @return int|null the result status
     */
    public function get_status() {
        return $this->status;
    }

    /**
     * @return string|null status message
     */
    public function get_message() {
        return $this->message;
    }

    /**
     * @return int|null the timestamp of when the final status was set
     */
    public function get_timeend() {
        return $this->timeend;
    }

    /**
     * Appends a new message to the log
     *
     * The available levels are
     *  ok - success, eg. new allocation was created
     *  info - informational message
     *  error - error message, eg. no more peers available
     *  debug - debugging info
     *
     * @param string $message message text to display
     * @param string $type the type of the message
     * @param int $indent eventual indentation level (the message is related to the previous one with the lower indent)
     */
    public function log($message, $type = 'ok', $indent = 0) {
        $log = new stdClass();
        $log->message = $message;
        $log->type = $type;
        $log->indent = $indent;

        $this->logs[] = $log;
    }

    /**
     * Returns list of logged messages
     *
     * Each object in the list has public properties
     *  message string, text to display
     *  type string, the type of the message
     *  indent int, indentation level
     *
     * @see self::log()
     * @return array of log objects
     */
    public function get_logs() {
        return $this->logs;
    }
}
