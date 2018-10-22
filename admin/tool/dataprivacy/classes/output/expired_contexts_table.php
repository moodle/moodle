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

    /** @var purpose[] Array of purposes by their id. */
    protected $purposes = [];

    /** @var purpose[] Map of context => purpose. */
    protected $purposemap = [];

    /** @var array List of roles. */
    protected $roles = [];

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
            'tobedeleted' => get_string('tobedeleted', 'tool_dataprivacy'),
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
        $this->no_sorting('tobedeleted');

        // Make this table sorted by first name by default.
        $this->sortable(true, 'timecreated');

        // We use roles in several places.
        $this->roles = role_get_names();
    }

    /**
     * The context name column.
     *
     * @param stdClass $expiredctx The row data.
     * @return string
     * @throws coding_exception
     */
    public function col_name($expiredctx) {
        global $OUTPUT;
        $context = context_helper::instance_by_id($expiredctx->get('contextid'));
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
     * @param stdClass $expiredctx The row data.
     * @return string
     * @throws coding_exception
     */
    public function col_info($expiredctx) {
        global $OUTPUT;

        $context = context_helper::instance_by_id($expiredctx->get('contextid'));

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
     * @param stdClass $expiredctx The row data.
     * @return mixed
     * @throws coding_exception
     * @throws dml_exception
     */
    public function col_category($expiredctx) {
        $context = context_helper::instance_by_id($expiredctx->get('contextid'));
        $category = api::get_effective_context_category($context);

        return s($category->get('name'));
    }

    /**
     * The purpose column.
     *
     * @param stdClass $expiredctx The row data.
     * @return string
     * @throws coding_exception
     */
    public function col_purpose($expiredctx) {
        $purpose = $this->get_purpose_for_expiry($expiredctx);

        return s($purpose->get('name'));
    }

    /**
     * The retention period column.
     *
     * @param stdClass $expiredctx The row data.
     * @return string
     */
    public function col_retentionperiod($expiredctx) {
        $purpose = $this->get_purpose_for_expiry($expiredctx);

        $expiries = [];

        $expiry = html_writer::tag('dt', get_string('default'), ['class' => 'col-sm-3']);
        if ($expiredctx->get('defaultexpired')) {
            $expiries[get_string('default')] = get_string('expiredrolewithretention', 'tool_dataprivacy', (object) [
                    'retention' => api::format_retention_period(new \DateInterval($purpose->get('retentionperiod'))),
                ]);
        } else {
            $expiries[get_string('default')] = get_string('unexpiredrolewithretention', 'tool_dataprivacy', (object) [
                    'retention' => api::format_retention_period(new \DateInterval($purpose->get('retentionperiod'))),
                ]);
        }

        if (!$expiredctx->is_fully_expired()) {
            $purposeoverrides = $purpose->get_purpose_overrides();

            foreach ($expiredctx->get('unexpiredroles') as $roleid) {
                $role = $this->roles[$roleid];
                $override = $purposeoverrides[$roleid];

                $expiries[$role->localname] = get_string('unexpiredrolewithretention', 'tool_dataprivacy', (object) [
                        'retention' => api::format_retention_period(new \DateInterval($override->get('retentionperiod'))),
                    ]);
            }

            foreach ($expiredctx->get('expiredroles') as $roleid) {
                $role = $this->roles[$roleid];
                $override = $purposeoverrides[$roleid];

                $expiries[$role->localname] = get_string('expiredrolewithretention', 'tool_dataprivacy', (object) [
                        'retention' => api::format_retention_period(new \DateInterval($override->get('retentionperiod'))),
                    ]);
            }
        }

        $output = array_map(function($rolename, $expiry) {
            $return = html_writer::tag('dt', $rolename, ['class' => 'col-sm-3']);
            $return .= html_writer::tag('dd', $expiry, ['class' => 'col-sm-9']);

            return $return;
        }, array_keys($expiries), $expiries);

        return html_writer::tag('dl', implode($output), ['class' => 'row']);
    }

    /**
     * The timecreated a.k.a. the context expiry date column.
     *
     * @param stdClass $expiredctx The row data.
     * @return string
     */
    public function col_timecreated($expiredctx) {
        return userdate($expiredctx->get('timecreated'));
    }

    /**
     * Generate the select column.
     *
     * @param stdClass $expiredctx The row data.
     * @return string
     */
    public function col_select($expiredctx) {
        $id = $expiredctx->get('id');
        return html_writer::checkbox('expiredcontext_' . $id, $id, $this->selectall, '', ['class' => 'selectcontext']);
    }

    /**
     * Formatting for the 'tobedeleted' column which indicates in a friendlier fashion whose data will be removed.
     *
     * @param stdClass $expiredctx The row data.
     * @return string
     */
    public function col_tobedeleted($expiredctx) {
        if ($expiredctx->is_fully_expired()) {
            return get_string('defaultexpired', 'tool_dataprivacy');
        }

        $purpose = $this->get_purpose_for_expiry($expiredctx);

        $a = (object) [];

        $expiredroles = [];
        foreach ($expiredctx->get('expiredroles') as $roleid) {
            $expiredroles[] = html_writer::tag('li', $this->roles[$roleid]->localname);
        }
        $a->expired = html_writer::tag('ul', implode($expiredroles));

        $unexpiredroles = [];
        foreach ($expiredctx->get('unexpiredroles') as $roleid) {
            $unexpiredroles[] = html_writer::tag('li', $this->roles[$roleid]->localname);
        }
        $a->unexpired = html_writer::tag('ul', implode($unexpiredroles));

        if ($expiredctx->get('defaultexpired')) {
            return get_string('defaultexpiredexcept', 'tool_dataprivacy', $a);
        } else if (empty($unexpiredroles)) {
            return get_string('defaultunexpired', 'tool_dataprivacy', $a);
        } else {
            return get_string('defaultunexpiredwithexceptions', 'tool_dataprivacy', $a);
        }
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
        $contextids = [];
        foreach ($expiredcontexts as $persistent) {
            $this->rawdata[] = $persistent;
            $contextids[] = $persistent->get('contextid');
        }

        $this->preload_contexts($contextids);

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

    /**
     * Get the purpose for the specified expired context.
     *
     * @param   expired_context $expiredcontext
     * @return  purpose
     */
    protected function get_purpose_for_expiry(expired_context $expiredcontext) : purpose {
        $context = context_helper::instance_by_id($expiredcontext->get('contextid'));

        if (empty($this->purposemap[$context->id])) {
            $purpose = api::get_effective_context_purpose($context);
            $this->purposemap[$context->id] = $purpose->get('id');

            if (empty($this->purposes[$purpose->get('id')])) {
                $this->purposes[$purpose->get('id')] = $purpose;
            }
        }

        return $this->purposes[$this->purposemap[$context->id]];
    }

    /**
     * Preload context records given a set of contextids.
     *
     * @param   array   $contextids
     */
    protected function preload_contexts(array $contextids) {
        global $DB;

        if (empty($contextids)) {
            return;
        }

        $ctxfields = \context_helper::get_preload_record_columns_sql('ctx');
        list($insql, $inparams) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED);
        $sql = "SELECT {$ctxfields} FROM {context} ctx WHERE ctx.id {$insql}";
        $contextlist = $DB->get_recordset_sql($sql, $inparams);
        foreach ($contextlist as $contextdata) {
            \context_helper::preload_from_record($contextdata);
        }
        $contextlist->close();

    }
}
