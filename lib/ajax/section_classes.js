/**
 * library for ajaxcourse formats, the classes and related functions for
 * sections and resources.
 *
 * This library requires a 'main' object created in calling document.
 *
 * Drag and drop notes:
 *
 *   Dropping an activity or resource on a section will always add the activity
 *   or resource at the end of that section.
 *
 *   Dropping an activity or resource on another activity or resource will
 *   always move the former just above the latter.
 *
 * $Id$
 */


/**
 * section_class
 */
function section_class(id, group, config, isDraggable) {
    this.init_section(id, group, config, isDraggable);
}

YAHOO.extend(section_class, YAHOO.util.DDProxy);


section_class.prototype.debug = false;


section_class.prototype.init_section = function(id, group, config, isDraggable) {

    if (!id) {
        return;
    }

    this.is = 'section';
    this.sectionId = null; // Section number. This is NOT the section id from
                            // the database.

    if (!isDraggable) {
        this.initTarget(id, group, config);
        this.removeFromGroup('sections');
    } else {
        this.init(id, group, config);
        this.handle = null;
    }

    this.createFrame();
    this.isTarget = true;

    this.resources = [];
    this.numberDisplay = null; // Used to display the section number on the top left
                                // of the section. Not used in all course formats.
    this.summary = null;
    this.content_td = null;
    this.hidden = false;
    this.highlighted = false;
    this.showOnly = false;
    this.resources_ul = null;
    this.process_section();

    this.viewButton = null;
    this.highlightButton = null;
    this.showOnlyButton = null;
    this.init_buttons();

    if (isDraggable) {
        this.add_handle();
    }
    if (this.debug) {
        YAHOO.log("init_section "+id+" draggable="+isDraggable);
    }
    if (YAHOO.util.Dom.hasClass(this.getEl(),'hidden')) {
        this.toggle_hide(null,null,true);
    }
}


section_class.prototype.init_buttons = function() {
    var commandContainer = YAHOO.util.Dom.getElementsByClassName('right',null,this.getEl())[0];

    //clear all but show only button
    var commandContainerCount = commandContainer.childNodes.length;

    for (var i=(commandContainerCount-1); i>0; i--) {
        commandContainer.removeChild(commandContainer.childNodes[i])
    }

    if (main.getString('courseformat', this.sectionId) != "weeks" && this.sectionId > 0) {
        var highlightbutton = main.mk_button('div', '/i/marker.gif', main.getString('marker', this.sectionId));
        YAHOO.util.Event.addListener(highlightbutton, 'click', this.mk_marker, this, true);
        commandContainer.appendChild(highlightbutton);
        this.highlightButton = highlightbutton;
    }

    if (this.sectionId > 0 ) {
        var viewbutton = main.mk_button('div', '/i/hide.gif', main.getString('hidesection', this.sectionId), [['title', main.portal.strings['hide'] ]]);
    YAHOO.util.Event.addListener(viewbutton, 'click', this.toggle_hide, this,true);
    commandContainer.appendChild(viewbutton);
    this.viewButton = viewbutton;
}

}

section_class.prototype.add_handle = function() {
    var handleRef = main.mk_button('a', '/i/move_2d.gif', main.getString('movesection', this.sectionId),
            [['title', main.portal.strings['move'] ], ['style','cursor:move']]);

    YAHOO.util.Dom.generateId(handleRef, 'sectionHandle');

    this.handle = handleRef;

    this.getEl().childNodes[0].appendChild(handleRef);
    this.setHandleElId(this.handle.id);
}


