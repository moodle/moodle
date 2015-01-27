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

require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../../../user/profile/lib.php');
require_once(dirname(__FILE__) . '/../../../local/iomad/lib/company.php');

class EmailTemplate {
    protected $user = null;
    protected $course = null;
    protected $template = null;
    protected $templatename = null;
    protected $company = null;
    protected $invoice = null;
    protected $classroom = null;
    protected $sender = null;
    protected $headers = null;
    protected $approveuser = null;
    protected $event = null;

    /**
     * Send an email to (a) specified user(s)
     *
     * @param string $templatename Name of the template as described in
     *                             the global $email array or overridden
     *                             in the mdl_email_template table
     * @param array $options array of options to pass into each email
     * @param array $loopoptions array of array of options to pass into
     *                           individual emails. This could for instance
     *                           be used to send emails to multiple users
     *                           send($tname,
     *                                array('course' =>1 ),
     *                                array(array('user' => 1),array('user' => 2),array('user' => 3))
     *                           )
     * @return bool if no $loopoptions where specified:
     *              Returns true if mail was sent OK and false if there was an error
     *              or in case $loopoptions were specified:
     *              returns number of successes (ie. count of $loopoptions)
     *              or if there was an error, those $loopoptions for which there was an error
     */
    public static function send($templatename, $options = array(), $loopoptions = array()) {
        if (count($loopoptions)) {
            $results = array();
            foreach ($loopoptions as $loptions) {
                $combinedoptions = array_merge($options, $loptions);
                $ok = false;
                try {
                    $ok = self::send($templatename, $combinedoptions);
                } catch (Exception $e) {
                    // Something to go here.
                }
                if (!$ok) {
                    $results[] = $loptions;
                }
            }

            if (count($results)) {
                return $results;
            } else {
                return count($loopoptions);
            }
        } else {
            $emailtemplate = new self($templatename, $options);
            return $emailtemplate->queue_for_cron();
        }
    }

    /**
     * Send an email to all users in a department (and it's subdepartments)
     *
     * @param integer $departmentid id of the department
     * @param string $templatename Name of the template as described in
     *                             the global $email array or overridden
     *                             in the mdl_email_template table
     * @param array $options array of options to pass into each email
     * @return bool Returns true if mail was sent OK and false if there was an error
     */
    public static function send_to_all_users_in_department($departmentid, $templatename, $options = array()) {
        global $DB;

        $users = company::get_recursive_department_users($departmentid);
        $useroptions = array_map('self::getuseroption', $users);
        $result = self::send($templatename, $options, $useroptions);
        if ($result === true) {
            return true;
        } else {
            return $result == count($useroptions);
        }
    }

    /**
     * Gets the users options from the user reference object passed.
     *
     * Input $userrefobject = stdclass();
     *
     * Returns Array.
     *
     **/
    private static function getuseroption($userrefobject) {
        return array('user' => $userrefobject->userid);
    }

