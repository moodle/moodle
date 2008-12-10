<?php // $Id$
// In fact, this is very similar to the "topics" format. 
// The main difference is that news forum is replaced by LAMS learner
// interface.

require_once($CFG->dirroot.'/mod/lams/constants.php');
require_once($CFG->dirroot.'/lib/weblib.php');

$topic = optional_param('topic', -1, PARAM_INT);
// Bounds for block widths
// more flexible for theme designers taken from theme config.php
$lmin = (empty($THEME->block_l_min_width)) ? 100 : $THEME->block_l_min_width;
$lmax = (empty($THEME->block_l_max_width)) ? 210 : $THEME->block_l_max_width;
$rmin = (empty($THEME->block_r_min_width)) ? 100 : $THEME->block_r_min_width;
$rmax = (empty($THEME->block_r_max_width)) ? 210 : $THEME->block_r_max_width;

define('BLOCK_L_MIN_WIDTH', $lmin);
define('BLOCK_L_MAX_WIDTH', $lmax);
define('BLOCK_R_MIN_WIDTH', $rmin);
define('BLOCK_R_MAX_WIDTH', $rmax);
  
$preferred_width_left  = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),  
                                        BLOCK_L_MAX_WIDTH);
$preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 
                                        BLOCK_R_MAX_WIDTH);

if ($topic != -1) {
    $displaysection = course_set_display($course->id, $topic);
} else {
    if (isset($USER->display[$course->id])) {       // for admins, mostly
        $displaysection = $USER->display[$course->id];
    } else {
        $displaysection = course_set_display($course->id, 0);
    }
}

$context = get_context_instance(CONTEXT_COURSE, $course->id);

if (($marker >=0) and has_capability('moodle/course:setcurrentsection', $context) and confirm_sesskey()) {
    $course->marker = $marker;
    if (! set_field("course", "marker", $marker, "id", $course->id)) {
        error("Could not mark that topic for this course");
    }
}

$streditsummary   = get_string('editsummary');
$stradd           = get_string('add');
$stractivities    = get_string('activities');
$strshowalltopics = get_string('showalltopics');
$strtopic         = get_string('topic');
$strgroups        = get_string('groups');
$strgroupmy       = get_string('groupmy');
$editing          = $PAGE->user_is_editing();

if ($editing) {
    $strstudents = moodle_strtolower($course->students);
    $strtopichide = get_string('topichide', '', $strstudents);
    $strtopicshow = get_string('topicshow', '', $strstudents);
    $strmarkthistopic = get_string('markthistopic');
    $strmarkedthistopic = get_string('markedthistopic');
    $strmoveup = get_string('moveup');
    $strmovedown = get_string('movedown');
}


/// Layout the whole page as three big columns.
echo '<table id="layout-table" cellspacing="0" summary="'.get_string('layouttable').'"><tr>';

/// The left column ...

if (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $editing) {
    echo '<td style="width:'.$preferred_width_left.'px" id="left-column">';
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
    echo '</td>';
}

/// Start main column
echo '<td id="middle-column">'. skip_main_destination();

print_heading_block(get_string('lamsoutline','lams'), 'outline');

echo '<table class="topics" width="100%" height="100%" summary="'.get_string('layouttable').'">';

/// If currently moving a file then show the current clipboard
if (ismoving($course->id)) {
    $stractivityclipboard = strip_tags(get_string('activityclipboard', '', addslashes($USER->activitycopyname)));
    $strcancel= get_string('cancel');
    echo '<tr class="clipboard">';
    echo '<td colspan="3">';
    echo $stractivityclipboard.'&nbsp;&nbsp;(<a href="mod.php?cancelcopy=true&amp;sesskey='.$USER->sesskey.'">'.$strcancel.'</a>)';
    echo '</td>';
    echo '</tr>';
}

/// Print Section 0

$section = 0;
$thissection = $sections[$section];

