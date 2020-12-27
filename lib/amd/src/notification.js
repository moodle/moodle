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

import Pending from 'core/pending';
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
 * Poll the server for any new notifications.
 *
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
 * @param {Array} notifications The list of notificaitons
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
 * @param {Object}  notification                The notification to add.
 * @param {string}  notification.message        The body of the notification
 * @param {string}  notification.type           The type of notification to add (error, warning, info, success).
 * @param {Boolean} notification.closebutton    Whether to show the close button.
 * @param {Boolean} notification.announce       Whether to announce to screen readers.
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
 * @param {String|Promise} title
 * @param {String|Promise} message
 * @param {String|Promise} cancelText
 * @returns {Promise}
 */
export const alert = async(title, message, cancelText) => {
    var pendingPromise = new Pending('core/notification:alert');

    const ModalFactory = await import('core/modal_factory');

    return ModalFactory.create({
        type: ModalFactory.types.ALERT,
        body: message,
        title: title,
        buttons: {
            cancel: cancelText,
        },
        removeOnClose: true,
    })
    .then(function(modal) {
        modal.show();

        pendingPromise.resolve();
        return modal;
    });
};

/**
 * The confirm has now been replaced with a save and cancel dialogue.
 *
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
 * @param {String|Promise} title
 * @param {String|Promise} question
 * @param {String|Promise} saveLabel
 * @param {String|Promise} saveCallback
 * @param {String|Promise} cancelCallback
 * @returns {Promise}
 */
export const saveCancel = async(title, question, saveLabel, saveCallback, cancelCallback) => {
    const pendingPromise = new Pending('core/notification:confirm');

    const [
        ModalFactory,
        ModalEvents,
    ] = await Promise.all([
        import('core/modal_factory'),
        import('core/modal_events'),
    ]);

    return ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        title: title,
        body: question,
        buttons: {
            // Note: The noLabel is no longer supported.
            save: saveLabel,
        },
        removeOnClose: true,
    })
    .then(function(modal) {
        modal.show();

        modal.getRoot().on(ModalEvents.save, saveCallback);
        modal.getRoot().on(ModalEvents.cancel, cancelCallback);
        pendingPromise.resolve();

        return modal;
    });
};

/**
 * Wrap M.core.exception.
 *
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
 * @param {Number} contextId
 * @param {Array} notificationList
 */
export const init = (contextId, notificationList) => {
    currentContextId = contextId;

    // Setup the message target region if it isn't setup already
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
    exception,
};
