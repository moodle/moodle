<?php //$Id$

// Developer's debug assistant - if true then the display string will not cache, only
// the magpie object's built in caching will be used
define('BLOCK_RSS_SECONDARY_CACHE_ENABLED', true);

class block_rss_client extends block_base {

    function init() {
        $this->title = get_string('block_rss_feeds_title', 'block_rss_client');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2004112000;
    }

    function specialization() {
        // After the block has been loaded we customize the block's title display
        if (!empty($this->config) && !empty($this->config->title)) {
            // There is a customized block title, display it
            $this->title = $this->config->title;
        } else {
            // No customized block title, use localized remote news feed string
            $this->title = get_string('block_rss_remote_news_feed', 'block_rss_client');
        }
    }
    
    function get_content() {
        global $CFG, $editing;

        require_once($CFG->libdir .'/rsslib.php');

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        
        if (empty($this->instance)) {
            // We're being asked for content without an associated instance
            $this->content->text = '';
            return $this->content;
        }

        $output = '';
        $rssid = -1;
        $display_description = false;
        if (isset($CFG->block_rss_client_num_entries) && is_numeric($CFG->block_rss_client_num_entries) ) {
            $shownumentries = intval($CFG->block_rss_client_num_entries);
        } else {
            $shownumentries = 5; //default to 5 entries is not specified by admin or instance
        }

        if (!empty($this->config)) {
            if (!empty($this->config->rssid)) {
                if (is_array($this->config->rssid)) { 
                    $rssidarray = $this->config->rssid;
                } else {     // Make an array of the single value 
                    $rssidarray = array($this->config->rssid);
                }
            }
            if (!empty($this->config->display_description)) {
                $display_description = intval($this->config->display_description);
            }
            if (!empty($this->config->shownumentries)) {
                $shownumentries = intval($this->config->shownumentries);
            }
        }

        $submitters = $CFG->block_rss_client_submitters;

        $isteacher = false;
        $courseid = '';
        if ($this->instance->pagetype == MOODLE_PAGE_COURSE) {
            $isteacher = isteacher($this->instance->pageid);
            $courseid = $this->instance->pageid;
        }

        //if the user is an admin, course teacher, or all users are allowed
        // then allow the user to add rss feeds
        global $USER;
        $userisloggedin = false;
        if (isset($USER) && isset($USER->id) && $USER->id && !isguest()) {
            $userisloggedin = true;
        }
        if ( $userisloggedin && (isadmin() ||  $submitters == SUBMITTERS_ALL_ACCOUNT_HOLDERS || ($submitters == SUBMITTERS_ADMIN_AND_TEACHER && $isteacher)) ) {
            echo 'ADDING TO OUTPUT';
            $output .= '<div align="center"><a href="'. $CFG->wwwroot .'/blocks/rss_client/block_rss_client_action.php?courseid='. $courseid .'">'. get_string('block_rss_feeds_add_edit', 'block_rss_client') .'</a></div><br />';
        }

        // Daryl Hawes note: if count of rssidarray is greater than 1 
        // we should possibly display a drop down menu of selected feed titles
        // so user can select a single feed to view (similar to RSSFeed)
        if (!empty($rssidarray)) {
            $numids = count($rssidarray);
            $count = 0;
            foreach ($rssidarray as $rssid) {
                $rssfeedstring =  $this->get_rss_by_id($rssid, $display_description, $shownumentries, ($numids > 1) ? true : false);
                $output .= format_text($rssfeedstring);
                if ($numids > 1 && $count != $numids -1 && !empty($rssfeedstring)) {
                    $output .= '<hr width="80%" />';
                }
                $count ++;
            }
        }
        
        $this->content->text = $output;
        return $this->content;
    }
    
    function instance_allow_multiple() {
        return true;
    }

    function has_config() {
        return true;
    }

    function instance_allow_config() {
        return true;
    }
    
