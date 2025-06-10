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
 * Learning plan report navigation.
 *
 * @module     report_lpmonitoring/learningplan
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 */

define(['jquery',
    'core/templates',
    'core/ajax',
    'core/notification',
    'core/str',
    'core/chartjs',
    'core/form-autocomplete',
    'core/modal_factory',
    'core/modal_events',
    'report_lpmonitoring/user_competency_popup',
    'tool_lp/grade_user_competency_inline',
    'report_lpmonitoring/fieldsettoggler',
    'report_lpmonitoring/colorcontrast',
    'report_lpmonitoring/paginated_datatable',
    'report_lpmonitoring/resetgrade_dialogue'],
    function($, templates, ajax, notification, str, Chart, autocomplete, ModalFactory, ModalEvents,
        Popup, InlineGrader, fieldsettoggler,
        colorcontrast, DataTable, ResetGradeDialogue) {

        /**
         * Learning plan report.
         * @param {Boolean} userview True if the report is for user view (student).
         * @param {Boolean} cmcompgradingenabled True if grading in course module competency is enabled.
         */
        var LearningplanReport = function(userview, cmcompgradingenabled) {
            this.userView = userview || false;
            this.cmcompgradingEnabled = cmcompgradingenabled || false;

            // Init the form filter.
            this.initPage();

            // Init the color contrast object.
            this.colorContrast = colorcontrast.init();

            // Init User competency page popup.
            var learningplan = this;
            var popup = new Popup('[data-region=list-competencies-section]', '[data-user-competency=true]');
            // Override the after show refresh method of the user competency popup.
            popup._refresh = function() {
                var self = this;
                learningplan.reloadCompetencyDetail(self._competencyId, self._userId, self._planId);
                self.close();
            };

            $(this.templateSelector).on('change', this.templateChangeHandler.bind(this)).change();
            $(this.learningplanSelector).on('change', this.learningplanChangeHandler.bind(this)).change();
            $(this.studentSelector).on('change', this.studentChangeHandler.bind(this)).change();
            $(this.studentPlansSelector).on('change', this.studentPlansChangeHandler.bind(this)).change();
            $(this.tagSelector).on('change', this.tagChangeHandler.bind(this)).change();
            $(this.learningplanTagSelector).on('change', this.learningplanTagChangeHandler.bind(this)).change();
            $(this.learningplanTagCommentsSelector).on('change', this.learningplanTagChangeHandler.bind(this)).change();

            $('.competencyreport').on('change',
                '.scalefiltercontainer input[name="optionscalefilter"]',
                this.changeScaleApplyHandler.bind(this)).change();
            $('.competencyreport').on('change',
                '.scalesortordercontainer input[name="optionscalesortorder"]',
                this.changeScaleSortorderHandler.bind(this)).change();
            $('.competencyreport').on('change', '.scalefiltervalues', this.changeScaleHandler.bind(this)).change();
            $('.competencyreport input[name=optionfilter]').prop("disabled", false);
            $('.competencyreport input[name=optionscalesortorder]').prop("disabled", false);
            // Display rating in user plan.
            $(".competencyreport").on("change", ".displayratings input[type=checkbox]",
                this.changeDisplayRating.bind(this)).change();
            // Only plans with comments filter.
            $('.competencyreport').on('change', '#filter-comment', this.changeWithcommentsHandler.bind(this)).change();
            // Only students with at least two plans.
            $('.competencyreport').on('change', '#filter-plan', this.changeWithplansHandler.bind(this)).change();
            // When the tags are modified we reload the tags filter.
            $(".competencyreport").on('DOMSubtreeModified', ".tags-stats", this.reloadTagsIfNeeded.bind(this));
            // When the comments is checked we modify the tags filter.
            // Only plans with comments filter and tags.
            $('.competencyreport').on('change', '#filter-comments', this.reloadTagsIfNeeded.bind(this));

        };

        /** @var {Number} The template ID. */
        LearningplanReport.prototype.templateId = null;
        /** @var {Boolean} If report is for user view */
        LearningplanReport.prototype.userView = false;
        /** @var {Number} The learning plan ID from template. */
        LearningplanReport.prototype.learningplanId = null;
        /** @var {Number} The learning plan ID from student. */
        LearningplanReport.prototype.studentLearningplanId = null;
        /** @var {Number} The learning plan ID from tag. */
        LearningplanReport.prototype.tagLearningplanId = null;
        /** @var {String} The selected tag ID. */
        LearningplanReport.prototype.tagId = null;
        /** @var {Number} The user ID. */
        LearningplanReport.prototype.userId = null;
        /** @var {Boolean} If template option is selected. */
        LearningplanReport.prototype.templateSelected = null;
        /** @var {Boolean} If student option is selected. */
        LearningplanReport.prototype.studentSelected = null;
        /** @var {Array} Competencies informations. */
        LearningplanReport.prototype.competencies = {};
        /** @var {String} Scales values filter. */
        LearningplanReport.prototype.scalesvaluesSelected = null;
        /** @var {ColorContrast} ColorContrast object instance. */
        LearningplanReport.prototype.colorcontrast = null;
        /** @var {String} Apply scale filters on grade in plan, course or course module. */
        LearningplanReport.prototype.scalefilterin = '';
        /** @var {String} Apply scale sortorder. */
        LearningplanReport.prototype.scalesortorder = 'ASC';
        /** @var {String} Apply filter for only plans with comments. */
        LearningplanReport.prototype.withcomments = false;
        /** @var {String} Apply filter for only students with at least two plans. */
        LearningplanReport.prototype.withplans = false;
        /** @var {Boolean} Is course module competency grading enabled. */
        LearningplanReport.prototype.cmcompgradingEnabled = false;

        /** @var {String} The template select box selector. */
        LearningplanReport.prototype.templateSelector = "#templateSelectorReport";
        /** @var {String} The learning plan select box selector. */
        LearningplanReport.prototype.learningplanSelector = '#learningplanSelectorReport';
        /** @var {String} The student selector. */
        LearningplanReport.prototype.studentSelector = '#studentSelectorReport';
        /** @var {String} The student plans selector. */
        LearningplanReport.prototype.studentPlansSelector = '#studentPlansSelectorReport';
        /** @var {String} The tag select box selector. */
        LearningplanReport.prototype.tagSelector = "#tagSelectorReport";
        /** @var {String} The learning plan with this tag select box selector. */
        LearningplanReport.prototype.learningplanTagSelector = '#learningplanTagSelectorReport';
        /** @var {String} */
        LearningplanReport.prototype.learningplanTagCommentsSelector = '#filter-comments';

        /** @var {Boolean} Indicate that the tags are currently loading, to prevent loading them twice at the same time. */
        LearningplanReport.prototype.tagsAreLoading = false;

        /** @var {String} Active tab course or course module. */
        LearningplanReport.prototype.compDetailActiveTab = null;

        /**
         * Triggered when a template is selected.
         *
         * @name   templateChangeHandler
         * @param  {Event} e
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.templateChangeHandler = function(e) {
            var self = this;
            self.templateId = $(e.target).val();
            $(self.learningplanSelector).data('templateid', self.templateId);
            $(self.learningplanSelector).data('scalefilter', '');
            $(self.learningplanSelector).data('scalesortorder', '');
            self.resetUserUsingLPTemplateSelection();
            self.learningplanId = null;
            if (self.templateId !== '') {
                $('.competencyreport .moreless-actions').removeClass('hidden');
                if ($('.competencyreport .show-toggler').hasClass('hidden')) {
                    $('.competencyreport .advanced').show();
                }
                self.loadScalesFromTemplate(self.templateId);
                self.disableUserTemplateSelector(false);
            } else {
                $('.competencyreport .moreless-actions').addClass('hidden');
                $('.competencyreport .advanced').hide();
                $('.competencyreport #scale').empty();
                self.disableUserTemplateSelector(true);
            }
            self.checkDataFormReady();
        };

        /**
         * Load the tags list from the webservice.
         *
         * @name   loadTags
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.loadTags = function() {
            var self = this;
            if (self.tagsAreLoading === false) {
                self.tagsAreLoading = true;
                $(self.tagSelector + ' option').remove();

                var promise = ajax.call([{
                    methodname: 'report_lpmonitoring_search_tags_for_accessible_plans',
                    args: {

                    }
                }]);

                promise[0].then(function(results) {
                    str.get_string('selecttag', 'report_lpmonitoring').done(
                        function(selecttag) {
                            $(self.tagSelector).append($('<option>').text(selecttag).val(''));

                            $.each(results, function(index, tag) {
                                $(self.tagSelector).append($('<option>').text(tag.tag).val(tag.id));
                            });

                            // Select the option that was selected before.
                            var optionExists = ( $(self.tagSelector + " option[value=" + self.tagId + "]").length > 0 );
                            if (optionExists === true) {
                                $(self.tagSelector).val(self.tagId);
                            } else {
                                self.tagId = null;
                                self.tagLearningplanId = null;
                            }

                            // Simulate a change to reload the learning plans associated to the tag.
                            $(self.tagSelector).change();
                            self.tagsAreLoading = false;
                        }
                    );
                }).fail(
                    function(exp) {
                        notification.exception(exp);
                        self.tagsAreLoading = false;
                    }
                );
            }
        };

        /**
         * Triggered when the tags are modified in a user plan. Reloads the tags filter, if this filter is currently selected.
         * @name   reloadTagsIfNeeded
         * @param {Event} event
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.reloadTagsIfNeeded = function(event) {
            event.preventDefault();
            if ($('.competencyreport #tag').is(':checked')) {
                this.loadTags();
            }
        };

        /**
         * Triggered when a tag is selected.
         *
         * @name   tagChangeHandler
         * @param  {Event} e
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.tagChangeHandler = function(e) {
            var self = this;
            self.tagId = $(e.target).val();
            self.withcomments = $("#filter-comments").is(':checked');

            if (self.tagId == '') {
                self.tagId = null;
            }
            var promise = ajax.call([{
                methodname: 'report_lpmonitoring_search_plans_with_tag',
                args: {
                    tagid: self.tagId,
                    withcomments: self.withcomments
                }
            }]);

            promise[0].then(function(results) {
                var label = '';
                // Render the options of the select for learning plans.
                var oldTagLearningplanId = self.tagLearningplanId;
                $(self.learningplanTagSelector + ' option').remove();

                if (results.length > 0) {
                    str.get_string('selectlearningplan', 'report_lpmonitoring').done(
                        function(selectlearningplan) {
                            $(self.learningplanTagSelector).append($('<option>').text(selectlearningplan).val(''));

                            $.each(results, function(index, plan) {
                                label = plan.fullname + ' - ' + plan.planname;
                                $(self.learningplanTagSelector).append($('<option>').text(label).val(plan.planid));
                            });
                            $(self.learningplanTagSelector).prop("disabled", false);

                            // Select the option that was selected before.
                            var selectorOption = self.learningplanTagSelector + " option[value=" + oldTagLearningplanId + "]";
                            var optionExists = ( $(selectorOption).length > 0 );
                            if (optionExists === true) {
                                $(self.learningplanTagSelector).val(oldTagLearningplanId);
                                self.tagLearningplanId = oldTagLearningplanId;
                            } else {
                                self.tagLearningplanId = null;
                            }
                        }
                    );
                } else {
                    $(self.learningplanTagSelector).prop("disabled", true);
                    str.get_string('nolearningplanavailable', 'report_lpmonitoring').done(
                        function(nolearningplanavailable) {
                            $(self.learningplanTagSelector).append($('<option>').text(nolearningplanavailable).val(''));
                        }
                    );
                }
                $(self.learningplanTagSelector).trigger('change');

            }).fail(
                function(exp) {
                    notification.exception(exp);
                }
            );
            self.checkDataFormReady();
        };

        /**
         * Triggered when a student's plan associated to a tag is selected.
         *
         * @name   studentPlansChangeHandler
         * @param  {Event} e
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.learningplanTagChangeHandler = function(e) {
            var self = this;
            self.tagLearningplanId = $(e.target).val();
            self.checkDataFormReady();
        };

        /**
         * Set display rating for plan.
         *
         * @name   changeDisplayRating
         * @param  {Event} e
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.changeDisplayRating = function(e) {
            var displayrating = 0;
            if ($(e.target).is( ":checked" )) {
                displayrating = 1;
            }
            var planid = $(e.target).data('displayrating-plan');

            var promise = ajax.call([{
                methodname: 'tool_lp_set_display_rating_for_plan',
                args: {
                    planid: planid,
                    visible: displayrating
                }
            }, {
                methodname: 'tool_lp_can_reset_display_rating_for_plan',
                args: {
                    planid: planid
                }
            }
            ]);

            promise[0].then(function() {
                promise[1].then(function(canresetdisplayrating) {
                    if (displayrating) {
                        $('.competencyreport .displayratings input[type=checkbox]').prop("checked", true);
                    } else {
                        $('.competencyreport .displayratings input[type=checkbox]').prop("checked", false);
                    }
                    if (canresetdisplayrating) {
                        $('.competencyreport .resetdisplayrating').show();
                    }
                }).fail(
                function(exp) {
                    notification.exception(exp);
                }
                );
            }).fail(
                function(exp) {
                    notification.exception(exp);
                }
            );
        };

        /**
         * Reset display rating for plan.
         *
         * @name   resetDisplayRating
         * @param  {Number} planid
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.resetDisplayRating = function(planid) {
            var promise = ajax.call([{
                methodname: 'tool_lp_reset_display_rating_for_plan',
                args: {
                    planid: planid
                }
            }, {
                methodname: 'tool_lp_has_to_display_rating',
                args: {
                    planid: planid
                }
            }
            ]);

            promise[0].then(function() {
                promise[1].then(function(displayrating) {
                    if (displayrating) {
                        $('.competencyreport .displayratings input[type=checkbox]').prop("checked", true);
                    } else {
                        $('.competencyreport .displayratings input[type=checkbox]').prop("checked", false);
                    }
                    $('.competencyreport .resetdisplayrating').hide();
                }).fail(
                function(exp) {
                    notification.exception(exp);
                }
                );
            }).fail(
                function(exp) {
                    notification.exception(exp);
                }
            );
        };

        /**
         * Reset the user using learning plan template selection.
         *
         * @name   resetUserUsingLPTemplateSelection
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.resetUserUsingLPTemplateSelection = function() {
            var self = this,
            autocomplete = $('.competencyreport .templatefilter .form-autocomplete-selection'),
            selection = autocomplete.find('span[aria-selected="true"]');
            self.learningplanId = null;
            if (selection.length) {
                selection.remove();
                $(self.learningplanSelector + ' option').remove();
                str.get_string('nouserselected', 'report_lpmonitoring').done(
                    function(nouserselected) {
                        autocomplete.append($('<span>').text(nouserselected));
                    }
                );
            }
        };

        /**
         * Method to enable or disable the user selector linked to the template filter
         * @name toggleUserSelector
         * @param {boolean} state true to disable false to enable
         * @return {void}
         */
        LearningplanReport.prototype.disableUserTemplateSelector = function(state = true) {
            var userAutocomplete = $('.competencyreport .templatefilter .for-autocomplete .fautocomplete .position-relative'),
            inputSelector = userAutocomplete.find(':text'),
            arrowSelector = userAutocomplete.find('.form-autocomplete-downarrow');

            if (inputSelector.length > 0) {
                inputSelector.prop("disabled", state);
            }
            if (arrowSelector.length > 0) {
                arrowSelector.toggleClass('disabled-option', state);
            }
        };

        /**
         * Load scales from template.
         *
         * @name   loadScalesFromTemplate
         * @param  {Number} templateid
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.loadScalesFromTemplate = function(templateid) {
            var self = this;
            var promise = ajax.call([{
                methodname: 'report_lpmonitoring_get_scales_from_template',
                args: {
                    templateid: parseInt(templateid)
                }
            }]);
            promise[0].then(function(results) {
                var context = {};
                context.scales = results;
                context.cmcompgradingenabled = self.cmcompgradingEnabled;
                templates.render('report_lpmonitoring/scale_filter', context).done(function(html, js) {
                    $('.competencyreport #scale').html(html);
                    templates.runTemplateJS(js);
                });
                if (results.length > 0) {
                    $('.competencyreport #scalefilterapply').show();
                    templates.render('report_lpmonitoring/scale_filter_apply', context).done(function(html, js) {
                        $('.competencyreport #scalefilter').html(html);
                        templates.runTemplateJS(js);
                    });
                    $('.competencyreport #scalesortorderlabel').show();
                    templates.render('report_lpmonitoring/scale_filter_sortorder', context).done(function(html, js) {
                        $('.competencyreport #scalesortorder').html(html);
                        templates.runTemplateJS(js);
                    });
                } else {
                    $('.competencyreport #scalefilterapply').hide();
                    $('.competencyreport #scalesortorderlabel').hide();
                    $('.competencyreport #scalefilter').html('');
                    $('.competencyreport #scalesortorder').html('');
                }
            }).fail(
                function(exp) {
                    notification.exception(exp);
                }
            );
        };

        /**
         * Build options for learning plan.
         *
         * @name   buildLearningplanOptions
         * @param  {Array} options
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.buildLearningplanOptions = function(options) {
            var self = this;
            // Reset options scales.
            $(self.scaleSelector + ' option').remove();
            $(self.scaleSelector).append($('<option>'));

            $.each(options, function(key, value) {
                $(self.scaleSelector).append($('<option>').text(value.name).val(value.id));
            });
        };

        /**
         * Triggered when a learning plan is selected.
         *
         * @name   learningplanChangeHandler
         * @param  {Event} e
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.learningplanChangeHandler = function(e) {
            var self = this;
            self.learningplanId = $(e.target).val();
            self.checkDataFormReady();
        };

        /**
         * Triggered when a student is selected.
         *
         * @name   studentChangeHandler
         * @param  {Event} e
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.studentChangeHandler = function(e) {
            var self = this;
            self.userId = $(e.target).val();
            if (self.userId) {
                var promise = ajax.call([{
                    methodname: 'core_competency_list_user_plans',
                    args: {
                        userid: self.userId
                    }
                }]);

                promise[0].then(function(results) {
                    // Reset options learning plans.
                    $(self.studentPlansSelector + ' option').remove();
                    if (results.length > 0) {
                        $(self.studentPlansSelector).prop("disabled", false);
                        $.each(results, function(key, value) {
                            $(self.studentPlansSelector).append($('<option>').text(value.name).val(value.id));
                        });
                    } else {
                        $(self.studentPlansSelector).prop("disabled", true);
                        str.get_string('nolearningplanavailable', 'report_lpmonitoring').done(
                            function(nolearningplanavailable) {
                                $(self.studentPlansSelector).append($('<option>').text(nolearningplanavailable).val(''));
                            }
                        );
                    }
                    $(self.studentPlansSelector).trigger('change');
                }, notification.exception);
            }
            self.checkDataFormReady();
        };

        /**
         * Triggered when a student plans is selected.
         *
         * @name   studentPlansChangeHandler
         * @param  {Event} e
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.studentPlansChangeHandler = function(e) {
            var self = this;
            self.studentLearningplanId = $(e.target).val();
            self.checkDataFormReady();
        };

        /**
         * Check if we can submit the form.
         *
         * @name   checkDataFormReady
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.checkDataFormReady = function() {
            var self = this,
                conditionByTemplate = false,
                conditionStudent = false,
                conditionByTag = false;

            if (self.userView === false) {
                conditionByTemplate = $('#template').is(':checked') && $(self.templateSelector).val() !== '';
                conditionStudent = $('#student').is(':checked') && $(self.studentSelector).val() !== null &&
                        $('option:selected', $(self.studentSelector)).attr('value') !== undefined &&
                        $("option:selected", $(self.studentPlansSelector)).attr('value') !== null &&
                        $("option:selected", $(self.studentPlansSelector)).attr('value') !== undefined &&
                        $("option:selected", $(self.studentPlansSelector)).attr('value') !== '';
                conditionByTag = $('#tag').is(':checked') && $(self.tagSelector).val() !== '';
            } else {
                conditionStudent = $(self.studentPlansSelector).val() !== null &&
                        $(self.studentPlansSelector).val() !== '';
            }

            if (conditionByTemplate || conditionStudent || conditionByTag) {
                $('#submitFilterReportButton').removeAttr('disabled');
            } else {
                $('#submitFilterReportButton').attr('disabled', 'disabled');
            }
        };

        /**
         * Load list of competencies of a specified plan.
         *
         * @name   loadListCompetencies
         * @param  {Object} plan
         * @param  {Object} elementloading element
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.loadListCompetencies = function(plan, elementloading) {
            var self = this;

            var promiselistCompetencies = ajax.call([{
                methodname: 'report_lpmonitoring_list_plan_competencies',
                args: {
                    id: plan.id
                }
            }]);
            promiselistCompetencies[0].then(function(results) {
                if (results.length > 0) {
                    // Get the "Detail" tab content.
                    var competencies = {competencies_list:results, plan:plan, hascompetencies: true};
                    templates.render('report_lpmonitoring/list_competencies', competencies).done(function(html, js) {
                        $("#listPlanCompetencies").html(html);
                        templates.runTemplateJS(js);
                        self.loadCompetencyDetail(results, plan, elementloading);
                        $("#nav-tabs").removeClass("hidden");
                    });
                } else {
                    elementloading.removeClass('loading');
                    templates.render('report_lpmonitoring/list_competencies', {}).done(function(html, js) {
                        $("#listPlanCompetencies").html(html);
                        templates.runTemplateJS(js);
                        $("#report-content").empty();
                        $("#summary-content").empty();
                        $("#nav-tabs").addClass("hidden");
                    });
                }
                self.loadSummaryTab(plan);
                self.loadReportTab(plan);
            }).fail(
                function(exp) {
                    elementloading.removeClass('loading');
                    notification.exception(exp);
                }
            );
        };

        /**
         * Load competency detail.
         *
         * @name  loadCompetencyDetail
         * @param {Object[]} competencies
         * @param {Object} plan
         * @param {Object} element loader
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.loadCompetencyDetail = function(competencies, plan, element) {
            var requests = [];
            var self = this;

            $.each(competencies, function(index, record) {
                // Locally store user competency information.
                self.competencies[record.competency.id] = {usercompetency:record.usercompetency};
                requests.push({
                    methodname: 'report_lpmonitoring_get_competency_detail',
                    args: {
                        competencyid: record.competency.id,
                        userid: plan.user.id,
                        planid: plan.id
                    }
                });
            });

            var promises = ajax.call(requests);
            $.each(promises, function(index, promise) {
                promise.then(function(context) {
                    // Locally store competency information.
                    self.competencies[context.competencyid].competencydetail = context;
                    context.plan = plan;
                    context.plan.userid = plan.user.id;
                    context.cmcompgradingenabled = self.cmcompgradingEnabled;
                    templates.render('report_lpmonitoring/competency_detail', context).done(function(html, js) {
                        var compid = context.competencyid;
                        var userid = plan.user.id;
                        var planid = plan.id;
                        var scaleid = context.scaleid;
                        $('#comp-' + compid + ' .x_content').html(html);

                        // Show comptency ratings details tabs.
                        if (self.compDetailActiveTab === 'incoursemodule') {
                            $('.detail-comp-tab a[href="#tab-incms-content-' + context.competencyid + '"]').tab('show');
                        } else {
                            $('.detail-comp-tab a[href="#tab-incourses-content-' + context.competencyid + '"]').tab('show');
                        }
                        if (context.cangrade) {
                            // Apply inline grader.
                            self.applyInlineGrader(compid, userid, planid, scaleid);
                        }

                        // Apply Donut Graph to the competency in courses.
                        if (context.hasrating !== false) {
                            self.ApplyDonutGraph(compid, context, false);
                        }

                        // Apply Donut Graph to the competency in courses modules.
                        if (context.hasratingincms !== false && self.cmcompgradingEnabled) {
                            self.ApplyDonutGraph(compid, context, true);
                        }

                        // If all template are loaded then hide the loader.
                        if (index === requests.length - 1) {
                            element.removeClass('loading');
                            // Show collapse links.
                            $('.competencyreport .competency-detail a.collapse-link').css('visibility', '');
                        }
                        templates.runTemplateJS(js);
                        self.colorContrast.apply('#comp-' + compid + ' .x_content .tile-stats .badge.cr-scalename');
                    });
                });
            });
        };

        /**
         * Load the report tab.
         *
         * @param {Object} plan
         * @function
         */
        LearningplanReport.prototype.loadReportTab = function(plan) {
            var learningplan = this;
            // Get the "Report" tab content.
            var promiseCompetenciesReport = ajax.call([{
                methodname: 'report_lpmonitoring_list_plan_competencies_report',
                args: {
                    id: plan.id
                }
            }]);
            promiseCompetenciesReport[0].then(function(results) {
                if (results['competencies_list'].length > 0) {
                    var competencies = {reportinfos:results, plan:plan, hascompetencies: true};

                    // Keep the filter and search values.
                    var checkedvalue = $('input[type=radio][name=reportfilter]:checked').val();
                    if (checkedvalue == 'course') {
                        competencies.filterchecked_course = true;
                    } else if (checkedvalue == 'module') {
                        competencies.filterchecked_module = true;
                    } else {
                        competencies.filterchecked_both = true;
                    }

                    competencies.tablesearchvalue = $('#table-search-competency').val();
                    competencies.tablesearchvaluecolumn = $('#table-search-columns').val();
                    competencies.scalefilterreport = $('#scale-filter-report option:selected').val();

                    // Render the "Report" data table template.
                    templates.render('report_lpmonitoring/datatable', competencies).done(function(html, js) {
                        $("#report-content").html(html);
                        templates.runTemplateJS(js);
                        var popup = new Popup('[data-region=report-competencies-section]', '[data-user-competency=true]');
                        // Override the after show refresh method of the user competency popup.
                        popup._refresh = function() {
                            var self = this;
                            learningplan.reloadCompetencyDetail(self._competencyId, self._userId, self._planId);
                            self.close();
                        };
                    });
                } else {
                    var competencies = {hascompetencies: false};
                    templates.render('report_lpmonitoring/datatable', competencies).done(function(html, js) {
                        $("#report-content").html(html);
                        templates.runTemplateJS(js);
                    });
                }
            }).fail(
                function(exp) {
                    notification.exception(exp);
                }
            );
        };

        /**
         * Load the summary tab.
         *
         * @param {Object} plan
         * @function
         */
        LearningplanReport.prototype.loadSummaryTab = function(plan) {
            var learningplan = this;

            // Get the "Summary" tab content.
            var promiseCompetenciesSummary = ajax.call([{
                methodname: 'report_lpmonitoring_list_plan_competencies_summary',
                args: {
                    id: plan.id
                }
            }]);
            promiseCompetenciesSummary[0].then(function(results) {
                if (results['scale_competency'].length > 0) {
                    var competencies = {reportinfos:results, plan:plan, hascompetencies: true};

                    // Keep the filter and search values.
                    var checkedvalue = $('input[type=radio][name=summaryfilter]:checked').val();
                    if (checkedvalue == 'course') {
                        competencies.filterchecked_course = true;
                    } else if (checkedvalue == 'module') {
                        competencies.filterchecked_module = true;
                    } else {
                        competencies.filterchecked_both = true;
                    }

                    var scaleselected = $('#scale-filter-summary').val();
                    for (var i = 0; i < competencies.reportinfos.scale_competency.length; i++) {
                        var scaleid = competencies.reportinfos.scale_competency[i].scaleid;
                        competencies.reportinfos.scale_competency[i].scaleselected = false;
                        if (scaleid == scaleselected) {
                            competencies.reportinfos.scale_competency[i].scaleselected = true;
                        }
                        var searchvalue = $( '#summary-search-competency-' + scaleid ).val();
                        competencies.reportinfos.scale_competency[i].tablesearchvalue = searchvalue;
                    }

                    // Render the "Summary" data table template.
                    templates.render('report_lpmonitoring/summary', competencies).done(function(html, js) {
                        $("#summary-content").html(html);
                        templates.runTemplateJS(js);
                        var popup = new Popup('[data-region=summary-competencies-section]', '[data-user-competency=true]');
                        // Override the after show refresh method of the user competency popup.
                        popup._refresh = function() {
                            var self = this;
                            learningplan.reloadCompetencyDetail(self._competencyId, self._userId, self._planId);
                            self.close();
                        };
                    });
                } else {
                    var competencies = {hascompetencies: false};
                    templates.render('report_lpmonitoring/summary', competencies).done(function(html, js) {
                        $("#summary-content").html(html);
                        templates.runTemplateJS(js);
                    });
                }
            }).fail(
                function(exp) {
                    notification.exception(exp);
                }
            );
        };

        /**
         * Apply inline grader for the rate button.
         *
         * @name  applyInlineGrader
         * @param {Number} competencyid comp ID
         * @param {Number} userid user ID
         * @param {Number} planid plan ID
         * @param {Number} scaleid scale ID
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.applyInlineGrader = function(competencyid, userid, planid, scaleid) {
            var self = this;
            str.get_string('chooserating', 'tool_lp').done(
                function(chooserateoption) {
                    // Set the inline grader.
                    var grader = new InlineGrader('#rate_' + competencyid,
                        scaleid,
                        competencyid,
                        userid,
                        planid,
                        '',
                        chooserateoption
                    );
                    // Callback when finishing rating.
                    grader.on('competencyupdated', function() {
                        self.reloadCompetencyDetail(competencyid, userid, planid);
                    });
                }
            );
        };

        /**
         * Reload competency detail and proficiency.
         *
         * @name  reloadCompetencyDetail
         * @param {Number} competencyid Competency ID
         * @param {Number} userid User ID
         * @param {Number} planid Plan ID
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.reloadCompetencyDetail = function(competencyid, userid, planid) {
            var self = this;
            self.competencies[competencyid] = {};
            var promise = ajax.call([{
                methodname: 'core_competency_read_plan',
                args: { id: planid }
            }, {
                methodname: 'report_lpmonitoring_get_competency_detail',
                args: {
                    competencyid: competencyid,
                    userid: userid,
                    planid: planid
                }
            }, {
                methodname: 'report_lpmonitoring_read_plan',
                args: {
                    scalevalues: "",
                    templateid: null,
                    planid: planid,
                    scalefilterin: self.scalefilterin,
                    tagid: null,
                    withcomments: false,
                    withplans: false
                }
            }
            ]);

            promise[0].then(function(plan) {
                promise[1].then(function(results) {
                    // Locally store competency information.
                    self.competencies[results.competencyid].competencydetail = results;
                    results.plan = plan;
                    results.cmcompgradingenabled = self.cmcompgradingEnabled;
                    templates.render('report_lpmonitoring/competency_detail', results).done(function(html, js) {
                        $('#comp-' + results.competencyid + ' .x_content').html(html);
                        templates.runTemplateJS(js);
                        // Show comptency ratings details tabs.
                        if (self.compDetailActiveTab === 'incoursemodule') {
                            $('.detail-comp-tab a[href="#tab-incms-content-' + results.competencyid + '"]').tab('show');
                        } else {
                            $('.detail-comp-tab a[href="#tab-incourses-content-' + results.competencyid + '"]').tab('show');
                        }
                        if (results.cangrade) {
                            // Apply inline grader.
                            self.applyInlineGrader(results.competencyid, userid, planid, results.scaleid);
                        }

                        // Apply Donut Graph to the competency in courses.
                        if (results.hasrating !== false) {
                            self.ApplyDonutGraph(results.competencyid, results, false);
                        }

                        // Apply Donut Graph to the competency in courses modules.
                        if (results.hasratingincm !== false && self.cmcompgradingEnabled) {
                            self.ApplyDonutGraph(results.competencyid, results, true);
                        }
                        self.colorContrast.apply('#comp-' + results.competencyid + ' .x_content .tile-stats .badge.cr-scalename');
                    });
                    templates.render('report_lpmonitoring/competency_proficiency', results).done(function(html, js) {
                        $('#comp-' + results.competencyid + ' span.level').html(html);
                        templates.runTemplateJS(js);
                    });
                    // Reload plan stats.
                    promise[2].then(function(results) {
                        templates.render('report_lpmonitoring/plan_stats_report',
                        {
                            plan:results.plan,
                            hascompetencies:true
                        }).done(function(html, js) {
                            $('#plan-stats-report').html(html);
                            templates.runTemplateJS(js);
                        });
                    });
                });
            });

        };

        /**
         * Apply Donut Grapth to the competency.
         *
         * @name   ApplyDonutGraph
         * @param  {Number} competencyid
         * @param  {Array} data
         * @param  {Boolean} forcm
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.ApplyDonutGraph = function(competencyid, data, forcm) {
            var options = {
                legend: false,
                responsive: false,
                tooltips: {enabled: false}
            };
            var colors = [];
            var canvasselector = '#canvas-graph-' + competencyid;
            if (forcm) {
                canvasselector = '#cm-canvas-graph-' + competencyid;
            }

            var itemsbyscales = [];
            $.each(data.scalecompetencyitems, function(index, record) {
                colors.push(record.color);
                if (forcm) {
                    itemsbyscales.push(record.nbcm);
                } else {
                    itemsbyscales.push(record.nbcourse);
                }
            });

            new Chart($(canvasselector), {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: itemsbyscales,
                        backgroundColor: colors,
                        hoverBackgroundColor: []
                    }]
                },
                options: options
            });
        };

        /**
         * Submit filter form.
         *
         * @name   submitFormHandler
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.submitFormHandler = function() {
            var self = this;
            var templateSelected = $("#template").is(':checked');
            var tagSelected = $("#tag").is(':checked');
            var templateid = null;
            var planid = null;
            var tagid = null;
            if (templateSelected === true) {
                templateid = self.templateId;
                planid = self.learningplanId;
                self.withcomments = $("#filter-comment").is(':checked');
                self.withplans = $("#filter-plan").is(':checked');
            } else if (tagSelected === true) {
                tagid = self.tagId;
                planid = self.tagLearningplanId;
            } else {
                // Else = "#student" is selected.
                planid = self.studentLearningplanId;
            }
            self.scalesvaluesSelected = $(self.learningplanSelector).data('scalefilter');
            if (self.scalefilterin === 'coursemodule') {
                self.compDetailActiveTab = 'incoursemodule';
            } else {
                self.compDetailActiveTab = 'incourse';
            }

            self.displayPlan(planid, templateid, tagid);
        };

        /**
         * Handler on scale change.
         *
         * @name   changeScaleHandler
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.changeScaleHandler = function() {
            var self = this;
            var scalefiltervalues = [];
            $('.competencyreport .scalefiltervalues').each(function() {
                if ($(this).is(":checked")) {
                    scalefiltervalues.push({scalevalue : $(this).data("scalevalue"), scaleid : $(this).data("scaleid")});
                }
            });

            if (scalefiltervalues.length > 0) {
                $('.competencyreport input[name=optionscalefilter]').prop("disabled", false);
                $('.competencyreport input[name=optionscalesortorder]').prop("disabled", false);

                if ($("#scalefiltercourse").is(":not(:checked)") && $("#scalefilterplan").is(":not(:checked)")) {
                    if (self.cmcompgradingEnabled) {
                        $('#scalefiltercoursemodule').prop("checked", true);
                    } else {
                        $('#scalefiltercourse').prop("checked", true);
                    }
                }
                if ($("#scalesortorderasc").is(":not(:checked)") && $("#scalesortorderdesc").is(":not(:checked)")) {
                    $('#scalesortorderasc').prop("checked", true);
                }
            } else {
                $('.competencyreport input[name=optionscalefilter]').prop("checked", false);
                $('.competencyreport input[name=optionscalefilter]').prop("disabled", true);

                $('.competencyreport input[name=optionscalesortorder]').prop("disabled", true);
                $('.competencyreport input[name=optionscalesortorder]').prop("checked", false);
            }
            self.changeScaleApplyHandler();
            self.changeScaleSortorderHandler();
            self.resetUserUsingLPTemplateSelection();
            var filterscaleinputs = JSON.stringify(scalefiltervalues);
            $(self.learningplanSelector).data('scalefilter', filterscaleinputs);
        };

        /**
         * Handler on scale filter application change.
         *
         * @name   changeScaleApplyHandler
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.changeScaleApplyHandler = function() {
            var self = this;

            self.scalefilterin = '';
            if ($("#scalefilterplan").is(':checked')) {
                self.scalefilterin = '';
            }
            if ($("#scalefiltercourse").is(':checked')) {
                self.scalefilterin = 'course';
            }
            if ($("#scalefiltercoursemodule").is(':checked')) {
                self.scalefilterin = 'coursemodule';
            }
            $(self.learningplanSelector).data('scalefilterapply', self.scalefilterin);
        };

        /**
         * Handler on scale sort order change.
         *
         * @name   changeScaleSortorderHandler
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.changeScaleSortorderHandler = function() {
            var self = this;
            self.scalesortorder = 'ASC';
            if ($("#scalesortorderdesc").is(':checked')) {
                self.scalesortorder = 'DESC';
            }
            $(self.learningplanSelector).data('scalesortorder', self.scalesortorder);
        };

        /**
         * Handler on "only plans with comments" filter change.
         *
         * @name   changeWithcommentsHandler
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.changeWithcommentsHandler = function() {
            var self = this;
            self.withcomments = $("#filter-comment").is(':checked');
            $(self.learningplanSelector).data('withcomments', self.withcomments);
        };

        /**
         * Handler on "only students with minimum two plans" filter change.
         *
         * @name   changeWithplansHandler
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.changeWithplansHandler = function() {
            var self = this;
            self.withplans = $("#filter-plan").is(':checked');
            $(self.learningplanSelector).data('withplans', self.withplans);
        };

        /**
         * Display the list of evidences in competency.
         *
         * @name   displayEvidencelist
         * @param {Object} evidences Evidence list
         * @param {Object} trigger element
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.displayEvidencelist = function(evidences, trigger) {
            var self = this;
            if (evidences.listevidence.length > 0) {
                str.get_string('listofevidence', 'tool_lp').done(function(title) {
                    return ModalFactory.create({
                        type: ModalFactory.types.DEFAULT,
                        title: title,
                        body: templates.render('report_lpmonitoring/list_evidences_in_competency', evidences),
                        large: true
                    }).done(function(modal) {
                        modal.getRoot().on(ModalEvents.hidden, function() {
                            modal.destroy();
                            self.focusContentItem(trigger);
                        }.bind(this));
                        modal.getRoot().on(ModalEvents.bodyRendered, function() {
                            DataTable.apply('#listevidencecompetency-' + evidences.competencyid, true, true);
                        }.bind(this));
                        modal.show();
                    }.bind(this));
                }).fail(notification.exception);
            }
        };

        /**
         * Display the list of courses in competency.
         *
         * @name   displayCourselist
         * @param {Object[]} listcourses
         * @param {Object} trigger element
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.displayCourselist = function(listcourses, trigger) {
            var self = this;
            if (listcourses.listtotalcourses.length > 0) {
                str.get_string('linkedcourses', 'tool_lp').done(function(title) {
                    return ModalFactory.create({
                        type: ModalFactory.types.DEFAULT,
                        title: title,
                        body: templates.render('report_lpmonitoring/list_courses_in_competency', listcourses),
                        large: true
                    }).done(function(modal) {
                        modal.getRoot().on(ModalEvents.hidden, function() {
                            modal.destroy();
                            self.focusContentItem(trigger);
                        }.bind(this));
                        modal.getRoot().on(ModalEvents.bodyRendered, function() {
                            DataTable.apply('#listcoursecompetency-' + listcourses.competencyid, true, true);
                        }.bind(this));
                        modal.show();
                    }.bind(this));
                }).fail(notification.exception);
            }
        };

        /**
         * Display the list of courses modules in competency.
         *
         * @name   displayCmlist
         * @param {Object[]} listcms
         * @param {Object} trigger element
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.displayCmlist = function(listcms, trigger) {
            var self = this;
            if (listcms.listtotalcms.length > 0) {
                str.get_string('linkedcms', 'report_lpmonitoring').done(function(title) {
                    return ModalFactory.create({
                        type: ModalFactory.types.DEFAULT,
                        title: title,
                        body: templates.render('report_lpmonitoring/list_cms_in_competency', listcms),
                        large: true
                    }).done(function(modal) {
                        modal.getRoot().on(ModalEvents.hidden, function() {
                            modal.destroy();
                            self.focusContentItem(trigger);
                        }.bind(this));
                        modal.getRoot().on(ModalEvents.bodyRendered, function() {
                            DataTable.apply('#listcmcompetency-' + listcms.competencyid, true, true);
                        }.bind(this));
                        modal.show();
                    }.bind(this));
                }).fail(notification.exception);
            }
        };

        /**
         * Display plan.
         *
         * @name   displayPlan
         * @param {Number} planid The learning plan ID
         * @param {Number} templateid The learning plan template ID
         * @param {Number} tagid The tag ID
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.displayPlan = function(planid, templateid, tagid) {
            var elementloading = null,
                    self = this;
            if ($('#plan-user-info').length) {
                elementloading = $('#plan-user-info');
            } else {
                elementloading = $("#reportFilter button");
            }
            elementloading.addClass('loading');
            // Hide collapse links as long as the competencies details are not displayed.
            $('.competencyreport .competency-detail a.collapse-link').css('visibility', 'hidden');

            // Set scales values empty if not defined.
            self.scalesvaluesSelected = self.userView === false ? self.scalesvaluesSelected : "";

            var promise = ajax.call([{
                methodname: 'report_lpmonitoring_read_plan',
                args: {
                    planid: parseInt(planid),
                    templateid: parseInt(templateid),
                    scalevalues: self.scalesvaluesSelected,
                    scalefilterin: self.scalefilterin,
                    scalesortorder: self.scalesortorder,
                    tagid: parseInt(tagid),
                    withcomments: self.withcomments,
                    withplans: self.withplans
                }
            }]);
            promise[0].then(function(results) {
                results.templateid = parseInt(templateid);
                M.cfg.contextid = results.plan.usercontext;
                if (results.hasnavigation === false) {
                    $('.plan-info-container').addClass('nonavigation');
                } else {
                    $('.plan-info-container').removeClass('nonavigation');
                }
                // State of navigation.
                results.navigationclosed = false;
                if ($('.plan-info-container').hasClass('closed')) {
                    results.navigationclosed = true;
                }
                if (self.userView === false) {
                    return templates.render('report_lpmonitoring/user_info', results).done(function(html) {
                        $("#userInfoContainer").html(html);
                        self.loadListCompetencies(results.plan, elementloading);
                        return templates.render('report_lpmonitoring/users_list_navigation', results).done(function(html) {
                            $("#users-list-full-navigation").html(html);
                        });
                    });
                } else {
                    str.get_string('learningplancompetencies', 'report_lpmonitoring', results.plan.name).done(function(planname) {
                        $('#planInfoContainer h3').text(planname);
                        self.loadListCompetencies(results.plan, elementloading);
                    });
                }
            }).fail(
                    function(exp) {
                        elementloading.removeClass('loading');
                        if (exp.errorcode === 'emptytemplate') {
                            var exception = {exception:exp};
                            return templates.render('report_lpmonitoring/user_info', exception).done(function(html) {
                                $("#userInfoContainer").html(html);
                                $("#listPlanCompetencies").empty();
                                $("#plan-stats-report").empty();
                                $("#report-content").empty();
                                $("#summary-content").empty();
                                $("#nav-tabs").hide();
                                $("#users-list-full-navigation").empty();
                            });
                        } else {
                            notification.exception(exp);
                        }
                    }
                );
        };

        /**
         * Display the list of courses in competency.
         *
         * @name   displayScaleCourseList
         * @param {Object[]} listcourses list of courses
         * @param {Object} trigger element
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.displayScaleCourseList = function(listcourses, trigger) {
            var self = this;
            if (listcourses.scalecompetencyitem.listcourses.length > 0) {
                str.get_string('linkedcourses', 'tool_lp').done(function(title) {
                    return ModalFactory.create({
                        type: ModalFactory.types.DEFAULT,
                        title: title,
                        body: templates.render('report_lpmonitoring/list_courses_in_scale_value', listcourses),
                        large: true
                    }).done(function(modal) {
                        modal.getRoot().on(ModalEvents.hidden, function() {
                            modal.destroy();
                            self.focusContentItem(trigger);
                        }.bind(this));
                        modal.getRoot().on(ModalEvents.bodyRendered, function() {
                            DataTable.apply('#listscalecoursecompetency-' + listcourses.competencyid, true, true);
                            self.colorContrast.apply('.moodle-dialogue-base .badge.cr-scalename');
                        }.bind(this));
                        modal.show();
                    }.bind(this));
                }).fail(notification.exception);
            }
        };

        /**
         * Display the list of courses modules in competency.
         *
         * @name   displayScaleCmList
         * @param {Object[]} listitems
         * @param {Object} trigger element
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.displayScaleCmList = function(listitems, trigger) {
            var self = this;
            if (listitems.scalecompetencyitem.listcms.length > 0) {
                str.get_string('linkedcms', 'report_lpmonitoring').done(function(title) {
                    return ModalFactory.create({
                        type: ModalFactory.types.DEFAULT,
                        title: title,
                        body: templates.render('report_lpmonitoring/list_cms_in_scale_value', listitems),
                        large: true
                    }).done(function(modal) {
                        modal.getRoot().on(ModalEvents.hidden, function() {
                            modal.destroy();
                            self.focusContentItem(trigger);
                        }.bind(this));
                        modal.getRoot().on(ModalEvents.bodyRendered, function() {
                            DataTable.apply('#listscalecmcompetency-' + listitems.competencyid, true, true);
                            self.colorContrast.apply('.moodle-dialogue-base .badge.cr-scalename');
                        }.bind(this));
                        modal.show();
                    }.bind(this));
                }).fail(notification.exception);
            }
        };

        /**
         * Focus the given content item or the first focusable element within
         * the content item.
         *
         * @method focusContentItem
         * @param {object} item The content item jQuery element
         */
        LearningplanReport.prototype.focusContentItem = function(item) {
            var focusable = 'input:not([type="hidden"]), a[href], button, textarea, select, [tabindex]';
            if (item.is(focusable)) {
                item.focus();
            } else {
                item.find(focusable).first().focus();
            }
        };

        /**
         * Reset grade for one competency.
         *
         * @name   resetGrade
         * @param  {Number} planid
         * @param  {Number} userid
         * @param  {Number} competencyid or null for all competencies of this plan
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.resetGrade = function(planid, userid, competencyid) {
            var self = this;
            var allcompetencies = false;
            if (competencyid === null) {
                allcompetencies = true;
            }
            var dialogue = new ResetGradeDialogue(allcompetencies);
            dialogue.on('rated', function(e, data) {
                var promise = ajax.call([{
                    methodname: 'report_lpmonitoring_reset_grading',
                    args: {
                        planid: planid,
                        competencyid: competencyid,
                        note: data.note
                    }
                }]);

                promise[0].then(function() {
                    if (competencyid === null) {
                        self.displayPlan(planid, self.templateId, self.tagId);
                    } else {
                        self.reloadCompetencyDetail(competencyid, userid, planid);
                    }
                }).fail(
                    function(exp) {
                        notification.exception(exp);
                    }
                );
            });
            dialogue.display();
        };

        /**
         * Init the differents page blocks and inputs form.
         *
         * @name   initPage
         * @return {Void}
         * @function
         */
        LearningplanReport.prototype.initPage = function() {
            var self = this;
            str.get_strings([
                { key: 'selectuser', component: 'report_lpmonitoring' },
                { key: 'nouserselected', component: 'report_lpmonitoring' }]
            ).done(
                function(strings) {
                    // Autocomplete users in templates.
                    autocomplete.enhance(
                        self.learningplanSelector,
                        false,
                        'report_lpmonitoring/learningplan',
                        strings[0],
                        false,
                        true,
                        strings[1])
                        .then(()=> self.disableUserTemplateSelector());
                    // Autocomplete users.
                    autocomplete.enhance(
                        self.studentSelector,
                        false,
                        'tool_lp/form-user-selector',
                        strings[0],
                        false,
                        true,
                        strings[1]);
                    if (self.userView === false) {
                        if ($('.competencyreport #student').is(':checked')) {
                            $('.competencyreport .templatefilter').toggleClass('disabled-option', true);
                            $('.competencyreport .tagfilter').toggleClass('disabled-option', true);
                        } else if ($('.competencyreport #tag').is(':checked')) {
                            $('.competencyreport .studentfilter').toggleClass('disabled-option', true);
                            $('.competencyreport .templatefilter').toggleClass('disabled-option', true);
                        } else {
                            $('.competencyreport .studentfilter').toggleClass('disabled-option', true);
                            $('.competencyreport .tagfilter').toggleClass('disabled-option', true);
                        }
                    }
                    self.checkDataFormReady();
                }
            ).fail(notification.exception);
            $(".competencyreport").on('click', '.moreless-toggler', function(event) {
                event.preventDefault();
                $(".advanced").toggle();
                $(this).toggleClass("hidden").siblings().removeClass('hidden');
            });

            // Allow collapse of block panels.
            fieldsettoggler.init();

            // Collapse block panels.
            $(".competencyreport").on('click', '.collapse-link', function(event) {
                event.preventDefault();
                var e = $(this).closest(".x_panel"),
                t = $(this).find("i"),
                n = e.find(".x_content");
                t.toggleClass("fa-chevron-right fa-chevron-down");
                n.slideToggle();
                e.toggleClass("panel-collapsed");
            });

            // Handle click on scale number courses.
            $(".competencyreport").on('click', 'a.scaleinfo', function(event) {
                event.preventDefault();
                var trigger = $(event.target).closest('td');
                var competencyid = $(this).data("competencyid");
                var scalevalue = $(this).data("scalevalue");
                var type = $(this).data("type");

                if (typeof self.competencies[competencyid] !== 'undefined') {
                    var listitems = {};
                    var competencydetail = self.competencies[competencyid].competencydetail;
                    listitems.scalecompetencyitem = competencydetail.scalecompetencyitems[scalevalue - 1];
                    listitems.competencyid = competencyid;
                    if (type === 'incm') {
                        self.displayScaleCmList(listitems, trigger);
                    } else {
                        self.displayScaleCourseList(listitems, trigger);
                    }
                }
            });

            $('.competencyreport #student').on('change', function(){
                if ($(this).is(':checked')){
                    $('.competencyreport .studentfilter').toggleClass('disabled-option', false);
                    $('.competencyreport .templatefilter').toggleClass('disabled-option', true);
                    $('.competencyreport .tagfilter').toggleClass('disabled-option', true);
                }
                self.checkDataFormReady();
            });

            $('.competencyreport #template').on('change', function(){
                if ($(this).is(':checked')){
                    $('.competencyreport .studentfilter').toggleClass('disabled-option', true);
                    $('.competencyreport .templatefilter').toggleClass('disabled-option', false);
                    $('.competencyreport .tagfilter').toggleClass('disabled-option', true);
                }
                self.checkDataFormReady();
            });

            $('.competencyreport #tag').on('change', function(){
                if ($(this).is(':checked')){
                    self.loadTags();
                    $('.competencyreport .studentfilter').toggleClass('disabled-option', true);
                    $('.competencyreport .templatefilter').toggleClass('disabled-option', true);
                    $('.competencyreport .tagfilter').toggleClass('disabled-option', false);
                }
                self.checkDataFormReady();
            });

            // Filter form submit.
            $(document).on('submit', '#reportFilter', function(){
                self.submitFormHandler();
                return false;
            });

            // User plan navigation.
            $(".competencyreport").on('click', 'a.navigatetoplan', function(event) {
                event.preventDefault();
                var planid = $(this).data('planid');
                var templateid = $(this).data('templateid');
                var tagid = $(this).data('tagid');
                self.displayPlan(planid, templateid, tagid);
            });

            // User plan full navigation.
            $(".competencyreport").on('click', '.toggle-lp-list-users', function(event) {
                event.preventDefault();
                $(this).find('i.angle').toggleClass('fa-angle-double-right fa-angle-double-left');
                $(this).closest('.plan-info-container').toggleClass('closed');
                $('div[data-region="blocks-lp-list-users"]').toggleClass('lp-list-users-closed');
            });
            var tmpselector = '.plan-info-container .table-list-users .nav-list-users-displayinfo';
            $(".competencyreport").on('click', tmpselector, function(event) {
                event.stopPropagation();
                event.preventDefault();
                $(this).find('i.fa').toggleClass('fa-angle-down fa-angle-up');
                $(this).parent().find('span.item-nav-list-users-info').toggleClass('hidden');
            });
            $(".competencyreport").on('click', '.plan-info-container .table-list-users tr', function(event) {
                event.preventDefault();
                var planid = $(this).data('planid');
                var templateid = $(this).data('templateid');
                var tagid = $(this).data('tagid');
                self.displayPlan(planid, templateid, tagid);
            });

            // Handle click on list evidence.
            $(".competencyreport").on('click', 'a.listevidence', function(event) {
                event.preventDefault();
                var trigger = $(event.target);
                var competencyid = $(this).data('competencyid');
                if (typeof self.competencies[competencyid] !== 'undefined') {
                    var listevidence = {};
                    listevidence.listevidence = self.competencies[competencyid].competencydetail.listevidence;
                    listevidence.competencyid = competencyid;
                    self.displayEvidencelist(listevidence, trigger);
                }
            });

            // Handle click on total number courses.
            $(".competencyreport").on('click', 'a.totalnbcourses', function(event) {
                event.preventDefault();
                var trigger = $(event.target);
                var competencyid = $(this).data('competencyid');
                if (typeof self.competencies[competencyid] !== 'undefined') {
                    var totallistcourses = {};
                    totallistcourses.listtotalcourses = self.competencies[competencyid].competencydetail.listtotalcourses;
                    totallistcourses.competencyid = competencyid;
                    self.displayCourselist(totallistcourses, trigger);
                }
            });

            // Handle click on total number of courses modules.
            $(".competencyreport").on('click', 'a.totalnbcms', function(event) {
                event.preventDefault();
                var trigger = $(event.target);
                var competencyid = $(this).data('competencyid');
                if (typeof self.competencies[competencyid] !== 'undefined') {
                    var totallistcms = {};
                    totallistcms.listtotalcms = self.competencies[competencyid].competencydetail.listtotalcms;
                    totallistcms.competencyid = competencyid;

                    self.displayCmlist(totallistcms, trigger);
                }
            });
            // Handle click on rating tabs.
            $(".competencyreport").on('click', '.detail-comp-tab a', function(event) {
                event.preventDefault();
                if ($(event.target).hasClass('incm')) {
                    self.compDetailActiveTab = "incoursemodule";
                } else {
                    self.compDetailActiveTab = "incourse";
                }
            });

            // Handle click on reset display rating.
            $(".competencyreport").on('click', '.resetdisplayrating a', function(event) {
                event.preventDefault();
                var planid = $(this).data('canresetdisplayrating-plan');
                self.resetDisplayRating(planid);
            });

            // Handle click on reset one competency.
            $(".competencyreport").on('click', '.reset-grade a', function(event) {
                event.preventDefault();
                var planid = $(this).data('resetgrade-plan');
                var competencyid = $(this).data('competencyid');
                var userid = $(this).data('userid');
                self.resetGrade(planid, userid, competencyid);
            });

            // Handle click on reset all competencies.
            $(".competencyreport").on('click', '.reset-grade-all a', function(event) {
                event.preventDefault();
                var planid = $(this).data('resetgrade-plan');
                var userid = $(this).data('userid');
                self.resetGrade(planid, userid, null);
            });

            // Collapse/Expand all.
            str.get_strings([
                { key: 'collapseall'},
                { key: 'expandall'}]
            ).done(
                function(strings) {
                    var collapseall = strings[0];
                    var expandall = strings[1];
                    $(".competencyreport").on('click', '.collapsible-actions a', function(event) {
                        event.preventDefault();
                        if ($(this).hasClass('collapse-all')) {
                            $(this).text(expandall);
                            $('#listPlanCompetencies div.x_panel:not(.panel-collapsed) a.collapse-link').trigger('click');
                        } else {
                            $(this).text(collapseall);
                            $('#listPlanCompetencies div.panel-collapsed a.collapse-link').trigger('click');
                        }
                        $(this).toggleClass("collapse-all expand-all");
                    });
                }
            ).fail(notification.exception);
        };

        return {
            /**
             * Main initialisation.
             *
             * @param {Boolean} userview True if the report is for user view (student).
             * @param {Boolean} cmcompgradingenabled True if grading in course module competency is enabled.
             * @return {LearningplanReport} A new instance of LearningplanReport.
             * @method init
             */
            init: function(userview, cmcompgradingenabled) {
                return new LearningplanReport(userview, cmcompgradingenabled);
            },
            /**
             * Process result autocomplete.
             *
             * @param {type} selector
             * @param {type} results
             * @returns {Array}
             */
            processResults: function(selector, results) {
                var users = [];
                $.each(results, function(index, userplan) {
                    users.push({
                        value: userplan.planid,
                        label: userplan._label
                    });
                });
                return users;
            },

            /**
             * Transport method for autocomplete.
             *
             * @param {type} selector
             * @param {type} query
             * @param {type} success
             * @param {type} failure
             * @returns {undefined}
             */
            transport: function(selector, query, success, failure) {
                var promise;
                var scalefilterapply = $(selector).data('scalefilterapply');
                var scalesortorder = $(selector).data('scalesortorder');
                scalesortorder = scalesortorder ? scalesortorder : 'ASC';
                var withcomments = $(selector).data('withcomments');
                withcomments = withcomments ? withcomments : false;
                var withplans = $(selector).data('withplans');
                withplans = withplans ? withplans : false;
                var templateid = $(selector).data('templateid');
                if (templateid === '') {
                    return [];
                }

                promise = ajax.call([{
                    methodname: 'report_lpmonitoring_search_users_by_templateid',
                    args: {
                        query: query,
                        templateid: parseInt(templateid),
                        scalevalues: $(selector).data('scalefilter'),
                        scalefilterin: scalefilterapply,
                        scalesortorder: scalesortorder,
                        withcomments: withcomments,
                        withplans: withplans
                    }
                }]);

                promise[0].then(function(results) {
                    var promises = [],
                        i = 0;
                    // Render the label.
                    $.each(results, function(index, user) {
                        var ctx = user,
                            identity = [];
                        $.each(['idnumber', 'email', 'phone1', 'phone2', 'department', 'institution'], function(i, k) {
                            if (typeof user[k] !== 'undefined' && user[k] !== '') {
                                ctx.hasidentity = true;
                                identity.push(user[k]);
                            }
                        });
                        ctx.identity = identity.join(', ');
                        promises.push(templates.render('report_lpmonitoring/form-user-selector-suggestion', ctx));
                    });

                    // Apply the label to the results.
                    return $.when.apply($.when, promises).then(function() {
                        var args = arguments;
                        $.each(results, function(index, user) {
                            user._label = args[i];
                            i++;
                        });
                        success(results);
                    });

                }, failure);
            }
        };

    });