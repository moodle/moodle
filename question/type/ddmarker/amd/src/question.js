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
 * Question class for drag and drop marker question type, used to support the question and preview pages.
 *
 * @package    qtype_ddmarker
 * @subpackage question
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/dragdrop', 'qtype_ddmarker/shapes', 'core/key_codes'], function($, dragDrop, Shapes, keys) {

    "use strict";

    /**
     * Object to handle one drag-drop markers question.
     *
     * @param {String} containerId id of the outer div for this question.
     * @param {String} bgImgUrl the URL of the background image.
     * @param {boolean} readOnly whether the question is being displayed read-only.
     * @param {Object[]} visibleDropZones the geometry of any drop-zones to show.
     *      Objects have fields shape, coords and markertext.
     * @constructor
     */
    function DragDropMarkersQuestion(containerId, bgImgUrl, readOnly, visibleDropZones) {
        this.containerId = containerId;
        this.visibleDropZones = visibleDropZones;
        if (readOnly) {
            this.getRoot().addClass('qtype_ddmarker-readonly');
        }
        this.loadImage(bgImgUrl);
    }

    /**
     * Load the background image is loaded, then do the rest of the display.
     *
     * @param {String} bgImgUrl the URL of the background image.
     */
    DragDropMarkersQuestion.prototype.loadImage = function(bgImgUrl) {
        var thisQ = this;
        this.getRoot().find('.dropbackground')
            .one('load', function() {
                if (thisQ.visibleDropZones.length > 0) {
                    thisQ.drawDropzones();
                }
                thisQ.repositionDrags();
            })
            .attr('src', bgImgUrl)
            .css({'border': '1px solid #000', 'max-width': 'none'});
    };

    /**
     * Draws the svg shapes of any drop zones that should be visible for feedback purposes.
     */
    DragDropMarkersQuestion.prototype.drawDropzones = function() {
        var bgImage = this.getRoot().find('img.dropbackground');

        this.getRoot().find('div.dropzones').html('<svg xmlns="http://www.w3.org/2000/svg" class="dropzones" ' +
            'width="' + bgImage.outerWidth() + '" ' +
            'height="' + bgImage.outerHeight() + '"></svg>');
        var svg = this.getRoot().find('svg.dropzones');
        svg.css('position', 'absolute');

        var nextColourIndex = 0;
        for (var dropZoneNo = 0; dropZoneNo < this.visibleDropZones.length; dropZoneNo++) {
            var colourClass = 'color' + nextColourIndex;
            nextColourIndex = (nextColourIndex + 1) % 8;
            this.addDropzone(svg, dropZoneNo, colourClass);
        }
    };

    /**
     * Adds a dropzone shape with colour, coords and link provided to the array of shapes.
     *
     * @param {jQuery} svg the SVG image to which to add this drop zone.
     * @param {int} dropZoneNo which drop-zone to add.
     * @param {string} colourClass class name
     */
    DragDropMarkersQuestion.prototype.addDropzone = function(svg, dropZoneNo, colourClass) {
        var dropZone = this.visibleDropZones[dropZoneNo],
            shape = Shapes.make(dropZone.shape, ''),
            existingmarkertext;
        if (!shape.parse(dropZone.coords)) {
            return;
        }

        existingmarkertext = this.getRoot().find('div.markertexts span.markertext' + dropZoneNo);
        if (existingmarkertext.length) {
            if (dropZone.markertext !== '') {
                existingmarkertext.html(dropZone.markertext);
            } else {
                existingmarkertext.remove();
            }
        } else if (dropZone.markertext !== '') {
            var classnames = 'markertext markertext' + dropZoneNo;
            this.getRoot().find('div.markertexts').append('<span class="' + classnames + '">' +
                dropZone.markertext + '</span>');
        }

        var shapeSVG = shape.makeSvg(svg[0]);
        shapeSVG.setAttribute('class', 'dropzone ' + colourClass);
    };

    /**
     * Draws the drag items on the page (and drop zones if required).
     * The idea is to re-draw all the drags and drops whenever there is a change
     * like a widow resize or an item dropped in place.
     */
    DragDropMarkersQuestion.prototype.repositionDropZones = function() {
        var svg = this.getRoot().find('svg.dropzones');
        if (svg.length === 0) {
            return;
        }
        var bgPosition = this.convertToWindowXY(new Shapes.Point(-1, 0));
        svg.offset({'left': bgPosition.x, 'top': bgPosition.y});

        for (var dropZoneNo = 0; dropZoneNo < this.visibleDropZones.length; dropZoneNo++) {
            var markerspan = this.getRoot().find('div.ddarea div.markertexts span.markertext' + dropZoneNo);
            if (markerspan.length === 0) {
                continue;
            }
            var dropZone = this.visibleDropZones[dropZoneNo],
                shape = Shapes.make(dropZone.shape, '');
            if (!shape.parse(dropZone.coords)) {
                continue;
            }
            var handles = shape.getHandlePositions(),
                textPos = this.convertToWindowXY(handles.moveHandle.offset(
                    -markerspan.outerWidth() / 2, -markerspan.outerHeight() / 2));
            markerspan.offset({'left': textPos.x - 4, 'top': textPos.y});
        }
    };

    /**
     * Draws the drag items on the page (and drop zones if required).
     * The idea is to re-draw all the drags and drops whenever there is a change
     * like a widow resize or an item dropped in place.
     */
    DragDropMarkersQuestion.prototype.repositionDrags = function() {
        var root = this.getRoot(),
            thisQ = this;

        root.find('div.dragitems .dragitem').each(function(key, item) {
            $(item).addClass('unneeded');
        });

        root.find('input.choices').each(function(key, input) {
            var choiceNo = thisQ.getChoiceNoFromElement(input),
                coords = thisQ.getCoords(input),
                dragHome = thisQ.dragHome(choiceNo);
            for (var i = 0; i < coords.length; i++) {
                var drag = thisQ.dragItem(choiceNo, i);
                if (!drag.length || drag.hasClass('beingdragged')) {
                    drag = thisQ.cloneNewDragItem(dragHome, i);
                } else {
                    drag.removeClass('unneeded');
                }
                drag.offset({'left': coords[i].x, 'top': coords[i].y});
            }
        });

        root.find('div.dragitems .dragitem').each(function(key, itm) {
            var item = $(itm);
            if (item.hasClass('unneeded') && !item.hasClass('beingdragged')) {
                item.remove();
            }
        });

        this.repositionDropZones();

        var bgImage = this.bgImage(),
            bgPosition = bgImage.offset();
        bgImage.data('prev-top', bgPosition.top).data('prev-left', bgPosition.left);
    };

    /**
     * Determine what drag items need to be shown and
     * return coords of all drag items except any that are currently being dragged
     * based on contents of hidden inputs and whether drags are 'infinite' or how many
     * drags should be shown.
     *
     * @param {jQuery} inputNode
     * @returns {Point[]} coordinates of however many copies of the drag item should be shown.
     */
    DragDropMarkersQuestion.prototype.getCoords = function(inputNode) {
        var root = this.getRoot(),
            choiceNo = this.getChoiceNoFromElement(inputNode),
            noOfDrags = Number(this.getClassnameNumericSuffix(inputNode, 'noofdrags')),
            dragging = root.find('span.dragitem.beingdragged.choice' + choiceNo).length > 0,
            coords = [],
            val = $(inputNode).val();
        if (val !== '') {
            var coordsStrings = val.split(';');
            for (var i = 0; i < coordsStrings.length; i++) {
                coords[i] = this.convertToWindowXY(Shapes.Point.parse(coordsStrings[i]));
            }
        }
        var displayeddrags = coords.length + (dragging ? 1 : 0);
        if ($(inputNode).hasClass('infinite') || (displayeddrags < noOfDrags)) {
            coords[coords.length] = this.dragHomeXY(choiceNo);
        }
        return coords;
    };

    /**
     * Converts the relative x and y position coordinates into
     * absolute x and y position coordinates.
     *
     * @param {Point} point relative to the background image.
     * @returns {Point} point relative to the page.
     */
    DragDropMarkersQuestion.prototype.convertToWindowXY = function(point) {
        var bgImage = this.bgImage();
        // The +1 seems rather odd, but seems to give the best results in
        // the three main browsers at a range of zoom levels.
        // (Its due to the 1px border around the image, that shifts the
        // image pixels by 1 down and to the left.)
        return point.offset(bgImage.offset().left + 1, bgImage.offset().top + 1);
    };

    /**
     * Utility function converting window coordinates to relative to the
     * background image coordinates.
     *
     * @param {Point} point relative to the page.
     * @returns {Point} point relative to the background image.
     */
    DragDropMarkersQuestion.prototype.convertToBgImgXY = function(point) {
        var bgImage = this.bgImage();
        return point.offset(-bgImage.offset().left - 1, -bgImage.offset().top - 1);
    };

    /**
     * Is the point within the background image?
     *
     * @param {Point} point relative to the BG image.
     * @return {boolean} true it they are.
     */
    DragDropMarkersQuestion.prototype.coordsInBgImg = function(point) {
        var bgImage = this.bgImage();
        return point.x > 0 && point.x <= bgImage.width() &&
                point.y > 0 && point.y <= bgImage.height();
    };

    /**
     * Returns coordinates for the home position of a choice.
     *
     * @param {Number} choiceNo
     * @returns {Point} coordinates
     */
    DragDropMarkersQuestion.prototype.dragHomeXY = function(choiceNo) {
        var dragItemHome = this.dragHome(choiceNo);
        return new Shapes.Point(dragItemHome.offset().left, dragItemHome.offset().top);
    };

    /**
     * Get the outer div for this question.
     * @returns {jQuery} containing that div.
     */
    DragDropMarkersQuestion.prototype.getRoot = function() {
        return $(document.getElementById(this.containerId));
    };

    /**
     * Get the img that is the background image.
     * @returns {jQuery} containing that img.
     */
    DragDropMarkersQuestion.prototype.bgImage = function() {
        return this.getRoot().find('img.dropbackground');
    };

    /**
     * Return the DOM node for this choice's home position.
     * @param {Number} choiceNo
     * @returns {jQuery} containing the home.
     */
    DragDropMarkersQuestion.prototype.dragHome = function(choiceNo) {
        return this.getRoot().find('div.dragitems span.draghome.choice' + choiceNo);
    };

    /**
     * Return the DOM node for a particular instance of a particular choice.
     * @param {Number} choiceNo
     * @param {Number} itemNo
     * @returns {jQuery} containing the item.
     */
    DragDropMarkersQuestion.prototype.dragItem = function(choiceNo, itemNo) {
        return this.getRoot().find('div.dragitems span.dragitem.choice' + choiceNo + '.item' + itemNo);
    };

    /**
     * Create a draggable copy of the drag item.
     *
     * @param {jQuery} dragHome to clone
     * @param {Number} itemNo new item number
     * @return {jQuery} drag
     */
    DragDropMarkersQuestion.prototype.cloneNewDragItem = function(dragHome, itemNo) {
        var drag = dragHome.clone(true);
        drag.removeClass('draghome').addClass('dragitem').addClass('item' + itemNo);
        dragHome.after(drag);
        drag.attr('tabIndex', 0);
        return drag;
    };

    DragDropMarkersQuestion.prototype.handleDragStart = function(e) {
        var thisQ = this,
            dragged = $(e.target).closest('.dragitem');

        var info = dragDrop.prepare(e);
        if (!info.start) {
            return;
        }

        dragged.addClass('beingdragged');
        dragDrop.start(e, dragged, function() {
            void (1); // Nothing to do, but we need a function.
        }, function(x, y, dragged) {
            thisQ.dragEnd(dragged);
        });
    };

    /**
     * Functionality at the end of a drag drop.
     * @param {jQuery} dragged the marker that was dragged.
     */
    DragDropMarkersQuestion.prototype.dragEnd = function(dragged) {
        dragged.removeClass('beingdragged');
        var choiceNo = this.getChoiceNoFromElement(dragged);
        this.saveCoordsForChoice(choiceNo, dragged);
        this.repositionDrags();
    };

    /**
     * Save the coordinates for a dropped item in the form field.
     * @param {Number} choiceNo which copy of the choice this was.
     * @param {jQuery} dropped the choice that was dropped here.
     */
    DragDropMarkersQuestion.prototype.saveCoordsForChoice = function(choiceNo, dropped) {
        var coords = [],
            numItems = this.getRoot().find('span.dragitem.choice' + choiceNo).length,
            bgImgXY,
            addme = true;

        // Re-build the coords array based on data in the ddform inputs.
        // While long winded and unnecessary if there is only one drop item
        // for a choice, it does account for moving any one of several drop items
        // within a choice that have already been placed.
        for (var i = 0; i <= numItems; i++) {
            var drag = this.dragItem(choiceNo, i);
            if (drag.length === 0) {
                continue;
            }

            if (!drag.hasClass('beingdragged')) {
                bgImgXY = this.convertToBgImgXY(new Shapes.Point(drag.offset().left, drag.offset().top));
                if (this.coordsInBgImg(bgImgXY)) {
                    coords[coords.length] = bgImgXY;
                }
            }

            if (dropped && dropped.length !== 0 && (dropped[0].innerText === drag[0].innerText)) {
                addme = false;
            }
        }

        // If dropped has been passed it is because a new item has been dropped onto the background image
        // so add its coordinates to the array.
        if (addme) {
            bgImgXY = this.convertToBgImgXY(new Shapes.Point(dropped.offset().left, dropped.offset().top));
            if (this.coordsInBgImg(bgImgXY)) {
                coords[coords.length] = bgImgXY;
            }
        }

        this.getRoot().find('input.choice' + choiceNo).val(coords.join(';'));
    };

    /**
     * Handle key down / press events on markers.
     * @param {KeyboardEvent} e
     */
    DragDropMarkersQuestion.prototype.handleKeyPress = function(e) {
        var drag = $(e.target).closest('.dragitem'),
            point = new Shapes.Point(drag.offset().left, drag.offset().top),
            choiceNo = this.getChoiceNoFromElement(drag);

        switch (e.keyCode) {
            case keys.arrowLeft:
            case 65: // A.
                point.x -= 1;
                break;
            case keys.arrowRight:
            case 68: // D.
                point.x += 1;
                break;
            case keys.arrowDown:
            case 83: // S.
                point.y += 1;
                break;
            case keys.arrowUp:
            case 87: // W.
                point.y -= 1;
                break;
            case keys.space:
            case keys.escape:
                point = null;
                break;
            default:
                return; // Ingore other keys.
        }
        e.preventDefault();

        if (point !== null) {
            point = this.constrainToBgImg(point);
        } else {
            point = this.dragHomeXY(choiceNo);
        }
        drag.offset({'left': point.x, 'top': point.y});
        this.saveCoordsForChoice(choiceNo, drag);
        this.repositionDrags();
    };

    /**
     * Makes sure the dragged item always exists within the background image area.
     *
     * @param {Point} windowxy
     * @returns {Point} coordinates
     */
    DragDropMarkersQuestion.prototype.constrainToBgImg = function(windowxy) {
        var bgImg = this.bgImage(),
            bgImgXY = this.convertToBgImgXY(windowxy);
        bgImgXY.x = Math.max(0, bgImgXY.x);
        bgImgXY.y = Math.max(0, bgImgXY.y);
        bgImgXY.x = Math.min(bgImg.width(), bgImgXY.x);
        bgImgXY.y = Math.min(bgImg.height(), bgImgXY.y);
        return this.convertToWindowXY(bgImgXY);
    };

    /**
     * Returns the choice number for a node.
     *
     * @param {Element|jQuery} node
     * @returns {Number}
     */
    DragDropMarkersQuestion.prototype.getChoiceNoFromElement = function(node) {
        return Number(this.getClassnameNumericSuffix(node, 'choice'));
    };

    /**
     * Returns the numeric part of a class with the given prefix.
     *
     * @param {Element|jQuery} node
     * @param {String} prefix
     * @returns {Number|null}
     */
    DragDropMarkersQuestion.prototype.getClassnameNumericSuffix = function(node, prefix) {
        var classes = $(node).attr('class');
        if (classes !== undefined && classes !== '') {
            var classesarr = classes.split(' ');
            for (var index = 0; index < classesarr.length; index++) {
                var patt1 = new RegExp('^' + prefix + '([0-9])+$');
                if (patt1.test(classesarr[index])) {
                    var patt2 = new RegExp('([0-9])+$');
                    var match = patt2.exec(classesarr[index]);
                    return Number(match[0]);
                }
            }
        }
        return null;
    };

    /**
     * Handle when the window is resized.
     */
    DragDropMarkersQuestion.prototype.handleResize = function() {
        this.repositionDrags();
    };

    /**
     * Check to see if the background image has moved. If so, refresh the layout.
     */
    DragDropMarkersQuestion.prototype.fixLayoutIfBackgroundMoved = function() {
        var bgImage = this.bgImage(),
            bgPosition = bgImage.offset(),
            prevTop = bgImage.data('prev-top'),
            prevLeft = bgImage.data('prev-left');
        if (prevLeft === undefined || prevTop === undefined) {
            // Question is not set up yet. Nothing to do.
            return;
        }
        if (prevTop === bgPosition.top && prevLeft === bgPosition.left) {
            // Things have not moved.
            return;
        }
        // We need to reposition things.
        this.repositionDrags();
    };

    /**
     * Singleton that tracks all the DragDropToTextQuestions on this page, and deals
     * with event dispatching.
     *
     * @type {Object}
     */
    var questionManager = {

        /**
         * {boolean} ensures that the event handlers are only initialised once per page.
         */
        eventHandlersInitialised: false,

        /**
         * {Object} all the questions on this page, indexed by containerId (id on the .que div).
         */
        questions: {}, // An object containing all the information about each question on the page.

        /**
         * Initialise one question.
         *
         * @param {String} containerId the id of the div.que that contains this question.
         * @param {String} bgImgUrl URL fo the background image.
         * @param {boolean} readOnly whether the question is read-only.
         * @param {Object[]} visibleDropZones data on any drop zones to draw as part of the feedback.
         */
        init: function(containerId, bgImgUrl, readOnly, visibleDropZones) {
            questionManager.questions[containerId] =
                new DragDropMarkersQuestion(containerId, bgImgUrl, readOnly, visibleDropZones);
            if (!questionManager.eventHandlersInitialised) {
                questionManager.setupEventHandlers();
                questionManager.eventHandlersInitialised = true;
            }
        },

        /**
         * Set up the event handlers that make this question type work. (Done once per page.)
         */
        setupEventHandlers: function() {
            $('body').on('mousedown touchstart',
                '.que.ddmarker:not(.qtype_ddmarker-readonly) div.dragitems .dragitem',
                questionManager.handleDragStart)
                .on('keydown keypress',
                    '.que.ddmarker:not(.qtype_ddmarker-readonly) div.dragitems .dragitem',
                    questionManager.handleKeyPress);
            $(window).on('resize', questionManager.handleWindowResize);
            setTimeout(questionManager.fixLayoutIfThingsMoved, 100);
        },

        /**
         * Handle mouse down / touch start events on markers.
         * @param {Event} e the DOM event.
         */
        handleDragStart: function(e) {
            e.preventDefault();
            var question = questionManager.getQuestionForEvent(e);
            if (question) {
                question.handleDragStart(e);
            }
        },

        /**
         * Handle key down / press events on markers.
         * @param {Event} e
         */
        handleKeyPress: function(e) {
            var question = questionManager.getQuestionForEvent(e);
            if (question) {
                question.handleKeyPress(e);
            }
        },

        /**
         * Handle when the window is resized.
         */
        handleWindowResize: function() {
            for (var containerId in questionManager.questions) {
                if (questionManager.questions.hasOwnProperty(containerId)) {
                    questionManager.questions[containerId].handleResize();
                }
            }
        },

        /**
         * Sometimes, despite our best efforts, things change in a way that cannot
         * be specifically caught (e.g. dock expanding or collapsing in Boost).
         * Therefore, we need to periodically check everything is in the right position.
         */
        fixLayoutIfThingsMoved: function() {
            for (var containerId in questionManager.questions) {
                if (questionManager.questions.hasOwnProperty(containerId)) {
                    questionManager.questions[containerId].fixLayoutIfBackgroundMoved();
                }
            }

            // We use setTimeout after finishing work, rather than setInterval,
            // in case positioning things is slow. We want 100 ms gap
            // between executions, not what setInterval does.
            setTimeout(questionManager.fixLayoutIfThingsMoved, 100);
        },

        /**
         * Given an event, work out which question it effects.
         * @param {Event} e the event.
         * @returns {DragDropMarkersQuestion|undefined} The question, or undefined.
         */
        getQuestionForEvent: function(e) {
            var containerId = $(e.currentTarget).closest('.que.ddmarker').attr('id');
            return questionManager.questions[containerId];
        }
    };

    /**
     * @alias module:qtype_ddmarker/question
     */
    return {
        /**
         * Initialise one drag-drop markers question.
         *
         * @param {String} containerId id of the outer div for this question.
         * @param {String} bgImgUrl the URL of the background image.
         * @param {boolean} readOnly whether the question is being displayed read-only.
         * @param {String[]} visibleDropZones the geometry of any drop-zones to show.
         */
        init: questionManager.init
    };
});
