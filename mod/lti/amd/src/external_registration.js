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
 * Encapsules the behavior for creating a tool type and tool proxy from a
 * registration url in Moodle.
 *
 * Manages the UI while operations are occuring, including rendering external
 * registration page within the iframe.
 *
 * See template: mod_lti/external_registration
 *
 * @module     mod_lti/external_registration
 * @class      external_registration
 * @package    mod_lti
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery', 'core/ajax', 'core/notification', 'core/templates', 'mod_lti/events',
        'mod_lti/tool_proxy', 'mod_lti/tool_type', 'mod_lti/keys', 'core/str'],
        function($, ajax, notification, templates, ltiEvents, toolProxy, toolType, KEYS, str) {

    var SELECTORS = {
        EXTERNAL_REGISTRATION_CONTAINER: '#external-registration-page-container',
        EXTERNAL_REGISTRATION_TEMPLATE_CONTAINER: '#external-registration-template-container',
        EXTERNAL_REGISTRATION_CANCEL_BUTTON: '#cancel-external-registration',
        TOOL_TYPE_CAPABILITIES_CONTAINER: '#tool-type-capabilities-container',
        TOOL_TYPE_CAPABILITIES_TEMPLATE_CONTAINER: '#tool-type-capabilities-template-container',
        CAPABILITIES_AGREE_CONTAINER: '.capabilities-container',
    };

    /**
     * Return the external registration cancel button element. This button is
     * the cancel button that appears while the iframe is rendered.
     *
     * @method getExternalRegistrationCancelButton
     * @private
     * @return object jQuery object
     */
    var getExternalRegistrationCancelButton = function() {
        return $(SELECTORS.EXTERNAL_REGISTRATION_CANCEL_BUTTON);
    };

    /**
     * Return the container that holds all elements for the external registration, including
     * the cancel button and the iframe.
     *
     * @method getExternalRegistrationContainer
     * @private
     * @return object jQuery object
     */
    var getExternalRegistrationContainer = function() {
        return $(SELECTORS.EXTERNAL_REGISTRATION_CONTAINER);
    };

    /**
     * Return the container that holds the external registration page template. It should
     * be the iframe.
     *
     * @method getExternalRegistrationTemplateContainer
     * @private
     * @return object jQuery object
     */
    var getExternalRegistrationTemplateContainer = function() {
        return $(SELECTORS.EXTERNAL_REGISTRATION_TEMPLATE_CONTAINER);
    };

    /**
     * Return the container that holds the elements for displaying the list of capabilities
     * that this tool type requires. This container wraps the loading indicator and the template
     * container.
     *
     * @method getToolTypeCapabilitiesContainer
     * @private
     * @return object jQuery object
     */
    var getToolTypeCapabilitiesContainer = function() {
        return $(SELECTORS.TOOL_TYPE_CAPABILITIES_CONTAINER);
    };

    /**
     * Return the container that holds the template that lists the capabilities that the
     * tool type will require.
     *
     * @method getToolTypeCapabilitiesTemplateContainer
     * @private
     * @return object jQuery object
     */
    var getToolTypeCapabilitiesTemplateContainer = function() {
        return $(SELECTORS.TOOL_TYPE_CAPABILITIES_TEMPLATE_CONTAINER);
    };

    /**
     * Triggers a visual indicator to show that the capabilities section is loading.
     *
     * @method startLoadingCapabilitiesContainer
     * @private
     */
    var startLoadingCapabilitiesContainer = function() {
        getToolTypeCapabilitiesContainer().addClass('loading');
    };

    /**
     * Removes the visual indicator that shows the capabilities section is loading.
     *
     * @method stopLoadingCapabilitiesContainer
     * @private
     */
    var stopLoadingCapabilitiesContainer = function() {
        getToolTypeCapabilitiesContainer().removeClass('loading');
    };

    /**
     * Adds a visual indicator that shows the cancel button is loading.
     *
     * @method startLoadingCancel
     * @private
     */
    var startLoadingCancel = function() {
        getExternalRegistrationCancelButton().addClass('loading');
    };

    /**
     * Adds a visual indicator that shows the cancel button is loading.
     *
     * @method startLoadingCancel
     * @private
     */
    var stopLoadingCancel = function() {
        getExternalRegistrationCancelButton().removeClass('loading');
    };

    /**
     * Stops displaying the tool type capabilities container.
     *
     * @method hideToolTypeCapabilitiesContainer
     * @private
     */
    var hideToolTypeCapabilitiesContainer = function() {
        getToolTypeCapabilitiesContainer().addClass('hidden');
    };

    /**
     * Displays the tool type capabilities container.
     *
     * @method showToolTypeCapabilitiesContainer
     * @private
     */
    var showToolTypeCapabilitiesContainer = function() {
        getToolTypeCapabilitiesContainer().removeClass('hidden');
    };

    /**
     * Stops displaying the external registration content.
     *
     * @method hideExternalRegistrationContent
     * @private
     */
    var hideExternalRegistrationContent = function() {
        getExternalRegistrationContainer().addClass('hidden');
    };

    /**
     * Displays the external registration content.
     *
     * @method showExternalRegistrationContent
     * @private
     */
    var showExternalRegistrationContent = function() {
        getExternalRegistrationContainer().removeClass('hidden');
    };

    /**
     * Save the given tool proxy id on the DOM.
     *
     * @method setToolProxyId
     * @private
     * @param int Tool proxy ID
     */
    var setToolProxyId = function(id) {
        var button = getExternalRegistrationCancelButton();
        button.attr('data-tool-proxy-id', id);
    };

    /**
     * Return the saved tool proxy id.
     *
     * @method getToolProxyId
     * @private
     * @return string Tool proxy ID
     */
    var getToolProxyId = function() {
        var button = getExternalRegistrationCancelButton();
        return button.attr('data-tool-proxy-id');
    };

    /**
     * Remove the saved tool proxy id.
     *
     * @method clearToolProxyId
     * @private
     */
    var clearToolProxyId = function() {
        var button = getExternalRegistrationCancelButton();
        button.removeAttr('data-tool-proxy-id');
    };

    /**
     * Returns true if a tool proxy id has been recorded.
     *
     * @method hasToolProxyId
     * @private
     * @return bool
     */
    var hasToolProxyId = function() {
        return getToolProxyId() ? true : false;
    };

    /**
     * Checks if this process has created a tool proxy within
     * Moodle yet.
     *
     * @method hasCreatedToolProxy
     * @private
     * @return bool
     */
    var hasCreatedToolProxy = function() {
        var button = getExternalRegistrationCancelButton();
        return button.attr('data-tool-proxy-new') && hasToolProxyId();
    };

    /**
     * Records that this process has created a tool proxy.
     *
     * @method setProxyAsNew
     * @private
     * @return bool
     */
    var setProxyAsNew = function() {
        var button = getExternalRegistrationCancelButton();
        return button.attr('data-tool-proxy-new', "new");
    };

    /**
     * Records that this process has not created a tool proxy.
     *
     * @method setProxyAsOld
     * @private
     * @return bool
     */
    var setProxyAsOld = function() {
        var button = getExternalRegistrationCancelButton();
        return button.removeAttr('data-tool-proxy-new');
    };

    /**
     * Gets the external registration request required to be sent to the external
     * registration page using a form.
     *
     * See mod_lti/tool_proxy_registration_form template.
     *
     * @method getRegistrationRequest
     * @private
     * @param int Tool Proxy ID
     * @return object jQuery Deferred object
     */
    var getRegistrationRequest = function(id) {
        var request = {
            methodname: 'mod_lti_get_tool_proxy_registration_request',
            args: {
                id: id
            }
        };

        return ajax.call([request])[0];
    };

    /**
     * Cancel an in progress external registration. This will perform any necessary
     * clean up of tool proxies and return the page section back to the home section.
     *
     * @method cancelRegistration
     * @private
     * @return object jQuery Deferred object
     */
    var cancelRegistration = function() {
        startLoadingCancel();
        var promise = $.Deferred();

        // If we've created a proxy as part of this process then
        // we need to delete it to clean up the data in the back end.
        if (hasCreatedToolProxy()) {
            var id = getToolProxyId();
            toolProxy.delete(id).done(function() {
                promise.resolve();
            }).fail(function (failure) {
                promise.reject(failure);
            });
        } else {
            promise.resolve();
        }

        promise.done(function() {
            // Return to the original page.
            finishExternalRegistration();
            stopLoadingCancel();
        }).fail(function (failure) {
            notification.exception(failure);
            finishExternalRegistration();
            stopLoadingCancel();
            str.get_strings([{key: 'error', component: 'moodle'},
                             {key: 'failedtodeletetoolproxy', component: 'mod_lti'}]).done(function (s) {
                var feedback = {
                    status: s[0],
                    message: s[1],
                    error: true
                };
                $(document).trigger(ltiEvents.REGISTRATION_FEEDBACK, feedback);
            }).fail(notification.exception);
        });

        return promise;
    };

    /**
     * Load the external registration template and render it in the DOM and display it.
     *
     * @method renderExternalRegistrationWindow
     * @private
     * @return object jQuery Deferred object
     */
    var renderExternalRegistrationWindow = function(registrationRequest) {
        var promise = templates.render('mod_lti/tool_proxy_registration_form', registrationRequest);

        promise.done(function(html, js) {
            // Show the external registration page in an iframe.
            var container = getExternalRegistrationTemplateContainer();
            container.append(html);
            templates.runTemplateJS(js);

            container.find('form').submit();
            showExternalRegistrationContent();
        }).fail(notification.exception);

        return promise;
    };

    /**
     * Send a request to Moodle server to set the state of the tool type to configured (active).
     *
     * @method setTypeStatusActive
     * @private
     * @param object A set of data representing a type, as returned by a request to get a type
     *               from the Moodle server.
     * @return object jQuery Deferred object
     */
    var setTypeStatusActive = function(typeData) {
        return toolType.update({
            id: typeData.id,
            state: toolType.constants.state.configured
        });
    };

    /**
     * Render and display an agreement page for the user to acknowledge the list of capabilities
     * (groups of data) that the external tool requires in order to work. If the user agrees then
     * we will activate the tool so that it is immediately available. If they don't agree then
     * the tool remains in a pending state within Moodle until agreement is given.
     *
     * @method promptForToolTypeCapabilitiesAgreement
     * @private
     * @param object A set of data representing a type, as returned by a request to get a type
     *               from the Moodle server.
     * @return object jQuery Deferred object
     */
    var promptForToolTypeCapabilitiesAgreement = function(typeData) {
        var promise = $.Deferred();

        templates.render('mod_lti/tool_type_capabilities_agree', typeData).done(function(html, js) {
            var container = getToolTypeCapabilitiesTemplateContainer();

            hideExternalRegistrationContent();
            showToolTypeCapabilitiesContainer();

            templates.replaceNodeContents(container, html, js);

            var choiceContainer = container.find(SELECTORS.CAPABILITIES_AGREE_CONTAINER);

            // The user agrees to allow the tool to use the groups of data so we can go
            // ahead and activate it for them so that it can be used straight away.
            choiceContainer.on(ltiEvents.CAPABILITIES_AGREE, function() {
                startLoadingCapabilitiesContainer();
                setTypeStatusActive(typeData).always(function() {
                    stopLoadingCapabilitiesContainer();
                    container.empty();
                    promise.resolve();
                });
            });

            // The user declines to let the tool use the data. In this case we leave
            // the tool as pending and they can delete it using the main screen if they
            // wish.
            choiceContainer.on(ltiEvents.CAPABILITIES_DECLINE, function() {
                container.empty();
                promise.resolve();
            });
        }).fail(promise.reject);

        promise.done(function() {
            hideToolTypeCapabilitiesContainer();
        }).fail(notification.exception);

        return promise;
    };

    /**
     * Send a request to the Moodle server to create a tool proxy using the registration URL the user
     * has provided. The proxy is required for the external registration page to work correctly.
     *
     * After the proxy is created the external registration page is rendered within an iframe for the user
     * to complete the registration in the external page.
     *
     * If the tool proxy creation fails then we redirect the page section back to the home section and
     * display the error, rather than rendering the external registration page.
     *
     * @method createAndRegisterToolProxy
     * @private
     * @param url Tool registration URL to register
     * @return object jQuery Deferred object
     */
    var createAndRegisterToolProxy = function(url) {
        var promise = $.Deferred();

        if (!url || url === "") {
            // No URL has been input so do nothing.
            promise.resolve();
        } else {
            // A tool proxy needs to exist before the external page is rendered because
            // the external page sends requests back to Moodle for information that is stored
            // in the proxy.
            toolProxy.create({regurl: url})
                .done(function(result) {
                        // Note that it's a new proxy so we will always clean it up.
                        setProxyAsNew();
                        promise = registerProxy(result.id);
                    })
                .fail(function(exception) {
                        // Clean up.
                        cancelRegistration();
                        // Let the user know what the error is.
                        str.get_string('error', 'moodle')
                            .done(function (s) {
                                    var feedback = {
                                        status: s,
                                        message: exception.message,
                                        error: true
                                    };
                                    $(document).trigger(ltiEvents.REGISTRATION_FEEDBACK, feedback);
                                })
                            .fail(notification.exception);
                        promise.reject(exception);
                    });
        }

        return promise;
    };

    /**
     * Loads the window to register a proxy, given an ID.
     *
     * @method registerProxy
     * @private
     * @param id Proxy id to register
     * @return jQuery Deferred object to fail or resolve
     */
    var registerProxy = function(id) {
        var promise = $.Deferred();
        // Save the id on the DOM to cleanup later.
        setToolProxyId(id);

        // There is a specific set of data needed to send to the external registration page
        // in a form, so let's get it from our server.
        getRegistrationRequest(id)
            .done(function(registrationRequest) {
                    renderExternalRegistrationWindow(registrationRequest)
                        .done(function() {
                                promise.resolve();
                            })
                        .fail(promise.fail);
                })
            .fail(promise.fail);

        return promise;
    };

    /**
     * Complete the registration process, clean up any left over data and
     * trigger the appropriate events.
     *
     * @method finishExternalRegistration
     * @private
     */
    var finishExternalRegistration = function() {
        if (hasToolProxyId()) {
            clearToolProxyId();
        }
        setProxyAsOld(false);

        hideExternalRegistrationContent();
        var container = getExternalRegistrationTemplateContainer();
        container.empty();

        $(document).trigger(ltiEvents.STOP_EXTERNAL_REGISTRATION);
    };

    /**
     * Sets up the listeners for user interaction on the page.
     *
     * @method registerEventListeners
     * @private
     */
    var registerEventListeners = function() {

        $(document).on(ltiEvents.START_EXTERNAL_REGISTRATION, function(event, data) {
                if (!data) {
                    return;
                }
                if (data.url) {
                    createAndRegisterToolProxy(data.url);
                }
                if (data.proxyid) {
                    registerProxy(data.proxyid);
                }
            });

        var cancelExternalRegistrationButton = getExternalRegistrationCancelButton();
        cancelExternalRegistrationButton.click(function(e) {
            e.preventDefault();
            cancelRegistration();
        });
        cancelExternalRegistrationButton.keypress(function(e) {
            if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                if (e.keyCode == KEYS.ENTER || e.keyCode == KEYS.SPACE) {
                    e.preventDefault();
                    cancelRegistration();
                }
            }
        });

        // This is gross but necessary due to isolated jQuery scopes between
        // child iframe and parent windows. There is no other way to communicate.
        //
        // This function gets called by the moodle page that received the redirect
        // from the external registration page and handles the external page's returned
        // parameters.
        //
        // See AMD module mod_lti/external_registration_return.
        window.triggerExternalRegistrationComplete = function(data) {
            var promise = $.Deferred();
            var feedback = {
                status: data.status,
                message: "",
                error: false
            };

            if (data.status == "success") {
                str.get_strings([{key: 'success', component: 'moodle'},
                                 {key: 'successfullycreatedtooltype', component: 'mod_lti'}]).done(function (s) {
                    feedback.status = s[0];
                    feedback.message = s[1];
                }).fail(notification.exception);

                // Trigger appropriate events when we've completed the necessary requests.
                promise.done(function() {
                    finishExternalRegistration();
                    $(document).trigger(ltiEvents.REGISTRATION_FEEDBACK, feedback);
                    $(document).trigger(ltiEvents.NEW_TOOL_TYPE);
                }).fail(notification.exception);

                // We should have created a tool proxy by this point.
                if (hasCreatedToolProxy()) {
                    var proxyId = getToolProxyId();

                    // We need the list of types that are linked to this proxy. We're assuming it'll
                    // only be one because this process creates a one-to-one type->proxy.
                    toolType.getFromToolProxyId(proxyId).done(function(types) {
                        if (types && types.length) {
                            // There should only be one result.
                            var typeData = types[0];

                            // Check if the external tool required access to any Moodle data (users, courses etc).
                            if (typeData.hascapabilitygroups) {
                                // If it did then we ask the user to agree to those groups before the type is
                                // activated (i.e. can be used in Moodle).
                                promptForToolTypeCapabilitiesAgreement(typeData).always(function() {
                                    promise.resolve();
                                });
                            } else {
                                promise.resolve();
                            }
                        } else {
                            promise.resolve();
                        }
                    }).fail(function() {
                        promise.resolve();
                    });
                }
            } else {
                // Anything other than success is failure.
                feedback.message = data.error;
                feedback.error = true;

                // Cancel registration to clean up any proxies and tools that were
                // created.
                promise.done(function() {
                    cancelRegistration().always(function() {
                        $(document).trigger(ltiEvents.REGISTRATION_FEEDBACK, feedback);
                    });
                }).fail(notification.exception);

                promise.resolve();
            }

            return promise;
        };
    };

    return /** @alias module:mod_lti/external_registration */ {

        /**
         * Initialise this module.
         */
        init: function() {
            registerEventListeners();
        }
    };
});
