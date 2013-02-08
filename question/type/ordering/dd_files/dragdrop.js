/**********************************************************
 Adapted from the sortable lists example by Tim Taylor
 http://tool-man.org/examples/sorting.html
 Modified by Tom Westcott : http://www.cyberdummy.co.uk 
 **********************************************************/

var DragDrop = {
	firstContainer : null,
	lastContainer : null,
        parent_id : null,
        parent_group : null,
	makeListContainer : function(list, group) {
		// each container becomes a linked list node
		if (this.firstContainer == null) {
			this.firstContainer = this.lastContainer = list;
			list.previousContainer = null;
			list.nextContainer = null;
		} else {
			list.previousContainer = this.lastContainer;
			list.nextContainer = null;
			this.lastContainer.nextContainer = list;
			this.lastContainer = list;
		}
		
		// these functions are called when an item is draged over
		// a container or out of a container bounds.  onDragOut
		// is also called when the drag ends with an item having
		// been added to the container
		list.onDragOver = new Function();
		list.onDragOut = new Function();
                list.onDragDrop = new Function();
                list.group = group;
		
    	var items = list.getElementsByTagName( "li" );
    	
		for (var i = 0; i < items.length; i++) {
			DragDrop.makeItemDragable(items[i]);
		}
	},
        
        serData : function ( group, theid ) {
                var container = DragDrop.firstContainer;
		var j = 0;
                var string = "";
                
                while (container != null) {
                
                        if(theid != null && container.id != theid)
                        {
                          container = container.nextContainer;
                          continue;
                        }

                        if(group != null && container.group != group)
                        {
                          container = container.nextContainer;
                          continue;
                        }
                
                        j ++;
                        if(j > 1)
                        {
                          string += ":";
                        }
                        string += container.id;
                        
                        var items = container.getElementsByTagName( "li" );
    	                string += "(";
		        for (var i = 0; i < items.length; i++) {
                            if(i > 0)
                            {
                              string += ",";
                            }
			    string += items[i].id;
		        }
                        string += ")";
                        
			container = container.nextContainer;
		}
                return string;   
        },

	makeItemDragable : function(item) {
		Drag.makeDraggable(item);
		item.setDragThreshold(5);
		
		// tracks if the item is currently outside all containers
		item.isOutside = false;
		
		item.onDragStart = DragDrop.onDragStart;
		item.onDrag = DragDrop.onDrag;
		item.onDragEnd = DragDrop.onDragEnd;
	},

	onDragStart : function(nwPosition, sePosition, nwOffset, seOffset) {
		// update all container bounds, since they may have changed
		// on a previous drag
		//
		// could be more smart about when to do this
		var container = DragDrop.firstContainer;
		while (container != null) {
			container.northwest = Coordinates.northwestOffset( container, true );
			container.southeast = Coordinates.southeastOffset( container, true );
			container = container.nextContainer;
		}
		
		// item starts out over current parent
		this.parentNode.onDragOver();
                parent_id = this.parentNode.id;
                parent_group = this.parentNode.group;
	},

	onDrag : function(nwPosition, sePosition, nwOffset, seOffset) {
		// check if we were nowhere
		if (this.isOutside) {
			// check each container to see if in its bounds
			var container = DragDrop.firstContainer;
			while (container != null) {

				if ((nwOffset.inside( container.northwest, container.southeast ) ||
					seOffset.inside( container.northwest, container.southeast )) && container.group == parent_group) {
					// we're inside this one
					container.onDragOver();
					this.isOutside = false;
					
					// since isOutside was true, the current parent is a
					// temporary clone of some previous container node and
					// it needs to be removed from the document
					var tempParent = this.parentNode;
					tempParent.removeChild( this );
					container.appendChild( this );
					tempParent.parentNode.removeChild( tempParent );
					break;
				}
				container = container.nextContainer;
			}
			// we're still not inside the bounds of any container
			if (this.isOutside)
				return;
		
		// check if we're outside our parent's bounds
		} else if (!(nwOffset.inside( this.parentNode.northwest, this.parentNode.southeast ) ||
			seOffset.inside( this.parentNode.northwest, this.parentNode.southeast ))) {
			
			this.parentNode.onDragOut();
			this.isOutside = true;
			
			// check if we're inside a new container's bounds
			var container = DragDrop.firstContainer;
			while (container != null) {
				if ((nwOffset.inside( container.northwest, container.southeast ) ||
					seOffset.inside( container.northwest, container.southeast )) && container.group == parent_group) {
					// we're inside this one
					container.onDragOver();
					this.isOutside = false;
					this.parentNode.removeChild( this );
					container.appendChild( this );
					break;
				}
				container = container.nextContainer;
			}
			// if we're not in any container now, make a temporary clone of
			// the previous container node and add it to the document
			if (this.isOutside) {
				var tempParent = this.parentNode.cloneNode( false );
				this.parentNode.removeChild( this );
				tempParent.appendChild( this );
				// body puts a border or item at bottom of page if do not have this
                                tempParent.style.border = 0;
				document.getElementsByTagName( "body" ).item(0).appendChild( tempParent );
				return;
			}
		}
		
		// if we get here, we're inside some container bounds, so we do
		// everything the original dragsort script did to swap us into the
		// correct position
		
		var parent = this.parentNode;
				
		var item = this;
		var next = DragUtils.nextItem(item);
		while (next != null && this.offsetTop >= next.offsetTop - 2) {
			var item = next;
			var next = DragUtils.nextItem(item);
		}
		if (this != item) {
			DragUtils.swap(this, next);
			return;
		}

		var item = this;
		var previous = DragUtils.previousItem(item);
		while (previous != null && this.offsetTop <= previous.offsetTop + 2) {
			var item = previous;
			var previous = DragUtils.previousItem(item);
		}
		if (this != item) {
			DragUtils.swap(this, item);
			return;
		}
	},

	onDragEnd : function(nwPosition, sePosition, nwOffset, seOffset) {
		// if the drag ends and we're still outside all containers
		// it's time to remove ourselves from the document or add 
                // to the trash bin
		if (this.isOutside) {
                        var container = DragDrop.firstContainer;
                        while (container != null) {
                           if(container.id == parent_id)
                           {
                             break;
                           }
                           container = container.nextContainer;
                        }
			this.isOutside = false;
			this.parentNode.removeChild( this );
			container.appendChild( this );
                        this.style["top"] = "0px";
		        this.style["left"] = "0px";
                        //var container = DragDrop.firstContainer;
                        //container.appendChild( this );
			return;
		}
		this.parentNode.onDragOut();
                this.parentNode.onDragDrop();
      		this.style["top"] = "0px";
		this.style["left"] = "0px";
	}
};

var DragUtils = {
	swap : function(item1, item2) {
		var parent = item1.parentNode;
		parent.removeChild(item1);
		parent.insertBefore(item1, item2);

		item1.style["top"] = "0px";
		item1.style["left"] = "0px";
	},

	nextItem : function(item) {
		var sibling = item.nextSibling;
		while (sibling != null) {
			if (sibling.nodeName == item.nodeName) return sibling;
			sibling = sibling.nextSibling;
		}
		return null;
	},

	previousItem : function(item) {
		var sibling = item.previousSibling;
		while (sibling != null) {
			if (sibling.nodeName == item.nodeName) return sibling;
			sibling = sibling.previousSibling;
		}
		return null;
	}		
};
