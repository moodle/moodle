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
 * Notification popup.
 *
 * @copyright  2022 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/templates', 'core/modal', 'core/modal_events', 'core/notification'], function (
    Templates,
    Modal,
    ModalEvents,
    Notification
) {
    // Trigger pre-loading.
    Templates.render('block_xp/modal-popup-notification', []);

    /**
     * Show the modal.
     *
     * @param {Object} context The template context.
     * @param {Object} options The options.
     */
    function show(context, options) {
        options = options || {};
        Templates.render('block_xp/modal-popup-notification', context)
            .then((html) => {
                const modal = new Modal(html);
                const footer = modal.getFooter()[0];
                const hideBtn = footer.querySelector('[data-action="hide"]');

                // Prevent dismissing by clicking outside.
                if (typeof ModalEvents.outsideClick !== 'undefined') {
                    modal.getRoot().on(ModalEvents.outsideClick, (e) => {
                        e.preventDefault();
                    });
                }

                // Broadcast when the modal has been shown.
                modal.getRoot().on(ModalEvents.shown, () => {
                    if (options.onShown) {
                        options.onShown();
                    }

                    // Show the button after a few seconds.
                    setTimeout(() => {
                        hideBtn.classList.remove('xp-pointer-events-none');
                        hideBtn.classList.remove('xp-invisible');
                        hideBtn.classList.remove('xp-opacity-0');
                    }, 4000);
                });

                // Broadcast when the modal has been dismissed.
                modal.getRoot().on(ModalEvents.hidden, () => {
                    if (options.onDismissed) {
                        options.onDismissed();
                    }
                });

                modal.show();
                return;
            })
            .catch(Notification.exception);
    }

    return {
        show,
    };
});
