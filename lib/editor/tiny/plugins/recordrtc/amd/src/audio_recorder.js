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
import ModalRegistry from 'core/modal_registry';
import {component} from 'tiny_recordrtc/common';

export default class Audio extends BaseClass {
    configurePlayer() {
        return this.modalRoot.querySelector('audio');
    }

    getSupportedTypes() {
        return [
            'audio/webm;codecs=opus',
            'audio/ogg;codecs=opus',
        ];
    }

    getParsedRecordingOptions() {
        return {
            audioBitsPerSecond: parseInt(this.config.audiobitrate),
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
        return `${prefix}-audio.ogg`;
    }

    static getModalClass() {
        const modalType = `${component}/audio_recorder`;
        const registration = ModalRegistry.get(modalType);
        if (registration) {
            return registration.module;
        }

        const AudioModal = class extends Modal {
            static TYPE = modalType;
            static TEMPLATE = `${component}/audio_recorder`;
        };

        ModalRegistry.register(AudioModal.TYPE, AudioModal, AudioModal.TEMPLATE);
        return AudioModal;
    }
}
