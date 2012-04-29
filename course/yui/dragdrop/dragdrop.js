YUI.add('moodle-course-dragdrop', function(Y) {

    var CSS = {
        ACTIVITY : 'activity',
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
        SUMMARY : 'summary',
        TOPICS : 'topics',
        WEEKDATES: 'weekdates'
    };

    var DRAGSECTION = function() {
        DRAGSECTION.superclass.constructor.apply(this, arguments);
    };
    Y.extend(DRAGSECTION, M.core.dragdrop, {
        initializer : function(params) {
            // Set group for parent class
            this.groups = ['section'];
            this.samenodeclass = CSS.SECTION;
            this.parentnodeclass = CSS.TOPICS;

            // Check if we are in single section mode
            if (Y.Node.one('.'+CSS.JUMPMENU)) {
                return false;
            }
            // Initialise sections dragging
            this.setup_for_section('.'+CSS.COURSECONTENT+' li.'+CSS.SECTION);
            M.course.coursebase.register_module(this);
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
                    if (movedown) {
                        movedown.remove();
                    }
                    var moveup = sectionnode.one('.'+CSS.RIGHT+' a.'+CSS.MOVEUP);
                    if (moveup) {
                        moveup.remove();
                    }
                    // Add dragger icon
                    var title = M.util.get_string('movesection', 'moodle', sectionid);
                    var cssleft = sectionnode.one('.'+CSS.LEFT);
                    cssleft.setStyle('cursor', 'move');
                    cssleft.appendChild(Y.Node.create('<br />'));
                    cssleft.appendChild(this.get_drag_handle(title, CSS.SECTIONHANDLE));

                    // Make each li element in the lists of sections draggable
                    var dd = new Y.DD.Drag({
                        node: sectionnode,
                        // Make each li a Drop target too
                        groups: this.groups,
                        target: true,
                        handles: ['.'+CSS.LEFT]
                    }).plug(Y.Plugin.DDProxy, {
                        // Don't move the node at the end of the drag
                        moveOnEnd: false
                    }).plug(Y.Plugin.DDConstrained, {
                        // Keep it inside the .course-content
                        constrain: '#'+CSS.PAGECONTENT,
                        stickY: true
                    });
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
            var ul = Y.Node.create('<ul></ul>');
            ul.addClass(CSS.TOPICS);
            var li = Y.Node.create('<li></li>');
            li.addClass(CSS.SECTION);
            li.setStyle('margin', 0);
            li.setContent(drag.get('node').get('innerHTML'));
            ul.appendChild(li);
            drag.get('dragNode').setContent(ul);
            drag.get('dragNode').addClass(CSS.COURSECONTENT);
        },

        drop_hit : function(e) {
            var drag = e.drag;
            // Get a reference to our drag node
            var dragnode = drag.get('node');
            var dropnode = e.drop.get('node');
            // Prepare some variables
            var dragnodeid = Number(this.get_section_id(dragnode));
            var dropnodeid = Number(this.get_section_id(dropnode));

            var targetoffset = 0;
            var loopstart = dragnodeid;
            var loopend = dropnodeid;

            if (this.goingup) {
                targetoffset = 1;
                loopstart = dropnodeid;
                loopend = dragnodeid;
            }

            // Get the list of nodes
            drag.get('dragNode').removeClass(CSS.COURSECONTENT);
            var sectionlist = Y.Node.all('.'+CSS.COURSECONTENT+' li.'+CSS.SECTION);

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
            params.value = dropnodeid - targetoffset;

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
                        window.setTimeout(function(e) {
                            lightbox.hide();
                        }, 250);
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
                                    // Swap left block
                                    sectionlist.item(i-1).one('.'+CSS.LEFT).swap(sectionlist.item(i).one('.'+CSS.LEFT));
                                    // Swap right block
                                    sectionlist.item(i-1).one('.'+CSS.RIGHT).swap(sectionlist.item(i).one('.'+CSS.RIGHT));
                                    // Swap menus
                                    sectionlist.item(i-1).one('.'+CSS.SECTIONADDMENUS).swap(sectionlist.item(i).one('.'+CSS.SECTIONADDMENUS));
                                    // Swap week dates if in weekly format
                                    var weekdates = sectionlist.item(i-1).one('.'+CSS.WEEKDATES);
                                    if (weekdates) {
                                        weekdates.swap(sectionlist.item(i).one('.'+CSS.WEEKDATES));
                                    }
                                    // Update flag
                                    swapped = true;
                                }
                            }
                            loopend = loopend - 1;
                        } while (swapped);
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

            // Go through all sections
            this.setup_for_section('.'+CSS.COURSECONTENT+' li.'+CSS.SECTION);
            M.course.coursebase.register_module(this);
            M.course.dragres = this;
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

                // Define each ul as droptarget, so that item could be moved to empty list
                var tar = new Y.DD.Drop({
                    node: resources,
                    groups: this.groups,
                    padding: '20 0 20 0'
                });
                // Go through each li element and make them draggable
                this.setup_for_resource('li#'+sectionnode.get('id')+' li.'+CSS.ACTIVITY);
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
                    move.replace(this.get_drag_handle(M.str.moodle.move, CSS.EDITINGMOVE, CSS.ICONCLASS));
                    // Make each li element in the lists of sections draggable
                    var dd = new Y.DD.Drag({
                        node: resourcesnode,
                        groups: this.groups,
                        // Make each li a Drop target too
                        target: true,
                        handles: ['.' + CSS.EDITINGMOVE]
                    }).plug(Y.Plugin.DDProxy, {
                        // Don't move the node at the end of the drag
                        moveOnEnd: false
                    }).plug(Y.Plugin.DDConstrained, {
                        // Keep it inside the .course-content
                        constrain: '#'+CSS.PAGECONTENT
                    });
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

        drop_hit : function(e) {
            var drag = e.drag;
            // Get a reference to our drag node
            var dragnode = drag.get('node');
            var dropnode = e.drop.get('node');

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
            params.sectionId = this.get_section_id(dropnode.ancestor('li.'+CSS.SECTION));

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
                    },
                    success: function(tid, response) {
                        this.unlock_drag_handle(drag, CSS.EDITINGMOVE);
                    },
                    failure: function(tid, response) {
                        this.ajax_failure(response);
                        this.unlock_drag_handle(drag, CSS.SECTIONHANDLE);
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

    M.core_course = M.core_course || {};
    M.core_course.init_resource_dragdrop = function(params) {
        new DRAGRESOURCE(params);
    }
    M.core_course.init_section_dragdrop = function(params) {
        new DRAGSECTION(params);
    }
}, '@VERSION@', {requires:['base', 'node', 'io', 'dom', 'dd', 'moodle-core-dragdrop', 'moodle-enrol-notification', 'moodle-course-coursebase']});
