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
window.resizeTo(700, 460);

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

function indexFrom() {
    var set_url = document.getElementById('findex');
    var url = set_url.value;
    window.fbrowser.location.replace(url);
    
    var resetme = document.forms['mainform'];
    resetme.fcreated.value = "";
    resetme.ftype.value = "";
    resetme.fsize.value = "";
    return false;
};
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
input,select {
font-family: Tahoma, sans-serif;
font-size: 11px;
}
legend {
font-family: Tahoma, sans-serif;
font-size: 11px;
}
p {
margin-left: 10px;
background-color: transparent;
font-family: Tahoma, sans-serif;
font-size: 11px;
color: black;
}
td {
font-family: Tahoma, sans-serif;
font-size: 11px;
}
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
		<table width="82%">
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
<p>&nbsp;</p>
</body>
</html>
