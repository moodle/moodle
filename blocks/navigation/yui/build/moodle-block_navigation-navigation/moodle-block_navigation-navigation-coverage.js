if (typeof _yuitest_coverage == "undefined"){
    _yuitest_coverage = {};
    _yuitest_coverline = function(src, line){
        var coverage = _yuitest_coverage[src];
        if (!coverage.lines[line]){
            coverage.calledLines++;
        }
        coverage.lines[line]++;
    };
    _yuitest_coverfunc = function(src, name, line){
        var coverage = _yuitest_coverage[src],
            funcId = name + ":" + line;
        if (!coverage.functions[funcId]){
            coverage.calledFunctions++;
        }
        coverage.functions[funcId]++;
    };
}
_yuitest_coverage["build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js"] = {
    lines: {},
    functions: {},
    coveredLines: 0,
    calledLines: 0,
    coveredFunctions: 0,
    calledFunctions: 0,
    path: "build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js",
    code: []
};
_yuitest_coverage["build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js"].code=["YUI.add('moodle-block_navigation-navigation', function (Y, NAME) {","","/**"," * A 'actionkey' Event to help with Y.delegate()."," * The event consists of the left arrow, right arrow, enter and space keys."," * More keys can be mapped to action meanings."," * actions: collapse , expand, toggle, enter."," *"," * This event is delegated to branches in the navigation tree."," * The on() method to subscribe allows specifying the desired trigger actions as JSON."," *"," * Todo: This could be centralised, a similar Event is defined in blocks/dock.js"," */","Y.Event.define(\"actionkey\", {","   // Webkit and IE repeat keydown when you hold down arrow keys.","    // Opera links keypress to page scroll; others keydown.","    // Firefox prevents page scroll via preventDefault() on either","    // keydown or keypress.","    _event: (Y.UA.webkit || Y.UA.ie) ? 'keydown' : 'keypress',","","    _keys: {","        //arrows","        '37': 'collapse',","        '39': 'expand',","        //(@todo: lrt/rtl/M.core_dock.cfg.orientation decision to assign arrow to meanings)","        '32': 'toggle',","        '13': 'enter'","    },","","    _keyHandler: function (e, notifier, args) {","        var actObj;","        if (!args.actions) {","            actObj = {collapse:true, expand:true, toggle:true, enter:true};","        } else {","            actObj = args.actions;","        }","        if (this._keys[e.keyCode] && actObj[this._keys[e.keyCode]]) {","            e.action = this._keys[e.keyCode];","            notifier.fire(e);","        }","    },","","    on: function (node, sub, notifier) {","        // subscribe to _event and ask keyHandler to handle with given args[0] (the desired actions).","        if (sub.args === null) {","            //no actions given","            sub._detacher = node.on(this._event, this._keyHandler,this, notifier, {actions:false});","        } else {","            sub._detacher = node.on(this._event, this._keyHandler,this, notifier, sub.args[0]);","        }","    },","","    detach: function (node, sub) {","        //detach our _detacher handle of the subscription made in on()","        sub._detacher.detach();","    },","","    delegate: function (node, sub, notifier, filter) {","        // subscribe to _event and ask keyHandler to handle with given args[0] (the desired actions).","        if (sub.args === null) {","            //no actions given","            sub._delegateDetacher = node.delegate(this._event, this._keyHandler,filter, this, notifier, {actions:false});","        } else {","            sub._delegateDetacher = node.delegate(this._event, this._keyHandler,filter, this, notifier, sub.args[0]);","        }","    },","","    detachDelegate: function (node, sub) {","        sub._delegateDetacher.detach();","    }","});","","var EXPANSIONLIMIT_EVERYTHING = 0,","    //EXPANSIONLIMIT_COURSE     = 20,","    //EXPANSIONLIMIT_SECTION    = 30,","    EXPANSIONLIMIT_ACTIVITY   = 40;","","/**"," * Mappings for the different types of nodes coming from the navigation."," * Copied from lib/navigationlib.php navigation_node constants."," * @type object"," */","var NODETYPE = {","    /** @type int Root node = 0 */","    ROOTNODE : 0,","    /** @type int System context = 1 */","    SYSTEM : 1,","    /** @type int Course category = 10 */","    CATEGORY : 10,","    /** @type int MYCATEGORY = 11 */","    MYCATEGORY : 11,","    /** @type int Course = 20 */","    COURSE : 20,","    /** @type int Course section = 30 */","    SECTION : 30,","    /** @type int Activity (course module) = 40 */","    ACTIVITY : 40,","    /** @type int Resource (course module = 50 */","    RESOURCE : 50,","    /** @type int Custom node (could be anything) = 60 */","    CUSTOM : 60,","    /** @type int Setting = 70 */","    SETTING : 70,","    /** @type int User context = 80 */","    USER : 80,","    /** @type int Container = 90 */","    CONTAINER : 90","};","","/**"," * Navigation tree class."," *"," * This class establishes the tree initially, creating expandable branches as"," * required, and delegating the expand/collapse event."," */","var TREE = function() {","    TREE.superclass.constructor.apply(this, arguments);","};","TREE.prototype = {","    /**","     * The tree's ID, normally its block instance id.","     */","    id : null,","    /**","     * An array of initialised branches.","     */","    branches : [],","    /**","     * Initialise the tree object when its first created.","     */","    initializer : function(config) {","        this.id = config.id;","","        var node = Y.one('#inst'+config.id);","","        // Can't find the block instance within the page","        if (node === null) {","            return;","        }","","        // Delegate event to toggle expansion","        Y.delegate('click', this.toggleExpansion, node.one('.block_tree'), '.tree_item.branch', this);","        Y.delegate('actionkey', this.toggleExpansion, node.one('.block_tree'), '.tree_item.branch', this);","","        // Gather the expandable branches ready for initialisation.","        var expansions = [];","        if (config.expansions) {","            expansions = config.expansions;","        } else if (window['navtreeexpansions'+config.id]) {","            expansions = window['navtreeexpansions'+config.id];","        }","        // Establish each expandable branch as a tree branch.","        for (var i in expansions) {","            var branch = new BRANCH({","                tree:this,","                branchobj:expansions[i],","                overrides : {","                    expandable : true,","                    children : [],","                    haschildren : true","                }","            }).wire();","            M.block_navigation.expandablebranchcount++;","            this.branches[branch.get('id')] = branch;","        }","        if (M.block_navigation.expandablebranchcount > 0) {","            // Delegate some events to handle AJAX loading.","            Y.delegate('click', this.fire_branch_action, node.one('.block_tree'), '.tree_item.branch[data-expandable]', this);","            Y.delegate('actionkey', this.fire_branch_action, node.one('.block_tree'), '.tree_item.branch[data-expandable]', this);","        }","","        // Call the generic blocks init method to add all the generic stuff","        if (this.get('candock')) {","            this.initialise_block(Y, node);","        }","    },","    /**","     * Fire actions for a branch when an event occurs.","     */","    fire_branch_action : function(event) {","        var id = event.currentTarget.getAttribute('id');","        var branch = this.branches[id];","        branch.ajaxLoad(event);","    },","    /**","     * This is a callback function responsible for expanding and collapsing the","     * branches of the tree. It is delegated to rather than multiple event handles.","     */","    toggleExpansion : function(e) {","        // First check if they managed to click on the li iteslf, then find the closest","        // LI ancestor and use that","","        if (e.target.test('a') && (e.keyCode === 0 || e.keyCode === 13)) {","            // A link has been clicked (or keypress is 'enter') don't fire any more events just do the default.","            e.stopPropagation();","            return;","        }","","        // Makes sure we can get to the LI containing the branch.","        var target = e.target;","        if (!target.test('li')) {","            target = target.ancestor('li');","        }","        if (!target) {","            return;","        }","","        // Toggle expand/collapse providing its not a root level branch.","        if (!target.hasClass('depth_1')) {","            if (e.type === 'actionkey') {","                switch (e.action) {","                    case 'expand' :","                        target.removeClass('collapsed');","                        target.set('aria-expanded', true);","                        break;","                    case 'collapse' :","                        target.addClass('collapsed');","                        target.set('aria-expanded', false);","                        break;","                    default :","                        target.toggleClass('collapsed');","                        target.set('aria-expanded', !target.hasClass('collapsed'));","                }","                e.halt();","            } else {","                target.toggleClass('collapsed');","                target.set('aria-expanded', !target.hasClass('collapsed'));","            }","        }","","        // If the accordian feature has been enabled collapse all siblings.","        if (this.get('accordian')) {","            target.siblings('li').each(function(){","                if (this.get('id') !== target.get('id') && !this.hasClass('collapsed')) {","                    this.addClass('collapsed');","                    this.set('aria-expanded', false);","                }","            });","        }","","        // If this block can dock tell the dock to resize if required and check","        // the width on the dock panel in case it is presently in use.","        if (this.get('candock')) {","            M.core_dock.resize();","            var panel = M.core_dock.getPanel();","            if (panel.visible) {","                panel.correctWidth();","            }","        }","    }","};","// The tree extends the YUI base foundation.","Y.extend(TREE, Y.Base, TREE.prototype, {","    NAME : 'navigation-tree',","    ATTRS : {","        instance : {","            value : null","        },","        candock : {","            validator : Y.Lang.isBool,","            value : false","        },","        accordian : {","            validator : Y.Lang.isBool,","            value : false","        },","        expansionlimit : {","            value : 0,","            setter : function(val) {","                return parseInt(val, 10);","            }","        }","    }","});","if (M.core_dock && M.core_dock.genericblock) {","    Y.augment(TREE, M.core_dock.genericblock);","}","","/**"," * The tree branch class."," * This class is used to manage a tree branch, in particular its ability to load"," * its contents by AJAX."," */","BRANCH = function() {","    BRANCH.superclass.constructor.apply(this, arguments);","};","BRANCH.prototype = {","    /**","     * The node for this branch (p)","     */","    node : null,","    /**","     * Initialises the branch when it is first created.","     */","    initializer : function(config) {","        var i,","            children;","        if (config.branchobj !== null) {","            // Construct from the provided xml","            for (i in config.branchobj) {","                this.set(i, config.branchobj[i]);","            }","            children = this.get('children');","            this.set('haschildren', (children.length > 0));","        }","        if (config.overrides !== null) {","            // Construct from the provided xml","            for (i in config.overrides) {","                this.set(i, config.overrides[i]);","            }","        }","        // Get the node for this branch","        this.node = Y.one('#', this.get('id'));","        // Now check whether the branch is not expandable because of the expansionlimit","        var expansionlimit = this.get('tree').get('expansionlimit');","        var type = this.get('type');","        if (expansionlimit !== EXPANSIONLIMIT_EVERYTHING &&  type >= expansionlimit && type <= EXPANSIONLIMIT_ACTIVITY) {","            this.set('expandable', false);","            this.set('haschildren', false);","        }","    },","    /**","     * Draws the branch within the tree.","     *","     * This function creates a DOM structure for the branch and then injects","     * it into the navigation tree at the correct point.","     */","    draw : function(element) {","","        var isbranch = (this.get('expandable') || this.get('haschildren'));","        var branchli = Y.Node.create('<li></li>');","        var link = this.get('link');","        var branchp = Y.Node.create('<p class=\"tree_item\"></p>').setAttribute('id', this.get('id'));","        if (!link) {","            //add tab focus if not link (so still one focus per menu node).","            // it was suggested to have 2 foci. one for the node and one for the link in MDL-27428.","            branchp.setAttribute('tabindex', '0');","        }","        if (isbranch) {","            branchli.addClass('collapsed').addClass('contains_branch');","            branchli.set('aria-expanded', false);","            branchp.addClass('branch');","        }","","        // Prepare the icon, should be an object representing a pix_icon","        var branchicon = false;","        var icon = this.get('icon');","        if (icon && (!isbranch || this.get('type') == NODETYPE.ACTIVITY)) {","            branchicon = Y.Node.create('<img alt=\"\" />');","            branchicon.setAttribute('src', M.util.image_url(icon.pix, icon.component));","            branchli.addClass('item_with_icon');","            if (icon.alt) {","                branchicon.setAttribute('alt', icon.alt);","            }","            if (icon.title) {","                branchicon.setAttribute('title', icon.title);","            }","            if (icon.classes) {","                for (var i in icon.classes) {","                    branchicon.addClass(icon.classes[i]);","                }","            }","        }","","        if (!link) {","            var branchspan = Y.Node.create('<span></span>');","            if (branchicon) {","                branchspan.appendChild(branchicon);","            }","            branchspan.append(this.get('name'));","            if (this.get('hidden')) {","                branchspan.addClass('dimmed_text');","            }","            branchp.appendChild(branchspan);","        } else {","            var branchlink = Y.Node.create('<a title=\"'+this.get('title')+'\" href=\"'+link+'\"></a>');","            if (branchicon) {","                branchlink.appendChild(branchicon);","            }","            branchlink.append(this.get('name'));","            if (this.get('hidden')) {","                branchlink.addClass('dimmed');","            }","            branchp.appendChild(branchlink);","        }","","        branchli.appendChild(branchp);","        element.appendChild(branchli);","        this.node = branchp;","        return this;","    },","    /**","     * Attaches required events to the branch structure.","     */","    wire : function() {","        this.node = this.node || Y.one('#'+this.get('id'));","        if (!this.node) {","            return false;","        }","        if (this.get('expandable')) {","            this.node.setAttribute('data-expandable', '1');","            this.node.setAttribute('data-loaded', '0');","        }","        return this;","    },","    /**","     * Gets the UL element that children for this branch should be inserted into.","     */","    getChildrenUL : function() {","        var ul = this.node.next('ul');","        if (!ul) {","            ul = Y.Node.create('<ul></ul>');","            this.node.ancestor().append(ul);","        }","        return ul;","    },","    /**","     * Load the content of the branch via AJAX.","     *","     * This function calls ajaxProcessResponse with the result of the AJAX","     * request made here.","     */","    ajaxLoad : function(e) {","        if (e.type === 'actionkey' && e.action !== 'enter') {","            e.halt();","        } else {","            e.stopPropagation();","        }","        if (e.type === 'actionkey' && e.action === 'enter' && e.target.test('A')) {","            // No ajaxLoad for enter.","            this.node.setAttribute('data-expandable', '0');","            this.node.setAttribute('data-loaded', '1');","            return true;","        }","","        if (this.node.hasClass('loadingbranch')) {","            // Already loading. Just skip.","            return true;","        }","","        if (this.node.getAttribute('data-loaded') === '1') {","            // We've already loaded this stuff.","            return true;","        }","        this.node.addClass('loadingbranch');","","        var params = {","            elementid : this.get('id'),","            id : this.get('key'),","            type : this.get('type'),","            sesskey : M.cfg.sesskey,","            instance : this.get('tree').get('instance')","        };","","        Y.io(M.cfg.wwwroot+'/lib/ajax/getnavbranch.php', {","            method:'POST',","            data:  build_querystring(params),","            on: {","                complete: this.ajaxProcessResponse","            },","            context:this","        });","        return true;","    },","    /**","     * Processes an AJAX request to load the content of this branch through","     * AJAX.","     */","    ajaxProcessResponse : function(tid, outcome) {","        this.node.removeClass('loadingbranch');","        this.node.setAttribute('data-loaded', '1');","        try {","            var object = Y.JSON.parse(outcome.responseText);","            if (object.children && object.children.length > 0) {","                var coursecount = 0;","                for (var i in object.children) {","                    if (typeof(object.children[i])==='object') {","                        if (object.children[i].type == NODETYPE.COURSE) {","                            coursecount++;","                        }","                        this.addChild(object.children[i]);","                    }","                }","                if ((this.get('type') == NODETYPE.CATEGORY || this.get('type') == NODETYPE.ROOTNODE || this.get('type') == NODETYPE.MYCATEGORY)","                    && coursecount >= M.block_navigation.courselimit) {","                    this.addViewAllCoursesChild(this);","                }","                return true;","            }","        } catch (ex) {","            // If we got here then there was an error parsing the result","        }","        // The branch is empty so class it accordingly","        this.node.replaceClass('branch', 'emptybranch');","        return true;","    },","    /**","     * Turns the branch object passed to the method into a proper branch object","     * and then adds it as a child of this branch.","     */","    addChild : function(branchobj) {","        // Make the new branch into an object","        var branch = new BRANCH({tree:this.get('tree'), branchobj:branchobj});","        if (branch.draw(this.getChildrenUL())) {","            this.get('tree').branches[branch.get('id')] = branch;","            branch.wire();","            var count = 0, i, children = branch.get('children');","            for (i in children) {","                // Add each branch to the tree","                if (children[i].type == NODETYPE.COURSE) {","                    count++;","                }","                if (typeof(children[i]) === 'object') {","                    branch.addChild(children[i]);","                }","            }","            if ((branch.get('type') == NODETYPE.CATEGORY || branch.get('type') == NODETYPE.MYCATEGORY)","                && count >= M.block_navigation.courselimit) {","                this.addViewAllCoursesChild(branch);","            }","        }","        return true;","    },","","    /**","     * Add a link to view all courses in a category","     */","    addViewAllCoursesChild: function(branch) {","        var url = null;","        if (branch.get('type') == NODETYPE.ROOTNODE) {","            if (branch.get('key') === 'mycourses') {","                url = M.cfg.wwwroot + '/my';","            } else {","                url = M.cfg.wwwroot + '/course/index.php';","            }","        } else {","            url = M.cfg.wwwroot+'/course/index.php?categoryid=' + branch.get('key');","        }","        branch.addChild({","            name : M.str.moodle.viewallcourses,","            title : M.str.moodle.viewallcourses,","            link : url,","            haschildren : false,","            icon : {'pix':\"i/navigationitem\",'component':'moodle'}","        });","    }","};","Y.extend(BRANCH, Y.Base, BRANCH.prototype, {","    NAME : 'navigation-branch',","    ATTRS : {","        tree : {","            validator : Y.Lang.isObject","        },","        name : {","            value : '',","            validator : Y.Lang.isString,","            setter : function(val) {","                return val.replace(/\\n/g, '<br />');","            }","        },","        title : {","            value : '',","            validator : Y.Lang.isString","        },","        id : {","            value : '',","            validator : Y.Lang.isString,","            getter : function(val) {","                if (val === '') {","                    val = 'expandable_branch_'+M.block_navigation.expandablebranchcount;","                    M.block_navigation.expandablebranchcount++;","                }","                return val;","            }","        },","        key : {","            value : null","        },","        type : {","            value : null","        },","        link : {","            value : false","        },","        icon : {","            value : false,","            validator : Y.Lang.isObject","        },","        expandable : {","            value : false,","            validator : Y.Lang.isBool","        },","        hidden : {","            value : false,","            validator : Y.Lang.isBool","        },","        haschildren : {","            value : false,","            validator : Y.Lang.isBool","        },","        children : {","            value : [],","            validator : Y.Lang.isArray","        }","    }","});","","/**"," * This namespace will contain all of the contents of the navigation blocks"," * global navigation and settings."," * @namespace"," */","M.block_navigation = M.block_navigation || {","    /** The number of expandable branches in existence */","    expandablebranchcount:1,","    courselimit : 20,","    instance : null,","    /**","     * Add new instance of navigation tree to tree collection","     */","    init_add_tree:function(properties) {","        if (properties.courselimit) {","            this.courselimit = properties.courselimit;","        }","        if (M.core_dock) {","            M.core_dock.init(Y);","        }","        new TREE(properties);","    }","};","","","}, '@VERSION@', {","    \"requires\": [","        \"base\",","        \"core_dock\",","        \"io-base\",","        \"node\",","        \"dom\",","        \"event-custom\",","        \"event-delegate\",","        \"json-parse\"","    ]","});"];
_yuitest_coverage["build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js"].lines = {"1":0,"14":0,"31":0,"32":0,"33":0,"35":0,"37":0,"38":0,"39":0,"45":0,"47":0,"49":0,"55":0,"60":0,"62":0,"64":0,"69":0,"73":0,"83":0,"116":0,"117":0,"119":0,"132":0,"134":0,"137":0,"138":0,"142":0,"143":0,"146":0,"147":0,"148":0,"149":0,"150":0,"153":0,"154":0,"163":0,"164":0,"166":0,"168":0,"169":0,"173":0,"174":0,"181":0,"182":0,"183":0,"193":0,"195":0,"196":0,"200":0,"201":0,"202":0,"204":0,"205":0,"209":0,"210":0,"211":0,"213":0,"214":0,"215":0,"217":0,"218":0,"219":0,"221":0,"222":0,"224":0,"226":0,"227":0,"232":0,"233":0,"234":0,"235":0,"236":0,"243":0,"244":0,"245":0,"246":0,"247":0,"253":0,"270":0,"275":0,"276":0,"284":0,"285":0,"287":0,"296":0,"298":0,"300":0,"301":0,"303":0,"304":0,"306":0,"308":0,"309":0,"313":0,"315":0,"316":0,"317":0,"318":0,"319":0,"330":0,"331":0,"332":0,"333":0,"334":0,"337":0,"339":0,"340":0,"341":0,"342":0,"346":0,"347":0,"348":0,"349":0,"350":0,"351":0,"352":0,"353":0,"355":0,"356":0,"358":0,"359":0,"360":0,"365":0,"366":0,"367":0,"368":0,"370":0,"371":0,"372":0,"374":0,"376":0,"377":0,"378":0,"380":0,"381":0,"382":0,"384":0,"387":0,"388":0,"389":0,"390":0,"396":0,"397":0,"398":0,"400":0,"401":0,"402":0,"404":0,"410":0,"411":0,"412":0,"413":0,"415":0,"424":0,"425":0,"427":0,"429":0,"431":0,"432":0,"433":0,"436":0,"438":0,"441":0,"443":0,"445":0,"447":0,"455":0,"463":0,"470":0,"471":0,"472":0,"473":0,"474":0,"475":0,"476":0,"477":0,"478":0,"479":0,"481":0,"484":0,"486":0,"488":0,"494":0,"495":0,"503":0,"504":0,"505":0,"506":0,"507":0,"508":0,"510":0,"511":0,"513":0,"514":0,"517":0,"519":0,"522":0,"529":0,"530":0,"531":0,"532":0,"534":0,"537":0,"539":0,"548":0,"558":0,"569":0,"570":0,"571":0,"573":0,"613":0,"622":0,"623":0,"625":0,"626":0,"628":0};
_yuitest_coverage["build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js"].functions = {"_keyHandler:30":0,"on:43":0,"detach:53":0,"delegate:58":0,"detachDelegate:68":0,"TREE:116":0,"initializer:131":0,"fire_branch_action:180":0,"(anonymous 2):233":0,"toggleExpansion:189":0,"setter:269":0,"BRANCH:284":0,"initializer:295":0,"draw:328":0,"wire:395":0,"getChildrenUL:409":0,"ajaxLoad:423":0,"ajaxProcessResponse:469":0,"addChild:501":0,"addViewAllCoursesChild:528":0,"setter:557":0,"getter:568":0,"init_add_tree:621":0,"(anonymous 1):1":0};
_yuitest_coverage["build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js"].coveredLines = 216;
_yuitest_coverage["build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js"].coveredFunctions = 24;
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 1);
YUI.add('moodle-block_navigation-navigation', function (Y, NAME) {

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
_yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "(anonymous 1)", 1);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 14);
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
        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "_keyHandler", 30);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 31);
