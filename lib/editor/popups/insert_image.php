<?php include("../../../config.php");

	require_variable($id);

    if (!$course = get_record("course", "id", $id)) {
        $course->fullname = "";   // Just to keep display happy, though browsing may fail
    }
?>
<html style="width: 398; height: 218">

<head>
  <title>Insert Image</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="popup.js"></script>

<script type="text/javascript">
var preview_window = null;

function Init() {
  __dlg_init();
  //document.getElementById("f_url").focus();
};

function onOK() {
  var required = {
    "f_url": "You must enter the URL",
    "f_alt": "Please enter the alternate text"
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
    alert("You have to enter an URL first");
    f_url.focus();
    return false;
  }
  var img = new Image();
  img.src = url;
  var win = null;
  if (!document.all) {
    win = window.open("about:blank", "ha_imgpreview", "toolbar=no,menubar=no,personalbar=no,innerWidth=100,innerHeight=100,scrollbars=no,resizable=yes");
  } else {
    win = window.open("about:blank", "ha_imgpreview", "channelmode=no,directories=no,height=100,width=100,location=no,menubar=no,resizable=yes,scrollbars=no,toolbar=no");
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
function set_url_value()
{
	var url = "<?php echo $CFG->wwwroot ?>/lib/editor/courseimages.php?id=<?php echo $id ?>";
	window.open(url,'koje','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=yes, width=700, height=450, top=150, left=200');
}
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

<div class="title"><?php print(get_string("insertimage","htmlarea"));?></div>

<form action="" method="get">
<table border="0" width="100%" style="padding: 0px; margin: 0px">
  <tbody>

  <tr>
    <td style="width: 7em; text-align: right"><?php print(get_string("imageurl","htmlarea"));?>:</td>
    <td><input type="text" name="url" id="f_url" style="width:75%"
      title="Enter the image URL here" />
      <button name="preview" onclick="return onPreview();"
      title="Preview the image in a new window"><?php print(get_string("preview","htmlarea"));?></button>
    </td>
  </tr>
  <tr>
    <td style="width: 7em; text-align: right"><?php print(get_string("alternatetext","htmlarea"));?>:</td>
    <td><input type="text" name="alt" id="f_alt" style="width:100%"
      title="For browsers that don't support images" /></td>
  </tr>

  </tbody>
</table>

<p />

<fieldset style="float: left; margin-left: 5px;">
<legend><?php print(get_string("layout","htmlarea"));?></legend>

<div class="space"></div>

<div class="fl"><?php print(get_string("alignment","htmlarea"));?>:</div>
<select size="1" name="align" id="f_align"
  title="Positioning of this image">
  <option value=""                             ><?php print(get_string("notset","htmlarea"));?></option>
  <option value="left"                         ><?php print(get_string("left","htmlarea"));?></option>
  <option value="right"                        ><?php print(get_string("right","htmlarea"));?></option>
  <option value="texttop"                      ><?php print(get_string("texttop","htmlarea"));?></option>
  <option value="absmiddle"                    ><?php print(get_string("absmiddle","htmlarea"));?></option>
  <option value="baseline" selected="1"        ><?php print(get_string("baseline","htmlarea"));?></option>
  <option value="absbottom"                    ><?php print(get_string("absbottom","htmlarea"));?></option>
  <option value="bottom"                       ><?php print(get_string("bottom","htmlarea"));?></option>
  <option value="middle"                       ><?php print(get_string("middle","htmlarea"));?></option>
  <option value="top"                          ><?php print(get_string("top","htmlarea"));?></option>
</select>

<p />

<div class="fl"><?php print(get_string("borderthickness","htmlarea"));?>:</div>
<input type="text" name="border" id="f_border" size="5"
title="Leave empty for no border" />

<div class="space"></div>

</fieldset>

<fieldset style="float:right; margin-right: 5px;">
<legend><?php print(get_string("spacing","htmlarea"));?></legend>

<div class="space"></div>

<div class="fr"><?php print(get_string("horizontal","htmlarea"));?>:</div>
<input type="text" name="horiz" id="f_horiz" size="5"
title="Horizontal padding" />

<p />

<div class="fr"><?php print(get_string("vertical","htmlarea"));?>:</div>
<input type="text" name="vert" id="f_vert" size="5"
title="Vertical padding" />

<div class="space"></div>

</fieldset>

<div style="margin-top: 85px; text-align: right;">
<hr />
<?php 
print(isteacher($id))?"<button title=\"$course->fullname\" type=\"button\" name=\"browse\" onclick=\"set_url_value()\">".get_string("browse","htmlarea")."</button>&nbsp;\n":"";
?>
<button type="button" name="ok" onclick="return onOK();"><?php print(get_string("ok","htmlarea"));?></button>
<button type="button" name="cancel" onclick="return onCancel();"><?php print(get_string("cancel","htmlarea"));?></button>
</div>

</form>

</body>
</html>
