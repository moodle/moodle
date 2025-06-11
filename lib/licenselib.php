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
 * A namespace contains license specific functions
 *
 * @since      Moodle 2.0
 * @package    core
 * @subpackage lib
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class license_manager {

    /**
     * License is a core license and can not be updated or deleted.
     */
    const CORE_LICENSE = 0;

    /**
     * License is a custom license and can be updated and/or deleted.
     */
    const CUSTOM_LICENSE = 1;

    /**
     * Integer representation of boolean for a license that is enabled.
     */
    const LICENSE_ENABLED = 1;

    /**
     * Integer representation of boolean for a license that is disabled.
     */
    const LICENSE_DISABLED = 0;

    /**
     * Integer for moving a license up order.
     */
    const LICENSE_MOVE_UP = -1;

    /**
     * Integer for moving a license down order.
     */
    const LICENSE_MOVE_DOWN = 1;

    /**
     * Save a license record.
     *
     * @param object $license {
     *            shortname => string a shortname of license, will be refered by files table[required]
     *            fullname  => string the fullname of the license [required]
     *            source => string the homepage of the license type[required]
     *            enabled => int is it enabled?
     *            version  => int a version number used by moodle [required]
     * }
     */
    public static function save($license) {

        $existinglicense = self::get_license_by_shortname($license->shortname);

        if (!empty($existinglicense)) {
            $id = $existinglicense->id;
            if ($existinglicense->custom == self::CORE_LICENSE) {
                // Can only update the enabled status and sortorder for core licenses.
                $existinglicense->enabled = $license->enabled;
                $existinglicense->sortorder = $license->sortorder;
                $license = $existinglicense;
            }
            $license->id = $id;
            self::update($license);
        } else {
            self::create($license);
        }

        return true;
    }

    /**
     * @deprecated Since Moodle 3.9, MDL-45184.
     */
    public function add() {
        throw new coding_exception('license_manager::add() is deprecated. Please use license_manager::save() instead.');
    }

    /**
     * Create a license record.
     *
     * @param object $license the license to create record for.
     */
    protected static function create($license) {
        global $DB;

        $licensecount = count(self::get_licenses());
        $license->sortorder = $licensecount + 1;
        // Enable all created license by default.
        $license->enabled = self::LICENSE_ENABLED;
        // API can only create custom licenses, core licenses
        // are directly created at install or upgrade.
        $license->custom = self::CUSTOM_LICENSE;

        $DB->insert_record('license', $license);
        self::reset_license_cache();
        // Update the config setting of active licenses.
        self::set_active_licenses();
    }

    /**
     * Read licens record(s) from database.
     *
     * @param array $params license parameters to return licenses for.
     *
     * @return array $filteredlicenses object[] of licenses.
     */
    public static function read(array $params = []) {
        $licenses = self::get_licenses();

        $filteredlicenses = [];

        foreach ($licenses as $shortname => $license) {
            $filtermatch = true;
            foreach ($params as $key => $value) {
                if ($license->$key != $value) {
                    $filtermatch = false;
                }
            }
            if ($filtermatch) {
                $filteredlicenses[$shortname] = $license;
            }
        }
        return $filteredlicenses;

    }

    /**
     * Update a license record.
     *
     * @param object $license the license to update record for.
     *
     * @throws \moodle_exception if attempting to update a core license.
     */
    protected static function update($license) {
        global $DB;

        $DB->update_record('license', $license);
        self::reset_license_cache();
    }

    /**
     * Delete a custom license.
     *
     * @param string $licenseshortname the shortname of license.
     *
     * @throws \moodle_exception when attempting to delete a license you are not allowed to.
     */
    public static function delete($licenseshortname) {
        global $DB;

        $licensetodelete = self::get_license_by_shortname($licenseshortname);

        if (!empty($licensetodelete)) {
            if ($licensetodelete->custom == self::CUSTOM_LICENSE) {
                // Check that the license is not in use by any non-draft files, if so it cannot be deleted.
                $countfilesselect = 'license = :license AND NOT (component = \'user\' AND filearea = \'draft\')';
                $countfilesusinglicense = $DB->count_records_select('files', $countfilesselect, ['license' => $licenseshortname]);
                if ($countfilesusinglicense > 0) {
                    throw new moodle_exception('cannotdeletelicenseinuse', 'license');
                }
                $deletedsortorder = $licensetodelete->sortorder;
                $DB->delete_records('license', ['id' => $licensetodelete->id]);

                // We've deleted a license, so update our list of licenses so we don't save the deleted license again.
                self::reset_license_cache();
                $licenses = self::get_licenses();

                foreach ($licenses as $license) {
                    if ($license->sortorder > $deletedsortorder) {
                        $license->sortorder = $license->sortorder - 1;
                        self::save($license);
                    }
                }

                // Update the config setting of active licenses as well.
                self::set_active_licenses();

            } else {
                throw new moodle_exception('cannotdeletecore', 'license');
            }
        } else {
            throw new moodle_exception('licensenotfoundshortname', 'license', $licenseshortname);
        }
    }

    /**
     * Get license records.
     *
     * @return array|false object[] of license records of false if none.
     */
    public static function get_licenses() {
        global $DB;

        $cache = \cache::make('core', 'license');
        $licenses = $cache->get('licenses');

        if ($licenses === false) {
            $licenses = [];
            $records = $DB->get_records_select('license', null, null, 'sortorder ASC');
            foreach ($records as $license) {
                $licenses[$license->shortname] = $license;
            }
            $cache->set('licenses', $licenses);
        }

        foreach ($licenses as $license) {
            // Localise the license names.
            if ($license->custom == self::CORE_LICENSE) {
                $license->fullname = get_string($license->shortname, 'core_license');
            } else {
                $license->fullname = format_string($license->fullname);
            }
        }

        return $licenses;
    }

    /**
     * Change the sort order of a license (and it's sibling license as a result).
     *
     * @param int $direction value to change sortorder of license by.
     * @param string $licenseshortname the shortname of license to changes sortorder for.
     *
     * @throws \moodle_exception if attempting to use invalid direction value.
     */
    public static function change_license_sortorder(int $direction, string $licenseshortname): void {

        if ($direction != self::LICENSE_MOVE_UP && $direction != self::LICENSE_MOVE_DOWN) {
            throw new coding_exception(
                'Must use a valid licence API move direction constant (LICENSE_MOVE_UP or LICENSE_MOVE_DOWN)');
        }

        $licenses = self::get_licenses();
        $licensetoupdate = $licenses[$licenseshortname];

        $currentsortorder = $licensetoupdate->sortorder;
        $targetsortorder = $currentsortorder + $direction;

        if ($targetsortorder > 0 && $targetsortorder <= count($licenses) ) {
            foreach ($licenses as $license) {
                if ($license->sortorder == $targetsortorder) {
                    $license->sortorder = $license->sortorder - $direction;
                    self::update($license);
                }
            }
            $licensetoupdate->sortorder = $targetsortorder;
            self::update($licensetoupdate);
        }
    }

    /**
     * Get license record by shortname
     *
     * @param string $name the shortname of license
     * @return object|null the license or null if no license found.
     */
    public static function get_license_by_shortname(string $name) {
        $licenses = self::read(['shortname' => $name]);

        if (!empty($licenses)) {
            $license = reset($licenses);
        } else {
            $license = null;
        }

        return $license;
    }

    /**
     * Enable a license
     * @param string $license the shortname of license
     * @return boolean
     */
    public static function enable($license) {
        if ($license = self::get_license_by_shortname($license)) {
            $license->enabled = self::LICENSE_ENABLED;
            self::update($license);
        }
        self::set_active_licenses();

        return true;
    }

    /**
     * Disable a license
     * @param string $license the shortname of license
     * @return boolean
     */
    public static function disable($license) {
        global $CFG;
        // Site default license cannot be disabled!
        if ($license == $CFG->sitedefaultlicense) {
            throw new \moodle_exception('error');
        }
        if ($license = self::get_license_by_shortname($license)) {
            $license->enabled = self::LICENSE_DISABLED;
            self::update($license);
        }
        self::set_active_licenses();

        return true;
    }

    /**
     * Store active licenses in global config.
     */
    protected static function set_active_licenses() {
        $licenses = self::read(['enabled' => self::LICENSE_ENABLED]);
        $result = array();
        foreach ($licenses as $l) {
            $result[] = $l->shortname;
        }
        set_config('licenses', implode(',', $result));
    }

    /**
     * Get the globally configured active licenses.
     *
     * @return array of license objects.
     * @throws \coding_exception
     */
    public static function get_active_licenses() {
        global $CFG;

        $result = [];

        if (!empty($CFG->licenses)) {
            $activelicenses = explode(',', $CFG->licenses);
            $licenses = self::get_licenses();
            foreach ($licenses as $license) {
                if (in_array($license->shortname, $activelicenses)) {
                    $result[$license->shortname] = $license;
                }
            }
        }

        return $result;
    }

    /**
     * Get the globally configured active licenses as an array.
     *
     * @return array $licenses an associative array of licenses shaped as ['shortname' => 'fullname']
     */
    public static function get_active_licenses_as_array() {
        $activelicenses = self::get_active_licenses();

        $licenses = [];
        foreach ($activelicenses as $license) {
            $licenses[$license->shortname] = $license->fullname;
        }

        return $licenses;
    }

    /**
     * Install moodle built-in licenses.
     */
    public static function install_licenses() {
        global $CFG;

        require_once($CFG->libdir . '/db/upgradelib.php');

        upgrade_core_licenses();
    }

    /**
     * Reset the license cache so it rebuilds next time licenses are fetched.
     */
    public static function reset_license_cache() {
        $cache = \cache::make('core', 'license');
        $cache->delete('licenses');
    }
}
