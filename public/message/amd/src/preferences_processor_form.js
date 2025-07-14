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
 * Manages the processor form on the message preferences page.
 *
 * @module     core_message/preferences_processor_form
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/notification'],
        function($, Ajax, Notification) {
    /**
     * Constructor for the ProcessorForm.
     *
     * @class
     * @param {object} element jQuery object root element of the preference
     */
    var ProcessorForm = function(element) {
        this.root = $(element);
        this.userId = this.root.attr('data-user-id');
        this.name = this.root.attr('data-processor-name');

        this.root.find('form').on('submit', function(e) {
            e.preventDefault();
            this.save().done(function() {
                $(element).trigger('mpp:formsubmitted');
            });
        }.bind(this));
    };

    /**
     * Flag the processor as loading.
     *
     * @method startLoading
     */
    ProcessorForm.prototype.startLoading = function() {
        this.root.addClass('loading');
    };

    /**
     * Remove the loading flag for this processor.
     *
     * @method stopLoading
     */
    ProcessorForm.prototype.stopLoading = function() {
        this.root.removeClass('loading');
    };

    /**
     * Check if this processor is loading.
     *
     * @method isLoading
     * @return {bool}
     */
    ProcessorForm.prototype.isLoading = function() {
        return this.root.hasClass('loading');
    };

    /**
     * Persist the processor configuration.
     *
     * @method save
     * @return {object} jQuery promise
     */
    ProcessorForm.prototype.save = function() {
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

        return Ajax.call([request])[0]
            .fail(Notification.exception)
            .always(function() {
                this.stopLoading();
            }.bind(this));
    };

    return ProcessorForm;
});