section_class.prototype.process_section = function() {
    this.content_td = this.getEl().childNodes[1];

    if (YAHOO.util.Dom.hasClass(this.getEl(),'current')) {
        this.highlighted = true;
        main.marker = this;
    }

    // Create holder for display number for access later

    this.numberDisplay = document.createElement('div');
    this.numberDisplay.innerHTML = this.getEl().childNodes[0].innerHTML;
    this.getEl().childNodes[0].innerHTML = '';
    this.getEl().childNodes[0].appendChild(this.numberDisplay);

    this.sectionId = this.id.replace(/section-/i, ''); // Okay, we will have to change this if we
    // ever change the id attributes format
    // for the sections.
    if (this.debug) {
        YAHOO.log("Creating section "+this.getEl().id+" in position "+this.sectionId);
    }

    // Find/edit resources
    this.resources_ul = this.content_td.getElementsByTagName('ul')[0];
    var i=0;
    while (this.resources_ul && this.resources_ul.className != 'section img-text') {
        this.resources_ul = this.content_td.getElementsByTagName('ul')[i];
        i++;
    }

    if (!this.resources_ul) {
        this.resources_ul = document.createElement('ul');
        this.resources_ul.className='section';
        this.content_td.insertBefore(this.resources_ul, this.content_td.lastChild);
    }
    var resource_count = this.resources_ul.getElementsByTagName('li').length;

    for (var i=0;i<resource_count;i++) {
        var resource = this.resources_ul.getElementsByTagName('li')[i];
        this.resources[this.resources.length] = new resource_class(resource.id, 'resources', null, this);
    }
    var summarysection = YAHOO.util.Dom.getElementsByClassName('summary', null, this.getEl())[0].firstChild;
    if (summarysection != null) {
        this.summary = summarysection.data || '';
    }
}

section_class.prototype.startDrag = function(x, y) {
    //operates in point mode
    YAHOO.util.DDM.mode = YAHOO.util.DDM.POINT;

    //remove from resources group temporarily
    this.removeFromGroup('resources');

    //reinitialize dd element
    this.getDragEl().innerHTML = '';

    var targets = YAHOO.util.DDM.getRelated(this, true);

    if (this.debug) {
        YAHOO.log(this.id + " startDrag, "+targets.length + " targets");
    }
}


section_class.prototype.onDragDrop = function(e, id) {
    // get the drag and drop object that was targeted
    var target = YAHOO.util.DDM.getDDById(id);

    if (this.debug) {
        YAHOO.log("Section dropped on id="+id+" (I am "+this.getEl().id+") x="
                +YAHOO.util.Dom.getXY(this.getDragEl()));
    }
    this.move_to_section(target);

    //add back to resources group
    this.addToGroup('resources');
}


section_class.prototype.endDrag = function() {
    //nessicary to defeat default action

    //add back to resources group
    this.addToGroup('resources');
}


section_class.prototype.move_to_section = function(target) {
    var tempTd = document.createElement('td');
    var tempStore = null;
    var sectionCount = main.sections.length;
    var found = null;

    //determine if original is above or below target and adjust loop
    var oIndex = main.get_section_index(this);
    var tIndex = main.get_section_index(target);

    if (this.debug) {
        YAHOO.log("original is at: "+oIndex+" target is at:"+tIndex+" of "+(sectionCount-1));
    }
    if (oIndex < tIndex) {
        var loopCondition = 'i<sectionCount';
        var loopStart = 1;
        var loopInc = 'i++';
        var loopmodifier = 'i - 1';
        var targetOffset = 0;
    } else {
        var loopCondition = 'i > 0';
        var loopStart = sectionCount - 1;
        var loopInc = 'i--';
        var loopmodifier = 'i + 1';
        var targetOffset = 1;
    }

    //move on backend
    main.connect('POST','class=section&field=move',null,'id='+this.sectionId+'&value=' + (target.sectionId - targetOffset));

    //move on front end
    for (var i=loopStart; eval(loopCondition); eval(loopInc)) {

        if ((main.sections[i] == this) && !found) {
            //encounter with original node
            if (this.debug) {
                YAHOO.log("Found Original "+main.sections[i].getEl().id);
            }
            if (main.sections[i] == this) {
                found = true;
            }
        } else if (main.sections[i] == target) {
            //encounter with target node
            if (this.debug) {
                YAHOO.log("Found target "+main.sections[i].getEl().id);
            }
            main.sections[i].swap_with_section(main.sections[eval(loopmodifier)]);
            main.sections[i].swap_dates(main.sections[eval(loopmodifier)]);
            found = false;
            break;
        } else if (found) {
            //encounter with nodes inbetween
            main.sections[i].swap_with_section(main.sections[eval(loopmodifier)]);
            main.sections[i].swap_dates(main.sections[eval(loopmodifier)]);
        }
    }
}


