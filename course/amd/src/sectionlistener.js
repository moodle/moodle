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
 * Define listeners related to sections.
 *
 * @module     core_course/sectionlistener
 * @package    core_course
 * @copyright  2020 University of Nottingham
 * @author     Neill Magill <neill.magill@nottingham.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    /**
     * Processes an updated event.
     *
     * We only want to handle events related to a section, and not activities.
     *
     * @param {Event} e The updated event
     */
    var processUpdatedEvent = function(e) {
        var editable = $(this);
        if (editable.data('itemtype') === 'sectionname') {
            var config = M.course.format.get_config();
            // A section name got updated.
            var section = editable.closest('.' + config.section_class);
            updateSectionName(section, e.ajaxreturn.value);
        }
        // Ignore other types of inplace updates, for example activity name changes.
    };

    /**
     * Updates the name of the section in places outside of the inplace editable field.
     *
     * @param {jQuery} section The section that was updated.
     * @param {String} newname The new name for the section.
     */
    var updateSectionName = function(section, newname) {
        section.attr('aria-label', newname);
        section.find('.hidden.sectionname').text(newname);
    };

    $('body').on('updated', '.inplaceeditable', processUpdatedEvent);
});