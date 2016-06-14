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
 * Standard Ajax wrapper for Moodle. It calls the central Ajax script,
 * which can call any existing webservice using the current session.
 * In addition, it can batch multiple requests and return multiple responses.
 *
 * @module     mod_lti/tool_configure_controller
 * @class      tool_configure_controller
 * @package    mod_lti
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery', 'core/ajax', 'core/notification', 'core/templates', 'mod_lti/events', 'mod_lti/keys', 'mod_lti/tool_type',
        'mod_lti/tool_proxy', 'core/str'],
        function($, ajax, notification, templates, ltiEvents, KEYS, toolType, toolProxy, str) {

    var SELECTORS = {
        EXTERNAL_REGISTRATION_CONTAINER: '#external-registration-container',
        EXTERNAL_REGISTRATION_PAGE_CONTAINER: '#external-registration-page-container',
        CARTRIDGE_REGISTRATION_CONTAINER: '#cartridge-registration-container',
        CARTRIDGE_REGISTRATION_FORM: '#cartridge-registration-form',
        ADD_TOOL_FORM: '#add-tool-form',
        TOOL_LIST_CONTAINER: '#tool-list-container',
        TOOL_CREATE_BUTTON: '#tool-create-button',
        REGISTRATION_CHOICE_CONTAINER: '#registration-choice-container',
        TOOL_URL: '#tool-url'
    };

    /**
     * Get the tool create button element.
     *
     * @method getToolCreateButton
     * @private
     * @return object jQuery object
     */
    var getToolCreateButton = function() {
        return $(SELECTORS.TOOL_CREATE_BUTTON);
    };

    /**
     * Get the tool list container element.
     *
     * @method getToolListContainer
     * @private
     * @return object jQuery object
     */
    var getToolListContainer = function() {
        return $(SELECTORS.TOOL_LIST_CONTAINER);
    };

    /**
     * Get the external registration container element.
     *
     * @method getExternalRegistrationContainer
     * @private
     * @return object jQuery object
     */
    var getExternalRegistrationContainer = function() {
        return $(SELECTORS.EXTERNAL_REGISTRATION_CONTAINER);
    };

    /**
     * Get the cartridge registration container element.
     *
     * @method getCartridgeRegistrationContainer
     * @private
     * @return object jQuery object
     */
    var getCartridgeRegistrationContainer = function() {
        return $(SELECTORS.CARTRIDGE_REGISTRATION_CONTAINER);
    };

    /**
     * Get the registration choice container element.
     *
     * @method getRegistrationChoiceContainer
     * @private
     * @return object jQuery object
     */
    var getRegistrationChoiceContainer = function() {
        return $(SELECTORS.REGISTRATION_CHOICE_CONTAINER);
    };

    /**
     * Get the tool type URL.
     *
     * @method getToolURL
     * @private
     * @return string
     */
    var getToolURL = function() {
        return $(SELECTORS.TOOL_URL).val();
    };

    /**
     * Hide the external registration container.
     *
     * @method hideExternalRegistration
     * @private
     */
    var hideExternalRegistration = function() {
        getExternalRegistrationContainer().addClass('hidden');
    };

    /**
     * Hide the cartridge registration container.
     *
     * @method hideCartridgeRegistration
     * @private
     */
    var hideCartridgeRegistration = function() {
        getCartridgeRegistrationContainer().addClass('hidden');
    };

    /**
     * Hide the registration choice container.
     *
     * @method hideRegistrationChoices
     * @private
     */
    var hideRegistrationChoices = function() {
        getRegistrationChoiceContainer().addClass('hidden');
    };

    /**
     * Display the external registration panel and hides the other
     * panels.
     *
     * @method showExternalRegistration
     * @private
     */
    var showExternalRegistration = function() {
        hideCartridgeRegistration();
        hideRegistrationChoices();
        getExternalRegistrationContainer().removeClass('hidden');
        screenReaderAnnounce(getExternalRegistrationContainer());
    };

    /**
     * Display the cartridge registration panel and hides the other
     * panels.
     *
     * @method showCartridgeRegistration
     * @private
     */
    var showCartridgeRegistration = function(url) {
        hideExternalRegistration();
        hideRegistrationChoices();
        getCartridgeRegistrationContainer().removeClass('hidden');
        getCartridgeRegistrationContainer().find(SELECTORS.CARTRIDGE_REGISTRATION_FORM).attr('data-cartridge-url', url);
        screenReaderAnnounce(getCartridgeRegistrationContainer());
    };

    /**
     * JAWS does not notice visibility changes with aria-live.
     * Remove and add the content back to force it to read it out.
     * This function can be removed once JAWS supports visibility.
     *
     * @method screenReaderAnnounce
     * @private
     */
    var screenReaderAnnounce = function(element) {
        var childClones = element.children().clone(true, true);
        element.empty();
        element.append(childClones);
    };

    /**
     * Display the registration choices panel and hides the other
     * panels.
     *
     * @method showRegistrationChoices
     * @private
     */
    var showRegistrationChoices = function() {
        hideExternalRegistration();
        hideCartridgeRegistration();
        getRegistrationChoiceContainer().removeClass('hidden');
        screenReaderAnnounce(getRegistrationChoiceContainer());
    };

    /**
     * Hides the list of tool types.
     *
     * @method hideToolList
     * @private
     */
    var hideToolList = function() {
        getToolListContainer().addClass('hidden');
    };

    /**
     * Display the list of tool types.
     *
     * @method hideToolList
     * @private
     */
    var showToolList = function() {
        getToolListContainer().removeClass('hidden');
    };

    /**
     * Display the registration feedback alert and hide the other panels.
     *
     * @method showRegistrationFeedback
     * @private
     */
    var showRegistrationFeedback = function(data) {
        var type = data.error ? 'error' : 'success';
        notification.addNotification({
            message: data.message,
            type: type
        });
    };

    /**
     * Show the loading animation
     *
     * @method startLoading
     * @private
     * @param object jQuery object
     */
    var startLoading = function(element) {
        element.addClass("loading");
    };

    /**
     * Hide the loading animation
     *
     * @method stopLoading
     * @private
     * @param object jQuery object
     */
    var stopLoading = function(element) {
        element.removeClass("loading");
    };

    /**
     * Refresh the list of tool types and render the new ones.
     *
     * @method reloadToolList
     * @private
     */
    var reloadToolList = function() {
        var promise = $.Deferred();
        var container = getToolListContainer();
        startLoading(container);

        $.when(
                toolType.query(),
                toolProxy.query({'orphanedonly': true})
            )
            .done(function(types, proxies) {
                    templates.render('mod_lti/tool_list', {tools: types, proxies: proxies})
                        .done(function(html, js) {
                                container.empty();
                                container.append(html);
                                templates.runTemplateJS(js);
                                promise.resolve();
                            }).fail(promise.reject);
                })
            .fail(promise.reject);

        promise.fail(notification.exception)
            .always(function () {
                    stopLoading(container);
                });
    };

    /**
     * Trigger appropriate registration process process for the user input
     * URL. It can either be a cartridge or a registration url.
     *
     * @method addTool
     * @private
     * @return object jQuery deferred object
     */
    var addTool = function() {
        var url = $.trim(getToolURL());

        if (url === "") {
            return $.Deferred().resolve();
        }

        var toolButton = getToolCreateButton();
        startLoading(toolButton);

        var promise = toolType.isCartridge(url);

        promise.always(function() { stopLoading(toolButton); });

        promise.done(function(result) {
            if (result.iscartridge) {
                $(SELECTORS.TOOL_URL).val('');
                $(document).trigger(ltiEvents.START_CARTRIDGE_REGISTRATION, url);
            } else {
                $(document).trigger(ltiEvents.START_EXTERNAL_REGISTRATION, {url: url});
            }
        });

        promise.fail(function () {
            str.get_string('errorbadurl', 'mod_lti')
                .done(function (s) {
                        $(document).trigger(ltiEvents.REGISTRATION_FEEDBACK, {
                                message: s,
                                error: true
                            });
                    })
                .fail(notification.exception);
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

        // These are events fired by the registration processes. Either
        // the cartridge registration or the external registration url.
        $(document).on(ltiEvents.NEW_TOOL_TYPE, function() {
            reloadToolList();
        });

        $(document).on(ltiEvents.START_EXTERNAL_REGISTRATION, function() {
            showExternalRegistration();
            $(SELECTORS.TOOL_URL).val('');
            hideToolList();
        });

        $(document).on(ltiEvents.STOP_EXTERNAL_REGISTRATION, function() {
            showToolList();
            showRegistrationChoices();
        });

        $(document).on(ltiEvents.START_CARTRIDGE_REGISTRATION, function(event, url) {
            showCartridgeRegistration(url);
        });

        $(document).on(ltiEvents.STOP_CARTRIDGE_REGISTRATION, function() {
            getCartridgeRegistrationContainer().find(SELECTORS.CARTRIDGE_REGISTRATION_FORM).removeAttr('data-cartridge-url');
            showRegistrationChoices();
        });

        $(document).on(ltiEvents.REGISTRATION_FEEDBACK, function(event, data) {
            showRegistrationFeedback(data);
        });

        var form = $(SELECTORS.ADD_TOOL_FORM);
        form.submit(function(e) {
            e.preventDefault();
            addTool();
        });

    };

    return /** @alias module:mod_lti/cartridge_registration_form */ {

        /**
         * Initialise this module.
         */
        init: function() {
            registerEventListeners();
            reloadToolList();
        }
    };
});
