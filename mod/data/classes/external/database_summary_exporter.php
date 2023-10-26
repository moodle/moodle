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
 * Class for exporting partial database data.
 *
 * @package    mod_data
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_data\external;
defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;
use external_files;
use external_util;

/**
 * Class for exporting partial database data (some fields are only viewable by admins).
 *
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class database_summary_exporter extends exporter {

    protected static function define_properties() {

        return array(
            'id' => array(
                'type' => PARAM_INT,
                'description' => 'Database id'),
            'course' => array(
                'type' => PARAM_INT,
                'description' => 'Course id'),
            'name' => array(
                'type' => PARAM_RAW,
                'description' => 'Database name'),
            'intro' => array(
                'type' => PARAM_RAW,
                'description' => 'The Database intro',
            ),
            'introformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_MOODLE
            ),
            'lang' => array(
                'type' => PARAM_LANG,
                'description' => 'Forced activity language',
                'null' => NULL_ALLOWED,
            ),
            'comments' => array(
                'type' => PARAM_BOOL,
                'description' => 'comments enabled',
            ),
            'timeavailablefrom' => array(
                'type' => PARAM_INT,
                'description' => 'timeavailablefrom field',
            ),
            'timeavailableto' => array(
                'type' => PARAM_INT,
                'description' => 'timeavailableto field',
            ),
            'timeviewfrom' => array(
                'type' => PARAM_INT,
                'description' => 'timeviewfrom field',
            ),
            'timeviewto' => array(
                'type' => PARAM_INT,
                'description' => 'timeviewto field',
            ),
            'requiredentries' => array(
                'type' => PARAM_INT,
                'description' => 'requiredentries field',
            ),
            'requiredentriestoview' => array(
                'type' => PARAM_INT,
                'description' => 'requiredentriestoview field',
            ),
            'maxentries' => array(
                'type' => PARAM_INT,
                'description' => 'maxentries field',
            ),
            'rssarticles' => array(
                'type' => PARAM_INT,
                'description' => 'rssarticles field',
            ),
            'singletemplate' => array(
                'type' => PARAM_RAW,
                'description' => 'singletemplate field',
                'null' => NULL_ALLOWED,
            ),
            'listtemplate' => array(
                'type' => PARAM_RAW,
                'description' => 'listtemplate field',
                'null' => NULL_ALLOWED,
            ),
            'listtemplateheader' => array(
                'type' => PARAM_RAW,
                'description' => 'listtemplateheader field',
                'null' => NULL_ALLOWED,
            ),
            'listtemplatefooter' => array(
                'type' => PARAM_RAW,
                'description' => 'listtemplatefooter field',
                'null' => NULL_ALLOWED,
            ),
            'addtemplate' => array(
                'type' => PARAM_RAW,
                'description' => 'addtemplate field',
                'null' => NULL_ALLOWED,
            ),
            'rsstemplate' => array(
                'type' => PARAM_RAW,
                'description' => 'rsstemplate field',
                'null' => NULL_ALLOWED,
            ),
            'rsstitletemplate' => array(
                'type' => PARAM_RAW,
                'description' => 'rsstitletemplate field',
                'null' => NULL_ALLOWED,
            ),
            'csstemplate' => array(
                'type' => PARAM_RAW,
                'description' => 'csstemplate field',
                'null' => NULL_ALLOWED,
            ),
            'jstemplate' => array(
                'type' => PARAM_RAW,
                'description' => 'jstemplate field',
                'null' => NULL_ALLOWED,
            ),
            'asearchtemplate' => array(
                'type' => PARAM_RAW,
                'description' => 'asearchtemplate field',
                'null' => NULL_ALLOWED,
            ),
            'approval' => array(
                'type' => PARAM_BOOL,
                'description' => 'approval field',
            ),
            'manageapproved' => array(
                'type' => PARAM_BOOL,
                'description' => 'manageapproved field',
            ),
            'scale' => array(
                'type' => PARAM_INT,
                'description' => 'scale field',
                'optional' => true,
            ),
            'assessed' => array(
                'type' => PARAM_INT,
                'description' => 'assessed field',
                'optional' => true,
            ),
            'assesstimestart' => array(
                'type' => PARAM_INT,
                'description' => 'assesstimestart field',
                'optional' => true,
            ),
            'assesstimefinish' => array(
                'type' => PARAM_INT,
                'description' => 'assesstimefinish field',
                'optional' => true,
            ),
            'defaultsort' => array(
                'type' => PARAM_INT,
                'description' => 'defaultsort field',
            ),
            'defaultsortdir' => array(
                'type' => PARAM_INT,
                'description' => 'defaultsortdir field',
            ),
            'editany' => array(
                'type' => PARAM_BOOL,
                'description' => 'editany field (not used any more)',
                'optional' => true,
            ),
            'notification' => array(
                'type' => PARAM_INT,
                'description' => 'notification field (not used any more)',
                'optional' => true,
            ),
            'timemodified' => array(
                'type' => PARAM_INT,
                'description' => 'Time modified',
                'optional' => true,
            ),
        );
    }

    protected static function define_related() {
        return array(
            'context' => 'context'
        );
    }

    protected static function define_other_properties() {
        return array(
            'coursemodule' => array(
                'type' => PARAM_INT
            ),
            'introfiles' => array(
                'type' => external_files::get_properties_for_exporter(),
                'multiple' => true,
                'optional' => true,
            ),
        );
    }

    protected function get_other_values(renderer_base $output) {
        $context = $this->related['context'];

        $values = array(
            'coursemodule' => $context->instanceid,
            'introfiles' => external_util::get_area_files($context->id, 'mod_data', 'intro', false, false),
        );

        return $values;
    }

    /**
     * Get the formatting parameters for the intro.
     *
     * @return array
     */
    protected function get_format_parameters_for_intro() {
        return [
            'component' => 'mod_data',
            'filearea' => 'intro',
            'options' => array('noclean' => true),
        ];
    }
}
