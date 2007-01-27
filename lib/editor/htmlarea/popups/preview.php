<?php // $Id$ preview for insert image dialog

    require("../../../../config.php");

    $id = optional_param('id', SITEID, PARAM_INT);
    $imageurl = required_param('imageurl', PARAM_RAW);

    require_login($id);
    require_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $id));

    @header('Content-Type: text/html; charset=utf-8');


    $imagetag = clean_text('<img src="'.htmlSpecialChars(stripslashes_safe($imageurl)).'" alt="" />');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php echo get_string('preview') ?></title>
<style type="text/css">
 body { margin: 2px; }
</style>
</head>
<body bgcolor="#ffffff">

<?php echo $imagetag ?>

</body>
</html>
