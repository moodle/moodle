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
<script language="JavaScript" type="text/javascript">

function onCancel() {
  window.close();
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
html, body { background-color: rgb(212,208,200); }
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
p { margin-left: 10px;
background-color: transparent; font-family: Tahoma, sans-serif;
font-size: 11px; color: black; }
td { font-family: Tahoma, sans-serif; font-size: 11px; }
button { width: 70px; font-family: Tahoma, sans-serif; font-size: 11px; }
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

<body>
<div class="title"><?php print_string("insertlink","editor");?></div>
  <table width="450" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td width="450" valign="top"><fieldset>
        <legend><?php
        if(isteacher($id)) {
            print_string("filebrowser","editor");
        } else {
            print "";
        }?></legend>

        <div class="space"></div>
        <?php print(isteacher($id))?
        "<iframe id=\"fbrowser\" name=\"fbrowser\" src=\"../coursefiles.php?id=".$course->id."\" width=\"420\" height=\"180\"></iframe>":
        ""; ?>
        <p>
        </p>
        <div class="space"></div>
        </fieldset>&nbsp;</td>
    </tr>
  </table>
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
  <td>
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
          </tr>
    </table>
  </td>
  <td>
    <button type="button" name="close" onclick="return onCancel();"><?php print_string("close","editor");?></button>
  </td>
  </tr>
  </table>
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
