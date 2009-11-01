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
 * Some very important general namespaces to act as containers for the general
 * objects required to manage the navigation.
 *
 * For anyone looking to improve this javascript taking a little time to turn
 * the classes into namespaced classes, and giving the class structure in this file
 * a similar structure to YUI on a moodle namespace would be AWESOME
 */
YAHOO.namespace('moodle.navigation');
YAHOO.namespace('moodle.navigation.sideblockwidth');
YAHOO.namespace('moodle.navigation.tabpanel');
YAHOO.namespace('moodle.navigation.treecollection');

/**
 * Instatiate some very important variables that allow us to manage the navigaiton
 * objects without having to hit my arch enemy `undefined`
 */
YAHOO.moodle.navigation.sideblockwidth = null;
YAHOO.moodle.navigation.tabpanel = null;
YAHOO.moodle.navigation.treecollection = Array();
YAHOO.moodle.navigation.expandablebranchcount = 0;

/**
 * Navigation Tree object (function) used to control a global navigation tree
 * handling things such as collapse, expand, and AJAX requests for more branches
 *
 * You should never call this directly.. you should use {@link start_new_navtree()}
 * which will create the class and make it accessible in a smart way
 *
 * @class navigation_tree
 * @constructor
 * @param {string} treename
 * @param {string} key
 */
function navigation_tree (treename, key) {
    this.name = treename;
    this.key = key;
    this.errorlog = '';
    this.ajaxbranches = 0;
    this.expansions = Array();
    this.instance = null
    this.cachedcontent = null;
    this.cachedfooter = null;
    this.position = 'block';
    this.skipsetposition = false;
    this.togglesidetabdisplay = '[[togglesidetabdisplay]]';
    this.toggleblockdisplay = '[[toggleblockdisplay]]';
    this.sideblockwidth = null;
    if (window[this.name]) {
        if (window[this.name].expansions) {
            this.expansions = window[this.name].expansions;
        }
        if (window[this.name].instance) {
            this.instance = window[this.name].instance;
        }
        if (window[this.name].togglesidetabdisplay) {
            this.togglesidetabdisplay = window[this.name].togglesidetabdisplay;
        }
        if (window[this.name].toggleblockdisplay) {
            this.toggleblockdisplay = window[this.name].toggleblockdisplay;
        }
    }
}
/**
 * Initialise function used to attach the initial events to the navigation tree
 * This function attachs toggles and ajax calls
 */
navigation_tree.prototype.initialise = function() {
    if (!document.getElementById(this.name)) {
        return;
    }
    var e = document.getElementById(this.name);
    var i = 0;
    while (!YAHOO.util.Dom.hasClass(e, 'sideblock') && e.nodeName.toUpperCase()!='BODY') {
        e = e.parentNode;
    }
    var movetos = YAHOO.util.Dom.getElementsByClassName('moveto', 'a', e);
    if (movetos !== null && movetos.length > 0) {
        for (i = 0;i<movetos.length;i++) {
            YAHOO.util.Event.addListener(movetos[i], 'click', this.toggle_block_display, this, true);
        }
    }
    for (i = 0; i<this.expansions.length; i++) {
        try {
            this.expansions[i].element = document.getElementById(this.expansions[i].id);
            YAHOO.util.Event.addListener(this.expansions[i].id, 'click', this.init_load_ajax, this.expansions[i], this);
            YAHOO.moodle.navigation.expandablebranchcount++;
        } catch (err) {
            this.errorlog += "attaching ajax load events: \t"+err+"\n";
        }
    }
    var items = YAHOO.util.Dom.getElementsByClassName('tree_item branch', '', document.getElementById(this.name));
    if (items != null && items.length>0) {
        for (i = 0; i<items.length; i++) {
            try {
                YAHOO.util.Event.addListener(items[i], 'click', this.toggleexpansion, this, true);
            } catch (err) {
                this.errorlog += "attaching toggleexpansion events: \t"+err+"\n";
            }
        }
    }

    var customcommands = YAHOO.util.Dom.getElementsByClassName('customcommand', 'a', e);
    var commands = YAHOO.util.Dom.getElementsByClassName('commands', 'div', e);
    if (commands.length === 1 && customcommands.length > 0) {
        for (i = 0; i < customcommands.length; i++) {
            customcommands[i].parentNode.removeChild(customcommands[i]);
            commands[0].appendChild(customcommands[i]);
        }
    }

    if (YAHOO.util.Dom.hasClass(e, 'sideblock_js_sidebarpopout')) {
        YAHOO.util.Dom.removeClass(e, 'sideblock_js_sidebarpopout');
        this.skipsetposition = true;
        this.toggle_block_display(e, this);
    } else if (YAHOO.util.Dom.hasClass(e, 'sideblock_js_expansion')) {
        YAHOO.util.Event.addListener(e, 'mouseover', this.togglesize, e, this);
        YAHOO.util.Event.addListener(e, 'mouseout', this.togglesize, e, this);
    }
}
/**
 * Toogle a branch either collapsed or expanded... CSS styled
 * @param {object} e Event object
 */
