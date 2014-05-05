YUI.add('moodle-course-toolboxes', function (Y, NAME) {

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
        ACTIVITYINSTANCE : 'activityinstance',
        AVAILABILITYINFODIV : 'div.availabilityinfo',
        CONTENTWITHOUTLINK : 'contentwithoutlink',
        CONDITIONALHIDDEN : 'conditionalhidden',
        DIMCLASS : 'dimmed',
        DIMMEDTEXT : 'dimmed_text',
        EDITINSTRUCTIONS : 'editinstructions',
        EDITINGTITLE: 'editor_displayed',
        HIDE : 'hide',
        MODINDENTCOUNT : 'mod-indent-',
        MODINDENTHUGE : 'mod-indent-huge',
        MODULEIDPREFIX : 'module-',
        SECTIONHIDDENCLASS : 'hidden',
        SECTIONIDPREFIX : 'section-',
        SHOW : 'editing_show',
        TITLEEDITOR : 'titleeditor'
    },
    // The CSS selectors we use.
    SELECTOR = {
        ACTIONAREA: '.actions',
        ACTIONLINKTEXT : '.actionlinktext',
        ACTIVITYACTION : 'a.cm-edit-action[data-action], a.editing_title',
        ACTIVITYFORM : '.' + CSS.ACTIVITYINSTANCE + ' form',
        ACTIVITYICON : 'img.activityicon',
        ACTIVITYINSTANCE : '.' + CSS.ACTIVITYINSTANCE,
        ACTIVITYLINK: '.' + CSS.ACTIVITYINSTANCE + ' > a',
        ACTIVITYLI : 'li.activity',
        ACTIVITYTITLE : 'input[name=title]',
        COMMANDSPAN : '.commands',
        CONTENTAFTERLINK : 'div.contentafterlink',
        CONTENTWITHOUTLINK : 'div.contentwithoutlink',
        EDITTITLE: 'a.editing_title',
        HIDE : 'a.editing_hide',
        HIGHLIGHT : 'a.editing_highlight',
        INSTANCENAME : 'span.instancename',
        MODINDENTDIV : '.mod-indent',
        MODINDENTOUTER : '.mod-indent-outer',
        PAGECONTENT : 'body',
        SECTIONLI : 'li.section',
        SHOW : 'a.'+CSS.SHOW,
        SHOWHIDE : 'a.editing_showhide'
    },
    INDENTLIMITS = {
        MIN: 0,
        MAX: 16
    },
    BODY = Y.one(document.body);

// Setup the basic namespace.
M.course = M.course || {};

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
        data.courseId = this.get('courseid');

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
                    } catch (e) {}

                    // Run the callback if we have one.
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
    NAME: 'course-toolbox',
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
        format: {
            'value': 'topics'
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
 * when viewing a course in editing mode.
 *
 * @module moodle-course-toolboxes
 * @namespace M.course.toolboxes
 */

/**
 * Resource and activity toolbox class.
 *
 * This is a class extending TOOLBOX containing code specific to resources
 *
 * This class is responsible for managing AJAX interactions with activities and resources
 * when viewing a course in editing mode.
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
     * No groups are being used.
     *
     * @property GROUPS_NONE
     * @protected
     * @type Number
     */
    GROUPS_NONE: 0,

    /**
     * Separate groups are being used.
     *
     * @property GROUPS_SEPARATE
     * @protected
     * @type Number
     */
    GROUPS_SEPARATE: 1,

    /**
     * Visible groups are being used.
     *
     * @property GROUPS_VISIBLE
     * @protected
     * @type Number
     */
    GROUPS_VISIBLE: 2,

    /**
     * An Array of events added when editing a title.
     * These should all be detached when editing is complete.
     *
     * @property edittitleevents
     * @protected
     * @type Array
     * @protected
     */
    edittitleevents: [],

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
        M.course.coursebase.register_module(this);
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
     * @return {boolean}
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
            case 'edittitle':
                // The user wishes to edit the title of the event.
                this.edit_title(ev, node, activity, action);
                break;
            case 'moveleft':
            case 'moveright':
                // The user changing the indent of the activity.
                this.change_indent(ev, node, activity, action);
                break;
            case 'delete':
                // The user is deleting the activity.
                this.delete_with_confirmation(ev, node, activity, action);
                break;
            case 'duplicate':
                // The user is duplicating the activity.
                this.duplicate(ev, node, activity, action);
                break;
            case 'hide':
            case 'show':
                // The user is changing the visibility of the activity.
                this.change_visibility(ev, node, activity, action);
                break;
            case 'groupsseparate':
            case 'groupsvisible':
            case 'groupsnone':
                // The user is changing the group mode.
                callback = 'change_groupmode';
                this.change_groupmode(ev, node, activity, action);
                break;
            case 'move':
            case 'update':
            case 'duplicate':
            case 'assignroles':
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
     * Change the indent of the activity or resource.
     *
     * @method change_indent
     * @protected
     * @param {EventFacade} ev The event that was fired.
     * @param {Node} button The button that triggered this action.
     * @param {Node} activity The activity node that this action will be performed on.
     * @param {String} action The action that has been requested. Will be 'moveleft' or 'moveright'.
     */
    change_indent: function(ev, button, activity, action) {
        // Prevent the default button action
        ev.preventDefault();

        var direction = (action === 'moveleft') ? -1: 1;

        // And we need to determine the current and new indent level
        var indentdiv = activity.one(SELECTOR.MODINDENTDIV),
            indent = indentdiv.getAttribute('class').match(/mod-indent-(\d{1,})/),
            oldindent = 0,
            newindent;

        if (indent) {
            oldindent = parseInt(indent[1], 10);
        }
        newindent = oldindent + parseInt(direction, 10);

        if (newindent < INDENTLIMITS.MIN || newindent > INDENTLIMITS.MAX) {
            return;
        }

        if (indent) {
            indentdiv.removeClass(indent[0]);
        }

        // Perform the move
        indentdiv.addClass(CSS.MODINDENTCOUNT + newindent);
        var data = {
            'class': 'resource',
            'field': 'indent',
            'value': newindent,
            'id': Y.Moodle.core_course.util.cm.getId(activity)
        };
        var spinner = this.add_spinner(activity);
        this.send_request(data, spinner);

        var remainingmove;

        // Handle removal/addition of the moveleft button.
        if (newindent === INDENTLIMITS.MIN) {
            button.addClass('hidden');
            remainingmove = activity.one('.editing_moveright');
        } else if (newindent > INDENTLIMITS.MIN && oldindent === INDENTLIMITS.MIN) {
            button.ancestor('.menu').one('[data-action=moveleft]').removeClass('hidden');
        }

        if (newindent === INDENTLIMITS.MAX) {
            button.addClass('hidden');
            remainingmove = activity.one('.editing_moveleft');
        } else if (newindent < INDENTLIMITS.MAX && oldindent === INDENTLIMITS.MAX) {
            button.ancestor('.menu').one('[data-action=moveright]').removeClass('hidden');
        }

        // Handle massive indentation to match non-ajax display
        var hashugeclass = indentdiv.hasClass(CSS.MODINDENTHUGE);
        if (newindent > 15 && !hashugeclass) {
            indentdiv.addClass(CSS.MODINDENTHUGE);
        } else if (newindent <= 15 && hashugeclass) {
            indentdiv.removeClass(CSS.MODINDENTHUGE);
        }

        if (ev.type && ev.type === "key" && remainingmove) {
            remainingmove.focus();
        }
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
            plugindata = {
                type: M.util.get_string('pluginname', element.getAttribute('class').match(/modtype_([^\s]*)/)[1])
            };
        if (Y.Moodle.core_course.util.cm.getName(element) !== null) {
            plugindata.name = Y.Moodle.core_course.util.cm.getName(element);
            confirmstring = M.util.get_string('deletechecktypename', 'moodle', plugindata);
        } else {
            confirmstring = M.util.get_string('deletechecktype', 'moodle', plugindata);
        }

        // Create the confirmation dialogue.
        var confirm = new M.core.confirm({
            question: confirmstring,
            modal: true
        });

        // If it is confirmed.
        confirm.on('complete-yes', function() {

            // Actually remove the element.
            element.remove();
            var data = {
                'class': 'resource',
                'action': 'DELETE',
                'id': Y.Moodle.core_course.util.cm.getId(element)
            };
            this.send_request(data);
            if (M.core.actionmenu && M.core.actionmenu.instance) {
                M.core.actionmenu.instance.hideMenu();
            }

        }, this);

        return this;
    },

    /**
     * Duplicates the activity.
     *
     * @method duplicate
     * @protected
     * @param {EventFacade} ev The event that was fired.
     * @param {Node} button The button that triggered this action.
     * @param {Node} activity The activity node that this action will be performed on.
     * @chainable
     */
    duplicate: function(ev, button, activity) {
        // Prevent the default button action
        ev.preventDefault();

        // Get the element we're working on
        var element = activity;

        // Add the lightbox.
        var section = activity.ancestor(M.course.format.get_section_selector(Y)),
            lightbox = M.util.add_lightbox(Y, section).show();

        // Build and send the request.
        var data = {
            'class': 'resource',
            'field': 'duplicate',
            'id': Y.Moodle.core_course.util.cm.getId(element),
            'sr': button.getData('sr')
        };
        this.send_request(data, lightbox, function(response) {
            var newcm = Y.Node.create(response.fullcontent);

            // Append to the section?
            activity.insert(newcm, 'after');
            Y.use('moodle-course-coursebase', function() {
                M.course.coursebase.invoke_function('setup_for_resource', newcm);
            });
            if (M.core.actionmenu && M.core.actionmenu.newDOMNode) {
                M.core.actionmenu.newDOMNode(newcm);
            }
        });
        return this;
    },

    /**
     * Changes the visibility of this activity or resource.
     *
     * @method change_visibility
     * @protected
     * @param {EventFacade} ev The event that was fired.
     * @param {Node} button The button that triggered this action.
     * @param {Node} activity The activity node that this action will be performed on.
     * @param {String} action The action that has been requested.
     * @chainable
     */
    change_visibility: function(ev, button, activity, action) {
        // Prevent the default button action
        ev.preventDefault();

        // Get the element we're working on
        var element = activity;
        var value = this.handle_resource_dim(button, activity, action);

        // Send the request
        var data = {
            'class': 'resource',
            'field': 'visible',
            'value': value,
            'id': Y.Moodle.core_course.util.cm.getId(element)
        };
        var spinner = this.add_spinner(element);
        this.send_request(data, spinner);

        return this;
    },

    /**
     * Handles the UI aspect of dimming the activity or resource.
     *
     * @method handle_resource_dim
     * @protected
     * @param {Node} button The button that triggered the action.
     * @param {Node} activity The activity node that this action will be performed on.
     * @param {String} action 'show' or 'hide'.
     * @return {Number} 1 if we changed to visible, 0 if we were hiding.
     */
    handle_resource_dim: function(button, activity, action) {
        var toggleclass = CSS.DIMCLASS,
            dimarea = activity.one([
                    SELECTOR.ACTIVITYLINK,
                    SELECTOR.CONTENTWITHOUTLINK
                ].join(', ')),
            availabilityinfo = activity.one(CSS.AVAILABILITYINFODIV),
            nextaction = (action === 'hide') ? 'show': 'hide',
            buttontext = button.one('span'),
            newstring = M.util.get_string(nextaction, 'moodle'),
            buttonimg = button.one('img');

        // Update button info.
        buttonimg.setAttrs({
            'src': M.util.image_url('t/' + nextaction)
        });

        if (Y.Lang.trim(button.getAttribute('title'))) {
            button.setAttribute('title', newstring);
        }

        if (Y.Lang.trim(buttonimg.getAttribute('alt'))) {
            buttonimg.setAttribute('alt', newstring);
        }

        button.replaceClass('editing_'+action, 'editing_'+nextaction);
        button.setData('action', nextaction);
        if (buttontext) {
            buttontext.set('text', newstring);
        }

        if (activity.one(SELECTOR.CONTENTWITHOUTLINK)) {
            dimarea = activity.one(SELECTOR.CONTENTWITHOUTLINK);
            toggleclass = CSS.DIMMEDTEXT;
        }

        // If activity is conditionally hidden, then don't toggle.
        if (!dimarea.hasClass(CSS.CONDITIONALHIDDEN)) {
            // Change the UI.
            dimarea.toggleClass(toggleclass);
            // We need to toggle dimming on the description too.
            activity.all(SELECTOR.CONTENTAFTERLINK).toggleClass(CSS.DIMMEDTEXT);
        }
        // Toggle availablity info for conditional activities.
        if (availabilityinfo) {
            availabilityinfo.toggleClass(CSS.HIDE);
        }
        return (action === 'hide') ? 0: 1;
    },

    /**
     * Changes the groupmode of the activity to the next groupmode in the sequence.
     *
     * @method change_groupmode
     * @protected
     * @param {EventFacade} ev The event that was fired.
     * @param {Node} button The button that triggered this action.
     * @param {Node} activity The activity node that this action will be performed on.
     * @chainable
     */
    change_groupmode: function(ev, button, activity) {
        // Prevent the default button action.
        ev.preventDefault();

        // Current Mode
        var groupmode = parseInt(button.getData('nextgroupmode'), 10),
            newtitle = '',
            iconsrc = '',
            newtitlestr,
            data,
            spinner,
            nextgroupmode = groupmode + 1,
            buttonimg = button.one('img');

        if (nextgroupmode > 2) {
            nextgroupmode = 0;
        }

        if (groupmode === this.GROUPS_NONE) {
            newtitle = 'groupsnone';
            iconsrc = M.util.image_url('i/groupn', 'moodle');
        } else if (groupmode === this.GROUPS_SEPARATE) {
            newtitle = 'groupsseparate';
            iconsrc = M.util.image_url('i/groups', 'moodle');
        } else if (groupmode === this.GROUPS_VISIBLE) {
            newtitle = 'groupsvisible';
            iconsrc = M.util.image_url('i/groupv', 'moodle');
        }
        newtitlestr = M.util.get_string('clicktochangeinbrackets', 'moodle', M.util.get_string(newtitle, 'moodle'));

        // Change the UI
        buttonimg.setAttrs({
            'src': iconsrc
        });
        if (Y.Lang.trim(button.getAttribute('title'))) {
            button.setAttribute('title', newtitlestr).setData('action', newtitle).setData('nextgroupmode', nextgroupmode);
        }

        if (Y.Lang.trim(buttonimg.getAttribute('alt'))) {
            buttonimg.setAttribute('alt', newtitlestr);
        }

        // And send the request
        data = {
            'class': 'resource',
            'field': 'groupmode',
            'value': groupmode,
            'id': Y.Moodle.core_course.util.cm.getId(activity)
        };

        spinner = this.add_spinner(activity);
        this.send_request(data, spinner);
        return this;
    },

    /**
     * Edit the title for the resource
     *
     * @method edit_title
     * @protected
     * @param {EventFacade} ev The event that was fired.
     * @param {Node} button The button that triggered this action.
     * @param {Node} activity The activity node that this action will be performed on.
     * @param {String} action The action that has been requested.
     * @chainable
     */
    edit_title: function(ev, button, activity) {
        // Get the element we're working on
        var activityid = Y.Moodle.core_course.util.cm.getId(activity),
            instancename  = activity.one(SELECTOR.INSTANCENAME),
            instance = activity.one(SELECTOR.ACTIVITYINSTANCE),
            currenttitle = instancename.get('firstChild'),
            oldtitle = currenttitle.get('data'),
            titletext = oldtitle,
            thisevent,
            anchor = instancename.ancestor('a'),// Grab the anchor so that we can swap it with the edit form.
            data = {
                'class': 'resource',
                'field': 'gettitle',
                'id': activityid
            };

        // Prevent the default actions.
        ev.preventDefault();

        this.send_request(data, null, function(response) {
            if (M.core.actionmenu && M.core.actionmenu.instance) {
                M.core.actionmenu.instance.hideMenu();
            }

            // Try to retrieve the existing string from the server
            if (response.instancename) {
                titletext = response.instancename;
            }

            // Create the editor and submit button
            var editform = Y.Node.create('<form action="#" />');
            var editinstructions = Y.Node.create('<span class="'+CSS.EDITINSTRUCTIONS+'" id="id_editinstructions" />')
                .set('innerHTML', M.util.get_string('edittitleinstructions', 'moodle'));
            var editor = Y.Node.create('<input name="title" type="text" class="'+CSS.TITLEEDITOR+'" />').setAttrs({
                'value': titletext,
                'autocomplete': 'off',
                'aria-describedby': 'id_editinstructions',
                'maxLength': '255'
            });

            // Clear the existing content and put the editor in
            editform.appendChild(activity.one(SELECTOR.ACTIVITYICON).cloneNode());
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
            activity.addClass(CSS.EDITINGTITLE);

            // Focus and select the editor text
            editor.focus().select();

            // Cancel the edit if we lose focus or the escape key is pressed.
            thisevent = editor.on('blur', this.edit_title_cancel, this, activity, false);
            this.edittitleevents.push(thisevent);
            thisevent = editor.on('key', this.edit_title_cancel, 'esc', this, activity, true);
            this.edittitleevents.push(thisevent);

            // Handle form submission.
            thisevent = editform.on('submit', this.edit_title_submit, this, activity, oldtitle);
            this.edittitleevents.push(thisevent);
        });
        return this;
    },

    /**
     * Handles the submit event when editing the activity or resources title.
     *
     * @method edit_title_submit
     * @protected
     * @param {EventFacade} ev The event that triggered this.
     * @param {Node} activity The activity whose title we are altering.
     * @param {String} originaltitle The original title the activity or resource had.
     */
    edit_title_submit: function(ev, activity, originaltitle) {
        // We don't actually want to submit anything
        ev.preventDefault();

        var newtitle = Y.Lang.trim(activity.one(SELECTOR.ACTIVITYFORM + ' ' + SELECTOR.ACTIVITYTITLE).get('value'));
        this.edit_title_clear(activity);
        var spinner = this.add_spinner(activity);
        if (newtitle !== null && newtitle !== "" && newtitle !== originaltitle) {
            var data = {
                'class': 'resource',
                'field': 'updatetitle',
                'title': newtitle,
                'id': Y.Moodle.core_course.util.cm.getId(activity)
            };
            this.send_request(data, spinner, function(response) {
                if (response.instancename) {
                    activity.one(SELECTOR.INSTANCENAME).setContent(response.instancename);
                }
            });
        }
    },

    /**
     * Handles the cancel event when editing the activity or resources title.
     *
     * @method edit_title_cancel
     * @protected
     * @param {EventFacade} ev The event that triggered this.
     * @param {Node} activity The activity whose title we are altering.
     * @param {Boolean} preventdefault If true we should prevent the default action from occuring.
     */
    edit_title_cancel: function(ev, activity, preventdefault) {
        if (preventdefault) {
            ev.preventDefault();
        }
        this.edit_title_clear(activity);
    },

    /**
     * Handles clearing the editing UI and returning things to the original state they were in.
     *
     * @method edit_title_clear
     * @protected
     * @param {Node} activity  The activity whose title we were altering.
     */
    edit_title_clear: function(activity) {
        // Detach all listen events to prevent duplicate triggers
        new Y.EventHandle(this.edittitleevents).detach();

        var editform = activity.one(SELECTOR.ACTIVITYFORM),
            instructions = activity.one('#id_editinstructions');
        if (editform) {
            editform.replace(editform.getData('anchor'));
        }
        if (instructions) {
            instructions.remove();
        }

        // Remove the editing class again to revert the display.
        activity.removeClass(CSS.EDITINGTITLE);

        // Refocus the link which was clicked originally so the user can continue using keyboard nav.
        Y.later(100, this, function() {
            activity.one(SELECTOR.EDITTITLE).focus();
        });
    },

    /**
     * Set the visibility of the specified resource to match the visible parameter.
     *
     * Note: This is not a toggle function and only changes the visibility
     * in the browser (no ajax update is performed).
     *
     * @method set_visibility_resource_ui
     * @param {object} args An object containing the required information to trigger a change.
     * @param {Node} args.element The resource to toggle
     * @param {Boolean} args.visible The target visibility
     */
    set_visibility_resource_ui: function(args) {
        var element = args.element,
            buttonnode = element.one(SELECTOR.HIDE),
            // By default we assume that the item is visible and we're going to hide it.
            currentVisibility = true,
            targetVisibility = false;

        if (!buttonnode) {
            // If the buttonnode was not found, try to find the HIDE button
            // and change the target visibility setting to false.
            buttonnode = element.one(SELECTOR.SHOW);
            currentVisibility = false;
            targetVisibility = true;
        }

        if (typeof args.visible !== 'undefined') {
            // If we were provided with a visibility argument, use that instead.
            targetVisibility = args.visible;
        }

        // Only trigger a change if necessary.
        if (currentVisibility !== targetVisibility) {
            var action = 'hide';
            if (targetVisibility) {
                action = 'show';
            }

            this.handle_resource_dim(buttonnode, element, action);
        }
    }
}, {
    NAME: 'course-resource-toolbox',
    ATTRS: {
    }
});

