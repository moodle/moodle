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
 * JavaScript to allow dragging options to slots (using mouse down or touch) or tab through slots using keyboard.
 *
 * @package    qtype
 * @subpackage ddwtos
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/dragdrop'], function ($, dd) {

    "use strict";

    var t = {
        /**
         * The accuracy of the drag item in brink of a slot to be accepted.
         * @private


         */
        DRAG_PROXIMITY: 10,

        /**
         * Tolerance in px for highlighting the borders of question container
         * when a drag is about to leave the containe's boundaries.
         * @private
         */
        CONTAINER_BORDER_TOLERANCE: 10,

        /**
         * Animation time in milliseconds
         * @private
         */
        ANIMATION_DURATION: 300,

        /**
         * Params object, Todo: I only need to know whether the question is 'readonly'
         * @private
         */
        params: {},

        nextDragItemNo : 1,

        /**
         * Put all our selectors in the same place so we can quickly find and change them later
         * if the structure of the document changes.
         */
        cssSelectors: function (topnode) {
            return {
                topNode: function () {
                    return topnode;
                },
                dragContainer: function () {
                    return topnode + ' div.drags';
                },
                drags: function () {
                    return this.dragContainer() + ' span.drag';
                },
                drag: function (no) {
                    return this.drags() + '.no' + no;
                },
                dragsInGroup: function (groupno) {
                    return this.drags() + '.group' + groupno;
                },
                dragsForChoiceInGroup: function (choiceno, groupno) {
                    return this.dragsInGroup(groupno) + '.choice' + choiceno;
                },
                placedDragsInGroup: function (groupno) {
                    return this.dragsInGroup(groupno) + '.placed';
                },
                unplacedDragsInGroup: function (groupno) {
                    return this.dragsInGroup(groupno) + '.unplaced';
                },
                unplacedDragsForChoiceInGroup: function (choiceno, groupno) {
                    return this.unplacedDragsInGroup(groupno) + '.choice' + choiceno;
                },
                dragHomes: function () {
                    return topnode + ' span.draghome';
                },
                dragHomesGroup: function (groupno) {
                    return topnode + ' .draggrouphomes' + groupno + ' span.draghome';
                },
                dragHome: function (groupno, choiceno) {
                    return topnode + ' .draggrouphomes' + groupno + ' span.draghome.choice' + choiceno;
                },
                // List of selectors related to drops.
                drops: function () {
                    return topnode + ' span.drop';
                },
                dropForPlace: function (placeno) {
                    return this.drops() + '.place' + placeno;
                },
                dropsInGroup: function (groupno) {
                    return this.drops() + '.group' + groupno;
                },
                dropsGroup: function (groupno) {
                    return topnode + ' span.drop.group' + groupno;
                }
            };
        },

        /**
         * Return an array of inputids.
         * @param topNode
         * @returns {Array}
         */
        getInputIds : function (topNode) {
            var inputIds = [];
            var count = 0;
            $(topNode).find('input[type=hidden]').each(function(index, input) {
                var questionIdNumber = parseInt(topNode.match(/\d/g));
                if ($(input)[0].id.indexOf('_' + questionIdNumber + '_p') !== -1) {
                    inputIds[++count] = $(input)[0].id;
                }
            });
            return inputIds;
        },

        /**
         * Return a given inputid
         * @param topNode
         * @param no
         * @returns {*}
         */
        getInputId : function(topNode, no) {
            return t.getInputIds(topNode)[no];
        },

        /**
         * Initialise questions
         * @param params
         */
        init : function (params) {
            var pendingId = 'qtype_ddwtos-' + Math.random().toString(36).slice(2); // Random string.
            M.util.js_pending(pendingId);
            t.params = params;
            t.initQuestion(params['topnode']);
            M.util.js_complete(pendingId);
        },

        /**
         * Update the position on the drags on resize
         * @param string topNode, unique question id
         */
        updateOnResize : function(topNode) {
            $(window).on('resize', function () {
                t.updateDragsPositions(topNode);
            }).resize();
        },

        /**
         * Initialise question.
         * @param topNode
         */
        initQuestion: function (topNode) {
            // Return if question has been initialised.
            if ($(topNode).data('initialised') === 1 ) {
                return;
            }
            t.setPaddingSizesAll(topNode);
            t.cloneDragItems(topNode);
            t.updateDragsPositions(topNode);
            t.updateOnResize(topNode);
            t.makeDropZones(topNode);
            // Remember that the question has been initialised.
            $(topNode).data('initialised', 1);
        },

        /**
         * Set the position of the drag to the position of the drop item.
         * @param topNode
         * @param drag
         * @param drop
         */
        putDragInDrop: function (topNode, drag, drop) {
            var inputNode = $('input#' + t.getInputId(topNode, t.getPlace(drop)));
            if (inputNode.val() > 0) {
                var prevDrag = $(t.cssSelectors(topNode).dragsForChoiceInGroup(inputNode.val(), t.getGroup(drop)));
                if (prevDrag.length) {
                    t.putBackToOrigin(topNode, prevDrag);
                }
            }
            if (drag && drag.length) {
                inputNode.val(t.getChoice(drag));
                t.setDragOffset(drag, drop);
                drag.css('z-index', (Number(drag.css('z-index')) - 1));
            } else {
                inputNode.val(0);
                drag.removeClass('placed');
                drag.addClass('unplaced');
            }
        },

        /**
         * Update the position of drags
         * @param topNode
         */
        updateDragsPositions : function (topNode) {
            var unplacedDrags = $(t.cssSelectors(topNode).drags());
            unplacedDrags.addClass('unplaced');
            for (var i = 0; i < unplacedDrags.length; i++) {
                var unplaceDrag = $(unplacedDrags[i]);
                var groupNo = t.getGroup(unplaceDrag);
                var choiceNo = t.getChoice(unplaceDrag);
                unplaceDrag.offset($(topNode + ' .draggrouphomes' + groupNo + ' span:nth-child(' + choiceNo + ')').offset());
            }
            // Let the drag in target follow the position of the target after resize.
            for (var pn in t.getInputIds(topNode)) {
                //var drop = $(t.cssSelectors(topNode).dropForPlace(pn));
                var inputNode = $('input#' + t.getInputId(topNode, pn));
                var choiceNo = Number(inputNode.val());
                if (choiceNo !== 0) {
                    var drop = $(t.cssSelectors(topNode).dropForPlace(pn));
                    var groupNo = t.getGroup(drop);
                    var drag = $(t.cssSelectors(topNode).unplacedDragsForChoiceInGroup(choiceNo, groupNo));
                    if (drag.hasClass('infinite')) {
                        drag = $(t.cssSelectors(topNode).drag(t.getNo(drag)));
                    }
                    t.setDragOffset(drag, drop);
                }
            }
        },

        /**
         * Invisible 'drag homes' are output by the renderer. These have the same properties
         * as the drag items but are invisible. We clone these invisible elements to make the
         * actual drag items.
         * @param topNode
         */
        cloneDragItems: function(topNode) {
            t.nextDragItemNo = 1;
            $(t.cssSelectors(topNode).dragHomes()).each(function(index, draghome) {
                t.cloneDragItemsForOneChoice(topNode, $(draghome));
            });
        },
        /**
         * Clone drag item for one choice.
         * @param topNode
         * @param draghome
         */
        cloneDragItemsForOneChoice: function(topNode, draghome) {
            if (draghome.hasClass('infinite')) {
                var groupno = t.getGroup(draghome);
                var noofdrags = $(t.cssSelectors(topNode).dropsInGroup(groupno)).length;
                for (var i = 0; i < noofdrags; i++) {
                    t.cloneDragItem(topNode, draghome);
                }
            } else {
                t.cloneDragItem(topNode, draghome);
            }
        },
        /**
         * Clone drag item.
         * @param topNode
         * @param draghome
         */
        cloneDragItem: function(topNode, draghome) {
            var drag = draghome.clone();
            drag.removeClass('draghome');
            drag.addClass('drag');
            drag.addClass('no' + t.nextDragItemNo);
            t.nextDragItemNo++;
            drag.css({
                'top': draghome.offset().top,
                'left': draghome.offset().left,
                'visibility': 'visible',
                'position': 'absolute'
            });
            $(t.cssSelectors(topNode).dragContainer()).append(drag);
            if (!t.params['readonly']) {
                drag.addClass('unplaced');
                drag.on('mousedown touchstart', t.mouseDownOrTouchStart(topNode, drag));
            }
        },

        /**
         * Return a function on mousedown or touchstart.
         * @param dragProxy
         * @returns {Function}
         */
        mouseDownOrTouchStart: function(topNode, dragProxy) {
            return function (e) {
                if (dd.prepare(e).start === true) {
                    e.preventDefault();
                    dragProxy.css('cursor', 'move');
                    dragProxy.addClass('moodle-has-zindex');
                    var choice = t.getChoice(dragProxy);
                    var group = t.getGroup(dragProxy);
                    var draginfo = {};
                    draginfo.id = 'g' + group + 'c' + choice;
                    draginfo.group = group;
                    draginfo.choice = choice;
                    draginfo.drag = dragProxy;
                    draginfo.origin = dragProxy.position();
                    var om = function (x, y) {
                        t.onMove(topNode, x, y, draginfo, dragProxy);
                    };
                    var od = function () {
                        t.onDrop(topNode, draginfo, dragProxy);
                    };
                    dragProxy.css('z-index', (Number(dragProxy.css('z-index')) + 1));
                    dd.start(e, dragProxy, om, od);
                }
            };
        },

        /**
         * Called when user drags a drag item.
         *
         * @param topNode
         * @param x
         * @param y
         * @param draginfo
         * @param dragProxy
         */
        onMove: function (topNode, x, y, draginfo, dragProxy) {
            var container = $(topNode).find('.formulation');
            if (t.isDragOutsideQuestionContainer(container, x, y)) {
                // Highlight the border around the question container.
                container.addClass('outside-container');

                dragProxy.on('mouseup', function() {
                    // Remove the highlighted border around the question container.
                    container.removeClass('outside-container');
                });
                return;
            }
            var drops = $(t.cssSelectors(topNode).dropsInGroup(draginfo.group));
            drops.each(function(index, drop){
                if (t.isDragCloseToTarget(dragProxy, $(drop))) {
                    $(drop).addClass('valid-drag-over-drop');
                } else {
                    $(drop).removeClass('valid-drag-over-drop');
                }
            });
        },

        /**
         * Called when user drops a drag item and applies the change.
         *
         * @param topNode
         * @param draginfo
         * @param dragProxy
         */
        onDrop: function (topNode, draginfo, dragProxy) {
            var drops = $(t.cssSelectors(topNode).dropsInGroup(draginfo.group));
            var breakOut = false;
            drops.each(function(index, drop){
                // If dropping the same drag inside the drop break out.
                if (t.isDragInTheSameDrop(dragProxy, $(drop))) {
                    breakOut = true;
                    return false;
                }
                if (t.isDragCloseToTarget(dragProxy, $(drop))) {
                    $(drop).removeClass('valid-drag-over-drop');
                    t.putDragInDrop(topNode, $(dragProxy), $(drop));

                    breakOut = true;
                    return false;
                }
            });
            if(breakOut) {
                //breakOut = false;
                return false;
            } else {
                t.putBackToOrigin(topNode, dragProxy);
            }
        },

        /**
         * Make drop zones for keyboard access.
         * @param topNode
         */
        makeDropZones: function(topNode) {
            $(t.cssSelectors(topNode).drops()).each(function(index, drop){
                t.makeDropZone(topNode, $(drop));
            });
        },

        /**
         * Make a drop dropn zone to accept key press.
         * @param topNode
         * @param drop
         */
        makeDropZone: function(topNode, drop) {
            if (!t.params['readonly']) {
                drop.on('keydown', function(e) {
                    t.dropZoneKeyPress(topNode, e);
                });
            }
        },

        /**
         * It put back the drag in position
         * @param drag
         */
        putBackToOrigin: function (topNode, drag) {
            drag.removeClass('placed');
            drag.addClass('unplaced');
            var groupNo = t.getGroup(drag);
            var choiceNo = t.getChoice(drag);
            // Find the drag in the list of this draggrouphomes, make the original visible and destroy the cloned drag.
            var dragHome = $(topNode + ' .draggrouphomes' + groupNo + ' span:nth-child(' + choiceNo + ')');
            // Animate the option back to the original position.
            t.animateEl(drag, dragHome, t.ANIMATION_DURATION, 'swing');
        },

        /**
         * Check whether a slot contains an option and returns the option.
         *
         * @param slot the slot object which may contain an option object
         * @returns option object if slot contains an option or null if it doesn't
         */
         isDragInDrop: function(topNode, drop) {
            if (!drop.length) {
                return false;
            }
            // If there is a drag in this drop put it back to origin.
            var dragInDrop = drop.children(topNode + ' span.placed');
            if (dragInDrop.length) {
                //t.putBackToOrigin(dragInDrop);
                return dragInDrop;
            }
            return false;
        },

        isDragOutsideQuestionContainer : function (container, x, y) {
            var containerEdges = {
                'lt': { 'Y': container.offset().top + t.CONTAINER_BORDER_TOLERANCE,
                    'X': container.offset().left + t.CONTAINER_BORDER_TOLERANCE},
                'rt': { 'Y' : container.offset().top + t.CONTAINER_BORDER_TOLERANCE,
                    'X' : container.offset().left + container[0].clientWidth - t.CONTAINER_BORDER_TOLERANCE},
                'lb': { 'Y' : container.offset().top + container[0].clientHeight - t.CONTAINER_BORDER_TOLERANCE,
                    'X' : container.offset().left},
                'rb': { 'Y' : container.offset().top + container[0].clientHeight - t.CONTAINER_BORDER_TOLERANCE,
                    'X' : container.offset().left + container[0].clientWidth - t.CONTAINER_BORDER_TOLERANCE}
            };

            // Check boundaries (I know that these ifs could be simplified as one if, bit i think this is more readable).
            if (y < containerEdges.lt.Y || x < containerEdges.lt.X) {
                $(container).addClass('outside-container');
                return true;
            }
            if (y < containerEdges.rt.Y || x > containerEdges.rt.X) {
                $(container).addClass('outside-container');
                return true;
            }
            if (y > containerEdges.lb.Y || x < containerEdges.lb.X) {
                $(container).addClass('outside-container');
                return true;
            }
            if (y > containerEdges.rb.Y || x > containerEdges.rb.X) {
                $(container).addClass('outside-container');
                return true;
            }
            $(container).removeClass('outside-container');
            return false;
        },

        /**
         * Animate the object to the given destination.
         *
         * @param el object: the object to be animated
         * @param duration int: duration on animation in miliseconds
         * @param easing string: the easing type
         * @param ctop css value of current top
         * @param cleft css value of current left
         * @param top css value of origin top
         * @param left css value of origin left
         */
        animateEl: function (dragBack,  dragHome, duration, easing) {
            var durationFactor = (Math.abs(dragBack.css('left') - dragHome.offset().left) +
                                Math.abs(dragBack.css('top') - dragHome.offset().top));
            durationFactor = (durationFactor > 1) ? durationFactor : 1;

            dragBack.animate(
                {
                    opacity: 0.5,
                    width: (parseInt(dragBack.css('width')) * 1.2) + 'px',
                    height: (parseInt(dragBack.css('height')) * 1.2) + 'px',
                    top: dragHome.offset().top,
                    left: dragHome.offset().left
                },
                {
                    duration: duration / durationFactor,
                    easing: easing
                }
            );
            dragBack.animate(
                {
                    opacity: 1,
                    width: parseInt(dragBack.css('width')) +'px',
                    height: parseInt(dragBack.css('height')) + 'px',
                    top: dragHome.offset().top,
                    left: dragHome.offset().left
                },
                {
                    duration: duration / durationFactor,
                    easing: easing
                }
            );
        },

        /**
         * isDragCloseToTarget checks whether the drag item is close enough to the target
         *
         * @param drag
         * @param target
         * @returns {boolean}
         */
        isDragCloseToTarget: function (drag, target) {
            var midDragX = drag.offset().left + drag.outerWidth() / 2;
            var midDragY = drag.offset().top + drag.outerHeight() / 2;
            var midTargetX = target.offset().left + target.outerWidth() / 2;
            var midTargetY = target.offset().top + target.outerHeight() / 2;
            // Is the drag item close to target?
            if ((midDragX > (midTargetX - t.DRAG_PROXIMITY) &&
                midDragX < (midTargetX + t.DRAG_PROXIMITY)) &&
                (midDragY > (midTargetY - t.DRAG_PROXIMITY) &&
                midDragY < (midTargetY + t.DRAG_PROXIMITY))) {
                return true;
            }
            return false;
        },

        /**
         * Set padding to all drags and drops.
         */
        setPaddingSizesAll: function (topNode) {
            for (var groupno = 1; groupno <= 8; groupno++) {
                t.setPaddingSizeForGroup(topNode, groupno);
            }
        },

        /**
         * Set padding to drags and drops in a group.
         * @param groupNo
         */
        setPaddingSizeForGroup: function (topNode, groupNo) {
            var groupItems = $(t.cssSelectors(topNode).dragHomesGroup(groupNo));
            if (groupItems.length != 0) {
                var maxWidth = 0;
                var maxHeight = 0;
                // Find max height and width
                $(groupItems).each(function (item, groupItem) {
                    maxWidth = Math.max(maxWidth, Math.ceil(groupItem.offsetWidth));
                    maxHeight = Math.max(maxHeight, Math.ceil(groupItem.offsetHeight));
                });
                maxWidth += 8;
                maxHeight += 2;

                $(groupItems).each(function (item, dragItem) {
                    t.padWidthHeight(dragItem, maxWidth, maxHeight);

                    t.positionDrags(topNode, groupNo, dragItem);
                });
                $(t.cssSelectors(topNode).dropsGroup(groupNo)).each(function (item, dropItem) {
                    t.padWidthHeight(dropItem, maxWidth + 2, maxHeight + 2);
                });
            }
        },

        /**
         *
         *
         * @param topNode
         * @param groupNo
         * @param dragItem
         */
        positionDrags: function (topNode, groupNo, dragItem) {
            var dragsingroup = $(t.cssSelectors(topNode).dragsInGroup(groupNo));
            $(t.cssSelectors(topNode).dragContainer()).append($(dragsingroup).append($(dragItem)));
        },

        padWidthHeight: function (node, width, height) {
            $(node).css(
                {
                    'width': width + 'px',
                    'height': height + 'px',
                    'lineHeight': height + 'px'
                }
            );
        },

        /**
         * Return the number at the end of the prefix.
         * @param node
         * @param prefix
         * @returns {*|number}
         */
        getClassnameNumericSuffix: function (node, prefix) {
            var classes = node.attr('class');
            if (classes !== '') {
                var classesArr = classes.split(' ');
                for (var index = 0; index < classesArr.length; index++) {
                    var patt1 = new RegExp('^' + prefix + '([0-9])+$');
                    if (patt1.test(classesArr[index])) {
                        var patt2 = new RegExp('([0-9])+$');
                        var match = patt2.exec(classesArr[index]);
                        return Number(match[0]);
                    }
                }
            }
            throw 'Prefix "' + prefix + '" not found in class names.';
        },
        getChoice: function (node) {
            return t.getClassnameNumericSuffix(node, 'choice');
        },
        getGroup: function (node) {
            return t.getClassnameNumericSuffix(node, 'group');
        },
        getPlace: function (node) {
            return t.getClassnameNumericSuffix(node, 'place');
        },
        getNo: function (node) {
            return t.getClassnameNumericSuffix(node, 'no');
        },

        /**
         * Set drag's offset to the appropriate drop's offset.
         *
         * @param drag
         * @param drop
         */
        setDragOffset : function(drag, drop) {
            drag.removeClass('unplaced');
            drag.addClass('placed');
            drag.offset(
                {
                    'top' : Math.round(drop.offset().top) + 1,
                    'left' : Math.round(drop.offset().left) + 1
                }
            );
        },

        /**
         * Check whether user drop the current drag is in the same drop
         * @param drag
         * @param drop
         * @returns {boolean}
         */
        isDragInTheSameDrop: function (drag, drop) {
            if (parseInt(drag.css('top')) === Math.round(drop.offset().top) + 1) {
                return true;
            }
            return false;
        },

        /**
         * Remove drag from drop.
         * @param drop
         */
        removeDragFromDrop: function (topNode, drop) {
            t.putDragInDrop(topNode, null, drop);
        },

        /**
         * Tab through drops and place drag in drop or remove drag from drop using keybord.
         * @param e
         */
        dropZoneKeyPress: function(topNode, e) {
            var keys =  {
                '27': 'remove',   // Escape
                '32': 'next',     // Space
                '37': 'previous', // Left arrow
                '38': 'previous', // Up arrow
                '39': 'next',     // Right arrow
                '40': 'next'      // Down arrow
            };
            e.direction = keys[e.keyCode];
            //e.preventDefault();
            switch (e.direction) {
                case 'next' :
                    e.preventDefault();
                    t.placeNextDragIn(topNode, $(e.target));
                    break;
                case 'previous' :
                    e.preventDefault();
                    t.placePreviousDragIn(topNode, $(e.target));
                    break;
                case 'remove' :
                    e.preventDefault();
                    t.removeDragFromDrop(topNode, $(e.target));
                    break;
            }
        },

        /**
         * Place next drag in drop.
         * @param drop
         */
        placeNextDragIn: function(topNode, drop) {
            t.chooseNextChoiceForDrop(topNode, drop, 1);
        },

        /**
         * Place previous drag in drop.
         * @param drop
         */
        placePreviousDragIn: function(topNode, drop) {
            t.chooseNextChoiceForDrop(topNode, drop, -1);
        },

        /**
         * Choose the next drag to put in drop.
         * @param drop
         * @param direction
         */
        chooseNextChoiceForDrop: function(topNode, drop, direction) {
            var groupNo = t.getGroup(drop);
            var current = t.currentChoiceInDrop(topNode, $(drop));
            var unplacedDragsInGroup = $(t.cssSelectors(topNode).unplacedDragsInGroup(groupNo));
            var next = current;
            var arrayOfDrags = unplacedDragsInGroup.toArray();
            if (0 === current) {
                if (direction === 1) {
                    next = t.getChoice($(arrayOfDrags.shift()));
                } else {
                    next = t.getChoice($(arrayOfDrags.pop()));
                }
            } else {
                next = current + direction;
            }
            var drag = $(t.cssSelectors(topNode).unplacedDragsForChoiceInGroup(next, groupNo));
            if (drag.length === 0) {
                drag = $(t.cssSelectors(topNode).unplacedDragsForChoiceInGroup(next + direction, groupNo));
            }
            if ($(t.cssSelectors(topNode).dragsForChoiceInGroup(next, groupNo)) === null) {
                t.removeDragFromDrop(topNode, drop);
                return;
            } else {
                if (drag.hasClass('infinite')) {
                    var bOut = false; // Break out of each loop boolean variable.
                    drag.each(function (index, draginf) {
                        if ($(draginf).hasClass('unplaced')) {
                            t.putDragInDrop(topNode, $(draginf), drop);
                            bOut = true;
                            return false;
                        }
                    });
                    if (bOut) {
                        bOut = false;
                        return false;
                    }
                } else {
                    t.putDragInDrop(topNode, drag, drop);
                }
            }
        },

        /**
         * Return curent drag choice in drop.
         *
         * @param drop
         * @returns {*|number}
         */
        currentChoiceInDrop: function(topNode, drop) {
            var inputNode = $('input#' + t.getInputId(topNode, t.getPlace(drop)));
            return Number($(inputNode).val());
        }
    };
    return t;
});
