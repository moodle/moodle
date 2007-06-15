<?php // $Id$

require_once($CFG->dirroot.'/enrol/enrol.class.php');
require_once($CFG->dirroot.'/enrol/authorize/const.php');

/**
 * enrolment_plugin_authorize
 *
 */
class enrolment_plugin_authorize
{
    /**
     * Credit card error messages.
     *
     * @var array
     * @access public
     */
    var $ccerrors = array();

    /**
     * Cron log.
     *
     * @var string
     * @access public
     */
    var $log;


    /**
     * Returns information about the courses a student has access to
     *
     * Set the $user->student course array
     * Set the $user->timeaccess course array
     *
     * @param object &$user must contain $user->id already set
     */
    function get_student_courses(&$user) {
        $manual = enrolment_factory::factory('manual');
        $manual->get_student_courses($user);
    }


    /**
     * Returns information about the courses a teacher has access to
     *
     * Set the $user->teacher course array
     * Set the $user->teacheredit course array
     * Set the $user->timeaccess course array
     *
     * @param object &$user must contain $user->id already set
     */
    function get_teacher_courses(&$user) {
        $manual = enrolment_factory::factory('manual');
        $manual->get_teacher_courses($user);
    }


    /**
     * Shows a credit card form for registration.
     *
     * @param object $course Course info
     * @access public
     */
    function print_entry($course) {
        global $CFG, $USER, $form;

        $zerocost = enrolment_plugin_authorize::zero_cost($course);
        if ($zerocost) {
            $manual = enrolment_factory::factory('manual');
            if (!empty($this->errormsg)) {
                $manual->errormsg = $this->errormsg;
            }
            $manual->print_entry($course);
            return;
        }

        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 443) { // MDL-9836
            if (empty($CFG->loginhttps)) {
                error(get_string('httpsrequired', 'enrol_authorize'));
            } else {
                $wwwsroot = str_replace('http:','https:', $CFG->wwwroot);
                redirect("$wwwsroot/course/enrol.php?id=$course->id");
                exit;
            }
        }

        httpsrequired();

        $strcourses = get_string('courses');
        $strloginto = get_string('loginto', '', $course->shortname);

        print_header($strloginto, $course->fullname, "<a href=\"$CFG->wwwroot/course/\">$strcourses</a> -> $strloginto");
        print_course($course, '80%');

        if ($course->password) {
            print_heading(get_string('choosemethod', 'enrol_authorize'), 'center');
        }

        print_simple_box_start('center');
        if (isguest()) {
            $curcost = enrolment_plugin_authorize::get_course_cost($course);
            echo '<div align="center">';
            echo '<p>'.get_string('paymentrequired').'</p>';
            echo '<p><b>'.get_string('cost').": $curcost[currency] $curcost[cost]".'</b></p>';
            echo '<p><a href="'.$CFG->httpswwwroot.'/login/">'.get_string('loginsite').'</a></p>';
            echo '</div>';
        } else {
            include($CFG->dirroot.'/enrol/authorize/enrol.html');
        }
        print_simple_box_end();

        if ($course->password) {
            $password = '';
            $teacher = get_teacher($course->id);
            include($CFG->dirroot.'/enrol/manual/enrol.html');
        }

