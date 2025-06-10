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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2023 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\services;

use local_intellidata\helpers\RolesHelper;
use local_intellidata\helpers\TrackingHelper;

/**
 *
 */
class new_export_service {

    /** @var null|string */
    public $entityclases = null;

    /** @var array */
    public static $selecteventtables = [
        'course_modules',
        'forum_posts',
        'questionnaire_question',
    ];

    /**
     * Insert record event.
     *
     * @param string $table
     * @param array $params
     * @return void
     */
    public function insert_record_event($table, $params) {
        global $DB;

        if (!TrackingHelper::new_tracking_enabled()) {
            return;
        }

        $this->get_datatypes_observer($table, true);
        if (!$this->entityclases || !TrackingHelper::enabled()) {
            return;
        }

        foreach ($this->entityclases as $entity) {
            $requiredatatype = !isset($entity::$datatype);
            // Check if not optional date type and passed filters.
            if ($requiredatatype && !$this->filter($entity::TYPE, $params)) {
                continue;
            }

            $record = $params;
            if ($requiredatatype) {
                $record = $entity::prepare_export_data($params, [], $table);
            }

            if (!$record) {
                continue;
            }

            $record->crud = 'c';
            $entity->set_values($record);

            $this->export($requiredatatype ? $entity::TYPE : $entity::$datatype, $entity->export());
        }
    }

    /**
     * Insert records event.
     *
     * @param string $table
     * @param array $dataobjects
     *
     * @return void
     */
    public function insert_records_event($table, $dataobjects) {
        global $DB;

        if (!TrackingHelper::new_tracking_enabled()) {
            return;
        }

        $this->get_datatypes_observer($table);
        if (!$this->entityclases || !TrackingHelper::enabled()) {
            return;
        }

        foreach ($this->entityclases as $entity) {
            $requiredatatype = !isset($entity::$datatype);

            foreach ($dataobjects as $dataobject) {
                if ($requiredatatype && !$this->filter_id($entity::TYPE, (object)$dataobject)) {
                    $params = $this->prepare_data_for_query($table, (array)$dataobject);
                    $records = $DB->get_records($table, $params, 'id DESC', '*', 0, 1);
                    if (!$record = array_shift($records)) {
                        continue;
                    }
                } else {
                    $record = (object)$dataobject;

                    if (!isset($record->id)) {
                        $params = $this->prepare_data_for_query($table, (array)$dataobject);
                        $records = $DB->get_records($table, $params, 'id DESC', '*', 0, 1);
                        if (!$record = array_shift($records)) {
                            continue;
                        }
                    }
                }

                if (!isset($record) || ($requiredatatype && !$this->filter($entity::TYPE, $record))) {
                    continue;
                }

                if ($requiredatatype) {
                    $record = $entity::prepare_export_data($record);
                }

                $record->crud = 'c';
                $entity->set_values($record);

                $this->export($requiredatatype ? $entity::TYPE : $entity::$datatype, $entity->export());
            }
        }
    }

