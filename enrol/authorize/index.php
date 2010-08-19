<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Adds new instance of enrol_authorize to specified course
 * or edits current instance.
 *
 * @package    enrol
 * @subpackage authorize
 * @copyright  2010 Eugene Venter
 * @author     Eugene Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


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

