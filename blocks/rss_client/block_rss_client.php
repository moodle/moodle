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
 * Contains block_rss_client
 * @package    block_rss_client
 * @copyright  Daryl Hawes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

/**
 * A block which displays Remote feeds
 *
 * @package   block_rss_client
 * @copyright  Daryl Hawes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

 class block_rss_client extends block_base {
    /** @var bool track whether any of the output feeds have recorded failures */
    private $hasfailedfeeds = false;

    function init() {
        $this->title = get_string('pluginname', 'block_rss_client');
    }

    function applicable_formats() {
        return array('all' => true, 'tag' => false);   // Needs work to make it work on tags MDL-11960
    }

    function specialization() {
        // After the block has been loaded we customize the block's title display
        if (!empty($this->config) && !empty($this->config->title)) {
            // There is a customized block title, display it
            $this->title = $this->config->title;
        } else {
            // No customized block title, use localized remote news feed string
            $this->title = get_string('remotenewsfeed', 'block_rss_client');
        }
    }

    /**
     * Gets the footer, which is the channel link of the last feed in our list of feeds
     *
     * @param array $feedrecords The feed records from the database.
     * @return block_rss_client\output\footer|null The renderable footer or null if none should be displayed.
     */
    protected function get_footer($feedrecords) {
        global $PAGE;
        $footer = null;

        if ($this->config->block_rss_client_show_channel_link) {
            global $CFG;
            require_once($CFG->libdir.'/simplepie/moodle_simplepie.php');

            $feedrecord     = array_pop($feedrecords);
            $feed           = new moodle_simplepie($feedrecord->url);
            $channellink    = new moodle_url($feed->get_link());

            if (!empty($channellink)) {
                $footer = new block_rss_client\output\footer($channellink);
            }
        }

        if ($this->hasfailedfeeds) {
            if (has_any_capability(['block/rss_client:manageownfeeds', 'block/rss_client:manageanyfeeds'], $this->context)) {
                if ($footer === null) {
                    $footer = new block_rss_client\output\footer();
                }
                $manageurl = new moodle_url('/blocks/rss_client/managefeeds.php', ['courseid' => $PAGE->course->id]);
                $footer->set_failed($manageurl);
            }
        }

        return $footer;
    }

    function get_content() {
        global $CFG, $DB;

        if ($this->content !== NULL) {
            return $this->content;
        }

        // initalise block content object
        $this->content = new stdClass;
        $this->content->text   = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        if (!isset($this->config)) {
            // The block has yet to be configured - just display configure message in
            // the block if user has permission to configure it

            if (has_capability('block/rss_client:manageanyfeeds', $this->context)) {
                $this->content->text = get_string('feedsconfigurenewinstance2', 'block_rss_client');
            }

            return $this->content;
        }

        // How many feed items should we display?
        $maxentries = 5;
        if ( !empty($this->config->shownumentries) ) {
            $maxentries = intval($this->config->shownumentries);
        }elseif( isset($CFG->block_rss_client_num_entries) ) {
            $maxentries = intval($CFG->block_rss_client_num_entries);
        }

        /* ---------------------------------
         * Begin Normal Display of Block Content
         * --------------------------------- */

        $renderer = $this->page->get_renderer('block_rss_client');
        $block = new \block_rss_client\output\block();

        if (!empty($this->config->rssid)) {
            list($rssidssql, $params) = $DB->get_in_or_equal($this->config->rssid);
            $rssfeeds = $DB->get_records_select('block_rss_client', "id $rssidssql", $params);

            if (!empty($rssfeeds)) {
                $showtitle = false;
                if (count($rssfeeds) > 1) {
                    // When many feeds show the title for each feed.
                    $showtitle = true;
                }

                foreach ($rssfeeds as $feed) {
                    if ($renderablefeed = $this->get_feed($feed, $maxentries, $showtitle)) {
                        $block->add_feed($renderablefeed);
                    }
                }

                $footer = $this->get_footer($rssfeeds);
            }
        }

        $this->content->text = $renderer->render_block($block);
        if (isset($footer)) {
            $this->content->footer = $renderer->render_footer($footer);
        }

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
     * Returns the html of a feed to be displaed in the block
     *
     * @param mixed feedrecord The feed record from the database
     * @param int maxentries The maximum number of entries to be displayed
     * @param boolean showtitle Should the feed title be displayed in html
     * @return block_rss_client\output\feed|null The renderable feed or null of there is an error
     */
    public function get_feed($feedrecord, $maxentries, $showtitle) {
        global $CFG;
        require_once($CFG->libdir.'/simplepie/moodle_simplepie.php');

        if ($feedrecord->skipuntil) {
            // Last attempt to gather this feed via cron failed - do not try to fetch it now.
            $this->hasfailedfeeds = true;
            return null;
        }

        $simplepiefeed = new moodle_simplepie($feedrecord->url);

        if(isset($CFG->block_rss_client_timeout)){
            $simplepiefeed->set_cache_duration($CFG->block_rss_client_timeout * 60);
        }

        if ($simplepiefeed->error()) {
            debugging($feedrecord->url .' Failed with code: '.$simplepiefeed->error());
            return null;
        }

        if(empty($feedrecord->preferredtitle)){
            // Simplepie does escape HTML entities.
            $feedtitle = $this->format_title($simplepiefeed->get_title());
        }else{
            // Moodle custom title does not does escape HTML entities.
            $feedtitle = $this->format_title(s($feedrecord->preferredtitle));
        }

        if (empty($this->config->title)){
            //NOTE: this means the 'last feed' displayed wins the block title - but
            //this is exiting behaviour..
            $this->title = strip_tags($feedtitle);
        }

        $feed = new \block_rss_client\output\feed($feedtitle, $showtitle, $this->config->block_rss_client_show_channel_image);

        if ($simplepieitems = $simplepiefeed->get_items(0, $maxentries)) {
            foreach ($simplepieitems as $simplepieitem) {
                try {
                    $item = new \block_rss_client\output\item(
                        $simplepieitem->get_id(),
                        new moodle_url($simplepieitem->get_link()),
                        $simplepieitem->get_title(),
                        $simplepieitem->get_description(),
                        new moodle_url($simplepieitem->get_permalink()),
                        $simplepieitem->get_date('U'),
                        $this->config->display_description
                    );

                    $feed->add_item($item);
                } catch (moodle_exception $e) {
                    // If there is an error with the RSS item, we don't
                    // want to crash the page. Specifically, moodle_url can
                    // throw an exception of the param is an extremely
                    // malformed url.
                    debugging($e->getMessage());
                }
            }
        }

        // Feed image.
        if ($imageurl = $simplepiefeed->get_image_url()) {
            try {
                $image = new \block_rss_client\output\channel_image(
                    new moodle_url($imageurl),
                    $simplepiefeed->get_image_title(),
                    new moodle_url($simplepiefeed->get_image_link())
                );

                $feed->set_image($image);
            } catch (moodle_exception $e) {
                // If there is an error with the RSS image, we don'twant to
                // crash the page. Specifically, moodle_url can throw an
                // exception if the param is an extremely malformed url.
                debugging($e->getMessage());
            }
        }

        return $feed;
    }

    /**
     * Strips a large title to size and adds ... if title too long
     * This function does not escape HTML entities, so they have to be escaped
     * before being passed here.
     *
     * @param string title to shorten
     * @param int max character length of title
     * @return string title shortened if necessary
     */
    function format_title($title,$max=64) {

        if (core_text::strlen($title) <= $max) {
            return $title;
        } else {
            return core_text::substr($title, 0, $max - 3) . '...';
        }
    }

    /**
     * Return the plugin config settings for external functions.
     *
     * @return stdClass the configs for both the block instance and plugin
     * @since Moodle 3.8
     */
    public function get_config_for_external() {
        global $CFG;

        // Return all settings for all users since it is safe (no private keys, etc..).
        $instanceconfigs = !empty($this->config) ? $this->config : new stdClass();
        $pluginconfigs = (object) [
            'num_entries' => $CFG->block_rss_client_num_entries,
            'timeout' => $CFG->block_rss_client_timeout
        ];

        return (object) [
            'instance' => $instanceconfigs,
            'plugin' => $pluginconfigs,
        ];
    }
}
