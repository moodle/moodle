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
 * Adds support for confirmation via JS modal for some management actions at the Manage policies page.
 *
 * @module      tool_iomadpolicy/managedocsactions
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/log',
    'core/config',
    'core/str',
    'core/modal_factory',
    'core/modal_events'
], function($, Log, Config, Str, ModalFactory, ModalEvents) {

    "use strict";

    /**
     * List of action selectors.
     *
     * @property {string} LINKS - Selector for all action links
     * @property {string} MAKE_CURRENT
     */
    var ACTION = {
        LINKS: '[data-action]',
        MAKE_CURRENT: '[data-action="makecurrent"]',
        INACTIVATE: '[data-action="inactivate"]',
        DELETE: '[data-action="delete"]'
    };

    /**
     * @constructor
     * @param {Element} base - Management area wrapping element
     */
    function ManageDocsActions(base) {
        this.base = base;

        this.initEvents();
    }

    /**
     * Register event listeners.
     */
    ManageDocsActions.prototype.initEvents = function() {
        var self = this;

        self.base.on('click', ACTION.LINKS, function(e) {
            e.stopPropagation();

            var link = $(e.currentTarget);
            var promise;
            var strings;

            if (link.is(ACTION.MAKE_CURRENT)) {
                promise = Str.get_strings([
                    {key: 'activating', component: 'tool_iomadpolicy'},
                    {key: 'activateconfirm', component: 'tool_iomadpolicy', param: {
                        name: link.closest('[data-iomadpolicy-name]').attr('data-iomadpolicy-name'),
                        revision: link.closest('[data-iomadpolicy-revision]').attr('data-iomadpolicy-revision')
                    }},
                    {key: 'activateconfirmyes', component: 'tool_iomadpolicy'}
                ]);

            } else if (link.is(ACTION.INACTIVATE)) {
                promise = Str.get_strings([
                    {key: 'inactivating', component: 'tool_iomadpolicy'},
                    {key: 'inactivatingconfirm', component: 'tool_iomadpolicy', param: {
                        name: link.closest('[data-iomadpolicy-name]').attr('data-iomadpolicy-name'),
                        revision: link.closest('[data-iomadpolicy-revision]').attr('data-iomadpolicy-revision')
                    }},
                    {key: 'inactivatingconfirmyes', component: 'tool_iomadpolicy'}
                ]);

            } else if (link.is(ACTION.DELETE)) {
                promise = Str.get_strings([
                    {key: 'deleting', component: 'tool_iomadpolicy'},
                    {key: 'deleteconfirm', component: 'tool_iomadpolicy', param: {
                        name: link.closest('[data-iomadpolicy-name]').attr('data-iomadpolicy-name'),
                        revision: link.closest('[data-iomadpolicy-revision]').attr('data-iomadpolicy-revision')
                    }},
                    {key: 'delete', component: 'core'}
                ]);

            } else {
                Log.error('unknown action type detected', 'tool_iomadpolicy/managedocsactions');
                return;
            }

            e.preventDefault();

            promise.then(function(strs) {
                strings = strs;
                return ModalFactory.create({
                    title: strings[0],
                    body: strings[1],
                    type: ModalFactory.types.SAVE_CANCEL
                });

            }).then(function(modal) {
                modal.setSaveButtonText(strings[2]);
                modal.getRoot().on(ModalEvents.save, function() {
                    window.location.href = link.attr('href') + '&sesskey=' + Config.sesskey + '&confirm=1';
                });

                modal.getRoot().on(ModalEvents.hidden, function() {
                    modal.destroy();
                });

                modal.show();
                return true;

            }).catch(function(e) {
                Log.error(e);
                return false;
            });
        });
    };

    return {
        /**
         * Factory method returning instance of the ManageDocsActions
         *
         * @param {String} baseid - ID of the management area wrapping element
         * @return {ManageDocsActions}
         */
        init: function(baseid) {
            var base = $(document.getElementById(baseid));

            if (base.length) {
                return new ManageDocsActions(base);

            } else {
                throw new Error("managedocsactions: Invalid base element identifier");
            }
        }
    };
});