navigation_tree.prototype.toggleexpansion = function(e) {
    YAHOO.util.Event.stopPropagation(e);
    var target = YAHOO.util.Event.getTarget(e);
    var parent = target.parentNode;
    while (parent.nodeName.toUpperCase()!='LI') {
        parent = parent.parentNode;
    }
    if (YAHOO.util.Dom.hasClass(parent, 'collapsed')) {
        YAHOO.util.Dom.removeClass(parent, 'collapsed');
    } else {
        YAHOO.util.Dom.addClass(parent, 'collapsed');
    }
    if (this.position === 'sidebar') {
        YAHOO.moodle.navigation.tabpanel.resize_tab();
    }
}
/**
 * Toggles the size on an element by adding/removing the mouseover class
 * @param {object} e Event object
 * @param {element} element The element to add/remove the class from
 */
navigation_tree.prototype.togglesize = function(e, element) {
    if (e.type == 'mouseout') {
        var mp = YAHOO.util.Event.getXY(e);
        if (mp[0] == -1) {
            return true;
        }
        var ep = YAHOO.util.Dom.getXY(element);
        ep[2] = ep[0]+element.offsetWidth;
        ep[3] = ep[1]+element.offsetHeight;
        var withinrealm = (mp[0] > ep[0] && mp[0] < ep[2] && mp[1] > ep[1] && mp[1] < ep[3]);
        if (!withinrealm) {
            YAHOO.util.Event.stopEvent(e);
            YAHOO.util.Dom.removeClass(element, 'mouseover');
        }
    } else {
        YAHOO.util.Event.stopEvent(e);
        element.style.width = element.offsetWidth +'px';
        YAHOO.util.Dom.addClass(element, 'mouseover');
    }
    return true;
}
/**
 * This function makes the initial call to load a branch of the navigation
 * tree by AJAX
 * @param {object} e Event object
 * @param {object} branch The branch object from navigation_tree::expansions
 * @return {bool}
 */
navigation_tree.prototype.init_load_ajax = function(e, branch) {
    YAHOO.util.Event.stopPropagation(e);
    if (YAHOO.util.Event.getTarget(e).nodeName.toUpperCase() != 'P') {
        return true;
    }
    var postargs = 'elementid='+branch.id+'&id='+branch.branchid+'&type='+branch.type+'&sesskey='+moodle_cfg.sesskey;
    if (this.instance != null) {
        postargs += '&instance='+this.instance;
    }
    YAHOO.util.Connect.asyncRequest('POST', moodle_cfg.wwwroot+'/lib/ajax/getnavbranch.php', callback={
        success:function(o) {this.load_ajax(o);},
        failure:function(o) {this.load_ajax(o);},
        argument: {gntinstance:this,branch:branch,event:e, target:YAHOO.util.Event.getTarget(e)},
        scope: this
    }, postargs);
    return true;
}
/**
 * This function loads a branch returned by AJAX into the XHTML tree structure
 * @param {object} outcome The AJAX response
 * @return {bool}
 */
navigation_tree.prototype.load_ajax = function(outcome) {
    // Check the status
    if (outcome.status!=0 && outcome.responseXML!=null) {
        var branch = outcome.responseXML.documentElement;
        if (branch!=null && this.add_branch(branch,outcome.argument.target ,1)) {
            // If we get here everything worked perfectly
            YAHOO.util.Event.removeListener(outcome.argument.branch.element, 'click', navigation_tree.prototype.init_load_ajax);
            if (this.position === 'sidebar') {
                YAHOO.moodle.navigation.tabpanel.resize_tab();
            }
            return true;
        }
    }
    // Something went wrong or there simply wasn't anything more to display
    // add the emptybranch css class so we can flag it
    YAHOO.util.Dom.replaceClass(outcome.argument.target, 'branch', 'emptybranch');
    return false;
}
/**
 * This recursive function takes an XML branch and includes it in the tree
 * @param {xmlnode} branchxml The XML node for the branch
 * @param {element} target The target node to add to
 * @param {int} depth The depth we have delved (recusive counter)
 * @return {bool}
 */
