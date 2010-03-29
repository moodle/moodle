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
 * @since 2.0
 * @package moodlecore
 * @subpackage moodlecore
 * @copyright 2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class license_manager {
    /**
     * Adding a new license type
     * @param object $license {
     *            shortname => string a shortname of license, will be refered by files table[required]
     *            fullname  => string the fullname of the license [required]
     *            source => string the homepage of the license type[required]
     *            enabled => int is it enabled?
     *            version  => int a version number used by moodle [required]
     * }
     */
    static public function add($license) {
        global $DB;
        if ($record = $DB->get_record('license', array('shortname'=>$license->shortname))) {
            // record exists
            if ($record->version < $license->version) {
                // update license record
                $license->id = $record->id;
                $DB->update_record('license', $license);
            } else {
                debugging('won\'t update');
            }
        } else {
            if ($license_id = $DB->insert_record('license', $license)) {
                return $license_id;
            } else {
                return false;
            }
        }
    }

    /**
     * Get license record by shortname
     * @param mixed $param the shortname of license, or an array
     * @return object
     */
    static public function get($param = null) {
        global $DB;
        if (empty($param)) {
            $param = array();
        }

        // get license by shortname
        if (is_string($param)) {
            if ($record = $DB->get_record('license', array('shortname'=>$name))) {
                return $record;
            } else {
                return null;
            }
        } else if (is_array($param)) {
            // get licenses by conditions
            if ($records = $DB->get_records('license', $param)) {
                return $records;
            } else {
                return null;
            }
        }
    }

    /**
     * Enable a license
     * @param string $license the shortname of license
     * @return boolean
     */
    static public function enable($license) {
        global $DB;
        if ($record = $DB->get_record('license', array('shortname'=>$license))) {
            $record->enabled = 1;
            $DB->update_record('license', $record);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Disable a license
     * @param string $license the shortname of license
     * @return boolean
     */
    static public function disable($license) {
        global $DB;
        if ($record = $DB->get_record('license', array('shortname'=>$license))) {
            $record->enabled = 0;
            $DB->update_record('license', $record);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Install moodle build-in licenses
     */
    static public function install_licenses() {
        $license = new stdclass;

        $license->shortname = 'allrightsreserved';
        $license->fullname = 'All rights reserved';
        $license->source = 'http://en.wikipedia.org/wiki/All_rights_reserved';
        $license->enabled = 1;
        $license->version = '2010032500';
        self::add($license);

        $license->shortname = 'public';
        $license->fullname = 'Public Domain';
        $license->source = 'http://creativecommons.org/licenses/publicdomain/';
        $license->enabled = 1;
        $license->version = '2010032500';
        self::add($license);

        $license->shortname = 'cc';
        $license->fullname = 'Creative Commons';
        $license->source = 'http://creativecommons.org/licenses/by/3.0/';
        $license->enabled = 1;
        $license->version = '2010032500';
        self::add($license);

        $license->shortname = 'cc-nd';
        $license->fullname = 'Creative Commons - NoDerivs';
        $license->source = 'http://creativecommons.org/licenses/by-nd/3.0/';
        $license->enabled = 1;
        $license->version = '2010032500';
        self::add($license);

        $license->shortname = 'cc-nc-nd';
        $license->fullname = 'Creative Commons - No Commercial NoDerivs';
        $license->source = 'http://creativecommons.org/licenses/by-nc-nd/3.0/';
        $license->enabled = 1;
        $license->version = '2010032500';
        self::add($license);

        $license->shortname = 'cc-nc';
        $license->fullname = 'Creative Commons - No Commercial';
        $license->source = 'http://creativecommons.org/licenses/by-nd/3.0/';
        $license->enabled = 1;
        $license->version = '2010032500';
        self::add($license);

        $license->shortname = 'cc-nc-sa';
        $license->fullname = 'Creative Commons - No Commercial ShareAlike';
        $license->source = 'http://creativecommons.org/licenses/by-nc-sa/3.0/';
        $license->enabled = 1;
        $license->version = '2010032500';
        self::add($license);

        $license->shortname = 'cc-sa';
        $license->fullname = 'Creative Commons - ShareAlike';
        $license->source = 'http://creativecommons.org/licenses/by-sa/3.0/';
        $license->enabled = 1;
        $license->version = '2010032500';
        self::add($license);
    }
}
