<?php //$Id$

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

        //if the user is an admin, course teacher, or all user are allowed - 
        // then allow the user to add rss feeds
        if ( !isguest() && (isadmin() ||  $submitters == SUBMITTERS_ALL_ACCOUNT_HOLDERS || ($submitters == SUBMITTERS_ADMIN_AND_TEACHER && $isteacher)) ) {
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
                if ($numids > 1 && $count != $numids -1) {
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
        require_once($CFG->dirroot .'/rss/rsslib.php');
        require_once(MAGPIE_DIR .'rss_fetch.inc');
        
        // Check if there is a cached string which has not timed out.
        if (isset($this->config->{'rssid'. $rssid}) && 
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
                    print '<a href="'. $CFG->wwwroot .'/blocks/rss_client/block_rss_client_error.php?error='. urlencode($rsserror) .'">Error loading a feed.</a><br />'; //Daryl Hawes note: localize this line
                }
                return;
            }
            
            if ($showtitle) {
                $returnstring .= '<p><div align="center" class="rssclienttitle">'. $rss_record->title .'</div></p>';
            }
            if ($shownumentries > 0 && $shownumentries < count($rss->items) ) {
                $rss->items = array_slice($rss->items, 0, $shownumentries);
            }

            foreach ($rss->items as $item) {
                if ($item['title'] == '') {
                    $item['title'] = substr(strip_tags($item['description']), 0, 20) . '...';
                }
        
                if ($item['link'] == '') {
                    $item['link'] = $item['guid'];
                }

                $returnstring .= '<div class="rssclientlink"><a href="'. $item['link'] .'" target="_new">'. $item['title'] . '</a></div>' ."\n";
                
                if ($display_description && !empty($item['description'])){
                    $returnstring .= '<div class="rssclientdescription">'.clean_text($item['description']) . '</div>' ."\n";
                }
            }

            if ( isset($rss->channel['link']) && isset($rss->channel['title']) ) {
                $feedtitle = '<a href="'. $rss->channel['link'] .'">'. $rss->channel['title'] .'</a>';
            }
        }

        if (isset($feedtitle) && $feedtitle != '' && $feedtitle != '<a href="'. $rss->channel['link'] .'"></a>') {
            $this->title = $feedtitle;
        }
        $returnstring .= '<br />';
        
        // store config setting for this rssid so we do not need to read from file each time
        $this->config->{'rssid'. $rssid} = addslashes($returnstring);
        $this->config->{'rssid'. $rssid .'timestamp'} = $now; 
        $this->instance_config_save($this->config);
        return $returnstring;
    }
}
?>
