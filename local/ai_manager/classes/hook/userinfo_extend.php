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

namespace local_ai_manager\hook;

use coding_exception;
use local_ai_manager\local\userinfo;

/**
 * Hook for providing information for the rights config table filter.
 *
 * This hook will be dispatched when it's rendering the rights config table.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins to override the behavior of the userinfo class.')]
#[\core\attribute\tags('local_ai_manager')]
class userinfo_extend {

    /** @var ?int Stores the default role in the hook object */
    private ?int $defaultrole = null;

    /**
     * Constructor for the hook.
     *
     * @param int $userid the id of the user to determine the default role for
     */
    public function __construct(
            /** @var int $userid the id of the user to determine the default role for */
            private int $userid
    ) {
    }

    /**
     * Standard getter.
     *
     * @return int the userid which is stored in this hook object
     */
    public function get_userid(): int {
        return $this->userid;
    }

    /**
     * Standard getter.
     *
     * @return ?int the currently set default role, or null if none has been set by any hook callbacks
     */
    public function get_default_role(): ?int {
        return $this->defaultrole;
    }

    /**
     * Setter to allow hook callbacks to define a default role.
     *
     * @param int $defaultrole the default role
     * @throws coding_exception if the default role is none of {@see userinfo::ROLE_BASIC},
     *  {@see userinfo::ROLE_EXTENDED}, {@see userinfo::ROLE_UNLIMITED}
     */
    public function set_default_role(int $defaultrole): void {
        if (!in_array($defaultrole, [userinfo::ROLE_BASIC, userinfo::ROLE_EXTENDED, userinfo::ROLE_UNLIMITED])) {
            throw new coding_exception('You have to provide one of the constants ROLE_BASIC,
                ROLE_EXTENDED or ROLE_UNLIMITED from userinfo class');
        }
        $this->defaultrole = $defaultrole;
    }
}