var actObj;
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 32);
if (!args.actions) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 33);
actObj = {collapse:true, expand:true, toggle:true, enter:true};
        } else {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 35);
actObj = args.actions;
        }
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 37);
if (this._keys[e.keyCode] && actObj[this._keys[e.keyCode]]) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 38);
e.action = this._keys[e.keyCode];
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 39);
notifier.fire(e);
        }
    },

    on: function (node, sub, notifier) {
        // subscribe to _event and ask keyHandler to handle with given args[0] (the desired actions).
        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "on", 43);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 45);
if (sub.args === null) {
            //no actions given
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 47);
sub._detacher = node.on(this._event, this._keyHandler,this, notifier, {actions:false});
        } else {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 49);
sub._detacher = node.on(this._event, this._keyHandler,this, notifier, sub.args[0]);
        }
    },

    detach: function (node, sub) {
        //detach our _detacher handle of the subscription made in on()
        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "detach", 53);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 55);
sub._detacher.detach();
    },

    delegate: function (node, sub, notifier, filter) {
        // subscribe to _event and ask keyHandler to handle with given args[0] (the desired actions).
        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "delegate", 58);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 60);
if (sub.args === null) {
            //no actions given
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 62);
sub._delegateDetacher = node.delegate(this._event, this._keyHandler,filter, this, notifier, {actions:false});
        } else {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 64);
