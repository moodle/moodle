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
 * Contain the logic for the yes/no confirmation modal.
 * This has been deprecated and should not be used anymore. Please use core/modal_save_cancel instead.
 * See MDL-59759.
 *
 * @deprecated Since Moodle 3.4
 * @module     core/modal_confirm
 * @class      modal_confirm
 * @package    core
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/custom_interaction_events', 'core/modal_events', 'core/modal_save_cancel', 'core/log'],
        function($, CustomEvents, ModalEvents, ModalSaveCancel, Log) {

    /**
     * Constructor for the Modal.
     *
     * @param {object} root The root jQuery element for the modal
     */
    var ModalConfirm = function(root) {
        Log.warn("The CONFIRM modal type has been deprecated and should not be used anymore." +
            " Please use the SAVE_CANCEL modal type instead.");
        ModalSaveCancel.call(this, root);
    };

    ModalConfirm.prototype = Object.create(ModalSaveCancel.prototype);
    ModalConfirm.prototype.constructor = ModalConfirm;

    return ModalConfirm;
});
