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
 * IntelliData migration table.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

namespace local_intellidata\output\tables;

use local_intellidata\helpers\SettingsHelper;
use local_intellidata\repositories\export_log_repository;

/**
 * IntelliData migration table.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
class migrations_table {

    /** @var array[] */
    private $fields;
    /** @var array */
    private $headers;
    /** @var array */
    private $statuses;
    /** @var array */
    private $data = [];

    /**
     * Migrations table construct.
     *
     * @throws \coding_exception
     */
    public function __construct() {
        $this->fields = $this->get_fields();
        $this->headers = $this->get_headers();
        $this->statuses = $this->get_statuses();
    }

    /**
     * Get fields.
     *
     * @return array[]
     * @throws \coding_exception
     */
    private function get_fields() {
        $fields = [
            'datatype' => [
                'label' => get_string('datatype', 'local_intellidata'),
            ],
            'status' => [
                'label' => get_string('status', 'local_intellidata'),
            ],
            'progress' => [
                'label' => get_string('progress', 'local_intellidata'),
            ],
            'timestart' => [
                'label' => get_string('timestart', 'local_intellidata'),
            ],
            'timeend' => [
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
    private function get_headers() {
        $headers = [];

        if (count($this->fields)) {
            foreach ($this->fields as $field => $options) {
                $headers[$field] = $options['label'];
            }
        }

        return $headers;
    }

    /**
     * Get statuses.
     *
     * @return array
     * @throws \coding_exception
     */
    private function get_statuses() {
        $statuses = [
            'completed' => get_string('status_completed', 'local_intellidata'),
            'inprogress' => get_string('status_inprogress', 'local_intellidata'),
            'pending' => get_string('status_pending', 'local_intellidata'),
        ];

        return $statuses;
    }

    /**
     * Get status.
     *
     * @param $migrated
     * @return mixed
     */
    private function get_status($migrated) {
        return $migrated == '0' ? $this->statuses['inprogress'] :
            ($migrated == '1' ? $this->statuses['completed'] : $this->statuses['pending']);
    }

    /**
     * Get record.
     *
     * @param $datatypename
     * @param array $datatype
     * @return array|null
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function get_record($datatypename, $datatype = []) {
        $exportlogrepository = new export_log_repository();
        $tabledatatypes = $exportlogrepository->get_assoc_datatypes('datatype');

        if (!empty($datatype['migration'])) {

            $item = array_fill_keys(array_keys($this->headers), '-');
            $item['datatype'] = $datatypename;

            if (isset($tabledatatypes[$datatypename])) {
                $tablerecord = $tabledatatypes[$datatypename];

                if ($tablerecord->get('recordscount') || $tablerecord->get('last_exported_id') || $tablerecord->get('migrated')) {
                    $item['status'] = $this->get_status($tablerecord->get('migrated'));
                    $item['progress'] = (($tablerecord->get('migrated'))
                            ? $tablerecord->get('recordscount')
                            : $tablerecord->get('recordsmigrated')) . '/' . $tablerecord->get('recordscount');
                    $item['timestart'] = $this->col_datetime($tablerecord->get('timestart'));
                    $item['timeend'] = $this->col_datetime($tablerecord->get('last_exported_time'));
                }
            }

            return $item;
        }

        return null;
    }

    /**
     * Col datetime.
     *
     * @param $timestamp
     * @return string
     * @throws \coding_exception
     */
    private function col_datetime($timestamp) {
        return ($timestamp) ? userdate($timestamp, get_string('strftimedatetime', 'langconfig')) : '-';
    }

    /**
     * Generate datatype.
     *
     * @param $datafiles
     * @param array $datatypes
     */
    public function generate($datafiles, $datatypes = []) {
        foreach ($datafiles as $datatypename => $params) {
            $datatype = isset($datatypes[$datatypename]) ? $datatypes[$datatypename] : [];
            $dataitem = $this->get_record($datatypename, $datatype);

            if ($dataitem) {
                $this->data[] = $dataitem;
            }
        }
    }

    /**
     * Output.
     *
     * @return string
     */
    public function out() {

        $output = '';

        $output .= \html_writer::start_tag('div', ['class' => 'form-group d-flex justify-content-end']);

        $output .= $this->calculate_progress_button();

        $url = new \moodle_url('/local/intellidata/migrations/index.php', ['action' => 'enablemigration', 'sesskey' => sesskey()]);
        $output .= \html_writer::link($url, get_string('enablemigration', 'local_intellidata'),
            [
                'class' => 'btn btn-primary', 'onclick' => "if (!confirm('" .
                get_string('resetmigrationmsg', 'local_intellidata') .
                "')) return false;",
            ]);
        $output .= \html_writer::end_tag('div');

        $table = new \html_table();
        $table->head = array_values($this->headers);
        $table->data = $this->data;
        $output .= \html_writer::table($table);

        return $output;
    }

    /**
     * Get the html for the calculate progress button.
     *
     * @return mixed
     */
    public function calculate_progress_button() {

        $output = '';

        if (!SettingsHelper::get_setting('enableprogresscalculation')) {
            $url = new \moodle_url('/local/intellidata/migrations/index.php', [
                'action' => 'calculateprogress', 'sesskey' => sesskey(),
            ]);
            $output .= \html_writer::link($url, get_string('calculateprogress', 'local_intellidata'),
                [
                    'class' => 'btn btn-primary mr-1', 'onclick' => "if (!confirm('" .
                    get_string('calculateprogressmsg', 'local_intellidata') .
                    "')) return false;",
                ]);
        }

        return $output;
    }
}
