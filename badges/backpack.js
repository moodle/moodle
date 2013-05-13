/**
 * Push badges to backpack.
 */
function addtobackpack(event, args) {
    OpenBadges.issue([args.assertion], function(errors, successes) { });
}

/**
 * Check if website is externally accessible from the backpack.
 */
function check_site_access() {
    var add = Y.one('#check_connection');

    var callback = {
            method: "GET",
            on: {
                success: function(id, o, args) {
                            var data = Y.JSON.parse(o.responseText);
                            if (data.code == 'http-unreachable') {
                                add.setHTML(data.response);
                                add.removeClass('hide');
                            }
                        },
                failure: function(o) { }
            }
        };

    Y.use('io-base', function(Y) {
        Y.io('ajax.php', callback);
    });

    return false;
}