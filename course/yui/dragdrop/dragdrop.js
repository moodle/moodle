YUI.add('moodle-course-dragdrop', function(Y) {

    var CSS = {
        ACTIVITY : 'activity',
        COMMANDSPAN : 'span.commands',
        CONTENT : 'content',
        COURSECONTENT : 'course-content',
        EDITINGMOVE : 'editing_move',
        ICONCLASS : 'iconsmall',
        JUMPMENU : 'jumpmenu',
        LEFT : 'left',
        LIGHTBOX : 'lightbox',
        MOVEDOWN : 'movedown',
        MOVEUP : 'moveup',
        PAGECONTENT : 'page-content',
        RIGHT : 'right',
        SECTION : 'section',
        SECTIONADDMENUS : 'section_add_menus',
        SECTIONHANDLE : 'section-handle',
        SUMMARY : 'summary'
    };

    var DRAGSECTION = function() {
        DRAGSECTION.superclass.constructor.apply(this, arguments);
    };
    Y.extend(DRAGSECTION, M.core.dragdrop, {
        sectionlistselector : null,

        initializer : function(params) {
            // Set group for parent class
            this.groups = ['section'];
            this.samenodeclass = M.course.format.get_sectionwrapperclass();
            this.parentnodeclass = M.course.format.get_containerclass();

            // Check if we are in single section mode
            if (Y.Node.one('.'+CSS.JUMPMENU)) {
                return false;
            }
            // Initialise sections dragging
            this.sectionlistselector = M.course.format.get_section_wrapper(Y);
            if (this.sectionlistselector) {
                this.sectionlistselector = '.'+CSS.COURSECONTENT+' '+this.sectionlistselector;
                this.setup_for_section(this.sectionlistselector);

                // Make each li element in the lists of sections draggable
                var nodeselector = this.sectionlistselector.slice(CSS.COURSECONTENT.length+2);
                var del = new Y.DD.Delegate({
                    container: '.'+CSS.COURSECONTENT,
                    nodes: nodeselector,
                    target: true,
                    handles: ['.'+CSS.LEFT],
                    dragConfig: {groups: this.groups}
                });
                del.dd.plug(Y.Plugin.DDProxy, {
                    // Don't move the node at the end of the drag
                    moveOnEnd: false
                });
                del.dd.plug(Y.Plugin.DDConstrained, {
                    // Keep it inside the .course-content
                    constrain: '#'+CSS.PAGECONTENT,
                    stickY: true
                });
                del.dd.plug(Y.Plugin.DDWinScroll);
            }
        },

         /**
         * Apply dragdrop features to the specified selector or node that refers to section(s)
         *
         * @param baseselector The CSS selector or node to limit scope to
         * @return void
         */
        setup_for_section : function(baseselector) {
            Y.Node.all(baseselector).each(function(sectionnode) {
                // Determine the section ID
                var sectionid = this.get_section_id(sectionnode);

                // We skip the top section as it is not draggable
                if (sectionid > 0) {
                    // Remove move icons
                    var movedown = sectionnode.one('.'+CSS.RIGHT+' a.'+CSS.MOVEDOWN);
                    var moveup = sectionnode.one('.'+CSS.RIGHT+' a.'+CSS.MOVEUP);

                    // Add dragger icon
                    var title = M.util.get_string('movesection', 'moodle', sectionid);
                    var cssleft = sectionnode.one('.'+CSS.LEFT);

                    if ((movedown || moveup) && cssleft) {
                        cssleft.setStyle('cursor', 'move');
                        cssleft.appendChild(this.get_drag_handle(title, CSS.SECTIONHANDLE, 'icon', true));

                        if (moveup) {
                            moveup.remove();
                        }
                        if (movedown) {
                            movedown.remove();
                        }
                    }
                }
            }, this);
        },

        get_section_id : function(node) {
            return Number(node.get('id').replace(/section-/i, ''));
        },

        /*
         * Drag-dropping related functions
         */
        drag_start : function(e) {
            // Get our drag object
            var drag = e.target;
            // Creat a dummy structure of the outer elemnents for clean styles application
            var containernode = Y.Node.create('<'+M.course.format.get_containernode()+'></'+M.course.format.get_containernode()+'>');
            containernode.addClass(M.course.format.get_containerclass());
            var sectionnode = Y.Node.create('<'+ M.course.format.get_sectionwrappernode()+'></'+ M.course.format.get_sectionwrappernode()+'>');
            sectionnode.addClass( M.course.format.get_sectionwrapperclass());
            sectionnode.setStyle('margin', 0);
            sectionnode.setContent(drag.get('node').get('innerHTML'));
            containernode.appendChild(sectionnode);
            drag.get('dragNode').setContent(containernode);
            drag.get('dragNode').addClass(CSS.COURSECONTENT);
        },

        drag_dropmiss : function(e) {
            // Missed the target, but we assume the user intended to drop it
            // on the last last ghost node location, e.drag and e.drop should be
            // prepared by global_drag_dropmiss parent so simulate drop_hit(e).
            this.drop_hit(e);
        },

        drop_hit : function(e) {
            var drag = e.drag;
            // Get a reference to our drag node
            var dragnode = drag.get('node');
            var dropnode = e.drop.get('node');
            // Prepare some variables
            var dragnodeid = Number(this.get_section_id(dragnode));
            var dropnodeid = Number(this.get_section_id(dropnode));

            var loopstart = dragnodeid;
            var loopend = dropnodeid;

            if (this.goingup) {
                loopstart = dropnodeid;
                loopend = dragnodeid;
            }

            // Get the list of nodes
            drag.get('dragNode').removeClass(CSS.COURSECONTENT);
            var sectionlist = Y.Node.all(this.sectionlistselector);

            // Add lightbox if it not there
            var lightbox = M.util.add_lightbox(Y, dragnode);

            var params = {};

            // Handle any variables which we must pass back through to
            var pageparams = this.get('config').pageparams;
            for (varname in pageparams) {
                params[varname] = pageparams[varname];
            }

            // Prepare request parameters
            params.sesskey = M.cfg.sesskey;
            params.courseId = this.get('courseid');
            params['class'] = 'section';
            params.field = 'move';
            params.id = dragnodeid;
            params.value = dropnodeid;

            // Do AJAX request
            var uri = M.cfg.wwwroot + this.get('ajaxurl');

            Y.io(uri, {
                method: 'POST',
                data: params,
                on: {
                    start : function(tid) {
                        lightbox.show();
                    },
                    success: function(tid, response) {
                        // Update section titles, we can't simply swap them as
                        // they might have custom title
                        try {
                            var responsetext = Y.JSON.parse(response.responseText);
                            if (responsetext.error) {
                                new M.core.ajaxException(responsetext);
                            }
                            M.course.format.process_sections(Y, sectionlist, responsetext, loopstart, loopend);
                        } catch (e) {}

                        // Classic bubble sort algorithm is applied to the section
                        // nodes between original drag node location and the new one.
                        do {
                            var swapped = false;
                            for (var i = loopstart; i <= loopend; i++) {
                                if (this.get_section_id(sectionlist.item(i-1)) > this.get_section_id(sectionlist.item(i))) {
                                    // Swap section id
                                    var sectionid = sectionlist.item(i-1).get('id');
                                    sectionlist.item(i-1).set('id', sectionlist.item(i).get('id'));
                                    sectionlist.item(i).set('id', sectionid);
                                    // See what format needs to swap
                                    M.course.format.swap_sections(Y, i-1, i);
                                    // Update flag
                                    swapped = true;
                                }
                            }
                            loopend = loopend - 1;
                        } while (swapped);

                        // Finally, hide the lightbox
                        window.setTimeout(function(e) {
                            lightbox.hide();
                        }, 250);
                    },
                    failure: function(tid, response) {
                        this.ajax_failure(response);
                        lightbox.hide();
                    }
                },
                context:this
            });
        }

    }, {
        NAME : 'course-dragdrop-section',
        ATTRS : {
            courseid : {
                value : null
            },
            ajaxurl : {
                'value' : 0
            },
            config : {
                'value' : 0
            }
        }
    });

    var DRAGRESOURCE = function() {
        DRAGRESOURCE.superclass.constructor.apply(this, arguments);
    };
    Y.extend(DRAGRESOURCE, M.core.dragdrop, {
        initializer : function(params) {
            // Set group for parent class
            this.groups = ['resource'];
            this.samenodeclass = CSS.ACTIVITY;
            this.parentnodeclass = CSS.SECTION;
            this.resourcedraghandle = this.get_drag_handle(M.str.moodle.move, CSS.EDITINGMOVE, CSS.ICONCLASS);

            // Go through all sections
            var sectionlistselector = M.course.format.get_section_selector(Y);
            if (sectionlistselector) {
                sectionlistselector = '.'+CSS.COURSECONTENT+' '+sectionlistselector;
                this.setup_for_section(sectionlistselector);

                // Initialise drag & drop for all resources/activities
                var nodeselector = sectionlistselector.slice(CSS.COURSECONTENT.length+2)+' li.'+CSS.ACTIVITY;
                var del = new Y.DD.Delegate({
                    container: '.'+CSS.COURSECONTENT,
                    nodes: nodeselector,
                    target: true,
                    handles: ['.' + CSS.EDITINGMOVE],
                    dragConfig: {groups: this.groups}
                });
                del.dd.plug(Y.Plugin.DDProxy, {
                    // Don't move the node at the end of the drag
                    moveOnEnd: false,
                    cloneNode: true
                });
                del.dd.plug(Y.Plugin.DDConstrained, {
                    // Keep it inside the .course-content
                    constrain: '#'+CSS.PAGECONTENT
                });
                del.dd.plug(Y.Plugin.DDWinScroll);

                M.course.coursebase.register_module(this);
                M.course.dragres = this;
            }
        },

         /**
         * Apply dragdrop features to the specified selector or node that refers to section(s)
         *
         * @param baseselector The CSS selector or node to limit scope to
         * @return void
         */
        setup_for_section : function(baseselector) {
            Y.Node.all(baseselector).each(function(sectionnode) {
                var resources = sectionnode.one('.'+CSS.CONTENT+' ul.'+CSS.SECTION);
                // See if resources ul exists, if not create one
                if (!resources) {
                    var resources = Y.Node.create('<ul></ul>');
                    resources.addClass(CSS.SECTION);
                    sectionnode.one('.'+CSS.CONTENT+' div.'+CSS.SUMMARY).insert(resources, 'after');
                }
                // Define empty ul as droptarget, so that item could be moved to empty list
                var tar = new Y.DD.Drop({
                    node: resources,
                    groups: this.groups,
                    padding: '20 0 20 0'
                });

                // Initialise each resource/activity in this section
                this.setup_for_resource('#'+sectionnode.get('id')+' li.'+CSS.ACTIVITY);
            }, this);
        },
        /**
         * Apply dragdrop features to the specified selector or node that refers to resource(s)
         *
         * @param baseselector The CSS selector or node to limit scope to
         * @return void
         */
        setup_for_resource : function(baseselector) {
            Y.Node.all(baseselector).each(function(resourcesnode) {
                // Replace move icons
                var move = resourcesnode.one('a.'+CSS.EDITINGMOVE);
                if (move) {
                    move.replace(this.resourcedraghandle.cloneNode(true));
                }
            }, this);
        },

        get_section_id : function(node) {
            return Number(node.get('id').replace(/section-/i, ''));
        },

        get_resource_id : function(node) {
            return Number(node.get('id').replace(/module-/i, ''));
        },

        drag_start : function(e) {
            // Get our drag object
            var drag = e.target;
            drag.get('dragNode').setContent(drag.get('node').get('innerHTML'));
            drag.get('dragNode').all('img.iconsmall').setStyle('vertical-align', 'baseline');
        },

        drag_dropmiss : function(e) {
            // Missed the target, but we assume the user intended to drop it
            // on the last last ghost node location, e.drag and e.drop should be
            // prepared by global_drag_dropmiss parent so simulate drop_hit(e).
            this.drop_hit(e);
        },

        drop_hit : function(e) {
            var drag = e.drag;
            // Get a reference to our drag node
            var dragnode = drag.get('node');
            var dropnode = e.drop.get('node');

            // Add spinner if it not there
            var spinner = M.util.add_spinner(Y, dragnode.one(CSS.COMMANDSPAN));

            var params = {};

            // Handle any variables which we must pass back through to
            var pageparams = this.get('config').pageparams;
            for (varname in pageparams) {
                params[varname] = pageparams[varname];
            }

            // Prepare request parameters
            params.sesskey = M.cfg.sesskey;
            params.courseId = this.get('courseid');
            params['class'] = 'resource';
            params.field = 'move';
            params.id = Number(this.get_resource_id(dragnode));
            params.sectionId = this.get_section_id(dropnode.ancestor(M.course.format.get_section_wrapper(Y), true));

            if (dragnode.next()) {
                params.beforeId = Number(this.get_resource_id(dragnode.next()));
            }

            // Do AJAX request
            var uri = M.cfg.wwwroot + this.get('ajaxurl');

            Y.io(uri, {
                method: 'POST',
                data: params,
                on: {
                    start : function(tid) {
                        this.lock_drag_handle(drag, CSS.EDITINGMOVE);
                        spinner.show();
                    },
                    success: function(tid, response) {
                        var responsetext = Y.JSON.parse(response.responseText);
                        var params = {element: dragnode, visible: responsetext.visible};
                        M.course.coursebase.invoke_function('set_visibility_resource_ui', params);
                        this.unlock_drag_handle(drag, CSS.EDITINGMOVE);
                        window.setTimeout(function(e) {
                            spinner.hide();
                        }, 250);
                    },
                    failure: function(tid, response) {
                        this.ajax_failure(response);
                        this.unlock_drag_handle(drag, CSS.SECTIONHANDLE);
                        spinner.hide();
                        // TODO: revert nodes location
                    }
                },
                context:this
            });
        }
    }, {
        NAME : 'course-dragdrop-resource',
        ATTRS : {
            courseid : {
                value : null
            },
            ajaxurl : {
                'value' : 0
            },
            config : {
                'value' : 0
            }
        }
    });

    M.course = M.course || {};
    M.course.init_resource_dragdrop = function(params) {
        new DRAGRESOURCE(params);
    }
    M.course.init_section_dragdrop = function(params) {
        new DRAGSECTION(params);
    }
}, '@VERSION@', {requires:['base', 'node', 'io', 'dom', 'dd', 'dd-scroll', 'moodle-core-dragdrop', 'moodle-core-notification', 'moodle-course-coursebase']});
