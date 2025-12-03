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
 * Strings for component 'tool_mobile', language 'en'
 *
 * @package    tool_mobile
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['activitiesoverview'] = 'Activities overview';
$string['adodbdebugwarning'] = 'ADOdb debugging is enabled. It should be disabled in the external database authentication or external database enrolment plugin settings.';
$string['androidappid'] = 'Android app\'s unique identifier';
$string['androidappid_desc'] = 'This setting may be left as default unless you have a custom Android app.';
$string['apppolicy'] = 'App policy URL';
$string['apppolicy_help'] = 'The URL of a policy for app users which is listed on the About page in the app. If the field is left empty, the site policy URL will be used instead.';
$string['apprequired'] = 'This functionality is only available when accessed via the Moodle mobile or desktop app.';
$string['autologinkeygenerationlockout'] = 'Auto-login key generation is blocked. You need to wait {$a} minutes between requests.';
$string['autologinmintimebetweenreq'] = 'Minimum time between auto-login requests';
$string['autologinmintimebetweenreq_desc'] = 'The minimum time between auto-login requests from the mobile app. If app users are frequently asked to enter their credentials when viewing content embedded from the site, then set a lower value.';
$string['autologinnotallowedtoadmins'] = 'Auto-login is not allowed for site admins.';
$string['autologout'] = 'Enforce auto logout for your users';
$string['autologout_desc'] = 'For security reasons, you can enforce automatic logout for your users when they leave or close the app, or it goes to background. Users will have to log in again when they return to the app.';
$string['autologoutcustom'] = 'Custom time after users leave or close the app';
$string['autologoutinmediate'] = 'Immediately after users leave or close the app';
$string['autologouttime'] = 'Auto logout timer';
$string['cachedef_plugininfo'] = 'This stores the list of plugins with mobile addons';
$string['cachedef_subscriptiondata'] = 'This stores the Moodle app subscription information.';
$string['clickheretolaunchtheapp'] = 'Click here if the app does not open automatically.';
$string['configmobilecssurl'] = 'A CSS file to customise your mobile app interface.';
$string['customlangstrings'] = 'Custom language strings';
$string['customlangstrings_desc'] = 'Words and phrases displayed in the app can be customised here. Enter each custom language string on a new line with format: string identifier, custom language string and language code, separated by pipe characters. For example:
<pre>
core.user.student|Learner|en
core.user.student|Aprendiz|es
</pre>
For a complete list of string identifiers, see the documentation.';
$string['custommenuitems'] = 'Custom menu items';
$string['custommenuitems_desc'] = 'Additional items can be added to the app\'s main menu by specifying them here. Enter each custom menu item on a new line with format: item text, link URL, link-opening method and language code (optional, for displaying the item to users of the specified language only), separated by pipe characters.

Link-opening methods are: app (for linking to an activity supported by the app), inappbrowser (for opening a link in a browser without leaving the app), browser (for opening the link in the device default browser outside the app) and embedded (for displaying the link in an iframe in a new page in the app).

When items are missing a translation for a given language, they will use other languages as fallback unless "_only" is appended to the language code.

For example:
<pre>
App help|https://someurl.xyz/help|inappbrowser
My grades|https://someurl.xyz/local/mygrades/index.php|embedded|en
Mis calificaciones|https://someurl.xyz/local/mygrades/index.php|embedded|es
You will only see this in English|https://someurl.xyz/english|browser|en_only
</pre>';
$string['customusermenuitems'] = 'Custom user menu items';
$string['customusermenuitems_desc'] = 'Additional items can be added to the app\'s user menu by specifying them here. Enter each custom user menu item on a new line with format: item text, link URL, link-opening method and language code (optional, for displaying the item to users of the specified language only), separated by pipe characters.

Link-opening methods are: app (for linking to an activity supported by the app), inappbrowser (for opening a link in a browser without leaving the app), browser (for opening the link in the device default browser outside the app) and embedded (for displaying the link in an iframe in a new page in the app).

When items are missing a translation for a given language, they will use other languages as fallback unless "_only" is appended to the language code.

