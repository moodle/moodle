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


if (isset($_SERVER['HTTP_REFERER'])) {
    $referrer = $_SERVER['HTTP_REFERER'];
} else {
    $referrer = $CFG->wwwroot.'/';
}


// Ensure that the logged in user is not using the guest account
if (isguest()) {
    print_error('noguestpost', 'forum', $referrer);
}


$url = optional_param('url','',PARAM_URL);

if (!empty($url)) {
    // attempting to replace feed and rss url types with http
    // it appears that the rss feed validator will validate these url types but magpie will not load them    $url = str_replace ("feed://", "http://", "$url");
    // Shifting this forward since PARAM_URL rejects these feed types as invalid entries!
    $url = str_replace ("feed://", "http://", "$url");
    $url = str_replace ("FEED://", "http://", "$url");
    $url = str_replace ("rss://", "http://", "$url");
    $url = str_replace ("RSS://", "http://", "$url");
}

$act            = optional_param('act', NULL, PARAM_ALPHA);
$rssid          = optional_param('rssid', NULL, PARAM_INT);
$id             = optional_param('id', SITEID, PARAM_INT);
//$url            = clean_param($url, PARAM_URL);
$preferredtitle = optional_param('preferredtitle', '', PARAM_TEXT);
$shared         = optional_param('shared', 0, PARAM_INT);


if (!defined('MAGPIE_OUTPUT_ENCODING')) {
    define('MAGPIE_OUTPUT_ENCODING', 'utf-8');  // see bug 3107
}


if (!empty($id)) {
    // we get the complete $course object here because print_header assumes this is
    // a complete object (needed for proper course theme settings)
    if ($course = get_record('course', 'id', $id)) {
        $context = get_context_instance(CONTEXT_COURSE, $id);
    }
} else {
    $context = get_context_instance(CONTEXT_SYSTEM);
}


$straddedit = get_string('feedsaddedit', 'block_rss_client');
$link = $CFG->wwwroot.'/course/view.php?id='.$id;
if (empty($course)) {
    $link = '';
}
$navlinks = array();
$navlinks = array(array('name' => get_string('administration'), 'link' => "$CFG->wwwroot/$CFG->admin/index.php", 'type' => 'misc'));
$navlinks[] = array('name' => get_string('managemodules'), 'link' => null, 'type' => 'misc');
$navlinks[] = array('name' => get_string('blocks'), 'link' => null, 'type' => 'misc');
$navlinks[] = array('name' => get_string('feedstitle', 'block_rss_client'), 'link' => "$CFG->wwwroot/$CFG->admin/settings.php?section=blocksettingrss_client", 'type' => 'misc');
$navlinks[] = array('name' => get_string('addnew', 'block_rss_client'), 'link' => null,  'type' => 'misc');
$navigation = build_navigation($navlinks);
print_header($straddedit, $straddedit, $navigation);


if ( !isset($act) ) {
    rss_display_feeds($id, $USER->id, '', $context);
    rss_print_form($act, $url, $rssid, $preferredtitle, $shared, $id, $context);
    print_footer();
    die();
}

if ( isset($rssid) ) {
    $rss_record = get_record('block_rss_client', 'id', $rssid);
}


if (isset($rss_record)) {
    $managefeeds = ($rss_record->userid == $USER->id && has_capability('block/rss_client:manageownfeeds', $context))
                || ($rss_record->userid != $USER->id && has_capability('block/rss_client:manageanyfeeds', $context));
}


