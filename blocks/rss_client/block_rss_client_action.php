<?php //$Id$

    require_once('../../config.php');
    require_once($CFG->dirroot .'/rss/rsslib.php');
    require_once(MAGPIE_DIR .'rss_fetch.inc');
    global $USER, $CFG;
    
    require_login();
    
    //ensure that the logged in user is not using the guest account
    if (isset($_SERVER['HTTP_REFERER'])){
        $referrer = $_SERVER['HTTP_REFERER'];
    } else {
        $referrer = $CFG->wwwroot;
    }
    if (isguest()) {
        error(get_string('noguestpost', 'forum'), $referrer);
    }
    
    optional_variable($act, 'none');
    optional_variable($rssid, 'none');
    optional_variable($courseid, 'none');
    optional_variable($url);
    optional_variable($rsstype);
    optional_variable($item);
    
    print_header(get_string('block_rss_feeds_add_edit', 'block_rss_client'), 
                 get_string('block_rss_feeds_add_edit', 'block_rss_client'), 
                 get_string('block_rss_feeds_add_edit', 'block_rss_client') );

    //check to make sure that the user is allowed to post new feeds
    $submitters = $CFG->block_rss_client_submitters;
    $isteacher = false;
    if ($courseid != 'none'){
        $isteacher = isteacher($courseid);
    }
    //if the user is an admin or course teacher then allow the user to
    //assign categories to other uses than personal
    if (! ( isadmin() || $submitters == SUBMITTERS_ALL_ACCOUNT_HOLDERS || ($submitters == SUBMITTERS_ADMIN_AND_TEACHER && $isteacher) ) ) {
        error(get_string('noguestpost', 'forum'), $referrer);
    }

    if ($act == 'none') {
        rss_display_feeds();
        rss_get_form($act, $url, $rssid, $rsstype);

    } else if ($act == 'updfeed') {
        require_variable($url);
        
        // By capturing the output from fetch_rss this way
        // error messages do not display and clutter up the moodle interface
        // however, we do lose out on seeing helpful messages like "cache hit", etc.
        ob_start();
        $rss = fetch_rss($url);
        $rsserror = ob_get_contents();
        ob_end_clean();
        
        $dataobject->id = $rssid;
        $dataobject->type = $rsstype;
        if ($rss === false) {
            $dataobject->description = addslashes($rss->channel['description']);
            $dataobject->title = addslashes($rss->channel['title']);
        } else {
            $dataobject->description = '';
            $dataobject->title = '';
        }
        $dataobject->url = addslashes($url);
            
        if (!update_record('block_rss_client', $dataobject)) {
            error('There was an error trying to update rss feed with id:'. $rssid);
        }
                    
        rss_display_feeds($rssid);
        print '<strong>'. get_string('block_rss_feed_updated', 'block_rss_client') .'</strong>';                
        rss_get_form($act, $url, $rssid, $rsstype);
            
    } else if ($act == 'addfeed' ) {
    
        require_variable($url);
        require_variable($rsstype);
            
        $dataobject->userid = $USER->id;
        $dataobject->type = $rsstype;
        $dataobject->description = '';
        $dataobject->title = '';
        $dataobject->url = addslashes($url);
            
        $rssid = insert_record('block_rss_client', $dataobject);
        if (!$rssid){
            error('There was an error trying to add a new rss feed:'. $url);
        }
            
        // By capturing the output from fetch_rss this way
        // error messages do not display and clutter up the moodle interface
        // however, we do lose out on seeing helpful messages like "cache hit", etc.
        ob_start();
        $rss = fetch_rss($rss_record->url);
        $rsserror = ob_get_contents();
        ob_end_clean();
        
        if ($rss === false) {
            print 'There was an error loading this rss feed. You may want to verify the url you have specified before using it.'; //Daryl Hawes note: localize this line
        } else {
        
            $dataobject->id = $rssid;
            if (!empty($rss->channel['description'])) {
                $dataobject->description = addslashes($rss->channel['description']);
            } else if (!empty($rss->channel['title'])) {
                $dataobject->title = addslashes($rss->channel['title']);
            } 
            if (!update_record('block_rss_client', $dataobject)) {
                error('There was an error trying to update rss feed with id:'. $rssid);
            }
            print '<strong>'. get_string('block_rss_feed_added', 'block_rss_client') .'</strong>';
        }
        rss_display_feeds();
        rss_get_form($act, $url, $rssid, $rsstype);
            
    } else if ( $act == 'rss_edit') {
        
        $rss_record = get_record('block_rss_client', 'id', $rssid);
        $fname = stripslashes_safe($rss_record->title);
        $url = stripslashes_safe($rss_record->url);
        $rsstype = $rss_record->type;
        rss_get_form($act, $url, $rssid, $rsstype);
        
    } else if ($act == 'delfeed') {
        
        $file = $CFG->dataroot .'/cache/rsscache/'. $rssid .'.xml';
        if (file_exists($file)) {
            unlink($file);
        }

        // echo "DEBUG: act = delfeed"; //debug
        //Daryl Hawes note: convert this sql statement to a moodle function call
        $sql = 'DELETE FROM '. $CFG->prefix .'block_rss_client WHERE id='. $rssid;
        $res= $db->Execute($sql);

        rss_display_feeds();
        print '<strong>'. get_string('block_rss_feed_deleted', 'block_rss_client') .'</strong>';
        rss_get_form($act, $url, $rssid, $rsstype);

    } else if ($act == 'view') {
        global $THEME;
        //              echo $sql; //debug
        //              print_object($res); //debug
        $rss_record = get_record('block_rss_client', 'id', $rssid);
        if (!$rss_record->id){
            print '<strong>'. get_string('block_rss_could_not_find_feed', 'block_rss_client') .': '. $rssid .'</strong>';
        } else {
            // By capturing the output from fetch_rss this way
            // error messages do not display and clutter up the moodle interface
            // however, we do lose out on seeing helpful messages like "cache hit", etc.
            ob_start();
            $rss = fetch_rss($url);
            $rsserror = ob_get_contents();
            ob_end_clean();
            
            print '<table align=\"center\" width=\"50%\" cellspacing=\"1\">'."\n";
            print '<tr><td colspan=\"2\"><strong>'. $rss->channel['title'] .'</strong></td></tr>'."\n";
            for($y=0; $y < count($rss->items); $y++) {
//                $rss->items[$y]['title'] = blog_unhtmlentities($rss->items[$y]['title']);
                if ($rss->items[$y]['link'] == '') {
                    $rss->items[$y]['link'] = $rss->items[$y]['guid'];
                }
                
                if ($rss->items[$y]['title'] == '') {
                    $rss->items[$y]['title'] = '&gt;&gt;';
                }
                
                print '<tr bgcolor="'. $THEME->cellcontent .'"><td valign=\"middle\">'."\n";
                print '<a href="'. $rss->items[$y]['link'] .'" target=_new><strong>'. $rss->items[$y]['title'];
                print '</strong></a>'."\n";
                print '</td>'."\n";
                if (file_exists($CFG->dirroot .'/blog/lib.php')) {
                    print '<td align=\"right\">'."\n";
                    print '<img src="'. $CFG->pixpath .'/blog/blog.gif" alt="'. get_string('blog_blog_this', 'blog').'" title="'. get_string('blog_blog_this', 'blog') .'" border=\"0\" align=\"middle\" />'."\n";
                    print '<a href="'. $CFG->wwwroot .'/blog/blogthis.php?blogid='. $blogid .'&act=use&item='. $y .'&rssid='. $rssid .'"><small><strong>'. get_string('blog_blog_this', 'blog') .'</strong></small></a>'."\n";
                } else {
                    print '<td>&nbsp;';
                }
                print '</td></tr>'."\n";
//                $rss->items[$y]['description'] = blog_unhtmlentities($rss->items[$y]['description']);
                print '<tr bgcolor="'. $THEME->cellcontent2 .'"><td colspan=2><small>';
                print $rss->items[$y]['description'] .'</small></td></tr>'."\n";
            }
            print '</table>'."\n";
        }
    } else {
        rss_display_feeds();
        rss_get_form($act, $url, $rssid, $rsstype);
    }

    print_footer();
?>