navigation_tree.prototype.add_branch = function(branchxml, target, depth) {
    var branch = new navigation_tree_branch(this.name);
    branch.load_from_xml_node(branchxml);
    if (depth>1) {
        target = branch.inject_into_dom(target,this);
    }
    var dropcount = 5;
    while (target.nodeName.toUpperCase() !== 'LI') {
        target = target.parentNode;
        if (dropcount==0 && moodle_cfg.developerdebug) {
            return alert("dropped because of exceeding dropcount");
        }
        dropcount--;
    }
    if (branch.haschildren && branch.mychildren && branch.mychildren.childNodes) {
        for (var i=0;i<branch.mychildren.childNodes.length;i++) {
            if (branch.haschildren) {
                var ul = document.createElement('ul');
                target.appendChild(ul);
            }
            var child = branch.mychildren.childNodes[i];
            this.add_branch(child, ul, depth+1);
        }
    } else if(depth==1) {
        // If we are here then we got a valid response however there are no children
        // to display for the branch that we are expanding, thus we will return false
        // so we can add the emptybranch class
        return false;
    }
    return true;
}
/**
 * This switches a navigation block between its block position and the sidebar
 *
 * @param {element} e Event object
 */
navigation_tree.prototype.toggle_block_display = function(e) {
    if (e !== null) {
        YAHOO.util.Event.stopPropagation(e);
    }
    if (this.position === 'block') {
        this.move_to_sidebar_popout(e);
        this.position = 'sidebar';
    } else {
        this.move_to_block_position(e);
        this.position = 'block';
    }
}
/**
 * This function gets called from {@link navigation_tree.toggle_block_display()}
 * and is responsible for moving the block from the block position to the sidebar
 * @return {bool}
 */
navigation_tree.prototype.move_to_sidebar_popout = function(e) {

    YAHOO.util.Event.stopEvent(e);

    var element = document.getElementById(this.name).parentNode;
    if (element == null) {
        return false;
    }
    var tabcontent = document.getElementById(this.name).parentNode;
    while (!YAHOO.util.Dom.hasClass(element, 'sideblock')) {
        element = element.parentNode;
    }
    this.cachedcontent = element;

    var sideblocknode = element;
    while (sideblocknode && !YAHOO.util.Dom.hasClass(sideblocknode, 'block-region')) {
        sideblocknode = sideblocknode.parentNode;
    }

    var moveto = YAHOO.util.Dom.getElementsByClassName('moveto customcommand', 'a', this.cachedcontent);
    if (moveto.length > 0) {
        for (var i=0;i<moveto.length;i++) {
            var moveicon = moveto[i].getElementsByTagName('img');
            if (moveicon.length>0) {
                for (var j=0;j<moveicon.length;j++) {
                    moveicon[j].src = moveicon[j].src.replace(/movetosidetab/, 'movetoblock');
                    moveicon[j].setAttribute('alt', this.toggleblockdisplay);
                    moveicon[j].setAttribute('title', this.toggleblockdisplay);
                }
            }
        }
    }

    var placeholder = document.createElement('div');
    placeholder.setAttribute('id', this.name+'_content_placeholder');
    element.parentNode.replaceChild(placeholder, element);
    element = null;
    var tabtitle = this.cachedcontent.getElementsByTagName('h2')[0].cloneNode(true);
    tabtitle.innerHTML = tabtitle.innerHTML.replace(/([a-zA-Z0-9])/g, "$1<br />");
    var commands = YAHOO.util.Dom.getElementsByClassName('commands', 'div', this.cachedcontent);
    var tabcommands = null;
    if (commands.length > 0) {
        tabcommands = commands[0];
    } else {
        tabcommands = document.createElement('div');
        YAHOO.util.Dom.addClass(tabcommands, 'commands');
    }

    if (YAHOO.util.Dom.hasClass(sideblocknode, 'block-region')) {
        var blocks = YAHOO.util.Dom.getElementsByClassName('sideblock', 'div', sideblocknode);
        if (blocks.length === 0) {
            YAHOO.moodle.navigation.sideblockwidth = YAHOO.util.Dom.getStyle(sideblocknode, 'width');
            YAHOO.util.Dom.setStyle(sideblocknode, 'width', '0px');
        }
    }

    if (YAHOO.moodle.navigation.tabpanel === null) {
        YAHOO.moodle.navigation.tabpanel = new navigation_tab_panel();
    }
    YAHOO.moodle.navigation.tabpanel.add_to_tab_panel(this.name, tabtitle, tabcontent, tabcommands);
    if (!this.skipsetposition) {
        set_user_preference('nav_in_tab_panel_'+this.name, 1);
    } else {
        this.skipsetposition = false;
    }
    return true;
}
/**
 * This function gets called from {@link navigation_tree.toggle_block_display()}
 * and is responsible for moving the block from the sidebar to the block position
 * @return {bool}
 */
