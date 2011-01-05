var timefromitems = ['fromday','frommonth','fromyear','fromhour', 'fromminute'];
var timetoitems = ['today','tomonth','toyear','tohour','tominute'];

function forum_produce_subscribe_link(forumid, backtoindex, ltext, ltitle) {
    var elementid = "subscriptionlink";
    var subs_link = document.getElementById(elementid);
    if(subs_link){
        subs_link.innerHTML = "<a title='"+ltitle+"' href='"+M.cfg.wwwroot+"/mod/forum/subscribe.php?id="+forumid+backtoindex+"&amp;sesskey="+M.cfg.sesskey+"'>"+ltext+"<\/a>";
    }
}

function forum_produce_tracking_link(forumid, ltext, ltitle) {
    var elementid = "trackinglink";
    var subs_link = document.getElementById(elementid);
    if(subs_link){
        subs_link.innerHTML = "<a title='"+ltitle+"' href='"+M.cfg.wwwroot+"/mod/forum/settracking.php?id="+forumid+"'>"+ltext+"<\/a>";
    }
}

function lockoptions_timetoitems() {
    lockoptions('searchform','timefromrestrict', timefromitems);
}

function lockoptions_timefromitems() {
    lockoptions('searchform','timetorestrict', timetoitems);
}

function lockoptions(formid, master, subitems) {
    // Subitems is an array of names of sub items.
    // Optionally, each item in subitems may have a
    // companion hidden item in the form with the
    // same name but prefixed by "h".
    var form = document.forms[formid], i;
    if (form[master].checked) {
        for (i=0; i<subitems.length; i++) {
            unlockoption(form, subitems[i]);
        }
    } else {
        for (i=0; i<subitems.length; i++) {
            lockoption(form, subitems[i]);
        }
    }
    return(true);
}


function lockoption(form,item) {
    form[item].setAttribute('disabled', 'disabled');
    if (form.elements['h'+item]) {
        form.elements['h'+item].value=1;
    }
}

function unlockoption(form,item) {
    form[item].removeAttribute('disabled');
    if (form.elements['h'+item]) {
        form.elements['h'+item].value=0;
    }
}
