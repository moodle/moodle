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
namespace local_intellidata\entities\roles;

use local_intellidata\helpers\RolesHelper;

/**
 * Class for migration Users.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ramigration extends \local_intellidata\entities\migration {
    /** @var string */
    public $entity = '\local_intellidata\entities\roles\roleassignment';
    /** @var string */
    public $eventname = '\core\event\role_assigned';
    /** @var string */
    public $table = 'role_assignments';
    /** @var string */
    public $tablealias = 'ra';

    /**
     * Prepare SQL query to get data from DB.
     *
     * @param false $count
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_sql($count = false, $condition = null, $conditionparams = []) {
        global $DB;

        list($insql, $params) = $DB->get_in_or_equal(array_keys(RolesHelper::CONTEXTLIST), SQL_PARAMS_NAMED);
        $where = "cxt.contextlevel $insql";

        $select = ($count) ?
            "SELECT COUNT(ra.id) as recordscount" :
            "SELECT ra.id, ra.roleid, ra.userid, ra.timemodified, ra.component,
                    ra.itemid, cxt.contextlevel, cxt.instanceid as courseid";

        $sql = "$select
                  FROM {role_assignments} ra
             LEFT JOIN {context} cxt ON cxt.id = ra.contextid
                 WHERE $where";

        return $this->set_condition($condition, $conditionparams, $sql, $params);
    }

    /**
     * Prepare records for export.
     *
     * @param $records
     * @return \Generator
     * @throws \coding_exception
     */
    public function prepare_records_iterable($records) {
        foreach ($records as $record) {

            $record->contexttype = RolesHelper::get_contexttype($record->contextlevel);

            $entity = new $this->entity($record);
            $recorddata = $entity->export();

            yield $recorddata;
        }
    }
}