navigation_tree.prototype.move_to_block_position = function(e) {

    YAHOO.util.Event.stopEvent(e);

    if (this.sideblockwidth !== null) {
        YAHOO.util.Dom.setStyle(sideblocknode, 'width', this.sideblockwidth);
        this.sideblockwidth = null;
    }

    var placeholder = document.getElementById(this.name+'_content_placeholder');
    if (!placeholder || YAHOO.moodle.navigation.tabpanel == null) {
        return false;
    }

    if (YAHOO.moodle.navigation.tabpanel.showntab !== null) {
        YAHOO.moodle.navigation.tabpanel.hide_tab(e, YAHOO.moodle.navigation.tabpanel.showntab.tabname);
    }

    var tabcontent = YAHOO.moodle.navigation.tabpanel.get_tab_panel_contents(this.name);
    this.cachedcontent.appendChild(tabcontent);
    placeholder.parentNode.replaceChild(this.cachedcontent, placeholder);

    if (YAHOO.moodle.navigation.sideblockwidth !== null) {
        var sideblocknode = this.cachedcontent;
        while (sideblocknode && !YAHOO.util.Dom.hasClass(sideblocknode, 'block-region')) {
            sideblocknode = sideblocknode.parentNode;
        }
        if (YAHOO.util.Dom.hasClass(sideblocknode, 'block-region')) {
            YAHOO.util.Dom.setStyle(sideblocknode, 'width', YAHOO.moodle.navigation.sideblockwidth);
        }
    }

    var moveto = YAHOO.util.Dom.getElementsByClassName('moveto customcommand', 'a', this.cachedcontent);
    if (moveto.length > 0) {
        for (var i=0;i<moveto.length;i++) {
            var moveicon = moveto[i].getElementsByTagName('img');
            if (moveicon.length>0) {
                for (var j=0;j<moveicon.length;j++) {
                    moveicon[j].src = moveicon[j].src.replace(/movetoblock/, 'movetosidetab');
                    moveicon[j].setAttribute('alt', this.togglesidetabdisplay);
                    moveicon[j].setAttribute('title', this.togglesidetabdisplay);
                }
            }
        }
    }

    var commands = YAHOO.util.Dom.getElementsByClassName('commands', 'div', this.cachedcontent);
    var blocktitle = YAHOO.util.Dom.getElementsByClassName('title', 'div', this.cachedcontent);
    if (commands.length === 1 && blocktitle.length === 1) {
        commands[0].parentNode.removeChild(commands[0]);
        blocktitle[0].appendChild(commands[0]);
    }

    YAHOO.moodle.navigation.tabpanel.remove_from_tab_panel(this.name);

    var block = this.cachedcontent;
    while (!YAHOO.util.Dom.hasClass(block, 'sideblock')) {
        block = block.parentNode;
    }
    set_user_preference('nav_in_tab_panel_'+this.name, 0);
    return true;
}

/**
 * This class is used to manage the navigation tab panel
 *
 * Through this class you can add, remove, and manage items from the navigation
 * tab panel.
 * Note you only EVER need one of these
 * @constructor
 * @class navigation_tab_panel
 */
function navigation_tab_panel() {
    this.tabpanelexists = false;
    this.tabpanelelementnames = Array();
    this.tabpanelelementcontents = Array();
    this.navigationpanel = null;
    this.tabpanel = null;
    this.tabpanels = Array();
    this.tabcount = 0;
    this.preventhide = false;
    this.showntab = null;
}
/**
 * This creates a tab panel element and injects it into the DOM
 * @method create_tab_panel
 * @return {bool}
 */
