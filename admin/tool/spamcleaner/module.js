M.tool_spamcleaner = {
    Y: null,
    row: null,
    me: null,

    del_all: function() {
        var context = M.tool_spamcleaner;

        var yes = confirm(M.str.tool_spamcleaner.spamdeleteallconfirm);
        if (yes) {
            var cfg = {
                method: "POST",
                on: {
                    success : function(id, o, args) {
                        try {
                            var resp = context.Y.JSON.parse(o.responseText);
                        } catch(e) {
                            alert(M.str.tool_spamcleaner.spaminvalidresult);
                            return;
                        }
                        if (resp == true) {
                            window.location.href=window.location.href;
                        }
                    }
                }
            };
            context.Y.io(context.me+'?delall=yes&sesskey='+M.cfg.sesskey, cfg);
        }
    },

    del_user: function(obj, id) {
        var context = M.tool_spamcleaner;

        if (context.Y == null) {
            // not initialised yet
            return;
        }

        var yes = confirm(M.str.tool_spamcleaner.spamdeleteconfirm);
        if (yes) {
            context.row = obj;
            var cfg = {
                method: "POST",
                on: {
                    success : function(id, o, args) {
                        try {
                            var resp = context.Y.JSON.parse(o.responseText);
                        } catch(e) {
                            alert(M.str.tool_spamcleaner.spaminvalidresult);
                            return;
                        }
                        if (context.row) {
                            if (resp == true) {
                                while(context.row.tagName != 'TR') {
                                    context.row = context.row.parentNode;
                                }
                                context.row.parentNode.removeChild(context.row);
                                context.row = null;
                            } else {
                                alert(M.str.tool_spamcleaner.spamcannotdelete);
                            }
                        }
                    }
                }
            }
            context.Y.io(context.me+'?del=yes&sesskey='+M.cfg.sesskey+'&id='+id, cfg);
        }
    },

    ignore_user: function(obj, id) {
        var context = M.tool_spamcleaner;

        if (context.Y == null) {
            // not initilised yet
            return;
        }

        context.row = obj;
        var cfg = {
            method: "POST",
            on: {
                success : function(id, o, args) {
                    try {
                        var resp = context.Y.JSON.parse(o.responseText);
                    } catch(e) {
                        alert(M.str.tool_spamcleaner.spaminvalidresult);
                        return;
                    }
                    if (context.row) {
                        if (resp == true){
                            while(context.row.tagName != 'TR') {
                                context.row = context.row.parentNode;
                            }
                            context.row.parentNode.removeChild(context.row);
                            context.row = null;
                        }
                    }
                }
            }
        }
        context.Y.io(context.me+'?ignore=yes&sesskey='+M.cfg.sesskey+'&id='+id, cfg);
    },

    init: function(Y, me) {
        var context = M.tool_spamcleaner;

        Y.use('json', 'io-base', function (Y) {
            context.Y = Y;
            context.me = me;
            if (Y.one("#removeall_btn")) {
                Y.on("click", context.del_all, "#removeall_btn");
            }
        });
    }
}
