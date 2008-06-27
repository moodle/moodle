<?php
require_once('../config.php');
require_once('lib.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="content-type">
    <title>File Picker</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body class="frame">
    <div id='nav'>
        <a href="###">Root</a> &gt; <a href="###">&nbsp;Folder 1</a> &gt; <a href="###">&nbsp;Sub-folder 1_1</a>
    </div>
<table border="0">
<tbody>
    <tr>
    <td class="content" colspan="4"><img src="<?php echo $CFG->pixpath.'/f/folder.gif';?>" alt="folder"/> ..</td>
    </tr>

    <tr>
    <td class="content"><input type="radio" name="file" value="" /></td>
    <td class="content">basket_readme.avi</td>
    <td class="content">8 Kb</td>
    <td class="content">22/05/2008 4:45PM</td>
    </tr>

    <tr>
    <td class="content"><input type="radio" name="file" value="" /></td>
    <td class="content">readme.doc</td>
    <td class="content">2 Kb</td>
    <td class="content">22/05/2008 4:45PM</td>
    </tr>

    <tr>
    <td class="content"><input type="radio" name="file" value="" /></td>
    <td class="content">moodle_readme.txt</td>
    <td class="content">2 Kb</td>
    <td class="content">22/05/2008 4:45PM</td>
    </tr>
</tbody>
</table>
</body>
</html>
