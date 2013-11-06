<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Moodle tag local library - output functions
 *
 * @package    core_tag
 * @copyright  2007 Luiz Cruz <luiz.laydner@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once($CFG->dirroot.'/tag/lib.php');
require_once($CFG->libdir.'/filelib.php');

/**
 * Prints or returns a HTML tag cloud with varying classes styles depending on the popularity and type of each tag.
 *
 * @package  core_tag
 * @access   public
 * @category tag
 * @param    array     $tagset Array of tags to display
 * @param    int       $nr_of_tags Limit for the number of tags to return/display, used if $tagset is null
 * @param    bool      $return     if true the function will return the generated tag cloud instead of displaying it.
 * @param    string    $sort (optional) selected sorting, default is alpha sort (name) also timemodified or popularity
 * @return string|null a HTML string or null if this function does the output
 */
function tag_print_cloud($tagset=null, $nr_of_tags=150, $return=false, $sort='') {
    global $CFG, $DB;

    $can_manage_tags = has_capability('moodle/tag:manage', context_system::instance());

    if (is_null($tagset)) {
        // No tag set received, so fetch tags from database
        if ( !$tagsincloud = $DB->get_records_sql('SELECT tg.rawname, tg.id, tg.name, tg.tagtype, COUNT(ti.id) AS count, tg.flag
                                                   FROM {tag_instance} ti JOIN {tag} tg ON tg.id = ti.tagid
                                                   WHERE ti.itemtype <> \'tag\'
                                                   GROUP BY tg.id, tg.rawname, tg.name, tg.flag, tg.tagtype
                                                   ORDER BY count DESC, tg.name ASC', null, 0, $nr_of_tags) ) {
            $tagsincloud = array();
        }
    } else {
        $tagsincloud = $tagset;
    }

    $tagkeys = array_keys($tagsincloud);
    if (!empty($tagkeys)) {
        $firsttagkey = $tagkeys[0];
        $maxcount = $tagsincloud[$firsttagkey]->count;
    }

    $etags = array();

    foreach ($tagsincloud as $tag) {
        $size = (int) (( $tag->count / $maxcount) * 20);
        $tag->class = "$tag->tagtype s$size";
        $etags[] = $tag;
    }

    // Set up sort global - used to pass sort type into tag_cloud_sort through usort() avoiding multiple sort functions.
    // TODO make calling functions pass 'count' or 'timemodified' not 'popularity' or 'date'.
    $oldsort = empty($CFG->tagsort) ? null : $CFG->tagsort;
    if ($sort == 'popularity') {
        $CFG->tagsort = 'count';
    } else if ($sort == 'date') {
        $CFG->tagsort = 'timemodified';
    } else {
        $CFG->tagsort = 'name';
    }
    usort($etags, "tag_cloud_sort");
    $CFG->tagsort = $oldsort;

    $output = '';
    $output .= "\n<ul class='tag_cloud inline-list'>\n";
    foreach ($etags as $tag) {
        if ($tag->flag > 0 && $can_manage_tags) {
            $tagname = '<span class="flagged-tag">'. tag_display_name($tag) .'</span>';
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
 * This function is used by print_tag_cloud, to usort() the tags in the cloud. See php.net/usort for the parameters documentation.
 * This was originally in blocks/blog_tags/block_blog_tags.php, named blog_tags_sort().
 *
 * @package core_tag
 * @access  private
 * @param   string $a Tag name to compare against $b
 * @param   string $b Tag name to compare against $a
 * @return  int    The result of the comparison/validation 1, 0 or -1
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
 * @package core_tag
 * @access  public
 * @todo    MDL-31149 create a system setting for $max_tags_displayed, instead of using an in code literal
 * @param   stdClass    $tag_object
 * @param   bool        $return     if true the function will return the generated tag cloud instead of displaying it.
 * @return  string/null a HTML box showing a description of the tag object and it's relationsips or null if output is done directly
 *                      in the function.
 */
function tag_print_description_box($tag_object, $return=false) {

    global $USER, $CFG, $OUTPUT;

    $max_tags_displayed = 10;

    $tagname  = tag_display_name($tag_object);
    $related_tags = tag_get_related_tags($tag_object->id, TAG_RELATED_ALL, $max_tags_displayed+1); // this gets one more than we want

    $content = !empty($tag_object->description) || $related_tags;
    $output = '';

    if ($content) {
        $output .= $OUTPUT->box_start('generalbox', 'tag-description');
    }

    if (!empty($tag_object->description)) {
        $options = new stdClass();
        $options->para = false;
        $options->overflowdiv = true;
        $tag_object->description = file_rewrite_pluginfile_urls($tag_object->description, 'pluginfile.php', context_system::instance()->id, 'tag', 'description', $tag_object->id);
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
        $output .= $OUTPUT->box_end();
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
 * @access public
 * @param  stdClass    $tag_object
 * @param  bool        $return     if true the function will return the generated tag cloud instead of displaying it.
 * @return string|null a HTML string or null if this function does the output
 */
function tag_print_management_box($tag_object, $return=false) {

    global $USER, $CFG, $OUTPUT;

    $tagname  = tag_display_name($tag_object);
    $output = '';

    if (!isguestuser()) {
        $output .= $OUTPUT->box_start('box','tag-management-box');
        $systemcontext   = context_system::instance();
        $links = array();

        // Add a link for users to add/remove this from their interests
        if (tag_record_tagged_with('user', $USER->id, $tag_object->name)) {
            $links[] = '<a href="'. $CFG->wwwroot .'/tag/user.php?action=removeinterest&amp;sesskey='. sesskey() .'&amp;tag='. rawurlencode($tag_object->name) .'">'. get_string('removetagfrommyinterests', 'tag', $tagname) .'</a>';
        } else {
            $links[] = '<a href="'. $CFG->wwwroot .'/tag/user.php?action=addinterest&amp;sesskey='. sesskey() .'&amp;tag='. rawurlencode($tag_object->name) .'">'. get_string('addtagtomyinterests', 'tag', $tagname) .'</a>';
        }

        // Flag as inappropriate link.  Only people with moodle/tag:flag capability.
        if (has_capability('moodle/tag:flag', $systemcontext)) {
            $links[] = '<a href="'. $CFG->wwwroot .'/tag/user.php?action=flaginappropriate&amp;sesskey='. sesskey() .'&amp;tag='. rawurlencode($tag_object->name) .'">'. get_string('flagasinappropriate', 'tag', rawurlencode($tagname)) .'</a>';
        }

        // Edit tag: Only people with moodle/tag:edit capability who either have it as an interest or can manage tags
        if (has_capability('moodle/tag:edit', $systemcontext) ||
            has_capability('moodle/tag:manage', $systemcontext)) {
            $links[] = '<a href="'. $CFG->wwwroot .'/tag/edit.php?tag='. rawurlencode($tag_object->name) .'">'. get_string('edittag', 'tag') .'</a>';
        }

        $output .= implode(' | ', $links);
        $output .= $OUTPUT->box_end();
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
 * @access public
 * @param  bool        $return if true return html string
 * @return string|null a HTML string or null if this function does the output
 */
function tag_print_search_box($return=false) {
    global $CFG, $OUTPUT;

    $output = $OUTPUT->box_start('','tag-search-box');
    $output .= '<form action="'.$CFG->wwwroot.'/tag/search.php" style="display:inline">';
    $output .= '<div>';
    $output .= '<label class="accesshide" for="searchform_search">'.get_string('searchtags', 'tag').'</label>';
    $output .= '<input id="searchform_search" name="query" type="text" size="40" />';
    $output .= '<button id="searchform_button" type="submit">'. get_string('search', 'tag') .'</button><br />';
    $output .= '</div>';
    $output .= '</form>';
    $output .= $OUTPUT->box_end();

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
 * @access public
 * @param string       $query text that tag names will be matched against
 * @param int          $page current page
 * @param int          $perpage nr of users displayed per page
 * @param bool         $return if true return html string
 * @return string|null a HTML string or null if this function does the output
 */
function tag_print_search_results($query,  $page, $perpage, $return=false) {

    global $CFG, $USER, $OUTPUT;

    $norm = tag_normalize($query, TAG_CASE_ORIGINAL);
    $query = array_shift($norm);

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
        $output .= $OUTPUT->heading(get_string('searchresultsfor', 'tag', htmlspecialchars($query)) ." : {$count}", 3, 'main');

        //print a link "Add $query to my interests"
        if (!empty($addtaglink)) {
            $output .= $OUTPUT->box($addtaglink, 'box', 'tag-management-box');
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

        $output .= $OUTPUT->paging_bar($count, $page, $perpage, $baseurl);
    }
    else { //no results were found!!
        $output .= $OUTPUT->heading(get_string('noresultsfor', 'tag', htmlspecialchars($query)), 3, 'main');

        //print a link "Add $query to my interests"
        if (!empty($addtaglink)) {
            $output .= $OUTPUT->box($addtaglink, 'box', 'tag-management-box');
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
 * @param  int         $tag_object the tag we wish to return data for
 * @param  int         $limitfrom (optional, required if $limitnum is set) prints users starting at this point.
 * @param  int         $limitnum (optional, required if $limitfrom is set) prints this many users.
 * @param  bool        $return if true return html string
 * @return string|null a HTML string or null if this function does the output
 */
function tag_print_tagged_users_table($tag_object, $limitfrom='', $limitnum='', $return=false) {

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
 * @param user_object  $user  (contains the following fields: id, firstname, lastname and picture)
 * @param bool         $return if true return html string
 * @return string|null a HTML string or null if this function does the output
 */
function tag_print_user_box($user, $return=false) {
    global $CFG, $OUTPUT;

    $usercontext = context_user::instance($user->id);
    $profilelink = '';

    if ($usercontext and (has_capability('moodle/user:viewdetails', $usercontext) || has_coursecontact_role($user->id))) {
        $profilelink = $CFG->wwwroot .'/user/view.php?id='. $user->id;
    }

    $output = $OUTPUT->box_start('user-box', 'user'. $user->id);
    $fullname = fullname($user);
    $alt = '';

    if (!empty($profilelink)) {
        $output .= '<a href="'. $profilelink .'">';
        $alt = $fullname;
    }

    $output .= $OUTPUT->user_picture($user, array('size'=>100));
    $output .= '<br />';

    if (!empty($profilelink)) {
        $output .= '</a>';
    }

    //truncate name if it's too big
    if (textlib::strlen($fullname) > 26) {
        $fullname = textlib::substr($fullname, 0, 26) .'...';
    }

    $output .= '<strong>'. $fullname .'</strong>';
    $output .= $OUTPUT->box_end();

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
 * @param  array       $userlist an array of user objects
 * @param  bool        $return if true return html string, otherwise output the result
 * @return string|null a HTML string or null if this function does the output
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
