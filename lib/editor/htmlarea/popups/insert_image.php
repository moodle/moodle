<?php // $Id$

    require("../../../../config.php");

    $id = optional_param('id', SITEID, PARAM_INT);

    require_login($id);
    require_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $id));

    @header('Content-Type: text/html; charset=utf-8');

    $upload_max_filesize = get_max_upload_file_size($CFG->maxbytes);

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
      document.getElementById("f_align").value = param["f_align"];
      document.getElementById("f_vert").value = param["f_vert"] != -1 ? param["f_vert"] : 0;
      document.getElementById("f_horiz").value = param["f_horiz"] != -1 ? param["f_horiz"] : 0;
      document.getElementById("f_width").value = param["f_width"];
      document.getElementById("f_height").value = param["f_height"];
      window.ipreview.location.replace('preview.php?id='+ <?php print($id);?> +'&imageurl='+ param.f_url);
  }
  document.getElementById("f_url").focus();
};

function onOK() {
  var required = {
    "f_url": "<?php print_string("mustenterurl", "editor");?>",
    "f_alt": "<?php print_string("pleaseenteralt", "editor");?>"
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
                "f_horiz", "f_vert","f_width","f_height"];
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
        window.ibrowser.document.dirform.action.value = "delete";
    }
    if(dothis == "move") {
        window.ibrowser.document.dirform.action.value = "move";
    }
    if(dothis == "zip") {
        window.ibrowser.document.dirform.action.value = "zip";
    }

    window.ibrowser.document.dirform.submit();
    return false;
}

