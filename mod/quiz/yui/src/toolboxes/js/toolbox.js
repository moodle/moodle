/* eslint-disable no-unused-vars */
/**
 * Resource and activity toolbox class.
 *
 * This class is responsible for managing AJAX interactions with activities and resources
 * when viewing a course in editing mode.
 *
 * @module moodle-course-toolboxes
 * @namespace M.course.toolboxes
 */

// The CSS classes we use.
var CSS = {
        ACTIVITYINSTANCE: 'activityinstance',
        AVAILABILITYINFODIV: 'div.availabilityinfo',
        CONTENTWITHOUTLINK: 'contentwithoutlink',
        CONDITIONALHIDDEN: 'conditionalhidden',
        DIMCLASS: 'dimmed',
        DIMMEDTEXT: 'dimmed_text',
        EDITINSTRUCTIONS: 'editinstructions',
        EDITINGMAXMARK: 'editor_displayed',
        HIDE: 'hide',
        JOIN: 'page_join',
        MODINDENTCOUNT: 'mod-indent-',
        MODINDENTHUGE: 'mod-indent-huge',
        PAGE: 'page',
        SECTIONHIDDENCLASS: 'hidden',
        SECTIONIDPREFIX: 'section-',
        SELECTMULTIPLE: 'select-multiple',
        SLOT: 'slot',
        SHOW: 'editing_show',
        TITLEEDITOR: 'titleeditor'
    },
    // The CSS selectors we use.
    SELECTOR = {
        ACTIONAREA: '.actions',
        ACTIONLINKTEXT: '.actionlinktext',
        ACTIVITYACTION: 'a.cm-edit-action[data-action], a.editing_maxmark, a.editing_section, input.shuffle_questions',
        ACTIVITYFORM: 'span.instancemaxmarkcontainer form',
        ACTIVITYINSTANCE: '.' + CSS.ACTIVITYINSTANCE,
        SECTIONINSTANCE: '.sectioninstance',
        ACTIVITYLI: 'li.activity, li.section',
        ACTIVITYMAXMARK: 'input[name=maxmark]',
        COMMANDSPAN: '.commands',
        CONTENTAFTERLINK: 'div.contentafterlink',
        CONTENTWITHOUTLINK: 'div.contentwithoutlink',
        DELETESECTIONICON: 'a.editing_delete .icon',
        DESELECTALL: '#questiondeselectall',
        EDITMAXMARK: 'a.editing_maxmark',
        EDITSECTION: 'a.editing_section',
        EDITSECTIONICON: 'a.editing_section .icon',
        EDITSHUFFLEQUESTIONSACTION: 'input.cm-edit-action[data-action]',
        EDITSHUFFLEAREA: '.instanceshufflequestions .shuffle-progress',
        HIDE: 'a.editing_hide',
        HIGHLIGHT: 'a.editing_highlight',
        INSTANCENAME: 'span.instancename',
        INSTANCEMAXMARK: 'span.instancemaxmark',
        INSTANCESECTION: 'span.instancesection',
        INSTANCESECTIONAREA: 'div.section-heading',
        MODINDENTDIV: '.mod-indent',
        MODINDENTOUTER: '.mod-indent-outer',
        NUMQUESTIONS: '.numberofquestions',
        PAGECONTENT: 'div#page-content',
        PAGELI: 'li.page',
        SECTIONLI: 'li.section',
        SECTIONUL: 'ul.section',
        SECTIONFORM: '.instancesectioncontainer form',
        SECTIONINPUT: 'input[name=section]',
        SELECTMULTIPLEBUTTON: '#selectmultiplecommand',
        SELECTMULTIPLECANCELBUTTON: '#selectmultiplecancelcommand',
        SELECTMULTIPLECHECKBOX: '.select-multiple-checkbox',
        SELECTMULTIPLEDELETEBUTTON: '#selectmultipledeletecommand',
        SELECTALL: '#questionselectall',
        SHOW: 'a.' + CSS.SHOW,
        SLOTLI: 'li.slot',
        SUMMARKS: '.mod_quiz_summarks'
    },
    BODY = Y.one(document.body);

// Setup the basic namespace.
M.mod_quiz = M.mod_quiz || {};

/**
 * The toolbox class is a generic class which should never be directly
 * instantiated. Please extend it instead.
 *
 * @class toolbox
 * @constructor
 * @protected
 * @extends Base
 */
var TOOLBOX = function() {
    TOOLBOX.superclass.constructor.apply(this, arguments);
};

Y.extend(TOOLBOX, Y.Base, {
    /**
     * Send a request using the REST API
     *
     * @method send_request
     * @param {Object} data The data to submit with the AJAX request
     * @param {Node} [statusspinner] A statusspinner which may contain a section loader
     * @param {Function} success_callback The callback to use on success
     * @param {Object} [optionalconfig] Any additional configuration to submit
     * @chainable
     */
    send_request: function(data, statusspinner, success_callback, optionalconfig) {
        // Default data structure
        if (!data) {
            data = {};
        }

        // Handle any variables which we must pass back through to
        var pageparams = this.get('config').pageparams,
            varname;
        for (varname in pageparams) {
            data[varname] = pageparams[varname];
        }

        data.sesskey = M.cfg.sesskey;
        data.courseid = this.get('courseid');
        data.quizid = this.get('quizid');

        var uri = M.cfg.wwwroot + this.get('ajaxurl');

        // Define the configuration to send with the request
        var responsetext = [];
        var config = {
            method: 'POST',
            data: data,
            on: {
                success: function(tid, response) {
                    try {
                        responsetext = Y.JSON.parse(response.responseText);
                        if (responsetext.error) {
                            new M.core.ajaxException(responsetext);
                        }
                    } catch (e) {
                        // Ignore.
                    }

                    // Run the callback if we have one.
                    if (responsetext.hasOwnProperty('newsummarks')) {
                        Y.one(SELECTOR.SUMMARKS).setHTML(responsetext.newsummarks);
                    }
                    if (responsetext.hasOwnProperty('newnumquestions')) {
                        Y.one(SELECTOR.NUMQUESTIONS).setHTML(
                                M.util.get_string('numquestionsx', 'quiz', responsetext.newnumquestions)
                            );
                    }
                    if (success_callback) {
                        Y.bind(success_callback, this, responsetext)();
                    }

                    if (statusspinner) {
                        window.setTimeout(function() {
                            statusspinner.hide();
                        }, 400);
                    }
                },
                failure: function(tid, response) {
                    if (statusspinner) {
                        statusspinner.hide();
                    }
                    new M.core.ajaxException(response);
                }
            },
            context: this
        };

        // Apply optional config
        if (optionalconfig) {
            for (varname in optionalconfig) {
                config[varname] = optionalconfig[varname];
            }
        }

        if (statusspinner) {
            statusspinner.show();
        }

        // Send the request
        Y.io(uri, config);
        return this;
    }
},
{
    NAME: 'mod_quiz-toolbox',
    ATTRS: {
        /**
         * The ID of the Moodle Course being edited.
         *
         * @attribute courseid
         * @default 0
         * @type Number
         */
        courseid: {
            'value': 0
        },

        /**
         * The Moodle course format.
         *
         * @attribute format
         * @default 'topics'
         * @type String
         */
        quizid: {
            'value': 0
        },
        /**
         * The URL to use when submitting requests.
         * @attribute ajaxurl
         * @default null
         * @type String
         */
        ajaxurl: {
            'value': null
        },
        /**
         * Any additional configuration passed when creating the instance.
         *
         * @attribute config
         * @default {}
         * @type Object
         */
        config: {
            'value': {}
        }
    }
}
);
