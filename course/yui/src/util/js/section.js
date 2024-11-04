/**
 * A collection of utility classes for use with course sections.
 *
 * TODO: remove this module as part of MDL-83627.
 *
 * @module moodle-course-util
 * @submodule moodle-course-util-section
 */

Y.namespace('Moodle.core_course.util.section');

Y.log(
    'YUI Moodle.core_course.util.cm is deprecated. Please, add support_components to your course format.',
    'warn',
    'moodle-course-coursebase'
);

/**
 * A collection of utility classes for use with course sections.
 *
 * @class Moodle.core_course.util.section
 * @static
 */
Y.Moodle.core_course.util.section = {
    CONSTANTS: {
        SECTIONIDPREFIX: 'section-'
    },

    /**
     * Determines the section ID for the provided section.
     *
     * @method getId
     * @param section {Node} The section to find an ID for.
     * @return {Number|false} The ID of the section in question or false if no ID was found.
     */
    getId: function(section) {
        // We perform a simple substitution operation to get the ID.
        var id = section.get('id').replace(
                this.CONSTANTS.SECTIONIDPREFIX, '');

        // Attempt to validate the ID.
        id = parseInt(id, 10);
        if (typeof id === 'number' && isFinite(id)) {
            return id;
        }
        return false;
    }
};
