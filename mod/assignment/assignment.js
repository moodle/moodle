var assignment = {};

function setNext(){
    document.getElementById('submitform').mode.value = 'next';
    document.getElementById('submitform').userid.value = assignment.nextid;
}

function saveNext(){
    document.getElementById('submitform').mode.value = 'saveandnext';
    document.getElementById('submitform').userid.value = assignment.nextid;
    document.getElementById('submitform').saveuserid.value = assignment.userid;
    document.getElementById('submitform').menuindex.value = document.getElementById('submitform').grade.selectedIndex;
}

function initNext(nextid, usserid) {
	assignment.nextid = nextid;
	assignment.userid = userid;
}

M.mod_assignment = {};

M.mod_assignment.init_tree = function(Y, expand_all, htmlid) {
    Y.use('yui2-treeview', function(Y) {
        var tree = new Y.YUI2.widget.TreeView(htmlid);

        tree.subscribe("clickEvent", function(node, event) {
            // we want normal clicking which redirects to url
            return false;
        });

        if (expand_all) {
            tree.expandAll();
        }

        tree.render();
    });
};

M.mod_assignment.init_grade_change = function(Y) {
    var gradenode = Y.one('#id_grade');
    if (gradenode) {
        var originalvalue = gradenode.get('value');
        gradenode.on('change', function() {
            if (gradenode.get('value') != originalvalue) {
                alert(M.str.mod_assignment.changegradewarning);
            }
        });
    }
};
