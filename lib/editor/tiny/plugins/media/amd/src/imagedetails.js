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
 * Tiny media plugin image details class for Moodle.
 *
 * @module      tiny_media/imagedetails
 * @copyright   2024 Meirza <meirza.arson@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Config from 'core/config';
import ModalEvents from 'core/modal_events';
import Notification from 'core/notification';
import Pending from 'core/pending';
import Selectors from './selectors';
import Templates from 'core/templates';
import {getString} from 'core/str';
import {ImageInsert} from 'tiny_media/imageinsert';
import {
    bodyImageInsert,
    footerImageInsert,
    showElements,
    hideElements,
    isPercentageValue,
} from 'tiny_media/imagehelpers';

export class ImageDetails {
    DEFAULTS = {
        WIDTH: 160,
        HEIGHT: 160,
    };

    rawImageDimensions = null;

    constructor(
        root,
        editor,
        currentModal,
        canShowFilePicker,
        canShowDropZone,
        currentUrl,
        image,
    ) {
        this.root = root;
        this.editor = editor;
        this.currentModal = currentModal;
        this.canShowFilePicker = canShowFilePicker;
        this.canShowDropZone = canShowDropZone;
        this.currentUrl = currentUrl;
        this.image = image;
    }

    init = function() {
        this.currentModal.setTitle(getString('imagedetails', 'tiny_media'));
        this.imageTypeChecked();
        this.presentationChanged();
        this.storeImageDimensions(this.image);
        this.setImageDimensions();
        this.registerEventListeners();
    };

    /**
     * Loads and displays a preview image based on the provided URL, and handles image loading events.
     */
    loadInsertImage = async function() {
        const templateContext = {
            elementid: this.editor.id,
            showfilepicker: this.canShowFilePicker,
            showdropzone: this.canShowDropZone,
        };

        Promise.all([bodyImageInsert(templateContext, this.root), footerImageInsert(templateContext, this.root)])
            .then(() => {
                const imageinsert = new ImageInsert(
                    this.root,
                    this.editor,
                    this.currentModal,
                    this.canShowFilePicker,
                    this.canShowDropZone,
                );
                imageinsert.init();
                return;
            })
            .catch(error => {
                window.console.log(error);
            });
    };

    storeImageDimensions(image) {
        // Store dimensions of the raw image, falling back to defaults for images without dimensions (e.g. SVG).
        this.rawImageDimensions = {
            width: image.width || this.DEFAULTS.WIDTH,
            height: image.height || this.DEFAULTS.HEIGHT,
        };

        const getCurrentWidth = (element) => {
            if (element.value === '') {
                element.value = this.rawImageDimensions.width;
            }
            return element.value;
        };

        const getCurrentHeight = (element) => {
            if (element.value === '') {
                element.value = this.rawImageDimensions.height;
            }
            return element.value;
        };

        const widthInput = this.root.querySelector(Selectors.IMAGE.elements.width);
        const currentWidth = getCurrentWidth(widthInput);

        const heightInput = this.root.querySelector(Selectors.IMAGE.elements.height);
        const currentHeight = getCurrentHeight(heightInput);

        const preview = this.root.querySelector(Selectors.IMAGE.elements.preview);
        preview.setAttribute('src', image.src);
        preview.style.display = '';

        // Ensure the checkbox always in unchecked status when an image loads at first.
        const constrain = this.root.querySelector(Selectors.IMAGE.elements.constrain);
        if (isPercentageValue(currentWidth) && isPercentageValue(currentHeight)) {
            constrain.checked = currentWidth === currentHeight;
        } else if (image.width === 0 || image.height === 0) {
            // If we don't have both dimensions of the image, we can't auto-size it, so disable control.
            constrain.disabled = 'disabled';
        } else {
            // This is the same as comparing to 3 decimal places.
            const widthRatio = Math.round(100 * parseInt(currentWidth, 10) / image.width);
            const heightRatio = Math.round(100 * parseInt(currentHeight, 10) / image.height);
            constrain.checked = widthRatio === heightRatio;
        }

        /**
         * Sets the selected size option based on current width and height values.
         *
         * @param {number} currentWidth - The current width value.
         * @param {number} currentHeight - The current height value.
         */
        const setSelectedSize = (currentWidth, currentHeight) => {
            if (this.rawImageDimensions.width === currentWidth &&
                this.rawImageDimensions.height === currentHeight
            ) {
                this.currentWidth = this.rawImageDimensions.width;
                this.currentHeight = this.rawImageDimensions.height;
                this.sizeChecked('original');
            } else {
                this.currentWidth = currentWidth;
                this.currentHeight = currentHeight;
                this.sizeChecked('custom');
            }
        };

        setSelectedSize(Number(currentWidth), Number(currentHeight));
    }

