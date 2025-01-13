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
 * Tiny Record RTC - audio recorder configuration.
 *
 * @module      tiny_recordrtc/audio_recorder
 * @copyright   2022 Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import BaseClass from './base_recorder';
import Modal from './modal';
import {component} from 'tiny_recordrtc/common';
import {convertMp3} from './convert_to_mp3';
import {add as addToast} from 'core/toast';

export default class Audio extends BaseClass {

    // A mapping of MIME types to their corresponding file extensions.
    fileExtensions = {
        'audio/ogg': 'ogg',
        'audio/mp4': 'mp4',
        'audio/webm': 'webm',
    };

    configurePlayer() {
        return this.modalRoot.querySelector('audio');
    }

    getSupportedTypes() {
        return [
            // Firefox supports webm and ogg but Chrome only supports ogg.
            // So we use ogg to maximize the compatibility.
            'audio/ogg;codecs=opus',

            // Safari supports mp4.
            'audio/mp4;codecs=opus',
            'audio/mp4;codecs=wav',
            'audio/mp4;codecs=mp3',

            // Set webm as a fallback.
            'audio/webm;codecs=opus',
        ];
    }

    getRecordingOptions() {
        return {
            audioBitsPerSecond: parseInt(this.config.audiobitrate),
            audioBitsPerSecondInKb: parseInt(this.config.audiobitrate / 1000),
        };
    }

    getMediaConstraints() {
        return {
            audio: true,
        };
    }

    getRecordingType() {
        return 'audio';
    }

    getTimeLimit() {
        return this.config.audiotimelimit;
    }

    getEmbedTemplateName() {
        return 'tiny_recordrtc/embed_audio';
    }

    getFileName(prefix) {
        return `${prefix}-audio.${this.getFileExtension()}`;
    }

    getFileExtension() {
        if (this.config.audiortcformat === 1) {
            return 'mp3';
        }

        const options = super.getParsedRecordingOptions(); // Call parent method.
        if (options?.mimeType) {
            const mimeType = options.mimeType.split(';')[0];
            return this.fileExtensions[mimeType];
        }

        window.console.warn(`Unknown file type for MediaRecorder API`);
        return '';
    }

    static getModalClass() {
        return class extends Modal {
            static TYPE = `${component}/audio_recorder`;
            static TEMPLATE = `${component}/audio_recorder`;
        };
    }

    async uploadRecording() {
        if (this.getFileExtension() === "mp3") {
            try {
                const options = this.getRecordingOptions();
                this.blob = await convertMp3(this.player.src, options.audioBitsPerSecondInKb);
                this.player.src = URL.createObjectURL(this.blob);
            } catch (error) {
                // Display a user-friendly error message
                const message = `MP3 conversion failed: ${error.message || 'Unknown error'}. Please try again.`;
                addToast(message, {type: 'error', delay: 6000});

                // Disable the upload button.
                this.setUploadButtonState(false);

                return;
            }
        }

        super.uploadRecording();
    }
}
