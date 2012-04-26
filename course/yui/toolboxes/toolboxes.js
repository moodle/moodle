YUI.add('moodle-course-toolboxes', function(Y) {
    WAITICON = {'pix':"i/loading_small",'component':'moodle'};
    // The CSS selectors we use
    var CSS = {
        ACTIVITYLI : 'li.activity',
        COMMANDSPAN : 'span.commands',
        CONTENTAFTERLINK : 'div.contentafterlink',
        DELETE : 'a.editing_delete',
        DIMCLASS : 'dimmed',
        DIMMEDTEXT : 'dimmed_text',
        EDITTITLECLASS : 'edittitle',
        GENERICICONCLASS : 'iconsmall',
        GROUPSNONE : 'a.editing_groupsnone',
        GROUPSSEPARATE : 'a.editing_groupsseparate',
        GROUPSVISIBLE : 'a.editing_groupsvisible',
        HASLABEL : 'label',
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
        RIGHTDIV : 'div.right',
        SECTIONHIDDENCLASS : 'hidden',
        SECTIONIDPREFIX : 'section-',
        SECTIONLI : 'li.section',
        SHOW : 'a.editing_show',
        SHOWHIDE : 'a.editing_showhide'
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
         * Replace the button click at the selector with the specified
         * callback
         *
         * @param toolboxtarget The selector of the working area
         * @param selector The 'button' to replace
         * @param callback The callback to apply
         * @param cursor An optional cursor style to apply
         */
        replace_button : function(toolboxtarget, selector, callback, cursor) {
            if (!cursor) {
                // Set the default cursor type to pointer to match the
                // anchor
                cursor = 'pointer';
            }
            var button = Y.one(toolboxtarget).all(selector)
                .removeAttribute('href')
                .setStyle('cursor', cursor);

            // on isn't chainable and will return an event
            button.on('click', callback, this);

            return button;
        },
          /**
           * Toggle the visibility and availability for the specified
           * resource show/hide button
           */
        toggle_hide_resource_ui : function(button) {
            var element = button.ancestor(CSS.ACTIVITYLI);
            var hideicon = button.one('img');

            var dimarea;
            var toggle_class;
            if (this.is_label(element)) {
                toggle_class = CSS.DIMMEDTEXT;
                dimarea = element.one(CSS.MODINDENTDIV + ' div');
            } else {
                toggle_class = CSS.DIMCLASS;
                dimarea = element.one('a');
            }

            var status = '';
            var value;
            if (dimarea.hasClass(toggle_class)) {
                status = 'hide';
                value = 1;
            } else {
                status = 'show';
                value = 0;
            }

            // Change the UI
            dimarea.toggleClass(toggle_class);
            // We need to toggle dimming on the description too
            element.all(CSS.CONTENTAFTERLINK).toggleClass(CSS.DIMMEDTEXT);
            var newstring = M.util.get_string(status, 'moodle');
            hideicon.setAttrs({
                'alt' : newstring,
                'title' : newstring,
                'src'   : M.util.image_url('t/' + status)
            });
            button.set('title', newstring);
            button.set('className', 'editing_'+status);

            return value;
        },
        /**
         * Send a request using the REST API
         *
         * @param data The data to submit
         * @param loadingiconat (optional) Show the loading icon spinner at the specified location (replaces text)
         * @param lightbox (optional) A lightbox which may contain a section loader
         * @param optionalconfig (optional) Any additional configuration to submit
         * @return response responseText field from responce
         */
        send_request : function(data, loadingiconat, lightbox, optionalconfig) {
            // Default data structure
            if (!data) {
                data = {};
            }
            // Handle any variables which we must pass back through to
            var pageparams = this.get('config').pageparams;
            for (varname in pageparams) {
                data[varname] = pageparams[varname];
            }

            // Make a note of the icon for displaying the loadingicon spinner
            var originalicon;
            if (loadingiconat) {
                originalicon = loadingiconat.getAttribute('src');
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
                        if (originalicon) {
                            // Replace the spinner with the original icon We use a pause to give
                            // positive feedback that something is happening
                            window.setTimeout(function(e) {
                                loadingiconat.setAttribute('src', originalicon);
                            }, 250);
                        }
                        if (lightbox) {
                            window.setTimeout(function(e) {
                                lightbox.hide();
                            }, 250);
                        }
                    },
                    failure : function(tid, response) {
                        if (originalicon) {
                            loadingiconat.setAttribute('src', originalicon);
                        }
                        if (lightbox) {
                            lightbox.hide();
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

            if (loadingiconat) {
                loadingiconat.removeAttribute('innerHTML');
                loadingiconat.set('src', M.util.image_url(WAITICON.pix, WAITICON.component));
            }

            // Send the request
            Y.io(uri, config);
            return responsetext;
        },
        is_label : function(target) {
            return target.hasClass(CSS.HASLABEL);
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
                var baseselector = CSS.PAGECONTENT;
            }

            Y.all(baseselector).each(this._setup_for_resource, this);
        },
        _setup_for_resource : function(toolboxtarget) {
            // Move left and right
            this.replace_button(toolboxtarget, CSS.COMMANDSPAN + ' ' + CSS.MOVELEFT, this.move_left);
            this.replace_button(toolboxtarget, CSS.COMMANDSPAN + ' ' + CSS.MOVERIGHT, this.move_right);

            // Delete
            this.replace_button(toolboxtarget, CSS.COMMANDSPAN + ' ' + CSS.DELETE, this.delete_resource);

            // Show/Hide
            var showhide = this.replace_button(toolboxtarget, CSS.COMMANDSPAN + ' ' + CSS.HIDE, this.toggle_hide_resource);
            var shown = this.replace_button(toolboxtarget, CSS.COMMANDSPAN + ' ' + CSS.SHOW, this.toggle_hide_resource);

            showhide = showhide.concat(shown);
            showhide.each(function(node) {
                var section = node.ancestor(CSS.SECTIONLI);
                if (section && section.hasClass(CSS.SECTIONHIDDENCLASS)) {
                    node.setStyle('cursor', 'auto');
                }
            });

            // Change Group Mode
            var groups;
            groups = this.replace_button(toolboxtarget, CSS.COMMANDSPAN + ' ' + CSS.GROUPSNONE, this.toggle_groupmode);
            groups.setAttribute('groupmode', this.GROUPS_NONE);

            groups = this.replace_button(toolboxtarget, CSS.COMMANDSPAN + ' ' + CSS.GROUPSSEPARATE, this.toggle_groupmode);
            groups.setAttribute('groupmode', this.GROUPS_SEPARATE);

            groups = this.replace_button(toolboxtarget, CSS.COMMANDSPAN + ' ' + CSS.GROUPSVISIBLE, this.toggle_groupmode);
            groups.setAttribute('groupmode', this.GROUPS_VISIBLE);
        },
        move_left : function(e) {
            this.move_leftright(e, -1, CSS.MOVELEFT);
        },
        move_right : function(e) {
            this.move_leftright(e, 1, CSS.MOVERIGHT);
        },
        move_leftright : function(e, direction, buttonselector) {
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
            var editbutton = element.one(buttonselector + ' img');
            this.send_request(data, editbutton);

            // Handle removal/addition of the moveleft button
            if (newindent == 0) {
                window.setTimeout(function(e) {
                    element.one(CSS.MOVELEFT).remove();
                }, 250);
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
            // Get the element we're working on
            var element   = e.target.ancestor(CSS.ACTIVITYLI);

            var confirmstring = '';
            if (this.is_label(element)) {
                // Labels are slightly different to other activities
                var plugindata = {
                    type : M.util.get_string('pluginname', 'label')
                }
                confirmstring = M.util.get_string('deletechecktype', 'moodle', plugindata)
            } else {
                var plugindata = {
                    type : M.util.get_string('pluginname', element.getAttribute('class').match(/modtype_([^\s]*)/)[1]),
                    name : element.one(CSS.INSTANCENAME).get('firstChild').get('data')
                }
                confirmstring = M.util.get_string('deletechecktypename', 'moodle', plugindata);
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
            // Return early if the current section is hidden
            var section = e.target.ancestor(CSS.SECTIONLI);
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
            this.send_request(data, button.one('img'));
        },
        toggle_groupmode : function(e) {
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
                'title' : newtitle,
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
            this.send_request(data, icon);
        },
        /**
         * Add the moveleft button
         * This is required after moving left from an initial position of 0
         *
         * @param target The encapsulating <li> element
         */
        add_moveleft : function(target) {
            var left_string = M.util.get_string('moveleft', 'moodle');
            var newicon = Y.Node.create('<img />')
                .addClass(CSS.GENERICICONCLASS)
                .setAttrs({
                    'src'   : M.util.image_url('t/left', 'moodle'),
                    'title' : left_string,
                    'alt'   : left_string
                });
            var anchor = new Y.Node.create('<a />')
                .setStyle('cursor', 'pointer')
                .addClass(CSS.MOVELEFTCLASS)
                .set('title', left_string);
            anchor.appendChild(newicon);
            anchor.on('click', this.move_left, this);
            target.one(CSS.MOVERIGHT).insert(anchor, 'before');
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
        },
        /**
         * Update any section areas within the scope of the specified
         * selector with AJAX equivelants
         *
         * @param baseselector The selector to limit scope to
         * @return void
         */
        setup_for_section : function(baseselector) {
            if (!baseselector) {
                var baseselector = CSS.PAGECONTENT;
            }

            Y.all(baseselector).each(this._setup_for_section, this);
        },
        _setup_for_section : function(toolboxtarget) {
            // Section Highlighting
            this.replace_button(toolboxtarget, CSS.RIGHTDIV + ' ' + CSS.HIGHLIGHT, this.toggle_highlight);

            // Section Visibility
            this.replace_button(toolboxtarget, CSS.RIGHTDIV + ' ' + CSS.SHOWHIDE, this.toggle_hide_section);
        },
        toggle_hide_section : function(e) {
            // Get the section we're working on
            var section = e.target.ancestor(CSS.SECTIONLI);
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
                'title' : newstring,
                'src'   : M.util.image_url('i/' + status)
            });
            button.set('title', newstring);

            // Change the highlight status
            var data = {
                'class' : 'section',
                'field' : 'visible',
                'id'    : this.get_section_id(section),
                'value' : value
            };

            var lightbox = M.util.add_lightbox(Y, section);
            lightbox.show();

            var response = this.send_request(data, null, lightbox);

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

                if (value == 0) {
                    button.setStyle('cursor', 'auto');
                } else {
                    button.setStyle('cursor', 'pointer');
                }
            }, this);
        },
        toggle_highlight : function(e) {
            // Get the section we're working on
            var section = e.target.ancestor(CSS.SECTIONLI);
            var button = e.target.ancestor('a', true);
            var buttonicon = button.one('img');

            // Determine whether the marker is currently set
            var togglestatus = section.hasClass('current');
            var value = 0;

            // Set the current highlighted item text
            var old_string = M.util.get_string('markthistopic', 'moodle');
            Y.one(CSS.PAGECONTENT)
                .all(CSS.SECTIONLI + '.current ' + CSS.HIGHLIGHT)
                .set('title', old_string);
            Y.one(CSS.PAGECONTENT)
                .all(CSS.SECTIONLI + '.current ' + CSS.HIGHLIGHT + ' img')
                .set('title', old_string)
                .set('alt', old_string)
                .set('src', M.util.image_url('i/marker'));

            // Remove the highlighting from all sections
            var allsections = Y.one(CSS.PAGECONTENT).all(CSS.SECTIONLI)
                .removeClass('current');

            // Then add it if required to the selected section
            if (!togglestatus) {
                section.addClass('current');
                value = this.get_section_id(section);
                var new_string = M.util.get_string('markedthistopic', 'moodle');
                button
                    .set('title', new_string);
                buttonicon
                    .set('title', new_string)
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
            this.send_request(data, null, lightbox);
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
