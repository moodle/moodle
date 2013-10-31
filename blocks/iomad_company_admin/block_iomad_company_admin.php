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

require_once('lib.php');
require_once($CFG->dirroot.'/local/iomad_dashboard/menu.php');

/**
 * Company / User Admin Block
 */

class block_iomad_company_admin extends block_base {

    public function init() {
        $this->title = get_string('blocktitle', 'block_iomad_company_admin');

    }

    public function hide_header() {
        return true;
    }

    public function get_content() {
        global $OUTPUT, $CFG, $SESSION;

        // TODO: Really need a cap check to prevent it being displayed at all.

        if ($this->content !== null) {
            return $this->content;
        }

        $context = context_system::instance();

        // Selected tab.
        $selectedtab = optional_param('tabid', 0, PARAM_INT);

        // Set the current tab to stick.
        if (!empty($selectedtab)) {
            $SESSION->iomad_company_admin_tab = $selectedtab;
        } else if (!empty($SESSION->iomad_company_admin_tab)) {
            $selectedtab = $SESSION->iomad_company_admin_tab;
        } else {
            $selectedtab = 1;
        }

        // Title.
        $this->content = new stdClass();
        $this->content->text = '<h3>'.
                               get_string('managementtitle', 'block_iomad_company_admin').
                               '</h3>';

        // Build tabs.
        $tabs = array();
        $tabs[1] = get_string('companymanagement', 'block_iomad_company_admin');
        $tabs[2] = get_string('usermanagement', 'block_iomad_company_admin');
        $tabs[3] = get_string('coursemanagement', 'block_iomad_company_admin');
        $tabs[4] = get_string('licensemanagement', 'block_iomad_company_admin');
        if (has_capability('block/iomad_commerce:admin_view', $context)) {
            $tabs[5] = get_string('blocktitle', 'block_iomad_commerce');
        }
        $tabhtml = $this->gettabs($tabs, $selectedtab);

        $this->content->text .= $tabhtml;

        // Build content for selected tab (from menu array).
        $adminmenu = new iomad_admin_menu();
        $menus = $adminmenu->getmenu();
        $html = '<div class="iomadlink_container clearfix">';
        foreach ($menus as $key => $menu) {

            // If it's the wrong tab then move on.
            if ($menu['tab'] != $selectedtab) {
                continue;
            }

            // If no capability the move on.
            if (!has_capability($menu['cap'], $context)) {
                continue;
            }

            // Build correct url.
            if (substr($menu['url'], 0, 1) == '/') {
                $url = new moodle_url($menu['url']);
            } else {
                $url = new moodle_url('/blocks/iomad_company_admin/'.$menu['url']);
            }

            // Build image url.
            if (!empty($menu['icon'])) {
                $imgsrc = $OUTPUT->pix_url($menu['icon'], 'block_iomad_company_admin');
                $icon = '<img src="'.$imgsrc.'" alt="'.$menu['name'].'" /><br />';
            } else {
                $icon = '';
            }

            // Put together link.
            $html .= '<div class="iomadlink">';
            $html .= "<a href=\"$url\">" . $icon . $menu['name'] . "</a>";
            $html .= '</div>';
        }
        $html .= '</div>';
        $this->content->text .= $html;

        // A clearfix for the floated linked.
        $this->content->text .= '<div class="clearfix"></div>';

        return $this->content;
    }

    /**
     * Build tabs for selecting admin page
     */
    public function gettabs($tabs, $selected) {

        // Get base url.
        $url = $this->page->url;

        // Build output.
        $html = '';

        // Build list.
        $html .= '<ul class="iomadtab">';
        foreach ($tabs as $key => $tab) {
            $url->param('tabid', $key);
            if ($key == $selected) {
                $class = 'class="iomadselected"';
                $link = $tab;
            } else {
                $class = '';
                $link = "<a href=\"$url\">$tab</a>";
            }
            $html .= "<li $class>$link</li>";
        }
        $html .= '</ul>';

        return $html;
    }

    /* email out passwords for newly created users
     * based on the code for 'creating passwords for new users' in cronlib.php and
     * setnew_password_and_mail function in moodlelib.php
     *
     * difference is that the passwords have already been generated so that the admin could
     * download them in a spreadsheet
     */
    public function cron() {
        global $DB;

        if ($DB->count_records('user_preferences', array('name' => 'iomad_send_password',
                                                         'value' => '1'))) {
            mtrace('creating passwords for new users');
            $newusers = $DB->get_records_sql("SELECT u.id as id, u.email, u.firstname,
                                                     u.lastname, u.username,
                                                     p.id as prefid,
                                                     p.value as prefvalue
                                                FROM {user} u
                                                JOIN {user_preferences} p ON u.id=p.userid
                                                JOIN {user_preferences} p2 ON u.id=p2.userid
                                               WHERE p.name='iomad_temporary'
                                                 AND u.email !=''
                                                 AND p2.name='iomad_send_password'
                                                 AND p2.value='1' ");

            mtrace('sending passwords to ' . count($newusers) . ' new users');

            foreach ($newusers as $newuserid => $newuser) {
                // Email user.
                if ($this->mail_password($newuser, company_user::rc4decrypt($newuser->prefvalue))) {
                    // Remove user pref.
                    unset_user_preference('iomad_send_password', $newuser);
                } else {
                    trigger_error("Could not mail new user password!");
                }
            }
        }
    }

    /**
     * Send the password to the user via email.
     *
     * @global object
     * @global object
     * @param user $user A {@link $USER} object
     * @return boolean|string Returns "true" if mail was sent OK and "false" if there was an error
     */
    public function mail_password($user, $password) {
        global $CFG, $DB;

        $site  = get_site();

        $supportuser = generate_email_supportuser();

        $a = new stdClass();
        $a->firstname   = fullname($user, true);
        $a->sitename    = format_string($site->fullname);
        $a->username    = $user->username;
        $a->newpassword = $password;
        $a->link        = $CFG->wwwroot .'/login/';
        $a->signoff     = generate_email_signoff();

        $message = get_string('newusernewpasswordtext', '', $a);

        $subject = format_string($site->fullname) .': '. get_string('newusernewpasswordsubj');

        return email_to_user($user, $supportuser, $subject, $message);
    }
}