sub._delegateDetacher = node.delegate(this._event, this._keyHandler,filter, this, notifier, sub.args[0]);
        }
    },

    detachDelegate: function (node, sub) {
        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "detachDelegate", 68);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 69);
sub._delegateDetacher.detach();
    }
});

_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 73);
var EXPANSIONLIMIT_EVERYTHING = 0,
    //EXPANSIONLIMIT_COURSE     = 20,
    //EXPANSIONLIMIT_SECTION    = 30,
    EXPANSIONLIMIT_ACTIVITY   = 40;

/**
 * Mappings for the different types of nodes coming from the navigation.
 * Copied from lib/navigationlib.php navigation_node constants.
 * @type object
 */
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 83);
var NODETYPE = {
    /** @type int Root node = 0 */
    ROOTNODE : 0,
    /** @type int System context = 1 */
    SYSTEM : 1,
    /** @type int Course category = 10 */
    CATEGORY : 10,
    /** @type int MYCATEGORY = 11 */
    MYCATEGORY : 11,
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
};

/**
 * Navigation tree class.
 *
 * This class establishes the tree initially, creating expandable branches as
 * required, and delegating the expand/collapse event.
 */
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 116);
var TREE = function() {
    _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "TREE", 116);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 117);
TREE.superclass.constructor.apply(this, arguments);
};
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 119);
TREE.prototype = {
    /**
     * The tree's ID, normally its block instance id.
     */
    id : null,
    /**
     * An array of initialised branches.
     */
    branches : [],
    /**
     * Initialise the tree object when its first created.
     */
    initializer : function(config) {
        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "initializer", 131);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 132);
