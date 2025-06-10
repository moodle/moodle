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
namespace local_intellidata\entities\courses;

use local_intellidata\services\dbschema_service;

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
    public $entity = '\local_intellidata\entities\courses\course';
    /** @var string */
    public $eventname = '\core\event\course_created';
    /** @var string */
    public $table = 'course';
    /** @var string */
    public $tablealias = 'c';

    /**
     * Prepare SQL query to get data from DB.
     *
     * @param false $count
     * @param null $condition
     * @param array $conditionparams
     * @return array
     */
    public function get_sql($count = false, $condition = null, $conditionparams = []) {
        global $CFG;

        $where = 'c.id > :cid';
        $sqlparams = ['cid' => 1];

        $dbschema = new dbschema_service();
        $visible = 'c.visible';
        if (isset($CFG->audiencevisibility) && ($CFG->audiencevisibility == 1) &&
            $dbschema->column_exists('course', 'audiencevisible')) {
            $visible = 'CASE WHEN c.audiencevisible = ' . COHORT_VISIBLE_NOUSERS . ' THEN 0 ELSE 1 END as visible';
        }

        $select = ($count) ?
            "SELECT COUNT(c.id) as recordscount" :
            "SELECT c.id, c.idnumber, c.fullname, c.startdate, c.shortname,
                    c.enddate, c.timecreated, $visible, c.format, c.sortorder, c.category";

        $sql = "$select
                  FROM {".$this->table."} c
                 WHERE $where";

        return $this->set_condition($condition, $conditionparams, $sql, $sqlparams);
    }
}