section_class.prototype.swap_with_section = function(sectionIn) {
    var tmpStore = null;

    thisIndex = main.get_section_index(this);
    targetIndex = main.get_section_index(sectionIn);
    main.sections[targetIndex] = this;
    main.sections[thisIndex] = sectionIn;

    this.changeId(targetIndex);
    sectionIn.changeId(thisIndex);

    if (this.debug) {
        YAHOO.log("Swapping "+this.getEl().id+" with "+sectionIn.getEl().id);
    }
    // Swap the sections.
    YAHOO.util.DDM.swapNode(this.getEl(), sectionIn.getEl());

    // Sections contain forms to add new resources/activities. These forms
    // have not been updated to reflect the new positions of the sections that
    // we have swapped. Let's swap the two sections' forms around.
    if (this.getEl().getElementsByTagName('form')[0].parentNode
            && sectionIn.getEl().getElementsByTagName('form')[0].parentNode) {

        YAHOO.util.DDM.swapNode(this.getEl().getElementsByTagName('form')[0].parentNode,
                sectionIn.getEl().getElementsByTagName('form')[0].parentNode);
    } else {
        YAHOO.log("Swapping sections: form not present in one or both sections", "warn");
    }
}


section_class.prototype.toggle_hide = function(e,target,superficial) {
    var strhide = main.portal.strings['hide'];
    var strshow = main.portal.strings['show'];
    if (this.hidden) {
        YAHOO.util.Dom.removeClass(this.getEl(), 'hidden');
        this.viewButton.childNodes[0].src = this.viewButton.childNodes[0].src.replace(/show.gif/i, 'hide.gif');
        this.viewButton.childNodes[0].alt = this.viewButton.childNodes[0].alt.replace(strshow, strhide);
        this.viewButton.childNodes[0].title = this.viewButton.childNodes[0].title.replace(strshow, strhide); //IE hack.
        this.viewButton.title = this.viewButton.title.replace(strshow, strhide);
        this.hidden = false;

        if (!superficial) {
            main.connect('POST', 'class=section&field=visible', null, 'value=1&id='+this.sectionId);
            for (var x=0; x<this.resources.length; x++) {
                this.resources[x].toggle_hide(null, null, true, this.resources[x].hiddenStored);
                this.resources[x].hiddenStored = null;
            }
        }

    } else {
        YAHOO.util.Dom.addClass(this.getEl(), 'hidden');
        this.viewButton.childNodes[0].src = this.viewButton.childNodes[0].src.replace(/hide.gif/i, 'show.gif');
        this.viewButton.childNodes[0].alt = this.viewButton.childNodes[0].alt.replace(strhide, strshow);
        this.viewButton.childNodes[0].title = this.viewButton.childNodes[0].title.replace(strhide, strshow); //IE hack.
        this.viewButton.title = this.viewButton.title.replace(strhide, strshow);
        this.hidden = true;

        if (!superficial) {
            main.connect('POST', 'class=section&field=visible', null, 'value=0&id='+this.sectionId);
            for (var x=0; x<this.resources.length; x++) {
                this.resources[x].hiddenStored = this.resources[x].hidden;
                this.resources[x].toggle_hide(null, null, true, true);
            }
        }
    }
}


section_class.prototype.toggle_highlight = function() {
    if (this.highlighted) {
        YAHOO.util.Dom.removeClass(this.getEl(), 'current');
        this.highlighted = false;
    } else {
        YAHOO.util.Dom.addClass(this.getEl(), 'current');
        this.highlighted = true;
    }
}


section_class.prototype.mk_marker = function() {
    if (main.marker != this) {
        main.update_marker(this);
    } else {
        // If currently the marker
        main.marker = null;

        main.connect('POST', 'class=course&field=marker', null, 'value=0');
        this.toggle_highlight();
    }
}


