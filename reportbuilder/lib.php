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
 * Callback methods for reportbuilder component
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

use core_reportbuilder\form\filter;

/**
 * Return the filters form fragment
 *
 * @param array $params
 * @return string
 */
function core_reportbuilder_output_fragment_filters_form(array $params): string {
    $filtersform = new filter(null, null, 'post', '', [], true, [
        'reportid' => $params['reportid'],
        'parameters' => $params['parameters'],
    ]);

    $filtersform->set_data_for_dynamic_submission();

    return $filtersform->render();
}