this.id = config.id;

        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 134);
var node = Y.one('#inst'+config.id);

        // Can't find the block instance within the page
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 137);
if (node === null) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 138);
return;
        }

        // Delegate event to toggle expansion
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 142);
Y.delegate('click', this.toggleExpansion, node.one('.block_tree'), '.tree_item.branch', this);
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 143);
Y.delegate('actionkey', this.toggleExpansion, node.one('.block_tree'), '.tree_item.branch', this);

        // Gather the expandable branches ready for initialisation.
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 146);
var expansions = [];
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 147);
if (config.expansions) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 148);
expansions = config.expansions;
        } else {_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 149);
if (window['navtreeexpansions'+config.id]) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 150);
expansions = window['navtreeexpansions'+config.id];
        }}
        // Establish each expandable branch as a tree branch.
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 153);
for (var i in expansions) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 154);
var branch = new BRANCH({
                tree:this,
                branchobj:expansions[i],
                overrides : {
                    expandable : true,
                    children : [],
                    haschildren : true
                }
            }).wire();
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 163);
M.block_navigation.expandablebranchcount++;
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 164);
this.branches[branch.get('id')] = branch;
        }
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 166);
if (M.block_navigation.expandablebranchcount > 0) {
            // Delegate some events to handle AJAX loading.
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 168);
Y.delegate('click', this.fire_branch_action, node.one('.block_tree'), '.tree_item.branch[data-expandable]', this);
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 169);
Y.delegate('actionkey', this.fire_branch_action, node.one('.block_tree'), '.tree_item.branch[data-expandable]', this);
        }

        // Call the generic blocks init method to add all the generic stuff
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 173);
if (this.get('candock')) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 174);
this.initialise_block(Y, node);
        }
    },
    /**
     * Fire actions for a branch when an event occurs.
     */
    fire_branch_action : function(event) {
        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "fire_branch_action", 180);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 181);
