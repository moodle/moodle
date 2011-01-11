<?php // $Id$

require_once('../config.php');
require_once('lib.php');

require_login();

if (empty($CFG->usetags)) {
    print_error('tagsaredisabled', 'tag');
}

/// Headers to make it not cacheable and json
@header('Content-type: application/json; charset=utf-8');
@header('Cache-Control: no-store, no-cache, must-revalidate');
@header('Cache-Control: post-check=0, pre-check=0', false);
@header('Pragma: no-cache');
@header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
@header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
@header('Accept-Ranges: none');

$query = optional_param('query', '', PARAM_RAW);

if ($similar_tags = tag_autocomplete($query)) {
    foreach ($similar_tags as $tag) {
        echo clean_param($tag->name, PARAM_TAG) . "\t" . tag_display_name($tag) . "\n";
    }
}

?>
