<?php //$Id$

/*******************************************************************
* This file contains no classes. It will display a list of existing feeds
* defined for the site and allow add/edit/delete of site feeds.
*
* @author Daryl Hawes
* @version  $Id$
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package base
******************************************************************/

require_once('../../config.php');
require_once($CFG->libdir .'/rsslib.php');
require_once(MAGPIE_DIR .'rss_fetch.inc');

require_login();
global $USER;

//ensure that the logged in user is not using the guest account
if (isset($_SERVER['HTTP_REFERER'])) {
    $referrer = $_SERVER['HTTP_REFERER'];
} else {
    $referrer = $CFG->wwwroot;
}
if (isguest()) {
    error(get_string('noguestpost', 'forum'), $referrer);
}

$act            = optional_param('act', NULL, PARAM_ALPHA);
$rssid          = optional_param('rssid', NULL, PARAM_INT);
$id             = optional_param('id', SITEID, PARAM_INT);
$url            = optional_param('url', NULL, PARAM_URL);
$preferredtitle = optional_param('preferredtitle', '', PARAM_ALPHA);

if (!defined('MAGPIE_OUTPUT_ENCODING')) {
    define('MAGPIE_OUTPUT_ENCODING', get_string('thischarset'));  // see bug 3107
}

if (!empty($id)) {
    // we get the complete $course object here because print_header assumes this is 
    // a complete object (needed for proper course theme settings)
    $course = get_record('course', 'id', $id);
}

$straddedit = get_string('feedsaddedit', 'block_rss_client');
if ( isadmin() ) {
    $navigation = '<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/index.php">'.get_string('administration').'</a> -> '.
        '<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/configure.php">'.get_string('configuration').'</a> -> '.$straddedit;
} else if (!empty($course)) {
    $navigation = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$id.'">'.$course->shortname.'</a> -> '.$straddedit;
} else {
    $navigation = $straddedit;
}

print_header($straddedit, $straddedit, $navigation);

//check to make sure that the user is allowed to post new feeds
$submitters = $CFG->block_rss_client_submitters;
$isteacher  = empty($course) ? false : isteacher($id);

if ($act == NULL) {
    rss_display_feeds($id);
    rss_print_form($act, $url, $rssid, $preferredtitle, $id);
    print_footer();
    die();
}

if ($rssid != NULL) {
    $rss_record = get_record('block_rss_client', 'id', $rssid);
}

//if the user is an admin or course teacher then allow the user to
//assign categories to other uses than personal
if (isset($rss_record) && !( isadmin() || $submitters == SUBMITTERS_ALL_ACCOUNT_HOLDERS || 
        ($submitters == SUBMITTERS_ADMIN_AND_TEACHER && $isteacher) || 
            ( ($act == 'rss_edit' || $act == 'delfeed' || $act == 'updfeed') && $USER->id == $rss_record->userid)  ) ) {
        error(get_string('noguestpost', 'forum').' You are not allowed to make modifications to this RSS feed at this time.', $referrer);
}

