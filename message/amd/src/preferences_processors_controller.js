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
 * Controls the processors page
 *
 * @module     core_message/preferences_processors_controller
 * @class      preferences_processors_controller
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
    var SELECTORS = {
        PROCESSOR: '[data-processor-name]',
    };

    /**
     * Constructor for the Processor.
     *
     * @param element jQuery object root element of the preference
     * @param int the current user id
     * @return object Processor
     */
    var Processor = function(element, userId) {
        this.root = $(element);
        this.userId = userId;
        this.name = this.root.attr('data-processor-name');
    };

    /**
     * Flag the processor as loading.
     *
     * @method startLoading
     */
    Processor.prototype.startLoading = function() {
        this.root.addClass('loading');
    };

    /**
     * Remove the loading flag for this processor.
     *
     * @method stopLoading
     */
    Processor.prototype.stopLoading = function() {
        this.root.removeClass('loading');
    };

    /**
     * Check if this processor is loading.
     *
     * @method isLoading
     * @return bool
     */
    Processor.prototype.isLoading = function() {
        return this.root.hasClass('loading');
    };

    /**
     * Persist the processor configuration.
     *
     * @method save
     * @return promise
     */
    Processor.prototype.save = function() {
        if (this.isLoading()) {
            return $.Deferred();
        }

        this.startLoading();

        var data = this.root.find('form').serializeArray();
        var request = {
            methodname: 'core_message_message_processor_config_form',
            args: {
                userid: this.userId,
                name: this.name,
                formvalues: data,
            }
        };

        return ajax.call([request])[0]
            .fail(notification.exception)
            .always(function() { this.stopLoading(); }.bind(this));
    };

    /**
     * Constructor for the ProcessorsController.
     *
     * @param element jQuery object root element of the preference
     * @return object ProcessorsController
     */
    var ProcessorsController = function(element) {
        this.root = $(element);
        this.userId = this.root.attr('data-user-id');

        this.root.on('change', function(e) {
            var element = $(e.target).closest(SELECTORS.PROCESSOR);
            var processor = new Processor(element, this.userId);
            processor.save();
        }.bind(this));
    };

    return ProcessorsController;
});
