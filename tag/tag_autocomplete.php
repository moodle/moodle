<?php // $Id$

require_once('lib.php');

if( empty($CFG->usetags)) {
    error(get_string('tagsaredisabled', 'tag'));
}

$query    = optional_param('query',     0,      PARAM_TEXT);  
$query    = tag_normalize($query);

$similar_tags = similar_tags($query);

$count = 0;
foreach ($similar_tags as $tag){
    echo $tag->name . "\t" . $tag->id . "\n";
}

?>
