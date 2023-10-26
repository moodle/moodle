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
use core_reportbuilder\manager;
use core_reportbuilder\permission;
use core_reportbuilder\local\helpers\aggregation;
use core_reportbuilder\local\models\column;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once("{$CFG->libdir}/external/externallib.php");

/**
 * Column aggregation editable component
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column_aggregation_editable extends inplace_editable {

    /**
     * Class constructor
     *
     * @param int $columnid
     * @param column|null $column
     */
    public function __construct(int $columnid, ?column $column = null) {
        if ($column === null) {
            $column = new column($columnid);
        }

        $report = $column->get_report();
        $editable = permission::can_edit_report($report);

        $columninstance = manager::get_report_from_persistent($report)
            ->get_column($column->get('uniqueidentifier'));

        $currentvalue = (string) $column->get('aggregation');

        $editlabel = get_string('aggregatecolumn', 'core_reportbuilder', $columninstance->get_title());
        parent::__construct('core_reportbuilder', 'columnaggregation', $column->get('id'), $editable, null, $currentvalue,
            $editlabel, $editlabel);

        // List of available aggregation methods for the column type, minus any specifically disabled.
        $options = aggregation::get_column_aggregations($columninstance->get_type(),
            $columninstance->get_disabled_aggregation());

        $this->set_type_select(['' => get_string('aggregationnone', 'core_reportbuilder')] + $options);
    }

    /**
     * Update column persistent and return self, called from inplace_editable callback
     *
     * @param int $columnid
     * @param string $value
     * @return self
     */
    public static function update(int $columnid, string $value): self {
        $column = new column($columnid);

        $report = $column->get_report();

        core_external::validate_context($report->get_context());
        permission::require_can_edit_report($report);

        $value = clean_param($value, PARAM_TEXT);
        $column
            ->set('aggregation', $value)
            ->update();

        return new self(0, $column);
    }
}
