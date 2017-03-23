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

        this.samenodelabel = {
            identifier: 'afterresource',
            component: 'moodle'
        };
        this.parentnodelabel = {
            identifier: 'totopofsection',
            component: 'moodle'
        };

        // Go through all sections
        var sectionlistselector = M.course.format.get_section_selector(Y);
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
                // Keep it inside the .course-content
                constrain: '#' + CSS.PAGECONTENT
            });
            del.dd.plug(Y.Plugin.DDWinScroll);

            M.course.coursebase.register_module(this);
            M.course.dragres = this;
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
            // See if resources ul exists, if not create one
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
            var draggroups = resourcesnode.getData('draggroups');
            if (!draggroups) {
                // This Drop Node has not been set up. Configure it now.
                resourcesnode.setAttribute('data-draggroups', this.groups.join(' '));
                // Define empty ul as droptarget, so that item could be moved to empty list
                new Y.DD.Drop({
                    node: resourcesnode,
                    groups: this.groups,
                    padding: '20 0 20 0'
                });
            }

            // Replace move icons
            var move = resourcesnode.one('a.' + CSS.EDITINGMOVE);
            if (move) {
                var sr = move.getData('sectionreturn');
                move.replace(this.get_drag_handle(M.util.get_string('movecoursemodule', 'moodle'),
                             CSS.EDITINGMOVE, CSS.ICONCLASS, true).setAttribute('data-sectionreturn', sr));
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
        // on the last last ghost node location, e.drag and e.drop should be
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
        params.courseId = this.get('courseid');
        params['class'] = 'resource';
        params.field = 'move';
        params.id = Number(Y.Moodle.core_course.util.cm.getId(dragnode));
        params.sectionId = Y.Moodle.core_course.util.section.getId(dropnode.ancestor(M.course.format.get_section_wrapper(Y), true));

        if (dragnode.next()) {
            params.beforeId = Number(Y.Moodle.core_course.util.cm.getId(dragnode.next()));
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
                    M.course.coursebase.invoke_function('set_visibility_resource_ui', params);
                    this.unlock_drag_handle(drag, CSS.EDITINGMOVE);
                    window.setTimeout(function() {
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
            context: this
        });
    }
}, {
    NAME: 'course-dragdrop-resource',
    ATTRS: {
        courseid: {
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

M.course = M.course || {};
M.course.init_resource_dragdrop = function(params) {
    new DRAGRESOURCE(params);
};
