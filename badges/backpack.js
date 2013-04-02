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
        success: function(o) {
            var data = Y.JSON.parse(o.responseText);
            if (data.code == 'http-unreachable') {
                add.setHTML(data.response);
                add.removeClass('hide');
            }
        },
        failure: function(o) { }
    };

    YUI().use('yui2-connection', function (Y) {
        Y.YUI2.util.Connect.asyncRequest('GET', 'ajax.php', callback, null);
    });

    return false;
}