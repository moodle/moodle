function local_file_set_value(choose, localpath) {
    var txt = document.getElementById('myform').myfile.value;
    if (txt.indexOf('/') > -1) {
        var mpath = txt.substring(txt.indexOf('/'),txt.length);
    } else if (txt.indexOf('\\') > -1) {
        var mpath = txt.substring(txt.indexOf('\\'),txt.length);
    } else {
        window.close();
        return;
    }
    opener.document.getElementById('<?php echo $choose ?>').value = localpath+mpath;
    window.close();
}

function local_path_set_value(txt) {
    if (txt.indexOf('/') > -1) {
        txt = txt.substring(0,txt.lastIndexOf('/'));
    } else if (txt.indexOf('\\') > -1) {
        txt = txt.substring(0,txt.lastIndexOf('\\'));
    }
    document.getElementById('myform').pathname.value = txt;
    document.getElementById('myform').submit();
}

function resizeEmbeddedHtml(viewportheight) {
    //calculate new embedded html height size
    objectheight =  YAHOO.util.Dom.getViewportHeight() - viewportheight;

    if (objectheight < 200) {
        objectheight = 200;
    }
    //resize the embedded html object
    YAHOO.util.Dom.setStyle("embeddedhtml", "height", objectheight+"px");
    YAHOO.util.Dom.setStyle("embeddedhtml", "width", "100%");
}

function file_resource_init(viewportheight) {
    resizeEmbeddedHtml();
    YAHOO.widget.Overlay.windowResizeEvent.subscribe(resizeEmbeddedHtml);
}