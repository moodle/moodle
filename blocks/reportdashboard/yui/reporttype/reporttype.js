YUI.add('moodle-block_reportdashboard-reporttype', function (Y) {
    
    M.block_reportdashboard = M.block_reportdashboard || {};

    M.block_reportdashboard.reporttype = {
        init: function (params) {
            if (M.cfg.developerdebug) {
                var basic = new Y.Console({logSource: Y.Global});
                basic.render();
            }
            // Find the node.
            var selectid = Y.one('#' + params.selectid);
            var containerid = Y.one('#' + params.reportcontainer);
            if (containerid) {
                // Set the to execute 2 seconds after page is being loaded.
                selectid.on('change', function () {
                    var ioconfig = {
                        method: "POST",
                        sync: false,
                        timeout: 10000,
                        data: {'reportid': params.reportid, 'selreport': selectid.get('value'), 'blockinstanceid': params.block_instanceid},
                        on: {
                            success: function (id, o) {
                                try {
                                    // We got some valid response. Let's add it to the block.
                                    containerid.setHTML(o.responseText);
                                } catch (err) {
                                    Y.log(err.message);
                                }
                            },
                            failure: function (id, o) {
                                try {
                                    containerid.setHTML(o.response);
                                } catch (err) {
                                    Y.log(err.message);
                                }
                            }
                        }
                    };
                    Y.io(M.cfg.wwwroot + '/blocks/reportdashboard/action.php', ioconfig);
                }, [], false);
            } else {
                Y.log('Unable to find tag!');
            }
        } // end init
    }; // end module

}, '@VERSION@', {requires: ['node', 'io-base', 'querystring-stringify-simple', 'console']}
);