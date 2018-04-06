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
 * Strings for component 'atto_recordrtc', language 'en'.
 *
 * @package    atto_recordrtc
 * @author     Jesus Federico (jesus [at] blindsidenetworks [dt] com)
 * @author     Jacob Prud'homme (jacob [dt] prudhomme [at] blindsidenetworks [dt] com)
 * @copyright  2017 Blindside Networks Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'RecordRTC';
$string['settings'] = 'RecordRTC settings';
$string['audiortc'] = 'Insert audio recording';
$string['videortc'] = 'Insert video recording';

$string['onlyaudio'] = 'Audio only';
$string['onlyvideo'] = 'Video only';
$string['both'] = 'Audio and Video';
$string['allowedtypes'] = 'Allowed types';
$string['allowedtypes_desc'] = 'Which recording buttons should appear in Atto';
$string['audiobitrate'] = 'Audio bitrate';
$string['audiobitrate_desc'] = 'Quality of audio recording (larger number means higher quality)';
$string['videobitrate'] = 'Video bitrate';
$string['videobitrate_desc'] = 'Quality of video recording (larger number means higher quality)';
$string['timelimit'] = 'Time limit in seconds';
$string['timelimit_desc'] = 'Maximum recording length allowed for the audio/video clips';

$string['nowebrtc_title'] = 'WebRTC not supported';
$string['nowebrtc'] = 'Your browser offers limited or no support for WebRTC technologies yet, and cannot be used with this plugin. Please switch or upgrade your browser';
$string['gumabort_title'] = 'Something happened';
$string['gumabort'] = 'Something strange happened which prevented the webcam/microphone from being used';
$string['gumnotallowed_title'] = 'Wrong permissions';
$string['gumnotallowed'] = 'The user must allow the browser access to the webcam/microphone';
$string['gumnotfound_title'] = 'Device missing';
$string['gumnotfound'] = 'There is no input device connected or enabled';
$string['gumnotreadable_title'] = 'Hardware error';
$string['gumnotreadable'] = 'Something is preventing the browser from accessing the webcam/microphone';
$string['gumoverconstrained_title'] = 'Problem with constraints';
$string['gumoverconstrained'] = 'The current webcam/microphone can not produce a stream with the required constraints';
$string['gumsecurity_title'] = 'No support for insecure connection';
$string['gumsecurity'] = 'Your browser does not support recording over an insecure connection and must close the plugin';
$string['gumtype_title'] = 'No constraints specified';
$string['gumtype'] = 'Tried to get stream from the webcam/microphone, but no constraints were specified';
$string['insecurealert_title'] = 'Insecure connection!';
$string['insecurealert'] = 'Your browser might not allow this plugin to work unless it is used either over HTTPS or from localhost';
$string['browseralert_title'] = 'Warning!';
$string['browseralert'] = 'Use Firefox >= 29, Chrome >= 49 or Opera >= 36 for best experience';
$string['startrecording'] = 'Start Recording';
$string['recordagain'] = 'Record Again';
$string['stoprecording'] = 'Stop Recording';
$string['recordingfailed'] = 'Recording failed, try again';
$string['attachrecording'] = 'Attach Recording as Annotation';
$string['norecordingfound_title'] = 'No recording found';
$string['norecordingfound'] = 'Something appears to have gone wrong, it seems nothing has been recorded';
$string['nearingmaxsize_title'] = 'Recording stopped';
$string['nearingmaxsize'] = 'You have attained the maximum size limit for file uploads';
$string['uploadprogress'] = 'completed';
$string['uploadfailed'] = 'Upload failed:';
$string['uploadfailed404'] = 'Upload failed: file too large';
$string['uploadaborted'] = 'Upload aborted:';
$string['annotationprompt'] = 'What should the annotation appear as?';
$string['annotation:audio'] = 'Audio annotation';
$string['annotation:video'] = 'Video annotation';