section_class.prototype.changeId = function(newId) {
    this.sectionId = newId;
    this.numberDisplay.firstChild.data = newId;

    //main.connectQueue_add('POST','class=section&field=all',null,'id='+newId+"&summary="+main.mk_safe_for_transport(this.summary)+"&sequence="+this.write_sequence_list(true)+'&visible='+(this.hidden?0:1))

    if (main.marker == this) {
        main.update_marker(this);
    }
}


section_class.prototype.get_resource_index = function(el) {
    for (var x=0; x<this.resources.length; x++) {
        if (this.resources[x] == el) {
            return x;
        }
    }
    YAHOO.log("Could not find resource to remove "+el.getEl().id, "error");
    return -1;
}


section_class.prototype.remove_resource = function(el) {

    var resourceEl = el.getEl();
    var parentEl = resourceEl.parentNode;
    if (!parentEl) {
        return false;
    }

    var resourceCount = this.resources.length;

    if (resourceCount == 1) {
        if (this.resources[0] == el) {
            this.resources = new Array();
        }
    } else {
        var found = false;
        for (var i=0; i<resourceCount; i++) {
            if (found) {
                this.resources[i - 1] = this.resources[i];
                if (i == resourceCount - 1) {
                    this.resources = this.resources.slice(0, -1);
                    resourceCount--;
                }
                this.resources[i - 1].update_index(i - 1);
            } else if (this.resources[i] == el) {
                found = true;
            }
        }
    }
    // Remove any extra text nodes to keep DOM clean.
    var kids = parentEl.childNodes;

    for (var i=0; i<kids.length; i++) {
        if (kids[i].nodeType == 3) {
            YAHOO.log('Removed extra text node.');
            parentEl.removeChild(kids[i]);
        }
    }
    parentEl.removeChild(resourceEl);

    this.write_sequence_list();
    return true;
}


section_class.prototype.insert_resource = function(el, targetel) {
    var resourcecount = this.resources.length;
    var found = false;
    var tempStore = nextStore = null;

    //update in backend
    var targetId = '';
    if (targetel) {
        targetId = targetel.id;
    }
    if (this.debug) {
        YAHOO.log('id='+el.id+', beforeId='+targetId+', sectionId='+this.sectionId);
    }
    main.connect('POST', 'class=resource&field=move', null,
            'id='+el.id+'&beforeId='+targetId+'&sectionId='+this.sectionId);

    //if inserting into a hidden resource hide
    if (this.hidden) {
        el.hiddenStored = el.hidden;
        el.toggle_hide(null, null, true, true);
    } else {
        if (el.hiddenStored != null) {
            el.toggle_hide(null, null, true, el.hiddenStored);
            el.hiddenStored = null;
        }
    }
    //update model
    if (!targetel) {
        this.resources[this.resources.length] = el;
    } else {
        for (var i=0; i<resourcecount; i++) {
            if (found) {
                tempStore = this.resources[i];
                this.resources[i] = nextStore;
                nextStore = tempStore;

                if (nextStore != null)
                    nextStore.update_index(i+1);

            } else if (this.resources[i] == targetel) {
                found = true;
                nextStore = this.resources[i];
                this.resources[i] = el;
                resourcecount++;

                this.resources[i].update_index(i, this.ident);
                nextStore.update_index(i + 1);
            }
        }
    }
    //update on frontend
    if (targetel) {
        this.resources_ul.insertBefore(el.getEl(), targetel.getEl());
        //this.resources_ul.insertBefore(document.createTextNode(' '), targetel.getEl());
    } else {
        this.resources_ul.appendChild(el.getEl());
        //this.resources_ul.appendChild(document.createTextNode(' '));
    }
    el.parentObj = this;
}


section_class.prototype.write_sequence_list = function(toReturn) {
    var listOutput = '';

    for (var i=0; i<this.resources.length; i++) {
        listOutput += this.resources[i].id;
        if (i != (this.resources.length-1)) {
            listOutput += ',';
        }
    }
    if (toReturn) {
        return listOutput;
    }
}




