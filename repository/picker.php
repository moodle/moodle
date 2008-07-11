<?php
/*******************************************************\

  This is page is deprecated, we are developing ajax
  File Picker in the future. This page won't work.

\*******************************************************/
require_once('../config.php');
require_once('lib.php');
$id        = required_param('id', PARAM_INT);
$action    = optional_param('action', '', PARAM_RAW);
if(!$repository = $DB->get_record('repository', array('id'=>$id))) {
    print_error('invalidrepostoryid');
}

if(is_file($CFG->dirroot.'/repository/'.$repository->repositorytype.'/repository.class.php')) {
    require_once($CFG->dirroot.'/repository/'.$repository->repositorytype.'/repository.class.php');
    $classname = 'repository_' . $repository->repositorytype;
    $repo = new $classname($id, SITEID);
} else {
    print_error('invalidplugin', 'repository');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="content-type"/>
    <title>File Picker</title>
    <link href="style.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<h1>This page is deprecated, please access ajax.php.</h1>
    <table border="0" cellspacing="10" cellpadding="10">
    <tbody>
        <tr>
        <td>
            <h3>Repository</h3>
            <ul id='repolist'>
                <li><a href="###" class="link">Local Server</a></li>
                <li><a href="###">Remote Moodle</a></li>
                <li class='line'>------------------------------</li>
                <li><a href="?id=1">Box.net</a></li>
                <li><a href="?id=2">Flickr</a></li>
                <li><a href="###"><strike>Briefcase</strike></a></li>
                <li><a href="###"><strike>Door</strike></a></li>
                <li><a href="###"><strike>Google Docs</strike></a></li>
                <li><a href="###"><strike>Mahara</strike></a></li>
                <li><a href="###"><strike>Merlot</strike></a></li>
                <li><a href="###"><strike>Myspace</strike></a></li>
                <li><a href="###"><strike>Oki</strike></a></li>
                <li><a href="###"><strike>Skydrive</strike></a></li>
                <li><a href="###"><strike>Youtube</strike></a></li>
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
                <?php
                $repo->print_search();
                ?>
            </td>
        </tr>
        </table>
        <div>
        <?php
            if($action == 'list') {
                $repo->print_listing();
            } else {
                $repo->print_login();
            }
        ?>
        </div>
        <!--
        <iframe src="ibrowse.php" width="100%" height='250px' class="frame"></iframe>
        <div class="right">
        <input type="submit" value="Select" name="select"  />
        &nbsp;&nbsp;
        <input type="submit" value="Cancel" name="cancel"/>
        </div>
        -->
        </td>
        </tr>
    </tbody>
    </table>
</body>
</html>
