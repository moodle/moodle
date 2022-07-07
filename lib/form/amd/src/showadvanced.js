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
 * A class to help show and hide advanced form content.
 *
 * @module     core_form/showadvanced
 * @class      showadvanced
 * @package    core_form
 * @copyright  2016 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/log', 'core/str', 'core/notification'], function($, Log, Strings, Notification) {

    var SELECTORS = {
            FIELDSETCONTAINSADVANCED: 'fieldset.containsadvancedelements',
            DIVFITEMADVANCED: 'div.fitem.advanced',
            DIVFCONTAINER: 'div.fcontainer',
            MORELESSLINK: 'fieldset.containsadvancedelements .moreless-toggler'
        },
        CSS = {
            SHOW: 'show',
            MORELESSACTIONS: 'moreless-actions',
            MORELESSTOGGLER: 'moreless-toggler',
            SHOWLESS: 'moreless-less'
        },
        WRAPPERS = {
            FITEM: '<div class="fitem"></div>',
            FELEMENT: '<div class="felement"></div>'
        },
        IDPREFIX = 'showadvancedid-';

    /** @type {Integer} uniqIdSeed Auto incrementing number used to generate ids. */
    var uniqIdSeed = 0;

    /**
     * ShowAdvanced behaviour class.
     * @param {String} id The id of the form.
     */
    var ShowAdvanced = function(id) {
        this.id = id;

        var form = $(document.getElementById(id));
        this.enhanceForm(form);
    };

    /** @type {String} id The form id to enhance. */
    ShowAdvanced.prototype.id = '';

    /**
     * @method enhanceForm
     * @param {JQuery} form JQuery selector representing the form
     * @return {ShowAdvanced}
     */
    ShowAdvanced.prototype.enhanceForm = function(form) {
        var fieldsets = form.find(SELECTORS.FIELDSETCONTAINSADVANCED);

        // Enhance each fieldset in the form matching the selector.
        fieldsets.each(function(index, item) {
            this.enhanceFieldset($(item));
        }.bind(this));

        // Attach some event listeners.
        // Subscribe more/less links to click event.
        form.on('click', SELECTORS.MORELESSLINK, this.switchState);

        // Subscribe to key events but filter for space or enter.
        form.on('keydown', SELECTORS.MORELESSLINK, function(e) {
            // Enter or space.
            if (e.which == 13 || e.which == 32) {
                return this.switchState(e);
            }
            return true;
        }.bind(this));
        return this;
    };


    /**
     * Generates a uniq id for the dom element it's called on unless the element already has an id.
     * The id is set on the dom node before being returned.
     *
     * @method generateId
     * @param {JQuery} node JQuery selector representing a single DOM Node.
     * @return {String}
     */
    ShowAdvanced.prototype.generateId = function(node) {
        var id = node.prop('id');
        if (typeof id === 'undefined') {
            id = IDPREFIX + (uniqIdSeed++);
            node.prop('id', id);
        }
        return id;
    };

    /**
     * @method enhanceFieldset
     * @param {JQuery} fieldset JQuery selector representing a fieldset
     * @return {ShowAdvanced}
     */
    ShowAdvanced.prototype.enhanceFieldset = function(fieldset) {
        var statuselement = $('input[name=mform_showmore_' + fieldset.prop('id') + ']');
        if (!statuselement.length) {
            Log.debug("M.form.showadvanced::processFieldset was called on an fieldset without a status field: '" +
                fieldset.prop('id') + "'");
            return this;
        }

        // Fetch some strings.
        Strings.get_strings([{
            key: 'showmore',
            component: 'core_form'
        }, {
            key: 'showless',
            component: 'core_form'
        }]).then(function(results) {
            var showmore = results[0],
                showless = results[1];

            // Generate more/less links.
            var morelesslink = $('<a href="#"></a>');
            morelesslink.addClass(CSS.MORELESSTOGGLER);
            if (statuselement.val() === '0') {
                morelesslink.html(showmore);
            } else {
                morelesslink.html(showless);
                morelesslink.addClass(CSS.SHOWLESS);
                fieldset.find(SELECTORS.DIVFITEMADVANCED).addClass(CSS.SHOW);
            }
            // Build a list of advanced fieldsets.
            var idlist = [];
            fieldset.find(SELECTORS.DIVFITEMADVANCED).each(function(index, node) {
                idlist[idlist.length] = this.generateId($(node));
            }.bind(this));

            // Set aria attributes.
            morelesslink.attr('role', 'button');
            morelesslink.attr('aria-controls', idlist.join(' '));

            // Add elements to the DOM.
            var fitem = $(WRAPPERS.FITEM);
            fitem.addClass(CSS.MORELESSACTIONS);
            var felement = $(WRAPPERS.FELEMENT);
            felement.append(morelesslink);
            fitem.append(felement);

            fieldset.find(SELECTORS.DIVFCONTAINER).append(fitem);
            return true;
        }.bind(this)).fail(Notification.exception);

        return this;
    };

    /**
     * @method switchState
     * @param {Event} e Event that triggered this action.
     * @return {Boolean}
     */
    ShowAdvanced.prototype.switchState = function(e) {
        e.preventDefault();

        // Fetch some strings.
        Strings.get_strings([{
            key: 'showmore',
            component: 'core_form'
        }, {
            key: 'showless',
            component: 'core_form'
        }]).then(function(results) {
            var showmore = results[0],
                showless = results[1],
                fieldset = $(e.target).closest(SELECTORS.FIELDSETCONTAINSADVANCED);

            // Toggle collapsed class.
            fieldset.find(SELECTORS.DIVFITEMADVANCED).toggleClass(CSS.SHOW);

            // Get corresponding hidden variable.
            var statuselement = $('input[name=mform_showmore_' + fieldset.prop('id') + ']');

            // Invert it and change the link text.
            if (statuselement.val() === '0') {
                statuselement.val(1);
                $(e.target).addClass(CSS.SHOWLESS);
                $(e.target).html(showless);
            } else {
                statuselement.val(0);
                $(e.target).removeClass(CSS.SHOWLESS);
                $(e.target).html(showmore);
            }
            return true;
        }).fail(Notification.exception);

        return this;
    };

    return {
        /**
         * Initialise this module.
         * @method init
         * @param {String} formid
         * @return {ShowAdvanced}
         */
        init: function(formid) {
            return new ShowAdvanced(formid);
        }
    };
});