        print_footer();
    }


    /**
     * Checks form params.
     *
     * @param object $form Form parameters
     * @param object $course Course info
     * @access public
     */
    function check_entry($form, $course) {
        if (enrolment_plugin_authorize::zero_cost($course) or
           (!empty($course->password) and !empty($form->password))) {
            $manual = enrolment_factory::factory('manual');
            $manual->check_entry($form, $course);
            if (!empty($manual->errormsg)) {
                $this->errormsg = $manual->errormsg;
            }
        } elseif ((!empty($form->ccsubmit)) and $this->validate_enrol_form($form)) {
            $this->cc_submit($form, $course);
        }
    }


    /**
     * Credit card number mode.
     * Send to authorize.net.
     *
     * @param object $form Form parameters
     * @param object $course Course info
     * @access private
     */
    function cc_submit($form, $course)
    {
        global $CFG, $USER, $SESSION;
        require_once('authorizenetlib.php');

        enrolment_plugin_authorize::prevent_double_paid($course);

        $useripno = getremoteaddr();
        $curcost = enrolment_plugin_authorize::get_course_cost($course);
        $exp_date = sprintf("%02d", $form->ccexpiremm) . $form->ccexpireyyyy;

        // NEW ORDER
        $timenow = time();
        $order = new stdClass();
        $order->cclastfour = substr($form->cc, -4);
        $order->ccname = $form->firstname . " " . $form->lastname;
        $order->courseid = $course->id;
        $order->userid = $USER->id;
        $order->status = AN_STATUS_NONE; // it will be changed...
        $order->settletime = 0; // cron changes this.
        $order->transid = 0; // Transaction Id
        $order->timecreated = $timenow;
        $order->amount = $curcost['cost'];
        $order->currency = $curcost['currency'];
        $order->id = insert_record("enrol_authorize", $order);
        if (!$order->id) {
            enrolment_plugin_authorize::email_to_admin("Error while trying to insert new data", $order);
            $this->ccerrors['header'] = "Insert record error. Admin has been notified!";
            return;
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

        $message = '';
        $an_review = !empty($CFG->an_review);
        $action = $an_review ? AN_ACTION_AUTH_ONLY : AN_ACTION_AUTH_CAPTURE;
        $success = authorize_action($order, $message, $extra, $action, $form->cctype);
        if (!$success) {
            enrolment_plugin_authorize::email_to_admin($message, $order);
            $this->ccerrors['header'] = $message;
            return;
        }

        $SESSION->ccpaid = 1; // security check: don't duplicate payment
        if ($order->transid == 0) { // TEST MODE
            if ($an_review) {
                redirect($CFG->wwwroot, get_string("reviewnotify", "enrol_authorize"), '30');
            }
            else {
                $timestart = $timenow;
                $timeend = $timestart + (3600 * 24); // just enrol for 1 days :)
                enrol_student($USER->id, $course->id, $timestart, $timeend, 'manual');
                redirect("$CFG->wwwroot/course/view.php?id=$course->id");
            }
            return;
        }

        if ($an_review) { // review enabled, inform admin and redirect user to main page.
            $a = new stdClass;
            $a->url = "$CFG->wwwroot/enrol/authorize/index.php?order=$order->id";
            $a->orderid = $order->id;
            $a->transid = $order->transid;
            $a->amount = "$order->currency $order->amount";
            $a->expireon = userdate(authorize_getsettletime($timenow + (30 * 3600 * 24)));
            $a->captureon = userdate(authorize_getsettletime($timenow + (intval($CFG->an_capture_day) * 3600 * 24)));
            $a->course = $course->fullname;
            $a->user = fullname($USER);
            $a->acstatus = ($CFG->an_capture_day > 0) ? get_string('yes') : get_string('no');
            $emailmessage = get_string('adminneworder', 'enrol_authorize', $a);
            $a = new stdClass;
            $a->course = $course->shortname;
            $a->orderid = $order->id;
            $emailsubject = get_string('adminnewordersubject', 'enrol_authorize', $a);
            $admins = get_admins();
            foreach ($admins as $admin) {
                email_to_user($admin, $USER, $emailsubject, $emailmessage);
            }
            redirect($CFG->wwwroot, get_string("reviewnotify", "enrol_authorize"), '30');
            return;
        }

        // Credit card captured, ENROL student now...
        if ($course->enrolperiod) {
            $timestart = $timenow;
            $timeend = $timestart + $course->enrolperiod;
        } else {
            $timestart = $timeend = 0;
        }

        if (enrol_student($USER->id, $course->id, $timestart, $timeend, 'manual')) {
            $teacher = get_teacher($course->id);
            if (!empty($CFG->enrol_mailstudents)) {
                $a = new stdClass;
                $a->coursename = "$course->fullname";
                $a->profileurl = "$CFG->wwwroot/user/view.php?id=$USER->id";
                email_to_user($USER,
                              $teacher,
                              get_string("enrolmentnew", '', $course->shortname),
                              get_string('welcometocoursetext', '', $a));
            }
            if (!empty($CFG->enrol_mailteachers)) {
                $a = new stdClass;
                $a->course = "$course->fullname";
                $a->user = fullname($USER);
                email_to_user($teacher,
                              $USER,
                              get_string("enrolmentnew", '', $course->shortname),
                              get_string('enrolmentnewuser', '', $a));
            }
            if (!empty($CFG->enrol_mailadmins)) {
                $a = new stdClass;
                $a->course = "$course->fullname";
                $a->user = fullname($USER);
                $admins = get_admins();
                foreach ($admins as $admin) {
                    email_to_user($admin,
                                  $USER,
                                  get_string("enrolmentnew", '', $course->shortname),
                                  get_string('enrolmentnewuser', '', $a));
                }
            }
        } else {
            enrolment_plugin_authorize::email_to_admin("Error while trying to enrol " .
            fullname($USER) . " in '$course->fullname'", $order);
        }

        if ($SESSION->wantsurl) {
            $destination = $SESSION->wantsurl; unset($SESSION->wantsurl);
        } else {
            $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
        }
        redirect($destination);
    }

    /**
     * validate_enrol_form
     *
     * @param object $form Form parameters
     * @access private
     */
    function validate_enrol_form($form)
    {
        global $CFG;
        require_once('ccval.php');

        if (empty($form->cc)) {
            $this->ccerrors['cc'] = get_string('missingcc', 'enrol_authorize');
        }
        if (empty($form->ccexpiremm) || empty($form->ccexpireyyyy)) {
            $this->ccerrors['ccexpire'] = get_string('missingccexpire', 'enrol_authorize');
        }
        else {
            $expdate = sprintf("%02d", intval($form->ccexpiremm)) . $form->ccexpireyyyy;
            $validcc = CCVal($form->cc, $form->cctype, $expdate);
            if (!$validcc) {
                if ($validcc === 0) {
                    $this->ccerrors['ccexpire'] = get_string('ccexpired', 'enrol_authorize');
                }
                else {
                    $this->ccerrors['cc'] = get_string('ccinvalid', 'enrol_authorize');
                }
            }
        }

        if (empty($form->firstname) || empty($form->lastname)) {
            $this->ccerrors['ccfirstlast'] = get_string('missingfullname');
        }

        if (empty($form->cvv) || !is_numeric($form->cvv)) {
            $this->ccerrors['cvv'] = get_string('missingcvv', 'enrol_authorize');
        }

        if (empty($form->cctype) or
            !in_array($form->cctype, array_keys(enrolment_plugin_authorize::get_list_of_creditcards()))) {
            $this->ccerrors['cctype'] = get_string('missingcctype', 'enrol_authorize');
        }

        if (!empty($CFG->an_avs)) {
            if (empty($form->ccaddress)) {
                $this->ccerrors['ccaddress'] = get_string('missingaddress', 'enrol_authorize');
            }
            if (empty($form->cccity)) {
                $this->ccerrors['cccity'] = get_string('missingcity');
            }
            if (empty($form->cccountry)) {
                $this->ccerrors['cccountry'] = get_string('missingcountry');
            }
        }
        if (empty($form->cczip) || !is_numeric($form->cczip)) {
            $this->ccerrors['cczip'] = get_string('missingzip', 'enrol_authorize');
        }

        if (!empty($this->ccerrors)) {
            $this->ccerrors['header'] = get_string('someerrorswerefound');
            return false;
        }

        return true;
    }


    /**
     * Gets access icons.
     *
     * @param object $course
     * @return string
     * @access public
     */
    function get_access_icons($course) {

        $manual = enrolment_factory::factory('manual');
        $str = $manual->get_access_icons($course);
        $curcost = enrolment_plugin_authorize::get_course_cost($course);

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
    function config_form($frm)
    {
        global $CFG;
        $mconfig = get_config('enrol/authorize');

        if (! enrolment_plugin_authorize::check_openssl_loaded()) {
            notify('PHP must be compiled with SSL support (--with-openssl)');
        }

        if (empty($CFG->loginhttps) and substr($CFG->wwwroot, 0, 5) !== 'https') {
            $a = new stdClass;
            $a->url = "$CFG->wwwroot/$CFG->admin/config.php#configsectionsecurity";
            notice(get_string('adminconfighttps', 'enrol_authorize', $a));
        }
        elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 443) { // MDL-9836
            $wwwsroot = qualified_me();
            $wwwsroot = str_replace('http:', 'https:', $wwwsroot);
            $a = new stdClass;
            $a->url = $wwwsroot;
            notice(get_string('adminconfighttpsgo', 'enrol_authorize', $a));
        }

        if (!empty($frm->an_review)) {
            $captureday = intval($frm->an_capture_day);
            $emailexpired = intval($frm->an_emailexpired);
            if ($captureday > 0 || $emailexpired > 0) {
                if ((time() - intval($mconfig->an_lastcron) > 3600 * 24)) {
                    notify(get_string('admincronsetup', 'enrol_authorize'));
                }
            }
        }

        if ($count = count_records('enrol_authorize', 'status', AN_STATUS_AUTH)) {
            $a = new stdClass;
            $a->count = $count;
            $a->url = $CFG->wwwroot."/enrol/authorize/index.php?status=".AN_STATUS_AUTH;
            notify(get_string('adminpendingorders', 'enrol_authorize', $a));
        }

        if (data_submitted()) {
            if (empty($mconfig->an_login)) {
                notify("an_login required");
            }
            if (empty($mconfig->an_tran_key) && empty($mconfig->an_password)) {
                notify("an_tran_key or an_password required");
            }
        }

        include($CFG->dirroot.'/enrol/authorize/config.html');
    }


    /**
     * process_config
     *
     * @param object $config
     * @return bool true if it will be saved.
     * @access public
     */
    function process_config($config)
    {
        global $CFG;
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
        set_config('an_test', optional_param('an_test', 0, PARAM_BOOL));
        set_config('an_teachermanagepay', optional_param('an_teachermanagepay', 0, PARAM_BOOL));
        set_config('an_referer', optional_param('an_referer', 'http://', PARAM_URL));

        $acceptccs = optional_param('acceptccs',
                                    array_keys(enrolment_plugin_authorize::get_list_of_creditcards()),
                                    PARAM_ALPHA);
        set_config('an_acceptccs', implode(',', $acceptccs));

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
            if (time() - intval($mconfig->an_lastcron) > 3600 * 24) {
                return false;
            }
        }

        set_config('an_review', $reviewval);
        set_config('an_capture_day', $captureday);
        set_config('an_emailexpired', $emailexpired);
        set_config('an_emailexpiredteacher', $emailexpiredteacher);
        set_config('an_sorttype', $sorttype);

        // https and openssl library is required
        if ((substr($CFG->wwwroot, 0, 5) !== 'https' and empty($CFG->loginhttps)) or
            !enrolment_plugin_authorize::check_openssl_loaded()) {
            return false;
        }

        // REQUIRED fields;
        // an_login
        $loginval = optional_param('an_login', '');
        if (empty($loginval) && empty($mconfig->an_login)) {
            return false;
        }
        $loginval = !empty($loginval) ? rc4encrypt($loginval) : strval($mconfig->an_login);
        set_config('an_login', $loginval, 'enrol/authorize');

        // an_tran_key, an_password
        $tranval = optional_param('an_tran_key', '');
        $tranval = !empty($tranval) ? rc4encrypt($tranval) : (isset($mconfig->an_tran_key)?$mconfig->an_tran_key:'');
        $passwordval = optional_param('an_password', '');
        $passwordval = !empty($passwordval) ? rc4encrypt($passwordval) :(isset($mconfig->an_password)?$mconfig->an_password:'');
        $deletecurrent = optional_param('delete_current', '0', PARAM_BOOL);
        if (!empty($deletecurrent) and !empty($tranval)) {
            delete_records('config_plugins', 'name', 'an_password', 'plugin', 'enrol/authorize');
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
     * zero_cost (static method)
     *
     * @param unknown_type $course
     * @return number
     * @static
     */
    function zero_cost($course) {
        $curcost = enrolment_plugin_authorize::get_course_cost($course);
        return (abs($curcost['cost']) < 0.01);
    }


    /**
     * get_course_cost (static method)
     *
     * @param object $course
     * @return array
     * @static
     */
    function get_course_cost($course)
    {
        global $CFG;

        $cost = (float)0;
        $currency = (!empty($course->currency))
                     ? $course->currency :( empty($CFG->enrol_currency)
                                            ? 'USD' : $CFG->enrol_currency );

        if (!empty($course->cost)) {
            $cost = (float)(((float)$course->cost) < 0) ? $CFG->enrol_cost : $course->cost;
        }

        $cost = format_float($cost, 2);
        $ret = array('cost' => $cost, 'currency' => $currency);

        return $ret;
    }

    /**
     * email_to_admin (static method)
     *
     * @param string $subject
     * @param mixed $data
     * @static
     */
    function email_to_admin($subject, $data)
    {
        global $SITE;

        $admin = get_admin();
        $data = (array)$data;

        $message = "$SITE->fullname: Transaction failed.\n\n$subject\n\n";
        $message .= print_r($data, true);
        email_to_user($admin, $admin, "$SITE->fullname: Authorize.net ERROR", $message);
    }

    /**
     * prevent_double_paid (static method)
     *
     * @param object $course
     * @static
     */
    function prevent_double_paid($course)
    {
        global $CFG, $SESSION, $USER;

        $status = empty($CFG->an_test) ? AN_STATUS_AUTH : AN_STATUS_NONE;

        if ($rec=get_record('enrol_authorize','userid',$USER->id,'courseid',$course->id,'status',$status,'id')) {
            $a = new stdClass;
            $a->orderid = $rec->id;
            $a->url = "$CFG->wwwroot/enrol/authorize/index.php?order=$a->orderid";
            redirect($a->url, get_string("paymentpending", "enrol_authorize", $a), '10');
            return;
        }
        if (isset($SESSION->ccpaid)) {
            unset($SESSION->ccpaid);
            redirect($CFG->wwwroot . '/login/logout.php?sesskey='.sesskey());
            return;
        }
    }

    /**
     * check_openssl_loaded (static method)
     *
     * @return bool
     * @static
     */
    function check_openssl_loaded() {
        return extension_loaded('openssl');
    }

    /**
     * get_list_of_creditcards (static method)
     *
     * @param bool $getall
     * @return array
     * @static
     */
    function get_list_of_creditcards($getall = false)
    {
        global $CFG;

        $alltypes = array(
            'mcd' => 'Master Card',
            'vis' => 'Visa',
            'amx' => 'American Express',
            'dsc' => 'Discover',
            'dnc' => 'Diners Club',
            'jcb' => 'JCB',
            'swi' => 'Switch',
            'dlt' => 'Delta',
            'enr' => 'EnRoute'
        );

        if ($getall or empty($CFG->an_acceptccs)) {
            return $alltypes;
        }

        $ret = array();
        $ccs = explode(',', $CFG->an_acceptccs);
        foreach ($ccs as $key) {
            $ret[$key] = $alltypes[$key];
        }
        return $ret;
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
    function cron()
    {
        global $CFG, $SITE;
        require_once($CFG->dirroot.'/enrol/authorize/authorizenetlib.php');

        $oneday = 86400;
        $timenow = time();
        $settlementtime = authorize_getsettletime($timenow);
        $timediff30 = $settlementtime - (30 * $oneday);

        $mconfig = get_config('enrol/authorize');
        set_config('an_lastcron', $timenow, 'enrol/authorize');

        mtrace("Processing authorize cron...");

        if (intval($mconfig->an_dailysettlement) < $settlementtime) {
            set_config('an_dailysettlement', $settlementtime, 'enrol/authorize');
            mtrace("    daily cron; some cleanups and sending email to admins the count of pending orders expiring", ": ");
            $this->cron_daily();
            mtrace("done");
        }

        mtrace("    scheduled capture", ": ");
        if (empty($CFG->an_review) or
           (!empty($CFG->an_test)) or
           (intval($CFG->an_capture_day) < 1) or
           (!enrolment_plugin_authorize::check_openssl_loaded())) {
            mtrace("disabled");
            return; // order review disabled or test mode or manual capture or openssl wasn't loaded.
        }

        $timediffcnf = $settlementtime - (intval($CFG->an_capture_day) * $oneday);
        $sql = "SELECT E.*, C.fullname, C.enrolperiod " .
               "FROM {$CFG->prefix}enrol_authorize E " .
               "INNER JOIN {$CFG->prefix}course C ON C.id = E.courseid " .
               "WHERE (E.status = '" .AN_STATUS_AUTH. "') " .
               "  AND (E.timecreated < '$timediffcnf') AND (E.timecreated > '$timediff30')";

        if (!$orders = get_records_sql($sql)) {
            mtrace("no pending orders");
            return;
        }

        $eachconn = intval($mconfig->an_eachconnsecs);
        if (empty($eachconn)) $eachconn = 3;
        elseif ($eachconn > 60) $eachconn = 60;

        $ordercount = count((array)$orders);
        if (($ordercount * $eachconn) + intval($mconfig->an_lastcron) > $timenow) {
            mtrace("blocked");
            return;
        }

        mtrace("    $ordercount orders are being processed now", ": ");

        $faults = '';
        $sendem = array();
        $elapsed = time();
        @set_time_limit(0);
        $this->log = "AUTHORIZE.NET AUTOCAPTURE CRON: " . userdate($timenow) . "\n";

        foreach ($orders as $order) {
            $message = '';
            $extra = NULL;
            $success = authorize_action($order, $message, $extra, AN_ACTION_PRIOR_AUTH_CAPTURE);
            if ($success) {
                $timestart = $timeend = 0;
                if ($order->enrolperiod) {
                    $timestart = $timenow;
                    $timeend = $order->settletime + $order->enrolperiod;
                }
                if (enrol_student($order->userid, $order->courseid, $timestart, $timeend, 'manual')) {
                    $this->log .= "User($order->userid) has been enrolled to course($order->courseid).\n";
                    if (!empty($CFG->enrol_mailstudents)) {
                        $sendem[] = $order->id;
                    }
                }
                else {
                    $user = get_record('user', 'id', $order->userid);
                    $faults .= "Error while trying to enrol ".fullname($user)." in '$order->fullname' \n";
                    foreach ($order as $okey => $ovalue) {
                        $faults .= "   $okey = $ovalue\n";
                    }
                }
            }
            else {
                $this->log .= "Error, Order# $order->id: " . $message . "\n";
            }
        }

        mtrace("processed");

        $timenow = time();
        $elapsed = $timenow - $elapsed;
        $eachconn = ceil($elapsed / $ordercount);
        set_config('an_eachconnsecs', $eachconn, 'enrol/authorize');

        $this->log .= "AUTHORIZE.NET CRON FINISHED: " . userdate($timenow);

        $adminuser = get_admin();
        if (!empty($faults)) {
            email_to_user($adminuser, $adminuser, "AUTHORIZE.NET CRON FAULTS", $faults);
        }
        if (!empty($CFG->enrol_mailadmins)) {
            email_to_user($adminuser, $adminuser, "AUTHORIZE.NET CRON LOG", $this->log);
        }

        // Send emails to students about which courses have enrolled.
        if (empty($sendem)) {
            return;
        }

        mtrace("    sending welcome messages to students", ": ");
        $select = "SELECT e.id, e.courseid, e.userid, c.fullname
                 FROM {$CFG->prefix}enrol_authorize e
                 INNER JOIN {$CFG->prefix}course c ON c.id = e.courseid
               WHERE e.id IN(" . implode(',', $sendem) . ")
               ORDER BY e.userid";

        $emailinfo = get_records_sql($select);
        $ei = reset($emailinfo);
        while ($ei !== false) {
            $usercourses = array();
            $lastuserid = $ei->userid;
            for ($current = $ei; $current !== false && $current->userid == $lastuserid; $current = next($emailinfo)) {
                $usercourses[] = $current->fullname;
            }
            $ei = $current;
            $a = new stdClass;
            $a->courses = implode("\n", $usercourses);
            $a->profileurl = "$CFG->wwwroot/user/view.php?id=$lastuserid";
            $a->paymenturl = "$CFG->wwwroot/enrol/authorize/index.php?user=$lastuserid";
            $emailmessage = get_string('welcometocoursesemail', 'enrol_authorize', $a);
            $user = get_record('user', 'id', $lastuserid);
            @email_to_user($user, $adminuser, get_string("enrolmentnew", '', $SITE->shortname), $emailmessage);
        }
        mtrace("sent");
    }

    /**
     * Daily cron. It executes at settlement time (default is 00:05).
     *
     * @access private
     */
    function cron_daily()
    {
        global $CFG, $SITE;
        require_once($CFG->dirroot.'/enrol/authorize/authorizenetlib.php');

        $oneday = 86400;
        $timenow = time();
        $settlementtime = authorize_getsettletime($timenow);
        $timediff30 = $settlementtime - (30 * $oneday);

        // Delete orders that no transaction was made.
        $select = "(status='".AN_STATUS_NONE."') AND (timecreated<'$timediff30')";
        delete_records_select('enrol_authorize', $select);

        // Pending orders are expired with in 30 days.
        $select = "(status='".AN_STATUS_AUTH."') AND (timecreated<'$timediff30')";
        execute_sql("UPDATE {$CFG->prefix}enrol_authorize SET status='".AN_STATUS_EXPIRE."' WHERE $select", false);

        // Delete expired orders 60 days later.
        $timediff60 = $settlementtime - (60 * $oneday);
        $select = "(status='".AN_STATUS_EXPIRE."') AND (timecreated<'$timediff60')";
        delete_records_select('enrol_authorize', $select);

        // Daily warning email for pending orders expiring.
        if (empty($CFG->an_emailexpired)) {
            return; // not enabled
        }

        // Pending orders count will be expired.
        $timediffem = $settlementtime - ((30 - intval($CFG->an_emailexpired)) * $oneday);
        $select = "(status='". AN_STATUS_AUTH ."') AND (timecreated<'$timediffem') AND (timecreated>'$timediff30')";
        $count = count_records_select('enrol_authorize', $select);
        if (!$count) {
            return;
        }

        // Email to admin
        $a = new stdClass;
        $a->pending = $count;
        $a->days = $CFG->an_emailexpired;
        $a->course = $SITE->shortname;
        $subject = get_string('pendingorderssubject', 'enrol_authorize', $a);
        $a = new stdClass;
        $a->pending = $count;
        $a->days = $CFG->an_emailexpired;
        $a->course = $SITE->fullname;
        $a->enrolurl = "$CFG->wwwroot/$CFG->admin/users.php";
        $a->url = $CFG->wwwroot.'/enrol/authorize/index.php?status='.AN_STATUS_AUTH;
        $message = get_string('pendingordersemail', 'enrol_authorize', $a);
        $adminuser = get_admin();
        email_to_user($adminuser, $adminuser, $subject, $message);

        // Email to teachers
        if (empty($CFG->an_teachermanagepay) or empty($CFG->an_emailexpiredteacher)) {
            return; // teachers can't manage payments or email feature disabled for teachers.
        }

        $sorttype = empty($CFG->an_sorttype) ? 'ttl' : $CFG->an_sorttype;
        $where = "(E.status='". AN_STATUS_AUTH ."') AND (E.timecreated<'$timediffem') AND (E.timecreated>'$timediff30')";
        $sql = "SELECT E.courseid, E.currency, C.fullname, C.shortname, " .
               "COUNT(E.courseid) AS cnt, SUM(E.amount) as ttl " .
               "FROM {$CFG->prefix}enrol_authorize E " .
               "INNER JOIN {$CFG->prefix}course C ON C.id = E.courseid " .
               "WHERE $where GROUP BY E.courseid ORDER BY $sorttype DESC";

        $courseinfos = get_records_sql($sql);
        foreach($courseinfos as $courseinfo) {
            $lastcourse = $courseinfo->courseid;
            if ($teachers = get_course_teachers($lastcourse)) {
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
                $a->url = $CFG->wwwroot.'/enrol/authorize/index.php?course='.
                          $lastcourse.'&amp;status='.AN_STATUS_AUTH;
                $message = get_string('pendingordersemailteacher', 'enrol_authorize', $a);
                foreach ($teachers as $teacher) {
                    email_to_user($teacher, $adminuser, $subject, $message);
                }
            }
        }
    }
}
?>
