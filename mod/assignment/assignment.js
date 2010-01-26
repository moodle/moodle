var assignment = {};

function setNext(){
    document.getElementById('submitform').mode.value = 'next';
    document.getElementById('submitform').userid.value = assignment.nextid;
}

function saveNext(){
    document.getElementById('submitform').mode.value = 'saveandnext';
    document.getElementById('submitform').userid.value = assignment.nextid;
    document.getElementById('submitform').saveuserid.value = assignment.userid;
    document.getElementById('submitform').menuindex.value = document.getElementById('submitform').grade.selectedIndex;
}

function initNext(nextid, usserid) {
	assignment.nextid = nextid;
	assignment.userid = userid;
}