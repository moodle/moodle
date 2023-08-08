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
                // Add a sr-only default section heading text to make sure we don't end up with an empty section heading.
                instancesectiontext = M.util.get_string('sectionnoname', 'quiz');
                instancesection.addClass('sr-only');
            } else {
                // Show the section heading when a non-empty value is set.
                instancesection.removeClass('sr-only');
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
                            // Add a sr-only default section heading text.
                            instancesectiontext = M.util.get_string('sectionnoname', 'quiz');
                            instancesection.addClass('sr-only');
                        } else {
                            instancesectiontext = response.instancesection;
                            // Show the section heading when a non-empty value is set.
                            instancesection.removeClass('sr-only');
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
