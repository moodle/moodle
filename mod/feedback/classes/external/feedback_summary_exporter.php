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
 * Class for exporting partial feedback data.
 *
 * @package    mod_feedback
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_feedback\external;
defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;
use external_util;
use external_files;

/**
 * Class for exporting partial feedback data (some fields are only viewable by admins).
 *
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback_summary_exporter extends exporter {

    protected static function define_properties() {
        return array(
            'id' => array(
                'type' => PARAM_INT,
                'description' => 'The primary key of the record.',
            ),
            'course' => array(
                'type' => PARAM_INT,
                'description' => 'Course id this feedback is part of.',
            ),
            'name' => array(
                'type' => PARAM_TEXT,
                'description' => 'Feedback name.',
            ),
            'intro' => array(
                'default' => '',
                'type' => PARAM_RAW,
                'description' => 'Feedback introduction text.',
            ),
            'introformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_MOODLE,
                'description' => 'Feedback intro text format.',
            ),
            'anonymous' => array(
                'type' => PARAM_INT,
                'description' => 'Whether the feedback is anonymous.',
            ),
            'email_notification' => array(
                'type' => PARAM_BOOL,
                'optional' => true,
                'description' => 'Whether email notifications will be sent to teachers.',
            ),
            'multiple_submit' => array(
                'default' => 1,
                'type' => PARAM_BOOL,
                'description' => 'Whether multiple submissions are allowed.',
            ),
            'autonumbering' => array(
                'default' => 1,
                'type' => PARAM_BOOL,
                'description' => 'Whether questions should be auto-numbered.',
            ),
            'site_after_submit' => array(
                'type' => PARAM_TEXT,
                'optional' => true,
                'description' => 'Link to next page after submission.',
            ),
            'page_after_submit' => array(
                'type' => PARAM_RAW,
                'optional' => true,
                'description' => 'Text to display after submission.',
            ),
            'page_after_submitformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_MOODLE,
                'description' => 'Text to display after submission format.',
            ),
            'publish_stats' => array(
                'default' => 0,
                'type' => PARAM_BOOL,
                'description' => 'Whether stats should be published.',
            ),
            'timeopen' => array(
                'type' => PARAM_INT,
                'optional' => true,
                'description' => 'Allow answers from this time.',
            ),
            'timeclose' => array(
                'type' => PARAM_INT,
                'optional' => true,
                'description' => 'Allow answers until this time.',
            ),
            'timemodified' => array(
                'type' => PARAM_INT,
                'optional' => true,
                'description' => 'The time this record was modified.',
            ),
            'completionsubmit' => array(
                'default' => 0,
                'type' => PARAM_BOOL,
                'description' => 'If this field is set to 1, then the activity will be automatically marked as complete on submission.',
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
                'multiple' => true
            ),
            'pageaftersubmitfiles' => array(
                'type' => external_files::get_properties_for_exporter(),
                'multiple' => true,
                'optional' => true
            ),
        );
    }

    protected function get_other_values(renderer_base $output) {
        $context = $this->related['context'];

        $values = array(
            'coursemodule' => $context->instanceid,
        );

        $values['introfiles'] = external_util::get_area_files($context->id, 'mod_feedback', 'intro', false, false);

        if (!empty($this->data->page_after_submit)) {
            $values['pageaftersubmitfiles'] = external_util::get_area_files($context->id, 'mod_feedback', 'page_after_submit');
        }

        return $values;
    }

    /**
     * Get the formatting parameters for the intro.
     *
     * @return array
     */
    protected function get_format_parameters_for_intro() {
        return [
            'component' => 'mod_feedback',
            'filearea' => 'intro',
            'options' => array('noclean' => true),
        ];
    }

    /**
     * Get the formatting parameters for the page_after_submit.
     *
     * @return array
     */
    protected function get_format_parameters_for_page_after_submit() {
        return [
            'component' => 'mod_feedback',
            'filearea' => 'page_after_submit',
            'itemid' => 0
        ];
    }
}
