<?php
require_once('../config.php');
require_once('lib.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta content="text/html; charset=UTF-8" http-equiv="content-type"/>
<title>File Picker</title>
<link href="style.css" rel="stylesheet" type="text/css"/>
</head>
<body>
    <table border="0" cellspacing="10" cellpadding="10">
    <tbody>
        <tr>
        <td>
            <h3>Repository</h3>
            <ul id='repolist'>
                <li><a href="###" class="link">Local Server</a></li>
                <li><a href="###">Remote Moodle</a></li>
                <li class='line'>------------------------------</li>
                <li><a href="?repo=boxnet">Box.net</a></li>
                <li><a href="###">Briefcase</a></li>
                <li><a href="###">Door</a></li>
                <li><a href="###">Flickr</a></li>
                <li><a href="###">Google Docs</a></li>
                <li><a href="###">Mahara</a></li>
                <li><a href="###">Merlot</a></li>
                <li><a href="###">Myspace</a></li>
                <li><a href="###">Oki</a></li>
                <li><a href="###">Skydrive</a></li>
                <li><a href="###">Youtube</a></li>
                <li class='line'>------------------------------</li>
            </ul>
        </td>
        <td>
        <table>
        <tr>
            <td class="header">
                <img src="<?php echo $CFG->pixpath.'/moodlelogo.gif';?>" alt="Manage Google Docs" />
            </td>
            <td class="header">
                <input type="text" name="Search" value="search terms..." size="40" class="right"/>
            </td>
        </tr>
        </table>
        <iframe src="ibrowse.php" width="100%" height='250px' class="frame"></iframe>
        <div class="right">
        <input type="submit" value="Select" name="select"  />
        &nbsp;&nbsp;
        <input type="submit" value="Cancel" name="cancel"/>
        </div>
        </td>
        </tr>
    </tbody>
    </table>
</body>
</html>
