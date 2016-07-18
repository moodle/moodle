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
 * Controls the message popover in the nav bar.
 *
 * See template: message/message_menu
 *
 * @module     core_message/message_popover_controller
 * @class      message_popover_controller
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery', 'theme_bootstrapbase/bootstrap', 'core/ajax', 'core/templates', 'core/str',
            'core/notification', 'core/custom_interaction_events', 'core/mdl_popover_controller',
            'core_message/message_repository'],
        function($, bootstrap, ajax, templates, str, debugNotification, customEvents,
            PopoverController, messageRepo) {

    var SELECTORS = {
        MARK_ALL_READ_BUTTON: '.mark-all-read-button',
        USER_ID: 'data-userid',
        MODE_TOGGLE: '.mdl-popover-header-actions .fancy-toggle',
        CONTENT: '.messages',
        CONTENT_ITEM_CONTAINER: '.content-item-container',
        EMPTY_MESSAGE: '.empty-message',
    };

    /**
     * Constructor for the MessagePopoverController.
     * Extends MdlPopoverController.
     *
     * @param element jQuery object root element of the popover
     * @return object MessagePopoverController
     */
    var MessagePopoverController = function(element) {
        // Initialise base class.
        PopoverController.call(this, element);

        this.markAllReadButton = this.root.find(SELECTORS.MARK_ALL_READ_BUTTON);
        this.content = this.root.find(SELECTORS.CONTENT);
        this.userId = this.root.attr(SELECTORS.USER_ID);
        this.limit = 20;
        this.offset = 0;
        this.loadedAll = false;
        this.initialLoad = false;

        // Let's find out how many unread messages there are.
        this.loadUnreadMessageCount();
        this.root.find('[data-toggle="tooltip"]').tooltip();
    };

    /**
     * Clone the parent prototype.
     */
    MessagePopoverController.prototype = Object.create(PopoverController.prototype);

    /**
     * Get the element holding the messages.
     *
     * @method getContent
     * @return jQuery element
     */
    MessagePopoverController.prototype.getContent = function() {
        return this.content;
    };

    /**
     * Increment the offset.
     *
     * @method incrementOffset
     */
    MessagePopoverController.prototype.incrementOffset = function() {
        this.offset += this.limit;
    };

    /**
     * Set the correct aria label on the menu toggle button to be read out by screen
     * readers. The message will indicate the state of the unread notifications.
     *
     * @method updateButtonAriaLabel
     */
    MessagePopoverController.prototype.updateButtonAriaLabel = function() {
        if (this.isMenuOpen()) {
            str.get_string('hidemessagewindow', 'message').done(function(string) {
                this.menuToggle.attr('aria-label', string);
            }.bind(this));
        } else {
            if (this.unreadCount) {
                str.get_string('showmessagewindowwithcount', 'message', this.unreadCount).done(function(string) {
                    this.menuToggle.attr('aria-label', string);
                }.bind(this));
            } else {
                str.get_string('showmessagewindownonew', 'message').done(function(string) {
                    this.menuToggle.attr('aria-label', string);
                }.bind(this));
            }
        }
    };

    /**
     * Show the unread notification count badge on the menu toggle if there
     * are unread notifications, otherwise hide it.
     *
     * @method renderUnreadCount
     */
    MessagePopoverController.prototype.renderUnreadCount = function() {
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
    MessagePopoverController.prototype.hideUnreadCount = function() {
        this.root.find('.count-container').addClass('hidden');
    };

    /**
     * Ask the server how many unread notifications are left, render the value
     * as a badge on the menu toggle and update the aria labels on the menu
     * toggle.
     *
     * @method loadUnreadMessageCount
     */
    MessagePopoverController.prototype.loadUnreadMessageCount = function() {
        messageRepo.countUnread({useridto: this.userId}).then(function(count) {
            this.unreadCount = count;
            this.renderUnreadCount();
            this.updateButtonAriaLabel();
        }.bind(this));
    };

    /**
     * Render the notification data with the appropriate template and add it to the DOM.
     *
     * @method renderMessages
     * @param messages array message data
     * @param container jQuery object the container to append the rendered messages
     * @return jQuery promise that is resolved when all notifications have been
     *                rendered and added to the DOM
     */
    MessagePopoverController.prototype.renderMessages = function(messages, container) {
        var promises = [];

        if (messages.length) {
            $.each(messages, function(index, message) {
                var promise = templates.render('message/message_content_item', message);
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
     * Send a request for more messages from the server, if we aren't already
     * loading some and haven't already loaded all of them.
     *
     * @method loadMoreMessages
     * @return jQuery promise that is resolved when notifications have been
     *                        retrieved and added to the DOM
     */
    MessagePopoverController.prototype.loadMoreMessages = function() {
        if (this.isLoading || this.loadedAll) {
            return $.Deferred().resolve();
        }

        this.startLoading();
        var request = {
            userid: this.userId,
            limit: this.limit,
            offset: this.offset,
        };

        var container = this.getContent();
        var promise = messageRepo.query(request).then(function(result) {
            var messages = result.contacts;
            this.loadedAll = !messages.length || messages.length < this.limit;
            this.initialLoad = true;
            this.updateButtonAriaLabel();

            if (messages.length) {
                this.incrementOffset();
                return this.renderMessages(messages, container);
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
    MessagePopoverController.prototype.markAllAsRead = function() {
        this.markAllReadButton.addClass('loading');

        return messageRepo.markAllAsRead({useridto: this.userId})
            .then(function() {
                this.unreadCount = 0;
                this.clearUnreadNotifications();
            }.bind(this))
            .always(function() { this.markAllReadButton.removeClass('loading'); }.bind(this));
    };

    /**
     * Add all of the required event listeners for this notification popover.
     *
     * @method registerEventListeners
     */
    MessagePopoverController.prototype.registerEventListeners = function() {
        // Update the notification information when the menu is opened.
        this.root.on(this.events().menuOpened, function() {
            this.hideUnreadCount();
            this.updateButtonAriaLabel();

            if (!this.initialLoad) {
                this.loadMoreMessages();
            }
        }.bind(this));

        // Update the notification information when the menu is opened.
        this.root.on(this.events().menuClosed, function() {
            this.renderUnreadCount();
            this.updateButtonAriaLabel();
        }.bind(this));
    };

    return MessagePopoverController;
});
