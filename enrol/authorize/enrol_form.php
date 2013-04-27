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
 * Authorize.Net enrol plugin implementation.
 *
 * @package    enrol_authorize
 * @copyright  2010 Eugene Venter
 * @author     Eugene Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class enrol_authorize_form extends moodleform
{
    protected $instance;

    function definition() {
        global $USER;

        $mform = $this->_form;
        $this->instance = $this->_customdata;
        $plugin = enrol_get_plugin('authorize');

        $paymentmethodsenabled = get_list_of_payment_methods();
        $paymentmethod = optional_param('paymentmethod', $paymentmethodsenabled[0], PARAM_ALPHA);
        if (!in_array($paymentmethod, $paymentmethodsenabled)) {
            print_error('invalidpaymentmethod', '', '', $paymentmethod);
        }

        $othermethodstr = $this->other_method_available($paymentmethod);
        if ($othermethodstr) {
            $mform->addElement('static', '', '<div class="mdl-right">' . $othermethodstr . '</div>', '');
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $this->instance->courseid);

        $mform->addElement('hidden', 'instanceid');
        $mform->setType('instanceid', PARAM_INT);
        $mform->setDefault('instanceid', $this->instance->id);

        $mform->addElement('hidden', 'paymentmethod', $paymentmethod);
        $mform->setType('paymentmethod', PARAM_ALPHA);

        $firstlastnamestr = (AN_METHOD_CC == $paymentmethod) ? get_string('nameoncard', 'enrol_authorize') : get_string('echeckfirslasttname', 'enrol_authorize');
        $mform->addElement('text', 'firstname', get_string('firstnameoncard', 'enrol_authorize'), 'size="16"');
        $mform->addElement('text', 'lastname', get_string('lastnameoncard', 'enrol_authorize'), 'size="16"');
        $mform->addRule('firstname', get_string('missingfirstname'), 'required', null, 'client');
        $mform->addRule('lastname', get_string('missinglastname'), 'required', null, 'client');
        $mform->setType('firstname', PARAM_ALPHANUM);
        $mform->setType('lastname', PARAM_ALPHANUM);
        $mform->setDefault('firstname', $USER->firstname);
        $mform->setDefault('lastname', $USER->lastname);

        if (AN_METHOD_CC == $paymentmethod)
        {
            $mform->addElement('passwordunmask', 'cc', get_string('ccno', 'enrol_authorize'), 'size="20"');
            $mform->setType('cc', PARAM_ALPHANUM);
            $mform->setDefault('cc', '');
            $mform->addRule('cc', get_string('missingcc', 'enrol_authorize'), 'required', null, 'client');
            $mform->addRule('cc', get_string('ccinvalid', 'enrol_authorize'), 'numeric', null, 'client');

            $monthsmenu = array('' => get_string('choose'));
            for ($i = 1; $i <= 12; $i++) {
                $monthsmenu[$i] = userdate(gmmktime(12, 0, 0, $i, 15, 2000), "%B");
            }
            $nowdate = getdate();
            $startyear = $nowdate["year"] - 1;
            $endyear = $startyear + 20;
            $yearsmenu = array('' => get_string('choose'));
            for ($i = $startyear; $i < $endyear; $i++) {
                $yearsmenu[$i] = $i;
            }
            $mform->addElement('select', 'ccexpiremm', get_string('expiremonth', 'enrol_authorize'), $monthsmenu);
            $mform->addElement('select', 'ccexpireyyyy', get_string('expireyear', 'enrol_authorize'), $yearsmenu);
            $mform->addRule('ccexpiremm', get_string('missingccexpiremonth', 'enrol_authorize'), 'required', null, 'client');
            $mform->addRule('ccexpireyyyy', get_string('missingccexpireyear', 'enrol_authorize'), 'required', null, 'client');
            $mform->setType('ccexpiremm', PARAM_INT);
            $mform->setType('ccexpireyyyy', PARAM_INT);
            $mform->setDefault('ccexpiremm', '');
            $mform->setDefault('ccexpireyyyy', '');

            $creditcardsmenu = array('' => get_string('choose')) + get_list_of_creditcards();
            $mform->addElement('select', 'cctype', get_string('cctype', 'enrol_authorize'), $creditcardsmenu);
            $mform->setType('cctype', PARAM_ALPHA);
            $mform->addRule('cctype', get_string('missingcctype', 'enrol_authorize'), 'required', null, 'client');
            $mform->setDefault('cctype', '');

            $mform->addElement('text', 'cvv', get_string('ccvv', 'enrol_authorize'), 'size="4"');
            $mform->setType('cvv', PARAM_ALPHANUM);
            $mform->setDefault('cvv', '');
            $mform->addRule('cvv', get_string('missingcvv', 'enrol_authorize'), 'required', null, 'client');
            $mform->addRule('cvv', get_string('missingcvv', 'enrol_authorize'), 'numeric', null, 'client');

            if ($plugin->get_config('an_authcode')) {
                $ccauthgrp = array();
                $ccauthgrp[] = &$mform->createElement('checkbox', 'haveauth', null, get_string('haveauthcode', 'enrol_authorize'));
                $ccauthgrp[] = &$mform->createElement('static', 'nextline', null, '<br />');
                $ccauthgrp[] = &$mform->createElement('text', 'ccauthcode', '', 'size="8"');
                $mform->addGroup($ccauthgrp, 'ccauthgrp', get_string('authcode', 'enrol_authorize'), '&nbsp;', false);

                $ccauthgrprules = array();
                $ccauthgrprules['ccauthcode'][] = array(get_string('missingccauthcode', 'enrol_authorize'), 'numeric', null, 'client');
                $mform->addGroupRule('ccauthgrp', $ccauthgrprules);
                $mform->setDefault('haveauth', '');
                $mform->setDefault('ccauthcode', '');
            }

            if ($plugin->get_config('an_avs')) {
                $mform->addElement('header', 'addressheader', '&nbsp;&nbsp;' . get_string('address'), '');

                $mform->addElement('text', 'ccaddress', get_string('address'), 'size="30"');
                $mform->setType('ccaddress', PARAM_ALPHANUM);
                $mform->setDefault('ccaddress', $USER->address);
                $mform->addRule('ccaddress', get_string('missingaddress', 'enrol_authorize'), 'required', null, 'client');

                $mform->addElement('text', 'cccity', get_string('cccity', 'enrol_authorize'), 'size="14"');
                $mform->addElement('text', 'ccstate', get_string('ccstate', 'enrol_authorize'), 'size="8"');
                $mform->addRule('cccity', get_string('missingcity'), 'required', null, 'client');
                $mform->setType('cccity', PARAM_ALPHANUM);
                $mform->setType('ccstate', PARAM_ALPHANUM);
                $mform->setDefault('cccity', $USER->city);
                $mform->setDefault('ccstate', '');

                $mform->addElement('select', 'cccountry', get_string('country'), get_string_manager()->get_list_of_countries());
                $mform->addRule('cccountry', get_string('missingcountry'), 'required', null, 'client');
                $mform->setType('cccountry', PARAM_ALPHA);
                $mform->setDefault('cccountry', $USER->country);
            }
            else {
                $mform->addElement('hidden', 'ccstate', '');
                $mform->setType('ccstate', PARAM_ALPHANUM);
                $mform->addElement('hidden', 'ccaddress', $USER->address);
                $mform->setType('ccaddress', PARAM_ALPHANUM);
                $mform->addElement('hidden', 'cccity', $USER->city);
                $mform->setType('cccity', PARAM_ALPHANUM);
                $mform->addElement('hidden', 'cccountry', $USER->country);
                $mform->setType('ccountry', PARAM_ALPHA);
                $mform->setDefault('cccountry', $USER->country);
            }
        } elseif (AN_METHOD_ECHECK == $paymentmethod) {
            $mform->addElement('text', 'abacode', get_string('echeckabacode', 'enrol_authorize'), 'size="9" maxlength="9"');
            $mform->setType('abacode', PARAM_ALPHANUM);
            $mform->setDefault('abacode', '');
            $mform->addRule('abacode', get_string('missingaba', 'enrol_authorize'), 'required', null, 'client');
            $mform->addRule('abacode', get_string('missingaba', 'enrol_authorize'), 'numeric', null, 'client');

            $mform->addElement('text', 'accnum', get_string('echeckaccnum', 'enrol_authorize'), 'size="20" maxlength="20"');
            $mform->setType('accnum', PARAM_ALPHANUM);
            $mform->setDefault('accnum', '');
            $mform->addRule('accnum', get_string('invalidaccnum', 'enrol_authorize'), 'required', null, 'client');
            $mform->addRule('accnum', get_string('invalidaccnum', 'enrol_authorize'), 'numeric', null, 'client');

            $acctypes = array();
            $acctypesenabled = get_list_of_bank_account_types();
            foreach ($acctypesenabled as $key) {
                $acctypes[$key] = get_string("echeck".strtolower($key), "enrol_authorize");
            }
            $acctypes = array('' => get_string('choose')) + $acctypes;
            $mform->addElement('select', 'acctype', get_string('echeckacctype', 'enrol_authorize'), $acctypes);
            $mform->setType('acctype', PARAM_ALPHA);
            $mform->addRule('acctype', get_string('invalidacctype', 'enrol_authorize'), 'required', null, 'client');
            $mform->setDefault('acctype', '');

            $mform->addElement('text', 'bankname', get_string('echeckbankname', 'enrol_authorize'), 'size="20" maxlength="50"');
            $mform->setType('bankname', PARAM_ALPHANUM);
            $mform->setDefault('bankname', '');
            $mform->addRule('bankname', get_string('missingbankname', 'enrol_authorize'), 'required', null, 'client');
        }

        $mform->addElement('text', 'cczip', get_string('zipcode', 'enrol_authorize'), 'size="5"');
        $mform->setType('cczip', PARAM_ALPHANUM);
        $mform->setDefault('cczip', '');
        $mform->addRule('cczip', get_string('missingzip', 'enrol_authorize'), 'required', null, 'client');

        $this->add_action_buttons(false, get_string('sendpaymentbutton', 'enrol_authorize'));
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $plugin = enrol_get_plugin('authorize');

        if (AN_METHOD_CC == $data['paymentmethod'])
        {
            if (!in_array($data['cctype'], array_keys(get_list_of_creditcards()))) {
                $errors['cctype'] = get_string('missingcctype', 'enrol_authorize');
            }

            $expdate = sprintf("%02d", intval($data['ccexpiremm'])) . $data['ccexpireyyyy'];
            $validcc = $this->validate_cc($data['cc'], $data['cctype'], $expdate);
            if (!$validcc) {
                if ($validcc === 0) {
                    $errors['ccexpiremm'] = get_string('ccexpired', 'enrol_authorize');
                }
                else {
                    $errors['cc'] = get_string('ccinvalid', 'enrol_authorize');
                }
            }

            if ($plugin->get_config('an_authcode') && !empty($data['haveauth']) && empty($data['ccauthcode'])) {
                $errors['ccauthgrp'] = get_string('missingccauthcode', 'enrol_authorize');
            }
        }
        elseif (AN_METHOD_ECHECK == $data['paymentmethod'])
        {
            if (!$this->validate_aba($data['abacode'])) {
                $errors['abacode'] = get_string('invalidaba', 'enrol_authorize');
            }

            if (!in_array($data['acctype'], get_list_of_bank_account_types())) {
                $errors['acctype'] = get_string('invalidacctype', 'enrol_authorize');
            }
        }

        return $errors;
    }

    private function other_method_available($currentmethod)
    {

        if ($currentmethod == AN_METHOD_CC) {
            $otheravailable = in_array(AN_METHOD_ECHECK, get_list_of_payment_methods());
            $url = 'index.php?id='.$this->instance->courseid.'&amp;paymentmethod='.AN_METHOD_ECHECK;
            $stringtofetch = 'usingecheckmethod';
        } else {
            $otheravailable = in_array(AN_METHOD_CC, get_list_of_payment_methods());
            $url = 'index.php?id='.$this->instance->courseid.'&amp;paymentmethod='.AN_METHOD_CC;
            $stringtofetch = 'usingccmethod';
        }
        if ($otheravailable) {
            $a = new stdClass;
            $a->url = $url;
            return get_string($stringtofetch, "enrol_authorize", $a);
        }
        else {
            return '';
        }
    }

    private function validate_aba($aba)
    {
        if (preg_match("/^[0-9]{9}$/", $aba)) {
            $n = 0;
            for($i = 0; $i < 9; $i += 3) {
                $n += (substr($aba, $i, 1) * 3) + (substr($aba, $i + 1, 1) * 7) + (substr($aba, $i + 2, 1));
            }
            if ($n != 0 and $n % 10 == 0) {
                return true;
            }
        }
        return false;
    }

    private function validate_cc($Num, $Name = "n/a", $Exp = "")
    {
        // Check the expiration date first
        if (strlen($Exp))
        {
            $Month = substr($Exp, 0, 2);
            $Year  = substr($Exp, -2);
            $WorkDate = "$Month/01/$Year";
            $WorkDate = strtotime($WorkDate);
            $LastDay  = date("t", $WorkDate);
            $Expires  = strtotime("$Month/$LastDay/$Year 11:59:59");
            if ($Expires < time()) return 0;
        }

        //  Innocent until proven guilty
        $GoodCard = true;

        //  Get rid of any non-digits
        $Num = preg_replace("/[^0-9]~/", "", $Num);

        // Perform card-specific checks, if applicable
        switch ($Name)
        {
            case "mcd" :
                $GoodCard = preg_match("/^5[1-5].{14}$/", $Num);
                break;

            case "vis" :
                $GoodCard = preg_match("/^4.{15}$|^4.{12}$/", $Num);
                break;

            case "amx" :
                $GoodCard = preg_match("/^3[47].{13}$/", $Num);
                break;

            case "dsc" :
                $GoodCard = preg_match("/^6011.{12}$/", $Num);
                break;

            case "dnc" :
                $GoodCard = preg_match("/^30[0-5].{11}$|^3[68].{12}$/", $Num);
                break;

            case "jcb" :
                $GoodCard = preg_match("/^3.{15}$|^2131|1800.{11}$/", $Num);
                break;

            case "dlt" :
                $GoodCard = preg_match("/^4.{15}$/", $Num);
                break;

            case "swi" :
                $GoodCard = preg_match("/^[456].{15}$|^[456].{17,18}$/", $Num);
                break;

            case "enr" :
                $GoodCard = preg_match("/^2014.{11}$|^2149.{11}$/", $Num);
                break;
        }

        // The Luhn formula works right to left, so reverse the number.
        $Num = strrev($Num);
        $Total = 0;

        for ($x=0; $x < strlen($Num); $x++)
        {
            $digit = substr($Num, $x, 1);

            // If it's an odd digit, double it
            if ($x/2 != floor($x/2)) {
                $digit *= 2;

                // If the result is two digits, add them
                if (strlen($digit) == 2)
                $digit = substr($digit, 0, 1) + substr($digit, 1, 1);
            }
            // Add the current digit, doubled and added if applicable, to the Total
            $Total += $digit;
        }

        // If it passed (or bypassed) the card-specific check and the Total is
        // evenly divisible by 10, it's cool!
        return ($GoodCard && $Total % 10 == 0);
    }

}