navigation_tab_panel.prototype.create_tab_panel = function () {
    var navbar  = document.createElement('div');
    navbar.style.display = 'none';
    navbar.setAttribute('id', 'sidebarpopup');
    var navbarspacer = document.createElement('div');
    navbarspacer.style.height = '10px';
    navbar.appendChild(navbarspacer);
    YAHOO.util.Dom.addClass(navbar, 'navigation_bar');
    if (YAHOO.env.ua.ie > 0 && YAHOO.env.ua.ie < 7) {
        YAHOO.util.Dom.setStyle(navbar, 'height', YAHOO.util.Dom.getViewportHeight()+'px');
    }

    var navbarcontrol = document.createElement('div');
    YAHOO.util.Dom.addClass(navbarcontrol, 'controls');
    var removeall = document.createElement('img');
    removeall.setAttribute('src', moodle_cfg.wwwroot+'/pix/t/movetoblock.png');
    removeall.setAttribute('title', mstr.moodle.moveallsidetabstoblock);
    removeall.setAttribute('alt', mstr.moodle.moveallsidetabstoblock);
    navbarcontrol.appendChild(removeall);
    navbar.appendChild(navbarcontrol);

    document.getElementsByTagName('body')[0].appendChild(navbar);
    navbar.appendChild(create_shadow(false, true, true, false));
    YAHOO.util.Dom.addClass(document.getElementsByTagName('body')[0], 'has_navigation_bar');
    this.navigationpanel = navbar;
    this.tabpanelexists = true;
    navbar.style.display = 'block';

    YAHOO.util.Event.addListener(removeall, 'click', move_all_sidetabs_to_block_position);

    return true;
}
/**
 * This removes the tab panel element from the page
 * @method remove_tab_panel
 * @return {bool}
 */
navigation_tab_panel.prototype.remove_tab_panel = function () {
    var panel = document.getElementById('sidebarpopup');
    if (!panel) {
        return false;
    }
    this.tabpanel = null;
    panel.parentNode.removeChild(panel);
    this.tabpanelexists = false;
    this.navigationpanel = null;
    if (YAHOO.util.Dom.hasClass(document.getElementsByTagName('body')[0], 'has_navigation_bar')) {
        YAHOO.util.Dom.removeClass(document.getElementsByTagName('body')[0], 'has_navigation_bar')
    }
    return true;
}
/**
 * This function retrieves the content of a tab in the navigation tab panel
 * @method get_tab_panel_contents
 * @param {string} tabname The name of the tab
 * @return {element} The content element
 */
navigation_tab_panel.prototype.get_tab_panel_contents = function(tabname) {
    remove_shadow(this.tabpanelelementcontents[tabname]);
    return this.tabpanelelementcontents[tabname];
}
/**
 * This function adds a tab to the navigation tab panel
 *
 * If you find that it takes a long time to make the initial transaction then I
 * would first check the time that set_user_preference is taking, during development
 * the code needed to be re-jigged because it was taking a very long time to execute
 *
 * @method add_to_tab_panel
 * @param {string} tabname The string name of the tab
 * @param {element} tabtitle The title of the tab
 * @param {element} tabcontent The content for the tab
 * @param {element} tabcommands The commands for the tab
 */
