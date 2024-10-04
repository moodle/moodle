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

namespace core_backup\hook;

use MoodleQuickForm;

/**
 * Hook to allow adding extra fields to the copy course form.
 * This should be used together with core_backup\hook\copy_helper_process_formdata
 *
 * @package core_backup
 * @copyright 2024 Monash University (https://www.monash.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Use to add extra elements to the copy course form.')]
#[\core\attribute\tags('backup')]
final class after_copy_form_definition {

    /** @var MoodleQuickForm */
    public readonly MoodleQuickForm $mform;

    /**
     * Constructor.
     *
     * @param MoodleQuickForm $mform
     */
    public function __construct(MoodleQuickForm $mform) {
        $this->mform = $mform;
    }
}
