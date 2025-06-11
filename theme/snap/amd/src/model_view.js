/**
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Model view handling.
 */
define(['jquery', 'core/notification', 'core/templates', 'core/log'], function($, notification, templates, log) {
    return function(element, templateName, model) {

        /**
         * Make the target's attributes identical to the sources.
         * @param {jQuery} src
         * @param {jQuery} target
         * @param {bool} skipid
         */
        var cloneAttributes = function(src, target, skipid) {
            var srcKeys = [];
            $.each($(src)[0].attributes, function() {
                if (this.name == 'data-model') {
                    return true;
                }
                if (skipid && this.name == 'id') {
                    return true;
                }
                srcKeys.push(this.name);
                $(target).attr(this.name, this.value);
                return true;
            });
            $.each($(target)[0].attributes, function() {
                if (srcKeys.indexOf(this.name) == -1) {
                    $(target).removeAttr(this.name);
                }
            });
        };

        /**
         * Update element with new model.
         * @param {null|object} newModel
         * @param {null|function} callback
         */
        var updateModel = function(newModel, callback) {
            if (!newModel) {
                log.debug('Using data element for model');
                newModel = $(element).data('model');
            } else {
                log.debug('Using object for model');
            }

            // Update model.
            for (var m in newModel) {
                model[m] = newModel[m];
            }

            $(element).data('model', model);

            // Update element.
            templates.render(templateName, model)
                .done(function(result) {
                    // Using replaceWith can be jerky as you are taking the element out of the dom instead of replacing
                    // its content. So instead of $(cardEl).replaceWith(result); we parse the html from the template and
                    // update the html of the card.
                    var tempEl = $($.parseHTML(result));
                    $(element).html(tempEl.html());
                    cloneAttributes(tempEl, $(element), true);
                    if (typeof (callback) === 'function') {
                        callback();
                    }
                    $(element).trigger('modelUpdated');
                }).fail(notification.exception);
        };

        /**
         * Main initialising function - create custom modelUpdate function on element.
         */
        var init = function() {
            if (!model && $(element).data('model')) {
                model = $(element).data('model');
            }
            if ($(element).data('modelInitialised') != 1) {
                $(element).on("modelUpdate", function(event, newModel, callback) {
                    updateModel(newModel, callback);
                });
            }
            $(element).data('modelInitialised', 1);
        };

        init();
    };
});
