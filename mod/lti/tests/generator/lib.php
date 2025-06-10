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
 * mod_lti data generator
 *
 * @package    mod_lti
 * @category   test
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author     Mark Nielsen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/lti/locallib.php');

/**
 * LTI module data generator class
 *
 * @package    mod_lti
 * @category   test
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author     Mark Nielsen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_lti_generator extends testing_module_generator {

    public function create_instance($record = null, array $options = null) {
        $record  = (object) (array) $record;

        if (!isset($record->toolurl)) {
            $record->toolurl = '';
        } else {
            $toolurl = new moodle_url($record->toolurl);
            $record->toolurl = $toolurl->out(false);
        }
        if (!isset($record->resourcekey)) {
            $record->resourcekey = '12345';
        }
        if (!isset($record->password)) {
            $record->password = 'secret';
        }
        if (!isset($record->grade)) {
            $record->grade = 100;
        }
        if (!isset($record->instructorchoicesendname)) {
            $record->instructorchoicesendname = 1;
        }
        if (!isset($record->instructorchoicesendemailaddr)) {
            $record->instructorchoicesendemailaddr = 1;
        }
        if (!isset($record->instructorchoiceacceptgrades)) {
            $record->instructorchoiceacceptgrades = 1;
        }
        if (!isset($record->typeid)) {
            $record->typeid = null;
        }
        return parent::create_instance($record, (array)$options);
    }

    /**
     * Create a tool proxy.
     *
     * @param array $config
     */
    public function create_tool_proxies(array $config) {
        if (!isset($config['capabilityoffered'])) {
            $config['capabilityoffered'] = '';
        }
        if (!isset($config['serviceoffered'])) {
            $config['serviceoffered'] = '';
        }
        lti_add_tool_proxy((object) $config);
    }

    /**
     * Create a tool type.
     *
     * @param array $type
     * @param array|null $config
     */
    public function create_tool_types(array $type, ?array $config = null) {
        if (!isset($type['baseurl'])) {
            throw new coding_exception('Must specify baseurl when creating a LTI tool type.');
        }
        lti_add_type((object) $type, (object) $config);
    }
}