/**
 * resource_class extends util.DDProxy
 */
function resource_class(id,group,config,parentObj) {
    this.init_resource(id,group,config,parentObj);
}

YAHOO.extend(resource_class, YAHOO.util.DDProxy);


resource_class.prototype.debug = false;


resource_class.prototype.init_resource = function(id, group, config, parentObj) {
    if (!id) {
        YAHOO.log("Init resource, NO ID FOUND!", 'error');
        return;
    }

    // Some constants.
    this.NOGROUPS = 0;
    this.SEPARATEGROUPS = 1;
    this.VISIBLEGROUPS = 2;

    this.is = 'resource';
    this.init(id, group, config);
    this.createFrame();
    this.isTarget = true;

    this.id = this.getEl().id.replace(/module-/i, '');

    this.hidden = false;
    if (YAHOO.util.Dom.hasClass(this.getEl().getElementsByTagName('a')[0], 'dimmed') ||
        YAHOO.util.Dom.hasClass(this.getEl().getElementsByTagName('span')[0], 'dimmed_text')) {
        this.hidden = true;
    }
    this.hiddenStored = null;

    this.groupmode = null;  // Can be null (i.e. does not apply), 0, 1 or 2.

    // Check for label first
    firstspan = this.getEl().getElementsByTagName('span')[0];
    if (firstspan.className == 'label' || firstspan.className == 'dimmed_text') {
        this.linkContainer = firstspan;
    } else {
        this.linkContainer = this.getEl().getElementsByTagName('a')[0];
    }

    this.commandContainer = null;
    this.indentLeftButton = null;
    this.indentRightButton = null;
    this.viewButton = null;
    this.groupButton = null;
    this.handle = null;
    this.init_buttons();

    this.parentObj = parentObj;

    if (this.debug) {
        YAHOO.log("init_resource "+id+" parent = "+parentObj.getEl().id);
    }
}


/**
 * The current strategy is to look at the DOM tree to get information on the
 * resource and it's current mode. This is bad since we are dependant on
 * the html that is output from serverside logic. Seemingly innocuous changes
 * like changing the language string for the title of a button will break
 * our JavaScript here. This is brittle.
 *
 * First, we clear the buttons container. Then:
 *   We need to add the new-style move handle.
 *   The old style move button (up/down) needs to be removed.
 *   Move left button (if any) needs an event handler.
 *   Move right button (if any) needs an event handler.
 *   Update button stays as it is. Add it back.
 *   Delete button needs an event handler.
 *   Visible button is a toggle. It needs an event handler too.
 *   Group mode button is a toggle. It needs an event handler too.
 */