if ($thissection->summary or $thissection->sequence or isediting($course->id)) {

    echo '<tr id="section-0" class="section main">';
    echo '<td class="left side">&nbsp;</td>';
    echo '<td class="content">';

    echo '<div class="summary">';
    $summaryformatoptions->noclean = true;
    echo format_text($thissection->summary, FORMAT_HTML, $summaryformatoptions);

    if (isediting($course->id) && has_capability('moodle/course:update', $course->id)) {
        echo '<a title="'.$streditsummary.'" '.
            ' href="editsection.php?id='.$thissection->id.'"><img src="'.$CFG->pixpath.'/t/edit.gif" '.
            ' class="iconsmall" alt="'.$streditsummary.'" /></a><br /><br />';
    }
    echo '</div>';
    if(!isset($CFG->lams_serverurl)||!isset($CFG->lams_serverid)||!isset($CFG->lams_serverkey)){
        echo '<table width="100%" class="section">'.
            '<tr>'.
            '<td class="activity forum">'.
            '<table align="center" width="100%"  class="noticebox" border="0" cellpadding="15" cellspacing="0">'.
            '<tr><td bgcolor="#FFAAAA" class="noticeboxcontent">'.
            '<h3   class="main">All the LAMS module settings have not been set up!<br/> Please contact your administrator.</h3>'.
            '</td></tr></table>'.                                        
            '</td>'.
            '</tr>'.
            '</table>';
    }else{
        if(isediting($course->id)){//editing turned on. In this case
            echo '<table width="100%" class="section"><tr>';
            echo '<td align="left"><img src="../mod/lams/icon.gif" class="iconsmall" boarder="1" alt="LAMS"/>&nbsp;LAMS course</td>';

            $datetime =    date("F d,Y g:i a");
            $plaintext = trim($datetime).trim($USER->username).trim($LAMSCONSTANTS->author_method).trim($CFG->lams_serverid).trim($CFG->lams_serverkey);
            $hash = sha1(strtolower($plaintext));
            $author_url = $CFG->lams_serverurl.$LAMSCONSTANTS->login_request.
                '?'.$LAMSCONSTANTS->param_uid.'='.$USER->username.
                '&'.$LAMSCONSTANTS->param_method.'='.$LAMSCONSTANTS->author_method.
                '&'.$LAMSCONSTANTS->param_timestamp.'='.urlencode($datetime).
                '&'.$LAMSCONSTANTS->param_serverid.'='.$CFG->lams_serverid.
                '&'.$LAMSCONSTANTS->param_hash.'='.$hash.
                '&'.$LAMSCONSTANTS->param_courseid.'='.$course->id;
            echo '<div style="text-align: right"><td align="right">';
            //echo '<a target="popup" title="Open Author" href="../help.php?module=moodle&amp;file=resource/types.html"><span class="helplink"><img class="iconhelp" alt="Open Author" src="../pix/help.gif" /></span></a>';
            print_simple_box_start('right');
            echo '<a target="LAMS Author" title="LAMS Author" href="'.$author_url.'">'.get_string("openauthor", "lams").'</a>';
            print_simple_box_end();

            $datetime =    date("F d,Y g:i a");
            $plaintext = trim($datetime).trim($USER->username).trim($LAMSCONSTANTS->monitor_method).trim($CFG->lams_serverid).trim($CFG->lams_serverkey);
            $hash = sha1(strtolower($plaintext));
            $monitor_url = $CFG->lams_serverurl.$LAMSCONSTANTS->login_request.
                '?'.$LAMSCONSTANTS->param_uid.'='.$USER->username.
                '&'.$LAMSCONSTANTS->param_method.'='.$LAMSCONSTANTS->monitor_method.
                '&'.$LAMSCONSTANTS->param_timestamp.'='.urlencode($datetime).
                '&'.$LAMSCONSTANTS->param_serverid.'='.$CFG->lams_serverid.
                '&'.$LAMSCONSTANTS->param_hash.'='.$hash.
                '&'.$LAMSCONSTANTS->param_courseid.'='.$course->id;

            //echo '<a target="popup" title="Open Monitor" href="../help.php?module=moodle&amp;file=resource/types.html"><span class="helplink"><img class="iconhelp" alt="Open Monitor" src="../pix/help.gif" /></span></a>';                                                 
            print_simple_box_start('right');
            echo '<a target="LAMS Monitor" title="LAMS Monitor" href="'.$monitor_url.'">'.get_string("openmonitor", "lams").'</a>';
            print_simple_box_end();
            echo '</td></div>';

            echo '</tr></table>';                                                 

        }else{//editing turned off
            $datetime =    date("F d,Y g:i a");
            $plaintext = trim($datetime).trim($USER->username).trim($LAMSCONSTANTS->learner_method).trim($CFG->lams_serverid).trim($CFG->lams_serverkey);
            $hash = sha1(strtolower($plaintext));
            $url = $CFG->lams_serverurl.$LAMSCONSTANTS->login_request.
                '?'.$LAMSCONSTANTS->param_uid.'='.$USER->username.
                '&'.$LAMSCONSTANTS->param_method.'='.$LAMSCONSTANTS->learner_method.
                '&'.$LAMSCONSTANTS->param_timestamp.'='.urlencode($datetime).
                '&'.$LAMSCONSTANTS->param_serverid.'='.$CFG->lams_serverid.
                '&'.$LAMSCONSTANTS->param_hash.'='.$hash.
                '&'.$LAMSCONSTANTS->param_courseid.'='.$course->id;

            echo '<table width="100%" height="600" class="section">'.
                '<tr>'.
                '<td class="activity forum">'.
                '<iframe name="iframe" id="iframe" src="'.$url.'"  width="100%" height="100%" frameborder="1">'.
                '</iframe>'.
                '</td>'.
                '</tr>'.
                '</table>';
        }
    }                            
    //print_section($course, $thissection, $mods, $modnamesused);

    /*if (isediting($course->id)) {
      print_section_add_menus($course, $section, $modnames);
      }*/

    echo '</td>';
    echo '<td class="right side">&nbsp;</td>';
    echo '</tr>';
    echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';
}


