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
 * TinyMCE helper javascript functions
 *
 * @package    editor_tinymce
 * @copyright  2010 Petr Skoda (skodak) info@skoda.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.editor_tinymce = M.editor_tinymce || {};

M.editor_tinymce.editor_options = M.editor_tinymce.options || {};
M.editor_tinymce.filepicker_options = M.editor_tinymce.filepicker_options || {};

M.editor_tinymce.init_editor = function(Y, editorid, options) {
    M.editor_tinymce.editor_options[editorid] = options;
    tinyMCE.init(options);

    var item = document.getElementById(editorid+'_filemanager');
    if (item) {
        item.parentNode.removeChild(item);
    }
};

M.editor_tinymce.init_filepicker = function(Y, editorid, options) {
    M.editor_tinymce.filepicker_options[editorid] = options;
};

M.editor_tinymce.toggle = function(id) {
    tinyMCE.execCommand('mceToggleEditor', false, id);
};

M.editor_tinymce.filepicker_callback = function(args) {
};

M.editor_tinymce.filepicker = function(target_id, url, type, win) {
    YUI(M.yui.loader).use('core_filepicker', function (Y) {
        var editor_id = tinyMCE.selectedInstance.editorId;
        if (editor_id == 'mce_fullscreen') {
            editor_id = tinyMCE.selectedInstance.settings.elements;
        }
        var options = null;
        if (type == 'media') {
            // when mediaw button clicked
            options = M.editor_tinymce.filepicker_options[editor_id]['media'];
        } else if (type == 'file') {
            // when link button clicked
            options = M.editor_tinymce.filepicker_options[editor_id]['link'];
        } else if (type == 'image') {
            // when image button clicked
            options = M.editor_tinymce.filepicker_options[editor_id]['image'];
        } 

        options.formcallback = M.editor_tinymce.filepicker_callback;
        options.editor_target = win.document.getElementById(target_id);

        M.core_filepicker.show(Y, options);
    });
};

M.editor_tinymce.onblur_event = function(ed) {
    //Attach event only after tinymce is intialized.
    if (ed.onInit != undefined) {
        var s = ed.settings;
        //Save before event is attached, so that if this event is not generated then textarea should
        //have loaded contents and submitting form should not throw error.
        ed.save();

        //Attach blur event for tinymce to save contents to textarea
        var doc = s.content_editable ? ed.getBody() : (tinymce.isGecko ? ed.getDoc() : ed.getWin());
        tinymce.dom.Event.add(doc, 'blur', function() {
            //save contents to textarea before calling validation script.
            ed.save();
        });
    };
};
