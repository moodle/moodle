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
 * @package   plagiarism_turnitin
 * @copyright 2025 Turnitin
 * @author    Jack Milgate
 */

namespace plagiarism_turnitin;

defined('MOODLE_INTERNAL') || die();

use core\hook\output\before_footer_html_generation;

class hook_callbacks {

    /**
     * This is a workaround to allow the EULA to be displayed on the quiz page.
     * This function fires on every page, but only does anything if the user is on the quiz page.
     *
     * @param before_footer_html_generation $hook
     */
    public static function before_footer_html_generation(before_footer_html_generation $hook): void {
        global $CFG, $PAGE;

        // Check whether the user is on the quiz page. If not, we don't need to do anything.
        if ($PAGE->pagetype !== 'mod-quiz-view') {
            return;
        }

        // Include lib.php so we can access the Turnitin plagiarism plugin class.
        require_once($CFG->dirroot.'/plagiarism/turnitin/lib.php');
        $pluginturnitin = new \plagiarism_plugin_turnitin();

        $moduletiienabled = $pluginturnitin->get_config_settings('mod_'.$PAGE->cm->modname);
        // Exit if Turnitin is not being used for this activity type.
        if (empty($moduletiienabled)) {
            return;
        }

        // Check that turnitin is enabled for this quiz.
        $plagiarismsettings = $pluginturnitin->get_settings($PAGE->cm->id);
        if (empty($plagiarismsettings['use_turnitin']) || $plagiarismsettings['use_turnitin'] != '1') {
            return;
        }

        // This function checks whether the user has accepted the EULA.
        // If they haven't, it will return the EULA form. If they have, it will return an empty string.
        $eulaform = $pluginturnitin->render_eula_form($PAGE->cm);
        if ($eulaform == '') {
            return;
        }

        // Echo the form onto the page. The quizEula script will then move it to the correct location on the page.
        echo $eulaform;

        // This script hides the "Start Quiz" button and moves the EULA form into the correct place on the page.
        $PAGE->requires->js_call_amd('plagiarism_turnitin/quiz_eula', 'quizEula');

        // This script handles the EULA modal, which is displayed when the user clicks the "View EULA" link.
        $PAGE->requires->js_call_amd('plagiarism_turnitin/new_eula_modal', 'newEulaLaunch');
    }
}