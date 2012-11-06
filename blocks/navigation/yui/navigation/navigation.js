YUI.add('moodle-block_navigation-navigation', function(Y){

/**
 * A 'actionkey' Event to help with Y.delegate().
 * The event consists of the left arrow, right arrow, enter and space keys.
 * More keys can be mapped to action meanings.
 * actions: collapse , expand, toggle, enter.
 *
 * This event is delegated to branches in the navigation tree.
 * The on() method to subscribe allows specifying the desired trigger actions as JSON.
 *
 * Todo: This could be centralised, a similar Event is defined in blocks/dock.js
 */
Y.Event.define("actionkey", {
   // Webkit and IE repeat keydown when you hold down arrow keys.
    // Opera links keypress to page scroll; others keydown.
    // Firefox prevents page scroll via preventDefault() on either
    // keydown or keypress.
    _event: (Y.UA.webkit || Y.UA.ie) ? 'keydown' : 'keypress',

    _keys: {
        //arrows
        '37': 'collapse',
        '39': 'expand',
        //(@todo: lrt/rtl/M.core_dock.cfg.orientation decision to assign arrow to meanings)
        '32': 'toggle',
        '13': 'enter'
    },

    _keyHandler: function (e, notifier, args) {
        if (!args.actions) {
            var actObj = {collapse:true, expand:true, toggle:true, enter:true};
        } else {
            var actObj = args.actions;
        }
        if (this._keys[e.keyCode] && actObj[this._keys[e.keyCode]]) {
            e.action = this._keys[e.keyCode];
            notifier.fire(e);
        }
    },

    on: function (node, sub, notifier) {
        // subscribe to _event and ask keyHandler to handle with given args[0] (the desired actions).
        if (sub.args == null) {
            //no actions given
            sub._detacher = node.on(this._event, this._keyHandler,this, notifier, {actions:false});
        } else {
            sub._detacher = node.on(this._event, this._keyHandler,this, notifier, sub.args[0]);
        }
    },

    detach: function (node, sub, notifier) {
        //detach our _detacher handle of the subscription made in on()
        sub._detacher.detach();
    },

    delegate: function (node, sub, notifier, filter) {
        // subscribe to _event and ask keyHandler to handle with given args[0] (the desired actions).
        if (sub.args == null) {
            //no actions given
            sub._delegateDetacher = node.delegate(this._event, this._keyHandler,filter, this, notifier, {actions:false});
        } else {
            sub._delegateDetacher = node.delegate(this._event, this._keyHandler,filter, this, notifier, sub.args[0]);
        }
    },

    detachDelegate: function (node, sub, notifier) {
        sub._delegateDetacher.detach();
    }
});

var EXPANSIONLIMIT_EVERYTHING = 0,
    EXPANSIONLIMIT_COURSE     = 20,
    EXPANSIONLIMIT_SECTION    = 30,
    EXPANSIONLIMIT_ACTIVITY   = 40;

/**
 * Mappings for the different types of nodes coming from the navigation.
 * Copied from lib/navigationlib.php navigation_node constants.
 * @type object
 */
var NODETYPE = {
    /** @type int Root node = 0 */
    ROOTNODE : 0,
    /** @type int System context = 1 */
    SYSTEM : 1,
    /** @type int Course category = 10 */
    CATEGORY : 10,
    /** @type int Course = 20 */
    COURSE : 20,
    /** @type int Course section = 30 */
    SECTION : 30,
    /** @type int Activity (course module) = 40 */
    ACTIVITY : 40,
    /** @type int Resource (course module = 50 */
    RESOURCE : 50,
    /** @type int Custom node (could be anything) = 60 */
    CUSTOM : 60,
    /** @type int Setting = 70 */
    SETTING : 70,
    /** @type int User context = 80 */
    USER : 80,
    /** @type int Container = 90 */
    CONTAINER : 90
}

/**
 * Navigation tree class.
 *
 * This class establishes the tree initially, creating expandable branches as
 * required, and delegating the expand/collapse event.
 */
var TREE = function(config) {
    TREE.superclass.constructor.apply(this, arguments);
}
TREE.prototype = {
    /**
     * The tree's ID, normally its block instance id.
     */
    id : null,
    /**
     * Initialise the tree object when its first created.
     */
    initializer : function(config) {
        this.id = config.id;

        var node = Y.one('#inst'+config.id);

        // Can't find the block instance within the page
        if (node === null) {
            return;
        }

        // Delegate event to toggle expansion
        var self = this;
        Y.delegate('click', function(e){self.toggleExpansion(e);}, node.one('.block_tree'), '.tree_item.branch');
        Y.delegate('actionkey', function(e){self.toggleExpansion(e);}, node.one('.block_tree'), '.tree_item.branch');

        // Gather the expandable branches ready for initialisation.
        var expansions = [];
        if (config.expansions) {
            expansions = config.expansions;
        } else if (window['navtreeexpansions'+config.id]) {
            expansions = window['navtreeexpansions'+config.id];
        }
        // Establish each expandable branch as a tree branch.
        for (var i in expansions) {
            new BRANCH({
                tree:this,
                branchobj:expansions[i],
                overrides : {
                    expandable : true,
                    children : [],
                    haschildren : true
                }
            }).wire();
            M.block_navigation.expandablebranchcount++;
        }

        // Call the generic blocks init method to add all the generic stuff
        if (this.get('candock')) {
            this.initialise_block(Y, node);
        }
    },
    /**
     * This is a callback function responsible for expanding and collapsing the
     * branches of the tree. It is delegated to rather than multiple event handles.
     */
    toggleExpansion : function(e) {
        // First check if they managed to click on the li iteslf, then find the closest
        // LI ancestor and use that

        if (e.target.test('a') && (e.keyCode == 0 || e.keyCode == 13)) {
            // A link has been clicked (or keypress is 'enter') don't fire any more events just do the default.
            e.stopPropagation();
            return;
        }

        // Makes sure we can get to the LI containing the branch.
        var target = e.target;
        if (!target.test('li')) {
            target = target.ancestor('li')
        }
        if (!target) {
            return;
        }

        // Toggle expand/collapse providing its not a root level branch.
        if (!target.hasClass('depth_1')) {
            if (e.type == 'actionkey') {
                switch (e.action) {
                    case 'expand' :
                        target.removeClass('collapsed');
                        target.set('aria-expanded', true);
                        break;
                    case 'collapse' :
                        target.addClass('collapsed');
                        target.set('aria-expanded', false);
                        break;
                    default :
                        target.toggleClass('collapsed');
                        target.set('aria-expanded', !target.hasClass('collapsed'));
                }
                e.halt();
            } else {
                target.toggleClass('collapsed');
                target.set('aria-expanded', !target.hasClass('collapsed'));
            }
        }

        // If the accordian feature has been enabled collapse all siblings.
        if (this.get('accordian')) {
            target.siblings('li').each(function(){
                if (this.get('id') !== target.get('id') && !this.hasClass('collapsed')) {
                    this.addClass('collapsed');
                    this.set('aria-expanded', false);
                }
            });
        }

        // If this block can dock tell the dock to resize if required and check
        // the width on the dock panel in case it is presently in use.
        if (this.get('candock')) {
            M.core_dock.resize();
            var panel = M.core_dock.getPanel();
            if (panel.visible) {
                panel.correctWidth();
            }
        }
    }
}
// The tree extends the YUI base foundation.
Y.extend(TREE, Y.Base, TREE.prototype, {
    NAME : 'navigation-tree',
    ATTRS : {
        instance : {
            value : null
        },
        candock : {
            validator : Y.Lang.isBool,
            value : false
        },
        accordian : {
            validator : Y.Lang.isBool,
            value : false
        },
        expansionlimit : {
            value : 0,
            setter : function(val) {
                return parseInt(val);
            }
        }
    }
});
if (M.core_dock && M.core_dock.genericblock) {
    Y.augment(TREE, M.core_dock.genericblock);
}

/**
 * The tree branch class.
 * This class is used to manage a tree branch, in particular its ability to load
 * its contents by AJAX.
 */
var BRANCH = function(config) {
    BRANCH.superclass.constructor.apply(this, arguments);
}
BRANCH.prototype = {
    /**
     * The node for this branch (p)
     */
    node : null,
    /**
     * A reference to the ajax load event handlers when created.
     */
    event_ajaxload : null,
    event_ajaxload_actionkey : null,
    /**
     * Initialises the branch when it is first created.
     */
    initializer : function(config) {
        if (config.branchobj !== null) {
            // Construct from the provided xml
            for (var i in config.branchobj) {
                this.set(i, config.branchobj[i]);
            }
            var children = this.get('children');
            this.set('haschildren', (children.length > 0));
        }
        if (config.overrides !== null) {
            // Construct from the provided xml
            for (var i in config.overrides) {
                this.set(i, config.overrides[i]);
            }
        }
        // Get the node for this branch
        this.node = Y.one('#', this.get('id'));
        // Now check whether the branch is not expandable because of the expansionlimit
        var expansionlimit = this.get('tree').get('expansionlimit');
        var type = this.get('type');
        if (expansionlimit != EXPANSIONLIMIT_EVERYTHING &&  type >= expansionlimit && type <= EXPANSIONLIMIT_ACTIVITY) {
            this.set('expandable', false);
            this.set('haschildren', false);
        }
    },
    /**
     * Draws the branch within the tree.
     *
     * This function creates a DOM structure for the branch and then injects
     * it into the navigation tree at the correct point.
     */
    draw : function(element) {

        var isbranch = (this.get('expandable') || this.get('haschildren'));
        var branchli = Y.Node.create('<li></li>');
        var link = this.get('link');
        var branchp = Y.Node.create('<p class="tree_item"></p>').setAttribute('id', this.get('id'));
        if (!link) {
            //add tab focus if not link (so still one focus per menu node).
            // it was suggested to have 2 foci. one for the node and one for the link in MDL-27428.
            branchp.setAttribute('tabindex', '0');
        }
        if (isbranch) {
            branchli.addClass('collapsed').addClass('contains_branch');
            branchli.set('aria-expanded', false);
            branchp.addClass('branch');
        }

        // Prepare the icon, should be an object representing a pix_icon
        var branchicon = false;
        var icon = this.get('icon');
        if (icon && (!isbranch || this.get('type') == NODETYPE.ACTIVITY)) {
            branchicon = Y.Node.create('<img alt="" />');
            branchicon.setAttribute('src', M.util.image_url(icon.pix, icon.component));
            branchli.addClass('item_with_icon');
            if (icon.alt) {
                branchicon.setAttribute('alt', icon.alt);
            }
            if (icon.title) {
                branchicon.setAttribute('title', icon.title);
            }
            if (icon.classes) {
                for (var i in icon.classes) {
                    branchicon.addClass(icon.classes[i]);
                }
            }
        }

        if (!link) {
            if (branchicon) {
                branchp.appendChild(branchicon);
            }
            branchp.append(this.get('name'));
        } else {
            var branchlink = Y.Node.create('<a title="'+this.get('title')+'" href="'+link+'"></a>');
            if (branchicon) {
                branchlink.appendChild(branchicon);
            }
            branchlink.append(this.get('name'));
            if (this.get('hidden')) {
                branchlink.addClass('dimmed');
            }
            branchp.appendChild(branchlink);
        }

        branchli.appendChild(branchp);
        element.appendChild(branchli);
        this.node = branchp;
        return this;
    },
    /**
     * Attaches required events to the branch structure.
     */
    wire : function() {
        this.node = this.node || Y.one('#'+this.get('id'));
        if (!this.node) {
            return false;
        }
        if (this.get('expandable')) {
            this.event_ajaxload = this.node.on('ajaxload|click', this.ajaxLoad, this);
            this.event_ajaxload_actionkey = this.node.on('actionkey', this.ajaxLoad, this);
        }
        return this;
    },
    /**
     * Gets the UL element that children for this branch should be inserted into.
     */
    getChildrenUL : function() {
        var ul = this.node.next('ul');
        if (!ul) {
            ul = Y.Node.create('<ul></ul>');
            this.node.ancestor().append(ul);
        }
        return ul;
    },
    /**
     * Load the content of the branch via AJAX.
     *
     * This function calls ajaxProcessResponse with the result of the AJAX
     * request made here.
     */
    ajaxLoad : function(e) {
        if (e.type == 'actionkey' && e.action != 'enter') {
            e.halt();
        } else {
            e.stopPropagation();
        }
        if (e.type = 'actionkey' && e.action == 'enter' && e.target.test('A')) {
            this.event_ajaxload_actionkey.detach();
            this.event_ajaxload.detach();
            return true; // no ajaxLoad for enter
        }

        if (this.node.hasClass('loadingbranch')) {
            return true;
        }

        this.node.addClass('loadingbranch');

        var params = {
            elementid : this.get('id'),
            id : this.get('key'),
            type : this.get('type'),
            sesskey : M.cfg.sesskey,
            instance : this.get('tree').get('instance')
        };

        Y.io(M.cfg.wwwroot+'/lib/ajax/getnavbranch.php', {
            method:'POST',
            data:  build_querystring(params),
            on: {
                complete: this.ajaxProcessResponse
            },
            context:this
        });
        return true;
    },
    /**
     * Processes an AJAX request to load the content of this branch through
     * AJAX.
     */
    ajaxProcessResponse : function(tid, outcome) {
        this.node.removeClass('loadingbranch');
        this.event_ajaxload.detach();
        this.event_ajaxload_actionkey.detach();
        try {
            var object = Y.JSON.parse(outcome.responseText);
            if (object.children && object.children.length > 0) {
                var coursecount = 0;
                for (var i in object.children) {
                    if (typeof(object.children[i])=='object') {
                        if (object.children[i].type == NODETYPE.COURSE) {
                            coursecount++;
                        }
                        this.addChild(object.children[i]);
                    }
                }
                if ((this.get('type') == NODETYPE.CATEGORY || this.get('type') == NODETYPE.ROOTNODE) && coursecount >= M.block_navigation.courselimit) {
                    this.addViewAllCoursesChild(this);
                }
                this.get('tree').toggleExpansion({target:this.node});
                return true;
            }
        } catch (ex) {
            // If we got here then there was an error parsing the result
        }
        // The branch is empty so class it accordingly
        this.node.replaceClass('branch', 'emptybranch');
        return true;
    },
    /**
     * Turns the branch object passed to the method into a proper branch object
     * and then adds it as a child of this branch.
     */
    addChild : function(branchobj) {
        // Make the new branch into an object
        var branch = new BRANCH({tree:this.get('tree'), branchobj:branchobj});
        if (branch.draw(this.getChildrenUL())) {
            branch.wire();
            var count = 0, i, children = branch.get('children');
            for (i in children) {
                // Add each branch to the tree
                if (children[i].type == NODETYPE.COURSE) {
                    count++;
                }
                if (typeof(children[i])=='object') {
                    branch.addChild(children[i]);
                }
            }
            if (branch.get('type') == NODETYPE.CATEGORY && count >= M.block_navigation.courselimit) {
                this.addViewAllCoursesChild(branch);
            }
        }
        return true;
    },

    /**
     * Add a link to view all courses in a category
     */
    addViewAllCoursesChild: function(branch) {
        var url = null;
        if (branch.get('type') == NODETYPE.ROOTNODE) {
            if (branch.get('key') === 'mycourses') {
                url = M.cfg.wwwroot + '/my';
            } else {
                url = M.cfg.wwwroot + '/course/index.php';
            }
        } else {
            url = M.cfg.wwwroot+'/course/category.php?id=' + branch.get('key');
        }
        branch.addChild({
            name : M.str.moodle.viewallcourses,
            title : M.str.moodle.viewallcourses,
            link : url,
            haschildren : false,
            icon : {'pix':"i/navigationitem",'component':'moodle'}
        });
    }
}
Y.extend(BRANCH, Y.Base, BRANCH.prototype, {
    NAME : 'navigation-branch',
    ATTRS : {
        tree : {
            validator : Y.Lang.isObject
        },
        name : {
            value : '',
            validator : Y.Lang.isString,
            setter : function(val) {
                return val.replace(/\n/g, '<br />');
            }
        },
        title : {
            value : '',
            validator : Y.Lang.isString
        },
        id : {
            value : '',
            validator : Y.Lang.isString,
            getter : function(val) {
                if (val == '') {
                    val = 'expandable_branch_'+M.block_navigation.expandablebranchcount;
                    M.block_navigation.expandablebranchcount++;
                }
                return val;
            }
        },
        key : {
            value : null
        },
        type : {
            value : null
        },
        link : {
            value : false
        },
        icon : {
            value : false,
            validator : Y.Lang.isObject
        },
        expandable : {
            value : false,
            validator : Y.Lang.isBool
        },
        hidden : {
            value : false,
            validator : Y.Lang.isBool
        },
        haschildren : {
            value : false,
            validator : Y.Lang.isBool
        },
        children : {
            value : [],
            validator : Y.Lang.isArray
        }
    }
});

/**
 * This namespace will contain all of the contents of the navigation blocks
 * global navigation and settings.
 * @namespace
 */
M.block_navigation = M.block_navigation || {
    /** The number of expandable branches in existence */
    expandablebranchcount:1,
    courselimit : 20,
    instance : null,
    /**
     * Add new instance of navigation tree to tree collection
     */
    init_add_tree:function(properties) {
        if (properties.courselimit) {
            this.courselimit = properties.courselimit;
        }
        if (M.core_dock) {
            M.core_dock.init(Y);
        }
        new TREE(properties);
    }
};

}, '@VERSION@', {requires:['base', 'core_dock', 'io-base', 'node', 'dom', 'event-custom', 'event-delegate', 'json-parse']});
