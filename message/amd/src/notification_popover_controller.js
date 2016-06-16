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
 * See template: message/notification_menu
 *
 * @module     message/notification_popover_controller
 * @class      notification_popover_controller
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery', 'theme_bootstrapbase/bootstrap', 'core/ajax', 'core/templates', 'core/str',
            'core/notification', 'core/custom_interaction_events', 'core/mdl_popover_controller',
            'message/notification_repository'],
        function($, bootstrap, ajax, templates, str, debugNotification, customEvents,
            popoverController, notificationRepo) {

    var SELECTORS = {
        MARK_ALL_READ_BUTTON: '#mark-all-read-button',
        USER_ID: 'data-userid',
        MODE_TOGGLE: '.mdl-popover-header-actions .fancy-toggle',
        UNREAD_NOTIFICATIONS_CONTAINER: '.unread-notifications',
        ALL_NOTIFICATIONS_CONTAINER: '.all-notifications',
        SHOW_BUTTON: '.show-button',
        HIDE_BUTTON: '.hide-button',
        CONTENT_ITEM_CONTAINER: '.content-item-container',
        EMPTY_MESSAGE: '.empty-message',
        CONTENT_BODY_SHORT: '.content-body-short',
        CONTENT_BODY_FULL: '.content-body-full',
    };

    /**
     * Constructor for the NotificationPopoverController.
     * Extends MdlPopoverController.
     *
     * @param element jQuery object root element of the popover
     * @return object NotificationPopoverController
     */
    var NotificationPopoverController = function(element) {
        // Initialise base class.
        popoverController.call(this, element);

        this.markAllReadButton = this.root.find(SELECTORS.MARK_ALL_READ_BUTTON);
        this.unreadCount = 0;
        this.userId = this.root.attr(SELECTORS.USER_ID);
        this.modeToggle = this.root.find(SELECTORS.MODE_TOGGLE);
        this.state = {
            unread: {
                container: this.root.find(SELECTORS.UNREAD_NOTIFICATIONS_CONTAINER),
                limit: 6,
                offset: 0,
                loadedAll: false,
                initialLoad: false,
            },
            all: {
                container: this.root.find(SELECTORS.ALL_NOTIFICATIONS_CONTAINER),
                limit: 20,
                offset: 0,
                loadedAll: false,
                initialLoad: false,
            }
        };

        // Let's find out how many unread notifications there are.
        this.loadUnreadNotificationCount();
        this.root.find('[data-toggle="tooltip"]').tooltip();
    };

    /**
     * Clone the parent prototype.
     */
    NotificationPopoverController.prototype = Object.create(popoverController.prototype);

    /**
     * Set the correct aria label on the menu toggle button to be read out by screen
     * readers. The message will indicate the state of the unread notifications.
     *
     * @method updateButtonAriaLabel
     */
    NotificationPopoverController.prototype.updateButtonAriaLabel = function() {
        if (this.isMenuOpen()) {
            str.get_string('hidenotificationwindow', 'message').done(function(string) {
                this.menuToggle.attr('aria-label', string);
            }.bind(this));
        } else {
            if (this.unreadCount) {
                str.get_string('shownotificationwindowwithcount', 'message', this.unreadCount).done(function(string) {
                    this.menuToggle.attr('aria-label', string);
                }.bind(this));
            } else {
                str.get_string('shownotificationwindownonew', 'message').done(function(string) {
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
     * @return jQuery object currently visible content contianer
     */
    NotificationPopoverController.prototype.getContent = function() {
        return this.getState().container;
    };

    /**
     * Check whether the notification menu is showing unread notification or
     * all notifications.
     *
     * @method unreadOnlyMode
     * @return bool true if only showing unread notifications, false otherwise
     */
    NotificationPopoverController.prototype.unreadOnlyMode = function() {
        return this.modeToggle.hasClass('on');
    };

    /**
     * Get the current state of the notification menu. Checks whether
     * the popover is in unread only mode.
     *
     * The internal state tracks various properties required for loading
     * notifications.
     *
     * @method getState
     * @return object unread state or all state
     */
    NotificationPopoverController.prototype.getState = function() {
        if (this.unreadOnlyMode()) {
            return this.state.unread;
        } else {
            return this.state.all;
        }
    };

    /**
     * Get the offset value for the current state of the popover in order
     * to sent to the backend to correctly paginate the notifications.
     *
     * @method getOffset
     * @return int current offset
     */
    NotificationPopoverController.prototype.getOffset = function() {
        return this.getState().offset;
    };

    /**
     * Increment the offset for the current state, if required.
     *
     * @method incrementOffset
     */
    NotificationPopoverController.prototype.incrementOffset = function() {
        // Only need to increment offset if we're combining read and unread
        // because all unread messages are marked as read when we retrieve them
        // which acts as the result set increment for us.
        if (!this.unreadOnlyMode()) {
            this.getState().offset += this.getState().limit;
        }
    };

    /**
     * Reset the offset to zero for the current state.
     *
     * @method resetOffset
     */
    NotificationPopoverController.prototype.resetOffset = function() {
        this.getState().offset = 0;
    };

    /**
     * Check if the first load of notification has been triggered for the current
     * state of the popover.
     *
     * @method hasDoneInitialLoad
     * @return bool true if first notification loaded, false otherwise
     */
    NotificationPopoverController.prototype.hasDoneInitialLoad = function() {
        return this.getState().initialLoad;
    };

    /**
     * Check if we've loaded all of the notifications for the current popover
     * state.
     *
     * @method hasLoadedAllContent
     * @return bool true if all notifications loaded, false otherwise
     */
    NotificationPopoverController.prototype.hasLoadedAllContent = function() {
        return this.getState().loadedAll;
    };

    /**
     * Set the state of the loaded all content property for the current state
     * of the popover.
     *
     * @method setLoadedAllContent
     * @param bool true if all content is loaded, false otherwise
     */
    NotificationPopoverController.prototype.setLoadedAllContent = function(val) {
        this.getState().loadedAll = val;
    };

    /**
     * Reset the unread notification state and empty the unread notification content
     * element.
     *
     * @method clearUnreadNotifications
     */
    NotificationPopoverController.prototype.clearUnreadNotifications = function() {
        this.state.unread.offset = 0;
        this.state.unread.loadedAll = false;
        this.state.unread.initialLoad = false;
        this.state.unread.container.empty();
    };

    /**
     * Show the unread notification count badge on the menu toggle if there
     * are unread notifications, otherwise hide it.
     *
     * @method renderUnreadCount
     */
    NotificationPopoverController.prototype.renderUnreadCount = function() {
        var element = this.root.find('.count-container');

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
        this.root.find('.count-container').addClass('hidden');
    };

    /**
     * Ask the server how many unread notifications are left, render the value
     * as a badge on the menu toggle and update the aria labels on the menu
     * toggle.
     *
     * @method loadUnreadNotificationCount
     */
    NotificationPopoverController.prototype.loadUnreadNotificationCount = function() {
        notificationRepo.countUnread({useridto: this.userId}).then(function(count) {
            this.unreadCount = count;
            this.renderUnreadCount();
            this.updateButtonAriaLabel();
        }.bind(this));
    };

    /**
     * Render the notification data with the appropriate template and add it to the DOM.
     *
     * @method renderNotifications
     * @param notifications array notification data
     * @param container jQuery object the container to append the rendered notifications
     * @return jQuery promise that is resolved when all notifications have been
     *                rendered and added to the DOM
     */
    NotificationPopoverController.prototype.renderNotifications = function(notifications, container) {
        var promises = [];

        if (notifications.length) {
            $.each(notifications, function(index, notification) {
                var promise = templates.render('message/notification_content_item', notification);
                promise.then(function(html, js) {
                    container.append(html);
                    templates.runTemplateJS(js);
                }.bind(this));

                promises.push(promise);
            }.bind(this));
        }

        return $.when.apply($.when, promises);
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
     * @return jQuery promise that is resolved when notifications have been
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
            markasread: true,
            embeduserto: false,
            embeduserfrom: true,
        };

        if (this.unreadOnlyMode()) {
            request.status = 'unread';
        }

        var container = this.getContent();
        var promise = notificationRepo.query(request).then(function(result) {
            var notifications = result.notifications;
            this.unreadCount = result.unreadcount;
            this.setLoadedAllContent(!notifications.length || notifications.length < this.limit);
            this.getState().initialLoad = true;
            this.updateButtonAriaLabel();

            if (notifications.length) {
                this.incrementOffset();
                return this.renderNotifications(notifications, container);
            }
        }.bind(this))
        .always(function() { this.stopLoading(); }.bind(this));

        return promise;
    };

    /**
     * Send a request to the server to mark all unread notifications as read and update
     * the unread count and unread notification elements appropriately.
     *
     * @method markAllAsRead
     */
    NotificationPopoverController.prototype.markAllAsRead = function() {
        this.markAllReadButton.addClass('loading');

        return notificationRepo.markAllAsRead({useridto: this.userId})
            .then(function() {
                this.unreadCount = 0;
                this.clearUnreadNotifications();
            }.bind(this))
            .always(function() { this.markAllReadButton.removeClass('loading'); }.bind(this));
    };

    /**
     * Shift focus to the next content item in the list if the content item
     * list current contains focus, otherwise the first item in the list is
     * given focus.
     *
     * Overrides MdlPopoverController.focusNextContentItem
     * @method focusNextContentItem
     */
    NotificationPopoverController.prototype.focusNextContentItem = function() {
        var currentFocus = $(document.activeElement);
        var container = this.getContent();

        if (container.has(currentFocus).length) {
            var currentNotification = currentFocus.closest(SELECTORS.CONTENT_ITEM_CONTAINER);
            currentNotification.next().focus();
        } else {
            this.focusFirstContentItem();
        }
    };

    /**
     * Shift focus to the previous content item in the content item list, if the
     * content item list contains focus.
     *
     * Overrides MdlPopoverController.focusPreviousContentItem
     * @method focusPreviousContentItem
     */
    NotificationPopoverController.prototype.focusPreviousContentItem = function() {
        var currentFocus = $(document.activeElement);
        var container = this.getContent();

        if (container.has(currentFocus).length) {
            var currentNotification = currentFocus.closest(SELECTORS.CONTENT_ITEM_CONTAINER);
            currentNotification.prev().focus();
        }
    };

    /**
     * Give focus to the first item in the list of content items.
     *
     * Overrides MdlPopoverController.focusFirstContentItem
     * @method focusFirstContentItem
     */
    NotificationPopoverController.prototype.focusFirstContentItem = function() {
        var container = this.getContent();
        var notification = container.children().first();

        if (!notification.length) {
            // If we don't have any notifications then we should focus the empty
            // empty message for the user.
            notification = container.next(SELECTORS.EMPTY_MESSAGE);
        }

        notification.focus();
    };

    /**
     * Give focus to the last item in the list of content items, that is the list
     * of notifications that have already been loaded.
     *
     * Overrides MdlPopoverController.focusLastContentItem
     * @method focusLastContentItem
     */
    NotificationPopoverController.prototype.focusLastContentItem = function() {
        var container = this.getContent();
        var notification = container.children().last();

        if (!notification.length) {
            // If we don't have any notifications then we should focus the empty
            // empty message for the user.
            notification = container.next(SELECTORS.EMPTY_MESSAGE);
        }

        notification.focus();
    };

    /**
     * Expand all the currently rendered notificaitons in the current state
     * of the popover (unread or all).
     *
     * @method expandAllContentItems
     */
    NotificationPopoverController.prototype.expandAllContentItems = function() {
        this.getContent()
            .find(SELECTORS.CONTENT_ITEM_CONTAINER)
            .addClass('expanded')
            .attr('aria-expanded', 'true');
    };

    /**
     * Expand a single content item.
     *
     * @method expandContentItem
     * @param item jQuery object the content item to be expanded
     */
    NotificationPopoverController.prototype.expandContentItem = function(item) {
        item.addClass('expanded');
        item.attr('aria-expanded', 'true');
        item.find(SELECTORS.SHOW_BUTTON).attr('aria-hidden', 'true');
        item.find(SELECTORS.CONTENT_BODY_SHORT).attr('aria-hidden', 'true');
        item.find(SELECTORS.CONTENT_BODY_FULL).attr('aria-hidden', 'false');
        item.find(SELECTORS.HIDE_BUTTON).attr('aria-hidden', 'false').focus();
    };

    /**
     * Collapse a single content item.
     *
     * @method collapseContentItem
     * @param item jQuery object the content item to be collapsed.
     */
    NotificationPopoverController.prototype.collapseContentItem = function(item) {
        item.removeClass('expanded');
        item.attr('aria-expanded', 'false');
        item.find(SELECTORS.HIDE_BUTTON).attr('aria-hidden', 'true');
        item.find(SELECTORS.CONTENT_BODY_FULL).attr('aria-hidden', 'true');
        item.find(SELECTORS.CONTENT_BODY_SHORT).attr('aria-hidden', 'false');
        item.find(SELECTORS.SHOW_BUTTON).attr('aria-hidden', 'false').focus();
    };

    /**
     * Navigate the browser to the content URL for the content item, if it has one.
     *
     * @method navigateToContextURL
     * @param item jQuery object representing the content item
     */
    NotificationPopoverController.prototype.navigateToContextURL = function(item) {
        var url = item.attr('data-context-url');

        if (url) {
            window.location.assign(url);
        }
    };

    /**
     * Add all of the required event listeners for this notification popover.
     *
     * @method registerEventListeners
     */
    NotificationPopoverController.prototype.registerEventListeners = function() {
        customEvents.define(this.root, [
            customEvents.events.activate,
            customEvents.events.next,
            customEvents.events.previous,
            customEvents.events.asterix,
        ]);

        // Expand the content item if the user activates (click/enter/space) the show
        // button.
        this.root.on(customEvents.events.activate, SELECTORS.SHOW_BUTTON, function(e, data) {
            var container = $(e.target).closest(SELECTORS.CONTENT_ITEM_CONTAINER);
            this.expandContentItem(container);

            e.stopPropagation();
            data.originalEvent.preventDefault();
        }.bind(this));

        // Expand the content item if the user triggers the next event (right arrow in LTR).
        this.root.on(customEvents.events.next, SELECTORS.CONTENT_ITEM_CONTAINER, function(e) {
            var contentItem = $(e.target).closest(SELECTORS.CONTENT_ITEM_CONTAINER);
            this.expandContentItem(contentItem);
        }.bind(this));

        // Collapse the content item if the user activates the hide button.
        this.root.on(customEvents.events.activate, SELECTORS.HIDE_BUTTON, function(e, data) {
            var container = $(e.target).closest(SELECTORS.CONTENT_ITEM_CONTAINER);
            this.collapseContentItem(container);

            e.stopPropagation();
            data.originalEvent.preventDefault();
        }.bind(this));

        // Collapse the content item if the user triggers the previous event (left arrow in LTR).
        this.root.on(customEvents.events.previous, SELECTORS.CONTENT_ITEM_CONTAINER, function(e) {
            var contentItem = $(e.target).closest(SELECTORS.CONTENT_ITEM_CONTAINER);
            this.collapseContentItem(contentItem);
        }.bind(this));

        // Switch between popover states (read/unread) if the user activates the toggle.
        this.root.on(customEvents.events.activate, SELECTORS.MODE_TOGGLE, function(e) {
            if (this.modeToggle.hasClass('on')) {
                this.clearUnreadNotifications();
                this.modeToggle.removeClass('on');
                this.modeToggle.addClass('off');
                this.root.removeClass('unread-only');

                str.get_string('shownewnotifications', 'message').done(function(string) {
                    this.modeToggle.attr('aria-label', string);
                }.bind(this));
            } else {
                this.modeToggle.removeClass('off');
                this.modeToggle.addClass('on');
                this.root.addClass('unread-only');

                str.get_string('showallnotifications', 'message').done(function(string) {
                    this.modeToggle.attr('aria-label', string);
                }.bind(this));
            }

            if (!this.hasDoneInitialLoad()) {
                this.loadMoreNotifications();
            }

            e.stopPropagation();
        }.bind(this));

        // Follow the context URL if the user activates the content item.
        this.root.on(customEvents.events.activate, SELECTORS.CONTENT_ITEM_CONTAINER, function(e) {
            var contentItem = $(e.target).closest(SELECTORS.CONTENT_ITEM_CONTAINER);
            this.navigateToContextURL(contentItem);
            e.stopPropagation();
        }.bind(this));

        // Mark all notifications read if the user activates the mark all as read button.
        this.root.on(customEvents.events.activate, SELECTORS.MARK_ALL_READ_BUTTON, function(e) {
            this.markAllAsRead();
            e.stopPropagation();
        }.bind(this));

        // Expand all the currently visible content items if the user hits the
        // asterix key.
        this.root.on(customEvents.events.asterix, function() {
            this.expandAllContentItems();
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
            this.clearUnreadNotifications();
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
        this.getContentContainer().on(customEvents.events.scrollBottom, function() {
            if (!this.isLoading && !this.hasLoadedAllContent()) {
                this.loadMoreNotifications();
            }
        }.bind(this));
    };

    return NotificationPopoverController;
});
