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
 * Controls the notification popover in the nav bar.
 *
 * See template: message_popup/notification_popover
 *
 * @module     message_popup/notification_popover_controller
 * @class      notification_popover_controller
 * @package    message_popup
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/str', 'core/url',
            'core/notification', 'core/custom_interaction_events', 'core/popover_region_controller',
            'message_popup/notification_repository', 'message_popup/notification_area_events'],
        function($, Ajax, Templates, Str, URL, DebugNotification, CustomEvents,
            PopoverController, NotificationRepo, NotificationAreaEvents) {

    var SELECTORS = {
        MARK_ALL_READ_BUTTON: '[data-action="mark-all-read"]',
        ALL_NOTIFICATIONS_CONTAINER: '[data-region="all-notifications"]',
        NOTIFICATION: '[data-region="notification-content-item-container"]',
        UNREAD_NOTIFICATION: '[data-region="notification-content-item-container"].unread',
        NOTIFICATION_LINK: '[data-action="content-item-link"]',
        EMPTY_MESSAGE: '[data-region="empty-message"]',
        COUNT_CONTAINER: '[data-region="count-container"]',
    };

    /**
     * Constructor for the NotificationPopoverController.
     * Extends PopoverRegionController.
     *
     * @param {object} element jQuery object root element of the popover
     */
    var NotificationPopoverController = function(element) {
        // Initialise base class.
        PopoverController.call(this, element);

        this.markAllReadButton = this.root.find(SELECTORS.MARK_ALL_READ_BUTTON);
        this.unreadCount = 0;
        this.userId = this.root.attr('data-userid');
        this.container = this.root.find(SELECTORS.ALL_NOTIFICATIONS_CONTAINER);
        this.limit = 20;
        this.offset = 0;
        this.loadedAll = false;
        this.initialLoad = false;

        // Let's find out how many unread notifications there are.
        this.unreadCount = this.root.find(SELECTORS.COUNT_CONTAINER).html();
    };

    /**
     * Clone the parent prototype.
     */
    NotificationPopoverController.prototype = Object.create(PopoverController.prototype);

    /**
     * Make sure the constructor is set correctly.
     */
    NotificationPopoverController.prototype.constructor = NotificationPopoverController;

    /**
     * Set the correct aria label on the menu toggle button to be read out by screen
     * readers. The message will indicate the state of the unread notifications.
     *
     * @method updateButtonAriaLabel
     */
    NotificationPopoverController.prototype.updateButtonAriaLabel = function() {
        if (this.isMenuOpen()) {
            Str.get_string('hidenotificationwindow', 'message').done(function(string) {
                this.menuToggle.attr('aria-label', string);
            }.bind(this));
        } else {
            if (this.unreadCount) {
                Str.get_string('shownotificationwindowwithcount', 'message', this.unreadCount).done(function(string) {
                    this.menuToggle.attr('aria-label', string);
                }.bind(this));
            } else {
                Str.get_string('shownotificationwindownonew', 'message').done(function(string) {
                    this.menuToggle.attr('aria-label', string);
                }.bind(this));
            }
        }
    };

    /**
     * Return the jQuery element with the content. This will return either
     * the unread notification container or the all notification container
     * depending on which is currently visible.
     *
     * @method getContent
     * @return {object} jQuery object currently visible content contianer
     */
    NotificationPopoverController.prototype.getContent = function() {
        return this.container;
    };

    /**
     * Get the offset value for the current state of the popover in order
     * to sent to the backend to correctly paginate the notifications.
     *
     * @method getOffset
     * @return {int} current offset
     */
    NotificationPopoverController.prototype.getOffset = function() {
        return this.offset;
    };

    /**
     * Increment the offset for the current state, if required.
     *
     * @method incrementOffset
     */
    NotificationPopoverController.prototype.incrementOffset = function() {
        this.offset += this.limit;
    };

    /**
     * Check if the first load of notification has been triggered for the current
     * state of the popover.
     *
     * @method hasDoneInitialLoad
     * @return {bool} true if first notification loaded, false otherwise
     */
    NotificationPopoverController.prototype.hasDoneInitialLoad = function() {
        return this.initialLoad;
    };

    /**
     * Check if we've loaded all of the notifications for the current popover
     * state.
     *
     * @method hasLoadedAllContent
     * @return {bool} true if all notifications loaded, false otherwise
     */
    NotificationPopoverController.prototype.hasLoadedAllContent = function() {
        return this.loadedAll;
    };

    /**
     * Set the state of the loaded all content property for the current state
     * of the popover.
     *
     * @method setLoadedAllContent
     * @param {bool} val True if all content is loaded, false otherwise
     */
    NotificationPopoverController.prototype.setLoadedAllContent = function(val) {
        this.loadedAll = val;
    };

    /**
     * Show the unread notification count badge on the menu toggle if there
     * are unread notifications, otherwise hide it.
     *
     * @method renderUnreadCount
     */
    NotificationPopoverController.prototype.renderUnreadCount = function() {
        var element = this.root.find(SELECTORS.COUNT_CONTAINER);

        if (this.unreadCount) {
            element.text(this.unreadCount);
            element.removeClass('hidden');
        } else {
            element.addClass('hidden');
        }
    };

    /**
     * Hide the unread notification count badge on the menu toggle.
     *
     * @method hideUnreadCount
     */
    NotificationPopoverController.prototype.hideUnreadCount = function() {
        this.root.find(SELECTORS.COUNT_CONTAINER).addClass('hidden');
    };

    /**
     * Find the notification element for the given id.
     *
     * @param {int} id
     * @method getNotificationElement
     * @return {object|null} The notification element
     */
    NotificationPopoverController.prototype.getNotificationElement = function(id) {
        var element = this.root.find(SELECTORS.NOTIFICATION + '[data-id="' + id + '"]');
        return element.length == 1 ? element : null;
    };

    /**
     * Render the notification data with the appropriate template and add it to the DOM.
     *
     * @method renderNotifications
     * @param {array} notifications Notification data
     * @param {object} container jQuery object the container to append the rendered notifications
     * @return {object} jQuery promise that is resolved when all notifications have been
     *                  rendered and added to the DOM
     */
    NotificationPopoverController.prototype.renderNotifications = function(notifications, container) {
        var promises = [];

        $.each(notifications, function(index, notification) {
            // Determine what the offset was when loading this notification.
            var offset = this.getOffset() - this.limit;
            // Update the view more url to contain the offset to allow the notifications
            // page to load to the correct position in the list of notifications.
            notification.viewmoreurl = URL.relativeUrl('/message/output/popup/notifications.php', {
                notificationid: notification.id,
                offset: offset,
            });

            // Link to mark read page before loading the actual link.
            notification.contexturl = URL.relativeUrl('message/output/popup/mark_notification_read.php', {
                notificationid: notification.id,
                redirecturl: notification.contexturl
            });

            var promise = Templates.render('message_popup/notification_content_item', notification)
            .then(function(html, js) {
                return {html: html, js: js};
            });
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
     * Send a request for more notifications from the server, if we aren't already
     * loading some and haven't already loaded all of them.
     *
     * Takes into account the current mode of the popover and will request only
     * unread notifications if required.
     *
     * All notifications are marked as read by the server when they are returned.
     *
     * @method loadMoreNotifications
     * @return {object} jQuery promise that is resolved when notifications have been
     *                        retrieved and added to the DOM
     */
    NotificationPopoverController.prototype.loadMoreNotifications = function() {
        if (this.isLoading || this.hasLoadedAllContent()) {
            return $.Deferred().resolve();
        }

        this.startLoading();
        var request = {
            limit: this.limit,
            offset: this.getOffset(),
            useridto: this.userId,
        };

        var container = this.getContent();
        return NotificationRepo.query(request).then(function(result) {
            var notifications = result.notifications;
            this.unreadCount = result.unreadcount;
            this.setLoadedAllContent(!notifications.length || notifications.length < this.limit);
            this.initialLoad = true;
            this.updateButtonAriaLabel();

            if (notifications.length) {
                this.incrementOffset();
                return this.renderNotifications(notifications, container);
            }

            return false;
        }.bind(this))
        .always(function() {
            this.stopLoading();
        }.bind(this));
    };

    /**
     * Send a request to the server to mark all unread notifications as read and update
     * the unread count and unread notification elements appropriately.
     *
     * @return {Promise}
     * @method markAllAsRead
     */
    NotificationPopoverController.prototype.markAllAsRead = function() {
        this.markAllReadButton.addClass('loading');

        return NotificationRepo.markAllAsRead({useridto: this.userId})
            .then(function() {
                this.unreadCount = 0;
                this.root.find(SELECTORS.UNREAD_NOTIFICATION).removeClass('unread');
            }.bind(this))
            .always(function() {
                this.markAllReadButton.removeClass('loading');
            }.bind(this));
    };

    /**
     * Add all of the required event listeners for this notification popover.
     *
     * @method registerEventListeners
     */
    NotificationPopoverController.prototype.registerEventListeners = function() {
        CustomEvents.define(this.root, [
            CustomEvents.events.activate,
        ]);

        // Mark all notifications read if the user activates the mark all as read button.
        this.root.on(CustomEvents.events.activate, SELECTORS.MARK_ALL_READ_BUTTON, function(e, data) {
            this.markAllAsRead();
            e.stopPropagation();
            data.originalEvent.preventDefault();
        }.bind(this));

        // Mark individual notification read if the user activates it.
        this.root.on(CustomEvents.events.activate, SELECTORS.NOTIFICATION_LINK, function(e) {
            var element = $(e.target).closest(SELECTORS.NOTIFICATION);

            if (element.hasClass('unread')) {
                this.unreadCount--;
                element.removeClass('unread');
            }

            e.stopPropagation();
        }.bind(this));

        // Update the notification information when the menu is opened.
        this.root.on(this.events().menuOpened, function() {
            this.hideUnreadCount();
            this.updateButtonAriaLabel();

            if (!this.hasDoneInitialLoad()) {
                this.loadMoreNotifications();
            }
        }.bind(this));

        // Update the unread notification count when the menu is closed.
        this.root.on(this.events().menuClosed, function() {
            this.renderUnreadCount();
            this.updateButtonAriaLabel();
        }.bind(this));

        // Set aria attributes when popover is loading.
        this.root.on(this.events().startLoading, function() {
            this.getContent().attr('aria-busy', 'true');
        }.bind(this));

        // Set aria attributes when popover is finished loading.
        this.root.on(this.events().stopLoading, function() {
            this.getContent().attr('aria-busy', 'false');
        }.bind(this));

        // Load more notifications if the user has scrolled to the end of content
        // item list.
        this.getContentContainer().on(CustomEvents.events.scrollBottom, function() {
            if (!this.isLoading && !this.hasLoadedAllContent()) {
                this.loadMoreNotifications();
            }
        }.bind(this));

        // Stop mouse scroll from propagating to the window element and
        // scrolling the page.
        CustomEvents.define(this.getContentContainer(), [
            CustomEvents.events.scrollLock
        ]);

        // Listen for when a notification is shown in the notifications page and mark
        // it as read, if it's unread.
        $(document).on(NotificationAreaEvents.notificationShown, function(e, notification) {
            if (!notification.read) {
                var element = this.getNotificationElement(notification.id);

                if (element) {
                    element.removeClass('unread');
                }

                this.unreadCount--;
                this.renderUnreadCount();
            }
        }.bind(this));
    };

    return NotificationPopoverController;
});
