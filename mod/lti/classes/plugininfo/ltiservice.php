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
 * LTI service plugin info.
 *
 * @package    mod_lti
 * @copyright  2014 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lti\plugininfo;

use core\plugininfo\base;

defined('MOODLE_INTERNAL') || die();


/**
 * The mod_lti\plugininfo\ltiservice class.
 *
 * @package    mod_lti
 * @since      Moodle 2.8
 * @copyright  2014 Vital Source Technologies http://vitalsource.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ltiservice extends base {

    /**
     * Should there be a way to uninstall the plugin via the administration UI?
     *
     * Uninstallation is not allowed for core subplugins.
     *
     * @return boolean
     */
    public function is_uninstall_allowed() {

        if ($this->is_standard()) {
            return false;
        }

        return true;

    }

}