navigation_tab_panel.prototype.add_to_tab_panel = function (tabname, tabtitle, tabcontent, tabcommands) {
    if (!this.tabpanelexists) {
        this.create_tab_panel();
    }

    var firsttab = (this.tabcount==0);

    var sidetab = document.createElement('div');
    sidetab.setAttribute('id', tabname+'_sidebarpopup');
    YAHOO.util.Dom.addClass(sidetab, 'sideblock_tab');

    if (firsttab) {
        YAHOO.util.Dom.addClass(sidetab, 'firsttab');
    }
    var sidetabtitle = document.createElement('div');
    sidetabtitle.appendChild(tabtitle);
    sidetabtitle.setAttribute('id', tabname+'_title');
    YAHOO.util.Dom.addClass(sidetabtitle, 'title');
    tabcontent.appendChild(create_shadow(true, true, true, false));
    sidetab.appendChild(sidetabtitle);

    if (tabcommands.childNodes.length>0) {
        tabcontent.appendChild(tabcommands);
    }

    this.navigationpanel.appendChild(sidetab);

    var position = YAHOO.util.Dom.getXY(sidetabtitle);
    position[0] += sidetabtitle.offsetWidth;
    if (YAHOO.env.ua.ie > 0 && YAHOO.env.ua.ie < 8) {
        position[0] -= 2;
    }

    this.tabpanels[tabname] = new YAHOO.widget.Panel('navigation_tab_panel_'+tabname, {
        close:false,
        draggable:false,
        constraintoviewport: false,
        underlay:"none",
        visible:false,
        monitorresize:false,
        /*context:[tabname+'_title','tl','tr',['configChanged','beforeShow','changeBody']],*/
        xy:position,
        autofillheight:'body'});
    this.tabpanels[tabname].showEvent.subscribe(this.resize_tab, this, true);
    this.tabpanels[tabname].setBody(tabcontent);
    this.tabpanels[tabname].render(this.navigationpanel);

    this.tabpanelelementnames[this.tabpanelelementnames.length] = tabname;
    this.tabpanelelementcontents[tabname] = tabcontent;
    this.tabcount++;

    YAHOO.util.Event.addListener(sidetab, "mouseover", this.show_tab, tabname, this);
}
/**
 * This function handles checking the size, and positioning of the navigaiton
 * panel when expansion events occur, or when the panel is shown, or if the window
 * is resized
 *
 * There are undoubtably some bugs in this little bit of code. For one it relies
 * on the padding set in CSS by the YUI:sam skin, if you are hitting a problem
 * whereby the navigation extends beyond its border, or doesn't fill to its own
 * border check the value assigned to padding for the panel body `.yui_panel .bd`
 *
 * @return {bool}
 */
navigation_tab_panel.prototype.resize_tab = function () {
    var screenheight = YAHOO.util.Dom.getViewportHeight();
    var tabheight = parseInt(this.tabpanels[this.showntab.tabname].body.offsetHeight);
    var tabtop = parseInt(this.tabpanels[this.showntab.tabname].cfg.getProperty('y'));
    var titletop = YAHOO.util.Dom.getY(this.showntab.tabname+'_title');
    var scrolltop = (document.all)?document.body.scrollTop:window.pageYOffset;
    // This makes sure that the panel is the same height as the tab title to
    // begin with
    if (tabtop > (10+scrolltop) && tabtop > (titletop+scrolltop)) {
        this.tabpanels[this.showntab.tabname].cfg.setProperty('y', titletop+scrolltop);
    }

    // This makes sure that if the panel is big it is moved up to ensure we don't
    // have wasted space above the panel
    if ((tabtop+tabheight)>screenheight && tabtop > 10) {
        tabtop = (screenheight-tabheight-10);
        if (tabtop<10) {
            tabtop = 10;
        }
        this.tabpanels[this.showntab.tabname].cfg.setProperty('y', tabtop+scrolltop);
    }

    // This makes the panel constrain to the screen's height if the panel is big
    if (tabtop <= 10 && ((tabheight+tabtop*2) > screenheight || YAHOO.util.Dom.hasClass(this.tabpanels[this.showntab.tabname].body, 'oversized_content'))) {
        this.tabpanels[this.showntab.tabname].cfg.setProperty('height', (screenheight-39));
        YAHOO.util.Dom.setStyle(this.tabpanels[this.showntab.tabname].body, 'height', (screenheight-59)+'px');
        YAHOO.util.Dom.addClass(this.tabpanels[this.showntab.tabname].body, 'oversized_content');
    }
}
/**
 * This function sets everything up for the show even and then calls the panel's
 * show event once we are happy.
 *
 * This function is responsible for closing any open panels, removing show events
 * so we don't refresh unnessecarily and adding events to trap closing, and resizing
 * events
 *
 * @param {event} e The event that fired to get us here
 * @param {string} tabname The tabname to open
 * @return {bool}
 */
navigation_tab_panel.prototype.show_tab = function (e, tabname) {
    if (this.showntab !== null) {
        this.hide_tab(e, this.showntab.tabname);
    }
    this.showntab = {event:e, tabname:tabname};
    this.tabpanels[tabname].show(e, this.tabpanel);
    YAHOO.util.Dom.addClass(tabname+'_title', 'active_tab');
    YAHOO.util.Event.removeListener(tabname+'_sidebarpopup', "mouseover", this.show_tab);
    YAHOO.util.Event.addListener('navigation_tab_panel_'+tabname, "click", function (e){this.preventhide = true}, this, true);
    YAHOO.util.Event.addListener(tabname+'_sidebarpopup', "click", this.hide_tab, tabname, this);
    YAHOO.util.Event.addListener(window, 'resize', this.resize_tab, this, true);
    YAHOO.util.Event.addListener(document.body, "click", this.hide_tab, tabname, this);
    return true;
}
/**
 * This function closes the open tab and sets the listeners up to handle the show
 * event again
 *
 * @param {event} e The event that fired to get us here
 * @param {string} tabname The tabname to close
 * @return {bool}
 */
