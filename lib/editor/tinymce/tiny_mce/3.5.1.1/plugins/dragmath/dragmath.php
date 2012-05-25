<?php

define('NO_MOODLE_COOKIES', true);

require("../../../../../../../config.php");

$lang = required_param('elanguage', PARAM_SAFEDIR);

if (!get_string_manager()->translation_exists($lang, false)) {
    $lang = 'en';
}
$SESSION->lang = $lang;

$langmapping = array('cs'=>'cz', 'pt_br'=>'pt-br');

// fix non-standard lang names
if (array_key_exists($lang, $langmapping)) {
    $lang = $langmapping[$lang];
}

if (!file_exists("$CFG->dirroot/lib/dragmath/applet/lang/$lang.xml")) {
    $lang = 'en';
}

@header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print_string('dragmath:dragmath_title', 'editor_tinymce')?></title>
<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
<script type="text/javascript" src="js/dragmath.js"></script>
</head>
<body>

<applet
	name="dragmath"
	codebase="<?php echo $CFG->httpswwwroot.'/lib/dragmath/applet' ?>"
	code="Display/MainApplet.class"
	archive="DragMath.jar,lib/AbsoluteLayout.jar,lib/swing-layout-1.0.jar,lib/jdom.jar,lib/jep.jar"
	width="540" height="300"
>
	<param name="language" value="<?php echo $lang; ?>" />
	<param name="outputFormat" value="MoodleTex" />
    <?php print_string('dragmath:dragmath_javaneeded', 'editor_tinymce', '<a href="http://www.java.com">Java.com</a>')?>
</applet>
<form name="form" action="">
	<div>
	<button type="button" onclick="return DragMathDialog.insert();"><?php print_string('common:insert', 'editor_tinymce'); ?></button>
	<button type="button" onclick="return tinyMCEPopup.close();"><?php print_string('cancel'); ?></button>
	</div>
</form>

</body>
</html>
