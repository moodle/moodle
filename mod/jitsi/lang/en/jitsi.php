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
 * English strings for jitsi
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_jitsi
 * @copyright  2019 Sergio Comerón Sánchez-Paniagua <sergiocomeron@icloud.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Jitsi';
$string['modulenameplural'] = 'Jitsis';
$string['modulename_help'] = 'Use the Jitsi module for videoconference. These videoconferences will use your Moodle username by displaying your username and avatar in videoconferences.

Jitsi-meet is an open-source videoconferencing solution that enables you to easily build and implement secure video conferencing solutions.';
$string['jitsi:addinstance'] = 'Add a new Jitsi';
$string['jitsi:moderation'] = 'Jitsi Moderation';
$string['jitsi:record'] = 'Record session';
$string['jitsi:hide'] = 'Hide recordings';
$string['jitsi:createlink'] = 'View and copy invite links for guest users';
$string['jitsi:sharedesktop'] = 'Share Desktop';
$string['jitsi:view'] = 'View Jitsi';
$string['jitsi:addinstance'] = 'Add a new Jitsi';
$string['jitsi:viewusersonsession'] = 'Access to the attendees reports';
$string['jitsiname'] = 'Session name';
$string['jitsi'] = 'Jitsi';
$string['pluginadministration'] = 'Jitsi administration';
$string['pluginname'] = 'Jitsi';
$string['instruction'] = 'Click the button to access';
$string['access'] = 'Access';
$string['calendarstart'] = 'The videoconference \'{$a}\' start';
$string['allow'] = 'Start of videoconference';
$string['close'] = 'Finish of videoconference';
$string['nostart'] = 'The session has not started. You can access {$a}';
$string['finish'] = 'The session has finished.';
$string['privacy:metadata:jitsi'] = 'In order to integrate with a Jitsi session, user data needs to be exchanged with that service.';
$string['privacy:metadata:jitsi:username'] = 'The username is sent from moodle to show to the other users of the Jitsi session';
$string['privacy:metadata:jitsi:avatar'] = 'The avatar is sent from moodle to show to the other users of the Jitsi session';
$string['noviewpermission'] = 'You do not have permission for view this Jitsi session';
$string['minpretime'] = 'Minutes to access';
$string['help'] = 'Help';
$string['helpex'] = 'This help text will be displayed at the end in all the Jitsi activities. It\'s a good place for netiquette rules or help instruccions for users.';
$string['identification'] = 'ID User';
$string['identificationex'] = 'ID to show in the session';
$string['username'] = 'Username';
$string['nameandsurname'] = 'Firstname + Lastname';
$string['tokennconfig'] = 'Token configuration';
$string['tokenconfigurationex'] = 'If you are using a Jitsi server configured in "token mode", for example your own server or an 8x8 server, fill in the following parameters depending on the type of server you are using. Required to promote your users with the (mod/jitsi:moderation) capability enabled as native Jitsi moderators.';
$string['separator'] = 'Separator';
$string['separatorex'] = 'Define the field separator for the session name';
$string['sessionnamefields'] = 'Session name fields';
$string['sessionnamefieldsex'] = 'Fields that define the session name';
$string['securitybutton'] = 'Security Button';
$string['securitybuttonex'] = 'Enables native Jitsi "Security Options" and the "Lobby mode". Probably you should disable this option if you have set a password above because the password will be displayed to users. With token configuration you can experiment with this option';
$string['invitebutton'] = 'Invite Options';
$string['invitebuttonex'] = 'Allow users with the mod/jitsi:createlink capability (teachers) to create invite links for users not enroled in the course.';
$string['appid'] = 'App_ID';
$string['appidex'] = 'App ID for token configuration';
$string['secret'] = 'Secret';
$string['secretex'] = 'Secret for token configuration';
$string['simultaneouscameras'] = 'Simultaneous cameras';
$string['simultaneouscamerasex'] = 'Maximun simultaneous cameras users can see. That could be overriden by your Jitsi server with a lower value. A lot of cameras allowed could overload your clients browsers.';
$string['streamingbutton'] = 'Live Streaming';
$string['streamingbuttonex'] = 'Enable the Live Streaming features to users with mod/jitsi:record capability enabled (teachers). If enabled probably you will want to disable the "record" option above.';
$string['blurbutton'] = 'Show Background options';
$string['blurbuttonex'] = 'Show the "Select Backgroud" option to all users. This feature is cool but require powerfull computers. Maybe you should disable it.';
$string['youtubebutton'] = 'Youtube sharing option';
$string['youtubebuttonex'] = 'Show the youtube videos sharing option';
$string['watermarklink'] = 'Watermark Link';
$string['watermarklinkex'] = 'Watermark Link';
$string['finishandreturn'] = 'Finish and Return';
$string['finishandreturnex'] = 'Return to course when finish the session. Using public Jitsi users return to course when they close advertising';
$string['password'] = 'Password';
$string['passwordex'] = 'Password to secure your sessions. Recommended if you use public server';
$string['alias'] = 'Alias';
$string['privatesession'] = '{$a} private session';
$string['myprivatesession'] = 'My private session';
$string['privatesessions'] = 'Private sessions';
$string['privatesessionsex'] = 'Add private sessions to user profiles';
$string['showavatars'] = 'Show avatars in Jitsi';
$string['showavatarsex'] = 'Show the avatar of the user in Jitsi. If the user has no profile picture this will load the default profile picture from Moodle instead of the initials Jitsi will show when no picture is set.';
$string['streamingconfig'] = 'Streaming configuration';
$string['streamingconfigex'] = 'Default streaming configuration works "out of the box" and users can stream/record their sessions with their own streaming accounts in streaming services (Youtube, Peertube...) but teacher is responsible to publish their watch links to students in the course. </br></br>For a better experience you can enable the "Moodle integrated" method in order to record in a corporate stream account (only YouTube available now) and recordings will be automatically available for students.';
$string['oauthid'] = 'OAuth2 id';
$string['oauthidex'] = 'Oauth2 id google account with YouTube Data API v3 enabled and this Authorized redirect URIs <b>\'{$a}\'</b> on Google api console.';
$string['oauthsecret'] = 'OAuth2 secret';
$string['oauthsecretex'] = 'Oauth2 Secret google account';
$string['streamingandrecording'] = 'Stream & Record';
$string['buttonopeninbrowser'] = 'Open in browser';
$string['buttonopenwithapp'] = 'Join this meeting using the app';
$string['buttondownloadapp'] = 'Download application';
$string['appaccessinfo'] = 'If you want to join the meeting using a mobile device, you will need the Jitsi Meet mobile application.';
$string['desktopaccessinfo'] = 'If you want to join the meeting, click on the button below to open Jitsi in your browser.';
$string['appinstalledtext'] = 'If you already have the app:';
$string['appnotinstalledtext'] = "If you don't have the app yet:";
$string['deeplink'] = 'Deep Link';
$string['deeplinkex'] = 'When on moodle app allows to transfer Jitsi sessions to Jitsi app.';
$string['accessto'] = 'Access to {$a}. Enter the name to be displayed.';
$string['accesstowithlogin'] = 'Access to {$a}.';
$string['accesstotitle'] = 'Access to {$a}';
$string['noinviteaccess'] = 'Guest access is currently not allowed.';
$string['copied'] = 'Copied to clipboard';
$string['streamingoption'] = 'Live Streaming Method';
$string['streamingoptionex'] = '<b>Jitsi interface</b> enable the "Start Live Streming" in the Jitsi interface and users can use their own streaming accounts. </br><b>Moodle integrated</b> is the easyest option for users. Teachers can start a "Record & Stream" inmediatly and no credetials account will be required to them. Stremings/Recordings are stored in a corporate account and will be available inmediatly for students. You must set OAuth2 credentials and a streming account bellow.';
$string['record'] = 'Record option';
$string['recordex'] = 'Enable native Jitsi recording options (actually dropbox) to users with mod/jitsi:record capability enabled (teachers). If you set the "Streaming configuration" with the "Moodle integrated" method probably you will want to disable this.';
$string['entersession'] = 'Enter to session';
$string['guestform'] = 'Enter to guest form';
$string['records'] = 'Recordings';
$string['messageprovider:onprivatesession'] = 'User on private session';
$string['messageprovider:callprivatesession'] = 'Call to Jitsi private';
$string['userenter'] = '{$a} is on your private Jitsi Room';
$string['usercall'] = '{$a} calls you for a private Jitsi';
$string['hasentered'] = 'has entered on your private Jitsi session';
$string['iscalling'] = 'is calling you to enter on his private Jitsi';
$string['here'] = 'here';
$string['toenter'] = 'to enter';
$string['click'] = 'Click';
$string['privatesessiondisabled'] = 'Private sessions are disabled';
$string['warningprivate'] = 'If you access, {$a} will be warned with a notification.';
$string['sharetoinvite'] = 'Share this link to invite someone to the session';
$string['invitations'] = 'Invitations';
$string['finishinvitation'] = 'Invitation link will expire on';
$string['linkexpiredon'] = 'This link expired on {$a}';
$string['invitationsnotactivated'] = 'The invitations is not activated';
$string['reactions'] = 'Reactions';
$string['reactionsex'] = 'Shows sound emoticons of applause, surprise, etc ... "Raise hand button" enabled is required';
$string['updated'] = 'Updated';
$string['record'] = 'Record';
$string['editrecordname'] = 'Edit record name';
$string['newvaluefor'] = 'New value for ';
$string['account'] = 'Account';
$string['accounts'] = 'Streaming/Recording Accounts';
$string['accountconnected'] = 'Account successfully connected and put <b>in use</b>.';
$string['deletesources'] = 'Recordings available to delete';
$string['inuse'] = ' <b>(in use)</b>';
$string['deleteq'] = 'Delete and disconect this acco?';
$string['loginq'] = 'Do you want to put in use this accout?';
$string['tablelistjitsis'] = "List with all the videos in your Streaming/Recording Accounts providers which are available to be deleted because they are no more linked in Jitsi activities in this moodle instance. You can safely delete them in order to free up space on the streaming server. The list could include videos that now are in the 'Recycle bin' in some course. It's recommended to delete just old recordings that you know won't be required. </br></br> <b>¡¡¡ WARNING!!! </b>If you have moodle backup instances you should NOT remove these videos if they are linked in other instances.";
$string['deletesourceq'] = 'Are you sure? The recording will be permanently deleted from the video server and it can not be recovered';
$string['participatingsession'] = 'Participating in session';
$string['participantspane'] = 'Participants panel';
$string['participantspaneex'] = 'Show the participants panel to all users. When uncheck only users with Jitsi Moderation capability (mod/jitsi:moderation), usually teachers, can watch the panel.';
$string['raisehand'] = 'Raise hand button';
$string['raisehandex'] = 'Show the raisehad button to all users. When users raise their hands they can access to the participants panel. If you hide the participants panels may be you should hide this button.';
$string['completionminutes'] = 'Student must attend ';
$string['completionminutesex'] = 'Minutes to attend ';
$string['completionminutes_help'] = 'Number of minutes that student must attend to give the activity as completed';
$string['completiondetail:minutes'] = 'Attend {$a} minutes';
$string['connectedattendeesnow'] = 'Connected attendees now';
$string['minutesconnected'] = 'You have been connected for {$a} minutes';
$string['attendeesreport'] = 'Attendees report';
$string['accountinsufficientprivileges'] = 'The streaming account set up has insufficient privileges. Please contact your administrator.';
$string['deprecated'] = 'Deprecated';
$string['deprecatedex'] = 'Deprecated params that probably will not work because Jitsi Meet changed its implementation';
$string['experimental'] = 'Experimental';
$string['experimentalex'] = 'These are options that we are experimenting with and that may disappear in future versions.';
$string['adminaccountex'] = 'At least one account is required in order to stream/record sessions with the "Moodle integrated" method for streaming.
 </br>Just one account can be "<b>in use</b>" and will be used to stream/record all your teacher sessions.
 </br>When adding new accounts it\'s recommended <b>to name them with real account names</b> because in the future you could be required to re-login in order to re-authorize the account.
 </br>Only accounts with no recordings related to teacher\'s Jitsi activities and no recordings pending to be deleted from the streaming servers can be removed here using the trash icon.
 </br>New accounts without credentials could appear here when Jitsi activities backups from other server are restored in this one with accounts that was not present here.';
