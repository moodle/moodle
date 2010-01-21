var timefromitems = ['fromday','frommonth','fromyear','fromhour', 'fromminute'];
var timetoitems = ['today','tomonth','toyear','tohour','tominute'];

function forum_produce_subscribe_link(forumid, backtoindex, ltext, ltitle) {
    var elementid = "subscriptionlink";
    var subs_link = document.getElementById(elementid);
    if(subs_link){
        subs_link.innerHTML = "<a title="+ltitle+" href='"+M.cfg.wwwroot+"/mod/forum/subscribe.php?id="+forumid+backtoindex+"'>"+ltext+"<\/a>";
    }
}

function forum_produce_tracking_link(forumid, ltext, ltitle) {
    var elementid = "trackinglink";
    var subs_link = document.getElementById(elementid);
    if(subs_link){
        subs_link.innerHTML = "<a title="+ltitle+" href='"+M.cfg.wwwroot+"/mod/forum/settracking.php?id="+forumid+"'>"+ltext+"<\/a>";
    }
}

function lockoptions_timetoitems() {
    lockoptions('searchform','timefromrestrict', timefromitems);
}

function lockoptions_timefromitems() {
    lockoptions('searchform','timetorestrict', timetoitems);
}