    /**
     * @param int $rssid The feed to be displayed
     * @param bool $display_description Should the description information from the feed be displayed or simply the title?
     * @param int $shownumentries The maximum number of feed entries to be displayed.
     * @param bool $showtitle True if the feed title should be displayed above the feed entries.
     * @return string|NULL
     */
    function get_rss_by_id($rssid, $display_description, $shownumentries, $showtitle=false) {
        global $CFG;
        $returnstring = '';
        $now = time();
        require_once($CFG->libdir .'/rsslib.php');
        require_once(MAGPIE_DIR .'rss_fetch.inc');
        
        // Check if there is a cached string which has not timed out.
        if (BLOCK_RSS_SECONDARY_CACHE_ENABLED &&
                isset($this->config->{'rssid'. $rssid}) && 
                    isset($this->config->{'rssid'. $rssid .'timestamp'}) && 
                        $this->config->{'rssid'. $rssid .'timestamp'} >= $now - $CFG->block_rss_timeout * 60) {
            // If the cached string is not too stale 
            // use it rather than going any further
            return stripslashes_safe($this->config->{'rssid'. $rssid});
        }

        $rss_record = get_record('block_rss_client', 'id', $rssid);
        if (isset($rss_record) && isset($rss_record->id)) {
                    
            // By capturing the output from fetch_rss this way
            // error messages do not display and clutter up the moodle interface
            // however, we do lose out on seeing helpful messages like "cache hit", etc.
            ob_start();
            $rss = fetch_rss($rss_record->url);
            $rsserror = ob_get_contents();
            ob_end_clean();
            
            if ($rss === false) {
                if ($CFG->debug && !empty($rsserror)) {
                    // There was a failure in loading the rss feed, print link to full error text
                    return '<a href="'. $CFG->wwwroot .'/blocks/rss_client/block_rss_client_error.php?error='. urlencode($rsserror) .'">Error loading a feed.</a><br />'; //Daryl Hawes note: localize this line
                }
            }

            if ($shownumentries > 0 && $shownumentries < count($rss->items) ) {
                $rss->items = array_slice($rss->items, 0, $shownumentries);
            }

            if (empty($rss_record->preferredtitle)) {
                $feedtitle =  stripslashes_safe(rss_unhtmlentities($rss->channel['title']));
            } else {
                $feedtitle = stripslashes_safe($rss_record->preferredtitle);
            }
//            print_object($rss);
            if (isset($this->config) && 
                    isset($this->config->block_rss_client_show_channel_image) && 
                        $this->config->block_rss_client_show_channel_image &&
                            isset($rss->image) && isset($rss->image['link']) && isset($rss->image['title']) && isset($rss->image['url']) ) {
                $returnstring .= '<div class="rssclientimage"><a href="'. $rss->image['link'] .'"><img src="'. $rss->image['url'] .'" alt="'. $rss->image['title'] .'"/></a></div><br />';
            }

            if ($showtitle) {
                $returnstring .= '<div class="rssclienttitle">'. $feedtitle .'</div><br /><br />';
            }                        

            foreach ($rss->items as $item) {
                $item['title'] = stripslashes_safe(rss_unhtmlentities($item['title']));
                $item['description'] = stripslashes_safe(rss_unhtmlentities($item['description']));
                if ($item['title'] == '') {
                    // no title present, use portion of description
                    $item['title'] = substr(strip_tags($item['description']), 0, 20) . '...';
                }
        
                if ($item['link'] == '') {
                    $item['link'] = $item['guid'];
                }

                $item['link'] = str_replace('&', '&amp;', $item['link']);

                $returnstring .= '<div class="rssclientlink"><a href="'. $item['link'] .'" target="_new">'. $item['title'] . '</a></div>' ."\n";
                
                if ($display_description && !empty($item['description'])){
                    $returnstring .= '<div class="rssclientdescription">'.clean_text($item['description']) . '</div>' ."\n";
                }
            }

            if (!empty($rss->channel['link'])) {
                if (!empty($this->config) && isset($this->config->block_rss_client_show_channel_link) && $this->config->block_rss_client_show_channel_link) {
                    $returnstring .=  '<div class="rssclientchannellink"><br /><a href="'. $rss->channel['link'] .'">'. get_string('block_rss_client_channel_link', 'block_rss_client') .'</a></div>';
                } 
                if (!empty($feedtitle) ) {
                    $feedtitle = '<a href="'. $rss->channel['link'] .'">'. $feedtitle .'</a>';
                }
            }
        }

        if (!empty($feedtitle) and ($feedtitle != '<a href="'. $rss->channel['link'] .'"></a>')) {
            $this->title = $feedtitle;
        }
        
        // store config setting for this rssid so we do not need to read from file each time
        $this->config->{'rssid'. $rssid} = addslashes($returnstring);
        $this->config->{'rssid'. $rssid .'timestamp'} = $now; 
        $this->instance_config_save($this->config);
        return $returnstring;
    }
}
?>