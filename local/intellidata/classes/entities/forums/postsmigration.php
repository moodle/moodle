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
 * Class for migration Forum Posts.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\forums;
use local_intellidata\services\dbschema_service;



/**
 * Class for migration Forum Posts.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class postsmigration extends \local_intellidata\entities\migration {
    /** @var string */
    public $entity = '\local_intellidata\entities\forums\forumpost';
    /** @var string */
    public $eventname = '\mod_forum\event\post_created';
    /** @var string */
    public $table = 'forum_posts';
    /** @var string */
    public $tablealias = 'p';

    /**
     * Prepare SQL query to get data from DB.
     *
     * @param false $count
     * @param null $condition
     * @param array $conditionparams
     * @return array
     */
    public function get_sql($count = false, $condition = null, $conditionparams = []) {

        $where = 'p.id > :pid';
        $params = ['pid' => 0];

        $select = ($count) ?
            "SELECT COUNT(p.id) as recordscount" :
            "SELECT p.id, p.userid, p.discussion, p.parent, p.message, p.created, p.modified, d.forum";

        // Validate deleted field.
        $dbschema = new dbschema_service();
        if ($dbschema->column_exists('forum_posts', 'deleted')) {
            if (!$count) {
                $select .= ', p.deleted';
            }
            $where .= ' AND p.deleted = :deleted';
            $params += [
                'deleted' => 0,
            ];
        } else {
            if (!$count) {
                $select .= ', 0 AS deleted';
            }
        }

        $sql = "$select
                  FROM {forum_posts} p
             LEFT JOIN {forum_discussions} d ON d.id = p.discussion
                 WHERE $where";

        return $this->set_condition($condition, $conditionparams, $sql, $params);
    }
}
