<?php

require_once("$CFG->dirroot/enrol/enrol.class.php");


// $CFG->enrol_paypalmailusers:      send email to users when they are enrolled in a course
// $CFG->enrol_paypalmailadmin:      email the log from the cron job to the admin


// Test data
$CFG->enrol_cost = 5.00;
$CFG->enrol_paypalbusiness = "payment@moodle.com";
$CFG->enrol_paypalcurrency = "USD";

// Accepted PayPal currencies (USD/EUR/JPY/GBP/CAD)




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
        echo "</div>";

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

    if ( (float) $course->cost < 0) {
        $cost = (float) $CFG->enrol_cost;
    } else {
        $cost = (float) $course->cost;
    }

    if (abs($cost) < 0.01) {
        $str = parent::get_access_icons($course);
    } else {
    
        $strrequirespayment = get_string("requirespayment");
        
        if (! file_exists("$CFG->dirroot/pix/m/$CFG->enrol_paypalcurrency.gif")) {
            $icon = "$CFG->pixpath/m/USD.gif";
        } else {
            $icon = "$CFG->pixpath/m/$CFG->enrol_paypalcurrency.gif";
        }
        
        $str .= "<a title=\"$strrequirespayment\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\">";
        $str .= "<img vspace=4 alt=\"$strrequirespayment\" height=16 width=16 border=0 src=\"$icon\"></a>";
        
    }

    return $str;
}



} // end of class definition
