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
 * core_renderer.php
 *
 * This is built using the boost template to allow for new theme's using
 * Moodle's new Boost theme engine
 *
 * @package     theme_eguru
 * @copyright   2015 LMSACE Dev Team, lmsace.com
 * @author      LMSACE Dev Team
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This class has function for renderer user menu and login page
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_eguru_core_renderer extends theme_boost\output\core_renderer {

    /**
     * This function have the code to create the earlier user menu from the settings.
     * @return string
     */
    public function earlier_user_menu() {
        global $CFG , $SITE;

        if ($CFG->branch > "27") {
            return '';
        }
        $uname = fullname($USER, true);
        $dlink = new moodle_url("/my");
        $plink = new moodle_url("/user/profile.php", array("id" => $USER->id));
        $lo = new moodle_url('/login/logout.php', array('sesskey' => sesskey()));
        $dashboard = get_string('myhome');
        $profile = get_string('profile');
        $logout = get_string('logout');

        $content = '<li class="dropdown no-divider"><a class="dropdown-toggle"
        data-toggle="dropdown" href="#">'.$uname.'<i class="fa fa-chevron-down"></i><span class="caretup"></span></a><ul class="dropdown-menu"><li><a href="'.$dlink.'">'.$dashboard.'</a></li><li><a href="'.$plink.'">'.$profile.'</a></li><li><a href="'.$lo.'">'.$logout.'</a></li></ul></li>';
        return $content;
    }

    /**
     * Render the login page template.
     * @param \core_auth\output\login $form
     * @return string
     */
    public function render_login(\core_auth\output\login $form) {
        global $CFG, $SITE;

        $context = $form->export_for_template($this);

        // Override because rendering is not supported in template yet.
        $context->cookieshelpiconformatted = $this->help_icon('cookiesenabled');
        $context->errorformatted = $this->error_text($context->error);
        $url = $this->get_logo_url();
        if ($url) {
            $url = $url->out(false);
        }
        $context->logourl = $url;
        $context->sitename = format_string($SITE->fullname, true, ['
            context' => context_course::instance(SITEID), "
            escape" => false]);
        $maincontent = $this->render_from_template('theme_eguru/login_form', $context);
        return $maincontent;
    }
}