resource_class.prototype.init_buttons = function() {

    var commandContainer = YAHOO.util.Dom.getElementsByClassName('commands',
            'span', this.getEl())[0];

    if (commandContainer == null) {
        YAHOO.log('Cannot find command container for '+this.getEl().id, 'error');
        return;
    }

    // Language strings.
    var strgroupsnone = main.portal.strings['groupsnone']+' ('+main.portal.strings['clicktochange']+')';
    var strgroupsseparate = main.portal.strings['groupsseparate']+' ('+main.portal.strings['clicktochange']+')';
    var strgroupsvisible = main.portal.strings['groupsvisible']+' ('+main.portal.strings['clicktochange']+')';

    this.commandContainer = commandContainer;
    var buttons = commandContainer.getElementsByTagName('a');

    // Buttons that we might need to add back in.
    var moveLeft = false;
    var moveRight = false;
    var updateButton = null;

    // for RTL support
    var isrtl = (document.getElementsByTagName("html")[0].dir=="rtl");

    for (var x=0; x<buttons.length; x++) {
        if (buttons[x].className == 'editing_moveleft') {
            moveLeft = true;
        } else if (buttons[x].className == 'editing_moveright') {
            moveRight = true;
        } else if (buttons[x].className == 'editing_update') {
            updateButton = buttons[x].cloneNode(true);
        } else if (buttons[x].className == 'editing_groupsnone') {
            this.groupmode = this.NOGROUPS;
        } else if (buttons[x].className == 'editing_groupsseparate') {
            this.groupmode = this.SEPARATEGROUPS;
        } else if (buttons[x].className == 'editing_groupsvisible') {
            this.groupmode = this.VISIBLEGROUPS;
        }
    }

    if (updateButton == null) {
        // Update button must always be present.
        YAHOO.log('Cannot find updateButton for '+this.getEl().id, 'error');
    }

    // Clear all the buttons.
    commandContainer.innerHTML = '';

    // Add move-handle for drag and drop.
    var handleRef = main.mk_button('a', '/i/move_2d.gif', main.portal.strings['move'],
            [['style', 'cursor:move']],
            [['height', '11'], ['width', '11'], ['style', 'margin-right:3px; border:0;']]);

    YAHOO.util.Dom.generateId(handleRef, 'sectionHandle');
    this.handle = handleRef;
    commandContainer.appendChild(handleRef);
    this.setHandleElId(this.handle.id);

    // Add indentation buttons if needed (move left, move right).
    if (moveLeft) {
        var button = main.mk_button('a', (isrtl?'/t/right.gif':'/t/left.gif'), main.portal.strings['moveleft'],
                [['class', 'editing_moveleft']]);
        YAHOO.util.Event.addListener(button, 'click', this.indent_left, this, true);
        commandContainer.appendChild(button);
        this.indentLeftButton = button;
    }

    if (moveRight) {
        var button = main.mk_button('a', (isrtl?'/t/left.gif':'/t/right.gif'), main.portal.strings['moveright'],
                [['class', 'editing_moveright']]);
        YAHOO.util.Event.addListener(button, 'click', this.indent_right, this, true);
        commandContainer.appendChild(button);
        this.indentRightButton = button;
    }

    // Add edit button back in.
    commandContainer.appendChild(updateButton);

    // Add the delete button.
    var button = main.mk_button('a', '/t/delete.gif', main.portal.strings['delete']);
    YAHOO.util.Event.addListener(button, 'click', this.delete_button, this, true);
    commandContainer.appendChild(button);

    // Add the hide or show button.
    if (this.hidden) {
        var button = main.mk_button('a', '/t/show.gif', main.portal.strings['show']);
    } else {
        var button = main.mk_button('a', '/t/hide.gif', main.portal.strings['hide']);
    }
    YAHOO.util.Event.addListener(button, 'click', this.toggle_hide, this, true);
    commandContainer.appendChild(button);
    this.viewButton = button;

    // Add the groupmode button if needed.
    if (this.groupmode != null) {
        if (this.groupmode == this.NOGROUPS) {
            var button = main.mk_button('a', '/t/groupn.gif', strgroupsnone);
        } else if (this.groupmode == this.SEPARATEGROUPS) {
            var button = main.mk_button('a', '/t/groups.gif', strgroupsseparate);
        } else {
            var button = main.mk_button('a', '/t/groupv.gif', strgroupsvisible);
        }
        YAHOO.util.Event.addListener(button, 'click', this.toggle_groupmode, this, true);
        commandContainer.appendChild(button);
        this.groupButton = button;
    }
}


resource_class.prototype.indent_left = function() {

    var spacer = YAHOO.util.Dom.getElementsByClassName('spacer',
            'img', this.getEl())[0];
    if (!spacer) {
        if (this.debug) {
            YAHOO.log('Could not indent left: spacer image does not exist', 'error');
        }
        return false;
    }
    if (spacer.width > 20) {
        spacer.width -= 20;
    } else {
        // Remove the spacer.
        resource = this.getEl();
        resource.removeChild(spacer);

        // Remove the indent left button as well.
        var commandContainer = YAHOO.util.Dom.getElementsByClassName('commands',
                'span', this.getEl())[0];

        commandContainer.removeChild(this.indentLeftButton);
        this.indentLeftButton = null;
    }
    main.connect('POST', 'class=resource&field=indentleft', null, 'id='+this.id);
    return true;
}