var id = event.currentTarget.getAttribute('id');
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 182);
var branch = this.branches[id];
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 183);
branch.ajaxLoad(event);
    },
    /**
     * This is a callback function responsible for expanding and collapsing the
     * branches of the tree. It is delegated to rather than multiple event handles.
     */
    toggleExpansion : function(e) {
        // First check if they managed to click on the li iteslf, then find the closest
        // LI ancestor and use that

        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "toggleExpansion", 189);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 193);
if (e.target.test('a') && (e.keyCode === 0 || e.keyCode === 13)) {
            // A link has been clicked (or keypress is 'enter') don't fire any more events just do the default.
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 195);
e.stopPropagation();
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 196);
return;
        }

        // Makes sure we can get to the LI containing the branch.
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 200);
var target = e.target;
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 201);
if (!target.test('li')) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 202);
target = target.ancestor('li');
        }
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 204);
if (!target) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 205);
return;
        }

        // Toggle expand/collapse providing its not a root level branch.
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 209);
if (!target.hasClass('depth_1')) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 210);
if (e.type === 'actionkey') {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 211);
switch (e.action) {
                    case 'expand' :
                        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 213);
target.removeClass('collapsed');
                        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 214);
target.set('aria-expanded', true);
                        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 215);
break;
                    case 'collapse' :
                        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 217);
