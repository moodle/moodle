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

namespace core_reportbuilder\local\models;

use context;
use core_reportbuilder\event\schedule_created;
use core_reportbuilder\event\schedule_deleted;
use core_reportbuilder\event\schedule_updated;
use lang_string;
use core\persistent;

/**
 * Persistent class to represent a report schedule
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class schedule extends persistent {

    /** @var string Table name */
    public const TABLE = 'reportbuilder_schedule';

    /** @var int Send report schedule as viewed by recipient */
    public const REPORT_VIEWAS_RECIPIENT = -1;

    /** @var int Send report schedule as viewed by creator */
    public const REPORT_VIEWAS_CREATOR = 0;

    /** @var int Send report schedule as viewed by specific user */
    public const REPORT_VIEWAS_USER = 1;

    /** @var int No recurrence */
    public const RECURRENCE_NONE = 0;

    /** @var int Daily recurrence */
    public const RECURRENCE_DAILY = 1;

    /** @var int Daily recurrence for week days only */
    public const RECURRENCE_WEEKDAYS = 2;

    /** @var int Weekly recurrence */
    public const RECURRENCE_WEEKLY = 3;

    /** @var int Monthly recurrence */
    public const RECURRENCE_MONTHLY = 4;

    /** @var int Annual recurrence */
    public const RECURRENCE_ANNUALLY = 5;

    /** @var int Send schedule with empty report */
    public const REPORT_EMPTY_SEND_EMPTY = 0;

    /** @var int Send schedule without report */
    public const REPORT_EMPTY_SEND_WITHOUT = 1;

    /** @var int Don't send schedule if report is empty */
    public const REPORT_EMPTY_DONT_SEND = 2;

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'reportid' => [
                'type' => PARAM_INT,
            ],
            'name' => [
                'type' => PARAM_TEXT,
            ],
            'enabled' => [
                'type' => PARAM_BOOL,
                'default' => true,
            ],
            'audiences' => [
                'type' => PARAM_RAW,
                'default' => '[]',
            ],
            'format' => [
                'type' => PARAM_PLUGIN,
            ],
            'subject' => [
                'type' => PARAM_TEXT,
            ],
            'message' => [
                'type' => PARAM_CLEANHTML,
            ],
            'messageformat' => [
                'type' => PARAM_INT,
                'default' => FORMAT_HTML,
                'choices' => [
                    FORMAT_MOODLE,
                    FORMAT_HTML,
                    FORMAT_PLAIN,
                    FORMAT_MARKDOWN,
                ],
            ],
            'userviewas' => [
                'type' => PARAM_INT,
                'default' => self::REPORT_VIEWAS_CREATOR,
            ],
            'timescheduled' => [
                'type' => PARAM_INT,
            ],
            'recurrence' => [
                'type' => PARAM_INT,
                'default' => self::RECURRENCE_NONE,
                'choices' => [
                    self::RECURRENCE_NONE,
                    self::RECURRENCE_DAILY,
                    self::RECURRENCE_WEEKDAYS,
                    self::RECURRENCE_WEEKLY,
                    self::RECURRENCE_MONTHLY,
                    self::RECURRENCE_ANNUALLY,
                ],
            ],
            'reportempty' => [
                'type' => PARAM_INT,
                'default' => self::REPORT_EMPTY_SEND_EMPTY,
                'choices' => [
                    self::REPORT_EMPTY_SEND_EMPTY,
                    self::REPORT_EMPTY_SEND_WITHOUT,
                    self::REPORT_EMPTY_DONT_SEND,
                ],
            ],
            'timelastsent' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'timenextsend' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'usercreated' => [
                'type' => PARAM_INT,
                'default' => static function(): int {
                    global $USER;
                    return (int) $USER->id;
                },
            ],
        ];
    }

    /**
     * Validate reportid property
     *
     * @param int $reportid
     * @return bool|lang_string
     */
    protected function validate_reportid(int $reportid) {
        if (!report::record_exists($reportid)) {
            return new lang_string('invaliddata', 'error');
        }

        return true;
    }

    /**
     * Return the report this schedule belongs to
     *
     * @return report
     */
    public function get_report(): report {
        return new report($this->get('reportid'));
    }

    /**
     * Return formatted schedule name
     *
     * @param context|null $context If the context of the report is already known, it should be passed here
     * @return string
     */
    public function get_formatted_name(?context $context = null): string {
        if ($context === null) {
            $context = $this->get_report()->get_context();
        }

        return format_string($this->raw_get('name'), true, ['context' => $context]);
    }

    /**
     * Hook to execute after creation
     */
    protected function after_create(): void {
        schedule_created::create_from_object($this)->trigger();
    }

    /**
     * Hook to execute after update
     *
     * @param bool $result
     */
    protected function after_update($result): void {
        if ($result) {
            schedule_updated::create_from_object($this)->trigger();
        }
    }

    /**
     * Hook to execute after deletion
     *
     * @param bool $result
     */
    protected function after_delete($result): void {
        if ($result) {
            schedule_deleted::create_from_object($this)->trigger();
        }
    }
}
