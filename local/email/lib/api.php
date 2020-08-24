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

require_once(dirname(__FILE__) . '/../local_lib.php');
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
    protected $activity = null;
    protected $due = null;
    protected $nugget = null;
    protected $attachment = null;

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
        global $DB;

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

            // If company isn't set - can't send.
            if (empty($emailtemplate->company)) {
                return true;
            }
            //Is the template enabled for the company?
            $company = new company($emailtemplate->company->id);
            $managertype = 0;
            if (strpos($templatename, 'manager')) {
                $managertype = 1;
            }
            if (strpos($templatename, 'supervisor')) {
                $managertype = 2;
            }
            if (!$company->email_template_is_enabled($templatename, $managertype)) {
                return true;
            }

            // It's Ok to send, so do so.
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
        global $USER, $SESSION, $COURSE, $DB, $CFG;

        $user = array_key_exists('user', $options) ? $options['user'] : null;
        $course = array_key_exists('course', $options) ? $options['course'] : null;
        $this->invoice = array_key_exists('invoice', $options) ? $options['invoice'] : null;
        $this->due = array_key_exists('due', $options) ? $options['due'] : time();
        $sender = array_key_exists('sender', $options) ? $options['sender'] : null;
        $approveuser = array_key_exists('approveuser', $options) ? $options['approveuser'] : null;
        $this->event = array_key_exists('event', $options) ? $options['event'] : null;
        $this->classroom = array_key_exists('classroom', $options) ? $options['classroom'] : null;
        $this->license = array_key_exists('license', $options) ? $options['license'] : null;
        $this->headers = array_key_exists('headers', $options) ? $options['headers'] : null;
        $this->company = array_key_exists('company', $options) ? $options['company'] : null;
        $this->activity = array_key_exists('activity', $options) ? $options['activity'] : null;
        $this->nugget = array_key_exists('nugget', $options) ? $options['nugget'] : null;
        $this->attachment = array_key_exists('attachment', $options) ? $options['attachment'] : null;

        if (!isset($user)) {
            $user =& $USER;
        }
        if (!isset($course)) {
            $course =& $COURSE;
        }
        if (!isset($sender) || is_siteadmin($USER->id)) {
            if ($USER->id == 0 || is_siteadmin($USER->id)) {
                // We are being run from cron.
                $sender = self::get_sender($user);
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
        } else if (empty($this->company)) {
            $companyid = iomad::get_my_companyid(context_system::instance(), false);
            $this->company = new company($companyid);
        }

        $this->course = $this->get_course($course);

        $this->templatename = $templatename;
        $this->template = $this->get_template($templatename);
        if (empty($this->attachment) && !empty($this->template->id)) {
            $context = context_system::instance();
            if ($files = $DB->get_records('files', array('contextid' => $context->id,
                                                         'component' => 'local_email',
                                                         'filearea' => 'companylogo',
                                                         'itemid' => $this->template->id))) {
                foreach ($files as $file) {
                    if ($file->filename != '.') {
                        $filedir1 = substr($file->contenthash,0,2);
                        $filedir2 = substr($file->contenthash,2,2);
                        $this->attachment = new stdclass();
                        $this->attachment->filepath = $CFG->dataroot . '/filedir/' . $filedir1 . '/' . $filedir2 . '/' . $file->contenthash;
                        $this->attachment->filename = $file->filename;
                    }
                }
            }
        }
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
     * Gets the signature for the email template from the language file
     * and sets a class variable from it.
     *
     **/
    public function signature() {
        return $this->fill($this->template->signature);
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
            if (!empty($this->template->signature)) {
                $email->body = $this->body() . get_string('signatureseparator', 'local_email') . $this->signature();
            } else {
                $email->body = $this->body();
                $this->template->signature = '';
            }
            $email->varsreplaced = 1;
            $email->userid = $this->user->id;
            $email->due = $this->due;
            $email->companyid = $this->company->id;
            if (isset($email->headers)) {
                $email->customheaders = unserialize($email->headers);
            } else {
                $email->customheaders = array();
            }

            // Deal with any attachment.
            if (!empty($this->attachment)) {
                // add in the attachment to the body.
                $email->customheaders['attachment'] = $this->attachment;
            }
            // Deal with To users
            if (!empty($this->template->emailto)) {
                $tousers = explode(',', $this->template->emailto);
                foreach ($tousers as $touser) {
                    if ($touserrec = $DB->get_record('user', array('id' => $touser, 'deleted' => 0, 'suspended' => 0))) {
                        $email->customheaders[] = "To:".$touserrec->email;
                    }
                }
            } else {
                $this->template->emailto = '';
            }
            if (!empty($this->template->emailtoother)) {
                $tootherusers = explode(',', $this->template->emailtoother);
                foreach ($tootherusers as $tootheruser) {
                    if (validate_email($tootheruser)) {
                        $email->customheaders[] = "To:".$tootheruser;
                    }
                }
            } else {
                $this->template->emailtoother = '';
            }

            // Deal with CC users
            if (!empty($this->template->emailcc)) {
                $ccusers = explode(',', $this->template->emailcc);
                foreach ($ccusers as $ccuser) {
                    if ($ccuserrec = $DB->get_record('user', array('id' => $ccuser, 'deleted' => 0, 'suspended' => 0))) {
                        $email->customheaders[] = "Cc:".$ccuserrec->email;
                    }
                }
            } else {
                $this->template->emailcc = '';
            }
            if (!empty($this->template->emailccother)) {
                $ccotherusers = explode(',', $this->template->emailccother);
                foreach ($ccotherusers as $ccotheruser) {
                    if (validate_email($ccotheruser)) {
                        $email->customheaders[] = "Cc:".$ccotheruser;
                    }
                }
            } else {
                $this->template->emailccother = '';
            }

            // Deal with reply user
            if (!empty($this->template->emailreplyto)) {
                if ($replytouserrec = $DB->get_record('user', array('id' => $this->template->emailreplyto, 'deleted' => 0, 'suspended' => 0))) {
                    $email->customheaders[] = "reply-to:".$replytouserrec->email;
                }
            } else {
                $this->template->emailreplyto = '';
            }
            if (!empty($this->template->emailreplytoother) && validate_email($this->template->emailreplytoother)) {
                $email->customheaders[] = "reply-to:".$this->template->emailreplytoother;
            } else {
                $this->template->emailreplytoother = '';
            }

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

            if (empty($this->template->disabled)) {
                $this->template->disabled = 0;
            }

            if (empty($this->template->disabledmanager)) {
                $this->template->disabledmanager = 0;
            }

            if (empty($this->template->disabledsupervisor)) {
                $this->template->disabledsupervisor = 0;
            }

            if (empty($this->template->repeatperiod)) {
                $this->template->repeatperiod = 0;
            }

            if (empty($this->template->repeatvalue)) {
                $this->template->repeatvalue = 0;
            }

            if (empty($this->template->repeatday)) {
                $this->template->repeatday = 0;
            }

            if (empty($this->template->emailfrom)) {
                $this->template->emailfrom = '';
            }

            if (empty($this->template->emailfromother)) {
                $this->template->emailfromother = '';
            }

            if (empty($this->template->emailfromothername)) {
                $this->template->emailfromothername = '';
            }

            if (empty($this->template->name)) {
                $this->template->name = $email->templatename;
            }
            $email->customheaders['template'] = $this->template;
            $email->headers = serialize($email->customheaders);

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
        global $USER, $DB;

        $supportuser = new stdclass();
        $company = new company($email->companyid);

        // Check if the user to be sent to is valid.
        if ($user = self::get_user($email->userid)) {
            if (isset($email->senderid) && !is_siteadmin($email->senderid) && $email->senderid > 0) {
                $supportuser = self::get_user($email->senderid);
            } else {
                $supportuser = self::get_user(self::get_sender($user));
            }

            if (empty($supportuser)) {
                $supportuser = new stdclass();
            }

            if (!empty($email->headers)) {
                $supportuser->customheaders = unserialize($email->headers);
            } else {
                $supportuser->customheaders = '';
            }

            if (!empty($supportuser->customheaders['template'])) {
                $template = $supportuser->customheaders['template'];
                unset($supportuser->customheaders['template']);
            } else {
                $emailtemplate = new EmailTemplate($email->templatename, array('course' => $email->courseid, 'user' => $user, 'company' => $company));
                $template = $emailtemplate->template;
            }

            if (!empty($template->replyto)) {
                $replytouser = self::get_user($template->replyto);
                $supportuser->customheaders['From'] = $replytouser->email;
                $supportuser->customheaders['Reply-to'] = $replytouser->email;
            }
            if (!empty($template->replytoother) && validate_email($template->replytoother)) {
                $supportuser->customheaders['From'] = $template->replytoother;
                $supportuser->customheaders['Reply-to'] = $template->replytoother;
            }
            if (!empty($template->emailfromother) && validate_email($template->emailfromother) ) {
                $supportuser->email = $template->emailfromother;
                $supportuser->customheaders['From'] = $template->emailfromother;
            }
            if (!empty($template->emailfromothername)) {
                if ($template->emailfromothername == "{Company_Name}") {
                    $supportuser->firstname = $company->get_name();
                } else {
                    $supportuser->firstname = $template->emailfromothername;
                }
            }
            if (!empty($template->emailfrom)) {
                $fromuser = self::get_user($template->emailfrom);
                $supportuser->email = $fromuser->email;
                $supportuser->firstname = $fromuser->firstname;
                $supportuser->customheaders['From'] = $fromuser->email;
            }

            if (!empty($supportuser->customheaders['attachment'])) {
                $attachment = $supportuser->customheaders['attachment'];
                unset($supportuser->customheaders['attachment']);
            } else {
                $attachment = null;
            }
            // Send the main email.
            if (!self::email_direct($user->email,
                               $supportuser,
                               $email->subject,
                               html_to_text($email->body),
                               $email->body,
                               $attachment)) {
                return false;
            }
            // Send to all of the to user emails.
            if (!empty($template->emailto)) {
                $touserids = explode(',', $template->emailto);
                foreach ($touserids as $touserid) {
                    if ($touser = $DB->get_record('user', array('id' => $touserid, 'deleted' => 0, 'suspended' => 0))) {
                        if (!self::email_direct($touser->email,
                                           $supportuser,
                                           $email->subject,
                                           html_to_text($email->body),
                                           $email->body,
                                           $attachment)) {
                            return false;
                        }
                    }
                }
            }

            // Send to all of the cc user emails.
            if (!empty($template->emailcc)) {
                $ccuserids = explode(',', $template->emailcc);
                foreach ($ccuserids as $ccuserid) {
                    if ($ccuser = $DB->get_record('user', array('id' => $ccuserid, 'deleted' => 0, 'suspended' => 0))) {
                        if (!self::email_direct($ccuser->email,
                                           $supportuser,
                                           $email->subject,
                                           html_to_text($email->body),
                                           $email->body,
                                           $attachment)) {
                            return false;
                        }
                    }
                }
            }

            // Deal with the manual to users.
            if (!empty($template->emailtoother)) {
                $toothers = explode(',', $template->emailtoother);
                foreach ($toothers as $toother) {
                    if (validate_email($toother)) {
                        if (!self::email_direct($toother,
                                                $supportuser,
                                                $email->subject,
                                                html_to_text($email->body),
                                                $email->body,
                                                $attachment)) {
                            return false;
                        }
                    }
                }
            }

            // Deal with the manual cc users.
            if (!empty($template->emailccother)) {
                $ccothers = explode(',', $template->emailccother);
                foreach ($ccothers as $ccother) {
                    if (validate_email($ccother)) {
                        if (!self::email_direct($ccother,
                                                $supportuser,
                                                $email->subject,
                                                html_to_text($email->body),
                                                $email->body,
                                                $attachment)) {
                            return false;
                        }
                    }
                }
            }

            // is this a user template?
            if (self::is_user_template($email->templatename)) {
                // Do we send to managers as well?
                if (empty($template->disabledmanager)) {
                    // Get the users managers.
                    if ($managers = company::get_my_managers($email->userid, 1)) {
                        foreach ($managers as $manager) {
                            if ($managerrec = $DB->get_record('user', array('deleted' => 0, 'suspended' => 0, 'id' => $manager->userid))) {
                                if (!self::email_direct($managerrec->email,
                                                        $supportuser,
                                                        $email->subject,
                                                        html_to_text($email->body),
                                                        $email->body,
                                                        $attachment)) {
                                    return false;
                                }
                            }
                        }
} else {
                    }
                }

                // Do we send to external supervisors as well?
                if (empty($template->disabledsupervisor)) {
                    // Get the users supervisors.
                    if ($supervisors = company::get_usersupervisor($email->userid)) {
                        foreach ($supervisors as $supervisor) {
                            if (!self::email_direct($supervisor,
                                                    $supportuser,
                                                    $email->subject,
                                                    html_to_text($email->body),
                                                    $email->body,
                                                    $attachment)) {
                                return false;
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Class handling of the global email_to_user Moodle function.
     *
     *
     **/
    public function email_to_user() {
        global $USER;

        $supportuser = new stdclass();
        $subject = $this->subject();
        $body = $this->body();
        $company = new company($this->companyid);

        if (isset($this->emailfrom)) {
            $supportuser = self::get_user($this->emailfrom);
            if (isset($email->headers)) {
                $supportuser->customheaders = unserialize($email->headers);
            } else {
                $supportuser->customheaders = array();
            }

            if (empty($supportuser)) {
                $supportuser = new stdclass();
            }

        } else {
            if (isset($this->emailfromother) && validate_email($this->emailfromother)) {
                $supportuser == core_user::get_support_user();
                if (isset($email->headers)) {
                    $supportuser->customheaders = unserialize($email->headers);
                } else {
                    $supportuser->customheaders = array();
                }
                $supportuser->emailaddress = $this->emailfromother;
                if ($this->emailfromothername == "{Company_Name}") {
                    $supportuser->firstname = $this->emailfromother;
                } else {
                    $supportuser->firstname = $this->emailfromothername;
                }
            } else if (isset($this->sender->id)) {
                $supportuser = self::get_user($this->sender->id);
                if (isset($email->headers)) {
                    $supportuser->customheaders = unserialize($email->headers);
                } else {
                    $supportuser->customheaders = array();
                }
            } else {
                $supportuser = self::get_user(self::get_sender($this->user->id));
                if (isset($email->headers)) {
                    $supportuser->customheaders = unserialize($email->headers);
                } else {
                    $supportuser->customheaders = array();
                }
            }
        }

        // Deal with To users
        if (!empty($this->emailto)) {
            $tousers = explode(',', $this->emailto);
            foreach ($tousers as $touser) {
                if ($touserrec = $DB->get_record('user', array('id' => $touser, 'deleted' => 0, 'suspended' => 0))) {
                    $supportuser->customheaders[] = "To:".$touserrec->email;
                }
            }
        }
        if (!empty($this->emailtoother)) {
            $tootherusers = explode(',', $this->emailtoother);
            foreach ($tootherusers as $tootheruser) {
                if (validate_email($tootheruser)) {
                    $supportuser->customheaders[] = "To:".$tootheruser;
                }
            }
        }

        // Deal with CC users
        if (!empty($this->emailcc)) {
            $ccusers = explode(',', $this->emailcc);
            foreach ($ccusers as $ccuser) {
                if ($ccuserrec = $DB->get_record('user', array('id' => $ccuser, 'deleted' => 0, 'suspended' => 0))) {
                    $supportuser->customheaders[] = "Cc:".$ccuserrec->email;
                }
            }
        }
        if (!empty($this->emailccother)) {
            $ccotherusers = explode(',', $this->emailccother);
            foreach ($ccotherusers as $ccotheruser) {
                if (validate_email($ccotheruser)) {
                    $supportuser->customheaders[] = "Cc:".$ccotheruser;
                }
            }
        }

        // Deal with reply user
        if (!empty($this->emailreplyto)) {
            if ($replytouserrec = $DB->get_record('user', array('id' => $this->emailreplyto, 'deleted' => 0, 'suspended' => 0))) {
                $supportuser->customheaders[] = "reply-to:".$replytouserrec->email;
            }
        }
        if (!empty($this->emailreplytoother) && validate_email($this->emailreplytoother)) {
            $supportuser->customheaders[] = "reply-to:".$this->emailreplytoother;
        }

        if (empty($this->attachment)) {
            self::email_direct($user->email,
                               $supportuser,
                                $email->subject,
                                html_to_text($email->body),
                                $email->body);
        } else {
            self::email_direct($user->email,
                                $supportuser,
                                $email->subject,
                                html_to_text($email->body),
                                $email->body,
                                $this->attachment);
        }

        $this->email_supervisor;
    }

    /**
     * Send to  Moodle function.supervisor.
     *
     *
     **/
    public function email_supervisor() {
        global $USER, $CFG;

        // Do we send this template?
        if (!$this->template_enabled(2)) {
            return true;
        }

        $supportuser = new stdclass();
        $subject = $this->subject();
        $body = $this->body();
        if (isset($this->sender->id)) {
            $supportuser = self::get_user($this->sender->id);
        } else {
            $supportuser = self::get_user(self::get_sender($this->user->id));
        }

        if (empty($supportuser)) {
            $supportuser = new stdclass();
            $supportuser->firstname = "";
        }

        if (isset($email->headers)) {
            $supportuser->customheaders = unserialize($email->headers);
        } else {
            $supportuser->customheaders = '';
        }
        // Do we have a supervisor?
        if ($supervisoremails = company::get_usersupervisor($this->user->id)) {
            $mail = get_mailer();
            if ($CFG->smtphosts == 'qmail') {
                // Use Qmail system.
                $mail->isQmail();

            } else if (empty($CFG->smtphosts)) {
                // Use PHP mail() = sendmail.
                $mail->isMail();

            } else {
                // Use SMTP directly.
                $mail->isSMTP();
                if (!empty($CFG->debugsmtp)) {
                    $mail->SMTPDebug = true;
                }
                // Specify main and backup servers.
                $mail->Host          = $CFG->smtphosts;
                // Specify secure connection protocol.
                $mail->SMTPSecure    = $CFG->smtpsecure;
                // Use previous keepalive.

                if ($CFG->smtpuser) {
                    // Use SMTP authentication.
                    $mail->SMTPAuth = true;
                    $mail->Username = $CFG->smtpuser;
                    $mail->Password = $CFG->smtppass;
                }
            }

            foreach ($supervisoremails as $supervisoremail) {
                $mail->Sender = $CFG->noreplyaddress;
                $mail->FromName = $supportuser->firstname;
                $mail->From     = $CFG->noreplyaddress;
                if (empty($CFG->divertallemailsto)) {
                    $mail->Subject = substr($subject, 0, 900);
                } else {
                    $mail->Subject = substr('[DIVERTED ' . $supervisoremail . '] ' . $subject, 0, 900);
                    $supervisoremail = $CFG->divertallemailsto;
                }
                $mail->addAddress($supervisoremail, '');

                // Set word wrap.
                $mail->WordWrap = 79;
                $mail->Body =  "\n$body\n";
                // Do we have an attachment.
                if (!empty($this->attachment)) {
                    require_once($CFG->libdir.'/filelib.php');
                    $mimetype = mimeinfo('type', $this->attachment->filename);
                    $mail->addAttachment($this->attachment->filepath, $this->attachment->filename, 'base64', $mimetype);
                }
                if (empty($CFG->noemailever)) {
                    if(!$mail->send()) {
                        mtrace( 'Message could not be sent.');
                        mtrace( 'Mailer Error: ' . $mail->ErrorInfo);
                    }
                }
            }
        }

        return true;
    }

    /**
     * Send to  Moodle function.supervisor.
     *
     *
     **/
    private static function email_direct($emailaddress, $supportuser, $subject, $messagetext, $messagehtml = '', $attachment = null) {
        global $USER, $CFG;

        $mail = get_mailer();
        if ($CFG->smtphosts == 'qmail') {
            // Use Qmail system.
            $mail->isQmail();

        } else if (empty($CFG->smtphosts)) {
            // Use PHP mail() = sendmail.
            $mail->isMail();

        } else {
            // Use SMTP directly.
            $mail->isSMTP();
            if (!empty($CFG->debugsmtp)) {
                $mail->SMTPDebug = true;
            }
            // Specify main and backup servers.
            $mail->Host          = $CFG->smtphosts;
            // Specify secure connection protocol.
            $mail->SMTPSecure    = $CFG->smtpsecure;
            // Use previous keepalive.

            if ($CFG->smtpuser) {
                // Use SMTP authentication.
                $mail->SMTPAuth = true;
                $mail->Username = $CFG->smtpuser;
                $mail->Password = $CFG->smtppass;
            }
        }

        if (!empty($supportuser->customheaders['From'])) {
            $mail->From = $supportuser->customheaders['From'];
            unset($supportuser->customheaders['Reply-to']);
        } else {
            $mail->From = $CFG->noreplyaddress;
        }

        if (!empty($supportuser->customheaders['Reply-to'])) {
            $mail->addReplyTo($supportuser->customheaders['Reply-to']);
            unset($supportuser->customheaders['Reply-to']);
        }
        foreach ($supportuser->customheaders as $value) {
            $mail->addCustomHeader($value);
        }

        $mail->Sender = $CFG->noreplyaddress;
        $mail->FromName = $supportuser->firstname;
        if (empty($CFG->divertallemailsto)) {
            $mail->Subject = substr($subject, 0, 900);
            $mail->addAddress($emailaddress, '');
        } else {
            $mail->Subject = substr('[DIVERTED ' . $emailaddress . '] ' . $subject, 0, 900);
            $mail->addAddress($CFG->divertallemailsto, '');
        }

        // Set word wrap.
        $mail->WordWrap = 79;

        if ($messagehtml) {
            // Don't ever send HTML to users who don't want it.
            $mail->isHTML(true);
            $mail->Encoding = 'quoted-printable';
            $mail->Body    =  $messagehtml;
            $mail->AltBody =  "\n$messagetext\n";
        } else {
            $mail->IsHTML(false);
            $mail->Body =  "\n$messagetext\n";
        }

        // Do we have an attachment.
        if (!empty($attachment)) {
            require_once($CFG->libdir.'/filelib.php');
            $mimetype = mimeinfo('type', $attachment->filename);
            $mail->addAttachment($attachment->filepath, $attachment->filename, 'base64', $mimetype);
        }
        if (empty($CFG->noemailever)) {
            if(!$mail->send()) {
                mtrace( 'Message could not be sent.');
                mtrace( 'Mailer Error: ' . $mail->ErrorInfo);
                return false;
            }
        }

        return true;
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
                        } else if ($user == core_user::get_support_user()) {
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
        global $DB;

        if (isset($this->company->id)) {
            $companyid = $this->company->id;
        }

        $email = local_email::get_templates();
        // Try to get it out of the database, otherwise get it from config file.
        if (!isset($companyid) || !$template = $DB->get_record('email_template', array('name' => $templatename,
                                                                                       'companyid' => $companyid,
                                                                                       'lang' => $this->user->lang), '*')) {
            if (!$template = $DB->get_record('email_template', array('name' => $templatename,
                                                                     'companyid' => $companyid,
                                                                     'lang' => 'en'), '*')) {
                if (isset($email[$templatename])) {
                    $template = (object) $email[$templatename];
                } else {
                    print_error("Email template '$templatename' not found");
                }
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
                              $this->nugget,
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

    /**
     * Checks if the template is enabled for this company.
     *
     **/
    private function template_enabled($type = 0) {
        global $DB;

        // Is this template enabled for the company.
        if ($DB->get_records('email_template', array('name' => $this->templatename, 'companyid' => $this->company->id, 'disabled' =>1))) {
            return false;
        }

        if ($type == 2 || strpos('supervisor', $this->templatename) !== false) {
            // Is this template enabled for the supervisor.
            if ($DB->get_records('email_template', array('name' => $this->templatename, 'companyid' => $this->company->id, 'disabledsupervisor' =>1))) {
                return false;
            }
        }

        if ($type == 3 || strpos('manager', $this->templatename) !== false) {
            // Is this template enabled for the supervisor.
            if ($DB->get_records('email_template', array('name' => $this->templatename, 'companyid' => $this->company->id, 'disabledmanager' =>1))) {
                return false;
            }
        }

        // Default return true.
        return true;
    }

    /**
     * Checks if this is a user template or not.
     *
     **/
    private static function is_user_template($templatename) {

        $usertemplates = array( 'completion_course_user' => 'completion_course_user',
                                'course_not_started_warning' => 'course_not_started_warning',
                                'completion_warn_user' => 'completion_warn_user',
                                'expiry_warn_user' => 'expiry_warn_user',
                                'license_allocated' => 'license_allocated',
                                'license_removed' => 'license_removed',
                                'microlearning_nugget_scheduled',
                                'microlearning_nugget_reminder1',
                                'microlearning_nugget_reminder2',
                                'password_update' => 'password_update',
                                'user_added_to_course' => 'user_added_to_course',
                                'user_create' => 'user_create',
                                'user_deleted' => 'user_deleted',
                                'user_programcompleted' => 'user_programcompleted',
                                'user_promoted' => 'user_promoted',
                                'user_removed_from_event' => 'user_removed_from_event',
                                'user_reset' => 'user_reset',
                                'user_signed_up_for_event' => 'user_signed_up_for_event',
                                'user_suspended' => 'user_suspended',
                                'user_unsuspended' => 'user_unsuspended');

        if (!empty($usertemplates[$templatename])) {
            return true;
        }

        return false;
    }
}
