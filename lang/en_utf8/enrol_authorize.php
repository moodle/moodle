<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.8 dev (2006112200)


$string['adminacceptccs'] = 'Which credit card types will be accepted?';
$string['adminaccepts'] = 'Select payment methods allowed and their types';
$string['adminauthcode'] = 'If an user\'s credit card cannot be captured on the internet directly, obtain authorization code over the phone from customer\'s bank.';
$string['adminauthorizeccapture'] = 'Order Review &amp; Scheduled-Capture Settings';
$string['adminauthorizeemail'] = 'Email Sending Settings';
$string['adminauthorizesettings'] = 'Authorize.net Settings';
$string['adminauthorizewide'] = 'Site-Wide Settings';
$string['adminavs'] = 'Check this if you have activated Address Verification System (AVS) in your authorize.net account. This demands address fields like street, state, country and zip when user fills out payment form.';
$string['adminconfighttps'] = 'Please ensure that you have \"<a href=\"$a->url\">turned loginhttps ON</a>\" to use this plugin<br />in Admin >> Variables >> Security >> HTTP security.';
$string['adminconfighttpsgo'] = 'Go to the <a href=\"$a->url\">secure page</a> to configure this plugin.';
$string['admincronsetup'] = 'The cron.php maintenance script has not been run for at least 24 hours.<br />Cron must be enabled if you want to use scheduled-capture feature.<br /><b>Enable</b> \'Authorize.net plugin\' and <b>setup cron</b> properly; or <b>uncheck an_review</b> again.<br />If you disable scheduled-capture, transactions will be cancelled unless you review them within 30 days.<br />Check <b>an_review</b> and enter <b>\'0\' to an_capture_day</b> field<br />if you want to <b>manually</b> accept/deny payments within 30 days.';
$string['adminemailexpired'] = 'This is useful for \'Manual-Capture\'. Admins are notified <b>$a</b> days prior to pending orders expiring.';
$string['adminemailexpiredsort'] = 'When the number of pending orders expiring are sent to the teachers via email, which one is important?';
$string['adminemailexpiredsortcount'] = 'Order count';
$string['adminemailexpiredsortsum'] = 'Total amount';
$string['adminemailexpiredteacher'] = 'If you have enabled manual-capture (see above) and teachers can manage the payments, they may also notified about pending orders expiring. This will send an email to each course teachers about the count of the pending orders to expire.';
$string['adminemailexpsetting'] = '(0=disable sending email, default=2, max=5)<br />(Manual capture settings for sending email: cron=enabled, an_review=checked, an_capture_day=0, an_emailexpired=1-5)';
$string['adminhelpcapturetitle'] = 'Scheduled-Capture Day';
$string['adminhelpreviewtitle'] = 'Order Review';
$string['adminneworder'] = 'Dear Admin,

  You have received a new pending order:

   Order ID: $a->orderid
   Transaction ID: $a->transid
   User: $a->user
   Course: $a->course
   Amount: $a->amount

   SCHEDULED-CAPTURE ENABLED?: $a->acstatus

  If the scheduled-capture is active, the credit card is to be captured on $a->captureon
  and then the user is to be enrolled to course; otherwise it will be expired
  on $a->expireon and cannot be captured after this day.

  You can also accept/deny the payment to enrol the student immediately following this link:
  $a->url';
