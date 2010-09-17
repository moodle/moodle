/**
 * library for ajaxcourse formats, the classes and related functions for drag and drop blocks
 *
 * this library requires a 'main' object created in calling document
 */


//set Drag and Drop to Intersect mode:
YAHOO.util.DDM.mode = YAHOO.util.DDM.INTERSECT;


/**
 * class for draggable block, extends YAHOO.util.DDProxy
 */
function block_class(id,group,config){
    this.init_block(id,group,config);
}
YAHOO.extend(block_class, YAHOO.util.DDProxy);

block_class.prototype.debug = false;


block_class.prototype.init_block = function(id, sGroup, config) {

    if (!id) {
        return;
    }

    //Drag and Drop
    this.init(id, sGroup, config);
    this.initFrame();
    this.createFrame();

    this.is = 'block';
    this.instanceId = this.getEl().id.replace(/inst/i, '');

    // Add the drag class (move handle) only to blocks that need it.
    YAHOO.util.Dom.addClass(this.getEl(), 'drag');

    this.addInvalidHandleType('a');

    var s = this.getEl().style;
    s.opacity = 0.76;
    s.filter = "alpha(opacity=76)";

    // specify that this is not currently a drop target
    this.isTarget = false;

    this.region = YAHOO.util.Region.getRegion(this.getEl());
    this.type = block_class.TYPE;

    //DHTML
    this.viewbutton = null;
    this.originalClass = this.getEl().className;

    this.init_buttons();
};


block_class.prototype.startDrag = function(x, y) {
    //operates in intersect mode
    YAHOO.util.DDM.mode = YAHOO.util.DDM.INTERSECT;

    YAHOO.log(this.id + " startDrag");

    var dragEl = this.getDragEl();
    var clickEl = this.getEl();

    dragEl.innerHTML = clickEl.innerHTML;
    dragEl.className = clickEl.className;
    dragEl.style.color = this.DDM.getStyle(clickEl, "color");;
    dragEl.style.backgroundColor = this.DDM.getStyle(clickEl, "backgroundColor");
    dragEl.style.border = '0px';

    var s = clickEl.style;
    s.opacity = .1;
    s.filter = "alpha(opacity=10)";

    var targets = YAHOO.util.DDM.getRelated(this, true);
    YAHOO.log(targets.length + " targets");

    //restyle side boxes to highlight
    for (var i=0; i<targets.length; i++) {

        var targetEl = targets[i].getEl();

        targetEl.style.background = "#fefff0";
        targetEl.opacity = .3;
        targetEl.filter = "alpha(opacity=30)";
    }
};

block_class.prototype.endDrag = function() {
    // reset the linked element styles
    var s = this.getEl().style;
    s.opacity = 1;
    s.filter = "alpha(opacity=100)";
    this.resetTargets();
};


block_class.prototype.onDragDrop = function(e, id) {
    // get the drag and drop object that was targeted
    var oDD;

    if ("string" == typeof id) {
        oDD = YAHOO.util.DDM.getDDById(id);
    } else {
        oDD = YAHOO.util.DDM.getBestMatch(id);
    }

    var el = this.getEl();

    if (this.debug) {
        YAHOO.log("id="+id+" el="+e+" x="+YAHOO.util.Dom.getXY(this.getDragEl()));
    }
    //var collisions = this.find_collisions(e,id);

    this.move_block(id);
    //YAHOO.util.DDM.moveToEl(el, oDD.getEl());

    this.resetTargets();
};


block_class.prototype.find_target = function(column){
        var collisions = column.find_sub_collision(YAHOO.util.Region.getRegion(this.getDragEl()));

        //determine position
        var insertbefore = null;
        if(collisions.length == 0)
          return;

       insertbefore = column.blocks[collisions[0][0]];

        return insertbefore;
    };

block_class.prototype.resetTargets = function() {
        // reset the target styles
        var targets = YAHOO.util.DDM.getRelated(this, true);
        for (var i=0; i<targets.length; i++) {
            var targetEl = targets[i].getEl();
            targetEl.style.background = "";
            targetEl.opacity = 1;
            targetEl.filter = "alpha(opacity=100)";
        }
    };

