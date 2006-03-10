<!--//
function del(baseurl, id, userid) {

	if(confirm("Do you really want to delete that blog entry?")) {
		document.location = baseurl+"/edit.php?act=del&postid="+id+"&userid="+userid;
	}	
}

function openPrev() { 
    //dh - added try{}catch{} statements to allow the function to continue along even
    //if some of the elements it is expecting are not present in the original form

    preview = window.open('', 'preview', 'width=640,height=480,scrollbars=yes,status=yes,resizable=yes');
	document.prev.elements['format'].value = document.entry.format.selectedIndex;
	document.prev.elements['etitle'].value = document.entry.elements['etitle'].value;
//    alert('title = '+document.entry.elements['etitle'].value);

    if (window.frames.length > 0) {
        // editor is loaded
        document.prev.elements['body'].value = document.all ? frames[0].document.body.innerHTML : frames[1].document.body.innerHTML;
        try {
            document.prev.elements['extendedbody'].value = document.all ? frames[1].document.body.innerHTML : frames[0].document.body.innerHTML;
        } catch(e) {
            ; //ignore failure
        }
    } else {
        // standard webforms
        document.prev.elements['body'].value = document.entry.elements['body'].value;
        try {
            document.prev.elements['extendedbody'].value = document.entry.elements['extendedbody'].value;
        } catch(e) {
            ; //ignore failure
        }
    }

    try {
        var sourceSelect = document.entry.elements['categoryid[]'];
        var targetSelect = document.prev.elements['categoryid[]'];            

        for (i=0; i < sourceSelect.length; i++) {
            if (sourceSelect.options[i].selected == true) {
                targetSelect.options[i].selected = true;
            } else {
                targetSelect.options[i].selected = false;
            }
        }
    } catch(e) {
        ; //ignore failure
    }
    document.prev.submit();
}
//-->