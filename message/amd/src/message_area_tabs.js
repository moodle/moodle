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
 * This module handles the tabs of the messaging area.
 *
 * @module     core_message/message_area_tabs
 * @package    core_message
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/custom_interaction_events'], function(CustomEvents) {

    /**
     * Tabs class.
     *
     * @param {Messagearea} messageArea The messaging area object.
     */
    function Tabs(messageArea) {
        this.messageArea = messageArea;
        this._init();
    }

    /** @type {Boolean} checks if we are currently deleting */
    Tabs.prototype._isDeleting = false;

    /** @type {Messagearea} The messaging area object. */
    Tabs.prototype.messageArea = null;

    /**
     * Initialise the event listeners.
     *
     * @private
     */
    Tabs.prototype._init = function() {
        CustomEvents.define(this.messageArea.node, [
            CustomEvents.events.activate,
            CustomEvents.events.up,
            CustomEvents.events.down,
            CustomEvents.events.next,
            CustomEvents.events.previous,
            CustomEvents.events.ctrlPageUp,
            CustomEvents.events.ctrlPageDown,
        ]);

        this.messageArea.onDelegateEvent(CustomEvents.events.activate, this.messageArea.SELECTORS.VIEWCONVERSATIONS,
                this._viewConversations.bind(this));
        this.messageArea.onDelegateEvent(CustomEvents.events.activate, this.messageArea.SELECTORS.VIEWCONTACTS,
                this._viewContacts.bind(this));

        // Change to the other tab if any arrow keys are pressed, since there are only two tabs.
        this.messageArea.onDelegateEvent(CustomEvents.events.up, this.messageArea.SELECTORS.VIEWCONVERSATIONS,
                this._toggleTabs.bind(this));
        this.messageArea.onDelegateEvent(CustomEvents.events.down, this.messageArea.SELECTORS.VIEWCONVERSATIONS,
                this._toggleTabs.bind(this));
        this.messageArea.onDelegateEvent(CustomEvents.events.next, this.messageArea.SELECTORS.VIEWCONVERSATIONS,
                this._toggleTabs.bind(this));
        this.messageArea.onDelegateEvent(CustomEvents.events.previous, this.messageArea.SELECTORS.VIEWCONVERSATIONS,
                this._toggleTabs.bind(this));
        // Change to the other tab if any arrow keys are pressed, since there are only two tabs.
        this.messageArea.onDelegateEvent(CustomEvents.events.up, this.messageArea.SELECTORS.VIEWCONTACTS,
                this._toggleTabs.bind(this));
        this.messageArea.onDelegateEvent(CustomEvents.events.down, this.messageArea.SELECTORS.VIEWCONTACTS,
                this._toggleTabs.bind(this));
        this.messageArea.onDelegateEvent(CustomEvents.events.next, this.messageArea.SELECTORS.VIEWCONTACTS,
                this._toggleTabs.bind(this));
        this.messageArea.onDelegateEvent(CustomEvents.events.previous, this.messageArea.SELECTORS.VIEWCONTACTS,
                this._toggleTabs.bind(this));
        // Tab panel keyboard handling.
        this.messageArea.onDelegateEvent(CustomEvents.events.ctrlPageUp, this.messageArea.SELECTORS.CONTACTSPANELS,
                this._toggleTabs.bind(this));
        this.messageArea.onDelegateEvent(CustomEvents.events.ctrlPageDown, this.messageArea.SELECTORS.CONTACTSPANELS,
                this._toggleTabs.bind(this));

        this.messageArea.onCustomEvent(this.messageArea.EVENTS.CHOOSEMESSAGESTODELETE, function() {
            this._isDeleting = true;
        }.bind(this));
        this.messageArea.onCustomEvent(this.messageArea.EVENTS.MESSAGESDELETED, function() {
            this._isDeleting = false;
        }.bind(this));
        this.messageArea.onCustomEvent(this.messageArea.EVENTS.CANCELDELETEMESSAGES, function() {
            this._isDeleting = false;
        }.bind(this));
        this.messageArea.onCustomEvent(this.messageArea.EVENTS.MESSAGESENT, function() {
            this._selectTab(this.messageArea.SELECTORS.VIEWCONVERSATIONS, this.messageArea.SELECTORS.VIEWCONTACTS);
        }.bind(this));
    };

    /**
     * Handles when the conversation tab is selected.
     *
     * @private
     */
    Tabs.prototype._viewConversations = function() {
        if (this._isDeleting) {
            return;
        }

        this.messageArea.trigger(this.messageArea.EVENTS.CONVERSATIONSSELECTED);
        this._selectTab(this.messageArea.SELECTORS.VIEWCONVERSATIONS, this.messageArea.SELECTORS.VIEWCONTACTS);
    };

    /**
     * Handles when the contacts tab is selected.
     *
     * @private
     */
    Tabs.prototype._viewContacts = function() {
        if (this._isDeleting) {
            return;
        }

        this.messageArea.trigger(this.messageArea.EVENTS.CONTACTSSELECTED);
        this._selectTab(this.messageArea.SELECTORS.VIEWCONTACTS, this.messageArea.SELECTORS.VIEWCONVERSATIONS);
    };

    /**
     * Sets a tab to selected.
     *
     * @param {String} tabselect The name of the tab to select
     * @param {String} tabdeselect The name of the tab to deselect
     * @private
     */
    Tabs.prototype._selectTab = function(tabselect, tabdeselect) {
        tabdeselect = this.messageArea.find(tabdeselect);
        tabdeselect.removeClass('selected');
        tabdeselect.attr('aria-selected', 'false');
        tabdeselect.attr('tabindex', '-1');

        tabselect = this.messageArea.find(tabselect);
        tabselect.addClass('selected');
        tabselect.attr('aria-selected', 'true');
        tabselect.attr('tabindex', '0');
    };

    /**
     * Change to the inactive tab.
     *
     * @param {event} e The javascript event
     * @param {object} data The additional event data
     * @private
     */
    Tabs.prototype._toggleTabs = function(e, data) {
        var activeTab = this.messageArea.find(this.messageArea.SELECTORS.ACTIVECONTACTSTAB);

        if (activeTab.is(this.messageArea.SELECTORS.VIEWCONVERSATIONS)) {
            this._viewContacts();
        } else {
            this._viewConversations();
        }

        this.messageArea.find(this.messageArea.SELECTORS.ACTIVECONTACTSTAB).focus();

        e.preventDefault();
        e.stopPropagation();
        data.originalEvent.preventDefault();
        data.originalEvent.stopPropagation();
    };

    return Tabs;
});
