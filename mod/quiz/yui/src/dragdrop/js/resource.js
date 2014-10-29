/**
 * Resource drag and drop.
 *
 * @class M.course.dragdrop.resource
 * @constructor
 * @extends M.core.dragdrop
 */
var DRAGRESOURCE = function() {
    DRAGRESOURCE.superclass.constructor.apply(this, arguments);
};
Y.extend(DRAGRESOURCE, M.core.dragdrop, {
    initializer: function() {
        // Set group for parent class
        this.groups = ['resource'];
        this.samenodeclass = CSS.ACTIVITY;
        this.parentnodeclass = CSS.SECTION;
        this.resourcedraghandle = this.get_drag_handle(M.str.moodle.move, CSS.EDITINGMOVE, CSS.ICONCLASS, true);

        this.samenodelabel = {
            identifier: 'dragtoafter',
            component: 'quiz'
        };
        this.parentnodelabel = {
            identifier: 'dragtostart',
            component: 'quiz'
        };

        // Go through all sections
        var sectionlistselector = M.mod_quiz.edit.get_section_selector(Y);
        if (sectionlistselector) {
            sectionlistselector = '.' + CSS.COURSECONTENT + ' ' + sectionlistselector;
            this.setup_for_section(sectionlistselector);

            // Initialise drag & drop for all resources/activities
            var nodeselector = sectionlistselector.slice(CSS.COURSECONTENT.length + 2) + ' li.' + CSS.ACTIVITY;
            var del = new Y.DD.Delegate({
                container: '.' + CSS.COURSECONTENT,
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
                // Keep it inside the .mod-quiz-edit-content
                constrain: '#' + CSS.SLOTS
            });
            del.dd.plug(Y.Plugin.DDWinScroll);

            M.mod_quiz.quizbase.register_module(this);
            M.mod_quiz.dragres = this;
        }
    },

    /**
     * Apply dragdrop features to the specified selector or node that refers to section(s)
     *
     * @method setup_for_section
     * @param {String} baseselector The CSS selector or node to limit scope to
     */
    setup_for_section: function(baseselector) {
        Y.Node.all(baseselector).each(function(sectionnode) {
            var resources = sectionnode.one('.' + CSS.CONTENT + ' ul.' + CSS.SECTION);
            // See if resources ul exists, if not create one.
            if (!resources) {
                resources = Y.Node.create('<ul></ul>');
                resources.addClass(CSS.SECTION);
                sectionnode.one('.' + CSS.CONTENT + ' div.' + CSS.SUMMARY).insert(resources, 'after');
            }
            resources.setAttribute('data-draggroups', this.groups.join(' '));
            // Define empty ul as droptarget, so that item could be moved to empty list
            new Y.DD.Drop({
                node: resources,
                groups: this.groups,
                padding: '20 0 20 0'
            });

            // Initialise each resource/activity in this section
            this.setup_for_resource('#' + sectionnode.get('id') + ' li.' + CSS.ACTIVITY);
        }, this);
    },

    /**
     * Apply dragdrop features to the specified selector or node that refers to resource(s)
     *
     * @method setup_for_resource
     * @param {String} baseselector The CSS selector or node to limit scope to
     */
    setup_for_resource: function(baseselector) {
        Y.Node.all(baseselector).each(function(resourcesnode) {
            // Replace move icons
            var move = resourcesnode.one('a.' + CSS.EDITINGMOVE);
            if (move) {
                move.replace(this.resourcedraghandle.cloneNode(true));
            }
        }, this);
    },

    drag_start: function(e) {
        // Get our drag object
        var drag = e.target;
        drag.get('dragNode').setContent(drag.get('node').get('innerHTML'));
        drag.get('dragNode').all('img.iconsmall').setStyle('vertical-align', 'baseline');
    },

    drag_dropmiss: function(e) {
        // Missed the target, but we assume the user intended to drop it
        // on the last ghost node location, e.drag and e.drop should be
        // prepared by global_drag_dropmiss parent so simulate drop_hit(e).
        this.drop_hit(e);
    },

    drop_hit: function(e) {
        var drag = e.drag;
        // Get a reference to our drag node
        var dragnode = drag.get('node');
        var dropnode = e.drop.get('node');

        // Add spinner if it not there
        var actionarea = dragnode.one(CSS.ACTIONAREA);
        var spinner = M.util.add_spinner(Y, actionarea);

        var params = {};

        // Handle any variables which we must pass back through to
        var pageparams = this.get('config').pageparams;
        var varname;
        for (varname in pageparams) {
            params[varname] = pageparams[varname];
        }

        // Prepare request parameters
        params.sesskey = M.cfg.sesskey;
        params.courseid = this.get('courseid');
        params.quizid = this.get('quizid');
        params['class'] = 'resource';
        params.field = 'move';
        params.id = Number(Y.Moodle.mod_quiz.util.slot.getId(dragnode));
        params.sectionId = Y.Moodle.core_course.util.section.getId(dropnode.ancestor(M.mod_quiz.edit.get_section_wrapper(Y), true));

        var previousslot = dragnode.previous(SELECTOR.SLOT);
        if (previousslot) {
            params.previousid = Number(Y.Moodle.mod_quiz.util.slot.getId(previousslot));
        }

        var previouspage = dragnode.previous(SELECTOR.PAGE);
        if (previouspage) {
            params.page = Number(Y.Moodle.mod_quiz.util.page.getId(previouspage));
        }

        // Do AJAX request
        var uri = M.cfg.wwwroot + this.get('ajaxurl');

        Y.io(uri, {
            method: 'POST',
            data: params,
            on: {
                start: function() {
                    this.lock_drag_handle(drag, CSS.EDITINGMOVE);
                    spinner.show();
                },
                success: function(tid, response) {
                    var responsetext = Y.JSON.parse(response.responseText);
                    var params = {element: dragnode, visible: responsetext.visible};
                    M.mod_quiz.quizbase.invoke_function('set_visibility_resource_ui', params);
                    this.unlock_drag_handle(drag, CSS.EDITINGMOVE);
                    window.setTimeout(function() {
                        spinner.hide();
                    }, 250);
                    M.mod_quiz.resource_toolbox.reorganise_edit_page();
                },
                failure: function(tid, response) {
                    this.ajax_failure(response);
                    this.unlock_drag_handle(drag, CSS.SECTIONHANDLE);
                    spinner.hide();
                    window.location.reload(true);
                }
            },
            context:this
        });
    },

    global_drop_over: function(e) {
        //Overriding parent method so we can stop the slots being dragged before the first page node.

        // Check that drop object belong to correct group.
        if (!e.drop || !e.drop.inGroup(this.groups)) {
            return;
        }

        // Get a reference to our drag and drop nodes.
        var drag = e.drag.get('node'),
            drop = e.drop.get('node');

        // Save last drop target for the case of missed target processing.
        this.lastdroptarget = e.drop;

        // Are we dropping within the same parent node?
        if (drop.hasClass(this.samenodeclass)) {
            var where;

            if (this.goingup) {
                where = "before";
            } else {
                where = "after";
            }

            drop.insert(drag, where);
        } else if ((drop.hasClass(this.parentnodeclass) || drop.test('[data-droptarget="1"]')) && !drop.contains(drag)) {
            // We are dropping on parent node and it is empty
            if (this.goingup) {
                drop.append(drag);
            } else {
                drop.prepend(drag);
            }
        }
        this.drop_over(e);
    }
}, {
    NAME: 'mod_quiz-dragdrop-resource',
    ATTRS: {
        courseid: {
            value: null
        },
        quizid: {
            value: null
        },
        ajaxurl: {
            value: 0
        },
        config: {
            value: 0
        }
    }
});

M.mod_quiz = M.mod_quiz || {};
M.mod_quiz.init_resource_dragdrop = function(params) {
    new DRAGRESOURCE(params);
};