navigation_tab_panel.prototype.hide_tab = function(e, tabname) {
    if (this.preventhide===true) {
        this.preventhide = false;
    } else {
        this.showntab = null;
        YAHOO.util.Event.addListener(tabname+'_sidebarpopup', "mouseover", this.show_tab, tabname, this);
        YAHOO.util.Event.removeListener(window, 'resize', this.resize_tab);
        YAHOO.util.Event.removeListener(document.body, "click", this.hide_tab);
        YAHOO.util.Dom.removeClass(tabname+'_title', 'active_tab');
        this.tabpanels[tabname].hide(e, this.tabpanel);
    }
}
/**
 * This function removes a tab from the navigation tab panel
 * @param {string} tabname
 * @return {bool}
 */
navigation_tab_panel.prototype.remove_from_tab_panel = function(tabname) {
    var tab = document.getElementById(tabname+'_sidebarpopup');
    if (!tab) {
        return false;
    }
    tab.parentNode.removeChild(tab);
    this.tabpanels[tabname].destroy();
    this.tabpanels[tabname] = null;
    this.tabcount--;
    if (this.tabcount === 0) {
        this.remove_tab_panel();
    }
    return true;
}

/**
 * Global navigation tree branch object used to parse an XML branch
 * into a usable object, and then to inject it into the DOM
 * @class navigation_tree_branch
 * @constructor
 */
function navigation_tree_branch(treename) {
    this.treename = treename;
    this.myname = null;
    this.mytitle = null;
    this.myclass = null;
    this.myid = null;
    this.mykey = null;
    this.mytype = null;
    this.mylink = null;
    this.myicon = null;
    this.myexpandable = null;
    this.expansionceiling = null;
    this.myhidden = false;
    this.haschildren = false;
    this.mychildren = false;
}
/**
 * This function populates the object from an XML branch
 * @param {xmlnode} branch The XML branch to turn into an object
 */
navigation_tree_branch.prototype.load_from_xml_node = function (branch) {
    this.myname = null;
    this.mytitle = branch.getAttribute('title');
    this.myclass = branch.getAttribute('class');
    this.myid = branch.getAttribute('id');
    this.mylink = branch.getAttribute('link');
    this.myicon = branch.getAttribute('icon');
    this.mykey = branch.getAttribute('key');
    this.mytype = branch.getAttribute('type');
    this.myexpandable = branch.getAttribute('expandable');
    this.expansionceiling = branch.getAttribute('expansionceiling');
    this.myhidden = (branch.getAttribute('hidden')=='true');
    this.haschildren = (branch.getAttribute('haschildren')=='true');

    if (this.myid && this.myid.match(/^expandable_branch_\d+$/)) {
        YAHOO.moodle.navigation.expandablebranchcount++;
        this.myid = 'expandable_branch_'+YAHOO.moodle.navigation.expandablebranchcount;
    }

    for (var i=0; i<branch.childNodes.length;i++) {
        var node = branch.childNodes[i];
        switch (node.nodeName.toLowerCase()) {
            case 'name':
                this.myname = node.firstChild.nodeValue;
                break;
            case 'children':
                this.mychildren = node;
        }
    }
}
/**
 * This function injects the node into the navigation tree
 * @param {element} element The branch to inject into {element}
 * @param {navigation_tree} gntinstance The instance of the navigaiton_tree that this branch
 *         is associated with
 * @return {element} The now added node
 */
