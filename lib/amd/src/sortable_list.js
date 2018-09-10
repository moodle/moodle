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
 * A javascript module to handle list items drag and drop
 *
 * Example of usage:
 *
 * define(['jquery', 'core/sortable_list'], function($, sortableList) {
 *     sortableList.init({
 *         listSelector: 'ul.my-awesome-list', // mandatory, CSS selector for the list (usually <ul> or <tbody>)
 *         moveHandlerSelector: '.draghandle'  // CSS selector of the crossarrow handle. Make sure that this
 *         element can handle keypress and mouse click events for displaying accessible move popup.
 *     });
 *     $('ul.my-awesome-list > *').on('sortablelist-drop', function(evt, info) {
 *         console.log(info);
 *     });
 * }
 *
 * More details: https://docs.moodle.org/dev/Sortable_list
 *
 * For the full list of possible parameters see var defaultParameters below.
 *
 * The following jQuery events are fired:
 * - sortablelist-dragstart : when user started dragging a list element
 * - sortablelist-drag : when user dragged a list element to a new position
 * - sortablelist-drop : when user dropped a list element
 * - sortablelist-dragend : when user finished dragging - either fired right after dropping or
 *                          if "Esc" was pressed during dragging
 *
 * @module     core/sortable_list
 * @class      sortable_list
 * @package    core
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/log', 'core/autoscroll', 'core/str', 'core/modal_factory', 'core/modal_events', 'core/notification'],
function($, log, autoScroll, str, ModalFactory, ModalEvents, Notification) {

    /**
     * Default parameters
     *
     * @property {String} listSelector                CSS selector for sortable lists, must be specified during initialization.
     * @property {String} moveHandlerSelector         CSS selector for a drag handle. By default the whole item is a handle.
     *                                                Without drag handle sorting is not accessible!
     * @property {Boolean|Function} isHorizontal      Set to true if the list is horizontal
     *                                                (can also be a callback with list as an argument)
     * @property {Boolean} autoScroll                 Engages autoscroll module for automatic vertical scrolling of the whole page
     * @property {Function} elementNameCallback       Should return a string or Promise. Used for move dialogue title and
     *                                                destination name
     * @property {Function} destinationNameCallback   Callback that returns a string or Promise with the label
     *                                                for the move destination
     * @property {Function} moveDialogueTitleCallback Should return a string or Promise. Used to form move dialogue title
     *
     * @private
     * @type {Object}
     */
    var defaultParameters = {
        listSelector: null,
        moveHandlerSelector: null,
        isHorizontal: false,
        autoScroll: true,
        elementNameCallback: function(element) {
            return element.text();
        },
        destinationNameCallback: function(parentElement, afterElement) {
            if (!afterElement.length) {
                return str.get_string('movecontenttothetop', 'moodle');
            } else {
                return getElementName(afterElement)
                    .then(function(name) {
                        return str.get_string('movecontentafter', 'moodle', name);
                    });
            }
        },
        moveDialogueTitleCallback: function(element) {
            return getElementName(element).then(function(name) {
                return str.get_string('movecontent', 'moodle', name);
            });
        }
    };

    /**
     * Class names for different elements that may be changed during sorting
     *
     * @private
     * @type {Object}
     */
    var CSS = {
        keyboardDragClass: 'dragdrop-keyboard-drag', /* Class of the list of destinations in the popup */
        isDraggedClass: 'sortable-list-is-dragged', /* Class added to the element that is dragged. */
        currentPositionClass: 'sortable-list-current-position', /* Class added to the current position of a dragged element. */
        sourceListClass: 'sortable-list-source', /* Class added to the list where dragging was started from. */
        targetListClass: 'sortable-list-target', /* Class added to all lists where item can be dropped. */
        overElementClass: 'sortable-list-over-element' /* Class added to the list element when the dragged element is above it. */
    };

    /**
     * Stores parameters of the currently dragged item
     *
     * @private
     * @type {Object}
     */
    var params = {};

    /**
     * Stores information about currently dragged item
     *
     * @private
     * @type {Object}
     */
    var info = null;

    /**
     * Stores the proxy object
     *
     * @private
     * @type {jQuery}
     */
    var proxy;

    /**
     * Stores initial position of the proxy
     *
     * @private
     * @type {Object}
     */
    var proxyDelta;

    /**
     * Counter of drag events
     *
     * @private
     * @type {Number}
     */
    var dragCounter = 0;

    /**
     * Resets the temporary classes assigned during dragging
     * @private
     */
    var resetDraggedClasses = function() {
        var lists = $(params.listSelector);
        lists.children()
            .removeClass(params.isDraggedClass)
            .removeClass(params.currentPositionClass)
            .removeClass(params.overElementClass);
        lists
            .removeClass(params.targetListClass)
            .removeClass(params.sourceListClass);
        if (proxy) {
            proxy.remove();
            proxy = $();
        }
    };

    /**
     * {Event} stores the last event that had pageX and pageY defined
     * @private
     */
    var lastEvent;

    /**
     * Calculates evt.pageX, evt.pageY, evt.clientX and evt.clientY
     *
     * For touch events pageX and pageY are taken from the first touch;
     * For the emulated mousemove event they are taken from the last real event.
     *
     * @private
     * @param {Event} evt
     */
    var calculatePositionOnPage = function(evt) {

        if (evt.originalEvent && evt.originalEvent.touches && evt.originalEvent.touches[0] !== undefined) {
            // This is a touchmove or touchstart event, get position from the first touch position.
            var touch = evt.originalEvent.touches[0];
            evt.pageX = touch.pageX;
            evt.pageY = touch.pageY;
        }

        if (evt.pageX === undefined) {
            // Information is not present in case of touchend or when event was emulated by autoScroll.
            // Take the absolute mouse position from the last event.
            evt.pageX = lastEvent.pageX;
            evt.pageY = lastEvent.pageY;
        } else {
            lastEvent = evt;
        }

        if (evt.clientX === undefined) {
            // If not provided in event calculate relative mouse position.
            evt.clientX = Math.round(evt.pageX - $(window).scrollLeft());
            evt.clientY = Math.round(evt.pageY - $(window).scrollTop());
        }
    };

    /**
     * Handler from dragstart event
     *
     * @private
     * @param {Event} evt
     */
    var dragStartHandler = function(evt) {
        params = evt.data.params;
        if (info !== null) {
            if (info.type === 'click') {
                // Ignore double click.
                return;
            }
            // Mouse down or touch while already dragging, cancel previous dragging.
            moveElement(info.sourceList, info.sourceNextElement);
            finishDragging();
        }

        if (evt.type === 'mousedown' && evt.which !== 1) {
            // We only need left mouse click.
            return;
        }

        calculatePositionOnPage(evt);
        var movedElement = $(evt.currentTarget);

        // Check that we grabbed the element by the handle.
        if (params.moveHandlerSelector !== null) {
            if (!$(evt.target).closest(params.moveHandlerSelector, movedElement).length) {
                return;
            }
        }

        evt.stopPropagation();
        evt.preventDefault();

        // Information about moved element with original location.
        // This object is passed to event observers.
        dragCounter++;
        info = {
            element: movedElement,
            sourceNextElement: movedElement.next(),
            sourceList: movedElement.parent(),
            targetNextElement: movedElement.next(),
            targetList: movedElement.parent(),
            type: evt.type,
            dropped: false,
            startX: evt.pageX,
            startY: evt.pageY,
            startTime: new Date().getTime()
        };

        $(params.listSelector).addClass(params.targetListClass);

        var offset = movedElement.offset();
        movedElement.addClass(params.currentPositionClass);
        proxyDelta = {x: offset.left - evt.pageX, y: offset.top - evt.pageY};
        proxy = $();
        var thisDragCounter = dragCounter;
        setTimeout(function() {
            if (info === null || info.type === 'click' || info.type === 'keypress' || dragCounter !== thisDragCounter) {
                return;
            }

            // Create a proxy - the copy of the dragged element that moves together with a mouse.
            createProxy();
        }, 500);

        // Start drag.
        $('body').on('mousemove touchmove mouseup touchend', dragHandler);
        $('body').on('keypress', dragcancelHandler);

        // Start autoscrolling. Every time the page is scrolled emulate the mousemove event.
        if (params.autoScroll) {
            autoScroll.start(function () {
                $('body').trigger('mousemove');
            });
        }

        executeCallback('dragstart');
    };

    /**
     * Creates a "proxy" object - a copy of the element that is being moved that always follows the mouse
     * @private
     */
    var createProxy = function() {
        proxy = info.element.clone();
        info.sourceList.append(proxy);
        proxy.removeAttr('id').removeClass(params.currentPositionClass)
            .addClass(params.isDraggedClass).css({position: 'fixed'});
        proxy.offset({top: proxyDelta.y + lastEvent.pageY, left: proxyDelta.x + lastEvent.pageX});
    };

    /**
     * Handler for click event - when user clicks on the drag handler or presses Enter on keyboard
     *
     * @private
     * @param {Event} evt
     */
    var clickHandler = function(evt) {
        if (evt.type === 'keypress' && evt.originalEvent.keyCode !== 13 && evt.originalEvent.keyCode !== 32) {
            return;
        }
        if (info !== null && info.type === 'click') {
            // Ignore double click.
            return;
        }
        evt.preventDefault();
        evt.stopPropagation();
        params = evt.data.params;

        // Find the element that this draghandle belongs to.
        var sourceList = $(evt.currentTarget).closest(params.listSelector),
            movedElement = sourceList.children().filter(function() {
                return $.contains(this, evt.currentTarget);
            });
        if (!movedElement.length) {
            return;
        }

        // Store information about moved element with original location.
        dragCounter++;
        info = {
            element: movedElement,
            sourceNextElement: movedElement.next(),
            sourceList: sourceList,
            targetNextElement: movedElement.next(),
            targetList: sourceList,
            dropped: false,
            type: evt.type,
            startTime: new Date().getTime()
        };

        executeCallback('dragstart');
        displayMoveDialogue();
    };

    /**
     * Finds the position of the mouse inside the element - on the top, on the bottom, on the right or on the left\
     *
     * Used to determine if the moved element should be moved after or before the current element
     *
     * @private
     * @param {Number} pageX
     * @param {Number} pageY
     * @param {jQuery} element
     * @returns {(Object|null)}
     */
    var getPositionInNode = function(pageX, pageY, element) {
        if (!element.length) {
            return null;
        }
        var node = element[0],
            offset = 0,
            rect = node.getBoundingClientRect(),
            y = pageY - (rect.top + window.scrollY),
            x = pageX - (rect.left + window.scrollX);
        if (x >= -offset && x <= rect.width + offset && y >= -offset && y <= rect.height + offset) {
            return {
                x: x,
                y: y,
                xRatio: rect.width ? (x / rect.width) : 0,
                yRatio: rect.height ? (y / rect.height) : 0
            };
        }
        return null;
    };

    /**
     * Callback for filter that checks that current element is not proxy
     *
     * @private
     * @return {boolean}
     */
    var isNotProxy = function() {
        return !proxy || !proxy.length || this !== proxy[0];
    };

    /**
     * Check if list is horizontal
     *
     * @param {jQuery} element
     * @return {Boolean}
     */
    var isListHorizontal = function(element) {
        var isHorizontal = params.isHorizontal;
        if (isHorizontal === true || isHorizontal === false) {
            return isHorizontal;
        }
        return isHorizontal(element);
    };

    /**
     * Handler for events mousemove touchmove mouseup touchend
     *
     * @private
     * @param {Event} evt
     */
    var dragHandler = function(evt) {

        calculatePositionOnPage(evt);

        // We can not use evt.target here because it will most likely be our proxy.
        // Move the proxy out of the way so we can find the element at the current mouse position.
        proxy.offset({top: -1000, left: -1000});
        // Find the element at the current mouse position.
        var element = $(document.elementFromPoint(evt.clientX, evt.clientY));

        // Find the list element and the list over the mouse position.
        var current = element.closest('.' + params.targetListClass + ' > :not(.' + params.isDraggedClass + ')'),
            currentList = element.closest('.' + params.targetListClass);

        // Add the specified class to the list element we are hovering.
        $('.' + params.overElementClass).removeClass(params.overElementClass);
        current.addClass(params.overElementClass);

        // Move proxy to the current position.
        proxy.offset({top: proxyDelta.y + evt.pageY, left: proxyDelta.x + evt.pageX});

        if (currentList.length && !currentList.children().filter(isNotProxy).length) {
            // Mouse is over an empty list.
            moveElement(currentList, $());
        } else if (current.length === 1 && !info.element.find(current[0]).length) {
            // Mouse is over an element in a list - find whether we should move the current position
            // above or below this element.
            var coordinates = getPositionInNode(evt.pageX, evt.pageY, current);
            if (coordinates) {
                var parent = current.parent(),
                    ratio = isListHorizontal(parent) ? coordinates.xRatio : coordinates.yRatio,
                    subList = current.find('.' + params.targetListClass),
                    isNotCurrent = function() {
                        return this !== info.element[0];
                    },
                    subListEmpty = !subList.children().filter(isNotProxy).filter(isNotCurrent).length;
                if (subList.length && subListEmpty && ratio > 0.2 && ratio < 0.8) {
                    // This is an element that is a parent of an empty list and we are around the middle of this element.
                    // Treat it as if we are over this empty list.
                    moveElement(subList, $());
                } else if (ratio > 0.5) {
                    // Insert after this element.
                    moveElement(parent, current.next().filter(isNotProxy));
                } else {
                    // Insert before this element.
                    moveElement(parent, current);
                }
            }
        }

        if (evt.type === 'mouseup' || evt.type === 'touchend') {
            // Drop the moved element.
            info.endX = evt.pageX;
            info.endY = evt.pageY;
            info.endTime = new Date().getTime();
            info.dropped = true;
            executeCallback('drop');
            finishDragging();
        }
    };

    /**
     * Moves the current position of the dragged element
     *
     * @private
     * @param {jQuery} parentElement
     * @param {jQuery} beforeElement
     */
    var moveElement = function(parentElement, beforeElement) {
        var dragEl = info.element;
        if (beforeElement.length && beforeElement[0] === dragEl[0]) {
            // Insert before the current position of the dragged element - nothing to do.
            return;
        }
        if (parentElement[0] === info.targetList[0] &&
                beforeElement.length === info.targetNextElement.length &&
                beforeElement[0] === info.targetNextElement[0]) {
            // Insert in the same location as the current position - nothing to do.
            return;
        }

        if (beforeElement.length) {
            // Move the dragged element before the specified element.
            parentElement[0].insertBefore(dragEl[0], beforeElement[0]);
        } else if (proxy && proxy.parent().length && proxy.parent()[0] === parentElement[0]) {
            // We need to move to the end of the list but the last element in this list is a proxy.
            // Always leave the proxy in the end of the list.
            parentElement[0].insertBefore(dragEl[0], proxy[0]);
        } else {
            // Insert in the end of a list (when proxy is in another list).
            parentElement[0].appendChild(dragEl[0]);
        }

        // Save the current position of the dragged element in the list.
        info.targetList = parentElement;
        info.targetNextElement = beforeElement;
        executeCallback('drag');
    };

    /**
     * Finish dragging (when dropped or cancelled).
     * @private
     */
    var finishDragging = function() {
        resetDraggedClasses();
        if (params.autoScroll) {
            autoScroll.stop();
        }
        $('body').off('mousemove touchmove mouseup touchend', dragHandler);
        $('body').off('keypress', dragcancelHandler);
        executeCallback('dragend');
        info = null;
    };

    /**
     * Executes callback specified in sortable list parameters
     *
     * @private
     * @param {String} eventName
     */
    var executeCallback = function(eventName) {
        info.element.trigger('sortablelist-' + eventName, info);
    };

    /**
     * Handler from keypress event (cancel dragging when Esc is pressed)
     *
     * @private
     * @param {Event} evt
     */
    var dragcancelHandler = function(evt) {
        if (evt.type !== 'keypress' || evt.originalEvent.keyCode !== 27) {
            // Only cancel dragging when Esc was pressed.
            return;
        }
        // Dragging was cancelled. Return item to the original position.
        moveElement(info.sourceList, info.sourceNextElement);
        finishDragging();
    };

    /**
     * Helper method to convert a string to a promise
     *
     * @private
     * @param {(String|Promise)} value
     * @return {Promise}
     */
    var convertToPromise = function(value) {
        var p = value;
        if (typeof value !== 'object' || !value.hasOwnProperty('then')) {
            p = $.Deferred();
            p.resolve(value);
        }
        return p;
    };

    /**
     * Returns the name of the current element to be used in the move dialogue
     *
     * @private
     * @param {jQuery} element
     * @return {Promise}
     */
    var getElementName = function(element) {
        return convertToPromise(params.elementNameCallback(element));
    };

    /**
     * Returns the label for the potential move destination, i.e. "After ElementX" or "To the top of the list"
     *
     * Note that we use "after" in the label for better UX
     *
     * @private
     * @param {jQuery} parentElement
     * @param {jQuery} afterElement
     * @return {Promise}
     */
    var getDestinationName = function(parentElement, afterElement) {
        return convertToPromise(params.destinationNameCallback(parentElement, afterElement));
    };

    /**
     * Returns the title for the move dialogue ("Move elementY")
     *
     * @private
     * @param {jQuery} element
     * @return {Promise}
     */
    var getMoveDialogueTitle = function(element) {
        return convertToPromise(params.moveDialogueTitleCallback(element));
    };

    /**
     * Returns the list of possible move destinations with their onclick handlers
     *
     * @private
     * @param {Modal} modal
     * @return {jQuery}
     */
    var getDestinationsList = function(modal) {
        var addedLists = [],
            targets = $(params.listSelector),
            list = $('<ul/>').addClass(params.keyboardDragClass),
            createLink = function(parentElement, beforeElement, afterElement) {
                if (beforeElement.is(info.element) || afterElement.is(info.element)) {
                    return;
                }
                var li = $('<li/>').appendTo(list);
                var a = $('<a href="#"/>')
                    .click(function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        moveElement(parentElement, beforeElement);
                        info.endTime = new Date().getTime();
                        info.dropped = true;
                        info.element.find(params.moveHandlerSelector).focus();
                        executeCallback('drop');
                        modal.hide();
                    })
                    .appendTo(li);
                getDestinationName(parentElement, afterElement)
                    .then(function(txt) {
                        a.text(txt);
                        return txt;
                    }).catch(Notification.exception);
            },
            addList = function() {
                if ($.inArray(this, addedLists) !== -1) {
                    return;
                }
                addedLists.push(this);
                var list = $(this),
                    children = list.children();
                children.each(function() {
                    var element = $(this);
                    createLink(list, element, element.prev());
                    // Add all nested lists.
                    element.find(targets).each(addList);
                });
                createLink(list, $(), children.last());
            };
        targets.each(addList);
        return list;
    };

    /**
     * Displays the dialogue to move element.
     * @private
     */
    var displayMoveDialogue = function() {
        ModalFactory.create({
            type: ModalFactory.types.CANCEL,
            title: getMoveDialogueTitle(info.element)
        }).then(function(modal) {
            modal.getRoot().on(ModalEvents.hidden, function() {
                // Always destroy when hidden, it is generated dynamically each time.
                modal.destroy();
                finishDragging();
            });
            modal.getBody().append(getDestinationsList(modal));
            modal.setLarge();
            modal.show();
            return modal;
        });
    };

    return {
        /**
         * Initialise sortable list.
         *
         * @param {Object} params Parameters for the list. See defaultParameters above for examples.
         */
        init: function(params) {
            if (typeof params.listSelector === 'undefined') {
                log.error('Parameter listSelector must be specified');
                return;
            }
            params = $.extend({}, defaultParameters, CSS, params);
            $(params.listSelector).on('mousedown touchstart', '> *', {params: params}, dragStartHandler);
            if (params.moveHandlerSelector !== null) {
                $(params.listSelector).on('click keypress', params.moveHandlerSelector, {params: params}, clickHandler);
            }
        }
    };
});
