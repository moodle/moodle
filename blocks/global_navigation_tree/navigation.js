// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains classes used to manage the navigation structures in Moodle
 * and was introduced as part of the changes occuring in Moodle 2.0
 *
 * @since 2.0
 * @package javascript
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This namespace will contain all of the contents of the navigation blocks
 * global navigation and settings.
 * @namespace
 */
M.block_navigation = M.block_navigation || {
    /** The number of expandable branches in existence */
    expandablebranchcount:0,
    /** An array of initialised trees */
    treecollection:[],
    /**
     * Will contain all of the classes for the navigation blocks
     * @namespace
     */
    classes:{},
    /**
     * This function gets called when the module is first loaded as required by
     * the YUI.add statement at the bottom of the page.
     * 
     * NOTE: This will only be executed ONCE
     * @function
     */
    init:function(Y) {
        if (M.core_dock.genericblock) {
            // Give the tree class the dock block properties
            Y.augment(M.block_navigation.classes.tree, M.core_dock.genericblock);
        }
    },
    /**
     * Add new instance of navigation tree to tree collection
     */
    init_add_tree:function(Y, id, properties) {
    	M.block_navigation.treecollection[id] = new M.block_navigation.classes.tree(Y, id, properties);
    }
};

/**
 * @class tree
 * @constructor
 * @base M.core_dock.abstractblock
 * @param {YUI} Y A yui instance to use with the navigation
 * @param {string} id The name of the tree
 * @param {int} key The internal id within the tree store
 * @param {object} properties Object containing tree properties
 */
M.block_navigation.classes.tree = function(Y, id, properties) {
    this.Y = Y;
    this.id = id;
    this.key = id;
    this.errorlog = [];
    this.ajaxbranches = 0;
    this.expansions = [];
    this.instance = id;
    this.cachedcontentnode = null;
    this.cachedfooter = null;
    this.position = 'block';
    this.skipsetposition = false;
    this.candock = false;
    
    if (properties.expansions) {
        this.expansions = properties.expansions;
    }
    if (properties.instance) {
        this.instance = properties.instance;
    }
    if (properties.candock) {
        this.candock = true;
    }

    var node = this.Y.one('#inst'+this.id);
    
    // Can't find the block instance within the page
    if (node === null) {
        return;
    }
    // Attach event to toggle expansion
    node.all('.tree_item.branch').on('click', this.toggleexpansion , this);

    // Attache events to expand by AJAX
    for (var i in this.expansions) {
        this.Y.one('#'+this.expansions[i].id).on('ajaxload|click', this.init_load_ajax, this, this.expansions[i]);
        M.block_navigation.expandablebranchcount++;
    }

    if (node.hasClass('block_js_expansion')) {
        node.on('mouseover', function(e){this.toggleClass('mouseover');}, node);
        node.on('mouseout', function(e){this.toggleClass('mouseover');}, node);
    }

    // Call the generic blocks init method to add all the generic stuff
    if (this.candock) {
        this.init(Y, node);
    }
}

/**
 * Loads a branch via AJAX
 * @param {event} e The event object
 * @param {object} branch A branch to load via ajax
 */
M.block_navigation.classes.tree.prototype.init_load_ajax = function(e, branch) {
    e.stopPropagation();
    if (e.target.get('nodeName').toUpperCase() != 'P') {
        return true;
    }
    var cfginstance = '';
    if (this.instance != null) {
        cfginstance = '&instance='+this.instance
    }
    this.Y.io(M.cfg.wwwroot+'/lib/ajax/getnavbranch.php', {
        method:'POST',
        data:'elementid='+branch.id+'&id='+branch.branchid+'&type='+branch.type+'&sesskey='+M.cfg.sesskey+cfginstance,
        on: {
            complete:this.load_ajax,
            success:function() {this.Y.detach('click', this.init_load_ajax, e.target);}
        },
        context:this,
        arguments:{
            target:e.target
        }
    });
    return true;
}

/**
 * Takes an branch provided through ajax and loads it into the tree
 * @param {int} tid The transaction id
 * @param {object} outcome
 * @param {mixed} args
 * @return bool
 */
M.block_navigation.classes.tree.prototype.load_ajax = function(tid, outcome, args) {
    try {
        var object = this.Y.JSON.parse(outcome.responseText);
        if (this.add_branch(object, args.target.ancestor('LI') ,1)) {
            if (this.candock) {
                M.core_dock.resize();
            }
            return true;
        }
    } catch (e) {
        // If we got here then there was an error parsing the result
    }
    // The branch is empty so class it accordingly
    args.target.replaceClass('branch', 'emptybranch');
    return true;
}

