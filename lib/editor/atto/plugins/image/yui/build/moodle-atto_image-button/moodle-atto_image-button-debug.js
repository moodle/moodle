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

var SELECTORS = {
        TAGS: 'img'
    },
    CSS = {
        INPUTALIGNMENT: 'atto_image_alignment',
        INPUTALT: 'atto_image_altentry',
        INPUTHEIGHT: 'atto_image_heightentry',
        INPUTSUMBIT: 'atto_image_urlentrysubmit',
        INPUTURL: 'atto_image_urlentry',
        INPUTWIDTH: 'atto_image_widthentry',
        IMAGEALTWARNING: 'atto_image_altwarning',
        IMAGEBROWSER: 'openimagebrowser',
        IMAGEPRESENTATION: 'atto_image_presentation',
        IMAGEPREVIEW: 'atto_image_preview'
    },
    ALIGNMENTS,
    ALIGNMENT;

/**
 * Alignment class to aid with image alignment.
 *
 * @class ALIGNMENT
 * @constructor
 * @param {String} value
 * @param {String} style
 */
ALIGNMENT = function(value, style) {
    this.value = value;
    this.style = style;
    this.regex = new RegExp(this._regex_escape(style) + ' *: *' + this._regex_escape(value));
};
ALIGNMENT.prototype = {

    /**
     * The value of this alignment instance.
     * @property value
     * @type {String}
     */
    value: null,

    /**
     * The style this alignment instance will use.
     * @property style
     * @type {String}
     */
    style: null,

    /**
     * A regex to match this alignment instance in use.
     * @property regex
     * @type {RegExp}
     */
    regex: null,

    /**
     * Tests a given style string to check if this instance is used within it.
     * @method test
     * @param {String} str
     * @returns {Boolean}
     */
    test: function(str) {
        return this.regex.test(str);
    },

    /**
     * Escapes a string for use in a RegExp definition.
     * @method _regex_escape
     * @private
     * @param str
     * @returns {String}
     */
    _regex_escape: function(str) {
        return str.replace(/([.*+?\^=!:${}()|\[\]\/\\])/g, "\\$1");
    },

    /**
     * Applys this style to a given node.
     * @method apply
     * @param {Node} node
     */
    apply: function(node) {
        var style = node.getAttribute('style');
        if (style !== '' && style.substr(style.length - 1, 1) !== ';') {
            style += ';';
        }
        style += this.style + ': ' + this.value + ';';
    },

    /**
     * Returns this alignment instance as a select option.
     * @method to_select_option
     * @returns {string}
     */
    to_select_option: function() {
        var str = M.util.get_string('alignment_'+this.value.replace('-', ''), 'atto_image');
            value = this.style + ': '+ this.value;
        return '<option value="' + value + '">' + str + '</option>';
    }
};

/**
 * An array containing all of the valid alignments an image can have.
 * @type {ALIGNMENT[]}
 */
ALIGNMENTS = [
    // Vertical alignment.
    new ALIGNMENT('baseline', 'vertical-align'),
    new ALIGNMENT('sub', 'vertical-align'),
    new ALIGNMENT('super', 'vertical-align'),
    new ALIGNMENT('top', 'vertical-align'),
    new ALIGNMENT('text-top', 'vertical-align'),
    new ALIGNMENT('middle', 'vertical-align'),
    new ALIGNMENT('bottom', 'vertical-align'),
    new ALIGNMENT('text-bottom', 'vertical-align'),
    // Floats.
    new ALIGNMENT('left', 'float'),
    new ALIGNMENT('right', 'float')
];

