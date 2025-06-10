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
 * This class implements Wiris Quizzes LockProvider interface
 * in order to manage concurrent calls to Wiris Quizzes services
 *
 * @package    qtype
 * @subpackage wq
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/question/type/wq/classes/moodlelock.php');

class moodlelockprovider {

    /**
     * Lock the resource identified by a String and return a Lock object.
     *
     * The thread execution is blocked until a Lock can be acquired. Implemetations of this method must be atomic.
     *
     * @param id The unique identifier of the resource.
     * @return The Lock object.
     *
     * @throw Error if the timeout is reached or other unexpected situation occurs. Implementations should guarantee
     * that, in this case, the resource is not locked.
     * **/
    // @codingStandardsIgnoreStart
    public function getLock($id) {
        // @codingStandardsIgnoreStop
        $timeout = 10;

        $resource = $id;

        $lockfactory = new \core\lock\db_record_lock_factory('qtype_wq_persistenvariables');
        $lock = $lockfactory->get_lock($resource, $timeout);
        if ($lock === false) {
            throw new moodle_exception('couldnotaquirelock', 'qtype_wq', '', 'Could not acquire lock');
        }

        return new moodlelock($lock);
    }
}
