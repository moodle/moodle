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
 * Javascript Module to handle in course actions like delete / hide activity
 *
 * @module edit_actions
 * @package course/format
 * @subpackage tiles
 * @copyright 2019 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.3
 */

/* eslint space-before-function-paren: 0 */

define(["jquery", "core/str", 'core/notification'], function ($, str, Notification) {
    "use strict";
    // Much of this is copied from core_course/actions because we cannot access the originals from "Tiles".
    // We could reinitialise *all* sections using core, but that would create multiple listeners for each item.
    // We want to do it instead by *section*, so we have to do it ourselves.

    var Selector = {
        ACTIONAREA: '.actions'
    };

    var CSS = {
        EDITINPROGRESS: 'editinprogress'
    };

    /**
     * Wrapper for Y.Moodle.core_course.util.cm.getName
     *
     * @param {object} element
     * @returns {String}
     */
    var getModuleName = function(element) {
        var name = '';
        Y.use('moodle-course-util', function(Y) {
            name = Y.Moodle.core_course.util.cm.getName(Y.Node(element.get(0)));
        });
        return name;
    };

    /**
     * Displays the delete confirmation to delete a module
     *
     * @param {object} mainelement activity element we perform action on
     * @param {function} onconfirm function to execute on confirm
     */
    var confirmDeleteModule = function(mainelement, onconfirm) {
        var modtypename = mainelement.attr('class').match(/modtype_([^\s]*)/)[1];
        var modulename = getModuleName(mainelement);

        str.get_string('pluginname', modtypename).done(function(pluginname) {
            var plugindata = {
                type: pluginname,
                name: modulename
            };
            str.get_strings([
                {key: 'confirm'},
                {
                    key: (modulename === null ? 'deletechecktype' : 'deletechecktypename'),
                    param: plugindata
                },
                {key: 'yes'},
                {key: 'no'}
            ]).done(function(s) {
                    Notification.confirm(s[0], s[1], s[2], s[3], onconfirm);
            }).fail(
                Notification.exception
            );
        });
    };

    /**
     * Wrapper for M.util.add_spinner for an activity
     *
     * @param {object} activity
     * @returns {Node}
     */
    var addActivitySpinner = function(activity) {
        activity.addClass(CSS.EDITINPROGRESS);
        var actionarea = activity.find(Selector.ACTIONAREA).get(0);
        if (actionarea) {
            var spinner = M.util.add_spinner(Y, Y.Node(actionarea));
            spinner.show();
            return spinner;
        }
        return null;
    };

    /**
     * Wrapper for M.util.add_lightbox
     *
     * @param {object} sectionelement
     * @returns {Node}
     */
    var addSectionLightbox = function(sectionelement) {
        var lightbox = M.util.add_lightbox(Y, Y.Node(sectionelement.get(0)));
        lightbox.show();
        return lightbox;
    };

    /**
     * Removes the spinner element
     *
     * @param {object} element
     * @param {Node} spinner
     * @param {Number} delay
     */
    var removeSpinner = function(element, spinner, delay) {
        window.setTimeout(function() {
            element.removeClass(CSS.EDITINPROGRESS);
            if (spinner) {
                spinner.hide();
            }
        }, delay);
    };

    /**
     * Removes the lightbox element
     *
     * @param {Node} lightbox lighbox YUI element returned by addSectionLightbox
     * @param {Number} delay
     */
    var removeLightbox = function(lightbox, delay) {
        if (lightbox) {
            window.setTimeout(function() {
                lightbox.hide();
            }, delay);
        }
    };

    return {
        confirmDeleteModule: function(mainelement, onconfirm) {
            confirmDeleteModule(mainelement, onconfirm);
        },
        removeLightbox: function (lightbox, delay) {
            removeLightbox(lightbox, delay);
        },
        removeSpinner: function (element, spinner, delay) {
            removeSpinner(element, spinner, delay);
        },
        getModuleName: function (element) {
            getModuleName(element);
        },
        addSectionLightbox: function(sectionelement) {
            addSectionLightbox(sectionelement);
        },
        addActivitySpinner: function(activity) {
            addActivitySpinner(activity);
        }
    };
});