if ($act == 'updfeed') {

    if (!$managefeeds) {
        error(get_string('noguestpost', 'forum').
                ' You are not allowed to make modifications to this RSS feed at this time.',
                $referrer);
        //print_error('noguestpost', 'forum', $referrer, 'You are not allowed to make modifications to this RSS feed at this time.');
    }


    if (empty($url)) {
        error( 'URL not defined for rss feed' );
    }

    // By capturing the output from fetch_rss this way
    // error messages do not display and clutter up the moodle interface
    // however, we do lose out on seeing helpful messages like "cache hit", etc.
    $message = '';
    ob_start();
    $rss = fetch_rss($url);
    if (debugging()) {
        $message .= ob_get_contents();
    }
    ob_end_clean();

    $canaddsharedfeeds = has_capability('block/rss_client:createsharedfeeds', $context);

    $dataobject->id = $rssid;
    if ($rss === false) {
        $dataobject->description = '';
        $dataobject->title = '';
        $dataobject->preferredtitle = '';
        $dataobject->shared = 0;
    } else {
        $dataobject->description = addslashes($rss->channel['description']);
        $dataobject->title = addslashes($rss->channel['title']);
        $dataobject->preferredtitle = addslashes($preferredtitle);
        if ($shared == 1 && $canaddsharedfeeds) {
            $dataobject->shared = 1;
        } else {
            $dataobject->shared = 0;
        }
    }
    $dataobject->url = addslashes($url);

    if (!update_record('block_rss_client', $dataobject)) {
        error('There was an error trying to update rss feed with id:'. $rssid);
    }

    $message .= '<br />'. get_string('feedupdated', 'block_rss_client');
    redirect($referrer, $message);

} else if ($act == 'addfeed' ) {

    $canaddprivfeeds = has_capability('block/rss_client:createprivatefeeds', $context);
    $canaddsharedfeeds = has_capability('block/rss_client:createsharedfeeds', $context);

    if (!$canaddprivfeeds && !$canaddsharedfeeds) {
        error('You do not have the permission to add RSS feeds');
    }

    if (empty($url)) {
        error('URL not defined for rss feed');
    }
    $dataobject->userid = $USER->id;
    $dataobject->description = '';
    $dataobject->title = '';
    $dataobject->url = addslashes($url);
    $dataobject->preferredtitle = addslashes($preferredtitle);

    if ($shared == 1 && $canaddsharedfeeds) {
        $dataobject->shared = 1;
    } else {
        $dataobject->shared = 0;
    }

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
    if (debugging()) {
        $message .= ob_get_contents();
    }
    ob_end_clean();

    if ($rss === false) {
        $message .= '<br /><br />There was an error loading this rss feed. You may want to verify the url you have specified before using it.'; //Daryl Hawes note: localize this line
    } else {

        $dataobject->id = $rssid;
        if (!empty($rss->channel['description'])) {
            $dataobject->description = addslashes($rss->channel['description']);
        }
        if (!empty($rss->channel['title'])) {
            $dataobject->title = addslashes($rss->channel['title']);
        }
        if (!update_record('block_rss_client', $dataobject)) {
            error('There was an error trying to update rss feed with id:'. $rssid);
        }
        $message .= '<br />'. get_string('feedadded', 'block_rss_client');
    }
    redirect($referrer, $message);
/*
        rss_display_feeds($id, $USER->id, '', $context);
        rss_print_form($act, $dataobject->url, $dataobject->id, $dataobject->preferredtitle, $shared, $id, $context);
*/
} else if ( isset($rss_record) && $act == 'rssedit' ) {

    $preferredtitle = stripslashes_safe($rss_record->preferredtitle);
    if (empty($preferredtitle)) {
        $preferredtitle = stripslashes_safe($rss_record->title);
    }
    $url = stripslashes_safe($rss_record->url);
    $shared = stripslashes_safe($rss_record->shared);
    rss_display_feeds($id, $USER->id, $rssid, $context);
    rss_print_form($act, $url, $rssid, $preferredtitle, $shared, $id, $context);

} else if ($act == 'delfeed') {

    if (!$managefeeds) {
        error(get_string('noguestpost', 'forum').
                ' You are not allowed to make modifications to this RSS feed at this time.',
                $referrer);
        //print_error('noguestpost', 'forum', $referrer, 'You are not allowed to make modifications to this RSS feed at this time.');
    }

    $file = $CFG->dataroot .'/cache/rsscache/'. $rssid .'.xml';
    if (file_exists($file)) {
        unlink($file);
    }

    // echo "DEBUG: act = delfeed"; //debug
    delete_records('block_rss_client', 'id', $rssid);

    redirect($referrer, get_string('feeddeleted', 'block_rss_client') );

} else if ( isset($rss_record) && $act == 'view' ) {
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
            $feedtitle = $rss_record->preferredtitle;
        } else {
            $feedtitle =  $rss->channel['title'];
        }
        print '<table align="center" width="50%" cellspacing="1">'."\n";
        print '<tr><td colspan="2"><strong>'. $feedtitle .'</strong></td></tr>'."\n";
        for($y=0; $y < count($rss->items); $y++) {
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
            print '<td align="right">'."\n";

            // MDL-9291, blog this feature needs further discussion/implementation
            // temporarily disabling for now.

            // print '<img src="'. $CFG->pixpath .'/blog/blog.gif" alt="'. get_string('blogthis', 'blog').'" title="'. get_string('blogthis', 'blog') .'" border="0" align="middle" />'."\n";
            // print '<a href="'. $CFG->wwwroot .'/blog/blogthis.php?userid='. $USER->id .'&act=use&item='. $y .'&rssid='. $rssid .'"><small><strong>'. get_string('blogthis', 'blog') .'</strong></small></a>'."\n";
            print '</td></tr>'."\n";
            print '<tr><td colspan="2"><small>';
            print $rss->items[$y]['description'] .'</small></td></tr>'."\n";
        }
        print '</table>'."\n";
    }
} else {
    rss_display_feeds($id, $USER->id, '', $context);
    rss_print_form($act, $url, $rssid, $preferredtitle, $shared, $id, $context);
}
print_footer();
?>
