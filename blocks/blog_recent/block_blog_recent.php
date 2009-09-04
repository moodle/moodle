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
 * Recent Blog Entries Block page.
 *
 * @package    moodlecore
 * @subpackage blog
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot .'/blog/lib.php');
require_once($CFG->dirroot .'/blog/locallib.php');

/**
 * This block simply outputs a list of links to recent blog entries, depending on
 * the context of the current page.
 */
class block_blog_recent extends block_base {

    function init() {
        $this->title = get_string('blockrecenttitle', 'blog');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2009070900;
    }

    function get_content() {
        global $CFG, $USER, $PAGE, $DB;

        $this->content = new stdClass();
        $this->content->footer = '';

        $tag     = optional_param('tag', null, PARAM_NOTAGS);
        $tagid   = optional_param('tagid', null, PARAM_INT);
        $entryid = optional_param('entryid', null, PARAM_INT);
        $groupid = optional_param('groupid', null, PARAM_INT);
        $search  = optional_param('search', null, PARAM_RAW);

        //correct tagid if a text tag is provided as a param
        if (!empty($tag)) {  //text tag parameter takes precedence
            if ($tagrec = $DB->get_record_sql("SELECT * FROM {tag} WHERE name LIKE ?", array($tag))) {
                $tagid = $tagrec->id;
            } else {
                unset($tagid);
            }
        }

        $context = $PAGE->get_context();
        
        $strlevel = '';

        switch ($context->contextlevel) {
            case CONTEXT_COURSE:
                $strlevel = ($context->instanceid == SITEID) ? '' : get_string('course');
                break;
            case CONTEXT_MODULE:
                $strlevel = print_context_name($context);
                break;
            case CONTEXT_USER:
                $strlevel = get_string('user');
                break;
        }

        $filters = array();

        if (!empty($entryid)) {
            $filters['entry'] = $entryid;
        }

        if (!empty($groupid)) {
            $filters['group'] = $groupid;
        }

        if (!empty($tagid)) {
            $filters['tag'] = $tagid;
        }

        if (!empty($search)) {
            $filters['search'] = $search;
        }

        $blog_listing = new blog_listing($filters);
        $entries = $blog_listing->get_entries(0, get_user_preferences('blogrecententriesnumber', 4));

        $this->content->text = '<ul class="list">';
        $viewblog_url = $CFG->wwwroot . '/blog/index.php?entryid=';

        foreach ($entries as $entry_id => $entry) {
            $this->content->text .= "<li><a href=\"$viewblog_url$entry_id\">".shorten_text($entry->subject)."</a></li>\n";
        }

        $this->content->text .= '<li>&nbsp;</li>';
        $this->content->text .= '<li><a href="'.blog_get_context_url().'">'.get_string('viewallblogentries', 'blog', $strlevel).'</a></li>'; 
        $this->content->text .= '</ul>';
    }
}
