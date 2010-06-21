<?php

require_once($CFG->dirroot.'/enrol/authorize/const.php');
require_once($CFG->dirroot.'/enrol/authorize/localfuncs.php');
require_once($CFG->dirroot.'/enrol/authorize/authorizenet.class.php');
require_once($CFG->libdir.'/eventslib.php');

/**
 * Authorize.net Payment Gateway plugin
 */
class enrolment_plugin_authorize
{

    /**
     * Cron log.
     *
     * @var string
     * @access public
     */
    public $log;


    /**
     * Presents registration forms.
     *
     * @param object $course Course info
     * @access public
     */
    public function print_entry($course)
    {
        global $CFG, $USER, $OUTPUT, $PAGE, $form;

        $zerocost = zero_cost($course);
        if ($zerocost) {
            $manual = enrolment_factory::factory('manual');
            if (!empty($this->errormsg)) {
                $manual->errormsg = $this->errormsg;
            }
            $manual->print_entry($course);
            return;
        }

        prevent_double_paid($course);
        httpsrequired();

        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 443) { // MDL-9836
            if (empty($CFG->loginhttps)) {
                print_error('httpsrequired', 'enrol_authorize');
            } else {
                $wwwsroot = str_replace('http:','https:', $CFG->wwwroot);
                redirect("$wwwsroot/enrol/index.php?id=$course->id");
                exit;
            }
        }

        $strcourses = get_string('courses');
        $strloginto = get_string('loginto', '', $course->shortname);

        $PAGE->navbar->add($strcourses, new moodle_url('/course/'));
        $PAGE->navbar->add($strloginto);
        $PAGE->set_title($strloginto);
        $PAGE->set_heading($course->fullname);
        echo $OUTPUT->header();
        print_course($course, '80%');

        if ($course->password) {
            echo $OUTPUT->heading(get_string('choosemethod', 'enrol_authorize'));
        }

        if ($USER->username == 'guest') { // only real guest user, not for users with guest role
            $curcost = get_course_cost($course);
            echo '<div class="mdl-align">';
            echo '<p>'.get_string('paymentrequired').'</p>';
            echo '<p><b>'.get_string('cost').": $curcost[currency] $curcost[cost]".'</b></p>';
            echo '<p><a href="'.$CFG->httpswwwroot.'/login/">'.get_string('loginsite').'</a></p>';
            echo '</div>';
        }
        else {
            require_once($CFG->dirroot.'/enrol/authorize/enrol_form.php');
            $frmenrol = new enrol_authorize_form('enrol.php', compact('course'));
            if ($frmenrol->get_data()) {
                $authorizeerror = '';
                switch ($form->paymentmethod) {
                    case AN_METHOD_CC:
                        $authorizeerror = $this->cc_submit($form, $course);
                        break;

                    case AN_METHOD_ECHECK:
                        $authorizeerror = $this->echeck_submit($form, $course);
                        break;
                }
                if (!empty($authorizeerror)) {
                    print_error('authorizeerror', 'enrol_authorize', '', $authorizeerror);
                }
            }

            echo $OUTPUT->box_start();
            $frmenrol->display();
            echo $OUTPUT->box_end();
        }

        if ($course->password) {
            $password = '';
            include($CFG->dirroot.'/enrol/manual/enrol.html');
        }

