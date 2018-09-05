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
 * Moodle's crisp theme, an example of how to make a Bootstrap theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package   theme_crisp
 * @copyright 2014 dualcube {@link http://dualcube.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot .'/blog/renderer.php');

class theme_crisp_core_blog_renderer extends core_blog_renderer {
    public function render_blog_entry(blog_entry $entry) {
        global $CFG, $DB;
        $syscontext = context_system::instance();
        $stredit = get_string('edit');
        $strdelete = get_string('delete');
        $filename = array();
        foreach ($entry->renderable->attachments as $file => $fileval) {
            $filename[] = $fileval->filename;
        }
        $attachedfiles = $DB->get_records_sql('select * from {files} mf where mf.component = ?
          and mf.mimetype IS NOT NULL AND mf.filearea = ? AND mf.filename IN (?)',
          array('blog', 'attachment', implode("\", \"", $filename)));
        if (isset($attachedfiles) && !empty($attachedfiles)) {
              $resourceclass = '';
              $storyclass = '';
            foreach ($attachedfiles as $attachedfile) {
                  $val = $attachedfile->mimetype;
                  $res = strpos($val, '/');
                  $result = substr($val, 0, $res);
                if ($result == "audio" || $result == "video") {
                    $resourceclass = ' resourceclass ';
                } else if ($result == "image" || $result == "application") {
                    $storyclass = ' storyclass ';
                }
            }
        }
        $o = '';
        // Header.
        $mainclass = 'forumpost blog_entry blog clearfix isotope-item item '. $resourceclass. $storyclass;
        if ($entry->renderable->unassociatedentry) {
            $mainclass .= 'draft';
        } else {
            $mainclass .= $entry->publishstate;
        }
        $o .= $this->output->container_start($mainclass, 'b' . $entry->id);
        $o .= $this->output->container_start('row header clearfix');
        // Attachments.
        $o .= '<div class="attachedfile" id="attach'.$entry->id.'">';
        $attachmentsoutputs = array();
        if ($entry->renderable->attachments) {
            foreach ($entry->renderable->attachments as $attachment) {
                $o .= $this->render($attachment, false);
            }
        }
        $o .= '</div>';
        $o .= $this->output->container_start('topicheading');
        // Title.
        $titlelink = html_writer::link(new moodle_url('/blog/index.php',
           array('entryid' => $entry->id)), format_string($entry->subject));
        $o .= $this->output->container($titlelink, 'subject');
        // Adding external blog link.
        if (!empty($entry->renderable->externalblogtext)) {
            $o .= $this->output->container($entry->renderable->externalblogtext, 'externalblog');
        }
        // Closing subject tag and header tag.
        $o .= $this->output->container_end();
        $o .= $this->output->container_end();
        // Post content.
        $o .= $this->output->container_start('row maincontent clearfix');
        // Entry.
        $o .= $this->output->container_start('blogcontent ');
        // Determine text for publish state.
        switch ($entry->publishstate) {
            case 'draft':
                $blogtype = get_string('publishtonoone', 'blog');
                break;
            case 'site':
                $blogtype = get_string('publishtosite', 'blog');
                break;
            case 'public':
                $blogtype = get_string('publishtoworld', 'blog');
                break;
            default:
                $blogtype = '';
                break;
        }
        // Body.
        $o .= format_text($entry->summary, $entry->summaryformat, array('overflowdiv' => true));
        if (!empty($entry->uniquehash)) {
            // Uniquehash is used as a link to an external blog.
            $url = clean_param($entry->uniquehash, PARAM_URL);
            if (!empty($url)) {
                $o .= $this->output->container_start('externalblog');
                $o .= html_writer::link($url, get_string('linktooriginalentry', 'blog'));
                $o .= $this->output->container_end();
            }
        }
        // Links to tags.
        $officialtags = tag_get_tags_csv('post', $entry->id, TAG_RETURN_HTML, 'official');
        $defaulttags = tag_get_tags_csv('post', $entry->id, TAG_RETURN_HTML, 'default');
        if (!empty($CFG->usetags) && ($officialtags || $defaulttags) ) {
            $o .= $this->output->container_start('tags');
            if ($officialtags) {
                $o .= get_string('tags', 'tag') .': '. $this->output->container($officialtags, 'officialblogtags');
                if ($defaulttags) {
                    $o .= ', ';
                }
            }
            $o .= $defaulttags;
            $o .= $this->output->container_end();
        }
        // Add associations.
        if (!empty($CFG->useblogassociations) && !empty($entry->renderable->blogassociations)) {
            // First find and show the associated course.
            $assocstr = '';
            $coursesarray = array();
            foreach ($entry->renderable->blogassociations as $assocrec) {
                if ($assocrec->contextlevel == CONTEXT_COURSE) {
                    $coursesarray[] = $this->output->action_icon($assocrec->url, $assocrec->icon, null, array(), true);
                }
            }
            if (!empty($coursesarray)) {
                $assocstr .= get_string('associated', 'blog', get_string('course')) . ': ' . implode(', ', $coursesarray);
            }
            // Now show mod association.
            $modulesarray = array();
            foreach ($entry->renderable->blogassociations as $assocrec) {
                if ($assocrec->contextlevel == CONTEXT_MODULE) {
                    $str = get_string('associated', 'blog', $assocrec->type) . ': ';
                    $str .= $this->output->action_icon($assocrec->url, $assocrec->icon, null, array(), true);
                    $modulesarray[] = $str;
                }
            }
            if (!empty($modulesarray)) {
                if (!empty($coursesarray)) {
                    $assocstr .= '<br/>';
                }
                $assocstr .= implode('<br/>', $modulesarray);
            }
            // Adding the asociations to the output.
            $o .= $this->output->container($assocstr, 'tags');
        }
        if ($entry->renderable->unassociatedentry) {
            $o .= $this->output->container(get_string('associationunviewable', 'blog'), 'noticebox');
        }
        // Commands.
        $o .= $this->output->container_start('commands');
        if ($entry->renderable->usercanedit) {
            // External blog entries should not be edited.
            if (empty($entry->uniquehash)) {
                $o .= html_writer::link(new moodle_url('/blog/edit.php',
                                                      array('action' => 'edit', 'entryid' => $entry->id)),
                                                      $stredit) . ' | ';
            }
            $o .= html_writer::link(new moodle_url('/blog/edit.php',
                                                  array('action' => 'delete', 'entryid' => $entry->id)),
                                                  $strdelete) . ' | ';
        }
        $entryurl = new moodle_url('/blog/index.php', array('entryid' => $entry->id));
        $o .= html_writer::link($entryurl, get_string('permalink', 'blog'));
        $o .= $this->output->container_end();
        $o .= $this->output->container_end();
        // Closing maincontent div.
        $o .= $this->output->container('&nbsp;', 'side options');
        $o .= $this->output->container_end();
        $o .= $this->output->container_end();
        return $o;
    }
    public function render_blog_entry_attachment(blog_entry_attachment $attachment) {
        $syscontext = context_system::instance();
        $o = '<a class="fancybox" href="#">';
        // Image attachments don't get printed as links.
        if (file_mimetype_in_typegroup($attachment->file->get_mimetype(), 'web_image')) {
            $attrs = array('src' => $attachment->url, 'alt' => '');
            $o .= html_writer::empty_tag('img', $attrs);
            $class = 'attachedimages';
        } else {
            $image = $this->output->pix_icon(file_file_icon($attachment->file), $attachment->filename,
              'moodle', array('class' => 'icon'));
            $o = html_writer::link($attachment->url, $image);
            $o .= format_text(html_writer::link($attachment->url, $attachment->filename),
              FORMAT_HTML, array('context' => $syscontext));
            $class = 'attachments';
        }
        $o .= '</a>';
        return $this->output->container($o, $class);
    }
}