block_class.prototype.move_block = function(columnid){
        if(this.debug)YAHOO.log("Dropped on "+columnid[0]);
        //var column = YAHOO.util.DDM.getDDById(columnid[0].);
        column = columnid[0];
        var inserttarget = this.find_target(column);

        if(this.debug && inserttarget != null)YAHOO.log("moving "+this.getEl().id+" before "+inserttarget.getEl().id+" - parentNode="+this.getEl().parentNode.id);

        if(this == inserttarget){
            if(this.debug)YAHOO.log("Dropping on self, resetting");
            this.endDrag();
            return;
        }

        //remove from document
        if(this.getEl().parentNode != null)
          this.getEl().parentNode.removeChild(this.getEl());

        //insert into correct place
        if(inserttarget != null ){
            inserttarget.getEl().parentNode.insertBefore(this.getEl(),inserttarget.getEl());

        }else if(column == main.rightcolumn){//if right side insert before admin block
            column.getEl().insertBefore(this.getEl(),main.adminBlock);

        }else{
            column.getEl().appendChild(this.getEl());
        }

        this.reset_regions();

        //remove block from current array
        if(main.rightcolumn.has_block(this))
              main.rightcolumn.remove_block(this);

        else if(main.leftcolumn.has_block(this))
              main.leftcolumn.remove_block(this);

        //insert into new array
        column.insert_block(this,inserttarget);

    };


block_class.prototype.reset_regions = function() {
    var blockcount = main.blocks.length;
    for (i=0; i<blockcount; i++) {
        main.blocks[i].region = YAHOO.util.Region.getRegion(main.blocks[i].getEl());
    }
};


block_class.prototype.init_buttons = function() {
    var viewbutton = main.mk_button('a', main.portal.icons['hide'], main.portal.strings['hide'], [['class', 'icon hide']]);
    YAHOO.util.Event.addListener(viewbutton, 'click', this.toggle_hide, this, true);

    var deletebutton = main.mk_button('a', main.portal.icons['delete'], main.portal.strings['delete'], [['class', 'icon delete']]);
    YAHOO.util.Event.addListener(deletebutton, 'click', this.delete_button, this, true);

    this.viewbutton = viewbutton;

    buttonCont = YAHOO.util.Dom.getElementsByClassName('commands', 'div', this.getEl())[0];

    if (buttonCont) {
        buttonCont.appendChild(viewbutton);
        buttonCont.appendChild(deletebutton);
    }
};


block_class.prototype.toggle_hide = function(e, target, isCosmetic) {
    var strhide = main.portal.strings['hide'];
    var strshow = main.portal.strings['show'];
    if (YAHOO.util.Dom.hasClass(this.getEl(), 'hidden')) {
        this.getEl().className = this.originalClass;
        this.viewbutton.childNodes[0].src = this.viewbutton.childNodes[0].src.replace(/show./i, 'hide.');
        this.viewbutton.childNodes[0].alt = this.viewbutton.childNodes[0].alt.replace(strshow, strhide);
        this.viewbutton.title = this.viewbutton.title.replace(strshow, strhide);

        if (!isCosmetic) {
            main.connect('POST', 'class=block&field=visible', null,
                    'value=1&instanceId='+this.instanceId);
        }
    } else {
        this.originalClass = this.getEl().className;
        this.getEl().className = "hidden block";
        this.viewbutton.childNodes[0].src = this.viewbutton.childNodes[0].src.replace(/hide./i,'show.');
        this.viewbutton.childNodes[0].alt = this.viewbutton.childNodes[0].alt.replace(strhide, strshow);
        this.viewbutton.title = this.viewbutton.title.replace(strhide, strshow);

        if (!isCosmetic) {
            main.connect('POST', 'class=block&field=visible', null,
                    'value=0&instanceId='+this.instanceId);
        }
    }
};


