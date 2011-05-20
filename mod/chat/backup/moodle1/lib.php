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
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 * Based off of a template @ http://docs.moodle.org/en/Development:Backup_1.9_conversion_for_developers
 *
 * @package    mod
 * @subpackage chat
 * @copyright  2011 Aparup Banerjee <aparup@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Chat conversion handler
 */
class moodle1_mod_chat_handler extends moodle1_mod_handler {

    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * The method returns list of {@link convert_path} instances.
     * For each path returned, the corresponding conversion method must be
     * defined.
     *
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/CHAT does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path(
                'chat', '/MOODLE_BACKUP/COURSE/MODULES/MOD/CHAT',
                array(
                    'newfields' => array(
                        'introformat' => 0
                    )
                )
            )
        );
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/CHAT
     * data available
     */
    public function process_chat($data) {
        // get the course module id and context id
        $instanceid = $data['id'];
        $moduleid   = $this->get_moduleid($instanceid);
        $contextid  = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        // we now have all information needed to start writing into the file
        $this->open_xml_writer("activities/chat_{$moduleid}/chat.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'chat', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('chat', array('id' => $instanceid));

        unset($data['id']); // we already write it as attribute, do not repeat it as child element
        foreach ($data as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/CHAT/MESSAGES
     * data available
     */
    public function process_chat_messages($data) {
        //@todo process user data
        //$this->write_xml('message', $data, array('/message/id'));
        //@todo populate chat_messages_current table?
    }

    /**
     * This is executed when we reach the closing </MOD> tag of our 'chat' path
     */
    public function on_chat_end() {
        $this->xmlwriter->end_tag('chat');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();
    }
}