target.addClass('collapsed');
                        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 218);
target.set('aria-expanded', false);
                        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 219);
break;
                    default :
                        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 221);
target.toggleClass('collapsed');
                        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 222);
target.set('aria-expanded', !target.hasClass('collapsed'));
                }
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 224);
e.halt();
            } else {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 226);
target.toggleClass('collapsed');
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 227);
target.set('aria-expanded', !target.hasClass('collapsed'));
            }
        }

        // If the accordian feature has been enabled collapse all siblings.
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 232);
if (this.get('accordian')) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 233);
target.siblings('li').each(function(){
                _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "(anonymous 2)", 233);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 234);
if (this.get('id') !== target.get('id') && !this.hasClass('collapsed')) {
                    _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 235);
this.addClass('collapsed');
                    _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 236);
this.set('aria-expanded', false);
                }
            });
        }

        // If this block can dock tell the dock to resize if required and check
        // the width on the dock panel in case it is presently in use.
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 243);
if (this.get('candock')) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 244);
M.core_dock.resize();
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 245);
var panel = M.core_dock.getPanel();
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 246);
if (panel.visible) {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 247);
panel.correctWidth();
            }
        }
    }
};
// The tree extends the YUI base foundation.
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 253);
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
                _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "setter", 269);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 270);
return parseInt(val, 10);
            }
        }
    }
});
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 275);
if (M.core_dock && M.core_dock.genericblock) {
    _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 276);
Y.augment(TREE, M.core_dock.genericblock);
}

/**
 * The tree branch class.
 * This class is used to manage a tree branch, in particular its ability to load
 * its contents by AJAX.
 */
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 284);
BRANCH = function() {
    _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "BRANCH", 284);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 285);
