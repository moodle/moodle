// Javascript functions for course format

M.course = M.course || {};

M.course.format = M.course.format || {};

/**
 * Get section list for this format
 *
 * @param {YUI} Y YUI3 instance
 * @return {string} section list selector
 */
M.course.format.get_section_selector = function(Y) {
    return 'li.section';
}

/**
 * Swap section
 *
 * @param {YUI} Y YUI3 instance
 * @param {string} node1 node to swap to
 * @param {string} node2 node to swap with
 * @return {NodeList} section list
 */
M.course.format.swap_sections = function(Y, node1, node2) {
    var CSS = {
        COURSECONTENT : 'course-content',
        LEFT : 'left',
        RIGHT : 'right',
        SECTIONADDMENUS : 'section_add_menus',
        WEEKDATES: 'weekdates'
    };

    var sectionlist = Y.Node.all('.'+CSS.COURSECONTENT+' '+M.course.format.get_section_selector(Y));
    // Swap left block
    sectionlist.item(node1).one('.'+CSS.LEFT).swap(sectionlist.item(node2).one('.'+CSS.LEFT));
    // Swap right block
    sectionlist.item(node1).one('.'+CSS.RIGHT).swap(sectionlist.item(node2).one('.'+CSS.RIGHT));
    // Swap menus
    sectionlist.item(node1).one('.'+CSS.SECTIONADDMENUS).swap(sectionlist.item(node2).one('.'+CSS.SECTIONADDMENUS));
    // Swap week dates
    sectionlist.item(node1).one('.'+CSS.WEEKDATES).swap(sectionlist.item(node2).one('.'+CSS.WEEKDATES));
}
