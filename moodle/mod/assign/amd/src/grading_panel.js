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
 * Javascript controller for the "Grading" panel at the right of the page.
 *
 * @module     mod_assign/grading_panel
 * @package    mod_assign
 * @class      GradingPanel
 * @copyright  2016 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery', 'core/yui', 'core/notification', 'core/templates', 'core/fragment',
        'core/ajax', 'core/str', 'mod_assign/grading_form_change_checker',
        'mod_assign/grading_events'],
       function($, Y, notification, templates, fragment, ajax, str, checker, GradingEvents) {

    /**
     * GradingPanel class.
     *
     * @class GradingPanel
     * @param {String} selector The selector for the page region containing the user navigation.
     */
    var GradingPanel = function(selector) {
        this._regionSelector = selector;
        this._region = $(selector);
        this._userCache = [];

        this.registerEventListeners();
    };

    /** @type {String} Selector for the page region containing the user navigation. */
    GradingPanel.prototype._regionSelector = null;

    /** @type {Integer} Remember the last user id to prevent unnessecary reloads. */
    GradingPanel.prototype._lastUserId = 0;

    /** @type {Integer} Remember the last attempt number to prevent unnessecary reloads. */
    GradingPanel.prototype._lastAttemptNumber = -1;

    /** @type {JQuery} JQuery node for the page region containing the user navigation. */
    GradingPanel.prototype._region = null;

    /**
     * Fade the dom node out, update it, and fade it back.
     *
     * @private
     * @method _niceReplaceNodeContents
     * @param {JQuery} node
     * @param {String} html
     * @param {String} js
     * @return {Deferred} promise resolved when the animations are complete.
     */
    GradingPanel.prototype._niceReplaceNodeContents = function(node, html, js) {
        var promise = $.Deferred();

        node.fadeOut("fast", function() {
            templates.replaceNodeContents(node, html, js);
            node.fadeIn("fast", function() {
                promise.resolve();
            });
        });

        return promise.promise();
    };

    /**
     * Make sure all form fields have the latest saved state.
     * @private
     * @method _saveFormState
     */
    GradingPanel.prototype._saveFormState = function() {
        // Grrrrr! TinyMCE you know what you did.
        if (typeof window.tinyMCE !== 'undefined') {
            window.tinyMCE.triggerSave();
        }

        // Copy data from notify students checkbox which was moved out of the form.
        var checked = $('[data-region="grading-actions-form"] [name="sendstudentnotifications"]').prop("checked");
        $('.gradeform [name="sendstudentnotifications"]').val(checked);
    };

    /**
     * Make form submit via ajax.
     *
     * @private
     * @param {Object} event
     * @param {Integer} nextUserId
     * @method _submitForm
     */
    GradingPanel.prototype._submitForm = function(event, nextUserId) {
        // The form was submitted - send it via ajax instead.
        var form = $(this._region.find('form.gradeform'));

        $('[data-region="overlay"]').show();

        // We call this, so other modules can update the form with the latest state.
        form.trigger('save-form-state');

        // Now we get all the current values from the form.
        var data = form.serialize();
        var assignmentid = this._region.attr('data-assignmentid');

        // Now we can continue...
        ajax.call([{
            methodname: 'mod_assign_submit_grading_form',
            args: {assignmentid: assignmentid, userid: this._lastUserId, jsonformdata: JSON.stringify(data)},
            done: this._handleFormSubmissionResponse.bind(this, data, nextUserId),
            fail: notification.exception
        }]);
    };

    /**
     * Handle form submission response.
     *
     * @private
     * @method _handleFormSubmissionResponse
     * @param {Array} formdata - submitted values
     * @param {Integer} nextUserId - optional. The id of the user to load after the form is saved.
     * @param {Array} response List of errors.
     */
    GradingPanel.prototype._handleFormSubmissionResponse = function(formdata, nextUserId, response) {
        if (typeof nextUserId === "undefined") {
            nextUserId = this._lastUserId;
        }
        if (response.length) {
            // There was an error saving the grade. Re-render the form using the submitted data so we can show
            // validation errors.
            $(document).trigger('reset', [this._lastUserId, formdata]);
        } else {
            str.get_strings([
                {key: 'changessaved', component: 'core'},
                {key: 'gradechangessaveddetail', component: 'mod_assign'},
            ]).done(function(strs) {
                notification.alert(strs[0], strs[1]);
            }).fail(notification.exception);
            Y.use('moodle-core-formchangechecker', function() {
                M.core_formchangechecker.reset_form_dirty_state();
            });
            if (nextUserId == this._lastUserId) {
                $(document).trigger('reset', nextUserId);
            } else {
                $(document).trigger('user-changed', nextUserId);
            }
        }
        $('[data-region="overlay"]').hide();
    };

    /**
     * Refresh form with default values.
     *
     * @private
     * @method _resetForm
     * @param {Event} e
     * @param {Integer} userid
     * @param {Array} formdata
     */
    GradingPanel.prototype._resetForm = function(e, userid, formdata) {
        // The form was cancelled - refresh with default values.
        var event = $.Event("custom");
        if (typeof userid == "undefined") {
            userid = this._lastUserId;
        }
        this._lastUserId = 0;
        this._refreshGradingPanel(event, userid, formdata);
    };

    /**
     * Open a picker to choose an older attempt.
     *
     * @private
     * @param {Object} e
     * @method _chooseAttempt
     */
    GradingPanel.prototype._chooseAttempt = function(e) {
        // Show a dialog.

        // The form is in the element pointed to by data-submissions.
        var link = $(e.target);
        var submissionsId = link.data('submissions');
        var submissionsform = $(document.getElementById(submissionsId));
        var formcopy = submissionsform.clone();
        var formhtml = formcopy.wrap($('<form/>')).html();

        str.get_strings([
            {key: 'viewadifferentattempt', component: 'mod_assign'},
            {key: 'view', component: 'core'},
            {key: 'cancel', component: 'core'},
        ]).done(function(strs) {
            notification.confirm(strs[0], formhtml, strs[1], strs[2], function() {
                var attemptnumber = $("input:radio[name='select-attemptnumber']:checked").val();

                this._refreshGradingPanel(null, this._lastUserId, '', attemptnumber);
            }.bind(this));
        }.bind(this)).fail(notification.exception);
    };

    /**
     * Add popout buttons
     *
     * @private
     * @method _addPopoutButtons
     * @param {JQuery} selector The region selector to add popout buttons to.
     */
    GradingPanel.prototype._addPopoutButtons = function(selector) {
        var region = $(selector);

        templates.render('mod_assign/popout_button', {}).done(function(html) {
            var parents = region.find('[data-fieldtype="filemanager"],[data-fieldtype="editor"],[data-fieldtype="grading"]')
                    .closest('.fitem');
            parents.addClass('has-popout').find('label').parent().append(html);

            region.on('click', '[data-region="popout-button"]', this._togglePopout.bind(this));
        }.bind(this)).fail(notification.exception);
    };

    /**
     * Make a div "popout" or "popback".
     *
     * @private
     * @method _togglePopout
     * @param {Event} event
     */
    GradingPanel.prototype._togglePopout = function(event) {
        event.preventDefault();
        var container = $(event.target).closest('.fitem');
        if (container.hasClass('popout')) {
            $('.popout').removeClass('popout');
        } else {
            $('.popout').removeClass('popout');
            container.addClass('popout');
            container.addClass('moodle-has-zindex');
        }
    };

    /**
     * Get the user context - re-render the template in the page.
     *
     * @private
     * @method _refreshGradingPanel
     * @param {Event} event
     * @param {Number} userid
     * @param {String} submissiondata serialised submission data.
     * @param {Integer} attemptnumber
     */
    GradingPanel.prototype._refreshGradingPanel = function(event, userid, submissiondata, attemptnumber) {
        var contextid = this._region.attr('data-contextid');
        if (typeof submissiondata === 'undefined') {
            submissiondata = '';
        }
        if (typeof attemptnumber === 'undefined') {
            attemptnumber = -1;
        }
        // Skip reloading if it is the same user.
        if (this._lastUserId == userid && this._lastAttemptNumber == attemptnumber && submissiondata === '') {
            return;
        }
        this._lastUserId = userid;
        this._lastAttemptNumber = attemptnumber;
        $(document).trigger('start-loading-user');
        // Tell behat to back off too.
        window.M.util.js_pending('mod-assign-loading-user');
        // First insert the loading template.
        templates.render('mod_assign/loading', {}).done(function(html, js) {
            // Update the page.
            this._niceReplaceNodeContents(this._region, html, js).done(function() {
                if (userid > 0) {
                    this._region.show();
                    // Reload the grading form "fragment" for this user.
                    var params = {userid: userid, attemptnumber: attemptnumber, jsonformdata: JSON.stringify(submissiondata)};
                    fragment.loadFragment('mod_assign', 'gradingpanel', contextid, params).done(function(html, js) {
                        this._niceReplaceNodeContents(this._region, html, js)
                        .done(function() {
                            checker.saveFormState('[data-region="grade-panel"] .gradeform');
                            $(document).on('editor-content-restored', function() {
                                // If the editor has some content that has been restored
                                // then save the form state again for comparison.
                                checker.saveFormState('[data-region="grade-panel"] .gradeform');
                            });
                            $('[data-region="attempt-chooser"]').on('click', this._chooseAttempt.bind(this));
                            this._addPopoutButtons('[data-region="grade-panel"] .gradeform');
                            $(document).trigger('finish-loading-user');
                            // Tell behat we are friends again.
                            window.M.util.js_complete('mod-assign-loading-user');
                        }.bind(this))
                        .fail(notification.exception);
                    }.bind(this)).fail(notification.exception);
                    $('[data-region="review-panel"]').show();
                } else {
                    this._region.hide();
                    $('[data-region="review-panel"]').hide();
                    $(document).trigger('finish-loading-user');
                    // Tell behat we are friends again.
                    window.M.util.js_complete('mod-assign-loading-user');
                }
            }.bind(this));
        }.bind(this)).fail(notification.exception);
    };

    /**
     * Get the grade panel element.
     *
     * @method getPanelElement
     * @return {jQuery}
     */
    GradingPanel.prototype.getPanelElement = function() {
        return $('[data-region="grade-panel"]');
    };

    /**
     * Hide the grade panel.
     *
     * @method collapsePanel
     */
    GradingPanel.prototype.collapsePanel = function() {
        this.getPanelElement().addClass('collapsed');
    };

    /**
     * Show the grade panel.
     *
     * @method expandPanel
     */
    GradingPanel.prototype.expandPanel = function() {
        this.getPanelElement().removeClass('collapsed');
    };

    /**
     * Register event listeners for the grade panel.
     *
     * @method registerEventListeners
     */
    GradingPanel.prototype.registerEventListeners = function() {
        var docElement = $(document);
        var region = $(this._region);
        // Add an event listener to prevent form submission when pressing enter key.
        region.on('submit', 'form', function(e) {
            e.preventDefault();
        });

        docElement.on('user-changed', this._refreshGradingPanel.bind(this));
        docElement.on('save-changes', this._submitForm.bind(this));
        docElement.on('reset', this._resetForm.bind(this));

        docElement.on('save-form-state', this._saveFormState.bind(this));

        docElement.on(GradingEvents.COLLAPSE_GRADE_PANEL, function() {
            this.collapsePanel();
        }.bind(this));

        // We should expand if the review panel is collapsed.
        docElement.on(GradingEvents.COLLAPSE_REVIEW_PANEL, function() {
            this.expandPanel();
        }.bind(this));

        docElement.on(GradingEvents.EXPAND_GRADE_PANEL, function() {
            this.expandPanel();
        }.bind(this));
    };

    return GradingPanel;
});
