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
 * block_mhaairs data generator
 *
 * @package    block_mhaairs
 * @category   test
 * @copyright  2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Mhaairs block data generator class
 *
 * @package    block_mhaairs
 * @category   test
 * @copyright  2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mhaairs_generator extends testing_block_generator {

    /**
     * Create new block instance
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass activity record with extra cmid field
     */
    public function create_instance($record = null, $options = null) {
        global $DB;

        $this->instancecount++;

        $record = (object)(array)$record;
        $options = (array)$options;

        $record = $this->prepare_record($record);

        if (empty($record->timecreated)) {
            $record->timecreated = time();
        }

        if (empty($record->timemodified)) {
            $record->timemodified = time();
        }

        $id = $DB->insert_record('block_instances', $record);
        context_block::instance($id);

        $instance = $DB->get_record('block_instances', array('id' => $id), '*', MUST_EXIST);

        return $instance;
    }
}
