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
 * Contains the class used for the displaying the expired contexts table.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy\output;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use coding_exception;
use context_helper;
use dml_exception;
use Exception;
use html_writer;
use pix_icon;
use stdClass;
use table_sql;
use tool_dataprivacy\api;
use tool_dataprivacy\expired_context;
use tool_dataprivacy\external\purpose_exporter;
use tool_dataprivacy\purpose;

defined('MOODLE_INTERNAL') || die;

/**
 * The class for displaying the expired contexts table.
 *
 * @copyright  2018 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class expired_contexts_table extends table_sql {

    /** @var int The context level acting as a filter for this table. */
    protected $contextlevel = null;

    /**
     * @var bool $selectall Has the user selected all users on the page? True by default.
     */
    protected $selectall = true;

    /** @var purpose[] Array of purposes mapped to the contexts. */
    protected $purposes = [];

    /**
     * expired_contexts_table constructor.
     *
     * @param int|null $contextlevel
     * @throws coding_exception
     */
    public function __construct($contextlevel = null) {
        parent::__construct('expired-contexts-table');

        $this->contextlevel = $contextlevel;

        $columnheaders = [
            'name' => get_string('name'),
            'info' => get_string('info'),
            'purpose' => get_string('purpose', 'tool_dataprivacy'),
            'category' => get_string('category', 'tool_dataprivacy'),
            'retentionperiod' => get_string('retentionperiod', 'tool_dataprivacy'),
            'timecreated' => get_string('expiry', 'tool_dataprivacy'),
        ];
        $checkboxattrs = [
            'title' => get_string('selectall'),
            'data-action' => 'selectall'
        ];
        $columnheaders['select'] = html_writer::checkbox('selectall', 1, true, null, $checkboxattrs);

        $this->define_columns(array_keys($columnheaders));
        $this->define_headers(array_values($columnheaders));
        $this->no_sorting('name');
        $this->no_sorting('select');
        $this->no_sorting('info');
        $this->no_sorting('purpose');
        $this->no_sorting('category');
        $this->no_sorting('retentionperiod');

        // Make this table sorted by first name by default.
        $this->sortable(true, 'timecreated');
    }

    /**
     * The context name column.
     *
     * @param stdClass $data The row data.
     * @return string
     * @throws coding_exception
     */
    public function col_name($data) {
        global $OUTPUT;
        $context = context_helper::instance_by_id($data->contextid);
        $parent = $context->get_parent_context();
        $contextdata = (object)[
            'name' => $context->get_context_name(false, true),
            'parent' => $parent->get_context_name(false, true),
        ];
        $fullcontexts = $context->get_parent_contexts(true);
        $contextsinpath = [];
        foreach ($fullcontexts as $contextinpath) {
            $contextsinpath[] = $contextinpath->get_context_name(false, true);
        }
        $infoicon = new pix_icon('i/info', implode(' / ', array_reverse($contextsinpath)));
        $infoiconhtml = $OUTPUT->render($infoicon);
        $name = html_writer::span(get_string('nameandparent', 'tool_dataprivacy', $contextdata), 'm-r-1');

        return  $name . $infoiconhtml;
    }

    /**
     * The context information column.
     *
     * @param stdClass $data The row data.
     * @return string
     * @throws coding_exception
     */
    public function col_info($data) {
        global $OUTPUT;

        $context = context_helper::instance_by_id($data->contextid);

        $children = $context->get_child_contexts();
        if (empty($children)) {
            return get_string('none');
        } else {
            $childnames = [];
            foreach ($children as $child) {
                $childnames[] = $child->get_context_name(false, true);
            }
            $infoicon = new pix_icon('i/info', implode(', ', $childnames));
            $infoiconhtml = $OUTPUT->render($infoicon);
            $name = html_writer::span(get_string('nchildren', 'tool_dataprivacy', count($children)), 'm-r-1');

            return  $name . $infoiconhtml;
        }
    }

    /**
     * The category name column.
     *
     * @param stdClass $data The row data.
     * @return mixed
     * @throws coding_exception
     * @throws dml_exception
     */
    public function col_category($data) {
        $context = context_helper::instance_by_id($data->contextid);
        $category = api::get_effective_context_category($context);

        return s($category->get('name'));
    }

    /**
     * The purpose column.
     *
     * @param stdClass $data The row data.
     * @return string
     * @throws coding_exception
     */
    public function col_purpose($data) {
        $purpose = $this->purposes[$data->contextid];

        return s($purpose->get('name'));
    }

    /**
     * The retention period column.
     *
     * @param stdClass $data The row data.
     * @return string
     * @throws Exception
     */
    public function col_retentionperiod($data) {
        global $PAGE;

        $purpose = $this->purposes[$data->contextid];

        $exporter = new purpose_exporter($purpose, ['context' => \context_system::instance()]);
        $exportedpurpose = $exporter->export($PAGE->get_renderer('core'));

        return $exportedpurpose->formattedretentionperiod;
    }

    /**
     * The timecreated a.k.a. the context expiry date column.
     *
     * @param stdClass $data The row data.
     * @return string
     */
    public function col_timecreated($data) {
        return userdate($data->timecreated);
    }

    /**
     * Generate the select column.
     *
     * @param stdClass $data The row data.
     * @return string
     */
    public function col_select($data) {
        $id = $data->id;
        return html_writer::checkbox('expiredcontext_' . $id, $id, $this->selectall, '', ['class' => 'selectcontext']);
    }

    /**
     * Query the database for results to display in the table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     * @throws dml_exception
     * @throws coding_exception
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        // Only count expired contexts that are awaiting confirmation.
        $total = expired_context::get_record_count_by_contextlevel($this->contextlevel, expired_context::STATUS_EXPIRED);
        $this->pagesize($pagesize, $total);

        $sort = $this->get_sql_sort();
        if (empty($sort)) {
            $sort = 'timecreated';
        }

        // Only load expired contexts that are awaiting confirmation.
        $expiredcontexts = expired_context::get_records_by_contextlevel($this->contextlevel, expired_context::STATUS_EXPIRED,
            $sort, $this->get_page_start(), $this->get_page_size());
        $this->rawdata = [];
        foreach ($expiredcontexts as $persistent) {
            $data = $persistent->to_record();

            $context = context_helper::instance_by_id($data->contextid);

            $purpose = api::get_effective_context_purpose($context);
            $this->purposes[$data->contextid] = $purpose;
            $this->rawdata[] = $data;
        }

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }

    /**
     * Override default implementation to display a more meaningful information to the user.
     */
    public function print_nothing_to_display() {
        global $OUTPUT;
        echo $this->render_reset_button();
        $this->print_initials_bar();
        echo $OUTPUT->notification(get_string('noexpiredcontexts', 'tool_dataprivacy'), 'warning');
    }

    /**
     * Override the table's show_hide_link method to prevent the show/hide link for the select column from rendering.
     *
     * @param string $column the column name, index into various names.
     * @param int $index numerical index of the column.
     * @return string HTML fragment.
     */
    protected function show_hide_link($column, $index) {
        if ($index < 6) {
            return parent::show_hide_link($column, $index);
        }
        return '';
    }
}
