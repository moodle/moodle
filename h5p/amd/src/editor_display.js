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
 * This module handles the display of the H5P authoring tool.
 *
 * @module     core_h5p/editor_display
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
/* global H5PEditor */

/**
 * Display the H5P authoring tool.
 *
 * @param {String} elementId Root element.
 */
export const init = (elementId) => {
    const editorwrapper = $('#' + elementId);
    const editor = $('.h5p-editor');
    const mform = editor.closest("form");
    const editorupload = $("h5p-editor-upload");
    const h5plibrary = $('input[name="h5plibrary"]');
    const h5pparams = $('input[name="h5pparams"]');
    const inputname = $('input[name="name"]');
    const h5paction = $('input[name="h5paction"]');

    // Cancel validation and submission of form if clicking cancel button.
    const cancelSubmitCallback = function($button) {
        return $button.is('[name="cancel"]');
    };

    h5paction.val("create");

    H5PEditor.init(
        mform,
        h5paction,
        editorupload,
        editorwrapper,
        editor,
        h5plibrary,
        h5pparams,
        '',
        inputname,
        cancelSubmitCallback
    );
    document.querySelector('#' + elementId + ' iframe').setAttribute('name', 'h5p-editor');
};
