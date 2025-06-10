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
 * Abstract class for IntelliData entities.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities;
use local_intellidata\helpers\DBHelper;
use local_intellidata\helpers\StorageHelper;
use local_intellidata\repositories\export_log_repository;
use local_intellidata\services\encryption_service;
use local_intellidata\services\export_service;
use local_intellidata\services\migration_service;
use local_intellidata\helpers\DebugHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\ParamsHelper;



/**
 * Abstract class for core objects saved to the DB.
 *
 * @author     IntelliBoard
 * @copyright  2022 intelliboard.net
 */
abstract class migration {

    /** @var encryption_service */
    public $encryptionservice = null;

    /** @var \stdClass */
    public $entity = null;
    /** @var string */
    public $table = null;
    /** @var string */
    public $tablealias = null;
    /** @var string */
    public $crud = 'c';
    /** @var string */
    public $datatype = null;
    /** @var int */
    public $writerecordslimits = null;
    /** @var migration_service */
    public $migrationservice = null;
    /** @var export_service */
    public $exportservice = null;
    /** @var export_log_repository */
    public $exportlogrepository = null;

    /**
     * Migration constructor.
     *
     * @param $datatype
     * @param string|null $forceformat
     * @param bool $initservices
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function __construct($datatype, string $forceformat = null, $initservices = true) {
        $this->datatype = $datatype;
        $this->writerecordslimits = (int)SettingsHelper::get_setting('migrationwriterecordslimit');

        if ($initservices) {
            $this->init_services(null, $forceformat);
        }
    }

    /**
     * Init Migration service for entity.
     *
     * @param null $services
     * @param null $forceformat
     * @throws \moodle_exception
     */
    public function init_services($services = null, $forceformat = null) {
        $this->encryptionservice = (!empty($services['encryptionservice']))
            ? $services['encryptionservice'] : new encryption_service();
        $this->migrationservice = (!empty($services['migrationservice']))
            ? $services['migrationservice'] : new migration_service($forceformat);
        $this->exportservice = (!empty($services['exportservice']))
            ? $services['exportservice'] : new export_service(ParamsHelper::MIGRATION_MODE_ENABLED);
    }

    /**
     * Get records for migration.
     *
     * @param $params
     * @return string
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_records($params) {

        $records = $this->get_data($params);

        return $this->prepare_records($records);
    }

    /**
     * Get records from DB and prepare to export.
     *
     * @param $params
     * @param $tablename
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function export_records($params, $tablename) {

        $records = $this->get_data($params);

        return $this->prepare_export_records($records, $tablename);
    }

    /**
     * Get data from DB.
     *
     * @param $params
     * @return \moodle_recordset
     * @throws \dml_exception
     */
    public function get_data($params) {
        list($sql, $sqlparams) = $this->get_sql();
        $sql = $this->set_order($sql);

        $db = DBHelper::get_db_client();

        return $db->get_recordset_sql($sql, $sqlparams, $params['start'], $params['limit']);
    }

    /**
     * Get count records from DB.
     *
     * @param null $condition
     * @param array $sqlparams
     * @return int
     * @throws \dml_exception
     */
    public function get_records_count($lastrecordid = null) {
        global $DB;

        $condition = null; $conditionparams = [];

        if ($lastrecordid) {
            $condition = (!empty($this->tablealias) ? $this->tablealias . "." : "") . "id <= :lastrecid";
            $conditionparams = ['lastrecid' => $lastrecordid];
        }

        list($sql, $sqlparams) = $this->get_sql(true, $condition, $conditionparams);

        return $DB->count_records_sql($sql, $sqlparams);
    }

    /**
     * Prepare SQL query to get data from DB.
     *
     * @param false $count
     * @param null $condition
     * @param array $sqlparams
     * @return array
     */
    public function get_sql($count = false, $condition = null, $conditionparams = []) {
        $select = ($count) ?
            "SELECT COUNT(id) as recordscount" :
            "SELECT *";

        $sql = "$select
                  FROM {".$this->table."}";

        $sqlparams = [];
        if ($condition) {
            $sql .= " WHERE " . $condition;
            $sqlparams += $conditionparams;
        }

        return [$sql, $sqlparams];
    }