$string['adminnewordersubject'] = '$a->course; New Pending Order: $a->orderid';
$string['adminpendingorders'] = 'You have disabled scheduled-capture feature.<br />Total $a->count transactions with the status of \'Authorized/Pending Capture\' are to be cancelled unless you check them.<br />To accept/deny payments, go to <a href=\'$a->url\'>Payment Management</a> page.';
$string['adminreview'] = 'Review order before processing the credit card.';
$string['adminteachermanagepay'] = 'Teachers can manage the payments of the course.';
$string['allpendingorders'] = 'All Pending Orders';
$string['amount'] = 'Amount';
$string['anlogin'] = 'Authorize.net: Login name';
$string['anpassword'] = 'Authorize.net: Password';
$string['anreferer'] = 'Define the URL referer if you have set up this in your authorize.net account. This will send a line \"Referer: URL\" embedded in the web request.';
$string['antestmode'] = 'Run transactions in test mode only (no money will be drawn)';
$string['antrankey'] = 'Authorize.net: Transaction Key';
$string['approvedreview'] = 'Approved Review';
$string['authcaptured'] = 'Authorized / Captured';
$string['authcode'] = 'Authorization Code';
$string['authorize:managepayments'] = 'Manage payments';
$string['authorize:uploadcsv'] = 'Upload CSV file';
$string['authorizedpendingcapture'] = 'Authorized / Pending Capture';
$string['avsa'] = 'Address (street) matches, postal code does not';
$string['avsb'] = 'Address information not provided';
$string['avse'] = 'Address Verification System Error';
$string['avsg'] = 'Non-U.S. Card Issuing Bank';
$string['avsn'] = 'No match on address (street) nor postal code';
$string['avsp'] = 'Address Verification System not applicable';
$string['avsr'] = 'Retry - System unavailable or timed out';
$string['avsresult'] = 'AVS Result: $a';
$string['avss'] = 'Service not supported by issuer';
$string['avsu'] = 'Address information is unavailable';
$string['avsw'] = '9 digit postal code matches, address (street) does not';
$string['avsx'] = 'Address (street) and 9 digit postal code match';
$string['avsy'] = 'Address (street) and 5 digit postal code match';
$string['avsz'] = '5 digit postal code matches, address (street) does not';
$string['canbecredit'] = 'Can be refunded to $a->upto';
$string['cancelled'] = 'Cancelled';
$string['capture'] = 'Capture';
$string['capturedpendingsettle'] = 'Captured / Pending Settlement';
$string['capturedsettled'] = 'Captured / Settled';
$string['captureyes'] = 'The credit card will be captured and the student will be enrolled to the course. Are you sure?';
$string['ccexpire'] = 'Expiry Date';
$string['ccexpired'] = 'The credit card has expired';
$string['ccinvalid'] = 'Invalid card number';
$string['ccno'] = 'Credit Card Number';
$string['cctype'] = 'Credit Card Type';
$string['ccvv'] = 'Card Verification';
$string['ccvvhelp'] = 'Look at the back of card (last 3 digits)';
$string['choosemethod'] = 'If you know the enrolment key of the cource, please enter it below;<br />Otherwise you need to pay for this course.';
$string['chooseone'] = 'Fill one or both of the following two fields. The password isn\'t shown.';
$string['costdefaultdesc'] = '<strong>In course settings, enter -1</strong> to use this default cost to course cost field.';
$string['cutofftime'] = 'Transaction Cut-Off Time. When the last transaction is picked up for settlement?';
$string['dataentered'] = 'Data entered';
$string['delete'] = 'Destroy';
$string['description'] = 'The Authorize.net module allows you to set up paid courses via payment providers. If the cost for any course is zero, then students are not asked to pay for entry. Two ways to set the course cost (1) a site-wide cost as a default for the whole site or (2) a course setting that you can set for each course individually. The course cost overrides the site cost.<br /><br /><b>Note:</b> If you enter an enrolment key in the course settings, then students will also have the option to enrol using a key. This is useful if you have a mixture of paying and non-paying students.';
$string['echeckabacode'] = 'Bank ABA Number';
$string['echeckaccnum'] = 'Bank Account Number';
$string['echeckacctype'] = 'Bank Account Type';
$string['echeckbankname'] = 'Bank Name';
$string['echeckbusinesschecking'] = 'Business Checking';
$string['echeckchecking'] = 'Checking';
$string['echeckfirslasttname'] = 'Bank Account Owner';
$string['echecksavings'] = 'Savings';
$string['enrolname'] = 'Authorize.net Payment Gateway';
$string['expired'] = 'Expired';
$string['haveauthcode'] = 'I have already an authorization code';
$string['howmuch'] = 'How much?';
$string['httpsrequired'] = 'We are sorry to inform you that your request cannot be processed now. This site\'s configuration couldn\'t be set up correctly.<br /><br />Please don\'t enter your credit card number unless you see a yellow lock at the bottom of the browser. If the symbol appears, it means the page encrypts all data sent between client and server. So the information during the transaction between the two computers is protected, hence your credit card number cannot be captured over the internet.';
$string['invalidaba'] = 'Invalid ABA number';
$string['invalidaccnum'] = 'Invalid account number';
$string['invalidacctype'] = 'Invalid account type';
$string['logindesc'] = 'This option must be ON. <br /><br />Please ensure that you have turned <a href=\"$a->url\">loginhttps ON</a> in Admin >> Variables >> Security.<br /><br />Turning this on will make Moodle use a secure https connection just for the login and payment pages.';
$string['logininfo'] = 'Login name, password and transaction key are not shown due to security precautions. There is no need to enter again if you have configured these fields before. You see a green text left of the box if some fields were already configured. If you enter these fields for the first time, the login name (*) is required and you must enter <strong>either</strong> the transaction key (#1) <strong>or</strong> the password (#2) in the appropriate box. We recommend you enter the transaction key due to security precautions. If you want to delete the current password, tick the checkbox.';
$string['methodcc'] = 'Credit Card';
$string['methodecheck'] = 'eCheck (ACH)';
$string['missingaba'] = 'Missing ABA number';
$string['missingaddress'] = 'Missing address';
$string['missingbankname'] = 'Missing bank name';
$string['missingcc'] = 'Missing card number';
$string['missingccauthcode'] = 'Missing authorization code';
$string['missingccexpire'] = 'Missing expiration date';
$string['missingcctype'] = 'Missing card type';
$string['missingcvv'] = 'Missing verification number';
$string['missingzip'] = 'Missing postal code';
$string['mypaymentsonly'] = 'Show my payments only';
$string['nameoncard'] = 'Name on Card';
$string['new'] = 'New';
$string['noreturns'] = 'No returns!';
$string['notsettled'] = 'Not settled';
$string['orderid'] = 'OrderID';
$string['paymentmanagement'] = 'Payment Management';
$string['paymentmethod'] = 'Payment Method';
$string['paymentpending'] = 'Your payment is pending for this course with this order number $a->orderid.  See <a href=\'$a->url\'>Order Details</a>.';
$string['pendingecheckemail'] = 'Dear manager,

