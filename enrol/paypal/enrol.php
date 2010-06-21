<?php
       // Implements all the main code for the PayPal plugin

class enrolment_plugin_paypal {


/// Override the base print_entry() function
function print_entry($course) {
    global $CFG, $USER, $OUTPUT, $PAGE;


    $strloginto = get_string("loginto", "", $course->shortname);
    $strcourses = get_string("courses");

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    // Pass $view=true to filter hidden caps if the user cannot see them
    if ($users = get_users_by_capability($context, 'moodle/course:update', 'u.*', 'u.id ASC',
                                         '', '', '', '', false, true)) {
        $users = sort_by_roleassignment_authority($users, $context);
        $teacher = array_shift($users);
    } else {
        $teacher = false;
    }

    if ( (float) $course->cost <= 0 ) {
        $cost = (float) $CFG->enrol_cost;
    } else {
        $cost = (float) $course->cost;
    }

    if (abs($cost) < 0.01) { // no cost, default to base class entry to course

        $manual = enrolment_factory::factory('manual');
        $manual->print_entry($course);

    } else {
        $PAGE->navbar->add($strcourses, new moodle_url('/course/'));
        $PAGE->navbar->add($strloginto);
        $PAGE->set_title($strloginto);
        $PAGE->set_heading($course->fullname);
        echo $OUTPUT->header();
        print_course($course, "80%");

        if ($course->password) {  // Presenting two options
            echo $OUTPUT->heading(get_string('costorkey', 'enrol_paypal'));
        }

        echo $OUTPUT->box_end();

        if ($USER->username == 'guest') { // force login only for guest user, not real users with guest role
            if (empty($CFG->loginhttps)) {
                $wwwroot = $CFG->wwwroot;
            } else {
                // This actually is not so secure ;-), 'cause we're
                // in unencrypted connection...
                $wwwroot = str_replace("http://", "https://", $CFG->wwwroot);
            }
            echo '<div class="mdl-align"><p>'.get_string('paymentrequired').'</p>';
            echo '<p><b>'.get_string('cost').": $CFG->enrol_currency $cost".'</b></p>';
            echo '<p><a href="'.$wwwroot.'/login/">'.get_string('loginsite').'</a></p>';
            echo '</div>';
        } else {
            //Sanitise some fields before building the PayPal form
            $coursefullname  = $course->fullname;
            $courseshortname = $course->shortname;
            $userfullname    = fullname($USER);
            $userfirstname   = $USER->firstname;
            $userlastname    = $USER->lastname;
            $useraddress     = $USER->address;
            $usercity        = $USER->city;

            include($CFG->dirroot.'/enrol/paypal/enrol.html');
        }

        echo $OUTPUT->box_end();

        if ($course->password) {  // Second option
            $password = '';
            include($CFG->dirroot.'/enrol/manual/enrol.html');
        }

        echo $OUTPUT->footer();

    }
} // end of function print_entry()




/// Override the get_access_icons() function
function get_access_icons($course) {
    global $CFG;

    $str = '';

    if ( (float) $course->cost < 0) {
        $cost = (float) $CFG->enrol_cost;
    } else {
        $cost = (float) $course->cost;
    }

    if (abs($cost) < 0.01) {
        $manual = enrolment_factory::factory('manual');
        $str = $manual->get_access_icons($course);

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
           case 'AUD': $currency = '$'; break;
           default:    $currency = '$'; break;
        }

        $str .= '<div class="cost" title="'.$strrequirespayment.'">'.$strcost.': ';
        $str .= $currency.format_float($cost,2).'</div>';

    }

    return $str;
}

/// Override the base class config_form() function
function config_form($frm) {
    global $CFG;

    $paypalcurrencies = array(  'USD' => 'US Dollars',
                                'EUR' => 'Euros',
                                'JPY' => 'Japanese Yen',
                                'GBP' => 'British Pounds',
                                'CAD' => 'Canadian Dollars',
                                'AUD' => 'Australian Dollars'
                             );

    $vars = array('enrol_cost', 'enrol_currency', 'enrol_paypalbusiness',
                  'enrol_mailstudents', 'enrol_mailteachers', 'enrol_mailadmins');
    foreach ($vars as $var) {
        if (!isset($frm->$var)) {
            $frm->$var = '';
        }
    }

    include("$CFG->dirroot/enrol/paypal/config.html");
}

function process_config($config) {

    if (!isset($config->enrol_cost)) {
        $config->enrol_cost = 0;
    }
    set_config('enrol_cost', $config->enrol_cost);

    if (!isset($config->enrol_currency)) {
        $config->enrol_currency = 'USD';
    }
    set_config('enrol_currency', $config->enrol_currency);

    if (!isset($config->enrol_paypalbusiness)) {
        $config->enrol_paypalbusiness = '';
    }
    $config->enrol_paypalbusiness = trim($config->enrol_paypalbusiness); // remove trailing spaces etc.
    set_config('enrol_paypalbusiness', $config->enrol_paypalbusiness);

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

    return true;

}

/**
* This function enables internal enrolment when PayPal is primary and course key is set at the same time.
*
* @param    form    the form data submitted, as an object
* @param    course  the current course, as an object
*/
function check_entry($form, $course) {
    $manual = enrolment_factory::factory('manual');
    $manual->check_entry($form, $course);
    if (isset($manual->errormsg)) {
        $this->errormsg = $manual->errormsg;
    }
}

/**
 * Provides method to print the enrolment key form code. This method is called
 * from /enrol/manual/enrol.html if it's included
 * @param  object a valid course object
 */
function print_enrolmentkeyfrom($course) {
    $manual = enrolment_factory::factory('manual');
    $manual->print_enrolmentkeyfrom($course);
}

} // end of class definition


