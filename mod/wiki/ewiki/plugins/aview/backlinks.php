<?php

#
#  this plugin prints the "pages linking to" below a page (the same
#  information the "links/" action does)
#
# altered to use ewiki_get_backlinks() by AndyFundinger.

$ewiki_plugins["view_append"][] = "ewiki_view_append_backlinks";

function ewiki_view_append_backlinks($id, $data, $action) {
    $pages = ewiki_get_backlinks($id);
    
    $o="";
    foreach ($pages as $id) {
        $o .= ' <a href="'.ewiki_script("",$id).'">'.$id.'</a>';
    }
    ($o) && ($o = "<div class=\"wiki_backlinks\"><small>".get_string('backlinks', 'wiki').":</small><br />$o</div>\n");
    
    return($o);
}
?>