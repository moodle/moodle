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

namespace local_ai_manager\local;

use local_ai_manager\base_purpose;
use stdClass;

/**
 * Data object class for handling user usage information when using an AI tool.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class userusage {

    /** @var int Constant for defining what means if a user can do unlimited requests */
    public const UNLIMITED_REQUESTS_PER_USER = 999999;

    /** @var int Constant for defining the default period until the requests of the users are being reset */
    public const MAX_REQUESTS_DEFAULT_PERIOD = DAYSECS;

    /**
     * @var int Constant for defining the minimum period the tenant manager can select until
     * the user usage information is being reset.
     */
    public const MAX_REQUESTS_MIN_PERIOD = HOURSECS;

    /** @var int The default value for the max requests for the basic role */
    public const MAX_REQUESTS_DEFAULT_ROLE_BASE = 10;

    /** @var int The default value for the max requests for the extended role */
    public const MAX_REQUESTS_DEFAULT_ROLE_EXTENDED = 50;

    /** @var false|stdClass The database record or false if none exists (yet) */
    private false|stdClass $record;

    /** @var int The amount of requests the user has used so far for the given purpose */
    private int $currentusage;

    /** @var int Unix time stamp when the user's usage information has been reset the last time */
    private int $lastreset;

    /**
     * Create the user usage object.
     *
     * @param base_purpose $purpose The purpose to create the user usage object for
     * @param int $userid the user id to create the usage object for
     */
    public function __construct(
            /** @var base_purpose $purpose The purpose to create the user usage object for */
            private readonly base_purpose $purpose,
            /** @var int $userid the user id to create the usage object for */
            private readonly int $userid) {
        $this->load();
    }

    /**
     * Loads the database record and stores its information into the object.
     */
    public function load(): void {
        global $DB;
        $this->record = $DB->get_record('local_ai_manager_userusage',
                ['purpose' => $this->purpose->get_plugin_name(), 'userid' => $this->userid]);
        $this->currentusage = !empty($this->record->currentusage) ? $this->record->currentusage : 0;
        $this->lastreset = !empty($this->record->lastreset) ? $this->record->lastreset : 0;
    }

    /**
     * Checks if a database record exists (yet).
     * @return bool true if a database record exists (yet)
     */
    public function record_exists(): bool {
        return !empty($this->record);
    }

    /**
     * Standard getter.
     * @return int The user id of the user for which this object contains the user information
     */
    public function get_userid(): int {
        return $this->userid;
    }

    /**
     * Persists the object data into the database.
     */
    public function store() {
        global $DB;
        $this->record = $DB->get_record('local_ai_manager_userusage',
                ['purpose' => $this->purpose->get_plugin_name(), 'userid' => $this->userid]);
        $newrecord = new stdClass();
        $newrecord->purpose = $this->purpose->get_plugin_name();
        $newrecord->userid = $this->userid;
        $newrecord->currentusage = $this->currentusage;
        $newrecord->lastreset = $this->lastreset;
        $newrecord->timemodified = time();
        if ($this->record) {
            $newrecord->id = $this->record->id;
            $DB->update_record('local_ai_manager_userusage', $newrecord);
        } else {
            $newrecord->id = $DB->insert_record('local_ai_manager_userusage', $newrecord);
        }
        $this->record = $newrecord;
    }

    /**
     * Standard getter.
     *
     * @return int the amount of requests a user has used so far for the given purpose
     */
    public function get_currentusage(): int {
        return $this->currentusage;
    }

    /**
     * Standard setter.
     *
     * @param int $currentusage The current amount of requests the user has used for this purpose
     */
    public function set_currentusage(int $currentusage): void {
        $this->currentusage = $currentusage;
    }

    /**
     * Standard getter.
     *
     * @return int the timestamp of the time when the counter has been reset the last time
     */
    public function get_lastreset(): int {
        return $this->lastreset;
    }

    /**
     * Standard setter.
     *
     * @param int $lastreset the timestamp when the counter has been reset the last time
     */
    public function set_lastreset(int $lastreset): void {
        $this->lastreset = $lastreset;
    }
}
