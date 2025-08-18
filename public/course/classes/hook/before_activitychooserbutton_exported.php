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

use cm_info;
use section_info;
use core\hook\described_hook;
use core_courseformat\output\local\content\activitychooserbutton;

/**
 * Hook before activity chooser button export.
 *
 * @package    core_course
 * @copyright  2024 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class before_activitychooserbutton_exported implements described_hook {
    /**
     * Constructor.
     *
     * @param activitychooserbutton $activitychooserbutton the activity chooser button output
     * @param section_info $section the course section
     * @param cm_info|null $cm the course module
     */
    public function __construct(
        /** @var activitychooserbutton the activity chooser button output */
        protected activitychooserbutton $activitychooserbutton,
        /** @var section_info the course section */
        protected section_info $section,
        /** @var cm_info|null the course module */
        protected ?cm_info $cm = null,
    ) {
    }

    /**
     * Describes the hook purpose.
     *
     * @return string
     */
    public static function get_hook_description(): string {
        return 'This hook is triggered when a activity chooser button is exported.';
    }

    /**
     * List of tags that describe this hook.
     *
     * @return string[]
     */
    public static function get_hook_tags(): array {
        return ['course'];
    }

    /**
     * Get activitychooserbutton output instance.
     *
     * @return activitychooserbutton
     */
    public function get_activitychooserbutton(): activitychooserbutton {
        return $this->activitychooserbutton;
    }

    /**
     * Get course section instance.
     *
     * @return section_info
     */
    public function get_section(): section_info {
        return $this->section;
    }

    /**
     * Get course module instance.
     *
     * @return cm_info|null
     */
    public function get_cm(): ?cm_info {
        return $this->cm;
    }
}
