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
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery', 'core/ajax', 'core/paged_content_factory', 'core/notification', 'core/templates', 'mod_lti/events',
        'mod_lti/keys', 'mod_lti/tool_types_and_proxies', 'mod_lti/tool_type', 'mod_lti/tool_proxy', 'core/str', 'core/config'],
        function($, ajax,
                 pagedContentFactory, notification, templates, ltiEvents, KEYS,
                 toolTypesAndProxies, toolType, toolProxy, str, config) {

    var SELECTORS = {
        EXTERNAL_REGISTRATION_CONTAINER: '#external-registration-container',
        EXTERNAL_REGISTRATION_PAGE_CONTAINER: '#external-registration-page-container',
        EXTERNAL_REGISTRATION_TEMPLATE_CONTAINER: '#external-registration-template-container',
        CARTRIDGE_REGISTRATION_CONTAINER: '#cartridge-registration-container',
        CARTRIDGE_REGISTRATION_FORM: '#cartridge-registration-form',
        ADD_TOOL_FORM: '#add-tool-form',
        TOOL_CARD_CONTAINER: '#tool-card-container',
        TOOL_LIST_CONTAINER: '#tool-list-container',
        TOOL_CREATE_BUTTON: '#tool-create-button',
        TOOL_CREATE_LTILEGACY_BUTTON: '#tool-createltilegacy-button',
        REGISTRATION_CHOICE_CONTAINER: '#registration-choice-container',
        TOOL_URL: '#tool-url'
    };

    /**
     * Get the tool list container element.
     *
     * @method getToolListContainer
     * @private
     * @return {Object} jQuery object
     */
    var getToolListContainer = function() {
        return $(SELECTORS.TOOL_LIST_CONTAINER);
    };

    /**
     * Get the tool card container element.
     *
     * @method getToolCardContainer
     * @private
     * @return {Object} jQuery object
     */
    const getToolCardContainer = function() {
        return $(SELECTORS.TOOL_CARD_CONTAINER);
    };

    /**
     * Get the external registration container element.
     *
     * @method getExternalRegistrationContainer
     * @private
     * @return {Object} jQuery object
     */
    var getExternalRegistrationContainer = function() {
        return $(SELECTORS.EXTERNAL_REGISTRATION_CONTAINER);
    };

    /**
     * Get the cartridge registration container element.
     *
     * @method getCartridgeRegistrationContainer
     * @private
     * @return {Object} jQuery object
     */
    var getCartridgeRegistrationContainer = function() {
        return $(SELECTORS.CARTRIDGE_REGISTRATION_CONTAINER);
    };

    /**
     * Get the registration choice container element.
     *
     * @method getRegistrationChoiceContainer
     * @private
     * @return {Object} jQuery object
     */
    var getRegistrationChoiceContainer = function() {
        return $(SELECTORS.REGISTRATION_CHOICE_CONTAINER);
    };

    /**
     * Close the LTI Advantage Registration IFrame.
     *
     * @private
     * @param {Object} e post message event sent from the registration frame.
     */
    var closeLTIAdvRegistration = function(e) {
        if (e.data && 'org.imsglobal.lti.close' === e.data.subject) {
            $(SELECTORS.EXTERNAL_REGISTRATION_TEMPLATE_CONTAINER).empty();
            hideExternalRegistration();
            showRegistrationChoices();
            showToolList();
            showRegistrationChoices();
            reloadToolList();
        }
    };

    /**
     * Load the external registration template and render it in the DOM and display it.
     *
     * @method initiateRegistration
     * @private
     * @param {String} url where to send the registration request
     */
    var initiateRegistration = function(url) {
        // Show the external registration page in an iframe.
        $(SELECTORS.EXTERNAL_REGISTRATION_PAGE_CONTAINER).removeClass('hidden');
        var container = $(SELECTORS.EXTERNAL_REGISTRATION_TEMPLATE_CONTAINER);
        container.append($("<iframe src='startltiadvregistration.php?url="
                         + encodeURIComponent(url) + "&sesskey=" + config.sesskey + "'></iframe>"));
        showExternalRegistration();
        window.addEventListener("message", closeLTIAdvRegistration, false);
    };

    /**
     * Get the tool type URL.
     *
     * @method getToolURL
     * @private
     * @return {String} the tool type url
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
     * @param {String} url
     * @private
     */
    var showCartridgeRegistration = function(url) {
        hideExternalRegistration();
        hideRegistrationChoices();
        // Don't save the key and secret from the last tool.
        var container = getCartridgeRegistrationContainer();
        container.find('input').val('');
        container.removeClass('hidden');
        container.find(SELECTORS.CARTRIDGE_REGISTRATION_FORM).attr('data-cartridge-url', url);
        screenReaderAnnounce(container);
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
     * JAWS does not notice visibility changes with aria-live.
     * Remove and add the content back to force it to read it out.
     * This function can be removed once JAWS supports visibility.
     *
     * @method screenReaderAnnounce
     * @param {Object} element
     * @private
     */
    var screenReaderAnnounce = function(element) {
        var children = element.children().detach();
        children.appendTo(element);
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
     * @param {Object} data
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
     * @param {Object} element jQuery object
     */
    var startLoading = function(element) {
        element.addClass("loading");
    };

    /**
     * Hide the loading animation
     *
     * @method stopLoading
     * @private
     * @param {Object} element jQuery object
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
        // Behat tests should wait for the tool list to load.
        M.util.js_pending('reloadToolList');

        const cardContainer = getToolCardContainer();
        const listContainer = getToolListContainer();
        const limit = 60;
        // Get initial data with zero limit and offset.
        fetchToolCount().done(function(data) {
            pagedContentFactory.createWithTotalAndLimit(
                data.count,
                limit,
                function(pagesData) {
                    return pagesData.map(function(pageData) {
                        return fetchToolData(pageData.limit, pageData.offset)
                            .then(function(data) {
                                return renderToolData(data);
                            });
                    });
                },
                {
                    'showFirstLast': true
                })
                .done(function(html, js) {
                // Add the paged content into the page.
                templates.replaceNodeContents(cardContainer, html, js);
                })
                .always(function() {
                    stopLoading(listContainer);
                    M.util.js_complete('reloadToolList');
                });
        });
        startLoading(listContainer);
    };

    /**
     * Fetch the count of tool type and proxy datasets.
     *
     * @return {*|void}
     */
    const fetchToolCount = function() {
        return toolTypesAndProxies.count({'orphanedonly': true})
            .done(function(data) {
                return data;
            }).catch(function(error) {
                // Add debug message, then return empty data.
                notification.exception(error);
                return {
                    'count': 0
                };
            });
    };

    /**
     * Fetch the data for tool type and proxy cards.
     *
     * @param {number} limit Maximum number of datasets to get.
     * @param {number} offset Offset count for fetching the data.
     * @return {*|void}
     */
    const fetchToolData = function(limit, offset) {
        const args = {'orphanedonly': true};
        // Only add limit and offset to args if they are integers and not null, otherwise defaults will be used.
        if (limit !== null && !Number.isNaN(limit)) {
            args.limit = limit;
        }
        if (offset !== null && !Number.isNaN(offset)) {
            args.offset = offset;
        }
        return toolTypesAndProxies.query(args)
            .done(function(data) {
                return data;
            }).catch(function(error) {
                // Add debug message, then return empty data.
                notification.exception(error);
                return {
                    'types': [],
                    'proxies': [],
                    'limit': limit,
                    'offset': offset
                };
        });
    };

    /**
     * Render Tool and Proxy cards from data.
     *
     * @param {Object} data Contains arrays of data objects to populate cards.
     * @return {*}
     */
    const renderToolData = function(data) {
        const context = {
            tools: data.types,
            proxies: data.proxies,
        };
        return templates.render('mod_lti/tool_list', context)
            .done(function(html, js) {
                    return {html, js};
                }
            );
    };

    /**
     * Start the LTI Advantage registration.
     *
     * @method addLTIAdvTool
     * @private
     */
    var addLTIAdvTool = function() {
        var url = getToolURL().trim();

        if (url) {
            $(SELECTORS.TOOL_URL).val('');
            hideToolList();
            initiateRegistration(url);
        }

    };

    /**
     * Trigger appropriate registration process process for the user input
     * URL. It can either be a cartridge or a registration url.
     *
     * @method addLTILegacyTool
     * @private
     * @return {Promise} jQuery Deferred object
     */
    var addLTILegacyTool = function() {
        var url = getToolURL().trim();

        if (url === "") {
            return $.Deferred().resolve();
        }
        var toolButton = $(SELECTORS.TOOL_CREATE_LTILEGACY_BUTTON);
        startLoading(toolButton);

        var promise = toolType.isCartridge(url);

        promise.always(function() {
          stopLoading(toolButton);
        });

        promise.done(function(result) {
            if (result.iscartridge) {
                $(SELECTORS.TOOL_URL).val('');
                $(document).trigger(ltiEvents.START_CARTRIDGE_REGISTRATION, url);
            } else {
                $(document).trigger(ltiEvents.START_EXTERNAL_REGISTRATION, {url: url});
            }
        });

        promise.fail(function() {
            str.get_string('errorbadurl', 'mod_lti')
                .done(function(s) {
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

        var addLegacyButton = $(SELECTORS.TOOL_CREATE_LTILEGACY_BUTTON);
        addLegacyButton.click(function(e) {
            e.preventDefault();
            addLTILegacyTool();
        });

        var addLTIButton = $(SELECTORS.TOOL_CREATE_BUTTON);
        addLTIButton.click(function(e) {
            e.preventDefault();
            addLTIAdvTool();
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