    /**
     * Handles the selection of image size options and updates the form inputs accordingly.
     *
     * @param {string} option - The selected image size option ("original" or "custom").
     */
    sizeChecked(option) {
        const widthInput = this.root.querySelector(Selectors.IMAGE.elements.width);
        const heightInput = this.root.querySelector(Selectors.IMAGE.elements.height);
        if (option === "original") {
            this.sizeOriginalChecked();
            widthInput.value = this.rawImageDimensions.width;
            heightInput.value = this.rawImageDimensions.height;
        } else if (option === "custom") {
            this.sizeCustomChecked();
            widthInput.value = this.currentWidth;
            heightInput.value = this.currentHeight;

            // If the current size is equal to the original size, then check the Keep proportion checkbox.
            if (this.currentWidth === this.rawImageDimensions.width && this.currentHeight === this.rawImageDimensions.height) {
                const constrainField = this.root.querySelector(Selectors.IMAGE.elements.constrain);
                constrainField.checked = true;
            }
        }
        this.autoAdjustSize();
    }

    autoAdjustSize(forceHeight = false) {
        // If we do not know the image size, do not do anything.
        if (!this.rawImageDimensions) {
            return;
        }

        const widthField = this.root.querySelector(Selectors.IMAGE.elements.width);
        const heightField = this.root.querySelector(Selectors.IMAGE.elements.height);

        const normalizeFieldData = (fieldData) => {
            fieldData.isPercentageValue = !!isPercentageValue(fieldData.field.value);
            if (fieldData.isPercentageValue) {
                fieldData.percentValue = parseInt(fieldData.field.value, 10);
                fieldData.pixelSize = this.rawImageDimensions[fieldData.type] / 100 * fieldData.percentValue;
            } else {
                fieldData.pixelSize = parseInt(fieldData.field.value, 10);
                fieldData.percentValue = fieldData.pixelSize / this.rawImageDimensions[fieldData.type] * 100;
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
                currentValue.field.value = this.rawImageDimensions[currentValue.type];
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
        const constrainField = this.root.querySelector(Selectors.IMAGE.elements.constrain);
        if (constrainField.checked) {
            const keyField = getKeyField();
            const relativeField = getRelativeField();
            // We are keeping the image in proportion.
            // Calculate the size for the relative field.
            if (keyField.isPercentageValue) {
                // In proportion, so the percentages are the same.
                relativeField.field.value = keyField.field.value;
                relativeField.percentValue = keyField.percentValue;
            } else {
                relativeField.pixelSize = Math.round(
                    keyField.pixelSize / this.rawImageDimensions[keyField.type] * this.rawImageDimensions[relativeField.type]
                );
                relativeField.field.value = relativeField.pixelSize;
            }
        }

        // Store the custom width and height to reuse.
        this.currentWidth = Number(widthField.value) !== this.rawImageDimensions.width ? widthField.value : this.currentWidth;
        this.currentHeight = Number(heightField.value) !== this.rawImageDimensions.height ? heightField.value : this.currentHeight;
    }

    /**
     * Sets the dimensions of the image preview element based on user input and constraints.
     */
    setImageDimensions = () => {
        const imagePreviewBox = this.root.querySelector(Selectors.IMAGE.elements.previewBox);
        const image = this.root.querySelector(Selectors.IMAGE.elements.preview);
        const widthField = this.root.querySelector(Selectors.IMAGE.elements.width);
        const heightField = this.root.querySelector(Selectors.IMAGE.elements.height);

        const updateImageDimensions = () => {
            // Get the latest dimensions of the preview box for responsiveness.
            const boxWidth = imagePreviewBox.clientWidth;
            const boxHeight = imagePreviewBox.clientHeight;
            // Get the new width and height for the image.
            const dimensions = this.fitSquareIntoBox(widthField.value, heightField.value, boxWidth, boxHeight);
            image.style.width = `${dimensions.width}px`;
            image.style.height = `${dimensions.height}px`;
        };
        // If the client size is zero, then get the new dimensions once the modal is shown.
        if (imagePreviewBox.clientWidth === 0) {
            // Call the shown event.
            this.currentModal.getRoot().on(ModalEvents.shown, () => {
                updateImageDimensions();
            });
        } else {
            updateImageDimensions();
        }
    };

    /**
     * Handles the selection of the "Original Size" option and updates the form elements accordingly.
     */
    sizeOriginalChecked() {
        this.root.querySelector(Selectors.IMAGE.elements.sizeOriginal).checked = true;
        this.root.querySelector(Selectors.IMAGE.elements.sizeCustom).checked = false;
        hideElements(Selectors.IMAGE.elements.properties, this.root);
    }

    /**
     * Handles the selection of the "Custom Size" option and updates the form elements accordingly.
     */
    sizeCustomChecked() {
        this.root.querySelector(Selectors.IMAGE.elements.sizeOriginal).checked = false;
        this.root.querySelector(Selectors.IMAGE.elements.sizeCustom).checked = true;
        showElements(Selectors.IMAGE.elements.properties, this.root);
    }

    /**
     * Handles changes in the image presentation checkbox and enables/disables the image alt text input accordingly.
     */
    presentationChanged() {
        const presentation = this.root.querySelector(Selectors.IMAGE.elements.presentation);
        const alt = this.root.querySelector(Selectors.IMAGE.elements.alt);
        alt.disabled = presentation.checked;

        // Counting the image description characters.
        this.handleKeyupCharacterCount();
    }

    /**
     * This function checks whether an image URL is local (within the same website's domain) or external (from an external source).
     * Depending on the result, it dynamically updates the visibility and content of HTML elements in a user interface.
     * If the image is local then we only show it's filename.
     * If the image is external then it will show full URL and it can be updated.
     */
    imageTypeChecked() {
        const regex = new RegExp(`${Config.wwwroot}`);

        // True if the URL is from external, otherwise false.
        const isExternalUrl = regex.test(this.currentUrl) === false;

        // Hide the URL input.
        hideElements(Selectors.IMAGE.elements.url, this.root);

        if (!isExternalUrl) {
            // Split the URL by '/' to get an array of segments.
            const segments = this.currentUrl.split('/');
            // Get the last segment, which should be the filename.
            const filename = segments.pop().split('?')[0];
            // Show the file name.
            this.setFilenameLabel(decodeURI(filename));
        } else {

            this.setFilenameLabel(decodeURI(this.currentUrl));
        }
    }

    /**
     * Set the string for the URL label element.
     *
     * @param {string} label - The label text to set.
     */
    setFilenameLabel(label) {
        const urlLabelEle = this.root.querySelector(Selectors.IMAGE.elements.fileNameLabel);
        if (urlLabelEle) {
            urlLabelEle.innerHTML = label;
            urlLabelEle.setAttribute("title", label);
        }
    }

    toggleAriaInvalid(selectors, predicate) {
        selectors.forEach((selector) => {
            const elements = this.root.querySelectorAll(selector);
            elements.forEach((element) => element.setAttribute('aria-invalid', predicate));
        });
    }

    hasErrorUrlField() {
        const urlError = this.currentUrl === '';
        if (urlError) {
            showElements(Selectors.IMAGE.elements.urlWarning, this.root);
        } else {
            hideElements(Selectors.IMAGE.elements.urlWarning, this.root);
        }
        this.toggleAriaInvalid([Selectors.IMAGE.elements.url], urlError);

        return urlError;
    }

    hasErrorAltField() {
        const alt = this.root.querySelector(Selectors.IMAGE.elements.alt).value;
        const presentation = this.root.querySelector(Selectors.IMAGE.elements.presentation).checked;
        const imageAltError = alt === '' && !presentation;
        if (imageAltError) {
            showElements(Selectors.IMAGE.elements.altWarning, this.root);
        } else {
            hideElements(Selectors.IMAGE.elements.urlWaaltWarningrning, this.root);
        }
        this.toggleAriaInvalid([Selectors.IMAGE.elements.alt, Selectors.IMAGE.elements.presentation], imageAltError);

        return imageAltError;
    }

    updateWarning() {
        const urlError = this.hasErrorUrlField();
        const imageAltError = this.hasErrorAltField();

        return urlError || imageAltError;
    }

    getImageContext() {
        // Check if there are any accessibility issues.
        if (this.updateWarning()) {
            return null;
        }

        const classList = [];
        const constrain = this.root.querySelector(Selectors.IMAGE.elements.constrain).checked;
        const sizeOriginal = this.root.querySelector(Selectors.IMAGE.elements.sizeOriginal).checked;
        if (constrain || sizeOriginal) {
            // If the Auto size checkbox is checked or the Original size is checked, then apply the responsive class.
            classList.push(Selectors.IMAGE.styles.responsive);
        } else {
            // Otherwise, remove it.
            classList.pop(Selectors.IMAGE.styles.responsive);
        }

        return {
            url: this.currentUrl,
            alt: this.root.querySelector(Selectors.IMAGE.elements.alt).value,
            width: this.root.querySelector(Selectors.IMAGE.elements.width).value,
            height: this.root.querySelector(Selectors.IMAGE.elements.height).value,
            presentation: this.root.querySelector(Selectors.IMAGE.elements.presentation).checked,
            customStyle: this.root.querySelector(Selectors.IMAGE.elements.customStyle).value,
            classlist: classList.join(' '),
        };
    }

    setImage() {
        const pendingPromise = new Pending('tiny_media:setImage');
        const url = this.currentUrl;
        if (url === '') {
            return;
        }

        // Check if there are any accessibility issues.
        if (this.updateWarning()) {
            pendingPromise.resolve();
            return;
        }

        // Check for invalid width or height.
        const width = this.root.querySelector(Selectors.IMAGE.elements.width).value;
        if (!isPercentageValue(width) && isNaN(parseInt(width, 10))) {
            this.root.querySelector(Selectors.IMAGE.elements.width).focus();
            pendingPromise.resolve();
            return;
        }

        const height = this.root.querySelector(Selectors.IMAGE.elements.height).value;
        if (!isPercentageValue(height) && isNaN(parseInt(height, 10))) {
            this.root.querySelector(Selectors.IMAGE.elements.height).focus();
            pendingPromise.resolve();
            return;
        }

        Templates.render('tiny_media/image', this.getImageContext())
        .then((html) => {
            this.editor.insertContent(html);
            this.currentModal.destroy();
            pendingPromise.resolve();

            return html;
        })
        .catch(error => {
            window.console.log(error);
        });
    }

    /**
     * Deletes the image after confirming with the user and loads the insert image page.
     */
    deleteImage() {
        Notification.deleteCancelPromise(
            getString('deleteimage', 'tiny_media'),
            getString('deleteimagewarning', 'tiny_media'),
        ).then(() => {
            hideElements(Selectors.IMAGE.elements.altWarning, this.root);
            // Removing the image in the preview will bring the user to the insert page.
            this.loadInsertImage();
            return;
        }).catch(error => {
            window.console.log(error);
        });
    }

    registerEventListeners() {
        const submitAction = this.root.querySelector(Selectors.IMAGE.actions.submit);
        submitAction.addEventListener('click', (e) => {
            e.preventDefault();
            this.setImage();
        });

        const deleteImageEle = this.root.querySelector(Selectors.IMAGE.actions.deleteImage);
        deleteImageEle.addEventListener('click', () => {
            this.deleteImage();
        });
        deleteImageEle.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                this.deleteImage();
            }
        });