/**
 * Atto text editor image plugin.
 *
 * @package    editor-atto
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.atto_image = M.atto_image || {
    dialogue: null,
    selection: null,
    currentlyselected : {},
    lastselectedimage : null,
    init: function(params) {
        var display_chooser = function(e, elementid) {
            e.preventDefault();
            if (!M.editor_atto.is_active(elementid)) {
                M.editor_atto.focus(elementid);
            }
            M.atto_image.selection = M.editor_atto.get_selection();
            if (M.atto_image.selection !== false) {
                var dialogue;
                if (!M.atto_image.dialogue) {
                    dialogue = new M.core.dialogue({
                        visible: false,
                        modal: true,
                        close: true,
                        draggable: true
                    });
                } else {
                    dialogue = M.atto_image.dialogue;
                }

                dialogue.set('bodyContent', M.atto_image.get_form_content(elementid));
                dialogue.set('headerContent', M.util.get_string('createimage', 'atto_image'));
                dialogue.render();
                dialogue.centerDialogue();
                M.atto_image.dialogue = dialogue;
                dialogue.show();
            }
        };

        var iconurl = M.util.image_url('e/insert_edit_image', 'core');
        M.editor_atto.add_toolbar_button(params.elementid, 'image', iconurl, params.group, display_chooser);
        M.editor_atto.currentlyselected = M.editor_atto.currentlyselected || {};
        M.editor_atto.currentlyselected[params.elementid] = null;

        // Attach an event listner to watch for "changes" in the contenteditable.
        // This includes cursor changes, we check if the button should be active or not, based
        // on the text selection.
        M.editor_atto.on('atto:selectionchanged', function(e) {
            if (M.editor_atto.selection_filter_matches(e.elementid, SELECTORS.TAGS, e.selectedNodes, false)) {
                M.editor_atto.add_widget_highlight(e.elementid, 'image');
                M.editor_atto.currentlyselected[e.elementid] = e.selectedNodes;
            } else {
                M.editor_atto.remove_widget_highlight(e.elementid, 'image');
                M.editor_atto.currentlyselected[e.elementid] = null;
            }
        });
    },
    open_filepicker: function(e) {
        var elementid = this.getAttribute('data-editor');
        e.preventDefault();

        M.editor_atto.show_filepicker(elementid, 'image', M.atto_image.filepicker_callback);
    },
    filepicker_callback: function(params) {
        if (params.url !== '') {
            var input = Y.one('#' + CSS.INPUTURL);
            input.set('value', params.url);

            // Auto set the width and height.
            var image = new Image();
            image.onload = function() {
                Y.one('#' + CSS.INPUTWIDTH).set('value', this.width);
                Y.one('#' + CSS.INPUTHEIGHT).set('value', this.height);
                Y.one('#' + CSS.IMAGEPREVIEW).set('src', this.src);
                Y.one('#' + CSS.IMAGEPREVIEW).setStyle('display', 'inline');
            };
            image.src = params.url;
        }
    },
    url_changed: function() {
        var input = Y.one('#' + CSS.INPUTURL);

        if (input.get('value') !== '') {
            // Auto set the width and height.
            var image = new Image();
            image.onload = function() {
                var input;

                input = Y.one('#' + CSS.INPUTWIDTH);
                if (input.get('value') === '') {
                    input.set('value', this.width);
                }
                input = Y.one('#' + CSS.INPUTHEIGHT);
                if (input.get('value') === '') {
                    input.set('value', this.height);
                }
                input = Y.one('#' + CSS.IMAGEPREVIEW);
                input.set('src', this.src);
                input.setStyle('display', 'inline');
            };
            image.src = input.get('value');
        }
    },
    set_image: function(e, elementid) {
        var form = e.currentTarget.ancestor('.atto_form'),
            url = form.one('#' + CSS.INPUTURL).get('value'),
            alt = form.one('#' + CSS.INPUTALT).get('value'),
            width = form.one('#' + CSS.INPUTWIDTH).get('value'),
            height = form.one('#' + CSS.INPUTHEIGHT).get('value'),
            alignment = form.one('#' + CSS.INPUTALIGNMENT).get('value'),
            presentation = form.one('#' + CSS.IMAGEPRESENTATION).get('checked'),
            imagehtml;

        e.preventDefault();

        if (alt === '' && !presentation) {
            form.one('#' + CSS.IMAGEALTWARNING).setStyle('display', 'block');
            form.one('#' + CSS.INPUTALT).setAttribute('aria-invalid', true);
            form.one('#' + CSS.IMAGEPRESENTATION).setAttribute('aria-invalid', true);
            return;
        } else {
            form.one('#' + CSS.IMAGEALTWARNING).setStyle('display', 'none');
            form.one('#' + CSS.INPUTALT).setAttribute('aria-invalid', false);
            form.one('#' + CSS.IMAGEPRESENTATION).setAttribute('aria-invalid', false);
        }

        M.atto_image.dialogue.hide();

        M.editor_atto.focus(elementid);
        if (url !== '') {
            if (this.lastselectedimage) {
                M.editor_atto.set_selection(M.editor_atto.get_selection_from_node(this.lastselectedimage));
            } else {
                M.editor_atto.set_selection(M.atto_image.selection);
            }
            imagehtml = '<img src="' + Y.Escape.html(url) + '" alt="' + Y.Escape.html(alt) + '"';

            if (width) {
                imagehtml += ' width="' + Y.Escape.html(width) + '"';
            }
            if (height) {
                imagehtml += ' height="' + Y.Escape.html(height) + '"';
            }
            if (presentation) {
                imagehtml += ' role="presentation"';
            }
            if (alignment) {
                imagehtml += ' style="' + alignment + '"';
            }
            imagehtml += '/>';

            M.editor_atto.insert_html_at_focus_point(imagehtml);

            // Clean the YUI ids from the HTML.
            M.editor_atto.text_updated(elementid);
        }
    },
    /**
     * Gets the properties of the currently selected image.
     *
     * The first image only if multiple images are selected.
     *
     * @method _get_selected_image_properties
     * @private
     * @param {string} elementid
     * @returns {object}
     */
    _get_selected_image_properties: function(elementid) {
        var properties = {
                src: null,
                alt :null,
                width: null,
                height: null,
                align: null,
                display: 'inline',
                presentation: false
            },
            images = M.editor_atto.currentlyselected[elementid],
            i, image, width, height, style;

        if (images) {
            images = images.filter('img');
        }

        if (images && images.size()) {
            image = images.item(0);
            this.lastselectedimage = image;

            style = image.getAttribute('style');
            width = parseInt(image.getAttribute('width'), 10);
            height = parseInt(image.getAttribute('height'), 10);

            if (width > 0) {
                properties.width = width;
            }
            if (height > 0) {
                properties.height = height;
            }
            for (i in ALIGNMENTS) {
                if (ALIGNMENTS[i].test(style)) {
                    properties.align = ALIGNMENTS[i];
                    break;
                }
            }
            properties.src = image.getAttribute('src');
            properties.alt = image.getAttribute('alt') || '';
            properties.presentation = (image.get('role') === 'presentation');
            return properties;
        }
        return false;
    },
    get_form_content: function(elementid) {

        // String collection for quick refernce.
        var str = {
                alignment: M.util.get_string('alignment', 'atto_image'),
                alt: M.util.get_string('enteralt', 'atto_image'),
                browse: M.util.get_string('browserepositories', 'atto_image'),
                create: M.util.get_string('createimage', 'atto_image'),
                height: M.util.get_string('height', 'atto_image'),
                presentation: M.util.get_string('presentation', 'atto_image'),
                presentationrequired: M.util.get_string('presentationoraltrequired', 'atto_image'),
                preview: M.util.get_string('preview', 'atto_image'),
                width: M.util.get_string('width', 'atto_image'),
                url: M.util.get_string('enterurl', 'atto_image')
            },
            html,
            i;


        // Start the form.
        html = '<form class="atto_form">' +
               '<label for="' + CSS.INPUTURL + '">' + str.url + '</label>' +
               '<input class="fullwidth" type="url" value="" id="' + CSS.INPUTURL + '" size="32"/>' +
               '<br/>';

        if (M.editor_atto.can_show_filepicker(elementid, 'image')) {
            // Add the repository browser button.
            html += '<button id="' + CSS.IMAGEBROWSER + '" data-editor="' + Y.Escape.html(elementid) + '" type="button">' + str.browse + '</button>' +
                    '<br/>';
        }

        // Add the Alt box.
        html += '<div style="display:none" role="alert" id="' + CSS.IMAGEALTWARNING + '" class="warning">' + str.presentationrequired + '</div>' +
                '<label for="' + CSS.INPUTALT + '">' + str.alt + '</label>' +
                '<input class="fullwidth" type="text" value="" id="' + CSS.INPUTALT + '" size="32"/>' +
                '<br/>';

        // Add the presentation select box.
        html += '<input type="checkbox" id="' + CSS.IMAGEPRESENTATION + '"/>' +
                '<label class="sameline" for="' + CSS.IMAGEPRESENTATION + '">' + str.presentation + '</label>' +
                '<br/>';

        // Add the width entry box.
        html += '<label class="sameline" for="' + CSS.INPUTWIDTH + '">' + str.width + '</label>' +
                '<input type="text" value="" id="' + CSS.INPUTWIDTH + '" size="10"/>' +
                '<br/>';

        // Add the height entry box.
        html += '<label class="sameline" for="' + CSS.INPUTHEIGHT + '">' + str.height + '</label>' +
                '<input type="text" value="" id="' + CSS.INPUTHEIGHT + '" size="10"/>' +
                '<br/>';

        // Add the alignment selector.
        html += '<label class="sameline" for="' + CSS.INPUTALIGNMENT + '">' + str.alignment + '</label>' +
                '<select id="' + CSS.INPUTALIGNMENT + '">';
        for (i in ALIGNMENTS) {
            html += ALIGNMENTS[i].to_select_option();
        }
        html += '</select>' +
                '<br/>';

        // Add the image preview.
        html += '<label for="' + CSS.IMAGEPREVIEW + '">' + str.preview + '</label>' +
                '<img src="#" width="200" id="' + CSS.IMAGEPREVIEW + '" alt="" style="display: none;"/>' +
                '<div class="mdl-align">' +
                '<br/>';

        // Add the submit button and close the form.
        html += '<button id="' + CSS.INPUTSUMBIT + '" type="submit">' + str.create + '</button>' +
                '</div>' +
                '</form>';

        var content = Y.Node.create(html);
        this._apply_image_properties(content, elementid);

        content.one('#' + CSS.INPUTURL).on('blur', M.atto_image.url_changed, this);
        content.one('#' + CSS.INPUTSUMBIT).on('click', M.atto_image.set_image, this, elementid);
        if (M.editor_atto.can_show_filepicker(elementid, 'image')) {
            content.one('#' + CSS.IMAGEBROWSER).on('click', M.atto_image.open_filepicker);
        }
        return content;
    },
    /**
     * Applies properties of an existing image to the image dialogue for editing.
     *
     * @method _apply_image_properties
     * @private
     * @param {Node} form
     * @param {string} elementid
     */
    _apply_image_properties: function(form, elementid) {
        var properties = this._get_selected_image_properties(elementid),
            img = form.one('#' + CSS.IMAGEPREVIEW);

        if (properties === false) {
            img.setStyle('display', 'none');
            return;
        }

        if (properties.align) {
            properties.align.apply(img);
        }
        if (properties.display) {
            img.setStyle('display', properties.display);
        }
        if (properties.width) {
            form.one('#' + CSS.INPUTWIDTH).set('value', properties.width);
        }
        if (properties.height) {
            form.one('#' + CSS.INPUTHEIGHT).set('value', properties.height);
        }
        if (properties.alt) {
            form.one('#' + CSS.INPUTALT).set('value', properties.alt);
        }
        if (properties.src) {
            form.one('#' + CSS.INPUTURL).set('value', properties.src);
            img.setAttribute('src', properties.src);
        }
        if (properties.presentation) {
            form.one('#' + CSS.IMAGEPRESENTATION).set('checked', 'checked');
        }
    }
};


}, '@VERSION@', {"requires": ["node", "escape"]});
