<?php // $Id$
    include("../../../config.php");

    $id = $_GET['id'];

    require_variable($id);

    if (!$course = get_record("course", "id", $id)) {
        $course->fullname = "";   // Just to keep display happy, though browsing may fail
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php print_string("insertlink","editor");?></title>
<meta http-equiv="Content-Type" content="text/html; <?php print_string("thischarset");?>">
<script type="text/javascript" src="popup.js"></script>
<script language="JavaScript" type="text/javascript">
//I18N = window.opener.HTMLArea.I18N.dialogs;

// function i18n(str) {
//  return (I18N[str] || str);
// };

function onTargetChanged() {
/*
  // commented out since it does not work!!!
  var f = document.getElementById("f_other_target");
  if (this.value == "_other") {
    f.style.visibility = "visible";
    f.select();
    f.focus();
  } else f.style.visibility = "hidden";
*/};

function Init() {
  //__dlg_translate(I18N);
  __dlg_init();
  var param = window.dialogArguments;
  var target_select = document.getElementById("f_target");
  if (param) {
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
  window.focus();
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
  if (param.f_target == "_other")
    param.f_target = document.getElementById("f_other_target").value;
  __dlg_close(param);
  return false;
};

function onCancel() {
  __dlg_close(null);
  return false;
};

function checkvalue(elm,formname) {
    var el = document.getElementById(elm);
    if(!el.value) {
        alert("Nothing to do!");
        el.focus();
        return false;
    }
}

function submit_form(dothis) {
    if(dothis == "delete") {
        window.fbrowser.document.dirform.action.value = "delete";
    }
    if(dothis == "move") {
        window.fbrowser.document.dirform.action.value = "move";
    }
    if(dothis == "zip") {
        window.fbrowser.document.dirform.action.value = "zip";
    }

    window.fbrowser.document.dirform.submit();
    return false;
}

</script>
<style type="text/css">
html, body {
width: 700;
height: 460;
background-color: rgb(212,208,200);
}
.title {
background-color: #ddddff;
padding: 5px;
border-bottom: 1px solid black;
font-family: Tahoma, sans-serif;
font-weight: bold;
font-size: 14px;
color: black;
}
input,select { font-family: Tahoma, sans-serif; font-size: 11px; }
legend { font-family: Tahoma, sans-serif; font-size: 11px; }
p {
margin-left: 10px;
background-color: transparent;
font-family: Tahoma, sans-serif;
font-size: 11px;
color: black;
}
td { font-family: Tahoma, sans-serif; font-size: 11px; }
button {
width: 70px;
font-family: Tahoma, sans-serif;
font-size: 11px;
}
#imodified,#itype,#isize {
background-color: rgb(212,208,200);
border: none;
font-family: Tahoma, sans-serif;
font-size: 11px;
color: black;
}
.space { padding: 2px; }
form { margin-bottom: 1px; margin-top: 1px; }
</style>
</head>

<body onload="Init()">
<div class="title"><?php print_string("insertlink","editor");?></div>
  <table width="660" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td width="380" valign="top"><fieldset>
        <legend><?php
        if(isteacher($id)) {
            print_string("filebrowser","editor");
        } else {
            print "";
        }?></legend>

        <div class="space"></div>
        <?php print(isteacher($id))?
        "<iframe id=\"fbrowser\" name=\"fbrowser\" src=\"../coursefiles.php?id=".$course->id."\" width=\"360\" height=\"260\"></iframe>":
        ""; ?>
        <p>
        </p>
        <div class="space"></div>
        </fieldset>&nbsp;</td>
      <td width="300" valign="top">
      <form name="mainform">
      <fieldset>
        <legend><?php print_string("properties","editor");?></legend>
        <div class="space"></div>
        <table width="298" border="0">
        <tr>
            <td width="35" align="right"><?php print_string("modified");?>:</td>
            <td align="left"><input id="imodified" type="text" name="imodified" size="40"></td>
        </tr>
          <tr>
            <td width="35" align="right"><?php print_string("type","editor");?>:</td>
            <td align="left"><input id="itype" type="text" name="itype" size="40"></td>
        </tr>
        <tr>
            <td width="35" align="right"><?php print_string("size","editor");?>:</td>
            <td align="left"><input id="isize" type="text" name="isize" size="40"></td>
        </tr>
        </table>
        <br>
        </fieldset>
        <fieldset><legend><?php print_string("linkproperties","editor");?></legend>
        <br>
        <table width="82%" border="0">
        <tr>
            <td width="35" align="right"><?php print_string("linkurl","editor");?>:</td>
            <td><input id="f_href" type="text" name="f_href" size="40"></td>
        </tr>
        <tr>
            <td width="35" align="right"><?php print_string("linktitle","editor");?>:</td>
            <td><input id="f_title" type="text" name="f_title" size="40"></td>
        </tr>
        <tr>
            <td width="35" align="right"><?php print_string("linktarget","editor");?>:</td>
            <td><select id="f_target" name="f_target">
            <option value=""><?php print_string("linktargetnone","editor");?></option>
            <option value="_blank"><?php print_string("linktargetblank","editor");?></option>
            <option value="_self"><?php print_string("linktargetself","editor");?></option>
            <option value="_top"><?php print_string("linktargettop","editor");?></option>
            </select></td>
        </tr>
        </table>
        <div class="space"></div>
         <table width="78%" border="0" cellspacing="0" cellpadding="2">
        <tr>
            <td align="right" valign="middle"><button name="btnOk" onclick="return onOK();"><?php print_string("ok","editor");?></button>&nbsp;
            <button name="btnCancel" onclick="return onCancel();"><?php print_string("cancel","editor");?></button></td>
        </tr>
        </table>
        <div class="space"></div>
        </fieldset>
      </form>
      </td>
    </tr>
  </table>
  <table border="0" cellpadding="2" cellspacing="0">
          <tr><td><?php print_string("selection","editor");?>: </td>
          <td><form name="idelete" id="idelete">
          <input name="btnDelete" type="submit" id="btnDelete" value="<?php print_string("delete","editor");?>" onclick="return submit_form('delete');"></form></td>
          <td><form name="imove" id="imove">
          <input name="btnMove" type="submit" id="btnMove" value="<?php print_string("move","editor");?>" onclick="return submit_form('move');"></form></td>
          <td><form name="izip" id="izip">
          <input name="btnZip" type="submit" id="btnZip" value="<?php print_string("zip","editor");?>" onclick="return submit_form('zip');"></form></td>
          <td><form name="irename" id="irename" method="post" action="../coursefiles.php" target="fbrowser">
          <input type="hidden" name="id" value="<?php print($course->id);?>">
          <input type="hidden" name="wdir" value="">
          <input type="hidden" name="file" value="">
          <input type="hidden" name="action" value="rename">
          <input name="btnRename" type="submit" id="btnRename" value="<?php print_string("rename","editor");?>"></form></td>
          </tr></table>
    <table border="0" cellpadding="1" cellspacing="1">
    <tr>
      <td height="22"><?php
      if(isteacher($id)) { ?>
          <form name="cfolder" id="cfolder" action="../coursefiles.php" method="post" target="fbrowser">
          <input type="hidden" name="id" value="<?php print($course->id);?>">
          <input type="hidden" name="wdir" value="">
          <input type="hidden" name="action" value="mkdir">
          <input name="name" type="text" id="foldername" size="35">
          <input name="btnCfolder" type="submit" id="btnCfolder" value="<?php print_string("createfolder","editor");?>" onclick="return checkvalue('foldername','cfolder');">
          </form>
          <form action="../coursefiles.php?id=<?php print($course->id);?>" method="post" enctype="multipart/form-data" name="uploader" target="fbrowser" id="uploader">
          <input type="hidden" name="MAX_FILE_SIZE" value="<?php print($upload_max_filesize);?>">
          <input type="hidden" name="id" VALUE="<?php print($course->id);?>">
          <input type="hidden" name="wdir" value="">
          <input type="hidden" name="action" value="upload">
          <input type="file" name="userfile" id="userfile" size="35">
          <input name="save" type="submit" id="save" onclick="return checkvalue('userfile','uploader');" value="<?php print_string("upload","editor");?>">
          </form>
          <?php
          } else {
              print "";
          } ?>
          </td>
    </tr>
  </table>
<p>&nbsp;</p>
</body>
</html>