/// Now all the normal modules by topic
/// Everything below uses "section" terminology - each "section" is a topic.

$timenow = time();
$section = 1;
$sectionmenu = array();

while ($section <= $course->numsections) {

    if (!empty($sections[$section])) {
        $thissection = $sections[$section];

    } else {
        unset($thissection);
        $thissection->course = $course->id;   // Create a new section structure
        $thissection->section = $section;
        $thissection->summary = '';
        $thissection->visible = 1;
        if (!$thissection->id = insert_record('course_sections', $thissection)) {
            notify('Error inserting new topic!');
        }
    }

    $showsection = (has_capability('moodle/course:viewhiddensections', $context) or $thissection->visible or !$course->hiddensections);

    if (!empty($displaysection) and $displaysection != $section) {
        if ($showsection) {
            $strsummary = ' - '.strip_tags($thissection->summary);
            if (strlen($strsummary) < 57) {
                $strsummary = ' - '.$strsummary;
            } else {
                $strsummary = ' - '.substr($strsummary, 0, 60).'...';
            }
            $sectionmenu['topic='.$section] = s($section.$strsummary);
        }
        $section++;
        continue;
    }

    if ($showsection) {

        $currenttopic = ($course->marker == $section);

        $currenttext = '';
        if (!$thissection->visible) {
            $sectionstyle = ' hidden';
        } else if ($currenttopic) {
            $sectionstyle = ' current';
            $currenttext = get_accesshide(get_string('currenttopic','access'));
        } else {
            $sectionstyle = '';
        }

        echo '<tr id="section-'.$section.'" class="section main'.$sectionstyle.'">';

        echo '<td class="left side">&nbsp;'.$currenttext.'</td>';

        echo '<td class="content">';
        if (!has_capability('moodle/course:viewhiddensections', $context) and !$thissection->visible) {   // Hidden for students
            echo get_string('notavailable');
        } else {
            echo '<div class="summary">';
            $summaryformatoptions->noclean = true;
            echo format_text($thissection->summary, FORMAT_HTML, $summaryformatoptions);

            if (isediting($course->id) && has_capability('moodle/course:update', $course->id)) {
                echo ' <a title="'.$streditsummary.'" href="editsection.php?id='.$thissection->id.'">'.
                    '<img src="'.$CFG->pixpath.'/t/edit.gif" class="iconsmall" alt="'.$streditsummary.'" /></a><br /><br />';
            }
            echo '</div>';

            print_section($course, $thissection, $mods, $modnamesused);

            if (isediting($course->id)) {
                print_section_add_menus($course, $section, $modnames);
            }
        }
        echo '</td>';

        echo '<td class="right side">';
        if ($displaysection == $section) {      // Show the zoom boxes
            echo '<a href="view.php?id='.$course->id.'&amp;topic=0#section-'.$section.'" title="'.$strshowalltopics.'">'.
                '<img src="'.$CFG->pixpath.'/i/all.gif" height="25" width="16" border="0" alt="'.$strshowalltopics.'" /></a><br />';
        } else {
            $strshowonlytopic = get_string('showonlytopic', '', $section);
            echo '<a href="view.php?id='.$course->id.'&amp;topic='.$section.'" title="'.$strshowonlytopic.'">'.
                '<img src="'.$CFG->pixpath.'/i/one.gif" height="16" width="16" border="0" alt="'.$strshowonlytopic.'" /></a><br />';
        }

            if (isediting($course->id) && has_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $course->id))) {
            if ($course->marker == $section) {  // Show the "light globe" on/off
                echo '<a href="view.php?id='.$course->id.'&amp;marker=0&amp;sesskey='.$USER->sesskey.'#section-'.$section.'" title="'.$strmarkedthistopic.'">'.
                    '<img src="'.$CFG->pixpath.'/i/marked.gif" height="16" width="16" border="0" alt="'.$strmarkedthistopic.'" /></a><br />';
            } else {
                echo '<a href="view.php?id='.$course->id.'&amp;marker='.$section.'&amp;sesskey='.$USER->sesskey.'#section-'.$section.'" title="'.$strmarkthistopic.'">'.
                    '<img src="'.$CFG->pixpath.'/i/marker.gif" height="16" width="16" border="0" alt="'.$strmarkthistopic.'" /></a><br />';
            }

            if ($thissection->visible) {        // Show the hide/show eye
                echo '<a href="view.php?id='.$course->id.'&amp;hide='.$section.'&amp;sesskey='.$USER->sesskey.'#section-'.$section.'" title="'.$strtopichide.'">'.
                    '<img src="'.$CFG->pixpath.'/i/hide.gif" height="16" width="16" border="0" alt="'.$strtopichide.'" /></a><br />';
            } else {
                echo '<a href="view.php?id='.$course->id.'&amp;show='.$section.'&amp;sesskey='.$USER->sesskey.'#section-'.$section.'" title="'.$strtopicshow.'">'.
                    '<img src="'.$CFG->pixpath.'/i/show.gif" height="16" width="16" border="0" alt="'.$strtopicshow.'" /></a><br />';
            }

            if ($section > 1) {                       // Add a arrow to move section up
                echo '<a href="view.php?id='.$course->id.'&amp;random='.rand(1,10000).'&amp;section='.$section.'&amp;move=-1&amp;sesskey='.$USER->sesskey.'#section-'.($section-1).'" title="'.$strmoveup.'">'.
                    '<img src="'.$CFG->pixpath.'/t/up.gif" class="iconsmall" alt="'.$strmoveup.'" /></a><br />';
            }

            if ($section < $course->numsections) {    // Add a arrow to move section down
                echo '<a href="view.php?id='.$course->id.'&amp;random='.rand(1,10000).'&amp;section='.$section.'&amp;move=1&amp;sesskey='.$USER->sesskey.'#section-'.($section+1).'" title="'.$strmovedown.'">'.
                    '<img src="'.$CFG->pixpath.'/t/down.gif" class="iconsmall" alt="'.$strmovedown.'" /></a><br />';
            }

        }

        echo '</td></tr>';
        echo '<tr class="section separator"><td colspan="3" class="spacer"></td></tr>';
    }

    $section++;
}
echo '</table>';

if (!empty($sectionmenu)) {
    echo '<div class="jumpmenu">';
    echo popup_form($CFG->wwwroot.'/course/view.php?id='.$course->id.'&', $sectionmenu,
            'sectionmenu', '', get_string('jumpto'), '', '', true);
    echo '</div>';
}


echo '</td>';

// The right column
if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $editing) {
    echo '<td style="width:'.$preferred_width_right.'px" id="right-column">';
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
    echo '</td>';
}

echo '</tr></table>';

?>