    /**
     * Prepare data for query.
     *
     * @param string $table
     * @param array $data
     *
     * @return array
     */
    private function prepare_data_for_query($table, $data) {
        global $DB;

        $columns = $DB->get_columns($table);
        foreach ($data as $key => $value) {
            $column = $columns[$key];
            if ($column->meta_type == 'X') {
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Set field select event.
     *
     * @param string $table
     * @param string $select
     * @param array $params
     *
     * @return void
     */
    public function set_field_select_event($table, $select, $params) {
        global $DB;

        if (!TrackingHelper::new_tracking_enabled()) {
            return;
        }

        if (!in_array($table, self::$selecteventtables)) {
            return;
        }

        $this->get_datatypes_observer($table);
        if (!$this->entityclases || !TrackingHelper::enabled()) {
            return;
        }

        $records = $DB->get_records_sql("SELECT * FROM {" . $table . "} " . $select, $params);
        foreach ($this->entityclases as $entity) {
            $requiredatatype = !isset($entity::$datatype);

            foreach ($records as $record) {
                if ($requiredatatype && !$this->filter($entity::TYPE, $record)) {
                    continue;
                }

                if ($requiredatatype) {
                    $record = $entity::prepare_export_data($record);
                }

                $record->crud = 'u';
                $entity->set_values($record);

                $this->export($requiredatatype ? $entity::TYPE : $entity::$datatype, $entity->export());
            }
        }
    }

    /**
     * Update record event.
     *
     * @param string $table
     * @param array $params
     *
     * @return void
     */
    public function update_record_event($table, $params) {
        global $DB;

        if (!TrackingHelper::new_tracking_enabled()) {
            return;
        }

        $this->get_datatypes_observer($table);
        if (!$this->entityclases || !TrackingHelper::enabled()) {
            return;
        }

        if (!isset($params->id)) {
            return;
        }

        if (!$record = $DB->get_record($table, ['id' => $params->id])) {
            return;
        }

        foreach ($this->entityclases as $entity) {
            $requiredatatype = !isset($entity::$datatype);
            // Check if not optional date type and passed filters.
            if ($requiredatatype && !$this->filter($entity::TYPE, $record)) {
                return;
            }

            if ($requiredatatype) {
                $record = $entity::prepare_export_data($record);
            }

            $record->crud = 'u';
            $entity->set_values($record);

            $this->export($requiredatatype ? $entity::TYPE : $entity::$datatype, $entity->export());
        }
    }

    /**
     * Delete record event.
     *
     * @param string $table
     * @param array $params
     *
     * @return void
     */
    public function delete_record_event($table, $params) {
        if (!TrackingHelper::new_tracking_enabled()) {
            return;
        }

        $this->get_datatypes_observer($table);
        if (!$this->entityclases || !TrackingHelper::enabled()) {
            return;
        }

        foreach ($this->entityclases as $entity) {
            $requiredatatype = !isset($entity::$datatype);

            $data = (object)$params;
            $data->crud = 'd';
            $entity->set_values($data);

            $this->export($requiredatatype ? $entity::TYPE : $entity::$datatype, $entity->export());
        }
    }

    /**
     * Delete records event.
     *
     * @param string $table
     * @param string $field
     * @param array $values
     *
     * @return void
     */
    public function delete_records_event($table, $field, $values) {
        global $DB;

        if (!TrackingHelper::new_tracking_enabled()) {
            return;
        }

        $this->get_datatypes_observer($table);
        if (!$this->entityclases || !TrackingHelper::enabled()) {
            return;
        }

        foreach ($this->entityclases as $entity) {
            $requiredatatype = !isset($entity::$datatype);

            foreach ($values as $value) {
                if (!$field == 'id') {
                    if (!$record = $DB->get_record($table, [$field => $value])) {
                        return;
                    }

                    $value = $record->id;
                    if (!$this->filter($entity::TYPE, $record)) {
                        return;
                    }
                }

                $params = new \stdClass;
                $params->id = $value;
                $params->crud = 'd';
                $entity->set_values($params);

                $this->export($requiredatatype ? $entity::TYPE : $entity::$datatype, $entity->export());
            }
        }
    }

    /**
     * Delete records select event.
     *
     * @param string $table
     * @param string $select
     * @param array $params
     *
     * @return void
     */
    public function delete_records_select_event($table, $select, $params = []) {
        global $DB;

        if (!TrackingHelper::new_tracking_enabled()) {
            return;
        }

        $this->get_datatypes_observer($table);
        if (!$this->entityclases || !TrackingHelper::enabled()) {
            return;
        }

        $sql = "SELECT id FROM {" . $table . "} WHERE $select";
        $ids = array_keys($DB->get_records_sql($sql, $params));

        foreach ($this->entityclases as $entity) {
            $requiredatatype = !isset($entity::$datatype);
            foreach ($ids as $id) {
                $record = new \stdClass;
                $record->id = $id;
                $record->crud = 'd';
                $entity->set_values($record);

                $this->export($requiredatatype ? $entity::TYPE : $entity::$datatype, $entity->export());
            }
        }
    }

    /**
     * Get datatypes observer.
     *
     * @param string $table
     * @param bool $useadditional
     * @return void
     */
    public function get_datatypes_observer($table, $useadditional = false) {
        $datatypes = datatypes_service::get_datatypes();
        $entities = [];
        foreach ($datatypes as $data) {
            $issetatable = isset($data['additional_tables']);
            if (!isset($data['table']) && !$issetatable) {
                continue;
            }

            if (($data['table'] == $table) || ($useadditional && $issetatable && in_array($table, $data['additional_tables']))) {
                $entities[] = datatypes_service::init_entity($data, []);
            }
        }

        $this->entityclases = !empty($entities) ? $entities : null;
    }

    /**
     * Get entity by datatype.
     *
     * @param $rdatatype
     * @return string
     */
    private function get_entity_by_datatype($rdatatype) {
        if (!is_array($rdatatype)) {
            $rdatatype = datatypes_service::get_required_datatypes()[$rdatatype];
        }
        return datatypes_service::get_datatype_entity_class(datatypes_service::get_datatype_entity_path($rdatatype));
    }

    /**
     * Filter data for export.
     *
     * @param string $table
     * @param \stdClass $data
     * @return bool
     */
    private function filter($datatype, $data) {
        global $DB;

        $access = true;
        switch ($datatype) {
            case 'participation':
                if (!in_array($data->crud, ['c', 'u']) || !$data->userid ||
                    !in_array($data->contextlevel, [CONTEXT_COURSE, CONTEXT_MODULE])) {
                    $access = false;
                }
                break;
            case 'userlogins':
                $access = isset($data->eventname) && ($data->eventname == '\core\event\user_loggedin') &&
                            $data->contextid == 1;
                break;
            case 'roleassignments':
                list($insql, $params) = $DB->get_in_or_equal(array_keys(RolesHelper::CONTEXTLIST), SQL_PARAMS_NAMED);
                if (!$DB->record_exists_sql("SELECT id FROM {context} WHERE contextlevel " . $insql, $params)) {
                    $access = false;
                }

                break;
            case 'quizquestionattemptstepsdata':
                if ($data->name != 'answer') {
                    $access = false;
                }

                break;
        }

        return $access;
    }

    /**
     * Filter id for export.
     *
     * @param string $table
     * @param \stdClass $data
     * @return bool
     */
    private function filter_id($datatype, $data) {
        $needid = true;
        switch ($datatype) {
            case 'participation':
                if (!in_array($data->crud, ['c', 'u']) || !$data->userid ||
                    !in_array($data->contextlevel, [CONTEXT_COURSE, CONTEXT_MODULE])) {
                    $needid = false;
                }
                break;
            case 'userlogins':
                $needid = isset($data->eventname) && ($data->eventname == '\core\event\user_loggedin') &&
                            $data->contextid == 1;
                break;
        }

        return $needid;
    }

    /**
     * Export datatype.
     *
     * @param string $datatype
     * @param \stdClass $data
     * @return bool
     */
    private function export($datatype, $data) {
        $tracking = new events_service($datatype);
        $tracking->track($data);
    }
}