/**
 * Adds a branch into the tree provided with some XML
 * @param {xmldoc} branchxml
 * @param {Y.Node} target
 * @param {int} depth
 * @return bool
 */
M.block_navigation.classes.tree.prototype.add_branch = function(branchobj, target, depth) {

    // Make the new branch into an object
    var branch = new M.block_navigation.classes.branch(this, branchobj);

    var childrenul = false;
    if (depth === 1) {
        if (!branch.children) {
            return false;
        }
        childrenul = this.Y.Node.create('<ul></ul>');
        target.appendChild(childrenul);
    } else {
        childrenul = branch.inject_into_dom(target);
    }
    if (childrenul) {
        for (i in branch.children) {
            // Add each branch to the tree
            this.add_branch(branch.children[i], childrenul, depth+1);
        }
    }
    return true;
}
/**
 * Toggle a branch as expanded or collapsed
 * @param {Event} e
 */
M.block_navigation.classes.tree.prototype.toggleexpansion = function(e) {
    // First check if they managed to click on the li iteslf, then find the closest
    // LI ancestor and use that
    if (e.target.get('nodeName').toUpperCase() == 'LI') {
        e.target.toggleClass('collapsed');
    } else if (e.target.ancestor('LI')) {
        e.target.ancestor('LI').toggleClass('collapsed');
    }
    if (this.candock) {
        M.core_dock.resize();
    }
}

/**
 * This class represents a branch for a tree
 * @class branch
 * @constructor
 * @param {M.block_navigation.classes.tree} tree
 * @param {object|null} obj
 */
M.block_navigation.classes.branch = function(tree, obj) {
    this.tree = tree;
    this.name = null;
    this.title = null;
    this.classname = null;
    this.id = null;
    this.key = null;
    this.type = null;
    this.link = null;
    this.icon = null;
    this.expandable = null;
    this.expansionceiling = null;
    this.hidden = false;
    this.haschildren = false;
    this.children = false;
    if (obj !== null) {
        // Construct from the provided xml
        this.construct_from_json(obj);
    }
}
M.block_navigation.classes.branch.prototype.construct_from_json = function(obj) {
    for (var i in obj) {
        this[i] = obj[i];
    }
    if (this.children) {
        this.haschildren = true;
    }
    if (this.id && this.id.match(/^expandable_branch_\d+$/)) {
        // Assign a new unique id for this new expandable branch
        M.block_navigation.expandablebranchcount++;
        this.id = 'expandable_branch_'+M.block_navigation.expandablebranchcount;
    }
}
/**
 * Injects a branch into the tree at the given location
 * @param {element} element
 */
M.block_navigation.classes.branch.prototype.inject_into_dom = function(element) {

    var branchli = this.tree.Y.Node.create('<li></li>');
    var branchp = this.tree.Y.Node.create('<p class="tree_item"></p>');

    if ((this.expandable !== null || this.haschildren) && this.expansionceiling===null) {
        branchli.addClass('collapsed');
        branchp.addClass('branch');
        branchp.on('click', this.tree.toggleexpansion, this.tree);
        if (this.expandable) {
            branchp.on('ajaxload|click', this.tree.init_load_ajax, this.tree, {branchid:this.key,id:this.id,type:this.type});
        }
    }

    if (this.myclass !== null) {
        branchp.addClass(this.myclass);
    }
    if (this.id !== null) {
        branchp.setAttribute('id', this.id);
    }

    var branchicon = false;
    if (this.icon != null) {
        branchicon = this.tree.Y.Node.create('<img src="'+this.icon+'" alt="" />');
        this.name = ' '+this.name;
    }
    if (this.link === null) {
        if (branchicon) {
            branchp.appendChild(branchicon);
        }
        branchp.append(this.name.replace(/\n/g, '<br />'));
    } else {
        var branchlink = this.tree.Y.Node.create('<a title="'+this.title+'" href="'+this.link+'">'+this.name.replace(/\n/g, '<br />')+'</a>');
        if (branchicon) {
            branchlink.appendChild(branchicon);
        }
        if (this.hidden) {
            branchlink.addClass('dimmed');
        }
        branchp.appendChild(branchlink);
    }

    branchli.appendChild(branchp);
    if (this.haschildren) {
        var childrenul = this.tree.Y.Node.create('<ul></ul>');
        branchli.appendChild(childrenul);
        element.appendChild(branchli);
        return childrenul
    } else {
        element.appendChild(branchli);
        return false;
    }
}

/**
 * Causes the navigation block module to initalise the first time the module
 * is used!
 *
 * NOTE: Never convert the second argument to a function reference...
 * doing so causes scoping issues
 */
YUI.add('block_navigation', function(Y){M.block_navigation.init(Y);}, '0.0.0.1', M.yui.loader.modules.block_navigation.requires);