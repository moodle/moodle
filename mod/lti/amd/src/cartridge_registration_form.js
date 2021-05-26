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
 * Encapsules the behavior for creating a tool type from a cartridge URL
 * in Moodle. Manages the UI while operations are occuring.
 *
 * See template: mod_lti/cartridge_registration_form
 *
 * @module     mod_lti/cartridge_registration_form
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery', 'core/ajax', 'core/notification', 'mod_lti/tool_type', 'mod_lti/events', 'mod_lti/keys', 'core/str'],
        function($, ajax, notification, toolType, ltiEvents, KEYS, str) {

    var SELECTORS = {
        CARTRIDGE_URL: '#cartridge-url',
        CONSUMER_KEY: '#registration-key',
        SHARED_SECRET: '#registration-secret',
        REGISTRATION_FORM: '#cartridge-registration-form',
        REGISTRATION_SUBMIT_BUTTON: '#cartridge-registration-submit',
        REGISTRATION_CANCEL_BUTTON: '#cartridge-registration-cancel',
    };

    /**
     * Return the URL the user entered for the cartridge.
     *
     * @method getCartridgeURL
     * @private
     * @return {String}
     */
    var getCartridgeURL = function() {
        return $(SELECTORS.REGISTRATION_FORM).attr('data-cartridge-url');
    };

    /**
     * Return the submit button element.
     *
     * @method getSubmitButton
     * @private
     * @return {JQuery} jQuery object
     */
    var getSubmitButton = function() {
        return $(SELECTORS.REGISTRATION_SUBMIT_BUTTON);
    };

    /**
     * Return the cancel button element.
     *
     * @method getCancelButton
     * @private
     * @return {JQuery} jQuery object
     */
    var getCancelButton = function() {
        return $(SELECTORS.REGISTRATION_CANCEL_BUTTON);
    };

    /**
     * Return the value that the user entered for the consumer key.
     *
     * @method getConsumerKey
     * @private
     * @return {String} the value entered for consumer key.
     */
    var getConsumerKey = function() {
        return $(SELECTORS.CONSUMER_KEY).val();
    };

    /**
     * Return the value that the user entered for the shared secret.
     *
     * @method getSharedSecret
     * @private
     * @return {String} the value entered for shared secret
     */
    var getSharedSecret = function() {
        return $(SELECTORS.SHARED_SECRET).val();
    };

    /**
     * Trigger a visual loading indicator.
     *
     * @method startLoading
     * @private
     */
    var startLoading = function() {
        getSubmitButton().addClass('loading');
    };

    /**
     * Stop the visual loading indicator.
     *
     * @method stopLoading
     * @private
     */
    var stopLoading = function() {
        getSubmitButton().removeClass('loading');
    };

    /**
     * Check if the page is currently loading.
     *
     * @method isLoading
     * @private
     * @return {Boolean}
     */
    var isLoading = function() {
        return getSubmitButton().hasClass('loading');
    };

    /**
     * Create a tool type from the cartridge URL that the user input. This will
     * send an ajax request to the Moodle server to create the Type. The request will
     * include the consumer key and secret, if any.
     *
     * On success the page will be re-rendered to take the user back to the original
     * page with the list of tools and an alert notifying them of success.
     *
     * @method submitCartridgeURL
     * @private
     * @return {Promise} jQuery Deferred object
     */
    var submitCartridgeURL = function() {
        if (isLoading()) {
            return false;
        }

        var url = getCartridgeURL();
        // No URL? Do nothing.
        if (url === "") {
            return false;
        }

        startLoading();
        var consumerKey = getConsumerKey();
        var sharedSecret = getSharedSecret();
        var promise = toolType.create({cartridgeurl: url, key: consumerKey, secret: sharedSecret});

        promise.done(function() {
            str.get_string('successfullycreatedtooltype', 'mod_lti').done(function(s) {
                $(document).trigger(ltiEvents.NEW_TOOL_TYPE);
                $(document).trigger(ltiEvents.STOP_CARTRIDGE_REGISTRATION);
                $(document).trigger(ltiEvents.REGISTRATION_FEEDBACK, {
                    message: s
                });
            }).fail(notification.exception);
        }).fail(function() {
            str.get_string('failedtocreatetooltype', 'mod_lti').done(function(s) {
                $(document).trigger(ltiEvents.NEW_TOOL_TYPE);
                $(document).trigger(ltiEvents.STOP_CARTRIDGE_REGISTRATION);
                $(document).trigger(ltiEvents.REGISTRATION_FEEDBACK, {
                    message: s,
                    error: true
                });
            }).fail(notification.exception);
        }).always(function() {
          stopLoading();
        });

        return promise;
    };

    /**
     * Sets up the listeners for user interaction on the page.
     *
     * @method registerEventListeners
     * @private
     */
    var registerEventListeners = function() {
        var form = $(SELECTORS.REGISTRATION_FORM);
        form.submit(function(e) {
            e.preventDefault();
            submitCartridgeURL();
        });

        var cancelButton = getCancelButton();
        cancelButton.click(function(e) {
            e.preventDefault();
            $(document).trigger(ltiEvents.STOP_CARTRIDGE_REGISTRATION);
        });
        cancelButton.keypress(function(e) {
            if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                if (e.keyCode == KEYS.ENTER || e.keyCode == KEYS.SPACE) {
                    e.preventDefault();
                    cancelButton.click();
                }
            }
        });
    };

    return /** @alias module:mod_lti/cartridge_registration_form */ {

        /**
         * Initialise this module.
         */
        init: function() {
            registerEventListeners();
        }
    };
});
