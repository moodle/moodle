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
  <title><?php print_string("inserttable","editor");?></title>
<script type="text/javascript" src="popup.js"></script>
<script type="text/javascript">
//<![CDATA[

function Init() {
  __dlg_init();
  document.getElementById('f_rows').focus();
};

function onOK() {
  var required = {
    "f_rows": "You must enter a number of rows",
    "f_cols": "You must enter a number of columns"
  };
  for (var i in required) {
    var el = document.getElementById(i);
    if (!el.value) {
      alert(required[i]);
      el.focus();
      return false;
    }
  }
  var fields = ["f_rows", "f_cols", "f_width", "f_unit",
                "f_align", "f_border", "f_spacing", "f_padding"];
  var param = new Object();
  for (var i in fields) {
    var id = fields[i];
    var el = document.getElementById(id);
    param[id] = el.value;
  }
  __dlg_close(param);
  return false;
};

function onCancel() {
  __dlg_close(null);
  return false;
};
//[[>
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
.space { padding: 2px; }

.title { background: #ddf; color: #000; font-weight: bold; font-size: 120%; padding: 3px 10px; margin-bottom: 10px;
border-bottom: 1px solid black; letter-spacing: 2px;
}
form { padding: 0px; margin: 0px; }
</style>
</head>
<body onload="Init()">

<div class="title"><?php print_string("inserttable","editor") ?></div>

<form action="" method="get">
<table border="0" style="padding: 0px; margin: 0px">
  <tbody>

  <tr>
    <td style="width: 4em; text-align: right"><?php print_string("rows","editor") ?>:</td>
    <td><input type="text" name="f_rows" id="f_rows" size="5" title="Number of rows" value="2" /></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td style="width: 4em; text-align: right"><?php print_string("cols","editor") ?>:</td>
    <td><input type="text" name="f_cols" id="f_cols" size="5" title="Number of columns" value="4" /></td>
    <td style="width: 4em; text-align: right"><?php print_string("width","editor") ?>:</td>
    <td><input type="text" name="f_width" id="f_width" size="5" title="Width of the table" value="100" /></td>
    <td><select size="1" name="f_unit" id="f_unit" title="Width unit">
      <option value="%" selected="selected"  ><?php print_string("percent","editor") ?></option>
      <option value="px"              ><?php print_string("pixels","editor") ?></option>
      <option value="em"              >Em</option>
    </select></td>
  </tr>

  </tbody>
</table>

<p />

<fieldset style="float: left; margin-left: 5px;">
<legend><?php print_string("layout","editor") ?></legend>

<div class="space"></div>

<div class="fl"><?php print_string("alignment","editor") ?>:</div>
<select size="1" name="f_align" id="f_align"
  title="Positioning of this image">
  <option value="" selected="selected"                ><?php print_string("notset","editor") ?></option>
  <option value="left"                         ><?php print_string("left","editor") ?></option>
  <option value="right"                        ><?php print_string("right","editor") ?></option>
  <option value="texttop"                      ><?php print_string("texttop","editor") ?></option>
  <option value="middle"                    ><?php print_string("middle","editor") ?></option>
  <option value="baseline"                     ><?php print_string("baseline","editor") ?></option>
  <option value="absbottom"                    ><?php print_string("absbottom","editor") ?></option>
  <option value="bottom"                       ><?php print_string("bottom","editor") ?></option>
  <option value="middle"                       ><?php print_string("middle","editor") ?></option>
  <option value="top"                          ><?php print_string("top","editor") ?></option>
</select>

<p />

<div class="fl"><?php print_string("borderthickness","editor") ?>:</div>
<input type="text" name="f_border" id="f_border" size="5" value="1"
title="Leave empty for no border" />
<!--
<p />

<div class="fl">Collapse borders:</div>
<input type="checkbox" name="collapse" id="f_collapse" />
-->
<div class="space"></div>

</fieldset>

<fieldset style="float:right; margin-right: 5px;">
<legend><?php print_string("spacing","editor") ?></legend>

<div class="space"></div>

<div class="fr"><?php print_string("cellspacing","editor") ?>:</div>
<input type="text" name="f_spacing" id="f_spacing" size="5" value="1"
title="Space between adjacent cells" />

<p />

<div class="fr"><?php print_string("cellpadding","editor") ?>:</div>
<input type="text" name="f_padding" id="f_padding" size="5" value="1"
title="Space between content and border in cell" />

<div class="space"></div>

</fieldset>

<div style="margin-top: 85px; text-align: right;">
<hr />
<button type="button" name="ok" onclick="return onOK();"><?php print_string("ok","editor") ?></button>
<button type="button" name="cancel" onclick="return onCancel();"><?php print_string("cancel","editor") ?></button>
</div>
</form>
</body>
</html>
