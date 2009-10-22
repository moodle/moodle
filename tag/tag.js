var coursetagdivs = null;
var coursetag_tags = new Array();

function init_tag_autocomplete() {
    // An XHR DataSource
    var myServer = "./tag_autocomplete.php";
    var myDataSource = new YAHOO.widget.DS_XHR(myServer, ["\n", "\t"]);
    myDataSource.responseType = YAHOO.widget.DS_XHR.TYPE_FLAT;
    myDataSource.maxCacheEntries = 60;
    myDataSource.queryMatchSubset = true;

    var myAutoComp = new YAHOO.widget.AutoComplete("id_relatedtags","relatedtags-autocomplete", myDataSource);
    myAutoComp.delimChar = ",";
    myAutoComp.maxResultsDisplayed = 20;
    myAutoComp.minQueryLength = 2;
    myAutoComp.allowBrowserAutocomplete = false;
    myAutoComp.formatResult = function(aResultItem, sQuery) {
        return aResultItem[1];
    }
}

function ctags_checkinput(val) {
    var len = val.length;
    if (len < 2 || len > 50) {
        alert(mstr.block_tags.jserror1);
        return false;
    } else if (val.indexOf("<") > 0) {
        alert(mstr.block_tags.jserror2);
        return false;
    } else if (val.indexOf(">") > 0) {
        alert(mstr.block_tags.jserror2);
        return false;
    } else {
        return true;
    }
}

function set_course_tag_divs(ctagdivs) {
    window.coursetagdivs = ctagdivs;
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
        }
        YAHOO.util.Event.addListener(link, 'click', callback);
        if (e.childNodes.length > 0) {
            e.appendChild(document.createTextNode(' | '));
        } else {
            e.appendChild(document.createElement('hr'));
        }
        e.appendChild(link);
    }
}