<?php //$Id$

class block_rss_client extends block_base {

    function init() {
        $this->title = get_string('block_rss_feeds_title', 'block_rss_client');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2004112000;
    }

    function specialization() {
        if (!empty($this->config) && !empty($this->config->title)) {
            $this->title = $this->config->title;
        }
    }
    
    function get_content() {
        global $CFG, $editing;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';        
        
        if (empty($this->instance) || empty($CFG->blog_version)) {
            // Either we're being asked for content without
            // an associated instance of the Blog module has never been installed.
            $this->content->text = '';
            return $this->content;
        }

        require_once($CFG->dirroot .'/rss/templib.php');
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
                $rssid = intval($this->config->rssid);
            }
            if (!empty($this->config->display_description)) {
                $display_description = intval($this->config->display_description);
            }
            if (!empty($this->config->shownumentries)) {
                $shownumentries = intval($this->config->shownumentries);
            }
        }

        if ($editing) {
            $submitters = $CFG->block_rss_client_submitters;

            $isteacher = false;
            $courseid = '';
            if ($this->instance->pagetype == MOODLE_PAGE_COURSE) {
                $isteacher = isteacher($this->instance->pageid);
                $courseid = $this->instance->pageid;
            }

            //if the user is an admin or course teacher then allow the user to
            //assign categories to other uses than personal
            if ( isadmin() || $submitters == 0 || ($submitters == 2 && $isteacher) ) {
                $output .= '<center><a href="'. $CFG->wwwroot .'/blocks/rss_client/block_rss_client_action.php?courseid='. $courseid .'">'. get_string('block_rss_feeds_add_edit', 'block_rss_client') .'</a></center><br /><br />';
            }
        }

        $rss_record = get_record('block_rss_client', 'id', $rssid);
        if (isset($rss_record) && isset($rss_record->id)) {
            $rss = rss_get_feed($rss_record->id, $rss_record->url, $rss_record->type);
    //      print_object($rss); //debug	

            if ($shownumentries > 0 && $shownumentries < count($rss->items) ) {
                $count_to = $shownumentries;
            } else {
                $count_to = count($rss->items);
            }

            for ($y = 0; $y < $count_to; $y++) {
                if ($rss->items[$y]['title'] == '') {
//                    $rss->items[$y]['description'] = blog_unhtmlentities($rss->items[$y]['description']);
                    //can define an additional instance/admin config item for this (20) - max_description_length
                    $rss->items[$y]['title'] = substr(strip_tags($rss->items[$y]['description']), 0, 20) . '...';
                }
        
                if ($rss->items[$y]['link'] == '') {
                    $rss->items[$y]['link'] = $rss->items[$y]['guid'];
                }

                $output .= '<a href="'. $rss->items[$y]['link'] .'" target=_new>'. $rss->items[$y]['title'] . '</a><br />' ."\n";
                
                if ($display_description){
                    $output .= $rss->items[$y]['description'] . '<br />' ."\n";
                }
            }

            $output .= '<br />';
    //      print_object($rss); //debug
            $feedtitle = get_string('block_rss_remote_news_feed', 'block_rss_client');
            
            if ( isset($rss->channel['link']) && isset($rss->channel['title']) ) {
                $feedtitle = '<a href="'. $rss->channel['link'] .'">'. $rss->channel['title'] .'</a>';
            }
        }

        //can we reset the title here?
        if (isset($feedtitle) && $feedtitle != '') {
            $this->title = $feedtitle;
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
}
?>