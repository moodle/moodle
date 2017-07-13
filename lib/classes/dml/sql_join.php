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
     * Create an object that contains sql join fragments.
     *
     * @param string $joins The join sql fragment.
     * @param string $wheres The where sql fragment.
     * @param array $params Any parameter values.
     */
    public function __construct($joins = '', $wheres = '', $params = array()) {
        $this->joins = $joins;
        $this->wheres = $wheres;
        $this->params = $params;
    }
}
