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
		bold:           "<?php print(get_string("bold","htmlarea"));?>",
		italic:         "<?php print(get_string("italic","htmlarea"));?>",
		underline:      "<?php print(get_string("underline","htmlarea"));?>",
		strikethrough:  "<?php print(get_string("strikethrough","htmlarea"));?>",
		subscript:      "<?php print(get_string("subscript","htmlarea"));?>",
		superscript:    "<?php print(get_string("superscript","htmlarea"));?>",
		justifyleft:    "<?php print(get_string("justifyleft","htmlarea"));?>",
		justifycenter:  "<?php print(get_string("justifycenter","htmlarea"));?>",
		justifyright:   "<?php print(get_string("justifyright","htmlarea"));?>",
		justifyfull:    "<?php print(get_string("justifyfull","htmlarea"));?>",
		insertorderedlist:    "<?php print(get_string("orderedlist","htmlarea"));?>",
		insertunorderedlist:  "<?php print(get_string("unorderedlist","htmlarea"));?>",
		outdent:        "<?php print(get_string("outdent","htmlarea"));?>",
		indent:         "<?php print(get_string("indent","htmlarea"));?>",
		forecolor:      "<?php print(get_string("forecolor","htmlarea"));?>",
		hilitecolor:    "<?php print(get_string("hilitecolor","htmlarea"));?>",
		inserthorizontalrule: "<?php print(get_string("horizontalrule","htmlarea"));?>",
		createlink:     "<?php print(get_string("createlink","htmlarea"));?>",
		insertimage:    "<?php print(get_string("insertimage","htmlarea"));?>",
		inserttable:    "<?php print(get_string("inserttable","htmlarea"));?>",
		htmlmode:       "<?php print(get_string("htmlmode","htmlarea"));?>",
		popupeditor:    "<?php print(get_string("popupeditor","htmlarea"));?>",
		about:          "<?php print(get_string("about","htmlarea"));?>",
		showhelp:       "<?php print(get_string("showhelp","htmlarea"));?>",
		textindicator:  "<?php print(get_string("textindicator","htmlarea"));?>",
		undo:           "<?php print(get_string("undo","htmlarea"));?>",
		redo:           "<?php print(get_string("redo","htmlarea"));?>",
		cut:            "<?php print(get_string("cut","htmlarea"));?>",
		copy:           "<?php print(get_string("copy","htmlarea"));?>",
		paste:          "<?php print(get_string("paste","htmlarea"));?>",
		insertsmile:	"<?php print(get_string("insertsmile","htmlarea"));?>",
		insertchar:		"<?php print(get_string("insertchar","htmlarea"));?>"
	},

	buttons: {
		"ok":           "<?php print(get_string("ok","htmlarea"));?>",
		"cancel":       "<?php print(get_string("cancel","htmlarea"));?>",
		"browse":		"<?php print(get_string("browse","htmlarea"));?>"
	},

	msg: {
		"Path":         "<?php print(get_string("Path","htmlarea"));?>",
		"TEXT_MODE":    "<?php print(get_string("TEXT_MODE","htmlarea"));?>"
	}
};
