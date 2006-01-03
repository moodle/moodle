<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005101200)


$string['adminauthorizeccapture'] = 'Order Review & Auto-Capture Settings';
$string['adminauthorizeemail'] = 'Email Sending Settings';
$string['adminauthorizesettings'] = 'Authorize.net Settings';
$string['adminauthorizewide'] = 'Site-Wide Settings';
$string['admincronsetup'] = 'The cron.php maintenance script has not been run for at least 24 hours. <br />Cron must be enabled if you want to use autocapture feature.<br /><a href=\"../doc/?frame=install.html&sub=cron\">Setup cron</a> or uncheck an_review again.<br />If you disable autocapture, transactions will be cancelled unless you review them within 30 days.<br />Check an_review and enter \'0\' to an_capture_day field<br />if you want to manually accept/deny payments within 30 days.';
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
$string['authorizedpendingcapture'] = 'Authorized / Pending Capture';
$string['canbecredit'] = 'Can be refunded to $a->upto';
$string['cancelled'] = 'Cancelled';
$string['capture'] = 'Capture';
$string['capturedpendingsettle'] = 'Captured / Pending Settlement';
$string['capturedsettled'] = 'Captured / Settled';
$string['capturetestwarn'] = 'Capture seems working, but no record was updated in test mode';
$string['captureyes'] = 'The credit card will be captured and student will be enrolled to course. Are you sure?';
$string['ccexpire'] = 'Expiry Date';
$string['ccexpired'] = 'The credit card has expired';
$string['ccinvalid'] = 'Invalid card number';
$string['ccno'] = 'Credit Card Number';
$string['cctype'] = 'Credit Card Type';
$string['ccvv'] = 'Card Verification';
$string['ccvvhelp'] = 'Look at the back of card (last 3 digits)';
$string['choosemethod'] = 'If you know enrolment key of the course, enter it; otherwise you need to pay for this course.';
$string['chooseone'] = 'Fill one or both of the following two fields';
$string['credittestwarn'] = 'Credit seems working, but no record was inserted to database in test mode';
$string['cutofftime'] = 'Transaction Cut-Off Time. When the last transaction is picked up for settlement?';
$string['description'] = 'The Authorize.net module allows you to set up paid courses by merchants.  If the cost for any course is zero, then students are not asked to pay for entry.  There is a site-wide cost that you set here as a default for the whole site and then a course setting that you can set for each course individually. The course cost overrides the site cost.<br /><br /><b>Note:</b> If you enter an enrolment key in the course settings, then students will also have the option to enrol using a key. This is useful if you have a mixture of paying and non-paying students.';
$string['enrolname'] = 'Authorize.net Credit Card Gateway';
$string['expired'] = 'Expired';
$string['howmuch'] = 'How much?';
$string['httpsrequired'] = 'We are sorry to inform you that your request cannot be processed currently. This site\'s configuration couldn\'t be set up correcly.
<br /><br />
Please don\'t enter your credit card number unless you see a yellow lock at the bottom of the browser. It means, it simply encrypts all data sent between client and server. So the information during the transaction between 2 computers is protected and your credit card number cannot captured over the internet.';
$string['logindesc'] = 'This option must be ON. <br /><br />
Please ensure that you have turned <a href=\"$a->url\">loginhttps ON</a> in Admin >> Variables >> Security.<br /><br />
Turning this on will make Moodle use a secure https connection just for the login and payment pages.';
$string['nameoncard'] = 'Name on Card';
$string['noreturns'] = 'No returns!';
$string['notsettled'] = 'Not settled';
$string['orderid'] = 'Order ID';
$string['paymentmanagement'] = 'Payment Management';
$string['paymentpending'] = 'Your payment is pending for this course with this order number $a->orderid.';
$string['pendingordersemail'] = ' Dear admin,

$a->pending transactions will be expired unless you accept payment with in 2 days.

This is a warning message, because you didn\'t enable autocapture. Means you have to accept or deny payments manually.

To accept/deny pending payments go to:
$a->url

To enable autocapture, means you will not receive any warning emails anymore, go to:
$a->enrolurl';
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
$string['transid'] = 'Transaction ID';
$string['unenrolstudent'] = 'Unenrol student?';
$string['void'] = 'Void';
$string['voidtestwarn'] = 'Void seems working, but no record was updated in test mode';
$string['voidyes'] = 'Transaction will be cancelled. Are you sure?';
$string['zipcode'] = 'Zip Code';

?>
