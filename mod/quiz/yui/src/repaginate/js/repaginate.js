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
 * Repaginate functionality for a popup in quiz editing page.
 *
 * @package   mod_quiz
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var CSS = {
    REPAGINATECONTAINERCLASS: '.rpcontainerclass',
    REPAGINATECOMMAND: '#repaginatecommand'
};

var PARAMS = {
    CMID: 'cmid',
    HEADER: 'header',
    FORM: 'form'
};

var POPUP = function() {
    POPUP.superclass.constructor.apply(this, arguments);
};

Y.extend(POPUP, Y.Base, {
    header: null,
    body: null,

    initializer: function() {
        var rpcontainerclass = Y.one(CSS.REPAGINATECONTAINERCLASS);

        // Set popup header and body.
        this.header = rpcontainerclass.getAttribute(PARAMS.HEADER);
        this.body = rpcontainerclass.getAttribute(PARAMS.FORM);
        Y.one(CSS.REPAGINATECOMMAND).on('click', this.display_dialog, this);
    },

    display_dialog: function(e) {
        e.preventDefault();

        // Configure the popup.
        var config = {
            headerContent: this.header,
            bodyContent: this.body,
            draggable: true,
            modal: true,
            zIndex: 1000,
            context: [CSS.REPAGINATECOMMAND, 'tr', 'br', ['beforeShow']],
            centered: false,
            width: '30em',
            visible: false,
            postmethod: 'form',
            footerContent: null
        };

        var popup = {dialog: null};
        popup.dialog = new M.core.dialogue(config);
        popup.dialog.show();
    }
});

M.mod_quiz = M.mod_quiz || {};
M.mod_quiz.repaginate = M.mod_quiz.repaginate || {};
M.mod_quiz.repaginate.init = function() {
    return new POPUP();
};
