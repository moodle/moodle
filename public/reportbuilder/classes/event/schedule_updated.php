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

namespace core_reportbuilder\event;

use coding_exception;
use core\event\base;
use core_reportbuilder\local\models\schedule;
use moodle_url;

/**
 * Report builder custom report schedule updated event class.
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - int    reportid:      The id of the report
 * }
 */
class schedule_updated extends base {

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['objecttable'] = schedule::TABLE;
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Creates an instance from a report schedule object
     *
     * @param schedule $schedule
     * @return self
     */
    public static function create_from_object(schedule $schedule): self {
        $eventparams = [
            'context'  => $schedule->get_report()->get_context(),
            'objectid' => $schedule->get('id'),
            'other' => [
                'reportid' => $schedule->get('reportid'),
            ]
        ];
        $event = self::create($eventparams);
        $event->add_record_snapshot($event->objecttable, $schedule->to_record());
        return $event;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('scheduleupdated', 'core_reportbuilder');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $reportid = $this->other['reportid'];
        return "The user with id '$this->userid' updated the schedule with id '$this->objectid' in the custom report" .
            " with id '$reportid'.";
    }

    /**
     * Custom validations.
     *
     * @throws coding_exception
     */
    protected function validate_data(): void {
        parent::validate_data();
        if (!isset($this->objectid)) {
            throw new coding_exception('The \'objectid\' must be set.');
        }
        if (!isset($this->other['reportid'])) {
            throw new coding_exception('The \'reportid\' must be set in other.');
        }
    }

    /**
     * Returns relevant URL.
     *
     * @return moodle_url
     */
    public function get_url(): moodle_url {
        return new moodle_url('/reportbuilder/edit.php', ['id' => $this->other['reportid']], 'schedules');
    }
}
