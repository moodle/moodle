<?php // $Id$
    require("../../../../config.php");

    $id = optional_param('id', SITEID, PARAM_INT);

    require_course_login($id);
    @header('Content-Type: text/html; charset=utf-8');
?>
<html>
<head><title><?php print_string("fullscreen","editor");?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<style type="text/css">
@import url(../htmlarea.css);
html, body {margin: 0px; border: 0px; background-color: buttonface;}
</style>
<script type="text/javascript" src="../htmlarea.php?id=<?php p($id); ?>"></script>
<script type="text/javascript" src="../lang/en.php"></script>
<script type="text/javascript" src="../dialog.js" charset="utf-8"></script>
<script type="text/javascript" src="../plugins/TableOperations/table-operations.js" charset="utf-8"></script>
<script type="text/javascript" src="../plugins/TableOperations/lang/en.js" charset="utf-8"></script>
<script type="text/javascript"> 
var parent_object  = null;
var editor         = null; // to be initialized later [ function init() ]

/* ---------------------------------------------------------------------- *\
  Function    :
  Description :
\* ---------------------------------------------------------------------- */

function _CloseOnEsc(ev) {
    try {
        if (document.all) {
            // IE
            ev || (ev = editor._iframe.contentWindow.event);
        }
        if (ev.keyCode == 27) {
            // update_parent();
            window.close();
            return;
        }
    } catch(e) {}
}

/* ---------------------------------------------------------------------- *\
  Function    : cloneObject
  Description : copy an object by value instead of by reference
  Usage       : var newObj = cloneObject(oldObj);
\* ---------------------------------------------------------------------- */

function cloneObject(obj) {
  var newObj = new Object;

  // check for array objects
  if (obj.constructor.toString().indexOf("function Array(") >= 0) {
    newObj = obj.constructor();
  }

  // check for function objects (as usual, IE is phucked up)
  if (obj.constructor.toString().indexOf("function Function(") >= 0) {
    newObj = obj; // just copy reference to it
  } else for (var n in obj) {
    var node = obj[n];
    if (typeof node == 'object') { newObj[n] = cloneObject(node); }
    else                         { newObj[n] = node; }
  }

  return newObj;
}

/* ---------------------------------------------------------------------- *\
  Function    : resize_editor
  Description : resize the editor when the user resizes the popup
\* ---------------------------------------------------------------------- */

function resize_editor() {  // resize editor to fix window
  var newHeight;
  if (document.all) {
    // IE
    newHeight = document.body.offsetHeight - editor._toolbar.offsetHeight;
    if (newHeight < 0) { newHeight = 0; }
  } else {
    // Gecko
    newHeight = window.innerHeight - editor._toolbar.offsetHeight;
  }
  if (editor.config.statusBar) {
    newHeight -= editor._statusBar.offsetHeight;
  }
  editor._textArea.style.height = editor._iframe.style.height = newHeight + "px";
}

/* ---------------------------------------------------------------------- *\
  Function    : init
  Description : run this code on page load
\* ---------------------------------------------------------------------- */

function init() {
    parent_object      = window.opener.HTMLArea._object;
    var config         = cloneObject( parent_object.config );
    config.editorURL   = "../";
    config.width       = "100%";
    config.height      = "auto";

    // change maximize button to minimize button
    config.btnList["popupeditor"] = [ "<?php print_string("minimize","editor");?>", "<?php echo $CFG->wwwroot ?>/lib/editor/htmlarea/images/fullscreen_minimize.gif", true,
        function() { window.close(); } ];

    // generate editor and resize it
    editor = new HTMLArea("editor", config);
    editor.registerPlugin(TableOperations);
    editor.generate();
    editor._iframe.style.width = "100%";
    editor._textArea.style.width = "100%";
    resize_editor();

    // set child window contents and event handlers, after a small delay
    setTimeout(function() {
        editor.setHTML(parent_object.getInnerHTML());

        // switch mode if needed
        if (parent_object._mode == "textmode") { editor.setMode("textmode"); }

        // continuously update parent editor window
        setInterval(update_parent, 500);

        // setup event handlers FAST FIX IS UNCOMMENT THESE, NOT WORKING!
        //document.body.onkeypress = _CloseOnEsc;
        //editor._doc.body.onkeypress = _CloseOnEsc;
        //editor._textArea.onkeypress = _CloseOnEsc;
        window.onresize = resize_editor;
    }, 333);                      // give it some time to meet the new frame
}

/* ---------------------------------------------------------------------- *\
  Function    : update_parent
  Description : update parent window editor field with contents from child window
\* ---------------------------------------------------------------------- */

function update_parent() {
  // use the fast version
  parent_object.setHTML(editor.getInnerHTML());
}
</script>
</head>
<body scroll="no" onload="init()" onunload="update_parent()">

<form style="margin: 0px; border: 1px solid; border-color: threedshadow threedhighlight threedhighlight threedshadow;">
<textarea name="editor" id="editor" style="width:100%; height:300px">&nbsp;</textarea>
</form>

</body></html>