        this.root.addEventListener('change', (e) => {
            const presentationEle = e.target.closest(Selectors.IMAGE.elements.presentation);
            if (presentationEle) {
                this.presentationChanged();
            }

            const constrainEle = e.target.closest(Selectors.IMAGE.elements.constrain);
            if (constrainEle) {
                this.autoAdjustSize();
            }

            const sizeOriginalEle = e.target.closest(Selectors.IMAGE.elements.sizeOriginal);
            if (sizeOriginalEle) {
                this.sizeChecked('original');
            }

            const sizeCustomEle = e.target.closest(Selectors.IMAGE.elements.sizeCustom);
            if (sizeCustomEle) {
                this.sizeChecked('custom');
            }
        });

        this.root.addEventListener('blur', (e) => {
            if (e.target.nodeType === Node.ELEMENT_NODE) {

                const presentationEle = e.target.closest(Selectors.IMAGE.elements.presentation);
                if (presentationEle) {
                    this.presentationChanged();
                }
            }
        }, true);

        // Character count.
        this.root.addEventListener('keyup', (e) => {
            const altEle = e.target.closest(Selectors.IMAGE.elements.alt);
            if (altEle) {
                this.handleKeyupCharacterCount();
            }
        });

        this.root.addEventListener('input', (e) => {
            const widthEle = e.target.closest(Selectors.IMAGE.elements.width);
            if (widthEle) {
                // Avoid empty value.
                widthEle.value = widthEle.value === "" ? 0 : Number(widthEle.value);
                this.autoAdjustSize();
            }

            const heightEle = e.target.closest(Selectors.IMAGE.elements.height);
            if (heightEle) {
                // Avoid empty value.
                heightEle.value = heightEle.value === "" ? 0 : Number(heightEle.value);
                this.autoAdjustSize(true);
            }
        });
    }

    handleKeyupCharacterCount() {
        const alt = this.root.querySelector(Selectors.IMAGE.elements.alt).value;
        const current = this.root.querySelector('#currentcount');
        current.innerHTML = alt.length;
    }

    /**
     * Calculates the dimensions to fit a square into a specified box while maintaining aspect ratio.
     *
     * @param {number} squareWidth - The width of the square.
     * @param {number} squareHeight - The height of the square.
     * @param {number} boxWidth - The width of the box.
     * @param {number} boxHeight - The height of the box.
     * @returns {Object} An object with the new width and height of the square to fit in the box.
     */
    fitSquareIntoBox = (squareWidth, squareHeight, boxWidth, boxHeight) => {
        if (squareWidth < boxWidth && squareHeight < boxHeight) {
          // If the square is smaller than the box, keep its dimensions.
          return {
            width: squareWidth,
            height: squareHeight,
          };
        }
        // Calculate the scaling factor based on the minimum scaling required to fit in the box.
        const widthScaleFactor = boxWidth / squareWidth;
        const heightScaleFactor = boxHeight / squareHeight;
        const minScaleFactor = Math.min(widthScaleFactor, heightScaleFactor);
        // Scale the square's dimensions based on the aspect ratio and the minimum scaling factor.
        const newWidth = squareWidth * minScaleFactor;
        const newHeight = squareHeight * minScaleFactor;
        return {
          width: newWidth,
          height: newHeight,
        };
    };
}
