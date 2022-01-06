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
 * @package    core_privacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_privacy\local\request;

defined('MOODLE_INTERNAL') || die();

/**
 * An implementation of a userlist which has been filtered and approved.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class approved_userlist extends userlist_base {

    /**
     * Create a new approved userlist.
     *
     * @param   \context        $context The context.
     * @param   string          $component the frankenstyle component name.
     * @param   \int[]          $userids The list of userids present in this list.
     */
    public function __construct(\context $context, string $component, array $userids) {
        parent::__construct($context, $component);

        $this->set_userids($userids);
    }

    /**
     * Create an approved userlist from a userlist.
     *
     * @param   userlist        $userlist The source list
     * @return  approved_userlist   The newly created approved userlist.
     */
    public static function create_from_userlist(userlist $userlist) : approved_userlist {
        $newlist = new static($userlist->get_context(), $userlist->get_component(), $userlist->get_userids());

        return $newlist;
    }
}
