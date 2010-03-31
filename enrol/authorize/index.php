<?php

/// Load libraries
    require_once('../../config.php');
    require_once('const.php');
    require_once('locallib.php');
    require_once('localfuncs.php');
    require_once('authorizenet.class.php');

/// Parameters
    $orderid  = optional_param('order', 0, PARAM_INT);
    $courseid = optional_param('course', SITEID, PARAM_INT);
    $userid   = optional_param('user', 0, PARAM_INT);

    $url = new moodle_url('/enrol/authorize/index.php');
    if ($orderid !== 0) {
        $url->param('order', $orderid);
    }
    if ($courseid !== SITEID) {
        $url->param('course', $courseid);
    }
    if ($userid !== 0) {
        $url->param('user', $userid);
    }
    $PAGE->set_url($url);

/// Get course
    if (!($course = $DB->get_record('course', array('id'=>$courseid)))) {
        print_error('invalidcourseid', '', '', $courseid);
    }

/// Only SITE users can access to this page
    require_login(); // Don't use $courseid! User may want to see old orders.
    if (isguestuser()) {
        print_error('noguest');
    }

/// Load strings. All strings should be defined here. locallib.php uses these strings.
    $strs = get_strings(array('search','status','action','time','course','confirm','yes','no','cancel','all','none','error'));
    $authstrs = get_strings(array('orderid','nameoncard','echeckfirslasttname','void','capture','refund','delete',
        'allpendingorders','authcaptured','authorizedpendingcapture','capturedpendingsettle','settled',
        'refunded','cancelled','expired','underreview','approvedreview','reviewfailed','tested','new',
        'paymentmethod','methodcc','methodecheck', 'paymentmanagement', 'orderdetails', 'cclastfour', 'isbusinesschecking','shopper',
        'transid','settlementdate','notsettled','amount','unenrolstudent'), 'enrol_authorize');

/// User wants to see all orders
    if (empty($orderid)) {
        authorize_print_orders($courseid, $userid);
    }
    else {
        authorize_print_order($orderid);
    }

