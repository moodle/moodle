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
 * JavaScript to make drag-drop into text questions work.
 *
 * Some vocabulary to help understand this code:
 *
 * The question text contains 'drops' - blanks into which the 'drags', the missing
 * words, can be put.
 *
 * The thing that can be moved into the drops are called 'drags'. There may be
 * multiple copies of the 'same' drag which does not really cause problems.
 * Each drag has a 'choice' number which is the value set on the drop's hidden
 * input when this drag is placed in a drop.
 *
 * These may be in separate 'groups', distinguished by colour.
 * Things can only interact with other things in the same group.
 * The groups are numbered from 1.
 *
 * The place where a given drag started from is called its 'home'.
 *
 * @module     qtype_ddwtos/ddwtos
 * @package    qtype_ddwtos
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.6
 */
define(['jquery', 'core/dragdrop', 'core/key_codes'], function($, dragDrop, keys) {

    "use strict";

    /**
     * Object to handle one drag-drop into text question.
     *
     * @param {String} containerId id of the outer div for this question.
     * @param {boolean} readOnly whether the question is being displayed read-only.
     * @constructor
     */
    function DragDropToTextQuestion(containerId, readOnly) {
        this.containerId = containerId;
        if (readOnly) {
            this.getRoot().addClass('qtype_ddwtos-readonly');
        }
        this.resizeAllDragsAndDrops();
        this.cloneDrags();
        this.positionDrags();
    }

    /**
     * In each group, resize all the items to be the same size.
     */
    DragDropToTextQuestion.prototype.resizeAllDragsAndDrops = function() {
        var thisQ = this;
        this.getRoot().find('.answercontainer > div').each(function(i, node) {
            thisQ.resizeAllDragsAndDropsInGroup(
                thisQ.getClassnameNumericSuffix($(node), 'draggrouphomes'));
        });
    };

    /**
     * In a given group, set all the drags and drops to be the same size.
     *
     * @param {int} group the group number.
     */
    DragDropToTextQuestion.prototype.resizeAllDragsAndDropsInGroup = function(group) {
        var thisQ = this,
            dragHomes = this.getRoot().find('.draggrouphomes' + group + ' span.draghome'),
            maxWidth = 0,
            maxHeight = 0;

        // Find the maximum size of any drag in this groups.
        dragHomes.each(function(i, drag) {
            maxWidth = Math.max(maxWidth, Math.ceil(drag.offsetWidth));
            maxHeight = Math.max(maxHeight, Math.ceil(0 + drag.offsetHeight));
        });

        // The size we will want to set is a bit bigger than this.
        maxWidth += 8;
        maxHeight += 2;

        // Set each drag home to that size.
        dragHomes.each(function(i, drag) {
            thisQ.setElementSize(drag, maxWidth, maxHeight);
        });

        // Set each drop to that size.
        this.getRoot().find('span.drop.group' + group).each(function(i, drop) {
            thisQ.setElementSize(drop, maxWidth, maxHeight);
        });
    };

    /**
     * Set a given DOM element to be a particular size.
     *
     * @param {HTMLElement} element
     * @param {int} width
     * @param {int} height
     */
    DragDropToTextQuestion.prototype.setElementSize = function(element, width, height) {
        $(element).width(width).height(height).css('lineHeight', height + 'px');
    };

    /**
     * Invisible 'drag homes' are output by the renderer. These have the same properties
     * as the drag items but are invisible. We clone these invisible elements to make the
     * actual drag items.
     */
    DragDropToTextQuestion.prototype.cloneDrags = function() {
        var thisQ = this;
        thisQ.getRoot().find('span.draghome').each(function(index, draghome) {
            var drag = $(draghome);
            var placeHolder = drag.clone();
            placeHolder.removeClass();
            placeHolder.addClass('draghome choice' +
                thisQ.getChoice(drag) + ' group' +
                thisQ.getGroup(drag) + ' dragplaceholder');
            drag.before(placeHolder);
        });
    };

    /**
     * Update the position of drags.
     */
    DragDropToTextQuestion.prototype.positionDrags = function() {
        var thisQ = this,
            root = this.getRoot();

        // First move all items back home.
        root.find('span.draghome').not('.dragplaceholder').each(function(i, dragNode) {
            var drag = $(dragNode),
                currentPlace = thisQ.getClassnameNumericSuffix(drag, 'inplace');
            drag.addClass('unplaced')
                .removeClass('placed');
            drag.removeAttr('tabindex');
            if (currentPlace !== null) {
                drag.removeClass('inplace' + currentPlace);
            }
        });

        // Then place the once that should be placed.
        root.find('input.placeinput').each(function(i, inputNode) {
            var input = $(inputNode),
                choice = input.val(),
                place = thisQ.getPlace(input);

            // Record the last known position of the drop.
            var drop = root.find('.drop.place' + place),
                dropPosition = drop.offset();
            drop.data('prev-top', dropPosition.top).data('prev-left', dropPosition.left);

            if (choice === '0') {
                // No item in this place.
                return;
            }

            // Get the unplaced drag.
            var unplacedDrag = thisQ.getUnplacedChoice(thisQ.getGroup(input), choice);
            // Get the clone of the drag.
            var hiddenDrag = thisQ.getDragClone(unplacedDrag);
            if (hiddenDrag.length) {
                hiddenDrag.addClass('active');
            }
            // Send the drag to drop.
            thisQ.sendDragToDrop(thisQ.getUnplacedChoice(thisQ.getGroup(input), choice), drop);
        });
    };

    /**
     * Handles the start of dragging an item.
     *
     * @param {Event} e the touch start or mouse down event.
     */
    DragDropToTextQuestion.prototype.handleDragStart = function(e) {
        var thisQ = this,
            drag = $(e.target).closest('.draghome');

        var info = dragDrop.prepare(e);
        if (!info.start) {
            return;
        }

        drag.addClass('beingdragged');
        var currentPlace = this.getClassnameNumericSuffix(drag, 'inplace');
        if (currentPlace !== null) {
            this.setInputValue(currentPlace, 0);
            drag.removeClass('inplace' + currentPlace);
            var hiddenDrop = thisQ.getDrop(drag, currentPlace);
            if (hiddenDrop.length) {
                hiddenDrop.addClass('active');
                drag.offset(hiddenDrop.offset());
            }
        } else {
            var hiddenDrag = thisQ.getDragClone(drag);
            if (hiddenDrag.length) {
                if (drag.hasClass('infinite')) {
                    var noOfDrags = this.noOfDropsInGroup(this.getGroup(drag));
                    var cloneDrags = this.getInfiniteDragClones(drag, false);
                    if (cloneDrags.length < noOfDrags) {
                        var cloneDrag = drag.clone();
                        cloneDrag.removeClass('beingdragged');
                        hiddenDrag.after(cloneDrag);
                        questionManager.addEventHandlersToDrag(cloneDrag);
                        drag.offset(cloneDrag.offset());
                    } else {
                        hiddenDrag.addClass('active');
                        drag.offset(hiddenDrag.offset());
                    }
                } else {
                    hiddenDrag.addClass('active');
                    drag.offset(hiddenDrag.offset());
                }
            }
        }

        dragDrop.start(e, drag, function(x, y, drag) {
            thisQ.dragMove(x, y, drag);
        }, function(x, y, drag) {
            thisQ.dragEnd(x, y, drag);
        });
    };

    /**
     * Called whenever the currently dragged items moves.
     *
     * @param {Number} pageX the x position.
     * @param {Number} pageY the y position.
     * @param {jQuery} drag the item being moved.
     */
    DragDropToTextQuestion.prototype.dragMove = function(pageX, pageY, drag) {
        var thisQ = this;
        this.getRoot().find('span.drop.group' + this.getGroup(drag)).each(function(i, dropNode) {
            var drop = $(dropNode);
            if (thisQ.isPointInDrop(pageX, pageY, drop)) {
                drop.addClass('valid-drag-over-drop');
            } else {
                drop.removeClass('valid-drag-over-drop');
            }
        });
        this.getRoot().find('span.draghome.placed.group' + this.getGroup(drag)).not('.beingdragged').each(function(i, dropNode) {
            var drop = $(dropNode);
            if (thisQ.isPointInDrop(pageX, pageY, drop)) {
                drop.addClass('valid-drag-over-drop');
            } else {
                drop.removeClass('valid-drag-over-drop');
            }
        });
    };

    /**
     * Called when user drops a drag item.
     *
     * @param {Number} pageX the x position.
     * @param {Number} pageY the y position.
     * @param {jQuery} drag the item being moved.
     */
    DragDropToTextQuestion.prototype.dragEnd = function(pageX, pageY, drag) {
        var thisQ = this,
            root = this.getRoot(),
            placed = false;
        root.find('span.drop.group' + this.getGroup(drag)).each(function(i, dropNode) {
            var drop = $(dropNode);
            if (!thisQ.isPointInDrop(pageX, pageY, drop)) {
                // Not this drop.
                return true;
            }

            // Now put this drag into the drop.
            drop.removeClass('valid-drag-over-drop');
            thisQ.sendDragToDrop(drag, drop);
            placed = true;
            return false; // Stop the each() here.
        });

        root.find('span.draghome.placed.group' + this.getGroup(drag)).not('.beingdragged').each(function(i, placedNode) {
            var placedDrag = $(placedNode);
            if (!thisQ.isPointInDrop(pageX, pageY, placedDrag)) {
                // Not this placed drag.
                return true;
            }

            // Now put this drag into the drop.
            placedDrag.removeClass('valid-drag-over-drop');
            var currentPlace = thisQ.getClassnameNumericSuffix(placedDrag, 'inplace');
            var drop = thisQ.getDrop(drag, currentPlace);
            thisQ.sendDragToDrop(drag, drop);
            placed = true;
            return false; // Stop the each() here.
        });

        if (!placed) {
            this.sendDragHome(drag);
        }
    };

    /**
     * Animate a drag item into a given place (or back home).
     *
     * @param {jQuery|null} drag the item to place. If null, clear the place.
     * @param {jQuery} drop the place to put it.
     */
    DragDropToTextQuestion.prototype.sendDragToDrop = function(drag, drop) {
        // Is there already a drag in this drop? if so, evict it.
        var oldDrag = this.getCurrentDragInPlace(this.getPlace(drop));
        if (oldDrag.length !== 0) {
            var currentPlace = this.getClassnameNumericSuffix(oldDrag, 'inplace');
            var hiddenDrop = this.getDrop(oldDrag, currentPlace);
            hiddenDrop.addClass('active');
            oldDrag.addClass('beingdragged');
            oldDrag.offset(hiddenDrop.offset());
            this.sendDragHome(oldDrag);
        }

        if (drag.length === 0) {
            this.setInputValue(this.getPlace(drop), 0);
            if (drop.data('isfocus')) {
                drop.focus();
            }
        } else {
            this.setInputValue(this.getPlace(drop), this.getChoice(drag));
            drag.removeClass('unplaced')
                .addClass('placed inplace' + this.getPlace(drop));
            drag.attr('tabindex', 0);
            this.animateTo(drag, drop);
        }
    };

    /**
     * Animate a drag back to its home.
     *
     * @param {jQuery} drag the item being moved.
     */
    DragDropToTextQuestion.prototype.sendDragHome = function(drag) {
        var currentPlace = this.getClassnameNumericSuffix(drag, 'inplace');
        if (currentPlace !== null) {
            drag.removeClass('inplace' + currentPlace);
        }
        drag.data('unplaced', true);

        this.animateTo(drag, this.getDragHome(this.getGroup(drag), this.getChoice(drag)));
    };

    /**
     * Handles keyboard events on drops.
     *
     * Drops are focusable. Once focused, right/down/space switches to the next choice, and
     * left/up switches to the previous. Escape clear.
     *
     * @param {KeyboardEvent} e
     */
    DragDropToTextQuestion.prototype.handleKeyPress = function(e) {
        var drop = $(e.target).closest('.drop');
        if (drop.length === 0) {
            var placedDrag = $(e.target);
            var currentPlace = this.getClassnameNumericSuffix(placedDrag, 'inplace');
            if (currentPlace !== null) {
                drop = this.getDrop(placedDrag, currentPlace);
            }
        }
        var currentDrag = this.getCurrentDragInPlace(this.getPlace(drop)),
            nextDrag = $();

        switch (e.keyCode) {
            case keys.space:
            case keys.arrowRight:
            case keys.arrowDown:
                nextDrag = this.getNextDrag(this.getGroup(drop), currentDrag);
                break;

            case keys.arrowLeft:
            case keys.arrowUp:
                nextDrag = this.getPreviousDrag(this.getGroup(drop), currentDrag);
                break;

            case keys.escape:
                break;

            default:
                questionManager.isKeyboardNavigation = false;
                return; // To avoid the preventDefault below.
        }

        if (nextDrag.length) {
            nextDrag.data('isfocus', true);
            nextDrag.addClass('beingdragged');
            var hiddenDrag = this.getDragClone(nextDrag);
            if (hiddenDrag.length) {
                if (nextDrag.hasClass('infinite')) {
                    var noOfDrags = this.noOfDropsInGroup(this.getGroup(nextDrag));
                    var cloneDrags = this.getInfiniteDragClones(nextDrag, false);
                    if (cloneDrags.length < noOfDrags) {
                        var cloneDrag = nextDrag.clone();
                        cloneDrag.removeClass('beingdragged');
                        cloneDrag.removeAttr('tabindex');
                        hiddenDrag.after(cloneDrag);
                        questionManager.addEventHandlersToDrag(cloneDrag);
                        nextDrag.offset(cloneDrag.offset());
                    } else {
                        hiddenDrag.addClass('active');
                        nextDrag.offset(hiddenDrag.offset());
                    }
                } else {
                    hiddenDrag.addClass('active');
                    nextDrag.offset(hiddenDrag.offset());
                }
            }
        } else {
            drop.data('isfocus', true);
        }

        e.preventDefault();
        this.sendDragToDrop(nextDrag, drop);
    };

    /**
     * Choose the next drag in a group.
     *
     * @param {int} group which group.
     * @param {jQuery} drag current choice (empty jQuery if there isn't one).
     * @return {jQuery} the next drag in that group, or null if there wasn't one.
     */
    DragDropToTextQuestion.prototype.getNextDrag = function(group, drag) {
        var choice,
            numChoices = this.noOfChoicesInGroup(group);

        if (drag.length === 0) {
            choice = 1; // Was empty, so we want to select the first choice.
        } else {
            choice = this.getChoice(drag) + 1;
        }

        var next = this.getUnplacedChoice(group, choice);
        while (next.length === 0 && choice < numChoices) {
            choice++;
            next = this.getUnplacedChoice(group, choice);
        }

        return next;
    };

    /**
     * Choose the previous drag in a group.
     *
     * @param {int} group which group.
     * @param {jQuery} drag current choice (empty jQuery if there isn't one).
     * @return {jQuery} the next drag in that group, or null if there wasn't one.
     */
    DragDropToTextQuestion.prototype.getPreviousDrag = function(group, drag) {
        var choice;

        if (drag.length === 0) {
            choice = this.noOfChoicesInGroup(group);
        } else {
            choice = this.getChoice(drag) - 1;
        }

        var previous = this.getUnplacedChoice(group, choice);
        while (previous.length === 0 && choice > 1) {
            choice--;
            previous = this.getUnplacedChoice(group, choice);
        }

        // Does this choice exist?
        return previous;
    };

    /**
     * Animate an object to the given destination.
     *
     * @param {jQuery} drag the element to be animated.
     * @param {jQuery} target element marking the place to move it to.
     */
    DragDropToTextQuestion.prototype.animateTo = function(drag, target) {
        var currentPos = drag.offset(),
            targetPos = target.offset(),
            thisQ = this;

        M.util.js_pending('qtype_ddwtos-animate-' + thisQ.containerId);
        // Animate works in terms of CSS position, whereas locating an object
        // on the page works best with jQuery offset() function. So, to get
        // the right target position, we work out the required change in
        // offset() and then add that to the current CSS position.
        drag.animate(
            {
                left: parseInt(drag.css('left')) + targetPos.left - currentPos.left,
                top: parseInt(drag.css('top')) + targetPos.top - currentPos.top
            },
            {
                duration: 'fast',
                done: function() {
                    $('body').trigger('qtype_ddwtos-dragmoved', [drag, target, thisQ]);
                    M.util.js_complete('qtype_ddwtos-animate-' + thisQ.containerId);
                }
            }
        );
    };

    /**
     * Detect if a point is inside a given DOM node.
     *
     * @param {Number} pageX the x position.
     * @param {Number} pageY the y position.
     * @param {jQuery} drop the node to check (typically a drop).
     * @return {boolean} whether the point is inside the node.
     */
    DragDropToTextQuestion.prototype.isPointInDrop = function(pageX, pageY, drop) {
        var position = drop.offset();
        return pageX >= position.left && pageX < position.left + drop.width()
                && pageY >= position.top && pageY < position.top + drop.height();
    };

    /**
     * Set the value of the hidden input for a place, to record what is currently there.
     *
     * @param {int} place which place to set the input value for.
     * @param {int} choice the value to set.
     */
    DragDropToTextQuestion.prototype.setInputValue = function(place, choice) {
        this.getRoot().find('input.placeinput.place' + place).val(choice);
    };

    /**
     * Get the outer div for this question.
     *
     * @returns {jQuery} containing that div.
     */
    DragDropToTextQuestion.prototype.getRoot = function() {
        return $(document.getElementById(this.containerId));
    };

    /**
     * Get drag home for a given choice.
     *
     * @param {int} group the group.
     * @param {int} choice the choice number.
     * @returns {jQuery} containing that div.
     */
    DragDropToTextQuestion.prototype.getDragHome = function(group, choice) {
        if (!this.getRoot().find('.draghome.dragplaceholder.group' + group + '.choice' + choice).is(':visible')) {
            return this.getRoot().find('.draggrouphomes' + group +
                ' span.draghome.infinite' +
                '.choice' + choice +
                '.group' + group);
        }
        return this.getRoot().find('.draghome.dragplaceholder.group' + group + '.choice' + choice);
    };

    /**
     * Get an unplaced choice for a particular group.
     *
     * @param {int} group the group.
     * @param {int} choice the choice number.
     * @returns {jQuery} jQuery wrapping the unplaced choice. If there isn't one, the jQuery will be empty.
     */
    DragDropToTextQuestion.prototype.getUnplacedChoice = function(group, choice) {
        return this.getRoot().find('.draghome.group' + group + '.choice' + choice + '.unplaced').slice(0, 1);
    };

    /**
     * Get the drag that is currently in a given place.
     *
     * @param {int} place the place number.
     * @return {jQuery} the current drag (or an empty jQuery if none).
     */
    DragDropToTextQuestion.prototype.getCurrentDragInPlace = function(place) {
        return this.getRoot().find('span.draghome.inplace' + place);
    };

    /**
     * Return the number of blanks in a given group.
     *
     * @param {int} group the group number.
     * @returns {int} the number of drops.
     */
    DragDropToTextQuestion.prototype.noOfDropsInGroup = function(group) {
        return this.getRoot().find('.drop.group' + group).length;
    };

    /**
     * Return the number of choices in a given group.
     *
     * @param {int} group the group number.
     * @returns {int} the number of choices.
     */
    DragDropToTextQuestion.prototype.noOfChoicesInGroup = function(group) {
        return this.getRoot().find('.draghome.group' + group).length;
    };

    /**
     * Return the number at the end of the CSS class name with the given prefix.
     *
     * @param {jQuery} node
     * @param {String} prefix name prefix
     * @returns {Number|null} the suffix if found, else null.
     */
    DragDropToTextQuestion.prototype.getClassnameNumericSuffix = function(node, prefix) {
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
        return null;
    };

    /**
     * Get the choice number of a drag.
     *
     * @param {jQuery} drag the drag.
     * @returns {Number} the choice number.
     */
    DragDropToTextQuestion.prototype.getChoice = function(drag) {
        return this.getClassnameNumericSuffix(drag, 'choice');
    };

    /**
     * Given a DOM node that is significant to this question
     * (drag, drop, ...) get the group it belongs to.
     *
     * @param {jQuery} node a DOM node.
     * @returns {Number} the group it belongs to.
     */
    DragDropToTextQuestion.prototype.getGroup = function(node) {
        return this.getClassnameNumericSuffix(node, 'group');
    };

    /**
     * Get the place number of a drop, or its corresponding hidden input.
     *
     * @param {jQuery} node the DOM node.
     * @returns {Number} the place number.
     */
    DragDropToTextQuestion.prototype.getPlace = function(node) {
        return this.getClassnameNumericSuffix(node, 'place');
    };

    /**
     * Get drag clone for a given drag.
     *
     * @param {jQuery} drag the drag.
     * @returns {jQuery} the drag's clone.
     */
    DragDropToTextQuestion.prototype.getDragClone = function(drag) {
        return this.getRoot().find('.draggrouphomes' +
            this.getGroup(drag) +
            ' span.draghome' +
            '.choice' + this.getChoice(drag) +
            '.group' + this.getGroup(drag) +
            '.dragplaceholder');
    };

    /**
     * Get infinite drag clones for given drag.
     *
     * @param {jQuery} drag the drag.
     * @param {Boolean} inHome in the home area or not.
     * @returns {jQuery} the drag's clones.
     */
    DragDropToTextQuestion.prototype.getInfiniteDragClones = function(drag, inHome) {
        if (inHome) {
            return this.getRoot().find('.draggrouphomes' +
                this.getGroup(drag) +
                ' span.draghome' +
                '.choice' + this.getChoice(drag) +
                '.group' + this.getGroup(drag) +
                '.infinite').not('.dragplaceholder');
        }
        return this.getRoot().find('span.draghome' +
            '.choice' + this.getChoice(drag) +
            '.group' + this.getGroup(drag) +
            '.infinite').not('.dragplaceholder');
    };

    /**
     * Get drop for a given drag and place.
     *
     * @param {jQuery} drag the drag.
     * @param {Integer} currentPlace the current place of drag.
     * @returns {jQuery} the drop's clone.
     */
    DragDropToTextQuestion.prototype.getDrop = function(drag, currentPlace) {
        return this.getRoot().find('.drop.group' + this.getGroup(drag) + '.place' + currentPlace);
    };

    /**
     * Singleton that tracks all the DragDropToTextQuestions on this page, and deals
     * with event dispatching.
     *
     * @type {Object}
     */
    var questionManager = {
        /**
         * {boolean} used to ensure the event handlers are only initialised once per page.
         */
        eventHandlersInitialised: false,

        /**
         * {boolean} is keyboard navigation or not.
         */
        isKeyboardNavigation: false,

        /**
         * {DragDropToTextQuestion[]} all the questions on this page, indexed by containerId (id on the .que div).
         */
        questions: {},

        /**
         * Initialise questions.
         *
         * @param {String} containerId id of the outer div for this question.
         * @param {boolean} readOnly whether the question is being displayed read-only.
         */
        init: function(containerId, readOnly) {
            questionManager.questions[containerId] = new DragDropToTextQuestion(containerId, readOnly);
            if (!questionManager.eventHandlersInitialised) {
                questionManager.setupEventHandlers();
                questionManager.eventHandlersInitialised = true;
            }
        },

        /**
         * Set up the event handlers that make this question type work. (Done once per page.)
         */
        setupEventHandlers: function() {
            // We do not use the body event here to prevent the other event on Mobile device, such as scroll event.
            questionManager.addEventHandlersToDrag($('.que.ddwtos:not(.qtype_ddwtos-readonly) span.draghome'));
            $('body')
                .on('keydown',
                    '.que.ddwtos:not(.qtype_ddwtos-readonly) span.drop',
                    questionManager.handleKeyPress)
                .on('keydown',
                    '.que.ddwtos:not(.qtype_ddwtos-readonly) span.draghome.placed:not(.beingdragged)',
                    questionManager.handleKeyPress)
                .on('qtype_ddwtos-dragmoved', questionManager.handleDragMoved);
        },

        /**
         * Binding the drag/touch event again for newly created element.
         *
         * @param {jQuery} element Element to bind the event
         */
        addEventHandlersToDrag: function(element) {
            element.on('mousedown touchstart', questionManager.handleDragStart);
        },

        /**
         * Handle mouse down / touch start on drags.
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
         * Handle key down / press on drops.
         * @param {KeyboardEvent} e
         */
        handleKeyPress: function(e) {
            if (questionManager.isKeyboardNavigation) {
                return;
            }
            questionManager.isKeyboardNavigation = true;
            var question = questionManager.getQuestionForEvent(e);
            if (question) {
                question.handleKeyPress(e);
            }
        },

        /**
         * Given an event, work out which question it affects.
         *
         * @param {Event} e the event.
         * @returns {DragDropToTextQuestion|undefined} The question, or undefined.
         */
        getQuestionForEvent: function(e) {
            var containerId = $(e.currentTarget).closest('.que.ddwtos').attr('id');
            return questionManager.questions[containerId];
        },

        /**
         * Handle when drag moved.
         *
         * @param {Event} e the event.
         * @param {jQuery} drag the drag
         * @param {jQuery} target the target
         * @param {DragDropToTextQuestion} thisQ the question.
         */
        handleDragMoved: function(e, drag, target, thisQ) {
            drag.removeClass('beingdragged');
            drag.css('top', '').css('left', '');
            target.after(drag);
            target.removeClass('active');
            if (typeof drag.data('unplaced') !== 'undefined' && drag.data('unplaced') === true) {
                drag.removeClass('placed').addClass('unplaced');
                drag.removeAttr('tabindex');
                drag.removeData('unplaced');
                if (drag.hasClass('infinite') && thisQ.getInfiniteDragClones(drag, true).length > 1) {
                    thisQ.getInfiniteDragClones(drag, true).first().remove();
                }
            }
            if (typeof drag.data('isfocus') !== 'undefined' && drag.data('isfocus') === true) {
                drag.focus();
                drag.removeData('isfocus');
            }
            if (typeof target.data('isfocus') !== 'undefined' && target.data('isfocus') === true) {
                target.removeData('isfocus');
            }
            if (questionManager.isKeyboardNavigation) {
                questionManager.isKeyboardNavigation = false;
            }
        }
    };

    /**
     * @alias module:qtype_ddwtos/ddwtos
     */
    return {
        /**
         * Initialise one drag-drop into text question.
         *
         * @param {String} containerId id of the outer div for this question.
         * @param {boolean} readOnly whether the question is being displayed read-only.
         */
        init: questionManager.init
    };
});
