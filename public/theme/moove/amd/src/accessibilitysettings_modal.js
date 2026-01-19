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
 * Theme settings modal js.
 *
 * @package
 * @copyright  2022 Willian Mano - https://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Ajax from 'core/ajax';
import Modal from 'core/modal';
import * as CustomEvents from 'core/custom_interaction_events';
import Notification from 'core/notification';

export default class AccessibilityModal extends Modal {
    static TYPE = "theme_moove/themesettings_modal";
    static TEMPLATE = "theme_moove/moove/accessibilitysettings_modal";

    constructor(root) {
        super(root);

        let request = Ajax.call([{
            methodname: 'theme_moove_getthemesettings',
            args: {}
        }]);

        request[0].done(function(result) {
            document.getElementById('fonttype').value = result.fonttype;

            if (result.enableaccessibilitytoolbar) {
                document.getElementById('enableaccessibilitytoolbar').checked = true;
            }
        });
    }

    /**
     * Set up all of the event handling for the modal.
     */
    registerEventListeners() {
        // Apply parent event listeners.
        super.registerEventListeners(this);

        this.getModal().on(CustomEvents.events.activate, '[data-action="save"]', function() {
            let request = Ajax.call([{
                methodname: 'theme_moove_savethemesettings',
                args: {
                    formdata: this.getBody().find('form').serialize()
                }
            }]);

            request[0].done(function() {
                document.location.reload(true);
            }).fail(function(error) {
                let message = error.message;

                if (!message) {
                    message = error.error;
                }

                Notification.addNotification({
                    message: message,
                    type: 'error'
                });

                this.hide();

                this.destroy();
            }.bind(this));
        }.bind(this));

        this.getModal().on(CustomEvents.events.activate, '[data-action="cancel"]', function() {
            this.hide();
            this.destroy();
        }.bind(this));
    }
}