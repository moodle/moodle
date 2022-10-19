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

/**
 * Class user_action_bar
 *
 * @package   gradereport_singleview
 * @copyright 2022 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_action_bar extends \core_grades\output\action_bar {

    protected \gradereport_singleview\report\singleview $report;

    public function __construct(\context $context, \gradereport_singleview\report\singleview $report) {
        parent::__construct($context);
        $this->report = $report;
    }

    public function get_template(): string {
        return 'gradereport_singleview/user_action_bar';
    }

    public function export_for_template(renderer_base $output) {
        $courseid = $this->context->instanceid;
        // Get the data used to output the general navigation selector.
        $generalnavselector = new \core_grades\output\general_action_bar(
            $this->context,
            new moodle_url('/grade/report/singleview/index.php', ['id' => $courseid]),
            'report',
            'singleview'
        );
        $data = $generalnavselector->export_for_template($output);

        $data['userselectactive'] = true;
        $data['gradezerolink'] = new moodle_url(
            '/grade/report/singleview/index.php',
            ['id' => $courseid, 'item' => 'grade_select']
        );
        $data['userzerolink'] = new moodle_url(
            '/grade/report/singleview/index.php',
            ['id' => $courseid, 'item' => 'user_select']
        );

        $data['groupselector'] = $this->report->group_selector;

        // User selector.
        $screen = new \gradereport_singleview\local\screen\grade($this->report->courseid, null, $this->report->currentgroup);
        $options = $screen->options();
        $userselector = new \core\output\select_menu('itemid', $options, $this->report->screen->item->id);
        $userselector->set_label(get_string('user'));
        $data['userselector'] = $userselector->export_for_template($output);

        $data['pbarurl'] = $this->report->pbarurl->out(false);

        return $data;
    }
}
