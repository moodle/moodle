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
 * A way to call HTML fragments to be inserted as required via JavaScript.
 *
 * @module     core/fragment
 * @class      fragment
 * @package    core
 * @copyright  2016 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {

    /**
     * Loads an HTML fragment through a callback.
     *
     * @method loadFragment
     * @param {string} component Component where callback is located.
     * @param {string} callback Callback function name.
     * @param {integer} contextid Context ID of the fragment.
     * @param {object} params Parameters for the callback.
     * @return {Promise} JQuery promise object resolved when the fragment has been loaded.
     */
    var loadFragment = function(component, callback, contextid, params) {
        // Change params into required webservice format.
        var formattedparams = [];
        for (var index in params) {
            formattedparams.push({name: index, value: params[index]});
        }

        // Ajax stuff.
        var deferred = $.Deferred();

        var promises = ajax.call([{
            methodname: 'core_get_fragment',
            args:{
                component: component,
                callback: callback,
                contextid: contextid,
                args: formattedparams
            }
        }], false);

        promises[0].done(function(data) {
            deferred.resolve(data);
        }).fail(function(ex) {
            deferred.reject(ex);
        });
        return deferred.promise();
    };

    /**
     * Removes and cleans children of a node. This includes event handlers and listeners that may be
     * attached to the nodes for both jquery and yui.
     *
     * @method recursiveCleanup
     * @param {object} DOM node to be cleaned.
     * @return {void}
     */
    var recursiveCleanup = function(node) {
        node.children().each(function(index, el) {
            var child = $(el);
            recursiveCleanup(child);
        });
        var yuinode = new Y.Node(node);
        node.empty();
        node.remove();
        yuinode.detachAll();
        if (yuinode.get('childNodes')) {
            yuinode.empty();
        }
        yuinode.remove(true);
    };

    return /** @alias module:core/fragment */{
        /**
         * Appends HTML and JavaScript fragments to specified nodes.
         * Callbacks called by this AMD module are responsible for doing the appropriate security checks
         * to access the information that is returned. This only does minimal validation on the context.
         *
         * @method fragmentAppend
         * @param {string} component Component where callback is located.
         * @param {string} callback Callback function name.
         * @param {integer} contextid Context ID of the fragment.
         * @param {object} params Parameters for the callback.
         * @param {string} htmlnodeidentifier The 'class' or 'id' to attach the HTML.
         * @param {string} javascriptnodeidentifier The 'class' or 'id' to attach the JavaScript.
         * @return {void}
         */
        fragmentAppend: function(component, callback, contextid, params, htmlnodeidentifier, javascriptnodeidentifier) {
            $.when(loadFragment(component, callback, contextid, params)).then(function(data) {
                // Clean up previous code if found first.
                recursiveCleanup($('#fragment-html'));
                recursiveCleanup($('#fragment-scripts'));
                // Attach new HTML and JavaScript.
                $(htmlnodeidentifier).append('<div id="fragment-html">' + data.html + '</div>');
                $(javascriptnodeidentifier).append('<div id="fragment-scripts">' + data.javascript + '</div>');

            }).fail(function(ex) {
                notification.exception(ex);
            });
        }
    };
});
