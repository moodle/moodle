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
     * The main instance we'll be working with.
     *
     * @type {Competencydialogue}
     */
    var instance;

    /**
     * Constructor for CompetencyDialogue.
     *
     * @param {Object} options
     *
     */
    var Competencydialogue = function() {
      // Intentionally left empty.
    };

    /**
     * Log the competency viewed event.
     *
     * @param  {Number} competencyId The competency ID.
     * @method triggerCompetencyViewedEvent
     */
    Competencydialogue.prototype.triggerCompetencyViewedEvent = function(competencyId) {
        ajax.call([{
                methodname: 'core_competency_competency_viewed',
                args: {id: competencyId}
        }]);
    };

    /**
     * Display a dialogue box by competencyid.
     *
     * @param {Number} competencyid The competency ID.
     * @param {Object} options The options.
     * @method showDialogue
     */
    Competencydialogue.prototype.showDialogue = function(competencyid, options) {

        var datapromise = this.getCompetencyDataPromise(competencyid, options);
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
                        html
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
     * @param {Event} e event click
     * @method clickEventHandler
     */
    Competencydialogue.prototype.clickEventHandler = function(e) {

        var compdialogue = e.data.compdialogue;
        var currentTarget = $(e.currentTarget);
        var competencyid = currentTarget.data('id');
        var includerelated = !(currentTarget.data('excluderelated'));
        var includecourses = currentTarget.data('includecourses');

        // Show the dialogue box.
        compdialogue.showDialogue(competencyid, {
            includerelated: includerelated,
            includecourses: includecourses
        });
        e.preventDefault();
    };

    /**
     * Get a promise on data competency.
     *
     * @param {Number} competencyid
     * @param {Object} options
     * @return {Promise} return promise on data request
     * @method getCompetencyDataPromise
     */
    Competencydialogue.prototype.getCompetencyDataPromise = function(competencyid, options) {

        var requests = ajax.call([
            {methodname: 'tool_lp_data_for_competency_summary',
              args: {competencyid: competencyid,
                      includerelated: options.includerelated || false,
                      includecourses: options.includecourses || false
                    }
            }
        ]);

        return requests[0].then(function(context) {
           return context;
        }).fail(notification.exception);
    };

    return /** @alias module:tool_lp/competencydialogue */ {

        /**
         * Initialise the competency dialogue module.
         *
         * Only the first call matters.
         */
        init: function() {
            if (typeof instance !== 'undefined') {
                return;
            }

            // Instantiate the one instance and delegate event on the body.
            instance = new Competencydialogue();
            $('body').delegate('[data-action="competency-dialogue"]', 'click', {compdialogue: instance},
                instance.clickEventHandler.bind(instance));
        }
    };
});
