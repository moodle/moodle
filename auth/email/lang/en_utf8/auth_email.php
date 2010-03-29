<?php
// Email plugin
$string['auth_emailchangecancel'] = 'Cancel email change';
$string['auth_emailchangepending'] = 'Change pending. Open the link sent to you at $a->preference_newemail.';
$string['auth_emaildescription'] = 'Email confirmation is the default authentication method.  When the user signs up, choosing their own new username and password, a confirmation email is sent to the user\'s email address.  This email contains a secure link to a page where the user can confirm their account. Future logins just check the username and password against the stored values in the Moodle database.';
$string['auth_emailnowexists'] = 'The email address you tried to assign to your profile has been assigned to someone else since your original request. Your request for change of email address is hereby cancelled, but you may try again with a different address.';
$string['auth_emailtitle'] = 'Email-based self-registration';
$string['auth_emailnoinsert'] = 'Could not add your record to the database!';
$string['auth_emailnoemail'] = 'Tried to send you an email but failed!';
$string['auth_emailrecaptcha'] = 'Adds a visual/audio confirmation form element to the signup page for email self-registering users. This protects your site against spammers and contributes to a worthwhile cause. See http://recaptcha.net/learnmore.html for more details. <br /><em>PHP cURL extension is required.</em>';
$string['auth_emailrecaptcha_key'] = 'Enable reCAPTCHA element';
$string['auth_emailsettings'] = 'Settings';
$string['auth_emailupdatemessage'] = 'Dear $a->fullname,

You have requested a change of your email address for your user account at $a->site. Please open the following URL in your browser in order to confirm this change.

$a->url';
$string['auth_emailupdatetitle'] = 'Confirmation of email update at $a->site';
$string['auth_invalidnewemailkey'] = 'Error: if you are trying to confirm a change of email address, you may have made a mistake in copying the URL we sent you by email. Please copy the address and try again.';
$string['auth_emailupdatesuccess'] = 'Email address of user <em>$a->fullname</em> was successfully updated to <em>$a->email</em>.';
$string['auth_outofnewemailupdateattempts'] = 'You have run out of allowed attempts to update your email address. Your update request has been cancelled.';
$string['auth_emailupdate'] = 'Email address update';
$string['auth_changingemailaddress'] = 'You have requested a change of email address, from $a->oldemail to $a->newemail. For security reasons, we are sending you an email message at the new address to confirm that it belongs to you. Your email address will be updated as soon as you open the URL sent to you in that message.';

?>