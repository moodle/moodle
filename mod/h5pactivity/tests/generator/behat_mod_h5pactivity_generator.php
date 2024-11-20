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
 * H5P-related steps definitions.
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_h5pactivity_generator extends behat_generator_base {

    protected function get_creatable_entities(): array {
        return [
            'attempts' => [
                'singular' => 'attempt',
                'datagenerator' => 'attempt',
                'required' => ['h5pactivity', 'user'],
                'switchids' => ['h5pactivity' => 'h5pactivityid', 'user' => 'userid'],
            ],
        ];
    }

    /**
     * Look up the id of a h5p from its name.
     *
     * @param string $h5pname the activity name, for example 'Test h5p'.
     * @return int corresponding id
     */
    protected function get_h5pactivity_id(string $h5pname): int {
        global $DB;

        if (!$id = $DB->get_field('h5pactivity', 'id', ['name' => $h5pname])) {
            throw new Exception('There is no h5p activity with name "' . $h5pname);
        }
        return $id;
    }
}