BRANCH.superclass.constructor.apply(this, arguments);
};
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 287);
BRANCH.prototype = {
    /**
     * The node for this branch (p)
     */
    node : null,
    /**
     * Initialises the branch when it is first created.
     */
    initializer : function(config) {
        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "initializer", 295);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 296);
var i,
            children;
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 298);
if (config.branchobj !== null) {
            // Construct from the provided xml
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 300);
for (i in config.branchobj) {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 301);
this.set(i, config.branchobj[i]);
            }
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 303);
children = this.get('children');
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 304);
this.set('haschildren', (children.length > 0));
        }
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 306);
if (config.overrides !== null) {
            // Construct from the provided xml
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 308);
for (i in config.overrides) {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 309);
this.set(i, config.overrides[i]);
            }
        }
        // Get the node for this branch
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 313);
this.node = Y.one('#', this.get('id'));
        // Now check whether the branch is not expandable because of the expansionlimit
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 315);
var expansionlimit = this.get('tree').get('expansionlimit');
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 316);
var type = this.get('type');
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 317);
if (expansionlimit !== EXPANSIONLIMIT_EVERYTHING &&  type >= expansionlimit && type <= EXPANSIONLIMIT_ACTIVITY) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 318);
this.set('expandable', false);
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 319);
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

        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "draw", 328);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 330);
var isbranch = (this.get('expandable') || this.get('haschildren'));
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 331);
var branchli = Y.Node.create('<li></li>');
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 332);
var link = this.get('link');
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 333);
var branchp = Y.Node.create('<p class="tree_item"></p>').setAttribute('id', this.get('id'));
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 334);
if (!link) {
            //add tab focus if not link (so still one focus per menu node).
            // it was suggested to have 2 foci. one for the node and one for the link in MDL-27428.
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 337);
branchp.setAttribute('tabindex', '0');
        }
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 339);
if (isbranch) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 340);
branchli.addClass('collapsed').addClass('contains_branch');
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 341);
branchli.set('aria-expanded', false);
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 342);
branchp.addClass('branch');
        }

        // Prepare the icon, should be an object representing a pix_icon
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 346);
var branchicon = false;
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 347);
var icon = this.get('icon');
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 348);
if (icon && (!isbranch || this.get('type') == NODETYPE.ACTIVITY)) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 349);
branchicon = Y.Node.create('<img alt="" />');
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 350);
branchicon.setAttribute('src', M.util.image_url(icon.pix, icon.component));
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 351);
branchli.addClass('item_with_icon');
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 352);
if (icon.alt) {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 353);
branchicon.setAttribute('alt', icon.alt);
            }
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 355);
if (icon.title) {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 356);
branchicon.setAttribute('title', icon.title);
            }
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 358);
if (icon.classes) {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 359);
for (var i in icon.classes) {
                    _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 360);
branchicon.addClass(icon.classes[i]);
                }
            }
        }

        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 365);
if (!link) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 366);
var branchspan = Y.Node.create('<span></span>');
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 367);
if (branchicon) {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 368);
branchspan.appendChild(branchicon);
            }
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 370);
branchspan.append(this.get('name'));
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 371);
if (this.get('hidden')) {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 372);
branchspan.addClass('dimmed_text');
            }
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 374);
branchp.appendChild(branchspan);
        } else {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 376);
var branchlink = Y.Node.create('<a title="'+this.get('title')+'" href="'+link+'"></a>');
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 377);
if (branchicon) {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 378);
branchlink.appendChild(branchicon);
            }
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 380);
branchlink.append(this.get('name'));
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 381);
if (this.get('hidden')) {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 382);
branchlink.addClass('dimmed');
            }
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 384);
branchp.appendChild(branchlink);
        }

        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 387);
branchli.appendChild(branchp);
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 388);
element.appendChild(branchli);
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 389);
this.node = branchp;
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 390);
return this;
    },
    /**
     * Attaches required events to the branch structure.
     */
    wire : function() {
        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "wire", 395);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 396);
this.node = this.node || Y.one('#'+this.get('id'));
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 397);
if (!this.node) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 398);
return false;
        }
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 400);
if (this.get('expandable')) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 401);
this.node.setAttribute('data-expandable', '1');
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 402);
this.node.setAttribute('data-loaded', '0');
        }
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 404);
return this;
    },
    /**
     * Gets the UL element that children for this branch should be inserted into.
     */
    getChildrenUL : function() {
        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "getChildrenUL", 409);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 410);
var ul = this.node.next('ul');
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 411);
if (!ul) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 412);
ul = Y.Node.create('<ul></ul>');
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 413);
this.node.ancestor().append(ul);
        }
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 415);
return ul;
    },
    /**
     * Load the content of the branch via AJAX.
     *
     * This function calls ajaxProcessResponse with the result of the AJAX
     * request made here.
     */
    ajaxLoad : function(e) {
        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "ajaxLoad", 423);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 424);
if (e.type === 'actionkey' && e.action !== 'enter') {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 425);
e.halt();
        } else {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 427);
e.stopPropagation();
        }
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 429);
if (e.type === 'actionkey' && e.action === 'enter' && e.target.test('A')) {
            // No ajaxLoad for enter.
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 431);
this.node.setAttribute('data-expandable', '0');
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 432);
this.node.setAttribute('data-loaded', '1');
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 433);
return true;
        }

        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 436);
