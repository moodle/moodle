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
 * Atto text editor image plugin.
 *
 * @package    editor-atto
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.atto_image = M.atto_image || {
    dialogue : null,
    selection : null,
    init : function(params) {
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

                var selectedText = M.editor_atto.get_selection_text();
                var i = 0;

                var images = [];
                for (i = 0; i < selectedText.childNodes.length; i++) {
                    var child = selectedText.childNodes[0];
                    if (images.length === 0) {
                        if (child.nodeName.toLowerCase() === 'img') {
                            images[0] = child;
                        } else {
                            if (child.getElementsByTagName) {
                                images = child.getElementsByTagName('img');
                            }
                        }
                    }
                }

                if (images.length > 0) {
                    var image = Y.one(images[0]);
                    var width = image.getAttribute('width');
                    var height = image.getAttribute('height');
                    if (width > 0) {
                        Y.one('#atto_image_widthentry').set('value', width);
                    }
                    if (height > 0) {
                        Y.one('#atto_image_heightentry').set('value', height);
                    }
                    Y.one('#atto_image_preview').set('src', image.get('src'));
                    Y.one('#atto_image_preview').setStyle('display', 'inline');
                    Y.one('#atto_image_altentry').set('value', image.get('alt'));
                    Y.one('#atto_image_urlentry').set('value', image.get('src'));
                    var role = image.get('role');
                    if (role === "presentation") {
                        Y.one('#atto_image_presentation').set('checked', 'checked');
                    }
                }
                dialogue.show();
            }
        };

        var iconurl = M.util.image_url('e/insert_edit_image', 'core');
        M.editor_atto.add_toolbar_button(params.elementid, 'image', iconurl, params.group, display_chooser, this);
    },
    open_filepicker : function(e) {
        var elementid = this.getAttribute('data-editor');
        e.preventDefault();

        M.editor_atto.show_filepicker(elementid, 'image', M.atto_image.filepicker_callback);
    },
    filepicker_callback : function(params) {
        if (params.url !== '') {
            var input = Y.one('#atto_image_urlentry');
            input.set('value', params.url);

            // Auto set the width and height.
            var image = new Image();
            image.onload = function() {
                Y.one('#atto_image_widthentry').set('value', this.width);
                Y.one('#atto_image_heightentry').set('value', this.height);
                Y.one('#atto_image_preview').set('src', this.src);
                Y.one('#atto_image_preview').setStyle('display', 'inline');
            };
            image.src = params.url;
        }
    },
    url_changed : function() {
        var input = Y.one('#atto_image_urlentry');

        if (input.get('value') !== '') {
            // Auto set the width and height.
            var image = new Image();
            image.onload = function() {
                var input;

                input = Y.one('#atto_image_widthentry');
                if (input.get('value') === '') {
                    input.set('value', this.width);
                }
                input = Y.one('#atto_image_heightentry');
                if (input.get('value') === '') {
                    input.set('value', this.height);
                }
                input = Y.one('#atto_image_preview');
                input.set('src', this.src);
                input.setStyle('display', 'inline');
            };
            image.src = input.get('value');
        }
    },
    set_image : function(e, elementid) {
        var input = e.currentTarget.ancestor('.atto_form').one('#atto_image_urlentry');

        var url = input.get('value');
        input = e.currentTarget.ancestor('.atto_form').one('#atto_image_altentry');
        var alt = input.get('value');
        input = e.currentTarget.ancestor('.atto_form').one('#atto_image_widthentry');
        var width = input.get('value');
        input = e.currentTarget.ancestor('.atto_form').one('#atto_image_heightentry');
        var height = input.get('value');
        input = e.currentTarget.ancestor('.atto_form').one('#atto_image_presentation');
        var presentation = input.get('checked');
        var alrt;

        e.preventDefault();

        if (alt === '' && !presentation) {
            alrt = e.currentTarget.ancestor('.atto_form').one('#atto_image_altwarning');
            alrt.setStyle('display', 'block');
            input = e.currentTarget.ancestor('.atto_form').one('#atto_image_altentry');
            input.setAttribute('aria-invalid', true);
            input = e.currentTarget.ancestor('.atto_form').one('#atto_image_presentation');
            input.setAttribute('aria-invalid', true);
            return;
        } else {
            alrt = e.currentTarget.ancestor('.atto_form').one('#atto_image_altwarning');
            alrt.setStyle('display', 'none');
            input = e.currentTarget.ancestor('.atto_form').one('#atto_image_altentry');
            input.setAttribute('aria-invalid', false);
            input = e.currentTarget.ancestor('.atto_form').one('#atto_image_presentation');
            input.setAttribute('aria-invalid', false);
        }

        M.atto_image.dialogue.hide();

        if (url !== '') {
            M.editor_atto.set_selection(M.atto_image.selection);
            var imagehtml = '<img src="' + Y.Escape.html(url) + '" alt="' + Y.Escape.html(alt) + '"';

            if (width) {
                imagehtml += ' width="' + Y.Escape.html(width) + '"';
            }
            if (height) {
                imagehtml += ' height="' + Y.Escape.html(height) + '"';
            }
            if (presentation) {
                imagehtml += ' role="presentation"';
            }
            imagehtml += '/>';

            if (document.selection && document.selection.createRange().pasteHTML) {
                document.selection.createRange().pasteHTML(imagehtml);
            } else {
                document.execCommand('insertHTML', false, imagehtml);
            }
            // Clean the YUI ids from the HTML.
            M.editor_atto.text_updated(elementid);
        }
    },
    get_form_content : function(elementid) {
        var html = '<form class="atto_form">' +
                   '<label for="atto_image_urlentry">' + M.util.get_string('enterurl', 'atto_image') +
                   '</label>' +
                   '<input class="fullwidth" type="url" value="" id="atto_image_urlentry" size="32"/>' +
                   '<br/>';
        if (M.editor_atto.can_show_filepicker(elementid, 'image')) {
            html += '<button id="openimagebrowser" data-editor="' + Y.Escape.html(elementid) + '">' +
                    M.util.get_string('browserepositories', 'atto_image') +
                    '</button>' +
                    '<br/>';
        }
        html += '<div style="display:none" role="alert" id="atto_image_altwarning" class="warning">' +
                M.util.get_string('presentationoraltrequired', 'atto_image') +
                '</div>' +
                '<label for="atto_image_altentry">' + M.util.get_string('enteralt', 'atto_image') +
                '</label>' +
                '<input class="fullwidth" type="text" value="" id="atto_image_altentry" size="32"/>' +
                '<br/>' +
                '<input type="checkbox" id="atto_image_presentation"/>' +
                '<label class="sameline" for="atto_image_presentation">' + M.util.get_string('presentation', 'atto_image') +
                '</label>' +
                '<br/>' +
                '<label class="sameline" for="atto_image_widthentry">' + M.util.get_string('width', 'atto_image') +
                '</label>' +
                '<input type="text" value="" id="atto_image_widthentry" size="10"/>' +
                '<br/>' +
                '<label class="sameline" for="atto_image_heightentry">' + M.util.get_string('height', 'atto_image') +
                '</label>' +
                '<input type="text" value="" id="atto_image_heightentry" size="10"/>' +
                '<br/>' +
                '<label for="atto_image_preview">' + M.util.get_string('preview', 'atto_image') +
                '</label>' +
                '<img src="#" width="200" id="atto_image_preview" alt="" style="display: none;"/>' +
                '<div class="mdl-align">' +
                '<br/>' +
                '<button id="atto_image_urlentrysubmit">' +
                M.util.get_string('createimage', 'atto_image') +
                '</button>' +
                '</div>' +
                '</form>' +
                '<hr/>' + M.util.get_string('accessibilityhint', 'atto_image');

        var content = Y.Node.create(html);

        content.one('#atto_image_urlentry').on('blur', M.atto_image.url_changed, this);
        content.one('#atto_image_urlentrysubmit').on('click', M.atto_image.set_image, this, elementid);
        if (M.editor_atto.can_show_filepicker(elementid, 'image')) {
            content.one('#openimagebrowser').on('click', M.atto_image.open_filepicker);
        }
        return content;
    }
};
