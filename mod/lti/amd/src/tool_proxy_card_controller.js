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
 * Controls all of the behaviour and interaction with a tool type card. These are
 * listed on the LTI tool type management page.
 *
 * See template: mod_lti/tool_proxy_card
 *
 * @module     mod_lti/tool_proxy_card_controller
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery', 'core/ajax', 'core/notification', 'core/templates', 'mod_lti/tool_proxy', 'mod_lti/events', 'mod_lti/keys',
        'core/str'],
        function($, ajax, notification, templates, toolProxy, ltiEvents, KEYS, str) {

    var SELECTORS = {
        DELETE_BUTTON: '.delete',
        CAPABILITIES_CONTAINER: '.capabilities-container',
        ACTIVATE_BUTTON: '.tool-card-footer a.activate',
    };

    // Timeout in seconds.
    var ANNOUNCEMENT_TIMEOUT = 2000;

    /**
     * Return the delete button element.
     *
     * @method getDeleteButton
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     * @return {JQuery} jQuery object
     */
    var getDeleteButton = function(element) {
        return element.find(SELECTORS.DELETE_BUTTON);
    };

    /**
     * Return the activate button for the type.
     *
     * @method getActivateButton
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     * @return {JQuery}  jQuery object
     */
    var getActivateButton = function(element) {
        return element.find(SELECTORS.ACTIVATE_BUTTON);
    };

    /**
     * Get the type id.
     *
     * @method getTypeId
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     * @return {String} Type ID
     */
    var getTypeId = function(element) {
        return element.attr('data-proxy-id');
    };

    /**
     * Stop any announcement currently visible on the card.
     *
     * @method clearAllAnnouncements
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     */
    var clearAllAnnouncements = function(element) {
        element.removeClass('announcement loading success fail capabilities');
    };

    /**
     * Show the loading announcement.
     *
     * @method startLoading
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     */
    var startLoading = function(element) {
        clearAllAnnouncements(element);
        element.addClass('announcement loading');
    };

    /**
     * Hide the loading announcement.
     *
     * @method stopLoading
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     */
    var stopLoading = function(element) {
        element.removeClass('announcement loading');
    };

    /**
     * Show the success announcement. The announcement is only
     * visible for 2 seconds.
     *
     * @method announceSuccess
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     * @return {Promise} jQuery Deferred object
     */
    var announceSuccess = function(element) {
        var promise = $.Deferred();

        clearAllAnnouncements(element);
        element.addClass('announcement success');
        setTimeout(function() {
            element.removeClass('announcement success');
            promise.resolve();
        }, ANNOUNCEMENT_TIMEOUT);

        return promise;
    };

    /**
     * Show the failure announcement. The announcement is only
     * visible for 2 seconds.
     *
     * @method announceFailure
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     * @return {Promise} jQuery Deferred object
     */
    var announceFailure = function(element) {
        var promise = $.Deferred();

        clearAllAnnouncements(element);
        element.addClass('announcement fail');
        setTimeout(function() {
                element.removeClass('announcement fail');
                promise.resolve();
            }, ANNOUNCEMENT_TIMEOUT);

        return promise;
    };

    /**
     * Delete the tool type from the Moodle server. Triggers a success
     * or failure announcement depending on the result.
     *
     * @method deleteType
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     * @return {Promise} jQuery Deferred object
     */
    var deleteType = function(element) {
        var promise = $.Deferred();
        var typeId = getTypeId(element);
        startLoading(element);

        if (typeId === "") {
            return $.Deferred().resolve();
        }

        str.get_strings([
                {
                    key: 'delete',
                    component: 'mod_lti'
                },
                {
                    key: 'delete_confirmation',
                    component: 'mod_lti'
                },
                {
                    key: 'delete',
                    component: 'mod_lti'
                },
                {
                    key: 'cancel',
                    component: 'core'
                },
            ])
            .done(function(strs) {
                    notification.confirm(strs[0], strs[1], strs[2], strs[3], function() {
                            toolProxy.delete(typeId)
                                .done(function() {
                                        stopLoading(element);
                                        announceSuccess(element)
                                            .done(function() {
                                                    element.remove();
                                                    promise.resolve();
                                                })
                                            .fail(notification.exception);
                                    })
                                .fail(function(error) {
                                        announceFailure(element);
                                        promise.reject(error);
                                    });
                    }, function() {
                            stopLoading(element);
                            promise.resolve();
                        });
                })
            .fail(function(error) {
                    stopLoading(element);
                    notification.exception(error);
                    promise.reject(error);
                });

        return promise;
    };

    /**
     * The user wishes to activate this tool so show them the capabilities that
     * they need to agree to or if there are none then set the tool type's state
     * to active.
     *
     * @method activateToolType
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     */
    var activateToolType = function(element) {
        var data = {proxyid: getTypeId(element)};
        $(document).trigger(ltiEvents.START_EXTERNAL_REGISTRATION, data);
    };

    /**
     * Sets up the listeners for user interaction on this tool type card.
     *
     * @method registerEventListeners
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     */
    var registerEventListeners = function(element) {
        var deleteButton = getDeleteButton(element);
        deleteButton.click(function(e) {
                e.preventDefault();
                deleteType(element);
            });
        deleteButton.keypress(function(e) {
                if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                    if (e.keyCode == KEYS.ENTER || e.keyCode == KEYS.SPACE) {
                        e.preventDefault();
                        deleteButton.click();
                    }
                }
            });

        var activateButton = getActivateButton(element);
        activateButton.click(function(e) {
                e.preventDefault();
                activateToolType(element);
            });
        activateButton.keypress(function(e) {
                if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                    if (e.keyCode == KEYS.ENTER || e.keyCode == KEYS.SPACE) {
                        e.preventDefault();
                        activateButton.click();
                    }
                }
            });
    };

    return /** @alias module:mod_lti/tool_card_controller */ {

        /**
         * Initialise this module.
         *
         * @param {JQuery} element jQuery object representing the tool card.
         */
        init: function(element) {
            registerEventListeners(element);
        }
    };
});
