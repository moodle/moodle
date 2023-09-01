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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\v1p1\factories;

use enrol_oneroster\local\factories\collection_factory as parent_collection_factory;
// Entities which resemble a class.
use enrol_oneroster\local\v1p1\collections\classes_for_user as classes_for_user_collection;
use enrol_oneroster\local\entities\user as user_entity;
use enrol_oneroster\local\filter;

/**
 * One Roster v1p1 Collection Factory.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class collection_factory extends parent_collection_factory {

    /**
     * Fetch a collection of classes for a user.
     *
     * The user id is automatically filled. Additional parameters can be supplied.
     *
     * @param   user_entity $user The user to fetch classes for
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  classes_for_user_collection
     */
    public function get_classes_for_user(
        user_entity $user,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): classes_for_user_collection {
        return new classes_for_user_collection(
            $this->container,
            array_merge([
                ':user_id' => $user->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }
}