resource_class.prototype.indent_right = function() {

    // for RTL support
    var isrtl = (document.getElementsByTagName("html")[0].dir=="rtl");

    var spacer = YAHOO.util.Dom.getElementsByClassName('spacer',
            'img', this.getEl())[0];
    if (!spacer) {
        var spacer = document.createElement('img');

        spacer.setAttribute('src', main.portal.strings['pixpath']+'/spacer.gif');
        spacer.className = 'spacer';
        spacer.setAttribute('alt', '');
        spacer.setAttribute('width', '20');
        spacer.setAttribute('height', '12');

        var resource = this.getEl();
        resource.insertBefore(spacer, resource.childNodes[0]);
    } else {
        spacer.width += 20;
    }
    // Add a indent left button if none is present.
    var commandContainer = YAHOO.util.Dom.getElementsByClassName('commands',
            'span', this.getEl())[0];

    if (!this.indentLeftButton) {
        var button = main.mk_button('a', (isrtl?'/t/right.gif':'/t/left.gif'), main.portal.strings['moveleft'],
                [['class', 'editing_moveleft']]);
        YAHOO.util.Event.addListener(button, 'click', this.indent_left, this, true);
        commandContainer.insertBefore(button, this.indentRightButton);
        this.indentLeftButton = button;
    }
    main.connect('POST', 'class=resource&field=indentright', null, 'id='+this.id);
    return true;
}


resource_class.prototype.toggle_hide = function(target, e, superficial, force) {
    var strhide = main.portal.strings['hide'];
    var strshow = main.portal.strings['show'];
    if (force != null) {
        if (this.debug) {
            YAHOO.log("Resource "+this.getEl().id+" forced to "+force);
        }
        this.hidden = !force;
    }
    if (this.hidden) {
        YAHOO.util.Dom.removeClass(this.linkContainer, 'dimmed');

        if (YAHOO.util.Dom.hasClass(this.linkContainer, 'dimmed_text')) {
            YAHOO.util.Dom.removeClass(this.linkContainer, 'dimmed_text');
            YAHOO.util.Dom.addClass(this.linkContainer, 'label');
        }
        this.viewButton.childNodes[0].src = this.viewButton.childNodes[0].src.replace(/show.gif/i, 'hide.gif');
        this.viewButton.childNodes[0].alt = this.viewButton.childNodes[0].alt.replace(strshow, strhide);
        this.viewButton.title = this.viewButton.title.replace(strshow, strhide);
        this.hidden = false;

        if (!superficial) {
            main.connect('POST', 'class=resource&field=visible', null, 'value=1&id='+this.id);
        }
    } else {
        YAHOO.util.Dom.addClass(this.linkContainer, 'dimmed');

        if (YAHOO.util.Dom.hasClass(this.linkContainer, 'label')) {
            YAHOO.util.Dom.addClass(this.linkContainer, 'dimmed_text');
            YAHOO.util.Dom.removeClass(this.linkContainer, 'label');
        }
        this.viewButton.childNodes[0].src = this.viewButton.childNodes[0].src.replace(/hide.gif/i, 'show.gif');
        this.viewButton.childNodes[0].alt = this.viewButton.childNodes[0].alt.replace(strhide, strshow);
        this.viewButton.title = this.viewButton.title.replace(strhide, strshow);
        this.hidden = true;

        if (!superficial) {
            main.connect('POST', 'class=resource&field=visible', null, 'value=0&id='+this.id);
        }
    }
}


resource_class.prototype.groupImages = ['/t/groupn.gif', '/t/groups.gif', '/t/groupv.gif'];


