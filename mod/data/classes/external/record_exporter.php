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
 * Class for exporting record data.
 *
 * @package    mod_data
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_data\external;
defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;
use core_user;
use core_tag\external\tag_item_exporter;

/**
 * Class for exporting record data.
 *
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class record_exporter extends exporter {

    protected static function define_properties() {

        return array(
            'id' => array(
                'type' => PARAM_INT,
                'description' => 'Record id.',
            ),
            'userid' => array(
                'type' => PARAM_INT,
                'description' => 'The id of the user who created the record.',
                'default' => 0,
            ),
            'groupid' => array(
                'type' => PARAM_INT,
                'description' => 'The group id this record belongs to (0 for no groups).',
                'default' => 0,
            ),
            'dataid' => array(
                'type' => PARAM_INT,
                'description' => 'The database id this record belongs to.',
                'default' => 0,
            ),
            'timecreated' => array(
                'type' => PARAM_INT,
                'description' => 'Time the record was created.',
                'default' => 0,
            ),
            'timemodified' => array(
                'type' => PARAM_INT,
                'description' => 'Last time the record was modified.',
                'default' => 0,
            ),
            'approved' => array(
                'type' => PARAM_BOOL,
                'description' => 'Whether the entry has been approved (if the database is configured in that way).',
                'default' => 0,
            ),
        );
    }

    protected static function define_related() {
        return array(
            'database' => 'stdClass',
            'user' => 'stdClass?',
            'context' => 'context',
            'contents' => 'stdClass[]?',
        );
    }

    protected static function define_other_properties() {
        return array(
            'canmanageentry' => array(
                'type' => PARAM_BOOL,
                'description' => 'Whether the current user can manage this entry',
            ),
            'fullname' => array(
                'type' => PARAM_TEXT,
                'description' => 'The user who created the entry fullname.',
                'optional' => true,
            ),
            'contents' => array(
                'type' => content_exporter::read_properties_definition(),
                'description' => 'The record contents.',
                'multiple' => true,
                'optional' => true,
            ),
            'tags' => array(
                'type' => tag_item_exporter::read_properties_definition(),
                'description' => 'Tags.',
                'multiple' => true,
                'optional' => true,
            ),
        );
    }

    protected function get_other_values(renderer_base $output) {
        global $PAGE;

        $values = array(
            'canmanageentry' => data_user_can_manage_entry($this->data, $this->related['database'], $this->related['context']),
        );

        if (!empty($this->related['user']) and !empty($this->related['user']->id)) {
            $values['fullname'] = fullname($this->related['user']);
        } else if ($this->data->userid) {
            $user = core_user::get_user($this->data->userid);
            $values['fullname'] = fullname($user);
        }

        if (!empty($this->related['contents'])) {
            $contents = [];
            foreach ($this->related['contents'] as $content) {
                $related = array('context' => $this->related['context']);
                $exporter = new content_exporter($content, $related);
                $contents[] = $exporter->export($PAGE->get_renderer('core'));
            }
            $values['contents'] = $contents;
        }

        $values['tags'] = \core_tag\external\util::get_item_tags('mod_data', 'data_records', $this->data->id);

        return $values;
    }
}
