var row = null;
var delall_cb = {
    success: function(o){
        try {
            var resp = YAHOO.lang.JSON.parse(o.responseText);
        } catch(e) {
            alert(mstr.report_spamcleaner.spaminvalidresult);
            return;
        }
        if(resp == true){
            window.location.href=window.location.href;
        }
    }
}
function init() {
    YAHOO.util.Event.addListener("removeall_btn", "click", function(){
        var yes = confirm(mstr.report_spamcleaner.spamdeleteallconfirm);
        if(yes){
            var cObj = YAHOO.util.Connect.asyncRequest('POST', spamcleaner.me+'?delall=yes&sesskey='+M.cfg.sesskey, delall_cb);
        }
    });
}
var del_cb = {
    success: function(o) {
        try {
            var resp = YAHOO.lang.JSON.parse(o.responseText);
        } catch(e) {
            alert(mstr.report_spamcleaner.spaminvalidresult);
            return;
        }
        if(row) {
            if(resp == true){
                while(row.tagName != 'TR') {
                    row = row.parentNode;
                }
                row.parentNode.removeChild(row);
                row = null;
            } else {
                alert(mstr.report_spamcleaner.spamcannotdelete);
            }
        }
    }
}
var ignore_cb = {
    success: function(o){
        try {
            var resp = YAHOO.lang.JSON.parse(o.responseText);
        } catch(e) {
            alert(mstr.report_spamcleaner.spaminvalidresult);
            return;
        }
        if(row) {
            if(resp == true){
                while(row.tagName != 'TR') {
                    row = row.parentNode;
                }
                row.parentNode.removeChild(row);
                row = null;
            }
        }
    }
}
function del_user(obj, id) {
    var yes = confirm(mstr.report_spamcleaner.spamdeleteconfirm);
    if(yes){
        row = obj;
        var cObj = YAHOO.util.Connect.asyncRequest('POST', spamcleaner.me+'?del=yes&sesskey='+M.cfg.sesskey+'&id='+id, del_cb);
    }
}
function ignore_user(obj, id) {
    row = obj;
    var cObj = YAHOO.util.Connect.asyncRequest('POST', spamcleaner.me+'?ignore=yes&sesskey='+M.cfg.sesskey+'&id='+id, ignore_cb);
}
YAHOO.util.Event.onDOMReady(init);