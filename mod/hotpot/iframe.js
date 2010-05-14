function domSniffer() {
    var t = true;
    var s = navigator.userAgent;
    if (s.indexOf("Mac") >=0) this.mac = t;
    if (s.indexOf("Opera") >=0) this.opera = t;
    var d = document;
    if (d.layers) this.n4 = t;
    if (d.childNodes) this.dom = t;
    if (d.all && d.plugins) this.ie = t;
}
function getContentH(lyr) {
    return (is.n4) ? lyr.document.height : (is.ie) ? (is.mac ? lyr.offsetHeight : lyr.scrollHeight) : (is.opera) ? lyr.style.pixelHeight : (is.dom) ? lyr.offsetHeight : 0;
}
function px(i) {
    return i + "px";
}
function setSize(obj, w, h) {
    if (is.n4) {
        if (w) obj.width = w;
        if (h) obj.height = h;
    } else if (is.opera) {
        // opera 5 needs pixelWidth/Height
        if (w) obj.style.pixelWidth = w;
        if (h) obj.style.pixelHeight = h;
    } else {
        if (w) obj.style.width = px(w);
        if (h) obj.style.height = px(h);
    }
}
function getElement(id, lyr) {
    var d = (document.layers && lyr) ? lyr.document : document;
    var obj = (document.layers) ? eval("d."+id) : (d.all) ? d.all[id] : (d.getElementById) ? d.getElementById(id) : null;
    return obj;
}
function set_embed_object_height(evt, embed_object) {
    if (typeof(embed_object)=='undefined') {
        if (evt) {
            // we are being called by the onload event handler
            if (evt.target) { // most browsers
                embed_object = evt.target;
            } else if (evt.srcElement) { // IE
                embed_object = evt.srcElement;
            }
        }
    }
    var obj = null;
    if (embed_object) {
        if (document.frames) { // IE
            switch (embed_object.tagName) {
                case 'IFRAME':
                    obj = document.frames[embed_object.name].document;
                    break;
                case 'OBJECT':
                    obj = embed_object; // already an HTML document element
                    break;
            }
        } else { // Firefox, Safari, Opera, Chrome
            obj = embed_object.document || embed_object.contentDocument || null;
        }
    }
    if (obj) {
        if (obj.body) {
            obj = obj.body;
        }
        var h = getContentH(obj);
        if (h) {
            setSize(embed_object, 0, h + 65);
        }
        // at some point the next two lines were important, but now it doesn't seeme to matter ?!
        // if (document.all) {
        //     embed_object.allowTransparency = true;
        //     obj.style.backgroundColor = 'transparent';
        // }
    }
}
is = new domSniffer();