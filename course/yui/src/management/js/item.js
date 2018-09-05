/**
 * A managed course.
 *
 * @namespace M.course.management
 * @class Item
 * @constructor
 * @extends Base
 */
function Item() {
    Item.superclass.constructor.apply(this, arguments);
}
Item.NAME = 'moodle-course-management-item';
Item.CSS_PREFIX = 'management-item';
Item.ATTRS = {
    /**
     * The node for this item.
     * @attribute node
     * @type Node
     */
    node: {},

    /**
     * The management console.
     * @attribute console
     * @type Console
     */
    console: {},

    /**
     * Describes the type of this item. Should be set by the extending class.
     * @attribute itemname
     * @type {String}
     * @default item
     */
    itemname: {
        value: 'item'
    }
};
Item.prototype = {
    /**
     * The highlight timeout for this item if there is one.
     * @property highlighttimeout
     * @protected
     * @type Timeout
     * @default null
     */
    highlighttimeout: null,

    /**
     * Checks and parses an AJAX response for an item.
     *
     * @method checkAjaxResponse
     * @protected
     * @param {Number} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @return {Object|Boolean}
     */
    checkAjaxResponse: function(transactionid, response, args) {
        if (response.status !== 200) {
            Y.log('Error: AJAX response resulted in non 200 status.', 'error', 'Item.checkAjaxResponse');
            return false;
        }
        if (transactionid === null || args === null) {
            Y.log('Error: Invalid AJAX response details provided.', 'error', 'Item.checkAjaxResponse');
            return false;
        }
        var outcome = Y.JSON.parse(response.responseText);
        if (outcome.error !== false) {
            new M.core.exception(outcome);
        }
        if (outcome.outcome === false) {
            return false;
        }
        return outcome;
    },

    /**
     * Moves an item up by one.
     *
     * @method moveup
     * @param {Number} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @return {Boolean}
     */
    moveup: function(transactionid, response, args) {
        var node,
            nodeup,
            nodedown,
            previous,
            previousup,
            previousdown,
            tmpnode,
            outcome = this.checkAjaxResponse(transactionid, response, args);
        if (outcome === false) {
            Y.log('AJAX request to move ' + this.get('itemname') + ' up failed by outcome.', 'warn', 'moodle-course-management');
            return false;
        }
        node = this.get('node');
        previous = node.previous('.listitem');
        if (previous) {
            previous.insert(node, 'before');
            previousup = previous.one(' > div a.action-moveup');
            nodedown = node.one(' > div a.action-movedown');
            if (!previousup || !nodedown) {
                // We can have two situations here:
                //   1. previousup is not set and nodedown is not set. This happens when there are only two courses.
                //   2. nodedown is not set. This happens when they are moving the bottom course up.
                // node up and previous down should always be there. They would be required to trigger the action.
                nodeup = node.one(' > div a.action-moveup');
                previousdown = previous.one(' > div a.action-movedown');
                if (!previousup && !nodedown) {
                    // Ok, must be two courses. We need to switch the up and down icons.
                    tmpnode = Y.Node.create('<a style="visibility:hidden;">&nbsp;</a>');
                    previousdown.replace(tmpnode);
                    nodeup.replace(previousdown);
                    tmpnode.replace(nodeup);
                    tmpnode.destroy();
                } else if (!nodedown) {
                    // previous down needs to be given to node.
                    nodeup.insert(previousdown, 'after');
                }
            }
            nodeup = node.one(' > div a.action-moveup');
            if (nodeup) {
                // Try to re-focus on up.
                nodeup.focus();
            } else {
                // If we can't focus up we're at the bottom, try to focus on up.
                nodedown = node.one(' > div a.action-movedown');
                if (nodedown) {
                    nodedown.focus();
                }
            }
            this.updated(true);
            Y.log('Success: ' + this.get('itemname') + ' moved up by AJAX.', 'info', 'moodle-course-management');
        } else {
            // Aha it succeeded but this is the top item in the list. Pagination is in play!
            // Refresh to update the state of things.
            Y.log(this.get('itemname') + ' cannot be moved up as its the top item on this page.',
                    'info', 'moodle-course-management');
            window.location.reload();
        }
    },

    /**
     * Moves an item down by one.
     *
     * @method movedown
     * @param {Number} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @return {Boolean}
     */
    movedown: function(transactionid, response, args) {
        var node,
            next,
            nodeup,
            nodedown,
            nextup,
            nextdown,
            tmpnode,
            outcome = this.checkAjaxResponse(transactionid, response, args);
        if (outcome === false) {
            Y.log('AJAX request to move ' + this.get('itemname') + ' down failed by outcome.', 'warn', 'moodle-course-management');
            return false;
        }
        node = this.get('node');
        next = node.next('.listitem');
        if (next) {
            node.insert(next, 'before');
            nextdown = next.one(' > div a.action-movedown');
            nodeup = node.one(' > div a.action-moveup');
            if (!nextdown || !nodeup) {
                // next up and node down should always be there. They would be required to trigger the action.
                nextup = next.one(' > div a.action-moveup');
                nodedown = node.one(' > div a.action-movedown');
                if (!nextdown && !nodeup) {
                    // We can have two situations here:
                    //   1. nextdown is not set and nodeup is not set. This happens when there are only two courses.
                    //   2. nodeup is not set. This happens when we are moving the first course down.
                    // Ok, must be two courses. We need to switch the up and down icons.
                    tmpnode = Y.Node.create('<a style="visibility:hidden;">&nbsp;</a>');
                    nextup.replace(tmpnode);
                    nodedown.replace(nextup);
                    tmpnode.replace(nodedown);
                    tmpnode.destroy();
                } else if (!nodeup) {
                    // next up needs to be given to node.
                    nodedown.insert(nextup, 'before');
                }
            }
            nodedown = node.one(' > div a.action-movedown');
            if (nodedown) {
                // Try to ensure the up is focused again.
                nodedown.focus();
            } else {
                // If we can't focus up we're at the top, try to focus on down.
                nodeup = node.one(' > div a.action-moveup');
                if (nodeup) {
                    nodeup.focus();
                }
            }
            this.updated(true);
            Y.log('Success: ' + this.get('itemname') + ' moved down by AJAX.', 'info', 'moodle-course-management');
        } else {
            // Aha it succeeded but this is the bottom item in the list. Pagination is in play!
            // Refresh to update the state of things.
            Y.log(this.get('itemname') + ' cannot be moved down as its the top item on this page.',
                    'info', 'moodle-course-management');
            window.location.reload();
        }
    },

    /**
     * Makes an item visible.
     *
     * @method show
     * @param {Number} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @return {Boolean}
     */
    show: function(transactionid, response, args) {
        var outcome = this.checkAjaxResponse(transactionid, response, args),
            hidebtn;
        if (outcome === false) {
            Y.log('AJAX request to show ' + this.get('itemname') + ' by outcome.', 'warn', 'moodle-course-management');
            return false;
        }

        this.markVisible();
        hidebtn = this.get('node').one('a[data-action=hide]');
        if (hidebtn) {
            hidebtn.focus();
        }
        this.updated();
        Y.log('Success: ' + this.get('itemname') + ' made visible by AJAX.', 'info', 'moodle-course-management');
    },

    /**
     * Marks the item as visible
     * @method markVisible
     */
    markVisible: function() {
        this.get('node').setAttribute('data-visible', '1');
        Y.log('Marked ' + this.get('itemname') + ' as visible', 'info', 'moodle-course-management');
        return true;
    },

    /**
     * Hides an item.
     *
     * @method hide
     * @param {Number} transactionid The transaction ID of the AJAX request (unique)
     * @param {Object} response The response from the AJAX request.
     * @param {Object} args The arguments given to the request.
     * @return {Boolean}
     */
    hide: function(transactionid, response, args) {
        var outcome = this.checkAjaxResponse(transactionid, response, args),
            showbtn;
        if (outcome === false) {
            Y.log('AJAX request to hide ' + this.get('itemname') + ' by outcome.', 'warn', 'moodle-course-management');
            return false;
        }
        this.markHidden();
        showbtn = this.get('node').one('a[data-action=show]');
        if (showbtn) {
            showbtn.focus();
        }
        this.updated();
        Y.log('Success: ' + this.get('itemname') + ' made hidden by AJAX.', 'info', 'moodle-course-management');
    },

    /**
     * Marks the item as hidden.
     * @method makeHidden
     */
    markHidden: function() {
        this.get('node').setAttribute('data-visible', '0');
        Y.log('Marked ' + this.get('itemname') + ' as hidden', 'info', 'moodle-course-management');
        return true;
    },

    /**
     * Called when ever a node is updated.
     *
     * @method updated
     * @param {Boolean} moved True if this item was moved.
     */
    updated: function(moved) {
        if (moved) {
            this.highlight();
        }
    },

    /**
     * Highlights this option for a breif time.
     *
     * @method highlight
     */
    highlight: function() {
        var node = this.get('node');
        node.siblings('.highlight').removeClass('highlight');
        node.addClass('highlight');
        if (this.highlighttimeout) {
            window.clearTimeout(this.highlighttimeout);
        }
        this.highlighttimeout = window.setTimeout(function() {
            node.removeClass('highlight');
        }, 2500);
    }
};
Y.extend(Item, Y.Base, Item.prototype);
