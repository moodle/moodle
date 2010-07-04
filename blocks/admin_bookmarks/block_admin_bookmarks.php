<?php

// seems to work...
// maybe I should add some pretty icons?
// or possibly add the ability to custom-name things?

class block_admin_bookmarks extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_admin_bookmarks');
    }

    function applicable_formats() {
        if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
            return array('all' => true);
        } else {
            return array('site' => true);
        }
    }

    function preferred_width() {
        return 210;
    }

    function create_item($visiblename,$link,$icon) {
        $this->tempcontent .= '<a href="' . $link . '"><img src="' . $icon . '" alt="" /> ' . $visiblename . '</a><br />' . "\n";
    }

    function get_content() {

        global $CFG, $USER;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        if (get_user_preferences('admin_bookmarks')) {
            // this is expensive! Only require when bookmakrs exist..
            require_once($CFG->libdir.'/adminlib.php');
            $adminroot = admin_get_root(false, false);  // settings not required - only pages

            $bookmarks = explode(',', get_user_preferences('admin_bookmarks'));
            // hmm... just a liiitle (potentially) processor-intensive
            // (recall that $adminroot->locate is a huge recursive call... and we're calling it repeatedly here

            /// Accessibility: markup as a list.
            $this->content->text .= '<ol class="list">'."\n";

            foreach($bookmarks as $bookmark) {
                $temp = $adminroot->locate($bookmark);
                if ($temp instanceof admin_settingpage) {
                    $this->content->text .= '<li><a href="' . $CFG->wwwroot . '/' . $CFG->admin . '/settings.php?section=' . $bookmark . '">' . $temp->visiblename . "</a></li>\n";
                } else if ($temp instanceof admin_externalpage) {
                    $this->content->text .= '<li><a href="' . $temp->url . '">' . $temp->visiblename . "</a></li>\n";
                }
            }
            $this->content->text .= "</ol>\n";
        } else {
            $bookmarks = array();
        }

        if (isset($this->page->section) and $this->page->section == 'search'){
            // the search page can't be properly bookmarked at present
            $this->content->footer = '';
        } else if (($section = (isset($this->page->section) ? $this->page->section : '')) && (in_array($section, $bookmarks))) {
            $this->content->footer = '<a href="' . $CFG->wwwroot . '/blocks/admin_bookmarks/delete.php?section=' . $section . '&amp;sesskey='.sesskey().'">' . get_string('unbookmarkthispage','admin') . '</a>';
        } else if ($section = (isset($this->page->section) ? $this->page->section : '')) {
            $this->content->footer = '<a href="' . $CFG->wwwroot . '/blocks/admin_bookmarks/create.php?section=' . $section . '&amp;sesskey='.sesskey().'">' . get_string('bookmarkthispage','admin') . '</a>';
        } else {
            $this->content->footer = '';
        }

        return $this->content;
    }
}


