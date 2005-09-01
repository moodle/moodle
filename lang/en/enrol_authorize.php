<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005081700)


$string['adminreview'] = 'Review order before capturing the credit card.';
$string['anlogin'] = 'Authorize.net: Login name';
$string['anpassword'] = 'Authorize.net: Password';
$string['anreferer'] = 'Define the URL referer if you have set up this in your authorize.net account. This will send a line \"Referer: URL\" embedded in the web request.';
$string['antestmode'] = 'Run transactions in test mode only (no money will be drawn)';
$string['antrankey'] = 'Authorize.net: Transaction Key';
$string['ccexpire'] = 'Expiry Date';
$string['ccexpired'] = 'The credit card has expired';
$string['ccinvalid'] = 'Invalid card number';
$string['ccno'] = 'Credit Card Number';
$string['cctype'] = 'Credit Card Type';
$string['ccvv'] = 'Card Verification';
$string['ccvvhelp'] = 'Look at the back of card (last 3 digits)';
$string['choosemethod'] = 'If you know enrolment key of the course, enter it; otherwise you need to paid for this course.';
$string['chooseone'] = 'Fill one or both of the following two fields';
$string['description'] = 'The Authorize.net module allows you to set up paid courses by merchants.  If the cost for any course is zero, then students are not asked to pay for entry.  There is a site-wide cost that you set here as a default for the whole site and then a course setting that you can set for each course individually. The course cost overrides the site cost.';
$string['enrolname'] = 'Authorize.net Credit Card Gateway';
$string['httpsrequired'] = 'We are sorry to inform you that your request cannot be processed currently. This site\'s configuration couldn\'t be set up correcly.
<br /><br />
Please don\'t enter your credit card number unless you see a yellow lock at the bottom of the browser. It means, it simply encrypts all data sent between client and server. So the information during the transaction between 2 computers is protected and your credit card number cannot captured over the internet.';
$string['logindesc'] = 'This option must be ON.
<br /><br />
You can set up <a href=\"$a->url\">loginhttps</a> option in Variables/Security section.
<br /><br />
Turning this on will make Moodle use a secure https connection just for the login and payment pages.';
$string['nameoncard'] = 'Name on Card';
$string['reviewday'] = 'Capture the credit card automatically unless a teacher or administrator review the order within <b>$a</b> days. CRON MUST BE ENABLED.<br />(0 day = disable autocapture = teacher,admin review it manually. Transaction will be cancelled if you disable autocapture or unless you review it within 30 days.)';
$string['reviewnotify'] = 'Your payment will be reviewed. Expect an email within a few days from your teacher.';
$string['sendpaymentbutton'] = 'Send Payment';
$string['zipcode'] = 'Zip Code';

?>
