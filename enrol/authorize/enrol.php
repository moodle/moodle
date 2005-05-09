<?php  // $Id$

// Authorize.net
define('AN_HOST', 'secure.authorize.net');
define('AN_PORT', 443);
define('AN_PATH', '/gateway/transact.dll');
define('AN_APPROVED', '1');
define('AN_DECLINED', '2');
define('AN_ERROR', '3');

require_once("$CFG->dirroot/enrol/enrol.class.php");

class enrolment_plugin extends enrolment_base {

/// Override: print_entry()
function print_entry($course) {
    global $CFG, $USER;
    
    $strloginto = get_string("loginto", "", $course->shortname);
    $strcourses = get_string("courses");
    $teacher = get_teacher($course->id);

    if ($this->zero_cost($course)) {
        parent::print_entry($course);
    } else {
     	// check payment
    	if (isset($SESSION->ccpaid))
    	{
    		// security check: don't duplicate payment
    		unset($SESSION->ccpaid);
    		redirect("$CFG->wwwroot/login/logout.php");
    		exit;    	
    	}
    	
    	if ((!empty($CFG->loginhttps)) && (!isset($_SERVER['HTTPS'])))
    	{
    		$wwwsroot = str_replace('http','https', $CFG->wwwroot);
    		$sdestination = "$wwwsroot/course/enrol.php?id=$course->id";
    		redirect($sdestination);
    		exit;
    	}

    	
        print_header($strloginto, $course->fullname, "<a href=\"$CFG->wwwroot/course/\">$strcourses</a> -> $strloginto");
        print_course($course, "80%");
        print_simple_box_start("center");

        //Sanitise some fields before building the CC form
        $coursefullname  = $this->sanitise_for_cc($course->fullname);
        $courseshortname = $this->sanitise_for_cc($course->shortname);
        $userfirstname   = $this->sanitise_for_cc($USER->firstname);
        $userlastname    = $this->sanitise_for_cc($USER->lastname);
        $useraddress     = $this->sanitise_for_cc($USER->address);
        $usercity        = $this->sanitise_for_cc($USER->city);
        
        $cost = $this->get_cource_cost($course);

        include("$CFG->dirroot/enrol/authorize/enrol.html");

        print_simple_box_end();
        print_footer();

    }
}

/// Override: base check_entry()
function check_entry($form, $course) {
    if ($this->zero_cost($course)) {
        parent::check_entry($form, $course);
    }
    else {
		$this->cc_submit($form, $course);
   }  
}

function cc_submit($form, $course)
{
    global $CFG, $USER, $SESSION;
    
    // check payment
    if (isset($SESSION->ccpaid))
    {
    	// security check: don't duplicate payment
    	unset($SESSION->ccpaid);
    	redirect("$CFG->wwwroot/login/logout.php");
    	exit;    	
    }
    
    if (empty($form->ccfirstname) || empty($form->cclastname) ||
    	empty($form->cc) || empty($form->ccexpiremm) || empty($form->ccexpireyyyy) ||
    	empty($form->cvv))
    	{
    		$this->errormsg = get_string("allfieldsrequired");
    		return;
    	}
    
    
    $order_number = 0; // can be get from db
    $formdata = array (
       'x_version' => '3.1',
       'x_delim_data' => 'True',
       'x_delim_char' => '|',
       'x_relay_response' => 'False',
       // config
       'x_login' => (!empty($CFG->an_login)) ? $CFG->an_login : 'testdrive',
       'x_tran_key' => $CFG->an_tran_key,
       'x_test_request' => (!empty($CFG->an_test)) ? 'True' : 'False',
       'x_type' => 'AUTH_CAPTURE',
		// user
       'x_first_name' => (empty($form->ccfirstname) ? $USER->firstname : $form->ccfirstname),
       'x_last_name' => (empty($form->cclastname) ? $USER->lastname : $form->cclastname),
       'x_address' => $USER->address,
       'x_city' => $USER->city,
       'x_state' => '',
       'x_zip' => $form->cczip,
       'x_country' => $USER->country,
       'x_card_num' => $form->cc,
       'x_card_code' => $form->cvv,
       'x_amount' => $CFG->enrol_currency . " " . $this->get_cource_cost($course),
       'x_exp_date' => (($form->ccexpiremm<10) ? strval('0'.$form->ccexpiremm) : strval($form->ccexpiremm)) . ($form->ccexpireyyyy),
       'x_email' => $USER->email,
       'x_email_customer' => 'False',
       'x_cust_id' => $USER->id,
       'x_customer_ip' => $_SERVER["REMOTE_ADDR"],
       'x_phone' => '',
       'x_fax' => '',
       'x_invoice_num' => $order_number,
       'x_description' => $course->shortname
     );

     //build the post string
     $poststring = '';
     foreach($formdata AS $key => $val) {
         $poststring .= urlencode($key) . "=" . urlencode($val) . "&";
     }
     // strip off trailing ampersand
     $poststring = substr($poststring, 0, -1);
     
     $fp = fsockopen("ssl://" . AN_HOST, AN_PORT, $errno, $errstr, $timeout = 60);
     if(!$fp) {
		$this->errormsg =  "$errstr ($errno)";
     }else{
       //send the server request
       fputs($fp,
			"POST " . AN_PATH . " HTTP/1.1\r\n" .
			"Host: " . AN_HOST . "\r\n" . 
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

       $response = explode("|", $data);
     }

     if ($response[0] == AN_APPROVED) {
         $SESSION->ccpaid = 1; // security check: don't duplicate payment
        
		 // XXX: Is this valid for paid cources?
         //if ($course->enrolperiod) {
         //    $timestart = time();
         //    $timeend   = $timestart + $course->enrolperiod;
         //} else {
         //    $timestart = $timeend = 0;
         //}
         // XXX: Is this valid for paid cources?
         // if (!enrol_student($USER->id, $course->id, $timestart, $timeend)) {
        
        if (!enrol_student($USER->id, $course->id)) {
			$this->email_cc_error_to_admin("Error while trying to enrol ".fullname($USER)." in '$course->fullname'", $response);
       } else {
       	// begin: send email
		$teacher = get_teacher($course->id);
		if (!empty($CFG->enrol_mailstudents)) {
			$a->coursename = "$course->fullname";
			$a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id";
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
		$datax->ccexp = $formdata['x_exp_date'];
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
       //
       redirect($destination);
     } else {
		$this->errormsg = $response[3];
     }
}

function zero_cost($course) {

	$cost = $this->get_cource_cost($course);

    if (abs($cost) < 0.01) { // no cost
        return true;
    }
    
    return false;
}

function get_cource_cost($course) {
    global $CFG;
    $cost = (float)0;
    
    if (isset($course->cost))
    {
    	if (((float)$course->cost) < 0)
    	{
    		$cost = (float)$CFG->enrol_cost;
    	}
    	else
    	{
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
    $cost = $this->get_cource_cost($course);

    if (abs($cost) < 0.01) {
        $str = parent::get_access_icons($course);
    } else {
    
        $strrequirespayment = get_string("requirespayment");
        $strcost = get_string("cost");

        if (empty($CFG->enrol_currency)) {
            set_config('enrol_currency', 'USD');
        }

        switch ($CFG->enrol_currency) {
           case 'EUR': $currency = '&euro;'; break;
           case 'CAD': $currency = '$'; break;
           case 'GBP': $currency = '&pound;'; break;
           case 'JPY': $currency = '&yen;'; break;
           default:    $currency = '$'; break;
        }
        
        $str .= "<p class=\"coursecost\"><font size=-1>$strcost: ".
                "<a title=\"$strrequirespayment\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\"></a>";
        $str .= "$currency".format_float($cost,2).'</a></p>';
        
    }

    return $str;
}


function config_form($frm) {
    global $CFG;

    $paypalcurrencies = array(  'USD' => 'US Dollars',
                                'EUR' => 'Euros',
                                'JPY' => 'Japanese Yen',
                                'GBP' => 'British Pounds',
                                'CAD' => 'Canadian Dollars'
                             );

    $vars = array('enrol_cost', 'enrol_currency', 'an_login', 'an_tran_key', 'an_password', 'an_test',
                  'enrol_mailstudents', 'enrol_mailteachers', 'enrol_mailadmins', 'enrol_allowinternal');
    foreach ($vars as $var) {
        if (!isset($frm->$var)) {
            $frm->$var = '';
        } 
    }

    include("$CFG->dirroot/enrol/authorize/config.html");
}

function process_config($config) {

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
    
    if (!isset($config->an_test)) {
        $config->an_test = '';
    }
    set_config('an_test', $config->an_test);
    
    // ----
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
    
    return true;
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


//To avoid wrong (for PayPal) characters in sent data
function sanitise_for_cc($text) {
    global $CFG;

    if (!empty($CFG->sanitise_for_paypal)) {
        //Array of characters to replace (not allowed by PayPal)
        //Can be expanded as necessary to add other diacritics
        $replace = array('á' => 'a',        //Spanish characters
                         'é' => 'e',
                         'í' => 'i',
                         'ó' => 'o',
                         'ú' => 'u',
                         'Á' => 'A',
                         'É' => 'E',
                         'Í' => 'I',
                         'Ó' => 'O',
                         'Ú' => 'U',
                         'ñ' => 'n',
                         'Ñ' => 'N',
                         'ü' => 'u',
                         'Ü' => 'U');
        $text = strtr($text, $replace);
    
        //Make here other sanities if necessary

    }

    return $text;

}


} // end of class definition

?>
