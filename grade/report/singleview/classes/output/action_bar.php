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

namespace gradereport_singleview\output;

use moodle_url;
use renderer_base;
use gradereport_singleview\report\singleview;

/**
 * Renderable class for the action bar elements in the single view report page.
 *
 * @package   gradereport_singleview
 * @copyright 2022 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_bar extends \core_grades\output\action_bar {

    /** @var singleview $report The single view report class. */
    protected singleview $report;

    /** @var string $itemtype The single view item type. */
    protected string $itemtype;

    /**
     * The class constructor.
     *
     * @param \context $context The context object.
     * @param singleview $report The single view report class.
     * @param string $itemtype The single view item type.
     */
    public function __construct(\context $context, singleview $report, string $itemtype) {
        parent::__construct($context);
        $this->report = $report;
        $this->itemtype = $itemtype;
    }

    /**
     * Returns the template for the action bar.
     *
     * @return string
     */
    public function get_template(): string {
        return 'gradereport_singleview/action_bar';
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        global $USER;

        $courseid = $this->context->instanceid;
        // Get the data used to output the general navigation selector.
        $generalnavselector = new \core_grades\output\general_action_bar(
            $this->context,
            new moodle_url('/grade/report/singleview/index.php', ['id' => $courseid]),
            'report',
            'singleview'
        );
        $data = $generalnavselector->export_for_template($output);

        // The data required to output the page toggle element.
        $data['pagetoggler'] = [
            'displaylabel' => true,
            'userselectactive' => $this->itemtype === 'user',
            'gradeselectactive' => $this->itemtype === 'grade',
            'gradezerolink' => (new moodle_url('/grade/report/singleview/index.php',
                ['id' => $courseid, 'item' => 'grade_select']))->out(false),
            'userzerolink' => (new moodle_url('/grade/report/singleview/index.php',
                ['id' => $courseid, 'item' => 'user_select']))->out(false)
        ];

        $data['groupselector'] = $this->report->group_selector;
        $data['itemselector'] = $this->report->itemselector;

        $data['pbarurl'] = $this->report->pbarurl->out(false);

        if (!empty($USER->editing) && isset($this->report->screen->item)) {
            $data['bulkactions'] = $this->report->bulk_actions_menu($output);
        }

        return $data;
    }
}
