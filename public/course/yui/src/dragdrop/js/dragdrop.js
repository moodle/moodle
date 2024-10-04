/* eslint-disable no-unused-vars */
/**
 * Drag and Drop for course sections and course modules.
 *
 * TODO: remove this module as part of MDL-83627.
 *
 * @module moodle-course-dragdrop
 */

Y.log(
    'YUI moodle-course-dragdrop is deprecated. Please, add support_components to your course format.',
    'warn',
    'moodle-course-coursebase'
);

var CSS = {
    ACTIONAREA: '.actions',
    ACTIVITY: 'activity',
    ACTIVITYINSTANCE: 'activityinstance',
    CONTENT: 'content',
    COURSECONTENT: 'course-content',
    EDITINGMOVE: 'editing_move',
    ICONCLASS: 'iconsmall',
    JUMPMENU: 'jumpmenu',
    LEFT: 'left',
    LIGHTBOX: 'lightbox',
    MOVEDOWN: 'movedown',
    MOVEUP: 'moveup',
    PAGECONTENT: 'page-content',
    RIGHT: 'right',
    SECTION: 'section',
    SECTIONADDMENUS: 'section_add_menus',
    SECTIONHANDLE: 'section-handle',
    SUMMARY: 'summary',
    SECTIONDRAGGABLE: 'sectiondraggable'
};

M.course = M.course || {};
