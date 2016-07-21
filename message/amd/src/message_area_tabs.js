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
define([], function() {

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
        this.messageArea.onDelegateEvent('click', this.messageArea.SELECTORS.VIEWCONVERSATIONS, this._viewConversations.bind(this));
        this.messageArea.onDelegateEvent('click', this.messageArea.SELECTORS.VIEWCONTACTS, this._viewContacts.bind(this));
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
        this.messageArea.find(tabdeselect).removeClass('selected');
        this.messageArea.find(tabselect).addClass('selected');
    };

    return Tabs;
});