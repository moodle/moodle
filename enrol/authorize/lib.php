<?PHP // $Id$

/**
 * backup_authorize_info
 *
 * @param resource $bf
 * @param object $prefs
 * @return bool
 */
function backup_authorize_info($bf, $prefs)
{
    global $CFG;

    $status = true;

    if ($orders = get_records("enrol_authorize", "courseid", $prefs->backup_course))
    {
        fwrite($bf, start_tag("ORDERS", 2 ,true));
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
            if ($refunds = get_records("enrol_authorize_refunds", "orderid", $order->id)) {
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
        $status = fwrite($bf, end_tag("ORDERS", 2, true));
    }
    return $status;
}


?>
