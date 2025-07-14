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
 * Tiny media plugin class helpers for image and embed.
 *
 * @module      tiny_media/mediabase
 * @copyright   2024 Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {
    isPercentageValue,
    hideElements,
    showElements,
} from './helpers';
import Selectors from './selectors';

export class MediaBase {

    /**
     * Handles the selection of media size options and updates the form inputs accordingly.
     *
     * @param {string} option - The selected media size option ("original" or "custom").
     */
    sizeChecked = async(option) => {
        const widthInput = this.root.querySelector(Selectors[this.selectorType].elements.width);
        const heightInput = this.root.querySelector(Selectors[this.selectorType].elements.height);
        if (option === "original") {
            this.sizeOriginalChecked();
            widthInput.value = this.mediaDimensions.width;
            heightInput.value = this.mediaDimensions.height;
        } else if (option === "custom") {
            this.sizeCustomChecked();
            widthInput.value = this.currentWidth;
            heightInput.value = this.currentHeight;

            // If the current size is equal to the original size and selectorType = IMAGE,
            // then check the Keep proportion checkbox.
            if (
                this.selectorType === Selectors.IMAGE.type &&
                this.currentWidth === this.mediaDimensions.width &&
                this.currentHeight === this.mediaDimensions.height
            ) {
                const constrainField = this.root.querySelector(Selectors[this.selectorType].elements.constrain);
                constrainField.checked = true;
            }
        }
        this.autoAdjustSize();
    };

    /**
     * Handles the selection of the "Original Size" option and updates the form elements accordingly.
     */
    sizeOriginalChecked() {
        this.root.querySelector(Selectors[this.selectorType].elements.sizeOriginal).checked = true;
        this.root.querySelector(Selectors[this.selectorType].elements.sizeCustom).checked = false;
        hideElements(Selectors[this.selectorType].elements.properties, this.root);
    }

    /**
     * Handles the selection of the "Custom Size" option and updates the form elements accordingly.
     */
    sizeCustomChecked() {
        this.root.querySelector(Selectors[this.selectorType].elements.sizeOriginal).checked = false;
        this.root.querySelector(Selectors[this.selectorType].elements.sizeCustom).checked = true;
        showElements(Selectors[this.selectorType].elements.properties, this.root);
    }

    /**
     * Auto adjust the media width/height.
     * It is put here so image.js and/or friends can extend this class and call this for media proportion.
     *
     * @param {boolean} forceHeight Whether set by height or not
     */
    autoAdjustSize = (forceHeight = false) => {
        // If we do not know the media size, do not do anything.
        if (!this.mediaDimensions) {
            return;
        }

        const widthField = this.root.querySelector(Selectors[this.selectorType].elements.width);
        const heightField = this.root.querySelector(Selectors[this.selectorType].elements.height);

        const normalizeFieldData = (fieldData) => {
            fieldData.isPercentageValue = isPercentageValue(fieldData.field.value);
            if (fieldData.isPercentageValue) {
                fieldData.percentValue = parseInt(fieldData.field.value, 10);
                fieldData.pixelSize = this.mediaDimensions[fieldData.type] / 100 * fieldData.percentValue;
            } else {
                fieldData.pixelSize = parseInt(fieldData.field.value, 10);
                fieldData.percentValue = fieldData.pixelSize / this.mediaDimensions[fieldData.type] * 100;
            }

            return fieldData;
        };

        const getKeyField = () => {
            const getValue = () => {
                if (forceHeight) {
                    return {
                        field: heightField,
                        type: 'height',
                    };
                } else {
                    return {
                        field: widthField,
                        type: 'width',
                    };
                }
            };

            const currentValue = getValue();
            if (currentValue.field.value === '') {
                currentValue.field.value = this.mediaDimensions[currentValue.type];
            }

            return normalizeFieldData(currentValue);
        };

        const getRelativeField = () => {
            if (forceHeight) {
                return normalizeFieldData({
                    field: widthField,
                    type: 'width',
                });
            } else {
                return normalizeFieldData({
                    field: heightField,
                    type: 'height',
                });
            }
        };

        // Now update with the new values.
        const constrainField = this.root.querySelector(Selectors[this.selectorType].elements.constrain); // Only image.
        if ((constrainField && constrainField.checked) || this.mediaType === 'video') {
            const keyField = getKeyField();
            const relativeField = getRelativeField();
            // We are keeping the media in proportion.
            // Calculate the size for the relative field.
            if (keyField.isPercentageValue) {
                // In proportion, so the percentages are the same.
                relativeField.field.value = keyField.field.value;
                relativeField.percentValue = keyField.percentValue;
            } else {
                relativeField.pixelSize = Math.round(
                    keyField.pixelSize / this.mediaDimensions[keyField.type] * this.mediaDimensions[relativeField.type]
                );
                relativeField.field.value = relativeField.pixelSize;
            }
        }

        if (this.selectorType === Selectors.IMAGE.type) {
            // Store the custom width and height to reuse.
            this.currentWidth = Number(widthField.value) !== this.mediaDimensions.width ? widthField.value : this.currentWidth;
            this.currentHeight = Number(heightField.value) !== this.mediaDimensions.height ? heightField.value : this.currentHeight;
        }
    };
}
