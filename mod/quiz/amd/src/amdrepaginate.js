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
 * This class manages the confirmation pop-up (also called the pre-flight check)
 * that is sometimes shown when a use clicks the start attempt button.
 *
 * This is also responsible for opening the pop-up window, if the quiz requires to be in one.
 *
 * @module    mod_quiz/questionbank
 * @class     questionbank
 * @package   mod_quiz
 * @copyright 2016 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     3.1
 */
define(['jquery'], function($) {

    var CSS = {
        REPAGINATECONTAINERCLASS: '.rpcontainerclass',
        REPAGINATECOMMAND: '#repaginatecommand'
    };

    var PARAMS = {
        PAGE: 'addonpage',
        HEADER: 'header',
        FORM: 'form'
    };

    var POPUP = function() {
        var rpcontainerclass = $(CSS.REPAGINATECONTAINERCLASS);
        // Set popup header and body.
        var header = rpcontainerclass.attr(PARAMS.HEADER);
        var body = rpcontainerclass.attr(PARAMS.FORM);
        $(CSS.REPAGINATECOMMAND).click(function(){
            display_dialog(header,body);
        });
    };

    function display_dialog(header,body) {
        // Configure the popup.
        var config = {
            headerContent: header,
            bodyContent: body,
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


    return {
        init: function() {
            M.mod_quiz = M.mod_quiz || {};
            M.mod_quiz.repaginate = M.mod_quiz.repaginate || {};
            return new POPUP();
        }
    };

});
