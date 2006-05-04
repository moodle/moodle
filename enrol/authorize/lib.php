<?PHP // $Id$

/**
 * backup_enrol_info
 *
 * @param resource $bf
 * @param object $prefs
 * @return bool
 */
function backup_enrol_info($bf, $prefs)
{
    global $CFG;

    $status = true;

    if ($orders = get_records_select('enrol_authorize', "courseid=$prefs->backup_course AND status!=0 AND transid!=0"))
    {
        fwrite($bf, start_tag("AUTHORIZEORDERS", 2 ,true));
        foreach ($orders as $order) {
            fwrite($bf, start_tag("ORDER", 3, true));
            fwrite($bf, full_tag("ID", 4, false, $order->id));
            fwrite($bf, full_tag("CCLASTFOUR", 4, false, $order->cclastfour));
            fwrite($bf, full_tag("CCNAME", 4, false, $order->ccname));
            fwrite($bf, full_tag("COURSEID", 4, false, $order->courseid));
            fwrite($bf, full_tag("USERID", 4, false, $order->userid));
            fwrite($bf, full_tag("TRANSID", 4, false, $order->transid));
            fwrite($bf, full_tag("STATUS", 4, false, $order->status));
            fwrite($bf, full_tag("TIMECREATED", 4, false, $order->timecreated));
            fwrite($bf, full_tag("SETTLETIME", 4, false, $order->settletime));
            fwrite($bf, full_tag("AMOUNT", 4, false, $order->amount));
            fwrite($bf, full_tag("CURRENCY", 4, false, $order->currency));
            if ($refunds = get_records_select("enrol_authorize_refunds", "orderid=$order->id AND status!=0 AND transid!=0")) {
                fwrite($bf, start_tag("REFUNDS",4, true));
                foreach ($refunds as $refund) {
                    fwrite($bf, start_tag("REFUND", 5, true));
                    fwrite($bf, full_tag("ID", 6, false, $refund->id));
                    fwrite($bf, full_tag("ORDERID", 6, false, $refund->orderid));
                    fwrite($bf, full_tag("STATUS", 6, false, $refund->status));
                    fwrite($bf, full_tag("AMOUNT", 6, false, $refund->amount));
                    fwrite($bf, full_tag("TRANSID", 6, false, $refund->transid));
                    fwrite($bf, full_tag("SETTLETIME", 6, false, $refund->settletime));
                    fwrite($bf, end_tag("REFUND", 5, true));
                }
                $status = fwrite($bf, end_tag("REFUNDS", 4, true));
            }
            fwrite ($bf, end_tag("ORDER", 3, true));
        }
        $status = fwrite($bf, end_tag("AUTHORIZEORDERS", 2, true));
    }
    return $status;
}


function restore_enrol_info($restore, $xml)
{
    global $CFG;

    $orders = restore_read_xml($xml, "AUTHORIZEORDERS", $restore);

    //print "******************";
    //var_dump($xml);
    //var_dump($orders);
    //print "******************";

    if ($orders && $orders !== true) {
        foreach ($orders as $order) {


            //print_r($order);

            //We'll need this later!!
            $olduserid = backup_todb($order['ORDER']['#']['USERID']['0']['#']); //To recode userid
            $oldordid = backup_todb($order['ORDER']['#']['ID']['0']['#']); //To store order pairs to backup_ids

            $ord = new stdClass();
            $ord->courseid = $restore->course_id;  //We get new course id from $restore

            $ord->cclastfour = backup_todb($order['ORDER']['#']['CCLASTFOUR']['0']['#']);
            $ord->ccname = backup_todb($order['ORDER']['#']['CCNAME']['0']['#']);
            $ord->transid = backup_todb($order['ORDER']['#']['TRANSID']['0']['#']);
            $ord->status = backup_todb($order['ORDER']['#']['STATUS']['0']['#']);
            $ord->timecreated = backup_todb($order['ORDER']['#']['TIMECREATED']['0']['#']);
            $ord->settletime = backup_todb($order['ORDER']['#']['SETTLETIME']['0']['#']);
            $ord->amount = backup_todb($order['ORDER']['#']['AMOUNT']['0']['#']);
            $ord->currency = backup_todb($order['ORDER']['#']['CURRENCY']['0']['#']);

            //Now we recode userid looking in the backup_ids table
            $user = backup_getid($restore->backup_unique_code, "user", $olduserid);
            if ($user) {
                $ord->userid = $user->new_id;
            }

            $newordid = insert_record("enrol_authorize", $ord);

            //We put ordid pair (oldordid, newordid) to backup_ids. Refunds restore will need them.
            if ($newordid) {
                backup_putid($restore->backup_unique_code, "enrol_authorize", $oldordid, $newordid);
            }

            $refunds = $order['ORDER']['#']['REFUNDS']['0']['#']['REFUND'];

            for($i = 0; $i < sizeof($refunds); $i++) {
                $refund_info = $refunds[$i];

                //We'll need this later
                $oldordidid = backup_todb($refund_info['#']['ID']['0']['#']);  //To recode orderid

                $refund->status = backup_todb($refund_info['#']['STATUS']['0']['#']);
                $refund->amount = backup_todb($refund_info['#']['AMOUNT']['0']['#']);
                $refund->transid = backup_todb($refund_info['#']['TRANSID']['0']['#']);
                $refund->settletime = backup_todb($refund_info['#']['SETTLETIME']['0']['#']);

                //Now we recode userid looking in the backup_ids table
                $order = backup_getid($restore->backup_unique_code,"enrol_authorize",$oldordidid);
                if ($order) {
                    $refund->orderid = $order->new_id;
                }

                $newid = insert_record("enrol_authorize_refunds", $refund);

            }
        }
    }
}
?>
