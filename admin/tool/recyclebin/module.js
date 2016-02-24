// Added a confirmation dialogue when the empty recycle bin
// button has been selected.
M.local_recyclebin = {
    init: function (Y) {
        // Confirmation dialogue function.
        function confirmDialogue(e, str, callback) {
            // Prevent the button from immediately performing its action.
            e.preventDefault();

            // Add a confirm dialogue box.
            YUI().use('moodle-core-notification-confirm', function(Y) {
                var confirm = new M.core.confirm({
                    question: str,
                    center: true,
                    modal: true,
                });

                // Perform the button's action if "Yes" is selected.
                confirm.on('complete-yes', callback, this);

                // Render the confirm dialogue.
                confirm.render().show();
            });
        }

        // Perform this action when any "Delete" button/link is clicked.
        Y.all('.recycle-bin-delete').each(function(node) {
            node.on('click', function(e) {
                // Get some strings from the Recycle bin lang file.
                var str = M.util.get_string('deleteconfirm', 'local_recyclebin');

                // Get the URL that leads to emptying the recycle bin.
                var urldelete = this.get('href');

                // Show the dialogue.
                confirmDialogue(e, str, function() {
                    window.location = urldelete;
                });
            });
        });

        // Find the "Delete All" button and perform an action when it is clicked.
        Y.one('.recycle-bin-delete-all input').on('click', function(e) {
            // Get some strings from the Recycle bin lang file.
            var str = M.util.get_string('emptyconfirm', 'local_recyclebin');

            // Show the dialogue.
            confirmDialogue(e, str, function() {
                Y.one('.recycle-bin-delete-all form').submit();
            });
        });
    }
};
