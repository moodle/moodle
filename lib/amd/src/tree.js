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
 * Based on http://oaa-accessibility.org/example/41/.
 *
 * @module     tool_lp/tree
 * @package    core
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    // Private variables and functions.
    var SELECTORS = {
        ITEM: '[role=treeitem]',
        GROUP: '[role=treeitem]:has([role=group]), [role=treeitem][aria-owns], [role=treeitem][data-requires-ajax=true]',
        CLOSED_GROUP: '[role=treeitem]:has([role=group])[aria-expanded=false], [role=treeitem][aria-owns][aria-expanded=false], ' +
                 '[role=treeitem][data-requires-ajax=true][aria-expanded=false]',
        FIRST_ITEM: '[role=treeitem]:first',
        VISIBLE_ITEM: '[role=treeitem]:visible',
        UNLOADED_AJAX_ITEM: '[role=treeitem][data-requires-ajax=true][data-loaded=false][aria-expanded=true]'
    };

    /**
     * Constructor.
     *
     * @param {String} selector
     * @param {function} selectCallback Called when the active node is changed.
     */
    var Tree = function(selector, selectCallback) {
        this.treeRoot = $(selector);

        this.treeRoot.data('activeItem', null);
        this.selectCallback = selectCallback;
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
            asterisk: 106
        };

        // Apply the standard default initialisation for all nodes, starting with the tree root.
        this.initialiseNodes(this.treeRoot);
        // Make the first item the active item for the tree so that it is added to the tab order.
        this.setActiveItem(this.treeRoot.find(SELECTORS.FIRST_ITEM));
        // Create the cache of the visible items.
        this.refreshVisibleItemsCache();
        // Create the event handlers for the tree.
        this.bindEventHandlers();
    };

    /**
     * Find all visible tree items and save a cache of them on the tree object.
     *
     * @method refreshVisibleItemsCache
     */
    Tree.prototype.refreshVisibleItemsCache = function() {
        this.treeRoot.data('visibleItems', this.treeRoot.find(SELECTORS.VISIBLE_ITEM));
    };

    /**
     * Get all visible tree items.
     *
     * @method getVisibleItems
     */
    Tree.prototype.getVisibleItems = function() {
        return this.treeRoot.data('visibleItems');
    };

    /**
     * Mark the given item as active within the tree and fire the callback for when the active item is set.
     *
     * @method setActiveItem
     * @param {object} item jquery object representing an item on the tree.
     */
    Tree.prototype.setActiveItem = function(item) {
        var currentActive = this.treeRoot.data('activeItem');
        if (item === currentActive) {
            return;
        }

        // Remove previous active from tab order.
        if (currentActive) {
            currentActive.attr('tabindex', '-1');
            currentActive.attr('aria-selected', 'false');
        }
        item.attr('tabindex', '0');
        item.attr('aria-selected', 'true');

        // Set the new active item.
        this.treeRoot.data('activeItem', item);

        if (typeof this.selectCallback === 'function') {
            this.selectCallback(item);
        }
    };

    /**
     * Determines if the given item is a group item (contains child tree items) in the tree.
     *
     * @method isGroupItem
     * @param {object} item jquery object representing an item on the tree.
     * @returns {bool}
     */
    Tree.prototype.isGroupItem = function(item) {
        return item.is(SELECTORS.GROUP);
    };

    /**
     * Determines if the given item is a group item (contains child tree items) in the tree.
     *
     * @method isGroupItem
     * @param {object} item jquery object representing an item on the tree.
     * @returns {bool}
     */
    Tree.prototype.getGroupFromItem = function(item) {
        return this.treeRoot.find('#' + item.attr('aria-owns')) || item.children('[role=group]');
    };

    /**
     * Determines if the given group item (contains child tree items) is collapsed.
     *
     * @method isGroupCollapsed
     * @param {object} item jquery object representing a group item on the tree.
     * @returns {bool}
     */
    Tree.prototype.isGroupCollapsed = function(item) {
        return item.attr('aria-expanded') === 'false';
    };

    /**
     * Determines if the given group item (contains child tree items) can be collapsed.
     *
     * @method isGroupCollapsible
     * @param {object} item jquery object representing a group item on the tree.
     * @returns {bool}
     */
    Tree.prototype.isGroupCollapsible = function(item) {
        return item.attr('data-collapsible') !== 'false';
    };

    /**
     * Performs the tree initialisation for all child items from the given node,
     * such as removing everything from the tab order and setting aria selected
     * on items.
     *
     * @method initialiseNodes
     * @param {object} node jquery object representing a node.
     */
    Tree.prototype.initialiseNodes = function(node) {
        this.removeAllFromTabOrder(node);
        this.setAriaSelectedFalseOnItems(node);

        // Get all ajax nodes that have been rendered as expanded but haven't loaded the child items yet.
        var thisTree = this;
        node.find(SELECTORS.UNLOADED_AJAX_ITEM).each(function() {
            var unloadedNode = $(this);
            // Collapse and then expand to trigger the ajax loading.
            thisTree.collapseGroup(unloadedNode);
            thisTree.expandGroup(unloadedNode);
        });
    };

    /**
     * Removes all child DOM elements of the given node from the tab order.
     *
     * @method removeAllFromTabOrder
     * @param {object} node jquery object representing a node.
     */
    Tree.prototype.removeAllFromTabOrder = function(node) {
        node.find('*').attr('tabindex', '-1');
        this.getGroupFromItem($(node)).find('*').attr('tabindex', '-1');
    };

    /**
     * Find all child tree items from the given node and set the aria selected attribute to false.
     *
     * @method setAriaSelectedFalseOnItems
     * @param {object} node jquery object representing a node.
     */
    Tree.prototype.setAriaSelectedFalseOnItems = function(node) {
        node.find(SELECTORS.ITEM).attr('aria-selected', 'false');
    };

    /**
     * Expand all group nodes within the tree.
     *
     * @method expandAllGroups
     */
    Tree.prototype.expandAllGroups = function() {
        var thisTree = this;

        this.treeRoot.find(SELECTORS.CLOSED_GROUP).each(function() {
            var groupNode = $(this);

            thisTree.expandGroup($(this)).done(function() {
                thisTree.expandAllChildGroups(groupNode);
            });
        });
    };

    /**
     * Find all child group nodes from the given node and expand them.
     *
     * @method expandAllChildGroups
     * @param {Object} item is the jquery id of the group.
     */
    Tree.prototype.expandAllChildGroups = function(item) {
        var thisTree = this;

        this.getGroupFromItem(item).find(SELECTORS.CLOSED_GROUP).each(function() {
            var groupNode = $(this);

            thisTree.expandGroup($(this)).done(function() {
                thisTree.expandAllChildGroups(groupNode);
            });
        });
    };

    /**
     * Expand a collapsed group.
     *
     * Handles expanding nodes that are ajax loaded (marked with a data-requires-ajax attribute).
     *
     * @method expandGroup
     * @param {Object} item is the jquery id of the parent item of the group.
     * @return {Object} a promise that is resolved when the group has been expanded.
     */
    Tree.prototype.expandGroup = function(item) {
        var promise = $.Deferred();
        // Ignore nodes that are explicitly maked as not expandable or are already expanded.
        if (item.attr('data-expandable') !== 'false' && this.isGroupCollapsed(item)) {
            // If this node requires ajax load and we haven't already loaded it.
            if (item.attr('data-requires-ajax') === 'true' && item.attr('data-loaded') !== 'true') {
                item.attr('data-loaded', false);
                // Get the closes ajax loading module specificed in the tree.
                var moduleName = item.closest('[data-ajax-loader]').attr('data-ajax-loader');
                var thisTree = this;
                // Flag this node as loading.
                item.addClass('loading');
                // Require the ajax module (must be AMD) and try to load the items.
                require([moduleName], function(loader) {
                    // All ajax module must implement a "load" method.
                    loader.load(item).done(function() {
                        item.attr('data-loaded', true);

                        // Set defaults on the newly constructed part of the tree.
                        thisTree.initialiseNodes(item);
                        thisTree.finishExpandingGroup(item);
                        // Make sure no child elements of the item we just loaded are tabbable.
                        item.removeClass('loading');
                        promise.resolve();
                    });
                });
            } else {
                this.finishExpandingGroup(item);
                promise.resolve();
            }
        } else {
            promise.resolve();
        }
        return promise;
    };

    /**
     * Perform the necessary DOM changes to display a group item.
     *
     * @method finishExpandingGroup
     * @param {Object} item is the jquery id of the parent item of the group.
     */
    Tree.prototype.finishExpandingGroup = function(item) {
        // Expand the group.
        var group = this.getGroupFromItem(item);
        group.attr('aria-hidden', 'false');
        item.attr('aria-expanded', 'true');

        // Update the list of visible items.
        this.refreshVisibleItemsCache();
    };

    /**
     * Collapse an expanded group.
     *
     * @method collapseGroup
     * @param {Object} item is the jquery id of the parent item of the group.
     */
    Tree.prototype.collapseGroup = function(item) {
        // If the item is not collapsible or already collapsed then do nothing.
        if (!this.isGroupCollapsible(item) || this.isGroupCollapsed(item)) {
            return;
        }

        // Collapse the group.
        var group = this.getGroupFromItem(item);
        group.attr('aria-hidden', 'true');
        item.attr('aria-expanded', 'false');

        // Update the list of visible items.
        this.refreshVisibleItemsCache();
    };

    /**
     * Expand or collapse a group.
     *
     * @method toggleGroup
     * @param {Object} item is the jquery id of the parent item of the group.
     */
    Tree.prototype.toggleGroup = function(item) {
        if (item.attr('aria-expanded') === 'true') {
            this.collapseGroup(item);
        } else {
            this.expandGroup(item);
        }
    };

    /**
     * Handle a key down event - ie navigate the tree.
     *
     * @method handleKeyDown
     * @param {Object} item is the jquery id of the parent item of the group.
     * @param {Event} e The event.
     */
    Tree.prototype.handleKeyDown = function(item, e) {
        var currentIndex = this.getVisibleItems().index(item);

        if ((e.altKey || e.ctrlKey || e.metaKey) || (e.shiftKey && e.keyCode != this.keys.tab)) {
            // Do nothing.
            return true;
        }

        switch (e.keyCode) {
            case this.keys.home: {
                // Jump to first item in tree.
                this.getVisibleItems().first().focus();

                e.stopPropagation();
                return false;
            }
            case this.keys.end: {
                // Jump to last visible item.
                this.getVisibleItems().last().focus();

                e.stopPropagation();
                return false;
            }
            case this.keys.enter: {
                var links = item.children('a').length ? item.children('a') : item.children().not(SELECTORS.GROUP).find('a');
                if (links.length) {
                    window.location.href = links.first().attr('href');
                } else if (this.isGroupItem(item)) {
                    this.toggleGroup(item, true);
                }

                e.stopPropagation();
                return false;
            }
            case this.keys.space: {
                if (this.isGroupItem(item)) {
                    this.toggleGroup(item, true);
                }

                e.stopPropagation();
                return false;
            }
            case this.keys.left: {
                var focusParent = function(tree) {
                    // Get the immediate visible parent group item that contains this element.
                    tree.getVisibleItems().filter(function() {
                        return tree.getGroupFromItem($(this)).has(item).length;
                    }).focus();
                };

                // If this is a goup item then collapse it and focus the parent group
                // in accordance with the aria spec.
                if (this.isGroupItem(item)) {
                    if (this.isGroupCollapsed(item)) {
                        focusParent(this);
                    } else {
                        this.collapseGroup(item);
                    }
                } else {
                    focusParent(this);
                }

                e.stopPropagation();
                return false;
            }
            case this.keys.right: {
                // If this is a group item then expand it and focus the first child item
                // in accordance with the aria spec.
                if (this.isGroupItem(item)) {
                    if (this.isGroupCollapsed(item)) {
                        this.expandGroup(item);
                    } else {
                        // Move to the first item in the child group.
                        this.getGroupFromItem(item).find(SELECTORS.ITEM).first().focus();
                    }
                }

                e.stopPropagation();
                return false;
            }
            case this.keys.up: {

                if (currentIndex > 0) {
                    var prev = this.getVisibleItems().eq(currentIndex - 1);

                    prev.focus();
                }

                e.stopPropagation();
                return false;
            }
            case this.keys.down: {

                if (currentIndex < this.getVisibleItems().length - 1) {
                    var next = this.getVisibleItems().eq(currentIndex + 1);

                    next.focus();
                }

                e.stopPropagation();
                return false;
            }
            case this.keys.asterisk: {
                // Expand all groups.
                this.expandAllGroups();
                e.stopPropagation();
                return false;
            }
        }
        return true;
    };

    /**
     * Handle a click (select).
     *
     * @method handleClick
     * @param {Object} item The jquery id of the parent item of the group.
     * @param {Event} e The event.
     */
    Tree.prototype.handleClick = function(item, e) {

        if (e.altKey || e.ctrlKey || e.shiftKey || e.metaKey) {
            // Do nothing.
            return true;
        }

        // Update the active item.
        item.focus();

        // If the item is a group node.
        if (this.isGroupItem(item)) {
            this.toggleGroup(item);
        }

        e.stopPropagation();
        return true;
    };

    /**
     * Handle a focus event.
     *
     * @method handleFocus
     * @param {Object} item The jquery id of the parent item of the group.
     * @param {Event} e The event.
     */
    Tree.prototype.handleFocus = function(item, e) {

        this.setActiveItem(item);

        e.stopPropagation();
        return true;
    };

    /**
     * Bind the event listeners we require.
     *
     * @method bindEventHandlers
     */
    Tree.prototype.bindEventHandlers = function() {
        var thisObj = this;

        // Bind event handlers to the tree items. Use event delegates to allow
        // for dynamically loaded parts of the tree.
        this.treeRoot.on({
            click: function(e) { return thisObj.handleClick($(this), e); },
            keydown: function(e) { return thisObj.handleKeyDown($(this), e); },
            focus: function(e) { return thisObj.handleFocus($(this), e); },
        }, SELECTORS.ITEM);
    };

    return /** @alias module:tool_lp/tree */ Tree;
});
