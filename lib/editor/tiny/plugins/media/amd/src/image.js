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
import {get_string as getString} from 'core/str';
import * as Modal from 'core/modal_factory';
import * as ModalEvents from 'core/modal_events';
import {displayFilepicker} from 'editor_tiny/utils';

export const MediaImage = class {

    CSS = {
        FORM: 'form.tiny_image_form',
        RESPONSIVE: 'img-fluid',
        INPUTALIGNMENT: 'tiny_image_alignment',
        INPUTALT: 'tiny_image_altentry',
        INPUTHEIGHT: 'tiny_image_heightentry',
        INPUTSUBMIT: 'tiny_image_urlentrysubmit',
        INPUTURL: 'tiny_image_urlentry',
        INPUTSIZE: 'tiny_image_size',
        INPUTWIDTH: 'tiny_image_widthentry',
        IMAGEALTWARNING: 'tiny_image_altwarning',
        IMAGEURLWARNING: 'tiny_image_urlwarning',
        IMAGEBROWSER: 'openimagebrowser',
        IMAGEPRESENTATION: 'tiny_image_presentation',
        INPUTCONSTRAIN: 'tiny_image_constrain',
        INPUTCUSTOMSTYLE: 'tiny_image_customstyle',
        IMAGEPREVIEW: 'tiny_image_preview',
        IMAGEPREVIEWBOX: 'tiny_image_preview_box',
        ALIGNSETTINGS: 'tiny_image_button'
    };

    FORMNAMES = {
        URL: 'urlentry',
        ALT: 'altentry'
    };

    REGEX = {
        ISPERCENT: /\d+%/
    };

    DEFAULTS = {
        WIDTH: 160,
        HEIGHT: 160,
    };

    ALIGNMENTS = [
        // Vertical alignment.
        {
            name: 'verticalAlign',
            str: 'alignment_top',
            value: 'text-top',
            margin: '0 0.5em'
        },
        {
            name: 'verticalAlign',
            str: 'alignment_middle',
            value: 'middle',
            margin: '0 0.5em'
        },
        {
            name: 'verticalAlign',
            str: 'alignment_bottom',
            value: 'text-bottom',
            margin: '0 0.5em',
            isDefault: true
        },

        // Floats.
        {
            name: 'float',
            str: 'alignment_left',
            value: 'left',
            margin: '0 0.5em 0 0'
        },
        {
            name: 'float',
            str: 'alignment_right',
            value: 'right',
            margin: '0 0 0 0.5em'
        }
    ];

    form = null;
    rawImageDimensions = null;
    canShowFilePicker = true;
    editor = null;
    currentModal = null;
    selectedImage = null;

    constructor(editor) {
        this.editor = editor;
    }

    displayDialogue() {
        // Reset the image dimensions.
        this.rawImageDimensions = null;

        Modal.create({
            type: Modal.types.DEFAULT,
            title: getString('imageproperties', 'tiny_media'),
            body: Templates.render('tiny_media/insert_image', {
                elementid: this.editor.getElement().id,
                CSS: this.CSS,
                FORMNAMES: this.FORMNAMES,
                showfilepicker: this.canShowFilePicker
            })
        }).then(modal => {
            this.currentModal = modal;
            modal.getRoot().on(ModalEvents.bodyRendered, () => {
                this.form = document.querySelector(this.CSS.FORM);
                // Configure the view of the current image.
                this.applyImageProperties();
                this.registerEventListeners();
            });
            modal.getRoot().on(ModalEvents.hidden, () => {
                modal.destroy();
            });
            modal.show();
            return modal;
        });
    }

    filePickerCallback(params, self) {
        if (params.url !== '') {
            const input = self.form.querySelector('.' + self.CSS.INPUTURL);
            input.value = params.url;

            // Auto set the width and height.
            self.form.querySelector('.' + self.CSS.INPUTWIDTH).value = '';
            self.form.querySelector('.' + self.CSS.INPUTHEIGHT).value = '';

            // Load the preview image.
            self.loadPreviewImage(params.url);
        }
    }

    loadPreviewImage(url) {
        const image = new Image();

        image.onerror = () => {
            const preview = this.form.querySelector('.' + CSS.IMAGEPREVIEW);
            preview.style.display = 'none';
        };

        image.onload = () => {
            let input, currentWidth, currentHeight, widthRatio, heightRatio;

            // Store dimensions of the raw image, falling back to defaults for images without dimensions (e.g. SVG).
            this.rawImageDimensions = {
                width: image.width || this.DEFAULTS.WIDTH,
                height: image.height || this.DEFAULTS.HEIGHT,
            };

            input = this.form.querySelector('.' + this.CSS.INPUTWIDTH);
            currentWidth = input.value;
            if (currentWidth === '') {
                input.value = this.rawImageDimensions.width;
                currentWidth = "" + this.rawImageDimensions.width;
            }

            input = this.form.querySelector('.' + this.CSS.INPUTHEIGHT);
            currentHeight = input.value;
            if (currentHeight === '') {
                input.value = this.rawImageDimensions.height;
                currentHeight = "" + this.rawImageDimensions.height;
            }

            input = this.form.querySelector('.' + this.CSS.IMAGEPREVIEW);
            input.setAttribute('src', image.src);
            input.style.display = 'inline';

            input = this.form.querySelector('.' + this.CSS.INPUTCONSTRAIN);
            if (currentWidth.match(this.REGEX.ISPERCENT) && currentHeight.match(this.REGEX.ISPERCENT)) {
                input.checked = currentWidth === currentHeight;
            } else if (image.width === 0 || image.height === 0) {
                // If we don't have both dimensions of the image, we can't auto-size it, so disable control.
                input.disabled = 'disabled';
            } else {
                // This is the same as comparing to 3 decimal places.
                widthRatio = Math.round(1000 * parseInt(currentWidth, 10) / image.width);
                heightRatio = Math.round(1000 * parseInt(currentHeight, 10) / image.height);
                input.checked = widthRatio === heightRatio;
            }
        };

        image.src = url;
    }

    urlChanged() {
        const input = this.form.querySelector('.' + this.CSS.INPUTURL);

        if (input.value !== '') {
            // Load the preview image.
            this.loadPreviewImage(input.value);
        }
    }

    hasErrorUrlField() {
        const url = this.form.querySelector('.' + this.CSS.INPUTURL).value;
        const urlError = url === '';
        this.toggleVisibility('.' + this.CSS.IMAGEURLWARNING, urlError);
        this.toggleAriaInvalid(['.' + this.CSS.INPUTURL], urlError);

        return urlError;
    }

    hasErrorAltField() {
        const alt = this.form.querySelector('.' + this.CSS.INPUTALT).value;
        const presentation = this.form.querySelector('.' + this.CSS.IMAGEPRESENTATION).checked;
        const imageAltError = alt === '' && !presentation;
        this.toggleVisibility('.' + this.CSS.IMAGEALTWARNING, imageAltError);
        this.toggleAriaInvalid(['.' + this.CSS.INPUTALT, '.' + this.CSS.IMAGEPRESENTATION], imageAltError);

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
            elements.forEach((element) => {
                element.setAttribute('aria-invalid', predicate);
            });
        });
    }

    getAlignmentClass(alignment) {
        return this.CSS.ALIGNSETTINGS + '_' + alignment;
    }

    updateWarning() {
        const urlError = this.hasErrorUrlField();
        const imageAltError = this.hasErrorAltField();

        return urlError || imageAltError;
    }

    setImage(e) {
        const url = this.form.querySelector('.' + this.CSS.INPUTURL).value,
            alt = this.form.querySelector('.' + this.CSS.INPUTALT).value,
            width = this.form.querySelector('.' + this.CSS.INPUTWIDTH).value,
            height = this.form.querySelector('.' + this.CSS.INPUTHEIGHT).value,
            alignment = this.getAlignmentClass(this.form.querySelector('.' + this.CSS.INPUTALIGNMENT).value),
            presentation = this.form.querySelector('.' + this.CSS.IMAGEPRESENTATION).checked,
            constrain = this.form.querySelector('.' + this.CSS.INPUTCONSTRAIN).value,
            customStyle = this.form.querySelector('.' + this.CSS.INPUTCUSTOMSTYLE).value;
        let imageHtml,
            classList = [];

        e.preventDefault();

        // Check if there are any accessibility issues.
        if (this.updateWarning()) {
            return;
        }

        if (url !== '') {
            if (constrain) {
                classList.push(this.CSS.RESPONSIVE);
            }

            // Add the alignment class for the image.
            classList.push(alignment);

            if (!width.match(this.REGEX.ISPERCENT) && isNaN(parseInt(width, 10))) {
                this.form.querySelector('.' + this.CSS.INPUTWIDTH).focus();
                return;
            }
            if (!height.match(this.REGEX.ISPERCENT) && isNaN(parseInt(height, 10))) {
                this.form.querySelector('.' + this.CSS.INPUTHEIGHT).focus();
                return;
            }

            Templates.render('tiny_media/image', {
                url: url,
                alt: alt,
                width: width,
                height: height,
                presentation: presentation,
                customstyle: customStyle,
                classlist: classList.join(' ')
            }).then(html => {
                imageHtml = html;
                this.editor.insertContent(imageHtml);
                this.currentModal.destroy();
            });
        }
    }

    handleKeyupCharacterCount() {
        const alt = this.form.querySelector('.' + this.CSS.INPUTALT).value,
            current = this.form.querySelector('#currentcount');
        current.innerHTML = alt.length;
    }

    autoAdjustSize(e, forceHeight) {
        forceHeight = forceHeight || false;

        let keyField = this.form.querySelector('.' + this.CSS.INPUTWIDTH),
            keyFieldType = 'width',
            subField = this.form.querySelector('.' + this.CSS.INPUTHEIGHT),
            subFieldType = 'height',
            constrainField = this.form.querySelector('.' + this.CSS.INPUTCONSTRAIN),
            keyFieldValue = keyField.value,
            subFieldValue = subField.value,
            imagePreview = this.form.querySelector('.' + this.CSS.IMAGEPREVIEW),
            rawPercentage,
            rawSize;

        // If we do not know the image size, do not do anything.
        if (!this.rawImageDimensions) {
            return;
        }

        // Set the width back to default if it is empty.
        if (keyFieldValue === '') {
            keyFieldValue = this.rawImageDimensions[keyFieldType];
            keyField.value = keyFieldValue;
            keyFieldValue = keyField.value;
        }

        // Clear the existing preview sizes.
        imagePreview.style.width = null;
        imagePreview.style.height = null;

        // Now update with the new values.
        if (!constrainField.checked) {
            // We are not keeping the image proportion - update the preview accordingly.

            // Width.
            if (keyFieldValue.match(this.REGEX.ISPERCENT)) {
                rawPercentage = parseInt(keyFieldValue, 10);
                rawSize = this.rawImageDimensions.width / 100 * rawPercentage;
                imagePreview.style.width = rawSize + 'px';
            } else {
                imagePreview.style.width = keyFieldValue + 'px';
            }

            // Height.
            if (subFieldValue.match(this.REGEX.ISPERCENT)) {
                rawPercentage = parseInt(subFieldValue, 10);
                rawSize = this.rawImageDimensions.height / 100 * rawPercentage;
                imagePreview.style.height = rawSize + 'px';
            } else {
                imagePreview.style.height = subFieldValue + 'px';
            }
        } else {
            // We are keeping the image in proportion.
            if (forceHeight) {
                // By default we update based on width. Swap the key and sub fields around to achieve a height-based scale.
                let temporaryValue;
                temporaryValue = keyField;
                keyField = subField;
                subField = temporaryValue;

                temporaryValue = keyFieldType;
                keyFieldType = subFieldType;
                subFieldType = temporaryValue;

                temporaryValue = keyFieldValue;
                keyFieldValue = subFieldValue;
                subFieldValue = temporaryValue;
            }

            if (keyFieldValue.match(this.REGEX.ISPERCENT)) {
                // This is a percentage based change. Copy it verbatim.
                subFieldValue = keyFieldValue;

                // Set the width to the calculated pixel width.
                rawPercentage = parseInt(keyFieldValue, 10);
                rawSize = this.rawImageDimensions.width / 100 * rawPercentage;

                // And apply the width/height to the container.
                imagePreview.style.width = rawSize;
                rawSize = this.rawImageDimensions.height / 100 * rawPercentage;
                imagePreview.style.height = rawSize;
            } else {
                // Calculate the scaled subFieldValue from the keyFieldValue.
                subFieldValue = Math.round((keyFieldValue / this.rawImageDimensions[keyFieldType]) *
                    this.rawImageDimensions[subFieldType]);

                if (forceHeight) {
                    imagePreview.style.width = subFieldValue;
                    imagePreview.style.height = keyFieldValue;
                } else {
                    imagePreview.style.width = keyFieldValue;
                    imagePreview.style.height = subFieldValue;
                }
            }

            // Update the subField's value within the form to reflect the changes.
            subField.value = subFieldValue;
        }
    }

    applyImageProperties() {
        const properties = this.getSelectedImageProperties(),
            img = this.form.querySelector('.' + this.CSS.IMAGEPREVIEW);

        if (properties === false) {
            img.style.display = 'none';
            // Set the default alignment.
            this.ALIGNMENTS.some(alignment => {
                if (alignment.isDefault) {
                    this.form.querySelector('.' + this.CSS.INPUTALIGNMENT).value = alignment.value;
                    return true;
                }

                return false;
            });

            return;
        }

        if (properties.align) {
            this.form.querySelector('.' + this.CSS.INPUTALIGNMENT).value = properties.align;
        }
        if (properties.customstyle) {
            this.form.querySelector('.' + this.CSS.INPUTCUSTOMSTYLE).value = properties.customstyle;
        }
        if (properties.width) {
            this.form.querySelector('.' + this.CSS.INPUTWIDTH).value = properties.width;
        }
        if (properties.height) {
            this.form.querySelector('.' + this.CSS.INPUTHEIGHT).value = properties.height;
        }
        if (properties.alt) {
            this.form.querySelector('.' + this.CSS.INPUTALT).value = properties.alt;
        }
        if (properties.src) {
            this.form.querySelector('.' + this.CSS.INPUTURL).value = properties.src;
            this.loadPreviewImage(properties.src);
        }
        if (properties.presentation) {
            this.form.querySelector('.' + this.CSS.IMAGEPRESENTATION).checked = 'checked';
        }

        // Update the image preview based on the form properties.
        this.autoAdjustSize();
    }

    getSelectedImageProperties() {
        let properties = {
                src: null,
                alt: null,
                width: null,
                height: null,
                align: '',
                presentation: false
            },

            // Get the current selection.
            image = this.getSelectedImage(),
            width,
            height,
            style;

        if (image) {
            image = this.removeLegacyAlignment(image);
            this.selectedImage = image;

            style = image.style;
            properties.customstyle = style;

            width = image.width;
            if (!String(width).match(this.REGEX.ISPERCENT)) {
                width = parseInt(width, 10);
            }
            height = image.height;
            if (!String(height).match(this.REGEX.ISPERCENT)) {
                height = parseInt(height, 10);
            }

            if (width !== 0) {
                properties.width = width;
            }
            if (height !== 0) {
                properties.height = height;
            }
            this.getAlignmentProperties(image, properties);
            properties.src = image.getAttribute('src');
            properties.alt = image.getAttribute('alt') || '';
            properties.presentation = (image.getAttribute('role') === 'presentation');
            return properties;
        }

        // No image selected - clean up.
        this.selectedImage = null;
        return false;
    }

    removeLegacyAlignment(imageNode) {
        if (!imageNode.style.margin) {
            // There is no margin therefore this cannot match any known alignments.
            return imageNode;
        }

        this.ALIGNMENTS.some(alignment => {
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

    getAlignmentProperties(image, properties) {
        let complete = false,
            defaultAlignment;

        // Check for an alignment value.
        complete = this.ALIGNMENTS.some(alignment => {
            const classname = this.getAlignmentClass(alignment.value);
            if (image.classList.contains(classname)) {
                properties.align = alignment.value;
                return true;
            }

            if (alignment.isDefault) {
                defaultAlignment = alignment.value;
            }

            return false;
        });

        if (!complete && defaultAlignment) {
            properties.align = defaultAlignment;
        }
    }

    getSelectedImage() {
        const imgElm = this.editor.selection.getNode();
        const figureElm = this.editor.dom.getParent(imgElm, 'figure.image');
        if (figureElm) {
            return this.editor.dom.select('img', figureElm)[0];
        }
        if (imgElm && (imgElm.nodeName !== 'IMG' || this.isPlaceholderImage(imgElm))) {
            return null;
        }
        return imgElm;
    }

    isPlaceholderImage(imgElm) {
        return imgElm.nodeName === 'IMG' && (imgElm.hasAttribute('data-mce-object') || imgElm.hasAttribute('data-mce-placeholder'));
    }

    registerEventListeners() {
        const self = this;
        this.form.querySelector('.' + this.CSS.INPUTURL).addEventListener('blur', () => {
            this.urlChanged();
        });
        this.form.querySelector('.' + this.CSS.INPUTURL).addEventListener('change', () => {
            this.hasErrorUrlField();
        });
        this.form.querySelector('.' + this.CSS.IMAGEPRESENTATION).addEventListener('change', () => {
            this.hasErrorAltField();
        });
        this.form.querySelector('.' + this.CSS.INPUTALT).addEventListener('blur', () => {
            this.hasErrorAltField();
        });
        this.form.querySelector('.' + this.CSS.INPUTWIDTH).addEventListener('blur', (e) => {
            this.autoAdjustSize(e);
        });
        this.form.querySelector('.' + this.CSS.INPUTHEIGHT).addEventListener('blur', (e) => {
            this.autoAdjustSize(e, true);
        });

        this.form.querySelector('.' + this.CSS.INPUTSUBMIT).addEventListener('click', (e) => {
            this.setImage(e);
        });
        if (this.canShowFilePicker) {
            this.form.querySelector('.' + this.CSS.IMAGEBROWSER).addEventListener('click', (e) => {
                e.preventDefault();
                displayFilepicker(this.editor, 'image').then((params) => {
                    this.filePickerCallback(params, self);
                }).catch();
            });
        }
        // Character count.
        this.form.querySelector('.' + this.CSS.INPUTALT).addEventListener('keyup', () => {
            this.handleKeyupCharacterCount();
        });
    }
};

