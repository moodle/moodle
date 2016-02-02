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
 * Display Competency in dialogue box.
 *
 * @module     tool_lp/Competencydialogue
 * @package    tool_lp
 * @copyright  2015 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery',
        'core/notification',
        'core/ajax',
        'core/templates',
        'core/str',
        'tool_lp/dialogue'],
       function($, notification, ajax, templates, str, Dialogue) {

    /**
     * Constructor for CompetencyDialogue.
     *
     * @param {Object} options
     *
     */
    var Competencydialogue = function(options) {
        this.options = {
            includerelated: false,
            includecourses: false
        };
        $.extend(this.options, options);
    };

    /**
     * Log the competency viewed event.
     *
     * @param  {Number} The competency ID.
     * @method triggerCompetencyViewedEvent
     */
    Competencydialogue.prototype.triggerCompetencyViewedEvent = function(competencyId) {
        ajax.call([{
                methodname: 'tool_lp_competency_viewed',
                args: { id: competencyId }
        }]);
    };

    /**
     * Callback on dialogue display, it apply enhance on competencies dialogue.
     *
     * @param {Dialogue} dialogue
     * @method enhanceDialogue
     */
    Competencydialogue.prototype.enhanceDialogue = function(dialogue) {
        //Apply watch on the related competencies and competencies in the dialogue.
        var comprelated = new Competencydialogue({includerelated : false});
        comprelated.watch(dialogue.getContent());
    };

    /**
     * Display a dialogue box by competencyid.
     *
     * @param {Number} the competency id
     * @param {Object} Options for tool_lp_data_for_competency_summary service
     * @param {Object} dataSource data to be used to display dialogue box
     * @method showDialogue
     */
    Competencydialogue.prototype.showDialogue = function(competencyid) {

        var datapromise = this.getCompetencyDataPromise(competencyid);
        var localthis = this;
        datapromise.done(function(data) {
            // Inner Html in the dialogue content.
            templates.render('tool_lp/competency_summary', data)
                .done(function(html) {
                    // Log competency viewed event.
                    localthis.triggerCompetencyViewedEvent(competencyid);

                    // Show the dialogue.
                    new Dialogue(
                        data.competency.shortname,
                        html,
                        localthis.enhanceDialogue
                    );
                }).fail(notification.exception);
        }).fail(notification.exception);
    };

    /**
     * Display a dialogue box from data.
     *
     * @param {Object} dataSource data to be used to display dialogue box
     * @method showDialogueFromData
     */
    Competencydialogue.prototype.showDialogueFromData = function(dataSource) {

        var localthis = this;
        // Inner Html in the dialogue content.
        templates.render('tool_lp/competency_summary', dataSource)
            .done(function(html) {
                // Log competency viewed event.
                localthis.triggerCompetencyViewedEvent(dataSource.id);

                // Show the dialogue.
                new Dialogue(
                    dataSource.shortname,
                    html,
                    localthis.enhanceDialogue
                );
            }).fail(notification.exception);
    };

    /**
     * The action on the click event.
     *
     * @param {Event} event click
     * @method clickEventHandler
     */
    Competencydialogue.prototype.clickEventHandler = function(e) {

        var compdialogue = e.data.compdialogue;
        var competencyid = $(e.target).data('id');

        // Show the dialogue box.
        compdialogue.showDialogue(competencyid);
        e.preventDefault();
    };

    /**
     * Get a promise on data competency.
     *
     * @param {Number} competencyid
     * @return {Promise} return promise on data request
     * @method getCompetencyDataPromise
     */
    Competencydialogue.prototype.getCompetencyDataPromise = function(competencyid) {

        var requests = ajax.call([
            { methodname: 'tool_lp_data_for_competency_summary',
              args: { competencyid: competencyid,
                      includerelated: this.options.includerelated,
                      includecourses: this.options.includecourses
                    }
            }
        ]);

        return requests[0].then(function(context) {
           return context;
        }).fail(notification.exception);
    };

    /**
     * Watch the competencies links in container.
     *
     * @param {String} container selector of node containing competencies links
     * @method watch
     */
    Competencydialogue.prototype.watch = function(containerSelector) {
        $(containerSelector).off('click', '[data-action="competency-dialogue"]', this.clickEventHandler);
        $(containerSelector).on('click', '[data-action="competency-dialogue"]', { compdialogue: this }, this.clickEventHandler);
    };

    return Competencydialogue;
});
