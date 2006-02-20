<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005101200)


$string['adminauthorizeccapture'] = 'Order Review & Auto-Capture Settings';
$string['adminauthorizeemail'] = 'Email Sending Settings';
$string['adminauthorizesettings'] = 'Authorize.net Settings';
$string['adminauthorizewide'] = 'Site-Wide Settings';
$string['adminavs'] = 'Check this if you have activated Address Verification System (AVS) in your authorize.net account. This demands address fields like street, state, country and zip when user fills out payment form.';
$string['admincronsetup'] = 'The cron.php maintenance script has not been run for at least 24 hours. <br />Cron must be enabled if you want to use autocapture feature.<br /><a href=\"../doc/?frame=install.html&sub=cron\">Setup cron</a> or uncheck an_review again.<br />If you disable autocapture, transactions will be cancelled unless you review them within 30 days.<br />Check an_review and enter \'0\' to an_capture_day field<br />if you want to manually accept/deny payments within 30 days.';
$string['adminemailexpired'] = 'Send warning email to admins <b>$a</b> days ago how many status of \'authorized/pending capture\' transaction is there, before transactions have expired. (0=disable sending email, default=2, max=5)<br />This is useful if you enabled capturing manually (an_review=checked, an_capture_day=0).';
$string['adminhelpcapture'] = 'Not only I want to manually accept/deny payments. But also, use autocapture to prevent cancelling payment. What will I do?

 - Setup cron.
 - Check an_review.
 - Enter a number between 1 and 29 to an_capture_day field. Card will be captured and user will be enrolled to course unless you capture it within an_capture_day.';
$string['adminhelpreview'] = 'How will I manually accept/deny payments?
- Check an_review.
- Enter \'0\' to an_capture_day field.

How students will be enrolled to courses immediately when they entered their card number?
- Uncheck an_review.';
$string['adminneworder'] = ' Dear Admin,
                
  You have received a new pending order:

   Order ID: $a->orderid
   Transaction ID: $a->transid
   User: $a->user
   Course: $a->course
   Amount: $a->amount
               
   AUTO-CAPTURE ENABLED?: $a->acstatus
                
  If auto-capture enabled the credit card will be captured on $a->captureon
  and then student will be enrolled to course, otherwise it will be expired
  on $a->expireon and cannot be captured after this day.
                
  Also you can accept/deny the payment to enrol the student immediately following this link:
  $a->url';
