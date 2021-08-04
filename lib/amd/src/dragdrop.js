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

/*
 * JavaScript to handle drag operations, including automatic scrolling.
 *
 * Note: this module is defined statically. It is a singleton. You
 * can only have one use of it active at any time. However, you
 * can only drag one thing at a time, this is not a problem in practice.
 *
 * @module     core/dragdrop
 * @copyright  2016 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.6
 */
define(['jquery', 'core/autoscroll'], function($, autoScroll) {
    var dragdrop = {
        /**
         * A boolean or options argument depending on whether browser supports passive events.
         * @private
         */
        eventCaptureOptions: {passive: false, capture: true},

        /**
         * Drag proxy if any.
         * @private
         */
        dragProxy: null,

        /**
         * Function called on move.
         * @private
         */
        onMove: null,

        /**
         * Function called on drop.
         * @private
         */
        onDrop: null,

        /**
         * Initial position of proxy at drag start.
         */
        initialPosition: null,

        /**
         * Initial page X of cursor at drag start.
         */
        initialX: null,

        /**
         * Initial page Y of cursor at drag start.
         */
        initialY: null,

        /**
         * If touch event is in progress, this will be the id, otherwise null
         */
        touching: null,

        /**
         * Prepares to begin a drag operation - call with a mousedown or touchstart event.
         *
         * If the returned object has 'start' true, then you can set up a drag proxy, and call
         * start. This function will call preventDefault automatically regardless of whether
         * starting or not.
         *
         * @public
         * @param {Object} event Event (should be either mousedown or touchstart)
         * @return {Object} Object with start (boolean flag) and x, y (only if flag true) values
         */
        prepare: function(event) {
            event.preventDefault();
            var start;
            if (event.type === 'touchstart') {
                // For touch, start if there's at least one touch and we are not currently doing
                // a touch event.
                start = (dragdrop.touching === null) && event.changedTouches.length > 0;
            } else {
                // For mousedown, start if it's the left button.
                start = event.which === 1;
            }
            if (start) {
                var details = dragdrop.getEventXY(event);
                details.start = true;
                return details;
            } else {
                return {start: false};
            }
        },

        /**
         * Call to start a drag operation, in response to a mouse down or touch start event.
         * Normally call this after calling prepare and receiving start true (you can probably
         * skip prepare if only supporting drag not touch).
         *
         * Note: The caller is responsible for creating a 'drag proxy' which is the
         * thing that actually gets dragged. At present, this doesn't really work
         * properly unless it is added directly within the body tag.
         *
         * You also need to ensure that there is CSS so the proxy is absolutely positioned,
         * and styled to look like it is floating.
         *
         * You also need to absolutely position the proxy where you want it to start.
         *
         * @public
         * @param {Object} event Event (should be either mousedown or touchstart)
         * @param {jQuery} dragProxy An absolute-positioned element for dragging
         * @param {Object} onMove Function that receives X and Y page locations for a move
         * @param {Object} onDrop Function that receives X and Y page locations when dropped
         */
        start: function(event, dragProxy, onMove, onDrop) {
            var xy = dragdrop.getEventXY(event);
            dragdrop.initialX = xy.x;
            dragdrop.initialY = xy.y;
            dragdrop.initialPosition = dragProxy.offset();
            dragdrop.dragProxy = dragProxy;
            dragdrop.onMove = onMove;
            dragdrop.onDrop = onDrop;

            switch (event.type) {
                case 'mousedown':
                    // Cannot use jQuery 'on' because events need to not be passive.
                    dragdrop.addEventSpecial('mousemove', dragdrop.mouseMove);
                    dragdrop.addEventSpecial('mouseup', dragdrop.mouseUp);
                    break;
                case 'touchstart':
                    dragdrop.addEventSpecial('touchend', dragdrop.touchEnd);
                    dragdrop.addEventSpecial('touchcancel', dragdrop.touchEnd);
                    dragdrop.addEventSpecial('touchmove', dragdrop.touchMove);
                    dragdrop.touching = event.changedTouches[0].identifier;
                    break;
                default:
                    throw new Error('Unexpected event type: ' + event.type);
            }
            autoScroll.start(dragdrop.scroll);
        },

        /**
         * Adds an event listener with special event capture options (capture, not passive). If the
         * browser does not support passive events, it will fall back to the boolean for capture.
         *
         * @private
         * @param {Object} event Event type string
         * @param {Object} handler Handler function
         */
        addEventSpecial: function(event, handler) {
            try {
                window.addEventListener(event, handler, dragdrop.eventCaptureOptions);
            } catch (ex) {
                dragdrop.eventCaptureOptions = true;
                window.addEventListener(event, handler, dragdrop.eventCaptureOptions);
            }
        },

        /**
         * Gets X/Y co-ordinates of an event, which can be either touchstart or mousedown.
         *
         * @private
         * @param {Object} event Event (should be either mousedown or touchstart)
         * @return {Object} X/Y co-ordinates
         */
        getEventXY: function(event) {
            switch (event.type) {
                case 'touchstart':
                    return {x: event.changedTouches[0].pageX,
                            y: event.changedTouches[0].pageY};
                case 'mousedown':
                    return {x: event.pageX, y: event.pageY};
                default:
                    throw new Error('Unexpected event type: ' + event.type);
            }
        },

        /**
         * Event handler for touch move.
         *
         * @private
         * @param {Object} e Event
         */
        touchMove: function(e) {
            e.preventDefault();
            for (var i = 0; i < e.changedTouches.length; i++) {
                if (e.changedTouches[i].identifier === dragdrop.touching) {
                    dragdrop.handleMove(e.changedTouches[i].pageX, e.changedTouches[i].pageY);
                }
            }
        },

        /**
         * Event handler for mouse move.
         *
         * @private
         * @param {Object} e Event
         */
        mouseMove: function(e) {
            dragdrop.handleMove(e.pageX, e.pageY);
        },

        /**
         * Shared handler for move event (mouse or touch).
         *
         * @private
         * @param {number} pageX X co-ordinate
         * @param {number} pageY Y co-ordinate
         */
        handleMove: function(pageX, pageY) {
            // Move the drag proxy, not letting you move it out of screen or window bounds.
            var current = dragdrop.dragProxy.offset();
            var topOffset = current.top - parseInt(dragdrop.dragProxy.css('top'));
            var leftOffset = current.left - parseInt(dragdrop.dragProxy.css('left'));
            var maxY = $(document).height() - dragdrop.dragProxy.outerHeight() - topOffset;
            var maxX = $(document).width() - dragdrop.dragProxy.outerWidth() - leftOffset;
            var minY = -topOffset;
            var minX = -leftOffset;
            var initial = dragdrop.initialPosition;
            var position = {
                top: Math.max(minY, Math.min(maxY, initial.top + (pageY - dragdrop.initialY) - topOffset)),
                left: Math.max(minX, Math.min(maxX, initial.left + (pageX - dragdrop.initialX) - leftOffset))
            };
            dragdrop.dragProxy.css(position);

            // Trigger move handler.
            dragdrop.onMove(pageX, pageY, dragdrop.dragProxy);
        },

        /**
         * Event handler for touch end.
         *
         * @private
         * @param {Object} e Event
         */
        touchEnd: function(e) {
            e.preventDefault();
            for (var i = 0; i < e.changedTouches.length; i++) {
                if (e.changedTouches[i].identifier === dragdrop.touching) {
                    dragdrop.handleEnd(e.changedTouches[i].pageX, e.changedTouches[i].pageY);
                }
            }
        },

        /**
         * Event handler for mouse up.
         *
         * @private
         * @param {Object} e Event
         */
        mouseUp: function(e) {
            dragdrop.handleEnd(e.pageX, e.pageY);
        },

        /**
         * Shared handler for end drag (mouse or touch).
         *
         * @private
         * @param {number} pageX X
         * @param {number} pageY Y
         */
        handleEnd: function(pageX, pageY) {
            if (dragdrop.touching !== null) {
                window.removeEventListener('touchend', dragdrop.touchEnd, dragdrop.eventCaptureOptions);
                window.removeEventListener('touchcancel', dragdrop.touchEnd, dragdrop.eventCaptureOptions);
                window.removeEventListener('touchmove', dragdrop.touchMove, dragdrop.eventCaptureOptions);
                dragdrop.touching = null;
            } else {
                window.removeEventListener('mousemove', dragdrop.mouseMove, dragdrop.eventCaptureOptions);
                window.removeEventListener('mouseup', dragdrop.mouseUp, dragdrop.eventCaptureOptions);
            }
            autoScroll.stop();
            dragdrop.onDrop(pageX, pageY, dragdrop.dragProxy);
        },

        /**
         * Called when the page scrolls.
         *
         * @private
         * @param {number} offset Amount of scroll
         */
        scroll: function(offset) {
            // Move the proxy to match.
            var maxY = $(document).height() - dragdrop.dragProxy.outerHeight();
            var currentPosition = dragdrop.dragProxy.offset();
            currentPosition.top = Math.min(maxY, currentPosition.top + offset);
            dragdrop.dragProxy.css(currentPosition);
        }
    };

    return {
        /**
         * Prepares to begin a drag operation - call with a mousedown or touchstart event.
         *
         * If the returned object has 'start' true, then you can set up a drag proxy, and call
         * start. This function will call preventDefault automatically regardless of whether
         * starting or not.
         *
         * @param {Object} event Event (should be either mousedown or touchstart)
         * @return {Object} Object with start (boolean flag) and x, y (only if flag true) values
         */
        prepare: dragdrop.prepare,

        /**
         * Call to start a drag operation, in response to a mouse down or touch start event.
         * Normally call this after calling prepare and receiving start true (you can probably
         * skip prepare if only supporting drag not touch).
         *
         * Note: The caller is responsible for creating a 'drag proxy' which is the
         * thing that actually gets dragged. At present, this doesn't really work
         * properly unless it is added directly within the body tag.
         *
         * You also need to ensure that there is CSS so the proxy is absolutely positioned,
         * and styled to look like it is floating.
         *
         * You also need to absolutely position the proxy where you want it to start.
         *
         * @param {Object} event Event (should be either mousedown or touchstart)
         * @param {jQuery} dragProxy An absolute-positioned element for dragging
         * @param {Object} onMove Function that receives X and Y page locations for a move
         * @param {Object} onDrop Function that receives X and Y page locations when dropped
         */
        start: dragdrop.start
    };
});
