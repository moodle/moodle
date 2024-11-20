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
 * Tiny Record RTC - Video recorder configuration.
 *
 * @module      tiny_recordrtc/video_recorder
 * @copyright   2022 Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import BaseClass from './base_recorder';
import Modal from 'tiny_recordrtc/modal';
import {component} from 'tiny_recordrtc/common';

export default class Video extends BaseClass {
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
            // In addition to this, we can record in v9, but VideoJS does not support an mp4 containern with v9 codec
            // for playback. We leave it as a final option as a just-in-case.
            'video/mp4;codecs=h264,opus',
            'video/mp4;codecs=h264,wav',
            'video/mp4;codecs=v9,opus',
        ];

    }

    getRecordingOptions() {
        return {
            audioBitsPerSecond: parseInt(this.config.audiobitrate),
            videoBitsPerSecond: parseInt(this.config.videobitrate)
        };
    }

    getMediaConstraints() {
        return {
            audio: true,
            video: {
                width: {
                    ideal: 640,
                },
                height: {
                    ideal: 480,
                },
            },
        };
    }

    playOnCapture() {
        // Play the recording back on capture.
        return true;
    }

    getRecordingType() {
        return 'video';
    }

    getTimeLimit() {
        return this.config.videotimelimit;
    }

    getEmbedTemplateName() {
        return 'tiny_recordrtc/embed_video';
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

    static getModalClass() {
        return class extends Modal {
            static TYPE = `${component}/video_recorder`;
            static TEMPLATE = `${component}/video_recorder`;
        };
    }
}
