<?php // $Id$

require_once('../config.php');
require_once('lib.php');

require_login();

if (empty($CFG->usetags)) {
    error(get_string('tagsaredisabled', 'tag'));
}

$query = optional_param('query', '', PARAM_TAG);  

if ($similar_tags = similar_tags($query)) {
    foreach ($similar_tags as $tag){
        echo tag_display_name($tag) . "\t" . $tag->id . "\n";
    }
}

?>
