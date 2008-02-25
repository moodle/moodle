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
 * @param boolean $shuffle wether or not to shuffle the array passed
 * @param int $max_size maximum text size, in percentage
 * @param int $min_size minimum text size, in percentage
 * @param $return if true return html string
 */
function tag_print_cloud($nr_of_tags=150, $shuffle=true, $max_size=180, $min_size=80, $return=false) {

    global $CFG;

    $query = 'SELECT tg.rawname, tg.id, tg.name, COUNT(ti.id) AS count, tg.flag '.
        'FROM '. $CFG->prefix .'tag_instance ti INNER JOIN '. $CFG->prefix .'tag tg ON tg.id = ti.tagid '.
        'GROUP BY tg.id, tg.rawname, tg.name, tg.flag '.
        'ORDER BY count DESC';

    $tagcloud = get_records_sql($query, 0, $nr_of_tags);

    if ($shuffle) {
        shuffle($tagcloud);
    } else {
        ksort($tagcloud);
    }

    $count = array();
    foreach ($tagcloud as $key => $tag){
        if(!empty($tag->count)) {
            $count[$key] = log10($tag->count);
        }
        else{
            $count[$key] = 0;
        }
    }

    $max = max($count);
    $min = min($count);

    $spread = $max - $min;
    if (0 == $spread) { // we don't want to divide by zero
        $spread = 1;
    }

    $step = ($max_size - $min_size)/($spread);

    $systemcontext   = get_context_instance(CONTEXT_SYSTEM);
    $can_manage_tags = has_capability('moodle/tag:manage', $systemcontext);

    //prints the tag cloud
    $output = '<ul id="tag-cloud-list">';
    foreach ($tagcloud as $key => $tag) {

        $size = $min_size + ((log10($tag->count) - $min) * $step);
        $size = ceil($size);

        $style = 'style="font-size: '. $size .'%"';
        $title = 'title="'. s(get_string('thingstaggedwith', 'tag', $tag)) .'"';
        $href = 'href="'. $CFG->wwwroot .'/tag/index.php?tag='. rawurlencode($tag->name) .'"';

        //highlight tags that have been flagged as inappropriate for those who can manage them
        $tagname = tag_display_name($tag);
        if ($tag->flag > 0 && $can_manage_tags) {
            $tagname =  '<span class="flagged-tag">'. tag_display_name($tag) .'</span>';
        }

        $tag_link = '<li><a '. $href .' '. $title .' '. $style .'>'. $tagname .'</a></li> ';

        $output .= $tag_link;
    }
    $output .= '</ul>';

    if ($return) {
        return $output;
    } else {
        echo $output;
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

    $tagname  = tag_display_name($tag_object);
    $related_tags = tag_get_related_tags($tag_object->id);

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
        $output .= '<br /><br /><strong>'. get_string('relatedtags', 'tag') .': </strong>'. tag_get_related_tags_csv($related_tags);
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
        
        // if the user is not tagged with the $tag_object tag, a link "add blahblah to my interests" will appear
        if( !tag_record_tagged_with(array('type'=>'user', 'id'=>$USER->id), $tag_object->name )) {
            $links[] = '<a href="'. $CFG->wwwroot .'/user/tag.php?action=addinterest&amp;sesskey='. sesskey() .'&amp;tag='. rawurlencode($tag_object->name) .'">'. get_string('addtagtomyinterests', 'tag', $tagname) .'</a>';
        }

        // only people with moodle/tag:edit capability may edit the tag description
        if ( has_capability('moodle/tag:edit', $systemcontext) && 
                (tag_record_tagged_with(array('type'=>'user', 'id'=>$USER->id), $tag_object->name) || is_siteadmin($USER->id)) ) {
            $links[] = '<a href="'. $CFG->wwwroot .'/tag/edit.php?tag='. rawurlencode($tag_object->name) .'">'. get_string('edittag', 'tag') .'</a>';
        }

        // flag as inappropriate link
        $links[] = '<a href="'. $CFG->wwwroot .'/user/tag.php?action=flaginappropriate&amp;sesskey='. sesskey() .'&amp;tag='. rawurlencode($tag_object->name) .'">'. get_string('flagasinappropriate', 'tag', rawurlencode($tagname)) .'</a>';

        // Manage all tags links
        if ( has_capability('moodle/tag:manage', $systemcontext) ) {
            $links[] =  '<a href="'. $CFG->wwwroot .'/tag/manage.php">'. get_string('managetags', 'tag') .'</a>' ;
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

    $count = sizeof(tag_search($query, false));
    $tags = array();

    if ( $found_tags = tag_search($query, true,  $page * $perpage, $perpage) ) {
        $tags = array_values($found_tags);
    }

    $baseurl = $CFG->wwwroot.'/tag/search.php?query='. rawurlencode($query);
    $output = '';

    // link "Add $query to my interests"
    $addtaglink = '';
    if( !is_item_tagged_with('user', $USER->id, $query) ) {
        $addtaglink = '<a href="'. $CFG->wwwroot .'/user/tag.php?action=addinterest&amp;sesskey='. sesskey() .'&amp;tag='. rawurlencode($query) .'">';
        $addtaglink .= get_string('addtagtomyinterests', 'tag', rawurlencode($query)) .'</a>';
    }

    if ( !empty($tags) ) { // there are results to display!!
        $output .= print_heading(get_string('searchresultsfor', 'tag', rawurlencode($query)) ." : {$count}", '', 3, 'main', true);

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
        $output .= print_heading(get_string('noresultsfor', 'tag', rawurlencode($query)), '', 3, 'main' , true);

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
    $userlist = tag_find_records($tag_object->name, 'user');

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
