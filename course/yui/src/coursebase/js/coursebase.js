/**
 * The coursebase class to provide shared functionality to Modules within
 * Moodle.
 *
 * @module moodle-course-coursebase
 */
var COURSEBASENAME = 'course-coursebase';

var COURSEBASE = function() {
    COURSEBASE.superclass.constructor.apply(this, arguments);
};

/**
 * The coursebase class to provide shared functionality to Modules within
 * Moodle.
 *
 * @class M.course.coursebase
 * @constructor
 */
Y.extend(COURSEBASE, Y.Base, {
    // Registered Modules
    registermodules : [],

    /**
     * Register a new Javascript Module
     *
     * @method register_module
     * @param {Object} The instantiated module to call functions on
     * @chainable
     */
    register_module : function(object) {
        this.registermodules.push(object);

        return this;
    },

    /**
     * Invoke the specified function in all registered modules with the given arguments
     *
     * @method invoke_function
     * @param {String} functionname The name of the function to call
     * @param {mixed} args The argument supplied to the function
     * @chainable
     */
    invoke_function : function(functionname, args) {
        var module;
        for (module in this.registermodules) {
            if (functionname in this.registermodules[module]) {
                this.registermodules[module][functionname](args);
            }
        }

        return this;
    }
}, {
    NAME : COURSEBASENAME,
    ATTRS : {}
});

// Ensure that M.course exists and that coursebase is initialised correctly
M.course = M.course || {};
M.course.coursebase = M.course.coursebase || new COURSEBASE();

// Abstract functions that needs to be defined per format (course/format/somename/format.js)
M.course.format = M.course.format || {};

/**
 * Swap section (should be defined in format.js if requred)
 *
 * @method M.course.format.swap_sections
 * @param {YUI} Y YUI3 instance
 * @param {string} node1 node to swap to
 * @param {string} node2 node to swap with
 * @return {NodeList} section list
 */
M.course.format.swap_sections = M.course.format.swap_sections || function() {
    return null;
};

/**
 * Process sections after ajax response (should be defined in format.js)
 * If some response is expected, we pass it over to format, as it knows better
 * hot to process it.
 *
 * @method M.course.format.process_sections
 * @param {YUI} Y YUI3 instance
 * @param {NodeList} list of sections
 * @param {array} response ajax response
 * @param {string} sectionfrom first affected section
 * @param {string} sectionto last affected section
 */
M.course.format.process_sections = M.course.format.process_sections || function() {
    return null;
};

/**
* Get sections config for this format, for examples see function definition
* in the formats.
*
* @method M.course.format.get_config
* @return {object} section list configuration
*/
M.course.format.get_config = M.course.format.get_config || function() {
    return {
        container_node : null, // compulsory
        container_class : null, // compulsory
        section_wrapper_node : null, // optional
        section_wrapper_class : null, // optional
        section_node : null,  // compulsory
        section_class : null  // compulsory
    };
};

/**
 * Get section list for this format (usually items inside container_node.container_class selector)
 *
 * @method M.course.format.get_section_selector
 * @param {YUI} Y YUI3 instance
 * @return {string} section selector
 */
M.course.format.get_section_selector = M.course.format.get_section_selector || function() {
    var config = M.course.format.get_config();
    if (config.section_node && config.section_class) {
        return config.section_node + '.' + config.section_class;
    }
    Y.log('section_node and section_class are not defined in M.course.format.get_config', 'warn', 'moodle-course-coursebase');
    return null;
};

/**
 * Get section wraper for this format (only used in case when each
 * container_node.container_class node is wrapped in some other element).
 *
 * @method M.course.format.get_section_wrapper
 * @param {YUI} Y YUI3 instance
 * @return {string} section wrapper selector or M.course.format.get_section_selector
 * if section_wrapper_node and section_wrapper_class are not defined in the format config.
 */
M.course.format.get_section_wrapper = M.course.format.get_section_wrapper || function(Y) {
    var config = M.course.format.get_config();
    if (config.section_wrapper_node && config.section_wrapper_class) {
        return config.section_wrapper_node + '.' + config.section_wrapper_class;
    }
    return M.course.format.get_section_selector(Y);
};

/**
 * Get the tag of container node
 *
 * @method M.course.format.get_containernode
 * @return {string} tag of container node.
 */
M.course.format.get_containernode = M.course.format.get_containernode || function() {
    var config = M.course.format.get_config();
    if (config.container_node) {
        return config.container_node;
    } else {
        Y.log('container_node is not defined in M.course.format.get_config', 'warn', 'moodle-course-coursebase');
    }
};

/**
 * Get the class of container node
 *
 * @method M.course.format.get_containerclass
 * @return {string} class of the container node.
 */
M.course.format.get_containerclass = M.course.format.get_containerclass || function() {
    var config = M.course.format.get_config();
    if (config.container_class) {
        return config.container_class;
    } else {
        Y.log('container_class is not defined in M.course.format.get_config', 'warn', 'moodle-course-coursebase');
    }
};

/**
 * Get the tag of draggable node (section wrapper if exists, otherwise section)
 *
 * @method M.course.format.get_sectionwrappernode
 * @return {string} tag of the draggable node.
 */
M.course.format.get_sectionwrappernode = M.course.format.get_sectionwrappernode || function() {
    var config = M.course.format.get_config();
    if (config.section_wrapper_node) {
        return config.section_wrapper_node;
    } else {
        return config.section_node;
    }
};

/**
 * Get the class of draggable node (section wrapper if exists, otherwise section)
 *
 * @method M.course.format.get_sectionwrapperclass
 * @return {string} class of the draggable node.
 */
M.course.format.get_sectionwrapperclass = M.course.format.get_sectionwrapperclass || function() {
    var config = M.course.format.get_config();
    if (config.section_wrapper_class) {
        return config.section_wrapper_class;
    } else {
        return config.section_class;
    }
};

/**
 * Get the tag of section node
 *
 * @method M.course.format.get_sectionnode
 * @return {string} tag of section node.
 */
M.course.format.get_sectionnode = M.course.format.get_sectionnode || function() {
    var config = M.course.format.get_config();
    if (config.section_node) {
        return config.section_node;
    } else {
        Y.log('section_node is not defined in M.course.format.get_config', 'warn', 'moodle-course-coursebase');
    }
};

/**
 * Get the class of section node
 *
 * @method M.course.format.get_sectionclass
 * @return {string} class of the section node.
 */
M.course.format.get_sectionclass = M.course.format.get_sectionclass || function() {
    var config = M.course.format.get_config();
    if (config.section_class) {
        return config.section_class;
    } else {
        Y.log('section_class is not defined in M.course.format.get_config', 'warn', 'moodle-course-coursebase');
    }
};
