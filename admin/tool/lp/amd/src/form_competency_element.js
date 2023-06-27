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
 * Badge select competency actions
 *
 * @module     tool_lp/form_competency_element
 * @copyright  2019 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'tool_lp/competencypicker', 'core/ajax', 'core/notification', 'core/templates'],
        function($, Picker, Ajax, Notification, Templates) {

    var pickerInstance = null;

    var pageContextId = 1;

    /**
     * Re-render the list of selected competencies.
     *
     * @method renderCompetencies
     * @return {boolean}
     */
    var renderCompetencies = function() {
        var currentCompetencies = $('[data-action="competencies"]').val();
        var requests = [];
        var i = 0;

        if (currentCompetencies != '') {
            currentCompetencies = currentCompetencies.split(',');
            for (i = 0; i < currentCompetencies.length; i++) {
                requests[requests.length] = {
                    methodname: 'core_competency_read_competency',
                    args: {id: currentCompetencies[i]}
                };
            }
        }

        $.when.apply($, Ajax.call(requests, false)).then(function() {
            var i = 0,
                competencies = [];

            for (i = 0; i < arguments.length; i++) {
                competencies[i] = arguments[i];
            }
            var context = {
                competencies: competencies
            };

            return Templates.render('tool_lp/form_competency_list', context);
        }).then(function(html, js) {
            Templates.replaceNode($('[data-region="competencies"]'), html, js);
            return true;
        }).fail(Notification.exception);

        return true;
    };

    /**
     * Deselect a competency
     *
     * @method unpickCompetenciesHandler
     * @param {Event} e
     * @return {boolean}
     */
    var unpickCompetenciesHandler = function(e) {
        var currentCompetencies = $('[data-action="competencies"]').val().split(','),
            newCompetencies = [],
            i,
            toRemove = $(e.currentTarget).data('id');

        for (i = 0; i < currentCompetencies.length; i++) {
            if (currentCompetencies[i] != toRemove) {
                newCompetencies[newCompetencies.length] = currentCompetencies[i];
            }
        }

        $('[data-action="competencies"]').val(newCompetencies.join(','));

        return renderCompetencies();
    };

    /**
     * Open a competencies popup to relate competencies.
     *
     * @method pickCompetenciesHandler
     */
    var pickCompetenciesHandler = function() {
        var currentCompetencies = $('[data-action="competencies"]').val().split(',');

        if (!pickerInstance) {
            pickerInstance = new Picker(pageContextId, false, 'parents', true);
            pickerInstance.on('save', function(e, data) {
                var before = $('[data-action="competencies"]').val();
                var compIds = data.competencyIds;
                if (before != '') {
                    compIds = compIds.concat(before.split(','));
                }
                var value = compIds.join(',');

                $('[data-action="competencies"]').val(value);

                return renderCompetencies();
            });
        }

        pickerInstance.setDisallowedCompetencyIDs(currentCompetencies);
        pickerInstance.display();
    };

    return /** @alias module:tool_lp/form_competency_element */ {
        /**
         * Listen for clicks on the competency picker and push the changes to the form element.
         *
         * @method init
         * @param {Integer} contextId
         */
        init: function(contextId) {
            pageContextId = contextId;
            renderCompetencies();
            $('[data-action="select-competencies"]').on('click', pickCompetenciesHandler);
            $('body').on('click', '[data-action="deselect-competency"]', unpickCompetenciesHandler);
        }
    };
});
