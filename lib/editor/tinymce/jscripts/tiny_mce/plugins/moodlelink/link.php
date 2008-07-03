<?php // $Id$
    require("../../../../../../../config.php");

    $id = optional_param('id', SITEID, PARAM_INT);

    require_course_login($id);
    @header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php print_string("insertlink","editor");?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
<script type="text/javascript" src="js/moodlelink.js"></script>
<script type="text/javascript">
var FileBrowserDialogue = {
    init : function () {
        // Here goes your code for setting your custom things onLoad.
    },
    mySubmit : function (link_url) {
        //call this function only after page has loaded
        //otherwise tinyMCEPopup.close will close the
        //"Insert/Edit Image" or "Insert/Edit Link" window instead

       //var URL = document.my_form.my_field.value;
       var win = tinyMCEPopup.getWindowArg("window");

       // insert information now
       win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = link_url;

       // for image browsers: update image dimensions
       if (win.getImageData) win.getImageData();

       // close popup window
       tinyMCEPopup.close();
    }
}

tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);


//<![CDATA[

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
//]]>
</script>
<style type="text/css">
html, body {
margin: 2px;
background-color: #F0F0EE;
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
<body>
  <table width="450" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td width="450" valign="top"><fieldset>
        <legend><?php
        if(has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $id))) {
            print_string("filebrowser","editor");
        } else {
            print "";
        }?></legend>

        <div class="space"></div>
        <?php print(has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $id)))?
        "<iframe id=\"fbrowser\" name=\"fbrowser\" src=\"../../../../coursefiles.php?id=".$id."\" width=\"420\" height=\"180\"></iframe>":
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
          <td><form id="idelete">
          <input name="btnDelete" type="submit" id="btnDelete" value="<?php print_string("delete","editor");?>" onclick="return submit_form('delete');" /></form></td>
          <td><form  id="imove">
          <input name="btnMove" type="submit" id="btnMove" value="<?php print_string("move","editor");?>" onclick="return submit_form('move');" /></form></td>
          <td><form id="izip">
          <input name="btnZip" type="submit" id="btnZip" value="<?php print_string("zip","editor");?>" onclick="return submit_form('zip');" /></form></td>
          <td><form id="irename" method="post" action="../../../../coursefiles.php" target="fbrowser">
          <input type="hidden" name="id" value="<?php print($id);?>" />
          <input type="hidden" name="wdir" value="" />
          <input type="hidden" name="file" value="" />
          <input type="hidden" name="action" value="rename" />
          <input type="hidden" name="sesskey" value="<?php p($USER->sesskey) ?>" />
          <input name="btnRename" type="submit" id="btnRename" value="<?php print_string("rename","editor");?>" /></form></td>
          </tr>
    </table>
  </td>
  <td>
    <button type="button" name="close" onclick="tinyMCEPopup.close();"><?php print_string("close","editor");?></button>
  </td>
  </tr>
  </table>
    <table border="0" cellpadding="1" cellspacing="1">
    <tr>
      <td height="22"><?php
      if(has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $id))) { ?>
          <form id="cfolder" action="../../../../coursefiles.php" method="post" target="fbrowser">
          <input type="hidden" name="id" value="<?php print($id);?>" />
          <input type="hidden" name="wdir" value="" />
          <input type="hidden" name="action" value="mkdir" />
          <input type="hidden" name="sesskey" value="<?php p($USER->sesskey) ?>" />
          <input name="name" type="text" id="foldername" size="35" />
          <input name="btnCfolder" type="submit" id="btnCfolder" value="<?php print_string("createfolder","editor");?>" onclick="return checkvalue('foldername','cfolder');" />
          </form>
          <form action="../../../../coursefiles.php?id=<?php print($id);?>" method="post" enctype="multipart/form-data" target="fbrowser" id="uploader">
          <input type="hidden" name="MAX_FILE_SIZE" value="<?php print($upload_max_filesize);?>" />
          <input type="hidden" name="id" VALUE="<?php print($id);?>" />
          <input type="hidden" name="wdir" value="" />
          <input type="hidden" name="action" value="upload" />
          <input type="hidden" name="sesskey" value="<?php p($USER->sesskey) ?>" />
          <input type="file" name="userfile" id="userfile" size="35" />
          <input name="save" type="submit" id="save" onclick="return checkvalue('userfile','uploader');" value="<?php print_string("upload","editor");?>" />
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
