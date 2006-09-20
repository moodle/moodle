<?php // $Id$ preview for insert image dialog

    include('../../../../config.php');

    $id       = required_param('id', PARAM_INT);
    $imageurl = required_param('imageurl', PARAM_RAW);

    if (! $course = get_record('course', 'id', $id) ) {
        error("That's an invalid course id");
    }

    require_login($course->id);
    require_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $course->id));

    $imagetag = clean_text('<img src="'.htmlSpecialChars(stripslashes_safe($imageurl)).'" alt="" />');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Preview</title>
<style type="text/css">
 body { margin: 2px; }
</style>
</head>
<body bgcolor="#ffffff">

<?php echo $imagetag ?>

</body>
</html>
