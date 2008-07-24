<?php

#################################################################################
##
## $Id$
##
#################################################################################

    require("../../../../../../../config.php");

    $id = optional_param('id', SITEID, PARAM_INT);

    require_course_login($id);
    @header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>"DragMath Equation Editor</title>
<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
<script type="text/javascript" src="js/dragmath.js"></script>
</head>
<body>

<applet 
	name="dragmath" 
	codebase="<?php echo $CFG->httpswwwroot.'/lib/editor/common/dragmath/applet/classes' ?>" 
	code="Display/MainApplet.class" 
	archive="Project.jar,AbsoluteLayout.jar,swing-layout-1.0.jar,jdom.jar,jep.jar" 
	width=540 height=300
>
	<param name=language value="en">
	<param name=outputFormat value="Latex">
	<param name=showOutputToolBar value="false">
	To use this page you need a Java-enabled browser. 
	Download the latest Java plug-in from 
	<a> href="http://www.java.com">Java.com</a>
</applet >
<form name="form">
	<div>
	<button type="button" onclick="return DragMathDialog.insert();">Insert</button>
	<button type="button" onclick="return tinyMCEPopup.close();">Cancel</button>
	</div>
</form>

</body>
</html>
