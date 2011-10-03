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
 * Implementaton of the quizaccess_securewindow plugin.
 *
 * @package    quizaccess
 * @subpackage securewindow
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');


/**
* A rule for ensuring that the quiz is opened in a popup, with some JavaScript
* to prevent copying and pasting, etc.
*
* @copyright  2009 Tim Hunt
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
class quizaccess_securewindow extends quiz_access_rule_base {
    /** @var array options that should be used for opening the secure popup. */
    public static $popupoptions = array(
        'left' => 0,
        'top' => 0,
        'fullscreen' => true,
        'scrollbars' => true,
        'resizeable' => false,
        'directories' => false,
        'toolbar' => false,
        'titlebar' => false,
        'location' => false,
        'status' => false,
        'menubar' => false,
    );

    /**
     * Make a link to the review page for an attempt.
     *
     * @param string $linktext the desired link text.
     * @param int $attemptid the attempt id.
     * @return string HTML for the link.
     */
    public function make_review_link($linktext, $attemptid) {
        global $OUTPUT;
        $url = $this->quizobj->review_url($attemptid);
        $button = new single_button($url, $linktext);
        $button->add_action(new popup_action('click', $url, 'quizpopup', self::$popupoptions));
        return $OUTPUT->render($button);
    }

    /**
     * Do the printheader call, etc. required for a secure page, including the necessary JS.
     *
     * @param string $title HTML title tag content, passed to printheader.
     * @param string $headtags extra stuff to go in the HTML head tag, passed to printheader.
     *                $headtags has been deprectaed since Moodle 2.0
     */
    public function setup_secure_page($title, $headtags=null) {
        global $PAGE;
        $PAGE->set_popup_notification_allowed(false);//prevent message notifications
        $PAGE->set_title($title);
        $PAGE->set_cacheable(false);
        $PAGE->set_pagelayout('popup');
        $PAGE->add_body_class('quiz-secure-window');
        $PAGE->requires->js_init_call('M.mod_quiz.secure_window.init',
                null, false, quiz_get_js_module());
    }
}