navigation_tree_branch.prototype.inject_into_dom = function (element, gntinstance) {
    var branchli = document.createElement('li');
    var branchp = document.createElement('p');
    YAHOO.util.Dom.addClass(branchp, 'tree_item');
    if ((this.myexpandable !==null || this.haschildren) && this.expansionceiling===null) {
        YAHOO.util.Dom.addClass(branchp, 'branch');
        YAHOO.util.Dom.addClass(branchli, 'collapsed');
        YAHOO.util.Event.addListener(branchp, 'click', gntinstance.toggleexpansion, this, gntinstance);
        if (this.myexpandable) {
            YAHOO.util.Event.addListener(branchp, 'click', gntinstance.init_load_ajax, {branchid:this.mykey,id:this.myid,type:this.mytype,element:branchp}, gntinstance);
        }
    }
    if (this.myclass != null) {
        YAHOO.util.Dom.addClass(branchp, this.myclass);
    }
    if (this.myid != null) {
        branchp.setAttribute('id',this.myid);
    }
    var branchicon = false;
    if (this.myicon != null) {
        branchicon = document.createElement('img');
        branchicon.setAttribute('src',this.myicon);
        branchicon.setAttribute('alt','');
        this.myname = ' '+this.myname;
    }
    if (this.mylink === null) {
        if (branchicon !== false) {
            branchp.appendChild(branchicon);
        }
        branchp.appendChild(document.createTextNode(this.myname.replace(/\n/g, '<br />')));
    } else {
        var branchlink = document.createElement('a');
        branchlink.setAttribute('title', this.mytitle);
        branchlink.setAttribute('href', this.mylink);
        if (branchicon !== false) {
            branchlink.appendChild(branchicon);
        }
        branchlink.appendChild(document.createTextNode(this.myname.replace(/\n/g, '<br />')));
        if (this.myhidden) {
            YAHOO.util.Dom.addClass(branchlink, 'dimmed');
        }
        branchp.appendChild(branchlink);
    }
    branchli.appendChild(branchp);
    element.appendChild(branchli);
    return branchli;
}

/**
 * Creates a new JS instance of a global navigation tree and kicks it into gear
 * @param {string} treename The name of the tree
 */
function setup_new_navtree(treename) {
    var key = YAHOO.moodle.navigation.treecollection.length;
    YAHOO.moodle.navigation.treecollection[key] = new navigation_tree(treename, key);
    YAHOO.moodle.navigation.treecollection[key].initialise();
}

/**
 * This function moves all navigation tree instances that are currently
 * displayed in the sidebar back into their block positions
 */
function move_all_sidetabs_to_block_position(e) {
    for (var i=0; i<YAHOO.moodle.navigation.treecollection.length;i++) {
        var navtree = YAHOO.moodle.navigation.treecollection[i];
        if (navtree.position != 'block') {
            navtree.move_to_block_position(e);
        }
    }
}

/**
 * This function create a series of DIV's appended to an element to give it a
 * shadow
 * @param {bool} top Displays a top shadow if true
 * @param {bool} right Displays a right shadow if true
 * @param {bool} bottom Displays a bottom shadow if true
 * @param {bool} left Displays a left shadow if true
 * @return {element}
 */
function create_shadow(top, right, bottom, left) {
    var shadow = document.createElement('div');
    YAHOO.util.Dom.addClass(shadow, 'divshadow');
    if (YAHOO.env.ua.ie > 0 && YAHOO.env.ua.ie < 7) {
        // IE6 just doest like my shadow...
        return shadow;
    }
    var createShadowDiv = function(cname) {
        var shadowdiv = document.createElement('div');
        YAHOO.util.Dom.addClass(shadowdiv, cname);
        if (YAHOO.env.ua.ie > 0 && YAHOO.env.ua.ie < 7) {
            // IE version less than 7 doesnt support alpha
            YAHOO.util.Dom.setStyle(shadowdiv, 'opacity', 0.3);
        }
        return shadowdiv;
    }
    if (top) shadow.appendChild(createShadowDiv('shadow_top'));
    if (right) shadow.appendChild(createShadowDiv('shadow_right'));
    if (bottom) shadow.appendChild(createShadowDiv('shadow_bottom'));
    if (left) shadow.appendChild(createShadowDiv('shadow_left'));
    if (top && left) shadow.appendChild(createShadowDiv('shadow_top_left'));
    if (bottom && left) shadow.appendChild(createShadowDiv('shadow_bottom_left'));
    if (top && right) shadow.appendChild(createShadowDiv('shadow_top_right'));
    if (bottom && right) shadow.appendChild(createShadowDiv('shadow_bottom_right'));
    return shadow;
}
/**
 * This function removes any shadows that a node and its children may have
 * @param {element} el The element to remove the shadow from
 * @return {bool}
 */
function remove_shadow(el) {
    var shadows = YAHOO.util.Dom.getElementsByClassName('divshadow', 'div', el);
    if (shadows == null || shadows.length == 0) return true;
    for (var i=0;i<shadows.length;i++) {
        shadows[i].parentNode.removeChild(shadows[i]);
    }
    return true;
}
