// I18N constants

// LANG: "en", ENCODING: UTF-8 | ISO-8859-1
// Author: Mihai Bazon, <mishoo@infoiasi.ro>

// FOR TRANSLATORS:
//
//   1. PLEASE PUT YOUR CONTACT INFO IN THE ABOVE LINE
//      (at least a valid email address)
//
//   2. PLEASE TRY TO USE UTF-8 FOR ENCODING;
//      (if this is not possible, please include a comment
//       that states what encoding is necessary.)
<?php
	include("../../../config.php");
	
?>
HTMLArea.I18N = {

	// the following should be the filename without .js extension
	// it will be used for automatically load plugin language.
	lang: "en",

	tooltips: {
		bold:           "<?php print_string("bold","editor") ?>",
		italic:         "<?php print_string("italic","editor") ?>",
		underline:      "<?php print_string("underline","editor") ?>",
		strikethrough:  "<?php print_string("strikethrough","editor") ?>",
		subscript:      "<?php print_string("subscript","editor") ?>",
		superscript:    "<?php print_string("superscript","editor") ?>",
		justifyleft:    "<?php print_string("justifyleft","editor") ?>",
		justifycenter:  "<?php print_string("justifycenter","editor") ?>",
		justifyright:   "<?php print_string("justifyright","editor") ?>",
		justifyfull:    "<?php print_string("justifyfull","editor") ?>",
		insertorderedlist:    "<?php print_string("orderedlist","editor") ?>",
		insertunorderedlist:  "<?php print_string("unorderedlist","editor") ?>",
		outdent:        "<?php print_string("outdent","editor") ?>",
		indent:         "<?php print_string("indent","editor") ?>",
		forecolor:      "<?php print_string("forecolor","editor") ?>",
		hilitecolor:    "<?php print_string("hilitecolor","editor") ?>",
		inserthorizontalrule: "<?php print_string("horizontalrule","editor") ?>",
		createlink:     "<?php print_string("createlink","editor") ?>",
		insertimage:    "<?php print_string("insertimage","editor") ?>",
		inserttable:    "<?php print_string("inserttable","editor") ?>",
		htmlmode:       "<?php print_string("htmlmode","editor") ?>",
		popupeditor:    "<?php print_string("popupeditor","editor") ?>",
		about:          "<?php print_string("about","editor") ?>",
		showhelp:       "<?php print_string("showhelp","editor") ?>",
		textindicator:  "<?php print_string("textindicator","editor") ?>",
		undo:           "<?php print_string("undo","editor") ?>",
		redo:           "<?php print_string("redo","editor") ?>",
		cut:            "<?php print_string("cut","editor") ?>",
		copy:           "<?php print_string("copy","editor") ?>",
		paste:          "<?php print_string("paste","editor") ?>",
		insertsmile:	"<?php print_string("insertsmile","editor") ?>",
		insertchar:		"<?php print_string("insertchar","editor") ?>"
	},

	buttons: {
		"ok":           "<?php print_string("ok","editor") ?>",
		"cancel":       "<?php print_string("cancel","editor") ?>",
		"browse":		"<?php print_string("browse","editor") ?>"
	},

	msg: {
		"Path":         "<?php print_string("path","editor") ?>",
		"TEXT_MODE":    "<?php print_string("textmode","editor") ?>"
	}
};
