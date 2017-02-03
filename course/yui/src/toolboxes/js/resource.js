/* global TOOLBOX, BODY, SELECTOR, INDENTLIMITS */

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
                this.change_groupmode(ev, node, activity, action);
                break;
            case 'move':
            case 'update':
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

        var direction = (action === 'moveleft') ? -1 : 1;

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
        var element = activity,
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
            modal: true,
            visible: false
        });
        confirm.show();

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
                M.core.actionmenu.instance.hideMenu(ev);
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
            nextaction = (action === 'hide') ? 'show' : 'hide',
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

        button.replaceClass('editing_' + action, 'editing_' + nextaction);
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
            if (action === 'hide') {
                // Change the UI.
                dimarea.addClass(toggleclass);
                // We need to toggle dimming on the description too.
                activity.all(SELECTOR.CONTENTAFTERLINK).addClass(CSS.DIMMEDTEXT);
                activity.all(SELECTOR.GROUPINGLABEL).addClass(CSS.DIMMEDTEXT);
            } else {
                // Change the UI.
                dimarea.removeClass(toggleclass);
                // We need to toggle dimming on the description too.
                activity.all(SELECTOR.CONTENTAFTERLINK).removeClass(CSS.DIMMEDTEXT);
                activity.all(SELECTOR.GROUPINGLABEL).removeClass(CSS.DIMMEDTEXT);
            }
        }
        // Toggle availablity info for conditional activities.
        if (availabilityinfo) {
            availabilityinfo.toggleClass(CSS.HIDE);
        }
        return (action === 'hide') ? 0 : 1;
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
        var oldAction = button.getData('action');
        button.replaceClass('editing_' + oldAction, 'editing_' + newtitle);
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
