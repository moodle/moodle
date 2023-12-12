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
 * Tiny Media plugin Image class for Moodle.
 *
 * @module      tiny_media/image
 * @copyright   2022 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import {getString, getStrings} from 'core/str';
import Pending from 'core/pending';
import {displayFilepicker} from 'editor_tiny/utils';
import Selectors from './selectors';
import ImageModal from './imagemodal';
import {getImagePermissions} from './options';
import {component} from "./common";
import {getFilePicker} from 'editor_tiny/options';

export default class MediaImage {
    DEFAULTS = {
        WIDTH: 160,
        HEIGHT: 160,
    };

    form = null;
    rawImageDimensions = null;
    canShowFilePicker = false;
    editor = null;
    currentModal = null;
    selectedImage = null;
    imageAlignment = null;

    constructor(editor) {
        const permissions = getImagePermissions(editor);

        // Indicates whether the file picker can be shown.
        this.canShowFilePicker = permissions.filepicker && (typeof getFilePicker(editor, 'image') !== 'undefined');

        this.editor = editor;
    }

    async displayDialogue() {
        // Reset the image dimensions.
        this.rawImageDimensions = null;

        const currentImageData = await this.getCurrentImageData();
        this.currentModal = await ImageModal.create({
            title: getString('imageproperties', 'tiny_media'),
            templateContext: await this.getTemplateContext(currentImageData),
        });

        if (currentImageData && currentImageData.src) {
            this.loadPreviewImage(currentImageData.src);
        }

        await this.registerEventListeners();
    }

    async getAlignmentTitles() {
        if (!this.alignmentTitles) {
            const [top, middle, bottom] = await getStrings([
                'alignment_top',
                'alignment_middle',
                'alignment_bottom',
            ].map((key) => ({key, component})));

            this.alignmentTitles = {
                top,
                middle,
                bottom,
            };
        }

        return this.alignmentTitles;
    }

    async getImageAlignment(selected = '') {
        const titles = await this.getAlignmentTitles();
        const alignments = [
            {
                text: titles.top,
                value: 'align-top',
            },
            {
                text: titles.middle,
                value: 'align-middle',
            },
            {
                text: titles.bottom,
                value: 'align-bottom',
            },
        ];

        if (selected) {
            alignments.forEach((alignment, index, array) => {
                if (alignment.value === selected) {
                    array[index].selected = true;
                }
            });
        }

        return alignments;
    }

    async getTemplateContext(data) {
        return {
            elementid: this.editor.id,
            showfilepicker: this.canShowFilePicker,
            alignoptions: await this.getImageAlignment(),
            ...data,
        };
    }

    async getCurrentImageData() {
        const selectedImageProperties = this.getSelectedImageProperties();
        if (!selectedImageProperties) {
            return {};
        }

        const properties = {...selectedImageProperties};
        if (properties.align) {
            properties.alignoptions = await this.getImageAlignment(properties.align);
        }

        if (properties.src) {
            properties.haspreview = true;
        }

        if (!properties.alt) {
            properties.presentation = true;
        }

        return properties;
    }

    filePickerCallback(params, self) {
        if (params.url) {
            const input = self.form.querySelector(Selectors.IMAGE.elements.url);
            input.value = params.url;

            // Auto set the width and height.
            self.form.querySelector(Selectors.IMAGE.elements.width).value = '';
            self.form.querySelector(Selectors.IMAGE.elements.height).value = '';

            // Load the preview image.
            self.loadPreviewImage(params.url);
        }
    }

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

        const widthInput = this.form.querySelector(Selectors.IMAGE.elements.width);
        const currentWidth = getCurrentWidth(widthInput);

        const heightInput = this.form.querySelector(Selectors.IMAGE.elements.height);
        const currentHeight = getCurrentHeight(heightInput);

        const preview = this.form.querySelector(Selectors.IMAGE.elements.preview);
        preview.setAttribute('src', image.src);
        preview.style.display = 'inline';

