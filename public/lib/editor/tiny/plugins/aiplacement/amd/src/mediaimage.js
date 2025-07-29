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
 * AI Modal for Tiny.
 *
 * @module      tiny_aiplacement/mediaimage
 * @copyright   2024 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getString} from 'core/str';
import MediaImage from 'tiny_media/image/image';
import Notification from 'core/notification';
import {prefetchStrings} from 'core/prefetch';
import {MAX_LENGTH_ALT} from 'tiny_media/image/imagehelpers';

prefetchStrings('core_ai', [
    'contentwatermark',
]);

export default class AiMediaImage extends MediaImage {
    constructor(editor, url, alt) {
        super(editor); // Call the parent class constructor
        this.generatedImageUrl = url;
        this.altText = alt;
        getString('contentwatermark', 'core_ai').then((watermark) => {
            this.watermark = watermark;
            return;
        }).catch(Notification.exception);
    }

    getSelectedImage() {
        const imgElement = document.createElement('img');

        // Set attributes for the img element
        imgElement.src = this.generatedImageUrl;
        imgElement.alt = this.truncateAltText(this.altText);

        return imgElement;
    }

    /**
     * Truncate the alt text if it is longer than the maximum length.
     * @param {String} altText The alt text
     * @return {string} The truncated alt text
     */
    truncateAltText(altText) {
        const maximumAltTextLength = MAX_LENGTH_ALT;
        const watermark = ' - ' + this.watermark;
        const ellipsis = '...';

        // Append the watermark to the alt text.
        if (altText.length + watermark.length <= maximumAltTextLength) {
            altText = altText + watermark;
        } else {
            const remainingLength = maximumAltTextLength - watermark.length - ellipsis.length;
            altText = altText.substring(0, remainingLength) + ellipsis + watermark;
        }
        return altText;
    }
}
