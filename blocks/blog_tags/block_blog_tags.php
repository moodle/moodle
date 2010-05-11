<?php

define('BLOGDEFAULTTIMEWITHIN', 90);
define('BLOGDEFAULTNUMBEROFTAGS', 20);
define('BLOGDEFAULTSORT', 'name');

require_once($CFG->dirroot .'/blog/lib.php');

class block_blog_tags extends block_base {
    function init() {
        $this->version = 2007101509;
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

        if (empty($CFG->usetags) || empty($CFG->bloglevel)) {
            $this->content->text = '';
            if ($this->page->user_is_editing()) {
                $this->content->text = get_string('tagsaredisabled', 'tag');
            }
            return $this->content;
        }

        if (empty($this->config->timewithin)) {
            $this->config->timewithin = BLOGDEFAULTTIMEWITHIN;
        }
        if (empty($this->config->numberoftags)) {
            $this->config->numberoftags = BLOGDEFAULTNUMBEROFTAGS;
        }
        if (empty($this->config->sort)) {
            $this->config->sort = BLOGDEFAULTSORT;
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
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
            usort($etags, "blog_tags_sort");

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

function blog_tags_sort($a, $b) {
    global $CFG;

    if (empty($CFG->tagsort)) {
        return 0;
    } else {
        $tagsort = $CFG->tagsort;
    }

    if (is_numeric($a->$tagsort)) {
        return ($a->$tagsort == $b->$tagsort) ? 0 : ($a->$tagsort > $b->$tagsort) ? 1 : -1;
    } elseif (is_string($a->$tagsort)) {
        return strcmp($a->$tagsort, $b->$tagsort);
    } else {
        return 0;
    }
}


