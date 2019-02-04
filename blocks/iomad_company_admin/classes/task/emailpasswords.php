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
 * Task for updating RSS feeds for rss client block
 *
 * @package   block_iomad_company_admin
 * @author    Howard Miller
 * @copyright Howard Miller 2018
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_company_admin\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Task for updating RSS feeds for rss client block
 *
 * @package   block_recent_activity
 * @author    Farhan Karmali <farhan6318@gmail.com>
 * @copyright Farhan Karmali 2018
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class emailpasswords extends \core\task\scheduled_task {

    /**
     * Name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('emailpasswordstask', 'block_iomad_company_admin');
    }

    /* email out passwords for newly created users
     * based on the code for 'creating passwords for new users' in cronlib.php and
     * setnew_password_and_mail function in moodlelib.php
     *
     * difference is that the passwords have already been generated so that the admin could
     * download them in a spreadsheet
     */
    public function execute() {
        global $CFG, $DB;

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
    protected function mail_password($user, $password) {
        global $CFG, $DB;

        $site  = get_site();

        $supportuser = generate_email_supportuser();

        $a = new \stdClass();
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
