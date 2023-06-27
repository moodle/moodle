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
 * @module     qtype_ddmarker/question
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/dragdrop', 'qtype_ddmarker/shapes', 'core/key_codes'], function($, dragDrop, Shapes, keys) {

    "use strict";

    /**
     * Object to handle one drag-drop markers question.
     *
     * @param {String} containerId id of the outer div for this question.
     * @param {boolean} readOnly whether the question is being displayed read-only.
     * @param {Object[]} visibleDropZones the geometry of any drop-zones to show.
     *      Objects have fields shape, coords and markertext.
     * @constructor
     */
    function DragDropMarkersQuestion(containerId, readOnly, visibleDropZones) {
        var thisQ = this;
        this.containerId = containerId;
        this.visibleDropZones = visibleDropZones;
        this.shapes = [];
        this.shapeSVGs = [];
        this.isPrinting = false;
        if (readOnly) {
            this.getRoot().addClass('qtype_ddmarker-readonly');
        }
        thisQ.cloneDrags();
        thisQ.repositionDrags();
        thisQ.drawDropzones();
    }

    /**
     * Draws the svg shapes of any drop zones that should be visible for feedback purposes.
     */
    DragDropMarkersQuestion.prototype.drawDropzones = function() {
        if (this.visibleDropZones.length > 0) {
            var bgImage = this.bgImage();

            this.getRoot().find('div.dropzones').html('<svg xmlns="http://www.w3.org/2000/svg" class="dropzones" ' +
                'width="' + bgImage.outerWidth() + '" ' +
                'height="' + bgImage.outerHeight() + '"></svg>');
            var svg = this.getRoot().find('svg.dropzones');

            var nextColourIndex = 0;
            for (var dropZoneNo = 0; dropZoneNo < this.visibleDropZones.length; dropZoneNo++) {
                var colourClass = 'color' + nextColourIndex;
                nextColourIndex = (nextColourIndex + 1) % 8;
                this.addDropzone(svg, dropZoneNo, colourClass);
            }
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
            existingmarkertext,
            bgRatio = this.bgRatio();
        if (!shape.parse(dropZone.coords, bgRatio)) {
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
            var markerspan = this.getRoot().find('div.ddarea div.markertexts span.markertext' + dropZoneNo);
            if (markerspan.length) {
                var handles = shape.getHandlePositions();
                var positionLeft = handles.moveHandle.x - (markerspan.outerWidth() / 2) - 4;
                var positionTop = handles.moveHandle.y - (markerspan.outerHeight() / 2);
                markerspan
                    .css('left', positionLeft)
                    .css('top', positionTop);
                markerspan
                    .data('originX', markerspan.position().left / bgRatio)
                    .data('originY', markerspan.position().top / bgRatio);
                this.handleElementScale(markerspan, 'center');
            }
        }

        var shapeSVG = shape.makeSvg(svg[0]);
        shapeSVG.setAttribute('class', 'dropzone ' + colourClass);

        this.shapes[this.shapes.length] = shape;
        this.shapeSVGs[this.shapeSVGs.length] = shapeSVG;
    };

    /**
     * Draws the drag items on the page (and drop zones if required).
     * The idea is to re-draw all the drags and drops whenever there is a change
     * like a widow resize or an item dropped in place.
     */
    DragDropMarkersQuestion.prototype.repositionDrags = function() {
        var root = this.getRoot(),
            thisQ = this;

        root.find('div.draghomes .marker').not('.dragplaceholder').each(function(key, item) {
            $(item).addClass('unneeded');
        });

        root.find('input.choices').each(function(key, input) {
            var choiceNo = thisQ.getChoiceNoFromElement(input),
                coords = thisQ.getCoords(input);
            if (coords.length) {
                var drag = thisQ.getRoot().find('.draghomes' + ' span.marker' + '.choice' + choiceNo).not('.dragplaceholder');
                drag.remove();
                for (var i = 0; i < coords.length; i++) {
                    var dragInDrop = drag.clone();
                    dragInDrop.data('pagex', coords[i].x).data('pagey', coords[i].y);
                    // We always save the coordinates in the 1:1 ratio.
                    // So we need to set the scale ratio to 1 for the initial load.
                    dragInDrop.data('scaleRatio', 1);
                    thisQ.sendDragToDrop(dragInDrop, false, true);
                }
                thisQ.getDragClone(drag).addClass('active');
                thisQ.cloneDragIfNeeded(drag);
            }
        });
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
        var coords = [],
            val = $(inputNode).val();
        if (val !== '') {
            var coordsStrings = val.split(';');
            for (var i = 0; i < coordsStrings.length; i++) {
                coords[i] = this.convertToWindowXY(Shapes.Point.parse(coordsStrings[i]));
            }
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
        var bgPosition = bgImage.offset();

        return point.x >= bgPosition.left && point.x < bgPosition.left + bgImage.width()
            && point.y >= bgPosition.top && point.y < bgPosition.top + bgImage.height();
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

    DragDropMarkersQuestion.prototype.handleDragStart = function(e) {
        var thisQ = this,
            dragged = $(e.target).closest('.marker');

        var info = dragDrop.prepare(e);
        if (!info.start) {
            return;
        }

        dragged.addClass('beingdragged').css('transform', '');

        var placed = !dragged.hasClass('unneeded');
        if (!placed) {
            var hiddenDrag = thisQ.getDragClone(dragged);
            if (hiddenDrag.length) {
                hiddenDrag.addClass('active');
                dragged.offset(hiddenDrag.offset());
            }
        }

        dragDrop.start(e, dragged, function() {
            void (1);
        }, function(x, y, dragged) {
            thisQ.dragEnd(dragged);
        });
    };

    /**
     * Functionality at the end of a drag drop.
     * @param {jQuery} dragged the marker that was dragged.
     */
    DragDropMarkersQuestion.prototype.dragEnd = function(dragged) {
        var placed = false,
            choiceNo = this.getChoiceNoFromElement(dragged),
            bgRatio = this.bgRatio(),
            dragXY;

        dragged.data('pagex', dragged.offset().left).data('pagey', dragged.offset().top);
        dragXY = new Shapes.Point(dragged.data('pagex'), dragged.data('pagey'));
        if (this.coordsInBgImg(dragXY)) {
            this.sendDragToDrop(dragged, true);
            placed = true;

            // It seems that the dragdrop sometimes leaves the drag
            // one pixel out of position. Put it in exactly the right place.
            var bgImgXY = this.convertToBgImgXY(dragXY);
            bgImgXY = new Shapes.Point(bgImgXY.x / bgRatio, bgImgXY.y / bgRatio);
            dragged.data('originX', bgImgXY.x).data('originY', bgImgXY.y);
        }

        if (!placed) {
            this.sendDragHome(dragged);
            this.removeDragIfNeeded(dragged);
        } else {
            this.cloneDragIfNeeded(dragged);
        }

        this.saveCoordsForChoice(choiceNo);
    };

    /**
     * Save the coordinates for a dropped item in the form field.
     * @param {Number} choiceNo which copy of the choice this was.
     */
    DragDropMarkersQuestion.prototype.saveCoordsForChoice = function(choiceNo) {
        var coords = [],
            items = this.getRoot().find('div.droparea span.marker.choice' + choiceNo),
            thiQ = this,
            bgRatio = this.bgRatio();

        if (items.length) {
            items.each(function() {
                var drag = $(this);
                if (!drag.hasClass('beingdragged')) {
                    if (drag.data('scaleRatio') !== bgRatio) {
                        // The scale ratio for the draggable item was changed. We need to update that.
                        drag.data('pagex', drag.offset().left).data('pagey', drag.offset().top);
                    }
                    var dragXY = new Shapes.Point(drag.data('pagex'), drag.data('pagey'));
                    if (thiQ.coordsInBgImg(dragXY)) {
                        var bgImgXY = thiQ.convertToBgImgXY(dragXY);
                        bgImgXY = new Shapes.Point(bgImgXY.x / bgRatio, bgImgXY.y / bgRatio);
                        coords[coords.length] = bgImgXY;
                    }
                }
            });
        }

        this.getRoot().find('input.choice' + choiceNo).val(coords.join(';'));
    };

    /**
     * Handle key down / press events on markers.
     * @param {KeyboardEvent} e
     */
    DragDropMarkersQuestion.prototype.handleKeyPress = function(e) {
        var drag = $(e.target).closest('.marker'),
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
            drag.offset({'left': point.x, 'top': point.y});
            drag.data('pagex', drag.offset().left).data('pagey', drag.offset().top);
            var dragXY = this.convertToBgImgXY(new Shapes.Point(drag.data('pagex'), drag.data('pagey')));
            drag.data('originX', dragXY.x / this.bgRatio()).data('originY', dragXY.y / this.bgRatio());
            if (this.coordsInBgImg(new Shapes.Point(drag.offset().left, drag.offset().top))) {
                if (drag.hasClass('unneeded')) {
                    this.sendDragToDrop(drag, true);
                    var hiddenDrag = this.getDragClone(drag);
                    if (hiddenDrag.length) {
                        hiddenDrag.addClass('active');
                    }
                    this.cloneDragIfNeeded(drag);
                }
            }
        } else {
            drag.css('left', '').css('top', '');
            drag.data('pagex', drag.offset().left).data('pagey', drag.offset().top);
            this.sendDragHome(drag);
            this.removeDragIfNeeded(drag);
        }
        drag.focus();
        this.saveCoordsForChoice(choiceNo);
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
        var thisQ = this,
            bgRatio = this.bgRatio();
        if (this.isPrinting) {
            bgRatio = 1;
        }

        this.getRoot().find('div.droparea .marker').not('.beingdragged').each(function(key, drag) {
            $(drag)
                .css('left', parseFloat($(drag).data('originX')) * parseFloat(bgRatio))
                .css('top', parseFloat($(drag).data('originY')) * parseFloat(bgRatio));
            thisQ.handleElementScale(drag, 'left top');
        });

        this.getRoot().find('div.droparea svg.dropzones')
            .width(this.bgImage().width())
            .height(this.bgImage().height());

        for (var dropZoneNo = 0; dropZoneNo < this.visibleDropZones.length; dropZoneNo++) {
            var dropZone = thisQ.visibleDropZones[dropZoneNo];
            var originCoords = dropZone.coords;
            var shape = thisQ.shapes[dropZoneNo];
            var shapeSVG = thisQ.shapeSVGs[dropZoneNo];
            shape.parse(originCoords, bgRatio);
            shape.updateSvg(shapeSVG);

            var handles = shape.getHandlePositions();
            var markerSpan = this.getRoot().find('div.ddarea div.markertexts span.markertext' + dropZoneNo);
            markerSpan
                .css('left', handles.moveHandle.x - (markerSpan.outerWidth() / 2) - 4)
                .css('top', handles.moveHandle.y - (markerSpan.outerHeight() / 2));
            thisQ.handleElementScale(markerSpan, 'center');
        }
    };

    /**
     * Clone the drag.
     */
    DragDropMarkersQuestion.prototype.cloneDrags = function() {
        var thisQ = this;
        this.getRoot().find('div.draghomes span.marker').each(function(index, draghome) {
            var drag = $(draghome);
            var placeHolder = drag.clone();
            placeHolder.removeClass();
            placeHolder.addClass('marker');
            placeHolder.addClass('choice' + thisQ.getChoiceNoFromElement(drag));
            placeHolder.addClass(thisQ.getDragNoClass(drag, false));
            placeHolder.addClass('dragplaceholder');
            drag.before(placeHolder);
        });
    };

    /**
     * Get the drag number of a drag.
     *
     * @param {jQuery} drag the drag.
     * @returns {Number} the drag number.
     */
    DragDropMarkersQuestion.prototype.getDragNo = function(drag) {
        return this.getClassnameNumericSuffix(drag, 'dragno');
    };

    /**
     * Get the drag number prefix of a drag.
     *
     * @param {jQuery} drag the drag.
     * @param {Boolean} includeSelector include the CSS selector prefix or not.
     * @return {String} Class name
     */
    DragDropMarkersQuestion.prototype.getDragNoClass = function(drag, includeSelector) {
        var className = 'dragno' + this.getDragNo(drag);
        if (this.isInfiniteDrag(drag)) {
            className = 'infinite';
        }

        if (includeSelector) {
            return '.' + className;
        }

        return className;
    };

    /**
     * Get drag clone for a given drag.
     *
     * @param {jQuery} drag the drag.
     * @returns {jQuery} the drag's clone.
     */
    DragDropMarkersQuestion.prototype.getDragClone = function(drag) {
        return this.getRoot().find('.draghomes' + ' span.marker' +
            '.choice' + this.getChoiceNoFromElement(drag) + this.getDragNoClass(drag, true) + '.dragplaceholder');
    };

    /**
     * Get the drop area element.
     * @returns {jQuery} droparea element.
     */
    DragDropMarkersQuestion.prototype.dropArea = function() {
        return this.getRoot().find('div.droparea');
    };

    /**
     * Animate a drag back to its home.
     *
     * @param {jQuery} drag the item being moved.
     */
    DragDropMarkersQuestion.prototype.sendDragHome = function(drag) {
        drag.removeClass('beingdragged')
            .addClass('unneeded')
            .css('top', '')
            .css('left', '')
            .css('transform', '');
        var placeHolder = this.getDragClone(drag);
        placeHolder.after(drag);
        placeHolder.removeClass('active');
    };

    /**
     * Animate a drag item into a given place.
     *
     * @param {jQuery} drag the item to place.
     * @param {boolean} isScaling Scaling or not.
     * @param {boolean} initialLoad Whether it is the initial load or not.
     */
    DragDropMarkersQuestion.prototype.sendDragToDrop = function(drag, isScaling, initialLoad = false) {
        var dropArea = this.dropArea(),
            bgRatio = this.bgRatio();
        drag.removeClass('beingdragged').removeClass('unneeded');
        var dragXY = this.convertToBgImgXY(new Shapes.Point(drag.data('pagex'), drag.data('pagey')));
        if (isScaling) {
            drag.data('originX', dragXY.x / bgRatio).data('originY', dragXY.y / bgRatio);
            drag.css('left', dragXY.x).css('top', dragXY.y);
        } else {
            drag.data('originX', dragXY.x).data('originY', dragXY.y);
            drag.css('left', dragXY.x * bgRatio).css('top', dragXY.y * bgRatio);
        }
        // We need to save the original scale ratio for each draggable item.
        if (!initialLoad) {
            // Only set the scale ratio for a current being-dragged item, not for the initial loading.
            drag.data('scaleRatio', bgRatio);
        }
        dropArea.append(drag);
        this.handleElementScale(drag, 'left top');
    };

    /**
     * Clone the drag at the draghome area if needed.
     *
     * @param {jQuery} drag the item to place.
     */
    DragDropMarkersQuestion.prototype.cloneDragIfNeeded = function(drag) {
        var inputNode = this.getInput(drag),
            noOfDrags = Number(this.getClassnameNumericSuffix(inputNode, 'noofdrags')),
            displayedDragsInDropArea = this.getRoot().find('div.droparea .marker.choice' +
                this.getChoiceNoFromElement(drag) + this.getDragNoClass(drag, true)).length,
            displayedDragsInDragHomes = this.getRoot().find('div.draghomes .marker.choice' +
                this.getChoiceNoFromElement(drag) + this.getDragNoClass(drag, true)).not('.dragplaceholder').length;

        if ((this.isInfiniteDrag(drag) ||
                !this.isInfiniteDrag(drag) && displayedDragsInDropArea < noOfDrags) && displayedDragsInDragHomes === 0) {
            var dragClone = drag.clone();
            dragClone.addClass('unneeded')
                .css('top', '')
                .css('left', '')
                .css('transform', '');
            this.getDragClone(drag)
                .removeClass('active')
                .after(dragClone);
            questionManager.addEventHandlersToMarker(dragClone);
        }
    };

    /**
     * Remove the clone drag at the draghome area if needed.
     *
     * @param {jQuery} drag the item to place.
     */
    DragDropMarkersQuestion.prototype.removeDragIfNeeded = function(drag) {
        var dragsInHome = this.getRoot().find('div.draghomes .marker.choice' +
            this.getChoiceNoFromElement(drag) + this.getDragNoClass(drag, true)).not('.dragplaceholder');
        var displayedDrags = dragsInHome.length;
        while (displayedDrags > 1) {
            dragsInHome.first().remove();
            displayedDrags--;
        }
    };

    /**
     * Get the input belong to drag.
     *
     * @param {jQuery} drag the item to place.
     * @returns {jQuery} input element.
     */
    DragDropMarkersQuestion.prototype.getInput = function(drag) {
        var choiceNo = this.getChoiceNoFromElement(drag);
        return this.getRoot().find('input.choices.choice' + choiceNo);
    };

    /**
     * Return the background ratio.
     *
     * @returns {number} Background ratio.
     */
    DragDropMarkersQuestion.prototype.bgRatio = function() {
        var bgImg = this.bgImage();
        var bgImgNaturalWidth = bgImg.get(0).naturalWidth;
        var bgImgClientWidth = bgImg.width();

        return bgImgClientWidth / bgImgNaturalWidth;
    };

    /**
     * Scale the drag if needed.
     *
     * @param {jQuery} element the item to place.
     * @param {String} type scaling type
     */
    DragDropMarkersQuestion.prototype.handleElementScale = function(element, type) {
        var bgRatio = parseFloat(this.bgRatio());
        if (this.isPrinting) {
            bgRatio = 1;
        }
        $(element).css({
            '-webkit-transform': 'scale(' + bgRatio + ')',
            '-moz-transform': 'scale(' + bgRatio + ')',
            '-ms-transform': 'scale(' + bgRatio + ')',
            '-o-transform': 'scale(' + bgRatio + ')',
            'transform': 'scale(' + bgRatio + ')',
            'transform-origin': type
        });
    };

    /**
     * Check if the given drag is in infinite mode or not.
     *
     * @param {jQuery} drag The drag item need to check.
     */
    DragDropMarkersQuestion.prototype.isInfiniteDrag = function(drag) {
        return drag.hasClass('infinite');
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
         * {Object} ensures that the marker event handlers are only initialised once per question,
         * indexed by containerId (id on the .que div).
         */
        markerEventHandlersInitialised: {},

        /**
         * {boolean} is printing or not.
         */
        isPrinting: false,

        /**
         * {boolean} is keyboard navigation.
         */
        isKeyboardNavigation: false,

        /**
         * {Object} all the questions on this page, indexed by containerId (id on the .que div).
         */
        questions: {}, // An object containing all the information about each question on the page.

        /**
         * Initialise one question.
         *
         * @param {String} containerId the id of the div.que that contains this question.
         * @param {boolean} readOnly whether the question is read-only.
         * @param {Object[]} visibleDropZones data on any drop zones to draw as part of the feedback.
         */
        init: function(containerId, readOnly, visibleDropZones) {
            questionManager.questions[containerId] =
                new DragDropMarkersQuestion(containerId, readOnly, visibleDropZones);
            if (!questionManager.eventHandlersInitialised) {
                questionManager.setupEventHandlers();
                questionManager.eventHandlersInitialised = true;
            }
            if (!questionManager.markerEventHandlersInitialised.hasOwnProperty(containerId)) {
                questionManager.markerEventHandlersInitialised[containerId] = true;
                // We do not use the body event here to prevent the other event on Mobile device, such as scroll event.
                var questionContainer = document.getElementById(containerId);
                if (questionContainer.classList.contains('ddmarker') &&
                    !questionContainer.classList.contains('qtype_ddmarker-readonly')) {
                    // TODO: Convert all the jQuery selectors and events to native Javascript.
                    questionManager.addEventHandlersToMarker($(questionContainer).find('div.draghomes .marker'));
                    questionManager.addEventHandlersToMarker($(questionContainer).find('div.droparea .marker'));
                }
            }
        },

        /**
         * Set up the event handlers that make this question type work. (Done once per page.)
         */
        setupEventHandlers: function() {
            $(window).on('resize', function() {
                questionManager.handleWindowResize(false);
            });
            window.addEventListener('beforeprint', function() {
                questionManager.isPrinting = true;
                questionManager.handleWindowResize(questionManager.isPrinting);
            });
            window.addEventListener('afterprint', function() {
                questionManager.isPrinting = false;
                questionManager.handleWindowResize(questionManager.isPrinting);
            });
            setTimeout(function() {
                questionManager.fixLayoutIfThingsMoved();
            }, 100);
        },

        /**
         * Binding the event again for newly created element.
         *
         * @param {jQuery} element Element to bind the event
         */
        addEventHandlersToMarker: function(element) {
            element
                .on('mousedown touchstart', questionManager.handleDragStart)
                .on('keydown keypress', questionManager.handleKeyPress)
                .focusin(function(e) {
                    questionManager.handleKeyboardFocus(e, true);
                })
                .focusout(function(e) {
                    questionManager.handleKeyboardFocus(e, false);
                });
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
         * @param {boolean} isPrinting
         */
        handleWindowResize: function(isPrinting) {
            for (var containerId in questionManager.questions) {
                if (questionManager.questions.hasOwnProperty(containerId)) {
                    questionManager.questions[containerId].isPrinting = isPrinting;
                    questionManager.questions[containerId].handleResize();
                }
            }
        },

        /**
         * Handle focus lost events on markers.
         * @param {Event} e
         * @param {boolean} isNavigating
         */
        handleKeyboardFocus: function(e, isNavigating) {
            questionManager.isKeyboardNavigation = isNavigating;
        },

        /**
         * Sometimes, despite our best efforts, things change in a way that cannot
         * be specifically caught (e.g. dock expanding or collapsing in Boost).
         * Therefore, we need to periodically check everything is in the right position.
         */
        fixLayoutIfThingsMoved: function() {
            if (!questionManager.isKeyboardNavigation) {
                this.handleWindowResize(questionManager.isPrinting);
            }
            // We use setTimeout after finishing work, rather than setInterval,
            // in case positioning things is slow. We want 100 ms gap
            // between executions, not what setInterval does.
            setTimeout(function() {
                questionManager.fixLayoutIfThingsMoved(questionManager.isPrinting);
            }, 100);
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
