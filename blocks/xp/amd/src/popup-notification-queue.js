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
 * Notification popup queue.
 *
 * @copyright  2022 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['block_xp/popup-notification', 'core/ajax'], function(PopupNotification, Ajax) {
    let instances = [];
    let isShowing = false;

    /**
     * Notify received new instance.
     */
    function notifyNewInstance() {
        if (!isShowing) {
            showNextInstance();
        }
    }

    /**
     * Show next instance.
     */
    function showNextInstance() {
        if (!instances.length) {
            return;
        }
        isShowing = true;
        const instance = instances.splice(0, 1)[0];
        PopupNotification.show(instance, {
            onShown: () => {
                Ajax.call([{
                    methodname: 'block_xp_mark_popup_notification_seen',
                    args: {
                        courseid: instance.courseid,
                        level: instance.levelnum
                    }
                }])[0].fail(function() {
                    // Nothing.
                });
            },
            onDismissed: () => {
                isShowing = false;
                setTimeout(() => showNextInstance(), 300);
            },
        });
    }

    /**
     * Queue instances.
     *
     * @param {Object[]} additionalInstances The instances.
     */
    function queue(additionalInstances) {
        instances = instances.concat(additionalInstances);
        notifyNewInstance();
    }

    /**
     * Queue from JSON node.
     *
     * @param {String} selector The JSON node selector.
     */
    const queueFromJson = (selector) => {
        try {
            const node = document.querySelector(selector);
            const data = node ? JSON.parse(node.textContent) : null;
            if (!Array.isArray(data)) {
                throw new Error("That's a bit strange.");
            }
            queue(data);
        } catch (err) {
            // Nothing.
        }
    };

    return {
        queue,
        queueFromJson,
    };
});
