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
 * Module to show a popup to view or add comments to a learning plan.
 *
 * @module     report_lpmonitoring/comments_popup
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'core/notification',
        'core/str',
        'core/ajax',
        'core/templates',
        'core/modal_factory',
        'core/modal_events'],
    function($, notification, str, ajax, templates, ModalFactory, ModalEvents) {

        /**
         * Constructor.
         *
         * @param {String} selector_button The CSS selector used to find triggers for the new dialogue.
         * @param {string} selector_nbcomments The CSS selector used to display the new number of comments for the plan.
         * @param {int} planid The learning plan id.
         *
         * Each call to init gets it's own instance of this class.
         */
        var CommentsPopup = function(selector_button, selector_nbcomments, planid) {
            var self = this;
            self.planid = planid;
            self.selector_nbcomments = selector_nbcomments;

            $(selector_button).on('click', this.handleClick.bind(this));
        };

        /**
         * @var {int} planid
         * @private
         */
        CommentsPopup.prototype.planid = -1;

        /**
         * @var {string} selector_nbcomments  The CSS selector used to display the new number of comments for the plan.
         * @private
         */
        CommentsPopup.prototype.selector_nbcomments = '';

        /**
         * @var {Dialogue} popup  The popup window (Dialogue).
         * @private
         */
        CommentsPopup.prototype.popup = null;

        /**
         * @var float actual_size  The size of the comment area.
         * @private
         */
        CommentsPopup.prototype.actual_size = 0;

        /**
         * Get the data from the clicked cell and open the popup.
         *
         * @method _handleClick
         * @param {Event} e
         */
        CommentsPopup.prototype.handleClick = function(e) {
            e.preventDefault();
            var trigger = $(e.target);
            var self = this;
            var requests = ajax.call([{
                methodname : 'report_lpmonitoring_get_comment_area_for_plan',
                args: { planid: self.planid }
            }]);
            $.when.apply($, requests).then(function(context) {
                self.commentareaLoaded.bind(this)(context, trigger);
                return;
            }.bind(this)).catch(notification.exception);
        };

        /**
         * We loaded the commentarea, now render the template.
         *
         * @method commentareaLoaded
         * @param {Object} commentarea
         * @param {Object} trigger
         */
        CommentsPopup.prototype.commentareaLoaded = function(commentarea, trigger) {
            var self = this;
            // We have to display user info in popup.
            return str.get_string('commentsedit', 'report_lpmonitoring').done(function(title) {
                return ModalFactory.create({
                    type: ModalFactory.types.DEFAULT,
                    title: title,
                    body: templates.render('report_lpmonitoring/comment_area', commentarea),
                    large: true
                }).done(function(modal) {
                    // Keep a reference to the modal.
                    self.popup = modal;
                    modal.getRoot().on(ModalEvents.hidden, function() {
                        self.close();
                        self.focusContentItem(trigger);
                    }.bind(this));
                    self.popup.show();
                }.bind(this));
        }).fail(notification.exception);
        };

        /**
         * Focus the given content item or the first focusable element within
         * the content item.
         *
         * @method focusContentItem
         * @param {object} item The content item jQuery element
         */
        CommentsPopup.prototype.focusContentItem = function(item) {
            var focusable = 'input:not([type="hidden"]), a[href], button, textarea, select, [tabindex]';
            if (item.is(focusable)) {
                item.focus();
            } else {
                item.find(focusable).first().focus();
            }
        };

        /**
         * Open the popup.
         *
         * @param {String} js
         * @method open
         */
        CommentsPopup.prototype.open = function(js) {
            templates.runTemplateJS(js);
        };

        /**
         * Close the popup and update comment count.
         *
         * @method close
         */
        CommentsPopup.prototype.close = function() {
            // Update the comment count.
            var self = this;
            var requests = ajax.call([{
                methodname : 'report_lpmonitoring_get_comment_area_for_plan',
                args: { planid: self.planid },
                fail: notification.exception
            }]);

            requests[0].then(function (commentarea) {
                $(self.selector_nbcomments).text(commentarea.count);
            });
            self.popup.destroy();
            self.popup = null;
        };

        return {
            /**
             * Attach event listeners to initialise this module.
             *
             * @method init
             * @param {string} selector_button The CSS selector used to find nodes that will trigger this module.
             * @param {string} selector_nbcomments The CSS selector used to display the new number of comments for the plan.
             * @param {int} planid The learning plan id.
             * @return {CommentsPopup} A new instance of CommentsPopup.
             */
            init: function(selector_button, selector_nbcomments, planid) {
                return new CommentsPopup(selector_button, selector_nbcomments, planid);
            }
        };
    });