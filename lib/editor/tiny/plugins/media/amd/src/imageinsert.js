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
 * Tiny media plugin image insertion class for Moodle.
 *
 * @module      tiny_media/imageinsert
 * @copyright   2024 Meirza <meirza.arson@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {prefetchStrings} from 'core/prefetch';
import {getStrings} from 'core/str';
import {component} from "./common";

prefetchStrings('tiny_media', [
    'insertimage',
    'enterurl',
    'enterurlor',
    'imageurlrequired',
    'uploading',
    'loading',
]);

export class ImageInsert {

    constructor(
        root,
        editor,
        currentModal,
        canShowFilePicker,
        canShowDropZone,
    ) {
        this.root = root;
        this.editor = editor;
        this.currentModal = currentModal;
        this.canShowFilePicker = canShowFilePicker;
        this.canShowDropZone = canShowDropZone;
    }

    init = async function() {
        // Get the localization lang strings and turn them into object.
        const langStringKeys = [
            'insertimage',
            'enterurl',
            'enterurlor',
            'imageurlrequired',
            'uploading',
            'loading',
        ];
        const langStringvalues = await getStrings([...langStringKeys].map((key) => ({key, component})));

        // Convert array to object.
        this.langStrings = Object.fromEntries(langStringKeys.map((key, index) => [key, langStringvalues[index]]));
        this.currentModal.setTitle(this.langStrings.insertimage);
    };
}