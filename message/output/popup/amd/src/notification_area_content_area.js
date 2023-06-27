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
 * Controls the content area of the notification area on the
 * notification page.
 *
 * @module     message_popup/notification_area_content_area
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/templates', 'core/notification', 'core/custom_interaction_events',
        'message_popup/notification_repository', 'message_popup/notification_area_events'],
    function($, Templates, DebugNotification, CustomEvents, NotificationRepo, NotificationAreaEvents) {

    var SELECTORS = {
        CONTAINER: '[data-region="notification-area"]',
        CONTENT: '[data-region="content"]',
        HEADER: '[data-region="header"]',
        FOOTER: '[data-region="footer"]',
        TOGGLE_MODE: '[data-action="toggle-mode"]',
    };

    var TEMPLATES = {
        HEADER: 'message_popup/notification_area_content_area_header',
        CONTENT: 'message_popup/notification_area_content_area_content',
        FOOTER: 'message_popup/notification_area_content_area_footer',
    };

    /**
     * Constructor for the ContentArea
     *
     * @class
     * @param {object} root The root element for the content area
     * @param {int} userId The user id of the current user
     */
    var ContentArea = function(root, userId) {
        this.root = $(root);
        this.container = this.root.closest(SELECTORS.CONTAINER);
        this.userId = userId;
        this.header = this.root.find(SELECTORS.HEADER);
        this.content = this.root.find(SELECTORS.CONTENT);
        this.footer = this.root.find(SELECTORS.FOOTER);

        this.registerEventListeners();
    };

    /**
     * Get the root element.
     *
     * @method getRoot
     * @return {object} jQuery element
     */
    ContentArea.prototype.getRoot = function() {
        return this.root;
    };

    /**
     * Get the container element (which the content area is within).
     *
     * @method getContainer
     * @return {object} jQuery element
     */
    ContentArea.prototype.getContainer = function() {
        return this.container;
    };

    /**
     * Get the user id.
     *
     * @method getUserId
     * @return {int}
     */
    ContentArea.prototype.getUserId = function() {
        return this.userId;
    };

    /**
     * Get the content area header element.
     *
     * @method getHeader
     * @return {object} jQuery element
     */
    ContentArea.prototype.getHeader = function() {
        return this.header;
    };

    /**
     * Get the content area content element.
     *
     * @method getContent
     * @return {object} jQuery element
     */
    ContentArea.prototype.getContent = function() {
        return this.content;
    };

    /**
     * Get the content area footer element.
     *
     * @method getFooter
     * @return {object} jQuery element
     */
    ContentArea.prototype.getFooter = function() {
        return this.footer;
    };

    /**
     * Display the content area. Typically used with responsive
     * styling on smaller screens.
     *
     * @method show
     */
    ContentArea.prototype.show = function() {
        this.getContainer().addClass('show-content-area');
    };

    /**
     * Hide the content area. Typically used with responsive
     * styling on smaller screens.
     *
     * @method hide
     */
    ContentArea.prototype.hide = function() {
        this.getContainer().removeClass('show-content-area');
    };

    /**
     * Change the HTML in the content area header element.
     *
     * @method setHeaderHTML
     * @param {string} html The HTML to be set
     */
    ContentArea.prototype.setHeaderHTML = function(html) {
        this.getHeader().empty().html(html);
    };

    /**
     * Change the HTML in the content area content element.
     *
     * @method setContentHMTL
     * @param {string} html The HTML to be set.
     */
    ContentArea.prototype.setContentHTML = function(html) {
        this.getContent().empty().html(html);
    };

    /**
     * Change the HTML in the content area footer element.
     *
     * @method setFooterHTML
     * @param {string} html The HTML to be set.
     */
    ContentArea.prototype.setFooterHTML = function(html) {
        this.getFooter().empty().html(html);
    };

    /**
     * Render the given notification context in the content area.
     *
     * @method showNotification
     * @param {object} notification The notification context (from a webservice)
     * @return {object} jQuery promise
     */
    ContentArea.prototype.showNotification = function(notification) {
        var headerPromise = Templates.render(TEMPLATES.HEADER, notification).done(function(html) {
            this.setHeaderHTML(html);
        }.bind(this));

        var contentPromise = Templates.render(TEMPLATES.CONTENT, notification).done(function(html) {
            this.setContentHTML(html);
        }.bind(this));

        var footerPromise = Templates.render(TEMPLATES.FOOTER, notification).done(function(html) {
            this.setFooterHTML(html);
        }.bind(this));

        return $.when(headerPromise, contentPromise, footerPromise).done(function() {
            this.show();
            this.getContainer().trigger(NotificationAreaEvents.notificationShown, [notification]);
        }.bind(this));
    };

    /**
     * Create the event listeners for the content area.
     *
     * @method registerEventListeners
     */
    ContentArea.prototype.registerEventListeners = function() {
        CustomEvents.define(this.getRoot(), [
            CustomEvents.events.activate
        ]);

        this.getRoot().on(CustomEvents.events.activate, SELECTORS.VIEW_TOGGLE, function() {
            this.hide();
        }.bind(this));

        this.getContainer().on(NotificationAreaEvents.showNotification, function(e, notification) {
            this.showNotification(notification);
        }.bind(this));
    };

    return ContentArea;
});