block_class.prototype.delete_button = function() {
    // Remove from local model.
    if (main.rightcolumn.has_block(this)) {
        main.rightcolumn.remove_block(this);
    } else if (main.leftcolumn.has_block(this)) {
        main.leftcolumn.remove_block(this);
    }
    // Remove block from the drag and drop group in YUI.
    this.removeFromGroup('blocks');

    // Remove from remote model.
    main.connect('POST', 'class=block&action=DELETE&instanceId='+this.instanceId);

    // Remove from view
    main.blocks.splice(main.get_block_index(this), 1);
    this.getEl().parentNode.removeChild(this.getEl());

    if (this.debug) {
        YAHOO.log("Deleting "+this.getEl().id);
    }
};


block_class.prototype.updatePosition = function(index, columnId) {
    //update the db for the position
    main.connectQueue_add('POST', 'class=block&field=position', null,
            'value='+index+'&column='+columnId+'&instanceId='+this.instanceId);

    if (this.debug) {
        YAHOO.log("Updating position of "+this.getEl().id+" to index "+index+" on column "+columnId);
    }
};


/*
 * column class, DD targets
 */

function column_class(id,group,config,ident){
    this.init_column(id,group,config,ident);
}
YAHOO.extend(column_class, YAHOO.util.DDTarget);

column_class.prototype.debug = false;

column_class.prototype.init_column = function(id, group,config,ident){
        if (!id) { return; }

        this.initTarget(id,group,config);
        this.blocks = new Array();
        this.ident = ident;

//      YAHOO.log("init_column "+id+"-"+el.id);
        this.region = YAHOO.util.Region.getRegion(id);

    };


column_class.prototype.find_sub_collision = function(dragRegion){
        if(this.debug)YAHOO.log("Finding Collisions on "+this.getEl().id+" with "+this.blocks.length+" blocks");
        //find collisions with sub_elements(blocks), return array of collisions with regions of collision
        var collisions = new Array();
        for(i=0;i<this.blocks.length;i++){
            if(this.debug)YAHOO.log("testing region "+this.blocks[i].region+" against" + dragRegion + "intersect ="+this.blocks[i].region.intersect(dragRegion));
            var intersect = this.blocks[i].region.intersect(dragRegion);
            if(intersect != null){
                index = collisions.length;
                collisions[index] = new Array();
                collisions[index][0] = i;
                collisions[index][1] = this.blocks[i].region.intersect(dragRegion);
                if(this.debug)YAHOO.log(index+" Collides with "+this.blocks[i].getEl().id+" area" + collisions[index][1].getArea());
            }
        }
      return collisions;
    };

column_class.prototype.add_block = function(el){
       this.blocks[this.blocks.length] = el;
     };

column_class.prototype.insert_block = function(el,targetel){
        var blockcount = this.blocks.length;
        var found = -1;
        var tempStore = nextStore = null;
        for(var i=0;i<blockcount;i++){
            if(found > 0){
                tempStore = this.blocks[i];
                this.blocks[i] = nextStore;
                nextStore = tempStore;

            }else if(this.blocks[i] == targetel){
                found = i;
                nextStore = this.blocks[i];
                this.blocks[i] = el;
                blockcount++;
            }
        }

        if(found<0){//insert at end
            found = this.blocks.length;
            this.add_block(el);

        }

        el.updatePosition(found,this.ident);
    };

column_class.prototype.has_block = function(el){
        var blockcount = this.blocks.length;
        for(var i=0;i<blockcount;i++)
            if(this.blocks[i]==el)
                 return true;
        return false;
    };


column_class.prototype.remove_block = function(el){
        var blockcount = this.blocks.length;
        var found = false;
        for(var i=0;i<blockcount;i++){
            if(this.blocks[i]==el || found){
               if(!found)
                    found = true;

               if(i < blockcount-1){
                   this.blocks[i] = this.blocks[i+1];
               }else{
                    this.blocks.pop();
               }
            }
        }
        YAHOO.log("column "+this.indent+" has "+blockcount+"blocks");
    };



