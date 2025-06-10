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
namespace local_intellidata\entities\users;

use local_intellidata\helpers\ParamsHelper;

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
    public $entity = '\local_intellidata\entities\users\user';
    /** @var string */
    public $eventname = '\core\event\user_created';
    /** @var string */
    public $table = 'user';
    /** @var string */
    public $tablealias = 'u';

    /**
     * Prepare SQL query to get data from DB.
     *
     * @param false $count
     * @param null $condition
     * @param array $conditionparams
     * @return array
     */
    public function get_sql($count = false, $condition = null, $conditionparams = []) {
        $where = $this->tablealias . '.deleted = :deleted';
        $select = ($count) ? "SELECT COUNT(u.id) as recordscount" : "SELECT u.*";

        $sql = "$select
                  FROM {".$this->table."} u
                 WHERE $where";

        $params = [
            'deleted' => 0,
        ];

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
        foreach ($records as $user) {
            $user->fullname = str_replace('"', "'", fullname($user));
            $user->state = ($user->confirmed && !$user->suspended) ?
                ParamsHelper::STATE_ACTIVE : ParamsHelper::STATE_INACTIVE;
            $user->lastlogin = max($user->lastlogin, $user->currentlogin);
            $user->email = trim($user->email);

            $entity = new $this->entity($user);
            $userdata = $entity->export();

            yield $userdata;
        }
    }
}
