<?php // $Id$

require_once($CFG->libdir.'/formslib.php');

class authorize_enrol_form extends moodleform
{
    function definition()
    {
        global $course;
        global $CFG, $USER;

        $paymentmethodsenabled = get_list_of_payment_methods();
        $paymentmethod = optional_param('paymentmethod', $paymentmethodsenabled[0], PARAM_ALPHA);
        if (!in_array($paymentmethod, $paymentmethodsenabled)) {
            error("Invalid payment method: $paymentmethod");
        }

        $mform =& $this->_form;
        $renderer =& $mform->defaultRenderer();

        $mform->addElement('header', '', '&nbsp;&nbsp;' . get_string('paymentrequired'), '');

        $mform->addElement('hidden', 'id', $course->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'paymentmethod', $paymentmethod);
        $mform->setType('paymentmethod', PARAM_ALPHA);

        $firstlastnamestr = (AN_METHOD_CC == $paymentmethod) ?
                             get_string('nameoncard', 'enrol_authorize') : get_string('echeckfirslasttname', 'enrol_authorize');
        $firstlastnamegrp = array();
        $firstlastnamegrp[] = &MoodleQuickForm::createElement('text', 'firstname', '', 'size="10"');
        $firstlastnamegrp[] = &MoodleQuickForm::createElement('text', 'lastname', '', 'size="10"');
        $mform->addGroup($firstlastnamegrp, 'firstlastgrp', $firstlastnamestr, '&nbsp;', false);
        $firstlastnamegrprules = array();
        $firstlastnamegrprules['firstname'][] = array(get_string('missingfirstname'), 'required', null, 'client');
        $firstlastnamegrprules['lastname'][] = array(get_string('missinglastname'), 'required', null, 'client');
        $mform->addGroupRule('firstlastgrp', $firstlastnamegrprules);
        $mform->setType('firstname', PARAM_ALPHANUM);
        $mform->setType('lastname', PARAM_ALPHANUM);
        $mform->setDefault('firstname', $USER->firstname);
        $mform->setDefault('lastname', $USER->lastname);

        if (AN_METHOD_CC == $paymentmethod)
        {
            $mform->addElement('text', 'cc', get_string('ccno', 'enrol_authorize'), 'size="16"');
            $mform->setType('cc', PARAM_ALPHANUM);
            $mform->setDefault('cc', '');
            $mform->addRule('cc', get_string('missingcc', 'enrol_authorize'), 'required', null, 'client');
            $mform->addRule('cc', get_string('ccinvalid', 'enrol_authorize'), 'numeric', null, 'client');

            $creditcardsmenu = array('' => get_string('choose')) + get_list_of_creditcards();
            $mform->addElement('select', 'cctype', get_string('cctype', 'enrol_authorize'), $creditcardsmenu);
            $mform->setType('cctype', PARAM_ALPHA);
            $mform->addRule('cctype', get_string('missingcctype', 'enrol_authorize'), 'required', null, 'client');
            $mform->setDefault('cctype', '');

            $monthsmenu = array('' => get_string('choose'));
            for ($i = 1; $i <= 12; $i++) {
                $monthsmenu[$i] = userdate(gmmktime(12, 0, 0, $i, 1, 2000), "%B");
            }
            $yearsmenu = array('' => get_string('choose'));
            $nowdate = getdate();
            $nowyear = $nowdate["year"] - 1;
            for ($i = $nowyear; $i <= $nowyear + 11; $i++) {
                $yearsmenu[$i] = $i;
            }
            $ccexpiregrp = array();
            $ccexpiregrp[] = &MoodleQuickForm::createElement('select', 'ccexpiremm', '', $monthsmenu);
            $ccexpiregrp[] = &MoodleQuickForm::createElement('select', 'ccexpireyyyy', '', $yearsmenu);
            $mform->addGroup($ccexpiregrp, 'ccexpiregrp', get_string('ccexpire', 'enrol_authorize'), '&nbsp;', false);
            $ccexpiregrprules = array();
            $ccexpiregrprules['ccexpiremm'][] = array(get_string('missingccexpire', 'enrol_authorize'), 'required', null, 'client');
            $ccexpiregrprules['ccexpireyyyy'][] = array(get_string('missingccexpire', 'enrol_authorize'), 'required', null, 'client');
            $mform->addGroupRule('ccexpiregrp', $ccexpiregrprules);
            $mform->setType('ccexpiremm', PARAM_INT);
            $mform->setType('ccexpireyyyy', PARAM_INT);
            $mform->setDefault('ccexpiremm', '');
            $mform->setDefault('ccexpireyyyy', '');

            $mform->addElement('text', 'cvv', get_string('ccvv', 'enrol_authorize'), 'size="4"');
            $mform->setHelpButton('cvv', array('cvv',get_string('ccvv', 'enrol_authorize'),'enrol/authorize'), true);
            $mform->setType('cvv', PARAM_ALPHANUM);
            $mform->setDefault('cvv', '');
            $mform->addRule('cvv', get_string('missingcvv', 'enrol_authorize'), 'required', null, 'client');
            $mform->addRule('cvv', get_string('missingcvv', 'enrol_authorize'), 'numeric', null, 'client');

            if (!empty($CFG->an_authcode)) {
                $ccauthgrp = array();
                $ccauthgrp[] = &MoodleQuickForm::createElement('checkbox', 'haveauth', null, get_string('haveauthcode', 'enrol_authorize'));
                $ccauthgrp[] = &MoodleQuickForm::createElement('static', 'nextline', null, '<br />');
                $ccauthgrp[] = &MoodleQuickForm::createElement('text', 'ccauthcode', '', 'size="8"');
                $mform->addGroup($ccauthgrp, 'ccauthgrp', get_string('authcode', 'enrol_authorize'), '&nbsp;', false);
                $mform->setHelpButton('ccauthgrp', array('authcode',get_string('authcode', 'enrol_authorize'),'enrol/authorize'), true);
                $ccauthgrprules = array();
                $ccauthgrprules['ccauthcode'][] = array(get_string('missingccauthcode', 'enrol_authorize'), 'numeric', null, 'client');
                $mform->addGroupRule('ccauthgrp', $ccauthgrprules);
                $mform->setDefault('haveauth', '');
                $mform->setDefault('ccauthcode', '');
            }

            if (!empty($CFG->an_avs)) {
                $mform->addElement('header', '', '&nbsp;&nbsp;' . get_string('address'), '');

                $mform->addElement('text', 'ccaddress', get_string('address'), 'size="20"');
                $mform->setType('ccaddress', PARAM_ALPHANUM);
                $mform->setDefault('ccaddress', $USER->address);
                $mform->addRule('ccaddress', get_string('missingaddress', 'enrol_authorize'), 'required', null, 'client');

                $citystategrp = array();
                $citystategrp[] = &MoodleQuickForm::createElement('text', 'cccity', '', 'size="10"');
                $citystategrp[] = &MoodleQuickForm::createElement('text', 'ccstate', '', 'size="10"');
                $mform->addGroup($citystategrp, 'citystategrp', get_string('city') . ' / ' . get_string('state'), '&nbsp;', false);
                $citystategrprules = array();
                $citystategrprules['cccity'][] = array(get_string('missingcity'), 'required', null, 'client');
                $mform->addGroupRule('citystategrp', $citystategrprules);
                $mform->setType('cccity', PARAM_ALPHANUM);
                $mform->setType('ccstate', PARAM_ALPHANUM);
                $mform->setDefault('cccity', $USER->city);
                $mform->setDefault('ccstate', '');

                $mform->addElement('select', 'cccountry', get_string('country'), get_list_of_countries());
                $mform->addRule('cccountry', get_string('missingcountry'), 'required', null, 'client');
                $mform->setType('cccountry', PARAM_ALPHA);
                $mform->setDefault('cccountry', $USER->country);
            }
            else {
                $mform->addElement('hidden', 'ccstate', '');
                $mform->addElement('hidden', 'ccaddress', $USER->address);
                $mform->addElement('hidden', 'cccity', $USER->city);
                $mform->addElement('hidden', 'cccountry', $USER->country);
            }
        }
        elseif (AN_METHOD_ECHECK == $paymentmethod)
        {
            $mform->addElement('text', 'abacode', get_string('echeckabacode', 'enrol_authorize'), 'size="9" maxlength="9"');
            $mform->setHelpButton('abacode', array('aba',get_string('echeckabacode', 'enrol_authorize'),'enrol/authorize'), true);
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
        $mform->addRule('cczip', get_string('missingzip', 'enrol_authorize'), 'numeric', null, 'client');

        $mform->addElement('submit', 'submit', get_string('sendpaymentbutton', 'enrol_authorize'));
        $renderer->addStopFieldsetElements('submit');
    }

    function validation($data)
    {
        global $CFG;

        $errors = array();

        if (AN_METHOD_CC == $data['paymentmethod'])
        {
            if (!in_array($data['cctype'], array_keys(get_list_of_creditcards()))) {
                $errors['cctype'] = get_string('missingcctype', 'enrol_authorize');
            }

            $expdate = sprintf("%02d", intval($data['ccexpiremm'])) . $data['ccexpireyyyy'];
            $validcc = CCVal($data['cc'], $data['cctype'], $expdate);
            if (!$validcc) {
                if ($validcc === 0) {
                    $errors['ccexpiregrp'] = get_string('ccexpired', 'enrol_authorize');
                }
                else {
                    $errors['cc'] = get_string('ccinvalid', 'enrol_authorize');
                }
            }

            if (!empty($CFG->an_authcode) && !empty($data['haveauth']) && empty($data['ccauthcode'])) {
                $errors['ccauthgrp'] = get_string('missingccauthcode', 'enrol_authorize');
            }
        }
        elseif (AN_METHOD_ECHECK == $data['paymentmethod'])
        {
            if (!ABAVal($data['abacode'])) {
                $errors['abacode'] = get_string('invalidaba', 'enrol_authorize');
            }

            if (!in_array($data['acctype'], get_list_of_bank_account_types())) {
                $errors['acctype'] = get_string('invalidacctype', 'enrol_authorize');
            }
        }

        return (empty($errors) ? true : $errors);
    }

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