M.course.resource_toolbox = null;
M.course.init_resource_toolbox = function(config) {
    M.course.resource_toolbox = new RESOURCETOOLBOX(config);
    return M.course.resource_toolbox;
};
/**
 * Resource and activity toolbox class.
 *
 * This class is responsible for managing AJAX interactions with activities and resources
 * when viewing a course in editing mode.
 *
 * @module moodle-course-toolboxes
 * @namespace M.course.toolboxes
 */

/**
 * Section toolbox class.
 *
 * This class is responsible for managing AJAX interactions with sections
 * when viewing a course in editing mode.
 *
 * @class section
 * @constructor
 * @extends M.course.toolboxes.toolbox
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
        M.course.coursebase.register_module(this);

        // Section Highlighting.
        Y.delegate('click', this.toggle_highlight, SELECTOR.PAGECONTENT, SELECTOR.SECTIONLI + ' ' + SELECTOR.HIGHLIGHT, this);

        // Section Visibility.
        Y.delegate('click', this.toggle_hide_section, SELECTOR.PAGECONTENT, SELECTOR.SECTIONLI + ' ' + SELECTOR.SHOWHIDE, this);
    },

    toggle_hide_section : function(e) {
        // Prevent the default button action.
        e.preventDefault();

        // Get the section we're working on.
        var section = e.target.ancestor(M.course.format.get_section_selector(Y)),
            button = e.target.ancestor('a', true),
            hideicon = button.one('img'),

        // The value to submit
            value,

        // The text for strings and images. Also determines the icon to display.
            action,
            nextaction;

        if (!section.hasClass(CSS.SECTIONHIDDENCLASS)) {
            section.addClass(CSS.SECTIONHIDDENCLASS);
            value = 0;
            action = 'hide';
            nextaction = 'show';
        } else {
            section.removeClass(CSS.SECTIONHIDDENCLASS);
            value = 1;
            action = 'show';
            nextaction = 'hide';
        }

        var newstring = M.util.get_string(nextaction + 'fromothers', 'format_' + this.get('format'));
        hideicon.setAttrs({
            'alt' : newstring,
            'src'   : M.util.image_url('i/' + nextaction)
        });
        button.set('title', newstring);

        // Change the highlight status
        var data = {
            'class' : 'section',
            'field' : 'visible',
            'id'    : Y.Moodle.core_course.util.section.getId(section.ancestor(M.course.format.get_section_wrapper(Y), true)),
            'value' : value
        };

        var lightbox = M.util.add_lightbox(Y, section);
        lightbox.show();

        this.send_request(data, lightbox, function(response) {
            var activities = section.all(SELECTOR.ACTIVITYLI);
            activities.each(function(node) {
                var button;
                if (node.one(SELECTOR.SHOW)) {
                    button = node.one(SELECTOR.SHOW);
                } else {
                    button = node.one(SELECTOR.HIDE);
                }
                var activityid = Y.Moodle.core_course.util.cm.getId(node);

                // NOTE: resourcestotoggle is returned as a string instead
                // of a Number so we must cast our activityid to a String.
                if (Y.Array.indexOf(response.resourcestotoggle, "" + activityid) !== -1) {
                    M.course.resource_toolbox.handle_resource_dim(button, node, action);
                }
            }, this);
        });
    },

    /**
     * Toggle highlighting the current section.
     *
     * @method toggle_highlight
     * @param {EventFacade} e
     */
    toggle_highlight : function(e) {
        // Prevent the default button action.
        e.preventDefault();

        // Get the section we're working on.
        var section = e.target.ancestor(M.course.format.get_section_selector(Y));
        var button = e.target.ancestor('a', true);
        var buttonicon = button.one('img');

        // Determine whether the marker is currently set.
        var togglestatus = section.hasClass('current');
        var value = 0;

        // Set the current highlighted item text.
        var old_string = M.util.get_string('markthistopic', 'moodle');
        Y.one(SELECTOR.PAGECONTENT)
            .all(M.course.format.get_section_selector(Y) + '.current ' + SELECTOR.HIGHLIGHT)
            .set('title', old_string);
        Y.one(SELECTOR.PAGECONTENT)
            .all(M.course.format.get_section_selector(Y) + '.current ' + SELECTOR.HIGHLIGHT + ' img')
            .set('alt', old_string)
            .set('src', M.util.image_url('i/marker'));

        // Remove the highlighting from all sections.
        Y.one(SELECTOR.PAGECONTENT).all(M.course.format.get_section_selector(Y))
            .removeClass('current');

        // Then add it if required to the selected section.
        if (!togglestatus) {
            section.addClass('current');
            value = Y.Moodle.core_course.util.section.getId(section.ancestor(M.course.format.get_section_wrapper(Y), true));
            var new_string = M.util.get_string('markedthistopic', 'moodle');
            button
                .set('title', new_string);
            buttonicon
                .set('alt', new_string)
                .set('src', M.util.image_url('i/marked'));
        }

        // Change the highlight status.
        var data = {
            'class' : 'course',
            'field' : 'marker',
            'value' : value
        };
        var lightbox = M.util.add_lightbox(Y, section);
        lightbox.show();
        this.send_request(data, lightbox);
    }
}, {
    NAME : 'course-section-toolbox',
    ATTRS : {
    }
});

M.course.init_section_toolbox = function(config) {
    return new SECTIONTOOLBOX(config);
};


}, '@VERSION@', {"requires": ["node", "base", "event-key", "node", "io", "moodle-course-coursebase", "moodle-course-util"]});
