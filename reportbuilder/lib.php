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
use core_reportbuilder\local\helpers\audience as audience_helper;
use core_reportbuilder\local\models\report;
use core_tag\output\{tagfeed, tagindex};

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
 * Callback to return tagged reports
 *
 * @param core_tag_tag $tag
 * @param bool $exclusivemode
 * @param int|null $fromcontextid
 * @param int|null $contextid
 * @param bool $recurse
 * @param int $page
 * @return tagindex
 */
function core_reportbuilder_get_tagged_reports(
    core_tag_tag $tag,
    bool $exclusivemode = false,
    ?int $fromcontextid = 0,
    ?int $contextid = 0,
    bool $recurse = true,
    int $page = 0,
): tagindex {
    global $OUTPUT;

    // Limit the returned list to those reports the current user can access.
    [$where, $params] = audience_helper::user_reports_list_access_sql('it');

    $tagcount = $tag->count_tagged_items('core_reportbuilder', 'reportbuilder_report', $where, $params);
    $perpage = $exclusivemode ? 20 : 5;
    $pagecount = ceil($tagcount / $perpage);

    $content = '';

    if ($tagcount > 0) {
        $tagfeed = new tagfeed();

        $pixicon = new pix_icon('i/report', new lang_string('customreport', 'core_reportbuilder'));

        $reports = $tag->get_tagged_items('core_reportbuilder', 'reportbuilder_report', $page * $perpage, $perpage,
            $where, $params);
        foreach ($reports as $report) {
            $tagfeed->add(
                $OUTPUT->render($pixicon),
                html_writer::link(
                    new moodle_url('/reportbuilder/view.php', ['id' => $report->id]),
                    (new report(0, $report))->get_formatted_name(),
                ),
            );
        }

        $content = $OUTPUT->render_from_template('core_tag/tagfeed', $tagfeed->export_for_template($OUTPUT));
    }

    return new tagindex($tag, 'core_reportbuilder', 'reportbuilder_report', $content, $exclusivemode, $fromcontextid,
        $contextid, $recurse, $page, $pagecount);
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
