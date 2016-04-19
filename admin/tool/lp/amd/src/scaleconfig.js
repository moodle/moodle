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
define(['jquery', 'core/notification', 'core/templates', 'core/ajax', 'tool_lp/dialogue', 'tool_lp/scalevalues'],
    function($, notification, templates, ajax, Dialogue, ModScaleValues) {

    /**
     * Scale config object.
     * @param {String} selectSelector The select box selector.
     * @param {String} inputSelector The hidden input field selector.
     * @param {String} triggerSelector The trigger selector.
     */
    var ScaleConfig = function(selectSelector, inputSelector, triggerSelector) {
        this.selectSelector = selectSelector;
        this.inputSelector = inputSelector;
        this.triggerSelector = triggerSelector;

        // Get the current scale ID.
        this.originalscaleid = $(selectSelector).val();
        $(selectSelector).on('change', this.scaleChangeHandler.bind(this)).change();
        $(triggerSelector).click(this.showConfig.bind(this));
    };

    /** @var {String} The select box selector. */
    ScaleConfig.prototype.selectSelector = null;
    /** @var {String} The hidden field selector. */
    ScaleConfig.prototype.inputSelector = null;
    /** @var {String} The trigger selector. */
    ScaleConfig.prototype.triggerSelector = null;
    /** @var {Array} scalevalues ID and name of the scales. */
    ScaleConfig.prototype.scalevalues = null;
    /** @var {Number) originalscaleid Original scale ID when the page loads. */
    ScaleConfig.prototype.originalscaleid = 0;
    /** @var {Number} scaleid Current scale ID. */
    ScaleConfig.prototype.scaleid = 0;
    /** @var {Dialogue} Reference to the popup. */
    ScaleConfig.prototype.popup = null;

    /**
     * Displays the scale configuration dialogue.
     *
     * @method showConfig
     */
    ScaleConfig.prototype.showConfig = function() {
        var self = this;

        this.scaleid = $(this.selectSelector).val();
        if (this.scaleid <= 0) {
            // This should not happen.
            return;
        }

        var scalename = $(this.selectSelector).find("option:selected").text();
        this.getScaleValues(this.scaleid).done(function() {

            var context = {
                scalename: scalename,
                scales: self.scalevalues
            };

            // Dish up the form.
            templates.render('tool_lp/scale_configuration_page', context)
                .done(function(html) {
                    new Dialogue(
                        scalename,
                        html,
                        self.initScaleConfig.bind(self)
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
    ScaleConfig.prototype.retrieveOriginalScaleConfig = function() {
        var jsonstring = $(this.inputSelector).val();
        if (jsonstring !== '') {
            var scaleconfiguration = $.parseJSON(jsonstring);
            // The first object should contain the scale ID for the configuration.
            var scaledetail = scaleconfiguration.shift();
            // Check that this scale id matches the one from the page before returning the configuration.
            if (scaledetail.scaleid === this.originalscaleid) {
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
    ScaleConfig.prototype.initScaleConfig = function(popup) {
        this.popup = popup;
        var body = $(popup.getContent());
        if (this.originalscaleid === this.scaleid) {
            // Set up the popup to show the current configuration.
            var currentconfig = this.retrieveOriginalScaleConfig();
            // Set up the form only if there is configuration settings to set.
            if (currentconfig !== '') {
                currentconfig.forEach(function(value) {
                    if (value.scaledefault === 1) {
                        body.find('[data-field="tool_lp_scale_default_' + value.id + '"]').attr('checked', true);
                    }
                    if (value.proficient === 1) {
                        body.find('[data-field="tool_lp_scale_proficient_' + value.id + '"]').attr('checked', true);
                    }
                });
            }
        }
        body.on('click', '[data-action="close"]', function() {
            this.setScaleConfig();
            popup.close();
        }.bind(this));
        body.on('click', '[data-action="cancel"]', function() {
            popup.close();
        }.bind(this));
    };

    /**
     * Set the scale configuration back into a JSON string in the hidden element.
     *
     * @method setScaleConfig
     */
    ScaleConfig.prototype.setScaleConfig = function() {
        var body = $(this.popup.getContent());
        // Get the data.
        var data = [{ scaleid: this.scaleid}];
        this.scalevalues.forEach(function(value) {
            var scaledefault = 0;
            var proficient = 0;
            if (body.find('[data-field="tool_lp_scale_default_' + value.id + '"]').is(':checked')) {
                scaledefault = 1;
            }
            if (body.find('[data-field="tool_lp_scale_proficient_' + value.id + '"]').is(':checked')) {
                proficient = 1;
            }

            if (!scaledefault && !proficient) {
                return;
            }

            data.push({
                id: value.id,
                scaledefault: scaledefault,
                proficient: proficient
            });
         });
        var datastring = JSON.stringify(data);
        // Send to the hidden field on the form.
        $(this.inputSelector).val(datastring);
        // Once the configuration has been saved then the original scale ID is set to the current scale ID.
        this.originalscaleid = this.scaleid;
    };

    /**
     * Get the scale values for the selected scale.
     *
     * @method getScaleValues
     * @param {Number} scaleid The scale ID of the selected scale.
     * @return {Promise} A deffered object with the scale values.
     */
    ScaleConfig.prototype.getScaleValues = function(scaleid) {
        return ModScaleValues.get_values(scaleid).then(function(values) {
            this.scalevalues = values;
            return values;
        }.bind(this));
    };

    /**
     * Triggered when a scale is selected.
     *
     * @name   scaleChangeHandler
     * @param  {Event} e
     * @return {Void}
     * @function
     */
    ScaleConfig.prototype.scaleChangeHandler = function(e) {
        if ($(e.target).val() <= 0) {
            $(this.triggerSelector).prop('disabled', true);
        } else {
            $(this.triggerSelector).prop('disabled', false);
        }

    };

    return {

        /**
         * Main initialisation.
         *
         * @param {String} selectSelector The select box selector.
         * @param {String} inputSelector The hidden input field selector.
         * @param {String} triggerSelector The trigger selector.
         * @return {ScaleConfig} A new instance of ScaleConfig.
         * @method init
         */
        init: function(selectSelector, inputSelector, triggerSelector) {
            return new ScaleConfig(selectSelector, inputSelector, triggerSelector);
        }
    };
});
