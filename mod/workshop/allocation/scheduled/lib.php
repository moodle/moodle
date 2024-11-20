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
 * Scheduled allocator that internally executes the random allocation later
 *
 * @package     workshopallocation_scheduled
 * @subpackage  mod_workshop
 * @copyright   2012 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');            // interface definition
require_once(__DIR__ . '/../../locallib.php');    // workshop internal API
require_once(__DIR__ . '/../random/lib.php');     // random allocator
require_once(__DIR__ . '/settings_form.php');     // our settings form

/**
 * Allocates the submissions randomly in a cronjob task
 */
class workshop_scheduled_allocator implements workshop_allocator {

    /** workshop instance */
    protected $workshop;

    /** workshop_scheduled_allocator_form with settings for the random allocator */
    protected $mform;

    /**
     * @param workshop $workshop Workshop API object
     */
    public function __construct(workshop $workshop) {
        $this->workshop = $workshop;
    }

    /**
     * Save the settings for the random allocator to execute it later
     */
    public function init() {
        global $PAGE, $DB;

        $result = new workshop_allocation_result($this);

        $customdata = array();
        $customdata['workshop'] = $this->workshop;

        $current = $DB->get_record('workshopallocation_scheduled',
            array('workshopid' => $this->workshop->id), '*', IGNORE_MISSING);

        $customdata['current'] = $current;

        $this->mform = new workshop_scheduled_allocator_form($PAGE->url, $customdata);

        if ($this->mform->is_cancelled()) {
            redirect($this->workshop->view_url());
        } else if ($settings = $this->mform->get_data()) {
            if (empty($settings->enablescheduled)) {
                $enabled = false;
            } else {
                $enabled = true;
            }
            if (empty($settings->reenablescheduled)) {
                $reset = false;
            } else {
                $reset = true;
            }
            $settings = workshop_random_allocator_setting::instance_from_object($settings);
            $this->store_settings($enabled, $reset, $settings, $result);
            if ($enabled) {
                $msg = get_string('resultenabled', 'workshopallocation_scheduled');
            } else {
                $msg = get_string('resultdisabled', 'workshopallocation_scheduled');
            }
            $result->set_status(workshop_allocation_result::STATUS_CONFIGURED, $msg);
            return $result;
        } else {
            // this branch is executed if the form is submitted but the data
            // doesn't validate and the form should be redisplayed
            // or on the first display of the form.

            if ($current !== false) {
                $data = workshop_random_allocator_setting::instance_from_text($current->settings);
                $data->enablescheduled = $current->enabled;
                $this->mform->set_data($data);
            }

            $result->set_status(workshop_allocation_result::STATUS_VOID);
            return $result;
        }
    }

    /**
     * Returns the HTML code to print the user interface
     */
    public function ui() {
        global $PAGE;

        $output = $PAGE->get_renderer('mod_workshop');

        $out = $output->container_start('scheduled-allocator');
        // the nasty hack follows to bypass the sad fact that moodle quickforms do not allow to actually
        // return the HTML content, just to display it
        ob_start();
        $this->mform->display();
        $out .= ob_get_contents();
        ob_end_clean();
        $out .= $output->container_end();

        return $out;
    }

