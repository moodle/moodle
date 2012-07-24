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
 * Renderers for outputting blog data
 *
 * @package    core_blog
 * @subpackage blog
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Blog renderer
 */
class core_blog_renderer extends plugin_renderer_base {

    /**
     * Renders a blog entry
     *
     * @param blog_entry $entry
     * @return string The table HTML
     */
    public function render_blog_entry(blog_entry $entry) {

        global $CFG;

        $stredit = get_string('edit');
        $strdelete = get_string('delete');

        // Start printing of the blog
        $table = new html_table();
        $table->cellspacing = 0;
        $table->attributes['class'] = 'forumpost blog_entry blog'. ($entry->renderable->unassociatedentry ? 'draft' : $entry->renderable->publishstate);
        $table->attributes['id'] = 'b'.$entry->id;
        $table->width = '100%';

        $picturecell = new html_table_cell();
        $picturecell->attributes['class'] = 'picture left';
        $picturecell->text = $this->output->user_picture($entry->renderable->user);

        $table->head[] = $picturecell;

        $topiccell = new html_table_cell();
        $topiccell->attributes['class'] = 'topic starter';
        $titlelink =  html_writer::link(new moodle_url('/blog/index.php', array('entryid' => $entry->id)), $entry->renderable->title);
        $topiccell->text = $this->output->container($titlelink, 'subject');
        $topiccell->text .= $this->output->container_start('author');

        // Post by
        $by = new stdClass();
        $coursecontext = get_context_instance(CONTEXT_COURSE, $this->page->course->id);
        $fullname = fullname($entry->renderable->user, has_capability('moodle/site:viewfullnames', $coursecontext));
        $userurlparams = array('id' => $entry->renderable->user->id, 'course' => $this->page->course->id);
        $by->name =  html_writer::link(new moodle_url('/user/view.php', $userurlparams), $fullname);
        $by->date = $entry->renderable->created;

        $topiccell->text .= get_string('bynameondate', 'forum', $by);
        $topiccell->text .= $this->output->container_end();

        // Adding external blog link
        if (!empty($entry->renderable->externalblogtext)) {
            $topiccell->text .= $this->output->container($entry->renderable->externalblogtext, 'externalblog');
        }

        $topiccell->header = false;
        $table->head[] = $topiccell;

        // Actual content
        $mainrow = new html_table_row();

        $leftsidecell = new html_table_cell();
        $leftsidecell->attributes['class'] = 'left side';
        $mainrow->cells[] = $leftsidecell;

        $contentcell = new html_table_cell();
        $contentcell->attributes['class'] = 'content';

        // Determine text for publish state
        switch ($entry->renderable->publishstate) {
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
        $contentcell->text .= $this->output->container($blogtype, 'audience');

        // Entry body
        $contentcell->text .= $entry->renderable->body;

        // Entry attachments
        $attachmentsoutputs = array();
        if ($entry->renderable->attachments) {
            foreach ($entry->renderable->attachments as $attachment) {
                $attachmentsoutputs[] = $this->render($attachment, false);
            }
            $contentcell->text .= $this->output->container(implode(', ', $attachmentsoutputs), 'attachments');
        }

        // Uniquehash is used as a link to an external blog
        if (!empty($entry->uniquehash)) {
            $contentcell->text .= $this->output->container_start('externalblog');
            $contentcell->text .= html_writer::link($entry->uniquehash, get_string('linktooriginalentry', 'blog'));
            $contentcell->text .= $this->output->container_end();
        }

        // Links to tags
        $officialtags = tag_get_tags_csv('post', $entry->id, TAG_RETURN_HTML, 'official');
        $defaulttags = tag_get_tags_csv('post', $entry->id, TAG_RETURN_HTML, 'default');

        if (!empty($CFG->usetags) && ($officialtags || $defaulttags) ) {
            $contentcell->text .= $this->output->container_start('tags');

            if ($officialtags) {
                $contentcell->text .= get_string('tags', 'tag') .': '. $this->output->container($officialtags, 'officialblogtags');
                if ($defaulttags) {
                    $contentcell->text .=  ', ';
                }
            }
            $contentcell->text .=  $defaulttags;
            $contentcell->text .= $this->output->container_end();
        }

        // Add associations
        if (!empty($CFG->useblogassociations) && !empty($entry->renderable->blogassociations)) {
            $contentcell->text .= $this->output->container_start('tags');
            $hascourseassocs = false;

            // First find and show the associated course
            $coursesstr = '';
            $coursesarray = array();
            foreach ($entry->renderable->blogassociations as $assocrec) {
                $context = get_context_instance_by_id($assocrec->contextid);
                if ($context->contextlevel ==  CONTEXT_COURSE) {
                    $coursesarray[] = $this->output->action_icon($assocrec->url, $assocrec->icon, null, array(), true);
                }
            }
            if (!empty($coursesarray)) {
                $coursesstr .= get_string('associated', 'blog', $assocrec->type) . ': ' . implode(', ', $coursesarray);
            }

            // Now show mod association
            $modulesstr = '';
            $modulesarray = array();
            foreach ($entry->renderable->blogassociations as $assocrec) {
                $context = get_context_instance_by_id($assocrec->contextid);

                if ($context->contextlevel ==  CONTEXT_MODULE) {
                    $str = get_string('associated', 'blog', $assocrec->type) . ': ';
                    $str .= $this->output->action_icon($assocrec->url, $assocrec->icon, null, array(), true);
                    $modulesarray[] = $str;
                }
            }
            if (!empty($modulesarray)) {
                if (!empty($coursesarray)) {
                    $modulesstr .= '<br/>';
                }
                $modulesstr .= implode('<br/>', $modulesarray);
            }

            $contentcell->text .= $coursesstr.$modulesstr;
            $contentcell->text .= $this->output->container_end();
        }

        if ($entry->renderable->unassociatedentry) {
            $contentcell->text .= $this->output->container(get_string('associationunviewable', 'blog'), 'noticebox');
        }

        /// Commands
        $contentcell->text .= $this->output->container_start('commands');
        if ($entry->renderable->usercanedit) {
            if (empty($entry->uniquehash)) {
                //External blog entries should not be edited
                $contentcell->text .= html_writer::link(new moodle_url('/blog/edit.php',
                                                        array('action' => 'edit', 'entryid' => $entry->id)),
                                                        $stredit) . ' | ';
            }
            $contentcell->text .= html_writer::link(new moodle_url('/blog/edit.php',
                                                    array('action' => 'delete', 'entryid' => $entry->id)),
                                                    $strdelete) . ' | ';
        }

        $entryurl = new moodle_url('/blog/index.php', array('entryid' => $entry->id));
        $contentcell->text .= html_writer::link($entryurl, get_string('permalink', 'blog'));

        $contentcell->text .= $this->output->container_end();

        if (isset($entry->renderable->lastmod) ) {
            $contentcell->text .= '<div>';
            $contentcell->text .= ' [ '.get_string('modified').': '.$entry->renderable->lastmod.' ]';
            $contentcell->text .= '</div>';
        }

        //add comments under everything
        if (!empty($entry->renderable->comment)) {
            $contentcell->text .= $entry->renderable->comment->output(true);
        }

        $mainrow->cells[] = $contentcell;
        $table->data = array($mainrow);

        return html_writer::table($table);
    }

    /**
     * Renders an entry attachment
     *
     * if return=html, then return a html string.
     * if return=text, then return a text-only string.
     * otherwise, print HTML for non-images, and return image HTML
     *
     * @param blog_entry_attachment $attachment
     * @param bool $return Whether to return or print the generated code
     * @return string List of attachments depending on the $return input
     */
    public function render_blog_entry_attachment(blog_entry_attachment $attachment, $return = false) {

        $syscontext = get_context_instance(CONTEXT_SYSTEM);
        $strattachment = get_string("attachment", "forum");

        $image = $this->output->pix_icon(file_file_icon($attachment->file), $attachment->filename, 'moodle', array('class'=>'icon'));

        $o = '';
        $imagereturn = '';

        if ($return == "html") {
            $o .= html_writer::link($attachment->url, $image);
            $o .= html_writer::link($attachment->url, $attachment->filename);

        } else if ($return == "text") {
            $o .= "$strattachment $attachment->filename:\n$attachment->url\n";

        } else {
            if (file_mimetype_in_typegroup($attachment->file->get_mimetype(), 'web_image')) {    // Image attachments don't get printed as links
                $imagereturn .= '<br /><img src="'.$attachment->url.'" alt="" />';
            } else {
                $imagereturn .= html_writer::link($attachment->url, $image);
                $imagereturn .= format_text(html_writer::link($attachment->url, $attachment->filename), FORMAT_HTML, array('context'=>$syscontext));
            }
        }

        if ($return) {
            return $o;
        }

        return $imagereturn;
    }
}
