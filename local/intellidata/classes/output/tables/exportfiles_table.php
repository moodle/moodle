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
 * Export files table.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

namespace local_intellidata\output\tables;
defined('MOODLE_INTERNAL') || die;

use html_writer;
use local_intellidata\helpers\StorageHelper;

require_once($CFG->libdir.'/tablelib.php');

/**
 * Export files table.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
class exportfiles_table extends \table_sql {

    /** @var bool|mixed */
    public $download = false;
    /** @var array|array[] */
    public $fields = [];
    /** @var array */
    protected $prefs = [];

    /**
     * Table configuration constructor.
     *
     * @param $uniqueid
     * @param $params
     * @throws \coding_exception
     */
    public function __construct($uniqueid, $params) {
        global $PAGE, $DB;

        parent::__construct($uniqueid);
        $this->download = $params['download'];
        $this->fields = $this->get_fields();
        $sqlparams = [];

        $this->sortable(true, 'timecreated', SORT_DESC);
        $this->is_collapsible = false;

        $this->define_columns(array_keys($this->fields));
        $this->define_headers($this->get_headers());

        $fields = "f.*";
        $from = "{files} f";

        $where = 'f.id > 0 AND f.component = :component AND f.mimetype IS NOT NULL';
        $sqlparams['component'] = 'local_intellidata';

        if (!empty($params['query'])) {
            $where .= " AND " . $DB->sql_like('f.filearea', ':searchquery', false, false, false);
            $sqlparams += [
                'searchquery' => '%' . $params['query'] . '%',
            ];
        }

        $this->set_sql($fields, $from, $where, $sqlparams);
        $this->define_baseurl($PAGE->url);
    }

    /**
     * Method to get columns labels.
     *
     * @return array[]
     * @throws \coding_exception
     */
    public function get_fields() {
        $fields = [
            'filearea' => [
                'label' => get_string('datatype', 'local_intellidata'),
            ],
            'filename' => [
                'label' => get_string('filename', 'local_intellidata'),
            ],
            'filesize' => [
                'label' => get_string('filesize', 'local_intellidata'),
            ],
            'timecreated' => [
                'label' => get_string('created', 'local_intellidata'),
            ],
            'actions' => [
                'label' => get_string('actions', 'local_intellidata'),
            ],
        ];

        return $fields;
    }

    /**
     * Setup table header.
     *
     * @return array
     * @throws \coding_exception
     */
    public function get_headers() {

        $headers = [];

        if (count($this->fields)) {
            foreach ($this->fields as $field => $options) {
                $headers[] = $options['label'];
            }
        }

        $headers[] = get_string('actions', 'local_intellidata');

        return$headers;
    }

    /**
     * Generate date field.
     *
     * @param $values
     * @return string
     * @throws \coding_exception
     */
    public function col_timecreated($values) {
        return ($values->timecreated) ? userdate($values->timecreated, get_string('strftimedatetime', 'langconfig')) : '-';
    }

    /**
     * Generate human readable filearea name.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_filearea($values) {
        if (get_string_manager()->string_exists('datatype_' . $values->filearea, 'local_intellidata')) {
            return get_string('datatype_' . $values->filearea, 'local_intellidata');
        } else {
            return $values->filearea;
        }
    }

    /**
     * Generate files size.
     *
     * @param $values
     * @return string
     */
    public function col_filesize($values) {
        return StorageHelper::convert_filesize($values->filesize);
    }

    /**
     * Display actions column.
     *
     * @param $values
     * @return string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function col_actions($values) {
        global $OUTPUT;

        $buttons = [];

        $urlparams = ['id' => $values->id];

        // Action download.
        $aurl = StorageHelper::make_pluginfile_url($values)->out(false);
        $buttons[] = $OUTPUT->action_icon(
            $aurl,
            new \pix_icon('t/download', get_string('download'), 'core', ['class' => 'iconsmall'])
        );

        $aurl = new \moodle_url('/local/intellidata/logs/index.php', $urlparams + ['action' => 'delete', 'sesskey' => sesskey()]);
        $buttons[] = $OUTPUT->action_icon($aurl, new \pix_icon('t/delete', get_string('delete'),
            'core', ['class' => 'iconsmall']), null,
            ['onclick' => "if (!confirm('".get_string('deletefileconfirmation', 'local_intellidata')."')) return false;"]
        );

        return implode(' ', $buttons);
    }

    /**
     * Start table output
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

        $this->wrap_html_start();
        // Start of main data table.

        echo html_writer::start_tag('div', ['class' => 'no-overflow']);
        echo html_writer::start_tag('table', $this->attributes);

    }

    /**
     * Get the html for the download buttons
     *
     * Usually only use internally
     */
    public function download_buttons() {
        global $OUTPUT, $PAGE;

        $output = '';

        if ($this->is_downloadable() && !$this->is_downloading()) {

            $output = $OUTPUT->download_dataformat_selector(get_string('downloadas', 'table'),
                $this->baseurl->out_omit_querystring(), 'download', $this->baseurl->params());

            $output .= \html_writer::start_tag('div', ['class' => 'form-group d-flex justify-content-end']);

            $exporturl = new \moodle_url('/local/intellidata/logs/index.php', ['action' => 'export', 'sesskey' => sesskey()]);
            $output .= \html_writer::link($exporturl, get_string('exportfiles', 'local_intellidata'),
                ['class' => 'btn btn-primary mr-1']);

            // Render search form.
            $output .= $this->search_form();

            $output .= \html_writer::end_tag('div');
        }

        return $output;
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
