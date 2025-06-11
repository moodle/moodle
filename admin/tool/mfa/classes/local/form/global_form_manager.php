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

namespace tool_mfa\local\form;

use tool_mfa\plugininfo\factor;

/**
 * MFA login form
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class global_form_manager {
    /** @var array factors to call hooks upon. */
    private $activefactors;

    /**
     * Create an instance of this class.
     */
    public function __construct() {
        $this->activefactors = factor::get_active_user_factor_types();
    }

    /**
     * Hook point for global auth form action hooks.
     *
     * @param \MoodleQuickForm $mform Form to inject global elements into.
     * @return void
     */
    public function definition(\MoodleQuickForm &$mform): void {
        foreach ($this->activefactors as $factor) {
            $factor->global_definition($mform);
        }
    }

    /**
     * Hook point for global auth form action hooks.
     *
     * @param \MoodleQuickForm $mform Form to inject global elements into.
     * @return void
     */
    public function definition_after_data(\MoodleQuickForm &$mform): void {
        foreach ($this->activefactors as $factor) {
            $factor->global_definition_after_data($mform);
        }
    }

    /**
     * Hook point for global auth form action hooks.
     *
     * @param array $data Data from the form.
     * @param array $files Files form the form.
     * @return array of errors from validation.
     */
    public function validation(array $data, array $files): array {
        $errors = [];
        foreach ($this->activefactors as $factor) {
            $errors = array_merge($errors, $factor->global_validation($data, $files));
        }
        return $errors;
    }

    /**
     * Hook point for global auth form submission hooks.
     *
     * @param \stdClass $data Data from the form.
     * @return void
     */
    public function submit(\stdClass $data): void {
        foreach ($this->activefactors as $factor) {
            $factor->global_submit($data);
        }
    }
}
