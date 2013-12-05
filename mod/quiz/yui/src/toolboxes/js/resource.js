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
        BODY.delegate('key', this.handle_data_action, 'down:enter', SELECTOR.ACTIVITYACTION, this);
        Y.delegate('click', this.handle_data_action, BODY, SELECTOR.ACTIVITYACTION, this);
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
            case 'linkpage':
            case 'unlinkpage':
                // The user is linking or unlinking pages.
                this.link_page(ev, node, activity, action);
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
     * @chainable
     */
    delete_with_confirmation: function(ev, button, activity) {
        // Prevent the default button action
        ev.preventDefault();

        // Get the element we're working on
        var element   = activity,
            // Create confirm string (different if element has or does not have name)
            confirmstring = '',
            qtypename = M.util.get_string('pluginname',
                        'qtype_' + element.getAttribute('class').match(/qtype_([^\s]*)/)[1]);
        confirmstring = M.util.get_string('confirmremovequestion', 'quiz', qtypename);

        // Create the confirmation dialogue.
        var confirm = new M.core.confirm({
            question: confirmstring,
            modal: true
        });

        // If it is confirmed.
        confirm.on('complete-yes', function() {

            // Actually remove the element.
            element.remove();
            Y.Moodle.mod_quiz.util.slot.reorder_slots();
            var data = {
                'class': 'resource',
                'action': 'DELETE',
                'id': Y.Moodle.mod_quiz.util.slot.getId(element)
            };
            this.send_request(data);
            if (M.core.actionmenu && M.core.actionmenu.instance) {
                M.core.actionmenu.instance.hideMenu();
            }
            window.location.reload(true);

        }, this);

        return this;
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
    edit_maxmark : function(ev, button, activity) {
        // Get the element we're working on
        var activityid = Y.Moodle.mod_quiz.util.slot.getId(activity),
            instancemaxmark  = activity.one(SELECTOR.INSTANCEMAXMARK),
            instance = activity.one(SELECTOR.ACTIVITYINSTANCE),
            currentmaxmark = instancemaxmark.get('firstChild'),
            oldmaxmark = currentmaxmark.get('data'),
            maxmarktext = oldmaxmark,
            thisevent,
            anchor = instancemaxmark,// Grab the anchor so that we can swap it with the edit form.
            data = {
                'class'   : 'resource',
                'field'   : 'getmaxmark',
                'id'      : activityid
            };

        // Prevent the default actions.
        ev.preventDefault();

        this.send_request(data, null, function(response) {
            if (M.core.actionmenu && M.core.actionmenu.instance) {
                M.core.actionmenu.instance.hideMenu();
            }

            // Try to retrieve the existing string from the server
            if (response.instancemaxmark) {
                maxmarktext = response.instancemaxmark;
            }

            // Create the editor and submit button
            var editform = Y.Node.create('<form action="#" />');
            var editinstructions = Y.Node.create('<span class="' + CSS.EDITINSTRUCTIONS + '" id="id_editinstructions" />')
                .set('innerHTML', M.util.get_string('edittitleinstructions', 'moodle'));
            var editor = Y.Node.create('<input name="maxmark" type="text" class="' + CSS.TITLEEDITOR + '" />').setAttrs({
                'value' : maxmarktext,
                'autocomplete' : 'off',
                'aria-describedby' : 'id_editinstructions',
                'maxLength' : '12',
                'size' : parseInt(this.get('config').questiondecimalpoints, 10) + 2
            });

            // Clear the existing content and put the editor in
            editform.appendChild(editor);
            editform.setData('anchor', anchor);
            instance.insert(editinstructions, 'before');
            anchor.replace(editform);

            // Force the editing instruction to match the mod-indent position.
            var padside = 'left';
            if (right_to_left()) {
                padside = 'right';
            }

            // We hide various components whilst editing:
            activity.addClass(CSS.EDITINGMAXMARK);

            // Focus and select the editor text
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
    edit_maxmark_submit : function(ev, activity, originalmaxmark) {
        // We don't actually want to submit anything
        ev.preventDefault();
        var newmaxmark = Y.Lang.trim(activity.one(SELECTOR.ACTIVITYFORM + ' ' + SELECTOR.ACTIVITYMAXMARK).get('value'));
        var spinner = this.add_spinner(activity);
        this.edit_maxmark_clear(activity);
        activity.one(SELECTOR.INSTANCEMAXMARK).setContent(newmaxmark);
        if (newmaxmark !== null && newmaxmark !== "" && newmaxmark !== originalmaxmark) {
            var data = {
                'class'   : 'resource',
                'field'   : 'updatemaxmark',
                'maxmark'   : newmaxmark,
                'id'      : Y.Moodle.mod_quiz.util.slot.getId(activity)
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
    edit_maxmark_cancel : function(ev, activity, preventdefault) {
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
    edit_maxmark_clear : function(activity) {
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

        // This hack is to keep Behat happy until they release a version of
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
     * @method link_page
     * @param {EventFacade} ev The event that was fired.
     * @param {Node} button The button that triggered this action.
     * @param {Node} activity The activity node that this action will be performed on.
     * @chainable
     */
    link_page: function(ev, button, activity, action) {
        // Prevent the default button action
        ev.preventDefault();

        activity = activity.next('li.activity.slot');
        var spinner = this.add_spinner(activity),
            slotid = 0;
        var value = action === 'linkpage' ? 1:2;

        var data = {
            'class': 'resource',
            'field': 'linkslottopage',
            'id':    slotid,
            'value': value
        };

        slotid = Y.Moodle.mod_quiz.util.slot.getId(activity);
        if (slotid) {
            data.id = Number(slotid);
        }
        this.send_request(data, spinner, function(response) {
            window.location.reload(true);
//            if (response.slots) {
//                this.repaginate_slots(response.slots);
//            }
        });

        return this;
    },
    repaginate_slots: function(slots) {
        this.slots = slots;
        var section = Y.one(SELECTOR.PAGECONTENT + ' ' + SELECTOR.SECTIONUL),
            activities = section.all(SELECTOR.ACTIVITYLI);
        activities.each(function(node) {

            // What element is it? page/slot/link
            // what is the current slot?
            var type;
            var slot;
            if(node.hasClass(CSS.PAGE)){
                type = this.NODE_PAGE;
                slot = node.next(SELECTOR.SLOTLI);
            } else if (node.hasClass(CSS.SLOT)){
                type = this.NODE_SLOT;
                slot = node;
            } else if (node.hasClass(CSS.JOIN)){
                type = this.NODE_JOIN;
                slot = node.previous(SELECTOR.SLOTLI);
            }

            // getSlotnumber() Should be a method of util.slot
            var slotnumber = Number(Y.Moodle.mod_quiz.util.slot.getNumber(slot));
            if(!type){
                // Nothing we can do.
                return;
            }

            // Is it correct?
            if(!this.slots.hasOwnProperty(slotnumber)){
                // An error. We should handle this.
                return;
            }

            var slotdata = this.slots[slotnumber];

            if(type === this.NODE_PAGE){
                // Get page number
                var pagenumber = Y.Moodle.mod_quiz.util.page.getNumber(node);
                // Is the page number correct?
                if (slotdata.page === pagenumber) {
                    console.log('slotdata.page == pagenumber return');
                    return;
                }

                if (pagenumber < slotdata.page) {
                    // Remove page node.
                    node.remove();
                }
                else {
                    // Add page node.
                    console.log('pagenumber > slotdata.page update page number');
                }

            }
        }, this);
    },

    NAME : 'mod_quiz-resource-toolbox',
    ATTRS : {
        courseid : {
            'value' : 0
        },
        quizid : {
            'value' : 0
        }
    }
});

M.mod_quiz.resource_toolbox = null;
M.mod_quiz.init_resource_toolbox = function(config) {
    M.mod_quiz.resource_toolbox = new RESOURCETOOLBOX(config);
    return M.mod_quiz.resource_toolbox;
};
