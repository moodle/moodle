<?PHP // $Id$

    require_once '../../config.php';
    require_once 'lib.php';

    if (! $site = get_site()) {
        error("Could not find a site!");
    }

    require_login();

    if (isguest()) {
        error("Guests cannot use this page.");
    }

    $strs = get_strings(array('user','status','action','delete','time','course','confirm','yes','no','none','error'));
    $authstrs = get_strings(array('paymentmanagement','orderid','void','capture','refund','delete',
                'authcaptured','authorizedpendingcapture','capturedpendingsettle','capturedsettled',
                'settled','refunded','cancelled','expired','tested',
                'transid','settlementdate','notsettled','amount',
                'howmuch','captureyes','unenrolstudent'), 'enrol_authorize');

    print_header("$site->shortname: $authstrs->paymentmanagement",
                 "$site->fullname",
                 "<a href=\"index.php\">$authstrs->paymentmanagement</a>", "");

    $orderid = optional_param('order', 0, PARAM_INT);

    if (!empty($orderid)) {
        print_authorize_order_details($orderid);
    }
    else {
        print_authorize_orders();
    }

    print_footer();
?>
