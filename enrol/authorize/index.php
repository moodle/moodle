<?PHP // $Id$

/// Load libraries
    require_once('../../config.php');
    require_once('locallib.php');

/// Parameters
    $orderid  = optional_param('order', 0, PARAM_INT);
    $courseid = optional_param('course', SITEID, PARAM_INT);
    $userid   = optional_param('user', 0, PARAM_INT);

/// Only site users can access to this page
    require_login($courseid);

    if (isguest()) {
        error("Guests cannot use this page.");
    }

/// Load strings. All strings should be defined here. locallib.php uses these strings.
    $strs = get_strings(array('user','status','action','delete','time','course','confirm','yes','no','all','none','error'));
    $authstrs = get_strings(array('paymentmanagement','orderid','void','capture','refund','delete',
                'authcaptured','authorizedpendingcapture','capturedpendingsettle','capturedsettled',
                'settled','refunded','cancelled','expired','tested',
                'transid','settlementdate','notsettled','amount',
                'howmuch','captureyes','unenrolstudent'), 'enrol_authorize');

/// Print header
    if (!$course = get_record('course', 'id', $courseid)) {
        error('Could not find that course');
    }
    print_header_simple("$authstrs->paymentmanagement", "", "<a href=\"index.php\">$authstrs->paymentmanagement</a>");


/// If orderid is empty, user wants to see all orders
    if (empty($orderid)) {
        authorize_print_orders();
    } else {
        authorize_print_order_details($orderid);
    }

/// Print footer
    print_footer();
?>
