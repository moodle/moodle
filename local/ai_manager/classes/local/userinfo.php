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

namespace local_ai_manager\local;

use local_ai_manager\hook\userinfo_extend;
use stdClass;

/**
 * Data object class for handling usage information when using an AI tool.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class userinfo {

    /** @var int Constant identifying the basic role */
    public const ROLE_BASIC = 1;

    /** @var int Constant identifying the extended role */
    public const ROLE_EXTENDED = 2;

    /** @var int Constant identifying the unlimited role */
    public const ROLE_UNLIMITED = 3;

    /** @var int This is not really a role, but is being used to signal that the default role for a user should be assigned. */
    public const ROLE_DEFAULT = -1;

    /** @var false|stdClass The database record or false if there is none (yet) */
    private false|stdClass $record;

    /** @var int The role of the current userinfo */
    private int $role;

    /** @var bool The locked state of the user */
    private bool $locked;

    /** @var bool The confirmed state of the user */
    private bool $confirmed;

    /**
     * Create a userinfo object.
     *
     * @param int $userid The userid to create the userinfo object for
     */
    public function __construct(
            /** @var int $userid The userid to create the userinfo object for */
            private readonly int $userid
    ) {
        $this->load();
    }

    /**
     * Tries to laod the record from database and store its information into the object.
     */
    public function load(): void {
        global $DB;
        $this->record = $DB->get_record('local_ai_manager_userinfo', ['userid' => $this->userid]);
        $this->role = !empty($this->record->role) ? $this->record->role : $this->get_default_role();
        $this->locked = !empty($this->record->locked);
        $this->confirmed = !empty($this->record->confirmed);
    }

    /**
     * Calculates the default role of a user.
     *
     * @return int the role constant integer to use as role for a user which has not been assigned a role yet
     */
    public function get_default_role() {
        $accessmanager = \core\di::get(access_manager::class);
        if (\core\di::get(tenant::class)->is_default_tenant()) {
            return $accessmanager->is_tenant_manager($this->userid) ? self::ROLE_UNLIMITED : self::ROLE_BASIC;
        }

        $userinfoextend = new userinfo_extend($this->userid);
        \core\di::get(\core\hook\manager::class)->dispatch($userinfoextend);

        $hookdefaultrole = $userinfoextend->get_default_role();
        if (!is_null($hookdefaultrole)) {
            return $hookdefaultrole;
        }
        $tenant = \core\di::get(tenant::class);

        $capabilities = ['local/ai_iomad_manager:manage', 'local/ai_manager:manage'];
        $allHasAccess = $accessmanager->check_tenant_capability($capabilities, $tenant->get_context(), $this->userid);


        if (($allHasAccess || $accessmanager->hasIOMADManagerAccess()) || has_capability('local/ai_manager:managetenants', \context_system::instance(), $this->userid)) {
            return self::ROLE_UNLIMITED;
        } else {
            return self::ROLE_BASIC;
        }
    }




    /**
     * Returns if a record exists (yet).
     *
     * @return bool if a database record exists
     */
    public function record_exists(): bool {
        return !empty($this->record);
    }

    /**
     * Standard getter.
     *
     * @return int the userid of this userinfo object
     */
    public function get_userid(): int {
        return $this->userid;
    }

    /**
     * Persist the information in this object to the database.
     */
    public function store() {
        global $DB;
        $this->record = $DB->get_record('local_ai_manager_userinfo', ['userid' => $this->userid]);
        $newrecord = new stdClass();
        $newrecord->userid = $this->userid;
        $newrecord->role = $this->role;
        $newrecord->locked = $this->locked ? 1 : 0;
        $newrecord->confirmed = $this->confirmed ? 1 : 0;
        $newrecord->timemodified = time();
        if ($this->record) {
            $newrecord->id = $this->record->id;
            $DB->update_record('local_ai_manager_userinfo', $newrecord);
        } else {
            $newrecord->id = $DB->insert_record('local_ai_manager_userinfo', $newrecord);
        }
        $this->record = $newrecord;
    }

    /**
     * Setter for the role.
     *
     * Does some additional validation and stores the new role for the user in this userinfo object.
     *
     * @param int $role the role constant integer
     */
    public function set_role(int $role): void {
        if (!in_array($role, [self::ROLE_BASIC, self::ROLE_EXTENDED, self::ROLE_UNLIMITED, self::ROLE_DEFAULT])) {
            throw new \coding_exception('Wrong role specified, use one of ROLE_BASIC, ROLE_EXTENDED,'
                    . ' ROLE_UNLIMITED or ROLE_DEFAULT');
        }
        if ($role === self::ROLE_DEFAULT) {
            $this->role = $this->get_default_role();
            return;
        }
        $this->role = $role;
    }

    /**
     * Standard setter.
     *
     * @param bool $locked the new locked state of the user
     */
    public function set_locked(bool $locked): void {
        $this->locked = $locked;
    }

    /**
     * Standard setter.
     *
     * @param bool $confirmed the new confirmed state of the user
     */
    public function set_confirmed(bool $confirmed): void {
        $this->confirmed = $confirmed;
    }

    /**
     * Standard getter.
     *
     * @return int the role constant integer, see {@see self::ROLE_BASIC}, {@see self::ROLE_EXTENDED},
     *  {@see self::ROLE_UNLIMITED}
     */
    public function get_role(): int {
        return $this->role;
    }

    /**
     * Standard getter.
     *
     * @return bool if the user is locked
     */
    public function is_locked(): bool {
        return $this->locked;
    }

    /**
     * Standard getter.
     *
     * @return bool if the user has confirmed the terms of use
     */
    public function is_confirmed(): bool {
        return $this->confirmed;
    }

    /**
     * Helper function to get the tenant for a user.
     *
     * @param int $userid the id of the user to get the tenant for
     * @return tenant the tenant object
     */
    public static function get_tenant_for_user(int $userid): tenant {
        $user = \core_user::get_user($userid);
        $tenantfield = get_config('local_ai_manager', 'tenantcolumn');
        if (empty($user->{$tenantfield})) {
            // Create the default tenant.
            return new tenant();
        }
        return new tenant($user->{$tenantfield});
    }

    /**
     * Helper function to get a string representation of the role defined by the role constant.
     *
     * @param int $role the role constant integer
     * @return string the string representation of the role, will be used to identify the role in webservices responses for example
     * @throws \coding_exception if a wrong role constant has been passed
     */
    public static function get_role_as_string(int $role): string {
        switch ($role) {
            case 1:
                return 'role_basic';
            case 2:
                return 'role_extended';
            case 3:
                return 'role_unlimited';
            default:
                throw new \coding_exception('Role integers must be 1, 2 or 3');
        }
    }
}
