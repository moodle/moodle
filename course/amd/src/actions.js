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
 * Various actions on modules and sections in the editing mode - hiding, duplicating, deleting, etc.
 *
 * @module     core_course/actions
 * @package    core
 * @copyright  2016 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.3
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification', 'core/str', 'core/url', 'core/yui',
        'core/modal_factory', 'core/modal_events', 'core/key_codes'],
    function($, ajax, templates, notification, str, url, Y, ModalFactory, ModalEvents, KeyCodes) {
        var CSS = {
            EDITINPROGRESS: 'editinprogress',
            SECTIONDRAGGABLE: 'sectiondraggable',
            EDITINGMOVE: 'editing_move'
        };
        var SELECTOR = {
            ACTIVITYLI: 'li.activity',
            ACTIONAREA: '.actions',
            ACTIVITYACTION: 'a.cm-edit-action',
            MENU: '.moodle-actionmenu[data-enhance=moodle-core-actionmenu]',
            TOGGLE: '.toggle-display,.dropdown-toggle',
            SECTIONLI: 'li.section',
            SECTIONACTIONMENU: '.section_action_menu',
            ADDSECTIONS: '#changenumsections [data-add-sections]'
        };

        Y.use('moodle-course-coursebase', function() {
            var courseformatselector = M.course.format.get_section_selector();
            if (courseformatselector) {
                SELECTOR.SECTIONLI = courseformatselector;
            }
        });

        /**
         * Wrapper for Y.Moodle.core_course.util.cm.getId
         *
         * @param {JQuery} element
         * @returns {Integer}
         */
        var getModuleId = function(element) {
            var id;
            Y.use('moodle-course-util', function(Y) {
                id = Y.Moodle.core_course.util.cm.getId(Y.Node(element.get(0)));
            });
            return id;
        };

        /**
         * Wrapper for Y.Moodle.core_course.util.cm.getName
         *
         * @param {JQuery} element
         * @returns {String}
         */
        var getModuleName = function(element) {
            var name;
            Y.use('moodle-course-util', function(Y) {
                name = Y.Moodle.core_course.util.cm.getName(Y.Node(element.get(0)));
            });
            return name;
        };

        /**
         * Wrapper for M.util.add_spinner for an activity
         *
         * @param {JQuery} activity
         * @returns {Node}
         */
        var addActivitySpinner = function(activity) {
            activity.addClass(CSS.EDITINPROGRESS);
            var actionarea = activity.find(SELECTOR.ACTIONAREA).get(0);
            if (actionarea) {
                var spinner = M.util.add_spinner(Y, Y.Node(actionarea));
                spinner.show();
                return spinner;
            }
            return null;
        };

        /**
         * Wrapper for M.util.add_spinner for a section
         *
         * @param {JQuery} sectionelement
         * @returns {Node}
         */
        var addSectionSpinner = function(sectionelement) {
            sectionelement.addClass(CSS.EDITINPROGRESS);
            var actionarea = sectionelement.find(SELECTOR.SECTIONACTIONMENU).get(0);
            if (actionarea) {
                var spinner = M.util.add_spinner(Y, Y.Node(actionarea));
                spinner.show();
                return spinner;
            }
            return null;
        };

        /**
         * Wrapper for M.util.add_lightbox
         *
         * @param {JQuery} sectionelement
         * @returns {Node}
         */
        var addSectionLightbox = function(sectionelement) {
            var lightbox = M.util.add_lightbox(Y, Y.Node(sectionelement.get(0)));
            lightbox.show();
            return lightbox;
        };

        /**
         * Removes the spinner element
         *
         * @param {JQuery} element
         * @param {Node} spinner
         * @param {Number} delay
         */
        var removeSpinner = function(element, spinner, delay) {
            window.setTimeout(function() {
                element.removeClass(CSS.EDITINPROGRESS);
                if (spinner) {
                    spinner.hide();
                }
            }, delay);
        };

        /**
         * Removes the lightbox element
         *
         * @param {Node} lightbox lighbox YUI element returned by addSectionLightbox
         * @param {Number} delay
         */
        var removeLightbox = function(lightbox, delay) {
            if (lightbox) {
                window.setTimeout(function() {
                    lightbox.hide();
                }, delay);
            }
        };

        /**
         * Initialise action menu for the element (section or module)
         *
         * @param {String} elementid CSS id attribute of the element
         */
        var initActionMenu = function(elementid) {
            // Initialise action menu in the new activity.
            Y.use('moodle-course-coursebase', function() {
                M.course.coursebase.invoke_function('setup_for_resource', '#' + elementid);
            });
            if (M.core.actionmenu && M.core.actionmenu.newDOMNode) {
                M.core.actionmenu.newDOMNode(Y.one('#' + elementid));
            }
        };

        /**
         * Returns focus to the element that was clicked or "Edit" link if element is no longer visible.
         *
         * @param {String} elementId CSS id attribute of the element
         * @param {String} action data-action property of the element that was clicked
         */
        var focusActionItem = function(elementId, action) {
            var mainelement = $('#' + elementId);
            var selector = '[data-action=' + action + ']';
            if (action === 'groupsseparate' || action === 'groupsvisible' || action === 'groupsnone') {
                // New element will have different data-action.
                selector = '[data-action=groupsseparate],[data-action=groupsvisible],[data-action=groupsnone]';
            }
            if (mainelement.find(selector).is(':visible')) {
                mainelement.find(selector).focus();
            } else {
                // Element not visible, focus the "Edit" link.
                mainelement.find(SELECTOR.MENU).find(SELECTOR.TOGGLE).focus();
            }
        };

        /**
         * Find next <a> after the element
         *
         * @param {JQuery} mainElement element that is about to be deleted
         * @returns {JQuery}
         */
        var findNextFocusable = function(mainElement) {
            var tabables = $("a:visible");
            var isInside = false;
            var foundElement = null;
            tabables.each(function() {
                if ($.contains(mainElement[0], this)) {
                    isInside = true;
                } else if (isInside) {
                    foundElement = this;
                    return false; // Returning false in .each() is equivalent to "break;" inside the loop in php.
                }
            });
            return foundElement;
        };

        /**
         * Performs an action on a module (moving, deleting, duplicating, hiding, etc.)
         *
         * @param {JQuery} moduleElement activity element we perform action on
         * @param {Number} cmid
         * @param {JQuery} target the element (menu item) that was clicked
         */
        var editModule = function(moduleElement, cmid, target) {
            var action = target.attr('data-action');
            var spinner = addActivitySpinner(moduleElement);
            var promises = ajax.call([{
                methodname: 'core_course_edit_module',
                args: {id: cmid,
                    action: action,
                    sectionreturn: target.attr('data-sectionreturn') ? target.attr('data-sectionreturn') : 0
                }
            }], true);

            var lightbox;
            if (action === 'duplicate') {
                lightbox = addSectionLightbox(target.closest(SELECTOR.SECTIONLI));
            }
            $.when.apply($, promises)
                .done(function(data) {
                    var elementToFocus = findNextFocusable(moduleElement);
                    moduleElement.replaceWith(data);
                    // Initialise action menu for activity(ies) added as a result of this.
                    $('<div>' + data + '</div>').find(SELECTOR.ACTIVITYLI).each(function(index) {
                        initActionMenu($(this).attr('id'));
                        if (index === 0) {
                            focusActionItem($(this).attr('id'), action);
                            elementToFocus = null;
                        }
                    });
                    // In case of activity deletion focus the next focusable element.
                    if (elementToFocus) {
                        elementToFocus.focus();
                    }
                    // Remove spinner and lightbox with a delay.
                    removeSpinner(moduleElement, spinner, 400);
                    removeLightbox(lightbox, 400);
                    // Trigger event that can be observed by course formats.
                    moduleElement.trigger($.Event('coursemoduleedited', {ajaxreturn: data, action: action}));
                }).fail(function(ex) {
                    // Remove spinner and lightbox.
                    removeSpinner(moduleElement, spinner);
                    removeLightbox(lightbox);
                    // Trigger event that can be observed by course formats.
                    var e = $.Event('coursemoduleeditfailed', {exception: ex, action: action});
                    moduleElement.trigger(e);
                    if (!e.isDefaultPrevented()) {
                        notification.exception(ex);
                    }
                });
        };

        /**
         * Requests html for the module via WS core_course_get_module and updates the module on the course page
         *
         * Used after d&d of the module to another section
         *
         * @param {JQuery} activityElement
         * @param {Number} cmid
         * @param {Number} sectionreturn
         */
        var refreshModule = function(activityElement, cmid, sectionreturn) {
            var spinner = addActivitySpinner(activityElement);
            var promises = ajax.call([{
                methodname: 'core_course_get_module',
                args: {id: cmid, sectionreturn: sectionreturn}
            }], true);

            $.when.apply($, promises)
                .done(function(data) {
                    removeSpinner(activityElement, spinner, 400);
                    replaceActivityHtmlWith(data);
                }).fail(function() {
                    removeSpinner(activityElement, spinner);
                });
        };

        /**
         * Displays the delete confirmation to delete a module
         *
         * @param {JQuery} mainelement activity element we perform action on
         * @param {function} onconfirm function to execute on confirm
         */
        var confirmDeleteModule = function(mainelement, onconfirm) {
            var modtypename = mainelement.attr('class').match(/modtype_([^\s]*)/)[1];
            var modulename = getModuleName(mainelement);

            str.get_string('pluginname', modtypename).done(function(pluginname) {
                var plugindata = {
                    type: pluginname,
                    name: modulename
                };
                str.get_strings([
                    {key: 'confirm'},
                    {key: modulename === null ? 'deletechecktype' : 'deletechecktypename', param: plugindata},
                    {key: 'yes'},
                    {key: 'no'}
                ]).done(function(s) {
                        notification.confirm(s[0], s[1], s[2], s[3], onconfirm);
                    }
                );
            });
        };

        /**
         * Displays the delete confirmation to delete a section
         *
         * @param {String} message confirmation message
         * @param {function} onconfirm function to execute on confirm
         */
        var confirmEditSection = function(message, onconfirm) {
            str.get_strings([
                {key: 'confirm'}, // TODO link text
                {key: 'yes'},
                {key: 'no'}
            ]).done(function(s) {
                    notification.confirm(s[0], message, s[1], s[2], onconfirm);
                }
            );
        };

        /**
         * Replaces an action menu item with another one (for example Show->Hide, Set marker->Remove marker)
         *
         * @param {JQuery} actionitem
         * @param {String} image new image name ("i/show", "i/hide", etc.)
         * @param {String} stringname new string for the action menu item
         * @param {String} stringcomponent
         * @param {String} titlestr not used
         * @param {String} titlecomponent not used
         * @param {String} newaction new value for data-action attribute of the link
         * @return {Promise} promise which is resolved when the replacement has completed
         */
        var replaceActionItem = function(actionitem, image, stringname,
                                           stringcomponent, titlestr, titlecomponent, newaction) {

            var stringRequests = [{key: stringname, component: stringcomponent}];
            // Do not provide an icon with duplicate, different text to the menu item.

            return str.get_strings(stringRequests).then(function(strings) {
                actionitem.find('span.menu-action-text').html(strings[0]);

                return templates.renderPix(image, 'core');
            }).then(function(pixhtml) {
                actionitem.find('.icon').replaceWith(pixhtml);
                actionitem.attr('data-action', newaction);
                return;
            }).catch(notification.exception);
        };

        /**
         * Default post-processing for section AJAX edit actions.
         *
         * This can be overridden in course formats by listening to event coursesectionedited:
         *
         * $('body').on('coursesectionedited', 'li.section', function(e) {
         *     var action = e.action,
         *         sectionElement = $(e.target),
         *         data = e.ajaxreturn;
         *     // ... Do some processing here.
         *     e.preventDefault(); // Prevent default handler.
         * });
         *
         * @param {JQuery} sectionElement
         * @param {JQuery} actionItem
         * @param {Object} data
         * @param {String} courseformat
         */
        var defaultEditSectionHandler = function(sectionElement, actionItem, data, courseformat) {
            var action = actionItem.attr('data-action');
            if (action === 'hide' || action === 'show') {
                if (action === 'hide') {
                    sectionElement.addClass('hidden');
                    replaceActionItem(actionItem, 'i/show',
                        'showfromothers', 'format_' + courseformat, null, null, 'show');
                } else {
                    sectionElement.removeClass('hidden');
                    replaceActionItem(actionItem, 'i/hide',
                        'hidefromothers', 'format_' + courseformat, null, null, 'hide');
                }
                // Replace the modules with new html (that indicates that they are now hidden or not hidden).
                if (data.modules !== undefined) {
                    for (var i in data.modules) {
                        replaceActivityHtmlWith(data.modules[i]);
                    }
                }
                // Replace the section availability information.
                if (data.section_availability !== undefined) {
                    sectionElement.find('.section_availability').first().replaceWith(data.section_availability);
                }
            } else if (action === 'setmarker') {
                var oldmarker = $(SELECTOR.SECTIONLI + '.current'),
                    oldActionItem = oldmarker.find(SELECTOR.SECTIONACTIONMENU + ' ' + 'a[data-action=removemarker]');
                oldmarker.removeClass('current');
                replaceActionItem(oldActionItem, 'i/marker',
                    'highlight', 'core', 'markthistopic', 'core', 'setmarker');
                sectionElement.addClass('current');
                replaceActionItem(actionItem, 'i/marked',
                    'highlightoff', 'core', 'markedthistopic', 'core', 'removemarker');
            } else if (action === 'removemarker') {
                sectionElement.removeClass('current');
                replaceActionItem(actionItem, 'i/marker',
                    'highlight', 'core', 'markthistopic', 'core', 'setmarker');
            }
        };

        /**
         * Replaces the course module with the new html (used to update module after it was edited or its visibility was changed).
         *
         * @param {String} activityHTML
         */
        var replaceActivityHtmlWith = function(activityHTML) {
            $('<div>' + activityHTML + '</div>').find(SELECTOR.ACTIVITYLI).each(function() {
                // Extract id from the new activity html.
                var id = $(this).attr('id');
                // Find the existing element with the same id and replace its contents with new html.
                $(SELECTOR.ACTIVITYLI + '#' + id).replaceWith(activityHTML);
                // Initialise action menu.
                initActionMenu(id);
            });
        };

        /**
         * Performs an action on a module (moving, deleting, duplicating, hiding, etc.)
         *
         * @param {JQuery} sectionElement section element we perform action on
         * @param {Nunmber} sectionid
         * @param {JQuery} target the element (menu item) that was clicked
         * @param {String} courseformat
         */
        var editSection = function(sectionElement, sectionid, target, courseformat) {
            var action = target.attr('data-action'),
                sectionreturn = target.attr('data-sectionreturn') ? target.attr('data-sectionreturn') : 0;
            var spinner = addSectionSpinner(sectionElement);
            var promises = ajax.call([{
                methodname: 'core_course_edit_section',
                args: {id: sectionid, action: action, sectionreturn: sectionreturn}
            }], true);

            var lightbox = addSectionLightbox(sectionElement);
            $.when.apply($, promises)
                .done(function(dataencoded) {
                    var data = $.parseJSON(dataencoded);
                    removeSpinner(sectionElement, spinner);
                    removeLightbox(lightbox);
                    sectionElement.find(SELECTOR.SECTIONACTIONMENU).find(SELECTOR.TOGGLE).focus();
                    // Trigger event that can be observed by course formats.
                    var e = $.Event('coursesectionedited', {ajaxreturn: data, action: action});
                    sectionElement.trigger(e);
                    if (!e.isDefaultPrevented()) {
                        defaultEditSectionHandler(sectionElement, target, data, courseformat);
                    }
                }).fail(function(ex) {
                    // Remove spinner and lightbox.
                    removeSpinner(sectionElement, spinner);
                    removeLightbox(lightbox);
                    // Trigger event that can be observed by course formats.
                    var e = $.Event('coursesectioneditfailed', {exception: ex, action: action});
                    sectionElement.trigger(e);
                    if (!e.isDefaultPrevented()) {
                        notification.exception(ex);
                    }
                });
        };

        // Register a function to be executed after D&D of an activity.
        Y.use('moodle-course-coursebase', function() {
            M.course.coursebase.register_module({
                // Ignore camelcase eslint rule for the next line because it is an expected name of the callback.
                // eslint-disable-next-line camelcase
                set_visibility_resource_ui: function(args) {
                    var mainelement = $(args.element.getDOMNode());
                    var cmid = getModuleId(mainelement);
                    if (cmid) {
                        var sectionreturn = mainelement.find('.' + CSS.EDITINGMOVE).attr('data-sectionreturn');
                        refreshModule(mainelement, cmid, sectionreturn);
                    }
                }
            });
        });

        return /** @alias module:core_course/actions */ {

            /**
             * Initialises course page
             *
             * @method init
             * @param {String} courseformat name of the current course format (for fetching strings)
             */
            initCoursePage: function(courseformat) {

                // Add a handler for course module actions.
                $('body').on('click keypress', SELECTOR.ACTIVITYLI + ' ' +
                        SELECTOR.ACTIVITYACTION + '[data-action]', function(e) {
                    if (e.type === 'keypress' && e.keyCode !== 13) {
                        return;
                    }
                    var actionItem = $(this),
                        moduleElement = actionItem.closest(SELECTOR.ACTIVITYLI),
                        action = actionItem.attr('data-action'),
                        moduleId = getModuleId(moduleElement);
                    switch (action) {
                        case 'moveleft':
                        case 'moveright':
                        case 'delete':
                        case 'duplicate':
                        case 'hide':
                        case 'stealth':
                        case 'show':
                        case 'groupsseparate':
                        case 'groupsvisible':
                        case 'groupsnone':
                            break;
                        default:
                            // Nothing to do here!
                            return;
                    }
                    if (!moduleId) {
                        return;
                    }
                    e.preventDefault();
                    if (action === 'delete') {
                        // Deleting requires confirmation.
                        confirmDeleteModule(moduleElement, function() {
                            editModule(moduleElement, moduleId, actionItem);
                        });
                    } else {
                        editModule(moduleElement, moduleId, actionItem);
                    }
                });

                // Add a handler for section show/hide actions.
                $('body').on('click keypress', SELECTOR.SECTIONLI + ' ' +
                            SELECTOR.SECTIONACTIONMENU + '[data-sectionid] ' +
                            'a[data-action]', function(e) {
                    if (e.type === 'keypress' && e.keyCode !== 13) {
                        return;
                    }
                    var actionItem = $(this),
                        sectionElement = actionItem.closest(SELECTOR.SECTIONLI),
                        sectionId = actionItem.closest(SELECTOR.SECTIONACTIONMENU).attr('data-sectionid');
                    e.preventDefault();
                    if (actionItem.attr('data-confirm')) {
                        // Action requires confirmation.
                        confirmEditSection(actionItem.attr('data-confirm'), function() {
                            editSection(sectionElement, sectionId, actionItem, courseformat);
                        });
                    } else {
                        editSection(sectionElement, sectionId, actionItem, courseformat);
                    }
                });

                // Add a handler for "Add sections" link to ask for a number of sections to add.
                str.get_string('numberweeks').done(function(strNumberSections) {
                    var trigger = $(SELECTOR.ADDSECTIONS),
                        modalTitle = trigger.attr('data-add-sections'),
                        newSections = trigger.attr('data-new-sections');
                    var modalBody = $('<div><label for="add_section_numsections"></label> ' +
                        '<input id="add_section_numsections" type="number" min="1" max="' + newSections + '" value="1"></div>');
                    modalBody.find('label').html(strNumberSections);
                    ModalFactory.create({
                        title: modalTitle,
                        type: ModalFactory.types.SAVE_CANCEL,
                        body: modalBody.html()
                    }, trigger)
                    .done(function(modal) {
                        var numSections = $(modal.getBody()).find('#add_section_numsections'),
                        addSections = function() {
                            // Check if value of the "Number of sections" is a valid positive integer and redirect
                            // to adding a section script.
                            if ('' + parseInt(numSections.val()) === numSections.val() && parseInt(numSections.val()) >= 1) {
                                document.location = trigger.attr('href') + '&numsections=' + parseInt(numSections.val());
                            }
                        };
                        modal.setSaveButtonText(modalTitle);
                        modal.getRoot().on(ModalEvents.shown, function() {
                            // When modal is shown focus and select the input and add a listener to keypress of "Enter".
                            numSections.focus().select().on('keydown', function(e) {
                                if (e.keyCode === KeyCodes.enter) {
                                    addSections();
                                }
                            });
                        });
                        modal.getRoot().on(ModalEvents.save, function(e) {
                            // When modal "Add" button is pressed.
                            e.preventDefault();
                            addSections();
                        });
                    });
                });
            },

            /**
             * Replaces a section action menu item with another one (for example Show->Hide, Set marker->Remove marker)
             *
             * This method can be used by course formats in their listener to the coursesectionedited event
             *
             * @param {JQuery} sectionelement
             * @param {String} selector CSS selector inside the section element, for example "a[data-action=show]"
             * @param {String} image new image name ("i/show", "i/hide", etc.)
             * @param {String} stringname new string for the action menu item
             * @param {String} stringcomponent
             * @param {String} titlestr string for "title" attribute (if different from stringname)
             * @param {String} titlecomponent
             * @param {String} newaction new value for data-action attribute of the link
             */
            replaceSectionActionItem: function(sectionelement, selector, image, stringname,
                                                    stringcomponent, titlestr, titlecomponent, newaction) {
                var actionitem = sectionelement.find(SELECTOR.SECTIONACTIONMENU + ' ' + selector);
                replaceActionItem(actionitem, image, stringname, stringcomponent, titlestr, titlecomponent, newaction);
            }
        };
    });
