<?php // $Id$

	require_once("../../config.php");
	require_once("lib.php");

	require_login();

	// check variables are all there
	if (isset($_GET['course']) && isset($_GET['reference']) && isset($_GET['action'])) {

		require_login($_GET['course']);
		
		if (!isteacher($_GET['course'])) {
			error("You are not allowed to view this page!");
		}
	
		// decode the reference (not usually necessary)
		$_GET['reference'] = urldecode($_GET['reference']);

		if (isadmin()) {
			$_GET['location'] = nvl($_GET['location'], HOTPOT_LOCATION_COURSEFILES);
		} else {
			$_GET['location'] = HOTPOT_LOCATION_COURSEFILES;
		}			

		$title = get_string($_GET['action'], 'hotpot').': '.$_GET['reference'];
		print_header($title, $title);		

		hotpot_print_show_links($_GET['course'], $_GET['location'], $_GET['reference']);
?>
<SCRIPT>
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
			alert('The contents of this page have been copied to the clipboard');
		}
	}
	document.write('<FONT size="1"> &nbsp; <A href="javascript:copy_contents()">Copy to Clipboard</A></FONT>');
-->
</SCRIPT>
<?php
		print_simple_box_start("center", "96%");
		if($hp = new hotpot_xml_quiz($_GET)) {
			print '<PRE id="contents">';
			switch ($_GET['action']) {
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
			print '</PRE>';
		} else {
			print_simple_box("Could not open Hot Potatoes XML file", "center", "", "#FFBBBB");
		}

		print_simple_box_end();
		print '<BR>';
		close_window_button();

    } else { // no form data given
        error("This script was called incorrectly");
	}
?>
