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
 * Class for exporting content associated to a record.
 *
 * @package    mod_data
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_data\external;
defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;
use core_external\external_files;
use core_external\util as external_util;

/**
 * Class for exporting content associated to a record.
 *
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_exporter extends exporter {

    protected static function define_properties() {

        return array(
            'id' => array(
                'type' => PARAM_INT,
                'description' => 'Content id.',
            ),
            'fieldid' => array(
                'type' => PARAM_INT,
                'description' => 'The field type of the content.',
                'default' => 0,
            ),
            'recordid' => array(
                'type' => PARAM_INT,
                'description' => 'The record this content belongs to.',
                'default' => 0,
            ),
            'content' => array(
                'type' => PARAM_RAW,
                'description' => 'Contents.',
                'null' => NULL_ALLOWED,
            ),
            'content1' => array(
                'type' => PARAM_RAW,
                'description' => 'Contents.',
                'null' => NULL_ALLOWED,
            ),
            'content2' => array(
                'type' => PARAM_RAW,
                'description' => 'Contents.',
                'null' => NULL_ALLOWED,
            ),
            'content3' => array(
                'type' => PARAM_RAW,
                'description' => 'Contents.',
                'null' => NULL_ALLOWED,
            ),
            'content4' => array(
                'type' => PARAM_RAW,
                'description' => 'Contents.',
                'null' => NULL_ALLOWED,
            ),
        );
    }

    protected static function define_related() {
        return array(
            'context' => 'context',
        );
    }

    protected static function define_other_properties() {
        return array(
            'files' => array(
                'type' => external_files::get_properties_for_exporter(),
                'multiple' => true,
                'optional' => true,
            ),
        );
    }

    protected function get_other_values(renderer_base $output) {
        $values = ['files' => external_util::get_area_files($this->related['context']->id, 'mod_data', 'content', $this->data->id)];

        return $values;
    }
}
