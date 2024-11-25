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

namespace core_question\output;

use context_course;
use core_question\local\bank\question_bank_helper;
use renderer_base;
use single_button;
use stdClass;

/**
 * Create the management view of shared and non-shared banks.
 *
 * @package    core_question
 * @copyright  2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Simon Adams <simon.adams@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_banks implements \templatable, \renderable {

    /**
     * Create a new view_banks instance.
     *
     * @param array $sharedbanks {@see question_bank_helper::get_activity_instances_with_shareable_questions()}
     * @param array $privatebanks {@see question_bank_helper::get_activity_instances_with_private_questions()}
     * @param stdClass $course the viewing course.
     */
    public function __construct(
        /** @var array Banks that can be shared */
        protected readonly array $sharedbanks,
        /** @var array Banks that cannot be shared */
        protected readonly array $privatebanks,
        /** @var stdClass current course object */
        protected readonly stdClass $course,
    ) {
    }

    /**
     * Create a list of shared and non-shared banks for the template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        $sharedbanksrenderable = new question_bank_list($this->sharedbanks);
        $sharedbankscontext = $sharedbanksrenderable->export_for_template($output);
        $privatebanksrenderable = new question_bank_list($this->privatebanks);
        $privatebankscontext = $privatebanksrenderable->export_for_template($output);
        $defaultbankname = question_bank_helper::get_default_question_bank_activity_name();
        $customqbankmods = array_filter(question_bank_helper::get_activity_types_with_shareable_questions(),
            static fn($plugin) => $plugin !== $defaultbankname
        );
        $addqbanklink = new single_button(
            new \moodle_url('/course/modedit.php', [
                'add' => $defaultbankname,
                'course' => $this->course->id,
                'section' => 0,
                'return' => 0,
                'sr' => 0,
                'beforemod' => 0,
            ]),
            get_string('add', 'core'),
            'post',
            single_button::BUTTON_PRIMARY
        );

        $addcustombanksrenderable = new add_bank_list($this->course, $customqbankmods);
        $createdefaultrenderable = new single_button(
            question_bank_helper::get_url_for_qbank_list($this->course->id, true),
            get_string('createdefault', 'question')
        );

        $cancreatedefault = has_capability('moodle/course:manageactivities', context_course::instance($this->course->id));

        return [
            'addqbank' => $addqbanklink->export_for_template($output),
            'hassharedbanks' => !empty($sharedbankscontext),
            'sharedbanks' => $sharedbankscontext,
            'hasprivatebanks' => !empty($privatebankscontext),
            'privatebanks' => $privatebankscontext,
            'addcustombanks' => $addcustombanksrenderable->export_for_template($output),
            'createdefault' => $cancreatedefault ? $createdefaultrenderable->export_for_template($output) : false,
        ];
    }
}
