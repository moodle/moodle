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
 * User evidence actions.
 *
 * @module     tool_lp/user_evidence_actions
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
        'tool_lp/competencypicker_user_plans'],
        function($, templates, ajax, notification, str, Menubar, PickerUserPlans) {

    /**
     * UserEvidenceActions class.
     *
     * Note that presently this cannot be instantiated more than once per page.
     *
     * @param {String} type The type of page we're in.
     */
    var UserEvidenceActions = function(type) {
        this._type = type;

        if (type === 'evidence') {
            // This is the page to view one evidence.
            this._region = '[data-region="user-evidence-page"]';
            this._evidenceNode = '[data-region="user-evidence-page"]';
            this._template = 'tool_lp/user_evidence_page';
            this._contextMethod = 'tool_lp_data_for_user_evidence_page';

        } else if (type === 'list') {
            // This is the page to view a list of evidence.
            this._region = '[data-region="user-evidence-list"]';
            this._evidenceNode = '[data-region="user-evidence-node"]';
            this._template = 'tool_lp/user_evidence_list_page';
            this._contextMethod = 'tool_lp_data_for_user_evidence_list_page';

        } else {
            throw new TypeError('Unexpected type.');
        }
    };

    /** @type {String} Ajax method to fetch the page data from. */
    UserEvidenceActions.prototype._contextMethod = null;
    /** @type {String} Selector to find the node describing the evidence. */
    UserEvidenceActions.prototype._evidenceNode = null;
    /** @type {String} Selector mapping to the region to update. Usually similar to wrapper. */
    UserEvidenceActions.prototype._region = null;
    /** @type {String} Name of the template used to render the region. */
    UserEvidenceActions.prototype._template = null;
    /** @type {String} Type of page/region we're in. */
    UserEvidenceActions.prototype._type = null;

    /**
     * Resolve the arguments to refresh the region.
     *
     * @param  {Object} evidenceData Evidence data from evidence node.
     * @return {Object} List of arguments.
     */
    UserEvidenceActions.prototype._getContextArgs = function(evidenceData) {
        var self = this,
            args = {};

        if (self._type === 'evidence') {
            args = {
                id: evidenceData.id
            };

        } else if (self._type === 'list') {
            args = {
                userid: evidenceData.userid
            };
        }

        return args;
    };

    /**
     * Callback to render the region template.
     *
     * @param {Object} context The context for the template.
     */
    UserEvidenceActions.prototype._renderView = function(context) {
        var self = this;
        templates.render(self._template, context)
            .done(function(newhtml, newjs) {
                templates.replaceNode($(self._region), newhtml, newjs);
            }.bind(self))
            .fail(notification.exception);
    };

    /**
     * Call multiple ajax methods, and refresh.
     *
     * @param  {Array}  calls    List of Ajax calls.
     * @param  {Object} evidenceData Evidence data from evidence node.
     * @return {Promise}
     */
    UserEvidenceActions.prototype._callAndRefresh = function(calls, evidenceData) {
        var self = this;

        calls.push({
            methodname: self._contextMethod,
            args: self._getContextArgs(evidenceData)
        });

        // Apply all the promises, and refresh when the last one is resolved.
        return $.when.apply($.when, ajax.call(calls))
            .then(function() {
                self._renderView.call(self, arguments[arguments.length - 1]);
            })
            .fail(notification.exception);
    };

    /**
     * Delete a plan and reload the region.
     *
     * @param  {Object} evidenceData Evidence data from evidence node.
     */
    UserEvidenceActions.prototype._doDelete = function(evidenceData) {
        var self = this,
            calls = [{
                methodname: 'core_competency_delete_user_evidence',
                args: { id: evidenceData.id }
            }];
        self._callAndRefresh(calls, evidenceData);
    };

    /**
     * Delete a plan.
     *
     * @param  {Object} evidenceData Evidence data from evidence node.
     */
    UserEvidenceActions.prototype.deleteEvidence = function(evidenceData) {
        var self = this,
            requests;

        requests = ajax.call([{
            methodname: 'core_competency_read_user_evidence',
            args: { id: evidenceData.id }
        }]);

        requests[0].done(function(evidence) {
            str.get_strings([
                { key: 'confirm', component: 'moodle' },
                { key: 'deleteuserevidence', component: 'tool_lp', param: evidence.name },
                { key: 'delete', component: 'moodle' },
                { key: 'cancel', component: 'moodle' }
            ]).done(function (strings) {
                notification.confirm(
                    strings[0], // Confirm.
                    strings[1], // Delete evidence X?
                    strings[2], // Delete.
                    strings[3], // Cancel.
                    function() {
                        self._doDelete(evidenceData);
                    }.bind(self)
                );
            }).fail(notification.exception);
        }).fail(notification.exception);

    };

    /**
     * Delete evidence handler.
     *
     * @param  {Event} e The event.
     */
    UserEvidenceActions.prototype._deleteEvidenceHandler = function(e) {
        e.preventDefault();
        var data = this._findEvidenceData($(e.target));
        this.deleteEvidence(data);
    };

    /**
     * Link a competency and reload.
     *
     * @param {Object} evidenceData Evidence data from evidence node.
     * @param {Number} competencyIds The competency IDs.
     * @param {Boolean} requestReview Send competencies to review.
     */
    UserEvidenceActions.prototype._doCreateUserEvidenceCompetency = function(evidenceData, competencyIds) {
        var self = this,
            calls = [];

        $.each(competencyIds, function(index, competencyId) {
            calls.push({
                methodname: 'core_competency_create_user_evidence_competency',
                args: {
                    userevidenceid: evidenceData.id,
                    competencyid: competencyId,
                }
            });
        });

        self._callAndRefresh(calls, evidenceData);
    };

    /**
     * Create a user evidence competency.
     *
     * @param  {Object} evidenceData Evidence data from evidence node.
     */
    UserEvidenceActions.prototype.createUserEvidenceCompetency = function(evidenceData) {
        var self = this,
            picker = new PickerUserPlans(evidenceData.userid);

        picker.on('save', function(e, data) {
            var competencyIds = data.competencyIds;
            self._doCreateUserEvidenceCompetency(evidenceData, competencyIds, data.requestReview);
        }.bind(self));

        picker.display();
    };

    /**
     * Create user evidence competency handler.
     *
     * @param  {Event} e The event.
     */
    UserEvidenceActions.prototype._createUserEvidenceCompetencyHandler = function(e) {
        e.preventDefault();
        var data = this._findEvidenceData($(e.target));
        this.createUserEvidenceCompetency(data);
    };

    /**
     * Remove a linked competency and reload.
     *
     * @param {Object} evidenceData Evidence data from evidence node.
     * @param {Number} competencyId The competency ID.
     */
    UserEvidenceActions.prototype._doDeleteUserEvidenceCompetency = function(evidenceData, competencyId) {
        var self = this,
            calls = [];

        calls.push({
            methodname: 'core_competency_delete_user_evidence_competency',
            args: {
                userevidenceid: evidenceData.id,
                competencyid: competencyId,
            }
        });

        self._callAndRefresh(calls, evidenceData);
    };

    /**
     * Delete a user evidence competency.
     *
     * @param  {Object} evidenceData Evidence data from evidence node.
     * @param  {Number} competencyId The competency ID.
     */
    UserEvidenceActions.prototype.deleteUserEvidenceCompetency = function(evidenceData, competencyId) {
        this._doDeleteUserEvidenceCompetency(evidenceData, competencyId);
    };

    /**
     * Delete user evidence competency handler.
     *
     * @param  {Event} e The event.
     */
    UserEvidenceActions.prototype._deleteUserEvidenceCompetencyHandler = function(e) {
        var data = this._findEvidenceData($(e.currentTarget)),
            competencyId = $(e.currentTarget).data('id');
        e.preventDefault();
        this.deleteUserEvidenceCompetency(data, competencyId);
    };

    /**
     * Send request review for user evidence competencies and reload the region.
     *
     * @param  {Object} evidenceData Evidence data from evidence node.
     */
    UserEvidenceActions.prototype._doReviewUserEvidenceCompetencies = function(evidenceData) {
        var self = this,
            calls = [{
                methodname: 'core_competency_request_review_of_user_evidence_linked_competencies',
                args: { id: evidenceData.id }
            }];
        self._callAndRefresh(calls, evidenceData);
    };

    /**
     * Send request review for user evidence competencies.
     *
     * @param  {Object} evidenceData Evidence data from evidence node.
     */
    UserEvidenceActions.prototype.reviewUserEvidenceCompetencies = function(evidenceData) {
        var self = this,
            requests;

        requests = ajax.call([{
            methodname: 'core_competency_read_user_evidence',
            args: { id: evidenceData.id }
        }]);

        requests[0].done(function(evidence) {
            str.get_strings([
                { key: 'confirm', component: 'moodle' },
                { key: 'sendallcompetenciestoreview', component: 'tool_lp', param: evidence.name },
                { key: 'confirm', component: 'moodle' },
                { key: 'cancel', component: 'moodle' }
            ]).done(function (strings) {
                notification.confirm(
                    strings[0], // Confirm.
                    strings[1], // Send all competencies in review for X?
                    strings[2], // Confirm.
                    strings[3], // Cancel.
                    function() {
                        self._doReviewUserEvidenceCompetencies(evidenceData);
                    }.bind(self)
                );
            }).fail(notification.exception);
        }).fail(notification.exception);

    };

    /**
     * Send request review for user evidence competencies handler.
     *
     * @param  {Event} e The event.
     */
    UserEvidenceActions.prototype._reviewUserEvidenceCompetenciesHandler = function(e) {
        e.preventDefault();
        var data = this._findEvidenceData($(e.target));
        this.reviewUserEvidenceCompetencies(data);
    };

    /**
     * Find the evidence data from the evidence node.
     *
     * @param  {Node} node The node to search from.
     * @return {Object} Evidence data.
     */
    UserEvidenceActions.prototype._findEvidenceData = function(node) {
        var parent = node.parentsUntil($(this._region).parent(), this._evidenceNode),
            data;

        if (parent.length != 1) {
            throw new Error('The evidence node was not located.');
        }

        data = parent.data();
        if (typeof data === 'undefined' || typeof data.id === 'undefined') {
            throw new Error('Evidence data could not be found.');
        }

        return data;
    };

    /**
     * Enhance a menu bar.
     *
     * @param  {String} selector Menubar selector.
     */
    UserEvidenceActions.prototype.enhanceMenubar = function(selector) {
        var self = this;
        Menubar.enhance(selector, {
            '[data-action="user-evidence-delete"]': self._deleteEvidenceHandler.bind(self),
            '[data-action="link-competency"]': self._createUserEvidenceCompetencyHandler.bind(self),
            '[data-action="send-competencies-review"]': self._reviewUserEvidenceCompetenciesHandler.bind(self),
        });
    };

    /**
     * Register the events in the region.
     *
     * At this stage this cannot be used with enhanceMenubar or multiple handlers
     * will be added to the same node.
     */
    UserEvidenceActions.prototype.registerEvents = function() {
        var wrapper = $(this._region),
            self = this;

        wrapper.find('[data-action="user-evidence-delete"]').click(self._deleteEvidenceHandler.bind(self));
        wrapper.find('[data-action="link-competency"]').click(self._createUserEvidenceCompetencyHandler.bind(self));
        wrapper.find('[data-action="delete-competency-link"]').click(self._deleteUserEvidenceCompetencyHandler.bind(self));
        wrapper.find('[data-action="send-competencies-review"]').click(self._reviewUserEvidenceCompetenciesHandler.bind(self));
    };

    return /** @alias module:tool_lp/user_evidence_actions */ UserEvidenceActions;
});
