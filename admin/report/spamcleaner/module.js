M.report_spamcleaner = {
	Y: null,
	row: null,
	me: null,

	del_all: function() {
        var yes = confirm(mstr.report_spamcleaner.spamdeleteallconfirm);
        if (yes) {
			var cfg = {
				method: "POST",
				on: {
					success : function(id, o, args) {
				        try {
				            var resp = M.report_spamcleaner.Y.JSON.parse(o.responseText);
				        } catch(e) {
				            alert(mstr.report_spamcleaner.spaminvalidresult);
				            return;
				        }
				        if (resp == true) {
				            window.location.href=window.location.href;
				        }
					}
				}
			}
			M.report_spamcleaner.Y.io(M.report_spamcleaner.me+'?delall=yes&sesskey='+M.cfg.sesskey, cfg);
        }
	},
	
	del_user: function(obj, id) {
	    var yes = confirm(mstr.report_spamcleaner.spamdeleteconfirm);
	    if (yes) {
	    	M.report_spamcleaner.row = obj;
			var cfg = {
				method: "POST",
				on: {
					success : function(id, o, args) {
				        try {
				        	var resp = M.report_spamcleaner.Y.JSON.parse(o.responseText);
				        } catch(e) {
				            alert(mstr.report_spamcleaner.spaminvalidresult);
				            return;
				        }
				        if (M.report_spamcleaner.row) {
				            if (resp == true) {
				                while(M.report_spamcleaner.row.tagName != 'TR') {
				                	M.report_spamcleaner.row = M.report_spamcleaner.row.parentNode;
				                }
				                M.report_spamcleaner.row.parentNode.removeChild(M.report_spamcleaner.row);
				                M.report_spamcleaner.row = null;
				            } else {
				                alert(mstr.report_spamcleaner.spamcannotdelete);
				            }
				        }
					}
				}
			}
			M.report_spamcleaner.Y.io(M.report_spamcleaner.me+'?del=yes&sesskey='+M.cfg.sesskey+'&id='+id, cfg);
	    }
	},

	ignore_user: function(obj, id) {
		M.report_spamcleaner.row = obj;
		var cfg = {
			method: "POST",
			on: {
				success : function(id, o, args) {
			        try {
			            var resp = M.report_spamcleaner.Y.JSON.parse(o.responseText);
			        } catch(e) {
			            alert(mstr.report_spamcleaner.spaminvalidresult);
			            return;
			        }
			        if (M.report_spamcleaner.row) {
			            if (resp == true){
			                while(M.report_spamcleaner.row.tagName != 'TR') {
			                	M.report_spamcleaner.row = M.report_spamcleaner.row.parentNode;
			                }
			                M.report_spamcleaner.row.parentNode.removeChild(M.report_spamcleaner.row);
			                M.report_spamcleaner.row = null;
			            }
			        }					
				}
			}
		}
		M.report_spamcleaner.Y.io(M.report_spamcleaner.me+'?ignore=yes&sesskey='+M.cfg.sesskey+'&id='+id, cfg);
	},

    init: function(Y, me) {
		M.report_spamcleaner.Y = Y.use('json', 'io');
		M.report_spamcleaner.me = me;
		Y.on("click", M.report_spamcleaner.del_all, "#removeall_btn");
	}
}
