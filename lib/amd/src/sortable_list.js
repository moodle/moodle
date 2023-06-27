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
 * Create a list (for example `<ul>` or `<tbody>`) where each draggable element has a drag handle.
 * The best practice is to use the template core/drag_handle:
 * $OUTPUT->render_from_template('core/drag_handle', ['movetitle' => get_string('movecontent', 'moodle', ELEMENTNAME)]);
 *
 * Attach this JS module to this list:
 *
 * Space between define and ( critical in comment but not allowed in code in order to function
 * correctly with Moodle's requirejs.php
 *
 * More details: https://docs.moodle.org/dev/Sortable_list
 *
 * For the full list of possible parameters see var defaultParameters below.
 *
 * The following jQuery events are fired:
 * - SortableList.EVENTS.DRAGSTART : when user started dragging a list element
 * - SortableList.EVENTS.DRAG : when user dragged a list element to a new position
 * - SortableList.EVENTS.DROP : when user dropped a list element
 * - SortableList.EVENTS.DROPEND : when user finished dragging - either fired right after dropping or
 *                          if "Esc" was pressed during dragging
 *
 * @example
 * define (['jquery', 'core/sortable_list'], function($, SortableList) {
 *     var list = new SortableList('ul.my-awesome-list'); // source list (usually <ul> or <tbody>) - selector or element
 *
 *     // Listen to the events when element is dragged.
 *     $('ul.my-awesome-list > *').on(SortableList.EVENTS.DROP, function(evt, info) {
 *         console.log(info);
 *     });
 *
 *     // Advanced usage. Overwrite methods getElementName, getDestinationName, moveDialogueTitle, for example:
 *     list.getElementName = function(element) {
 *         return $.Deferred().resolve(element.attr('data-name'));
 *     }
 * });
 *
 * @module     core/sortable_list
 * @class      core/sortable_list
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/log', 'core/autoscroll', 'core/str', 'core/modal_factory', 'core/modal_events', 'core/notification'],
function($, log, autoScroll, str, ModalFactory, ModalEvents, Notification) {

    /**
     * Default parameters
     *
     * @private
     * @type {Object}
     */
    var defaultParameters = {
        targetListSelector: null,
        moveHandlerSelector: '[data-drag-type=move]',
        isHorizontal: false,
        autoScroll: true
    };

    /**
     * Class names for different elements that may be changed during sorting
     *
     * @private
     * @type {Object}
     */
    var CSS = {
        keyboardDragClass: 'dragdrop-keyboard-drag',
        isDraggedClass: 'sortable-list-is-dragged',
        currentPositionClass: 'sortable-list-current-position',
        sourceListClass: 'sortable-list-source',
        targetListClass: 'sortable-list-target',
        overElementClass: 'sortable-list-over-element'
    };

    /**
     * Test the browser support for options objects on event listeners.
     * @return {Boolean}
     */
    var eventListenerOptionsSupported = function() {
        var passivesupported = false,
            options,
            testeventname = "testpassiveeventoptions";

        // Options support testing example from:
        // https://developer.mozilla.org/en-US/docs/Web/API/EventTarget/addEventListener

        try {
            options = Object.defineProperty({}, "passive", {
                get: function() {
                    passivesupported = true;
                }
            });

            // We use an event name that is not likely to conflict with any real event.
            document.addEventListener(testeventname, options, options);
            // We remove the event listener as we have tested the options already.
            document.removeEventListener(testeventname, options, options);
        } catch (err) {
            // It's already false.
            passivesupported = false;
        }
        return passivesupported;
    };

    /**
     * Allow to create non-passive touchstart listeners and prevent page scrolling when dragging
     * From: https://stackoverflow.com/a/48098097
     *
     * @param {string} eventname
     * @returns {object}
     */
    var registerNotPassiveListeners = function(eventname) {
        return {
            setup: function(x, ns, handle) {
                if (ns.includes('notPassive')) {
                    this.addEventListener(eventname, handle, {passive: false});
                    return true;
                } else {
                    return false;
                }
            }
        };
    };

    if (eventListenerOptionsSupported) {
        $.event.special.touchstart = registerNotPassiveListeners('touchstart');
        $.event.special.touchmove = registerNotPassiveListeners('touchmove');
        $.event.special.touchend = registerNotPassiveListeners('touchend');
    }

    /**
     * Initialise sortable list.
     *
     * @param {(String|jQuery|Element)} root JQuery/DOM element representing sortable list (i.e. <ul>, <tbody>) or CSS selector
     * @param {Object} config Parameters for the list. See defaultParameters above for examples.
     * @param {(String|jQuery|Element)} config.targetListSelector target lists, by default same as root
     * @param {String} config.moveHandlerSelector  CSS selector for a drag handle. By default '[data-drag-type=move]'
     * @param {String} config.listSelector   CSS selector for target lists. By default the same as root
     * @param {(Boolean|Function)} config.isHorizontal Set to true if the list is horizontal (can also be a callback
     *                                                 with list as an argument)
     * @param {Boolean} config.autoScroll Engages autoscroll module for automatic vertical scrolling of the whole page,
     *                                    by default true
     */
    var SortableList = function(root, config) {

        this.info = null;
        this.proxy = null;
        this.proxyDelta = null;
        this.dragCounter = 0;
        this.lastEvent = null;

        this.config = $.extend({}, defaultParameters, config || {});
        this.config.listSelector = root;
        if (!this.config.targetListSelector) {
            this.config.targetListSelector = root;
        }
        if (typeof this.config.listSelector === 'object') {
            // The root is an element on the page. Register a listener for this element.
            $(this.config.listSelector).on('mousedown touchstart.notPassive', $.proxy(this.dragStartHandler, this));
        } else {
            // The root is a CSS selector. Register a listener that picks up the element dynamically.
            $('body').on('mousedown touchstart.notPassive', this.config.listSelector, $.proxy(this.dragStartHandler, this));
        }
        if (this.config.moveHandlerSelector !== null) {
            $('body').on('click keypress', this.config.moveHandlerSelector, $.proxy(this.clickHandler, this));
        }

    };

    /**
     * Events fired by this entity
     *
     * @public
     * @type {Object}
     */
    SortableList.EVENTS = {
        DRAGSTART: 'sortablelist-dragstart',
        DRAG: 'sortablelist-drag',
        DROP: 'sortablelist-drop',
        DRAGEND: 'sortablelist-dragend'
    };

    /**
     * Resets the temporary classes assigned during dragging
     * @private
     */
     SortableList.prototype.resetDraggedClasses = function() {
        var classes = [
            CSS.isDraggedClass,
            CSS.currentPositionClass,
            CSS.overElementClass,
            CSS.targetListClass,
        ];
        for (var i in classes) {
            $('.' + classes[i]).removeClass(classes[i]);
        }
        if (this.proxy) {
            this.proxy.remove();
            this.proxy = $();
        }
    };

    /**
     * Calculates evt.pageX, evt.pageY, evt.clientX and evt.clientY
     *
     * For touch events pageX and pageY are taken from the first touch;
     * For the emulated mousemove event they are taken from the last real event.
     *
     * @private
     * @param {Event} evt
     */
    SortableList.prototype.calculatePositionOnPage = function(evt) {

        if (evt.originalEvent && evt.originalEvent.touches && evt.originalEvent.touches[0] !== undefined) {
            // This is a touchmove or touchstart event, get position from the first touch position.
            var touch = evt.originalEvent.touches[0];
            evt.pageX = touch.pageX;
            evt.pageY = touch.pageY;
        }

        if (evt.pageX === undefined) {
            // Information is not present in case of touchend or when event was emulated by autoScroll.
            // Take the absolute mouse position from the last event.
            evt.pageX = this.lastEvent.pageX;
            evt.pageY = this.lastEvent.pageY;
        } else {
            this.lastEvent = evt;
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
    SortableList.prototype.dragStartHandler = function(evt) {
        if (this.info !== null) {
            if (this.info.type === 'click' || this.info.type === 'touchend') {
                // Ignore double click.
                return;
            }
            // Mouse down or touch while already dragging, cancel previous dragging.
            this.moveElement(this.info.sourceList, this.info.sourceNextElement);
            this.finishDragging();
        }

        if (evt.type === 'mousedown' && evt.which !== 1) {
            // We only need left mouse click. If this is a mousedown event with right/middle click ignore it.
            return;
        }

        this.calculatePositionOnPage(evt);
        var movedElement = $(evt.target).closest($(evt.currentTarget).children());
        if (!movedElement.length) {
            // Can't find the element user wants to drag. They clicked on the list but outside of any element of the list.
            return;
        }

        // Check that we grabbed the element by the handle.
        if (this.config.moveHandlerSelector !== null) {
            if (!$(evt.target).closest(this.config.moveHandlerSelector, movedElement).length) {
                return;
            }
        }

        evt.stopPropagation();
        evt.preventDefault();

        // Information about moved element with original location.
        // This object is passed to event observers.
        this.dragCounter++;
        this.info = {
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

        $(this.config.targetListSelector).addClass(CSS.targetListClass);

        var offset = movedElement.offset();
        movedElement.addClass(CSS.currentPositionClass);
        this.proxyDelta = {x: offset.left - evt.pageX, y: offset.top - evt.pageY};
        this.proxy = $();
        var thisDragCounter = this.dragCounter;
        setTimeout($.proxy(function() {
            // This mousedown event may in fact be a beginning of a 'click' event. Use timeout before showing the
            // dragged object so we can catch click event. When timeout finishes make sure that click event
            // has not happened during this half a second.
            // Verify dragcounter to make sure the user did not manage to do two very fast drag actions one after another.
            if (this.info === null || this.info.type === 'click' || this.info.type === 'keypress'
                    || this.dragCounter !== thisDragCounter) {
                return;
            }

            // Create a proxy - the copy of the dragged element that moves together with a mouse.
            this.createProxy();
        }, this), 500);

        // Start drag.
        $(window).on('mousemove touchmove.notPassive mouseup touchend.notPassive', $.proxy(this.dragHandler, this));
        $(window).on('keypress', $.proxy(this.dragcancelHandler, this));

        // Start autoscrolling. Every time the page is scrolled emulate the mousemove event.
        if (this.config.autoScroll) {
            autoScroll.start(function() {
                $(window).trigger('mousemove');
            });
        }

       this.executeCallback(SortableList.EVENTS.DRAGSTART);
    };

    /**
     * Creates a "proxy" object - a copy of the element that is being moved that always follows the mouse
     * @private
     */
    SortableList.prototype.createProxy = function() {
        this.proxy = this.info.element.clone();
        this.info.sourceList.append(this.proxy);
        this.proxy.removeAttr('id').removeClass(CSS.currentPositionClass)
            .addClass(CSS.isDraggedClass).css({position: 'fixed'});
        this.proxy.offset({top: this.proxyDelta.y + this.lastEvent.pageY, left: this.proxyDelta.x + this.lastEvent.pageX});
    };

    /**
     * Handler for click event - when user clicks on the drag handler or presses Enter on keyboard
     *
     * @private
     * @param {Event} evt
     */
    SortableList.prototype.clickHandler = function(evt) {
        if (evt.type === 'keypress' && evt.originalEvent.keyCode !== 13 && evt.originalEvent.keyCode !== 32) {
            return;
        }
        if (this.info !== null) {
            // Ignore double click.
            return;
        }

        // Find the element that this draghandle belongs to.
        var clickedElement = $(evt.target).closest(this.config.moveHandlerSelector),
            sourceList = clickedElement.closest(this.config.listSelector),
            movedElement = clickedElement.closest(sourceList.children());
        if (!movedElement.length) {
            return;
        }

        evt.preventDefault();
        evt.stopPropagation();

        // Store information about moved element with original location.
        this.dragCounter++;
        this.info = {
            element: movedElement,
            sourceNextElement: movedElement.next(),
            sourceList: sourceList,
            targetNextElement: movedElement.next(),
            targetList: sourceList,
            dropped: false,
            type: evt.type,
            startTime: new Date().getTime()
        };

        this.executeCallback(SortableList.EVENTS.DRAGSTART);
        this.displayMoveDialogue(clickedElement);
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
    SortableList.prototype.getPositionInNode = function(pageX, pageY, element) {
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
     * Check if list is horizontal
     *
     * @param {jQuery} element
     * @return {Boolean}
     */
    SortableList.prototype.isListHorizontal = function(element) {
        var isHorizontal = this.config.isHorizontal;
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
    SortableList.prototype.dragHandler = function(evt) {

        evt.preventDefault();
        evt.stopPropagation();

        this.calculatePositionOnPage(evt);

        // We can not use evt.target here because it will most likely be our proxy.
        // Move the proxy out of the way so we can find the element at the current mouse position.
        this.proxy.offset({top: -1000, left: -1000});
        // Find the element at the current mouse position.
        var element = $(document.elementFromPoint(evt.clientX, evt.clientY));

        // Find the list element and the list over the mouse position.
        var mainElement = this.info.element[0],
            isNotSelf = function() {
                return this !== mainElement;
            },
            current = element.closest('.' + CSS.targetListClass + ' > :not(.' + CSS.isDraggedClass + ')').filter(isNotSelf),
            currentList = element.closest('.' + CSS.targetListClass),
            proxy = this.proxy,
            isNotProxy = function() {
                return !proxy || !proxy.length || this !== proxy[0];
            };

        // Add the specified class to the list element we are hovering.
        $('.' + CSS.overElementClass).removeClass(CSS.overElementClass);
        current.addClass(CSS.overElementClass);

        // Move proxy to the current position.
        this.proxy.offset({top: this.proxyDelta.y + evt.pageY, left: this.proxyDelta.x + evt.pageX});

        if (currentList.length && !currentList.children().filter(isNotProxy).length) {
            // Mouse is over an empty list.
            this.moveElement(currentList, $());
        } else if (current.length === 1 && !this.info.element.find(current[0]).length) {
            // Mouse is over an element in a list - find whether we should move the current position
            // above or below this element.
            var coordinates = this.getPositionInNode(evt.pageX, evt.pageY, current);
            if (coordinates) {
                var parent = current.parent(),
                    ratio = this.isListHorizontal(parent) ? coordinates.xRatio : coordinates.yRatio,
                    subList = current.find('.' + CSS.targetListClass),
                    subListEmpty = !subList.children().filter(isNotProxy).filter(isNotSelf).length;
                if (subList.length && subListEmpty && ratio > 0.2 && ratio < 0.8) {
                    // This is an element that is a parent of an empty list and we are around the middle of this element.
                    // Treat it as if we are over this empty list.
                   this.moveElement(subList, $());
                } else if (ratio > 0.5) {
                    // Insert after this element.
                   this.moveElement(parent, current.next().filter(isNotProxy));
                } else {
                    // Insert before this element.
                   this.moveElement(parent, current);
                }
            }
        }

        if (evt.type === 'mouseup' || evt.type === 'touchend') {
            // Drop the moved element.
            this.info.endX = evt.pageX;
            this.info.endY = evt.pageY;
            this.info.endTime = new Date().getTime();
            this.info.dropped = true;
            this.info.positionChanged = this.hasPositionChanged(this.info);
            var oldinfo = this.info;
            this.executeCallback(SortableList.EVENTS.DROP);
            this.finishDragging();

            if (evt.type === 'touchend'
                    && this.config.moveHandlerSelector !== null
                    && (oldinfo.endTime - oldinfo.startTime < 500)
                    && !oldinfo.positionChanged) {
                // The click event is not triggered on touch screens because we call preventDefault in touchstart handler.
                // If the touchend quickly followed touchstart without moving, consider it a "click".
                this.clickHandler(evt);
            }
        }
    };

    /**
     * Checks if the position of the dragged element in the list has changed
     *
     * @private
     * @param {Object} info
     * @return {Boolean}
     */
    SortableList.prototype.hasPositionChanged = function(info) {
        return info.sourceList[0] !== info.targetList[0] ||
            info.sourceNextElement.length !== info.targetNextElement.length ||
            (info.sourceNextElement.length && info.sourceNextElement[0] !== info.targetNextElement[0]);
    };

    /**
     * Moves the current position of the dragged element
     *
     * @private
     * @param {jQuery} parentElement
     * @param {jQuery} beforeElement
     */
    SortableList.prototype.moveElement = function(parentElement, beforeElement) {
        var dragEl = this.info.element;
        if (beforeElement.length && beforeElement[0] === dragEl[0]) {
            // Insert before the current position of the dragged element - nothing to do.
            return;
        }
        if (parentElement[0] === this.info.targetList[0] &&
                beforeElement.length === this.info.targetNextElement.length &&
                beforeElement[0] === this.info.targetNextElement[0]) {
            // Insert in the same location as the current position - nothing to do.
            return;
        }

        if (beforeElement.length) {
            // Move the dragged element before the specified element.
            parentElement[0].insertBefore(dragEl[0], beforeElement[0]);
        } else if (this.proxy && this.proxy.parent().length && this.proxy.parent()[0] === parentElement[0]) {
            // We need to move to the end of the list but the last element in this list is a proxy.
            // Always leave the proxy in the end of the list.
            parentElement[0].insertBefore(dragEl[0], this.proxy[0]);
        } else {
            // Insert in the end of a list (when proxy is in another list).
            parentElement[0].appendChild(dragEl[0]);
        }

        // Save the current position of the dragged element in the list.
        this.info.targetList = parentElement;
        this.info.targetNextElement = beforeElement;
        this.executeCallback(SortableList.EVENTS.DRAG);
    };

    /**
     * Finish dragging (when dropped or cancelled).
     * @private
     */
    SortableList.prototype.finishDragging = function() {
        this.resetDraggedClasses();
        if (this.config.autoScroll) {
            autoScroll.stop();
        }
        $(window).off('mousemove touchmove.notPassive mouseup touchend.notPassive', $.proxy(this.dragHandler, this));
        $(window).off('keypress', $.proxy(this.dragcancelHandler, this));
        this.executeCallback(SortableList.EVENTS.DRAGEND);
        this.info = null;
    };

    /**
     * Executes callback specified in sortable list parameters
     *
     * @private
     * @param {String} eventName
     */
    SortableList.prototype.executeCallback = function(eventName) {
        this.info.element.trigger(eventName, this.info);
    };

    /**
     * Handler from keypress event (cancel dragging when Esc is pressed)
     *
     * @private
     * @param {Event} evt
     */
    SortableList.prototype.dragcancelHandler = function(evt) {
        if (evt.type !== 'keypress' || evt.originalEvent.keyCode !== 27) {
            // Only cancel dragging when Esc was pressed.
            return;
        }
        // Dragging was cancelled. Return item to the original position.
        this.moveElement(this.info.sourceList, this.info.sourceNextElement);
        this.finishDragging();
    };

    /**
     * Returns the name of the current element to be used in the move dialogue
     *
     * @public
     * @param {jQuery} element
     * @return {Promise}
     */
    SortableList.prototype.getElementName = function(element) {
        return $.Deferred().resolve(element.text());
    };

    /**
     * Returns the label for the potential move destination, i.e. "After ElementX" or "To the top of the list"
     *
     * Note that we use "after" in the label for better UX
     *
     * @public
     * @param {jQuery} parentElement
     * @param {jQuery} afterElement
     * @return {Promise}
     */
    SortableList.prototype.getDestinationName = function(parentElement, afterElement) {
        if (!afterElement.length) {
            return str.get_string('movecontenttothetop', 'moodle');
        } else {
            return this.getElementName(afterElement)
                .then(function(name) {
                    return str.get_string('movecontentafter', 'moodle', name);
                });
        }
    };

    /**
     * Returns the title for the move dialogue ("Move elementY")
     *
     * @public
     * @param {jQuery} element
     * @param {jQuery} handler
     * @return {Promise}
     */
    SortableList.prototype.getMoveDialogueTitle = function(element, handler) {
        if (handler.attr('title')) {
            return $.Deferred().resolve(handler.attr('title'));
        }
        return this.getElementName(element).then(function(name) {
            return str.get_string('movecontent', 'moodle', name);
        });
    };

    /**
     * Returns the list of possible move destinations
     *
     * @private
     * @return {Promise}
     */
    SortableList.prototype.getDestinationsList = function() {
        var addedLists = [],
            targets = $(this.config.targetListSelector),
            destinations = $('<ul/>').addClass(CSS.keyboardDragClass),
            result = $.when().then(function() {
                return destinations;
            }),
            createLink = $.proxy(function(parentElement, beforeElement, afterElement) {
                if (beforeElement.is(this.info.element) || afterElement.is(this.info.element)) {
                    // Can not move before or after itself.
                    return;
                }
                if ($.contains(this.info.element[0], parentElement[0])) {
                    // Can not move to its own child.
                    return;
                }
                result = result
                .then($.proxy(function() {
                    return this.getDestinationName(parentElement, afterElement);
                }, this))
                .then(function(txt) {
                    var li = $('<li/>').appendTo(destinations);
                    var a = $('<a href="#"/>').attr('data-core_sortable_list-quickmove', 1).appendTo(li);
                    a.data('parent-element', parentElement).data('before-element', beforeElement).text(txt);
                    return destinations;
                });
            }, this),
            addList = function() {
                // Destination lists may be nested. We want to add all move destinations in the same
                // order they appear on the screen for the user.
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
        return result;
    };

    /**
     * Displays the dialogue to move element.
     * @param {jQuery} clickedElement element to return focus to after the modal is closed
     * @private
     */
    SortableList.prototype.displayMoveDialogue = function(clickedElement) {
        ModalFactory.create({
            type: ModalFactory.types.CANCEL,
            title: this.getMoveDialogueTitle(this.info.element, clickedElement),
            body: this.getDestinationsList()
        }).then($.proxy(function(modal) {
            var quickMoveHandler = $.proxy(function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.moveElement($(e.currentTarget).data('parent-element'), $(e.currentTarget).data('before-element'));
                this.info.endTime = new Date().getTime();
                this.info.positionChanged = this.hasPositionChanged(this.info);
                this.info.dropped = true;
                clickedElement.focus();
                this.executeCallback(SortableList.EVENTS.DROP);
                modal.hide();
            }, this);
            modal.getRoot().on('click', '[data-core_sortable_list-quickmove]', quickMoveHandler);
            modal.getRoot().on(ModalEvents.hidden, $.proxy(function() {
                // Always destroy when hidden, it is generated dynamically each time.
                modal.getRoot().off('click', '[data-core_sortable_list-quickmove]', quickMoveHandler);
                modal.destroy();
                this.finishDragging();
            }, this));
            modal.setLarge();
            modal.show();
            return modal;
        }, this)).catch(Notification.exception);
    };

    return SortableList;

});
