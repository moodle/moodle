YUI.add('moodle-atto_image-button', function (Y, NAME) {

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

/*
 * @package    atto_image
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_image_alignment-button
 */

/**
 * Atto image selection tool.
 *
 * @namespace M.atto_image
 * @class Button
 * @extends M.editor_atto.EditorPlugin
 */

var CSS = {
        RESPONSIVE: 'img-responsive',
        INPUTALIGNMENT: 'atto_image_alignment',
        INPUTALT: 'atto_image_altentry',
        INPUTHEIGHT: 'atto_image_heightentry',
        INPUTSUBMIT: 'atto_image_urlentrysubmit',
        INPUTURL: 'atto_image_urlentry',
        INPUTSIZE: 'atto_image_size',
        INPUTWIDTH: 'atto_image_widthentry',
        IMAGEALTWARNING: 'atto_image_altwarning',
        IMAGEBROWSER: 'openimagebrowser',
        IMAGEPRESENTATION: 'atto_image_presentation',
        INPUTCONSTRAIN: 'atto_image_constrain',
        INPUTCUSTOMSTYLE: 'atto_image_customstyle',
        IMAGEPREVIEW: 'atto_image_preview',
        IMAGEPREVIEWBOX: 'atto_image_preview_box'
    },
    SELECTORS = {
        INPUTURL: '.' + CSS.INPUTURL
    },
    ALIGNMENTS = [
        // Vertical alignment.
        {
            name: 'text-top',
            str: 'alignment_top',
            value: 'vertical-align',
            margin: '0 .5em'
        }, {
            name: 'middle',
            str: 'alignment_middle',
            value: 'vertical-align',
            margin: '0 .5em'
        }, {
            name: 'text-bottom',
            str: 'alignment_bottom',
            value: 'vertical-align',
            margin: '0 .5em',
            isDefault: true
        },

        // Floats.
        {
            name: 'left',
            str: 'alignment_left',
            value: 'float',
            margin: '0 .5em 0 0'
        }, {
            name: 'right',
            str: 'alignment_right',
            value: 'float',
            margin: '0 0 0 .5em'
        }, {
            name: 'customstyle',
            str: 'customstyle',
            value: 'style'
        }
    ],

    REGEX = {
        ISPERCENT: /\d+%/
    },

    COMPONENTNAME = 'atto_image',

    TEMPLATE = '' +
            '<form class="atto_form">' +
                '<label for="{{elementid}}_{{CSS.INPUTURL}}">{{get_string "enterurl" component}}</label>' +
                '<input class="fullwidth {{CSS.INPUTURL}}" type="url" id="{{elementid}}_{{CSS.INPUTURL}}" size="32"/>' +
                '<br/>' +

                // Add the repository browser button.
                '{{#if showFilepicker}}' +
                    '<button class="{{CSS.IMAGEBROWSER}}" type="button">{{get_string "browserepositories" component}}</button>' +
                '{{/if}}' +

                // Add the Alt box.
                '<div style="display:none" role="alert" class="warning {{CSS.IMAGEALTWARNING}}">' +
                    '{{get_string "presentationoraltrequired" component}}' +
                '</div>' +
                '<label for="{{elementid}}_{{CSS.INPUTALT}}">{{get_string "enteralt" component}}</label>' +
                '<input class="fullwidth {{CSS.INPUTALT}}" type="text" value="" id="{{elementid}}_{{CSS.INPUTALT}}" size="32"/>' +
                '<br/>' +

                // Add the presentation select box.
                '<input type="checkbox" class="{{CSS.IMAGEPRESENTATION}}" id="{{elementid}}_{{CSS.IMAGEPRESENTATION}}"/>' +
                '<label class="sameline" for="{{elementid}}_{{CSS.IMAGEPRESENTATION}}">{{get_string "presentation" component}}</label>' +
                '<br/>' +

                // Add the size entry boxes.
                '<label class="sameline" for="{{elementid}}_{{CSS.INPUTSIZE}}">{{get_string "size" component}}</label>' +
                '<div id="{{elementid}}_{{CSS.INPUTSIZE}}" class="{{CSS.INPUTSIZE}}">' +
                '<label class="accesshide" for="{{elementid}}_{{CSS.INPUTWIDTH}}">{{get_string "width" component}}</label>' +
                '<input type="text" class="{{CSS.INPUTWIDTH}} input-mini" id="{{elementid}}_{{CSS.INPUTWIDTH}}" size="4"/> x ' +

                // Add the height entry box.
                '<label class="accesshide" for="{{elementid}}_{{CSS.INPUTHEIGHT}}">{{get_string "height" component}}</label>' +
                '<input type="text" class="{{CSS.INPUTHEIGHT}} input-mini" id="{{elementid}}_{{CSS.INPUTHEIGHT}}" size="4"/>' +

                // Add the constrain checkbox.
                '<input type="checkbox" class="{{CSS.INPUTCONSTRAIN}} sameline" id="{{elementid}}_{{CSS.INPUTCONSTRAIN}}"/>' +
                '<label for="{{elementid}}_{{CSS.INPUTCONSTRAIN}}">{{get_string "constrain" component}}</label>' +
                '</div>' +

                // Add the alignment selector.
                '<label class="sameline" for="{{elementid}}_{{CSS.INPUTALIGNMENT}}">{{get_string "alignment" component}}</label>' +
                '<select class="{{CSS.INPUTALIGNMENT}}" id="{{elementid}}_{{CSS.INPUTALIGNMENT}}">' +
                    '{{#each alignments}}' +
                        '<option value="{{value}}:{{name}};">{{get_string str ../component}}</option>' +
                    '{{/each}}' +
                '</select>' +
                // Hidden input to store custom styles.
                '<input type="hidden" class="{{CSS.INPUTCUSTOMSTYLE}}"/>' +
                '<br/>' +

                // Add the image preview.
                '<div class="mdl-align">' +
                '<div class="{{CSS.IMAGEPREVIEWBOX}}">' +
                    '<img src="#" class="{{CSS.IMAGEPREVIEW}}" alt="" style="display: none;"/>' +
                '</div>' +

                // Add the submit button and close the form.
                '<button class="{{CSS.INPUTSUBMIT}}" type="submit">{{get_string "saveimage" component}}</button>' +
                '</div>' +
            '</form>',

        IMAGETEMPLATE = '' +
            '<img src="{{url}}" alt="{{alt}}" ' +
                '{{#if width}}width="{{width}}" {{/if}}' +
                '{{#if height}}height="{{height}}" {{/if}}' +
                '{{#if presentation}}role="presentation" {{/if}}' +
                'style="{{alignment}}{{margin}}{{customstyle}}"' +
                '{{#if classlist}}class="{{classlist}}" {{/if}}' +
                '/>';

Y.namespace('M.atto_image').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    /**
     * A reference to the current selection at the time that the dialogue
     * was opened.
     *
     * @property _currentSelection
     * @type Range
     * @private
     */
    _currentSelection: null,

    /**
     * The most recently selected image.
     *
     * @param _selectedImage
     * @type Node
     * @private
     */
    _selectedImage: null,

    /**
     * A reference to the currently open form.
     *
     * @param _form
     * @type Node
     * @private
     */
    _form: null,

    /**
     * The dimensions of the raw image before we manipulate it.
     *
     * @param _rawImageDimensions
     * @type Object
     * @private
     */
    _rawImageDimensions: null,

    initializer: function() {
        this.addButton({
            icon: 'e/insert_edit_image',
            callback: this._displayDialogue,
            tags: 'img',
            tagMatchRequiresAll: false
        });
        this.editor.delegate('dblclick', this._handleDoubleClick, 'img', this);
    },

    /**
     * Handle a double click on an image.
     *
     * @method _handleDoubleClick
     * @param {EventFacade} e
     * @private
     */
    _handleDoubleClick: function(e) {
        var image = e.target;

        var selection = this.get('host').getSelectionFromNode(image);
        this.get('host').setSelection(selection);
        this._displayDialogue();
    },

    /**
     * Display the image editing tool.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {
        // Store the current selection.
        this._currentSelection = this.get('host').getSelection();
        if (this._currentSelection === false) {
            return;
        }

        // Reset the image dimensions.
        this._rawImageDimensions = null;

        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('imageproperties', COMPONENTNAME),
            width: '480px',
            focusAfterHide: true,
            focusOnShowSelector: SELECTORS.INPUTURL
        });

        // Set the dialogue content, and then show the dialogue.
        dialogue.set('bodyContent', this._getDialogueContent())
                .show();
    },

    /**
     * Set the inputs for width and height if they are not set, and calculate
     * if the constrain checkbox should be checked or not.
     *
     * @method _loadPreviewImage
     * @param {String} url
     * @private
     */
    _loadPreviewImage: function(url) {
        var image = new Image(), self = this;

        image.onerror = function() {
            var preview = self._form.one('.' + CSS.IMAGEPREVIEW);
            preview.setStyles({
                'display': 'none'
            });

            // Centre the dialogue when clearing the image preview.
            self.getDialogue().centerDialogue();
        };

        image.onload = function() {
            var input, currentwidth, currentheight, widthRatio, heightRatio;

            self._rawImageDimensions = {
                width: this.width,
                height: this.height
            };

            input = self._form.one('.' + CSS.INPUTWIDTH);
            currentwidth = input.get('value');
            if (currentwidth === '') {
                input.set('value', this.width);
                currentwidth = "" + this.width;
            }
            input = self._form.one('.' + CSS.INPUTHEIGHT);
            currentheight = input.get('value');
            if (currentheight === '') {
                input.set('value', this.height);
                currentheight = "" + this.height;
            }
            input = self._form.one('.' + CSS.IMAGEPREVIEW);
            input.setAttribute('src', this.src);
            input.setStyles({
                'display': 'inline'
            });

            input = self._form.one('.' + CSS.INPUTCONSTRAIN);
            if (currentwidth.match(REGEX.ISPERCENT) && currentheight.match(REGEX.ISPERCENT)) {
                input.set('checked', currentwidth === currentheight);
            } else {
                if (this.width === 0) {
                    this.width = 1;
                }
                if (this.height === 0) {
                    this.height = 1;
                }
                // This is the same as comparing to 3 decimal places.
                widthRatio = Math.round(1000*parseInt(currentwidth, 10) / this.width);
                heightRatio = Math.round(1000*parseInt(currentheight, 10) / this.height);
                input.set('checked', widthRatio === heightRatio);
            }

            // Apply the image sizing.
            self._autoAdjustSize(self);

            // Centre the dialogue once the preview image has loaded.
            self.getDialogue().centerDialogue();
        };

        image.src = url;
    },

    /**
     * Return the dialogue content for the tool, attaching any required
     * events.
     *
     * @method _getDialogueContent
     * @return {Node} The content to place in the dialogue.
     * @private
     */
    _getDialogueContent: function() {
        var template = Y.Handlebars.compile(TEMPLATE),
            canShowFilepicker = this.get('host').canShowFilepicker('image'),
            content = Y.Node.create(template({
                elementid: this.get('host').get('elementid'),
                CSS: CSS,
                component: COMPONENTNAME,
                showFilepicker: canShowFilepicker,
                alignments: ALIGNMENTS
            }));

        this._form = content;

        // Configure the view of the current image.
        this._applyImageProperties(this._form);

        this._form.one('.' + CSS.INPUTURL).on('blur', this._urlChanged, this);
        this._form.one('.' + CSS.IMAGEPRESENTATION).on('change', this._updateWarning, this);
        this._form.one('.' + CSS.INPUTALT).on('change', this._updateWarning, this);
        this._form.one('.' + CSS.INPUTWIDTH).on('blur', this._autoAdjustSize, this);
        this._form.one('.' + CSS.INPUTHEIGHT).on('blur', this._autoAdjustSize, this, true);
        this._form.one('.' + CSS.INPUTCONSTRAIN).on('change', function(event) {
            if (event.target.get('checked')) {
                this._autoAdjustSize(event);
            }
        }, this);
        this._form.one('.' + CSS.INPUTURL).on('blur', this._urlChanged, this);
        this._form.one('.' + CSS.INPUTSUBMIT).on('click', this._setImage, this);

        if (canShowFilepicker) {
            this._form.one('.' + CSS.IMAGEBROWSER).on('click', function() {
                    this.get('host').showFilepicker('image', this._filepickerCallback, this);
            }, this);
        }

        return content;
    },

    _autoAdjustSize: function(e, forceHeight) {
        forceHeight = forceHeight || false;

        var keyField = this._form.one('.' + CSS.INPUTWIDTH),
            keyFieldType = 'width',
            subField = this._form.one('.' + CSS.INPUTHEIGHT),
            subFieldType = 'height',
            constrainField = this._form.one('.' + CSS.INPUTCONSTRAIN),
            keyFieldValue = keyField.get('value'),
            subFieldValue = subField.get('value'),
            imagePreview = this._form.one('.' + CSS.IMAGEPREVIEW),
            rawPercentage,
            rawSize;

        // If we do not know the image size, do not do anything.
        if (!this._rawImageDimensions) {
            return;
        }

        // Set the width back to default if it is empty.
        if (keyFieldValue === '') {
            keyFieldValue = this._rawImageDimensions[keyFieldType];
            keyField.set('value', keyFieldValue);
            keyFieldValue = keyField.get('value');
        }

        // Clear the existing preview sizes.
        imagePreview.setStyles({
            width: null,
            height: null
        });

        // Now update with the new values.
        if (!constrainField.get('checked')) {
            // We are not keeping the image proportion - update the preview accordingly.

            // Width.
            if (keyFieldValue.match(REGEX.ISPERCENT)) {
                rawPercentage = parseInt(keyFieldValue, 10);
                rawSize = this._rawImageDimensions.width / 100 * rawPercentage;
                imagePreview.setStyle('width', rawSize + 'px');
            } else {
                imagePreview.setStyle('width', keyFieldValue + 'px');
            }

            // Height.
            if (subFieldValue.match(REGEX.ISPERCENT)) {
                rawPercentage = parseInt(subFieldValue, 10);
                rawSize = this._rawImageDimensions.height / 100 * rawPercentage;
                imagePreview.setStyle('height', rawSize + 'px');
            } else {
                imagePreview.setStyle('height', subFieldValue + 'px');
            }
        } else {
            // We are keeping the image in proportion.
            if (forceHeight) {
                // By default we update based on width. Swap the key and sub fields around to achieve a height-based scale.
                var _temporaryValue;
                _temporaryValue = keyField;
                keyField = subField;
                subField = _temporaryValue;

                _temporaryValue = keyFieldType;
                keyFieldType = subFieldType;
                subFieldType = _temporaryValue;

                _temporaryValue = keyFieldValue;
                keyFieldValue = subFieldValue;
                subFieldValue = _temporaryValue;
            }

            if (keyFieldValue.match(REGEX.ISPERCENT)) {
                // This is a percentage based change. Copy it verbatim.
                subFieldValue = keyFieldValue;

                // Set the width to the calculated pixel width.
                rawPercentage = parseInt(keyFieldValue, 10);
                rawSize = this._rawImageDimensions.width / 100 * rawPercentage;

                // And apply the width/height to the container.
                imagePreview.setStyle('width', rawSize);
                rawSize = this._rawImageDimensions.height / 100 * rawPercentage;
                imagePreview.setStyle('height', rawSize);
            } else {
                // Calculate the scaled subFieldValue from the keyFieldValue.
                subFieldValue = Math.round((keyFieldValue / this._rawImageDimensions[keyFieldType]) *
                        this._rawImageDimensions[subFieldType]);

                if (forceHeight) {
                    imagePreview.setStyles({
                        'width': subFieldValue,
                        'height': keyFieldValue
                    });
                } else {
                    imagePreview.setStyles({
                        'width': keyFieldValue,
                        'height': subFieldValue
                    });
                }
            }

            // Update the subField's value within the form to reflect the changes.
            subField.set('value', subFieldValue);
        }
    },

    /**
     * Update the dialogue after an image was selected in the File Picker.
     *
     * @method _filepickerCallback
     * @param {object} params The parameters provided by the filepicker
     * containing information about the image.
     * @private
     */
    _filepickerCallback: function(params) {
        if (params.url !== '') {
            var input = this._form.one('.' + CSS.INPUTURL);
            input.set('value', params.url);

            // Auto set the width and height.
            this._form.one('.' + CSS.INPUTWIDTH).set('value', '');
            this._form.one('.' + CSS.INPUTHEIGHT).set('value', '');

            // Load the preview image.
            this._loadPreviewImage(params.url);
        }
    },

    /**
     * Applies properties of an existing image to the image dialogue for editing.
     *
     * @method _applyImageProperties
     * @param {Node} form
     * @private
     */
    _applyImageProperties: function(form) {
        var properties = this._getSelectedImageProperties(),
            img = form.one('.' + CSS.IMAGEPREVIEW),
            i;

        if (properties === false) {
            img.setStyle('display', 'none');
            // Set the default alignment.
            for (i in ALIGNMENTS) {
                if (ALIGNMENTS[i].isDefault === true) {
                    css = ALIGNMENTS[i].value + ':' + ALIGNMENTS[i].name + ';';
                    form.one('.' + CSS.INPUTALIGNMENT).set('value', css);
                }
            }
            // Remove the custom style option if this is a new image.
            form.one('.' + CSS.INPUTALIGNMENT).getDOMNode().options.remove(ALIGNMENTS.length - 1);
            return;
        }

        if (properties.align) {
            form.one('.' + CSS.INPUTALIGNMENT).set('value', properties.align);
            // Remove the custom style option if we have a standard alignment.
            form.one('.' + CSS.INPUTALIGNMENT).getDOMNode().options.remove(ALIGNMENTS.length - 1);
        } else {
            form.one('.' + CSS.INPUTALIGNMENT).set('value', 'style:customstyle;');
        }
        if (properties.customstyle) {
            form.one('.' + CSS.INPUTCUSTOMSTYLE).set('value', properties.customstyle);
        }
        if (properties.width) {
            form.one('.' + CSS.INPUTWIDTH).set('value', properties.width);
        }
        if (properties.height) {
            form.one('.' + CSS.INPUTHEIGHT).set('value', properties.height);
        }
        if (properties.alt) {
            form.one('.' + CSS.INPUTALT).set('value', properties.alt);
        }
        if (properties.src) {
            form.one('.' + CSS.INPUTURL).set('value', properties.src);
            this._loadPreviewImage(properties.src);
        }
        if (properties.presentation) {
            form.one('.' + CSS.IMAGEPRESENTATION).set('checked', 'checked');
        }

        // Update the image preview based on the form properties.
        this._autoAdjustSize();
    },

    /**
     * Gets the properties of the currently selected image.
     *
     * The first image only if multiple images are selected.
     *
     * @method _getSelectedImageProperties
     * @return {object}
     * @private
     */
    _getSelectedImageProperties: function() {
        var properties = {
                src: null,
                alt :null,
                width: null,
                height: null,
                align: '',
                presentation: false
            },

            // Get the current selection.
            images = this.get('host').getSelectedNodes(),
            i, width, height, style, css;

        if (images) {
            images = images.filter('img');
        }

        if (images && images.size()) {
            image = images.item(0);
            this._selectedImage = image;

            style = image.getAttribute('style');
            properties.customstyle = style;
            style = style.replace(/ /g, '');

            width = image.getAttribute('width');
            if (!width.match(REGEX.ISPERCENT)) {
                width = parseInt(width, 10);
            }
            height = image.getAttribute('height');
            if (!height.match(REGEX.ISPERCENT)) {
                height = parseInt(height, 10);
            }

            if (width !== 0) {
                properties.width = width;
            }
            if (height !== 0) {
                properties.height = height;
            }
            for (i in ALIGNMENTS) {
                css = ALIGNMENTS[i].value + ':' + ALIGNMENTS[i].name + ';';
                if (style.indexOf(css) !== -1) {
                    margin = 'margin:' + ALIGNMENTS[i].margin + ';';
                    margin = margin.replace(/ /g, '');
                    // Must match alignment and margins - otherwise custom style is selected.
                    if (style.indexOf(margin) !== -1) {
                        properties.align = css;
                        break;
                    }
                }
            }
            properties.src = image.getAttribute('src');
            properties.alt = image.getAttribute('alt') || '';
            properties.presentation = (image.get('role') === 'presentation');
            return properties;
        }

        // No image selected - clean up.
        this._selectedImage = null;
        return false;
    },

    /**
     * Update the form when the URL was changed. This includes updating the
     * height, width, and image preview.
     *
     * @method _urlChanged
     * @private
     */
    _urlChanged: function() {
        var input = this._form.one('.' + CSS.INPUTURL);

        if (input.get('value') !== '') {
            // Load the preview image.
            this._loadPreviewImage(input.get('value'));
        }
    },

    /**
     * Update the image in the contenteditable.
     *
     * @method _setImage
     * @param {EventFacade} e
     * @private
     */
    _setImage: function(e) {
        var form = this._form,
            url = form.one('.' + CSS.INPUTURL).get('value'),
            alt = form.one('.' + CSS.INPUTALT).get('value'),
            width = form.one('.' + CSS.INPUTWIDTH).get('value'),
            height = form.one('.' + CSS.INPUTHEIGHT).get('value'),
            alignment = form.one('.' + CSS.INPUTALIGNMENT).get('value'),
            margin = '',
            presentation = form.one('.' + CSS.IMAGEPRESENTATION).get('checked'),
            constrain = form.one('.' + CSS.INPUTCONSTRAIN).get('checked'),
            imagehtml,
            customstyle = '',
            i,
            classlist = [],
            host = this.get('host');

        e.preventDefault();

        // Check if there are any accessibility issues.
        if (this._updateWarning()) {
            return;
        }

        // Focus on the editor in preparation for inserting the image.
        host.focus();
        if (url !== '') {
            if (this._selectedImage) {
                host.setSelection(host.getSelectionFromNode(this._selectedImage));
            } else {
                host.setSelection(this._currentSelection);
            }

            if (alignment === 'style:customstyle;') {
                alignment = '';
                customstyle = form.one('.' + CSS.INPUTCUSTOMSTYLE).get('value');
            } else {
                for (i in ALIGNMENTS) {
                    css = ALIGNMENTS[i].value + ':' + ALIGNMENTS[i].name + ';';
                    if (alignment === css) {
                        margin = ' margin: ' + ALIGNMENTS[i].margin + ';';
                    }
                }
            }

            if (constrain) {
                classlist.push(CSS.RESPONSIVE);
            }

            if (!width.match(REGEX.ISPERCENT) && isNaN(parseInt(width, 10))) {
                form.one('.' + CSS.INPUTWIDTH).focus();
                return;
            }
            if (!height.match(REGEX.ISPERCENT) && isNaN(parseInt(height, 10))) {
                form.one('.' + CSS.INPUTHEIGHT).focus();
                return;
            }

            template = Y.Handlebars.compile(IMAGETEMPLATE);
            imagehtml = template({
                url: url,
                alt: alt,
                width: width,
                height: height,
                presentation: presentation,
                alignment: alignment,
                margin: margin,
                customstyle: customstyle,
                classlist: classlist.join(' ')
            });

            this.get('host').insertContentAtFocusPoint(imagehtml);

            this.markUpdated();
        }

        this.getDialogue({
            focusAfterHide: null
        }).hide();

    },

    /**
     * Update the alt text warning live.
     *
     * @method _updateWarning
     * @return {boolean} whether a warning should be displayed.
     * @private
     */
    _updateWarning: function() {
        var form = this._form,
            state = true,
            alt = form.one('.' + CSS.INPUTALT).get('value'),
            presentation = form.one('.' + CSS.IMAGEPRESENTATION).get('checked');
        if (alt === '' && !presentation) {
            form.one('.' + CSS.IMAGEALTWARNING).setStyle('display', 'block');
            form.one('.' + CSS.INPUTALT).setAttribute('aria-invalid', true);
            form.one('.' + CSS.IMAGEPRESENTATION).setAttribute('aria-invalid', true);
            state = true;
        } else {
            form.one('.' + CSS.IMAGEALTWARNING).setStyle('display', 'none');
            form.one('.' + CSS.INPUTALT).setAttribute('aria-invalid', false);
            form.one('.' + CSS.IMAGEPRESENTATION).setAttribute('aria-invalid', false);
            state = false;
        }
        this.getDialogue().centerDialogue();
        return state;
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
