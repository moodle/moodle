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
import ModalRegistry from 'core/modal_registry';
import {component} from 'tiny_recordrtc/common';

export default class Video extends BaseClass {
    configurePlayer() {
        return this.modalRoot.querySelector('video');
    }

    getSupportedTypes() {
        return [
            'video/webm;codecs=vp9,opus',
            'video/webm;codecs=h264,opus',
            'video/webm;codecs=vp8,opus',
        ];

    }

    getParsedRecordingOptions() {
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
        return `${prefix}-video.webm`;
    }

    static getModalClass() {
        const modalType = `${component}/video_recorder`;
        const registration = ModalRegistry.get(modalType);
        if (registration) {
            return registration.module;
        }

        const VideoModal = class extends Modal {
            static TYPE = modalType;
            static TEMPLATE = `${component}/video_recorder`;
        };

        ModalRegistry.register(VideoModal.TYPE, VideoModal, VideoModal.TEMPLATE);
        return VideoModal;
    }
}
