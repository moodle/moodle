<?php // $Id$
    require("../../../../config.php");

    $id = optional_param('id', SITEID, PARAM_INT);

    require_course_login($id);
    @header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print_string("searchandreplace","editor");?></title>
<script type="text/javascript" src="popup.js"></script>
<script type="text/javascript">
//<![CDATA[
function _CloseOnEsc(ev) {
    ev || (ev = window.event) || (ev = editor._iframe.contentWindow.event);
    if (ev.keyCode == 27) {
        // update_parent();
        window.close();
        return;
    }
}

//Initialize
function Init() {

  __dlg_init();

  document.body.onkeypress = _CloseOnEsc;
  var param = window.dialogArguments;
  document.getElementById("f_search").value = param["f_search"];

  document.getElementById("f_search").focus();
  document.getElementById("f_search").select();

};

//Actions
function onReplaceAll() {
    var searchtxt = document.getElementById("f_search").value;

    //Check a search string
    if (searchtxt.length < 1 ) {
        alert ("Search string is empty!");
        return true;
    }

    var replacetxt = document.getElementById("f_replace").value;
    var stringcase =  (document.getElementById("f_case").checked) ? "g" : "gi";
    var regularx = (document.getElementById("f_regx").checked) ? 1 : 0;
    //var closesar = (document.getElementById("f_csar").checked) ? 1 : 0;
    var closesar = 1;
    var param = [ searchtxt , replacetxt, stringcase, regularx, closesar ];

    //looks that not workin in ie :( need to fix!
    if (closesar) {
        __dlg_close(param);
        window.close();
        return false;
    } else {
        return true;
    }
};

function onCancel() {
  __dlg_close(null);
  return false;
};

//]]>
</script>

<style type="text/css">
html, body {
  background: ButtonFace;
  color: ButtonText;
  font: 11px Tahoma,Verdana,sans-serif;
  margin: 0px;
  padding: 0px;
}
body { padding: 5px; }
table {
  font: 11px Tahoma,Verdana,sans-serif;
}
form p {
  margin-top: 5px;
  margin-bottom: 5px;
}
.fl { width: 9em; float: left; padding: 2px 5px; text-align: right; }
.fr { width: 7em; float: left; padding: 2px 5px; text-align: right; }
fieldset { padding: 0px 10px 5px 5px; }
select, input, button { font: 11px Tahoma,Verdana,sans-serif; }
button { width: 70px; }
#buttons {
      margin-top: 1em; border-top: 1px solid #999;
      padding: 2px; text-align: right;
}

.space { padding: 2px; }

.title { background: #ddf; color: #000; font-weight: bold; font-size: 120%; padding: 3px 10px; margin-bottom: 10px;
border-bottom: 1px solid black; letter-spacing: 2px;
}
form { padding: 0px; margin: 0px; }
</style>
</head>
<body onload="Init()">
<div class="title"><?php print_string("searchandreplace","editor");?></div>
<form>
<table border="0" style="width: 100%;">
  <tr>
    <td class="label"><?php print_string("findwhat","editor");?>:</td>
    <td align="left"><input type="text" id="f_search" style="width: 280px" /></td>
  </tr>
  <tr>
    <td class="label"><?php print_string("replacewith","editor");?>:</td>
    <td align="left"><input type="text" id="f_replace" style="width: 280px" /></td>
  </tr>

  </table>
  <fieldset>
    <legend><span style="font-weight: bold;"><?php print_string("options","editor");?>:</span></legend>
<table border="0" style="width: 100%;">
  <tr>
    <td style="width: 20px;"><input type="checkbox" id="f_regx" checked="checked" /></td>
    <td><label for="f_regx"><?php print_string("regularexpressions","editor");?></label></td>
  </tr>
  <tr>
    <td style="width: 20px;"><input type="checkbox" id="f_case" checked="checked" /></td>
    <td><label for="f_case"><?php print_string("matchcase","editor");?></label></td>
  </tr>
  <!-- <tr>
    <td style="width: 20px;"><input type="checkbox" id="f_csar" checked="checked" /></td>
    <td><label for="f_csar"><?php print_string("closeafterreplace","editor");?></label></td>
  </tr> -->
</table>
</fieldset>
<div id="buttons">
  <button type="button" name="ok" onclick="return onReplaceAll();" style="width: 120px;"><?php print_string("replaceall","editor");?></button>
  <button type="button" name="cancel" onclick="return onCancel();"><?php print_string("cancel","editor");?></button>
</div>
</form>
</body>
</html>