$string['jitsiinterface'] = 'Jitsi interface';
$string['integrated'] = 'Moodle Integrated';
$string['deletetooltip'] = 'Delete';
$string['activatetooltip'] = 'Click to put into use';
$string['logintooltip'] = 'Credentials for this account are required';
$string['authq'] = 'Login with this account to get credentials and put "in use"?';
$string['addaccount'] = 'Add account';
$string['notloggedin'] = 'Account credentials required';
$string['confirmdeleterecordinactivity'] = 'Confirm you want to delete this recording. This operation can\'t be undone.';
$string['errordeleting'] = 'Error deleting';
$string['minpretime_help'] = 'Users with moderation permissions will be able to access these minutes before the start';
$string['domain'] = 'Domain';
$string['domainex'] = 'Domain Jitsi Server to use. You can search in Google for alternative public Jitsi servers that could be nearest to your users and with less latency.
 If you have your private own Jitsi Server inform it here without "https://".';
$string['sessionisbeingrecorded'] = 'Session is being recorded';
$string['recordtitle'] = 'Record';
$string['preparing'] = 'Preparing. Please wait...';
$string['closebeforeopen'] = 'Could not update the session. You have specified a close date before the open date.';
$string['validitytimevalidation'] = 'The invitation cannot expire before the session start date or after the session end date.';
$string['privatekey'] = 'Private key';
$string['tokennconfig8x8'] = '8x8 Servers configuration';
$string['tokenconfiguration8x8ex'] = 'If you use 8x8 servers you need to configure the following parameters.';
$string['privatekeyex'] = 'Private key to sign the token with 8x8 server. You can get it from the 8x8 server administration. (https://jaas.8x8.com/)';
$string['apikeyid8x8'] = 'Api Key ID';
$string['apikeyid8x8ex'] = 'Api Key ID to use with 8x8 server. You can get it from the 8x8 server administration. (https://jaas.8x8.com/)';
$string['nojitsis'] = 'No Jitsi activities found';