    /**
     * Executes the allocation
     *
     * @param bool $checksubmissionphase Check that the workshop is in submission phase before doing anything else.
     * @return workshop_allocation_result
     */
    public function execute(bool $checksubmissionphase = true) {
        global $DB;

        $result = new workshop_allocation_result($this);

        // Execution can occur in multiple places. Ensure we only allocate one at a time.
        $lockfactory = \core\lock\lock_config::get_lock_factory('mod_workshop_allocation_scheduled_execution');
        $executionlock = $lockfactory->get_lock($this->workshop->id, 1, 30);
        if (!$executionlock) {
            $result->set_status(workshop_allocation_result::STATUS_FAILED,
                get_string('resultfailed', 'workshopallocation_scheduled'));
        }

        try {
            // Make sure the workshop itself is at the expected state.

            if ($checksubmissionphase && $this->workshop->phase != workshop::PHASE_SUBMISSION) {
                $executionlock->release();
                $result->set_status(workshop_allocation_result::STATUS_FAILED,
                    get_string('resultfailedphase', 'workshopallocation_scheduled'));
                return $result;
            }

            if (empty($this->workshop->submissionend)) {
                $executionlock->release();
                $result->set_status(workshop_allocation_result::STATUS_FAILED,
                    get_string('resultfaileddeadline', 'workshopallocation_scheduled'));
                return $result;
            }

            if ($this->workshop->submissionend > time()) {
                $executionlock->release();
                $result->set_status(workshop_allocation_result::STATUS_VOID,
                    get_string('resultvoiddeadline', 'workshopallocation_scheduled'));
                return $result;
            }

            $current = $DB->get_record('workshopallocation_scheduled',
                array('workshopid' => $this->workshop->id, 'enabled' => 1), '*', IGNORE_MISSING);

            if ($current === false) {
                $executionlock->release();
                $result->set_status(workshop_allocation_result::STATUS_FAILED,
                    get_string('resultfailedconfig', 'workshopallocation_scheduled'));
                return $result;
            }

            if (!$current->enabled) {
                $executionlock->release();
                $result->set_status(workshop_allocation_result::STATUS_VOID,
                    get_string('resultdisabled', 'workshopallocation_scheduled'));
                return $result;
            }

            if (!is_null($current->timeallocated) and $current->timeallocated >= $this->workshop->submissionend) {
                $executionlock->release();
                $result->set_status(workshop_allocation_result::STATUS_VOID,
                    get_string('resultvoidexecuted', 'workshopallocation_scheduled'));
                return $result;
            }

            // So now we know that we are after the submissions deadline and either the scheduled allocation was not
            // executed yet or it was but the submissions deadline has been prolonged (and hence we should repeat the
            // allocations).

            $settings = workshop_random_allocator_setting::instance_from_text($current->settings);
            $randomallocator = $this->workshop->allocator_instance('random');
            $randomallocator->execute($settings, $result);

            // Store the result in the instance's table.
            $update = new stdClass();
            $update->id = $current->id;
            $update->timeallocated = $result->get_timeend();
            $update->resultstatus = $result->get_status();
            $update->resultmessage = $result->get_message();
            $update->resultlog = json_encode($result->get_logs());

            $DB->update_record('workshopallocation_scheduled', $update);

        } catch (\Exception $e) {
            $executionlock->release();
            $result->set_status(workshop_allocation_result::STATUS_FAILED,
                get_string('resultfailed', 'workshopallocation_scheduled'));

            throw $e;
        }

        $executionlock->release();

        return $result;
    }

    /**
     * Delete all data related to a given workshop module instance
     *
     * @see workshop_delete_instance()
     * @param int $workshopid id of the workshop module instance being deleted
     * @return void
     */
    public static function delete_instance($workshopid) {
        // TODO
        return;
    }

    /**
     * Stores the pre-defined random allocation settings for later usage
     *
     * @param bool $enabled is the scheduled allocation enabled
     * @param bool $reset reset the recent execution info
     * @param workshop_random_allocator_setting $settings settings form data
     * @param workshop_allocation_result $result logger
     */
    protected function store_settings($enabled, $reset, workshop_random_allocator_setting $settings, workshop_allocation_result $result) {
        global $DB;


        $data = new stdClass();
        $data->workshopid = $this->workshop->id;
        $data->enabled = $enabled;
        $data->submissionend = $this->workshop->submissionend;
        $data->settings = $settings->export_text();

        if ($reset) {
            $data->timeallocated = null;
            $data->resultstatus = null;
            $data->resultmessage = null;
            $data->resultlog = null;
        }

        $result->log($data->settings, 'debug');

        $current = $DB->get_record('workshopallocation_scheduled', array('workshopid' => $data->workshopid), '*', IGNORE_MISSING);

        if ($current === false) {
            $DB->insert_record('workshopallocation_scheduled', $data);

        } else {
            $data->id = $current->id;
            $DB->update_record('workshopallocation_scheduled', $data);
        }
    }
}
