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

namespace core_course\hook;

use core\hook\described_hook;
use course_edit_form;
use MoodleQuickForm;

/**
 * Allows plugins to extend course form after data is set.
 *
 * @see course_edit_form::definition_after_data()
 *
 * @package    core
 * @copyright  2023 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class after_form_definition_after_data implements described_hook {

    /**
     * Course form wrapper.
     *
     * @var course_edit_form
     */
    protected $formwrapper;

    /**
     * Form to be extended.
     *
     * @var \MoodleQuickForm
     */
    protected $mform;

    /**
     * Creates new hook.
     *
     * @param course_edit_form $formwrapper Course form wrapper..
     * @param MoodleQuickForm $mform Form to be extended.
     */
    public function __construct(course_edit_form $formwrapper, MoodleQuickForm $mform) {
        $this->formwrapper = $formwrapper;
        $this->mform = $mform;
    }

    /**
     * Returns form.
     *
     * @return MoodleQuickForm
     */
    public function get_mform(): MoodleQuickForm {
        return $this->mform;
    }

    /**
     * Returns form wrapper instance.
     *
     * @return course_edit_form
     */
    public function get_formwrapper(): course_edit_form {
        return $this->formwrapper;
    }

    /**
     * Describes the hook purpose.
     *
     * @return string
     */
    public static function get_hook_description(): string {
        return 'Allows plugins to extend course editing form after data is set';
    }

    /**
     * List of tags that describe this hook.
     *
     * @return string[]
     */
    public static function get_hook_tags(): array {
        return ['course'];
    }
}