For example:
<pre>
App help|https://someurl.xyz/help|inappbrowser
My grades|https://someurl.xyz/local/mygrades/index.php|embedded|en
Mis calificaciones|https://someurl.xyz/local/mygrades/index.php|embedded|es
You will only see this in English|https://someurl.xyz/english|browser|en_only
</pre>';
$string['darkmode'] = 'Dark mode';
$string['disabledfeatures'] = 'Disabled features';
$string['disabledfeatures_desc'] = 'Select here the features you want to disable in the Mobile app for your site. Please note that some features listed here could be already disabled via other site settings. You will have to log out and log in again in the app to see the changes.';
$string['displayerrorswarning'] = 'Display debug messages (debugdisplay) is enabled. It should be disabled.';
$string['downloadcourse'] = 'Download course';
$string['downloadcourses'] = 'Download courses';
$string['enablesmartappbanners'] = 'Enable App Banners';
$string['enablesmartappbanners_desc'] = 'If enabled, a banner promoting the mobile app will be displayed when accessing the site using a mobile browser.';
$string['filetypeexclusionlist'] = 'File type exclusion list';
$string['filetypeexclusionlist_desc'] = 'Select all file types which are not for use on a mobile device. Such files will be listed in the course, then if a user attempts to open them, a warning will be displayed advising that the file type is not intended for use on a mobile device. The user can then cancel or ignore the warning and open the file anyway.';
$string['filetypeexclusionlistplaceholder'] = 'Mobile file type exclusion list';
$string['forcedurlscheme'] = 'If you want to allow only your custom branded app to be opened via a browser window, then specify its URL scheme here. If you want to allow only the official app, then set the default value. Leave the field empty if you want to allow any app.';
$string['forcedurlscheme_key'] = 'URL scheme';
$string['forcelogout'] = 'Force log out';
$string['forcelogout_desc'] = 'If enabled, users will be always completely logged out even when switching accounts. They must then re-enter their password the next time they wish to access the site.';
$string['h5poffline'] = 'View H5P content offline';
$string['httpsrequired'] = 'HTTPS required';
$string['insecurealgorithmwarning'] = 'It seems that the HTTPS certificate uses an insecure algorithm for signing (SHA-1). Please try updating the certificate.';
$string['invalidcertificatechainwarning'] = 'It seems that the certificate chain is invalid. This certificate might work for a browser but not for a mobile app.';
$string['invalidcertificateexpiredatewarning'] = 'It seems that the HTTPS certificate for the site has expired.';
$string['invalidcertificatestartdatewarning'] = 'It seems that the HTTPS certificate for the site is not yet valid (with a start date in the future).';
$string['invalidprivatetoken'] = 'Invalid private token. Token should not be empty or passed via GET parameter.';
$string['invaliduserquotawarning'] = 'The user quota (userquota) is set to an invalid number. It should be set to a valid number (an integer value) in Site security settings.';
$string['iosappid'] = 'iOS app\'s unique identifier';
$string['iosappid_desc'] = 'This setting may be left as default unless you have a custom iOS app.';
$string['launchviasiteinbrowser'] = 'Launch via site in system browser';
$string['loginintheapp'] = 'Via the app';
$string['logininthebrowser'] = 'Via a browser window (for SSO plugins)';
$string['loginintheembeddedbrowser'] = 'Via an embedded browser (for SSO plugins)';
$string['logoutconfirmation'] = 'Are you sure you want to log out from the mobile app on your mobile devices? By logging out, you will then need to re-enter your username and password in the mobile app on all devices where you have the app installed.';
$string['mainmenu'] = 'Main menu';
$string['managefiletypes'] = 'Manage file types';
$string['minimumversion'] = 'If an app version is specified (3.8.0 or higher), any users using an older app version will be prompted to upgrade their app before being allowed access to the site.';
$string['minimumversion_key'] = 'Minimum app version required';
$string['mobileapp'] = 'Mobile app';
$string['mobileappenabled'] = 'This site has mobile app access enabled.<br /><a href="{$a}">Download the mobile app</a>.';
$string['mobileappearance'] = 'Mobile appearance';
$string['mobileappsubscription'] = 'Moodle app subscription';
$string['mobileauthentication'] = 'Mobile authentication';
$string['mobilecssurl'] = 'CSS';
$string['mobilefeatures'] = 'Mobile features';
$string['mobilenotificationsdisabledwarning'] = 'Mobile notifications are not enabled. They should be enabled in Notification settings.';
$string['mobilesettings'] = 'Mobile settings';
$string['moodleappsportalfeatureswarning'] = 'Please note that some features may be restricted depending on your Moodle app subscription. For details, visit the <a href="{$a}" target="_blank">Moodle Apps Portal</a>.';
$string['notifications'] = 'Notifications';
$string['notificationsactivedevices'] = 'Active devices';
$string['notificationsignorednotifications'] = 'Notifications not sent';
$string['notificationslimitreached'] = 'The monthly active user devices limit has been exceeded. Notifications for some users will not be sent. It is recommended that you upgrade your app plan in the <a href="{$a}" target="_blank">Moodle Apps Portal</a>.';
$string['notificationsmissingwarning'] = 'Moodle app notification statistics could not be retrieved. This is most likely because mobile notifications are not yet enabled on the site. You can enable them in Site Administration / Messaging / Mobile.';
$string['notificationsnewdevices'] = 'New devices';
$string['notificationsseemore'] = 'Note: Moodle app usage statistics are not calculated in real time. To access more detailed statistics, including data from previous months, please log in to the <a href="{$a}" target="_blank">Moodle Apps Portal</a>.';
$string['notificationssentnotifications'] = 'Notifications sent';
$string['notificationscurrentactivedevices'] = 'Devices receiving notifications this month';
$string['oauth2identityproviders'] = 'OAuth 2 identity providers';
$string['offlineuse'] = 'Offline use';
$string['pluginname'] = 'Moodle app tools';
$string['pluginnotenabledorconfigured'] = 'Plugin not enabled or configured.';
$string['qrcodedisabled'] = 'Access via QR code disabled';
$string['qrcodeformobileappaccess'] = 'QR code for mobile app access';
$string['qrcodeformobileapploginabout'] = 'Scan the QR code with your mobile app and you will be automatically logged in. The QR code will expire in {$a}.';
$string['qrcodeformobileappurlabout'] = 'Scan the QR code with your mobile app to fill in the site URL in your app.';
$string['qrsiteadminsnotallowed'] = 'For security reasons login via QR code is not allowed for site administrators or if you are logged in as another user.';
$string['qrcodetype'] = 'QR code access';
$string['qrcodetype_desc'] = 'A QR code can be provided for mobile app users to scan. This can be used to fill in the site URL, or where the site is secured using HTTPS, to automatically log the user in without having to enter their username and password.';
$string['qrcodetypeurl'] = 'QR code with site URL';
$string['qrcodetypelogin'] = 'QR code with automatic login';
$string['qrkeyttl'] = 'QR authentication key duration';
$string['qrkeyttl_desc'] = 'The length of time for which a QR code for automatic login is valid.';
$string['qrsameipcheck'] = 'QR authentication same IP check';
$string['qrsameipcheck_desc'] = 'Whether users must use the same network for both generating and scanning a QR code for login. Only disable it if users report issues with the QR login.';
$string['readingthisemailgettheapp'] = 'Are you reading this in an email? <a href="{$a}">Download the mobile app and receive notifications on your mobile device</a>.';
$string['remoteaddons'] = 'Remote add-ons';
$string['scanqrcode'] = 'Scan QR code';
$string['selfsignedoruntrustedcertificatewarning'] = 'It seems that the HTTPS certificate is self-signed or not trusted. The mobile app will only work with trusted sites. Please use any online SSL checker to diagnose the problem. If it indicates that your certificate is OK, you can ignore this warning.';
$string['setuplink'] = 'App download page';
$string['setuplink_desc'] = 'URL of page with options to download the mobile app from the App Store and Google Play. The app download page link is displayed in the page footer and in a user\'s profile. Leave blank to not display a link.';
$string['smartappbanners'] = 'App Banners';
$string['subscription'] = 'Subscription';
$string['subscriptioncreated'] = 'Start date';
$string['subscriptionerrorrequest'] = 'There was an unexpected error when trying to retrieve your Moodle app subscription information.';
$string['subscriptionexpiration'] = 'Expiry date';
$string['subscriptionfeaturenotapplied'] = 'This feature is configured on your site but it is not included in your Moodle app plan. Thus, the setting will have no effect.';
$string['subscriptionfeatures'] = 'Subscription features';
$string['subscriptionlimitsurpassed'] = 'Subscription limit exceeded';
$string['subscriptionregister'] = 'For details of the various app plans, and to access Moodle app usage statistics, please visit the <a href="{$a}" target="_blank">Moodle Apps Portal</a>.';
$string['subscriptionsseemore'] = 'Note: The information displayed is not updated in real time. You may need to log out and log in again to see updates. For information on upgrading your app plan, please log in to the <a href="{$a}" target="_blank">Moodle Apps Portal</a>.';
$string['typeoflogin'] = 'Type of login';
$string['typeoflogin_desc'] = 'If the site uses a SSO authentication method, then select via a browser window or via an embedded browser. An embedded browser provides a better user experience, though it doesn\'t work with all SSO plugins.';
$string['getmoodleonyourmobile'] = 'Get the mobile app';
$string['privacy:metadata:preference:tool_mobile_autologin_request_last'] = 'The date of the last auto-login key request. Between each request 6 minutes are required.';
$string['privacy:metadata:core_userkey'] = 'User\'s keys used to create auto-login key for the current user.';
$string['responsivemainmenuitems'] = 'Responsive menu items';
$string['switchaccount'] = 'Switch account';
$string['viewqrcode'] = 'View QR code';
