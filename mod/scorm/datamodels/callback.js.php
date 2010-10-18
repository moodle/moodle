
    this.connectPrereqCallback = {

        success: function(o) {
            scorm_tree_node = YAHOO.widget.TreeView.getTree('scorm_tree');
            if (o.responseText !== undefined) {
                    //alert('got a response: ' + o.responseText);
                    if (scorm_tree_node && o.responseText) {
                        var hnode = scorm_tree_node.getHighlightedNode();
                        var hidx = null;
                        if (hnode) {
                            hidx = hnode.index + scorm_tree_node.getNodeCount();
                        }
                        // all gone
                        var root_node = scorm_tree_node.getRoot();
                        while (root_node.children.length > 0) {
                            scorm_tree_node.removeNode(root_node.children[0]);
                        }
                    }
                    // make sure the temporary tree element is not there
                    var el_old_tree = document.getElementById('scormtree123');
                    if (el_old_tree) {
                        el_old_tree.parentNode.removeChild(el_old_tree);
                    }
                    var el_new_tree = document.createElement('div');
                    var pagecontent = document.getElementById("page-content");
                    el_new_tree.setAttribute('id','scormtree123');
                    el_new_tree.innerHTML = o.responseText;
                    // make sure it doesnt show
                    el_new_tree.style.display = 'none';
                    pagecontent.appendChild(el_new_tree)
                    // ignore the first level element as this is the title
                    var startNode = el_new_tree.firstChild.firstChild;
                    if (startNode.tagName == 'LI') {
                        // go back to the beginning
                        startNode = el_new_tree;
                    }
                    //var sXML = new XMLSerializer().serializeToString(startNode);
                    scorm_tree_node.buildTreeFromMarkup(startNode);
                    var el = document.getElementById('scormtree123');
                    el.parentNode.removeChild(el);
                    scorm_tree_node.expandAll();
                    scorm_tree_node.render();
                    if (hidx != null) {
                        hnode = scorm_tree_node.getNodeByIndex(hidx);
                        if (hnode) {
                            hnode.highlight();
                            scorm_layout_widget = YAHOO.widget.Layout.getLayoutById('scorm_layout');
                            var left = scorm_layout_widget.getUnitByPosition('left');
                            if (left.expanded) {
                                hnode.focus();
                            }
                        }
                    }
                }
        },

        failure: function(o) {
            // do some sort of error handling
            var sURL = "<?php echo $CFG->wwwroot; ?>" + "/mod/scorm/prereqs.php?a=<?php echo $scorm->id ?>&scoid=<?php echo $scoid ?>&attempt=<?php echo $attempt ?>&mode=<?php echo $mode ?>&currentorg=<?php echo $currentorg ?>&sesskey=<?php echo sesskey(); ?>";
            //TODO: Enable this error handing correctly - avoiding issues when closing player MDL-23470 
            //alert('Prerequisites update failed - must restart SCORM player');
            //window.location.href = sURL;
        }

    };


