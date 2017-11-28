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

$string['adodbdebugwarning'] = 'ADOdb debugging is enabled. It should be disabled in the external database authentication or external database enrolment plugin settings.';
$string['androidappid'] = 'Android app\'s unique identifier';
$string['androidappid_desc'] = 'This setting may be left as default unless you have a custom Android app.';
$string['autologinkeygenerationlockout'] = 'Auto-login key generation is blocked. You need to wait 6 minutes between requests.';
$string['autologinnotallowedtoadmins'] = 'Auto-login is not allowed for site admins.';
$string['cachedef_plugininfo'] = 'This stores the list of plugins with mobile addons';
$string['clickheretolaunchtheapp'] = 'Click here if the app does not open automatically.';
$string['configmobilecssurl'] = 'A CSS file to customise your mobile app interface.';
$string['customlangstrings'] = 'Custom language strings';
$string['customlangstrings_desc'] = 'Words and phrases displayed in the app can be customised here. Enter each custom language string on a new line with format: string identifier, custom language string and language code, separated by pipe characters. For example:
<pre>
mm.user.student|Learner|en
mm.user.student|Aprendiz|es
</pre>
For a complete list of string identifiers, see the documentation.';
$string['custommenuitems'] = 'Custom menu items';
$string['custommenuitems_desc'] = 'Additional items can be added to the app\'s main menu by specifying them here. Enter each custom menu item on a new line with format: item text, link URL, link-opening method and language code (optional, for displaying the item to users of the specified language only), separated by pipe characters.

Link-opening methods are: app (for linking to an activity supported by the app), inappbrowser (for opening a link in a browser without leaving the app), browser (for opening the link in the device default browser outside the app) and embedded (for displaying the link in an iframe in a new page in the app).

For example:
<pre>
App help|https://someurl.xyz/help|inappbrowser
My grades|https://someurl.xyz/local/mygrades/index.php|embedded|en
Mis calificaciones|https://someurl.xyz/local/mygrades/index.php|embedded|es
</pre>';
$string['disabledfeatures'] = 'Disabled features';
$string['disabledfeatures_desc'] = 'Select here the features you want to disable in the Mobile app for your site. Please note that some features listed here could be already disabled via other site settings. You will have to log out and log in again in the app to see the changes.';
$string['displayerrorswarning'] = 'Display debug messages (debugdisplay) is enabled. It should be disabled.';
$string['enablesmartappbanners'] = 'Enable App Banners';
$string['enablesmartappbanners_desc'] = 'If enabled, a banner promoting the mobile app will be displayed when accessing the site using a mobile browser.';
$string['forcedurlscheme'] = 'If you want to allow only your custom branded app to be opened via a browser window, then specify its URL scheme here; otherwise leave the field empty.';
$string['forcedurlscheme_key'] = 'URL scheme';
$string['forcelogout'] = 'Force log out';
$string['forcelogout_desc'] = 'If enabled, the app option \'Change site\' is replaced by \'Log out\'. This results in the user being completely logged out. They must then re-enter their password the next time they wish to access the site.';
$string['httpsrequired'] = 'HTTPS required';
$string['insecurealgorithmwarning'] = 'It seems that the HTTPS certificate uses an insecure algorithm for signing (SHA-1). Please try updating the certificate.';
$string['invalidcertificatechainwarning'] = 'It seems that the certificate chain is invalid.';
$string['invalidcertificateexpiredatewarning'] = 'It seems that the HTTPS certificate for the site has expired.';
$string['invalidcertificatestartdatewarning'] = 'It seems that the HTTPS certificate for the site is not yet valid (with a start date in the future).';
$string['invalidprivatetoken'] = 'Invalid private token. Token should not be empty or passed via GET parameter.';
$string['invaliduserquotawarning'] = 'The user quota (userquota) is set to an invalid number. It should be set to a valid number (an integer value) in Site policies.';
$string['iosappid'] = 'iOS app\'s unique identifier';
$string['iosappid_desc'] = 'This setting may be left as default unless you have a custom iOS app.';
$string['loginintheapp'] = 'Via the app';
$string['logininthebrowser'] = 'Via a browser window (for SSO plugins)';
$string['loginintheembeddedbrowser'] = 'Via an embedded browser (for SSO plugins)';
$string['mainmenu'] = 'Main menu';
$string['mobileapp'] = 'Mobile app';
$string['mobileappconnected'] = 'Mobile app connected';
$string['mobileappenabled'] = 'This site has mobile app access enabled.<br /><a href="{$a}">Download the mobile app</a>.';
$string['mobileappearance'] = 'Mobile appearance';
$string['mobileauthentication'] = 'Mobile authentication';
$string['mobilecssurl'] = 'CSS';
$string['mobilefeatures'] = 'Mobile features';
$string['mobilenotificationsdisabledwarning'] = 'Mobile notifications are not enabled. They should be enabled in Manage message outputs.';
$string['mobilesettings'] = 'Mobile settings';
$string['pluginname'] = 'Moodle Mobile tools';
$string['selfsignedoruntrustedcertificatewarning'] = 'It seems that the HTTPS certificate is self-signed or not trusted. The mobile app will only work with trusted sites.';
$string['setuplink'] = 'App download page';
$string['setuplink_desc'] = 'URL of page with links to download the mobile app from the App Store and Google Play.';
$string['smartappbanners'] = 'App Banners';
$string['pluginnotenabledorconfigured'] = 'Plugin not enabled or configured.';
$string['remoteaddons'] = 'Remote add-ons';
$string['typeoflogin'] = 'Type of login';
$string['typeoflogin_desc'] = 'If the site uses a SSO authentication method, then select via a browser window or via an embedded browser. An embedded browser provides a better user experience, though it doesn\'t work with all SSO plugins. If using SSO, autologinguests should be disabled.';
$string['getmoodleonyourmobile'] = 'Get the mobile app';
