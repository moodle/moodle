///////////////////////////////////////////////////////////////
// Table Editing Class
// Author:  Billy Cook (wcook@nuvox.net)
// Date:    2002-05-07
// Purpose: Provide methods to edit a table.  Only
//          works in Internet Explorer version 5.5
//          and above for now.
//

function tableEditor(docID, pntCell) {

   this.docID = docID;        // ID of editable portion of document
   this.pntCell = pntCell;    // TD contentarea is contained in if any
   this.tableCell = null;     // cell currently selected
   this.tableElem = null;     // table currently selected
   this.cellResizeObj = null; // object that user clicks on to resize cell
   this.cellWidth = null;     // selected cell's current width
   this.cellHeight = null;    // selected cell's current height
   this.cellX = null;         // x coord of selected cell's bottom right 
   this.cellY = null;         // y coord of selected cell's bottom right
   this.moveable = null;      // moveable div

   // define methods only once
   if (typeof(_tableEditor_prototype_called) == 'undefined') {
      _tableEditor_prototype_called = true;

      // public methods
      tableEditor.prototype.mergeDown = mergeDown;
      tableEditor.prototype.unMergeDown = unMergeDown;
      tableEditor.prototype.mergeRight = mergeRight;
      tableEditor.prototype.splitCell = splitCell;
      tableEditor.prototype.addCell = addCell;
      tableEditor.prototype.removeCell = removeCell;
      tableEditor.prototype.processRow = processRow;
      tableEditor.prototype.processColumn = processColumn;
      tableEditor.prototype.buildTable = buildTable;
      tableEditor.prototype.setTableElements = setTableElements;
      tableEditor.prototype.unSetTableElements = unSetTableElements;
      tableEditor.prototype.setDrag = setDrag;
      tableEditor.prototype.stopCellResize = stopCellResize;
      tableEditor.prototype.markCellResize = markCellResize;
      tableEditor.prototype.resizeCell = resizeCell;
      tableEditor.prototype.changePos = changePos;
      tableEditor.prototype.resizeColumn = resizeColumn;
      tableEditor.prototype.resizeRow = resizeRow;
      tableEditor.prototype.repositionArrows = repositionArrows;
      tableEditor.prototype.explore = explore;

      // private methods
      tableEditor.prototype.__addOrRemoveCols = __addOrRemoveCols;
      tableEditor.prototype.__findParentTable = __findParentTable;
      tableEditor.prototype.__hideArrows = __hideArrows;
      tableEditor.prototype.__showArrows = __showArrows;
      tableEditor.prototype.__resizeColumn = __resizeColumn;
   }

   // create divs for editing cell width and height
   document.body.innerHTML += ' <div id="rArrow" title="Drag to modify cell width." style="position:absolute; visibility:hidden; cursor: E-resize; z-index: 1" onmousedown="tEdit.markCellResize(this)" onmouseup="tEdit.stopCellResize(false)" ondragstart="handleDrag(0)"> <table border="0" cellpadding="0" cellspacing="0" width="7" height="7"> <tr><td bgcolor="#000000"></td></tr> </table> </div> <div id="dArrow" title="Drag to modify cell height." style="position:absolute; visibility:hidden; cursor: S-resize; z-index: 1" onmousedown="tEdit.markCellResize(this)" onmouseup="tEdit.stopCellResize(false)" ondragstart="handleDrag(0)"> <table border="0" cellpadding="0" cellspacing="0" width="7" height="7"> <tr><td bgcolor="#000000"></td></tr> </table> </div>';



   ////////////////////////////////////////////////////////////////
   //  method: setTableElements
   //    args: none
   // purpose: look to see if the cursor is inside a TD or TABLE and
   //          if so assign the TD to this.tableCell or the TABLE to
   //          this.tableElem
   //
   function setTableElements(){

      // stop resizing cell if already resizing one
      this.stopCellResize(true);
      this.tableCell = null;
      
      cursorPos=document.selection.createRange();

      if (document.selection.type == 'Text') {
         var elt = cursorPos.parentElement(); 
         while (elt) {
            if (elt.tagName == "TD") {
               break;
            }
            elt = elt.parentElement;
         }


         if (elt) {
            // don't select document area
            if (elt.id == this.docID)
               return;

            // don't select parent TD
            if (this.pntCell)
               if (this.pntCell == elt.id)
                  return;

            this.tableCell = elt;

            // set width and height as globals for 
            // resizing
            this.cellWidth = this.tableCell.offsetWidth;
            this.cellHeight = this.tableCell.offsetHeight;
            this.__showArrows();
         }
      } else {
         if (cursorPos.length == 1) {
            if (cursorPos.item(0).tagName == "TABLE") {
               this.tableElem = cursorPos.item(0);
               this.__hideArrows();
               this.tableCell = null;
            }
         }
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: unSetTableElements
   //    args: none
   // purpose: unset references to currently selected cell or table 
   //          
   function unSetTableElements(){

      this.tableCell = null;
      this.tableElem = null;
      return;
   }

   ////////////////////////////////////////////////////////////////
   //  method: mergeDown
   //    args: none
   // purpose: merge the currently selected cell with the one below it
   //
   function mergeDown() {
      if (!this.tableCell)
         return;
      
      if (!this.tableCell.parentNode.nextSibling) {
         alert("There is not a cell below this one to merge with.");
         return;
      }

      var topRowIndex = this.tableCell.parentNode.rowIndex;

      //               [  TD   ] [  TR    ] [  TBODY ] [                   TR                      ] [            TD                 ]
      var bottomCell = this.tableCell.parentNode.parentNode.childNodes[ topRowIndex + this.tableCell.rowSpan ].childNodes[ this.tableCell.cellIndex ];

      if (!bottomCell) {
         alert("There is not a cell below this one to merge with.");
         return;
      }

      // don't allow merging rows with different colspans
      if (this.tableCell.colSpan != bottomCell.colSpan) {
         alert("Can't merge cells with different colSpans."); 
         return;
      }

      // do the merge
      this.tableCell.innerHTML += bottomCell.innerHTML;
      this.tableCell.rowSpan += bottomCell.rowSpan;
      bottomCell.removeNode(true); 
      this.repositionArrows();
   }

   ////////////////////////////////////////////////////////////////
   //  method: unMergeDown
   //    args: none
   // purpose: merge the currently selected cell with the one below it
   //
   function unMergeDown() {
      if (!this.tableCell)
         return;
      
      if (this.tableCell.rowSpan <= 1) {
         alert("RowSpan is already set to 1.");
         return;
      }

      var topRowIndex = this.tableCell.parentNode.rowIndex;

      // add a cell to the beginning of the next row
      this.tableCell.parentNode.parentNode.childNodes[ topRowIndex + this.tableCell.rowSpan - 1 ].appendChild( document.createElement("TD") );

      this.tableCell.rowSpan -= 1;

   }

   ////////////////////////////////////////////////////////////////
   //  method: mergeRight
   //    args: none
   // purpose: merge the currently selected cell with the one to 
   //          the immediate right.  Won't allow user to merge cells
   //          with different rowspans.
   //
   function mergeRight() {
      if (!this.tableCell)
         return;
      if (!this.tableCell.nextSibling)
         return;

      // don't allow user to merge rows with different rowspans
      if (this.tableCell.rowSpan != this.tableCell.nextSibling.rowSpan) {
         alert("Can't merge cells with different rowSpans.");
         return;
      }

      this.tableCell.innerHTML += this.tableCell.nextSibling.innerHTML;
      this.tableCell.colSpan += this.tableCell.nextSibling.colSpan;
      this.tableCell.nextSibling.removeNode(true);
       

      this.repositionArrows();
      this.__hideArrows();
      this.tableCell = null;
   }

   ////////////////////////////////////////////////////////////////
   //  method: splitCell 
   //    args: none
   // purpose: split the currently selected cell back into two cells 
   //          it the cell has a colspan > 1.
   //
   function splitCell() {
      if (!this.tableCell)
         return;
      if (this.tableCell.colSpan < 2) {
         alert("Cell can't be divided.  Add another cell instead");
         return;
      }

      this.tableCell.colSpan = this.tableCell.colSpan - 1;
      var newCell = this.tableCell.parentNode.insertBefore( document.createElement("TD"), this.tableCell);
      newCell.rowSpan = this.tableCell.rowSpan;
      this.repositionArrows();
   }

   ////////////////////////////////////////////////////////////////
   //  method: removeCell 
   //    args: none
   // purpose: remove the currently selected cell
   //
   function removeCell() {
      if (!this.tableCell)
         return;

      // can't remove all cells for a row
      if (!this.tableCell.previousSibling && !this.tableCell.nextSibling) {
         alert("You can't remove the only remaining cell in a row.");
         return;
      }

      this.tableCell.removeNode(false);

      this.repositionArrows();
      this.tableCell = null;
   } 
 
   ////////////////////////////////////////////////////////////////
   //  method: addCell 
   //    args: none
   // purpose: add a cell to the right of the selected cell
   //
   function addCell() {
      if (!this.tableCell)
         return;

      this.tableCell.parentElement.insertBefore(document.createElement("TD"), this.tableCell.nextSibling);
   }

   ////////////////////////////////////////////////////////////////
   //  method: processRow 
   //    args: (string)action = "add" or "remove"
   // purpose: add a row above the row that 
   //          contains the currently selected cell or
   //          remove the row containing the selected cell
   //
   function processRow(action) {
      if (!this.tableCell)
        return;

      // go back to TABLE def and keep track of cell index
      var idx = 0;
      var rowidx = -1;
      var tr = this.tableCell.parentNode;
      var numcells = tr.childNodes.length;
     

      while (tr) {
         if (tr.tagName == "TR")
            rowidx++;
         tr = tr.previousSibling;
      }
      // now we should have a row index indicating where the
      // row should be added / removed

      var tbl = this.__findParentTable(this.tableCell);
  
      if (!tbl) {
         alert("Could not " + action + " row.");
         return;
      }
     
      if (action == "add") {
         var r = tbl.insertRow(rowidx);
         for (var i = 0; i < numcells; i++) {
            var c = r.appendChild( document.createElement("TD") );
            if (this.tableCell.parentNode.childNodes[i].colSpan)
               c.colSpan = this.tableCell.parentNode.childNodes[i].colSpan;
         }
      } else {
         tbl.deleteRow(rowidx);
         this.stopCellResize(true);
         this.tableCell = null;
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: processColumn
   //    args: (string)action = "add" or "remove"
   // purpose: add a column to the right column containing
   //          the selected cell
   //
   function processColumn(action) {
      if (!this.tableCell)
        return;

      // store cell index in a var because the cell will be
      // deleted when processing the first row
      var cellidx = this.tableCell.cellIndex;
      
      var tbl = this.__findParentTable(this.tableCell);
  
      if (!tbl) {
         alert("Could not " + action + " column.");
         return;
      }
         
      // now we have the table containing the cell
      this.__addOrRemoveCols(tbl, cellidx, action);

      // clear out global this.tableCell value for remove
      if (action == 'remove') {
         this.stopCellResize(true);
         this.tableCell = null;
      } else {
         this.repositionArrows();
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: __addOrRemoveCols
   //    args: (table object)tbl, (int)cellidx, (string)action
   //          tbl = the table containing the selected cell
   //          cellidx = the index of the selected cell in its row
   //          action = "add" or "remove" the column
   //
   // purpose: add or remove the column at the cell index
   //
   function __addOrRemoveCols(tbl, cellidx, action) {
      if (!tbl.childNodes.length)
         return;
      var i;
      for (i = 0; i < tbl.childNodes.length; i++) {
         if (tbl.childNodes[i].tagName == "TR") {
            var cell = tbl.childNodes[i].childNodes[ cellidx ];
            if (!cell)
               break; // can't add cell after cell that doesn't exist
            if (action == "add") {
               cell.insertAdjacentElement("AfterEnd",  document.createElement("TD") );
            } else {
               // don't delete too many cells because or a rowspan setting
                 
               if (cell.rowSpan > 1) {
                  i += (cell.rowSpan - 1);
               }
               cell.removeNode(true);
            }
         } else {
            // keep looking for a "TR"
            this.__addOrRemoveCols(tbl.childNodes[i], cellidx, action); 
         }
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: __findParentTable 
   //    args: (TD object)cell
   //          cell = the selected cell object
   //
   // purpose: locate the table object that contains the
   //          cell object passed in
   //
   function __findParentTable(cell) {
      var tbl = cell.parentElement
      while (tbl) {
         if (tbl.tagName == "TABLE") {
            return tbl;
         }
         tbl = tbl.parentElement;
      }
      return false;
   }

   ////////////////////////////////////////////////////////////////
   //  method: exploreTree 
   //    args: (obj)obj, (obj)pnt
   //          obj = object to explore
   //          pnt = object to append output to
   //
   // purpose: traverse the dom tree printing out all properties
   //          of the object, its children.....recursive.  helpful
   //          when looking for object properties.
   //
   function exploreTree(obj, pnt) {
      if (!obj.childNodes.length)
         return;
      var i;
      var ul = pnt.appendChild( document.createElement("UL") );
      for (i = 0; i < obj.childNodes.length; i++) {
         var li = document.createElement("LI");
         explore(obj.childNodes[i], li);
         ul.appendChild(li);
         exploreTree(obj.childNodes[i], li); 
         /*
         var n = document.createTextNode(obj.childNodes[i].tagName);
         li.appendChild(n);
         */
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: explore
   //    args: (obj)obj, (obj)pnt
   //          obj = object to explore
   //          pnt = object to append output to
   //
   // purpose: show all properties for the object "obj"
   //
   function explore(obj, pnt) {
      var i;
      for (i in obj) {
         var n = document.createTextNode(i +"="+obj[i]);
         pnt.appendChild(n);
         pnt.appendChild( document.createElement("BR") );
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: buildTable 
   //    args: pnt = parent to append table to
   //
   // purpose: build a test table for debugging
   //
   function buildTable(pnt) {
      var t = pnt.appendChild( document.createElement("TABLE") );
      t.border=1;
      t.cellPadding=2;
      t.cellSpacing=0;
      var tb = t.appendChild( document.createElement("TBODY") );
      for(var r = 0; r < 10; r++) {
         var tr = tb.appendChild( document.createElement("TR") );
         for(var c = 0; c < 10; c++) {
            var cell = tr.appendChild( document.createElement("TD") );
            cell.appendChild( document.createTextNode(r+"-"+c) );
         }
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: setDrag
   //    args: obj = object (DIV) that is currently draggable
   //
   // purpose: set the object to be moved with the mouse
   //
   function setDrag(obj) {
     if (this.moveable) 
       this.moveable = null;
     else 
       this.moveable = obj; 
   }


   ////////////////////////////////////////////////////////////////
   //  method: changePos
   //    args: none
   //          mouse pointer appear inside the object set by "setDrag"
   //          function above.
   //
   // purpose: move the object selected in the "setDrag" function defined
   //          above.
   //
   function changePos() {
      if (!this.moveable) 
         return;

      this.moveable.style.posTop = event.clientY - 10;
      this.moveable.style.posLeft = event.clientX - 25;
   }


   ////////////////////////////////////////////////////////////////
   //  method: markCellResize
   //    args: (object)obj = the square table div object that
   //          was clicked on by the user to resize a cell
   //
   // purpose: store the object in "this.cellResizeObj" to be referenced
   //          in the "resizeCell" function.
   //          
   //
   function markCellResize(obj) {
      if (this.cellResizeObj) {
         this.cellResizeObj = null;
      } else {
         this.cellResizeObj = obj;
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: stopCellResize
   //    args: (bool)hideArrows
   //
   // purpose: stop changing cell width and height
   //
   function stopCellResize(hidearrows) {
      this.cellResizeObj = null;
      if (hidearrows)
         this.__hideArrows();
   }

   ////////////////////////////////////////////////////////////////
   //  method: __hideArrows()
   //    args: none
   //
   // purpose: hide editing tabs that are positioned in the selected
   //          cell
   //
   function __hideArrows() {
      document.getElementById("rArrow").style.visibility = 'hidden';
      document.getElementById("dArrow").style.visibility = 'hidden';
   }

   ////////////////////////////////////////////////////////////////
   //  method: __showArrows()
   //    args: none
   //
   // purpose: position editing tabs in the middle or the right cell
   //          wall and middle of the bottom wall to be used to drag
   //          the cell's width and height dimensions
   //
   function __showArrows() {
      if (!this.tableCell)
         return;

      var cell_hgt = this.tableCell.offsetTop;
      var cell_wdt = this.tableCell.offsetLeft;
      var par = this.tableCell.offsetParent;
      while (par) {
         cell_hgt = cell_hgt + par.offsetTop;
         cell_wdt = cell_wdt + par.offsetLeft;
         current_obj = par;
         par = current_obj.offsetParent;
      }
      this.cellX = cell_wdt + this.tableCell.offsetWidth; //bottom right X
      this.cellY = cell_hgt + this.tableCell.offsetHeight; // bottom right Y

      var scrollTop = document.getElementById(this.docID).scrollTop;
      var scrollLeft = document.getElementById(this.docID).scrollLeft;

      document.getElementById("rArrow").style.posLeft = cell_wdt + this.tableCell.offsetWidth - 6 - scrollLeft;
      document.getElementById("rArrow").style.posTop = cell_hgt + (this.tableCell.offsetHeight / 2) - 2 - scrollTop;

      document.getElementById("dArrow").style.posLeft = cell_wdt + (this.tableCell.offsetWidth / 2) - 2 - scrollLeft;
      document.getElementById("dArrow").style.posTop = cell_hgt + this.tableCell.offsetHeight - 6 - scrollTop;

      document.getElementById("rArrow").style.visibility = 'visible';
      document.getElementById("dArrow").style.visibility = 'visible';
   }

   ////////////////////////////////////////////////////////////////
   //  method: repositionArrows()
   //    args: none
   //
   // purpose: reposition editing tabs in the middle or the right cell
   //          wall and middle of the bottom wall to be used to drag
   //          the cell's width and height dimensions.  this must be
   //          run while changing the cell's dimensions.
   //
   function repositionArrows() {

      if (!this.tableCell)
         return;

      var cell_hgt = this.tableCell.offsetTop;
      var cell_wdt = this.tableCell.offsetLeft;
      var par = this.tableCell.offsetParent;
      while (par) {
         cell_hgt = cell_hgt + par.offsetTop;
         cell_wdt = cell_wdt + par.offsetLeft;
         current_obj = par;
         par = current_obj.offsetParent;
      }

      var scrollTop = document.getElementById(this.docID).scrollTop;
      var scrollLeft = document.getElementById(this.docID).scrollLeft;

      document.getElementById("rArrow").style.posLeft = cell_wdt + this.tableCell.offsetWidth - 6 - scrollLeft;
      document.getElementById("rArrow").style.posTop = cell_hgt + (this.tableCell.offsetHeight / 2) - 2 - scrollTop;

      document.getElementById("dArrow").style.posLeft = cell_wdt + (this.tableCell.offsetWidth / 2) - 2 - scrollLeft; 
      document.getElementById("dArrow").style.posTop = cell_hgt + this.tableCell.offsetHeight - 6 - scrollTop;
   }

   ////////////////////////////////////////////////////////////////
   //  method: resizeCell()
   //    args: none
   //
   // purpose: resize the selected cell based on the direction of the mouse
   //
   function resizeCell() {
      if (!this.cellResizeObj)
         return;

      if (this.cellResizeObj.id == 'dArrow') {
         var scrollTop = document.getElementById(this.docID).scrollTop;
         var newHeight = (event.clientY - (this.cellY - scrollTop) ) + this.cellHeight;

         if (newHeight > 0)
            // don't resize entire row if rowspan > 1
            if (this.tableCell.rowSpan > 1) 
               this.tableCell.style.height = newHeight;
            else 
               this.resizeRow(newHeight);

         this.repositionArrows();

      } else if (this.cellResizeObj.id == 'rArrow') {
         var scrollLeft = document.getElementById(this.docID).scrollLeft;
         var newWidth = (event.clientX - (this.cellX - scrollLeft) ) + this.cellWidth;

         if (newWidth > 0) 
            // don't resize entire column if colspan > 1
            if (this.tableCell.colSpan > 1)
               this.tableCell.style.width = newWidth;
            else
               this.resizeColumn(newWidth);

         this.repositionArrows();

      } else {
         // do nothing
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: resizeRow 
   //    args: (int)size
   // purpose: set cell.style.height on all cells in a row that
   //          have rowspan = 1 
   //
   function resizeRow(size) {
      if (!this.tableCell)
        return;

      // go back to TABLE def and keep track of cell index
      var idx = 0;
      var rowidx = -1;
      var tr = this.tableCell.parentNode;
      var numcells = tr.childNodes.length;

      while (tr) {
         if (tr.tagName == "TR")
            rowidx++;
         tr = tr.previousSibling;
      }
      // now we should have a row index indicating where the
      // row should be added / removed

      var tbl = this.__findParentTable(this.tableCell);
  
      if (!tbl) {
         return;
      }
     
      // resize cells in the row
      for (var j = 0; j < tbl.rows(rowidx).cells.length; j++) {
         if (tbl.rows(rowidx).cells(j).rowSpan == 1)
            tbl.rows(rowidx).cells(j).style.height = size;
      }
   }


   ////////////////////////////////////////////////////////////////
   //  method: resizeColumn 
   //    args: (int)size = size in pixels
   // purpose: set column width
   //
   function resizeColumn(size) {
      if (!this.tableCell)
        return;

      // store cell index in a var because the cell will be
      // deleted when processing the first row
      var cellidx = this.tableCell.cellIndex;
      
      var tbl = this.__findParentTable(this.tableCell);
  
      if (!tbl) {
         alert("Could not resize  column.");
         return;
      }
         
      // now we have the table containing the cell
      this.__resizeColumn(tbl, cellidx, size);
   }

   ////////////////////////////////////////////////////////////////
   //  method: __resizeColumn
   //    args: (table object)tbl, (int)cellidx, (int)size
   //          tbl = the table containing the selected cell
   //          cellidx = the index of the selected cell in its row
   //          size = size in pixels
   //
   // purpose: resize all cells in the a column
   //
   function __resizeColumn(tbl, cellidx, size) {
      if (!tbl.childNodes.length)
         return;
      var i;
      for (i = 0; i < tbl.childNodes.length; i++) {
         if (tbl.childNodes[i].tagName == "TR") {
            var cell = tbl.childNodes[i].childNodes[ cellidx ];
            if (!cell)
               break; // can't add cell after cell that doesn't exist

            if (cell.colSpan == 1)
               cell.style.width = size;
         } else {
            // keep looking for a "TR"
            this.__resizeColumn(tbl.childNodes[i], cellidx, size); 
         }
      }
   }
} 
