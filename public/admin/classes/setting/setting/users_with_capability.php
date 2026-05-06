<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\setting;

/**
 * An admin setting for selecting one or more users who have a capability
 * in the system context
 *
 * An admin setting for selecting one or more users, who have a particular capability
 * in the system context. Warning, make sure the list will never be too long. There is
 * no paging or searching of this list.
 *
 * To correctly get a list of users from this config setting, you need to call the
 * get_users_from_config($CFG->mysetting, $capability); function in moodlelib.php.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class users_with_capability extends \core_admin\setting\setting\configmultiselect {
    /** @var string The capabilities name */
    protected $capability;
    /** @var int include admin users too */
    protected $includeadmins;

    /**
     * Constructor.
     *
     * @param string $name A unique ascii name for the setting.
     *      Either 'mysetting' for core settings, or 'myplugin/mysetting' for plugin settings.
     * @param string $visiblename localised name
     * @param string $description localised long description
     * @param array $defaultsetting array of usernames
     * @param string $capability string capability name.
     * @param bool $includeadmins include administrators
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $capability, $includeadmins = true) {
        $this->capability    = $capability;
        $this->includeadmins = $includeadmins;
        parent::__construct($name, $visiblename, $description, $defaultsetting, null);
    }

    /**
     * Load all of the uses who have the capability into choice array
     *
     * @return bool Always returns true
     */
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        [$sort, $sortparams] = users_order_by_sql('u');
        if (!empty($sortparams)) {
            throw new \coding_exception('users_order_by_sql returned some query parameters. ' .
                    'This is unexpected, and a problem because there is no way to pass these ' .
                    'parameters to get_users_by_capability. See MDL-34657.');
        }
        $userfieldsapi = \core_user\fields::for_name();
        $userfields = 'u.id, u.username, ' . $userfieldsapi->get_sql('u', false, '', '', false)->selects;
        $users = get_users_by_capability(\context_system::instance(), $this->capability, $userfields, $sort);
        $this->choices = [
            '$@NONE@$' => get_string('nobody'),
            '$@ALL@$' => get_string('everyonewhocan', 'admin', get_capability_string($this->capability)),
        ];
        if ($this->includeadmins) {
            $admins = get_admins();
            foreach ($admins as $user) {
                $this->choices[$user->id] = fullname($user);
            }
        }
        if (is_array($users)) {
            foreach ($users as $user) {
                $this->choices[$user->id] = fullname($user);
            }
        }
        return true;
    }

    /**
     * Returns the default setting for class
     *
     * @return mixed Array, or string. Empty string if no default
     */
    public function get_defaultsetting() {
        $this->load_choices();
        $defaultsetting = parent::get_defaultsetting();
        if (empty($defaultsetting)) {
            return ['$@NONE@$'];
        } else if (array_key_exists($defaultsetting, $this->choices)) {
                return $defaultsetting;
        } else {
            return '';
        }
    }

    /**
     * Returns the current setting
     *
     * @return mixed array or string
     */
    public function get_setting() {
        $result = parent::get_setting();
        if ($result === null) {
            // This is necessary for settings upgrade.
            return null;
        }
        if (empty($result)) {
            $result = ['$@NONE@$'];
        }
        return $result;
    }

    /**
     * Save the chosen setting provided as $data
     *
     * @param array $data
     * @return mixed string or array
     */
    public function write_setting($data) {
        // If all is selected, remove any explicit options.
        if (in_array('$@ALL@$', $data)) {
            $data = ['$@ALL@$'];
        }
        // None never needs to be written to the DB.
        if (in_array('$@NONE@$', $data)) {
            unset($data[array_search('$@NONE@$', $data)]);
        }
        return parent::write_setting($data);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(users_with_capability::class, \admin_setting_users_with_capability::class);
