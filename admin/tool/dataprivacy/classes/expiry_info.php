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
 * Expiry Data.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;

use core_privacy\manager;

defined('MOODLE_INTERNAL') || die();

/**
 * Expiry Data.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class expiry_info {

    /** @var bool Whether this context is fully expired */
    protected $fullyexpired = false;

    /** @var bool Whether the default expiry value of this purpose has been reached */
    protected $defaultexpiryreached = false;

    /** @var bool Whether the default purpose is protected */
    protected $defaultprotected = false;

    /** @var int[] List of expires roles */
    protected $expired = [];

    /** @var int[] List of unexpires roles */
    protected $unexpired = [];

    /** @var int[] List of unexpired roles which are also protected */
    protected $protectedroles = [];

    /**
     * Constructor for the expiry_info class.
     *
     * @param   bool    $default Whether the default expiry period for this context has been reached.
     * @param   bool    $defaultprotected Whether the default expiry is protected.
     * @param   int[]   $expired A list of roles in this context which have explicitly expired.
     * @param   int[]   $unexpired A list of roles in this context which have not yet expired.
     * @param   int[]   $protectedroles A list of unexpired roles in this context which are protected.
     */
    public function __construct(bool $default, bool $defaultprotected, array $expired, array $unexpired, array $protectedroles) {
        $this->defaultexpiryreached = $default;
        $this->defaultprotected = $defaultprotected;
        $this->expired = $expired;
        $this->unexpired = $unexpired;
        $this->protectedroles = $protectedroles;
    }

    /**
     * Whether this context has 'fully' expired.
     * That is to say that the default retention period has been reached, and that there are no unexpired roles.
     *
     * @return  bool
     */
    public function is_fully_expired() : bool {
        return $this->defaultexpiryreached && empty($this->unexpired);
    }

    /**
     * Whether any part of this context has expired.
     *
     * @return  bool
     */
    public function is_any_expired() : bool {
        if ($this->is_fully_expired()) {
            return true;
        }

        if (!empty($this->get_expired_roles())) {
            return true;
        }

        if ($this->is_default_expired()) {
            return true;
        }

        return false;
    }

    /**
     * Get the list of explicitly expired role IDs.
     * Note: This does not list roles which have been expired via the default retention policy being reached.
     *
     * @return  int[]
     */
    public function get_expired_roles() : array {
        if ($this->is_default_expired()) {
            return [];
        }
        return $this->expired;
    }

    /**
     * Check whether the specified role is explicitly expired.
     * Note: This does not list roles which have been expired via the default retention policy being reached.
     *
     * @param   int $roleid
     * @return  bool
     */
    public function is_role_expired(int $roleid) : bool {
        return false !== array_search($roleid, $this->expired);
    }

    /**
     * Whether the default retention policy has been reached.
     *
     * @return  bool
     */
    public function is_default_expired() : bool {
        return $this->defaultexpiryreached;
    }

    /**
     * Whether the default purpose is protected.
     *
     * @return  bool
     */
    public function is_default_protected() : bool {
        return $this->defaultprotected;
    }

    /**
     * Get the list of unexpired role IDs.
     *
     * @return  int[]
     */
    public function get_unexpired_roles() : array {
        return $this->unexpired;
    }

    /**
     * Get the list of unexpired protected roles.
     *
     * @return  int[]
     */
    public function get_unexpired_protected_roles() : array {
        return array_keys(array_filter($this->protectedroles));
    }

    /**
     * Get a list of all overridden roles which are unprotected.
     * @return  int[]
     */
    public function get_unprotected_overridden_roles() : array {
        $allroles = array_merge($this->expired, $this->unexpired);

        return array_diff($allroles, $this->protectedroles);
    }

    /**
     * Merge this expiry_info object with another belonging to a child context in order to set the 'safest' heritage.
     *
     * It is not possible to delete any part of a context that is not deleted by a parent.
     * So if a course's retention policy has been reached, then only parts where the children have also expired can be
     * deleted.
     *
     * @param   expiry_info $child The child record to merge with.
     * @return  $this
     */
    public function merge_with_child(expiry_info $child) : expiry_info {
        if ($child->is_fully_expired()) {
            return $this;
        }

        // If the child is not fully expired, then none of the parents can be either.
        $this->fullyexpired = false;

        // Remove any role in this node which is not expired in the child.
        foreach ($this->expired as $key => $roleid) {
            if (!$child->is_role_expired($roleid)) {
                unset($this->expired[$key]);
            }
        }

        array_merge($this->unexpired, $child->get_unexpired_roles());

        if (!$child->is_default_expired()) {
            $this->defaultexpiryreached = false;
        }

        return $this;
    }
}
