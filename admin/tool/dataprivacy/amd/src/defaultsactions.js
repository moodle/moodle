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
 * AMD module for data registry defaults actions.
 *
 * @module     tool_dataprivacy/defaultsactions
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/ajax',
    'core/notification',
    'core/str',
    'core/modal_factory',
    'core/modal_events',
    'core/templates'],
function($, Ajax, Notification, Str, ModalFactory, ModalEvents, Templates) {

    /**
     * List of action selectors.
     *
     * @type {{EDIT_LEVEL_DEFAULTS: string}}
     * @type {{NEW_ACTIVITY_DEFAULTS: string}}
     * @type {{EDIT_ACTIVITY_DEFAULTS: string}}
     * @type {{DELETE_ACTIVITY_DEFAULTS: string}}
     */
    var ACTIONS = {
        EDIT_LEVEL_DEFAULTS: '[data-action="edit-level-defaults"]',
        NEW_ACTIVITY_DEFAULTS: '[data-action="new-activity-defaults"]',
        EDIT_ACTIVITY_DEFAULTS: '[data-action="edit-activity-defaults"]',
        DELETE_ACTIVITY_DEFAULTS: '[data-action="delete-activity-defaults"]'
    };

    /** @type {{INHERIT: Number}} **/
    var INHERIT = -1;

    /**
     * DefaultsActions class.
     */
    var DefaultsActions = function() {
        this.registerEvents();
    };

    /**
     * Register event listeners.
     */
    DefaultsActions.prototype.registerEvents = function() {
        $(ACTIONS.EDIT_LEVEL_DEFAULTS).click(function(e) {
            e.preventDefault();

            var button = $(this);
            var contextLevel = button.data('contextlevel');
            var category = button.data('category');
            var purpose = button.data('purpose');

            // Get options.
            var requests = [
                {methodname: 'tool_dataprivacy_get_category_options', args: {}},
                {methodname: 'tool_dataprivacy_get_purpose_options', args: {}}
            ];

            var promises = Ajax.call(requests);
            var titlePromise = Str.get_string('editdefaults', 'tool_dataprivacy', $('#defaults-header').text());
            $.when(promises[0], promises[1], titlePromise).then(function(categoryResponse, purposeResponse, title) {
                var categories = categoryResponse.options;
                var purposes = purposeResponse.options;
                showDefaultsFormModal(title, contextLevel, category, purpose, null, categories, purposes, null);

                return true;
            }).catch(Notification.exception);
        });

        $(ACTIONS.NEW_ACTIVITY_DEFAULTS).click(function(e) {
            e.preventDefault();

            var button = $(this);
            var contextLevel = button.data('contextlevel');

            // Get options.
            var requests = [
                {methodname: 'tool_dataprivacy_get_category_options', args: {}},
                {methodname: 'tool_dataprivacy_get_purpose_options', args: {}},
                {methodname: 'tool_dataprivacy_get_activity_options', args: {'nodefaults': true}}
            ];

            var promises = Ajax.call(requests);
            var titlePromise = Str.get_string('addnewdefaults', 'tool_dataprivacy');

            $.when(promises[0], promises[1], promises[2], titlePromise).then(
                function(categoryResponse, purposeResponse, activityResponse, title) {
                    var categories = categoryResponse.options;
                    var purposes = purposeResponse.options;
                    var activities = activityResponse.options;

                    showDefaultsFormModal(title, contextLevel, null, null, null, categories, purposes, activities);

                    return true;

                }).catch(Notification.exception);
            }
        );

        $(ACTIONS.EDIT_ACTIVITY_DEFAULTS).click(function(e) {
            e.preventDefault();

            var button = $(this);
            var contextLevel = button.data('contextlevel');
            var category = button.data('category');
            var purpose = button.data('purpose');
            var activity = button.data('activityname');

            // Get options.
            var requests = [
                {methodname: 'tool_dataprivacy_get_category_options', args: {}},
                {methodname: 'tool_dataprivacy_get_purpose_options', args: {}},
                {methodname: 'tool_dataprivacy_get_activity_options', args: {}}
            ];

            var promises = Ajax.call(requests);
            var titlePromise = Str.get_string('editmoduledefaults', 'tool_dataprivacy');

            $.when(promises[0], promises[1], promises[2], titlePromise).then(
                function(categoryResponse, purposeResponse, activityResponse, title) {
                    var categories = categoryResponse.options;
                    var purposes = purposeResponse.options;
                    var activities = activityResponse.options;

                    showDefaultsFormModal(title, contextLevel, category, purpose, activity, categories, purposes, activities);

                    return true;

                }).catch(Notification.exception);
            }
        );

        $(ACTIONS.DELETE_ACTIVITY_DEFAULTS).click(function(e) {
            e.preventDefault();

            var button = $(this);
            var contextLevel = button.data('contextlevel');
            var activity = button.data('activityname');
            var activityDisplayName = button.data('activitydisplayname');
            // Set category and purpose to inherit (-1).
            var category = INHERIT;
            var purpose = INHERIT;

            ModalFactory.create({
                title: Str.get_string('deletedefaults', 'tool_dataprivacy', activityDisplayName),
                body: Templates.render('tool_dataprivacy/delete_activity_defaults', {"activityname": activityDisplayName}),
                type: ModalFactory.types.SAVE_CANCEL,
                large: true
            }).then(function(modal) {
                modal.setSaveButtonText(Str.get_string('delete'));

                // Handle save event.
                modal.getRoot().on(ModalEvents.save, function() {
                    setContextDefaults(contextLevel, category, purpose, activity, false);
                });

                // Handle hidden event.
                modal.getRoot().on(ModalEvents.hidden, function() {
                    // Destroy when hidden.
                    modal.destroy();
                });

                modal.show();

                return true;
            }).catch(Notification.exception);
        });
    };

    /**
     * Prepares and renders the modal for setting the defaults for the given context level/plugin.
     *
     * @param {String} title The modal's title.
     * @param {Number} contextLevel The context level to set defaults for.
     * @param {Number} category The current category ID.
     * @param {Number} purpose The current purpose ID.
     * @param {String} activity The plugin name of the activity. Optional.
     * @param {Array} categoryOptions The list of category options.
     * @param {Array} purposeOptions The list of purpose options.
     * @param {Array} activityOptions The list of activity options. Optional.
     */
    function showDefaultsFormModal(title, contextLevel, category, purpose, activity,
                                   categoryOptions, purposeOptions, activityOptions) {

        if (category !== null) {
            categoryOptions.forEach(function(currentValue) {
                if (currentValue.id === category) {
                    currentValue.selected = true;
                }
            });
        }

        if (purpose !== null) {
            purposeOptions.forEach(function(currentValue) {
                if (currentValue.id === purpose) {
                    currentValue.selected = true;
                }
            });
        }

        var templateContext = {
            "contextlevel": contextLevel,
            "categoryoptions": categoryOptions,
            "purposeoptions": purposeOptions
        };

        // Check the activityOptions parameter that was passed.
        if (activityOptions !== null && activityOptions.length) {
            // Check the activity parameter that was passed.
            if (activity === null) {
                // We're setting a new defaults for a module.
                templateContext.newactivitydefaults = true;

            } else {
                // Edit mode. Set selection.
                activityOptions.forEach(function(currentValue) {
                    if (activity === currentValue.name) {
                        currentValue.selected = true;
                    }
                });
            }

            templateContext.modemodule = true;
            templateContext.activityoptions = activityOptions;
        }

        ModalFactory.create({
            title: title,
            body: Templates.render('tool_dataprivacy/category_purpose_form', templateContext),
            type: ModalFactory.types.SAVE_CANCEL,
            large: true
        }).then(function(modal) {

            // Handle save event.
            modal.getRoot().on(ModalEvents.save, function() {
                var activity = $('#activity');
                var activityVal = typeof activity !== 'undefined' ? activity.val() : null;
                var override = $('#override');
                var overrideVal = typeof override !== 'undefined' ? override.is(':checked') : false;

                setContextDefaults($('#contextlevel').val(), $('#category').val(), $('#purpose').val(), activityVal, overrideVal);
            });

            // Handle hidden event.
            modal.getRoot().on(ModalEvents.hidden, function() {
                // Destroy when hidden.
                modal.destroy();
            });

            modal.show();

            return modal;
        }).catch(Notification.exception);
    }

    /**
     * Calls a the tool_dataprivacy_set_context_defaults WS function.
     *
     * @param {Number} contextLevel The context level.
     * @param {Number} category The category ID.
     * @param {Number} purpose The purpose ID.
     * @param {String} activity The plugin name of the activity module.
     * @param {Boolean} override Whether to override custom instances.
     */
    function setContextDefaults(contextLevel, category, purpose, activity, override) {
        var request = {
            methodname: 'tool_dataprivacy_set_context_defaults',
            args: {
                'contextlevel': contextLevel,
                'category': category,
                'purpose': purpose,
                'override': override,
                'activity': activity
            }
        };

        Ajax.call([request])[0].done(function(data) {
            if (data.result) {
                window.location.reload();
            }
        });
    }

    return /** @alias module:tool_dataprivacy/defaultsactions */ {
        // Public variables and functions.

        /**
         * Initialise the module.
         *
         * @method init
         * @return {DefaultsActions}
         */
        'init': function() {
            return new DefaultsActions();
        }
    };
});
