/**
 * @author Dongsheng Cai <dongsheng@moodle.com>
 */
tinyMCEPopup.requireLangPack();

var oldWidth, oldHeight, ed, url;

if (url = tinyMCEPopup.getParam("media_external_list_url")) {
    document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
}

function init() {
    ed = tinyMCEPopup.editor;
    document.getElementById('filebrowsercontainer').innerHTML = getBrowserHTML('filebrowser','src','media','media');
}

function insertMedia() {
    var f = document.forms[0];
    var url = f.filename.value;
    var linkname = url.substring(url.lastIndexOf('/')+1);
    var h = '<a href="'+f.src.value+'">'+linkname+'</a>';
    ed.execCommand('mceInsertContent', false, h);
    tinyMCEPopup.close();
}

function getType(v) {
    var fo, i, c, el, x, f = document.forms[0];

    fo = ed.getParam("media_types", "flash=swf;flv=flv;shockwave=dcr;qt=mov,qt,mpg,mp3,mp4,mpeg;shockwave=dcr;wmp=avi,wmv,wm,asf,asx,wmx,wvx;rmp=rm,ra,ram").split(';');

    // YouTube
    if (v.match(/watch\?v=(.+)(.*)/)) {
        f.src.value = 'http://www.youtube.com/v/' + v.match(/v=(.*)(.*)/)[0].split('=')[1];
        return 'flash';
    } else if (v.match(/v\/(.+)(.*)/)) {
        return 'flash';
    }

    // Google video
    if (v.indexOf('http://video.google.com/videoplay?docid=') == 0) {
        f.src.value = 'http://video.google.com/googleplayer.swf?docId=' + v.substring('http://video.google.com/videoplay?docid='.length) + '&hl=en';
        return 'flash';
    }

    for (i=0; i<fo.length; i++) {
        c = fo[i].split('=');

        el = c[1].split(',');
        for (x=0; x<el.length; x++)
            if (v.indexOf('.' + el[x]) != -1)
                return c[0];
    }

    return null;
}


function serializeParameters() {
    var d = document, f = d.forms[0], s = '';
    s += getStr(null, 'src');
    s += 'width:300,';
    s += 'height:225,';

    // delete the tail comma
    s = s.length > 0 ? s.substring(0, s.length - 1) : s;

    return s;
}


function getStr(p, n, d) {
    var e = document.forms[0].elements[(p != null ? p + "_" : "") + n];
    var v = e.type == "hidden" ? e.value : e.options[e.selectedIndex].value;

    if (n == 'src')
            v = tinyMCEPopup.editor.convertURL(v, 'src', null);

    return ((n == d || v == '') ? '' : n + ":'" + jsEncode(v) + "',");
}

function jsEncode(s) {
    s = s.replace(new RegExp('\\\\', 'g'), '\\\\');
    s = s.replace(new RegExp('"', 'g'), '\\"');
    s = s.replace(new RegExp("'", 'g'), "\\'");

    return s;
}

function generatePreview(c) {
    var f = document.forms[0], p = document.getElementById('prev'), h = '', cls, pl, n, type, codebase, wp, hp, nw, nh;

    p.innerHTML = '<!-- x --->';
    var type = getType(f.src.value);
    var re = new RegExp("(.+)\#(.+)", "i");
    var result = f.src.value.match(re);
    if (result) {
        f.src.value = result[1];
        f.filename.value = result[2];
    } else {
        f.src.value = f.src.value;
        f.filename.value = f.src.value;
    }

    // After constrain
    pl = serializeParameters();

    switch (type) {
        case 'flash':
            cls = 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000';
            codebase = 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0';
            type = 'application/x-shockwave-flash';
            break;
		case "shockwave":
			cls = 'clsid:166B1BCA-3F9C-11CF-8075-444553540000';
			codebase = 'http://download.macromedia.com/pub/shockwave/cabs/director/sw.cab#version=8,5,1,0';
			type = 'application/x-director';
			break;

		case "qt":
			cls = 'clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B';
			codebase = 'http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0';
			type = 'video/quicktime';
			break;

		case "wmp":
			cls = ed.getParam('media_wmp6_compatible') ? 'clsid:05589FA1-C356-11CE-BF01-00AA0055595A' : 'clsid:6BF52A52-394A-11D3-B153-00C04F79FAA6';
			codebase = 'http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701';
			type = 'application/x-mplayer2';
			break;

		case "rmp":
			cls = 'clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA';
			codebase = 'http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701';
			type = 'audio/x-pn-realaudio-plugin';
			break;
    }

    if (pl == '') {
            p.innerHTML = '';
            return;
    }

    pl = tinyMCEPopup.editor.plugins.moodlemedia._parse(pl);

    if (!pl.src) {
            p.innerHTML = '';
            return;
    }

    pl.src = tinyMCEPopup.editor.documentBaseURI.toAbsolute(pl.src);
    pl.width = !pl.width ? 100 : pl.width;
    pl.height = !pl.height ? 100 : pl.height;
    pl.id = !pl.id ? 'moodlemediaid' : pl.id;
    pl.name = !pl.name ? 'moodlemedianame' : pl.name;
    pl.align = !pl.align ? '' : pl.align;

    // Avoid annoying warning about insecure items
    if (!tinymce.isIE || document.location.protocol != 'https:') {
        h += '<object classid="' + cls + '" codebase="' + codebase + '" width="' + pl.width + '" height="' + pl.height + '" id="' + pl.id + '" name="' + pl.name + '" align="' + pl.align + '">';

        for (n in pl) {
                h += '<param name="' + n + '" value="' + pl[n] + '">';

                // Add extra url parameter if it's an absolute URL
                if (n == 'src' && pl[n].indexOf('://') != -1)
                        h += '<param name="url" value="' + pl[n] + '" />';
        }
    }

    h += '<embed type="' + type + '" ';

    for (n in pl) {
        h += n + '="' + pl[n] + '" ';
    }

    h += '></embed>';

    // Avoid annoying warning about insecure items
    if (!tinymce.isIE || document.location.protocol != 'https:')
        h += '</object>';

    p.innerHTML = "<!-- x --->" + h;
}

tinyMCEPopup.onInit.add(init);
