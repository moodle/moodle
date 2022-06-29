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
 * Request actions.
 *
 * @module     tool_dataprivacy/data_registry
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'core/ajax', 'core/notification', 'core/templates', 'core/modal_factory',
    'core/modal_events', 'core/fragment', 'tool_dataprivacy/add_purpose', 'tool_dataprivacy/add_category'],
    function($, Str, Ajax, Notification, Templates, ModalFactory, ModalEvents, Fragment, AddPurpose, AddCategory) {

        var SELECTORS = {
            TREE_NODES: '[data-context-tree-node=1]',
            FORM_CONTAINER: '#context-form-container',
        };

        var DataRegistry = function(systemContextId, initContextLevel, initContextId) {
            this.systemContextId = systemContextId;
            this.currentContextLevel = initContextLevel;
            this.currentContextId = initContextId;
            this.init();
        };

        /**
         * @var {int} systemContextId
         * @private
         */
        DataRegistry.prototype.systemContextId = 0;

        /**
         * @var {int} currentContextLevel
         * @private
         */
        DataRegistry.prototype.currentContextLevel = 0;

        /**
         * @var {int} currentContextId
         * @private
         */
        DataRegistry.prototype.currentContextId = 0;

        /**
         * @var {AddPurpose} addpurpose
         * @private
         */
        DataRegistry.prototype.addpurpose = null;

        /**
         * @var {AddCategory} addcategory
         * @private
         */
        DataRegistry.prototype.addcategory = null;

        DataRegistry.prototype.init = function() {
            // Add purpose and category modals always at system context.
            this.addpurpose = AddPurpose.getInstance(this.systemContextId);
            this.addcategory = AddCategory.getInstance(this.systemContextId);

            var stringKeys = [
                {
                    key: 'changessaved',
                    component: 'moodle'
                }, {
                    key: 'contextpurposecategorysaved',
                    component: 'tool_dataprivacy'
                }, {
                    key: 'noblockstoload',
                    component: 'tool_dataprivacy'
                }, {
                    key: 'noactivitiestoload',
                    component: 'tool_dataprivacy'
                }, {
                    key: 'nocoursestoload',
                    component: 'tool_dataprivacy'
                }
            ];
            this.strings = Str.get_strings(stringKeys);

            this.registerEventListeners();

            // Load the default context level form.
            if (this.currentContextId) {
                this.loadForm('context_form', [this.currentContextId], this.submitContextFormAjax.bind(this));
            } else {
                this.loadForm('contextlevel_form', [this.currentContextLevel], this.submitContextLevelFormAjax.bind(this));
            }
        };

        DataRegistry.prototype.registerEventListeners = function() {
            $(SELECTORS.TREE_NODES).on('click', function(ev) {
                ev.preventDefault();

                var trigger = $(ev.currentTarget);

                // Active node.
                $(SELECTORS.TREE_NODES).removeClass('active');
                trigger.addClass('active');

                var contextLevel = trigger.data('contextlevel');
                var contextId = trigger.data('contextid');
                if (contextLevel) {
                    // Context level level.

                    window.history.pushState({}, null, '?contextlevel=' + contextLevel);

                    // Remove previous add purpose and category listeners to avoid memory leaks.
                    this.addpurpose.removeListeners();
                    this.addcategory.removeListeners();

                    // Load the context level form.
                    this.currentContextLevel = contextLevel;
                    this.loadForm('contextlevel_form', [this.currentContextLevel], this.submitContextLevelFormAjax.bind(this));
                } else if (contextId) {
                    // Context instance level.

                    window.history.pushState({}, null, '?contextid=' + contextId);

                    // Remove previous add purpose and category listeners to avoid memory leaks.
                    this.addpurpose.removeListeners();
                    this.addcategory.removeListeners();

                    // Load the context level form.
                    this.currentContextId = contextId;
                    this.loadForm('context_form', [this.currentContextId], this.submitContextFormAjax.bind(this));
                } else {
                    // Expandable nodes.

                    var expandContextId = trigger.data('expandcontextid');
                    var expandElement = trigger.data('expandelement');
                    var expanded = trigger.data('expanded');

                    // Extra checking that there is an expandElement because we remove it after loading 0 branches.
                    if (expandElement) {

                        if (!expanded) {
                            if (trigger.data('loaded') || !expandContextId || !expandElement) {
                                this.expand(trigger);
                            } else {

                                trigger.find('> i').removeClass('fa-plus');
                                trigger.find('> i').addClass('fa-circle-o-notch fa-spin');
                                this.loadExtra(trigger, expandContextId, expandElement);
                            }
                        } else {
                            this.collapse(trigger);
                        }
                    }
                }

            }.bind(this));
        };

        DataRegistry.prototype.removeListeners = function() {
            $(SELECTORS.TREE_NODES).off('click');
        };

        DataRegistry.prototype.loadForm = function(fragmentName, fragmentArgs, formSubmitCallback) {

            this.clearForm();

            var fragment = Fragment.loadFragment('tool_dataprivacy', fragmentName, this.systemContextId, fragmentArgs);
            fragment.done(function(html, js) {

                $(SELECTORS.FORM_CONTAINER).html(html);
                Templates.runTemplateJS(js);

                this.addpurpose.registerEventListeners();
                this.addcategory.registerEventListeners();

                // We also catch the form submit event and use it to submit the form with ajax.
                $(SELECTORS.FORM_CONTAINER).on('submit', 'form', formSubmitCallback);

            }.bind(this)).fail(Notification.exception);
        };

        DataRegistry.prototype.clearForm = function() {
            // Remove previous listeners.
            $(SELECTORS.FORM_CONTAINER).off('submit', 'form');
        };

        /**
         * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
         *
         * @method submitForm
         * @param {Event} e Form submission event.
         * @private
         */
        DataRegistry.prototype.submitForm = function(e) {
            e.preventDefault();
            $(SELECTORS.FORM_CONTAINER).find('form').submit();
        };

        DataRegistry.prototype.submitContextLevelFormAjax = function(e) {
            this.submitFormAjax(e, 'tool_dataprivacy_set_contextlevel_form');
        };

        DataRegistry.prototype.submitContextFormAjax = function(e) {
            this.submitFormAjax(e, 'tool_dataprivacy_set_context_form');
        };

        DataRegistry.prototype.submitFormAjax = function(e, saveMethodName) {
            // We don't want to do a real form submission.
            e.preventDefault();

            // Convert all the form elements values to a serialised string.
            var formData = $(SELECTORS.FORM_CONTAINER).find('form').serialize();
            return this.strings.then(function(strings) {
                Ajax.call([{
                    methodname: saveMethodName,
                    args: {jsonformdata: JSON.stringify(formData)},
                    done: function() {
                        Notification.alert(strings[0], strings[1]);
                    },
                    fail: Notification.exception
                }]);
                return;
            }).catch(Notification.exception);

        };

        DataRegistry.prototype.loadExtra = function(parentNode, expandContextId, expandElement) {

            Ajax.call([{
                methodname: 'tool_dataprivacy_tree_extra_branches',
                args: {
                    contextid: expandContextId,
                    element: expandElement,
                },
                done: function(data) {
                    if (data.branches.length == 0) {
                        this.noElements(parentNode, expandElement);
                        return;
                    }
                    Templates.render('tool_dataprivacy/context_tree_branches', data)
                        .then(function(html) {
                            parentNode.after(html);
                            this.removeListeners();
                            this.registerEventListeners();
                            this.expand(parentNode);
                            parentNode.data('loaded', 1);
                            return;
                        }.bind(this))
                        .fail(Notification.exception);
                }.bind(this),
                fail: Notification.exception
            }]);
        };

        DataRegistry.prototype.noElements = function(node, expandElement) {
            node.data('expandcontextid', '');
            node.data('expandelement', '');
            this.strings.then(function(strings) {

                // 2 = blocks, 3 = activities, 4 = courses (although courses is not likely really).
                var key = 2;
                if (expandElement == 'module') {
                    key = 3;
                } else if (expandElement == 'course') {
                    key = 4;
                }
                node.text(strings[key]);
                return;
            }).fail(Notification.exception);
        };

        DataRegistry.prototype.collapse = function(node) {
            node.data('expanded', 0);
            node.siblings('nav').addClass('hidden');
            node.find('> i').removeClass('fa-minus');
            node.find('> i').addClass('fa-plus');
        };

        DataRegistry.prototype.expand = function(node) {
            node.data('expanded', 1);
            node.siblings('nav').removeClass('hidden');
            node.find('> i').removeClass('fa-plus');
            // Also remove the spinning one if data was just loaded.
            node.find('> i').removeClass('fa-circle-o-notch fa-spin');
            node.find('> i').addClass('fa-minus');
        };
        return /** @alias module:tool_dataprivacy/data_registry */ {

            /**
             * Initialise the page.
             *
             * @param {Number} systemContextId
             * @param {Number} initContextLevel
             * @param {Number} initContextId
             * @return {DataRegistry}
             */
            init: function(systemContextId, initContextLevel, initContextId) {
                return new DataRegistry(systemContextId, initContextLevel, initContextId);
            }
        };
    }
);