//]]>
</script>
<style type="text/css">
html, body {
margin: 2px;
background-color: rgb(212,208,200);
font-family: Tahoma, Verdana, sans-serif;
font-size: 11px;
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
td, input, select, button {
font-family: Tahoma, Verdana, sans-serif;
font-size: 11px;
}
button { width: 70px; }
.space { padding: 2px; }
form { margin-bottom: 0px; margin-top: 0px; }
</style>
</head>
<body onload="Init()">
  <div class="title"><?php print_string("insertimage","editor");?></div>
  <div class="space"></div>
  <div class="space"></div>
  <div class="space"></div>
  <form action="" method="get" id="first">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="15%" align="right"><?php print_string("imageurl","editor");?>:</td>
        <td width="60%"><input name="f_url" type="text" id="f_url" style="width: 100%;" /></td>
        <td width="23%" align="center">
          <button name="btnOK" type="button" id="btnOK" onclick="return onOK();"><?php print_string("ok","editor") ?></button></td>
      </tr>
      <tr>
        <td align="right"><?php print_string("alternatetext","editor");?>:</td>
        <td><input name="f_alt" type="text" id="f_alt" style="width: 100%;" /></td>
        <td align="center">
          <button name="btnCancel" type="button" id="btnCancel" onclick="return onCancel();"><?php print_string("cancel","editor") ?></button></td>
      </tr>
    </table>
    <div class="space"></div>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="32%" valign="top">
        <fieldset><legend><?php print_string("layout","editor");?></legend>
        <div class="space"></div>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="40%" align="right"><?php print_string("alignment","editor");?>:</td>
              <td width="60%">
                <select size="1" name="f_align" id="f_align"
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
                </select></td>
      </tr>
      <tr>
              <td width="40%" align="right"><?php print_string("borderthickness","editor") ?>:</td>
              <td width="60%">
                <input type="text" id="f_border" name="f_border" size="5" /></td>
            </tr>
        </table><div class="space"></div>
        </fieldset>&nbsp;</td>
        <td width="34%" valign="top">
        <fieldset><legend><?php print_string("spacing","editor");?></legend>
        <div class="space"></div>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="40%" align="right"><?php print_string("horizontal","editor");?>: </td>
              <td width="60%">
                <input name="f_horiz" type="text" id="f_horiz" size="5" />
              </td>
            </tr>
            <tr>
              <td width="40%" align="right"><?php print_string("vertical","editor");?>:</td>
              <td width="60%">
                <input name="f_vert" type="text" id="f_vert" size="5" /></td>
            </tr>
          </table><div class="space"></div></fieldset></td>
        <td width="34%" valign="top">
        <fieldset><legend><?php print_string("size","editor");?></legend>
        <div class="space"></div>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="40%" align="right"><?php print_string("width","editor");?>:</td>
              <td width="60%">
                <input name="f_width" type="text" id="f_width" size="5" />
              </td>
            </tr>
            <tr>
              <td width="40%" align="right"><?php print_string("height","editor");?>:</td>
              <td width="60%">
                <input name="f_height" type="text" id="f_height" size="5" /></td>
            </tr>
            </table><div class="space"></div>
            </fieldset></td>
    </tr>
  </table></form>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="55%" valign="top"><?php
          print_string("filebrowser","editor");
          echo "<br />";
          echo "<iframe id=\"ibrowser\" name=\"ibrowser\" src=\"{$CFG->wwwroot}/lib/editor/htmlarea/coursefiles.php?usecheckboxes=1&id=$id\" style=\"width: 100%; height: 200px;\"></iframe>";
      ?>
      </td>
      <td width="45%" valign="top"><?php print_string("preview","editor");?>:<br />
      <iframe id="ipreview" name="ipreview" src="<?php echo $url ?>blank.html" style="width: 100%; height: 200px;"></iframe>
      </td>
    </tr>
  </table>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="55%"><div class="space"></div>
        <?php if(has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $id))) { ?>
        <table border="0" cellpadding="2" cellspacing="0">
          <tr><td><?php print_string("selection","editor");?>: </td>
          <td><form id="idelete">
          <input name="btnDelete" type="submit" id="btnDelete" value="<?php print_string("delete","editor");?>" onclick="return submit_form('delete');" /></form></td>
          <td><form id="imove">
          <input name="btnMove" type="submit" id="btnMove" value="<?php print_string("move","editor");?>" onclick="return submit_form('move');" /></td>
          <td><form id="izip">
          <input name="btnZip" type="submit" id="btnZip" value="<?php print_string("zip","editor");?>" onclick="return submit_form('zip');" /></form></td>
          <td><form method="post" action="../coursefiles.php" target="ibrowser">
          <input type="hidden" name="id" value="<?php print($id);?>" />
          <input type="hidden" name="wdir" value="" />
          <input type="hidden" id="irename" name="file" value="" />
          <input type="hidden" name="action" value="rename" />
          <input type="hidden" name="sesskey" value="<?php p($USER->sesskey) ?>" />
          <input name="btnRename" type="submit" id="btnRename" value="<?php print_string("rename","editor");?>" /></form></td>
          <tr></table>
          <br />
          <?php
          } else {
              print "";
          } ?>
        </td>
      <td width="45%" rowspan="2" valign="top"><fieldset>
          <legend><?php print_string("properties","editor");?></legend>
          <div class="space"></div>
          <div class="space"></div>
          &nbsp;&nbsp;<?php print_string("size","editor");?>:
          <input type="text" id="isize" name="isize" size="10" style="background: transparent; border: none;" />
      <?php print_string("type","editor");?>: <input type="text" id="itype" name="itype" size="10" style="background: transparent; border: none;" />
      <div class="space"></div>
      <div class="space"></div>
      </fieldset></td>
    </tr>
    <tr>
      <td height="22">
          <form id="cfolder" action="../coursefiles.php" method="post" target="ibrowser">
          <input type="hidden" name="id" value="<?php print($id);?>" />
          <input type="hidden" name="wdir" value="" />
          <input type="hidden" name="action" value="mkdir" />
          <input type="hidden" name="sesskey" value="<?php p($USER->sesskey) ?>" />
          <input name="name" type="text" id="foldername" size="35" />
          <input name="btnCfolder" type="submit" id="btnCfolder" value="<?php print_string("createfolder","editor");?>" onclick="return checkvalue('foldername','cfolder');" />
          </form>
          <div class="space"></div>
          <form action="../coursefiles.php?id=<?php print($id);?>" method="post" enctype="multipart/form-data" target="ibrowser" id="uploader">
          <input type="hidden" name="MAX_FILE_SIZE" value="<?php print($upload_max_filesize);?>" />
          <input type="hidden" name="id" VALUE="<?php print($id);?>" />
          <input type="hidden" name="wdir" value="" />
          <input type="hidden" name="action" value="upload" />
          <input type="hidden" name="sesskey" value="<?php p($USER->sesskey) ?>" />
          <input type="file" name="userfile" id="userfile" size="35" />
          <input name="save" type="submit" id="save" onclick="return checkvalue('userfile','uploader');" value="<?php print_string("upload","editor");?>" />
          </form>
      </td>
    </tr>
  </table>
  <p>&nbsp;</p>
</body>
</html>
