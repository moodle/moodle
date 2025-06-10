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
use report_lsusql\utils;
use report_lsusql\local\category as report_category;

/**
 * Index page renderable class.
 *
 * @package    report_lsusql
 * @copyright  2021 The Open Univesity
 * @copyright  2022 Louisiana State University
 * @copyright  2022 Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class index_page implements renderable, templatable {
    /** @var array Categories' data. */
    private $categories;

    /** @var array Queries' data. */
    private $queries;

    /** @var context Context to check the capability. */
    private $context;

    /** @var moodle_url Return url for edit/delete link. */
    private $returnurl;

    /** @var int Shown category id from optional param. */
    private $showcat;

    /** @var int Hidden category id from optional param. */
    private $hidecat;

    /** Build the index page renderable object.
     *
     * @param array $categories Categories for renderer.
     * @param array $queries Queries for renderer.
     * @param context $context Context to check the capability.
     * @param moodle_url $returnurl Return url for edit/delete link.
     * @param int $showcat Showing Category Id.
     * @param int $hidecat Hiding Category Id.
     */
    public function __construct(array $categories, array $queries, context $context, moodle_url $returnurl,
            int $showcat = 0, int $hidecat = 0) {
        $this->categories = $categories;
        $this->queries = $queries;
        $this->context = $context;
        $this->returnurl = $returnurl;
        $this->showcat = $showcat;
        $this->hidecat = $hidecat;
    }

    public function export_for_template(renderer_base $output) {
        $categoriesdata = [];
        $grouppedqueries = utils::group_queries_by_category($this->queries);
        foreach ($this->categories as $record) {
            $category = new report_category($record);
            $queries = $grouppedqueries[$record->id] ?? [];
            $category->load_queries_data($queries);
            $categorywidget = new category($category, $this->context, true, $this->showcat, $this->hidecat, true,
                false, $this->returnurl);
            $categoriesdata[] = ['category' => $output->render($categorywidget)];
        }

        $addquerybutton = $managecategorybutton = '';
        if (has_capability('report/lsusql:definequeries', $this->context)) {
            $addquerybutton = $output->single_button(report_lsusql_url('edit.php', ['returnurl' => $this->returnurl]),
                get_string('addreport', 'report_lsusql'), 'post', ['class' => 'mb-1']);
        }
        if (has_capability('report/lsusql:managecategories', $this->context)) {
            $managecategorybutton = $output->single_button(report_lsusql_url('manage.php'),
                get_string('managecategories', 'report_lsusql'));
        }

        $data = [
            'expandable' => true,
            'expandcollapselinkattheend' => (count($this->categories) >= 5),
            'categories' => $categoriesdata,
            'addquerybutton' => $addquerybutton,
            'managecategorybutton' => $managecategorybutton
        ];
        return $data;
    }
}
