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
 * Export logs table.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

namespace local_intellidata\output\tables;
defined('MOODLE_INTERNAL') || die;

use html_writer;
use local_intellidata\persistent\export_logs;
use local_intellidata\persistent\datatypeconfig;

require_once($CFG->libdir.'/tablelib.php');

/**
 * Export logs table.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
class exportlogs_table extends \table_sql {

    /**
     * @var array|array[]
     */
    public $fields = [];
    /**
     * @var array|null
     */
    public $tabletypes = null;
    /**
     * @var array
     */
    protected $prefs = [];
    /**
     * @var \context_system|null
     */
    protected $context = null;

    /**
     * Export logs table construct.
     *
     * @param $uniqueid
     * @param $params
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function __construct($uniqueid, $params = '') {
        global $PAGE, $DB;

        $this->context = \context_system::instance();
        $this->tabletypes = datatypeconfig::get_tabletypes();
        parent::__construct($uniqueid);

        $this->fields = $this->get_fields();
        $sqlparams = [];

        $this->sortable(true, 'datatype', SORT_ASC);
        $this->is_collapsible = false;

        $this->define_columns(array_keys($this->fields));
        $this->define_headers($this->get_headers());

        $fields = "el.*, el.recordsmigrated as progress";
        $from = "{" . export_logs::TABLE . "} el";

        $where = 'el.id > 0';

        if (!empty($params['query'])) {
            $where .= " AND " . $DB->sql_like('el.datatype', ':searchquery', false, false, false);
            $sqlparams += [
                'searchquery' => '%' . $params['query'] . '%',
            ];
        }

        $this->set_sql($fields, $from, $where, $sqlparams);

        $this->define_baseurl($PAGE->url);
    }

    /**
     * Get fields.
     *
     * @return array[]
     * @throws \coding_exception
     */
    public function get_fields() {
        $fields = [
            'datatype' => [
                'label' => get_string('datatype', 'local_intellidata'),
            ],
            'tabletype' => [
                'label' => get_string('tabletype', 'local_intellidata'),
            ],
            'migrated' => [
                'label' => get_string('migrated', 'local_intellidata'),
            ],
            'progress' => [
                'label' => get_string('progress', 'local_intellidata'),
            ],
            'last_exported_id' => [
                'label' => get_string('lastexportedid', 'local_intellidata'),
            ],
            'timestart' => [
                'label' => get_string('timestart', 'local_intellidata'),
            ],
            'last_exported_time' => [
                'label' => get_string('timeend', 'local_intellidata'),
            ],
        ];

        return $fields;
    }

    /**
     * Get headers.
     *
     * @return array
     * @throws \coding_exception
     */
    public function get_headers() {

        $headers = [];

        if (count($this->fields)) {
            foreach ($this->fields as $options) {
                $headers[] = $options['label'];
            }
        }

        return$headers;
    }

    /**
     * Column datatype.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_datatype($values) {
        if (get_string_manager()->string_exists('datatype_' . $values->datatype, 'local_intellidata')) {
            return get_string('datatype_' . $values->datatype, 'local_intellidata');
        } else {
            return $values->datatype;
        }
    }

    /**
     * Column table type.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_tabletype($values) {
        return isset($this->tabletypes[$values->tabletype])
            ? $this->tabletypes[$values->tabletype]
            : '';
    }

    /**
     * Column migrated.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_migrated($values) {
        return ($values->migrated)
            ? get_string('yes')
            : get_string('no');
    }

    /**
     * Column progress.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_progress($values) {
        return (($values->migrated)
                ? $values->recordscount
                : $values->recordsmigrated) . '/' . $values->recordscount;
    }

    /**
     * Column time start.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_timestart($values) {
        return $this->col_datetime($values->timestart);
    }

    /**
     * Column last exported time.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_last_exported_time($values) {
        return $this->col_datetime($values->last_exported_time);
    }

    /**
     * Column date time.
     *
     * @param $timestamp
     * @return string
     * @throws \coding_exception
     */
    private function col_datetime($timestamp) {
        return ($timestamp) ? userdate($timestamp, get_string('strftimedatetime', 'langconfig')) : '-';
    }

    /**
     * Start html method.
     */
    public function start_html() {

        echo html_writer::start_tag('div', ['class' => 'custom-filtering-table']);

        // Render button to allow user to reset table preferences.
        echo $this->render_reset_button();

        // Do we need to print initial bars?
        $this->print_initials_bar();

        if (in_array(TABLE_P_TOP, $this->showdownloadbuttonsat)) {
            echo $this->download_buttons();
        }

        // Render search.
        echo html_writer::start_tag('div', ['class' => 'form-group d-flex justify-content-end']);

        // Render search form.
        echo $this->search_form();

        echo html_writer::end_tag('div');

        $this->wrap_html_start();
        // Start of main data table.

        echo html_writer::start_tag('div', ['class' => 'no-overflow']);
        echo html_writer::start_tag('table', $this->attributes);
    }

    /**
     * This function is not part of the public api.
     */
    public function print_nothing_to_display() {
        global $OUTPUT;

        // Render the dynamic table header.
        if (method_exists($this, 'get_dynamic_table_html_start')) {
            echo $this->get_dynamic_table_html_start();
        }

        // Render button to allow user to reset table preferences.
        echo $this->render_reset_button();

        $this->print_initials_bar();

        // Render search.
        echo html_writer::start_tag('div', ['class' => 'form-group d-flex justify-content-end']);

        // Render search form.
        echo $this->search_form();

        echo html_writer::end_tag('div');

        echo $OUTPUT->heading(get_string('nothingtodisplay'));

        // Render the dynamic table footer.
        if (method_exists($this, 'get_dynamic_table_html_end')) {
            echo $this->get_dynamic_table_html_end();
        }
    }

    /**
     * Get the html for the search form
     *
     * Usually only use internally
     */
    public function search_form() {
        global $PAGE;

        $renderer = $PAGE->get_renderer('local_intellidata');

        return $renderer->render_from_template(
            'local_intellidata/header_search_input',
            [
                'action' => $PAGE->url,
                'query' => $PAGE->url->get_param('query'),
                'sesskey' => sesskey(),
            ]
        );
    }
}
