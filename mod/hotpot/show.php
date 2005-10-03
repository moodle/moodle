<?php // $Id$

	require_once("../../config.php");
	require_once("lib.php");

	require_login();

	// fetch and clean the required $_GET parameters
	// (script stops here if any parameters are missing)
	unset($params);
	$params->action = required_param('action');
	$params->course = required_param('course');
	$params->reference = required_param('reference');

	require_login($params->course);

	if (!isteacher($params->course)) {
		error("You are not allowed to view this page!");
	}

	if (isadmin()) {
		$params->location = optional_param('location', HOTPOT_LOCATION_COURSEFILES);
	} else {
		$params->location = HOTPOT_LOCATION_COURSEFILES;
	}

	$title = get_string($params->action, 'hotpot').': '.$params->reference;
	print_header($title, $title);

	hotpot_print_show_links($params->course, $params->location, $params->reference);
?>
<script type="text/javascript" language="javascript">
<!--
	// http://www.krikkit.net/howto_javascript_copy_clipboard.html

	function copy_contents(id) {
		if (id==null) {
			id = 'contents';
		}
		var obj = null;
		if (document.getElementById) {
			obj = document.getElementById(id);
		}
		if (obj && window.clipboardData) {
			window.clipboardData.setData("Text", obj.innerText);
			alert('<? print_string('copiedtoclipboard', 'hotpot') ?>');
		}
	}
	document.write('<span class="helplink"> &nbsp; <a href="javascript:copy_contents()"><? print_string('copytoclipboard', 'hotpot') ?></A></span>');
-->
</script>
<?php
	print_simple_box_start("center", "96%");
	if($hp = new hotpot_xml_quiz($params)) {
		print '<pre id="contents">';
		switch ($params->action) {
			case 'showxmlsource':
				print htmlspecialchars($hp->source);
				break;
			case 'showxmltree':
				print_r($hp->xml);
				break;
			case 'showhtmlsource':
				print htmlspecialchars($hp->html);
				break;
			case 'showhtmlquiz':
				print $hp->html;
				break;
		}
		print '</pre>';
	} else {
		print_simple_box("Could not open Hot Potatoes XML file", "center", "", "#FFBBBB");
	}

	print_simple_box_end();
	print '<br />';
	close_window_button();
?>
