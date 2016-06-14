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
 * User competency workflow.
 *
 * @module     tool_lp/user_competency_workflow
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery',
        'core/templates',
        'core/ajax',
        'core/notification',
        'core/str',
        'tool_lp/menubar',
        'tool_lp/event_base'],
        function($, Templates, Ajax, Notification, Str, Menubar, EventBase) {

    /**
     * UserCompetencyWorkflow class.
     *
     * @param {String} selector The node containing the buttons to switch mode.
     */
    var UserCompetencyWorkflow = function() {
        EventBase.prototype.constructor.apply(this, []);
    };
    UserCompetencyWorkflow.prototype = Object.create(EventBase.prototype);

    /** @type {String} The selector to find the user competency data. */
    UserCompetencyWorkflow.prototype._nodeSelector = '[data-node="user-competency"]';

    /**
     * Cancel a review request and refresh the view.
     *
     * @param  {Object} data The user competency data.
     * @return {Void}
     * @method _cancelReviewRequest
     */
    UserCompetencyWorkflow.prototype._cancelReviewRequest = function(data) {
        var call = {
            methodname: 'core_competency_user_competency_cancel_review_request',
            args: {
                userid: data.userid,
                competencyid: data.competencyid
            }
        };

        Ajax.call([call])[0].then(function() {
            this._trigger('review-request-cancelled', data);
            this._trigger('status-changed', data);
        }.bind(this), function() {
            this._trigger('error-occured', data);
        }.bind(this));
    };

    /**
     * Cancel a review request an refresh the view.
     *
     * @param  {Object} data The user competency data.
     * @return {Void}
     * @method cancelReviewRequest
     */
    UserCompetencyWorkflow.prototype.cancelReviewRequest = function(data) {
        this._cancelReviewRequest(data);
    };

    /**
     * Cancel a review request handler.
     *
     * @param  {Event} e The event.
     * @return {Void}
     * @method _cancelReviewRequestHandler
     */
    UserCompetencyWorkflow.prototype._cancelReviewRequestHandler = function(e) {
        e.preventDefault();
        var data = this._findUserCompetencyData($(e.target));
        this.cancelReviewRequest(data);
    };

    /**
     * Request a review and refresh the view.
     *
     * @param  {Object} data The user competency data.
     * @return {Void}
     * @method _requestReview
     */
    UserCompetencyWorkflow.prototype._requestReview = function(data) {
        var call = {
            methodname: 'core_competency_user_competency_request_review',
            args: {
                userid: data.userid,
                competencyid: data.competencyid
            }
        };

        Ajax.call([call])[0].then(function() {
            this._trigger('review-requested', data);
            this._trigger('status-changed', data);
        }.bind(this), function() {
            this._trigger('error-occured', data);
        }.bind(this));
    };

    /**
     * Request a review.
     *
     * @param  {Object} data The user competency data.
     * @return {Void}
     * @method requestReview
     */
    UserCompetencyWorkflow.prototype.requestReview = function(data) {
        this._requestReview(data);
    };

    /**
     * Request a review handler.
     *
     * @param  {Event} e The event.
     * @return {Void}
     * @method _requestReviewHandler
     */
    UserCompetencyWorkflow.prototype._requestReviewHandler = function(e) {
        e.preventDefault();
        var data = this._findUserCompetencyData($(e.target));
        this.requestReview(data);
    };

    /**
     * Start a review and refresh the view.
     *
     * @param  {Object} data The user competency data.
     * @return {Void}
     * @method _startReview
     */
    UserCompetencyWorkflow.prototype._startReview = function(data) {
        var call = {
            methodname: 'core_competency_user_competency_start_review',
            args: {
                userid: data.userid,
                competencyid: data.competencyid
            }
        };

        Ajax.call([call])[0].then(function() {
            this._trigger('review-started', data);
            this._trigger('status-changed', data);
        }.bind(this), function() {
            this._trigger('error-occured', data);
        }.bind(this));
    };

    /**
     * Start a review.
     *
     * @param  {Object} data The user competency data.
     * @return {Void}
     * @method startReview
     */
    UserCompetencyWorkflow.prototype.startReview = function(data) {
        this._startReview(data);
    };

    /**
     * Start a review handler.
     *
     * @param  {Event} e The event.
     * @return {Void}
     * @method _startReviewHandler
     */
    UserCompetencyWorkflow.prototype._startReviewHandler = function(e) {
        e.preventDefault();
        var data = this._findUserCompetencyData($(e.target));
        this.startReview(data);
    };

    /**
     * Stop a review and refresh the view.
     *
     * @param  {Object} data The user competency data.
     * @return {Void}
     * @method _stopReview
     */
    UserCompetencyWorkflow.prototype._stopReview = function(data) {
        var call = {
            methodname: 'core_competency_user_competency_stop_review',
            args: {
                userid: data.userid,
                competencyid: data.competencyid
            }
        };

        Ajax.call([call])[0].then(function() {
            this._trigger('review-stopped', data);
            this._trigger('status-changed', data);
        }.bind(this), function() {
            this._trigger('error-occured', data);
        }.bind(this));
    };

    /**
     * Stop a review.
     *
     * @param  {Object} data The user competency data.
     * @return {Void}
     * @method stopReview
     */
    UserCompetencyWorkflow.prototype.stopReview = function(data) {
        this._stopReview(data);
    };

    /**
     * Stop a review handler.
     *
     * @param  {Event} e The event.
     * @return {Void}
     * @method _stopReviewHandler
     */
    UserCompetencyWorkflow.prototype._stopReviewHandler = function(e) {
        e.preventDefault();
        var data = this._findUserCompetencyData($(e.target));
        this.stopReview(data);
    };

    /**
     * Enhance a menu bar.
     *
     * @param  {String} selector Menubar selector.
     */
    UserCompetencyWorkflow.prototype.enhanceMenubar = function(selector) {
        Menubar.enhance(selector, {
            '[data-action="request-review"]': this._requestReviewHandler.bind(this),
            '[data-action="cancel-review-request"]': this._cancelReviewRequestHandler.bind(this),
        });
    };

    /**
     * Find the user competency data from a node.
     *
     * @param  {Node} node The node to search from.
     * @return {Object} User competency data.
     */
    UserCompetencyWorkflow.prototype._findUserCompetencyData = function(node) {
        var parent = node.parents(this._nodeSelector),
            data;

        if (parent.length != 1) {
            throw new Error('The evidence node was not located.');
        }

        data = parent.data();
        if (typeof data === 'undefined' || typeof data.userid === 'undefined' || typeof data.competencyid === 'undefined') {
            throw new Error('User competency data could not be found.');
        }

        return data;
    };

    /**
     * Enhance a menu bar.
     *
     * @param  {String} selector Menubar selector.
     */
    UserCompetencyWorkflow.prototype.enhanceMenubar = function(selector) {
        Menubar.enhance(selector, {
            '[data-action="request-review"]': this._requestReviewHandler.bind(this),
            '[data-action="cancel-review-request"]': this._cancelReviewRequestHandler.bind(this),
            '[data-action="start-review"]': this._startReviewHandler.bind(this),
            '[data-action="stop-review"]': this._stopReviewHandler.bind(this),
        });
    };

    /**
     * Register the events in the region.
     *
     * @param {String} selector The base selector to search nodes in and attach events.
     */
    UserCompetencyWorkflow.prototype.registerEvents = function(selector) {
        var wrapper = $(selector);

        wrapper.find('[data-action="request-review"]').click(this._requestReviewHandler.bind(this));
        wrapper.find('[data-action="cancel-review-request"]').click(this._cancelReviewRequestHandler.bind(this));
        wrapper.find('[data-action="start-review"]').click(this._startReviewHandler.bind(this));
        wrapper.find('[data-action="stop-review"]').click(this._stopReviewHandler.bind(this));
    };

    return /** @alias module:tool_lp/user_competency_actions */ UserCompetencyWorkflow;
});
