
define(['jquery', 'core/tree'], function($, Tree) {
    return {
        init: function() {

            // instantiate the tree container
            new Tree("#department_tree");

            $("#id_deptid").change(function() {
                alert("clicked again2");
            });
        }
    };
});
