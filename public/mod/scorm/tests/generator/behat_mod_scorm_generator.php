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
 * Behat data generator for mod_scorm.
 *
 * @package    mod_scorm
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_scorm_generator extends behat_generator_base {
    /**
     * Get a list of the entities that Behat can create using the generator step.
     *
     * @return array
     */
    protected function get_creatable_entities(): array {
        return [
            'grades' => [
                'singular' => 'grades',
                'datagenerator' => 'grade',
                'required' => ['scorm', 'user'],
                'switchids' => ['scorm' => 'scormid', 'user' => 'userid'],
            ],
            'attempts' => [
                'singular' => 'attempt',
                'datagenerator' => 'attempt',
                'required' => ['scorm', 'user'],
                'switchids' => ['scorm' => 'scormid', 'user' => 'userid'],
            ],
        ];
    }

    /**
     * Get the scorm cmid using an activity name or idnumber.
     *
     * @param string $identifier activity name or idnumber
     * @return int The scorm instance id of the scorm activity.
     */
    protected function get_scorm_id(string $identifier): int {
        $cm = $this->get_cm_by_activity_name('scorm', $identifier);
        $manager = \mod_scorm\manager::create_from_coursemodule($cm);
        return $manager->get_instance()->id;
    }
}
