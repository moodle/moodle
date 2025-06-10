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

namespace report_lsusql\output;

use context;
use moodle_url;
use renderable;
use templatable;
use renderer_base;
use report_lsusql\local\query as report_query;
use report_lsusql\local\category as report_category;

/**
 * Category renderable class.
 *
 * @package    report_lsusql
 * @copyright  2021 The Open University
 * @copyright  2022 Louisiana State University
 * @copyright  2022 Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category implements renderable, templatable {
    /** @var report_category Category object. */
    private $category;

    /** @var context Context. */
    private $context;

    /** @var int Shown category id from optional param. */
    private $showcat;

    /** @var int Hidden category id from optional param. */
    private $hidecat;

    /** @var bool Do we show the 'Show only' link? */
    private $showonlythislink;

    /** @var bool Can the category expanse/collapse? */
    private $expandable;

    /** @var moodle_url Return url. */
    private $returnurl;

    /** @var bool Show 'Add new query' button or not. */
    private $addnewquerybtn;

    /** Create the category renderable object.
     *
     * @param report_category $category Category object.
     * @param context $context Context to check the permission.
     * @param bool $expandable Can the category expanse/collapse?
     * @param int $showcat Shown category id from optional param
     * @param int $hidecat Hidden category id from optional param
     * @param bool $showonlythislink Do we show the 'Show only' link?
     * @param bool $addnewquerybtn Show 'Add new query' button or not.
     * @param moodle_url|null $returnurl Return url.
     */
    public function __construct(report_category $category, context $context, bool $expandable = false, int $showcat = 0,
            int $hidecat = 0, bool $showonlythislink = false, bool $addnewquerybtn = true, moodle_url $returnurl = null) {
        $this->category = $category;
        $this->context = $context;
        $this->expandable = $expandable;
        $this->showcat = $showcat;
        $this->hidecat = $hidecat;
        $this->showonlythislink = $showonlythislink;
        $this->addnewquerybtn = $addnewquerybtn;
        $this->returnurl = $returnurl ?? $this->category->get_url();
    }

    public function export_for_template(renderer_base $output) {

        $queriesdata = $this->category->get_queries_data();

        $querygroups = [];
        foreach ($queriesdata as $querygroup) {
            $queries = [];
            foreach ($querygroup['queries'] as $querydata) {
                $query = new report_query($querydata);
                if (!$query->can_view($this->context)) {
                    continue;
                }
                $querywidget = new category_query($query, $this->category, $this->context, $this->returnurl);
                $queries[] = ['categoryqueryitem' => $output->render($querywidget)];
            }

            $querygroups[] = [
                'type' => $querygroup['type'],
                'title' => get_string($querygroup['type'] . 'header', 'report_lsusql'),
                'helpicon' => $output->help_icon($querygroup['type'] . 'header', 'report_lsusql'),
                'queries' => $queries
            ];
        }

        $addquerybutton = '';
        if ($this->addnewquerybtn && has_capability('report/lsusql:definequeries', $this->context)) {
            $addnewqueryurl = report_lsusql_url('edit.php', ['categoryid' => $this->category->get_id(),
                'returnurl' => $this->returnurl->out_as_local_url(false)]);
            $addquerybutton = $output->single_button($addnewqueryurl, get_string('addreport', 'report_lsusql'), 'post',
                                        ['class' => 'mb-1']);
        }

        return [
            'id' => $this->category->get_id(),
            'name' => $this->category->get_name(),
            'expandable' => $this->expandable,
            'show' => $this->get_showing_state(),
            'showonlythislink' => $this->showonlythislink,
            'url' => $this->category->get_url()->out(false),
            'linkref' => $this->get_link_reference(),
            'statistic' => $this->category->get_statistic(),
            'querygroups' => $querygroups,
            'addquerybutton' => $addquerybutton
        ];
    }

    /**
     * Get showing state of category. Default is hidden.
     *
     * @return string
     */
    private function get_showing_state(): string {
        $categoryid = $this->category->get_id();

        return $categoryid == $this->showcat && $categoryid != $this->hidecat ? 'shown' : 'hidden';
    }

    /**
     * Get the link with showcat/hidecat parameter.
     *
     * @return string The url.
     */
    private function get_link_reference(): string {
        $categoryid = $this->category->get_id();
        if ($categoryid == $this->showcat) {
            $params = ['hidecat' => $categoryid];
        } else {
            $params = ['showcat' => $categoryid];
        }

        return report_lsusql_url('index.php', $params)->out(false);
    }
}