    /**
     * Prepare all records.
     *
     * @param $records
     * @return string
     * @throws \coding_exception
     */
    protected function prepare_records($records) {
        $data = [];

        foreach ($this->prepare_records_iterable($records) as $entitydata) {
            $data[] = $this->preparedata($entitydata);
        }
        $records->close();

        $this->save_log((isset($entitydata)) ? $entitydata : null);

        return implode(PHP_EOL, $data);
    }

    /**
     * Prepare records.
     *
     * @param $records
     * @return \Generator
     */
    public function prepare_records_iterable($records) {
        $entity = new $this->entity();
        foreach ($records as $record) {
            $entity->set_values($record);
            $entitydata = $entity->export();

            yield $entitydata;
        }
    }

    /**
     * Prepare records for export.
     *
     * @param $records
     * @param $tablename
     * @return bool
     * @throws \coding_exception
     */
    private function prepare_export_records($records, $tablename) {
        $data = [];
        $i = 0;
        $countrecords = 0;

        foreach ($this->prepare_records_iterable($records) as $entitydata) {
            $data[] = $this->preparedata($entitydata);

            if ($i >= $this->writerecordslimits) {
                mtrace("Complete $countrecords records.");
                // Save data into the file.
                $this->exportservice->store_data($tablename, implode(PHP_EOL, $data));
                $data = [];
                $i = 0;
            }
            $i++;
            $countrecords++;
        }
        $records->close();
        $this->exportservice->store_data($tablename, implode(PHP_EOL, $data));

        $this->save_log((isset($entitydata)) ? $entitydata : null);

        return true;
    }

    /**
     * Prepare data for export.
     *
     * @param $data
     * @return false|string
     */
    private function preparedata($data) {
        return StorageHelper::format_data(
            $this->migrationservice->exportdataformat, $data
        );
    }

    /**
     * Save migration logs.
     *
     * @param $record
     * @throws \coding_exception
     */
    public function save_log($record) {
        $entity = new $this->entity();

        $this->exportlogrepository = new export_log_repository();
        $this->exportlogrepository->save_last_processed_data(
            $entity::TYPE,
            $record,
            (isset($record->recordtimecreated)) ? (int)$record->recordtimecreated : time(),
            $this
        );
    }

    /**
     * Validate if datatype is migratable.
     *
     * @return bool
     * @throws \ddl_exception
     */
    public function can_migrate() {
        global $DB;
        $dbman = $DB->get_manager();

        return $dbman->table_exists($this->table);
    }

    /**
     * Set datatype as migrated.
     *
     * @throws \coding_exception
     */
    public function set_migrated() {
        $entity = new $this->entity();
        $this->exportlogrepository = new export_log_repository();
        $this->exportlogrepository->save_migrated($entity::TYPE);
    }

    /**
     * Validate if table exists.
     *
     * @param $tablename
     * @return bool
     * @throws \ddl_exception
     */
    public static function table_exists($tablename) {
        global $DB;

        return $DB->get_manager()->table_exists($tablename);
    }

    /**
     * Set datatype.
     *
     * @param $datatype
     * @return mixed
     */
    public function set_datatype($datatype) {
        return $this->datatype = $datatype;
    }

    /**
     * Apply condition to the sql.
     *
     * @param $datatype
     * @return mixed
     */
    public function set_condition($condition = null, $conditionparams = [], $sql = '', $params = []) {

        if (!empty($condition)) {
            $sql .= (count($params)) ? " AND " : ' WHERE ';
            $sql .= $this->apply_tablealias($condition);

            $params += $conditionparams;
        }

        return [$sql, $params];
    }

    /**
     * Apply table alias to condition.
     *
     * @param $condition
     * @return mixed|string
     */
    public function apply_tablealias($condition) {
        return (!empty($this->tablealias) && !stristr($condition, $this->tablealias . '.'))
            ? $this->tablealias . '.' . $condition : $condition;
    }

    /**
     * Apply order for table.
     *
     * @param $sql
     * @return string
     */
    protected function set_order($sql) {

        if (!stristr($sql, 'ORDER BY')) {
            $sql .= " ORDER BY " . (!empty($this->tablealias) ? $this->tablealias . "." : "") . "id";
        }

        return $sql;
    }
}
