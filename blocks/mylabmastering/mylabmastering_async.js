/**
 * JavaScript library for MyLabMastering Block
 */

M.block_mylabmastering = {};

M.block_mylabmastering.init = function(Y, courseId, userId, localCode) {
    Y.use('io', function(Y) {
        var uri = '../blocks/mylabmastering/mylabmastering_ajax.php?course_id=' + courseId + '&user_id=' + userId;

        function complete(id, o, args) {
            var data = o.responseText;
            console.log(data);

            if (data !== '' && data.indexOf("code") > 0 && data.indexOf("no update") < 0 && data.indexOf("error") && data.indexOf("block_mylabmastering") > 0) {
                if (localCode !== 'unmapped') {
                    // Reload the page
                    window.location.reload();
                }
                else {
                    // A new link was created. Set the text value on the page.
                    // A reload is not necessary here as there are no links to remove.
                    Y.use('json-parse', function(Y) {
                        try {
                            // Update the text
                            var mapping = Y.JSON.parse(data);
                            Y.one('#block_mylabmastering_tree').setHTML(mapping.description);
                        } catch (e) {
                            console.log("An error occurred reading the JSON response. " + e.message);
                        }
                    });

                }
            }
        };

        Y.on('io:complete', complete, Y, null);

        var request = Y.io(uri);
    });
};