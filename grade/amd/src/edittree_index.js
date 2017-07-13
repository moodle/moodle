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
 * Enhance the gradebook tree setup with various facilities.
 *
 * @module     core_grades/edittree_index
 * @package    core_grades
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
], function($) {
    /**
     * Enhance the edittree functionality.
     *
     * @method edittree
     */
    var edittree = function() {
        // Watch items and toggle the move menu accordingly.
        $('body').on('change', '.itemselect.ignoredirty', edittree.checkMoveMenuState);

        // Watch for the 'All' and 'None' links.
        $('body').on('click', '[data-action="grade_edittree-index-bulkselect"]', edittree.toggleAllSelectItems);

        // Watch for the weight override checkboxes.
        $('body').on('change', '.weightoverride', edittree.toggleWeightInput);

        // Watch changes to the bulk move menu and submit.
        $('#menumoveafter').on('change', function() {
            var form = $(this).closest('form'),
                bulkmove = form.find('#bulkmoveinput');

            bulkmove.val(1);
            form.submit();
        });

        // CHeck the initial state of the move menu.
        edittree.checkMoveMenuState();
    };

    /**
     * Toggle the weight input field based on its checkbox.
     *
     * @method toggleWeightInput
     * @param {EventFacade} e
     * @private
     */
    edittree.toggleWeightInput = function(e) {
        e.preventDefault();
        var node = $(this),
            row = node.closest('tr');

        $('input[name="weight_' + row.data('itemid') + '"]').prop('disabled', !node.prop('checked'));
    };

    /**
     * Toggle all select boxes on or off.
     *
     * @method toggleAllSelectItems
     * @param {EventFacade} e
     * @private
     */
    edittree.toggleAllSelectItems = function(e) {
        e.preventDefault();

        var node = $(this),
            row = node.closest('tr');
        $('.' + row.data('category') + ' .itemselect').prop('checked', node.data('checked'));

        edittree.checkMoveMenuState();
    };

    /**
     * Get the move menu.
     *
     * @method getMoveMenu
     * @private
     * @return {jQuery}
     */
    edittree.getMoveMenu = function() {
        return $('#menumoveafter');
    };

    /**
     * Check whether any checkboxes are ticked.
     *
     * @method checkMoveMenuState
     * @private
     * @return {Boolean}
     */
    edittree.checkMoveMenuState = function() {
        var menu = edittree.getMoveMenu();
        if (!menu.length) {
            return false;
        }

        var selected;
        $('.itemselect').each(function() {
            selected = $(this).prop('checked');

            // Return early if any are checked.
            return !selected;
        });

        menu.prop('disabled', !selected);

        return selected;
    };

    return /** @alias module:core_grades/edittree_index */ {
        enhance: edittree
    };
});