        echo $OUTPUT->footer();
    }


    function print_enrolmentkeyfrom($course)
    {
        $manual = enrolment_factory::factory('manual');
        $manual->print_enrolmentkeyfrom($course);
    }


    /**
     * Validates registration forms and enrols student to course.
     *
     * @param object $form Form parameters
     * @param object $course Course info
     * @access public
     */
    public function check_entry($form, $course)
    {
        global $CFG;

        if (zero_cost($course) || (!empty($course->password) && !empty($form->enrol) && $form->enrol == 'manual')) {
            $manual = enrolment_factory::factory('manual');
            $manual->check_entry($form, $course);
            if (!empty($manual->errormsg)) {
                $this->errormsg = $manual->errormsg;
            }
        }
    }



    /**
     * The user submitted credit card form.
     *
     * @param object $form Form parameters
     * @param object $course Course info
     * @return string NULL if ok, error message otherwise.
     * @access private
     */
    private function cc_submit($form, $course)
    {
        global $CFG, $USER, $SESSION, $OUTPUT, $DB;

        prevent_double_paid($course);

        $useripno = getremoteaddr();
        $curcost = get_course_cost($course);
        $exp_date = sprintf("%02d", $form->ccexpiremm) . $form->ccexpireyyyy;

        // NEW CC ORDER
        $timenow = time();
        $order = new stdClass();
        $order->paymentmethod = AN_METHOD_CC;
        $order->refundinfo = substr($form->cc, -4);
        $order->ccname = $form->firstname . " " . $form->lastname;
        $order->courseid = $course->id;
        $order->userid = $USER->id;
        $order->status = AN_STATUS_NONE; // it will be changed...
        $order->settletime = 0; // cron changes this.
        $order->transid = 0; // Transaction Id
        $order->timecreated = $timenow;
        $order->amount = $curcost['cost'];
        $order->currency = $curcost['currency'];
        $order->id = $DB->insert_record("enrol_authorize", $order);
        if (!$order->id) {
            message_to_admin("Error while trying to insert new data", $order);
            return "Insert record error. Admin has been notified!";
        }

        $extra = new stdClass();
        $extra->x_card_num = $form->cc;
        $extra->x_card_code = $form->cvv;
        $extra->x_exp_date = $exp_date;
        $extra->x_currency_code = $curcost['currency'];
        $extra->x_amount = $curcost['cost'];
        $extra->x_first_name = $form->firstname;
        $extra->x_last_name = $form->lastname;
        $extra->x_country = $form->cccountry;
        $extra->x_address = $form->ccaddress;
        $extra->x_state = $form->ccstate;
        $extra->x_city = $form->cccity;
        $extra->x_zip = $form->cczip;

        $extra->x_invoice_num = $order->id;
        $extra->x_description = $course->shortname;

        $extra->x_cust_id = $USER->id;
        $extra->x_email = $USER->email;
        $extra->x_customer_ip = $useripno;
        $extra->x_email_customer = empty($CFG->enrol_mailstudents) ? 'FALSE' : 'TRUE';
        $extra->x_phone = '';
        $extra->x_fax = '';

        if (!empty($CFG->an_authcode) && !empty($form->ccauthcode)) {
            $action = AN_ACTION_CAPTURE_ONLY;
            $extra->x_auth_code = $form->ccauthcode;
        }
        elseif (!empty($CFG->an_review)) {
            $action = AN_ACTION_AUTH_ONLY;
        }
        else {
            $action = AN_ACTION_AUTH_CAPTURE;
        }

        $message = '';
        if (AN_APPROVED == AuthorizeNet::process($order, $message, $extra, $action, $form->cctype))
        {
            $SESSION->ccpaid = 1; // security check: don't duplicate payment

            switch ($action)
            {
                // review enabled (authorize but capture: draw money but wait for settlement during 30 days)
                // the first step is to inform payment managers and to redirect the user to main page.
                // the next step is to accept/deny payment (AN_ACTION_PRIOR_AUTH_CAPTURE/VOID) within 30 days (payment management or scheduled-capture CRON)
                // unless you accept payment or enable auto-capture cron, the transaction is expired after 30 days and the user cannot enrol to the course during 30 days.
                // see also: admin/cron.php, $this->cron(), $CFG->an_capture_day...
                case AN_ACTION_AUTH_ONLY:
                {
                    $a = new stdClass;
                    $a->url = "$CFG->wwwroot/enrol/authorize/index.php?order=$order->id";
                    $a->orderid = $order->id;
                    $a->transid = $order->transid;
                    $a->amount = "$order->currency $order->amount";
                    $a->expireon = userdate(AuthorizeNet::getsettletime($timenow + (30 * 3600 * 24)));
                    $a->captureon = userdate(AuthorizeNet::getsettletime($timenow + (intval($CFG->an_capture_day) * 3600 * 24)));
                    $a->course = $course->fullname;
                    $a->user = fullname($USER);
                    $a->acstatus = ($CFG->an_capture_day > 0) ? get_string('yes') : get_string('no');
                    $emailmessage = get_string('adminneworder', 'enrol_authorize', $a);
                    $a = new stdClass;
                    $a->course = $course->shortname;
                    $a->orderid = $order->id;
                    $emailsubject = get_string('adminnewordersubject', 'enrol_authorize', $a);
                    $context = get_context_instance(CONTEXT_COURSE, $course->id);
                    if (($paymentmanagers = get_users_by_capability($context, 'enrol/authorize:managepayments'))) {
                        foreach ($paymentmanagers as $paymentmanager) {
                            $eventdata = new object();
                            $eventdata->modulename        = 'moodle';
                            $eventdata->userfrom          = $USER;
                            $eventdata->userto            = $paymentmanager;
                            $eventdata->subject           = $emailsubject;
                            $eventdata->fullmessage       = $emailmessage;
                            $eventdata->fullmessageformat = FORMAT_PLAIN;
                            $eventdata->fullmessagehtml   = '';
                            $eventdata->smallmessage      = '';
                            message_send($eventdata);
                        }
                    }
                    redirect($CFG->wwwroot, get_string("reviewnotify", "enrol_authorize"), '30');
                    break;
                }

                case AN_ACTION_CAPTURE_ONLY: // auth code received via phone and the code accepted.
                case AN_ACTION_AUTH_CAPTURE: // real time transaction, authorize and capture.
                {
                    // Credit card captured, ENROL student now...
                    if (enrol_into_course($course, $USER, 'authorize'))
                    {
                        if (!empty($CFG->enrol_mailstudents)) {
                            send_welcome_messages($order->id);
                        }
                        if (!empty($CFG->enrol_mailteachers)) {
                            $context = get_context_instance(CONTEXT_COURSE, $course->id);
                            $paymentmanagers = get_users_by_capability($context, 'enrol/authorize:managepayments', '', '', '0', '1');
                            $paymentmanager = array_shift($paymentmanagers);
                            $a = new stdClass;
                            $a->course = "$course->fullname";
                            $a->user = fullname($USER);

                            $eventdata = new object();
                            $eventdata->modulename        = 'moodle';
                            $eventdata->userfrom          = $USER;
                            $eventdata->userto            = $paymentmanager;
                            $eventdata->subject           = get_string("enrolmentnew", '', format_string($course->shortname));
                            $eventdata->fullmessage       = get_string('enrolmentnewuser', '', $a);
                            $eventdata->fullmessageformat = FORMAT_PLAIN;
                            $eventdata->fullmessagehtml   = '';
                            $eventdata->smallmessage      = '';
                            message_send($eventdata);
                        }
                        if (!empty($CFG->enrol_mailadmins)) {
                            $a = new stdClass;
                            $a->course = "$course->fullname";
                            $a->user = fullname($USER);
                            $admins = get_admins();
                            foreach ($admins as $admin) {
                                $eventdata = new object();
                                $eventdata->modulename  = 'moodle';
                                $eventdata->userfrom    = $USER;
                                $eventdata->userto      = $admin;
                                $eventdata->subject     = get_string("enrolmentnew", '', format_string($course->shortname));
                                $eventdata->fullmessage = get_string('enrolmentnewuser', '', $a);
                                $eventdata->fullmessageformat = FORMAT_PLAIN;
                                $eventdata->fullmessagehtml   = '';
                                $eventdata->smallmessage      = '';
                                message_send($eventdata);
                            }
                        }
                    }
                    else
                    {
                        message_to_admin("Error while trying to enrol " . fullname($USER) . " in '$course->fullname'", $order);
                    }

                    load_all_capabilities();

                    echo $OUTPUT->box_start('generalbox notice');
                    echo '<p>'. get_string('paymentthanks', 'moodle', $course->fullname) .'</p>';
                    echo $OUTPUT->container_start('buttons');
                    echo $OUTPUT->single_button(new moodle_url("$CFG->wwwroot/enrol/authorize/index.php", array('order'=>$order->id)), get_string('payments'));
                    echo $OUTPUT->single_button(new moodle_url("$CFG->wwwroot/course/view.php", array('id'=>$course->id)), $course->fullname);
                    echo $OUTPUT->container_end();
                    echo $OUTPUT->box_end();
                    echo $OUTPUT->footer();
                    exit; // break;
                }
            }
            return NULL;
        }
        else
        {
            message_to_admin($message, $order);
            return $message;
        }
    }


    /**
     * The user submitted echeck form.
     *
     * @param object $form Form parameters
     * @param object $course Course info
     * @return string NULL if ok, error message otherwise.
     * @access private
     */
    private function echeck_submit($form, $course)
    {
        global $CFG, $USER, $SESSION, $DB;

        prevent_double_paid($course);

        $useripno = getremoteaddr();
        $curcost = get_course_cost($course);
        $isbusinesschecking = ($form->acctype == 'BUSINESSCHECKING');

        // NEW ECHECK ORDER
        $timenow = time();
        $order = new stdClass();
        $order->paymentmethod = AN_METHOD_ECHECK;
        $order->refundinfo = $isbusinesschecking ? 1 : 0;
        $order->ccname = $form->firstname . ' ' . $form->lastname;
        $order->courseid = $course->id;
        $order->userid = $USER->id;
        $order->status = AN_STATUS_NONE; // it will be changed...
        $order->settletime = 0; // cron changes this.
        $order->transid = 0; // Transaction Id
        $order->timecreated = $timenow;
        $order->amount = $curcost['cost'];
        $order->currency = $curcost['currency'];
        $order->id = $DB->insert_record("enrol_authorize", $order);
        if (!$order->id) {
            message_to_admin("Error while trying to insert new data", $order);
            return "Insert record error. Admin has been notified!";
        }

        $extra = new stdClass();
        $extra->x_bank_aba_code = $form->abacode;
        $extra->x_bank_acct_num = $form->accnum;
        $extra->x_bank_acct_type = $form->acctype;
        $extra->x_echeck_type = $isbusinesschecking ? 'CCD' : 'WEB';
        $extra->x_bank_name = $form->bankname;
        $extra->x_currency_code = $curcost['currency'];
        $extra->x_amount = $curcost['cost'];
        $extra->x_first_name = $form->firstname;
        $extra->x_last_name = $form->lastname;
        $extra->x_country = $USER->country;
        $extra->x_address = $USER->address;
        $extra->x_city = $USER->city;
        $extra->x_state = '';
        $extra->x_zip = '';

        $extra->x_invoice_num = $order->id;
        $extra->x_description = $course->shortname;

        $extra->x_cust_id = $USER->id;
        $extra->x_email = $USER->email;
        $extra->x_customer_ip = $useripno;
        $extra->x_email_customer = empty($CFG->enrol_mailstudents) ? 'FALSE' : 'TRUE';
        $extra->x_phone = '';
        $extra->x_fax = '';

        $message = '';
        if (AN_REVIEW == AuthorizeNet::process($order, $message, $extra, AN_ACTION_AUTH_CAPTURE)) {
            $SESSION->ccpaid = 1; // security check: don't duplicate payment
            redirect($CFG->wwwroot, get_string("reviewnotify", "enrol_authorize"), '30');
            return NULL;
        }
        else {
            message_to_admin($message, $order);
            return $message;
        }
    }


    /**
     * Gets access icons.
     *
     * @param object $course
     * @return string
     * @access public
     */
    public function get_access_icons($course)
    {
        $manual = enrolment_factory::factory('manual');
        $str = $manual->get_access_icons($course);
        $curcost = get_course_cost($course);

        if (abs($curcost['cost']) > 0.00) {
            $strrequirespayment = get_string("requirespayment");
            $strcost = get_string("cost");
            $currency = $curcost['currency'];

            switch ($currency) {
                case 'USD': $currency = 'US$'; break;
                case 'CAD': $currency = 'C$'; break;
                case 'EUR': $currency = '&euro;'; break;
                case 'GBP': $currency = '&pound;'; break;
                case 'JPY': $currency = '&yen;'; break;
            }

            $str .= '<div class="cost" title="'.$strrequirespayment.'">'.$strcost.': ';
            $str .= $currency . ' ' . $curcost['cost'].'</div>';
        }

        return $str;
    }


    /**
     * Shows config form & errors
     *
     * @param object $frm
     * @access public
     */
    public function config_form($frm) {
        global $CFG, $DB, $OUTPUT;
        $mconfig = get_config('enrol/authorize');

        if (!check_curl_available()) {
            echo $OUTPUT->notification('PHP must be compiled with cURL+SSL support (--with-curl --with-openssl)');
        }

        if (empty($CFG->loginhttps) and substr($CFG->wwwroot, 0, 5) !== 'https') {
            $a = new stdClass;
            $a->url = "$CFG->wwwroot/$CFG->admin/settings.php?section=httpsecurity";
            echo $OUTPUT->notification(get_string('adminconfighttps', 'enrol_authorize', $a));
            return; // notice breaks the form and xhtml later
        }
        elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 443) { // MDL-9836
            $wwwsroot = qualified_me();
            $wwwsroot = str_replace('http:', 'https:', $wwwsroot);
            $a = new stdClass;
            $a->url = $wwwsroot;
            echo $OUTPUT->notification(get_string('adminconfighttpsgo', 'enrol_authorize', $a));
            return; // notice breaks the form and xhtml later
        }

        if (optional_param('verifyaccount', 0, PARAM_INT)) {
            echo $OUTPUT->notification(authorize_verify_account());
        }

        if (!empty($frm->an_review)) {
            $captureday = intval($frm->an_capture_day);
            $emailexpired = intval($frm->an_emailexpired);
            if ($captureday > 0 || $emailexpired > 0) {
                $lastcron = $DB->get_field_sql('SELECT max(lastcron) FROM {modules}');
                if ((time() - intval($lastcron) > 3600 * 24)) {
                    echo $OUTPUT->notification(get_string('admincronsetup', 'enrol_authorize'));
                }
            }
        }

        if (($count = $DB->count_records('enrol_authorize', array('status'=>AN_STATUS_AUTH)))) {
            $a = new stdClass;
            $a->count = $count;
            $a->url = $CFG->wwwroot."/enrol/authorize/index.php?status=".AN_STATUS_AUTH;
            echo $OUTPUT->notification(get_string('adminpendingorders', 'enrol_authorize', $a));
        }

        if (data_submitted()) {
            if (empty($mconfig->an_login)) {
                echo $OUTPUT->notification("an_login required");
            }
            if (empty($mconfig->an_tran_key) && empty($mconfig->an_password)) {
                echo $OUTPUT->notification("an_tran_key or an_password required");
            }
        }

        include($CFG->dirroot.'/enrol/authorize/config_form.php');
    }


    /**
     * process_config
     *
     * @param object $config
     * @return bool true if it will be saved.
     * @access public
     */
    public function process_config($config)
    {
        global $CFG, $DB;
        $mconfig = get_config('enrol/authorize');

        // site settings
        if (($cost = optional_param('enrol_cost', 5, PARAM_INT)) > 0) {
            set_config('enrol_cost', $cost);
        }
        set_config('enrol_currency', optional_param('enrol_currency', 'USD', PARAM_ALPHA));
        set_config('enrol_mailstudents', optional_param('enrol_mailstudents', 0, PARAM_BOOL));
        set_config('enrol_mailteachers', optional_param('enrol_mailteachers', 0, PARAM_BOOL));
        set_config('enrol_mailadmins', optional_param('enrol_mailadmins', 0, PARAM_BOOL));

        // optional authorize.net settings
        set_config('an_avs', optional_param('an_avs', 0, PARAM_BOOL));
        set_config('an_authcode', optional_param('an_authcode', 0, PARAM_BOOL));
        set_config('an_test', optional_param('an_test', 0, PARAM_BOOL));
        set_config('an_referer', optional_param('an_referer', 'http://', PARAM_URL));

        $acceptmethods = optional_param('acceptmethods', get_list_of_payment_methods(), PARAM_ALPHA);
        set_config('an_acceptmethods', implode(',', $acceptmethods));
        $acceptccs = optional_param('acceptccs', array_keys(get_list_of_creditcards()), PARAM_ALPHA);
        set_config('an_acceptccs', implode(',', $acceptccs));
        $acceptechecktypes = optional_param('acceptechecktypes', get_list_of_bank_account_types(), PARAM_ALPHA);
        set_config('an_acceptechecktypes', implode(',', $acceptechecktypes));

        $cutoff_hour = optional_param('an_cutoff_hour', 0, PARAM_INT);
        $cutoff_min = optional_param('an_cutoff_min', 5, PARAM_INT);
        set_config('an_cutoff', $cutoff_hour * 60 + $cutoff_min);

        // cron depencies
        $reviewval = optional_param('an_review', 0, PARAM_BOOL);
        $captureday = optional_param('an_capture_day', 5, PARAM_INT);
        $emailexpired = optional_param('an_emailexpired', 2, PARAM_INT);
        $emailexpiredteacher = optional_param('an_emailexpiredteacher', 0, PARAM_BOOL);
        $sorttype = optional_param('an_sorttype', 'ttl', PARAM_ALPHA);

        $captureday = ($captureday > 29) ? 29 : (($captureday < 0) ? 0 : $captureday);
        $emailexpired = ($emailexpired > 5) ? 5 : (($emailexpired < 0) ? 0 : $emailexpired);

        if (!empty($reviewval) && ($captureday > 0 || $emailexpired > 0)) {
            $lastcron = $DB->get_field_sql('SELECT max(lastcron) FROM {modules}');
            if (time() - intval($lastcron) > 3600 * 24) {
                return false;
            }
        }

        set_config('an_review', $reviewval);
        set_config('an_capture_day', $captureday);
        set_config('an_emailexpired', $emailexpired);
        set_config('an_emailexpiredteacher', $emailexpiredteacher);
        set_config('an_sorttype', $sorttype);

        // https and openssl library is required
        if ((substr($CFG->wwwroot, 0, 5) !== 'https' and empty($CFG->loginhttps)) or !check_curl_available()) {
            return false;
        }

        // REQUIRED fields;
        // an_login
        $loginval = optional_param('an_login', '', PARAM_RAW);
        if (empty($loginval) && empty($mconfig->an_login)) {
            return false;
        }
        $loginval = !empty($loginval) ? rc4encrypt($loginval) : strval($mconfig->an_login);
        set_config('an_login', $loginval, 'enrol/authorize');

        // an_tran_key, an_password
        $tranval = optional_param('an_tran_key', '', PARAM_RAW);
        $tranval = !empty($tranval) ? rc4encrypt($tranval) : (isset($mconfig->an_tran_key)?$mconfig->an_tran_key:'');
        $passwordval = optional_param('an_password', '', PARAM_RAW);
        $passwordval = !empty($passwordval) ? rc4encrypt($passwordval) :(isset($mconfig->an_password)?$mconfig->an_password:'');
        $deletecurrent = optional_param('delete_current', '0', PARAM_BOOL);
        if (!empty($deletecurrent) and !empty($tranval)) {
            unset_config('an_password', 'enrol/authorize');
            $passwordval = '';
        }
        elseif (!empty($passwordval)) {
            set_config('an_password', $passwordval, 'enrol/authorize');
        }
        if (empty($tranval) and empty($passwordval)) {
            return false;
        }
        if (!empty($tranval)) {
            set_config('an_tran_key', $tranval, 'enrol/authorize');
        }

        return true;
    }

    /**
     * This function is run by admin/cron.php every time if admin has enabled this plugin.
     *
     * Everyday at settlement time (default is 00:05), it cleans up some tables
     * and sends email to admin/teachers about pending orders expiring if manual-capture has enabled.
     *
     * If admin set up 'Order review' and 'Capture day', it captures credits cards and enrols students.
     *
     * @access public
     */
    public function cron()
    {
        global $CFG, $DB;

        $oneday = 86400;
        $timenow = time();
        $settlementtime = AuthorizeNet::getsettletime($timenow);
        $timediff30 = $settlementtime - (30 * $oneday);
        $mconfig = get_config('enrol/authorize');

        mtrace("Processing authorize cron...");

        if (intval($mconfig->an_dailysettlement) < $settlementtime) {
            set_config('an_dailysettlement', $settlementtime, 'enrol/authorize');
            mtrace("    Daily cron:");
            $this->cron_daily();
            mtrace("    Done");
        }

        mtrace("    Scheduled capture", ": ");
        if (empty($CFG->an_review) or (!empty($CFG->an_test)) or (intval($CFG->an_capture_day) < 1) or (!check_curl_available())) {
            mtrace("disabled");
            return; // order review disabled or test mode or manual capture or openssl wasn't loaded.
        }

        $timediffcnf = $settlementtime - (intval($CFG->an_capture_day) * $oneday);
        $select = "(status = ?) AND (timecreated < ?) AND (timecreated > ?)";
        $params = array(AN_STATUS_AUTH, $timediffcnf, $timediff30);
        if (!($ordercount = $DB->count_records_select('enrol_authorize', $select, $params))) {
            mtrace("no pending orders");
            return;
        }

        $eachconn = intval($mconfig->an_eachconnsecs);
        $eachconn = (($eachconn > 60) ? 60 : (($eachconn <= 0) ? 3 : $eachconn));
        if (($ordercount * $eachconn) + intval($mconfig->an_lastcron) > $timenow) {
            mtrace("blocked");
            return;
        }
        set_config('an_lastcron', $timenow, 'enrol/authorize');

        mtrace("    $ordercount orders are being processed now", ": ");

        $faults = '';
        $sendem = array();
        $elapsed = time();
        @set_time_limit(0);
        $this->log = "AUTHORIZE.NET AUTOCAPTURE CRON: " . userdate($timenow) . "\n";

        $lastcourseid = 0;
        $rs = $DB->get_recordset_select('enrol_authorize', $select, $params, 'courseid');
        foreach ( $rs as $order)
        {
            $message = '';
            $extra = NULL;
            if (AN_APPROVED == AuthorizeNet::process($order, $message, $extra, AN_ACTION_PRIOR_AUTH_CAPTURE)) {
                if ($lastcourseid != $order->courseid) {
                    $lastcourseid = $order->courseid;
                    $course = $DB->get_record('course', array('id'=>$lastcourseid));
                    $role = get_default_course_role($course);
                    $context = get_context_instance(CONTEXT_COURSE, $lastcourseid);
                }
                $timestart = $timeend = 0;
                if ($course->enrolperiod) {
                    $timestart = $timenow;
                    $timeend = $order->settletime + $course->enrolperiod;
                }
                $user = $DB->get_record('user', array('id'=>$order->userid));
                // TODO: do some real enrolment here
                if (role_assign($role->id, $user->id, $context->id, 'enrol_authorize')) {
                    $this->log .= "User($user->id) has been enrolled to course($course->id).\n";
                    if (!empty($CFG->enrol_mailstudents)) {
                        $sendem[] = $order->id;
                    }
                }
                else {
                    $faults .= "Error while trying to enrol ".fullname($user)." in '$course->fullname' \n";
                    foreach ($order as $okey => $ovalue) {
                        $faults .= "   $okey = $ovalue\n";
                    }
                }
            }
            else {
                $this->log .= "Error, Order# $order->id: " . $message . "\n";
            }
        }
        $rs->close();
        mtrace("processed");

        $timenow = time();
        $elapsed = $timenow - $elapsed;
        $eachconn = ceil($elapsed / $ordercount);
        set_config('an_eachconnsecs', $eachconn, 'enrol/authorize');

        $this->log .= "AUTHORIZE.NET CRON FINISHED: " . userdate($timenow);

        $adminuser = get_admin();
        if (!empty($faults)) {
            $eventdata = new object();
            $eventdata->modulename        = 'moodle';
            $eventdata->userfrom          = $adminuser;
            $eventdata->userto            = $adminuser;
            $eventdata->subject           = "AUTHORIZE.NET CRON FAULTS";
            $eventdata->fullmessage       = $faults;
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = '';
            $eventdata->smallmessage      = '';
            message_send($eventdata);
        }
        if (!empty($CFG->enrol_mailadmins)) {
            $eventdata = new object();
            $eventdata->modulename        = 'moodle';
            $eventdata->userfrom          = $adminuser;
            $eventdata->userto            = $adminuser;
            $eventdata->subject           = "AUTHORIZE.NET CRON LOG";
            $eventdata->fullmessage       = $this->log;
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = '';
            $eventdata->smallmessage      = '';
            message_send($eventdata);
        }

        // Send emails to students about which courses have enrolled.
        if (!empty($sendem)) {
            mtrace("    sending welcome messages to students", ": ");
            send_welcome_messages($sendem);
            mtrace("sent");
        }
    }

    /**
     * Daily cron. It executes at settlement time (default is 00:05).
     *
     * @access private
     */
    private function cron_daily()
    {
        global $CFG, $SITE, $DB;

        $oneday = 86400;
        $timenow = time();
        $onepass = $timenow - $oneday;
        $settlementtime = AuthorizeNet::getsettletime($timenow);
        $timediff30 = $settlementtime - (30 * $oneday);

        $select = "(status=?) AND (timecreated<?)";
        $params = array(AN_STATUS_NONE, $timediff30);
        if ($DB->delete_records_select('enrol_authorize', $select, $params)) {
            mtrace("        orders no transaction made have deleted");
        }

        $select = "(status=?) AND (timecreated<?)";
        $params = array(AN_STATUS_EXPIRE, AN_STATUS_AUTH, $timediff30);
        if ($DB->execute("UPDATE {enrol_authorize} SET status=? WHERE $select", $params)) {
            mtrace("        pending orders to expire have updated");
        }

        $timediff60 = $settlementtime - (60 * $oneday);
        $select = "(status=?) AND (timecreated<?)";
        $params = array(AN_STATUS_EXPIRE, $timediff60);
        if ($DB->delete_records_select('enrol_authorize', $select, $params)) {
            mtrace("        orders expired older than 60 days have deleted");
        }

        $adminuser = get_admin();
        $select = "status IN(?,?) AND (timecreated<?) AND (timecreated>?)";
        $params = array(AN_STATUS_UNDERREVIEW, AN_STATUS_APPROVEDREVIEW, $onepass, $timediff60);
        if (($count = $DB->count_records_select('enrol_authorize', $select, $params)) &&
            ($csvusers = get_users_by_capability(get_context_instance(CONTEXT_SYSTEM), 'enrol/authorize:uploadcsv'))) {
            $a = new stdClass;
            $a->count = $count;
            $a->course = $SITE->shortname;
            $subject = get_string('pendingechecksubject', 'enrol_authorize', $a);
            $a = new stdClass;
            $a->count = $count;
            $a->url = $CFG->wwwroot.'/enrol/authorize/uploadcsv.php';
            $message = get_string('pendingecheckemail', 'enrol_authorize', $a);
            foreach($csvusers as $csvuser) {
                $eventdata = new object();
                $eventdata->modulename        = 'moodle';
                $eventdata->userfrom          = $adminuser;
                $eventdata->userto            = $csvuser;
                $eventdata->subject           = $subject;
                $eventdata->fullmessage       = $message;
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml   = '';
                $eventdata->smallmessage      = '';
                message_send($eventdata);
            }
            mtrace("        users who have 'enrol/authorize:uploadcsv' were mailed");
        }

        mtrace("        early pending order warning email for manual capture", ": ");
        if (empty($CFG->an_emailexpired)) {
            mtrace("not enabled");
            return;
        }


        $timediffem = $settlementtime - ((30 - intval($CFG->an_emailexpired)) * $oneday);
        $select = "(status=?) AND (timecreated<?) AND (timecreated>?)";
        $params = array(AN_STATUS_AUTH, $timediffem, $timediff30);
        $count = $DB->count_records_select('enrol_authorize', $select, $params);
        if (!$count) {
            mtrace("no orders prior to $CFG->an_emailexpired days");
            return;
        }

        mtrace("$count orders prior to $CFG->an_emailexpired days");
        $a = new stdClass;
        $a->pending = $count;
        $a->days = $CFG->an_emailexpired;
        $a->course = $SITE->shortname;
        $subject = get_string('pendingorderssubject', 'enrol_authorize', $a);
        $a = new stdClass;
        $a->pending = $count;
        $a->days = $CFG->an_emailexpired;
        $a->course = $SITE->fullname;
        $a->enrolurl = "$CFG->wwwroot/$CFG->admin/enrol_config.php?enrol=authorize";
        $a->url = $CFG->wwwroot.'/enrol/authorize/index.php?status='.AN_STATUS_AUTH;
        $message = get_string('pendingordersemail', 'enrol_authorize', $a);

        $eventdata = new object();
        $eventdata->modulename        = 'moodle';
        $eventdata->userfrom          = $adminuser;
        $eventdata->userto            = $adminuser;
        $eventdata->subject           = $subject;
        $eventdata->fullmessage       = $message;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        message_send($eventdata);

        // Email to payment managers
        if (empty($CFG->an_emailexpiredteacher)) {
            return; // email feature disabled for teachers.
        }

        $sorttype = empty($CFG->an_sorttype) ? 'ttl' : $CFG->an_sorttype;
        $sql = "SELECT e.courseid, e.currency, c.fullname, c.shortname,
                  COUNT(e.courseid) AS cnt, SUM(e.amount) as ttl
                FROM {enrol_authorize} e
                  INNER JOIN {course} c ON c.id = e.courseid
                WHERE (e.status = ?)
                  AND (e.timecreated < ?)
                  AND (e.timecreated > ?)
                GROUP BY e.courseid
                ORDER BY $sorttype DESC";
        $params = array(AN_STATUS_AUTH, $timediffem, $timediff30);

        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $courseinfo)
        {
            $lastcourse = $courseinfo->courseid;
            $context = get_context_instance(CONTEXT_COURSE, $lastcourse);
            if (($paymentmanagers = get_users_by_capability($context, 'enrol/authorize:managepayments'))) {
                $a = new stdClass;
                $a->course = $courseinfo->shortname;
                $a->pending = $courseinfo->cnt;
                $a->days = $CFG->an_emailexpired;
                $subject = get_string('pendingorderssubject', 'enrol_authorize', $a);
                $a = new stdClass;
                $a->course = $courseinfo->fullname;
                $a->pending = $courseinfo->cnt;
                $a->currency = $courseinfo->currency;
                $a->sumcost = $courseinfo->ttl;
                $a->days = $CFG->an_emailexpired;
                $a->url = $CFG->wwwroot.'/enrol/authorize/index.php?course='.$lastcourse.'&amp;status='.AN_STATUS_AUTH;
                $message = get_string('pendingordersemailteacher', 'enrol_authorize', $a);
                foreach ($paymentmanagers as $paymentmanager) {
                    $eventdata = new object();
                    $eventdata->modulename        = 'moodle';
                    $eventdata->userfrom          = $adminuser;
                    $eventdata->userto            = $paymentmanager;
                    $eventdata->subject           = $subject;
                    $eventdata->fullmessage       = $message;
                    $eventdata->fullmessageformat = FORMAT_PLAIN;
                    $eventdata->fullmessagehtml   = '';
                    $eventdata->smallmessage      = '';
                    message_send($eventdata);
                }
            }
        }
        $rs->close();
    }
}

