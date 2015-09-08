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
