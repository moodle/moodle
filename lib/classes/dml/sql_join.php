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
 * An object that contains sql join fragments.
 *
 * @since      Moodle 3.1
 * @package    core
 * @category   dml
 * @copyright  2016 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\dml;

defined('MOODLE_INTERNAL') || die();

/**
 * An object that contains sql join fragments.
 *
 * An example of how to use this class in a simple query, where you have got
 * a join that is a join to the user table:
 *
 * $users = $DB->get_records_sql("SELECT u.*
 *         FROM {user} u
 *         {$sqljoin->joins}
 *         WHERE {$sqljoin->wheres}", $sqljoin->params);
 *
 * @since      Moodle 3.1
 * @package    core
 * @category   dml
 * @copyright  2016 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sql_join {

    /**
     * @var string joins.
     */
    public $joins;

    /**
     * @var string wheres.
     */
    public $wheres;

    /**
     * @var array params.
     */
    public $params;

    /**
     * @var bool if true this join is guaranteed to never match any rows.
     *      In this case, the calling code may be able to completely
     *      skip doing the database query.
     * @since Moodle 3.9/3.8.3/3.7.6.
     */
    public $cannotmatchanyrows;

    /**
     * Create an object that contains sql join fragments.
     *
     * Note, even if you set $cannotmatchanyrows to true, it is
     * important to also set the other fields because the calling
     * code is not required to check it. For example
     * new \core\dml\sql_join('', '1 = 2', [], true);
     *
     * @param string $joins The join sql fragment.
     * @param string $wheres The where sql fragment.
     * @param array $params Any parameter values.
     * @param bool $cannotmatchanyrows If true, this join is guaranteed to match no rows. See comment on the field above.
     */
    public function __construct($joins = '', $wheres = '', $params = array(), $cannotmatchanyrows = false) {
        $this->joins = $joins;
        $this->wheres = $wheres;
        $this->params = $params;
        $this->cannotmatchanyrows = $cannotmatchanyrows;
    }
}
