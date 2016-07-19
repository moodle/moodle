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
 * Controls the processors config section on the message preference page
 *
 * @module     core_message/preferences_processors_controller
 * @class      preferences_processors_controller
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery', 'core/ajax', 'core/notification', 'core_message/processor_form'], function($, ajax, notification, ProcessorForm) {
    var SELECTORS = {
        PROCESSOR: '[data-processor-name]',
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
            var processor = new ProcessorForm(element, this.userId);
            processor.save();
        }.bind(this));
    };

    return ProcessorsController;
});
