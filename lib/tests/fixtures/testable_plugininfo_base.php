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
 * Provides testable_plugininfo_base class.
 *
 * @package     core
 * @category    test
 * @copyright   2015 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Testable plugininfo subclass representing a fake plugin type instance.
 *
 * @copyright 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_plugininfo_base extends \core\plugininfo\base {

    public static function fake_plugin_instance($type, $typerootdir, $name, $namerootdir, $typeclass, $pluginman) {
        return self::make_plugin_instance($type, $typerootdir, $name, $namerootdir, $typeclass, $pluginman);
    }

    public function init_display_name() {
        $this->displayname = 'Testable fake pluginfo instance';
    }

    public function load_db_version() {
        $this->versiondb = null;
    }

    public function init_is_standard() {
        $this->source = core_plugin_manager::PLUGIN_SOURCE_EXTENSION;
    }
}
