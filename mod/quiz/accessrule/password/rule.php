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
 * Implementaton of the quizaccess_password plugin.
 *
 * @package    quizaccess
 * @subpackage password
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');


/**
 * A rule representing the password check. It does not actually implement the check,
 * that has to be done directly in attempt.php, but this facilitates telling users about it.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_password extends quiz_access_rule_base {
    public function description() {
        return get_string('requirepasswordmessage', 'quiz');
    }

    /**
     * Clear the flag in the session that says that the current user is allowed to do this quiz.
     */
    public function clear_access_allowed() {
        global $SESSION;
        if (!empty($SESSION->passwordcheckedquizzes[$this->quiz->id])) {
            unset($SESSION->passwordcheckedquizzes[$this->quiz->id]);
        }
    }

    /**
     * Actually ask the user for the password, if they have not already given it this session.
     * This function only returns is access is OK.
     *
     * @param bool $canpreview used to enfore securewindow stuff.
     * @param object $accessmanager the accessmanager calling us.
     * @return mixed return null, unless $return is true, and a form needs to be displayed.
     */
    public function do_password_check($canpreview, $accessmanager) {
        global $CFG, $SESSION, $OUTPUT, $PAGE;

        // We have already checked the password for this quiz this session, so don't ask again.
        if (!empty($SESSION->passwordcheckedquizzes[$this->quiz->id])) {
            return;
        }

        // If the user cancelled the password form, send them back to the view page.
        if (optional_param('cancelpassword', false, PARAM_BOOL)) {
            $accessmanager->back_to_view_page($output);
        }

        // If they entered the right password, let them in.
        $enteredpassword = optional_param('quizpassword', '', PARAM_RAW);
        $validpassword = false;
        if (strcmp($this->quiz->password, $enteredpassword) === 0) {
            $validpassword = true;
        } else if (isset($this->quiz->extrapasswords)) {
            // group overrides may have additional passwords
            foreach ($this->quiz->extrapasswords as $password) {
                if (strcmp($password, $enteredpassword) === 0) {
                    $validpassword = true;
                    break;
                }
            }
        }
        if ($validpassword) {
            $SESSION->passwordcheckedquizzes[$this->quiz->id] = true;
            return;
        }

        // User entered the wrong password, or has not entered one yet, so display the form.
        $output = '';

        // Start the page and print the quiz intro, if any.
        $PAGE->set_title(format_string($this->quizobj->get_quiz_name()));
        $accessmanager->setup_attempt_page($PAGE);

        echo $OUTPUT->header();
        if (trim(strip_tags($this->quiz->intro))) {
            $output .= $OUTPUT->box(format_module_intro('quiz', $this->quiz,
            $this->quizobj->get_cmid()), 'generalbox', 'intro');
        }
        $output .= $OUTPUT->box_start('generalbox', 'passwordbox');

        // If they have previously tried and failed to enter a password, tell them it was wrong.
        if (!empty($enteredpassword)) {
            $output .= '<p class="notifyproblem">' . get_string('passworderror', 'quiz') . '</p>';
        }

        // Print the password entry form.
        $output .= '<p>' . get_string('requirepasswordmessage', 'quiz') . "</p>\n";
        $output .= '<form id="passwordform" method="post" action="' . $CFG->wwwroot .
                '/mod/quiz/startattempt.php" onclick="this.autocomplete=\'off\'">' . "\n";
        $output .= "<div>\n";
        $output .= '<label for="quizpassword">' . get_string('password') . "</label>\n";
        $output .= '<input name="quizpassword" id="quizpassword" type="password" value=""/>' . "\n";
        $output .= '<input name="cmid" type="hidden" value="' .
        $this->quizobj->get_cmid() . '"/>' . "\n";
        $output .= '<input name="sesskey" type="hidden" value="' . sesskey() . '"/>' . "\n";
        $output .= '<input type="submit" value="' . get_string('ok') . '" />';
        $output .= '<input type="submit" name="cancelpassword" value="' .
        get_string('cancel') . '" />' . "\n";
        $output .= "</div>\n";
        $output .= "</form>\n";

        // Finish page.
        $output .= $OUTPUT->box_end();

        // return or display form.
        echo $output;
        echo $OUTPUT->footer();
        exit;
    }
}
