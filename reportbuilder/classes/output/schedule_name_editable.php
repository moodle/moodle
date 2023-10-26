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

namespace core_reportbuilder\output;

use core_external;
use core\output\inplace_editable;
use core_reportbuilder\permission;
use core_reportbuilder\local\models\schedule;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once("{$CFG->libdir}/external/externallib.php");

/**
 * Schedule name editable component
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class schedule_name_editable extends inplace_editable {

    /**
     * Class constructor
     *
     * @param int $scheduleid
     * @param schedule|null $schedule
     */
    public function __construct(int $scheduleid, ?schedule $schedule = null) {
        if ($schedule === null) {
            $schedule = new schedule($scheduleid);
        }

        $report = $schedule->get_report();
        $editable = permission::can_edit_report($report);

        $displayvalue = $schedule->get_formatted_name($report->get_context());

        parent::__construct('core_reportbuilder', 'schedulename', $schedule->get('id'), $editable, $displayvalue,
            $schedule->get('name'), get_string('editschedulename', 'core_reportbuilder'));
    }

    /**
     * Update schedule persistent and return self, called from inplace_editable callback
     *
     * @param int $scheduleid
     * @param string $value
     * @return self
     */
    public static function update(int $scheduleid, string $value): self {
        $schedule = new schedule($scheduleid);

        $report = $schedule->get_report();

        core_external::validate_context($report->get_context());
        permission::require_can_edit_report($report);

        $value = trim(clean_param($value, PARAM_TEXT));
        if ($value !== '') {
            $schedule
                ->set('name', $value)
                ->update();
        }

        return new self(0, $schedule);
    }
}
