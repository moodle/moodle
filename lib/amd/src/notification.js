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
 * Notification manager for in-page notifications in Moodle.
 *
 * @module     core/notification
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */

import Pending from 'core/pending';
import ModalEvents from 'core/modal_events';
import Log from 'core/log';

let currentContextId = M.cfg.contextid;

const notificationTypes = {
    success:  'core/notification_success',
    info:     'core/notification_info',
    warning:  'core/notification_warning',
    error:    'core/notification_error',
};

const notificationRegionId = 'user-notifications';

const Selectors = {
    notificationRegion: `#${notificationRegionId}`,
    fallbackRegionParents: [
        '#region-main',
        '[role="main"]',
        'body',
    ],
};

const setupTargetRegion = () => {
    let targetRegion = getNotificationRegion();
    if (targetRegion) {
        return false;
    }

    const newRegion = document.createElement('span');
    newRegion.id = notificationRegionId;

    return Selectors.fallbackRegionParents.some(selector => {
        const targetRegion = document.querySelector(selector);

        if (targetRegion) {
            targetRegion.prepend(newRegion);
            return true;
        }

        return false;
    });
};

/**
 * A notification object displayed to a user.
 *
 * @typedef  {Object} Notification
 * @property {string} message       The body of the notification
 * @property {string} type          The type of notification to add (error, warning, info, success).
 * @property {Boolean} closebutton  Whether to show the close button.
 * @property {Boolean} announce     Whether to announce to screen readers.
 */

/**
 * Poll the server for any new notifications.
 *
 * @method
 * @returns {Promise}
 */
export const fetchNotifications = async() => {
    const Ajax = await import('core/ajax');

    return Ajax.call([{
        methodname: 'core_fetch_notifications',
        args: {
            contextid: currentContextId
        }
    }])[0]
    .then(addNotifications);
};

/**
 * Add all of the supplied notifications.
 *
 * @method
 * @param {Notification[]} notifications The list of notificaitons
 * @returns {Promise}
 */
const addNotifications = notifications => {
    if (!notifications.length) {
        return Promise.resolve();
    }

    const pendingPromise = new Pending('core/notification:addNotifications');
    notifications.forEach(notification => renderNotification(notification.template, notification.variables));

    return pendingPromise.resolve();
};

/**
 * Add a notification to the page.
 *
 * Note: This does not cause the notification to be added to the session.
 *
 * @method
 * @param {Notification} notification The notification to add.
 * @returns {Promise}
 */
export const addNotification = notification => {
    const pendingPromise = new Pending('core/notification:addNotifications');

    let template = notificationTypes.error;

    notification = {
        closebutton:    true,
        announce:       true,
        type:           'error',
        ...notification,
    };

    if (notification.template) {
        template = notification.template;
        delete notification.template;
    } else if (notification.type) {
        if (typeof notificationTypes[notification.type] !== 'undefined') {
            template = notificationTypes[notification.type];
        }
        delete notification.type;
    }

    return renderNotification(template, notification)
    .then(pendingPromise.resolve);
};

const renderNotification = async(template, variables) => {
    if (typeof variables.message === 'undefined' || !variables.message) {
        Log.debug('Notification received without content. Skipping.');
        return;
    }

    const pendingPromise = new Pending('core/notification:renderNotification');
    const Templates = await import('core/templates');

    Templates.renderForPromise(template, variables)
    .then(({html, js = ''}) => {
        Templates.prependNodeContents(getNotificationRegion(), html, js);

        return;
    })
    .then(pendingPromise.resolve)
    .catch(exception);
};

const getNotificationRegion = () => document.querySelector(Selectors.notificationRegion);

/**
 * Alert dialogue.
 *
 * @method
 * @param {String|Promise} title
 * @param {String|Promise} message
 * @param {String|Promise} cancelText
 * @returns {Promise}
 */
export const alert = async(title, message, cancelText) => {
    var pendingPromise = new Pending('core/notification:alert');

    const AlertModal = await import('core/local/modal/alert');

    const modal = await AlertModal.create({
        body: message,
        title: title,
        buttons: {
            cancel: cancelText,
        },
        removeOnClose: true,
        show: true,
    });
    pendingPromise.resolve();
    return modal;
};

/**
 * The confirm has now been replaced with a save and cancel dialogue.
 *
 * @method
 * @param {String|Promise} title
 * @param {String|Promise} question
 * @param {String|Promise} saveLabel
 * @param {String|Promise} noLabel
 * @param {String|Promise} saveCallback
 * @param {String|Promise} cancelCallback
 * @returns {Promise}
 */
export const confirm = (title, question, saveLabel, noLabel, saveCallback, cancelCallback) =>
        saveCancel(title, question, saveLabel, saveCallback, cancelCallback);

/**
 * The Save and Cancel dialogue helper.
 *
 * @method
 * @param {String|Promise} title
 * @param {String|Promise} question
 * @param {String|Promise} saveLabel
 * @param {String|Promise} saveCallback
 * @param {String|Promise} cancelCallback
 * @param {Object} options
 * @param {HTMLElement} [options.triggerElement=null] The element that triggered the modal (will receive the focus after hidden)
 * @returns {Promise}
 */
