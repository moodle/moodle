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
 * See template: message_popup/message_popover
 *
 * @module     message_popup/message_popover_controller
 * @class      message_popover_controller
 * @package    message_popup
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/str',
            'core/notification', 'core/custom_interaction_events', 'core/popover_region_controller',
            'core_message/message_repository', 'core/url'],
        function($, Ajax, Templates, Str, Notification, CustomEvents,
            PopoverController, MessageRepo, URL) {

    var SELECTORS = {
        MARK_ALL_READ_BUTTON: '[data-action="mark-all-read"]',
        CONTENT: '[data-region="messages"]',
        CONTENT_ITEM_CONTAINER: '[data-region="message-content-item-container"]',
        EMPTY_MESSAGE: '[data-region="empty-message"]',
        COUNT_CONTAINER: '[data-region="count-container"]',
    };

    /**
     * Constructor for the MessagePopoverController.
     * Extends PopoverRegionController.
     *
     * @param {object} element jQuery object root element of the popover
     */
    var MessagePopoverController = function(element) {
        // Initialise base class.
        PopoverController.call(this, element);

        this.markAllReadButton = this.root.find(SELECTORS.MARK_ALL_READ_BUTTON);
        this.content = this.root.find(SELECTORS.CONTENT);
        this.userId = this.root.attr('data-userid');
        this.limit = 20;
        this.offset = 0;
        this.loadedAll = false;
        this.initialLoad = false;

        // Let's find out how many unread messages there are.
        this.loadUnreadMessageCount();
    };

    /**
     * Clone the parent prototype.
     */
    MessagePopoverController.prototype = Object.create(PopoverController.prototype);

    /**
     * Make sure the constructor is set correctly.
     */
    MessagePopoverController.prototype.constructor = MessagePopoverController;

    /**
     * Get the element holding the messages.
     *
     * @method getContent
     * @return {object} jQuery element
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
     * readers. The message will indicate the state of the unread messages.
     *
     * @method updateButtonAriaLabel
     */
    MessagePopoverController.prototype.updateButtonAriaLabel = function() {
        if (this.isMenuOpen()) {
            Str.get_string('hidemessagewindow', 'message').done(function(string) {
                this.menuToggle.attr('aria-label', string);
            }.bind(this));
        } else {
            if (this.unreadCount) {
                Str.get_string('showmessagewindowwithcount', 'message', this.unreadCount).done(function(string) {
                    this.menuToggle.attr('aria-label', string);
                }.bind(this));
            } else {
                Str.get_string('showmessagewindownonew', 'message').done(function(string) {
                    this.menuToggle.attr('aria-label', string);
                }.bind(this));
            }
        }
    };

    /**
     * Show the unread message count badge on the menu toggle if there
     * are unread messages, otherwise hide it.
     *
     * @method renderUnreadCount
     */
    MessagePopoverController.prototype.renderUnreadCount = function() {
        var element = this.root.find(SELECTORS.COUNT_CONTAINER);

        if (this.unreadCount) {
            element.text(this.unreadCount);
            element.removeClass('hidden');
        } else {
            element.addClass('hidden');
        }
    };

    /**
     * Hide the unread message count badge on the menu toggle.
     *
     * @method hideUnreadCount
     */
    MessagePopoverController.prototype.hideUnreadCount = function() {
        this.root.find(SELECTORS.COUNT_CONTAINER).addClass('hidden');
    };

    /**
     * Ask the server how many unread messages are left, render the value
     * as a badge on the menu toggle and update the aria labels on the menu
     * toggle.
     *
     * @method loadUnreadMessageCount
     */
    MessagePopoverController.prototype.loadUnreadMessageCount = function() {
        MessageRepo.countUnreadConversations({useridto: this.userId}).then(function(count) {
            this.unreadCount = count;
            this.renderUnreadCount();
            this.updateButtonAriaLabel();
        }.bind(this));
    };

    /**
     * Render the message data with the appropriate template and add it to the DOM.
     *
     * @method renderMessages
     * @param {array} messages Message data
     * @param {object} container jQuery object the container to append the rendered messages
     * @return {object} jQuery promise that is resolved when all messages have been
     *                rendered and added to the DOM
     */
    MessagePopoverController.prototype.renderMessages = function(messages, container) {
        var promises = [];
        var allhtml = [];
        var alljs = [];

        if (messages.length) {
            $.each(messages, function(index, message) {
                message.contexturl = URL.relativeUrl('/message/index.php', {
                    user: this.userId,
                    id: message.userid,
                });

                message.profileurl = URL.relativeUrl('/user/profile.php', {
                    id: message.userid,
                });

                var promise = Templates.render('message_popup/message_content_item', message);
                promises.push(promise);

                promise.then(function(html, js) {
                    allhtml[index] = html;
                    alljs[index] = js;
                });
            }.bind(this));
        }

        return $.when.apply($.when, promises).then(function() {
            if (messages.length) {
                $.each(messages, function(index) {
                    container.append(allhtml[index]);
                    Templates.runTemplateJS(alljs[index]);
                });
            }
        });
    };

    /**
     * Send a request for more messages from the server, if we aren't already
     * loading some and haven't already loaded all of them.
     *
     * @method loadMoreMessages
     * @return {object} jQuery promise that is resolved when messages have been
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
        return MessageRepo.query(request).then(function(result) {
            var messages = result.contacts;
            this.loadedAll = !messages.length || messages.length < this.limit;
            this.initialLoad = true;
            this.updateButtonAriaLabel();

            if (messages.length) {
                this.incrementOffset();
                return this.renderMessages(messages, container);
            }

            return false;
        }.bind(this))
        .always(function() {
            this.stopLoading();
        }.bind(this));
    };

    /**
     * Send a request to the server to mark all unread messages as read and update
     * the unread count and unread messages elements appropriately.
     *
     * @method markAllAsRead
     * @return {Promise}
     */
    MessagePopoverController.prototype.markAllAsRead = function() {
        if (this.markAllReadButton.hasClass('loading')) {
            return $.Deferred().resolve();
        }

        this.markAllReadButton.addClass('loading');

        return MessageRepo.markAllAsRead({useridto: this.userId})
            .then(function() {
                this.unreadCount = 0;
                this.hideUnreadCount();
                this.getContent().find(SELECTORS.CONTENT_ITEM_CONTAINER).removeClass('unread');
            }.bind(this))
            .always(function() {
                this.markAllReadButton.removeClass('loading');
            }.bind(this));
    };

    /**
     * Add all of the required event listeners for this messages popover.
     *
     * @method registerEventListeners
     */
    MessagePopoverController.prototype.registerEventListeners = function() {
        CustomEvents.define(this.root, [
            CustomEvents.events.keyboardActivate,
        ]);

        // Update the message information when the menu is opened.
        this.root.on(this.events().menuOpened, function() {
            this.hideUnreadCount();
            this.updateButtonAriaLabel();

            if (!this.initialLoad) {
                this.loadMoreMessages();
            }
        }.bind(this));

        // Update the message information when the menu is opened.
        this.root.on(this.events().menuClosed, function() {
            this.renderUnreadCount();
            this.updateButtonAriaLabel();
        }.bind(this));

        // Load more messages when we scroll to the bottom of the open menu.
        this.root.on(CustomEvents.events.scrollBottom, function() {
            this.loadMoreMessages();
        }.bind(this));

        // Mark all messages as read when button is activated.
        this.root.on(CustomEvents.events.activate, SELECTORS.MARK_ALL_READ_BUTTON, function(e, data) {
            this.markAllAsRead();

            e.stopPropagation();
            data.originalEvent.preventDefault();
        }.bind(this));

        // Stop mouse scroll from propagating to the window element and
        // scrolling the page.
        CustomEvents.define(this.getContentContainer(), [
            CustomEvents.events.scrollLock
        ]);

        // Check if we have marked a conversation as read in the messaging area.
        $(document).on('messagearea:conversationselected', function() {
            this.unreadCount--;
            this.renderUnreadCount();
        }.bind(this));
    };

    return MessagePopoverController;
});
