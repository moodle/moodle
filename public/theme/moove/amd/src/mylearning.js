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
 * Controls the mylearning popover in the nav bar.
 *
 * See template: theme_moove/mylearning
 *
 * @copyright   2023 Willian Mano {@link https://conecti.me}
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/str', 'core/url',
        'core/notification', 'core/custom_interaction_events', 'core/popover_region_controller'],
    function($, Ajax, Templates, Str, URL, DebugNotification, CustomEvents,
             PopoverController) {

        var SELECTORS = {
            ALL_LEARNING_CONTAINER: '[data-region="all-learning"]',
            NOTIFICATION: '[data-region="mylearning-content-item-container"]',
            NOTIFICATION_LINK: '[data-action="content-item-link"]',
        };

        /**
         * Constructor for the MyLearningPopoverController.
         * Extends PopoverRegionController.
         *
         * @param {object} element jQuery object root element of the popover
         */
        var MyLearningPopoverController = function(element) {
            // Initialise base class.
            PopoverController.call(this, element);

            this.container = this.root.find(SELECTORS.ALL_LEARNING_CONTAINER);
            this.loadedAll = false;
        };

        /**
         * Clone the parent prototype.
         */
        MyLearningPopoverController.prototype = Object.create(PopoverController.prototype);

        /**
         * Make sure the constructor is set correctly.
         */
        MyLearningPopoverController.prototype.constructor = MyLearningPopoverController;

        /**
         * Set the correct aria label on the menu toggle button to be read out by screen
         * readers. The message will indicate the state of the unread notifications.
         *
         * @method updateButtonAriaLabel
         */
        MyLearningPopoverController.prototype.updateButtonAriaLabel = function() {
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
        MyLearningPopoverController.prototype.getContent = function() {
            return this.container;
        };

        /**
         * Check if we've loaded all of the notifications for the current popover
         * state.
         *
         * @method hasLoadedAllContent
         * @return {bool} true if all notifications loaded, false otherwise
         */
        MyLearningPopoverController.prototype.hasLoadedAllContent = function() {
            return this.loadedAll;
        };

        /**
         * Set the state of the loaded all content property for the current state
         * of the popover.
         *
         * @method setLoadedAllContent
         * @param {bool} val True if all content is loaded, false otherwise
         */
        MyLearningPopoverController.prototype.setLoadedAllContent = function(val) {
            this.loadedAll = val;
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
        MyLearningPopoverController.prototype.loadMyLearning = function() {
            if (this.isLoading || this.hasLoadedAllContent()) {
                return $.Deferred().resolve();
            }

            this.startLoading();

            var container = this.getContent();

            var request = Ajax.call([{
                methodname: 'theme_moove_get_my_learning',
                args: {}
            }]);

            request[0].done(function(response) {
                this.setLoadedAllContent(true);

                return this.renderMyLearning(JSON.parse(response.courses), container);
            }.bind(this)).fail(function() {
                //console.log('error');
            }.bind(this)).always(function() {
                this.stopLoading();
            }.bind(this));
        };

        /**
         * Render the notification data with the appropriate template and add it to the DOM.
         *
         * @method renderNotifications
         * @param {array} courses Course data
         * @param {object} container jQuery object the container to append the rendered notifications
         * @return {object} jQuery promise that is resolved when all notifications have been
         *                  rendered and added to the DOM
         */
        MyLearningPopoverController.prototype.renderMyLearning = function(courses, container) {
            var promises = [];

            $.each(courses, function(index, course) {
                var promise = Templates.render('theme_moove/moove/mylearning_course', course)
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
         * Add all of the required event listeners for this notification popover.
         *
         * @method registerEventListeners
         */
        MyLearningPopoverController.prototype.registerEventListeners = function() {
            CustomEvents.define(this.root, [
                CustomEvents.events.activate,
            ]);

            // Update the notification information when the menu is opened.
            this.root.on(this.events().menuOpened, function() {
                this.updateButtonAriaLabel();

                if (!this.hasLoadedAllContent()) {
                    this.loadMyLearning();
                }
            }.bind(this));

            // Update the unread notification count when the menu is closed.
            this.root.on(this.events().menuClosed, function() {
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
        };

        return MyLearningPopoverController;
    });