<?php  // $Id$
       // Implements all the main code for the Paypal plugin

require_once("$CFG->dirroot/enrol/enrol.class.php");


class enrolment_plugin extends enrolment_base {


/// Override the base print_entry() function
function print_entry($course) {
    global $CFG, $USER;


    $strloginto = get_string("loginto", "", $course->shortname);
    $strcourses = get_string("courses");


    $teacher = get_teacher($course->id);


    if ( (float) $course->cost < 0 ) {
        $cost = (float) $CFG->enrol_cost;
    } else {
        $cost = (float) $course->cost;
    }
    $cost = format_float($cost, 2);


    if (abs($cost) < 0.01) { // no cost, default to base class entry to course


        parent::print_entry($course);

    } else {

        print_header($strloginto, $course->fullname, 
                     "<a href=\"$CFG->wwwroot/courses/\">$strcourses</a> -> $strloginto");
        print_course($course, "80%");
        print_simple_box_start("center");


        include("$CFG->dirroot/enrol/paypal/enrol.html");

        print_simple_box_end();
        print_footer();

    }
} // end of function print_entry()




/// Override the base check_entry() function
/// This should never be called for this type of enrolment anyway
function check_entry($form, $course) {
}       



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
        
        $str .= '<span class="courseboxcost" title="'.$strrequirespayment.'">'.$strcost.': ';
        $str .= $currency.format_float($cost,2).'</span>';
        
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
                                'CAD' => 'Canadian Dollars'
                             );

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


} // end of class definition
