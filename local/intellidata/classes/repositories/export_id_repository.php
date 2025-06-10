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
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\repositories;

use local_intellidata\persistent\export_ids;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class export_id_repository {

    /**
     * Get deleted records IDs.
     *
     * @param $datatype
     * @param $table
     * @return \moodle_recordset
     * @throws \dml_exception
     */
    public function get_deleted_ids($datatype, $table) {
        return $this->get_deleted_ids_request($datatype, $table);
    }

    /**
     * Get deleted records IDs.
     *
     * @param $table
     * @return \moodle_recordset
     * @throws \dml_exception
     */
    public function get_deleted_ids_request($datatype, $table) {
        global $DB;

        $ids = [];
        $count = 0;
        $lastid = 0;
        $prevlastid = 0;
        $existedrecords = $DB->get_recordset_sql("SELECT id FROM {{$table}} ORDER BY id ASC");

        foreach ($existedrecords as $existedrecord) {
            $ids[] = $existedrecord->id;
            $count++;
            $lastid = $existedrecord->id;

            if ($count >= 1000) {
                $records = $this->get_deleted_ids_records($ids, $lastid, $prevlastid, $datatype);
                foreach ($records as $record) {
                    yield $record;
                }

                $ids = [];
                $count = 0;
                $prevlastid = $lastid;
            }
        }

        if (count($ids) > 0) {
            $records = $this->get_deleted_ids_records($ids, $lastid, $prevlastid, $datatype);
            foreach ($records as $record) {
                yield $record;
            }
        }

        $inparams = [
            'lastid' => $lastid,
            'datatype' => $datatype,
        ];
        $records = $DB->get_recordset_sql("SELECT dataid AS id
                                                 FROM {" . export_ids::TABLE . "}
                                                WHERE dataid > :lastid
                                                  AND datatype = :datatype", $inparams);
        foreach ($records as $record) {
            yield $record;
        }
    }

    /**
     * Get deleted records IDs.
     *
     * @param $ids
     * @param $lastid
     * @param $prevlastid
     * @param $table
     * @return \moodle_recordset
     * @throws \dml_exception
     */
    private function get_deleted_ids_records($ids, $lastid, $prevlastid, $datatype) {
        global $DB;

        list($insql, $inparams) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED, 'param', false);
        $inparams['lastid'] = $lastid;
        $inparams['prevlastid'] = $prevlastid + 1;
        $inparams['datatype'] = $datatype;
        return $DB->get_recordset_sql("SELECT dataid AS id
                                             FROM {" . export_ids::TABLE . "}
                                            WHERE dataid {$insql}
                                              AND dataid BETWEEN :prevlastid
                                              AND :lastid
                                              AND datatype = :datatype", $inparams);
    }

    /**
     * Get created records IDs.
     *
     * @param $datatype
     * @param $table
     * @return \moodle_recordset
     * @throws \dml_exception
     */
    public function get_created_ids($datatype, $table) {
        global $DB;

        $ids = [];
        $count = 0;
        $lastid = 0;
        $prevlastid = 0;
        $storedrecords = $DB->get_recordset_sql("SELECT dataid
                                                       FROM {" . export_ids::TABLE . "}
                                                      WHERE datatype = :datatype
                                                   ORDER BY dataid ASC", ['datatype' => $datatype]);

        foreach ($storedrecords as $storedrecord) {
            $ids[] = $storedrecord->dataid;
            $count++;
            $lastid = $storedrecord->dataid;

            if ($count >= 1000) {
                $records = $this->get_created_ids_records($ids, $lastid, $prevlastid, $table);
                foreach ($records as $record) {
                    yield $record;
                }

                $ids = [];
                $count = 0;
                $prevlastid = $lastid;
            }
        }

        if (count($ids) > 0) {
            $records = $this->get_created_ids_records($ids, $lastid, $prevlastid, $table);
            foreach ($records as $record) {
                yield $record;
            }
        }

        $inparams = ['lastid' => $lastid];
        $records = $DB->get_recordset_sql("SELECT id
                                                 FROM {{$table}}
                                                WHERE id > :lastid", $inparams);
        foreach ($records as $record) {
            yield $record;
        }
    }

    /**
     * Get created records IDs.
     *
     * @param $ids
     * @param $lastid
     * @param $prevlastid
     * @param $table
     * @return \moodle_recordset
     * @throws \dml_exception
     */
    private function get_created_ids_records($ids, $lastid, $prevlastid, $table) {
        global $DB;

        list($insql, $inparams) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED, 'param', false);
        $inparams['lastid'] = $lastid;
        $inparams['prevlastid'] = $prevlastid + 1;

        return $DB->get_recordset_sql("SELECT id
                                             FROM {{$table}}
                                            WHERE id {$insql}
                                              AND id BETWEEN :prevlastid AND :lastid", $inparams);
    }

    /**
     * Delete deleted IDs from database.
     *
     * @param string $datatype
     * @param array $deletedids
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function clean_deleted_ids(string $datatype, array $deletedids) {
        global $DB;

        if (!count($deletedids)) {
            return;
        }

        list($insql, $params) = $DB->get_in_or_equal($deletedids, SQL_PARAMS_NAMED);
        $params['datatype'] = $datatype;
        $DB->execute("DELETE FROM {" . export_ids::TABLE . "}
                           WHERE datatype = :datatype
                             AND dataid {$insql}", $params);
    }

    /**
     * Delete deleted IDs from database.
     *
     * @param string $table
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function clean_deleted_ids_trigger(string $datatype) {
        global $DB;

        $inparams = [
            'datatype' => $datatype,
        ];

        $DB->delete_records(export_ids::TABLE, $inparams);
    }

    /**
     * Save exported IDs to database.
     *
     * @param $records
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function save($records) {
        global $DB;

        if (!count($records)) {
            return;
        }

        $DB->insert_records(export_ids::TABLE, $records);
    }
}
