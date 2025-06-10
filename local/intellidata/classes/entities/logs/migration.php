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
 * Class for migration Users.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\logs;

use local_intellidata\helpers\RolesHelper;
use local_intellidata\repositories\export_log_repository;
use local_intellidata\persistent\datatypeconfig;

/**
 * Class for migration Users.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migration extends \local_intellidata\entities\migration {
    /** @var string */
    public $entity = '\local_intellidata\entities\logs\log';
    /** @var string */
    public $eventname = null;
    /** @var string */
    public $table = 'logstore_standard_log';
    /** @var string */
    public $tablealias  = 'lsl';

    /**
     * Prepare SQL query to get data from DB.
     *
     * @param false $count
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_sql($count = false, $condition = null, $conditionparams = []) {

        $this->setup_datatype();

        $select = ($count) ?
            "SELECT COUNT($this->tablealias.id) as recordscount" :
            "SELECT $this->tablealias.*";

        $sql = "$select
                  FROM {" . $this->table . "} $this->tablealias
                 WHERE " . $this->where();

        $sqlparams = $this->sqlparams();

        return $this->set_condition($condition, $conditionparams, $sql, $sqlparams);
    }

    /**
     * Generate WHERE for SQL.
     *
     * @return string
     */
    protected function where() {
        $wheres = [$this->tablealias . '.id > 0'];

        if (!empty($this->datatype['params'])) {
            foreach ($this->datatype['params'] as $paramname => $paramvalue) {
                if (empty($paramvalue)) {
                    continue;
                }
                $wheres[] = "$this->tablealias.$paramname = :$paramname";
                $sqlparams[$paramname] = $paramvalue;
            }
        } else {
            $wheres = [$this->tablealias . '.id = 0'];
        }

        return implode(" AND ", $wheres);
    }

    /**
     * Generate SQL params.
     *
     * @return mixed
     */
    protected function sqlparams() {
        return (array)$this->datatype['params'];
    }

    /**
     * Set datatype for migration.
     *
     * @return mixed
     */
    protected function setup_datatype() {
        if (empty($this->datatype['params'])) {
            $config = datatypeconfig::get_record(['datatype' => $this->datatype['name']]);
            $this->datatype['params'] = $config->get('params');
        }
    }

    /**
     * Prepare records for export.
     *
     * @param $records
     * @return \Generator
     */
    public function prepare_records_iterable($records) {
        foreach ($records as $record) {

            $entity = new $this->entity($record);
            $recorddata = $entity->export();
            $recorddata->recordtimecreated = $record->timecreated;
            $recorddata->recordusermodified = $record->userid;

            yield $recorddata;
        }
    }

    /**
     * Save logs.
     *
     * @param $record
     * @throws \coding_exception
     */
    public function save_log($record) {
        $this->exportlogrepository = new export_log_repository();
        $this->exportlogrepository->save_last_processed_data(
            $this->datatype['name'],
            $record,
            (isset($record->recordtimecreated)) ? (int)$record->recordtimecreated : time(),
            $this
        );
    }

    /**
     * Mark datatype as migrated.
     *
     * @throws \coding_exception
     */
    public function set_migrated() {
        $this->exportlogrepository = new export_log_repository();
        $this->exportlogrepository->save_migrated($this->datatype['name']);
    }
}
