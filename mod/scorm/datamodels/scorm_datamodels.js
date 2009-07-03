function expandCollide(which,list,item) {
    var el = document.ids ? document.ids[list] : document.getElementById ? document.getElementById(list) : document.all[list];
    which = which.substring(0,(which.length));
    var el2 = document.ids ? document.ids[which] : document.getElementById ? document.getElementById(which) : document.all[which];
    if (el.style.display != "none") {
        el2.src = scormdata.plusicon;
        el.style.display='none';
        new cookie("hide:SCORMitem" + item, 1, 356, "/").set();
    } else {
        el2.src = scormdata.minusicon;
        el.style.display='block';
        new cookie("hide:SCORMitem" + item, 1, -1, "/").set();
    }
}