        const constrain = this.form.querySelector(Selectors.IMAGE.elements.constrain);
        if (this.isPercentageValue(currentWidth) && this.isPercentageValue(currentHeight)) {
            constrain.checked = currentWidth === currentHeight;
        } else if (image.width === 0 || image.height === 0) {
            // If we don't have both dimensions of the image, we can't auto-size it, so disable control.
            constrain.disabled = 'disabled';
        } else {
            // This is the same as comparing to 3 decimal places.
            const widthRatio = Math.round(1000 * parseInt(currentWidth, 10) / image.width);
            const heightRatio = Math.round(1000 * parseInt(currentHeight, 10) / image.height);
            constrain.checked = widthRatio === heightRatio;
        }
    }

    loadPreviewImage(url) {
        const image = new Image();

        image.addEventListener('error', () => {
            const preview = this.form.querySelector(Selectors.IMAGE.elements.preview);
            preview.style.display = 'none';
        });
        image.addEventListener('load', () => {
            this.storeImageDimensions(image);
            this.autoAdjustSize();
        });

        image.src = url;
    }

    urlChanged() {
        const input = this.form.querySelector(Selectors.IMAGE.elements.url);

        if (input.value) {
            // Load the preview image.
            this.loadPreviewImage(input.value);
        }
    }

    hasErrorUrlField() {
        const url = this.form.querySelector(Selectors.IMAGE.elements.url).value;
        const urlError = url === '';
        this.toggleVisibility(Selectors.IMAGE.elements.urlWarning, urlError);
        this.toggleAriaInvalid([Selectors.IMAGE.elements.url], urlError);

        return urlError;
    }

    hasErrorAltField() {
        const alt = this.form.querySelector(Selectors.IMAGE.elements.alt).value;
        const presentation = this.form.querySelector(Selectors.IMAGE.elements.presentation).checked;
        const imageAltError = alt === '' && !presentation;
        this.toggleVisibility(Selectors.IMAGE.elements.altWarning, imageAltError);
        this.toggleAriaInvalid([Selectors.IMAGE.elements.alt, Selectors.IMAGE.elements.presentation], imageAltError);

        return imageAltError;
    }

    toggleVisibility(selector, predicate) {
        const elements = this.form.querySelectorAll(selector);
        elements.forEach((element) => {
            element.style.display = predicate ? 'block' : 'none';
        });
    }

    toggleAriaInvalid(selectors, predicate) {
        selectors.forEach((selector) => {
            const elements = this.form.querySelectorAll(selector);
            elements.forEach((element) => element.setAttribute('aria-invalid', predicate));
        });
    }

    getAlignmentClass(alignment) {
        return alignment;
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

        const constrain = this.form.querySelector(Selectors.IMAGE.elements.constrain).value;
        if (constrain) {
            classList.push(Selectors.IMAGE.styles.responsive);
        }

        // Add the alignment class for the image.
        const alignment = this.getAlignmentClass(this.form.querySelector(Selectors.IMAGE.elements.alignment).value);
        classList.push(alignment);

        return {
            url: this.form.querySelector(Selectors.IMAGE.elements.url).value,
            alt: this.form.querySelector(Selectors.IMAGE.elements.alt).value,
            width: this.form.querySelector(Selectors.IMAGE.elements.width).value,
            height: this.form.querySelector(Selectors.IMAGE.elements.height).value,
            presentation: this.form.querySelector(Selectors.IMAGE.elements.presentation).checked,
            customStyle: this.form.querySelector(Selectors.IMAGE.elements.customStyle).value,
            classlist: classList.join(' '),
        };
    }

    setImage() {
        const pendingPromise = new Pending('tiny_media:setImage');
        const url = this.form.querySelector(Selectors.IMAGE.elements.url).value;
        if (url === '') {
            return;
        }

        // Check if there are any accessibility issues.
        if (this.updateWarning()) {
            pendingPromise.resolve();
            return;
        }

        // Check for invalid width or height.
        const width = this.form.querySelector(Selectors.IMAGE.elements.width).value;
        if (!this.isPercentageValue(width) && isNaN(parseInt(width, 10))) {
            this.form.querySelector(Selectors.IMAGE.elements.width).focus();
            pendingPromise.resolve();
            return;
        }

        const height = this.form.querySelector(Selectors.IMAGE.elements.height).value;
        if (!this.isPercentageValue(height) && isNaN(parseInt(height, 10))) {
            this.form.querySelector(Selectors.IMAGE.elements.height).focus();
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
        .catch();
    }

    handleKeyupCharacterCount() {
        const alt = this.form.querySelector(Selectors.IMAGE.elements.alt).value;
        const current = this.form.querySelector('#currentcount');
        current.innerHTML = alt.length;
    }

    autoAdjustSize(forceHeight = false) {
        // If we do not know the image size, do not do anything.
        if (!this.rawImageDimensions) {
            return;
        }

        const widthField = this.form.querySelector(Selectors.IMAGE.elements.width);
        const heightField = this.form.querySelector(Selectors.IMAGE.elements.height);
        const normalizeFieldData = (fieldData) => {
            fieldData.isPercentageValue = !!this.isPercentageValue(fieldData.field.value);
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


        const setImageDimensions = (image, keyField, relativeField, forceHeight = false) => {
            const getStyleValue = (field) => field.isPercentageValue ? `${field.percentValue}%` : `${field.pixelSize}px`;

            // If the values are constrained, then only update the width.
            if (forceHeight) {
                if (keyField.type === 'width') {
                    image.style.width = getStyleValue(keyField);
                } else {
                    image.style.width = getStyleValue(relativeField);
                }
            } else {
                image.style[keyField.type] = getStyleValue(keyField);
                image.style[relativeField.type] = getStyleValue(relativeField);
            }
        };

        const imagePreview = this.form.querySelector(Selectors.IMAGE.elements.preview);
        // Clear the existing preview sizes.
        imagePreview.style.width = '';
        imagePreview.style.height = '';

        // Now update with the new values.
        const constrainField = this.form.querySelector(Selectors.IMAGE.elements.constrain);
        const keyField = getKeyField();
        const relativeField = getRelativeField();
        if (constrainField.checked) {
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
        setImageDimensions(imagePreview, keyField, relativeField, !!constrainField.checked);
    }

    getSelectedImageProperties() {
        const image = this.getSelectedImage();
        if (!image) {
            this.selectedImage = null;
            return null;
        }

        const properties = {
            src: null,
            alt: null,
            width: null,
            height: null,
            align: '',
            presentation: false,
        };

        const getImageHeight = (image) => {
            if (!this.isPercentageValue(String(image.height))) {
                return parseInt(image.height, 10);
            }

            return image.height;
        };

        const getImageWidth = (image) => {
            if (!this.isPercentageValue(String(image.width))) {
                return parseInt(image.width, 10);
            }

            return image.width;
        };

        // Get the current selection.
        this.removeLegacyAlignment(image);
        this.selectedImage = image;

        properties.customStyle = image.style.cssText;

        const width = getImageWidth(image);
        if (width !== 0) {
            properties.width = width;
        }

        const height = getImageHeight(image);
        if (height !== 0) {
            properties.height = height;
        }

        const alignment = this.getAlignmentProperties(image, properties);
        if (alignment) {
            properties.align = alignment.value;
        }

        properties.src = image.getAttribute('src');
        properties.alt = image.getAttribute('alt') || '';
        properties.presentation = (image.getAttribute('role') === 'presentation');

        return properties;
    }

    removeLegacyAlignment(imageNode) {
        if (!imageNode.style.margin) {
            // There is no margin therefore this cannot match any known alignments.
            return imageNode;
        }

        Selectors.IMAGE.alignments.some(alignment => {
            if (imageNode.style[alignment.name] !== alignment.value) {
                // The name/value do not match. Skip.
                return false;
            }
            const normalisedNode = document.createElement('div');
            normalisedNode.style.margin = alignment.margin;
            if (imageNode.style.margin !== normalisedNode.style.margin) {
                // The margin does not match.
                return false;
            }

            imageNode.classList.add(this.getAlignmentClass(alignment.value));
            imageNode.style[alignment.name] = null;
            imageNode.style.margin = null;

            return true;
        });

        return imageNode;
    }

    getAlignmentProperties(image) {
        const currentAlignment = Selectors.IMAGE.alignments.find((alignment) => {
            if (image.classList.contains(this.getAlignmentClass(alignment.value))) {
                return true;
            }

            if (alignment.legacyValues) {
                return alignment.legacyValues.some((legacyValue) => image.classList.contains(legacyValue));
            }

            return false;
        });
        if (currentAlignment) {
            return currentAlignment;
        }

        return Selectors.IMAGE.alignments.find((alignment) => alignment.isDefault);
    }

    getSelectedImage() {
        const imgElm = this.editor.selection.getNode();
        const figureElm = this.editor.dom.getParent(imgElm, 'figure.image');
        if (figureElm) {
            return this.editor.dom.select('img', figureElm)[0];
        }

        if (imgElm && (imgElm.nodeName.toUpperCase() !== 'IMG' || this.isPlaceholderImage(imgElm))) {
            return null;
        }
        return imgElm;
    }

    isPlaceholderImage(imgElm) {
        if (imgElm.nodeName.toUpperCase() !== 'IMG') {
            return false;
        }

        return (imgElm.hasAttribute('data-mce-object') || imgElm.hasAttribute('data-mce-placeholder'));
    }

    isPercentageValue(value) {
        return value.match(/\d+%/);
    }

    async registerEventListeners() {
        await this.currentModal.getBody();
        const root = this.currentModal.getRoot()[0];

        this.form = root.querySelector(Selectors.IMAGE.elements.form);
        root.addEventListener('click', (e) => {
            const submitAction = e.target.closest(Selectors.IMAGE.actions.submit);
            const imageBrowserAction = e.target.closest(Selectors.IMAGE.actions.imageBrowser);
            if (submitAction) {
                e.preventDefault();
                this.setImage();
            }
            if (imageBrowserAction && this.canShowFilePicker) {
                e.preventDefault();
                displayFilepicker(this.editor, 'image').then((params) => {
                    const self = this;
                    this.filePickerCallback(params, self);

                    return;
                }).catch();
            }
        });

        root.addEventListener('change', (e) => {
            const urlEle = e.target.closest(Selectors.IMAGE.elements.url);
            if (urlEle) {
                this.hasErrorUrlField();
            }

            const presentationEle = e.target.closest(Selectors.IMAGE.elements.presentation);
            if (presentationEle) {
                this.hasErrorAltField();
            }

            const constrainEle = e.target.closest(Selectors.IMAGE.elements.constrain);
            if (constrainEle) {
                this.autoAdjustSize(true);
            }
        });

        root.addEventListener('blur', (e) => {
            if (e.target.nodeType === Node.ELEMENT_NODE) {
                const urlEle = e.target.closest(Selectors.IMAGE.elements.url);
                if (urlEle) {
                    this.urlChanged();
                }

                const altEle = e.target.closest(Selectors.IMAGE.elements.alt);
                if (altEle) {
                    this.hasErrorAltField();
                }

                const widthEle = e.target.closest(Selectors.IMAGE.elements.width);
                if (widthEle) {
                    this.autoAdjustSize();
                }

                const heightEle = e.target.closest(Selectors.IMAGE.elements.height);
                if (heightEle) {
                    this.autoAdjustSize(true);
                }
            }
        }, true);

        // Character count.
        root.addEventListener('keyup', (e) => {
            const altEle = e.target.closest(Selectors.IMAGE.elements.alt);
            if (altEle) {
                this.handleKeyupCharacterCount();
            }
        });
    }
}
