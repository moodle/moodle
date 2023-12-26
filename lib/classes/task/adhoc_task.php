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
 * Adhoc task abstract class.
 *
 * All background tasks should extend this class.
 *
 * @package    core
 * @category   task
 * @copyright  2013 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\task;

/**
 * Abstract class defining an adhoc task.
 * @copyright  2013 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class adhoc_task extends task_base {

    /** @var string $customdata - Custom data required for when this task is executed. */
    private $customdata = '';

    /** @var integer|null $id - Adhoc tasks each have their own database record id. */
    private $id = null;

    /** @var integer|null $userid - Adhoc tasks may choose to run as a specific user. */
    private $userid = null;

    /** @var \core\lock\lock The concurrency task lock for this task. */
    private $concurrencylock = null;

    /** @var integer|null $attemptsavailable - The remaining attempts of the task, or null for unlimited. */
    private $attemptsavailable = null;

    /**
     * Provide default implementation of the task name for backward compatibility. Extending classes are expected to implement
     * this method to provide a descriptive name for the task (shown to admins)
     *
     * @return string
     */
    public function get_name() {
        $classparts = explode('\\', get_called_class());
        $classname = end($classparts);

        // Try to make human readable, capitalized and with spaces.
        return ucfirst(str_replace('_', ' ', $classname));
    }

    /**
     * Setter for $id.
     * @param int|null $id
     */
    public function set_id($id) {
        $this->id = $id;
    }

    /**
     * Getter for $userid.
     * @return int|null $userid
     */
    public function get_userid() {
        return $this->userid;
    }

    /**
     * Setter for $customdata.
     * @param mixed $customdata (anything that can be handled by json_encode)
     */
    public function set_custom_data($customdata) {
        $this->customdata = json_encode($customdata);
    }

    /**
     * Alternate setter for $customdata. Expects the data as a json_encoded string.
     * @param string $customdata json_encoded string
     */
    public function set_custom_data_as_string($customdata) {
        $this->customdata = $customdata;
    }

    /**
     * Getter for $customdata.
     * @return mixed (anything that can be handled by json_decode).
     */
    public function get_custom_data() {
        return json_decode($this->customdata);
    }

    /**
     * Alternate getter for $customdata.
     * @return string this is the raw json encoded version.
     */
    public function get_custom_data_as_string() {
        return $this->customdata;
    }

    /**
     * Getter for $id.
     * @return int|null $id
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Setter for $userid.
     * @param int|null $userid
     */
    public function set_userid($userid) {
        $this->userid = $userid;
    }

    /**
     * Returns default concurrency limit for this task.
     *
     * @return int default concurrency limit
     */
    protected function get_default_concurrency_limit(): int {
        global $CFG;

        if (isset($CFG->task_concurrency_limit_default)) {
            return (int) $CFG->task_concurrency_limit_default;
        }
        return 0;
    }

    /**
     * Returns effective concurrency limit for this task.
     *
     * @return int effective concurrency limit for this task
     */
    final public function get_concurrency_limit(): int {
        global $CFG;

        $classname = get_class($this);

        if (isset($CFG->task_concurrency_limit[$classname])) {
            return (int) $CFG->task_concurrency_limit[$classname];
        }
        return $this->get_default_concurrency_limit();
    }

    /**
     * Sets concurrency task lock.
     *
     * @param   \core\lock\lock $lock concurrency lock to be set
     */
    final public function set_concurrency_lock(\core\lock\lock $lock): void {
        $this->concurrencylock = $lock;
    }

    /**
     * Release the concurrency lock for this task type.
     */
    final public function release_concurrency_lock(): void {
        if ($this->concurrencylock) {
            $this->concurrencylock->release();
        }
    }

    /**
     * Set the remaining attempts of the task.
     *
     * @param int|null $attemptsavailable Number of the remaining attempts of the task, or null for unlimited.
     */
    public function set_attempts_available(?int $attemptsavailable): void {
        $this->attemptsavailable = $attemptsavailable;
    }

    /**
     * Get the remaining attempts of the task.
     *
     * @return int|null Number of the remaining attempts of the task, or null for unlimited.
     */
    public function get_attempts_available(): ?int {
        return $this->attemptsavailable;
    }

    /**
     * Used to indicate if the task should be re-run if it fails.
     * By default, tasks will be retried until they succeed, other tasks can override this method to change this behaviour.
     *
     * @return bool true if the task should be retried until it succeeds, false otherwise.
     */
    public function retry_until_success(): bool {
        return true;
    }
}
