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
 * Adhoc tasks table.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2023 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

namespace local_intellidata\output\tables;
defined('MOODLE_INTERNAL') || die;

use local_intellidata\helpers\ParamsHelper;

require_once($CFG->libdir.'/tablelib.php');

/**
 * Adhoc tasks table.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2023 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
class adhoctasks_table extends \table_sql {

    /**
     * @var array|array[]
     */
    public $fields = [];
    /**
     * @var \context_system|null
     */
    protected $context = null;

    /**
     * Adhoc tasks table construct.
     *
     * @param $uniqueid
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function __construct($uniqueid) {
        global $PAGE, $CFG, $DB;

        $this->context = \context_system::instance();
        parent::__construct($uniqueid);

        $this->fields = $this->get_fields();

        $this->sortable(true, 'id', SORT_DESC);
        $this->is_collapsible = false;

        $this->define_columns(array_keys($this->fields));
        $this->define_headers($this->get_headers());

        $where = 'id > 0';
        $sqlparams = ['classname' => '%' . ParamsHelper::PLUGIN . '%'];
        $whereclasslike = $DB->sql_like(
            'classname', ':classname', false, false, false
        );

        $fields = "id, classname, nextruntime";

        if (!empty($CFG->version) && ($CFG->version > 2021052501)) {
            $fields .= ", timecreated, timestarted, pid";

            $where .= " AND (component = :component OR " . $whereclasslike . ")";
            $sqlparams['component'] = ParamsHelper::PLUGIN;
        } else {
            $where .= " AND " . $whereclasslike;
        }

        $fields .= ", faildelay, customdata, '' as actions";

        $from = "{task_adhoc}";

        $this->set_sql($fields, $from, $where, $sqlparams);

        $this->define_baseurl($PAGE->url);
    }

    /**
     * Function to define headers for the table.
     *
     * @return array[]
     * @throws \coding_exception
     */
    public function get_fields() {
        global $CFG;

        $fields = [
            'id' => [
                'label' => 'ID',
            ],
            'classname' => [
                'label' => get_string('taskname', ParamsHelper::PLUGIN),
            ],
            'nextruntime' => [
                'label' => get_string('nextruntime', ParamsHelper::PLUGIN),
            ],
        ];

        if (!empty($CFG->version) && ($CFG->version > 2021052501)) {
            $fields = array_merge($fields, [
                'timecreated' => [
                    'label' => get_string('timecreated', ParamsHelper::PLUGIN),
                ],
                'timestarted' => [
                    'label' => get_string('timestarted', ParamsHelper::PLUGIN),
                ],
                'pid' => [
                    'label' => get_string('pid', ParamsHelper::PLUGIN),
                ],
            ]);
        }

        return array_merge($fields, [
            'faildelay' => [
                'label' => get_string('faildelay', ParamsHelper::PLUGIN),
            ],
            'customdata' => [
                'label' => get_string('customdata', ParamsHelper::PLUGIN),
            ],
            'actions' => [
                'label' => get_string('actions', ParamsHelper::PLUGIN),
            ],
        ]);
    }

    /**
     * Generate table header array.
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

        return $headers;
    }

    /**
     * Task name column.
     *
     * @param $values
     * @return \lang_string|string
     */
    public function col_classname($values) {
        return str_replace('\local_intellidata\task\\', '', $values->classname);
    }

    /**
     * Time started column.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_timestarted($values) {
        return $this->col_datetime($values->timestarted);
    }

    /**
     * Time created column.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_timecreated($values) {
        return $this->col_datetime($values->timecreated);
    }

    /**
     * Next runtime column.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_nextruntime($values) {
        return $this->col_datetime($values->nextruntime);
    }

    /**
     * Datatype column.
     *
     * @param $timestamp
     * @return string
     * @throws \coding_exception
     */
    private function col_datetime($timestamp) {
        return ($timestamp) ? userdate($timestamp, get_string('strftimedatetime', 'langconfig')) : '-';
    }

    /**
     * Process ID column.
     *
     * @param $values
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public function col_pid($values) {
        return $values->pid ?? '-';
    }

    /**
     * Actions column.
     *
     * @param $values
     * @return string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function col_actions($values) {
        global $OUTPUT;

        if (!has_capability('local/intellidata:deleteadhoctasks', $this->context)) {
            return '';
        }

        $buttons = [];
        $aurl = new \moodle_url('/local/intellidata/logs/adhoctasks.php', [
            'id' => $values->id,
            'action' => 'delete',
            'sesskey' => sesskey(),
        ]);
        $buttons[] = $OUTPUT->action_icon(
            $aurl,
            new \pix_icon('t/delete', get_string('deletetask', ParamsHelper::PLUGIN),
                'core',
                ['class' => 'iconsmall']
            ),
            null,
            [
                'onclick' => "if (!confirm('" . get_string('deletetaskconfirmation', ParamsHelper::PLUGIN) .
                "')) return false;",
            ]
        );

        return implode(' ', $buttons);
    }
}
