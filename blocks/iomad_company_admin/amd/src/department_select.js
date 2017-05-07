
define(['jquery', 'core/tree'], function($, Tree) {
    return {
        init: function(selectname) {

            // instantiate the tree container
            new Tree("#department_tree");

            // Hide the original select element
            $("#id_" + selectname).hide();

            // get the data and set form
            $(".tree_dept_name").click(function(e) {
                if (e.which) {
                    var id = $(this).attr("data-id");
                    $("#id_" + selectname + " option[value=" + id + "]").prop("selected", true);
                }
            });
        }
    };
});
