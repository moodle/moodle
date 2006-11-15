<?php // $Id$

/// Prevent double paid
    prevent_double_paid($course);

/// Get payment methods enabled and use the first method as default payment method
    $paymentmethodsenabled = get_list_of_payment_methods(); // methods enabled
    $paymentmethod = optional_param('paymentmethod', $paymentmethodsenabled[0], PARAM_ALPHA); // user's payment preference

    if (!in_array($paymentmethod, $paymentmethodsenabled)) {
        error("Invalid payment method: $paymentmethod");
    }

    switch ($paymentmethod)
    {
        case AN_METHOD_CC:
        {
            print_cc_form($this);
            break;
        }

        case AN_METHOD_ECHECK:
        {
            print_echeck_form($this);
            break;
        }
    }

function print_cc_form($classreference)
{
    global $form, $course;
    global $CFG, $USER;

    $formvars = array(
        'ccaddress', 'cccity', 'ccstate', 'cccountry', 'cczip', 'ccauthcode', 'haveauth',
        'ccfirstname', 'cclastname', 'cc', 'ccexpiremm', 'ccexpireyyyy', 'cctype', 'cvv'
    );
    foreach ($formvars as $var) {
        if (!isset($form->$var)) {
            $form->$var = '';
        }
    }

    $curcost = get_course_cost($course);
    $userfirstname = empty($form->ccfirstname) ? $USER->firstname : $form->ccfirstname;
    $userlastname = empty($form->cclastname) ? $USER->lastname : $form->cclastname;
    $useraddress = empty($form->ccaddress) ? $USER->address : $form->ccaddress;
    $usercity = empty($form->cccity) ? $USER->city : $form->cccity;
    $usercountry = empty($form->cccountry) ? $USER->country : $form->cccountry;
?>
<!-- BEGIN CC -->
    <p align="center"><?php if (!empty($classreference->authorizeerrors['header'])) { formerr($classreference->authorizeerrors['header']); } ?></p>
    <div align="center">

    <p align="right"><?php print_other_method(AN_METHOD_CC) ?></p>
    <p><?php print_string("paymentrequired") ?></p>
    <p><b><?php echo get_string("cost").": $curcost[currency] $curcost[cost]"; ?></b></p>
    <p><?php print_string("paymentinstant") ?></p>

    <form name="form" method="post" action="enrol.php" autocomplete="off">
    <input type="hidden" name="id" value="<?php p($course->id) ?>" />
    <input type="hidden" name="paymentmethod" value="<?php p(AN_METHOD_CC) ?>" />
    <table align="center" width="100%" border=0>
    <tr>
      <td align="right"><?php print_string("ccno", "enrol_authorize") ?>: </td>
      <td align="left"><input type="text" name="cc" size="16" value="<?php p($form->cc) ?>" />
      <?php if (!empty($classreference->authorizeerrors['cc'])) { formerr($classreference->authorizeerrors['cc']); } ?></td>
    </tr>
    <tr>
      <td align="right"><?php print_string("nameoncard", "enrol_authorize") ?>: </td>
      <td align="left"><input type="text" name="ccfirstname" size="8" value="<?php p($userfirstname) ?>" />
      <input type="text" name="cclastname" size="8" value="<?php p($userlastname) ?>" />
      <?php if (!empty($classreference->authorizeerrors['ccfirstlast'])) { formerr($classreference->authorizeerrors['ccfirstlast']); } ?></td>
    </tr>
    <tr>
      <td align="right"><?php print_string("ccexpire", "enrol_authorize") ?>: </td>
      <td align="left"><?php
      for ($i=1; $i<=12; $i++) {
          $months[$i] = userdate(gmmktime(12,0,0,$i,1,2000), "%B");
      }
      choose_from_menu($months, 'ccexpiremm', $form->ccexpiremm);
      $nowdate = getdate();
      $nowyear = $nowdate["year"]-1;
      for ($i=$nowyear; $i<=$nowyear+11; $i++) {
          $years[$i] = $i;
      }
      choose_from_menu($years, 'ccexpireyyyy', $form->ccexpireyyyy);
      if (!empty($classreference->authorizeerrors['ccexpire'])) { formerr($classreference->authorizeerrors['ccexpire']); }
      ?></td>
    </tr>
    <tr>
      <td align="right"><?php print_string("cctype", "enrol_authorize") ?>: </td>
      <td align="left"><?php
      choose_from_menu(get_list_of_creditcards(), 'cctype', $form->cctype);
      if (!empty($classreference->authorizeerrors['cctype'])) { formerr($classreference->authorizeerrors['cctype']); }
      ?>
    </td>
    </tr>
    <tr>
      <td align="right"><?php print_string("ccvv", "enrol_authorize") ?>: </td>
      <td align="left"><input type="text" name="cvv" size="4" maxlength="4" value="<?php p($form->cvv) ?>" />
      <?php helpbutton('cvv', '', 'enrol/authorize'); ?>
      <?php if (!empty($classreference->authorizeerrors['cvv'])) { formerr($classreference->authorizeerrors['cvv']); } ?></td>
    </tr>

    <?php if (!empty($CFG->an_authcode)) : /* Authorization Code */ ?>
    <tr>
      <td align="right" valign="top"><?php print_string("authcode", "enrol_authorize") ?>: </td>
      <td align="left"><?php print_checkbox('haveauth', '1', !empty($form->haveauth), get_string("haveauthcode", "enrol_authorize")) ?>
      <?php helpbutton('authcode', '', 'enrol/authorize'); ?><br />
      <input type="text" name="ccauthcode" size="8" value="<?php p($form->ccauthcode) ?>" />
      <?php if (!empty($classreference->authorizeerrors['ccauthcode'])) { formerr($classreference->authorizeerrors['ccauthcode']); } ?></td>
    </tr>
    <?php endif; ?>

    <?php if (!empty($CFG->an_avs)) : /* Address Verification System */ ?>
    <tr>
      <td align="right"><?php print_string("address") ?>: </td>
      <td align="left"><input type="text" name="ccaddress" size="32" value="<?php p($useraddress) ?>" />
      <?php if (!empty($classreference->authorizeerrors['ccaddress'])) { formerr($classreference->authorizeerrors['ccaddress']); } ?></td>
    </tr>
    <tr>
      <td align="right"><?php print_string("city") ?> / <?php print_string("state") ?>: </td>
      <td align="left"><input type="text" name="cccity" size="16" value="<?php p($usercity) ?>" /> /
      <input type="text" name="ccstate" size="2" maxlength="2" value="<?php p($form->ccstate) ?>" />
      <?php if (!empty($classreference->authorizeerrors['cccity'])) { formerr($classreference->authorizeerrors['cccity']); } ?></td>
    </tr>
    <tr>
      <td align="right"><?php print_string("country") ?>: </td>
      <td align="left"><?php choose_from_menu(get_list_of_countries(), "cccountry", $usercountry, get_string("selectacountry")."..."); ?>
      <?php if (!empty($classreference->authorizeerrors['cccountry'])) { formerr($classreference->authorizeerrors['cccountry']); } ?></td>
    </tr>
    <?php else: /* not AVS */ ?>
    <tr>
    <td colspan="2">
      <input type="hidden" name="ccstate" value="" />
      <input type="hidden" name="ccaddress" value="<?php p($useraddress) ?>" />
      <input type="hidden" name="cccity" value="<?php p($usercity) ?>" />
      <input type="hidden" name="cccountry" value="<?php p($usercountry) ?>" />
    </td>
    </tr>
    <?php endif; ?>

    <tr>
      <td align="right"><?php print_string("zipcode", "enrol_authorize") ?>: </td>
      <td align="left"><input type="text" name="cczip" size="5" value="<?php p($form->cczip) ?>" />
      <?php if (!empty($classreference->authorizeerrors['cczip'])) { formerr($classreference->authorizeerrors['cczip']); } ?></td>
    </tr>
    </table>
    <input type="submit" value="<?php print_string("sendpaymentbutton", "enrol_authorize") ?>">
    </form>
    </div>
<!-- END CC -->
<?php
}

