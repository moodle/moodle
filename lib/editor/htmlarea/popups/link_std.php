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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php print_string("insertlink","editor");?></title>
  <script type="text/javascript" src="popup.js"></script>
  <script type="text/javascript">
//<![CDATA[
function onTargetChanged() {
  var f = document.getElementById("f_other_target");
  if (this.value == "_other") {
    f.style.visibility = "visible";
    f.select();
    f.focus();
  } else f.style.visibility = "hidden";
};
function Init() {
  //__dlg_translate(I18N);
  __dlg_init();

  var param = window.dialogArguments;
  if(param.f_anchors) {
      //anchors = param.f_anchors;
      var anchor = document.getElementById('f_anchors');
      for(var a in param.f_anchors) {
        var opti = document.createElement('option');
        opti.value = '#' + param.f_anchors[a];
        opti.innerHTML = opti.value;
        anchor.appendChild(opti);
    }
  }
  var target_select = document.getElementById("f_target");
  if (param.f_href) {
      document.getElementById("f_href").value = param["f_href"];
      document.getElementById("f_title").value = param["f_title"];
      //comboSelectValue(target_select, param["f_target"]);
      if (target_select.value != param.f_target) {
        var opt = document.createElement("option");
        opt.value = param.f_target;
        opt.innerHTML = opt.value;
        target_select.appendChild(opt);
        opt.selected = true;
      }
  } else {
      document.getElementById("f_href").value = "http://";
  }
  var opt = document.createElement("option");
  opt.value = "_other";
  opt.innerHTML = "<?php print_string("linktargetother","editor");?>";
  target_select.appendChild(opt);
  target_select.onchange = onTargetChanged;
  document.getElementById("f_href").focus();
  document.getElementById("f_href").select();
};

function onOK() {
  var required = {
    "f_href": "You must enter the URL where this link points to"
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
  var fields = ["f_href", "f_title", "f_target" ];
  var param = new Object();
  for (var i in fields) {
    var id = fields[i];
    var el = document.getElementById(id);
    param[id] = el.value;
  }
  if (param.f_target == "_other") {
    param.f_target = document.getElementById("f_other_target").value;
  }
  __dlg_close(param);
  return false;
};

function onCancel() {
  __dlg_close(null);
  return false;
};

function onBrowse() {
    var lx = (screen.width - 470) / 2;
    var tx = (screen.height - 400) / 2;

    var settings = "toolbar=no,";
    settings += " location=no,";
    settings += " directories=no,";
    settings += " status=no,";
    settings += " menubar=no,";
    settings += " scrollbars=no,";
    settings += " resizable=no,";
    settings += " width=470,";
    settings += " height=400,";

    var newwin = window.open("link.php?id=<?php echo $id; ?>","",""+ settings +" left="+ lx +", top="+ tx +"");
    return false;
}
function seturl() {
    var sel = document.getElementById('f_anchors');
    var txt = sel.options[sel.selectedIndex].text;
    if(txt != '----') {
        var f_url = document.getElementById('f_href');
        f_url.value = txt;
    }
}
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
table { font: 11px Tahoma,Verdana,sans-serif; }
select, input, button { font: 11px Tahoma,Verdana,sans-serif; }
button { width: 70px; }
table .label { text-align: right; width: 8em; }

.title { background: #ddf; color: #000; font-weight: bold; font-size: 120%; padding: 3px 10px; margin-bottom: 3px;
border-bottom: 1px solid black; letter-spacing: 2px;
}

#buttons {
      margin-top: 1em; border-top: 1px solid #999;
      padding: 2px; text-align: right;
}
</style>
</head>
<body onload="Init()">
<div class="title"><?php print_string("insertlink","editor");?></div>
<table border="0" style="width: 100%;">
  <tr>
    <td class="label"><?php print_string("linkurl","editor");?>:</td>
    <td><input type="text" id="f_href" style="width: 100%" /></td>
  </tr>
  <tr>
    <td class="label"><?php print_string("linktitle","editor");?>:</td>
    <td><input type="text" id="f_title" style="width: 100%" /></td>
  </tr>
  <tr>
    <td class="label"><?php print_string("linktarget","editor");?>:</td>
    <td><select id="f_target">
      <option value=""><?php print_string("linktargetnone","editor");?></option>
      <option value="_blank"><?php print_string("linktargetblank","editor");?></option>
      <option value="_self"><?php print_string("linktargetself","editor");?></option>
      <option value="_top"><?php print_string("linktargettop","editor");?></option>
    </select>
  <tr>
    <td class="label"><?php print_string("anchors","editor");?>:</td>
    <td><select id="f_anchors" onchange="seturl()">
    <option value="">----</option></select></td>
  </tr>
    <input type="text" name="f_other_target" id="f_other_target" size="10" style="visibility: hidden" />
    </td>
  </tr>
</table>

<div id="buttons">
  <?php if (has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $id))) {
            echo "<button type=\"button\" name=\"browse\" onclick=\"return onBrowse();\">".get_string("browse","editor")."...</button>";
        }
  ?>
  <button type="button" name="ok" onclick="return onOK();"><?php print_string("ok","editor");?></button>
  <button type="button" name="cancel" onclick="return onCancel();"><?php print_string("cancel","editor");?></button>
</div>

</body>
</html>
