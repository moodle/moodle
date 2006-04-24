<?PHP // $Id$

/// Load libraries
    require_once '../../config.php';
    require_once 'locallib.php';

/// Get site
    if (! $site = get_site()) {
        error("Could not find a site!");
    }

/// Only site users can access to this page
    require_login();
    if (isguest()) {
        error("Guests cannot use this page.");
    }

/// Load strings. All strings should be defined here. locallib.php uses these strings.
    $strs = get_strings(array('user','status','action','delete','time','course','confirm','yes','no','all','none','error'));
    $authstrs = get_strings(array('paymentmanagement','orderid','void','capture','refund','delete',
                'authcaptured','authorizedpendingcapture','capturedpendingsettle','capturedsettled',
                'settled','refunded','cancelled','expired','tested',
                'transid','settlementdate','notsettled','amount','returns','noreturns',
                'howmuch','captureyes','unenrolstudent'), 'enrol_authorize');

/// Print header
    print_header("$site->shortname: $authstrs->paymentmanagement",
                 "$site->fullname",
                 "<a href=\"index.php\">$authstrs->paymentmanagement</a>", "");

/// Get order id
    $orderid = optional_param('order', 0, PARAM_INT);

/// If orderid is empty, user wants to see all orders
    if (empty($orderid)) {
        authorize_print_orders();
    }
    else {
        authorize_print_order_details($orderid);
    }

/// Print footer
    print_footer();
?>
