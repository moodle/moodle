YUI.add('moodle-atto_recordrtc-recording', function (Y, NAME) {

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

    getFileExtension: function(type) {
        if (type === 'audio') {
            if (window.MediaRecorder.isTypeSupported('audio/ogg')) {
                return 'ogg';
            } else if (window.MediaRecorder.isTypeSupported('audio/mp4')) {
                return 'mp4';
            }
        } else {
            if (window.MediaRecorder.isTypeSupported('audio/webm')) {
                return 'webm';
            } else if (window.MediaRecorder.isTypeSupported('audio/mp4')) {
                return 'mp4';
            }
        }

        window.console.warn('Unknown file type for MediaRecorder API');
        return '';
    },

    // Upload recorded audio/video to server.
    upload_to_server: function(type, callback) {
        var xhr = new window.XMLHttpRequest();
        var fileExtension = this.getFileExtension(type);

        // Get src media of audio/video tag.
        xhr.open('GET', cm.player.get('src'), true);
        xhr.responseType = 'blob';

        xhr.onload = function() {
            if (xhr.status === 200) { // If src media was successfully retrieved.
                // blob is now the media that the audio/video tag's src pointed to.
                var blob = this.response;

                // Generate filename with random ID and file extension.
                var fileName = (Math.random() * 1000).toString().replace('.', '');
                fileName += (type === 'audio') ? '-audio.' + fileExtension
                                               : '-video.' + fileExtension;

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
 * Atto recordrtc library functions for checking browser compatibility
 *
 * @package    atto_recordrtc
 * @author     Jesus Federico (jesus [at] blindsidenetworks [dt] com)
 * @author     Jacob Prud'homme (jacob [dt] prudhomme [at] blindsidenetworks [dt] com)
 * @copyright  2017 Blindside Networks Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// ESLint directives.
/* eslint-disable camelcase */

// Scrutinizer CI directives.
/** global: M */

M.atto_recordrtc = M.atto_recordrtc || {};

// Shorten access to module namespaces.
var cm = M.atto_recordrtc.commonmodule,
    am = M.atto_recordrtc.abstractmodule;

M.atto_recordrtc.compatcheckmodule = {
    // Show alert and close plugin if browser does not support WebRTC at all.
    check_has_gum: function() {
        if (!(navigator.mediaDevices && window.MediaRecorder)) {
            am.show_alert('nowebrtc', function() {
                cm.editorScope.closeDialogue(cm.editorScope);
            });
        }
    },

    // Notify and redirect user if plugin is used from insecure location.
    check_secure: function() {
        var isSecureOrigin = (window.location.protocol === 'https:') ||
                             (window.location.host.indexOf('localhost') !== -1);

        if (!isSecureOrigin) {
            cm.alertDanger.ancestor().ancestor().removeClass('hide');
        }
    },
};
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
 * Atto recordrtc library functions for function abstractions
 *
 * @package    atto_recordrtc
 * @author     Jesus Federico (jesus [at] blindsidenetworks [dt] com)
 * @author     Jacob Prud'homme (jacob [dt] prudhomme [at] blindsidenetworks [dt] com)
 * @copyright  2017 Blindside Networks Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// ESLint directives.
/* eslint-disable camelcase */

// Scrutinizer CI directives.
/** global: M */
/** global: Y */

M.atto_recordrtc = M.atto_recordrtc || {};

// Shorten access to module namespaces.
var cm = M.atto_recordrtc.commonmodule,
    am = M.atto_recordrtc.abstractmodule;

M.atto_recordrtc.abstractmodule = {
    // A helper for making a Moodle alert appear.
    // Subject is the content of the alert (which error ther alert is for).
    // Possibility to add on-alert-close event.
    show_alert: function(subject, onCloseEvent) {
        Y.use('moodle-core-notification-alert', function() {
            var dialogue = new M.core.alert({
                title: M.util.get_string(subject + '_title', 'atto_recordrtc'),
                message: M.util.get_string(subject, 'atto_recordrtc')
            });

            if (onCloseEvent) {
                dialogue.after('complete', onCloseEvent);
            }
        });
    },

    // Handle getUserMedia errors.
    handle_gum_errors: function(error, commonConfig) {
        var btnLabel = M.util.get_string('recordingfailed', 'atto_recordrtc'),
            treatAsStopped = function() {
                commonConfig.onMediaStopped(btnLabel);
            };

        // Changes 'CertainError' -> 'gumcertain' to match language string names.
        var stringName = 'gum' + error.name.replace('Error', '').toLowerCase();

        // After alert, proceed to treat as stopped recording, or close dialogue.
        if (stringName !== 'gumsecurity') {
            am.show_alert(stringName, treatAsStopped);
        } else {
            am.show_alert(stringName, function() {
                cm.editorScope.closeDialogue(cm.editorScope);
            });
        }
    },

    // Select best options for the recording codec.
    select_rec_options: function(recType) {
        var types, options;

        if (recType === 'audio') {
            types = [
                // Firefox supports webm and ogg but Chrome only supports ogg.
                // So we use ogg to maximize the compatibility.
                'audio/ogg;codecs=opus',

                // Safari supports mp4.
                'audio/mp4;codecs=opus',
                'audio/mp4;codecs=wav',
                'audio/mp4;codecs=mp3',
            ];
            options = {
                audioBitsPerSecond: window.parseInt(cm.editorScope.get('audiobitrate'))
            };
        } else {
            types = [
                // Support webm as a preference.
                // This container supports both vp9, and vp8.
                // It does not support AVC1/h264 at all.
                // It is supported by Chromium, and Firefox browsers, but not Safari.
                'video/webm;codecs=vp9,opus',
                'video/webm;codecs=vp8,opus',

                // Fall back to mp4 if webm is not available.
                // The mp4 container supports v9, and h264 but neither of these are supported for recording on other
                // browsers.
                // In addition to this, we can record in v9, but VideoJS does not support an mp4 containern with v9 codec
                // for playback. We leave it as a final option as a just-in-case.
                'video/mp4;codecs=h264,opus',
                'video/mp4;codecs=h264,wav',
                'video/mp4;codecs=v9,opus',
            ];
            options = {
                audioBitsPerSecond: window.parseInt(cm.editorScope.get('audiobitrate')),
                videoBitsPerSecond: window.parseInt(cm.editorScope.get('videobitrate'))
            };
        }

        var possibleTypes = types.reduce(function(result, type) {
            result.push(type);
            // Safari seems to use codecs: instead of codecs=.
            // It is safe to add both, so we do, but we want them to remain in order.
            result.push(type.replace('=', ':'));
            return result;
        }, []);

        var compatTypes = possibleTypes.filter(function(type) {
            return window.MediaRecorder.isTypeSupported(type);
        });

        if (compatTypes.length !== 0) {
            options.mimeType = compatTypes[0];
        }

        return options;
    }
};
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
/* eslint-disable camelcase, spaced-comment */

// Scrutinizer CI directives.
/** global: M */
/** global: Y */

M.atto_recordrtc = M.atto_recordrtc || {};

// Shorten access to module namespaces.
var cm = M.atto_recordrtc.commonmodule,
    am = M.atto_recordrtc.abstractmodule,
    ccm = M.atto_recordrtc.compatcheckmodule;

M.atto_recordrtc.audiomodule = {
    init: function(scope) {
        // Assignment of global variables.
        cm.editorScope = scope; // Allows access to the editor's "this" context.
        cm.alertWarning = Y.one('div#alert-warning');
        cm.alertDanger = Y.one('div#alert-danger');
        cm.player = Y.one('audio#player');
        cm.playerDOM = document.querySelector('audio#player');
        cm.startStopBtn = Y.one('button#start-stop');
        cm.uploadBtn = Y.one('button#upload');
        cm.recType = 'audio';
        cm.maxUploadSize = scope.get('maxrecsize');

        // Show alert and close plugin if WebRTC is not supported.
        ccm.check_has_gum();
        // Show alert and redirect user if connection is not secure.
        ccm.check_secure();

        // Run when user clicks on "record" button.
        cm.startStopBtn.on('click', function() {
            cm.startStopBtn.set('disabled', true);

            // If button is displaying "Start Recording" or "Record Again".
            if ((cm.startStopBtn.get('textContent') === M.util.get_string('startrecording', 'atto_recordrtc')) ||
                (cm.startStopBtn.get('textContent') === M.util.get_string('recordagain', 'atto_recordrtc')) ||
                (cm.startStopBtn.get('textContent') === M.util.get_string('recordingfailed', 'atto_recordrtc'))) {
                // Make sure the audio player and upload button are not shown.
                cm.player.ancestor().ancestor().addClass('hide');
                cm.uploadBtn.ancestor().ancestor().addClass('hide');

                // Change look of recording button.
                cm.startStopBtn.replaceClass('btn-outline-danger', 'btn-danger');

                // Empty the array containing the previously recorded chunks.
                cm.chunks = [];
                cm.blobSize = 0;
                cm.uploadBtn.detach('click');

                // Initialize common configurations.
                var commonConfig = {
                    // When the stream is captured from the microphone/webcam.
                    onMediaCaptured: function(stream) {
                        // Make audio stream available at a higher level by making it a property of the common module.
                        cm.stream = stream;

                        cm.start_recording(cm.recType, cm.stream);
                    },

                    // Revert button to "Record Again" when recording is stopped.
                    onMediaStopped: function(btnLabel) {
                        cm.startStopBtn.set('textContent', btnLabel);
                        cm.startStopBtn.set('disabled', false);
                        cm.startStopBtn.replaceClass('btn-danger', 'btn-outline-danger');
                    },

                    // Handle recording errors.
                    onMediaCapturingFailed: function(error) {
                        am.handle_gum_errors(error, commonConfig);
                    }
                };

                // Capture audio stream from microphone.
                M.atto_recordrtc.audiomodule.capture_audio(commonConfig);
            } else { // If button is displaying "Stop Recording".
                // First of all clears the countdownTicker.
                window.clearInterval(cm.countdownTicker);

                // Disable "Record Again" button for 1s to allow background processing (closing streams).
                window.setTimeout(function() {
                    cm.startStopBtn.set('disabled', false);
                }, 1000);

                // Stop recording.
                cm.stop_recording(cm.stream);

                // Change button to offer to record again.
                cm.startStopBtn.set('textContent', M.util.get_string('recordagain', 'atto_recordrtc'));
                cm.startStopBtn.replaceClass('btn-danger', 'btn-outline-danger');
            }

            // Get dialogue centered.
            cm.editorScope.getDialogue().centered();
        });
    },

    // Setup to get audio stream from microphone.
    capture_audio: function(config) {
        cm.capture_user_media(
            // Media constraints.
            {
                audio: true
            },

            // Success callback.
            function(audioStream) {
                // Set audio player source to microphone stream.
                cm.playerDOM.srcObject = audioStream;

                config.onMediaCaptured(audioStream);
            },

            // Error callback.
            function(error) {
                config.onMediaCapturingFailed(error);
            }
        );
    }
};
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
/* eslint-disable camelcase, spaced-comment */

// Scrutinizer CI directives.
/** global: M */
/** global: Y */

M.atto_recordrtc = M.atto_recordrtc || {};

// Shorten access to module namespaces.
var cm = M.atto_recordrtc.commonmodule,
    am = M.atto_recordrtc.abstractmodule,
    ccm = M.atto_recordrtc.compatcheckmodule;

M.atto_recordrtc.videomodule = {
    init: function(scope) {
        // Assignment of global variables.
        cm.editorScope = scope; // Allows access to the editor's "this" context.
        cm.alertWarning = Y.one('div#alert-warning');
        cm.alertDanger = Y.one('div#alert-danger');
        cm.player = Y.one('video#player');
        cm.playerDOM = document.querySelector('video#player');
        cm.startStopBtn = Y.one('button#start-stop');
        cm.uploadBtn = Y.one('button#upload');
        cm.recType = 'video';
        cm.maxUploadSize = scope.get('maxrecsize');

        // Show alert and close plugin if WebRTC is not supported.
        ccm.check_has_gum();
        // Show alert and redirect user if connection is not secure.
        ccm.check_secure();

        // Run when user clicks on "record" button.
        cm.startStopBtn.on('click', function() {
            cm.startStopBtn.set('disabled', true);

            // If button is displaying "Start Recording" or "Record Again".
            if ((cm.startStopBtn.get('textContent') === M.util.get_string('startrecording', 'atto_recordrtc')) ||
                (cm.startStopBtn.get('textContent') === M.util.get_string('recordagain', 'atto_recordrtc')) ||
                (cm.startStopBtn.get('textContent') === M.util.get_string('recordingfailed', 'atto_recordrtc'))) {
                // Make sure the upload button is not shown.
                cm.uploadBtn.ancestor().ancestor().addClass('hide');

                // Change look of recording button.
                cm.startStopBtn.replaceClass('btn-outline-danger', 'btn-danger');

                // Empty the array containing the previously recorded chunks.
                cm.chunks = [];
                cm.blobSize = 0;
                cm.uploadBtn.detach('click');

                // Initialize common configurations.
                var commonConfig = {
                    // When the stream is captured from the microphone/webcam.
                    onMediaCaptured: function(stream) {
                        // Make video stream available at a higher level by making it a property of the common module.
                        cm.stream = stream;

                        cm.start_recording(cm.recType, cm.stream);
                    },

                    // Revert button to "Record Again" when recording is stopped.
                    onMediaStopped: function(btnLabel) {
                        cm.startStopBtn.set('textContent', btnLabel);
                        cm.startStopBtn.set('disabled', false);
                        cm.startStopBtn.replaceClass('btn-danger', 'btn-outline-danger');
                    },

                    // Handle recording errors.
                    onMediaCapturingFailed: function(error) {
                        am.handle_gum_errors(error, commonConfig);
                    }
                };

                // Show video tag without controls to view webcam stream.
                cm.player.ancestor().ancestor().removeClass('hide');
                cm.player.set('controls', false);

                // Capture audio+video stream from webcam/microphone.
                M.atto_recordrtc.videomodule.capture_audio_video(commonConfig);
            } else { // If button is displaying "Stop Recording".
                // First of all clears the countdownTicker.
                window.clearInterval(cm.countdownTicker);

                // Disable "Record Again" button for 1s to allow background processing (closing streams).
                window.setTimeout(function() {
                    cm.startStopBtn.set('disabled', false);
                }, 1000);

                // Stop recording.
                cm.stop_recording(cm.stream);

                // Change button to offer to record again.
                cm.startStopBtn.set('textContent', M.util.get_string('recordagain', 'atto_recordrtc'));
                cm.startStopBtn.replaceClass('btn-danger', 'btn-outline-danger');
            }

            // Get dialogue centered.
            cm.editorScope.getDialogue().centered();
        });
    },

    // Setup to get audio+video stream from microphone/webcam.
    capture_audio_video: function(config) {
        cm.capture_user_media(
            // Media constraints.
            {
                audio: true,
                video: {
                    width: {ideal: 640},
                    height: {ideal: 480}
                }
            },

            // Success callback.
            function(audioVideoStream) {
                // Set video player source to microphone+webcam stream, and play it back as it's recording.
                cm.playerDOM.srcObject = audioVideoStream;
                cm.playerDOM.play();

                config.onMediaCaptured(audioVideoStream);
            },

            // Error callback.
            function(error) {
                config.onMediaCapturingFailed(error);
            }
        );
    }
};


}, '@VERSION@', {"requires": ["moodle-atto_recordrtc-button"]});
