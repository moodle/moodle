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
 * Scale color configuration.
 *
 * @module     report_lpmonitoring/scalecolorconfiguration
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 */

define(['jquery',
    'core/templates',
    'core/ajax',
    'core/notification',
    'core/str',
    'report_lpmonitoring/fieldsettoggler'],
    function($, templates, ajax, notification, str, fieldsettoggler) {

        /**
         * Scale color config object.
         * @param {String} frameworkSelector The framework select box selector.
         * @param {String} scaleSelector The scale select box selector.
         * @param {String} scaleValuesSelector The scaleValuesSelector selector.
         */
        var ScaleColorConfiguration = function(frameworkSelector, scaleSelector, scaleValuesSelector) {
            this.frameworkSelector = frameworkSelector;
            this.scaleSelector = scaleSelector;
            this.scaleValuesSelector = scaleValuesSelector;

            $(frameworkSelector).on('change', this.frameworkChangeHandler.bind(this)).change();
            $(scaleSelector).on('change', this.scaleChangeHandler.bind(this)).change();
            $(document).on('submit', '#savecolor', this.saveHandler.bind(this));

            // Allow collapse of block panels.
            fieldsettoggler.init();
        };

        /** @var {Number} The framework ID. */
        ScaleColorConfiguration.prototype.frameworkid = null;
        /** @var {Number} The scale. */
        ScaleColorConfiguration.prototype.scaleid = null;

        /** @var {String} The framework select box selector. */
        ScaleColorConfiguration.prototype.frameworkSelector = null;
        /** @var {String} The scale select box selector. */
        ScaleColorConfiguration.prototype.scaleSelector = null;
        /** @var {String} The scaleValuesSelector selector. */
        ScaleColorConfiguration.prototype.scaleValuesSelector = null;

        /**
         * Triggered when a frameworkid is selected.
         *
         * @name   frameworkChangeHandler
         * @param  {Event} e
         * @return {Void}
         * @function
         */
        ScaleColorConfiguration.prototype.frameworkChangeHandler = function(e) {
            var self = this,
                    requests;

            self.frameworkid = $(e.target).val();
            if (self.frameworkid !== '') {
                $(self.scaleSelector).prop('disabled', false);
                self.updateScaleHeader();
                $('#loaderscale').show();
                requests = ajax.call([{
                    methodname: 'report_lpmonitoring_get_scales_from_framework',
                    args: {competencyframeworkid: self.frameworkid}
                }]);

                requests[0].done(function(context) {
                    self.buildScaleOptions(context);
                    self.updateScaleHeader();
                    self.loadScaleConfiguration();
                    $('#loaderscale').hide();
                    $('#id_scale').show();
                }).fail(notification.exception);
            } else {
                str.get_string('noscaleavailable', 'report_lpmonitoring').done(
                    function(noscaleavailable) {
                        $(self.scaleSelector).prop('disabled', true);
                        $(self.scaleSelector + ' option').remove();
                        $(self.scaleSelector).append($('<option>').text(noscaleavailable).val(''));
                        self.updateScaleHeader();
                        $('#loaderscale').hide();
                        $('#id_scale').hide();
                    }
                );
            }
        };

        /**
         * Update scale header.
         *
         * @name   updateScaleHeader
         * @return {Void}
         * @function
         */
        ScaleColorConfiguration.prototype.updateScaleHeader = function() {
            var selector = document.getElementById('scaleselector'),
                    scale = selector.options[selector.selectedIndex].text;
            str.get_string('colorsforscale', 'report_lpmonitoring', scale).done(function(s) {
                $('#scaleheader').text(s);
            });
        };

        /**
         * Build options from scale.
         *
         * @name   buildScaleOptions
         * @param  {Array} options
         * @return {Void}
         * @function
         */
        ScaleColorConfiguration.prototype.buildScaleOptions = function(options) {
            var self = this;

            // Reset options scales.
            $(self.scaleSelector + ' option').remove();

            $.each(options, function(key, value) {
                $(self.scaleSelector).append($('<option>').text(value.name).val(value.id));
            });

            self.scaleid = $(self.scaleSelector + ' option:first-child').attr('value');
        };

        /**
         * Triggered when a scale is selected.
         *
         * @name   scaleChangeHandler
         * @param  {Event} e
         * @return {Void}
         * @function
         */
        ScaleColorConfiguration.prototype.scaleChangeHandler = function(e) {
            var self = this;

            self.scaleid = $(e.target).val();

            if (self.scaleid) {
                $(self.scaleSelector).prop('disabled', false);
                self.updateScaleHeader();
                $("#loaderscalevalues").show();
                self.loadScaleConfiguration();
            } else {
                $(self.scaleValuesSelector).hide();
                $("#loaderscalevalues").hide();
            }
        };

        /**
         * Triggered when a scale is selected.
         *
         * @name   scaleChangeHandler
         * @return {Void}
         * @function
         */
        ScaleColorConfiguration.prototype.loadScaleConfiguration = function() {
            var self = this,
                requests;
            $("#loaderscalevalues").show();
            $('#submitScaleColorButton').prop('disabled', true);
            $(self.scaleValuesSelector).hide();
            requests = ajax.call([{
                methodname: 'report_lpmonitoring_read_report_competency_config',
                args: {competencyframeworkid: self.frameworkid,
                    scaleid: self.scaleid}
            }]);

            requests[0].done(function(context) {
                return templates.render('report_lpmonitoring/scalecolorconfigurationdetail', context).done(function(html, js) {
                    $(self.scaleValuesSelector).html(html);
                    templates.runTemplateJS(js);
                    $("#loaderscalevalues").hide();
                    $(self.scaleValuesSelector).show();
                    $('#submitScaleColorButton').prop('disabled', false);
                });
            }).fail(notification.exception);
        };

        /**
         * Triggered when a scale is selected.
         *
         * @name   scaleChangeHandler
         * @return {Void}
         * @function
         */
        ScaleColorConfiguration.prototype.saveHandler = function() {
            var colors = [], valuescaleid = 0, configid = '0';
            var methodname;
            var self = this,
                requests;

            valuescaleid = 1;
            $('#savecolor input[type=color]').each(function () {
                if ($(this).val() !== '') {
                    colors.push({id : valuescaleid, color : $(this).val()});
                    valuescaleid++;
                }
            });
            colors = JSON.stringify(colors);

            configid = $('#savecolor input[name=configid]').val();
            if (configid === '0') {
                methodname = 'report_lpmonitoring_create_report_competency_config';
            } else {
                methodname = 'report_lpmonitoring_update_report_competency_config';
            }

            requests = ajax.call([{
                methodname: methodname,
                args: {competencyframeworkid: self.frameworkid,
                    scaleid: self.scaleid,
                    scaleconfiguration: colors}
            }]);

            requests[0].done(function(context) {
                if (configid === '0') {
                    $('#savecolor input[name=configid]').val(context.id);
                }
                var selector = document.getElementById('scaleselector'),
                    scale = selector.options[selector.selectedIndex].text;
                str.get_string('colorsforscalesaved', 'report_lpmonitoring', scale).done(function(s) {
                    $('.alert-success .close').trigger('click');
                    notification.addNotification({
                        message: s,
                        type: "success"
                    });
                    $('html, body').animate({scrollTop : 0}, 500);
                }).fail(notification.exception);
            }).fail(notification.exception);

            return false;
        };

        return {
            /**
             * Main initialisation.
             *
             * @param {String} frameworkSelector The framework select box selector.
             * @param {String} scaleSelector The scale select box selector.
             * @param {String} scaleValuesSelector The scaleValuesSelector selector.
             * @return {ScaleColorConfiguration} A new instance of ScaleConfig.
             * @method init
             */
            init: function(frameworkSelector, scaleSelector, scaleValuesSelector) {
                return new ScaleColorConfiguration(frameworkSelector, scaleSelector, scaleValuesSelector);
            }
        };

    });
