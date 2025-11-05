// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Media selection widget handler.
 *
 * NOTE: this was abstracted form board.js
 *
 * @copyright  2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from "jquery";

/**
 * Indicates if this is a keycode we want to listend to for
 * aria purposes.
 *
 * @returns {boolean}
 * @param {number} key
 */
const isAriaTriggerKey = function(key) {
    return key == 13 || key == 32;
};

/**
 * Handler for keypress and click actions.
 *
 * @param {object} elem
 * @param {function} callback
 * @returns {*}
 */
const handleAction = function(elem, callback) {
    return elem.on('click keypress', function(e) {
        if (e.type === 'keypress') {
            if (isAriaTriggerKey(e.keyCode)) {
                e.preventDefault();
            } else {
                return;
            }
        }

        callback();
        e.preventDefault();
    });
};

/**
 * Entrypoint of the js.
 *
 * @method init
 * @param {String} formId Id attribute of note editing form.
 */
export const init = (formId) => {
    const form = $(document.getElementById(formId));
    const mediaElement = form.find('.mod_board_type');
    const mediaSelect = $(mediaElement).find('select');

    // First hide the select menu.
    mediaElement.hide();

    const changeEvent = document.createEvent('HTMLEvents');
    changeEvent.initEvent('change', true, true);

    let ytButton = form.find('.mod_board_attachment_button.youtube_button'),
        pictureButton = form.find('.mod_board_attachment_button.image_button'),
        linkButton = form.find('.mod_board_attachment_button.link_button'),
        fileButton = form.find('.mod_board_attachment_button.file_button'),
        updateMediaButtons = function() {
            ytButton.removeClass('selected');
            pictureButton.removeClass('selected');
            linkButton.removeClass('selected');
            fileButton.removeClass('selected');
            switch (mediaSelect.val()) {
                case ("1"):
                    ytButton.addClass('selected');
                    break;
                case ("2"):
                    pictureButton.addClass('selected');
                    break;
                case ("3"):
                    linkButton.addClass('selected');
                    break;
                case ("4"):
                    fileButton.addClass('selected');
                    break;
            }
        };

    updateMediaButtons();
    handleAction(ytButton, function() {
        if (mediaSelect.val() === "1") {
            mediaSelect.val(0);
        } else {
            mediaSelect.val(1);
        }
        updateMediaButtons();
        mediaSelect[0].dispatchEvent(changeEvent);
    });
    handleAction(pictureButton, function() {
        if (mediaSelect.val() === "2") {
            mediaSelect.val(0);
        } else {
            mediaSelect.val(2);
        }
        updateMediaButtons();
        mediaSelect[0].dispatchEvent(changeEvent);
    });
    handleAction(linkButton, function() {
        if (mediaSelect.val() === "3") {
            mediaSelect.val(0);
        } else {
            mediaSelect.val(3);
        }
        updateMediaButtons();
        mediaSelect[0].dispatchEvent(changeEvent);
    });
    handleAction(fileButton, function() {
        if (mediaSelect.val() === "4") {
            mediaSelect.val(0);
        } else {
            mediaSelect.val(4);
        }
        updateMediaButtons();
        mediaSelect[0].dispatchEvent(changeEvent);
    });
};
