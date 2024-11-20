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
 * An implementation of a userlist which has been filtered and approved.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy;

defined('MOODLE_INTERNAL') || die();

/**
 * An implementation of a userlist which can be filtered by role.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filtered_userlist extends \core_privacy\local\request\approved_userlist {

    /**
     * Apply filters to only remove users in the expireduserids list, and to remove any who are in the unexpired list.
     * The unexpired list wins where a user is in both lists.
     *
     * @param   int[]   $expireduserids The list of userids for users who should be expired.
     * @param   int[]   $unexpireduserids The list of userids for those users who should not be expired.
     * @return  $this
     */
    public function apply_expired_context_filters(array $expireduserids, array $unexpireduserids): filtered_userlist {
        // The current userlist content.
        $userids = $this->get_userids();

        if (!empty($expireduserids)) {
            // Now remove any not on the list of expired users.
            $userids = array_intersect($userids, $expireduserids);
        }

        if (!empty($unexpireduserids)) {
            // Remove any on the list of unexpiredusers users.
            $userids = array_diff($userids, $unexpireduserids);
        }

        $this->set_userids($userids);

        return $this;
    }
}
