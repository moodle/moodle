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
 * Special flatfile settings.
 *
 * @package    enrol_flatfile
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/adminlib.php");


/**
 * Setting class that stores only non-empty values.
 */
class enrol_flatfile_role_setting extends admin_setting_configtext {

    public function __construct($role) {
        parent::__construct('enrol_flatfile/map_'.$role->id, $role->localname, '', $role->shortname);
    }

    public function config_read($name) {
        $value = parent::config_read($name);
        if (is_null($value)) {
            // In other settings NULL means we have to ask user for new value,
            // here we just ignore missing role mappings.
            $value = '';
        }
        return $value;
    }

    public function config_write($name, $value) {
        if ($value === '') {
            // We do not want empty values in config table,
            // delete it instead.
            $value = null;
        }
        return parent::config_write($name, $value);
    }
}
