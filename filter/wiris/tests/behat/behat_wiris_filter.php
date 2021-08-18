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
 * Methods related to MathType filter settings.
 * @package    filter
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/behat_wiris_base.php');

use Behat\Mink\Exception\ExpectationException;

class behat_wiris_filter extends behat_wiris_base {

    /**
     * Turns MathType filter off in Filter settings
     *
     * @Given I turn MathType filter off
     * @throws ElementNotFoundException If MathType by Wiris field is not found.
     */
    public function i_turn_mathtype_filter_off() {
        $node = $this->get_node_in_container("option", "Off", "table_row", "MathType by WIRIS");
        $this->ensure_node_is_visible($node);
        $node->click();
    }

    /**
     * Check editor always active on MathType filter page
     *
     * @Given I check editor always active
     * @throws ExpectationException If editor always active checkbox is not found, it will throw an exception.
     */
    public function i_check_editor_always_active() {
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//*[@id="id_s_filter_wiris_allow_editorplugin_active_course" ]');
        if (empty($component)) {
            throw new ExpectationException('Editor always active checkbox not found.', $this->getSession());
        }
        $component->check();
    }

    /**
     * Check Image performance mode off on MathType filter page
     *
     * @Given I check image performance mode off
     * @throws ExpectationException If image performance mode is not found, it will throw an exception.
     */
    public function i_check_image_performance_mode_off() {
        $session = $this->getSession();
        $component = $session->getPage()->find('xpath', '//*[@id="id_s_filter_wiris_pluginperformance" ]');
        if (empty($component)) {
            throw new ExpectationException('Image performance checkbox not found.', $this->getSession());
        }
        $component->uncheck();
    }

    /**
     * Set the MathType filter render type to the given value.
     *
     * @Given /^the MathType filter render type is set to "(php|client)"$/
     */
    public function the_mathtype_filter_render_type_is_set_to($value) {
        set_config('rendertype', $value, 'filter_wiris');
    }

}