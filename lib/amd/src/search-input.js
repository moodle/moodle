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
 * Search box.
 *
 * @module     core/search-input
 * @class      search-input
 * @package    core
 * @copyright  2016 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
define(['jquery'], function($) {

    /**
     * This search box div node.
     *
     * @private
     */
    var wrapper = null;

    /**
     * Toggles the form visibility.
     *
     * @param {Event} ev
     * @method toggleForm
     * @private
     */
    var toggleForm = function(ev) {

        if (wrapper.hasClass('expanded')) {
            hideForm();
        } else {
            showForm(ev);
        }
    };

    /**
     * Shows the form or submits it depending on the window size.
     *
     * @param {Event} ev
     * @method showForm
     * @private
     */
    var showForm = function(ev) {

        var windowWidth = $(document).width();

        // We are only interested in enter and space keys (accessibility).
        if (ev.type === 'keydown' && ev.keyCode !== 13 && ev.keyCode !== 32) {
            return;
        }

        if (windowWidth <= 767 && (ev.type === 'click' || ev.type === 'keydown')) {
            // Move to the search page when using small window sizes as the input requires too much space.
            submitForm();
            return;
        } else if (windowWidth <= 767) {
            // Ignore mousedown events in while using small window sizes.
            return;
        }

        if (ev.type === 'keydown') {
            // We don't want to submit the form unless the user hits enter.
            ev.preventDefault();
        }

        wrapper.addClass('expanded');
        wrapper.find('form').addClass('expanded');
        wrapper.find('input').focus();
    };

    /**
     * Hides the form.
     *
     * @method hideForm
     * @private
     */
    var hideForm = function() {
        wrapper.removeClass('expanded');
        wrapper.find('form').removeClass('expanded');
    };

    /**
     * Submits the form.
     *
     * @param {Event} ev
     * @method submitForm
     * @private
     */
    var submitForm = function() {
        wrapper.find('form').submit();
    };

    return /** @alias module:core/search-input */ {
        // Public variables and functions.

        /**
         * Assigns listeners to the requested select box.
         *
         * @method init
         * @param {Number} id The search wrapper div id
         */
        init: function(id) {
            wrapper = $('#' + id);
            wrapper.on('click mouseover keydown', 'div', toggleForm);
        }
    };
});
