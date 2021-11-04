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

use core\output\inplace_editable;
use core_reportbuilder\form\audience;
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

/**
 * Return the audience form fragment
 *
 * @param array $params
 * @return string
 */
function core_reportbuilder_output_fragment_audience_form(array $params): string {
    global $PAGE;

    $audienceform = new audience(null, null, 'post', '', [], true, [
        'reportid' => $params['reportid'],
        'classname' => $params['classname'],
    ]);
    $audienceform->set_data_for_dynamic_submission();

    $context = [
        'instanceid' => 0,
        'heading' => $params['title'],
        'headingeditable' => $params['title'],
        'form' => $audienceform->render(),
        'canedit' => true,
        'candelete' => true,
        'showormessage' => $params['showormessage'],
    ];

    $renderer = $PAGE->get_renderer('core_reportbuilder');
    return $renderer->render_from_template('core_reportbuilder/local/audience/form', $context);
}

/**
 * Plugin inplace editable implementation
 *
 * @param string $itemtype
 * @param int $itemid
 * @param string $newvalue
 * @return inplace_editable|null
 */
function core_reportbuilder_inplace_editable(string $itemtype, int $itemid, string $newvalue): ?inplace_editable {
    switch ($itemtype) {
        case 'reportname':
            return \core_reportbuilder\output\report_name_editable::update($itemid, $newvalue);

        case 'columnheading':
            return \core_reportbuilder\output\column_heading_editable::update($itemid, $newvalue);

        case 'columnaggregation':
            return \core_reportbuilder\output\column_aggregation_editable::update($itemid, $newvalue);

        case 'filterheading':
            return \core_reportbuilder\output\filter_heading_editable::update($itemid, $newvalue);

        case 'audienceheading':
            return \core_reportbuilder\output\audience_heading_editable::update($itemid, $newvalue);

        case 'schedulename':
            return \core_reportbuilder\output\schedule_name_editable::update($itemid, $newvalue);
    }

    return null;
}
