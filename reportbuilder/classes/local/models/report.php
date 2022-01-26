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
use context_system;
use core\persistent;
use core_reportbuilder\event\report_created;
use core_reportbuilder\event\report_deleted;
use core_reportbuilder\event\report_updated;
use core_reportbuilder\local\report\base;

/**
 * Persistent class to represent a report
 *
 * @package     core_reportbuilder
 * @copyright   2018 Alberto Lara Hernández <albertolara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report extends persistent {

    /** @var string The table name. */
    public const TABLE = 'reportbuilder_report';

    /**
     * Return the definition of the properties of this model
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'name' => [
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'source' => [
                'type' => PARAM_RAW,
            ],
            'type' => [
                'type' => PARAM_INT,
                'choices' => [
                    base::TYPE_CUSTOM_REPORT,
                    base::TYPE_SYSTEM_REPORT,
                ],
            ],
            'uniquerows' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'conditiondata' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'settingsdata' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'contextid' => [
                'type' => PARAM_INT,
                'default' => static function(): int {
                    return context_system::instance()->id;
                }
            ],
            'component' => [
                'type' => PARAM_COMPONENT,
                'default' => '',
            ],
            'area' => [
                'type' => PARAM_AREA,
                'default' => '',
            ],
            'itemid' => [
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
     * Trigger report created event when persistent is created
     */
    protected function after_create(): void {
        if ($this->get('type') === base::TYPE_CUSTOM_REPORT) {
            report_created::create_from_object($this)->trigger();
        }
    }

    /**
     * Cascade report deletion, first deleting any linked persistents
     */
    protected function before_delete(): void {
        $reportparams = ['reportid' => $this->get('id')];

        // Columns.
        foreach (column::get_records($reportparams) as $column) {
            $column->delete();
        }

        // Filters.
        foreach (filter::get_records($reportparams) as $filter) {
            $filter->delete();
        }

        // Audiences.
        foreach (audience::get_records($reportparams) as $audience) {
            $audience->delete();
        }

        // Schedules.
        foreach (schedule::get_records($reportparams) as $schedule) {
            $schedule->delete();
        }
    }

    /**
     * Throw report deleted event when persistent is deleted
     *
     * @param bool $result
     */
    protected function after_delete($result): void {
        if (!$result || $this->get('type') === base::TYPE_SYSTEM_REPORT) {
            return;
        }
        report_deleted::create_from_object($this)->trigger();
    }

    /**
     * Throw report updated event when persistent is updated
     *
     * @param bool $result
     */
    protected function after_update($result): void {
        if (!$result || $this->get('type') === base::TYPE_SYSTEM_REPORT) {
            return;
        }
        report_updated::create_from_object($this)->trigger();
    }

    /**
     * Return report context, used by exporters
     *
     * @return context
     */
    public function get_context(): context {
        return context::instance_by_id($this->raw_get('contextid'));
    }

    /**
     * Return formatted report name
     *
     * @return string
     */
    public function get_formatted_name(): string {
        return format_string($this->raw_get('name'), true, ['context' => $this->get_context(), 'escape' => true]);
    }
}