There are $a->count pending echecks now and you have to upload a csv file to get the users enrolled.

Click the link and read the help file on the page seen:
$a->url';
$string['pendingechecksubject'] = '$a->course: Pending eChecks($a->count)';
$string['pendingordersemail'] = 'Dear admin,

$a->pending transactions for course \"$a->course\" will expire unless you accept payment within $a->days days.

This is a warning message, because you didn\'t enable scheduled-capture.
It means you have to accept or deny payments manually.

To accept/deny pending payments go to:
$a->url

To enable scheduled-capture, it means you will not receive any warning emails anymore, go to:

$a->enrolurl';
$string['pendingordersemailteacher'] = 'Dear teacher,

$a->pending transactions costed $a->currency $a->sumcost for course \"$a->course\"
will expire unless you accept payment with in $a->days days.

You have to accept or deny payments manually because of the admin hasn\'t enabled the scheduled-capture.

$a->url';
$string['pendingorderssubject'] = 'WARNING: $a->course, $a->pending order(s) will expire within $a->days day(s).';
$string['reason11'] = 'A duplicate transaction has been submitted.';
$string['reason13'] = 'The merchant Login ID is invalid or the account is inactive.';
$string['reason16'] = 'The transaction was not found.';
$string['reason17'] = 'The merchant does not accept this type of credit card.';
$string['reason245'] = 'This eCheck type is not allowed when using the payment gateway hosted payment form.';
$string['reason246'] = 'This eCheck type is not allowed.';
$string['reason27'] = 'The transaction resulted in an AVS mismatch. The address provided does not match billing address of cardholder.';
$string['reason28'] = 'The merchant does not accept this type of credit card.';
$string['reason30'] = 'The configuration with the processor is invalid. Call Merchant Service Provider.';
$string['reason39'] = 'The supplied currency code is either invalid, not supported, not allowed for this merchant or doesn\'t have an exchange rate.';
$string['reason43'] = 'The merchant was incorrectly set up at the processor. Call your Merchant Service Provider.';
$string['reason44'] = 'This transaction has been declined. Card Code filter error!';
$string['reason45'] = 'This transaction has been declined. Card Code / AVS filter error!';
$string['reason47'] = 'The amount requested for settlement may not be greater than the original amount authorized.';
$string['reason5'] = 'A valid amount is required.';
$string['reason50'] = 'This transaction is awaiting settlement and cannot be refunded.';
$string['reason51'] = 'The sum of all credits against this transaction is greater than the original transaction amount.';
$string['reason54'] = 'The referenced transaction does not meet the criteria for issuing a credit.';
$string['reason55'] = 'The sum of credits against the referenced transaction would exceed the original debit amount.';
$string['reason56'] = 'This merchant accepts eCheck (ACH) transactions only; no credit card transactions are accepted.';
$string['refund'] = 'Refund';
$string['refunded'] = 'Refunded';
$string['returns'] = 'Returns';
$string['reviewday'] = 'Capture the credit card automatically unless a teacher or administrator review the order within <b>$a</b> days. CRON MUST BE ENABLED.<br />(0 day means it will disable scheduled-capture, also means teacher or admin review order manually. Transaction will be cancelled if you disable scheduled-capture or unless you review it within 30 days.)';
$string['reviewfailed'] = 'Review Failed';
$string['reviewnotify'] = 'Your payment will be reviewed. Expect an email within a few days from your teacher.';
$string['sendpaymentbutton'] = 'Send Payment';
$string['settled'] = 'Settled';
$string['settlementdate'] = 'Settlement Date';
$string['subvoidyes'] = 'The transaction refunded ($a->transid) is going to be cancelled and this will cause crediting $a->amount to your account. Are you sure?';
$string['tested'] = 'Tested';
$string['testmode'] = '[TEST MODE]';
$string['testwarning'] = 'Capturing/Voiding/Refunding seems working in test mode, but no record was updated or inserted in database.';
$string['transid'] = 'TransactionID';
$string['underreview'] = 'Under Review';
$string['unenrolstudent'] = 'Unenrol student?';
$string['uploadcsv'] = 'Upload a CSV file';
$string['usingccmethod'] = 'Enrol using <a href=\"$a->url\"><strong>Credit Card</strong></a>';
$string['usingecheckmethod'] = 'Enrol using <a href=\"$a->url\"><strong>eCheck</strong></a>';
$string['void'] = 'Void';
$string['voidyes'] = 'The transaction will be cancelled. Are you sure?';
$string['welcometocoursesemail'] = 'Dear student,

Thanks for your payments. You have enrolled these courses:

$a->courses

You may edit your profile:
 $a->profileurl

You may view your payment details:
 $a->paymenturl';
$string['youcantdo'] = 'You can\'t do this action: $a->action';
$string['zipcode'] = 'Zip Code';

?>
