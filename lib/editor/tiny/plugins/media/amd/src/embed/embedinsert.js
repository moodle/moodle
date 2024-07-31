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
 * Tiny media plugin embed upload class.
 *
 * This handles the embed upload using url, drag-drop and repositories.
 *
 * @module      tiny_media/embed/embedinsert
 * @copyright   2024 Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {prefetchStrings} from 'core/prefetch';
import {getStrings} from 'core/str';
import {component} from "../common";
import {setPropertiesFromData} from '../helpers';
import Selectors from "../selectors";
import Dropzone from 'core/dropzone';

prefetchStrings('tiny_media', [
    'insertmedia',
    'addmediafilesdrop',
]);

export class EmbedInsert {

    constructor(data) {
        setPropertiesFromData(this, data); // Creates dynamic properties based on "data" param.
    }

    /**
     * Init the dropzone and lang strings.
     */
    init = async() => {
        const langStringKeys = [
            'insertmedia',
            'addmediafilesdrop',
        ];
        const langStringValues = await getStrings([...langStringKeys].map((key) => ({key, component})));
        this.langStrings = Object.fromEntries(langStringKeys.map((key, index) => [key, langStringValues[index]]));
        this.currentModal.setTitle(this.langStrings.insertmedia);

        // Let's init the dropzone if canShowDropZone is true and mediaType is null.
        if (this.canShowDropZone && !this.mediaType) {
            const dropZoneEle = document.querySelector(Selectors.EMBED.elements.dropzoneContainer);
            const dropZone = new Dropzone(
                dropZoneEle,
                'audio/*,video/*',
                files => {
                    this.handleUploadedFile(files);
                }
            );

            dropZone.setLabel(this.langStrings.addmediafilesdrop);
            dropZone.init();
        }
    };

    /**
     * Handles the uploaded file, initiates the upload process, and updates the UI during the upload.
     *
     * @param {FileList} files - The list of files to upload (usually from a file input field).
     */
    handleUploadedFile = (files) => {
        window.console.log(files);
    };
}
