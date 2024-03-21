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
 * @copyright  2016 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.3
 */
define(
    [
        'jquery',
        'core/ajax',
        'core/templates',
        'core/notification',
        'core/str',
        'core/url',
        'core/yui',
        'core/modal_copy_to_clipboard',
        'core/modal_save_cancel',
        'core/modal_events',
        'core/key_codes',
        'core/log',
        'core_courseformat/courseeditor',
        'core/event_dispatcher',
        'core_course/events'
    ],
    function(
        $,
        ajax,
        templates,
        notification,
        str,
        url,
        Y,
        ModalCopyToClipboard,
        ModalSaveCancel,
        ModalEvents,
        KeyCodes,
        log,
        editor,
        EventDispatcher,
        CourseEvents
    ) {

        // Eventually, core_courseformat/local/content/actions will handle all actions for
        // component compatible formats and the default actions.js won't be necessary anymore.
        // Meanwhile, we filter the migrated actions.
        const componentActions = [
            'moveSection', 'moveCm', 'addSection', 'deleteSection', 'cmDelete', 'cmDuplicate', 'sectionHide', 'sectionShow',
            'cmHide', 'cmShow', 'cmStealth', 'sectionHighlight', 'sectionUnhighlight', 'cmMoveRight', 'cmMoveLeft',
            'cmNoGroups', 'cmVisibleGroups', 'cmSeparateGroups',
        ];

        // The course reactive instance.
        const courseeditor = editor.getCurrentCourseEditor();

        // The current course format name (loaded on init).
        let formatname;

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
            SECTIONITEM: '[data-for="section_title"]',
            ADDSECTIONS: '.changenumsections [data-add-sections]',
            SECTIONBADGES: '[data-region="sectionbadges"]',
        };

        Y.use('moodle-course-coursebase', function() {
            var courseformatselector = M.course.format.get_section_selector();
            if (courseformatselector) {
                SELECTOR.SECTIONLI = courseformatselector;
            }
        });

        /**
         * Dispatch event wrapper.
         *
         * Old jQuery events will be replaced by native events gradually.
         *
         * @method dispatchEvent
         * @param {String} eventName The name of the event
         * @param {Object} detail Any additional details to pass into the eveent
         * @param {Node|HTMLElement} container The point at which to dispatch the event
         * @param {Object} options
         * @param {Boolean} options.bubbles Whether to bubble up the DOM
         * @param {Boolean} options.cancelable Whether preventDefault() can be called
         * @param {Boolean} options.composed Whether the event can bubble across the ShadowDOM boundary
         * @returns {CustomEvent}
         */
        const dispatchEvent = function(eventName, detail, container, options) {
            // Most actions still uses jQuery node instead of regular HTMLElement.
            if (!(container instanceof Element) && container.get !== undefined) {
                container = container.get(0);
            }
            return EventDispatcher.dispatchEvent(eventName, detail, container, options);
        };

        /**
         * Wrapper for Y.Moodle.core_course.util.cm.getId
         *
         * @param {JQuery} element
         * @returns {Integer}
         */
        var getModuleId = function(element) {
            // Check if we have a data-id first.
            const item = element.get(0);
            if (item.dataset.id) {
                return item.dataset.id;
            }
            // Use YUI way if data-id is not present.
            let id;
            Y.use('moodle-course-util', function(Y) {
                id = Y.Moodle.core_course.util.cm.getId(Y.Node(item));
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
            // Check if we have the name in the course state.
            const state = courseeditor.state;
            const cmid = getModuleId(element);
            if (!name && state && cmid) {
                name = state.cm.get(cmid)?.name;
            }
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
                // Lock the activity state element.
                if (activity.data('id') !== undefined) {
                    courseeditor.dispatch('cmLock', [activity.data('id')], true);
                }
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
                // Lock the section state element.
                if (sectionelement.data('id') !== undefined) {
                    courseeditor.dispatch('sectionLock', [sectionelement.data('id')], true);
                }
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
            const item = sectionelement.get(0);
            var lightbox = M.util.add_lightbox(Y, Y.Node(item));
            if (item.dataset.for == 'section' && item.dataset.id) {
                courseeditor.dispatch('sectionLock', [item.dataset.id], true);
                lightbox.setAttribute('data-state', 'section');
                lightbox.setAttribute('data-state-id', item.dataset.id);
            }
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
                // Unlock the state element.
                if (element.data('id') !== undefined) {
                    const mutation = (element.data('for') === 'section') ? 'sectionLock' : 'cmLock';
                    courseeditor.dispatch(mutation, [element.data('id')], false);
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
                    // Unlock state if necessary.
                    if (lightbox.getAttribute('data-state')) {
                        courseeditor.dispatch(
                            `${lightbox.getAttribute('data-state')}Lock`,
                            [lightbox.getAttribute('data-state-id')],
                            false
                        );
                    }
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
                return true;
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
                    sectionreturn: target.attr('data-sectionreturn') ? target.attr('data-sectionreturn') : null
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
                    let affectedids = [];
                    // Initialise action menu for activity(ies) added as a result of this.
                    $('<div>' + data + '</div>').find(SELECTOR.ACTIVITYLI).each(function(index) {
                        initActionMenu($(this).attr('id'));
                        if (index === 0) {
                            focusActionItem($(this).attr('id'), action);
                            elementToFocus = null;
                        }
                        // Save any activity id in cmids.
                        affectedids.push(getModuleId($(this)));
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

                    // Modify cm state.
                    courseeditor.dispatch('legacyActivityAction', action, cmid, affectedids);

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
         * @param {JQuery|Element} element
         * @param {Number} cmid
         * @param {Number} sectionreturn
         * @return {Promise} the refresh promise
         */
        var refreshModule = function(element, cmid, sectionreturn) {

            if (sectionreturn === undefined) {
                sectionreturn = courseeditor.sectionReturn;
            }

            const activityElement = $(element);
            var spinner = addActivitySpinner(activityElement);
            var promises = ajax.call([{
                methodname: 'core_course_get_module',
                args: {id: cmid, sectionreturn: sectionreturn}
            }], true);

            return new Promise((resolve, reject) => {
                $.when.apply($, promises)
                    .done(function(data) {
                        removeSpinner(activityElement, spinner, 400);
                        replaceActivityHtmlWith(data);
                        resolve(data);
                    }).fail(function() {
                        removeSpinner(activityElement, spinner);
                        reject();
                    });
            });
        };

        /**
         * Requests html for the section via WS core_course_edit_section and updates the section on the course page
         *
         * @param {JQuery|Element} element
         * @param {Number} sectionid
         * @param {Number} sectionreturn
         * @return {Promise} the refresh promise
         */
        var refreshSection = function(element, sectionid, sectionreturn) {

            if (sectionreturn === undefined) {
                sectionreturn = courseeditor.sectionReturn;
            }

            const sectionElement = $(element);
            const action = 'refresh';
            const promises = ajax.call([{
                methodname: 'core_course_edit_section',
                args: {id: sectionid, action, sectionreturn},
            }], true);

            var spinner = addSectionSpinner(sectionElement);
            return new Promise((resolve, reject) => {
                $.when.apply($, promises)
                    .done(dataencoded => {

                        removeSpinner(sectionElement, spinner);
                        const data = $.parseJSON(dataencoded);

                        const newSectionElement = $(data.content);
                        sectionElement.replaceWith(newSectionElement);

                        // Init modules menus.
                        $(`${SELECTOR.SECTIONLI}#${sectionid} ${SELECTOR.ACTIVITYLI}`).each(
                            (index, activity) => {
                                initActionMenu(activity.data('id'));
                            }
                        );

                        // Trigger event that can be observed by course formats.
                        const event = dispatchEvent(
                            CourseEvents.sectionRefreshed,
                            {
                                ajaxreturn: data,
                                action: action,
                                newSectionElement: newSectionElement.get(0),
                            },
                            newSectionElement
                        );

                        if (!event.defaultPrevented) {
                            defaultEditSectionHandler(
                                newSectionElement, $(SELECTOR.SECTIONLI + '#' + sectionid),
                                data,
                                formatname,
                                sectionid
                            );
                        }
                        resolve(data);
                    }).fail(ex => {
                        // Trigger event that can be observed by course formats.
                        const event = dispatchEvent(
                            'coursesectionrefreshfailed',
                            {exception: ex, action: action},
                            sectionElement
                        );
                        if (!event.defaultPrevented) {
                            notification.exception(ex);
                        }
                        reject();
                    });
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
                    {key: 'confirm', component: 'core'},
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
         * @param {String} newaction new value for data-action attribute of the link
         * @return {Promise} promise which is resolved when the replacement has completed
         */
        var replaceActionItem = function(actionitem, image, stringname,
                                           stringcomponent, newaction) {

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
         * @param {Number} sectionid
         */
        var defaultEditSectionHandler = function(sectionElement, actionItem, data, courseformat, sectionid) {
            var action = actionItem.attr('data-action');
            if (action === 'hide' || action === 'show') {
                if (action === 'hide') {
                    sectionElement.addClass('hidden');
                    setSectionBadge(sectionElement[0], 'hiddenfromstudents', true, false);
                    replaceActionItem(actionItem, 'i/show',
                        'showfromothers', 'format_' + courseformat, 'show');
                } else {
                    setSectionBadge(sectionElement[0], 'hiddenfromstudents', false, false);
                    sectionElement.removeClass('hidden');
                    replaceActionItem(actionItem, 'i/hide',
                        'hidefromothers', 'format_' + courseformat, 'hide');
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
                // Modify course state.
                const section = courseeditor.state.section.get(sectionid);
                if (section !== undefined) {
                    courseeditor.dispatch('sectionState', [sectionid]);
                }
            } else if (action === 'setmarker') {
                var oldmarker = $(SELECTOR.SECTIONLI + '.current'),
                    oldActionItem = oldmarker.find(SELECTOR.SECTIONACTIONMENU + ' ' + 'a[data-action=removemarker]');
                oldmarker.removeClass('current');
                replaceActionItem(oldActionItem, 'i/marker',
                    'highlight', 'core', 'setmarker');
                sectionElement.addClass('current');
                replaceActionItem(actionItem, 'i/marked',
                    'highlightoff', 'core', 'removemarker');
                courseeditor.dispatch('legacySectionAction', action, sectionid);
                setSectionBadge(sectionElement[0], 'iscurrent', true, true);
            } else if (action === 'removemarker') {
                sectionElement.removeClass('current');
                replaceActionItem(actionItem, 'i/marker',
                    'highlight', 'core', 'setmarker');
                courseeditor.dispatch('legacySectionAction', action, sectionid);
                setSectionBadge(sectionElement[0], 'iscurrent', false, true);
            }
        };

        /**
         * Get the focused element path in an activity if any.
         *
         * This method is used to restore focus when the activity HTML is refreshed.
         * Only the main course editor elements can be refocused as they are always present
         * even if the activity content changes.
         *
         * @param {String} id the element id the activity element
         * @return {String|undefined} the inner path of the focused element or undefined
         */
        const getActivityFocusedElement = function(id) {
            const element = document.getElementById(id);
            if (!element || !element.contains(document.activeElement)) {
                return undefined;
            }
            // Check if the actions menu toggler is focused.
            if (element.querySelector(SELECTOR.ACTIONAREA).contains(document.activeElement)) {
                return `${SELECTOR.ACTIONAREA} [tabindex="0"]`;
            }
            // Return the current element id if any.
            if (document.activeElement.id) {
                return `#${document.activeElement.id}`;
            }
            return undefined;
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
                // Check if the current focused element is inside the activity.
                let focusedPath = getActivityFocusedElement(id);
                // Find the existing element with the same id and replace its contents with new html.
                $(SELECTOR.ACTIVITYLI + '#' + id).replaceWith(activityHTML);
                // Initialise action menu.
                initActionMenu(id);
                // Re-focus the previous elements.
                if (focusedPath) {
                    const newItem = document.getElementById(id);
                    newItem.querySelector(focusedPath)?.focus();
                }

            });
        };

        /**
         * Performs an action on a module (moving, deleting, duplicating, hiding, etc.)
         *
         * @param {JQuery} sectionElement section element we perform action on
         * @param {Nunmber} sectionid
         * @param {JQuery} target the element (menu item) that was clicked
         * @param {String} courseformat
         * @return {boolean} true the action call is sent to the server or false if it is ignored.
         */
        var editSection = function(sectionElement, sectionid, target, courseformat) {
            var action = target.attr('data-action'),
                sectionreturn = target.attr('data-sectionreturn') ? target.attr('data-sectionreturn') : null;

            // Filter direct component handled actions.
            if (courseeditor.supportComponents && componentActions.includes(action)) {
                return false;
            }

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
                        defaultEditSectionHandler(sectionElement, target, data, courseformat, sectionid);
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
            return true;
        };

        /**
         * Sets the section badge in the section header.
         *
         * @param {JQuery} sectionElement section element we perform action on
         * @param {String} badgetype the type of badge this is for
         * @param {bool} add true to add, false to remove
         * @param {boolean} removeOther in case of adding a badge, whether to remove all other.
         */
        var setSectionBadge = function(sectionElement, badgetype, add, removeOther) {
            const sectionbadges = sectionElement.querySelector(SELECTOR.SECTIONBADGES);
            if (!sectionbadges) {
                return;
            }
            const badge = sectionbadges.querySelector('[data-type="' + badgetype + '"]');
            if (!badge) {
                return;
            }
            if (add) {
                if (removeOther) {
                    document.querySelectorAll('[data-type="' + badgetype + '"]').forEach((b) => {
                        b.classList.add('d-none');
                    });
                }
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
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
                },
                /**
                 * Update the course state when some cm is moved via YUI.
                 * @param {*} params
                 */
                updateMovedCmState: (params) => {
                    const state = courseeditor.state;

                    // Update old section.
                    const cm = state.cm.get(params.cmid);
                    if (cm !== undefined) {
                        courseeditor.dispatch('sectionState', [cm.sectionid]);
                    }
                    // Update cm state.
                    courseeditor.dispatch('cmState', [params.cmid]);
                },
                /**
                 * Update the course state when some section is moved via YUI.
                 */
                updateMovedSectionState: () => {
                    courseeditor.dispatch('courseState');
                },
            });
        });

        // From Moodle 4.0 all edit actions are being re-implemented as state mutation.
        // This means all method from this "actions" module will be deprecated when all the course
        // interface is migrated to reactive components.
        // Most legacy actions did not provide enough information to regenarate the course so they
        // use the mutations courseState, sectionState and cmState to get the updated state from
        // the server. However, some activity actions where we can prevent an extra webservice
        // call by implementing an adhoc mutation.
        courseeditor.addMutations({
            /**
             * Compatibility function to update Moodle 4.0 course state using legacy actions.
             *
             * This method only updates some actions which does not require to use cmState mutation
             * to get updated data form the server.
             *
             * @param {Object} statemanager the current state in read write mode
             * @param {String} action the performed action
             * @param {Number} cmid the affected course module id
             * @param {Array} affectedids all affected cm ids (for duplicate action)
             */
            legacyActivityAction: function(statemanager, action, cmid, affectedids) {

                const state = statemanager.state;
                const cm = state.cm.get(cmid);
                if (cm === undefined) {
                    return;
                }
                const section = state.section.get(cm.sectionid);
                if (section === undefined) {
                    return;
                }

                // Send the element is locked.
                courseeditor.dispatch('cmLock', [cm.id], true);

                // Now we do the real mutation.
                statemanager.setReadOnly(false);

                // This unlocked will take effect when the read only is restored.
                cm.locked = false;

                switch (action) {
                    case 'delete':
                        // Remove from section.
                        section.cmlist = section.cmlist.reduce(
                            (cmlist, current) => {
                                if (current != cmid) {
                                    cmlist.push(current);
                                }
                                return cmlist;
                            },
                            []
                        );
                        // Delete form list.
                        state.cm.delete(cmid);
                        break;

                    case 'hide':
                    case 'show':
                    case 'duplicate':
                        courseeditor.dispatch('cmState', affectedids);
                        break;
                }
                statemanager.setReadOnly(true);
            },
            legacySectionAction: function(statemanager, action, sectionid) {

                const state = statemanager.state;
                const section = state.section.get(sectionid);
                if (section === undefined) {
                    return;
                }

                // Send the element is locked. Reactive events are only triggered when the state
                // read only mode is restored. We want to notify the interface the element is
                // locked so we need to do a quick lock operation before performing the rest
                // of the mutation.
                statemanager.setReadOnly(false);
                section.locked = true;
                statemanager.setReadOnly(true);

                // Now we do the real mutation.
                statemanager.setReadOnly(false);

                // This locked will take effect when the read only is restored.
                section.locked = false;

                switch (action) {
                    case 'setmarker':
                        // Remove previous marker.
                        state.section.forEach((current) => {
                            if (current.id != sectionid) {
                                current.current = false;
                            }
                        });
                        section.current = true;
                        break;

                    case 'removemarker':
                        section.current = false;
                        break;
                }
                statemanager.setReadOnly(true);
            },
        });

        return /** @alias module:core_course/actions */ {

            /**
             * Initialises course page
             *
             * @method init
             * @param {String} courseformat name of the current course format (for fetching strings)
             */
            initCoursePage: function(courseformat) {

                formatname = courseformat;

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

                    if (actionItem.attr('data-action') === 'permalink') {
                        e.preventDefault();
                        ModalCopyToClipboard.create({
                            text: actionItem.attr('href'),
                        }, str.get_string('sectionlink', 'course')
                        );
                        return;
                    }

                    let isExecuted = true;
                    if (actionItem.attr('data-confirm')) {
                        // Action requires confirmation.
                        confirmEditSection(actionItem.attr('data-confirm'), function() {
                            isExecuted = editSection(sectionElement, sectionId, actionItem, courseformat);
                        });
                    } else {
                        isExecuted = editSection(sectionElement, sectionId, actionItem, courseformat);
                    }
                    // Prevent any other module from capturing the action if it is already in execution.
                    if (isExecuted) {
                        e.preventDefault();
                    }
                });

                // The section and activity names are edited using inplace editable.
                // The "update" jQuery event must be captured in order to update the course state.
                $('body').on('updated', `${SELECTOR.SECTIONITEM} [data-inplaceeditable]`, function(e) {
                    if (e.ajaxreturn && e.ajaxreturn.itemid) {
                        const state = courseeditor.state;
                        const section = state.section.get(e.ajaxreturn.itemid);
                        if (section !== undefined) {
                            courseeditor.dispatch('sectionState', [e.ajaxreturn.itemid]);
                        }
                    }
                });
                $('body').on('updated', `${SELECTOR.ACTIVITYLI} [data-inplaceeditable]`, function(e) {
                    if (e.ajaxreturn && e.ajaxreturn.itemid) {
                        courseeditor.dispatch('cmState', [e.ajaxreturn.itemid]);
                    }
                });

                // Component-based formats don't use modals to create sections.
                if (courseeditor.supportComponents && componentActions.includes('addSection')) {
                    return;
                }

                // Add a handler for "Add sections" link to ask for a number of sections to add.
                const trigger = $(SELECTOR.ADDSECTIONS);
                const modalTitle = trigger.attr('data-add-sections');
                const newSections = trigger.attr('data-new-sections');
                str.get_string('numberweeks')
                .then(function(strNumberSections) {
                    var modalBody = $('<div><label for="add_section_numsections"></label> ' +
                        '<input id="add_section_numsections" type="number" min="1" max="' + newSections + '" value="1"></div>');
                    modalBody.find('label').html(strNumberSections);

                    return modalBody.html();
                })
                .then((body) => ModalSaveCancel.create({
                    body,
                    title: modalTitle,
                }))
                .then(function(modal) {
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

                    trigger.on('click', (e) => {
                        e.preventDefault();
                        modal.show();
                    });

                    return modal;
                })
                .catch(notification.exception);
            },

            /**
             * Replaces a section action menu item with another one (for example Show->Hide, Set marker->Remove marker)
             *
             * This method can be used by course formats in their listener to the coursesectionedited event
             *
             * @deprecated since Moodle 3.9
             * @param {JQuery} sectionelement
             * @param {String} selector CSS selector inside the section element, for example "a[data-action=show]"
             * @param {String} image new image name ("i/show", "i/hide", etc.)
             * @param {String} stringname new string for the action menu item
             * @param {String} stringcomponent
             * @param {String} newaction new value for data-action attribute of the link
             */
            replaceSectionActionItem: function(sectionelement, selector, image, stringname,
                                                    stringcomponent, newaction) {
                log.debug('replaceSectionActionItem() is deprecated and will be removed.');
                var actionitem = sectionelement.find(SELECTOR.SECTIONACTIONMENU + ' ' + selector);
                replaceActionItem(actionitem, image, stringname, stringcomponent, newaction);
            },
            // Method to refresh a module.
            refreshModule,
            refreshSection,
        };
    });
