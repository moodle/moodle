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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class component_template
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class component_template extends component_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->plugins = false;
        $this->ordering = false;
        $this->form = true;
        $this->help = true;
    }

    /**
     * form_process_data
     *
     * @param moodleform $cform
     * @return void
     */
    public function form_process_data(moodleform $cform): void {
        global $DB;

        if ($this->form) {
            $data = $cform->get_data();

            // Function cr_serialize() will add slashes.
            $components = cr_unserialize($this->config->components);
            $components['template']['config'] = $data;

            $this->config->components = cr_serialize($components);

            $DB->update_record('block_configurable_reports', $this->config);
        }
    }

    /**
     * form_set_data
     *
     * @param moodleform $cform
     * @return void
     */
    public function form_set_data(moodleform $cform) {
        if ($this->form) {
            $components = cr_unserialize($this->config->components);
            $config = $components['template']['config'] ?? new stdclass;
            $cform->set_data($config);
        }
    }

}
