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
 * Class for exporting partial lesson data.
 *
 * @package    mod_lesson
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lesson\external;
defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;
use external_files;
use external_util;

/**
 * Class for exporting partial lesson data (some fields are only viewable by admins).
 *
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lesson_summary_exporter extends exporter {

    protected static function define_properties() {

        return array(
            'id' => array(
                'type' => PARAM_INT,
                'description' => 'Standard Moodle primary key.'
            ),
            'course' => array(
                'type' => PARAM_INT,
                'description' => 'Foreign key reference to the course this lesson is part of.'
            ),
            'coursemodule' => array(
                'type' => PARAM_INT,
                'description' => 'Course module id.'
            ),
            'name' => array(
                'type' => PARAM_RAW,
                'description' => 'Lesson name.'
            ),
            'intro' => array(
                'type' => PARAM_RAW,
                'description' => 'Lesson introduction text.',
                'optional' => true,
            ),
            'introformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_MOODLE
            ),
            'practice' => array(
                'type' => PARAM_BOOL,
                'description' => 'Practice lesson?',
                'optional' => true,
            ),
            'modattempts' => array(
                'type' => PARAM_BOOL,
                'description' => 'Allow student review?',
                'optional' => true,
            ),
            'usepassword' => array(
                'type' => PARAM_BOOL,
                'description' => 'Password protected lesson?',
                'optional' => true,
            ),
            'password' => array(
                'type' => PARAM_RAW,
                'description' => 'Password',
                'optional' => true,
            ),
            'dependency' => array(
                'type' => PARAM_INT,
                'description' => 'Dependent on (another lesson id)',
                'optional' => true,
            ),
            'conditions' => array(
                'type' => PARAM_RAW,
                'description' => 'Conditions to enable the lesson',
                'optional' => true,
            ),
            'grade' => array(
                'type' => PARAM_INT,
                'description' => 'The total that the grade is scaled to be out of',
                'optional' => true,
            ),
            'custom' => array(
                'type' => PARAM_BOOL,
                'description' => 'Custom scoring?',
                'optional' => true,
            ),
            'ongoing' => array(
                'type' => PARAM_BOOL,
                'description' => 'Display ongoing score?',
                'optional' => true,
            ),
            'usemaxgrade' => array(
                'type' => PARAM_INT,
                'description' => 'How to calculate the final grade',
                'optional' => true,
            ),
            'maxanswers' => array(
                'type' => PARAM_INT,
                'description' => 'Maximum answers per page',
                'optional' => true,
            ),
            'maxattempts' => array(
                'type' => PARAM_INT,
                'description' => 'Maximum attempts',
                'optional' => true,
            ),
            'review' => array(
                'type' => PARAM_BOOL,
                'description' => 'Provide option to try a question again',
                'optional' => true,
            ),
            'nextpagedefault' => array(
                'type' => PARAM_INT,
                'description' => 'Action for a correct answer',
                'optional' => true,
            ),
            'feedback' => array(
                'type' => PARAM_BOOL,
                'description' => 'Display default feedback',
                'optional' => true,
            ),
            'minquestions' => array(
                'type' => PARAM_INT,
                'description' => 'Minimum number of questions',
                'optional' => true,
            ),
            'maxpages' => array(
                'type' => PARAM_INT,
                'description' => 'Number of pages to show',
                'optional' => true,
            ),
            'timelimit' => array(
                'type' => PARAM_INT,
                'description' => 'Time limit',
                'optional' => true,
            ),
            'retake' => array(
                'type' => PARAM_BOOL,
                'description' => 'Re-takes allowed',
                'optional' => true,
            ),
            'activitylink' => array(
                'type' => PARAM_INT,
                'description' => 'Id of the next activity to be linked once the lesson is completed',
                'optional' => true,
            ),
            'mediafile' => array(
                'type' => PARAM_RAW,
                'description' => 'Local file path or full external URL',
                'optional' => true,
            ),
            'mediaheight' => array(
                'type' => PARAM_INT,
                'description' => 'Popup for media file height',
                'optional' => true,
            ),
            'mediawidth' => array(
                'type' => PARAM_INT,
                'description' => 'Popup for media with',
                'optional' => true,
            ),
            'mediaclose' => array(
                'type' => PARAM_INT,
                'description' => 'Display a close button in the popup?',
                'optional' => true,
            ),
            'slideshow' => array(
                'type' => PARAM_BOOL,
                'description' => 'Display lesson as slideshow',
                'optional' => true,
            ),
            'width' => array(
                'type' => PARAM_INT,
                'description' => 'Slideshow width',
                'optional' => true,
            ),
            'height' => array(
                'type' => PARAM_INT,
                'description' => 'Slideshow height',
                'optional' => true,
            ),
            'bgcolor' => array(
                'type' => PARAM_TEXT,
                'description' => 'Slideshow bgcolor',
                'optional' => true,
            ),
            'displayleft' => array(
                'type' => PARAM_BOOL,
                'description' => 'Display left pages menu?',
                'optional' => true,
            ),
            'displayleftif' => array(
                'type' => PARAM_INT,
                'description' => 'Minimum grade to display menu',
                'optional' => true,
            ),
            'progressbar' => array(
                'type' => PARAM_BOOL,
                'description' => 'Display progress bar?',
                'optional' => true,
            ),
            'available' => array(
                'type' => PARAM_INT,
                'description' => 'Available from',
                'optional' => true,
            ),
            'deadline' => array(
                'type' => PARAM_INT,
                'description' => 'Available until',
                'optional' => true,
            ),
            'timemodified' => array(
                'type' => PARAM_INT,
                'description' => 'Last time settings were updated',
                'optional' => true,
            ),
            'completionendreached' => array(
                'type' => PARAM_INT,
                'description' => 'Require end reached for completion?',
                'optional' => true,
            ),
            'completiontimespent' => array(
                'type' => PARAM_INT,
                'description' => 'Student must do this activity at least for',
                'optional' => true,
             ),
            'allowofflineattempts' => array(
                'type' => PARAM_BOOL,
                'description' => 'Whether to allow the lesson to be attempted offline in the mobile app',
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
            'mediafiles' => array(
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
        );

        if (isset($this->data->intro)) {
            $values['introfiles'] = external_util::get_area_files($context->id, 'mod_lesson', 'intro', false, false);
            $values['mediafiles'] = external_util::get_area_files($context->id, 'mod_lesson', 'mediafile', 0);
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
            'component' => 'mod_lesson',
            'filearea' => 'intro',
        ];
    }
}
