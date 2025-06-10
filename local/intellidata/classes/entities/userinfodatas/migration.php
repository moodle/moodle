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
 * Class for migration UserInfoDatas.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\userinfodatas;

/**
 * Class for migration UserInfoDatas.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migration extends \local_intellidata\entities\migration {

    /** @var string */
    public $entity = '\local_intellidata\entities\userinfodatas\userinfodata';

    /** @var string */
    public $eventname = '\core\event\user_created';

    /** @var string */
    public $table = 'user_info_data';

    /** @var string */
    public $tablealias = 'ud';

    /**
     * Prepare SQL query to get data from DB.
     *
     * @param false $count
     * @param null $condition
     * @param array $conditionparams
     * @return array
     */
    public function get_sql($count = false, $condition = null, $conditionparams = []) {

        $select = ($count) ?
            "SELECT COUNT(" .$this->tablealias . ".id) as recordscount" :
            "SELECT " .$this->tablealias . ".*";

        $sql = "$select
                  FROM {" . $this->table . "} " . $this->tablealias . "
             LEFT JOIN {user} u ON u.id = " . $this->tablealias . ".userid";

        if ($condition) {
            $sql .= " WHERE " . $condition;
        }

        return [$sql, $conditionparams];
    }
}
