$(document).ready(function () {
    $("#tabdiv").tabs();
    $.getJSON("tablist.php", function(data) {
        $("#tabdiv").select(data["default"]);
        for (var i = 0; i < data["tabs"].length; i++) {
            var tab = data["tabs"][i];
            $("#query_" + tab).liveUpdate("#list_" + tab);
            if (data["faventry"] === null && i === 0) {
                $("#query_" + tab).focus();
            }
        }
    });
});