if ($act == 'updfeed') {
    if (empty($url)) {
        error( 'url not defined for rss feed' );
    }

    // By capturing the output from fetch_rss this way
    // error messages do not display and clutter up the moodle interface
    // however, we do lose out on seeing helpful messages like "cache hit", etc.
    $message = '';
    ob_start();
    $rss = fetch_rss($url);
    if ($CFG->debug) {
        $message .= ob_get_contents();
    }
    ob_end_clean();

    $dataobject->id = $rssid;
    if ($rss === false) {
        $dataobject->description = '';
        $dataobject->title = '';
        $dataobject->preferredtitle = '';
    } else {
        $dataobject->description = addslashes(rss_unhtmlentities($rss->channel['description']));
        $dataobject->title = addslashes(rss_unhtmlentities($rss->channel['title']));
        $dataobject->preferredtitle = addslashes($preferredtitle);
    }
    $dataobject->url = addslashes($url);

    if (!update_record('block_rss_client', $dataobject)) {
        error('There was an error trying to update rss feed with id:'. $rssid);
    }

    $message .= '<br />'. get_string('feedupdated', 'block_rss_client');
    redirect($referrer, $message);

} else if ($act == 'addfeed' ) {

    if (empty($url)) {
        error('url not defined for rss feed');
    }
    $dataobject->userid = $USER->id;
    $dataobject->description = '';
    $dataobject->title = '';
    $dataobject->url = addslashes($url);
    $dataobject->preferredtitle = addslashes($preferredtitle);

    $rssid = insert_record('block_rss_client', $dataobject);
    if (!$rssid) {
        error('There was an error trying to add a new rss feed:'. $url);
    }

    // By capturing the output from fetch_rss this way
    // error messages do not display and clutter up the moodle interface
    // however, we do lose out on seeing helpful messages like "cache hit", etc.
    $message = '';
    ob_start();
    $rss = fetch_rss($url);
    if ($CFG->debug) {
        $message .= ob_get_contents();
    }
    ob_end_clean();

    if ($rss === false) {
        $message .= '<br /><br />There was an error loading this rss feed. You may want to verify the url you have specified before using it.'; //Daryl Hawes note: localize this line
    } else {

        $dataobject->id = $rssid;
        if (!empty($rss->channel['description'])) {
            $dataobject->description = addslashes(rss_unhtmlentities($rss->channel['description']));
        }
        if (!empty($rss->channel['title'])) {
            $dataobject->title = addslashes(rss_unhtmlentities($rss->channel['title']));
        } 
        if (!update_record('block_rss_client', $dataobject)) {
            error('There was an error trying to update rss feed with id:'. $rssid);
        }
        $message .= '<br />'. get_string('feedadded', 'block_rss_client');
    }
    redirect($referrer, $message);
/*
        rss_display_feeds($id);
        rss_print_form($act, $dataobject->url, $dataobject->id, $dataobject->preferredtitle, $id);
*/
} else if ( $rss_record != NULL && $act == 'rss_edit' ) {

    $preferredtitle = stripslashes_safe($rss_record->preferredtitle);
    if (empty($preferredtitle)) {
        $preferredtitle = stripslashes_safe($rss_record->title);
    }
    $url = stripslashes_safe($rss_record->url);
    rss_display_feeds($id, '', $rssid);
    rss_print_form($act, $url, $rssid, $preferredtitle, $id);

} else if ($act == 'delfeed') {

    $file = $CFG->dataroot .'/cache/rsscache/'. $rssid .'.xml';
    if (file_exists($file)) {
        unlink($file);
    }

    // echo "DEBUG: act = delfeed"; //debug
    delete_records('block_rss_client', 'id', $rssid);

    redirect($referrer, get_string('feeddeleted', 'block_rss_client') );

} else if ( $rss_record != NULL && $act == 'view' ) {
    //              echo $sql; //debug
    //              print_object($res); //debug
    if (!$rss_record->id) {
        print '<strong>'. get_string('couldnotfindfeed', 'block_rss_client') .': '. $rssid .'</strong>';
    } else {
        // By capturing the output from fetch_rss this way
        // error messages do not display and clutter up the moodle interface
        // however, we do lose out on seeing helpful messages like "cache hit", etc.
        ob_start();
        $rss = fetch_rss($rss_record->url);
        ob_end_clean();

        if (empty($rss_record->preferredtitle)) {
            $feedtitle = stripslashes_safe($rss_record->preferredtitle);
        } else {
            $feedtitle =  stripslashes_safe(rss_unhtmlentities($rss->channel['title']));
        }
        print '<table align="center" width="50%" cellspacing="1">'."\n";
        print '<tr><td colspan="2"><strong>'. $feedtitle .'</strong></td></tr>'."\n";
        for($y=0; $y < count($rss->items); $y++) {
            $rss->items[$y]['title'] = stripslashes_safe(rss_unhtmlentities($rss->items[$y]['title']));
            $rss->items[$y]['description'] = stripslashes_safe(rss_unhtmlentities($rss->items[$y]['description']));
            if ($rss->items[$y]['link'] == '') {
                $rss->items[$y]['link'] = $rss->items[$y]['guid'];
            }

            if ($rss->items[$y]['title'] == '') {
                $rss->items[$y]['title'] = '&gt;&gt;';
            }

            print '<tr><td valign="middle">'."\n";
            print '<a href="'. $rss->items[$y]['link'] .'" target="_blank"><strong>'. $rss->items[$y]['title'];
            print '</strong></a>'."\n";
            print '</td>'."\n";
            if (file_exists($CFG->dirroot .'/blog/lib.php')) {
                //Blog module is installed - provide "blog this" link
                print '<td align="right">'."\n";
                print '<img src="'. $CFG->pixpath .'/blog/blog.gif" alt="'. get_string('blogthis', 'blog').'" title="'. get_string('blogthis', 'blog') .'" border="0" align="middle" />'."\n";
                print '<a href="'. $CFG->wwwroot .'/blog/blogthis.php?userid='. $userid .'&act=use&item='. $y .'&rssid='. $rssid .'"><small><strong>'. get_string('blogthis', 'blog') .'</strong></small></a>'."\n";
            } else {
                print '<td>&nbsp;';
            }
            print '</td></tr>'."\n";
            print '<tr><td colspan=2><small>';
            print $rss->items[$y]['description'] .'</small></td></tr>'."\n";
        }
        print '</table>'."\n";
    }
} else {
    rss_display_feeds($id);
    rss_print_form($act, $url, $rssid, $preferredtitle, $id);
}
print_footer();
?>