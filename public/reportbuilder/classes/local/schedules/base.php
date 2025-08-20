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

declare(strict_types=1);

namespace core_reportbuilder\local\schedules;

use core_reportbuilder\local\helpers\schedule as helper;
use core_reportbuilder\local\models\schedule;
use MoodleQuickForm;
use progress_trace;
use stdClass;

/**
 * Schedule base class
 *
 * @package     core_reportbuilder
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base {
    /**
     * Private constructor, please use {@see instance} or {@see from_persistent} methods instead
     *
     * @param schedule $schedule The persistent object associated with this schedule
     */
    private function __construct(
        /** @var schedule The persistent object associated with this schedule */
        private schedule $schedule,
    ) {
        // Nothing to see here.
    }

    /**
     * Load instance of schedule type
     *
     * @param int $id
     * @param stdClass|null $record
     * @return self|null
     */
    final public static function instance(int $id = 0, ?stdClass $record = null): ?self {
        $schedule = new schedule($id, $record);
        return static::from_persistent($schedule);
    }

    /**
     * Load instance of schedule type from persistent
     *
     * @param schedule $schedule
     * @return self|null
     */
    final public static function from_persistent(schedule $schedule): ?self {
        // Ensure schedule class is always populated.
        if (!$classname = $schedule->get('classname')) {
            $classname = get_called_class();
            $schedule->set('classname', $classname);
        }

        if (!helper::valid($classname)) {
            return null;
        }

        return new $classname($schedule);
    }

    /**
     * Create instance of schedule type from record
     *
     * @param stdClass $record
     * @return self
     */
    final public static function create(stdClass $record): self {
        $schedule = new schedule(0, $record);

        $schedule->set_many([
            'name' => trim($schedule->get('name')),
            'timenextsend' => helper::calculate_next_send_time($schedule),
        ]);

        $instance = self::from_persistent($schedule);
        $instance->get_persistent()->create();

        return $instance;
    }

    /**
     * Name of the schedule
     *
     * @return string
     */
    abstract public function get_name(): string;

    /**
     * Description of the schedule
     *
     * @return string
     */
    abstract public function get_description(): string;

    /**
     * If the current user is able to add this schedule type
     *
     * @return bool
     */
    public function user_can_add(): bool {
        return true;
    }

    /**
     * Whether the schedule requires audience configuration
     *
     * @return bool
     */
    public function requires_audience(): bool {
        return true;
    }

    /**
     * Schedule specific form definition elements
     *
     * @param MoodleQuickForm $mform
     */
    abstract public function definition(MoodleQuickForm $mform): void;

    /**
     * Validate schedule specific form data
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validate(array $data, array $files): array {
        return [];
    }

    /**
     * Execute the schedule. If {@see requires_audience}, then a list of audience user records will be passed as parameter
     *
     * Will be called via cron as part of the {@see \core_reportbuilder\task\send_schedule} task
     *
     * @param stdClass[] $users
     * @param progress_trace $trace
     */
    abstract public function execute(array $users, progress_trace $trace): void;

    /**
     * Return schedule persistent
     *
     * @return schedule
     */
    final public function get_persistent(): schedule {
        return $this->schedule;
    }

    /**
     * Return decoded schedule config
     *
     * @return array
     */
    final public function get_configdata(): array {
        return json_decode($this->schedule->get('configdata'), true);
    }
}
