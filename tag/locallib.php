<?php // $Id$

/**
 * locallib.php - moodle tag local library - output functions
 *
 * @version: $Id$
 * @licence http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 *
 */

/**
 * Prints a tag cloud
 *
 * @param array $tagcloud array of tag objects (fields: id, name, rawname, count and flag)
 * @param $return if true return html string
 */
function tag_print_cloud($nr_of_tags=150, $return=false) {

    global $CFG;
    
    $can_manage_tags = has_capability('moodle/tag:manage', get_context_instance(CONTEXT_SYSTEM));

    if ( !$tagcloud = get_records_sql('SELECT tg.rawname, tg.id, tg.name, tg.tagtype, COUNT(ti.id) AS count, tg.flag '.
        'FROM '. $CFG->prefix .'tag_instance ti INNER JOIN '. $CFG->prefix .'tag tg ON tg.id = ti.tagid '.
        'WHERE ti.itemtype != \'tag\' '.
        'GROUP BY tg.id, tg.rawname, tg.name, tg.flag, tg.tagtype '.
        'ORDER BY count DESC, tg.name ASC', 0, $nr_of_tags) ) {
        $tagcloud = array();
    }

    $totaltags  = count($tagcloud);
    $currenttag = 0;
    $size = 20;
    $lasttagct = -1;

    $etags = array();
    foreach ($tagcloud as $tag) {

        $currenttag++;

        if ($currenttag == 1) {
            $lasttagct = $tag->count;
            $size = 20;
        } else if ($tag->count != $lasttagct) {
            $lasttagct = $tag->count;
            $size = 20 - ( (int)((($currenttag - 1) / $totaltags) * 20) );
        }

        $tag->class = "$tag->tagtype s$size";
        $etags[] = $tag;
    }

    usort($etags, "tag_cloud_sort"); 
    $output = '';
    $output .= "\n<ul class='tag_cloud inline-list'>\n";
    foreach ($etags as $tag) {
        if ($tag->flag > 0 && $can_manage_tags) {
            $tagname =  '<span class="flagged-tag">'. tag_display_name($tag) .'</span>';
        } else { 
            $tagname = tag_display_name($tag);
        }

        $link = $CFG->wwwroot .'/tag/index.php?tag='. rawurlencode($tag->name);
        $output .= '<li><a href="'. $link .'" class="'. $tag->class .'" '.
            'title="'. get_string('numberofentries', 'blog', $tag->count) .'">'.
            $tagname .'</a></li> ';
    }
    $output .= "\n</ul>\n";

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * This function is used by print_tag_cloud, to usort() the tags in the cloud.  
 * See php.net/usort for the parameters documentation. This was originally in
 * blocks/blog_tags/block_blog_tags.php, named blog_tags_sort().
 */
function tag_cloud_sort($a, $b) {
    global $CFG;

    if (empty($CFG->tagsort)) {
        $tagsort = 'name'; // by default, sort by name
    } else {
        $tagsort = $CFG->tagsort;
    }

    if (is_numeric($a->$tagsort)) {
        return ($a->$tagsort == $b->$tagsort) ? 0 : ($a->$tagsort > $b->$tagsort) ? 1 : -1;
    } elseif (is_string($a->$tagsort)) {
        return strcmp($a->$tagsort, $b->$tagsort);
    } else {
        return 0;
    }
}

/**
 * Prints a box with the description of a tag and its related tags
 *
 * @param unknown_type $tag_object
 * @param $return if true return html string
 */
function tag_print_description_box($tag_object, $return=false) {

    global $USER, $CFG;

    $max_tags_displayed = 10; // todo: turn this into a system setting

    $tagname  = tag_display_name($tag_object);
    $related_tags = tag_get_related_tags($tag_object->id, TAG_RELATED_ALL, $max_tags_displayed+1); // this gets one more than we want

    $content = !empty($tag_object->description) || $related_tags;
    $output = '';

    if ($content) {
        $output .= print_box_start('generalbox', 'tag-description', true);
    }

    if (!empty($tag_object->description)) {
        $options = new object();
        $options->para = false;
        $output .= format_text($tag_object->description, $tag_object->descriptionformat, $options);
    }

    if ($related_tags) {
        $more_links = false;
        if (count($related_tags) > $max_tags_displayed) {
            array_pop($related_tags);
            $more_links = true;
        }
        $output .= '<br /><br /><strong>'. get_string('relatedtags', 'tag') .': </strong>'. tag_get_related_tags_csv($related_tags);
        if ($more_links) {
            $output .= ' ...';
        }
    }

    if ($content) {
        $output .= print_box_end(true);
    }

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Prints a box that contains the management links of a tag
 *
 * @param $tagid
 * @param $return if true return html string
 */
function tag_print_management_box($tag_object, $return=false) {

    global $USER, $CFG;

    $tagname  = tag_display_name($tag_object);
    $output = '';

    if (!isguestuser()) {
        $output .= print_box_start('box','tag-management-box', true);
        $systemcontext   = get_context_instance(CONTEXT_SYSTEM);
        $links = array();
        
        // Add a link for users to add/remove this from their interests
        if (tag_record_tagged_with('user', $USER->id, $tag_object->name)) {
            $links[] = '<a href="'. $CFG->wwwroot .'/tag/user.php?action=removeinterest&amp;sesskey='. sesskey() .'&amp;tag='. rawurlencode($tag_object->name) .'">'. get_string('removetagfrommyinterests', 'tag', $tagname) .'</a>';
        } else {
            $links[] = '<a href="'. $CFG->wwwroot .'/tag/user.php?action=addinterest&amp;sesskey='. sesskey() .'&amp;tag='. rawurlencode($tag_object->name) .'">'. get_string('addtagtomyinterests', 'tag', $tagname) .'</a>';
        }

        // flag as inappropriate link
        $links[] = '<a href="'. $CFG->wwwroot .'/tag/user.php?action=flaginappropriate&amp;sesskey='. sesskey() .'&amp;tag='. rawurlencode($tag_object->name) .'">'. get_string('flagasinappropriate', 'tag', rawurlencode($tagname)) .'</a>';

        // Edit tag: Only people with moodle/tag:edit capability who either have it as an interest or can manage tags
        if (has_capability('moodle/tag:edit', $systemcontext) || 
            has_capability('moodle/tag:manage', $systemcontext)) {
            $links[] = '<a href="'. $CFG->wwwroot .'/tag/edit.php?tag='. rawurlencode($tag_object->name) .'">'. get_string('edittag', 'tag') .'</a>';
        }

        $output .= implode(' | ', $links);
        $output .= print_box_end(true);
    }

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Prints the tag search box
 *
 * @param bool $return if true return html string
 */
function tag_print_search_box($return=false) {
    global $CFG;

    $output = print_box_start('','tag-search-box', true);
    $output .= '<form action="'.$CFG->wwwroot.'/tag/search.php" style="display:inline">';
    $output .= '<div>';
    $output .= '<input id="searchform_search" name="query" type="text" size="40" />';
    $output .= '<button id="searchform_button" type="submit">'. get_string('search', 'tag') .'</button><br />';
    $output .= '</div>';
    $output .= '</form>';
    $output .= print_box_end(true);

    if ($return) {
        return $output;
    }
    else {
        echo $output;
    }
}

/**
 * Prints the tag search results
 *
 * @param string $query text that tag names will be matched against
 * @param int $page current page
 * @param int $perpage nr of users displayed per page
 * @param $return if true return html string
 */
function tag_print_search_results($query,  $page, $perpage, $return=false) {

    global $CFG, $USER;

    $query = array_shift(tag_normalize($query, TAG_CASE_ORIGINAL));

    $count = sizeof(tag_find_tags($query, false));
    $tags = array();

    if ( $found_tags = tag_find_tags($query, true,  $page * $perpage, $perpage) ) {
        $tags = array_values($found_tags);
    }

    $baseurl = $CFG->wwwroot.'/tag/search.php?query='. rawurlencode($query);
    $output = '';

    // link "Add $query to my interests"
    $addtaglink = '';
    if( !tag_record_tagged_with('user', $USER->id, $query) ) {
        $addtaglink = '<a href="'. $CFG->wwwroot .'/tag/user.php?action=addinterest&amp;sesskey='. sesskey() .'&amp;tag='. rawurlencode($query) .'">';
        $addtaglink .= get_string('addtagtomyinterests', 'tag', htmlspecialchars($query)) .'</a>';
    }

    if ( !empty($tags) ) { // there are results to display!!
        $output .= print_heading(get_string('searchresultsfor', 'tag', htmlspecialchars($query)) ." : {$count}", '', 3, 'main', true);

        //print a link "Add $query to my interests"
        if (!empty($addtaglink)) {
            $output .= print_box($addtaglink, 'box', 'tag-management-box', true);
        }

        $nr_of_lis_per_ul = 6;
        $nr_of_uls = ceil( sizeof($tags) / $nr_of_lis_per_ul );

        $output .= '<ul id="tag-search-results">';
        for($i = 0; $i < $nr_of_uls; $i++) {
            $output .= '<li>';
            foreach (array_slice($tags, $i * $nr_of_lis_per_ul, $nr_of_lis_per_ul) as $tag) {
                $tag_link = ' <a href="'. $CFG->wwwroot .'/tag/index.php?id='. $tag->id .'">'. tag_display_name($tag) .'</a>';
                $output .= '&#8226;'. $tag_link .'<br/>';
            }
            $output .= '</li>';
        }
        $output .= '</ul>';
        $output .= '<div>&nbsp;</div>'; // <-- small layout hack in order to look good in Firefox

        $output .= print_paging_bar($count, $page, $perpage, $baseurl .'&amp;', 'page', false, true);
    }
    else { //no results were found!!
        $output .= print_heading(get_string('noresultsfor', 'tag', htmlspecialchars($query)), '', 3, 'main' , true);

        //print a link "Add $query to my interests"
        if (!empty($addtaglink)) {
            $output .= print_box($addtaglink, 'box', 'tag-management-box', true);
        }
    }

    if ($return) {
        return $output;
    }
    else {
        echo $output;
    }
}

/**
 * Prints a table of the users tagged with the tag passed as argument
 *
 * @param $tag_object
 * @param int $users_per_row number of users per row to display
 * @param int $limitfrom prints users starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum prints this many users (optional, required if $limitfrom is set).
 * @param $return if true return html string
 */
function tag_print_tagged_users_table($tag_object, $limitfrom='' , $limitnum='', $return=false) {

    //List of users with this tag
    $userlist = tag_find_records($tag_object->name, 'user', $limitfrom, $limitnum);

    $output = tag_print_user_list($userlist, true);

    if ($return) {
        return $output;
    }
    else {
        echo $output;
    }
}

/**
 * Prints an individual user box
 *
 * @param $user user object (contains the following fields: id, firstname, lastname and picture)
 * @param $return if true return html string
 */
function tag_print_user_box($user, $return=false) {
    global $CFG;

    $textlib = textlib_get_instance();
    $usercontext = get_context_instance(CONTEXT_USER, $user->id);
    $profilelink = '';

    if ( has_capability('moodle/user:viewdetails', $usercontext) ) {
        $profilelink = $CFG->wwwroot .'/user/view.php?id='. $user->id;
    }

    $output = print_box_start('user-box', 'user'. $user->id, true);
    $fullname = fullname($user);
    $alt = '';

    if (!empty($profilelink)) {
        $output .= '<a href="'. $profilelink .'">';
        $alt = $fullname;
    }

    //print user image - if image is only content of link it needs ALT text!
    if ($user->picture) {
        $output .= '<img alt="'. $alt .'" class="user-image" src="'. $CFG->wwwroot .'/user/pix.php/'. $user->id .'/f1.jpg" />';
    } else {
        $output .= '<img alt="'. $alt .'" class="user-image" src="'. $CFG->wwwroot .'/pix/u/f1.png" />';
    }
    
    $output .= '<br />';

    if (!empty($profilelink)) {
        $output .= '</a>';
    }

    //truncate name if it's too big
    if ($textlib->strlen($fullname) > 26) {
        $fullname = $textlib->substr($fullname, 0, 26) .'...';
    }

    $output .= '<strong>'. $fullname .'</strong>';
    $output .= print_box_end(true);

    if ($return) {
        return $output;
    }
    else {
        echo $output;
    }
}
/**
 * Prints a list of users
 *
 * @param array $userlist an array of user objects
 * @param $return if true return html string
 */
function tag_print_user_list($userlist, $return=false) {

    $output = '<ul class="inline-list">';

    foreach ($userlist as $user){
        $output .= '<li>'. tag_print_user_box($user, true) ."</li>\n";
    }
    $output .= "</ul>\n";

    if ($return) {
        return $output;
    }
    else {
        echo $output;
    }
}


?>
