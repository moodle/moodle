<?php // $Id$

class block_loancalc extends block_base {

    function init() {
        $this->title = get_string('loancalc','block_loancalc');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2007101509;
    }

    function get_content() {
        global $CFG;

        $calc = $CFG->pixpath.'/i/calc.gif';
        
        $this->content->text = '
        <script type="text/javascript">
       // <![CDATA[
    function Next()
    {
        submitScreen("Next");
        document.getElementById("vbankform").submit();
    }
    function Back()
    {
        submitScreen("Back");
        document.getElementById("vbankform").submit();
    }

    function num_format(x) { // format numbers with two digits
    sgn = (x < 0);
    x = Math.abs(x);
    x = Math.floor((x * 100) + .5);
    i = 3;
    y = "";
    while(((i--) > 0) || (x > 0)) {
        y = (x % 10) + y;
        x = Math.floor(x / 10);
        if(i == 1) {
            y = "." + y;
        }
    }
    if(sgn) {
        y = "-" + y;
    }
    return(y);
}
function comp(v) { // general entry point for all cases

    // convert all entry fields into variables
    x = document.getElementById("vbankform");
    pv = parseFloat(x.LOANAMOUNT.value);
    lpp = parseFloat(x.LOANPAYPERIOD[x.LOANPAYPERIOD.selectedIndex].value);
    if (isNaN(pv) && (v != "pv"))
    {
        x.LOANAMOUNT.select();
        x.LOANAMOUNT.focus();
        alert("Numbers only to be entered");
        return;
    }
    fv = parseFloat("0");
    yr = parseFloat(x.LOANTERM.value);
    if (isNaN(yr) && (v != "np"))
    {
        x.LOANTERM.select();
        x.LOANTERM.focus();
        alert("Numbers only to be entered");
        return;
    }
    np = lpp * yr;
    pmt = -parseFloat(x.LOANREPAYMENT.value);
    if (isNaN(pmt) && (v != "pmt"))
    {
        x.LOANREPAYMENT.select();
        x.LOANREPAYMENT.focus();
        alert("Numbers only to be entered");
        return;
    }
    if(x.LOANINTRATE.value == "") {
        alert("You must enter an interest rate (ir).");
    }
    else {
        ir = parseFloat(x.LOANINTRATE.value);
        if (isNaN(ir))
        {
            x.LOANINTRATE.select();
            x.LOANINTRATE.focus();
            alert("Numbers only to be entered");
            return;
        }
        ir = ((ir / lpp) / 100);

        // test and compute all cases

        if (v == "pv") {
            if(ir == 0) {
                pv = -(fv + (pmt * np));
            }
            else {
                q1 = Math.pow(1 + ir,-np);
                q2 = Math.pow(1 + ir,np);
                pv = -(q1 * (fv * ir - pmt + q2 * pmt))/ir;
            }
            x.LOANAMOUNT.value = num_format(pv);
        }

        if (v == "np") {
            if(ir == 0) {
                if(pmt != 0) {
                    np = - (fv + pv)/pmt;
                }
                else {
                    alert("Divide by zero error.");
                }
            }
            else {
                np = Math.log((-fv * ir + pmt)/(pmt + ir * pv))/ Math.log(1 + ir);
            }
            if(np == 0) {
                alert("Can\'t compute Number of Periods for the present values.");
            }
            else {
                np = (np / lpp)
                if (isNaN(np)) {
                    alert("The repayment amount is less than the interest. You must increase your repayments to pay off this loan!");
                } else {
                    x.LOANTERM.value = num_format(np);
                }
            }
        }

        if (v == "pmt") {
            if(ir == 0.0) {
                if(np != 0) {
                    pmt = (fv + pv)/np;
                }
                else {
                    alert("Divide by zero error.");
                }
            }
            else {
                q = Math.pow(1 + ir,np);
                pmt = ((ir * (fv + q * pv))/(-1 + q));
            }
            x.LOANREPAYMENT.value = num_format(pmt);
        }


    }
} // function comp
//]]>
</script>
<form method="post" id="vbankform" action="">
            <table>
                <tr>
                    <td colspan="2">'.get_string('amountofloan','block_loancalc').'</td>
                </tr>
                <tr>
                    <td><input name="LOANAMOUNT" id="LOANAMOUNT" size="17" /></td>
                    <td><a href="JavaScript:comp(\'pv\');"><img src="'.$calc.'" alt="calculate" /></a></td>
                </tr>
                <tr>
                    <td colspan="2">'.get_string('repaymentamount','block_loancalc').'</td>
                </tr>
                <tr>
                    <td><input name="LOANREPAYMENT" id="LOANREPAYMENT" size="17" /></td>
                    <td><a href="JavaScript:comp(\'pmt\');"><img src="'.$calc.'" alt="calculate" /></a></td>
                </tr>
                <tr>
                    <td colspan="2">'.get_string('loanterm','block_loancalc').'</td>
                </tr>
                <tr>
                    <td><input name="LOANTERM" id="LOANTERM" size="17" /></td>
                    <td><a href="JavaScript:comp(\'np\');"><img src="'.$calc.'" alt="calculate" /></a></td>
                </tr>    
                <tr>
                    <td colspan="2">'.get_string('interestrate','block_loancalc').'</td>
                </tr>
                <tr>
                    <td><input name="LOANINTRATE" id="LOANINTRATE" size="17" /></td>
                    <td></td>
                </tr>    
                <tr>
                    <td colspan="2">'.get_string('repaymentfreq','block_loancalc').'</td>
                </tr>
                <tr>
                    <td>';
        $options[52] = get_string('weekly','block_loancalc');
        $options[26] = get_string('fortnightly','block_loancalc');
        $options[12] = get_string('monthly','block_loancalc');
        $this->content->text .= choose_from_menu($options,'LOANPAYPERIOD','12',NULL,NULL,NULL,true);
        $this->content->text .= '</td>
                    <td></td>
                </tr>
            </table>
            </form>';
        $this->content->footer = '';

        return $this->content;

    }
}
?>
