<script type="text/javascript">
//<![CDATA[

// If the mouse is clicked outside this element, the edit is CANCELLED (even if the mouse clicks another grade/feedback cell)
// If ctrl-arrow is used, or if [tab] or [enter] are pressed, the edit is RECORDED and the row is updated. The previous element returns to normal

YAHOO.namespace("grade_edit_weights");
YAHOO.grade_edit_weights.courseid = <?php echo $COURSE->id; ?>;
YAHOO.grade_edit_weights.wwwroot = '<?php echo $CFG->wwwroot; ?>';
YAHOO.grade_edit_weights.tree = null;
YAHOO.grade_edit_weights.treedata = <?php echo $tree_json; ?>;

YAHOO.grade_edit_weights.buildTreeNode = function(element, parentNode) {
    var gew = YAHOO.grade_edit_weights;

    if (parentNode === undefined) {
        parentNode = gew.tree.getRoot();
    }

    if (element === undefined) {
        element = gew.treedata;
    }

    if (element.item.table == 'grade_categories') {
        var tmpNode = new YAHOO.widget.TextNode(element.item.name, parentNode, parentNode.isRoot());
        for (var i = 0; i < element.children.length; i++) {
            gew.buildTreeNode(element.children[i], tmpNode, false);
        }
    } else if (element.item.itemtype == 'mod') {
        var tmpNode = new YAHOO.widget.TextNode(element.item.name, parentNode, false);
    }
};

YAHOO.grade_edit_weights.init = function() {
    var gew = YAHOO.grade_edit_weights;
    var div = document.getElementById('weightstree');
    gew.tree = new YAHOO.widget.TreeView('weightstree');

     //handler for expanding all nodes
     YAHOO.util.Event.on("expand", "click", function(e) {
         YAHOO.log("Expanding all TreeView  nodes.", "info", "example");
         gew.tree.expandAll();
         YAHOO.util.Event.preventDefault(e);
     });

     //handler for collapsing all nodes
     YAHOO.util.Event.on("collapse", "click", function(e) {
         YAHOO.log("Collapsing all TreeView  nodes.", "info", "example");
         gew.tree.collapseAll();
         YAHOO.util.Event.preventDefault(e);
     });

     gew.buildTreeNode();
     gew.tree.draw();
};

YAHOO.util.Event.onDOMReady(YAHOO.grade_edit_weights.init);

// ]]>
</script>
