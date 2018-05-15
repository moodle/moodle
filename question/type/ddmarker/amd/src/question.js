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

define(['jquery', 'core/dragdrop'], function($, dd) {

    "use strict";

    /**
     * @alias qtype_ddmarker/question
     */
    var t = {

        q: {}, // An object containing all the information about each question on the page.

        /**
         * Initialise the question and preview page javascript features.
         * @param {Object} config Question configuration.
         */
        init: function(config) {
            var questionNo = config.topnode.replace(/^div#/, '');
            t.q[questionNo] = {
                dropzones: [], // Array of dropzones for display if required.
                readOnly: false, // Read only mode.
                topnode: null, // The jQuery page container for this functionality.
                bgImageUrl: null, // Backgroung image url.
                doc: null, // A utility set of functions associated with elements on the page.
                colours: ['#FFFFFF', '#B0C4DE', '#DCDCDC', '#D8BFD8', '#87CEFA', '#DAA520', '#FFD700', '#F0E68C'],
                nextColourIndex: 0, // Next colour selector index.
                shapes: [], // Array of dropzone shapes, as svg elements.
                svg: null // Reference to the main dropzone shape svg element.
            };
            t.q[questionNo].dropzones = config.dropzones;
            t.q[questionNo].readOnly = config.readOnly;
            t.q[questionNo].topnode = $(config.topnode);
            t.q[questionNo].bgImageUrl = config.bgimgurl;
            t.q[questionNo].doc = t.docStructure(questionNo);
            if (!t.q[questionNo].readOnly) {
                // Add main drag and drop events.
                t.q[questionNo].doc.dragItemsArea.on('mousedown touchstart', function(event) {
                    event.preventDefault();
                    var dragged = $(event.target.parentNode);
                    if (dragged.hasClass('dragitem')) {
                        dragged.addClass('beingdragged');
                        dd.prepare(event);
                        dd.start(event, dragged, t.dragMove, t.dragEnd);
                    }
                });
                // Keyboard accessibility.
                t.q[questionNo].doc.dragItemsArea.on('keydown keypress', {questionNo: questionNo}, t.dropzoneKeyPress);
                $(window).on('resize', {questionNo: questionNo}, t.redrawDragsAndDrops);
            }
            t.loadImage(questionNo);
        },

        /**
         * Prevents adding drag and drop until the background image is loaded.
         * @param {string} questionNo
         */
        loadImage: function(questionNo) {
            t.q[questionNo].doc.bgImage().on('load', {questionNo: questionNo}, t.redrawDragsAndDrops);
            t.q[questionNo].doc.bgImage().attr('src', t.q[questionNo].bgImageUrl);
            // It would be nice to have the image resize with the viewport, (use max-width: 100%)
            // But that affects the dropzone coordinates, preventing correct marking.
            t.q[questionNo].doc.bgImage().css({'border': '1px solid #000', 'max-width': 'none'});
        },

        /**
         * Draws the drag items on the page (and drop zones if required).
         * The idea is to re-draw all the drags and drops whenever there is a change
         * like a widow resize or an item dropped in place.
         * @param {Object} event
         */
        redrawDragsAndDrops: function(event) {
            var questionNo = event.data.questionNo;
            t.q[questionNo].doc.dragItems().each(function(key, item) {
                $(item).addClass('unneeded');
            });
            // Add just one more clone than is already available.
            t.q[questionNo].doc.inputsForChoices(questionNo).each(function(key, input) {
                var choiceNo = t.getChoiceNoForNode(input);
                var coords = t.getCoords(questionNo, input);
                var dragItemHome = t.q[questionNo].doc.dragItemHome(choiceNo);
                for (var i = 0; i < coords.length; i++) {
                    var drag = t.q[questionNo].doc.dragItemForChoice(choiceNo, i);
                    if (!drag.length || drag.hasClass('beingdragged')) {
                        drag = t.cloneNewDragItem(questionNo, dragItemHome, i);
                    } else {
                        drag.removeClass('unneeded');
                    }
                    drag.css('left', coords[i][0]).css('top', coords[i][1]);
                }
            });
            t.q[questionNo].doc.dragItems().each(function(key, itm) {
                var item = $(itm);
                if (item.hasClass('unneeded') && !item.hasClass('beingdragged')) {
                    item.remove();
                }
            });
            // Add the dropzones if required (on submit and finish when some dropzones where missed).
            if (t.q[questionNo].dropzones.length !== 0) {
                t.restartColours(questionNo);
                for (var dropzoneno in t.q[questionNo].dropzones) {
                    var colourfordropzone = t.getNextColour(questionNo);
                    var d = t.q[questionNo].dropzones[dropzoneno];
                    t.addDropzone(questionNo, dropzoneno, d.markertext, d.shape, d.coords, colourfordropzone, true);
                }
                t.drawDropzones(questionNo);
            }
        },

        /**
         * Create a draggable copy of the drag item.
         * @param {string} questionNo
         * @param {Object} draghome
         * @param {string} itemno
         * @return {Object} drag
         */
        cloneNewDragItem: function(questionNo, draghome, itemno) {
            var drag = draghome.clone(true);
            drag.removeClass('draghome');
            drag.addClass('dragitem');
            drag.addClass('item' + itemno);
            drag.addClass('questionno' + questionNo);
            drag.find('span.markertext').css('opacity', 0.6);
            // Add the new drag item to the DOM.
            draghome.after(drag);
            drag.attr('tabIndex', itemno);
            return drag;
        },

        /**
         * Save the coordinates for a dropped item in the form field.
         * @param {string} questionNo
         * @param {string} choiceNo
         * @param {Object} dropped
         */
        saveCoordsForChoice: function(questionNo, choiceNo, dropped) {
            var coords = [];
            var bgImgXY, value, drag;
            var addme = true;
            // Re-build the coords array based on data in the ddform inputs.
            // While long winded and unnecessary if there is only one drop item
            // for a choice, it does account for moving any one of several drop items
            // within a choice that have already been placed.
            for (var i = 0; i <= t.q[questionNo].doc.dragItemsForChoice(choiceNo).length; i++) {
                drag = t.q[questionNo].doc.dragItemForChoice(choiceNo, i);
                if (drag.length) {
                    if (!drag.hasClass('beingdragged')) {
                        bgImgXY = t.convertToBgImgXY(questionNo, [drag.offset().left, drag.offset().top]);
                        if (t.coordsInBgImg(questionNo, bgImgXY)) {
                            coords[coords.length] = bgImgXY;
                        }
                    }
                    if (dropped && dropped.length !== 0 && (dropped.get(0).innerText === drag.get(0).innerText)) {
                        addme = false;
                    }
                }
            }
            // If dropped has been passed it is because a new item has been dropped onto the background image
            // so add its coordinates to the array.
            if (addme) {
                bgImgXY = t.convertToBgImgXY(questionNo, [dropped.offset().left, dropped.offset().top]);
                if (t.coordsInBgImg(questionNo, bgImgXY)) {
                    coords[coords.length] = bgImgXY;
                }
            }
            value = coords.join(';');
            t.q[questionNo].doc.inputForChoice(questionNo, choiceNo).val(value);
        },

        /**
         * Are the coords within the background image?
         * @param {string} questionNo
         * @param {string} coords
         * @returns {boolean}
         */
        coordsInBgImg: function(questionNo, coords) {
            return (
                coords[0] > 0 &&
                coords[1] > 0 &&
                coords[0] <= t.q[questionNo].doc.bgImage().width() &&
                coords[1] <= t.q[questionNo].doc.bgImage().height()
            );
        },

        /**
         * Makes sure the dragged item always exists within the background image area.
         * @param {string} questionNo
         * @param {Array} windowxy
         * @returns {Array} coordinates
         */
        constrainToBgImg: function(questionNo, windowxy) {
            var bgImgXY = t.convertToBgImgXY(questionNo, windowxy);
            bgImgXY[0] = Math.max(0, bgImgXY[0]);
            bgImgXY[1] = Math.max(0, bgImgXY[1]);
            bgImgXY[0] = Math.min(t.q[questionNo].doc.bgImage().width(), bgImgXY[0]);
            bgImgXY[1] = Math.min(t.q[questionNo].doc.bgImage().height(), bgImgXY[1]);
            return t.convertToWindowXY(questionNo, bgImgXY);
        },

        /**
         * Utility function converting window coordinates to relative to the background image coordinates.
         * @param {string} questionNo
         * @param {Array} windowXY
         * @returns {Array} coordinates
         */
        convertToBgImgXY: function(questionNo, windowXY) {
            return [Number(windowXY[0]) - t.q[questionNo].doc.bgImage().offset().left - 1,
                Number(windowXY[1]) - t.q[questionNo].doc.bgImage().offset().top - 1];
        },

        /**
         * Converts the relative x and y position coordinates into
         * absolute x and y position coordinates.
         * @param {string} questionNo
         * @param {Array} bgImgXY
         * @returns {Array} coordinates
         */
        convertToWindowXY: function(questionNo, bgImgXY) {
            // The +1 seems rather odd, but seems to give the best results in
            // the three main browsers at a range of zoom levels.
            // (Its due to the 1px border around the image, that shifts the
            // image pixels by 1 down and to the left.)
            return [Number(bgImgXY[0]) + t.q[questionNo].doc.bgImage().position().left + 1,
                Number(bgImgXY[1]) + t.q[questionNo].doc.bgImage().position().top + 1];
        },

        /**
         * Determine what drag items need to be shown and
         * return coords of all drag items except any that are currently being dragged
         * based on contents of hidden inputs and whether drags are 'infinite' or how many drags should be shown.
         * @param {string} questionNo
         * @param {Object} inputnode
         * @returns {Array} coords
         */
        getCoords: function(questionNo, inputnode) {
            var choiceNo = t.getChoiceNoForNode(inputnode);
            var val = $(inputnode).val();
            var infinite = $(inputnode).hasClass('infinite');
            var noofdrags = Number(t.getClassnameNumericSuffix(inputnode, 'noofdrags'));
            var dragging = (t.q[questionNo].doc.dragItemBeingDragged(choiceNo).length > 0);
            var coords = [];
            if (val !== '') {
                var coordsstrings = val.split(';');
                for (var i = 0; i < coordsstrings.length; i++) {
                    coords[i] = t.convertToWindowXY(questionNo, coordsstrings[i].split(','));
                }
            }
            var displayeddrags = coords.length + (dragging ? 1 : 0);
            if (infinite || (displayeddrags < noofdrags)) {
                coords[coords.length] = t.dragHomeXY(questionNo, choiceNo);
            }
            return coords;
        },

        /**
         * Returns coordinates for the home position of a choice.
         * @param {string} questionNo
         * @param {string} choiceNo
         * @returns {Array} coordinates
         */
        dragHomeXY: function(questionNo, choiceNo) {
            var dragItemHome = t.q[questionNo].doc.dragItemHome(choiceNo);
            return [dragItemHome.position().left, dragItemHome.position().top - 12];
        },

        /**
         * Returns the choice number for a node.
         * @param {Object} node
         * @returns {number}
         */
        getChoiceNoForNode: function(node) {
            return Number(t.getClassnameNumericSuffix(node, 'choice'));
        },

        /**
         * Returns the question number for a node.
         * @param {Object} node
         * @returns {string}
         */
        getQuestionNoForNode: function(node) {
            return 'q' + Number(t.getClassnameNumericSuffix(node, 'questionnoq'));
        },

        /**
         * Returns the numeric part of a class with the given prefix.
         * @param {Object} node
         * @param {string} prefix
         * @returns {number} or {null}
         */
        getClassnameNumericSuffix: function(node, prefix) {
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
        },

        /**
         * Keyboard accessibility.
         * @param {Object} e Event
         */
        dropzoneKeyPress: function(e) {
            var drag, xy, keys, direction, questionNo, choiceNo;
            drag = $(e.target);
            xy = [drag.offset().left, drag.offset().top];
            keys = {
                '32': 'remove', // Space
                '37': 'left', // Left arrow
                '38': 'up', // Up arrow
                '39': 'right', // Right arrow
                '40': 'down', // Down arrow
                '65': 'left', // Keypress a
                '87': 'up', // Keypress w
                '68': 'right', // Keypress d
                '83': 'down', // Keypress s
                '27': 'remove' // Escape
            };
            direction = keys[e.keyCode];
            if (!direction) {
                return;
            }
            e.preventDefault();
            switch (direction) {
                case 'left' :
                    xy[0] -= 1;
                    break;
                case 'right' :
                    xy[0] += 1;
                    break;
                case 'down' :
                    xy[1] += 1;
                    break;
                case 'up' :
                    xy[1] -= 1;
                    break;
                case 'remove' :
                    xy = null;
                    break;
            }
            questionNo = t.getQuestionNoForNode(drag);
            choiceNo = t.getChoiceNoForNode(drag);
            if (xy !== null) {
                xy = t.constrainToBgImg(questionNo, xy);
            } else {
                xy = t.dragHomeXY(questionNo, choiceNo);
            }
            drag.css('left', xy[0]).css('top', xy[1]);
            t.saveCoordsForChoice(questionNo, choiceNo, drag);
            t.redrawDragsAndDrops({data: {questionNo: questionNo}});
        },

        /**
         * Functionality during a drag drop.
         */
        dragMove: function() {
            // No action is required.
        },

        /**
         * Functionality at the end of a drag drop.
         * @param {number} x Unused
         * @param {number} y Unused
         * @param {Object} node jQuery node representing the dragged item that has been dropped.
         */
        dragEnd: function(x, y, node) {
            var choiceNo, questionNo;
            node.removeClass('beingdragged');
            questionNo = t.getQuestionNoForNode(node);
            choiceNo = t.getChoiceNoForNode(node);
            t.saveCoordsForChoice(questionNo, choiceNo, node);
            t.redrawDragsAndDrops({data: {questionNo: questionNo}});
        },

        /**
         * Utility functions.
         * @param {string} questionNo
         * @return {Object} object Contains useful functions associated with the current page elements.
         */
        docStructure: function(questionNo) {
            var dragItemsArea = t.q[questionNo].topnode.find('div.dragitems:first');
            return {
                dragItemsArea: dragItemsArea,
                bgImage: function() {
                    return t.q[questionNo].topnode.find('.dropbackground:first');
                },
                dragItems: function() {
                    return dragItemsArea.find('.dragitem');
                },
                dragItemsForChoice: function(choiceNo) {
                    return dragItemsArea.find('span.dragitem.choice' + choiceNo);
                },
                dragItemForChoice: function(choiceNo, itemno) {
                    return dragItemsArea.find('span.dragitem.choice' + choiceNo + '.item' + itemno);
                },
                dragItemBeingDragged: function(choiceNo) {
                    // Note this returns an object that might have length = 0.
                    return dragItemsArea.find('span.dragitem.beingdragged.choice' + choiceNo);
                },
                dragItemHome: function(choiceNo) {
                    return dragItemsArea.find('span.draghome.choice' + choiceNo);
                },
                dragItemHomes: function() {
                    return dragItemsArea.find('span.draghome');
                },
                inputsForChoices: function(questionNo) {
                    return t.q[questionNo].topnode.find('input.choices');
                },
                inputForChoice: function(questionNo, choiceNo) {
                    return t.q[questionNo].topnode.find('input.choice' + choiceNo);
                },
                markerTexts: function(questionNo) {
                    return t.q[questionNo].topnode.find('div.markertexts');
                }
            };
        },

        /**
         * Colours help distinguish choices, this resets the colour array.
         * @param {string} questionNo
         */
        restartColours: function(questionNo) {
            t.q[questionNo].nextColourIndex = 0;
        },

        /**
         * Returns a colour string for use in display of drop zones.
         * @param {string} questionNo
         * @returns {string} colour
         */
        getNextColour: function(questionNo) {
            var colour = t.q[questionNo].colours[t.q[questionNo].nextColourIndex];
            t.q[questionNo].nextColourIndex++;
            if (t.q[questionNo].nextColourIndex === t.q[questionNo].colours.length) {
                t.q[questionNo].nextColourIndex = 0;
            }
            return colour;
        },

        /**
         * Adds a dropzone shape with colour, coords and link provided to the array of shapes.
         * @param {string} questionNo
         * @param {number} dropzoneno
         * @param {string} markertext Should be escaped text
         * @param {string} shape Text either circle, rectangle or polygon
         * @param {Array} coords
         * @param {string} colour One colour from t.colours
         * @param {boolean} link
         */
        addDropzone: function(questionNo, dropzoneno, markertext, shape, coords, colour, link) {
            var existingmarkertext;
            if (link) {
                existingmarkertext = t.q[questionNo].doc.markerTexts(questionNo).find('span.markertext' + dropzoneno + ' a');
            } else {
                existingmarkertext = t.q[questionNo].doc.markerTexts(questionNo).find('span.markertext' + dropzoneno);
            }
            if (existingmarkertext.length) {
                if (markertext !== '') {
                    existingmarkertext.html(markertext);
                } else {
                    existingmarkertext.remove();
                }
            } else if (markertext !== '') {
                var classnames = 'markertext markertext' + dropzoneno;
                if (link) {
                    t.q[questionNo].doc.markerTexts(questionNo).append('<span class="' + classnames + '"><a href="#">' +
                        markertext + '</a></span>');
                } else {
                    t.q[questionNo].doc.markerTexts(questionNo).append('<span class="' + classnames + '">' +
                        markertext + '</span>');
                }
            }
            // Capitalise shape so we can use nice camel case function names.
            shape = shape.replace(/^./, shape[0].toUpperCase());
            var drawfunc = 'addShape' + shape;
            if (t[drawfunc] instanceof Function) {
                // Function t[drawfunc] creates the shape, and gets shape center coordinates to position the markertext.
                var xyfortext = t[drawfunc](questionNo, dropzoneno, coords, colour);
                if (xyfortext !== null) {
                    var markerspan = t.q[questionNo].topnode.find('div.ddarea div.markertexts span.markertext' + dropzoneno);
                    if (markerspan !== null) {
                        markerspan.css('opacity', '0.6');
                        xyfortext[0] -= markerspan.outerWidth() / 2;
                        xyfortext[1] -= markerspan.outerHeight() / 2;
                        var wxy = t.convertToWindowXY(questionNo, xyfortext);
                        markerspan.css('position', 'absolute').css('left', wxy[0] - 4).css('top', wxy[1]);
                        var markerspananchor = markerspan.find('a');
                        if (markerspananchor !== null) {
                            markerspananchor.on('click', function() {
                                t.q[questionNo].shapes[dropzoneno].attr('fill-opacity', 0.7);
                            });
                            markerspananchor.attr('tabIndex', 0);
                        }
                        // Need to re-draw the svg element for this shape to display, use drawDropzones.
                    }
                }
            }
        },

        /**
         * Draws or re-draws the svg shapes element on the page, appending them to the area.
         * If there is change in the dropzone shape then call this function again.
         * @param {string} questionNo
         */
        drawDropzones: function(questionNo) {
            var width, height, left, top, shapes;
            if (t.q[questionNo].shapes.length < 1) {
                return;
            }
            if (t.q[questionNo].svg) {
                t.q[questionNo].svg.remove();
                t.q[questionNo].svg = null;
            }
            // Insert the svg element and position it directly over the background image.
            width = t.q[questionNo].doc.bgImage().width();
            height = t.q[questionNo].doc.bgImage().height();
            left = t.q[questionNo].doc.bgImage().position().left;
            top = t.q[questionNo].doc.bgImage().position().top;
            shapes = '';
            for (var i in t.q[questionNo].shapes) {
                shapes += t.q[questionNo].shapes[i][0].outerHTML;
            }
            // This is the simple version, that seems to work.
            // It seems there is no need to set the namespace (.attr('xmlns', 'http://www.w3.org/2000/svg');)
            // and certainly there is no need to set the viewBox, width and height attributes to the svg element.
            t.q[questionNo].svg = $('<svg>' + shapes + '</svg>');
            t.q[questionNo].doc.dragItemsArea.append(t.q[questionNo].svg);
            t.q[questionNo].svg
                .css('position', 'absolute')
                // Add 1 to account for the border around the image.
                .css('left', left + 1)
                .css('top', top + 1)
                .css('width', width)
                .css('height', height);
        },

        /**
         * Creates the svg circle shape element and adds it to the shapes array
         * then returns an array containing center coordinates for this shape.
         * @param {string} questionNo
         * @param {string} dropzoneno
         * @param {Array} coords string This consists of "x,y;radius"
         * @param {string} colour
         * @returns {Array} center coordinates
         */
        addShapeCircle: function(questionNo, dropzoneno, coords, colour) {
            var coordsparts = coords.match(/(\d+),(\d+);(\d+)/);
            var shape;
            if (coordsparts && coordsparts.length === 4) {
                var centrex = Number(coordsparts[1]);
                var centrey = Number(coordsparts[2]);
                var radius = Number(coordsparts[3]);
                if (t.coordsInBgImg(questionNo, [centrex - radius, centrey - radius])) {
                    shape = $('<circle id="dz' + dropzoneno + '" cx="' + centrex + '" cy="' + centrey + '" r="' + radius +
                        '" fill="' + colour + '" fill-opacity="0.5" stroke="black" stroke-width="1" />');
                    t.q[questionNo].shapes[dropzoneno] = shape;
                    return [centrex, centrey];
                }
            }
            return null;
        },

        /**
         * Creates the svg rectangle shape element and adds it to the shapes array
         * then returns an array containing center coordinates for this shape.
         * @param {string} questionNo
         * @param {string} dropzoneno
         * @param {Array} coords string This consists of "x,y;w,h"
         * @param {string} colour
         * @returns {Array} center coordinates
         */
        addShapeRectangle: function(questionNo, dropzoneno, coords, colour) {
            var coordsparts = coords.match(/(\d+),(\d+);(\d+),(\d+)/);
            var shape;
            if (coordsparts && coordsparts.length === 5) {
                var x = Number(coordsparts[1]);
                var y = Number(coordsparts[2]);
                var width = Number(coordsparts[3]);
                var height = Number(coordsparts[4]);
                if (this.coordsInBgImg(questionNo, [x + width, y + height])) {
                    shape = $('<rect id="dz' + dropzoneno + '" width="' + width + '" height="' + height +
                        '" x="' + x + '" y="' + y + '" fill="' + colour +
                        '" fill-opacity="0.5" stroke="black" stroke-width="1" />');
                    t.q[questionNo].shapes[dropzoneno] = shape;
                    return [x + width / 2, y + height / 2];
                }
            }
            return null;

        },

        /**
         * Creates the svg polygon shape element and adds it to the shapes array
         * then returns an array containing center coordinates for this shape.
         * @param {string} questionNo
         * @param {string} dropzoneno
         * @param {Array} coords string This consists of a set of coords "x,y;x1,y1;..."
         * @param {string} colour
         * @returns {Array} center coordinates
         */
        addShapePolygon: function(questionNo, dropzoneno, coords, colour) {
            var coordsparts = coords.split(';');
            var xy = [];
            var polygon, parts, maxxy, minxy, path;
            var width = t.q[questionNo].doc.bgImage().width();
            var height = t.q[questionNo].doc.bgImage().height();
            var i;
            for (i in coordsparts) {
                parts = coordsparts[i].match(/^(\d+),(\d+)$/);
                if (parts !== null && this.coordsInBgImg(questionNo, [parts[1], parts[2]])) {
                    xy[xy.length] = [parts[1], parts[2]];
                }
            }
            if (xy.length > 2) {
                maxxy = [0, 0];
                minxy = [width, height];
                for (i = 0; i < xy.length; i++) {
                    // Calculate min and max points to find center.
                    minxy[0] = Math.min(xy[i][0], minxy[0]);
                    minxy[1] = Math.min(xy[i][1], minxy[1]);
                    maxxy[0] = Math.max(xy[i][0], maxxy[0]);
                    maxxy[1] = Math.max(xy[i][1], maxxy[1]);
                }
                path = coords.replace(/[,;]/g, ' ');
                polygon = $('<polygon id="dz' + dropzoneno + '" fill="' + colour + '" fill-opacity="0.5" stroke="black"' +
                    ' stroke-width="1" points="' + path + '" />');
                t.q[questionNo].shapes[dropzoneno] = polygon;
                return [(minxy[0] + maxxy[0]) / 2, (minxy[1] + maxxy[1]) / 2];
            }
            return null;
        }
    };

    return t;
});
