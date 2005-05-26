<?php  // $Id$

// Authorize.net
define('AN_HOST', 'secure.authorize.net');
define('AN_PORT', 443);
define('AN_PATH', '/gateway/transact.dll');
define('AN_APPROVED', '1');
define('AN_DECLINED', '2');
define('AN_ERROR', '3');
define('AN_DELIM', '|');
define('AN_ENCAP', '"');

require_once("$CFG->dirroot/enrol/enrol.class.php");

class enrolment_plugin extends enrolment_base {

/// Override: print_entry()
function print_entry($course) {
    global $CFG, $USER, $form;

    if ($this->zero_cost($course)) {
        if (!empty($CFG->enrol_allowinternal)) {
            parent::print_entry($course);
        } else {
            print_header();
            notice(get_string("enrolmentnointernal"), $CFG->wwwroot);
        }
    } else {
        // check payment
        $this->check_paid();

        if ((!empty($CFG->loginhttps)) && (!isset($_SERVER['HTTPS']))) {
            $wwwsroot = str_replace('http://','https://', $CFG->wwwroot);
            $sdestination = "$wwwsroot/course/enrol.php?id=$course->id";
            redirect($sdestination);
            exit;
        }

        $strloginto = get_string("loginto", "", $course->shortname);
        $strcourses = get_string("courses");
        $teacher = get_teacher($course->id);

        print_header($strloginto, $course->fullname, "<a href=\"$CFG->wwwroot/course/\">$strcourses</a> -> $strloginto");
        print_course($course, "80%");
        print_simple_box_start("center");

        $coursefullname	= $course->fullname;
        $courseshortname= $course->shortname;
        $userfirstname	= $USER->firstname;
        $userlastname	= $USER->lastname;
        $useraddress	= $USER->address;
        $usercity		= $USER->city;
        $cost			= $this->get_course_cost($course);

        $CCTYPES = array(
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

        $formvars = array('ccfirstname','cclastname','cc','ccexpiremm','ccexpireyyyy','cctype','cvv','cczip');
        foreach ($formvars as $var) {
            if (!isset($form->$var)) {
    			$form->$var = '';
    		} 
    	}

    	include($CFG->dirroot . '/enrol/authorize/enrol.html');
    	print_simple_box_end();
    	print_footer();
    }
}

/// Override: check_entry()
function check_entry($form, $course) {
    global $CFG;
    if ($this->zero_cost($course)) {
        if (!empty($CFG->enrol_allowinternal)) {
            parent::check_entry($form, $course);
        }
    } else {
    	$this->cc_submit($form, $course);
    }
}

function cc_submit($form, $course)
{
    global $CFG, $USER, $SESSION;
    require_once($CFG->dirroot . '/enrol/authorize/ccval.php');

    if (empty($form->ccfirstname) || empty($form->cclastname) ||
    	empty($form->cc) || empty($form->cvv) || empty($form->cctype) ||
    	empty($form->ccexpiremm) || empty($form->ccexpireyyyy)) {
    		$this->errormsg = get_string("allfieldsrequired");
    		return;
    	}

    $exp_date = (($form->ccexpiremm<10) ? strval('0'.$form->ccexpiremm) : strval($form->ccexpiremm)) . ($form->ccexpireyyyy);

    if (! CCVal($form->cc, $form->cctype, $exp_date)) {
    	$this->errormsg = get_string("ccinvalid", "enrol_authorize");
    	return;
    }
    
    $this->check_paid();
    $order_number = 0; // can be get from db
    $formdata = array (
    	'x_version'			=> '3.1',
    	'x_delim_data'		=> 'True',
    	'x_delim_char'		=> AN_DELIM,
    	'x_encap_char'		=> AN_ENCAP,
    	'x_relay_response'	=> 'False',
    	'x_login'			=> $CFG->an_login,
    	'x_test_request'	=> (!empty($CFG->an_test)) ? 'True' : 'False',
    	'x_type'			=> 'AUTH_CAPTURE',
    	'x_method'			=> 'CC',
    	// user
    	'x_first_name'		=> (empty($form->ccfirstname) ? $USER->firstname : $form->ccfirstname),
    	'x_last_name'		=> (empty($form->cclastname) ? $USER->lastname : $form->cclastname),
    	'x_address'			=> $USER->address,
    	'x_city'			=> $USER->city,
    	'x_state'			=> '',
    	'x_zip'				=> $form->cczip,
    	'x_country'			=> $USER->country,
    	'x_card_num'		=> $form->cc,
    	'x_card_code'		=> $form->cvv,
    	'x_currency_code'	=> $CFG->enrol_currency,
    	'x_amount'			=> $this->get_course_cost($course),
    	'x_exp_date'		=> $exp_date,
    	'x_email'			=> $USER->email,
    	'x_email_customer'	=> 'False',
    	'x_cust_id'			=> $USER->id,
    	'x_customer_ip'		=> $_SERVER["REMOTE_ADDR"],
    	'x_phone'			=> '',
    	'x_fax'				=> '',
    	'x_invoice_num'		=> $order_number,
    	'x_description'		=> $course->shortname
    );

    //build the post string
    $poststring = '';
    if (!empty($CFG->an_tran_key)) {
    	$poststring .= urlencode("x_tran_key") . "=" . urlencode($CFG->an_tran_key);
    } else {
    	$an_pswd = (isset($CFG->an_password)) ? $CFG->an_password : '';
    	$poststring .= urlencode("x_password") . "=" . urlencode($an_pswd);
    }
    foreach($formdata as $key => $val) {
    	$poststring .= "&" . urlencode($key) . "=" . urlencode($val);
    }
    //built

    $response = array();
    $anrefererheader = "";    	
    if (isset($CFG->an_referer) && (!empty($CFG->an_referer)) &&
    ($CFG->an_referer != "http://") && ($CFG->an_referer != "https://")) {
    	$anrefererheader = "Referer: " . $CFG->an_referer . "\r\n";
    }
    $fp = fsockopen("ssl://" . AN_HOST, AN_PORT, $errno, $errstr, 60);
    if(!$fp) {
    	$this->errormsg =  "$errstr ($errno)";
    	return;
    } else {
    	//send the server request
    	fputs($fp,
    		"POST " . AN_PATH . " HTTP/1.1\r\n" .
    		"Host: " . AN_HOST . "\r\n" . 
    		$anrefererheader .
    		"Content-type: application/x-www-form-urlencoded\r\n" .
    		"Content-length: " . strlen($poststring) . "\r\n" .
    		"Connection: close\r\n\r\n" .
    		$poststring . "\r\n\r\n");

    	//Get the response header from the server
    	$str = '';
    	while(!feof($fp) && !stristr($str, 'content-length')) {
    		$str = fgets($fp, 4096);
    	}
    	// If didnt get content-lenght, something is wrong.
    	if (!stristr($str, 'content-length')) {
    		$this->errormsg =  "content-length error";
    		return;
    	}
       
    	// Get length of data to be received.
    	$length = trim(substr($str,strpos($str,'content-length') + 15));
    	// Get buffer (blank data before real data)
    	fgets($fp, 4096);
    	// Get real data
    	$data = fgets($fp, $length);
    	fclose($fp);
    	$response = explode(AN_ENCAP.AN_DELIM.AN_ENCAP, $data);
    	if ($response === false)
    	{
    		$this->errormsg = "response error";
    		return;
    	}
    	$rcount = count($response) - 1;
    	if ($response[0]{0} == AN_ENCAP) {
    		$response[0] = substr($response[0], 1);
    	}
    	if (substr($response[$rcount], -1) == AN_ENCAP) {
    		$response[$rcount] = substr($response[$rcount], 0, -1);
    	}
    }

    if ($response[0] != AN_APPROVED) {
    	$this->errormsg = isset($response[3]) ? $response[3] : 'unknown error';
    } else {
    	$SESSION->ccpaid = 1; // security check: don't duplicate payment
    	if ($course->enrolperiod) {
    		$timestart = time();
    		$timeend   = $timestart + $course->enrolperiod;
    	} else {
    		$timestart = $timeend = 0;
    	}

    	if (!enrol_student($USER->id, $course->id, $timestart, $timeend)) {
    		$this->email_cc_error_to_admin("Error while trying to enrol ".fullname($USER)." in '$course->fullname'", $response);
    	} else {
    		// begin: send email
    		$teacher = get_teacher($course->id);
    		if (!empty($CFG->enrol_mailstudents)) {
    			$a->coursename = "$course->fullname";
    			$a->profileurl = "$CFG->wwwroot/user/view.php?id=$USER->id";
    			email_to_user($USER, $teacher, get_string("enrolmentnew", '', $course->shortname),
    			get_string('welcometocoursetext', '', $a));
    		}
    		if (!empty($CFG->enrol_mailteachers)) {
    			$a->course = "$course->fullname";
    			$a->user = fullname($USER);
    			email_to_user($teacher, $USER, get_string("enrolmentnew", '', $course->shortname),
    			get_string('enrolmentnewuser', '', $a));
    		}
    		if (!empty($CFG->enrol_mailadmins)) {
    			$a->course = "$course->fullname";
    			$a->user = fullname($USER);
    			$admins = get_admins();
    			foreach ($admins as $admin) {
    				email_to_user($admin, $USER, get_string("enrolmentnew", '', $course->shortname),
    				get_string('enrolmentnewuser', '', $a));
    			}
    		}
    		// end: send email

    		// begin: authorize_table
    		$cclast4 = substr($form->cc, -4);
    		$datax->cclastfour = ($cclast4 === false) ? '0000' : $cclast4;
    		$datax->ccexp = $exp_date;
    		$datax->cvv = $form->cvv;
    		$datax->ccname = $formdata['x_first_name'] . " " . $formdata['x_last_name'];
    		$datax->courseid = $course->id;
    		$datax->userid = $USER->id;
    		$datax->avscode = strval($response[5]);
    		$datax->transid = strval($response[6]);
    		if (!insert_record("enrol_authorize", $datax)) {	// Insert a transaction record
    			$this->email_cc_error_to_admin("Error while trying to insert valid transaction", $datax);
    		}

    	} // end if (!enrol_student)

    	if ($SESSION->wantsurl) {
    		$destination = $SESSION->wantsurl;
    		unset($SESSION->wantsurl);
    	} else {
    		$destination = "$CFG->wwwroot/course/view.php?id=$course->id";
    	}
    	redirect($destination);
    }
}

function zero_cost($course) {

    $cost = $this->get_course_cost($course);
    if (abs($cost) < 0.01) { // no cost
    	return true;
    }
    return false;
}

function get_course_cost($course) {
    global $CFG;
    $cost = (float)0;

    if (isset($course->cost)) {
    	if (((float)$course->cost) < 0) {
    		$cost = (float)$CFG->enrol_cost;
    	} else {
    		$cost = (float)$course->cost;
    	}   	
    }
    $cost = format_float($cost, 2);
    return $cost;
}

/// Override the get_access_icons() function
function get_access_icons($course) {
    global $CFG;

    $str = '';
    $cost = $this->get_course_cost($course);

    if (abs($cost) < 0.01) {
    	$str = parent::get_access_icons($course);
    } else {
    	$strrequirespayment = get_string("requirespayment");
    	$strcost = get_string("cost");

    	if (empty($CFG->enrol_currency)) {
    		set_config('enrol_currency', 'USD');
    	}

    	switch ($CFG->enrol_currency) {
    	case 'EUR':	$currency = '&euro;'; break;
    	case 'CAD':	$currency = '$'; break;
    	case 'GBP':	$currency = '&pound;'; break;
    	case 'JPY':	$currency = '&yen;'; break;
    	default:	$currency = '$'; break;
        }

        $str .= "<p class=\"coursecost\"><font size=-1>$strcost: " .
    			"<a title=\"$strrequirespayment\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\"></a>" .
    			"$currency" . format_float($cost, 2) . '</a></p>';
    }
    return $str;
}


function config_form($frm) {
    global $CFG;
    $ancurrencies = array(
    	'USD' => 'US Dollars',
    	'EUR' => 'Euros',
    	'JPY' => 'Japanese Yen',
    	'GBP' => 'British Pounds',
    	'CAD' => 'Canadian Dollars'
    );

    $vars = array('enrol_cost', 'enrol_currency', 'an_login', 'an_tran_key', 'an_password', 'an_referer', 'an_test',
    			  'enrol_mailstudents', 'enrol_mailteachers', 'enrol_mailadmins', 'enrol_allowinternal');

    foreach ($vars as $var) {
    	if (!isset($frm->$var)) {
    		$frm->$var = '';
    	}
    }

    if (!$this->check_openssl_loaded()) {
    	notify('PHP must be compiled with SSL support (--with-openssl)');
    }
    if (data_submitted()) {  // something POSTed
        // Some required fields
        if (empty($frm->an_login)) {
    	    notify("an_login required");
        }
        if (empty($frm->an_tran_key) && empty($frm->an_password)) {
    	    notify("an_tran_key or an_password required");
        }
    }
    include($CFG->dirroot.'/enrol/authorize/config.html');
}

function check_openssl_loaded() {
    return extension_loaded('openssl');
}

function process_config($config) {

    $return = $this->check_openssl_loaded();

    if (!isset($config->an_login)) {
    	$config->an_login = '';
    }
    set_config('an_login', $config->an_login);

    if (!isset($config->an_password)) {
    	$config->an_password = '';
    }
    set_config('an_password', $config->an_password);

    if (!isset($config->an_tran_key)) {
    	$config->an_tran_key = '';
    }
    set_config('an_tran_key', $config->an_tran_key);

    // Some required fields
    if (empty($config->an_login)) {
    	$return = false;   	
    }
    if (empty($config->an_tran_key) && empty($config->an_password)) {
    	$return = false;   	
    }

    if (empty($config->an_referer)) {
    	$config->an_referer = 'http://';
    }
    set_config('an_referer', $config->an_referer);

    if (!isset($config->an_test)) {
    	$config->an_test = '';
    }
    set_config('an_test', $config->an_test);

    // --------------------------------------
    if (!isset($config->enrol_cost)) {
    	$config->enrol_cost = '0';
    }
    set_config('enrol_cost', $config->enrol_cost);

    if (!isset($config->enrol_currency)) {
    	$config->enrol_currency = 'USD';
    }
    set_config('enrol_currency', $config->enrol_currency);

    if (!isset($config->enrol_mailstudents)) {
    	$config->enrol_mailstudents = '';
    }
    set_config('enrol_mailstudents', $config->enrol_mailstudents);

    if (!isset($config->enrol_mailteachers)) {
    	$config->enrol_mailteachers = '';
    }
    set_config('enrol_mailteachers', $config->enrol_mailteachers);

    if (!isset($config->enrol_mailadmins)) {
    	$config->enrol_mailadmins = '';
    }
    set_config('enrol_mailadmins', $config->enrol_mailadmins);

    if (!isset($config->enrol_allowinternal)) {
    	$config->enrol_allowinternal = '';
    }
    set_config('enrol_allowinternal', $config->enrol_allowinternal);

    return $return;
}

function email_cc_error_to_admin($subject, $data) {
    $admin = get_admin();
    $site = get_admin();

    $message = "$site->fullname:  Transaction failed.\n\n$subject\n\n";
    foreach ($data as $key => $value) {
    	$message .= "$key => $value\n";
    }
    email_to_user($admin, $admin, "CC ERROR: ".$subject, $message);
}

function check_paid() {

    global $CFG, $SESSION;

    if (isset($SESSION->ccpaid)) {
    	unset($SESSION->ccpaid);
    	redirect($CFG->wwwroot . '/login/logout.php');
    	exit;    	
    }
}

} // end of class definition
?>
