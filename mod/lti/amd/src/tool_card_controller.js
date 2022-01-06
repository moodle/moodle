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
 * See template: mod_lti/tool_card
 *
 * @module     mod_lti/tool_card_controller
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
 define(['jquery', 'core/ajax', 'core/notification', 'core/templates', 'core/modal_factory',
        'mod_lti/tool_type', 'mod_lti/events', 'mod_lti/keys',
        'core/str'],
        function($, ajax, notification, templates, modalFactory, toolType, ltiEvents, KEYS, str) {

    var SELECTORS = {
        DELETE_BUTTON: '.delete',
        NAME_ELEMENT: '.name',
        DESCRIPTION_ELEMENT: '.description',
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
     * Return the element representing the tool type name.
     *
     * @method getNameElement
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     * @return {JQuery} jQuery object
     */
    var getNameElement = function(element) {
        return element.find(SELECTORS.NAME_ELEMENT);
    };

    /**
     * Return the element representing the tool type description.
     *
     * @method getDescriptionElement
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     * @return {JQuery} jQuery object
     */
    var getDescriptionElement = function(element) {
        return element.find(SELECTORS.DESCRIPTION_ELEMENT);
    };

    /**
     * Return the activate button for the type.
     *
     * @method getActivateButton
     * @private
     * @param {Object} element jQuery object representing the tool card.
     * @return {Object} jQuery object
     */
    var getActivateButton = function(element) {
        return element.find(SELECTORS.ACTIVATE_BUTTON);
    };

    /**
     * Checks if the type card has an activate button.
     *
     * @method hasActivateButton
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     * @return {Boolean} true if has active buton
     */
    var hasActivateButton = function(element) {
        return getActivateButton(element).length ? true : false;
    };

    /**
     * Return the element that contains the capabilities approval for
     * the user.
     *
     * @method getCapabilitiesContainer
     * @private
     * @param {Object} element jQuery object representing the tool card.
     * @return {Object} The element
     */
    var getCapabilitiesContainer = function(element) {
        return element.find(SELECTORS.CAPABILITIES_CONTAINER);
    };

    /**
     * Checks if the tool type has capabilities that need approval. If it
     * does then the container will be present.
     *
     * @method hasCapabilitiesContainer
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     * @return {Boolean} true if has capbilities.
     */
    var hasCapabilitiesContainer = function(element) {
        return getCapabilitiesContainer(element).length ? true : false;
    };

    /**
     * Get the type id.
     *
     * @method getTypeId
     * @private
     * @param {Object} element jQuery object representing the tool card.
     * @return {String} Type ID
     */
    var getTypeId = function(element) {
        return element.attr('data-type-id');
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
                            toolType.delete(typeId)
                                .done(function() {
                                        stopLoading(element);
                                        announceSuccess(element)
                                            .done(function() {
                                                    element.remove();
                                                })
                                            .fail(notification.exception)
                                            .always(function() {
                                                    // Always resolve because even if the announcement fails the type was deleted.
                                                    promise.resolve();
                                                });
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
     * Save a given value in a data attribute on the element.
     *
     * @method setValueSnapshot
     * @private
     * @param {JQuery} element jQuery object representing the element.
     * @param {String} value to be saved.
     */
    var setValueSnapshot = function(element, value) {
        element.attr('data-val-snapshot', value);
    };

    /**
     * Return the saved value from the element.
     *
     * @method getValueSnapshot
     * @private
     * @param {JQuery} element jQuery object representing the element.
     * @return {String} the saved value.
     */
    var getValueSnapshot = function(element) {
        return element.attr('data-val-snapshot');
    };

    /**
     * Save the current value of the tool description.
     *
     * @method snapshotDescription
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     */
    var snapshotDescription = function(element) {
        var descriptionElement = getDescriptionElement(element);

        if (descriptionElement.hasClass('loading')) {
            return;
        }

        var description = descriptionElement.text().trim();
        setValueSnapshot(descriptionElement, description);
    };

    /**
     * Send a request to update the description value for this tool
     * in the Moodle server.
     *
     * @method updateDescription
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     * @return {Promise} jQuery Deferred object
     */
    var updateDescription = function(element) {
        var typeId = getTypeId(element);

        // Return early if we don't have an id because it's
        // required to save the changes.
        if (typeId === "") {
            return $.Deferred().resolve();
        }

        var descriptionElement = getDescriptionElement(element);

        // Return early if we're already saving a value.
        if (descriptionElement.hasClass('loading')) {
            return $.Deferred().resolve();
        }

        var description = descriptionElement.text().trim();
        var snapshotVal = getValueSnapshot(descriptionElement);

        // If the value hasn't change then don't bother sending the
        // update request.
        if (snapshotVal == description) {
            return $.Deferred().resolve();
        }

        descriptionElement.addClass('loading');

        var promise = toolType.update({id: typeId, description: description});

        promise.done(function(type) {
            descriptionElement.removeClass('loading');
            // Make sure the text is updated with the description from the
            // server, just in case the update didn't work.
            descriptionElement.text(type.description);
        }).fail(notification.exception);

        // Probably need to handle failures better so that we can revert
        // the value in the input for the user.
        promise.fail(function() {
          descriptionElement.removeClass('loading');
        });

        return promise;
    };

    /**
     * Save the current value of the tool name.
     *
     * @method snapshotName
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     */
    var snapshotName = function(element) {
        var nameElement = getNameElement(element);

        if (nameElement.hasClass('loading')) {
            return;
        }

        var name = nameElement.text().trim();
        setValueSnapshot(nameElement, name);
    };

    /**
     * Send a request to update the name value for this tool
     * in the Moodle server.
     *
     * @method updateName
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     * @return {Promise} jQuery Deferred object
     */
    var updateName = function(element) {
        var typeId = getTypeId(element);

        // Return if we don't have an id.
        if (typeId === "") {
            return $.Deferred().resolve();
        }

        var nameElement = getNameElement(element);

        // Return if we're already saving.
        if (nameElement.hasClass('loading')) {
            return $.Deferred().resolve();
        }

        var name = nameElement.text().trim();
        var snapshotVal = getValueSnapshot(nameElement);

        // If the value hasn't change then don't bother sending the
        // update request.
        if (snapshotVal == name) {
            return $.Deferred().resolve();
        }

        nameElement.addClass('loading');
        var promise = toolType.update({id: typeId, name: name});

        promise.done(function(type) {
            nameElement.removeClass('loading');
            // Make sure the text is updated with the name from the
            // server, just in case the update didn't work.
            nameElement.text(type.name);
        });

        // Probably need to handle failures better so that we can revert
        // the value in the input for the user.
        promise.fail(function() {
          nameElement.removeClass('loading');
        });

        return promise;
    };

    /**
     * Send a request to update the state for this tool to be configured (active)
     * in the Moodle server. A success or failure announcement is triggered depending
     * on the result.
     *
     * @method setStatusActive
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     * @return {Promise} jQuery Deferred object
     */
    var setStatusActive = function(element) {
        var id = getTypeId(element);

        // Return if we don't have an id.
        if (id === "") {
            return $.Deferred().resolve();
        }

        startLoading(element);

        var promise = toolType.update({
            id: id,
            state: toolType.constants.state.configured
        });

        promise.then(function(toolTypeData) {
            stopLoading(element);
            announceSuccess(element);
            return toolTypeData;
        }).then(function(toolTypeData) {
            return templates.render('mod_lti/tool_card', toolTypeData);
        }).then(function(html, js) {
            templates.replaceNode(element, html, js);
            return;
        }).catch(function() {
            stopLoading(element);
            announceFailure(element);
        });

        return promise;
    };

    /**
     * Show the capabilities approval screen to show which groups of data this
     * type requires access to in Moodle (if any).
     *
     * @method displayCapabilitiesApproval
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     */
    var displayCapabilitiesApproval = function(element) {
        element.addClass('announcement capabilities');
    };

    /**
     * Hide the capabilities approval screen.
     *
     * @method hideCapabilitiesApproval
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     */
    var hideCapabilitiesApproval = function(element) {
        element.removeClass('announcement capabilities');
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
        if (hasCapabilitiesContainer(element)) {
            displayCapabilitiesApproval(element);
        } else {
            setStatusActive(element);
        }
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

        var descriptionElement = getDescriptionElement(element);
        descriptionElement.focus(function(e) {
            e.preventDefault();
            // Save a copy of the current value for the description so that
            // we can check if the user has changed it before sending a request to
            // the server.
            snapshotDescription(element);
        });
        descriptionElement.blur(function(e) {
            e.preventDefault();
            updateDescription(element);
        });
        descriptionElement.keypress(function(e) {
            if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                if (e.keyCode == KEYS.ENTER) {
                    e.preventDefault();
                    descriptionElement.blur();
                }
            }
        });

        var nameElement = getNameElement(element);
        nameElement.focus(function(e) {
            e.preventDefault();
            // Save a copy of the current value for the name so that
            // we can check if the user has changed it before sending a request to
            // the server.
            snapshotName(element);
        });
        nameElement.blur(function(e) {
            e.preventDefault();
            updateName(element);
        });
        nameElement.keypress(function(e) {
            if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                if (e.keyCode == KEYS.ENTER) {
                    e.preventDefault();
                    nameElement.blur();
                }
            }
        });

        // Only pending tool type cards have an activate button.
        if (hasActivateButton(element)) {
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
        }

        if (hasCapabilitiesContainer(element)) {
            var capabilitiesContainer = getCapabilitiesContainer(element);

            capabilitiesContainer.on(ltiEvents.CAPABILITIES_AGREE, function() {
                setStatusActive(element);
            });

            capabilitiesContainer.on(ltiEvents.CAPABILITIES_DECLINE, function() {
                hideCapabilitiesApproval(element);
            });
        }
    };

    /**
     * Sets up the templates for the tool configuration modal on this tool type card.
     *
     * @method registerModal
     * @private
     * @param {JQuery} element jQuery object representing the tool card.
     */
    var registerModal = function(element) {
        var trigger = $('#' + element.data('uniqid') + '-' + element.data('deploymentid'));
        var context = {
            'uniqid': element.data('uniqid'),
            'platformid': element.data('platformid'),
            'clientid': element.data('clientid'),
            'deploymentid': element.data('deploymentid'),
            'urls': {
                'publickeyset': element.data('publickeyseturl'),
                'accesstoken': element.data('accesstokenurl'),
                'authrequest': element.data('authrequesturl')
            }
        };
        var bodyPromise = templates.render('mod_lti/tool_config_modal_body', context);
        var mailTo = 'mailto:?subject=' + encodeURIComponent(element.data('mailtosubject')) +
            '&body=' + encodeURIComponent(element.data('platformidstr')) + ':%20' +
            encodeURIComponent(element.data('platformid')) + '%0D%0A' +
            encodeURIComponent(element.data('clientidstr')) + ':%20' +
            encodeURIComponent(element.data('clientid')) + '%0D%0A' +
            encodeURIComponent(element.data('deploymentidstr')) + ':%20' +
            encodeURIComponent(element.data('deploymentid')) + '%0D%0A' +
            encodeURIComponent(element.data('publickeyseturlstr')) + ':%20' +
            encodeURIComponent(element.data('publickeyseturl')) + '%0D%0A' +
            encodeURIComponent(element.data('accesstokenurlstr')) + ':%20' +
            encodeURIComponent(element.data('accesstokenurl')) + '%0D%0A' +
            encodeURIComponent(element.data('authrequesturlstr')) + ':%20' +
            encodeURIComponent(element.data('authrequesturl')) + '%0D%0A';
        context = {
            'mailto': mailTo
        };
        var footerPromise = templates.render('mod_lti/tool_config_modal_footer', context);
        modalFactory.create({
          large: true,
          title: element.data('modaltitle'),
          body: bodyPromise,
          footer: footerPromise,
        }, trigger);
    };

    return /** @alias module:mod_lti/tool_card_controller */ {

        /**
         * Initialise this module.
         *
         * @param {JQuery} element jQuery object representing the tool card.
         */
        init: function(element) {
            registerEventListeners(element);
            registerModal(element);
        }
    };
});
