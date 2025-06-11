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

use core\output\inplace_editable;
use core_external\external_api;
use core_reportbuilder\manager;
use core_reportbuilder\permission;
use core_reportbuilder\local\models\filter;

/**
 * Filter heading editable component
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_heading_editable extends inplace_editable {

    /**
     * Class constructor
     *
     * @param int $filterid
     * @param filter|null $filter
     */
    public function __construct(int $filterid, ?filter $filter = null) {
        if ($filter === null) {
            $filter = new filter($filterid);
        }

        $report = $filter->get_report();
        $editable = permission::can_edit_report($report);

        $filterinstance = manager::get_report_from_persistent($report)
            ->get_filter($filter->get('uniqueidentifier'));

        // Use filter defined header if custom heading not set.
        if ('' !== $value = (string) $filter->get('heading')) {
            $displayvalue = $filter->get_formatted_heading($report->get_context());
        } else {
            $displayvalue = $value = $filterinstance->get_header();
        }

        parent::__construct('core_reportbuilder', 'filterheading', $filter->get('id'), $editable, $displayvalue, $value,
            get_string('renamefilter', 'core_reportbuilder', $filterinstance->get_header()));
    }

    /**
     * Update filter persistent and return self, called from inplace_editable callback
     *
     * @param int $filterid
     * @param string $value
     * @return self
     */
    public static function update(int $filterid, string $value): self {
        $filter = new filter($filterid);

        $report = $filter->get_report();

        external_api::validate_context($report->get_context());
        permission::require_can_edit_report($report);

        $value = clean_param($value, PARAM_TEXT);
        $filter
            ->set('heading', trim($value))
            ->update();

        return new self(0, $filter);
    }
}
