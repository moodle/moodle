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
 * IntelliData config table.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

namespace local_intellidata\output\tables;

defined('MOODLE_INTERNAL') || die;

use html_writer;
use local_intellidata\helpers\TrackingHelper;
use local_intellidata\persistent\datatypeconfig;
use local_intellidata\persistent\export_logs;
use local_intellidata\services\datatypes_service;

require_once($CFG->libdir.'/tablelib.php');

/**
 * IntelliData config table.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
class config_table extends \table_sql {

    /** @var array */
    public $fields = [];
    /** @var null|array */
    public $tabletypes = null;
    /** @var array */
    protected $prefs = [];
    /** @var context_system */
    protected $context = null;
    /** @var array */
    protected $datatypes = [];
    /** @var array */
    protected $datatypestoignoreindex = [
        'tracking', 'trackinglog', 'trackinglogdetail',
    ];

    /**
     * Config table construct.
     *
     * @param $uniqueid
     * @param $searchquery
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function __construct($uniqueid, $searchquery = '') {
        global $PAGE, $DB;

        $this->context = \context_system::instance();
        $this->tabletypes = datatypeconfig::get_tabletypes();
        $this->datatypes = datatypes_service::get_all_datatypes();

        parent::__construct($uniqueid);

        $this->fields = $this->get_fields();
        $sqlparams = [];

        $this->sortable(true, 'id', SORT_ASC);
        $this->is_collapsible = false;

        $this->define_columns(array_keys($this->fields));
        $this->define_headers($this->get_headers());

        $fields = "c.*, el.id as exportenabled";
        $from = "{" . datatypeconfig::TABLE . "} c
                LEFT JOIN {" . export_logs::TABLE . "} el ON el.datatype = c.datatype";

        $where = 'c.id > 0';

        if (!empty($searchquery)) {
            $where .= " AND " . $DB->sql_like('c.datatype', ':searchquery', false, false, false);
            $sqlparams += [
                'searchquery' => '%' . $searchquery . '%',
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
            'rewritable' => [
                'label' => get_string('rewritable', 'local_intellidata'),
            ],
        ];

        // Event tracking fields.
        if (!TrackingHelper::new_tracking_enabled()) {
            $fields = array_merge($fields, [
                'events_tracking' => [
                    'label' => get_string('events_tracking', 'local_intellidata'),
                ],
                'timemodified_field' => [
                    'label' => get_string('timemodified_field', 'local_intellidata'),
                ],
                'filterbyid' => [
                    'label' => get_string('filterbyid', 'local_intellidata'),
                ],
            ]);
        }

        return array_merge($fields, [
                'status' => [
                    'label' => get_string('status', 'local_intellidata'),
                ],
                'exportenabled' => [
                    'label' => get_string('export', 'local_intellidata'),
                ],
                'actions' => [
                    'label' => get_string('actions', 'local_intellidata'),
                ],
            ]
        );
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

        $headers[] = get_string('actions', 'local_intellidata');

        return $headers;
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
     * Column tabletype.
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
     * Column event tracking.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_events_tracking($values) {
        return $this->yes_or_now_column($values->events_tracking);
    }

    /**
     * Column filter ID.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_filterbyid($values) {
        return $this->yes_or_now_column($values->filterbyid);
    }

    /**
     * Column rewritable.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_rewritable($values) {
        return $this->yes_or_now_column($values->rewritable);
    }

    /**
     * Column status.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_status($values) {
        return ($values->status)
            ? get_string('enabled', 'local_intellidata')
            : get_string('disabled', 'local_intellidata');
    }

    /**
     * Column export enable.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_exportenabled($values) {
        return ($values->exportenabled)
            ? get_string('enabled', 'local_intellidata')
            : get_string('disabled', 'local_intellidata');
    }

    /**
     * Column time modified field.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_timemodified_field($values) {
        return !empty($values->timemodified_field)
            ? $values->timemodified_field
            : '';
    }

    /**
     * Column Actions.
     *
     * @param $values
     * @return string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function col_actions($values) {
        global $OUTPUT;

        if (!has_capability('local/intellidata:editconfig', $this->context)) {
            return '';
        }

        $buttons = [];

        if ($values->tabletype == datatypeconfig::TABLETYPE_LOGS) {
            $urlparams = ['id' => $values->id];
            if ($values->exportenabled) {
                $aurl = new \moodle_url('/local/intellidata/config/editlogsentity.php', $urlparams + [
                        'action' => 'reset', 'sesskey' => sesskey(),
                    ]);
                $buttons[] = $OUTPUT->action_icon(
                    $aurl,
                    new \pix_icon('t/reset', get_string('resetexport', 'local_intellidata'),
                        'core',
                        ['class' => 'iconsmall']),
                    null,
                    [
                        'onclick' => "if (!confirm('" . get_string('resetcordconfirmation', 'local_intellidata') .
                        "')) return false;",
                    ]
                );
            }

            $aurl = new \moodle_url('/local/intellidata/config/editlogsentity.php', $urlparams);
            $buttons[] = $OUTPUT->action_icon($aurl, new \pix_icon('t/edit', get_string('edit'),
                'core', ['class' => 'iconsmall']), null
            );

            $aurl = new \moodle_url('/local/intellidata/config/editlogsentity.php', $urlparams + [
                    'action' => 'delete', 'sesskey' => sesskey(),
                ]);
            $buttons[] = $OUTPUT->action_icon(
                $aurl,
                new \pix_icon('t/delete', get_string('delete'),
                    'core',
                    ['class' => 'iconsmall']),
                null,
                [
                    'onclick' => "if (!confirm('" . get_string('deletecordconfirmation', 'local_intellidata') .
                    "')) return false;",
                ]
            );
        } else {
            $urlparams = ['datatype' => $values->datatype];
            if ($values->exportenabled) {
                $aurl = new \moodle_url('/local/intellidata/config/edit.php', $urlparams + [
                        'action' => 'reset', 'sesskey' => sesskey(),
                    ]);
                $buttons[] = $OUTPUT->action_icon(
                    $aurl,
                    new \pix_icon('t/reset', get_string('resetexport', 'local_intellidata'),
                        'core',
                        ['class' => 'iconsmall']),
                    null,
                    [
                        'onclick' => "if (!confirm('" . get_string('resetcordconfirmation', 'local_intellidata') .
                        "')) return false;",
                    ]
                );
            }

            $aurl = new \moodle_url('/local/intellidata/config/edit.php', $urlparams);
            $buttons[] = $OUTPUT->action_icon($aurl, new \pix_icon('t/edit', get_string('edit'),
                'core', ['class' => 'iconsmall']), null
            );
        }

        $buttons = $this->index_actions($values, $buttons);

        return implode(' ', $buttons);
    }

    /**
     * Create or delete index if allowed.
     *
     * @param $values
     * @return void
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function index_actions($values, $buttons = []) {
        global $OUTPUT;

        if (!in_array($values->datatype, $this->datatypestoignoreindex) && !empty($this->datatypes[$values->datatype]['table'])) {

            $urlparams = ['datatype' => $values->datatype];

            if (!empty($values->tableindex)) {
                $aurl = new \moodle_url('/local/intellidata/config/edit.php', $urlparams + [
                        'action' => 'deleteindex',
                        'sesskey' => sesskey(),
                    ]);
                $buttons[] = $OUTPUT->action_icon(
                    $aurl,
                    new \pix_icon('t/switch_minus', get_string('deleteindex', 'local_intellidata'),
                        'core',
                        ['class' => 'iconsmall']),
                    null,
                    [
                        'onclick' => "if (!confirm('" .
                        get_string('deleteindexcordconfirmation', 'local_intellidata', $values->datatype) .
                        "')) return false;",
                    ]
                );
            } else if (!empty($values->timemodified_field)) {
                $aurl = new \moodle_url('/local/intellidata/config/edit.php', $urlparams + [
                    'action' => 'createindex',
                    'sesskey' => sesskey(),
                 ]);
                $buttons[] = $OUTPUT->action_icon(
                    $aurl,
                    new \pix_icon('t/switch_plus', get_string('createindex', 'local_intellidata'),
                        'core',
                        ['class' => 'iconsmall']),
                    null,
                    [
                        'onclick' => "if (!confirm('" .
                        get_string('createindexcordconfirmation', 'local_intellidata', $values->datatype) .
                        "')) return false;",
                    ]
                );
            }
        }

        return $buttons;
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

        echo html_writer::start_tag('div', ['class' => 'form-group d-flex justify-content-end']);

        // Add config button.
        echo $this->create_button();

        // Render config reset button.
        echo $this->reset_button();

        // Render import button.
        echo $this->import_button();

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

        echo html_writer::start_tag('div', ['class' => 'form-group d-flex justify-content-end']);

        // Render import button.
        echo $this->import_button();

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
     * Get the html for the reset buttons
     *
     * Usually only use internally
     */
    public function reset_button() {

        $reseturl = new \moodle_url('/local/intellidata/config/index.php', [
            'action' => 'reset',
            'sesskey' => sesskey(),
        ]);
        $output = \html_writer::link($reseturl, get_string('resettodefault', 'local_intellidata'),
            ['class' => 'btn btn-primary mr-1']);

        return $output;
    }

    /**
     * Get the html for the import buttons
     *
     * Usually only use internally
     */
    public function import_button() {

        $reseturl = new \moodle_url('/local/intellidata/config/index.php', [
            'action' => 'import',
            'sesskey' => sesskey(),
        ]);
        $output = \html_writer::link($reseturl, get_string('refreshconfig', 'local_intellidata'),
            ['class' => 'btn btn-primary mr-1']);

        return $output;
    }

    /**
     * Get the html for the create button.
     *
     * Usually only use internally
     */
    public function create_button() {

        $reseturl = new \moodle_url('/local/intellidata/config/editlogsentity.php');
        $output = \html_writer::link($reseturl, get_string('createlogsdatatype', 'local_intellidata'),
            ['class' => 'btn btn-primary mr-1']);

        return $output;
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

    /**
     * Column show.
     *
     * @param $value
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function yes_or_now_column($value) {
        return ($value) ? get_string('yes') : get_string('no');
    }
}
