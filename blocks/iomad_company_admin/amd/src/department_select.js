
define(['jquery', 'core/tree'], function($, Tree) {
    return {
        // selectname: identify the 'select' form item by name
        // selected: the id of the menu item to initially show bold
        // submitform: 1/0 optionaly submit the form on click
        init: function(selectname, submitform, selected) {

            // instantiate the tree container
            var tree = new Tree("#department_tree");

            // Hide the original select element
            //$("#id_" + selectname).hide();
            $("select[name=" + selectname + "]").parent().hide();

            // set the initial selected
            tree.setActiveItem($("span[data-id="+selected+"]"));

            // get the data and set form
            $(".tree_dept_name").click(function(e) {
                if (e.which) {
                    var id = $(this).attr("data-id");
                    $("select[name=" + selectname + "]" + " option[value=" + id + "]").prop("selected", true);

                    // if there's a form to submit...
                    if (submitform) {
                        $("select[name=" + selectname + "]").closest("form").submit();
                    }
                }
            });
        }
    };
});
