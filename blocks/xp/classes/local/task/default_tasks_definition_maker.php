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
 * Tasks definition maker.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\task;

/**
 * Tasks definition maker class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_tasks_definition_maker implements tasks_definition_maker {

    /**
     * Get the tasks definition.
     */
    public function get_tasks_definition() {
        return [
            [
                'classname' => 'block_xp\task\admin_notices',
                'blocking' => 0,
                'minute' => 'R',
                'hour' => 'R',
                'day' => '*',
                'dayofweek' => 'R',
                'month' => '*',
            ],
            [
                'classname' => 'block_xp\task\collection_logger_purge',
                'blocking' => 0,
                'minute' => 47,
                'hour' => 3,
                'day' => '*',
                'dayofweek' => '*',
                'month' => '*',
            ],
            [
                'classname' => 'block_xp\task\usage_report',
                'blocking' => 0,
                'minute' => 'R',
                'hour' => 'R',
                'day' => '*',
                'dayofweek' => 'R',
                'month' => '*',
            ],
        ];
    }

}
