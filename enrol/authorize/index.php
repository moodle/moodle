<?php // $Id$

/// Load libraries
    require_once('../../config.php');
    require_once('const.php');
    require_once('locallib.php');
    require_once('localfuncs.php');
    require_once('authorizenetlib.php');

/// Parameters
    $orderid  = optional_param('order', 0, PARAM_INT);
    $courseid = optional_param('course', SITEID, PARAM_INT);
    $userid   = optional_param('user', 0, PARAM_INT);

/// Get course
    if (! $course = get_record('course', 'id', $courseid)) {
        error('Could not find that course');
    }

/// Only site users can access to this page
    require_login(); // Don't use $courseid! User may want to see old orders.

    if (has_capability('moodle/legacy:guest', get_context_instance(CONTEXT_SYSTEM), $USER->id, false)) {
        error("Guests cannot use this page.");
    }

/// Load strings. All strings should be defined here. locallib.php uses these strings.
    $strs = get_strings(array('search','status','action','time','course','confirm','no','all','none','error'));
    $authstrs = get_strings(array('orderid','nameoncard','echeckfirslasttname','void','capture','refund','delete',
                'allpendingorders','authcaptured','authorizedpendingcapture','capturedpendingsettle','settled',
                'refunded','cancelled','expired','underreview','approvedreview','reviewfailed','tested','new',
                'paymentmethod','methodcc','methodecheck',
                'transid','settlementdate','notsettled','amount','unenrolstudent'), 'enrol_authorize');

/// Print header
    $strpaymentmanagement = get_string('paymentmanagement', 'enrol_authorize');
    $navlinks = array();
    $navlinks[] = array('name' => $strpaymentmanagement, 'link' => 'index.php', 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header_simple($strpaymentmanagement, "", $navigation);

/// If orderid is empty, user wants to see all orders
    if (empty($orderid)) {
        authorize_print_orders($courseid, $userid);
    } else {
        authorize_print_order_details($orderid);
    }

/// Print footer
    print_footer();
?>
