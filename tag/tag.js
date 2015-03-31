var coursetagdivs = null;
var coursetag_tags = new Array();

function init_tag_autocomplete() {
YUI().use('yui2-autocomplete', 'yui2-datasource', 'yui2-animation', 'yui2-connection', function(Y) {
    var myDataSource = new Y.YUI2.util.XHRDataSource("./tag_autocomplete.php");
    myDataSource.responseType = Y.YUI2.util.XHRDataSource.TYPE_TEXT;
    myDataSource.responseSchema = {
        recordDelim: "\n",
        fieldDelim: "\t"
    };
    myDataSource.maxCacheEntries = 60;

    // Instantiate the AutoComplete
    var myAutoComp = new Y.YUI2.widget.AutoComplete("id_relatedtags", "relatedtags-autocomplete", myDataSource);
    document.getElementById('id_relatedtags').style.width = '30%';
    myAutoComp.allowBrowserAutocomplete = false;
    myAutoComp.maxResultsDisplayed = 20;
    myAutoComp.minQueryLength = 3;
    myAutoComp.delimChar = [","," "];
    myAutoComp.formatResult = function(oResultData, sQuery, sResultMatch) {
        return (sResultMatch);
    };

    return {
        myDataSource: myDataSource,
        myAutoComp: myAutoComp
    };
});
}

function ctags_checkinput(val) {
    var len = val.length;
    if (len < 2 || len > 50) {
        alert(M.util.get_string('jserror1', 'block_tags'));
        return false;
    } else if (val.indexOf("<") > 0) {
        alert(M.util.get_string('jserror2', 'block_tags'));
        return false;
    } else if (val.indexOf(">") > 0) {
        alert(M.util.get_string('jserror2', 'block_tags'));
        return false;
    } else {
        return true;
    }
}

function set_course_tag(key) {
    window.coursetag_tags[window.coursetag_tags.length] = key;
}

function add_tag_footer_link(eid, ltitle, laction, ltext) {
    var e = document.getElementById(eid);
    if (e) {
        var link = document.createElement('a');
        link.setAttribute('href', '');
        link.setAttribute('title', ltitle);
        link.appendChild(document.createTextNode(ltext));
        var callback = function () {
            ctags_show_div(laction);
        };
        YUI().use('yui2-event', function(Y) {
            Y.YUI2.util.Event.addListener(link, 'click', callback);
        });
        if (e.childNodes.length > 0) {
            e.appendChild(document.createTextNode(' | '));
        } else {
            e.appendChild(document.createElement('hr'));
        }
        e.appendChild(link);
    }
}
