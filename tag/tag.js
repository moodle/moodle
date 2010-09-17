var coursetagdivs = null;
var coursetag_tags = new Array();

function init_tag_autocomplete() {

    var myDataSource = new YAHOO.util.XHRDataSource("./tag_autocomplete.php");
    myDataSource.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
    myDataSource.responseSchema = {
        recordDelim: "\n",
        fieldDelim: "\t"
    };
    myDataSource.maxCacheEntries = 60;
    myDataSource.minQueryLength = 3;

    // Instantiate the AutoComplete
    var myAutoComp = new YAHOO.widget.AutoComplete("id_relatedtags", "relatedtags-autocomplete", myDataSource);
    document.getElementById('id_relatedtags').style.width = '30%';
    myAutoComp.allowBrowserAutocomplete = false;
    myAutoComp.maxResultsDisplayed = 20;
    myAutoComp.delimChar = [","," "];
    myAutoComp.formatResult = function(oResultData, sQuery, sResultMatch) {
        return (sResultMatch);
    };

    return {
        myDataSource: myDataSource,
        myAutoComp: myAutoComp
    };

}

function ctags_checkinput(val) {
    var len = val.length;
    if (len < 2 || len > 50) {
        alert(M.str.block_tags.jserror1);
        return false;
    } else if (val.indexOf("<") > 0) {
        alert(M.str.block_tags.jserror2);
        return false;
    } else if (val.indexOf(">") > 0) {
        alert(M.str.block_tags.jserror2);
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
        };
        YAHOO.util.Event.addListener(link, 'click', callback);
        if (e.childNodes.length > 0) {
            e.appendChild(document.createTextNode(' | '));
        } else {
            e.appendChild(document.createElement('hr'));
        }
        e.appendChild(link);
    }
}