export const saveCancel = async(title, question, saveLabel, saveCallback, cancelCallback, {
    triggerElement = null,
} = {}) => {
    const pendingPromise = new Pending('core/notification:confirm');

    const [
        SaveCancelModal,
    ] = await Promise.all([
        import('core/modal_save_cancel'),
    ]);

    const modal = await SaveCancelModal.create({
        title,
        body: question,
        buttons: {
            // Note: The noLabel is no longer supported.
            save: saveLabel,
        },
        removeOnClose: true,
        show: true,
    });
    modal.getRoot().on(ModalEvents.save, saveCallback);
    modal.getRoot().on(ModalEvents.cancel, cancelCallback);
    modal.getRoot().on(ModalEvents.hidden, () => triggerElement?.focus());
    pendingPromise.resolve();

    return modal;
};

/**
 * The Delete and Cancel dialogue helper.
 *
 * @method
 * @param {String|Promise} title
 * @param {String|Promise} question
 * @param {String|Promise} deleteLabel
 * @param {String|Promise} deleteCallback
 * @param {String|Promise} cancelCallback
 * @param {Object} options
 * @param {HTMLElement} [options.triggerElement=null] The element that triggered the modal (will receive the focus after hidden)
 * @returns {Promise}
 */
export const deleteCancel = async(title, question, deleteLabel, deleteCallback, cancelCallback, {
    triggerElement = null,
} = {}) => {
    const pendingPromise = new Pending('core/notification:confirm');

    const [
        DeleteCancelModal,
    ] = await Promise.all([
        import('core/modal_delete_cancel'),
    ]);

    const modal = await DeleteCancelModal.create({
        title: title,
        body: question,
        buttons: {
            'delete': deleteLabel
        },
        removeOnClose: true,
        show: true,
    });
        modal.getRoot().on(ModalEvents.delete, deleteCallback);
        modal.getRoot().on(ModalEvents.cancel, cancelCallback);
        modal.getRoot().on(ModalEvents.hidden, () => triggerElement?.focus());
        pendingPromise.resolve();

        return modal;
};


/**
 * Add all of the supplied notifications.
 *
 * @param {Promise|String} title The header of the modal
 * @param {Promise|String} question What do we want the user to confirm
 * @param {Promise|String} saveLabel The modal action link text
 * @param {Object} options
 * @param {HTMLElement} [options.triggerElement=null] The element that triggered the modal (will receive the focus after hidden)
 * @return {Promise}
 */
export const saveCancelPromise = (title, question, saveLabel, {
    triggerElement = null,
} = {}) => new Promise((resolve, reject) => {
    saveCancel(title, question, saveLabel, resolve, reject, {triggerElement})
        .then(modal => modal.getRoot().on(ModalEvents.hidden, reject));
});

/**
 * Add all of the supplied notifications.
 *
 * @param {Promise|String} title The header of the modal
 * @param {Promise|String} question What do we want the user to confirm
 * @param {Promise|String} deleteLabel The modal action link text
 * @param {Object} options
 * @param {HTMLElement} [options.triggerElement=null] The element that triggered the modal (will receive the focus after hidden)
 * @return {Promise}
 */
export const deleteCancelPromise = (title, question, deleteLabel, {
    triggerElement = null,
} = {}) => new Promise((resolve, reject) => {
    deleteCancel(title, question, deleteLabel, resolve, reject, {triggerElement});
});

/**
 * Wrap M.core.exception.
 *
 * @method
 * @param {Error} ex
 */
export const exception = async ex => {
    const pendingPromise = new Pending('core/notification:displayException');

    // Fudge some parameters.
    if (!ex.stack) {
        ex.stack = '';
    }

    if (ex.debuginfo) {
        ex.stack += ex.debuginfo + '\n';
    }

    if (!ex.backtrace && ex.stacktrace) {
        ex.backtrace = ex.stacktrace;
    }

    if (ex.backtrace) {
        ex.stack += ex.backtrace;
        const ln = ex.backtrace.match(/line ([^ ]*) of/);
        const fn = ex.backtrace.match(/ of ([^:]*): /);
        if (ln && ln[1]) {
            ex.lineNumber = ln[1];
        }
        if (fn && fn[1]) {
            ex.fileName = fn[1];
            if (ex.fileName.length > 30) {
                ex.fileName = '...' + ex.fileName.substr(ex.fileName.length - 27);
            }
        }
    }

    if (typeof ex.name === 'undefined' && ex.errorcode) {
        ex.name = ex.errorcode;
    }

    const Y = await import('core/yui');
    Y.use('moodle-core-notification-exception', function() {
        var modal = new M.core.exception(ex);

        modal.show();

        pendingPromise.resolve();
    });
};

/**
 * Initialise the page for the suppled context, and displaying the supplied notifications.
 *
 * @method
 * @param {Number} contextId
 * @param {Notification[]} notificationList
 */
export const init = (contextId, notificationList) => {
    currentContextId = contextId;

    // Setup the message target region if it isn't setup already.
    setupTargetRegion();

    // Add provided notifications.
    addNotifications(notificationList);
};

// To maintain backwards compatability we export default here.
export default {
    init,
    fetchNotifications,
    addNotification,
    alert,
    confirm,
    saveCancel,
    deleteCancel,
    saveCancelPromise,
    deleteCancelPromise,
    exception,
};
