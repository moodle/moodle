<?php

// This file defines settingpages and externalpages under the "Payment" category

$ADMIN->add('payment', new admin_externalpage(
    'paymentaccounts',
    new lang_string('paymentaccounts', 'payment'),
    new moodle_url("/payment/accounts.php"),
    ['moodle/payment:manageaccounts', 'moodle/payment:viewpayments']));