    public function __construct($templatename, $options = array()) {
        global $USER, $SESSION, $COURSE, $DB;

        $user = array_key_exists('user', $options) ? $options['user'] : null;
        $course = array_key_exists('course', $options) ? $options['course'] : null;
        $this->invoice = array_key_exists('invoice', $options) ? $options['invoice'] : null;
        $sender = array_key_exists('sender', $options) ? $options['sender'] : null;
        $approveuser = array_key_exists('approveuser', $options) ? $options['approveuser'] : null;
        $event = array_key_exists('event',$options) ? $options['event'] : null;
        $this->classroom = array_key_exists('classroom', $options) ? $options['classroom'] : null;
        $this->license = array_key_exists('license', $options) ? $options['license'] : null;
        $this->headers = array_key_exists('headers', $options) ? $options['headers'] : null;

        if (!isset($user)) {
            $user =& $USER;
        }
        if (!isset($course)) {
            $course =& $COURSE;
        }
        if (!isset($sender)) {
            if ($USER->id == 0) {
                // We are being run from cron.
                $sender =& self::get_sender($user);
            } else {
                // Not been defined explicitly, use the current user.
                $sender = $USER;
            }
        }

        // Set the sender to the default site one if use real sender is not true.
        if (empty($CFG->iomad_email_senderisreal)) {
            $sender = core_user::get_support_user();
        }

        $this->user = $this->get_user($user);
        $this->approveuser = $this->get_user($approveuser);

        // Check if we are being passed a password and add it if so.
        if (isset($user->newpassword)) {
            $this->user->newpassword = $user->newpassword;
        }

        $this->sender = $this->get_user($sender);

        if (!isset($this->user->email)) {
            print_error("No user was specified or the specified user has no email to send $templatename to.");
        }

        if (isset($this->user->id) && !isset($this->user->profile)) {
            profile_load_custom_fields($this->user);
        }
        // Check if we are an admin with a company set.
        if (!empty($SESSION->currenteditingcompany)) {
            $this->company = new company($SESSION->currenteditingcompany);
            // Otherwise use the creating users company.
        } else {
            $this->company = $DB->get_record_sql("SELECT * FROM {company}
                                                  WHERE id = (
                                                   SELECT companyid FROM {company_users}
                                                   WHERE userid = :userid
                                                  )", array('userid' => $USER->id));
        }

        $this->course = $this->get_course($course);
        $this->event = $event;

        $this->templatename = $templatename;
        $this->template = $this->get_template($templatename);
    }

    /**
     * Gets the subject for the email template from the language file
     * and sets a class variable from it.
     *
     **/
    public function subject() {
        return $this->fill($this->template->subject);
    }

    /**
     * Gets the body for the email template from the language file
     * and sets a class variable from it.
     *
     **/
    public function body() {
        return $this->fill($this->template->body);
    }

    /**
     * Sets up an email to be sent out by the Moodle cron.
     *
     **/
    public function queue_for_cron() {
        global $DB;

        if (isset($this->user->id)) {
            $email = new stdClass;
            $email->templatename = $this->templatename;
            $email->modifiedtime = time();
            $email->subject = $this->subject();
            $email->body = $this->body();
            $email->varsreplaced = 1;
            $email->userid = $this->user->id;
            if ($this->course) {
                $email->courseid = $this->course->id;
            }
            if ($this->classroom) {
                $email->classroomid = $this->classroom->id;
            }
            if ($this->invoice) {
                $email->invoiceid = $this->invoice->id;
            }
            if ($this->sender) {
                $email->senderid = $this->sender->id;
            }
            if ($this->headers) {
                $email->headers = $this->headers;
            }

            return $DB->insert_record('email', $email);
        } else {
            // Can't queue it for cron, attempt to send it immediately.
            return $this->email_to_user();
        }
    }

    /**
     * Sends an email to the user it is meant for
     *
     * Parameters - $email = stdclass();
     *
     **/
    static public function send_to_user($email) {
        global $USER;

        // Check if the user to be sent to is valid.
        if ($user = self::get_user($email->userid)) {
            if (isset($email->senderid)) {
                $supportuser = self::get_user($email->senderid);
            } else {
                $supportuser = self::get_user(self::get_sender($user));
            }
            if (isset($email->headers)) {
                $supportuser->customheaders = unserialize($email->headers);
                email_to_user($USER, $supportuser, $email->subject, $email->body);
            }
            return email_to_user($user, $supportuser, $email->subject, $email->body);
        }
    }

    /**
     * Class handling of the global email_to_user Moodle function.
     *
     *
     **/
    public function email_to_user() {
        global $USER;

        $subject = $this->subject();
        $body = $this->body();
        if (isset($this->sender->id)) {
            $supportuser = self::get_user($this->sender->id);
        } else {
            $supportuser = self::get_user(self::get_sender($this->userid));
        }
        if (isset($email->headers)) {
                $supportuser->customheaders = unserialize($email->headers);
                email_to_user($USER, $supportuser, $email->subject, $email->body);
        }

        return email_to_user($this->user, $supportuser, $subject, $body);
    }

    /**
     * SGets the user information from the database for the user provided.
     *
     * Parameters - $user = stdclass() or int.
     *
     **/
    private static function get_user($user) {
        global $DB;

        if ($user) {
            // If $user is an integer, it is a user id, get the object from database.
            if (is_int($user) || is_string($user)) {
                if ($user = $DB->get_record('user', array('id' => $user), '*')) {
                    return $user;
                } else {
                    return false;
                }
            } else {
                if (!empty($user->id)) {
                    if ($user->id > 0) {
                        if ($user = $DB->get_record('user', array('id' => $user->id), '*')) {
                            return $user;
                        } else {
                            return false;
                        }
                    } else {
                        if (!empty($user->email) && !empty($user->firstname) && !empty($user->lastname)) {
                            return $user;
                        } else {
                            return false;
                        }
                    }
                }
            }
        }
    }

    /**
     * Gets the course information from the provided course variable
     *
     * Parameters - $course = int;
     *
     **/
    private function get_course($course) {
        global $DB;

        if ($course) {
            // If $course is an integer, it is a course id, get the object from database.
            if (is_int($course) || is_string($course)) {
                if (!$course = $DB->get_record('course', array('id' => $course), '*', MUST_EXIST)) {
                    print_error('Course ID was incorrect');
                }
            }

            if ($course) {
                return $course;
            }
        }
    }

    /**
     * Internal function to get the defined template for the email for the company.
     *
     * Parameters - $templatename = text
     *
     **/
    private function get_template($templatename) {
        global $DB, $email;

        if (isset($this->company->id)) {
            $companyid = $this->company->id;
        }

        // Try to get it out of the database, otherwise get it from config file.
        if (!isset($companyid) || !$template = $DB->get_record('email_template', array('name' => $templatename,
                                                                                       'companyid' => $companyid), '*')) {
            if (isset($email[$templatename])) {
                $template = (object) $email[$templatename];
            } else {
                print_error("Email template '$templatename' not found");
            }
        }

        return $template;
    }

    /**
     * Sets up the email class vars for the given template
     *
     * Parameters - $templatestring = text;
     *
     **/
    public function fill($templatestring) {
        $amethods = EmailVars::vars();

        $vars = new EmailVars($this->company,
                              $this->user,
                              $this->course,
                              $this->invoice,
                              $this->classroom,
                              $this->license,
                              $this->sender,
                              $this->approveuser,
                              $this->event);

        foreach ($amethods as $funcname) {
            $replacement = "{" . $funcname . "}";

            if (stripos($templatestring, $replacement) !== false) {
                $val = $vars->$funcname;

                $templatestring = str_replace($replacement, $val, $templatestring);
            }
        }

        return $templatestring;
    }

    /**
     * Gets the user company information for the provided user
     *
     * Parameters - $user = stdclass();
     *
     **/
    private static function get_sender($user) {

        // Get the user's company.
        if ($usercompany = company::get_company_byuserid($user->id)) {
            // Is there a default contact userid?
            if (isset($usercompany->defaultcontactid)) {
                $returnid = $usercompany->defaultcontactid;
            } else {
                // Use the default support email account.
                $returnid = core_user::get_support_user();
            }
        } else {
            // No company use default support user.
            $returnid = core_user::get_support_user();
        }
        return $returnid;
    }
}
