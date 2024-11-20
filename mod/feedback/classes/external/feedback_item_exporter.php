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
 * Class for exporting a feedback item (question).
 *
 * @package    mod_feedback
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_feedback\external;
defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;
use core_files\external\stored_file_exporter;

/**
 * Class for exporting a feedback item (question).
 *
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback_item_exporter extends exporter {

    protected static function define_properties() {
        return array(
            'id' => array(
                'type' => PARAM_INT,
                'description' => 'The record id.',
            ),
            'feedback' => array(
                'type' => PARAM_INT,
                'description' => 'The feedback instance id this records belongs to.',
                'default' => 0,
            ),
            'template' => array(
                'type' => PARAM_INT,
                'description' => 'If it belogns to a template, the template id.',
                'default' => 0,
            ),
            'name' => array(
                'type' => PARAM_RAW,
                'description' => 'The item name.',
            ),
            'label' => array(
                'type' => PARAM_NOTAGS,
                'description' => 'The item label.',
            ),
            'presentation' => array(
                'type' => PARAM_RAW,
                'description' => 'The text describing the item or the available possible answers.',
            ),
            'typ' => array(
                'type' => PARAM_ALPHA,
                'description' => 'The type of the item.',
            ),
            'hasvalue' => array(
                'type' => PARAM_INT,
                'description' => 'Whether it has a value or not.',
                'default' => 0,
            ),
            'position' => array(
                'type' => PARAM_INT,
                'description' => 'The position in the list of questions.',
                'default' => 0,
            ),
            'required' => array(
                'type' => PARAM_BOOL,
                'description' => 'Whether is a item (question) required or not.',
                'default' => 0,
            ),
            'dependitem' => array(
                'type' => PARAM_INT,
                'description' => 'The item id this item depend on.',
                'default' => 0,
            ),
            'dependvalue' => array(
                'type' => PARAM_RAW,
                'description' => 'The depend value.',
            ),
            'options' => array(
                'type' => PARAM_ALPHA,
                'description' => 'Different additional settings for the item (question).',
            ),
        );
    }

    protected static function define_related() {
        return array(
            'context' => 'context',
            'itemnumber' => 'int?'
        );
    }

    protected static function define_other_properties() {
        return array(
            'itemfiles' => array(
                'type' => stored_file_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'itemnumber' => array(
                'type' => PARAM_INT,
                'description' => 'The item position number',
                'null' => NULL_ALLOWED
            ),
            'otherdata' => array(
                'type' => PARAM_RAW,
                'description' => 'Additional data that may be required by external functions',
                'null' => NULL_ALLOWED
            ),
        );
    }

    protected function get_other_values(renderer_base $output) {
        $context = $this->related['context'];

        $itemobj = feedback_get_item_class($this->data->typ);
        $values = array(
            'itemfiles' => array(),
            'itemnumber' => $this->related['itemnumber'],
            'otherdata' => $itemobj->get_data_for_external($this->data),
        );

        $fs = get_file_storage();
        $files = array();
        $itemfiles = $fs->get_area_files($context->id, 'mod_feedback', 'item', $this->data->id, 'filename', false);
        if (!empty($itemfiles)) {
            foreach ($itemfiles as $storedfile) {
                $fileexporter = new stored_file_exporter($storedfile, array('context' => $context));
                $files[] = $fileexporter->export($output);
            }
            $values['itemfiles'] = $files;
        }

        return $values;
    }

    /**
     * Get the formatting parameters for the name.
     *
     * @return array
     */
    protected function get_format_parameters_for_name() {
        return [
            'component' => 'mod_feedback',
            'filearea' => 'item',
            'itemid' => $this->data->id
        ];
    }

    /**
     * Get the formatting parameters for the presentation.
     *
     * @return array
     */
    protected function get_format_parameters_for_presentation() {
        return [
            'component' => 'mod_feedback',
            'filearea' => 'item',
            'itemid' => $this->data->id
        ];
    }
}
