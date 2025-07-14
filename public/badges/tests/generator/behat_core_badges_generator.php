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

declare(strict_types=1);

/**
 * Badges test generator for Behat
 *
 * @package     core_badges
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_core_badges_generator extends behat_generator_base {

    /**
     * Get a list of the entities that can be created for this component
     *
     * @return array[]
     */
    protected function get_creatable_entities(): array {
        return [
            'Badges' => [
                'singular' => 'Badge',
                'datagenerator' => 'badge',
                'required' => [
                    'name',
                    'image',
                ],
                'switchids' => [
                    'course' => 'courseid',
                ],
            ],
            'Criterias' => [
                'singular' => 'Criteria',
                'datagenerator' => 'criteria',
                'required' => [
                    'badge',
                    'role',
                ],
                'switchids' => [
                    'badge' => 'badgeid',
                    'role' => 'roleid',
                ],
            ],
            'Issued badges' => [
                'singular' => 'Issued badge',
                'datagenerator' => 'issued_badge',
                'required' => [
                    'badge',
                    'user',
                ],
                'switchids' => [
                    'badge' => 'badgeid',
                    'user' => 'userid',
                ],
            ],
        ];
    }

    /**
     * Look up badge ID from given name
     *
     * @param string $name
     * @return int
     */
    protected function get_badge_id(string $name): int {
        global $DB;

        return (int) $DB->get_field('badge', 'id', ['name' => $name], MUST_EXIST);
    }

    /**
     * Pre-process badge entity
     *
     * @param array $badge
     * @return array
     */
    protected function preprocess_badge(array $badge): array {
        global $CFG;

        require_once("{$CFG->libdir}/badgeslib.php");

        // Allow text status' that correspond to badge constants.
        if (array_key_exists('status', $badge) && !is_numeric($badge['status'])) {
            $badge['status'] = constant('BADGE_STATUS_' . strtoupper($badge['status']));
        }

        return $badge;
    }
}