function print_echeck_form($classreference)
{
        global $form, $course;
        global $CFG, $USER;

        $formvars = array('abacode', 'accnum', 'acctype', 'bankname', 'firstname', 'lastname');
        foreach ($formvars as $var) {
            if (!isset($form->$var)) {
                $form->$var = '';
            }
        }

        $curcost = get_course_cost($course);
        $userfirstname = empty($form->firstname) ? $USER->firstname : $form->firstname;
        $userlastname = empty($form->lastname) ? $USER->lastname : $form->lastname;
?>
<!-- BEGIN ECHECK -->
    <p align="center"><?php if (!empty($classreference->authorizeerrors['header'])) { formerr($classreference->authorizeerrors['header']); } ?></p>
    <div align="center">

    <p align="right"><?php print_other_method(AN_METHOD_ECHECK) ?></p>
    <p><?php print_string("paymentrequired") ?></p>
    <p><b><?php echo get_string("cost").": $curcost[currency] $curcost[cost]"; ?></b></p>
    <p><?php print_string("paymentinstant") ?></p>

    <form name="form" method="post" action="enrol.php" autocomplete="off">
    <input type="hidden" name="id" value="<?php p($course->id) ?>" />
    <input type="hidden" name="paymentmethod" value="<?php p(AN_METHOD_ECHECK) ?>" />
    <table align="center" width="100%" border=0>
    <tr>
      <td align="right"><?php print_string("echeckabacode", "enrol_authorize") ?>: </td>
      <td align="left"><input type="text" name="abacode" size="9" maxlength="9" value="<?php p($form->abacode) ?>" />
      <?php helpbutton('aba', '', 'enrol/authorize'); ?>
      <?php if (!empty($classreference->authorizeerrors['abacode'])) { formerr($classreference->authorizeerrors['abacode']); } ?></td>
    </tr>
    <tr>
      <td align="right"><?php print_string("echeckaccnum", "enrol_authorize") ?>: </td>
      <td align="left"><input type="text" name="accnum" size="20" maxlength="20" value="<?php p($form->accnum) ?>" />
      <?php if (!empty($classreference->authorizeerrors['accnum'])) { formerr($classreference->authorizeerrors['accnum']); } ?></td>
    </tr>
    <tr>
      <td align="right"><?php print_string("echeckacctype", "enrol_authorize") ?>: </td>
      <td align="left"><?php
      $acctypes = array();
      $acctypesenabled = get_list_of_bank_account_types();
      foreach ($acctypesenabled as $key) {
          $acctypes[$key] = get_string("echeck".strtolower($key), "enrol_authorize");
      }
      choose_from_menu($acctypes, 'acctype', $form->acctype);
      if (!empty($classreference->authorizeerrors['acctype'])) { formerr($classreference->authorizeerrors['acctype']); }
      ?>
    </td>
    </tr>
    <tr>
      <td align="right"><?php print_string("echeckbankname", "enrol_authorize") ?>: </td>
      <td align="left"><input type="text" name="bankname" size="20" maxlength="50" value="<?php p($form->bankname) ?>" />
      <?php if (!empty($classreference->authorizeerrors['bankname'])) { formerr($classreference->authorizeerrors['bankname']); } ?></td>
    </tr>
    <tr>
      <td align="right"><?php print_string("echeckfirslasttname", "enrol_authorize") ?>: </td>
      <td align="left"><input type="text" name="firstname" size="8" value="<?php p($userfirstname) ?>" />
      <input type="text" name="lastname" size="8" value="<?php p($userlastname) ?>" />
      <?php if (!empty($classreference->authorizeerrors['firstlast'])) { formerr($classreference->authorizeerrors['firstlast']); } ?></td>
    </tr>
    </table>
    <input type="submit" value="<?php print_string("sendpaymentbutton", "enrol_authorize") ?>">
    </form>
    </div>
<!-- END ECHECK -->
<?php
}

function print_other_method($currentmethod)
{
    global $course;

    if ($currentmethod == AN_METHOD_CC) {
        $otheravailable = in_array(AN_METHOD_ECHECK, get_list_of_payment_methods());
        $url = 'enrol.php?id='.$course->id.'&amp;paymentmethod='.AN_METHOD_ECHECK;
        $stringtofetch = 'usingecheckmethod';
    }
    else {
        $otheravailable = in_array(AN_METHOD_CC, get_list_of_payment_methods());
        $url = 'enrol.php?id='.$course->id.'&amp;paymentmethod='.AN_METHOD_CC;
        $stringtofetch = 'usingccmethod';
    }
    if ($otheravailable) {
        $a = new stdClass;
        $a->url = $url;
        print_string($stringtofetch, "enrol_authorize", $a);
    }
}
?>
