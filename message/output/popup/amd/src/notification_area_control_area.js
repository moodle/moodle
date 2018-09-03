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
 * Controls the notification area on the notification page.
 *
 * @module     message_popup/notification_area_control_area
 * @class      notification_area_control_area
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/templates', 'core/notification', 'core/custom_interaction_events',
        'message_popup/notification_repository', 'message_popup/notification_area_events'],
    function($, Templates, DebugNotification, CustomEvents, NotificationRepo, NotificationAreaEvents) {

    var SELECTORS = {
        CONTAINER: '[data-region="notification-area"]',
        CONTENT: '[data-region="content"]',
        NOTIFICATION: '[data-region="notification-content-item-container"]',
        CAN_RECEIVE_FOCUS: 'input:not([type="hidden"]), a[href], button, textarea, select, [tabindex]',
    };

    var TEMPLATES = {
        NOTIFICATION: 'message_popup/notification_content_item',
    };

    /**
     * Constructor for ControlArea
     *
     * @param {object} root The root element for the content area
     * @param {int} userId The user id of the current user
     */
    var ControlArea = function(root, userId) {
        this.root = $(root);
        this.container = this.root.closest(SELECTORS.CONTAINER);
        this.userId = userId;
        this.content = this.root.find(SELECTORS.CONTENT);
        this.offset = 0;
        this.limit = 20;
        this.initialLoad = false;
        this.isLoading = false;
        this.loadedAll = false;
        this.notifications = {};

        this.registerEventListeners();
    };

    /**
     * Get the root element.
     *
     * @method getRoot
     * @return {object} jQuery element
     */
    ControlArea.prototype.getRoot = function() {
        return this.root;
    };

    /**
     * Get the container element (which the control area is within).
     *
     * @method getContainer
     * @return {object} jQuery element
     */
    ControlArea.prototype.getContainer = function() {
        return this.container;
    };

    /**
     * Get the user id.
     *
     * @method getUserId
     * @return {int}
     */
    ControlArea.prototype.getUserId = function() {
        return this.userId;
    };

    /**
     * Get the control area content element.
     *
     * @method getContent
     * @return {object} jQuery element
     */
    ControlArea.prototype.getContent = function() {
        return this.content;
    };

    /**
     * Get the offset value for paginated loading of the
     * notifications.
     *
     * @method getOffset
     * @return {int}
     */
    ControlArea.prototype.getOffset = function() {
        return this.offset;
    };

    /**
     * Get the limit value for the paginated loading of the
     * notifications.
     *
     * @method getLimit
     * @return {int}
     */
    ControlArea.prototype.getLimit = function() {
        return this.limit;
    };

    /**
     * Set the offset value for the paginated loading of the
     * notifications.
     *
     * @method setOffset
     * @param {int} value The new offset value
     */
    ControlArea.prototype.setOffset = function(value) {
        this.offset = value;
    };

    /**
     * Set the limit value for the paginated loading of the
     * notifications.
     *
     * @method setLimit
     * @param {int} value The new limit value
     */
    ControlArea.prototype.setLimit = function(value) {
        this.limit = value;
    };

    /**
     * Increment the offset by the limit amount.
     *
     * @method incrementOffset
     */
    ControlArea.prototype.incrementOffset = function() {
        this.offset += this.limit;
    };

    /**
     * Flag the control area as loading.
     *
     * @method startLoading
     */
    ControlArea.prototype.startLoading = function() {
        this.isLoading = true;
        this.getRoot().addClass('loading');
    };

    /**
     * Remove the loading flag from the control area.
     *
     * @method stopLoading
     */
    ControlArea.prototype.stopLoading = function() {
        this.isLoading = false;
        this.getRoot().removeClass('loading');
    };

    /**
     * Check if the first load of notifications has been triggered.
     *
     * @method hasDoneInitialLoad
     * @return {bool} true if first notification loaded, false otherwise
     */
    ControlArea.prototype.hasDoneInitialLoad = function() {
        return this.initialLoad;
    };

    /**
     * Check if all of the notifications have been loaded.
     *
     * @method hasLoadedAllContent
     * @return {bool}
     */
    ControlArea.prototype.hasLoadedAllContent = function() {
        return this.loadedAll;
    };

    /**
     * Set the state of the loaded all content property.
     *
     * @method setLoadedAllContent
     * @param {bool} val True if all content is loaded, false otherwise
     */
    ControlArea.prototype.setLoadedAllContent = function(val) {
        this.loadedAll = val;
    };

    /**
     * Save a notification in the cache.
     *
     * @method setCacheNotification
     * @param {object} notification A notification returned by a webservice
     */
    ControlArea.prototype.setCacheNotification = function(notification) {
        this.notifications[notification.id] = notification;
    };

    /**
     * Retrieve a notification from the cache.
     *
     * @method getCacheNotification
     * @param {int} id The id for the notification you wish to retrieve
     * @return {object} A notification (as returned by a webservice)
     */
    ControlArea.prototype.getCacheNotification = function(id) {
        return this.notifications[id];
    };

    /**
     * Find the notification element in the control area for the given id.
     *
     * @method getNotificationElement
     * @param {int} id The notification id
     * @return {(object|null)} jQuery element or null
     */
    ControlArea.prototype.getNotificationElement = function(id) {
        var element = this.getRoot().find(SELECTORS.NOTIFICATION + '[data-id="' + id + '"]');
        return element.length == 1 ? element : null;
    };

    /**
     * Scroll the notification element into view within the control area, if it
     * isn't already visible.
     *
     * @method scrollNotificationIntoView
     * @param {object} notificationElement The jQuery notification element
     */
    ControlArea.prototype.scrollNotificationIntoView = function(notificationElement) {
        var position = notificationElement.position();
        var container = this.getRoot();
        var relativeTop = position.top - container.scrollTop();

        // If the element isn't in the view window.
        if (relativeTop > container.innerHeight()) {
            var height = notificationElement.outerHeight();
            // Offset enough to make sure the notification will be in view.
            height = height * 4;
            var scrollTo = position.top - height;
            container.scrollTop(scrollTo);
        }
    };

    /**
     * Show the full notification for the given notification element. The notification
     * context is retrieved from the cache and send as data with an event to be
     * rendered in the content area.
     *
     * @method showNotification
     * @param {(int|object)} notificationElement The notification id or jQuery notification element
     */
    ControlArea.prototype.showNotification = function(notificationElement) {
        if (typeof notificationElement !== 'object') {
            // Assume it's an ID if it's not an object.
            notificationElement = this.getNotificationElement(notificationElement);
        }

        if (notificationElement && notificationElement.length) {
            this.getRoot().find(SELECTORS.NOTIFICATION).removeClass('selected');
            notificationElement.addClass('selected').find(SELECTORS.CAN_RECEIVE_FOCUS).focus();
            var notificationId = notificationElement.attr('data-id');
            var notification = this.getCacheNotification(notificationId);
            this.scrollNotificationIntoView(notificationElement);
            // Create a new version of the notification to send with the notification so
            // this copy isn't modified.
            this.getContainer().trigger(NotificationAreaEvents.showNotification, [$.extend({}, notification)]);
        }
    };

    /**
     * Send a request to mark the notification as read in the server and remove the unread
     * status from the element.
     *
     * @method markNotificationAsRead
     * @param {object} notificationElement The jQuery notification element
     * @return {object} jQuery promise
     */
    ControlArea.prototype.markNotificationAsRead = function(notificationElement) {
        return NotificationRepo.markAsRead(notificationElement.attr('data-id')).done(function() {
            notificationElement.removeClass('unread');
        });
    };


    /**
     * Render the notification data with the appropriate template and add it to the DOM.
     *
     * @method renderNotifications
     * @param {array} notifications Array of notification data
     * @return {object} jQuery promise that is resolved when all notifications have been
     *                  rendered and added to the DOM
     */
    ControlArea.prototype.renderNotifications = function(notifications) {
        var promises = [];
        var container = this.getContent();

        $.each(notifications, function(index, notification) {
            // Need to remove the contexturl so the item isn't rendered
            // as a link.
            var contextUrl = notification.contexturl;
            delete notification.contexturl;

            var promise = Templates.render(TEMPLATES.NOTIFICATION, notification)
            .then(function(html, js) {
                // Restore it for the cache.
                notification.contexturl = contextUrl;
                this.setCacheNotification(notification);
                // Pass the Rendered content out.
                return {html: html, js: js};
            }.bind(this));
            promises.push(promise);
        }.bind(this));

        return $.when.apply($, promises).then(function() {
            // Each of the promises in the when will pass its results as an argument to the function.
            // The order of the arguments will be the order that the promises are passed to when()
            // i.e. the first promise's results will be in the first argument.
            $.each(arguments, function(index, argument) {
                container.append(argument.html);
                Templates.runTemplateJS(argument.js);
            });
            return;
        });
    };

    /**
     * Load notifications from the server and render them.
     *
     * @method loadMoreNotifications
     * @return {object} jQuery promise
     */
    ControlArea.prototype.loadMoreNotifications = function() {
        if (this.isLoading || this.hasLoadedAllContent()) {
            return $.Deferred().resolve();
        }

        this.startLoading();
        var request = {
            limit: this.getLimit(),
            offset: this.getOffset(),
            useridto: this.getUserId(),
        };

        if (!this.initialLoad) {
            // If this is the first load we may have been given a non-zero offset,
            // in which case we need to load all notifications preceeding that offset
            // to make sure the full list is rendered.
            request.limit = this.getOffset() + this.getLimit();
            request.offset = 0;
        }

        var promise = NotificationRepo.query(request).then(function(result) {
            var notifications = result.notifications;
            this.unreadCount = result.unreadcount;
            this.setLoadedAllContent(!notifications.length || notifications.length < this.getLimit());
            this.initialLoad = true;

            if (notifications.length) {
                this.incrementOffset();
                return this.renderNotifications(notifications);
            }

            return false;
        }.bind(this))
        .always(function() {
            this.stopLoading();
        }.bind(this));

        return promise;
    };

    /**
     * Create the event listeners for the control area.
     *
     * @method registerEventListeners
     */
    ControlArea.prototype.registerEventListeners = function() {
        CustomEvents.define(this.getRoot(), [
            CustomEvents.events.activate,
            CustomEvents.events.scrollBottom,
            CustomEvents.events.scrollLock,
            CustomEvents.events.up,
            CustomEvents.events.down,
        ]);

        this.getRoot().on(CustomEvents.events.scrollBottom, function() {
            this.loadMoreNotifications();
        }.bind(this));

        this.getRoot().on(CustomEvents.events.activate, SELECTORS.NOTIFICATION, function(e) {
            var notificationElement = $(e.target).closest(SELECTORS.NOTIFICATION);
            this.showNotification(notificationElement);
        }.bind(this));

        // Show the previous notification in the list.
        this.getRoot().on(CustomEvents.events.up, SELECTORS.NOTIFICATION, function(e, data) {
            var notificationElement = $(e.target).closest(SELECTORS.NOTIFICATION);
            this.showNotification(notificationElement.prev());

            data.originalEvent.preventDefault();
        }.bind(this));

        // Show the next notification in the list.
        this.getRoot().on(CustomEvents.events.down, SELECTORS.NOTIFICATION, function(e, data) {
            var notificationElement = $(e.target).closest(SELECTORS.NOTIFICATION);
            this.showNotification(notificationElement.next());

            data.originalEvent.preventDefault();
        }.bind(this));

        this.getContainer().on(NotificationAreaEvents.notificationShown, function(e, notification) {
            if (!notification.read) {
                var element = this.getNotificationElement(notification.id);

                if (element) {
                    this.markNotificationAsRead(element);
                }

                var cachedNotification = this.getCacheNotification(notification.id);

                if (cachedNotification) {
                    cachedNotification.read = true;
                }
            }
        }.bind(this));
    };

    return ControlArea;
});
