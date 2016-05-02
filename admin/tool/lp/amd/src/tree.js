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
 * Implement an accessible aria tree widget, from a nested unordered list.
 * Based on http://oaa-accessibility.org/example/41/
 *
 * To respond to selection changed events - use tree.on("selectionchanged", handler).
 * The handler will receive an array of nodes, which are the list items that are currently
 * selected. (Or a single node if multiselect is disabled).
 *
 * @module     tool_lp/tree
 * @package    core
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/url', 'core/log'], function($, url, log) {
    // Private variables and functions.
    /** @var {String} expandedImage The html for an expanded tree node twistie. */
    var expandedImage = $('<img alt="" src="' + url.imageUrl('t/expanded') + '"/>');
    /** @var {String} collapsedImage The html for a collapsed tree node twistie. */
    var collapsedImage = $('<img alt="" src="' + url.imageUrl('t/collapsed') + '"/>');

    /**
     * Constructor
     *
     * @param {String} selector
     * @param {Boolean} multiSelect
     */
    var Tree = function(selector, multiSelect) {
        this.treeRoot = $(selector);
        this.multiSelect = (typeof multiSelect === 'undefined' || multiSelect === true);

        this.items = this.treeRoot.find('li');
        this.expandAll = this.items.length < 20;
        this.parents = this.treeRoot.find('li:has(ul)');

        if (multiSelect) {
            this.treeRoot.attr('aria-multiselectable', 'true');
        }

        this.items.attr('aria-selected', 'false');

        this.visibleItems = null;
        this.activeItem = null;
        this.lastActiveItem = null;

        this.keys = {
            tab:      9,
            enter:    13,
            space:    32,
            pageup:   33,
            pagedown: 34,
            end:      35,
            home:     36,
            left:     37,
            up:       38,
            right:    39,
            down:     40,
            eight:    56,
            asterisk: 106
        };

        this.init();

        this.bindEventHandlers();
    };
    // Public variables and functions.

    /**
     * Init this tree
     * @method init
     */
    Tree.prototype.init = function() {
        this.parents.attr('aria-expanded', 'true');
        this.parents.prepend(expandedImage.clone());

        this.items.attr('role', 'tree-item');
        this.items.attr('tabindex', '-1');
        this.parents.attr('role', 'group');
        this.treeRoot.attr('role', 'tree');

        this.visibleItems = this.treeRoot.find('li');

        var thisObj = this;
        if (!this.expandAll) {
            this.parents.each(function() {
                thisObj.collapseGroup($(this));
            });
            this.expandGroup(this.parents.first());
        }
    };

    /**
     * Expand a collapsed group.
     *
     * @method expandGroup
     * @param {Object} item is the jquery id of the parent item of the group
     */
    Tree.prototype.expandGroup = function(item) {
        // Find the first child ul node.
        var group = item.children('ul');

        // Expand the group.
        group.show().attr('aria-hidden', 'false');

        item.attr('aria-expanded', 'true');

        item.children('img').attr('src', expandedImage.attr('src'));

        // Update the list of visible items.
        this.visibleItems = this.treeRoot.find('li:visible');
    };

    /**
     * Collapse an expanded group.
     *
     * @method collapseGroup
     * @param {Object} item is the jquery id of the parent item of the group
     */
    Tree.prototype.collapseGroup = function(item) {
        var group = item.children('ul');

        // Collapse the group.
        group.hide().attr('aria-hidden', 'true');

        item.attr('aria-expanded', 'false');

        item.children('img').attr('src', collapsedImage.attr('src'));

        // Update the list of visible items.
        this.visibleItems = this.treeRoot.find('li:visible');
    };

    /**
     * Expand or collapse a group.
     *
     * @method toggleGroup
     * @param {Object} item is the jquery id of the parent item of the group
     */
    Tree.prototype.toggleGroup = function(item) {
        if (item.attr('aria-expanded') == 'true') {
            this.collapseGroup(item);
        } else {
            this.expandGroup(item);
        }
    };

    /**
     * Whenever the currently selected node has changed, trigger an event using this function.
     *
     * @method triggerChange
     */
    Tree.prototype.triggerChange = function() {
        var allSelected = this.items.filter('[aria-selected=true]');
        if (!this.multiSelect) {
            allSelected = allSelected.first();
        }
        this.treeRoot.trigger('selectionchanged', { selected: allSelected });
    };

    /**
     * Select all the items between the last focused item and this currently focused item.
     *
     * @method multiSelectItem
     * @param {Object} item is the jquery id of the newly selected item.
     */
    Tree.prototype.multiSelectItem = function(item) {
        if (!this.multiSelect) {
            this.items.attr('aria-selected', 'false');
        } else if (this.lastActiveItem !== null) {
            var lastIndex = this.visibleItems.index(this.lastActiveItem);
            var currentIndex = this.visibleItems.index(this.activeItem);
            var oneItem = null;

            while (lastIndex < currentIndex) {
                oneItem  = $(this.visibleItems.get(lastIndex));
                oneItem.attr('aria-selected', 'true');
                lastIndex++;
            }
            while (lastIndex > currentIndex) {
                oneItem  = $(this.visibleItems.get(lastIndex));
                oneItem.attr('aria-selected', 'true');
                lastIndex--;
            }
        }

        item.attr('aria-selected', 'true');
        this.triggerChange();
    };

    /**
     * Select a single item. Make sure all the parents are expanded. De-select all other items.
     *
     * @method selectItem
     * @param {Object} item is the jquery id of the newly selected item.
     */
    Tree.prototype.selectItem = function(item) {
        // Expand all nodes up the tree.
        var walk = item.parent();
        while (walk.attr('role') != 'tree') {
            walk = walk.parent();
            if (walk.attr('aria-expanded') == 'false') {
                this.expandGroup(walk);
            }
            walk = walk.parent();
        }
        this.items.attr('aria-selected', 'false');
        item.attr('aria-selected', 'true');
        this.triggerChange();
    };

    /**
     * Toggle the selected state for an item back and forth.
     *
     * @method toggleItem
     * @param {Object} item is the jquery id of the item to toggle.
     */
    Tree.prototype.toggleItem = function(item) {
        if (!this.multiSelect) {
            return this.selectItem(item);
        }

        var current = item.attr('aria-selected');
        if (current === 'true') {
            current = 'false';
        } else {
            current = 'true';
        }
        item.attr('aria-selected', current);
        this.triggerChange();
    };

    /**
     * Set the focus to this item.
     *
     * @method updateFocus
     * @param {Object} item is the jquery id of the parent item of the group
     */
    Tree.prototype.updateFocus = function(item) {
        this.lastActiveItem = this.activeItem;
        this.activeItem = item;
        // Expand all nodes up the tree.
        var walk = item.parent();
        while (walk.attr('role') != 'tree') {
            walk = walk.parent();
            if (walk.attr('aria-expanded') == 'false') {
                this.expandGroup(walk);
            }
            walk = walk.parent();
        }
        this.items.attr('tabindex', '-1');
        item.attr('tabindex', 0);
    };

    /**
     * Handle a key down event - ie navigate the tree.
     *
     * @method handleKeyDown
     * @param {Object} item is the jquery id of the parent item of the group
     * @param {Event} e The event.
     */
    Tree.prototype.handleKeyDown = function(item, e) {
        var currentIndex = this.visibleItems.index(item);
        var newItem = null;
        var hasKeyModifier = e.shiftKey || e.ctrlKey || e.metaKey || e.altKey;
        var thisObj = this;

        switch (e.keyCode) {
            case this.keys.home: {
                 // Jump to first item in tree.
                newItem = this.parents.first();
                newItem.focus();
                if (e.shiftKey) {
                    this.multiSelectItem(newItem);
                } else if (!hasKeyModifier) {
                    this.selectItem(newItem);
                }

                e.stopPropagation();
                return false;
            }
            case this.keys.end: {
                 // Jump to last visible item.
                newItem = this.visibleItems.last();
                newItem.focus();
                if (e.shiftKey) {
                    this.multiSelectItem(newItem);
                } else if (!hasKeyModifier) {
                    this.selectItem(newItem);
                }

                e.stopPropagation();
                return false;
            }
            case this.keys.enter:
            case this.keys.space: {

                if (e.shiftKey) {
                    this.multiSelectItem(item);
                } else if (e.metaKey || e.ctrlKey) {
                    this.toggleItem(item);
                } else {
                    this.selectItem(item);
                }

                e.stopPropagation();
                return false;
            }
            case this.keys.left: {
                if (item.has('ul') && item.attr('aria-expanded') == 'true') {
                    this.collapseGroup(item);
                } else {
                    // Move up to the parent.
                    var itemUL = item.parent();
                    var itemParent = itemUL.parent();
                    if (itemParent.is('li')) {
                        itemParent.focus();
                        if (e.shiftKey) {
                            this.multiSelectItem(itemParent);
                        } else if (!hasKeyModifier) {
                            this.selectItem(itemParent);
                        }
                    }
                }

                e.stopPropagation();
                return false;
            }
            case this.keys.right: {
                if (item.has('ul') && item.attr('aria-expanded') == 'false') {
                    this.expandGroup(item);
                } else {
                    // Move to the first item in the child group.
                    newItem = item.children('ul').children('li').first();
                    if (newItem.length > 0) {
                        newItem.focus();
                        if (e.shiftKey) {
                            this.multiSelectItem(newItem);
                        } else if (!hasKeyModifier) {
                            this.selectItem(newItem);
                        }
                    }
                }

                e.stopPropagation();
                return false;
            }
            case this.keys.up: {

                if (currentIndex > 0) {
                    var prev = this.visibleItems.eq(currentIndex - 1);
                    prev.focus();
                    if (e.shiftKey) {
                        this.multiSelectItem(prev);
                    } else if (!hasKeyModifier) {
                        this.selectItem(prev);
                    }
                }

                e.stopPropagation();
                return false;
            }
            case this.keys.down: {

                if (currentIndex < this.visibleItems.length - 1) {
                    var next = this.visibleItems.eq(currentIndex + 1);
                    next.focus();
                    if (e.shiftKey) {
                        this.multiSelectItem(next);
                    } else if (!hasKeyModifier) {
                        this.selectItem(next);
                    }
                }
                e.stopPropagation();
                return false;
            }
            case this.keys.asterisk: {
                // Expand all groups.
                this.parents.each(function() {
                    thisObj.expandGroup($(this));
                });

                e.stopPropagation();
                return false;
            }
            case this.keys.eight: {
                if (e.shiftKey) {
                    // Expand all groups.
                    this.parents.each(function() {
                        thisObj.expandGroup($(this));
                    });

                    e.stopPropagation();
                }

                return false;
            }
        }

        return true;
    };

    /**
     * Handle a key press event - ie navigate the tree.
     *
     * @method handleKeyPress
     * @param {Object} item is the jquery id of the parent item of the group
     * @param {Event} e The event.
     */
    Tree.prototype.handleKeyPress = function(item, e) {
        if (e.altKey || e.ctrlKey || e.shiftKey || e.metaKey) {
            // Do nothing.
            return true;
        }

        switch (e.keyCode) {
            case this.keys.tab: {
                return true;
            }
            case this.keys.enter:
            case this.keys.home:
            case this.keys.end:
            case this.keys.left:
            case this.keys.right:
            case this.keys.up:
            case this.keys.down: {
                e.stopPropagation();
                return false;
            }
            default : {
                var chr = String.fromCharCode(e.which);
                var match = false;
                var itemIndex = this.visibleItems.index(item);
                var itemCount = this.visibleItems.length;
                var currentIndex = itemIndex + 1;

                // Check if the active item was the last one on the list.
                if (currentIndex == itemCount) {
                    currentIndex = 0;
                }

                // Iterate through the menu items (starting from the current item and wrapping) until a match is found
                // or the loop returns to the current menu item.
                while (currentIndex != itemIndex)  {

                    var currentItem = this.visibleItems.eq(currentIndex);
                    var titleChr = currentItem.text().charAt(0);

                    if (currentItem.has('ul')) {
                        titleChr = currentItem.find('span').text().charAt(0);
                    }

                    if (titleChr.toLowerCase() == chr) {
                        match = true;
                        break;
                    }

                    currentIndex = currentIndex+1;
                    if (currentIndex == itemCount) {
                        // Reached the end of the list, start again at the beginning.
                        currentIndex = 0;
                    }
                }

                if (match === true) {
                    this.updateFocus(this.visibleItems.eq(currentIndex));
                }
                e.stopPropagation();
                return false;
            }
        }

        return true;
    };

    /**
     * Attach an event listener to the tree.
     *
     * @method on
     * @param {String} eventname This is the name of the event to listen for. Only 'selectionchanged' is supported for now.
     * @param {Function} handler The function to call when the event is triggered.
     */
    Tree.prototype.on = function(eventname, handler) {
        if (eventname !== 'selectionchanged') {
            log.warning('Invalid custom event name for tree. Only "selectionchanged" is supported.');
        } else {
            this.treeRoot.on(eventname, handler);
        }
    };

    /**
     * Handle a double click (expand/collapse).
     *
     * @method handleDblClick
     * @param {Object} item is the jquery id of the parent item of the group
     * @param {Event} e The event.
     */
    Tree.prototype.handleDblClick = function(item, e) {

        if (e.altKey || e.ctrlKey || e.shiftKey || e.metaKey) {
            // Do nothing.
            return true;
        }

        // Apply the focus markup.
        this.updateFocus(item);

        // Expand or collapse the group.
        this.toggleGroup(item);

        e.stopPropagation();
        return false;
    };

    /**
     * Handle a click (select).
     *
     * @method handleExpandCollapseClick
     * @param {Object} item is the jquery id of the parent item of the group
     * @param {Event} e The event.
     */
    Tree.prototype.handleExpandCollapseClick = function(item, e) {

        // Do not shift the focus.
        this.toggleGroup(item);
        e.stopPropagation();
        return false;
    };


    /**
     * Handle a click (select).
     *
     * @method handleClick
     * @param {Object} item is the jquery id of the parent item of the group
     * @param {Event} e The event.
     */
    Tree.prototype.handleClick = function(item, e) {

        if (e.shiftKey) {
            this.multiSelectItem(item);
        } else if (e.metaKey || e.ctrlKey) {
            this.toggleItem(item);
        } else {
            this.selectItem(item);
        }
        this.updateFocus(item);
        e.stopPropagation();
        return false;
    };

    /**
     * Handle a blur event
     *
     * @method handleBlur
     * @param {Object} item item is the jquery id of the parent item of the group
     * @param {Event} e The event.
     */
    Tree.prototype.handleBlur = function() {
        return true;
    };

    /**
     * Handle a focus event
     *
     * @method handleFocus
     * @param {Object} item item is the jquery id of the parent item of the group
     * @param {Event} e The event.
     */
    Tree.prototype.handleFocus = function(item) {

        this.updateFocus(item);

        return true;
    };

    /**
     * Bind the event listeners we require.
     *
     * @method bindEventHandlers
     */
    Tree.prototype.bindEventHandlers = function() {
        var thisObj = this;

        // Bind a dblclick handler to the parent items.
        this.parents.dblclick(function(e) {
            return thisObj.handleDblClick($(this), e);
        });

        // Bind a click handler.
        this.items.click(function(e) {
            return thisObj.handleClick($(this), e);
        });

        // Bind a toggle handler to the expand/collapse icons.
        this.items.children('img').click(function(e) {
            return thisObj.handleExpandCollapseClick($(this).parent(), e);
        });

        // Bind a keydown handler.
        this.items.keydown(function(e) {
            return thisObj.handleKeyDown($(this), e);
        });

        // Bind a keypress handler.
        this.items.keypress(function(e) {
            return thisObj.handleKeyPress($(this), e);
        });

        // Bind a focus handler.
        this.items.focus(function(e) {
            return thisObj.handleFocus($(this), e);
        });

        // Bind a blur handler.
        this.items.blur(function(e) {
            return thisObj.handleBlur($(this), e);
        });

    };

    return /** @alias module:tool_lp/tree */ Tree;
});
