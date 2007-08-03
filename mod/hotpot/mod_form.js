//<!--
//<![CDATA[

function getObjValue(obj) {
    var v = ''; // the value
    var t = (obj && obj.type) ? obj.type : "";
    if (t=="text" || t=="textarea" || t=="hidden") {
        v = obj.value;
    } else if (t=="select-one" || t=="select-multiple") {
        var l = obj.options.length;
        for (var i=0; i<l; i++) {
            if (obj.options[i].selected) {
                v += (v=="" ? "" : ",") + obj.options[i].value;
            }
        }
    }
    return v;
}
function getDir(s) {
    if (s.substring(0,7)=='http://' || s.substring(0,8)=='https://') {
        return '';
    }
    if (s.charAt(0) != '/') {
        s = '/' + s;
    }
    return s.substring(0, s.lastIndexOf('/'));
}
function AddWhiteSpace(BeforeOrAfter, id) {
    if (document.getElementById) {
        // locate the DIV object (class="fitem") containing the target element
        var obj = document.getElementById(id);
        while (obj && !(obj.className && (obj.className=='fitem' || obj.className.substring(0,6)=='fitem '))) {
            obj = obj.parentNode;
        }
        if (obj) {
			switch (BeforeOrAfter) {
				case 'before': obj.style.marginTop = '1.8em'; break;
				case 'after': obj.style.marginBottom = '0.8em'; break;
            }
        }
    }
}
AddWhiteSpace('after', 'id_name');
AddWhiteSpace('before', 'id_quizchain');
AddWhiteSpace('before', 'id_password');
AddWhiteSpace('before', 'id_review');
//]]>
//-->