<?php
require_once('../config.php');
require_once('lib.php');
if(!empty($_GET['create'])) {
    $result = true;
    $entry = new stdclass;
    $entry->repositoryname = 'Box.net';
    $entry->repositorytype = 'boxnet';
    $entry->contextid = SITEID;
    $entry->userid = $USER->id;
    $entry->timecreated = time();
    $entry->timemodified = time();
    $result = $result && $DB->insert_record('repository', $entry);
    $entry->repositoryname = 'Flickr!';
    $entry->repositorytype = 'flickr';
    $entry->contextid = SITEID;
    $entry->userid = $USER->id;
    $entry->timecreated = time();
    $entry->timemodified = time();
    $result = $result && $DB->insert_record('repository', $entry);
    if($result){
        die('200');
    } else {
        die('403');
    }
}
?>
<html>
<head>
<title> Ajax picker demo page </title>
<?php
/*******************************************************\

  This file is a demo page for ajax repository file
  picker.

\*******************************************************/
$ret = repository_get_client();
?>
</head>
<body class=" yui-skin-sam">
<div id='control'>
    <h1>Open the picker</h1>
    <input type="button" id="con1" onclick='openpicker({"env":"form"})' value="Open File Picker" style="font-size: 24px;padding: 1em" /> <br/>
    <input type='hidden' id="result">
</div>
<div>
    <div id="file-picker"></div>
</div>
<hr />

<div>
    <h1>Create Repository Instance</h1>
    <input type='button' id="create-repo" value="Create!" style="font-size: 24px;padding: 1em" />
<script type="text/javascript">
btn = document.getElementById('create-repo');
var create_cb = {
    success: function(o) {
        try{
            var ret = o.responseText;
        } catch(e) {
            alert(e);
        }
        if(ret == 200) {
            alert('Create Repository Instances successfully!');
            location.reload();
            btn.value='Done';
        } else {
            alert('Failed to create repository instances.');
            btn.value='Created';
            btn.disabled = false;
        }
    }
}
if(btn){
    btn.onclick = function(){
        btn.value = 'waiting...';
        btn.disabled = true;
        var trans = YAHOO.util.Connect.asyncRequest('GET', 'ajax.php?create=true', create_cb);
    }
}
</script>
</div>
<?php
echo $ret['js'];
?>
</body>
</html>
