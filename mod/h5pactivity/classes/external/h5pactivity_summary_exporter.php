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
 * Class for exporting h5p activity data.
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_h5pactivity\external;

use core\external\exporter;
use renderer_base;
use core_external\util as external_util;
use core_external\external_files;
use core_h5p\api;

/**
 * Class for exporting h5p activity data.
 *
 * @copyright  2020 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class h5pactivity_summary_exporter extends exporter {

    /**
     * Properties definition.
     *
     * @return array
     */
    protected static function define_properties() {

        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'The primary key of the record.',
            ],
            'course' => [
                'type' => PARAM_INT,
                'description' => 'Course id this h5p activity is part of.',
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'description' => 'The name of the activity module instance.',
            ],
            'timecreated' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp of when the instance was added to the course.',
                'optional' => true,
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp of when the instance was last modified.',
                'optional' => true,
            ],
            'intro' => [
                'default' => '',
                'type' => PARAM_RAW,
                'description' => 'H5P activity description.',
                'null' => NULL_ALLOWED,
            ],
            'introformat' => [
                'choices' => [FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN],
                'type' => PARAM_INT,
                'default' => FORMAT_MOODLE,
                'description' => 'The format of the intro field.',
            ],
            'grade' => [
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'The maximum grade for submission.',
                'optional' => true,
            ],
            'displayoptions' => [
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'H5P Button display options.',
            ],
            'enabletracking' => [
                'type' => PARAM_INT,
                'default' => 1,
                'description' => 'Enable xAPI tracking.',
            ],
            'grademethod' => [
                'type' => PARAM_INT,
                'default' => 1,
                'description' => 'Which H5P attempt is used for grading.',
            ],
            'contenthash' => [
                'type' => PARAM_ALPHANUM,
                'description' => 'Sha1 hash of file content.',
                'optional' => true,
            ],
        ];
    }

    /**
     * Related objects definition.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'context' => 'context',
            'factory' => 'core_h5p\\factory'
        ];
    }

    /**
     * Other properties definition.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'coursemodule' => [
                'type' => PARAM_INT
            ],
            'context' => [
                'type' => PARAM_INT
            ],
            'introfiles' => [
                'type' => external_files::get_properties_for_exporter(),
                'multiple' => true
            ],
            'package' => [
                'type' => external_files::get_properties_for_exporter(),
                'multiple' => true
            ],
            'deployedfile' => [
                'optional' => true,
                'description' => 'H5P file deployed.',
                'type' => [
                    'filename' => array(
                        'type' => PARAM_FILE,
                        'description' => 'File name.',
                        'optional' => true,
                        'null' => NULL_NOT_ALLOWED,
                    ),
                    'filepath' => array(
                        'type' => PARAM_PATH,
                        'description' => 'File path.',
                        'optional' => true,
                        'null' => NULL_NOT_ALLOWED,
                    ),
                    'filesize' => array(
                        'type' => PARAM_INT,
                        'description' => 'File size.',
                        'optional' => true,
                        'null' => NULL_NOT_ALLOWED,
                    ),
                    'fileurl' => array(
                        'type' => PARAM_URL,
                        'description' => 'Downloadable file url.',
                        'optional' => true,
                        'null' => NULL_NOT_ALLOWED,
                    ),
                    'timemodified' => array(
                        'type' => PARAM_INT,
                        'description' => 'Time modified.',
                        'optional' => true,
                        'null' => NULL_NOT_ALLOWED,
                    ),
                    'mimetype' => array(
                        'type' => PARAM_RAW,
                        'description' => 'File mime type.',
                        'optional' => true,
                        'null' => NULL_NOT_ALLOWED,
                    )
                ]
            ],
        ];
    }

    /**
     * Assign values to the defined other properties.
     *
     * @param renderer_base $output The output renderer object.
     * @return array
     */
    protected function get_other_values(renderer_base $output) {
        $context = $this->related['context'];
        $factory = $this->related['factory'];

        $values = [
            'coursemodule' => $context->instanceid,
            'context' => $context->id,
        ];

        $values['introfiles'] = external_util::get_area_files($context->id, 'mod_h5pactivity', 'intro', false, false);

        $values['package'] = external_util::get_area_files($context->id, 'mod_h5pactivity', 'package', false, true);

        // Only if this H5P activity has been deployed, return the exported file.
        $fileh5p = api::get_export_info_from_context_id($context->id, $factory, 'mod_h5pactivity', 'package');
        if ($fileh5p) {
            $values['deployedfile'] = $fileh5p;
        }

        return $values;
    }

    /**
     * Get the formatting parameters for the intro.
     *
     * @return array with the formatting parameters
     */
    protected function get_format_parameters_for_intro() {
        return [
            'component' => 'mod_h5pactivity',
            'filearea' => 'intro',
            'options' => ['noclean' => true],
        ];
    }

    /**
     * Get the formatting parameters for the package.
     *
     * @return array with the formatting parameters
     */
    protected function get_format_parameters_for_package() {
        return [
            'component' => 'mod_h5pactivity',
            'filearea' => 'package',
            'itemid' => 0,
            'options' => ['noclean' => true],
        ];
    }
}
