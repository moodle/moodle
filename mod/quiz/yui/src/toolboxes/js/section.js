/**
 * Resource and activity toolbox class.
 *
 * This class is responsible for managing AJAX interactions with activities and resources
 * when viewing a course in editing mode.
 *
 * @module moodle-mod_quiz-toolboxes
 * @namespace M.mod_quiz.toolboxes
 */

/**
 * Section toolbox class.
 *
 * This class is responsible for managing AJAX interactions with sections
 * when viewing a course in editing mode.
 *
 * @class section
 * @constructor
 * @extends M.mod_quiz.toolboxes.toolbox
 */
var SECTIONTOOLBOX = function() {
    SECTIONTOOLBOX.superclass.constructor.apply(this, arguments);
};

Y.extend(SECTIONTOOLBOX, TOOLBOX, {
    /**
     * Initialize the section toolboxes module.
     *
     * Updates all span.commands with relevant handlers and other required changes.
     *
     * @method initializer
     * @protected
     */
    initializer : function() {
        M.mod_quiz.quizbase.register_module(this);
    }
},  {
    NAME : 'mod_quiz-section-toolbox',
    ATTRS : {
        courseid : {
            'value' : 0
        },
        quizid : {
            'value' : 0
        }
    }
});

M.mod_quiz.init_section_toolbox = function(config) {
    return new SECTIONTOOLBOX(config);
};
