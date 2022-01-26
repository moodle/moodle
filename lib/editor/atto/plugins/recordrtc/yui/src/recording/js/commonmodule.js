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
//

/**
 * Atto recordrtc library functions
 *
 * @package    atto_recordrtc
 * @author     Jesus Federico (jesus [at] blindsidenetworks [dt] com)
 * @author     Jacob Prud'homme (jacob [dt] prudhomme [at] blindsidenetworks [dt] com)
 * @copyright  2017 Blindside Networks Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// ESLint directives.
/* eslint-disable camelcase, no-alert, spaced-comment */

// JSHint directives.
/*global M */
/*jshint es5: true */
/*jshint onevar: false */
/*jshint shadow: true */

// Scrutinizer CI directives.
/** global: M */
/** global: Y */

M.atto_recordrtc = M.atto_recordrtc || {};

// Shorten access to M.atto_recordrtc.commonmodule namespace.
var cm = M.atto_recordrtc.commonmodule,
    am = M.atto_recordrtc.abstractmodule;

M.atto_recordrtc.commonmodule = {
    // Unitialized variables to be used by the other modules.
    editorScope: null,
    alertWarning: null,
    alertDanger: null,
    player: null,
    playerDOM: null, // Used to manipulate DOM directly.
    startStopBtn: null,
    uploadBtn: null,
    countdownSeconds: null,
    countdownTicker: null,
    recType: null,
    stream: null,
    mediaRecorder: null,
    chunks: null,
    blobSize: null,
    maxUploadSize: null,

    // Capture webcam/microphone stream.
    capture_user_media: function(mediaConstraints, successCallback, errorCallback) {
        window.navigator.mediaDevices.getUserMedia(mediaConstraints).then(successCallback).catch(errorCallback);
    },

    // Add chunks of audio/video to array when made available.
    handle_data_available: function(event) {
        // Push recording slice to array.
        cm.chunks.push(event.data);
        // Size of all recorded data so far.
        cm.blobSize += event.data.size;

        // If total size of recording so far exceeds max upload limit, stop recording.
        // An extra condition exists to avoid displaying alert twice.
        if (cm.blobSize >= cm.maxUploadSize) {
            if (!window.localStorage.getItem('alerted')) {
                window.localStorage.setItem('alerted', 'true');

                cm.startStopBtn.simulate('click');
                am.show_alert('nearingmaxsize');
            } else {
                window.localStorage.removeItem('alerted');
            }

            cm.chunks.pop();
        }
    },

    // Handle recording end.
    handle_stop: function() {
        // Set source of audio player.
        var blob = new window.Blob(cm.chunks, {type: cm.mediaRecorder.mimeType});
        cm.player.set('srcObject', null);
        cm.player.set('src', window.URL.createObjectURL(blob));

        // Show audio player with controls enabled, and unmute.
        cm.player.set('muted', false);
        cm.player.set('controls', true);
        cm.player.ancestor().ancestor().removeClass('hide');

        // Show upload button.
        cm.uploadBtn.ancestor().ancestor().removeClass('hide');
        cm.uploadBtn.set('textContent', M.util.get_string('attachrecording', 'atto_recordrtc'));
        cm.uploadBtn.set('disabled', false);

        // Get dialogue centered.
        cm.editorScope.getDialogue().centered();

        // Handle when upload button is clicked.
        cm.uploadBtn.on('click', function() {
            // Trigger error if no recording has been made.
            if (cm.chunks.length === 0) {
                am.show_alert('norecordingfound');
            } else {
                cm.uploadBtn.set('disabled', true);

                // Upload recording to server.
                cm.upload_to_server(cm.recType, function(progress, fileURLOrError) {
                    if (progress === 'ended') { // Insert annotation in text.
                        cm.uploadBtn.set('disabled', false);
                        cm.insert_annotation(cm.recType, fileURLOrError);
                    } else if (progress === 'upload-failed') { // Show error message in upload button.
                        cm.uploadBtn.set('disabled', false);
                        cm.uploadBtn.set('textContent',
                            M.util.get_string('uploadfailed', 'atto_recordrtc') + ' ' + fileURLOrError);
                    } else if (progress === 'upload-failed-404') { // 404 error = File too large in Moodle.
                        cm.uploadBtn.set('disabled', false);
                        cm.uploadBtn.set('textContent', M.util.get_string('uploadfailed404', 'atto_recordrtc'));
                    } else if (progress === 'upload-aborted') {
                        cm.uploadBtn.set('disabled', false);
                        cm.uploadBtn.set('textContent',
                            M.util.get_string('uploadaborted', 'atto_recordrtc') + ' ' + fileURLOrError);
                    } else {
                        cm.uploadBtn.set('textContent', progress);
                    }
                });
            }
        });
    },

    // Get everything set up to start recording.
    start_recording: function(type, stream) {
        // The options for the recording codecs and bitrates.
        var options = am.select_rec_options(type);
        cm.mediaRecorder = new window.MediaRecorder(stream, options);

        // Initialize MediaRecorder events and start recording.
        cm.mediaRecorder.ondataavailable = cm.handle_data_available;
        cm.mediaRecorder.onstop = cm.handle_stop;
        cm.mediaRecorder.start(1000); // Capture in 1s chunks. Must be set to work with Firefox.

        // Mute audio, distracting while recording.
        cm.player.set('muted', true);

        // Set recording timer to the time specified in the settings.
        if (type === 'audio') {
            cm.countdownSeconds = cm.editorScope.get('audiotimelimit');
        } else if (type === 'video') {
            cm.countdownSeconds = cm.editorScope.get('videotimelimit');
        } else {
            // Default timer.
            cm.countdownSeconds = cm.editorScope.get('defaulttimelimit');
        }
        cm.countdownSeconds++;
        var timerText = M.util.get_string('stoprecording', 'atto_recordrtc');
        timerText += ' (<span id="minutes"></span>:<span id="seconds"></span>)';
        cm.startStopBtn.setHTML(timerText);
        cm.set_time();
        cm.countdownTicker = window.setInterval(cm.set_time, 1000);

        // Make button clickable again, to allow stopping recording.
        cm.startStopBtn.set('disabled', false);
    },

    // Get everything set up to stop recording.
    stop_recording: function(stream) {
        // Stop recording stream.
        cm.mediaRecorder.stop();

        // Stop each individual MediaTrack.
        var tracks = stream.getTracks();
        for (var i = 0; i < tracks.length; i++) {
            tracks[i].stop();
        }
    },

    // Upload recorded audio/video to server.
    upload_to_server: function(type, callback) {
        var xhr = new window.XMLHttpRequest();

        // Get src media of audio/video tag.
        xhr.open('GET', cm.player.get('src'), true);
        xhr.responseType = 'blob';

        xhr.onload = function() {
            if (xhr.status === 200) { // If src media was successfully retrieved.
                // blob is now the media that the audio/video tag's src pointed to.
                var blob = this.response;

                // Generate filename with random ID and file extension.
                var fileName = (Math.random() * 1000).toString().replace('.', '');
                fileName += (type === 'audio') ? '-audio.ogg'
                                               : '-video.webm';

                // Create FormData to send to PHP filepicker-upload script.
                var formData = new window.FormData(),
                    filepickerOptions = cm.editorScope.get('host').get('filepickeroptions').link,
                    repositoryKeys = window.Object.keys(filepickerOptions.repositories);

                formData.append('repo_upload_file', blob, fileName);
                formData.append('itemid', filepickerOptions.itemid);

                for (var i = 0; i < repositoryKeys.length; i++) {
                    if (filepickerOptions.repositories[repositoryKeys[i]].type === 'upload') {
                        formData.append('repo_id', filepickerOptions.repositories[repositoryKeys[i]].id);
                        break;
                    }
                }

                formData.append('env', filepickerOptions.env);
                formData.append('sesskey', M.cfg.sesskey);
                formData.append('client_id', filepickerOptions.client_id);
                formData.append('savepath', '/');
                formData.append('ctx_id', filepickerOptions.context.id);

                // Pass FormData to PHP script using XHR.
                var uploadEndpoint = M.cfg.wwwroot + '/repository/repository_ajax.php?action=upload';
                cm.make_xmlhttprequest(uploadEndpoint, formData,
                    function(progress, responseText) {
                        if (progress === 'upload-ended') {
                            callback('ended', window.JSON.parse(responseText).url);
                        } else {
                            callback(progress);
                        }
                    }
                );
            }
        };

        xhr.send();
    },

    // Handle XHR sending/receiving/status.
    make_xmlhttprequest: function(url, data, callback) {
        var xhr = new window.XMLHttpRequest();

        xhr.onreadystatechange = function() {
            if ((xhr.readyState === 4) && (xhr.status === 200)) { // When request is finished and successful.
                callback('upload-ended', xhr.responseText);
            } else if (xhr.status === 404) { // When request returns 404 Not Found.
                callback('upload-failed-404');
            }
        };

        xhr.upload.onprogress = function(event) {
            callback(Math.round(event.loaded / event.total * 100) + "% " + M.util.get_string('uploadprogress', 'atto_recordrtc'));
        };

        xhr.upload.onerror = function(error) {
            callback('upload-failed', error);
        };

        xhr.upload.onabort = function(error) {
            callback('upload-aborted', error);
        };

        // POST FormData to PHP script that handles uploading/saving.
        xhr.open('POST', url);
        xhr.send(data);
    },

    // Makes 1min and 2s display as 1:02 on timer instead of 1:2, for example.
    pad: function(val) {
        var valString = val + "";

        if (valString.length < 2) {
            return "0" + valString;
        } else {
            return valString;
        }
    },

    // Functionality to make recording timer count down.
    // Also makes recording stop when time limit is hit.
    set_time: function() {
        cm.countdownSeconds--;

        cm.startStopBtn.one('span#seconds').set('textContent', cm.pad(cm.countdownSeconds % 60));
        cm.startStopBtn.one('span#minutes').set('textContent', cm.pad(window.parseInt(cm.countdownSeconds / 60, 10)));

        if (cm.countdownSeconds === 0) {
            cm.startStopBtn.simulate('click');
        }
    },

    // Generates link to recorded annotation to be inserted.
    create_annotation: function(type, recording_url) {
        var html = '';
        if (type == 'audio') {
            html = "<audio controls='true'>";
        } else { // Must be video.
            html = "<video controls='true'>";
        }

        html += "<source src='" + recording_url + "'>" + recording_url;

        if (type == 'audio') {
            html += "</audio>";
        } else { // Must be video.
            html += "</video>";
        }

        return html;
    },

    // Inserts link to annotation in editor text area.
    insert_annotation: function(type, recording_url) {
        var annotation = cm.create_annotation(type, recording_url);

        // Insert annotation link.
        // If user pressed "Cancel", just go back to main recording screen.
        if (!annotation) {
            cm.uploadBtn.set('textContent', M.util.get_string('attachrecording', 'atto_recordrtc'));
        } else {
            cm.editorScope.setLink(cm.editorScope, annotation);
        }
    }
};
