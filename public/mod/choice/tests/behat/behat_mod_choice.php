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
 * Steps definitions for choice activity.
 *
 * @package   mod_choice
 * @category  test
 * @copyright 2013 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Choice activity definitions.
 *
 * @package   mod_choice
 * @category  test
 * @copyright 2013 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_choice extends behat_base {

    /**
     * Chooses the specified option from the choice activity named as specified. You should be located in the activity's course page.
     *
     * @Given /^I choose "(?P<option_string>(?:[^"]|\\")*)" from "(?P<choice_activity_string>(?:[^"]|\\")*)" choice activity$/
     * @param string $option
     * @param string $choiceactivity
     * @return array
     */
    public function I_choose_option_from_activity($option, $choiceactivity) {
        $this->execute('behat_navigation::i_am_on_page_instance', [$this->escape($choiceactivity), 'choice activity']);

        $this->execute('behat_forms::i_set_the_field_to', array( $this->escape($option), 1));

        $this->execute("behat_forms::press_button", get_string('savemychoice', 'choice'));
    }

    /**
     * Chooses the specified option from the choice activity named as specified and save the choice.
     * You should be located in the activity's course page.
     *
     * @Given /^I choose options (?P<option_string>"(?:[^"]|\\")*"(?:,"(?:[^"]|\\")*")*) from "(?P<choice_activity_string>(?:[^"]|\\")*)" choice activity$/
     * @param string $option
     * @param string $choiceactivity
     * @return array
     */
    public function I_choose_options_from_activity($option, $choiceactivity) {
        // Get Behat general and forms contexts.
        $behatgeneral = behat_context_helper::get('behat_general');
        $behatforms = behat_context_helper::get('behat_forms');

        // Go to choice activity.
        $this->execute("behat_navigation::i_am_on_page_instance", [$this->escape($choiceactivity), 'choice activity']);

        // Wait for page to be loaded.
        $this->wait_for_pending_js();

        // Set all options.
        $options = explode('","', trim($option, '"'));
        foreach ($options as $option) {
            $behatforms->i_set_the_field_to($this->escape($option), '1');
        }

        // Save choice.
        $behatforms->press_button(get_string('savemychoice', 'choice'));
    }

}
