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
 * @package    moodlecore
 * @subpackage backup-moodle2
 * @copyright  2011 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class extending standard restore_plugin in order to implement some
 * helper methods related with the plagiarism plugins
 *
 * TODO: Finish phpdocs
 */
abstract class restore_plagiarism_plugin extends restore_plugin {
    public function define_plugin_structure($connectionpoint) {
        global $CFG;
        if (!$connectionpoint instanceof restore_path_element) {
            throw new restore_step_exception('restore_path_element_required', $connectionpoint);
        }

        //check if enabled at site level and plugin is enabled.
        require_once($CFG->libdir . '/plagiarismlib.php');
        $enabledplugins = plagiarism_load_available_plugins();
        if (!array_key_exists($this->pluginname, $enabledplugins)) {
            return;
        }
        return parent::define_plugin_structure($connectionpoint);
    }
}
