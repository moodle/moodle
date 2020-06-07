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
 * @module     qtype_ddimageortext/question
 * @package    qtype_ddimageortext
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/dragdrop', 'core/key_codes'], function($, dragDrop, keys) {

    "use strict";

    /**
     * Initialise one drag-drop onto image question.
     *
     * @param {String} containerId id of the outer div for this question.
     * @param {boolean} readOnly whether the question is being displayed read-only.
     * @param {Array} places Information about the drop places.
     * @constructor
     */
    function DragDropOntoImageQuestion(containerId, readOnly, places) {
        this.containerId = containerId;
        M.util.js_pending('qtype_ddimageortext-init-' + this.containerId);
        this.places = places;
        this.allImagesLoaded = false;
        this.imageLoadingTimeoutId = null;
        this.isPrinting = false;
        if (readOnly) {
            this.getRoot().addClass('qtype_ddimageortext-readonly');
        }

        var thisQ = this;
        this.getNotYetLoadedImages().one('load', function() {
            thisQ.waitForAllImagesToBeLoaded();
        });
        this.waitForAllImagesToBeLoaded();
    }

    /**
     * Waits until all images are loaded before calling setupQuestion().
     *
     * This function is called from the onLoad of each image, and also polls with
     * a time-out, because image on-loads are allegedly unreliable.
     */
    DragDropOntoImageQuestion.prototype.waitForAllImagesToBeLoaded = function() {
        var thisQ = this;

        // This method may get called multiple times (via image on-loads or timeouts.
        // If we are already done, don't do it again.
        if (this.allImagesLoaded) {
            return;
        }

        // Clear any current timeout, if set.
        if (this.imageLoadingTimeoutId !== null) {
            clearTimeout(this.imageLoadingTimeoutId);
        }

        // If we have not yet loaded all images, set a timeout to
        // call ourselves again, since apparently images on-load
        // events are flakey.
        if (this.getNotYetLoadedImages().length > 0) {
            this.imageLoadingTimeoutId = setTimeout(function() {
                thisQ.waitForAllImagesToBeLoaded();
            }, 100);
            return;
        }

        // We now have all images. Carry on, but only after giving the layout a chance to settle down.
        this.allImagesLoaded = true;
        thisQ.setupQuestion();
    };

    /**
     * Get any of the images in the drag-drop area that are not yet fully loaded.
     *
     * @returns {jQuery} those images.
     */
    DragDropOntoImageQuestion.prototype.getNotYetLoadedImages = function() {
        var thisQ = this;
        return this.getRoot().find('.ddarea img').not(function(i, imgNode) {
            return thisQ.imageIsLoaded(imgNode);
        });
    };

    /**
     * Check if an image has loaded without errors.
     *
     * @param {HTMLImageElement} imgElement an image.
     * @returns {boolean} true if this image has loaded without errors.
     */
    DragDropOntoImageQuestion.prototype.imageIsLoaded = function(imgElement) {
        return imgElement.complete && imgElement.naturalHeight !== 0;
    };

    /**
     * Set up the question, once all images have been loaded.
     */
    DragDropOntoImageQuestion.prototype.setupQuestion = function() {
        this.resizeAllDragsAndDrops();
        this.cloneDrags();
        this.positionDragsAndDrops();
        M.util.js_complete('qtype_ddimageortext-init-' + this.containerId);
    };

    /**
     * In each group, resize all the items to be the same size.
     */
    DragDropOntoImageQuestion.prototype.resizeAllDragsAndDrops = function() {
        var thisQ = this;
        this.getRoot().find('.draghomes > div').each(function(i, node) {
            thisQ.resizeAllDragsAndDropsInGroup(
                    thisQ.getClassnameNumericSuffix($(node), 'dragitemgroup'));
        });
    };

    /**
     * In a given group, set all the drags and drops to be the same size.
     *
     * @param {int} group the group number.
     */
    DragDropOntoImageQuestion.prototype.resizeAllDragsAndDropsInGroup = function(group) {
        var root = this.getRoot(),
            dragHomes = root.find('.dragitemgroup' + group + ' .draghome'),
            maxWidth = 0,
            maxHeight = 0;

        // Find the maximum size of any drag in this groups.
        dragHomes.each(function(i, drag) {
            maxWidth = Math.max(maxWidth, Math.ceil(drag.offsetWidth));
            maxHeight = Math.max(maxHeight, Math.ceil(drag.offsetHeight));
        });

        // The size we will want to set is a bit bigger than this.
        maxWidth += 10;
        maxHeight += 10;

        // Set each drag home to that size.
        dragHomes.each(function(i, drag) {
            var left = Math.round((maxWidth - drag.offsetWidth) / 2),
                top = Math.floor((maxHeight - drag.offsetHeight) / 2);
            // Set top and left padding so the item is centred.
            $(drag).css({
                'padding-left': left + 'px',
                'padding-right': (maxWidth - drag.offsetWidth - left) + 'px',
                'padding-top': top + 'px',
                'padding-bottom': (maxHeight - drag.offsetHeight - top) + 'px'
            });
        });

        // Create the drops and make them the right size.
        for (var i in this.places) {
            if (!this.places.hasOwnProperty((i))) {
                continue;
            }
            var place = this.places[i],
                label = place.text;
            if (parseInt(place.group) !== group) {
                continue;
            }
            if (label === '') {
                label = M.util.get_string('blank', 'qtype_ddimageortext');
            }
            root.find('.dropzones').append('<div class="dropzone active group' + place.group +
                            ' place' + i + '" tabindex="0">' +
                    '<span class="accesshide">' + label + '</span>&nbsp;</div>');
            root.find('.dropzone.place' + i).width(maxWidth - 2).height(maxHeight - 2);
        }
    };

    /**
     * Invisible 'drag homes' are output by the renderer. These have the same properties
     * as the drag items but are invisible. We clone these invisible elements to make the
     * actual drag items.
     */
    DragDropOntoImageQuestion.prototype.cloneDrags = function() {
        var thisQ = this;
        thisQ.getRoot().find('.draghome').each(function(index, dragHome) {
            var drag = $(dragHome);
            var placeHolder = drag.clone();
            placeHolder.removeClass();
            placeHolder.addClass('draghome choice' +
                thisQ.getChoice(drag) + ' group' +
                thisQ.getGroup(drag) + ' dragplaceholder');
            drag.before(placeHolder);
        });
    };

    /**
     * Clone drag item for one choice.
     *
     * @param {jQuery} dragHome the drag home to clone.
     */
    DragDropOntoImageQuestion.prototype.cloneDragsForOneChoice = function(dragHome) {
        if (dragHome.hasClass('infinite')) {
            var noOfDrags = this.noOfDropsInGroup(this.getGroup(dragHome));
            for (var i = 0; i < noOfDrags; i++) {
                this.cloneDrag(dragHome);
            }
        } else {
            this.cloneDrag(dragHome);
        }
    };

    /**
     * Clone drag item.
     *
     * @param {jQuery} dragHome
     */
    DragDropOntoImageQuestion.prototype.cloneDrag = function(dragHome) {
        var drag = dragHome.clone();
        drag.removeClass('draghome')
            .addClass('drag unplaced moodle-has-zindex')
            .offset(dragHome.offset());
        this.getRoot().find('.dragitems').append(drag);
    };

    /**
     * Update the position of drags.
     */
    DragDropOntoImageQuestion.prototype.positionDragsAndDrops = function() {
        var thisQ = this,
            root = this.getRoot(),
            bgRatio = this.bgRatio();

        // Move the drops into position.
        root.find('.ddarea .dropzone').each(function(i, dropNode) {
            var drop = $(dropNode),
                place = thisQ.places[thisQ.getPlace(drop)];
            // The xy values come from PHP as strings, so we need parseInt to stop JS doing string concatenation.
            drop.css('left', parseInt(place.xy[0]) * bgRatio)
                .css('top', parseInt(place.xy[1]) * bgRatio);
            drop.data('originX', parseInt(place.xy[0]))
                .data('originY', parseInt(place.xy[1]));
            thisQ.handleElementScale(drop, 'left top');
        });

        // First move all items back home.
        root.find('.draghome').not('.dragplaceholder').each(function(i, dragNode) {
            var drag = $(dragNode),
                currentPlace = thisQ.getClassnameNumericSuffix(drag, 'inplace');
            drag.addClass('unplaced')
                .removeClass('placed');
            drag.removeAttr('tabindex');
            if (currentPlace !== null) {
                drag.removeClass('inplace' + currentPlace);
            }
        });

        // Then place the ones that should be placed.
        root.find('input.placeinput').each(function(i, inputNode) {
            var input = $(inputNode),
                choice = input.val();
            if (choice.length === 0 || (choice.length > 0 && choice === '0')) {
                // No item in this place.
                return;
            }

            var place = thisQ.getPlace(input);
            // Get the unplaced drag.
            var unplacedDrag = thisQ.getUnplacedChoice(thisQ.getGroup(input), choice);
            // Get the clone of the drag.
            var hiddenDrag = thisQ.getDragClone(unplacedDrag);
            if (hiddenDrag.length) {
                if (unplacedDrag.hasClass('infinite')) {
                    var noOfDrags = thisQ.noOfDropsInGroup(thisQ.getGroup(unplacedDrag));
                    var cloneDrags = thisQ.getInfiniteDragClones(unplacedDrag, false);
                    if (cloneDrags.length < noOfDrags) {
                        var cloneDrag = unplacedDrag.clone();
                        cloneDrag.removeClass('beingdragged');
                        cloneDrag.removeAttr('tabindex');
                        hiddenDrag.after(cloneDrag);
                    } else {
                        hiddenDrag.addClass('active');
                    }
                } else {
                    hiddenDrag.addClass('active');
                }
            }

            // Send the drag to drop.
            var drop = root.find('.dropzone.place' + place);
            thisQ.sendDragToDrop(unplacedDrag, drop);
        });
    };

    /**
     * Handles the start of dragging an item.
     *
     * @param {Event} e the touch start or mouse down event.
     */
    DragDropOntoImageQuestion.prototype.handleDragStart = function(e) {
        var thisQ = this,
            drag = $(e.target).closest('.draghome'),
            currentIndex = this.calculateZIndex(),
            newIndex = currentIndex + 2;

        var info = dragDrop.prepare(e);
        if (!info.start) {
            return;
        }

        drag.addClass('beingdragged').css('transform', '').css('z-index', newIndex);
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
                    var noOfDrags = this.noOfDropsInGroup(thisQ.getGroup(drag));
                    var cloneDrags = this.getInfiniteDragClones(drag, false);
                    if (cloneDrags.length < noOfDrags) {
                        var cloneDrag = drag.clone();
                        cloneDrag.removeClass('beingdragged');
                        cloneDrag.removeAttr('tabindex');
                        hiddenDrag.after(cloneDrag);
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
    DragDropOntoImageQuestion.prototype.dragMove = function(pageX, pageY, drag) {
        var thisQ = this;
        this.getRoot().find('.dropzone.group' + this.getGroup(drag)).each(function(i, dropNode) {
            var drop = $(dropNode);
            if (thisQ.isPointInDrop(pageX, pageY, drop)) {
                drop.addClass('valid-drag-over-drop');
            } else {
                drop.removeClass('valid-drag-over-drop');
            }
        });
        this.getRoot().find('.draghome.placed.group' + this.getGroup(drag)).not('.beingdragged').each(function(i, dropNode) {
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
    DragDropOntoImageQuestion.prototype.dragEnd = function(pageX, pageY, drag) {
        var thisQ = this,
            root = this.getRoot(),
            placed = false;
        root.find('.dropzone.group' + this.getGroup(drag)).each(function(i, dropNode) {
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

        root.find('.draghome.placed.group' + this.getGroup(drag)).not('.beingdragged').each(function(i, placedNode) {
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
    DragDropOntoImageQuestion.prototype.sendDragToDrop = function(drag, drop) {
        // Is there already a drag in this drop? if so, evict it.
        var oldDrag = this.getCurrentDragInPlace(this.getPlace(drop));
        if (oldDrag.length !== 0) {
            oldDrag.addClass('beingdragged');
            oldDrag.offset(oldDrag.offset());
            var currentPlace = this.getClassnameNumericSuffix(oldDrag, 'inplace');
            var hiddenDrop = this.getDrop(oldDrag, currentPlace);
            hiddenDrop.addClass('active');
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
    DragDropOntoImageQuestion.prototype.sendDragHome = function(drag) {
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
    DragDropOntoImageQuestion.prototype.handleKeyPress = function(e) {
        var drop = $(e.target).closest('.dropzone');
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
                questionManager.isKeyboardNavigation = false;
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
    DragDropOntoImageQuestion.prototype.getNextDrag = function(group, drag) {
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
    DragDropOntoImageQuestion.prototype.getPreviousDrag = function(group, drag) {
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
    DragDropOntoImageQuestion.prototype.animateTo = function(drag, target) {
        var currentPos = drag.offset(),
            targetPos = target.offset(),
            thisQ = this;

        M.util.js_pending('qtype_ddimageortext-animate-' + thisQ.containerId);
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
                    $('body').trigger('dragmoved', [drag, target, thisQ]);
                    M.util.js_complete('qtype_ddimageortext-animate-' + thisQ.containerId);
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
    DragDropOntoImageQuestion.prototype.isPointInDrop = function(pageX, pageY, drop) {
        var position = drop.offset();
        if (drop.hasClass('draghome')) {
            return pageX >= position.left && pageX < position.left + drop.outerWidth()
                && pageY >= position.top && pageY < position.top + drop.outerHeight();
        }
        return pageX >= position.left && pageX < position.left + drop.width()
            && pageY >= position.top && pageY < position.top + drop.height();
    };

    /**
     * Set the value of the hidden input for a place, to record what is currently there.
     *
     * @param {int} place which place to set the input value for.
     * @param {int} choice the value to set.
     */
    DragDropOntoImageQuestion.prototype.setInputValue = function(place, choice) {
        this.getRoot().find('input.placeinput.place' + place).val(choice);
    };

    /**
     * Get the outer div for this question.
     *
     * @returns {jQuery} containing that div.
     */
    DragDropOntoImageQuestion.prototype.getRoot = function() {
        return $(document.getElementById(this.containerId));
    };

    /**
     * Get the img that is the background image.
     * @returns {jQuery} containing that img.
     */
    DragDropOntoImageQuestion.prototype.bgImage = function() {
        return this.getRoot().find('img.dropbackground');
    };

    /**
     * Get drag home for a given choice.
     *
     * @param {int} group the group.
     * @param {int} choice the choice number.
     * @returns {jQuery} containing that div.
     */
    DragDropOntoImageQuestion.prototype.getDragHome = function(group, choice) {
        if (!this.getRoot().find('.draghome.dragplaceholder.group' + group + '.choice' + choice).is(':visible')) {
            return this.getRoot().find('.dragitemgroup' + group +
                ' .draghome.infinite' +
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
    DragDropOntoImageQuestion.prototype.getUnplacedChoice = function(group, choice) {
        return this.getRoot().find('.ddarea .draghome.group' + group + '.choice' + choice + '.unplaced').slice(0, 1);
    };

    /**
     * Get the drag that is currently in a given place.
     *
     * @param {int} place the place number.
     * @return {jQuery} the current drag (or an empty jQuery if none).
     */
    DragDropOntoImageQuestion.prototype.getCurrentDragInPlace = function(place) {
        return this.getRoot().find('.ddarea .draghome.inplace' + place);
    };

    /**
     * Return the number of blanks in a given group.
     *
     * @param {int} group the group number.
     * @returns {int} the number of drops.
     */
    DragDropOntoImageQuestion.prototype.noOfDropsInGroup = function(group) {
        return this.getRoot().find('.dropzone.group' + group).length;
    };

    /**
     * Return the number of choices in a given group.
     *
     * @param {int} group the group number.
     * @returns {int} the number of choices.
     */
    DragDropOntoImageQuestion.prototype.noOfChoicesInGroup = function(group) {
        return this.getRoot().find('.dragitemgroup' + group + ' .draghome').length;
    };

    /**
     * Return the number at the end of the CSS class name with the given prefix.
     *
     * @param {jQuery} node
     * @param {String} prefix name prefix
     * @returns {Number|null} the suffix if found, else null.
     */
    DragDropOntoImageQuestion.prototype.getClassnameNumericSuffix = function(node, prefix) {
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
    DragDropOntoImageQuestion.prototype.getChoice = function(drag) {
        return this.getClassnameNumericSuffix(drag, 'choice');
    };

    /**
     * Given a DOM node that is significant to this question
     * (drag, drop, ...) get the group it belongs to.
     *
     * @param {jQuery} node a DOM node.
     * @returns {Number} the group it belongs to.
     */
    DragDropOntoImageQuestion.prototype.getGroup = function(node) {
        return this.getClassnameNumericSuffix(node, 'group');
    };

    /**
     * Get the place number of a drop, or its corresponding hidden input.
     *
     * @param {jQuery} node the DOM node.
     * @returns {Number} the place number.
     */
    DragDropOntoImageQuestion.prototype.getPlace = function(node) {
        return this.getClassnameNumericSuffix(node, 'place');
    };

    /**
     * Get drag clone for a given drag.
     *
     * @param {jQuery} drag the drag.
     * @returns {jQuery} the drag's clone.
     */
    DragDropOntoImageQuestion.prototype.getDragClone = function(drag) {
        return this.getRoot().find('.dragitemgroup' +
            this.getGroup(drag) +
            ' .draghome' +
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
    DragDropOntoImageQuestion.prototype.getInfiniteDragClones = function(drag, inHome) {
        if (inHome) {
            return this.getRoot().find('.dragitemgroup' +
                this.getGroup(drag) +
                ' .draghome' +
                '.choice' + this.getChoice(drag) +
                '.group' + this.getGroup(drag) +
                '.infinite').not('.dragplaceholder');
        }
        return this.getRoot().find('.draghome' +
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
    DragDropOntoImageQuestion.prototype.getDrop = function(drag, currentPlace) {
        return this.getRoot().find('.dropzone.group' + this.getGroup(drag) + '.place' + currentPlace);
    };

    /**
     * Handle when the window is resized.
     */
    DragDropOntoImageQuestion.prototype.handleResize = function() {
        var thisQ = this,
            bgRatio = this.bgRatio();
        if (this.isPrinting) {
            bgRatio = 1;
        }

        this.getRoot().find('.ddarea .dropzone').each(function(i, dropNode) {
            $(dropNode)
                .css('left', parseInt($(dropNode).data('originX')) * parseFloat(bgRatio))
                .css('top', parseInt($(dropNode).data('originY')) * parseFloat(bgRatio));
            thisQ.handleElementScale(dropNode, 'left top');
        });

        this.getRoot().find('div.droparea .draghome').not('.beingdragged').each(function(key, drag) {
            $(drag)
                .css('left', parseFloat($(drag).data('originX')) * parseFloat(bgRatio))
                .css('top', parseFloat($(drag).data('originY')) * parseFloat(bgRatio));
            thisQ.handleElementScale(drag, 'left top');
        });
    };

    /**
     * Return the background ratio.
     *
     * @returns {number} Background ratio.
     */
    DragDropOntoImageQuestion.prototype.bgRatio = function() {
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
    DragDropOntoImageQuestion.prototype.handleElementScale = function(element, type) {
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
     * Calculate z-index value.
     *
     * @returns {number} z-index value
     */
    DragDropOntoImageQuestion.prototype.calculateZIndex = function() {
        var zIndex = 0;
        this.getRoot().find('.ddarea .dropzone, div.droparea .draghome').each(function(i, dropNode) {
            dropNode = $(dropNode);
            // Note that webkit browsers won't return the z-index value from the CSS stylesheet
            // if the element doesn't have a position specified. Instead it'll return "auto".
            var itemZIndex = dropNode.css('z-index') ? parseInt(dropNode.css('z-index')) : 0;

            if (itemZIndex > zIndex) {
                zIndex = itemZIndex;
            }
        });

        return zIndex;
    };

    /**
     * Singleton object that handles all the DragDropOntoImageQuestions
     * on the page, and deals with event dispatching.
     * @type {Object}
     */
    var questionManager = {

        /**
         * {boolean} ensures that the event handlers are only initialised once per page.
         */
        eventHandlersInitialised: false,

        /**
         * {boolean} is printing or not.
         */
        isPrinting: false,

        /**
         * {boolean} is keyboard navigation or not.
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
         * @param {Array} places data.
         */
        init: function(containerId, readOnly, places) {
            questionManager.questions[containerId] =
                new DragDropOntoImageQuestion(containerId, readOnly, places);
            if (!questionManager.eventHandlersInitialised) {
                questionManager.setupEventHandlers();
                questionManager.eventHandlersInitialised = true;
            }
        },

        /**
         * Set up the event handlers that make this question type work. (Done once per page.)
         */
        setupEventHandlers: function() {
            $('body')
                .on('mousedown touchstart',
                    '.que.ddimageortext:not(.qtype_ddimageortext-readonly) .draghome',
                    questionManager.handleDragStart)
                .on('keydown',
                    '.que.ddimageortext:not(.qtype_ddimageortext-readonly) .dropzones .dropzone',
                    questionManager.handleKeyPress)
                .on('keydown',
                    '.que.ddimageortext:not(.qtype_ddimageortext-readonly) .draghome.placed:not(.beingdragged)',
                    questionManager.handleKeyPress)
                .on('dragmoved', questionManager.handleDragMoved);
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
         * Handle mouse down / touch start events on drags.
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
         * Handle key down / press events on drags.
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
         * Sometimes, despite our best efforts, things change in a way that cannot
         * be specifically caught (e.g. dock expanding or collapsing in Boost).
         * Therefore, we need to periodically check everything is in the right position.
         */
        fixLayoutIfThingsMoved: function() {
            this.handleWindowResize(questionManager.isPrinting);
            // We use setTimeout after finishing work, rather than setInterval,
            // in case positioning things is slow. We want 100 ms gap
            // between executions, not what setInterval does.
            setTimeout(function() {
                questionManager.fixLayoutIfThingsMoved(questionManager.isPrinting);
            }, 100);
        },

        /**
         * Handle when drag moved.
         *
         * @param {Event} e the event.
         * @param {jQuery} drag the drag
         * @param {jQuery} target the target
         * @param {DragDropOntoImageQuestion} thisQ the question.
         */
        handleDragMoved: function(e, drag, target, thisQ) {
            drag.removeClass('beingdragged').css('z-index', '');
            drag.css('top', target.position().top).css('left', target.position().left);
            target.after(drag);
            target.removeClass('active');
            if (typeof drag.data('unplaced') !== 'undefined' && drag.data('unplaced') === true) {
                drag.removeClass('placed').addClass('unplaced');
                drag.removeAttr('tabindex');
                drag.removeData('unplaced');
                drag.css('top', '')
                    .css('left', '')
                    .css('transform', '');
                if (drag.hasClass('infinite') && thisQ.getInfiniteDragClones(drag, true).length > 1) {
                    thisQ.getInfiniteDragClones(drag, true).first().remove();
                }
            } else {
                drag.data('originX', target.data('originX')).data('originY', target.data('originY'));
                thisQ.handleElementScale(drag, 'left top');
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
        },

        /**
         * Given an event, work out which question it effects.
         * @param {Event} e the event.
         * @returns {DragDropOntoImageQuestion|undefined} The question, or undefined.
         */
        getQuestionForEvent: function(e) {
            var containerId = $(e.currentTarget).closest('.que.ddimageortext').attr('id');
            return questionManager.questions[containerId];
        }
    };

    /**
     * @alias module:qtype_ddimageortext/question
     */
    return {
        /**
         * Initialise one drag-drop onto image question.
         *
         * @param {String} containerId id of the outer div for this question.
         * @param {boolean} readOnly whether the question is being displayed read-only.
         * @param {Array} Information about the drop places.
         */
        init: questionManager.init
    };
});
