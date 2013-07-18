YUI.add('moodle-course-toolboxes', function(Y) {

    // The following properties contain common strings.
    // We separate them out here because when this JS is minified the content is less as
    // Variables get compacted to single/double characters and the full length of the string
    // exists only once.

    // The CSS classes we use.
    var CSS = {
        ACTIVITYINSTANCE : 'activityinstance',
        AVAILABILITYINFODIV : 'div.availabilityinfo',
        CONDITIONALHIDDEN : 'conditionalhidden',
        DIMCLASS : 'dimmed',
        DIMMEDTEXT : 'dimmed_text',
        EDITINSTRUCTIONS : 'editinstructions',
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
        ACTIONLINKTEXT : '.actionlinktext',
        ACTIVITYACTION : 'a.cm-edit-action[data-action]',
        ACTIVITYFORM : 'form.'+CSS.ACTIVITYINSTANCE,
        ACTIVITYICON : 'img.activityicon',
        ACTIVITYLI : 'li.activity',
        ACTIVITYTITLE : 'input[name=title]',
        COMMANDSPAN : '.commands',
        CONTENTAFTERLINK : 'div.contentafterlink',
        HIDE : 'a.editing_hide',
        HIGHLIGHT : 'a.editing_highlight',
        INSTANCENAME : 'span.instancename',
        MODINDENTDIV : 'div.mod-indent',
        PAGECONTENT : 'div#page-content',
        SECTIONLI : 'li.section',
        SHOW : 'a.'+CSS.SHOW,
        SHOWHIDE : 'a.editing_showhide'
    },
    BODY = Y.one(document.body);

    /**
     * The toolbox classes
     *
     * TOOLBOX is a generic class which should never be directly instantiated
     * RESOURCETOOLBOX is a class extending TOOLBOX containing code specific to resources
     * SECTIONTOOLBOX is a class extending TOOLBOX containing code specific to sections
     */
    var TOOLBOX = function() {
        TOOLBOX.superclass.constructor.apply(this, arguments);
    }

    Y.extend(TOOLBOX, Y.Base, {
        /**
         * Send a request using the REST API
         *
         * @param data The data to submit
         * @param statusspinner (optional) A statusspinner which may contain a section loader
         * @param optionalconfig (optional) Any additional configuration to submit
         * @return response responseText field from responce
         */
        send_request : function(data, statusspinner, optionalconfig) {
            // Default data structure
            if (!data) {
                data = {};
            }
            // Handle any variables which we must pass back through to
            var pageparams = this.get('config').pageparams;
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
                        if (statusspinner) {
                            window.setTimeout(function(e) {
                                statusspinner.hide();
                            }, 400);
                        }
                    },
                    failure : function(tid, response) {
                        if (statusspinner) {
                            statusspinner.hide();
                        }
                        new M.core.ajaxException(response);
                    }
                },
                context: this,
                sync: true
            }

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
            return responsetext;
        },
        /**
         * Return the name of the activity instance
         *
         * If activity has no name (for example label) null is returned
         *
         * @param element The <li> element to determine a name for
         * @return string|null Instance name
         */
        get_instance_name : function(target) {
            if (target.one(SELECTOR.INSTANCENAME)) {
                return target.one(SELECTOR.INSTANCENAME).get('firstChild').get('data');
            }
            return null;
        },
        /**
         * Return the module ID for the specified element
         *
         * @param element The <li> element to determine a module-id number for
         * @return string The module ID
         */
        get_element_id : function(element) {
            return element.get('id').replace(CSS.MODULEIDPREFIX, '');
        },
        /**
         * Return the module ID for the specified element
         *
         * @param element The <li> element to determine a module-id number for
         * @return string The module ID
         */
        get_section_id : function(section) {
            return section.get('id').replace(CSS.SECTIONIDPREFIX, '');
        }
    },
    {
        NAME : 'course-toolbox',
        ATTRS : {
            // The ID of the current course
            courseid : {
                'value' : 0
            },
            ajaxurl : {
                'value' : 0
            },
            config : {
                'value' : 0
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
     * @namespace M.course.toolbox
     * @class ResourceToolbox
     * @constructor
     */
    var RESOURCETOOLBOX = function() {
        RESOURCETOOLBOX.superclass.constructor.apply(this, arguments);
    }

    Y.extend(RESOURCETOOLBOX, TOOLBOX, {
        /**
         * No groups are being used.
         * @static
         * @const GROUPS_NONE
         * @type Number
         */
        GROUPS_NONE     : 0,
        /**
         * Separate groups are being used.
         * @static
         * @const GROUPS_SEPARATE
         * @type Number
         */
        GROUPS_SEPARATE : 1,
        /**
         * Visible groups are being used.
         * @static
         * @const GROUPS_VISIBLE
         * @type Number
         */
        GROUPS_VISIBLE  : 2,

        /**
         * Events that were added when editing a title.
         * These should all be detached when editing is complete.
         * @property edittitleevents
         * @type {Event[]}
         * @protected
         */
        edittitleevents : [],

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
         */
        initializer : function(config) {
            M.course.coursebase.register_module(this);
            Y.all(SELECTOR.ACTIVITYLI).each(function(activity){
                activity.setData('toolbox', this);
                activity.all(SELECTOR.COMMANDSPAN+ ' ' + SELECTOR.ACTIVITYACTION).each(function(){
                    this.setData('activity', activity);
                });
            }, this);
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
        handle_data_action : function(ev) {
            // We need to get the anchor element that triggered this event.
            var node = ev.target;
            if (!node.test('a')) {
                node = node.ancestor(SELECTOR.ACTIVITYACTION);
            }

            // From the anchor we can get both the activity (added during initialisation) and the action being
            // performed (added by the UI as a data attribute).
            var action = node.getData('action'),
                activity = node.getData('activity');
            if (!node.test('a') || !action || !activity) {
                // It wasn't a valid action node.
                return;
            }

            // Switch based upon the action and do the desired thing.
            switch (action) {
                case 'edittitle' :
                    // The user wishes to edit the title of the event.
                    this.edit_title(ev, node, activity, action);
                    break;
                case 'moveleft' :
                case 'moveright' :
                    // The user changing the indent of the activity.
                    this.change_indent(ev, node, activity, action);
                    break;
                case 'delete' :
                    // The user is deleting the activity.
                    this.delete_with_confirmation(ev, node, activity, action);
                    break;
                case 'hide' :
                case 'show' :
                    // The user is changing the visibility of the activity.
                    this.change_visibility(ev, node, activity, action);
                    break;
                case 'groupsseparate' :
                case 'groupsvisible' :
                case 'groupsnone' :
                    // The user is changing the group mode.
                    callback = 'change_groupmode';
                    this.change_groupmode(ev, node, activity, action);
                    break;
                case 'move' :
                case 'update' :
                case 'duplicate' :
                case 'assignroles' :
                default:
                    // Nothing to do here!
                    break;
            }
        },

        /**
         * Change the indent of the activity or resource.
         *
         * @protected
         * @method change_indent
         * @param {EventFacade} ev The event that was fired.
         * @param {Node} button The button that triggered this action.
         * @param {Node} activity The activity node that this action will be performed on.
         * @param {String} action The action that has been requested. Will be 'moveleft' or 'moveright'.
         */
        change_indent : function(ev, button, activity, action) {
            // Prevent the default button action
            ev.preventDefault();

            var direction = (action === 'moveleft') ? -1 : 1;

            // And we need to determine the current and new indent level
            var indentdiv = activity.one(SELECTOR.MODINDENTDIV);
            var indent = indentdiv.getAttribute('class').match(/mod-indent-(\d{1,})/);

            if (indent) {
                var oldindent = parseInt(indent[1]);
                var newindent = Math.max(0, (oldindent + parseInt(direction)));
                indentdiv.removeClass(indent[0]);
            } else {
                var oldindent = 0;
                var newindent = 1;
            }

            // Perform the move
            indentdiv.addClass(CSS.MODINDENTCOUNT + newindent);
            var data = {
                'class' : 'resource',
                'field' : 'indent',
                'value' : newindent,
                'id'    : this.get_element_id(activity)
            };
            var commands = activity.one(SELECTOR.COMMANDSPAN);
            var spinner = M.util.add_spinner(Y, commands).setStyles({
                position: 'absolute',
                top: 0
            });
            if (BODY.hasClass('dir-ltr')) {
                spinner.setStyle('left', '100%');
            }  else {
                spinner.setStyle('right', '100%');
            }
            this.send_request(data, spinner);

            // Handle removal/addition of the moveleft button.
            if (newindent == 0) {
                button.addClass('hidden');
            } else if (newindent == 1 && oldindent == 0) {
                button.ancestor('.menu').one('[data-action=moveleft]').removeClass('hidden');
            }

            // Handle massive indentation to match non-ajax display
            var hashugeclass = indentdiv.hasClass(CSS.MODINDENTHUGE);
            if (newindent > 15 && !hashugeclass) {
                indentdiv.addClass(CSS.MODINDENTHUGE);
            } else if (newindent <= 15 && hashugeclass) {
                indentdiv.removeClass(CSS.MODINDENTHUGE);
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
         * @return Boolean
         */
        delete_with_confirmation : function(ev, button, activity) {
            // Prevent the default button action
            ev.preventDefault();

            // Get the element we're working on
            var element   = activity

            // Create confirm string (different if element has or does not have name)
            var confirmstring = '';
            var plugindata = {
                type : M.util.get_string('pluginname', element.getAttribute('class').match(/modtype_([^\s]*)/)[1])
            }
            if (this.get_instance_name(element) != null) {
                plugindata.name = this.get_instance_name(element)
                confirmstring = M.util.get_string('deletechecktypename', 'moodle', plugindata);
            } else {
                confirmstring = M.util.get_string('deletechecktype', 'moodle', plugindata)
            }

            // Confirm element removal
            if (!confirm(confirmstring)) {
                return false;
            }

            // Actually remove the element
            element.remove();
            var data = {
                'class' : 'resource',
                'action' : 'DELETE',
                'id'    : this.get_element_id(element)
            };
            this.send_request(data);
            if (M.core.actionmenu && M.core.actionmenu.instance) {
                M.core.actionmenu.instance.hideMenu();
            }
        },

        /**
         * Changes the visibility of this activity or resource.
         *
         * @protected
         * @method change_visibility
         * @param {EventFacade} ev The event that was fired.
         * @param {Node} button The button that triggered this action.
         * @param {Node} activity The activity node that this action will be performed on.
         * @param {String} action The action that has been requested.
         * @return Boolean
         */
        change_visibility : function(ev, button, activity, action) {
            // Prevent the default button action
            ev.preventDefault();

            // Return early if the current section is hidden
            var section = activity.ancestor(M.course.format.get_section_selector(Y));
            if (section && section.hasClass(CSS.SECTIONHIDDENCLASS)) {
                return;
            }

            // Get the element we're working on
            var element = activity;
            var value = this.handle_resource_dim(button, activity, action);

            // Send the request
            var data = {
                'class' : 'resource',
                'field' : 'visible',
                'value' : value,
                'id'    : this.get_element_id(element)
            };
            var spinner = M.util.add_spinner(Y, element.one(SELECTOR.COMMANDSPAN));
            this.send_request(data, spinner);
            return false; // Need to return false to stop the delegate for the new state firing
        },

        /**
         * Handles the UI aspect of dimming the activity or resource.
         *
         * @protected
         * @method handle_resource_dim
         * @param {Node} button The button that triggered the action.
         * @param {Node} activity The activity node that this action will be performed on.
         * @param {String} status Whether the activity was shown or hidden.
         * @returns {number} 1 if we were changing to visible, 0 if we were hiding.
         */
        handle_resource_dim : function(button, activity, status) {
            var toggleclass = CSS.DIMCLASS,
                dimarea = activity.one('a'),
                availabilityinfo = activity.one(CSS.AVAILABILITYINFODIV),
                newstatus = (status === 'hide') ? 'show' : 'hide',
                newstring = M.util.get_string(newstatus, 'moodle');

            // Update button info.
            button.one('img').setAttrs({
                'alt' : newstring,
                'src'   : M.util.image_url('t/' + newstatus)
            });
            button.set('title', newstring);
            button.replaceClass('editing_'+status, 'editing_'+newstatus)
            button.setData('action', newstatus);

            // If activity is conditionally hidden, then don't toggle.
            if (this.get_instance_name(activity) == null) {
                toggleclass = CSS.DIMMEDTEXT;
                dimarea = activity.all(SELECTOR.MODINDENTDIV + ' > div').item(1);
            }
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
            return (status === 'hide') ? 0 : 1;
        },

        /**
         * Changes the groupmode of the activity to the next groupmode in the sequence.
         *
         * @protected
         * @method change_groupmode
         * @param {EventFacade} ev The event that was fired.
         * @param {Node} button The button that triggered this action.
         * @param {Node} activity The activity node that this action will be performed on.
         * @param {String} action The action that has been requested.
         * @return Boolean
         */
        change_groupmode : function(ev, button, activity, action) {
            // Prevent the default button action.
            ev.preventDefault();

            // Current Mode
            var groupmode = parseInt(button.getData('nextgroupmode'), 10),
                newtitle = '',
                iconsrc = '',
                newtitlestr,
                data,
                spinner,
                nextgroupmode = groupmode + 1;

            if (nextgroupmode > 2) {
                nextgroupmode = 0;
            }

            if (groupmode === this.GROUPS_NONE) {
                newtitle = 'groupsnone';
                iconsrc = M.util.image_url('t/groupn', 'moodle');
            } else if (groupmode === this.GROUPS_SEPARATE) {
                newtitle = 'groupsseparate';
                iconsrc = M.util.image_url('t/groups', 'moodle');
            } else if (groupmode === this.GROUPS_VISIBLE) {
                newtitle = 'groupsvisible';
                iconsrc = M.util.image_url('t/groupv', 'moodle');
            }
            newtitlestr = M.util.get_string(newtitle, 'moodle'),
            newtitlestr = M.util.get_string('clicktochangeinbrackets', 'moodle', newtitlestr);

            // Change the UI
            button.one('img').setAttrs({
                'alt' : newtitlestr,
                'src' : iconsrc
            });
            button.setAttribute('title', newtitlestr).setData('action', newtitle).setData('nextgroupmode', nextgroupmode);

            // And send the request
            data = {
                'class' : 'resource',
                'field' : 'groupmode',
                'value' : groupmode,
                'id'    : this.get_element_id(activity)
            };

            spinner = M.util.add_spinner(Y, activity.one(SELECTOR.COMMANDSPAN));
            this.send_request(data, spinner);
            return false; // Need to return false to stop the delegate for the new state firing
        },

        /**
         * Edit the title for the resource
         *
         * @protected
         * @method edit_title
         * @param {EventFacade} ev The event that was fired.
         * @param {Node} button The button that triggered this action.
         * @param {Node} activity The activity node that this action will be performed on.
         * @param {String} action The action that has been requested.
         * @return Boolean
         */
        edit_title : function(ev, button, activity) {
            // Get the element we're working on
            var activityid = this.get_element_id(activity),
                instancename  = activity.one(SELECTOR.INSTANCENAME),
                currenttitle = instancename.get('firstChild'),
                oldtitle = currenttitle.get('data'),
                titletext = oldtitle,
                thisevent,
                anchor = instancename.ancestor('a'),// Grab the anchor so that we can swap it with the edit form.
                data = {
                    'class'   : 'resource',
                    'field'   : 'gettitle',
                    'id'      : activityid
                },
                response = this.send_request(data);

            if (M.core.actionmenu && M.core.actionmenu.instance) {
                M.core.actionmenu.instance.hideMenu();
            }

            // Try to retrieve the existing string from the server
            if (response.instancename) {
                titletext = response.instancename;
            }

            // Create the editor and submit button
            var editform = Y.Node.create('<form class="'+CSS.ACTIVITYINSTANCE+'" action="#" />');
            var editinstructions = Y.Node.create('<span class="'+CSS.EDITINSTRUCTIONS+'" id="id_editinstructions" />')
                .set('innerHTML', M.util.get_string('edittitleinstructions', 'moodle'));
            var editor = Y.Node.create('<input name="title" type="text" class="'+CSS.TITLEEDITOR+'" />').setAttrs({
                'value' : titletext,
                'autocomplete' : 'off',
                'aria-describedby' : 'id_editinstructions',
                'maxLength' : '255'
            })

            // Clear the existing content and put the editor in
            editform.appendChild(activity.one(SELECTOR.ACTIVITYICON).cloneNode());
            editform.appendChild(editor);
            editform.setData('anchor', anchor);
            anchor.replace(editform);
            activity.one('div').appendChild(editinstructions);
            ev.preventDefault();

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
        },

        /**
         * Handles the submit event when editing the activity or resources title.
         *
         * @protected
         * @method edit_title_submit
         * @param {EventFacade} ev The event that triggered this.
         * @param {Node} activity The activity whose title we are altering.
         * @param {String} originaltitle The original title the activity or resource had.
         */
        edit_title_submit : function(ev, activity, originaltitle) {
            // We don't actually want to submit anything
            ev.preventDefault();

            var newtitle = Y.Lang.trim(activity.one(SELECTOR.ACTIVITYFORM + ' ' + SELECTOR.ACTIVITYTITLE).get('value'));
            this.edit_title_clear(activity);
            var spinner = M.util.add_spinner(Y, activity.one(SELECTOR.INSTANCENAME));
            if (newtitle != null && newtitle != "" && newtitle != originaltitle) {
                var data = {
                    'class'   : 'resource',
                    'field'   : 'updatetitle',
                    'title'   : newtitle,
                    'id'      : this.get_element_id(activity)
                };
                var response = this.send_request(data, spinner);
                if (response.instancename) {
                    activity.one(SELECTOR.INSTANCENAME).setContent(response.instancename);
                }
            }
        },

        /**
         * Handles the cancel event when editing the activity or resources title.
         *
         * @protected
         * @method edit_title_cancel
         * @param {EventFacade} ev The event that triggered this.
         * @param {Node} activity The activity whose title we are altering.
         * @param {Boolean} preventdefault If true we should prevent the default action from occuring.
         */
        edit_title_cancel : function(ev, activity, preventdefault) {
            if (preventdefault) {
                ev.preventDefault();
            }
            this.edit_title_clear(activity);
        },

        /**
         * Handles clearing the editing UI and returning things to the original state they were in.
         *
         * @protected
         * @method edit_title_clear
         * @param {Node} activity  The activity whose title we were altering.
         */
        edit_title_clear : function(activity) {
            // Detach all listen events to prevent duplicate triggers
            var thisevent;
            while (thisevent = this.edittitleevents.shift()) {
                thisevent.detach();
            }
            var editform = activity.one(SELECTOR.ACTIVITYFORM),
                instructions = activity.one('#id_editinstructions');
            if (editform) {
                editform.replace(editform.getData('anchor'));
            }
            if (instructions) {
                instructions.remove();
            }
        },

        /**
         * Set the visibility of the current resource (identified by the element)
         * to match the hidden parameter (this is not a toggle).
         * Only changes the visibility in the browser (no ajax update).
         *
         * @public This method is used by other modules.
         * @method set_visibility_resource_ui
         * @param args An object with 'element' being the A node containing the resource
         *             and 'visible' being the state that the visibility should be set to.
         */
        set_visibility_resource_ui: function(args) {
            var element = args.element,
                shouldbevisible = args.visible,
                buttonnode = element.one(SELECTOR.SHOW),
                visible = (buttonnode === null),
                status = 'hide';
            if (visible) {
                buttonnode = element.one(SELECTOR.HIDE);
                status = 'show'
            }
            if (visible != shouldbevisible) {
                this.handle_resource_dim(buttonnode, buttonnode.getData('activity'), status);
            }
        }
    }, {
        NAME : 'course-resource-toolbox',
        ATTRS : {
            courseid : {
                'value' : 0
            },
            format : {
                'value' : 'topics'
            }
        }
    });

    var SECTIONTOOLBOX = function() {
        SECTIONTOOLBOX.superclass.constructor.apply(this, arguments);
    }

    Y.extend(SECTIONTOOLBOX, TOOLBOX, {
        /**
         * Initialize the toolboxes module
         *
         * Updates all span.commands with relevant handlers and other required changes
         */
        initializer : function(config) {
            this.setup_for_section();
            M.course.coursebase.register_module(this);

            // Section Highlighting
            Y.delegate('click', this.toggle_highlight, SELECTOR.PAGECONTENT, SELECTOR.SECTIONLI + ' ' + SELECTOR.HIGHLIGHT, this);
            // Section Visibility
            Y.delegate('click', this.toggle_hide_section, SELECTOR.PAGECONTENT, SELECTOR.SECTIONLI + ' ' + SELECTOR.SHOWHIDE, this);
        },
        /**
         * Update any section areas within the scope of the specified
         * selector with AJAX equivelants
         *
         * @param baseselector The selector to limit scope to
         * @return void
         */
        setup_for_section : function(baseselector) {
            // Left here for potential future use - not currently needed due to YUI delegation in initializer()
            /*if (!baseselector) {
                var baseselector = SELECTOR.PAGECONTENT;
            }

            Y.all(baseselector).each(this._setup_for_section, this);*/
        },
        _setup_for_section : function(toolboxtarget) {
            // Left here for potential future use - not currently needed due to YUI delegation in initializer()
        },
        toggle_hide_section : function(e) {
            // Prevent the default button action
            e.preventDefault();

            // Get the section we're working on
            var section = e.target.ancestor(M.course.format.get_section_selector(Y));
            var button = e.target.ancestor('a', true);
            var hideicon = button.one('img');

            // The value to submit
            var value;
            // The status text for strings and images
            var status,
                oldstatus;

            if (!section.hasClass(CSS.SECTIONHIDDENCLASS)) {
                section.addClass(CSS.SECTIONHIDDENCLASS);
                value = 0;
                status = 'show';
                oldstatus = 'hide';
            } else {
                section.removeClass(CSS.SECTIONHIDDENCLASS);
                value = 1;
                status = 'hide';
                oldstatus = 'show';
            }

            var newstring = M.util.get_string(status + 'fromothers', 'format_' + this.get('format'));
            hideicon.setAttrs({
                'alt' : newstring,
                'src'   : M.util.image_url('i/' + status)
            });
            button.set('title', newstring);

            // Change the highlight status
            var data = {
                'class' : 'section',
                'field' : 'visible',
                'id'    : this.get_section_id(section.ancestor(M.course.format.get_section_wrapper(Y), true)),
                'value' : value
            };

            var lightbox = M.util.add_lightbox(Y, section);
            lightbox.show();

            var response = this.send_request(data, lightbox);

            var activities = section.all(SELECTOR.ACTIVITYLI);
            activities.each(function(node) {
                if (node.one(SELECTOR.SHOW)) {
                    var button = node.one(SELECTOR.SHOW);
                } else {
                    var button = node.one(SELECTOR.HIDE);
                }
                var activityid = this.get_element_id(node);

                if (Y.Array.indexOf(response.resourcestotoggle, activityid) != -1) {
                    node.getData('toolbox').handle_resource_dim(button, node, oldstatus);
                }
            }, this);
        },
        toggle_highlight : function(e) {
            // Prevent the default button action
            e.preventDefault();

            // Get the section we're working on
            var section = e.target.ancestor(M.course.format.get_section_selector(Y));
            var button = e.target.ancestor('a', true);
            var buttonicon = button.one('img');

            // Determine whether the marker is currently set
            var togglestatus = section.hasClass('current');
            var value = 0;

            // Set the current highlighted item text
            var old_string = M.util.get_string('markthistopic', 'moodle');
            Y.one(SELECTOR.PAGECONTENT)
                .all(M.course.format.get_section_selector(Y) + '.current ' + SELECTOR.HIGHLIGHT)
                .set('title', old_string);
            Y.one(SELECTOR.PAGECONTENT)
                .all(M.course.format.get_section_selector(Y) + '.current ' + SELECTOR.HIGHLIGHT + ' img')
                .set('alt', old_string)
                .set('src', M.util.image_url('i/marker'));

            // Remove the highlighting from all sections
            var allsections = Y.one(SELECTOR.PAGECONTENT).all(M.course.format.get_section_selector(Y))
                .removeClass('current');

            // Then add it if required to the selected section
            if (!togglestatus) {
                section.addClass('current');
                value = this.get_section_id(section.ancestor(M.course.format.get_section_wrapper(Y), true));
                var new_string = M.util.get_string('markedthistopic', 'moodle');
                button
                    .set('title', new_string);
                buttonicon
                    .set('alt', new_string)
                    .set('src', M.util.image_url('i/marked'));
            }

            // Change the highlight status
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
            courseid : {
                'value' : 0
            },
            format : {
                'value' : 'topics'
            }
        }
    });

    M.course = M.course || {};

    M.course.init_resource_toolbox = function(config) {
        return new RESOURCETOOLBOX(config);
    };

    M.course.init_section_toolbox = function(config) {
        return new SECTIONTOOLBOX(config);
    };

},
'@VERSION@', {
    requires : ['base', 'node', 'io', 'moodle-course-coursebase']
}
);
