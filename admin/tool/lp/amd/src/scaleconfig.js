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
 * Handle opening a dialogue to configure scale data.
 *
 * @module     tool_lp/scaleconfig
 * @package    tool_lp
 * @copyright  2015 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/notification', 'core/templates', 'core/ajax', 'tool_lp/dialogue'],
    function($, notification, templates, ajax, Dialogue) {

    /** @var {Array} scalevalues ID and name of the scales. */
    var scalevalues = null;
    /** @var {Number) originalscaleid Original scale ID when the page loads. */
    var originalscaleid = 0;
    /** @var {Number} scaleid Current scale ID. */
    var scaleid = 0;

    /**
     * Displays the scale configuration dialogue.
     *
     * @method showConfig
     */
    var showConfig = function() {
        scaleid = $("#id_scaleid").val();
        if (scaleid <= 0) {
            // This should not happen.
            return;
        }

        var scalename = $("#id_scaleid option:selected").text();
        getScaleValues(scaleid).done(function() {

            var context = {
                scalename: scalename,
                scales: scalevalues
            };

            // Dish up the form.
            templates.render('tool_lp/scale_configuration_page', context)
                .done(function(html) {
                    new Dialogue(
                        scalename,
                        html,
                        initScaleConfig
                    );
                }).fail(notification.exception);
        }).fail(notification.exception);
    };

    /**
     * Gets the original scale configuration if it was set.
     *
     * @method retrieveOriginalScaleConfig
     * @return {Object|String} scale configuration or empty string.
     */
    var retrieveOriginalScaleConfig = function() {
        var jsonstring = $('#tool_lp_scaleconfiguration').val();
        if (jsonstring !== '') {
            var scaleconfiguration = $.parseJSON(jsonstring);
            // The first object should contain the scale ID for the configuration.
            var scaledetail = scaleconfiguration.shift();
            // Check that this scale id matches the one from the page before returning the configuration.
            if (scaledetail.scaleid === originalscaleid) {
                return scaleconfiguration;
            }
        }
        return '';
    };

    /**
     * Initialises the scale configuration dialogue.
     *
     * @method initScaleConfig
     * @param {Dialogue} popup Dialogue object to initialise.
     */
    var initScaleConfig = function(popup) {
        var body = $(popup.getContent());
        if (originalscaleid === scaleid) {
            // Set up the popup to show the current configuration.
            var currentconfig = retrieveOriginalScaleConfig();
            // Set up the form only if there is configuration settings to set.
            if (currentconfig !== '') {
                currentconfig.forEach(function(value) {
                    if (value.scaledefault === 1) {
                        $('#tool_lp_scale_default_' + value.id).attr('checked', true);
                    }
                    if (value.proficient === 1) {
                        $('#tool_lp_scale_proficient_' + value.id).attr('checked', true);
                    }
                });
            }
        }
        body.on('click', '[data-action="close"]', function() { setScaleConfig(); popup.close(); });
        body.on('click', '[data-action="cancel"]', function() { popup.close(); });
    };

    /**
     * Set the scale configuration back into a JSON string in the hidden element.
     *
     * @method setScaleConfig
     */
    var setScaleConfig = function() {
        // Get the data.
        var data = [{ scaleid: scaleid}];
        scalevalues.forEach(function(value) {
            var scaledefault = 0;
            var proficient = 0;
            if ($('#tool_lp_scale_default_' + value.id).is(':checked')) { scaledefault = 1; }
            if ($('#tool_lp_scale_proficient_' + value.id).is(':checked')) { proficient = 1; }
            data.push({
                name: value.name,
                id: value.id,
                scaledefault: scaledefault,
                proficient: proficient
            });
         });
        var datastring = JSON.stringify(data);
        // Send to the hidden field on the form.
        $('#tool_lp_scaleconfiguration').val(datastring);
        // Once the configuration has been saved then the original scale ID is set to the current scale ID.
        originalscaleid = scaleid;
    };

    /**
     * Get the scale values for the selected scale.
     *
     * @method getScaleValues
     * @param {Number} scaleid The scale ID of the selected scale.
     * @return {Promise} A deffered object with the scale values.
     */
    var getScaleValues = function(scaleid) {
        var deferred = $.Deferred();
        var promises = ajax.call([{
            methodname: 'tool_lp_get_scale_values',
            args: {
               scaleid: scaleid
            }
        }]);
        promises[0].done(function(result) {
            scalevalues = result;
            deferred.resolve(result);
        }).fail(function(exception) {
            deferred.reject(exception);
        });
        return deferred.promise();
    };

    /**
     * Triggered when a scale is selected.
     *
     * @name   scaleChangeHandler
     * @param  {Event} e
     * @return {Void}
     * @function
     */
    var scaleChangeHandler = function(e) {
        if ($(e.target).val() <= 0) {
            $('#id_scaleconfigbutton').prop('disabled', true);
        } else {
            $('#id_scaleconfigbutton').prop('disabled', false);
        }

    };

    return {
        /**
         * Main initialisation.
         *
         * @method init
         */
        init: function() {
            // Get the current scale ID.
            originalscaleid = $("#id_scaleid").val();
            $("#id_scaleid").on('change', scaleChangeHandler).change();
            $('#id_scaleconfigbutton').click(showConfig);
        }
    };
});
