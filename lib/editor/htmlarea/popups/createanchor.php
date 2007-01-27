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
<title>Create anchor</title>
<script type="text/javascript" src="popup.js"></script>
<script type="text/javascript">
//<![CDATA[
function Init() {
    __dlg_init();
}

function onOK() {
  var required = {
    "f_anc": "You must enter the URL where this link points to"
  };
  var txt = document.forms[0].anc.value;
  if (!txt) {
    alert(required[f_anc]);
    el.focus();
    return false;
  }
  // pass data back to the calling window
  var param = new Object();
  param.anchor = txt;
  __dlg_close(param);
  return false;
};

function onCancel() {
  __dlg_close(null);
  return false;
};
//]]>
</script>
<style type="text/css">
<!--
body { background: ButtonFace; font-family: Tahoma, sans-serif; }
td, button, input { font-family: Tahoma, verdana, sans-serif; font-size: 8pt; }
button { width: 70px; }
.title { background: #ddf; color: #000; font-weight: bold; font-size: 10pt; padding: 3px 10px; margin-bottom: 10px;
border-bottom: 1px solid black; letter-spacing: 2px;
}
.note { font-size: 8pt; }
-->
</style>
</head>
<body>
<div class="title"><?php print_string("createanchor","editor");?></div>
<form id="fie">
<table border="0" cellpadding="2" cellspacing="0">
<tr>
    <td><?php print_string("anchorname","editor");?>: <input id="f_anc" name="anc" type="text" size="30" /></td>
</tr>
<tr>
    <td align="right">
    <br />
    <button onclick="return onOK();" type="button"><?php print_string("ok","editor");?></button>&nbsp;<button onclick="return onCancel();" type="button"><?php print_string("cancel","editor");?></button>&nbsp;
    <button type="button" onclick="javascript: void(0); alert('<?php print_string("anchorhelp","editor");?>');"><?php print_string("help");?></button></td>
</tr>
</table>
</form>
</body>
</html>
