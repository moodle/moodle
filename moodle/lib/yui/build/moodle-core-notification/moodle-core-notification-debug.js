YUI.add('moodle-core-notification', function (Y, NAME) {

/**
 * The notification module provides a standard set of dialogues for use
 * within Moodle.
 *
 * @module moodle-core-notification
 * @main
 */

/**
 * To avoid bringing moodle-core-notification into modules in it's
 * entirety, we now recommend using on of the subclasses of
 * moodle-core-notification. These include:
 * <dl>
 *  <dt> moodle-core-notification-dialogue</dt>
 *  <dt> moodle-core-notification-alert</dt>
 *  <dt> moodle-core-notification-confirm</dt>
 *  <dt> moodle-core-notification-exception</dt>
 *  <dt> moodle-core-notification-ajaxexception</dt>
 * </dl>
 *
 * @class M.core.notification
 * @deprecated
 */
Y.log("The moodle-core-notification parent module has been deprecated. " +
        "Please use one of its subclasses instead.", 'moodle-core-notification', 'warn');


}, '@VERSION@', {
    "requires": [
        "moodle-core-notification-dialogue",
        "moodle-core-notification-alert",
        "moodle-core-notification-confirm",
        "moodle-core-notification-exception",
        "moodle-core-notification-ajaxexception"
    ]
});
