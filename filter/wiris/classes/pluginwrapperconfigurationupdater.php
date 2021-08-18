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
//

/**
 * This class implements WIRIS com_wiris_plugin_configuration_ConfigurationUpdater interface
 * to use a custom Moodle configuration.
 *
 * @package    filter
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/filter/wiris/integration/lib/com/wiris/plugin/configuration/ConfigurationUpdater.interface.php');

class filter_wiris_pluginwrapperconfigurationupdater implements com_wiris_plugin_configuration_ConfigurationUpdater{

    private $customconfig;

    public function __construct($config) {
        $this->customconfig = $config;
    }

    // @codingStandardsIgnoreStart
    // Can't change implemented interface method name.
    public function updateConfiguration(&$configuration) {
        if (isset($this->customconfig)) {
            foreach ($this->customconfig as $key => $value) {
                $configuration[$key] = $value;
            }
        }

    }
    public function init($obj) {
    }
}
