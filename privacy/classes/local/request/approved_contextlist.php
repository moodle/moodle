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
 * An implementation of a contextlist which has been filtered and approved.
 *
 * @package    core_privacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_privacy\local\request;

defined('MOODLE_INTERNAL') || die();

/**
 * An implementation of a contextlist which has been filtered and approved.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class approved_contextlist extends contextlist_base {

    /**
     * @var \stdClass The user this contextlist belongs to.
     */
    protected $user;

    /**
     * Create a new approved contextlist.
     *
     * @param   \stdClass       $user The user record.
     * @param   string          $component the frankenstyle component name.
     * @param   \int[]          $contextids The list of contextids present in this list.
     */
    public function __construct(\stdClass $user, string $component, array $contextids) {
        $this->set_user($user);
        $this->set_component($component);
        $this->set_contextids($contextids);
    }

    /**
     * Specify the user which owns this request.
     *
     * @param   \stdClass       $user The user record.
     * @return  $this
     */
    protected function set_user(\stdClass $user): approved_contextlist {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the user which requested their data.
     *
     * @return  \stdClass
     */
    public function get_user(): \stdClass {
        return $this->user;
    }
}