if (this.node.hasClass('loadingbranch')) {
            // Already loading. Just skip.
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 438);
return true;
        }

        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 441);
if (this.node.getAttribute('data-loaded') === '1') {
            // We've already loaded this stuff.
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 443);
return true;
        }
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 445);
this.node.addClass('loadingbranch');

        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 447);
var params = {
            elementid : this.get('id'),
            id : this.get('key'),
            type : this.get('type'),
            sesskey : M.cfg.sesskey,
            instance : this.get('tree').get('instance')
        };

        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 455);
Y.io(M.cfg.wwwroot+'/lib/ajax/getnavbranch.php', {
            method:'POST',
            data:  build_querystring(params),
            on: {
                complete: this.ajaxProcessResponse
            },
            context:this
        });
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 463);
return true;
    },
    /**
     * Processes an AJAX request to load the content of this branch through
     * AJAX.
     */
    ajaxProcessResponse : function(tid, outcome) {
        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "ajaxProcessResponse", 469);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 470);
this.node.removeClass('loadingbranch');
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 471);
this.node.setAttribute('data-loaded', '1');
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 472);
try {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 473);
var object = Y.JSON.parse(outcome.responseText);
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 474);
if (object.children && object.children.length > 0) {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 475);
var coursecount = 0;
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 476);
for (var i in object.children) {
                    _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 477);
if (typeof(object.children[i])==='object') {
                        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 478);
if (object.children[i].type == NODETYPE.COURSE) {
                            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 479);
coursecount++;
                        }
                        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 481);
this.addChild(object.children[i]);
                    }
                }
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 484);
if ((this.get('type') == NODETYPE.CATEGORY || this.get('type') == NODETYPE.ROOTNODE || this.get('type') == NODETYPE.MYCATEGORY)
                    && coursecount >= M.block_navigation.courselimit) {
                    _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 486);
this.addViewAllCoursesChild(this);
                }
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 488);
return true;
            }
        } catch (ex) {
            // If we got here then there was an error parsing the result
        }
        // The branch is empty so class it accordingly
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 494);
this.node.replaceClass('branch', 'emptybranch');
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 495);
return true;
    },
    /**
     * Turns the branch object passed to the method into a proper branch object
     * and then adds it as a child of this branch.
     */
    addChild : function(branchobj) {
        // Make the new branch into an object
        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "addChild", 501);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 503);
var branch = new BRANCH({tree:this.get('tree'), branchobj:branchobj});
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 504);
if (branch.draw(this.getChildrenUL())) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 505);
this.get('tree').branches[branch.get('id')] = branch;
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 506);
branch.wire();
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 507);
var count = 0, i, children = branch.get('children');
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 508);
for (i in children) {
                // Add each branch to the tree
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 510);
if (children[i].type == NODETYPE.COURSE) {
                    _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 511);
count++;
                }
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 513);
if (typeof(children[i]) === 'object') {
                    _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 514);
branch.addChild(children[i]);
                }
            }
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 517);
if ((branch.get('type') == NODETYPE.CATEGORY || branch.get('type') == NODETYPE.MYCATEGORY)
                && count >= M.block_navigation.courselimit) {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 519);
this.addViewAllCoursesChild(branch);
            }
        }
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 522);
return true;
    },

    /**
     * Add a link to view all courses in a category
     */
    addViewAllCoursesChild: function(branch) {
        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "addViewAllCoursesChild", 528);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 529);
var url = null;
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 530);
if (branch.get('type') == NODETYPE.ROOTNODE) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 531);
if (branch.get('key') === 'mycourses') {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 532);
url = M.cfg.wwwroot + '/my';
            } else {
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 534);
url = M.cfg.wwwroot + '/course/index.php';
            }
        } else {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 537);
url = M.cfg.wwwroot+'/course/index.php?categoryid=' + branch.get('key');
        }
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 539);
branch.addChild({
            name : M.str.moodle.viewallcourses,
            title : M.str.moodle.viewallcourses,
            link : url,
            haschildren : false,
            icon : {'pix':"i/navigationitem",'component':'moodle'}
        });
    }
};
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 548);
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
                _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "setter", 557);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 558);
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
                _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "getter", 568);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 569);
if (val === '') {
                    _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 570);
val = 'expandable_branch_'+M.block_navigation.expandablebranchcount;
                    _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 571);
M.block_navigation.expandablebranchcount++;
                }
                _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 573);
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
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 613);
M.block_navigation = M.block_navigation || {
    /** The number of expandable branches in existence */
    expandablebranchcount:1,
    courselimit : 20,
    instance : null,
    /**
     * Add new instance of navigation tree to tree collection
     */
    init_add_tree:function(properties) {
        _yuitest_coverfunc("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", "init_add_tree", 621);
_yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 622);
if (properties.courselimit) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 623);
this.courselimit = properties.courselimit;
        }
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 625);
if (M.core_dock) {
            _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 626);
M.core_dock.init(Y);
        }
        _yuitest_coverline("build/moodle-block_navigation-navigation/moodle-block_navigation-navigation.js", 628);
new TREE(properties);
    }
};


}, '@VERSION@', {
    "requires": [
        "base",
        "core_dock",
        "io-base",
        "node",
        "dom",
        "event-custom",
        "event-delegate",
        "json-parse"
    ]
});
