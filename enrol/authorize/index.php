<?PHP // $Id$

/// Load libraries
    require_once('../../config.php');
    require_once('locallib.php');

/// Parameters
    $orderid  = optional_param('order', 0, PARAM_INT);
    $courseid = optional_param('course', SITEID, PARAM_INT);
    $userid   = optional_param('user', 0, PARAM_INT);

/// Get course
    if (!$course = get_record('course', 'id', $courseid)) {
        error('Could not find that course');
    }

/// Only site users can access to this page
    require_login(); // Don't use $courseid! User may want to see old orders.

    if (isguest()) {
        error("Guests cannot use this page.");
    }

/// Load strings. All strings should be defined here. locallib.php uses these strings.
    $strs = get_strings(array('status','action','time','course','confirm','no','all','none','error'));
    $authstrs = get_strings(array('orderid','nameoncard','void','capture','refund','delete',
                'authcaptured','authorizedpendingcapture','capturedpendingsettle','capturedsettled',
                'settled','refunded','cancelled','expired','tested','new',
                'transid','settlementdate','notsettled','amount','unenrolstudent'), 'enrol_authorize');

/// Print header
    $strpaymentmanagement = get_string('paymentmanagement', 'enrol_authorize');
    print_header_simple($strpaymentmanagement, "", "<a href=\"index.php\">$strpaymentmanagement</a>");

/// If orderid is empty, user wants to see all orders
    if (empty($orderid)) {
        authorize_print_orders();
    } else {
        authorize_print_order_details($orderid);
    }

/// Print footer
    print_footer();
?>
