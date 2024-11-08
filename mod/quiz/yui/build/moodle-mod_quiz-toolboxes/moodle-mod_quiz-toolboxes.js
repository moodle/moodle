YUI.add('moodle-mod_quiz-toolboxes', function (Y, NAME) {

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
/**
 * Resource and activity toolbox class.
 *
 * This class is responsible for managing AJAX interactions with activities and resources
 * when viewing a quiz in editing mode.
 *
 * @module mod_quiz-resource-toolbox
 * @namespace M.mod_quiz.resource_toolbox
 */

/**
 * Resource and activity toolbox class.
 *
 * This is a class extending TOOLBOX containing code specific to resources
 *
 * This class is responsible for managing AJAX interactions with activities and resources
 * when viewing a quiz in editing mode.
 *
 * @class resources
 * @constructor
 * @extends M.course.toolboxes.toolbox
 */
var RESOURCETOOLBOX = function() {
    RESOURCETOOLBOX.superclass.constructor.apply(this, arguments);
};

Y.extend(RESOURCETOOLBOX, TOOLBOX, {
    /**
     * An Array of events added when editing a max mark field.
     * These should all be detached when editing is complete.
     *
     * @property editmaxmarkevents
     * @protected
     * @type Array
     * @protected
     */
    editmaxmarkevents: [],

    /**
     *
     */
    NODE_PAGE: 1,
    NODE_SLOT: 2,
    NODE_JOIN: 3,

    /**
     * Initialize the resource toolbox
     *
     * For each activity the commands are updated and a reference to the activity is attached.
     * This way it doesn't matter where the commands are going to called from they have a reference to the
     * activity that they relate to.
     * This is essential as some of the actions are displayed in an actionmenu which removes them from the
     * page flow.
     *
     * This function also creates a single event delegate to manage all AJAX actions for all activities on
     * the page.
     *
     * @method initializer
     * @protected
     */
    initializer: function() {
        M.mod_quiz.quizbase.register_module(this);
        Y.delegate('click', this.handle_data_action, BODY, SELECTOR.ACTIVITYACTION, this);
        Y.delegate('click', this.handle_data_action, BODY, SELECTOR.DEPENDENCY_LINK, this);
        this.initialise_select_multiple();
    },

    /**
     * Initialize the select multiple options
     *
     * Add actions to the buttons that enable multiple slots to be selected and managed at once.
     *
     * @method initialise_select_multiple
     * @protected
     */
    initialise_select_multiple: function() {
        // Click select multiple button to show the select all options.
        Y.one(SELECTOR.SELECTMULTIPLEBUTTON).on('click', function(e) {
            e.preventDefault();
            Y.one('body').addClass(CSS.SELECTMULTIPLE);
        });

        // Click cancel button to show the select all options.
        Y.one(SELECTOR.SELECTMULTIPLECANCELBUTTON).on('click', function(e) {
            e.preventDefault();
            Y.one('body').removeClass(CSS.SELECTMULTIPLE);
        });

        // Assign the delete method to the delete multiple button.
        Y.delegate('click', this.delete_multiple_action, BODY, SELECTOR.SELECTMULTIPLEDELETEBUTTON, this);
    },

    /**
     * Handles the delegation event. When this is fired someone has triggered an action.
     *
     * Note not all actions will result in an AJAX enhancement.
     *
     * @protected
     * @method handle_data_action
     * @param {EventFacade} ev The event that was triggered.
     * @returns {boolean}
     */
    handle_data_action: function(ev) {
        // We need to get the anchor element that triggered this event.
        var node = ev.target;
        if (!node.test('a')) {
            node = node.ancestor(SELECTOR.ACTIVITYACTION);
        }

        // From the anchor we can get both the activity (added during initialisation) and the action being
        // performed (added by the UI as a data attribute).
        var action = node.getData('action'),
            activity = node.ancestor(SELECTOR.ACTIVITYLI);

        if (!node.test('a') || !action || !activity) {
            // It wasn't a valid action node.
            return;
        }

        // Switch based upon the action and do the desired thing.
        switch (action) {
            case 'editmaxmark':
                // The user wishes to edit the maxmark of the resource.
                this.edit_maxmark(ev, node, activity, action);
                break;
            case 'delete':
                // The user is deleting the activity.
                this.delete_with_confirmation(ev, node, activity, action);
                break;
            case 'addpagebreak':
            case 'removepagebreak':
                // The user is adding or removing a page break.
                this.update_page_break(ev, node, activity, action);
                break;
            case 'adddependency':
            case 'removedependency':
                // The user is adding or removing a dependency between questions.
                this.update_dependency(ev, node, activity, action);
                break;
            default:
                // Nothing to do here!
                break;
        }
    },

    /**
     * Add a loading icon to the specified activity.
     *
     * The icon is added within the action area.
     *
     * @method add_spinner
     * @param {Node} activity The activity to add a loading icon to
     * @return {Node|null} The newly created icon, or null if the action area was not found.
     */
    add_spinner: function(activity) {
        var actionarea = activity.one(SELECTOR.ACTIONAREA);
        if (actionarea) {
            return M.util.add_spinner(Y, actionarea);
        }
        return null;
    },

    /**
     * Deletes the given activity or resource after confirmation.
     *
     * @protected
     * @method delete_with_confirmation
     * @param {EventFacade} ev The event that was fired.
     * @param {Node} button The button that triggered this action.
     * @param {Node} activity The activity node that this action will be performed on.
     */
    delete_with_confirmation: function(ev, button, activity) {
        // Prevent the default button action.
        ev.preventDefault();

        // Get the element we're working on.
        var element = activity;
        // Create confirm string (different if element has or does not have name)
        var qtypename = M.util.get_string(
            'pluginname',
            'qtype_' + element.getAttribute('class').match(/qtype_([^\s]*)/)[1]
        );

        // Create the confirmation dialogue.
        require(['core/notification'], function(Notification) {
            Notification.saveCancelPromise(
                M.util.get_string('confirm', 'moodle'),
                M.util.get_string('confirmremovequestion', 'quiz', qtypename),
                M.util.get_string('yes', 'moodle')
            ).then(function() {
                var spinner = this.add_spinner(element);
                var data = {
                    'class': 'resource',
                    'action': 'DELETE',
                    'id': Y.Moodle.mod_quiz.util.slot.getId(element)
                };
                this.send_request(data, spinner, function(response) {
                    if (response.deleted) {
                        // Actually remove the element.
                        Y.Moodle.mod_quiz.util.slot.remove(element);
                        this.reorganise_edit_page();
                        if (M.core.actionmenu && M.core.actionmenu.instance) {
                            M.core.actionmenu.instance.hideMenu(ev);
                        }
                    }
                });

                return;
            }.bind(this)).catch(function() {
                // User cancelled.
            });
        }.bind(this));
    },

    /**
     * Finds the section that would become empty if we remove the selected slots.
     *
     * @protected
     * @method find_sections_that_would_become_empty
     * @returns {String} The name of the first section found
     */
    find_sections_that_would_become_empty: function() {
        var section;
        var sectionnodes = Y.all(SELECTOR.SECTIONLI);

        if (sectionnodes.size() > 1) {
            sectionnodes.some(function(node) {
                var sectionname = node.one(SELECTOR.INSTANCESECTION).getContent();
                var checked = node.all(SELECTOR.SELECTMULTIPLECHECKBOX + ':checked');
                var unchecked = node.all(SELECTOR.SELECTMULTIPLECHECKBOX + ':not(:checked)');

                if (!checked.isEmpty() && unchecked.isEmpty()) {
                    section = sectionname;
                }

                return section;
            });
        }

        return section;
    },

    /**
     * Takes care of what needs to happen when the user clicks on the delete multiple button.
     *
     * @protected
     * @method delete_multiple_action
     * @param {EventFacade} ev The event that was fired.
     */
    delete_multiple_action: function(ev) {
        var problemsection = this.find_sections_that_would_become_empty();

        if (typeof problemsection !== 'undefined') {
            require(['core/notification'], function(Notification) {
                Notification.alert(
                    M.util.get_string('cannotremoveslots', 'quiz'),
                    M.util.get_string('cannotremoveallsectionslots', 'quiz', problemsection)
                );
            });
        } else {
            this.delete_multiple_with_confirmation(ev);
        }
    },

    /**
     * Deletes the given activities or resources after confirmation.
     *
     * @protected
     * @method delete_multiple_with_confirmation
     * @param {EventFacade} ev The event that was fired.
     */
    delete_multiple_with_confirmation: function(ev) {
        ev.preventDefault();

        var ids = '';
        var slots = [];
        Y.all(SELECTOR.SELECTMULTIPLECHECKBOX + ':checked').each(function(node) {
            var slot = Y.Moodle.mod_quiz.util.slot.getSlotFromComponent(node);
            ids += ids === '' ? '' : ',';
            ids += Y.Moodle.mod_quiz.util.slot.getId(slot);
            slots.push(slot);
        });
        var element = Y.one('div.mod-quiz-edit-content');

        // Do nothing if no slots are selected.
        if (!slots || !slots.length) {
            return;
        }

        require(['core/notification'], function(Notification) {
            Notification.saveCancelPromise(
                M.util.get_string('confirm', 'moodle'),
                M.util.get_string('areyousureremoveselected', 'quiz'),
                M.util.get_string('yes', 'moodle')
            ).then(function() {
                var spinner = this.add_spinner(element);
                var data = {
                    'class': 'resource',
                    field: 'deletemultiple',
                    ids: ids
                };
                // Delete items on server.
                this.send_request(data, spinner, function(response) {
                    // Delete locally if deleted on server.
                    if (response.deleted) {
                        // Actually remove the element.
                        Y.all(SELECTOR.SELECTMULTIPLECHECKBOX + ':checked').each(function(node) {
                            Y.Moodle.mod_quiz.util.slot.remove(node.ancestor('li.activity'));
                        });
                        // Update the page numbers and sections.
                        this.reorganise_edit_page();

                        // Remove the select multiple options.
                        Y.one('body').removeClass(CSS.SELECTMULTIPLE);
                    }
                });

                return;
            }.bind(this)).catch(function() {
                // User cancelled.
            });
        }.bind(this));
    },

    /**
     * Edit the maxmark for the resource
     *
     * @protected
     * @method edit_maxmark
     * @param {EventFacade} ev The event that was fired.
     * @param {Node} button The button that triggered this action.
     * @param {Node} activity The activity node that this action will be performed on.
     * @param {String} action The action that has been requested.
     * @return Boolean
     */
    edit_maxmark: function(ev, button, activity) {
        // Get the element we're working on
        var instancemaxmark = activity.one(SELECTOR.INSTANCEMAXMARK),
            instance = activity.one(SELECTOR.ACTIVITYINSTANCE),
            currentmaxmark = instancemaxmark.get('firstChild'),
            oldmaxmark = currentmaxmark.get('data'),
            maxmarktext = oldmaxmark,
            thisevent,
            anchor = instancemaxmark, // Grab the anchor so that we can swap it with the edit form.
            data = {
                'class': 'resource',
                'field': 'getmaxmark',
                'id': Y.Moodle.mod_quiz.util.slot.getId(activity)
            };

        // Prevent the default actions.
        ev.preventDefault();

        this.send_request(data, null, function(response) {
            if (M.core.actionmenu && M.core.actionmenu.instance) {
                M.core.actionmenu.instance.hideMenu(ev);
            }

            // Try to retrieve the existing string from the server.
            if (response.instancemaxmark) {
                maxmarktext = response.instancemaxmark;
            }

            // Create the editor and submit button.
            var editform = Y.Node.create('<form action="#" />');
            var editinstructions = Y.Node.create('<span class="' + CSS.EDITINSTRUCTIONS + '" id="id_editinstructions" />')
                .set('innerHTML', M.util.get_string('edittitleinstructions', 'moodle'));
            var editor = Y.Node.create('<input name="maxmark" type="text" class="' + CSS.TITLEEDITOR + '" />').setAttrs({
                'value': maxmarktext,
                'autocomplete': 'off',
                'aria-describedby': 'id_editinstructions',
                'maxLength': '12',
                'size': parseInt(this.get('config').questiondecimalpoints, 10) + 2
            });

            // Clear the existing content and put the editor in.
            editform.appendChild(editor);
            editform.setData('anchor', anchor);
            instance.insert(editinstructions, 'before');
            anchor.replace(editform);

            // We hide various components whilst editing:
            activity.addClass(CSS.EDITINGMAXMARK);

            // Focus and select the editor text.
            editor.focus().select();

            // Cancel the edit if we lose focus or the escape key is pressed.
            thisevent = editor.on('blur', this.edit_maxmark_cancel, this, activity, false);
            this.editmaxmarkevents.push(thisevent);
            thisevent = editor.on('key', this.edit_maxmark_cancel, 'esc', this, activity, true);
            this.editmaxmarkevents.push(thisevent);

            // Handle form submission.
            thisevent = editform.on('submit', this.edit_maxmark_submit, this, activity, oldmaxmark);
            this.editmaxmarkevents.push(thisevent);
        });
    },

    /**
     * Handles the submit event when editing the activity or resources maxmark.
     *
     * @protected
     * @method edit_maxmark_submit
     * @param {EventFacade} ev The event that triggered this.
     * @param {Node} activity The activity whose maxmark we are altering.
     * @param {String} originalmaxmark The original maxmark the activity or resource had.
     */
    edit_maxmark_submit: function(ev, activity, originalmaxmark) {
        // We don't actually want to submit anything.
        ev.preventDefault();
        var newmaxmark = Y.Lang.trim(activity.one(SELECTOR.ACTIVITYFORM + ' ' + SELECTOR.ACTIVITYMAXMARK).get('value'));
        var spinner = this.add_spinner(activity);
        this.edit_maxmark_clear(activity);
        activity.one(SELECTOR.INSTANCEMAXMARK).setContent(newmaxmark);
        if (newmaxmark !== null && newmaxmark !== "" && newmaxmark !== originalmaxmark) {
            var data = {
                'class': 'resource',
                'field': 'updatemaxmark',
                'maxmark': newmaxmark,
                'id': Y.Moodle.mod_quiz.util.slot.getId(activity)
            };
            this.send_request(data, spinner, function(response) {
                if (response.instancemaxmark) {
                    activity.one(SELECTOR.INSTANCEMAXMARK).setContent(response.instancemaxmark);
                }
            });
        }
    },

    /**
     * Handles the cancel event when editing the activity or resources maxmark.
     *
     * @protected
     * @method edit_maxmark_cancel
     * @param {EventFacade} ev The event that triggered this.
     * @param {Node} activity The activity whose maxmark we are altering.
     * @param {Boolean} preventdefault If true we should prevent the default action from occuring.
     */
    edit_maxmark_cancel: function(ev, activity, preventdefault) {
        if (preventdefault) {
            ev.preventDefault();
        }
        this.edit_maxmark_clear(activity);
    },

    /**
     * Handles clearing the editing UI and returning things to the original state they were in.
     *
     * @protected
     * @method edit_maxmark_clear
     * @param {Node} activity  The activity whose maxmark we were altering.
     */
    edit_maxmark_clear: function(activity) {
        // Detach all listen events to prevent duplicate triggers
        new Y.EventHandle(this.editmaxmarkevents).detach();

        var editform = activity.one(SELECTOR.ACTIVITYFORM),
            instructions = activity.one('#id_editinstructions');
        if (editform) {
            editform.replace(editform.getData('anchor'));
        }
        if (instructions) {
            instructions.remove();
        }

        // Remove the editing class again to revert the display.
        activity.removeClass(CSS.EDITINGMAXMARK);

        // Refocus the link which was clicked originally so the user can continue using keyboard nav.
        Y.later(100, this, function() {
            activity.one(SELECTOR.EDITMAXMARK).focus();
        });

        // TODO MDL-50768 This hack is to keep Behat happy until they release a version of
        // MinkSelenium2Driver that fixes
        // https://github.com/Behat/MinkSelenium2Driver/issues/80.
        if (!Y.one('input[name=maxmark')) {
            Y.one('body').append('<input type="text" name="maxmark" style="display: none">');
        }
    },

    /**
     * Joins or separates the given slot with the page of the previous slot. Reorders the pages of
     * the other slots
     *
     * @protected
     * @method update_page_break
     * @param {EventFacade} ev The event that was fired.
     * @param {Node} button The button that triggered this action.
     * @param {Node} activity The activity node that this action will be performed on.
     * @param {String} action The action, addpagebreak or removepagebreak.
     * @chainable
     */
    update_page_break: function(ev, button, activity, action) {
        // Prevent the default button action
        ev.preventDefault();

        var nextactivity = activity.next('li.activity.slot');
        var spinner = this.add_spinner(nextactivity);
        var value = action === 'removepagebreak' ? 1 : 2;

        var data = {
            'class': 'resource',
            'field': 'updatepagebreak',
            'id':    Y.Moodle.mod_quiz.util.slot.getId(nextactivity),
            'value': value
        };

        this.send_request(data, spinner, function(response) {
            if (response.slots) {
                if (action === 'addpagebreak') {
                    Y.Moodle.mod_quiz.util.page.add(activity);
                } else {
                    var page = activity.next(Y.Moodle.mod_quiz.util.page.SELECTORS.PAGE);
                    Y.Moodle.mod_quiz.util.page.remove(page, true);
                }
                this.reorganise_edit_page();
            }
        });

        return this;
    },

    /**
     * Updates a slot to either require the question in the previous slot to
     * have been answered, or not,
     *
     * @protected
     * @method update_page_break
     * @param {EventFacade} ev The event that was fired.
     * @param {Node} button The button that triggered this action.
     * @param {Node} activity The activity node that this action will be performed on.
     * @param {String} action The action, adddependency or removedependency.
     * @chainable
     */
    update_dependency: function(ev, button, activity, action) {
        // Prevent the default button action.
        ev.preventDefault();
        var spinner = this.add_spinner(activity);

        var data = {
            'class': 'resource',
            'field': 'updatedependency',
            'id':    Y.Moodle.mod_quiz.util.slot.getId(activity),
            'value': action === 'adddependency' ? 1 : 0
        };

        this.send_request(data, spinner, function(response) {
            if (response.hasOwnProperty('requireprevious')) {
                Y.Moodle.mod_quiz.util.slot.updateDependencyIcon(activity, response.requireprevious);
            }
        });

        return this;
    },

    /**
     * Reorganise the UI after every edit action.
     *
     * @protected
     * @method reorganise_edit_page
     */
    reorganise_edit_page: function() {
        Y.Moodle.mod_quiz.util.slot.reorderSlots();
        Y.Moodle.mod_quiz.util.slot.reorderPageBreaks();
        Y.Moodle.mod_quiz.util.page.reorderPages();
        Y.Moodle.mod_quiz.util.slot.updateOneSlotSections();
        Y.Moodle.mod_quiz.util.slot.updateAllDependencyIcons();
    },

    NAME: 'mod_quiz-resource-toolbox',
    ATTRS: {
        courseid: {
            'value': 0
        },
        quizid: {
            'value': 0
        }
    }

});

M.mod_quiz.resource_toolbox = null;
M.mod_quiz.init_resource_toolbox = function(config) {
    M.mod_quiz.resource_toolbox = new RESOURCETOOLBOX(config);
    return M.mod_quiz.resource_toolbox;
};
/**
 * Section toolbox class.
 *
 * This class is responsible for managing AJAX interactions with sections
 * when adding, editing, removing section headings.
 *
 * @module moodle-mod_quiz-toolboxes
 * @namespace M.mod_quiz.toolboxes
 */

/**
 * Section toolbox class.
 *
 * This class is responsible for managing AJAX interactions with sections
 * when adding, editing, removing section headings when editing a quiz.
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
     * An Array of events added when editing a max mark field.
     * These should all be detached when editing is complete.
     *
     * @property editsectionevents
     * @protected
     * @type Array
     * @protected
     */
    editsectionevents: [],

    /**
     * Initialize the section toolboxes module.
     *
     * Updates all span.commands with relevant handlers and other required changes.
     *
     * @method initializer
     * @protected
     */
    initializer: function() {
        M.mod_quiz.quizbase.register_module(this);

        BODY.delegate('key', this.handle_data_action, 'down:enter', SELECTOR.ACTIVITYACTION, this);
        Y.delegate('click', this.handle_data_action, BODY, SELECTOR.ACTIVITYACTION, this);
        Y.delegate('change', this.handle_data_action, BODY, SELECTOR.EDITSHUFFLEQUESTIONSACTION, this);
    },

    /**
     * Handles the delegation event. When this is fired someone has triggered an action.
     *
     * Note not all actions will result in an AJAX enhancement.
     *
     * @protected
     * @method handle_data_action
     * @param {EventFacade} ev The event that was triggered.
     * @returns {boolean}
     */
    handle_data_action: function(ev) {
        // We need to get the anchor element that triggered this event.
        var node = ev.target;
        if (!node.test('a') && !node.test('input[data-action]')) {
            node = node.ancestor(SELECTOR.ACTIVITYACTION);
        }

        // From the anchor we can get both the activity (added during initialisation) and the action being
        // performed (added by the UI as a data attribute).
        var action = node.getData('action'),
            activity = node.ancestor(SELECTOR.ACTIVITYLI);

        if ((!node.test('a') && !node.test('input[data-action]')) || !action || !activity) {
            // It wasn't a valid action node.
            return;
        }

        // Switch based upon the action and do the desired thing.
        switch (action) {
            case 'edit_section_title':
                // The user wishes to edit the section headings.
                this.edit_section_title(ev, node, activity, action);
                break;
            case 'shuffle_questions':
                // The user wishes to edit the shuffle questions of the section (resource).
                this.edit_shuffle_questions(ev, node, activity, action);
                break;
            case 'deletesection':
                // The user is deleting the activity.
                this.delete_section_with_confirmation(ev, node, activity, action);
                break;
            default:
                // Nothing to do here!
                break;
        }
    },

    /**
     * Deletes the given section heading after confirmation.
     *
     * @protected
     * @method delete_section_with_confirmation
     * @param {EventFacade} ev The event that was fired.
     * @param {Node} button The button that triggered this action.
     * @param {Node} activity The activity node that this action will be performed on.
     * @chainable
     */
    delete_section_with_confirmation: function(ev, button, activity) {
        ev.preventDefault();
        require(['core/notification'], function(Notification) {
            Notification.saveCancelPromise(
                M.util.get_string('confirm', 'moodle'),
                M.util.get_string('confirmremovesectionheading', 'quiz', activity.getData('sectionname')),
                M.util.get_string('yes', 'moodle')
            ).then(function() {
                var spinner = M.util.add_spinner(Y, activity.one(SELECTOR.ACTIONAREA));
                var data = {
                    'class': 'section',
                    'action': 'DELETE',
                    'id': activity.get('id').replace('section-', '')
                };
                this.send_request(data, spinner, function(response) {
                    if (response.deleted) {
                        window.location.reload(true);
                    }
                });

                return;
            }.bind(this)).catch(function() {
                // User cancelled.
            });
        }.bind(this));
    },

    /**
     * Edit the edit section title for the section
     *
     * @protected
     * @method edit_section_title
     * @param {EventFacade} ev The event that was fired.
     * @param {Node} button The button that triggered this action.
     * @param {Node} activity The activity node that this action will be performed on.
     * @param {String} action The action that has been requested.
     * @return Boolean
     */
    edit_section_title: function(ev, button, activity) {
        // Get the element we're working on
        var activityid = activity.get('id').replace('section-', ''),
            instancesection = activity.one(SELECTOR.INSTANCESECTION),
            thisevent,
            anchor = instancesection, // Grab the anchor so that we can swap it with the edit form.
            data = {
                'class': 'section',
                'field': 'getsectiontitle',
                'id':    activityid
            };

        // Prevent the default actions.
        ev.preventDefault();

        this.send_request(data, null, function(response) {
            // Try to retrieve the existing string from the server.
            var oldtext = response.instancesection;

            // Create the editor and submit button.
            var editform = Y.Node.create('<form action="#" />');
            var editinstructions = Y.Node.create('<span class="' + CSS.EDITINSTRUCTIONS + '" id="id_editinstructions" />')
                .set('innerHTML', M.util.get_string('edittitleinstructions', 'moodle'));
            var editor = Y.Node.create('<input name="section" type="text" />').setAttrs({
                'value': oldtext,
                'autocomplete': 'off',
                'aria-describedby': 'id_editinstructions',
                'maxLength': '255' // This is the maxlength in DB.
            });

            // Clear the existing content and put the editor in.
            editform.appendChild(editor);
            editform.setData('anchor', anchor);
            instancesection.insert(editinstructions, 'before');
            anchor.replace(editform);

            // Focus and select the editor text.
            editor.focus().select();
            // Cancel the edit if we lose focus or the escape key is pressed.
            thisevent = editor.on('blur', this.edit_section_title_cancel, this, activity, false);
            this.editsectionevents.push(thisevent);
            thisevent = editor.on('key', this.edit_section_title_cancel, 'esc', this, activity, true);
            this.editsectionevents.push(thisevent);
            // Handle form submission.
            thisevent = editform.on('submit', this.edit_section_title_submit, this, activity, oldtext);
            this.editsectionevents.push(thisevent);
        });
    },

    /**
     * Handles the submit event when editing section heading.
     *
     * @protected
     * @method edit_section_title_submiy
     * @param {EventFacade} ev The event that triggered this.
     * @param {Node} activity The activity whose maxmark we are altering.
     * @param {String} oldtext The original maxmark the activity or resource had.
     */
    edit_section_title_submit: function(ev, activity, oldtext) {
         // We don't actually want to submit anything.
        ev.preventDefault();
        var newtext = Y.Lang.trim(activity.one(SELECTOR.SECTIONFORM + ' ' + SELECTOR.SECTIONINPUT).get('value'));
        var spinner = M.util.add_spinner(Y, activity.one(SELECTOR.INSTANCESECTIONAREA));
        this.edit_section_title_clear(activity);
        if (newtext !== null && newtext !== oldtext) {
            var instancesection = activity.one(SELECTOR.INSTANCESECTION);
            var instancesectiontext = newtext;
            if (newtext.trim() === '') {
                // Add a visually-hidden default section heading text to make sure we don't end up with an empty section heading.
                instancesectiontext = M.util.get_string('sectionnoname', 'quiz');
                instancesection.addClass('visually-hidden');
            } else {
                // Show the section heading when a non-empty value is set.
                instancesection.removeClass('visually-hidden');
            }
            instancesection.setContent(instancesectiontext);

            var data = {
                'class':      'section',
                'field':      'updatesectiontitle',
                'newheading': newtext,
                'id':         activity.get('id').replace('section-', '')
            };
            this.send_request(data, spinner, function(response) {
                if (response) {
                    // Set the content of the section heading if for some reason the response is different from the new text.
                    // e.g. filters were applied, the update failed, etc.
                    if (newtext !== response.instancesection) {
                        if (response.instancesection.trim() === '') {
                            // Add a visually-hidden default section heading text.
                            instancesectiontext = M.util.get_string('sectionnoname', 'quiz');
                            instancesection.addClass('visually-hidden');
                        } else {
                            instancesectiontext = response.instancesection;
                            // Show the section heading when a non-empty value is set.
                            instancesection.removeClass('visually-hidden');
                        }
                        instancesection.setContent(instancesectiontext);
                    }

                    activity.one(SELECTOR.EDITSECTIONICON).set('title',
                            M.util.get_string('sectionheadingedit', 'quiz', response.instancesection));
                    activity.one(SELECTOR.EDITSECTIONICON).set('alt',
                            M.util.get_string('sectionheadingedit', 'quiz', response.instancesection));
                    var deleteicon = activity.one(SELECTOR.DELETESECTIONICON);
                    if (deleteicon) {
                        deleteicon.set('title', M.util.get_string('sectionheadingremove', 'quiz', response.instancesection));
                        deleteicon.set('alt', M.util.get_string('sectionheadingremove', 'quiz', response.instancesection));
                    }
                }
            });
        }
    },

    /**
     * Handles the cancel event when editing the activity or resources maxmark.
     *
     * @protected
     * @method edit_maxmark_cancel
     * @param {EventFacade} ev The event that triggered this.
     * @param {Node} activity The activity whose maxmark we are altering.
     * @param {Boolean} preventdefault If true we should prevent the default action from occuring.
     */
    edit_section_title_cancel: function(ev, activity, preventdefault) {
        if (preventdefault) {
            ev.preventDefault();
        }
        this.edit_section_title_clear(activity);
    },

    /**
     * Handles clearing the editing UI and returning things to the original state they were in.
     *
     * @protected
     * @method edit_maxmark_clear
     * @param {Node} activity  The activity whose maxmark we were altering.
     */
    edit_section_title_clear: function(activity) {
        // Detach all listen events to prevent duplicate triggers
        new Y.EventHandle(this.editsectionevents).detach();

        var editform = activity.one(SELECTOR.SECTIONFORM),
            instructions = activity.one('#id_editinstructions');
        if (editform) {
            editform.replace(editform.getData('anchor'));
        }
        if (instructions) {
            instructions.remove();
        }

        // Refocus the link which was clicked originally so the user can continue using keyboard nav.
        Y.later(100, this, function() {
            activity.one(SELECTOR.EDITSECTION).focus();
        });

        // This hack is to keep Behat happy until they release a version of
        // MinkSelenium2Driver that fixes
        // https://github.com/Behat/MinkSelenium2Driver/issues/80.
        if (!Y.one('input[name=section]')) {
            Y.one('body').append('<input type="text" name="section" style="display: none">');
        }
    },

    /**
     * Edit the edit shuffle questions for the section
     *
     * @protected
     * @method edit_shuffle_questions
     * @param {EventFacade} ev The event that was fired.
     * @param {Node} button The button that triggered this action.
     * @param {Node} activity The activity node that this action will be performed on.
     * @return Boolean
     */
    edit_shuffle_questions: function(ev, button, activity) {
        var newvalue;
        if (activity.one(SELECTOR.EDITSHUFFLEQUESTIONSACTION).get('checked')) {
            newvalue = 1;
            activity.addClass('shuffled');
        } else {
            newvalue = 0;
            activity.removeClass('shuffled');
        }

        // Prevent the default actions.
        ev.preventDefault();

        // Get the element we're working on
        var data = {
            'class': 'section',
            'field': 'updateshufflequestions',
            'id': activity.get('id').replace('section-', ''),
            'newshuffle': newvalue
        };

        // Send request.
        var spinner = M.util.add_spinner(Y, activity.one(SELECTOR.EDITSHUFFLEAREA));
        this.send_request(data, spinner);
    }

}, {
    NAME: 'mod_quiz-section-toolbox',
    ATTRS: {
        courseid: {
            'value': 0
        },
        quizid: {
            'value': 0
        }
    }
});

M.mod_quiz.init_section_toolbox = function(config) {
    return new SECTIONTOOLBOX(config);
};


}, '@VERSION@', {
    "requires": [
        "base",
        "node",
        "event",
        "event-key",
        "io",
        "moodle-mod_quiz-quizbase",
        "moodle-mod_quiz-util-slot",
        "moodle-core-notification-ajaxexception"
    ]
});
