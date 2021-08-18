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
 * Strings for component 'media_jwplayer', language 'en'
 *
 * @package    media_jwplayer
 * @copyright  2017 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['appearanceconfig'] = 'Appearance';
$string['aspectratio'] = 'Aspect ratio';
$string['aspectratiodesc'] = 'Defines player proportions when Display mode is "Fixed width" or "Responsive". Also used when dimensions are set in media, but height is not defined.';
$string['customskinname'] = 'Custom skin name';
$string['customskinnamedesc'] = 'The name of your custom CSS skin to use for styling the player. The skin name will be appended to the <tt>.jw-skin-</tt> prefix and used in player element, this allows user to override player style by defining the class in Moodle theme as described in <a href="https://developer.jwplayer.com/jwplayer/docs/jw8-branding">Branding documentation</a>.';
$string['defaultposter'] = 'Default poster';
$string['defaultposterdesc'] = 'Default poster image to use with videos.';
$string['displaymode'] = 'Display mode';
$string['displaymodedesc'] = 'Display mode to use if dimensions are not defined in the element containing the media. In "Fixed" mode, player dimensions are static and <a href="{$a}">Common settings</a> default values of width and height are used. In "Fixed width" mode, width is default, but height is determined based on aspect ratio setting. Finally, in "Responsive mode" player expands to 100% and its proportions are set according to aspect ratio setting.';
$string['displayfixed'] = 'Fixed';
$string['displayfixedwidth'] = 'Fixed width';
$string['displayresponsive'] = 'Responsive';
$string['downloadbutton'] = 'Download button';
$string['downloadbuttondesc'] = 'Add download button to the control bar.';
$string['downloadbuttontitle'] = 'Download Media';
$string['emptytitle'] = 'Allow empty title';
$string['emptytitledesc'] = 'If enabled, media that does not have a title attribute will have no title displayed (by default filename is used as title).';
$string['enabledevents'] = 'Events tracking';
$string['enabledeventsdesc'] = 'Selected events will be tracked and recorded in activity logs (viewable in Reports section of the course). Make sure you select only required ones, as selecting more will increase logged data. By default, only "started" and "completed" events are tracked, which indicates that the media playback started from beginning and completed respectively.';
$string['enabledextensions'] = 'Enabled extensions';
$string['enabledextensionsdesc'] = 'The list contains file extensions supported by the player. Only selected extensions will be handled by the player.';
$string['errornoselfhostedlibrary'] = 'Self-hosted player library is not found at /media/player/jwplayer/jwplayer/ directory in Moodle';
$string['errornolicensekey'] = 'Self-hosted player library requires license key';
$string['errornotconfigured'] = 'Player configuration is not complete. Please check settings.';
$string['eventplaybackcompleted'] = 'Media playback completed';
$string['eventplaybackfailed'] = 'Media playback failed';
$string['eventplaybackpaused'] = 'Media playback paused';
$string['eventplaybackresumed'] = 'Media playback resumed';
$string['eventplaybackseek'] = 'Media playback seeked';
$string['eventplaybackstarted'] = 'Media playback started';
$string['galabel'] = 'Event label';
$string['galabeldesc'] = 'Send another playlist property, e.g. <tt>title</tt> or <tt>mediaid</tt>, as your event label in Google Analytics. If not specified, the name of the video file being played will be used (default property name <tt>file</tt>).';
$string['general'] = 'General';
$string['googleanalytics'] = 'Google Analytics Integration';
$string['googleanalyticsconfig'] = 'Google Analytics';
$string['googleanalyticsconfigdesc'] = 'Please refer to documentation on the <a href="https://support.jwplayer.com/articles/how-to-integrate-with-google-analytics">JW Player website</a> for more information on Google Analytics integration.';
$string['googleanalyticsdesc'] = 'Enable integration with Google Analytics. Requires GA script to be added to page head, you can add it using <a href="{$a}">Additional HTML</a> site setting. Most recent type of embed called <tt>gtag.js</tt> is supported as well as with older types.';
$string['hostingmethod'] = 'Hosting implementation';
$string['hostingmethodcloud'] = 'Cloud-hosted';
$string['hostingmethoddesc'] = 'It is recommended to use cloud-hosted library if you need flexibility and control over features and player layout through JWPlayer dashboard. You need to specify library URL below to use cloud-hosted method. You can use self-hosted method if you have Enterprise subscription. In this case you need to download library on "Player Downloads & Keys" page of JWPlayer dashboard, unpack it and place contents in <tt>/media/player/jwplayer/jwplayer/</tt> directory in Moodle. Using self-hosted methods requires a license key.';
$string['hostingmethodself'] = 'Self-hosted';
$string['logerrors'] = 'Log errors';
$string['logerrorsdesc'] = 'If enabled, errors in the playback process, resulting in user\'s inability to watch media (such as when media is not available), will be recorded in the activity logs.';
$string['libraryhosting'] = 'Player library hosting';
$string['libraryurl'] = 'Cloud-hosted library URL';
$string['libraryurldesc'] = 'Library URL is required for cloud-hosted implementation. It can be obtaned from "Player Downloads & Keys" page of JWPlayer dashboard. In "Cloud Hosted Player Libraries" section, please select a player title from the dropdown menu and copy-paste Cloud Player Library URL.';
$string['licensekey'] = 'License key';
$string['licensekeydesc'] = 'License key is required for self-hosted player library.';
$string['playbackrates'] = 'Playback rate button';
$string['playbackratesdesc'] = 'Display a button in the controlbar to adjust playback speed. Select speeds that need to be available for users. To disable control, select only \'1x\' or leave no options selected.';
$string['pluginname'] = 'JW Player 8';
$string['privacy:metadata'] = 'JWPlayer media plugin for Moodle does not store any personal data, however JW Player technology provider (legally known as LongTail Ad Solutions, Inc.) may collect usage and tracking data. Please refer to JW Player Privacy Policy (https://www.jwplayer.com/privacy/) for more details.';