$string['adminnewordersubject'] = '$a->course: New Pending Order($a->orderid)';
$string['adminpendingorders'] = 'You have disabled auto-capture feature.<br />Total $a->count transactions with a status of AN_STATUS_AUTH will be cancelled unless you check it.<br />To accept/deny payments go to <a href=\'$a->url\'>Payment Management</a> page.';
$string['adminreview'] = 'Review order before processing the credit card.';
$string['amount'] = 'Amount';
$string['anlogin'] = 'Authorize.net: Login name';
$string['anpassword'] = 'Authorize.net: Password';
$string['anreferer'] = 'Define the URL referer if you have set up this in your authorize.net account. This will send a line \"Referer: URL\" embedded in the web request.';
$string['antestmode'] = 'Run transactions in test mode only (no money will be drawn)';
$string['antrankey'] = 'Authorize.net: Transaction Key';
$string['authcaptured'] = 'Authorized / Captured';
$string['authorizedpendingcapture'] = 'Authorized / Pending Capture';
$string['avsa'] = 'Address (street) matches, postal code does not';
$string['avsb'] = 'Address information not provided';
$string['avse'] = 'Address Verification System Error';
$string['avsg'] = 'Non-U.S. Card Issuing Bank';
$string['avsn'] = 'No match on address (street) nor postal code';
$string['avsp'] = 'Address Verification System not applicable';
$string['avsr'] = 'Retry - System unavailable or timed out';
$string['avsresult'] = 'AVS Result:';
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
$string['captureyes'] = 'The credit card will be captured and student will be enrolled to course. Are you sure?';
$string['ccexpire'] = 'Expiry Date';
$string['ccexpired'] = 'The credit card has expired';
$string['ccinvalid'] = 'Invalid card number';
$string['ccno'] = 'Credit Card Number';
$string['cctype'] = 'Credit Card Type';
$string['ccvv'] = 'Card Verification';
$string['ccvvhelp'] = 'Look at the back of card (last 3 digits)';
$string['choosemethod'] = 'If you know the enrolment key of the cource, please enter it; otherwise you need to pay for this course.';
$string['chooseone'] = 'Fill one or both of the following two fields';
$string['cutofftime'] = 'Transaction Cut-Off Time. When the last transaction is picked up for settlement?';
$string['delete'] = 'Destroy';
$string['description'] = 'The Authorize.net module allows you to set up paid courses via CC providers.  If the cost for any course is zero, then students are not asked to pay for entry.  Two ways to set the course cost (1) a site-wide cost as a default for the whole site or (2) a course setting that you can set for each course individually. The course cost overrides the site cost.<br /><br /><b>Note:</b> If you enter an enrolment key in the course settings, then students will also have the option to enrol using a key. This is useful if you have a mixture of paying and non-paying students. ';
$string['enrolname'] = 'Authorize.net Credit Card Gateway';
$string['expired'] = 'Expired';
$string['howmuch'] = 'How much?';
$string['httpsrequired'] = 'We are sorry to inform you that your request cannot be processed now. This site\'s configuration couldn\'t be set up correctly.
<br /><br />
Please don\'t enter your credit card number unless you see a yellow lock at the bottom of the browser. If the symbol appears, it means the page encrypts all data sent between client and server. So the information during the transaction between the two computers is protected, hence your credit card number cannot be captured over the internet.';
$string['logindesc'] = 'This option must be ON. <br /><br />
Please ensure that you have turned <a href=\"$a->url\">loginhttps ON</a> in Admin >> Variables >> Security.<br /><br />
Turning this on will make Moodle use a secure https connection just for the login and payment pages.';
$string['missingaddress'] = 'Missing address';
$string['missingcc'] = 'Missing card number';
$string['missingccexpire'] = 'Missing expiration date';
$string['missingcctype'] = 'Missing card type';
$string['missingcvv'] = 'Missing verification number';
$string['missingzip'] = 'Missing postal code';
$string['nameoncard'] = 'Name on Card';
$string['noreturns'] = 'No returns!';
$string['notsettled'] = 'Not settled';
$string['orderid'] = 'Order ID';
$string['paymentmanagement'] = 'Payment Management';
$string['paymentpending'] = 'Your payment is pending for this course with this order number $a->orderid.';
$string['pendingordersemail'] = ' Dear admin,

$a->pending transactions will be expired unless you accept payment with in $a->days days.

This is a warning message, because you didn\'t enable autocapture. It means you have to accept or deny payments manually.

To accept/deny pending payments go to:
$a->url

To enable autocapture, means you will not receive any warning emails anymore, go to:
$a->enrolurl';
$string['reason11'] = 'A duplicate transaction has been submitted.';
$string['reason13'] = 'The merchant Login ID is invalid or the account is inactive.';
$string['reason16'] = 'The transaction was not found.';
$string['reason17'] = 'The merchant does not accept this type of credit card.';
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
$string['refund'] = 'Refund';
$string['refunded'] = 'Refunded';
$string['returns'] = 'Returns';
$string['reviewday'] = 'Capture the credit card automatically unless a teacher or administrator review the order within <b>$a</b> days. CRON MUST BE ENABLED.<br />(0 day means it will disable autocapture, also means teacher or admin review order manually. Transaction will be cancelled if you disable autocapture or unless you review it within 30 days.)';
$string['reviewnotify'] = 'Your payment will be reviewed. Expect an email within a few days from your teacher.';
$string['sendpaymentbutton'] = 'Send Payment';
$string['settled'] = 'Settled';
$string['settlementdate'] = 'Settlement Date';
$string['subvoidyes'] = 'Refunded transaction $a->transid will be cancelled and it will credit $a->amount to your account. Are you sure?';
$string['tested'] = 'Tested';
$string['testmode'] = '[TEST MODE]';
$string['testwarning'] = 'Capture/Void/Credit seems working in test mode, but no record was updated or inserted in database.';
$string['transid'] = 'Transaction ID';
$string['unenrolstudent'] = 'Unenrol student?';
$string['void'] = 'Void';
$string['voidyes'] = 'Transaction will be cancelled. Are you sure?';
$string['zipcode'] = 'Zip Code';

?>
