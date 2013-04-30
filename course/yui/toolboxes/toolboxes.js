YUI.add('moodle-course-toolboxes', function(Y) {
    WAITICON = {'pix':"i/loading_small",'component':'moodle'};
    // The CSS selectors we use
    var CSS = {
        ACTIVITYLI : 'li.activity',
        COMMANDSPAN : 'span.commands',
        SPINNERCOMMANDSPAN : 'span.commands',
        CONTENTAFTERLINK : 'div.contentafterlink',
        DELETE : 'a.editing_delete',
        DIMCLASS : 'dimmed',
        DIMMEDTEXT : 'dimmed_text',
        EDITTITLE : 'a.editing_title',
        EDITTITLECLASS : 'edittitle',
        GENERICICONCLASS : 'iconsmall',
        GROUPSNONE : 'a.editing_groupsnone',
        GROUPSSEPARATE : 'a.editing_groupsseparate',
        GROUPSVISIBLE : 'a.editing_groupsvisible',
        HIDE : 'a.editing_hide',
        HIGHLIGHT : 'a.editing_highlight',
        INSTANCENAME : 'span.instancename',
        LIGHTBOX : 'lightbox',
        MODINDENTCOUNT : 'mod-indent-',
        MODINDENTDIV : 'div.mod-indent',
        MODINDENTHUGE : 'mod-indent-huge',
        MODULEIDPREFIX : 'module-',
        MOVELEFT : 'a.editing_moveleft',
        MOVELEFTCLASS : 'editing_moveleft',
        MOVERIGHT : 'a.editing_moveright',
        PAGECONTENT : 'div#page-content',
        RIGHTSIDE : '.right',
        SECTIONHIDDENCLASS : 'hidden',
        SECTIONIDPREFIX : 'section-',
        SECTIONLI : 'li.section',
        SHOW : 'a.editing_show',
        SHOWHIDE : 'a.editing_showhide',
        CONDITIONALHIDDEN : 'conditionalhidden',
        AVAILABILITYINFODIV : 'div.availabilityinfo',
        SHOWCLASS : 'editing_show',
        HIDECLASS : 'hide'
    };

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
         * Toggle the visibility and availability for the specified
         * resource show/hide button
         */
        toggle_hide_resource_ui : function(button) {
            var element = button.ancestor(CSS.ACTIVITYLI);
            var hideicon = button.one('img');

            var dimarea;
            var toggle_class;
            if (this.get_instance_name(element) == null) {
                toggle_class = CSS.DIMMEDTEXT;
                dimarea = element.all(CSS.MODINDENTDIV + ' > div').item(1);
            } else {
                toggle_class = CSS.DIMCLASS;
                dimarea = element.one('a');
            }

            var status = '';
            var value;
            if (button.hasClass(CSS.SHOWCLASS)) {
                status = 'hide';
                value = 1;
            } else {
                status = 'show';
                value = 0;
            }
            // Update button info.
            var newstring = M.util.get_string(status, 'moodle');
            hideicon.setAttrs({
                'alt' : newstring,
                'src'   : M.util.image_url('t/' + status)
            });
            button.set('title', newstring);
            button.set('className', 'editing_'+status);

            // If activity is conditionally hidden, then don't toggle.
            if (!dimarea.hasClass(CSS.CONDITIONALHIDDEN)) {
                // Change the UI.
                dimarea.toggleClass(toggle_class);
                // We need to toggle dimming on the description too.
                element.all(CSS.CONTENTAFTERLINK).toggleClass(CSS.DIMMEDTEXT);
            }
            // Toggle availablity info for conditional activities.
            var availabilityinfo = element.one(CSS.AVAILABILITYINFODIV);

            if (availabilityinfo) {
                availabilityinfo.toggleClass(CSS.HIDECLASS);
            }
            return value;
        },
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
            if (target.one(CSS.INSTANCENAME)) {
                return target.one(CSS.INSTANCENAME).get('firstChild').get('data');
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


    var RESOURCETOOLBOX = function() {
        RESOURCETOOLBOX.superclass.constructor.apply(this, arguments);
    }

    Y.extend(RESOURCETOOLBOX, TOOLBOX, {
        // Variables
        GROUPS_NONE     : 0,
        GROUPS_SEPARATE : 1,
        GROUPS_VISIBLE  : 2,

        /**
         * Initialize the resource toolbox
         *
         * Updates all span.commands with relevant handlers and other required changes
         */
        initializer : function(config) {
            this.setup_for_resource();
            M.course.coursebase.register_module(this);

            var prefix = CSS.ACTIVITYLI + ' ' + CSS.COMMANDSPAN + ' ';
            Y.delegate('click', this.edit_resource_title, CSS.PAGECONTENT, prefix + CSS.EDITTITLE, this);
            Y.delegate('click', this.move_left, CSS.PAGECONTENT, prefix + CSS.MOVELEFT, this);
            Y.delegate('click', this.move_right, CSS.PAGECONTENT, prefix + CSS.MOVERIGHT, this);
            Y.delegate('click', this.delete_resource, CSS.PAGECONTENT, prefix + CSS.DELETE, this);
            Y.delegate('click', this.toggle_hide_resource, CSS.PAGECONTENT, prefix + CSS.HIDE, this);
            Y.delegate('click', this.toggle_hide_resource, CSS.PAGECONTENT, prefix + CSS.SHOW, this);
            Y.delegate('click', this.toggle_groupmode, CSS.PAGECONTENT, prefix + CSS.GROUPSNONE, this);
            Y.delegate('click', this.toggle_groupmode, CSS.PAGECONTENT, prefix + CSS.GROUPSSEPARATE, this);
            Y.delegate('click', this.toggle_groupmode, CSS.PAGECONTENT, prefix + CSS.GROUPSVISIBLE, this);
        },

        /**
         * Update any span.commands within the scope of the specified
         * selector with AJAX equivelants
         *
         * @param baseselector The selector to limit scope to
         * @return void
         */
        setup_for_resource : function(baseselector) {
            if (!baseselector) {
                var baseselector = CSS.PAGECONTENT + ' ' + CSS.ACTIVITYLI;
            }

            Y.all(baseselector).each(this._setup_for_resource, this);
        },
        _setup_for_resource : function(toolboxtarget) {
            toolboxtarget = Y.one(toolboxtarget);

            // Set groupmode attribute for use by this.toggle_groupmode()
            var groups;
            groups = toolboxtarget.all(CSS.COMMANDSPAN + ' ' + CSS.GROUPSNONE);
            groups.setAttribute('groupmode', this.GROUPS_NONE);

            groups = toolboxtarget.all(CSS.COMMANDSPAN + ' ' + CSS.GROUPSSEPARATE);
            groups.setAttribute('groupmode', this.GROUPS_SEPARATE);

            groups = toolboxtarget.all(CSS.COMMANDSPAN + ' ' + CSS.GROUPSVISIBLE);
            groups.setAttribute('groupmode', this.GROUPS_VISIBLE);
        },
        move_left : function(e) {
            this.move_leftright(e, -1);
        },
        move_right : function(e) {
            this.move_leftright(e, 1);
        },
        move_leftright : function(e, direction) {
            // Prevent the default button action
            e.preventDefault();

            // Get the element we're working on
            var element = e.target.ancestor(CSS.ACTIVITYLI);

            // And we need to determine the current and new indent level
            var indentdiv = element.one(CSS.MODINDENTDIV);
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
                'id'    : this.get_element_id(element)
            };
            var spinner = M.util.add_spinner(Y, element.one(CSS.SPINNERCOMMANDSPAN));
            this.send_request(data, spinner);

            // Handle removal/addition of the moveleft button
            if (newindent == 0) {
                element.one(CSS.MOVELEFT).remove();
            } else if (newindent == 1 && oldindent == 0) {
                this.add_moveleft(element);
            }

            // Handle massive indentation to match non-ajax display
            var hashugeclass = indentdiv.hasClass(CSS.MODINDENTHUGE);
            if (newindent > 15 && !hashugeclass) {
                indentdiv.addClass(CSS.MODINDENTHUGE);
            } else if (newindent <= 15 && hashugeclass) {
                indentdiv.removeClass(CSS.MODINDENTHUGE);
            }
        },
        delete_resource : function(e) {
            // Prevent the default button action
            e.preventDefault();

            // Get the element we're working on
            var element   = e.target.ancestor(CSS.ACTIVITYLI);

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
        },
        toggle_hide_resource : function(e) {
            // Prevent the default button action
            e.preventDefault();

            // Return early if the current section is hidden
            var section = e.target.ancestor(M.course.format.get_section_selector(Y));
            if (section && section.hasClass(CSS.SECTIONHIDDENCLASS)) {
                return;
            }

            // Get the element we're working on
            var element = e.target.ancestor(CSS.ACTIVITYLI);

            var button = e.target.ancestor('a', true);

            var value = this.toggle_hide_resource_ui(button);

            // Send the request
            var data = {
                'class' : 'resource',
                'field' : 'visible',
                'value' : value,
                'id'    : this.get_element_id(element)
            };
            var spinner = M.util.add_spinner(Y, element.one(CSS.SPINNERCOMMANDSPAN));
            this.send_request(data, spinner);
            return false; // Need to return false to stop the delegate for the new state firing
        },
        toggle_groupmode : function(e) {
            // Prevent the default button action
            e.preventDefault();

            // Get the element we're working on
            var element = e.target.ancestor(CSS.ACTIVITYLI);

            var button = e.target.ancestor('a', true);
            var icon = button.one('img');

            // Current Mode
            var groupmode = button.getAttribute('groupmode');
            groupmode++;
            if (groupmode > 2) {
                groupmode = 0;
            }
            button.setAttribute('groupmode', groupmode);

            var newtitle = '';
            var iconsrc = '';
            switch (groupmode) {
                case this.GROUPS_NONE:
                    newtitle = 'groupsnone';
                    iconsrc = M.util.image_url('t/groupn');
                    break;
                case this.GROUPS_SEPARATE:
                    newtitle = 'groupsseparate';
                    iconsrc = M.util.image_url('t/groups');
                    break;
                case this.GROUPS_VISIBLE:
                    newtitle = 'groupsvisible';
                    iconsrc = M.util.image_url('t/groupv');
                    break;
            }
            newtitle = M.util.get_string('clicktochangeinbrackets', 'moodle',
                    M.util.get_string(newtitle, 'moodle'));

            // Change the UI
            icon.setAttrs({
                'alt' : newtitle,
                'src' : iconsrc
            });
            button.setAttribute('title', newtitle);

            // And send the request
            var data = {
                'class' : 'resource',
                'field' : 'groupmode',
                'value' : groupmode,
                'id'    : this.get_element_id(element)
            };
            var spinner = M.util.add_spinner(Y, element.one(CSS.SPINNERCOMMANDSPAN));
            this.send_request(data, spinner);
            return false; // Need to return false to stop the delegate for the new state firing
        },
        /**
         * Add the moveleft button
         * This is required after moving left from an initial position of 0
         *
         * @param target The encapsulating <li> element
         */
        add_moveleft : function(target) {
            var left_string = M.util.get_string('moveleft', 'moodle');
            var moveimage = 't/left'; // ltr mode
            if ( Y.one(document.body).hasClass('dir-rtl') ) {
                moveimage = 't/right';
            } else {
                moveimage = 't/left';
            }
            var newicon = Y.Node.create('<img />')
                .addClass(CSS.GENERICICONCLASS)
                .setAttrs({
                    'src'   : M.util.image_url(moveimage, 'moodle'),
                    'alt'   : left_string
                });
            var moveright = target.one(CSS.MOVERIGHT);
            var newlink = moveright.getAttribute('href').replace('indent=1', 'indent=-1');
            var anchor = new Y.Node.create('<a />')
                .setStyle('cursor', 'pointer')
                .addClass(CSS.MOVELEFTCLASS)
                .setAttribute('href', newlink)
                .setAttribute('title', left_string);
            anchor.appendChild(newicon);
            moveright.insert(anchor, 'before');
        },
        /**
         * Edit the title for the resource
         */
        edit_resource_title : function(e) {
            // Get the element we're working on
            var element = e.target.ancestor(CSS.ACTIVITYLI);
            var elementdiv = element.one('div');
            var instancename  = element.one(CSS.INSTANCENAME);
            var currenttitle = instancename.get('firstChild');
            var oldtitle = currenttitle.get('data');
            var titletext = oldtitle;
            var editbutton = element.one('a.' + CSS.EDITTITLECLASS + ' img');

            // Handle events for edit_resource_title
            var listenevents = [];
            var thisevent;

            // Grab the anchor so that we can swap it with the edit form
            var anchor = instancename.ancestor('a');

            var data = {
                'class'   : 'resource',
                'field'   : 'gettitle',
                'id'      : this.get_element_id(element)
            };

            // Try to retrieve the existing string from the server
            var response = this.send_request(data, editbutton);
            if (response.instancename) {
                titletext = response.instancename;
            }

            // Create the editor and submit button
            var editor = Y.Node.create('<input />')
                .setAttrs({
                    'name'  : 'title',
                    'value' : titletext,
                    'autocomplete' : 'off',
                    'aria-describedby' : 'id_editinstructions',
                    'maxLength' : '255'
                })
                .addClass('titleeditor');
            var editform = Y.Node.create('<form />')
                .addClass('activityinstance')
                .setAttribute('action', '#');
            var editinstructions = Y.Node.create('<span />')
                .addClass('editinstructions')
                .setAttrs({'id' : 'id_editinstructions'})
                .set('innerHTML', M.util.get_string('edittitleinstructions', 'moodle'));
            var activityicon = element.one('img.activityicon').cloneNode();

            // Clear the existing content and put the editor in
            currenttitle.set('data', '');
            editform.appendChild(activityicon);
            editform.appendChild(editor);
            anchor.replace(editform);
            elementdiv.appendChild(editinstructions);
            e.preventDefault();

            // Focus and select the editor text
            editor.focus().select();

            // Handle removal of the editor
            var clear_edittitle = function() {
                // Detach all listen events to prevent duplicate triggers
                var thisevent;
                while (thisevent = listenevents.shift()) {
                    thisevent.detach();
                }

                if (editinstructions) {
                    // Convert back to anchor and remove instructions
                    editform.replace(anchor);
                    editinstructions.remove();
                    editinstructions = null;
                }
            }

            // Handle cancellation of the editor
            var cancel_edittitle = function(e) {
                clear_edittitle();

                // Set the title and anchor back to their previous settings
                currenttitle.set('data', oldtitle);
            };

            // Cancel the edit if we lose focus or the escape key is pressed
            thisevent = editor.on('blur', cancel_edittitle);
            listenevents.push(thisevent);
            thisevent = Y.one('document').on('keydown', function(e) {
                if (e.keyCode === 27) {
                    e.preventDefault();
                    cancel_edittitle(e);
                }
            });
            listenevents.push(thisevent);

            // Handle form submission
            thisevent = editform.on('submit', function(e) {
                // We don't actually want to submit anything
                e.preventDefault();

                // Clear the edit title boxes
                clear_edittitle();

                // We only accept strings which have valid content
                var newtitle = Y.Lang.trim(editor.get('value'));
                if (newtitle != null && newtitle != "" && newtitle != titletext) {
                    var data = {
                        'class'   : 'resource',
                        'field'   : 'updatetitle',
                        'title'   : newtitle,
                        'id'      : this.get_element_id(element)
                    };
                    var response = this.send_request(data, editbutton);
                    if (response.instancename) {
                        currenttitle.set('data', response.instancename);
                    }
                } else {
                    // Invalid content. Set the title back to it's original contents
                    currenttitle.set('data', oldtitle);
                }
            }, this);
            listenevents.push(thisevent);
        },
        /**
         * Set the visibility of the current resource (identified by the element)
         * to match the hidden parameter (this is not a toggle).
         * Only changes the visibility in the browser (no ajax update).
         * @param args An object with 'element' being the A node containing the resource
         *             and 'visible' being the state that the visiblity should be set to.
         * @return void
         */
        set_visibility_resource_ui: function(args) {
            var element = args.element;
            var shouldbevisible = args.visible;
            var buttonnode = element.one(CSS.SHOW);
            var visible = (buttonnode === null);
            if (visible) {
                buttonnode = element.one(CSS.HIDE);
            }
            if (visible != shouldbevisible) {
                this.toggle_hide_resource_ui(buttonnode);
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
            Y.delegate('click', this.toggle_highlight, CSS.PAGECONTENT, CSS.SECTIONLI + ' ' + CSS.HIGHLIGHT, this);
            // Section Visibility
            Y.delegate('click', this.toggle_hide_section, CSS.PAGECONTENT, CSS.SECTIONLI + ' ' + CSS.SHOWHIDE, this);
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
                var baseselector = CSS.PAGECONTENT;
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
            var status;

            if (!section.hasClass(CSS.SECTIONHIDDENCLASS)) {
                section.addClass(CSS.SECTIONHIDDENCLASS);
                value = 0;
                status = 'show';

            } else {
                section.removeClass(CSS.SECTIONHIDDENCLASS);
                value = 1;
                status = 'hide';
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

            var activities = section.all(CSS.ACTIVITYLI);
            activities.each(function(node) {
                if (node.one(CSS.SHOW)) {
                    var button = node.one(CSS.SHOW);
                } else {
                    var button = node.one(CSS.HIDE);
                }
                var activityid = this.get_element_id(node);

                if (Y.Array.indexOf(response.resourcestotoggle, activityid) != -1) {
                    this.toggle_hide_resource_ui(button);
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
            Y.one(CSS.PAGECONTENT)
                .all(M.course.format.get_section_selector(Y) + '.current ' + CSS.HIGHLIGHT)
                .set('title', old_string);
            Y.one(CSS.PAGECONTENT)
                .all(M.course.format.get_section_selector(Y) + '.current ' + CSS.HIGHLIGHT + ' img')
                .set('alt', old_string)
                .set('src', M.util.image_url('i/marker'));

            // Remove the highlighting from all sections
            var allsections = Y.one(CSS.PAGECONTENT).all(M.course.format.get_section_selector(Y))
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