resource_class.prototype.toggle_groupmode = function() {
    this.groupmode++;
    if (this.groupmode > 2) {
        this.groupmode = 0;
    }

    var newtitle = this.groupButton.title;

    switch (this.groupmode) {
        case 0:
            newtitle = main.portal.strings['groupsnone']+' ('+main.portal.strings['clicktochange']+')';
            break;
        case 1:
            newtitle = main.portal.strings['groupsseparate']+' ('+main.portal.strings['clicktochange']+')';
            break;
        case 2:
            newtitle = main.portal.strings['groupsvisible']+' ('+main.portal.strings['clicktochange']+')';
            break;
    }

    this.groupButton.getElementsByTagName('img')[0].alt = newtitle;
    this.groupButton.title = newtitle;

    this.groupButton.getElementsByTagName('img')[0].src = main.portal.strings['pixpath']+this.groupImages[this.groupmode];
    main.connect('POST', 'class=resource&field=groupmode', null, 'value='+this.groupmode+'&id='+this.id);
}


resource_class.prototype.delete_button = function() {
    if (this.debug) {
    YAHOO.log("Deleting "+this.getEl().id+" from parent "+this.parentObj.getEl().id);
    }
    if (!confirm(main.getString('deletecheck', main.getString(this.is)+" "+this.id))) {
        return false;
    }
    this.parentObj.remove_resource(this);
    main.connect('POST', 'class=resource&action=DELETE&id='+this.id);
}


resource_class.prototype.update_index = function(index) {
    if (this.debug) {
        YAHOO.log("Updating Index for resource "+this.getEl().id+" to "+index);
    }
}


resource_class.prototype.startDrag = function(x, y) {
    YAHOO.util.DDM.mode = YAHOO.util.DDM.INTERSECT;

    //reinitialize dd element
    this.getDragEl().innerHTML = '';

    var targets = YAHOO.util.DDM.getRelated(this, true);
    if (this.debug) {
        YAHOO.log(this.id + " startDrag "+targets.length + " targets");
    }
}


resource_class.prototype.clear_move_markers = function(target) {
    if (target.is == 'section') {
        resources = target.resources;
    } else {
        resources = target.parentObj.resources;
    }
    for (var i=0; i<resources.length; i++) {
        if (resources[i].getEl() != null) {
            YAHOO.util.Dom.setStyle(resources[i].getEl().id, 'border', 'none');
        }
    }
}


resource_class.prototype.onDragOver = function(e, ids) {
    var target = YAHOO.util.DDM.getBestMatch(ids);

    this.clear_move_markers(target);

    if (target != this && (target.is == 'resource' || target.is == 'activity')) {
        // Add a top border to show where the drop will place the resource.
        YAHOO.util.Dom.setStyle(target.getEl().id, 'border-top', '1px solid #BBB');
    } else if (target.is == 'section' && target.resources.length > 0) {
        // We need to have a border at the bottom of the last activity in
        // that section.
        if (target.resources[target.resources.length - 1].getEl() != null) {
            YAHOO.util.Dom.setStyle(target.resources[target.resources.length - 1].getEl().id,
                'border-bottom', '1px solid #BBB');
        }
    }
}


resource_class.prototype.onDragOut = function(e, ids) {
    var target = YAHOO.util.DDM.getBestMatch(ids);
    if (target) {
        this.clear_move_markers(target);
    }
}


resource_class.prototype.onDragDrop = function(e, ids) {
    var target = YAHOO.util.DDM.getBestMatch(ids);
    if (!target) {
        YAHOO.log('onDragDrop: Target is not valid!', 'error');
    }

    if (this.debug) {
        YAHOO.log("Dropped on section id="+target.sectionId
                +", el="+this.getEl().id
                +", x="+YAHOO.util.Dom.getXY( this.getDragEl() ));
    }
    this.parentObj.remove_resource(this);

    if (target.is == 'resource' || target.is == 'activity') {
        target.parentObj.insert_resource(this, target);
    } else if (target.is == 'section') {
        target.insert_resource(this);
    }
    this.clear_move_markers(target);
    return;
}


resource_class.prototype.endDrag = function() {
    // Eliminates default action
}

section_class.prototype.swap_dates = function(el){
    var i=1;
    var divs = YAHOO.util.Selector.query('div .weekdates');

    for (div in divs) {
        divs[div].innerHTML = main.sectiondates[i];
        i++;
    }
}

