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
 * mod_chat data generator.
 *
 * @package    core
 * @category   test
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * mod_chat data generator class.
 *
 * @package    core
 * @category   test
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_chat_generator extends testing_module_generator {

    /**
     * @var int keep track of how many messages have been created.
     */
    protected $messagecount = 0;

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->messagecount = 0;
        parent::reset();
    }

    public function create_instance($record = null, ?array $options = null) {
        $record = (object)(array)$record;

        if (!isset($record->keepdays)) {
            $record->keepdays = 0;
        }
        if (!isset($record->studentlogs)) {
            $record->studentlogs = 0;
        }
        if (!isset($record->chattime)) {
            $record->chattime = time() - 2;
        }
        if (!isset($record->schedule)) {
            $record->schedule = 0;
        }
        if (!isset($record->timemodified)) {
            $record->timemodified = time();
        }

        return parent::create_instance($record, (array)$options);
    }

}
