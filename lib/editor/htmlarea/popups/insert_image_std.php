<?php // $Id$
    require("../../../../config.php");

    $id = optional_param('id', SITEID, PARAM_INT);

    require_course_login($id);
    @header('Content-Type: text/html; charset=utf-8');

    if ($httpsrequired or (!empty($_SERVER['HTTPS']) and $_SERVER['HTTPS'] != 'off')) {
        $url = preg_replace('|https?://[^/]+|', '', $CFG->wwwroot).'/lib/editor/htmlarea/';
    } else {
        $url = $CFG->wwwroot.'/lib/editor/htmlarea/';
    }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title><?php print_string("insertimage","editor");?></title>

<script type="text/javascript" src="popup.js"></script>

<script type="text/javascript">
//<![CDATA[
var preview_window = null;

function Init() {
  __dlg_init();
  var param = window.dialogArguments;
  if (param) {
      var alt = param["f_url"].substring(param["f_url"].lastIndexOf('/') + 1);
      document.getElementById("f_url").value = param["f_url"];
      document.getElementById("f_alt").value = param["f_alt"] ? param["f_alt"] : alt;
      document.getElementById("f_border").value = parseInt(param["f_border"] || 0);
      document.getElementById("f_vert").value = param["f_vert"] != -1 ? param["f_vert"] : 0;
      document.getElementById("f_horiz").value = param["f_horiz"] != -1 ? param["f_horiz"] : 0;
  }
  document.getElementById("f_url").focus();
};

function onOK() {
  var required = {
    "f_url": "<?php print_string("mustenterurl", "editor");?>",
    "f_url": "<?php print_string("pleaseenteralt", "editor");?>"
  };
  for (var i in required) {
    var el = document.getElementById(i);
    if (!el.value) {
      alert(required[i]);
      el.focus();
      return false;
    }
  }
  // pass data back to the calling window
  var fields = ["f_url", "f_alt", "f_align", "f_border",
                "f_horiz", "f_vert"];
  var param = new Object();
  for (var i in fields) {
    var id = fields[i];
    var el = document.getElementById(id);
    param[id] = el.value;
  }
  if (preview_window) {
    preview_window.close();
  }
  __dlg_close(param);
  return false;
};

function onCancel() {
  if (preview_window) {
    preview_window.close();
  }
  __dlg_close(null);
  return false;
};

function onPreview() {
  var f_url = document.getElementById("f_url");
  var url = f_url.value;
  if (!url) {
    alert("<?php print_string("enterurlfirst","editor");?>");
    f_url.focus();
    return false;
  }
  var img = new Image();
  img.src = url;
  var win = null;
  if (!document.all) {
    win = window.open("<?php echo $url ?>blank.html", "ha_imgpreview", "toolbar=no,menubar=no,personalbar=no,innerWidth=100,innerHeight=100,scrollbars=no,resizable=yes");
  } else {
    win = window.open("<?php echo $url ?>blank.html", "ha_imgpreview", "channelmode=no,directories=no,height=100,width=100,location=no,menubar=no,resizable=yes,scrollbars=no,toolbar=no");
  }
  preview_window = win;
  var doc = win.document;
  var body = doc.body;
  if (body) {
    body.innerHTML = "";
    body.style.padding = "0px";
    body.style.margin = "0px";
    var el = doc.createElement("img");
    el.src = url;

    var table = doc.createElement("table");
    body.appendChild(table);
    table.style.width = "100%";
    table.style.height = "100%";
    var tbody = doc.createElement("tbody");
    table.appendChild(tbody);
    var tr = doc.createElement("tr");
    tbody.appendChild(tr);
    var td = doc.createElement("td");
    tr.appendChild(td);
    td.style.textAlign = "center";

    td.appendChild(el);
    win.resizeTo(el.offsetWidth + 30, el.offsetHeight + 30);
  }
  win.focus();
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
.fr { width: 6em; float: left; padding: 2px 5px; text-align: right; }
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

<div class="title"><?php print_string("insertimage","editor");?></div>

<form action="" method="get">
<table border="0" width="100%" style="padding: 0px; margin: 0px">
  <tbody>

  <tr>
    <td style="width: 7em; text-align: right"><?php print_string("imageurl","editor");?>:</td>
    <td><input type="text" name="url" id="f_url" style="width:75%"
      title="Enter the image URL here" />
      <button name="preview" onclick="return onPreview()"
      title="Preview the image in a new window"><?php print_string("preview","editor");?></button>
    </td>
  </tr>
  <tr>
    <td style="width: 7em; text-align: right"><?php print_string("alternatetext","editor");?>:</td>
    <td><input type="text" name="alt" id="f_alt" style="width:100%"
      title="For browsers that don't support images" /></td>
  </tr>
  </tbody>
</table>

<p />
<fieldset style="float: left; margin-left: 5px;">
<legend><?php print_string("layout","editor");?></legend>

<div class="space"></div>

<div class="fl"><?php print_string("alignment","editor");?>:</div>
<select size="1" name="align" id="f_align"
  title="Positioning of this image">
  <option value=""                       ><?php print_string("notset","editor") ?></option>
  <option value="left"                   ><?php print_string("left","editor") ?></option>
  <option value="right"                  ><?php print_string("right","editor") ?></option>
  <option value="texttop"                ><?php print_string("texttop","editor") ?></option>
  <option value="middle"              ><?php print_string("middle","editor") ?></option>
  <option value="baseline" selected="1"  ><?php print_string("baseline","editor") ?></option>
  <option value="absbottom"              ><?php print_string("absbottom","editor") ?></option>
  <option value="bottom"                 ><?php print_string("bottom","editor") ?></option>
  <option value="middle"                 ><?php print_string("middle","editor") ?></option>
  <option value="top"                    ><?php print_string("top","editor") ?></option>
</select>

<p />

<div class="fl"><?php print_string("borderthickness","editor");?>:</div>
<input type="text" name="border" id="f_border" size="5"
title="Leave empty for no border" />

<div class="space"></div>

</fieldset>

<fieldset style="float:right; margin-right: 5px;">
<legend><?php print_string("spacing","editor");?></legend>

<div class="space"></div>

<div class="fr"><?php print_string("horizontal","editor");?>:</div>
<input type="text" name="horiz" id="f_horiz" size="5"
title="Horizontal padding" />

<p />

<div class="fr"><?php print_string("vertical","editor");?>:</div>
<input type="text" name="vert" id="f_vert" size="5"
title="Vertical padding" />

<div class="space"></div>

</fieldset>

<div style="margin-top: 85px; text-align: right;">
<hr />
<button type="button" name="ok" onclick="return onOK();"><?php print_string("ok","editor");?></button>
<button type="button" name="cancel" onclick="return onCancel();"><?php print_string("cancel","editor");?></button>
</div>
</form>
</body>
</html>
