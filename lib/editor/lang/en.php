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
		bold:           "<?php print(get_string("bold","editor"));?>",
		italic:         "<?php print(get_string("italic","editor"));?>",
		underline:      "<?php print(get_string("underline","editor"));?>",
		strikethrough:  "<?php print(get_string("strikethrough","editor"));?>",
		subscript:      "<?php print(get_string("subscript","editor"));?>",
		superscript:    "<?php print(get_string("superscript","editor"));?>",
		justifyleft:    "<?php print(get_string("justifyleft","editor"));?>",
		justifycenter:  "<?php print(get_string("justifycenter","editor"));?>",
		justifyright:   "<?php print(get_string("justifyright","editor"));?>",
		justifyfull:    "<?php print(get_string("justifyfull","editor"));?>",
		insertorderedlist:    "<?php print(get_string("orderedlist","editor"));?>",
		insertunorderedlist:  "<?php print(get_string("unorderedlist","editor"));?>",
		outdent:        "<?php print(get_string("outdent","editor"));?>",
		indent:         "<?php print(get_string("indent","editor"));?>",
		forecolor:      "<?php print(get_string("forecolor","editor"));?>",
		hilitecolor:    "<?php print(get_string("hilitecolor","editor"));?>",
		inserthorizontalrule: "<?php print(get_string("horizontalrule","editor"));?>",
		createlink:     "<?php print(get_string("createlink","editor"));?>",
		insertimage:    "<?php print(get_string("insertimage","editor"));?>",
		inserttable:    "<?php print(get_string("inserttable","editor"));?>",
		htmlmode:       "<?php print(get_string("htmlmode","editor"));?>",
		popupeditor:    "<?php print(get_string("popupeditor","editor"));?>",
		about:          "<?php print(get_string("about","editor"));?>",
		showhelp:       "<?php print(get_string("showhelp","editor"));?>",
		textindicator:  "<?php print(get_string("textindicator","editor"));?>",
		undo:           "<?php print(get_string("undo","editor"));?>",
		redo:           "<?php print(get_string("redo","editor"));?>",
		cut:            "<?php print(get_string("cut","editor"));?>",
		copy:           "<?php print(get_string("copy","editor"));?>",
		paste:          "<?php print(get_string("paste","editor"));?>",
		insertsmile:	"<?php print(get_string("insertsmile","editor"));?>",
		insertchar:		"<?php print(get_string("insertchar","editor"));?>"
	},

	buttons: {
		"ok":           "<?php print(get_string("ok","editor"));?>",
		"cancel":       "<?php print(get_string("cancel","editor"));?>",
		"browse":		"<?php print(get_string("browse","editor"));?>"
	},

	msg: {
		"Path":         "<?php print(get_string("Path","editor"));?>",
		"TEXT_MODE":    "<?php print(get_string("TEXT_MODE","editor"));?>"
	}
};
