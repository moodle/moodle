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
 * Behat data generator for enrol_lti.
 *
 * @package    enrol_lti
 * @category   test
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_enrol_lti_generator extends behat_generator_base {

    /**
     * Get a list of the entities that Behat can create using the generator step.
     *
     * @return array the list of creatable entities.
     */
    protected function get_creatable_entities(): array {
        return [
            'application registrations' => [
                'singular' => 'application registration',
                'datagenerator' => 'application_registration',
                'required' => ['name']
            ],
            'published resources' => [
                'singular' => 'published resource',
                'datagenerator' => 'published_resource',
                'required' => ['name', 'activity', 'course'],
                'switchids' => ['activity' => 'activityid', 'course' => 'courseid']
            ]
        ];
    }
}
