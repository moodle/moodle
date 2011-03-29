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
 * Blog tags block.
 *
 * @package    block
 * @subpackage blog_tags
 * @copyright  2006 Shane Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('BLOCK_BLOG_TAGS_DEFAULTTIMEWITHIN', 90);
define('BLOCK_BLOG_TAGS_DEFAULTNUMBEROFTAGS', 20);
define('BLOCK_BLOG_TAGS_DEFAULTSORT', 'name');

class block_blog_tags extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_blog_tags');
    }

    function instance_allow_multiple() {
        return true;
    }

    function has_config() {
        return false;
    }

    function applicable_formats() {
        return array('all' => true, 'my' => false, 'tag' => false);
    }

    function instance_allow_config() {
        return true;
    }

    function specialization() {

        // load userdefined title and make sure it's never empty
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_blog_tags');
        } else {
            $this->title = $this->config->title;
        }
    }

    function get_content() {
        global $CFG, $SITE, $USER, $DB, $OUTPUT;

        if ($this->content !== NULL) {
            return $this->content;
        }

        // make sure blog and tags are actually enabled
        if (empty($CFG->bloglevel)) {
            $this->content = new stdClass();
            $this->content->text = '';
            if ($this->page->user_is_editing()) {
                $this->content->text = get_string('blogdisable', 'blog');
            }
            return $this->content;

        } else if (empty($CFG->usetags)) {
            $this->content = new stdClass();
            $this->content->text = '';
            if ($this->page->user_is_editing()) {
                $this->content->text = get_string('tagsaredisabled', 'tag');
            }
            return $this->content;

        } else if ($CFG->bloglevel < BLOG_GLOBAL_LEVEL and (!isloggedin() or isguestuser())) {
            $this->content = new stdClass();
            $this->content->text = '';
            return $this->content;
        }

        // require the libs and do the work
        require_once($CFG->dirroot .'/blog/lib.php');

        if (empty($this->config->timewithin)) {
            $this->config->timewithin = BLOCK_BLOG_TAGS_DEFAULTTIMEWITHIN;
        }
        if (empty($this->config->numberoftags)) {
            $this->config->numberoftags = BLOCK_BLOG_TAGS_DEFAULTNUMBEROFTAGS;
        }
        if (empty($this->config->sort)) {
            $this->config->sort = BLOCK_BLOG_TAGS_DEFAULTSORT;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        /// Get a list of tags
        $timewithin = time() - $this->config->timewithin * 24 * 60 * 60; /// convert to seconds

        $context = $this->page->context;

        // admins should be able to read all tags
        $type = '';
        if (!has_capability('moodle/user:readuserblogs', get_context_instance(CONTEXT_SYSTEM))) {
            $type = " AND (p.publishstate = 'site' or p.publishstate='public')";
        }

        $sql  = "SELECT t.id, t.tagtype, t.rawname, t.name, COUNT(DISTINCT ti.id) AS ct
                   FROM {tag} t, {tag_instance} ti, {post} p, {blog_association} ba
                  WHERE t.id = ti.tagid AND p.id = ti.itemid
                        $type
                        AND ti.itemtype = 'post'
                        AND ti.timemodified > $timewithin";

        if ($context->contextlevel == CONTEXT_MODULE) {
            $sql .= " AND ba.contextid = $context->id AND p.id = ba.blogid ";
        } else if ($context->contextlevel == CONTEXT_COURSE) {
            $sql .= " AND ba.contextid = $context->id AND p.id = ba.blogid ";
        }

        $sql .= "
               GROUP BY t.id, t.tagtype, t.name, t.rawname
               ORDER BY ct DESC, t.name ASC";

        if ($tags = $DB->get_records_sql($sql, null, 0, $this->config->numberoftags)) {

        /// There are 2 things to do:
        /// 1. tags with the same count should have the same size class
        /// 2. however many tags we have should be spread evenly over the
        ///    20 size classes

            $totaltags  = count($tags);
            $currenttag = 0;

            $size = 20;
            $lasttagct = -1;

            $etags = array();
            foreach ($tags as $tag) {

                $currenttag++;

                if ($currenttag == 1) {
                    $lasttagct = $tag->ct;
                    $size = 20;
                } else if ($tag->ct != $lasttagct) {
                    $lasttagct = $tag->ct;
                    $size = 20 - ( (int)((($currenttag - 1) / $totaltags) * 20) );
                }

                $tag->class = "$tag->tagtype s$size";
                $etags[] = $tag;

            }

        /// Now we sort the tag display order
            $CFG->tagsort = $this->config->sort;
            usort($etags, "block_blog_tags_sort");

        /// Finally we create the output
        /// Accessibility: markup as a list.
            $this->content->text .= "\n<ul class='inline-list'>\n";
            foreach ($etags as $tag) {
                $blogurl = new moodle_url('/blog/index.php');

                switch ($CFG->bloglevel) {
                    case BLOG_USER_LEVEL:
                        $blogurl->param('userid', $USER->id);
                    break;

                    default:
                        if ($context->contextlevel == CONTEXT_MODULE) {
                            $blogurl->param('modid', $context->instanceid);
                        } else if ($context->contextlevel == CONTEXT_COURSE) {
                            $blogurl->param('courseid', $context->instanceid);
                        }

                    break;
                }

                $blogurl->param('tagid', $tag->id);
                $link = html_writer::link($blogurl, tag_display_name($tag), array('class'=>$tag->class, 'title'=>get_string('numberofentries','blog',$tag->ct)));
                $this->content->text .= '<li>' . $link . '</li> ';
            }
            $this->content->text .= "\n</ul>\n";

        }
        return $this->content;
    }
}

function block_blog_tags_sort($a, $b) {
    global $CFG;

    if (empty($CFG->tagsort)) {
        return 0;
    } else {
        $tagsort = $CFG->tagsort;
    }

    if (is_numeric($a->$tagsort)) {
        return ($a->$tagsort == $b->$tagsort) ? 0 : ($a->$tagsort > $b->$tagsort) ? 1 : -1;
    } elseif (is_string($a->$tagsort)) {
        return strcmp($a->$tagsort, $b->$tagsort); //TODO: this is not compatible with UTF-8!!
    } else {
        return 0;
    }
}


