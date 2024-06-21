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
 * Tiny Record RTC - Screen recorder configuration.
 *
 * @module      tiny_recordrtc/screen_recorder
 * @copyright   2024 The Open University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import BaseClass from './base_recorder';
import Modal from 'tiny_recordrtc/modal';
import {component} from 'tiny_recordrtc/common';
import {getString} from 'core/str';

export default class Screen extends BaseClass {
    configurePlayer() {
        return this.modalRoot.querySelector('video');
    }

    getSupportedTypes() {
        return [
            // Support webm as a preference.
            // This container supports both vp9, and vp8.
            // It does not support AVC1/h264 at all.
            // It is supported by Chromium, and Firefox browsers, but not Safari.
            'video/webm;codecs=vp9,opus',
            'video/webm;codecs=vp8,opus',

            // Fall back to mp4 if webm is not available.
            // The mp4 container supports v9, and h264 but neither of these are supported for recording on other
            // browsers.
            // In addition to this, we can record in v9, but VideoJS does not support a mp4 container with v9 codec
            // for playback. We leave it as a final option as a just-in-case.
            'video/mp4;codecs=h264,opus',
            'video/mp4;codecs=h264,wav',
            'video/mp4;codecs=v9,opus',
        ];

    }

    getRecordingOptions() {
        return {
            videoBitsPerSecond: parseInt(this.config.screenbitrate),
            videoWidth: parseInt(this.config.videoscreenwidth),
            videoHeight: parseInt(this.config.videoscreenheight),
        };
    }

    getMediaConstraints() {
        return {
            audio: true,
            systemAudio: 'exclude',
            video: {
                displaySurface: 'monitor',
                frameRate: {ideal: 24},
                width: {
                    max: parseInt(this.config.videoscreenwidth),
                },
                height: {
                    max: parseInt(this.config.videoscreenheight),
                },
            },
        };
    }

    playOnCapture() {
        // Play the recording back on capture.
        return true;
    }

    getRecordingType() {
        return 'screen';
    }

    getTimeLimit() {
        return this.config.screentimelimit;
    }

    getEmbedTemplateName() {
        return 'tiny_recordrtc/embed_screen';
    }

    getFileName(prefix) {
        return `${prefix}-video.${this.getFileExtension()}`;
    }

    getFileExtension() {
        if (window.MediaRecorder.isTypeSupported('audio/webm')) {
            return 'webm';
        } else if (window.MediaRecorder.isTypeSupported('audio/mp4')) {
            return 'mp4';
        }

        window.console.warn(`Unknown file type for MediaRecorder API`);
        return '';
    }

    async captureUserMedia() {
        // Screen recording requires both audio and the screen, and we need to get them both together.
        const audioPromise = navigator.mediaDevices.getUserMedia({audio: true});
        const screenPromise = navigator.mediaDevices.getDisplayMedia(this.getMediaConstraints());
        // If the audioPromise is "rejected" (indicating that the user does not want to share their voice),
        // we will proceed to record their screen without audio.
        // Therefore, we will use Promise.allSettled instead of Promise.all.
        await Promise.allSettled([audioPromise, screenPromise]).then(this.combineAudioAndScreenRecording.bind(this));
    }

    /**
     * For starting screen recording, once we have both audio and video, combine them.
     *
     * @param {Object[]} results from the above Promise.allSettled call.
     */
    combineAudioAndScreenRecording(results) {
        const [audioData, screenData] = results;
        if (screenData.status !== 'fulfilled') {
            // If the user does not grant screen permission show warning popup.
            this.handleCaptureFailure(screenData.reason);
            return;
        }

        const screenStream = screenData.value;
        // Prepare to handle if the user clicks the browser's "Stop Sharing Screen" button.
        screenStream.getVideoTracks()[0].addEventListener('ended', this.handleStopScreenSharing.bind(this));

        // Handle microphone.
        if (audioData.status !== 'fulfilled') {
            // We could not get audio. In this case, we just continue without audio.
            this.handleCaptureSuccess(screenStream);
            return;
        }
        const audioStream = audioData.value;
        // Merge the video track from the media stream with the audio track from the microphone stream
        // and stop any unnecessary tracks to ensure that the recorded video has microphone sound.
        const composedStream = new MediaStream();
        screenStream.getTracks().forEach(function(track) {
            if (track.kind === 'video') {
                composedStream.addTrack(track);
            } else {
                track.stop();
            }
        });
        audioStream.getAudioTracks().forEach(function(micTrack) {
            composedStream.addTrack(micTrack);
        });

        this.handleCaptureSuccess(composedStream);
    }

    /**
     * Callback that is called by the user clicking Stop screen sharing on the browser.
     */
    handleStopScreenSharing() {
        if (this.isRecording() || this.isPaused()) {
            this.requestRecordingStop();
            this.cleanupStream();
        } else {
            this.setRecordButtonState(false);
            this.displayAlert(
                getString('screensharingstopped_title', component),
                getString('screensharingstopped', component)
            );
        }
    }

    handleRecordingStartStopRequested() {
        if (this.isRecording() || this.isPaused()) {
            this.requestRecordingStop();
            this.cleanupStream();
        } else {
            this.startRecording();
        }
    }

    static getModalClass() {
        return class extends Modal {
            static TYPE = `${component}/screen_recorder`;
            static TEMPLATE = `${component}/screen_recorder`;
        };
    }
}
