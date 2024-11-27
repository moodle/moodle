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
 * Renderes file code.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/renderer.php');

/**
 * Educard renderer.
 *
 */
class theme_educard_core_course_renderer extends core_course_renderer {
    /**
     * Returns HTML to display course content (summary, course contacts and optionally category name)
     *
     * This method is called from coursecat_coursebox() and may be re-used in AJAX
     *
     * @param coursecat_helper $chelper various display options
     * @param stdClass|core_course_list_element $course
     * @return string
     */
    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course) {
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }
        $content = \html_writer::start_tag('div', ['class' => 'row']);

        $content .= \html_writer::start_tag('div', ['class' => 'col-md-6 col-lg-5 col-sm-12']);
        $content .= $this->course_overview_files($course);
        $content .= \html_writer::end_tag('div');

        $content .= \html_writer::start_tag('div', ['class' => 'col-md-6 col-lg-7']);
        $content .= $this->course_summary($chelper, $course);
        $content .= \html_writer::end_tag('div');

        $content .= \html_writer::end_tag('div');
        $content .= $this->course_category_name($chelper, $course);
        $content .= $this->course_custom_fields($course);
        return $content;
    }
    /**
     * Returns HTML to display course overview files.
     *
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_overview_files(core_course_list_element $course): string {
        global $CFG;
        $contentimages = $contentfiles = '';
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $url = moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php",
                '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
            if ($isimage) {
                $contentimages .= html_writer::start_tag('div',
                    ['class' => 'courseimage single-course', 'style' => 'background-image: url("'.$url.'");']);
                // Teacher img.
                $contentimages .= html_writer::start_tag('ul', ['class' => 'teachers']);
                foreach ($course->get_course_contacts() as $coursecontact) {
                    $rolenames = array_map(function ($role) {
                        return $role->displayname;
                    }, $coursecontact['roles']);
                    $namesrole = implode(", ", $rolenames);
                    $user = \core_user::get_user($coursecontact['user']->id);
                    $userpicture = new user_picture($user);
                    $picture = $userpicture->get_url($this->page)->out(false);
                    $username = format_string($coursecontact['username']);
                    $name = " <div class='chip h6'><img src='{$picture}'";
                    $name .= " class='border border-secondary' title='{$namesrole}' data-toggle='tooltip'";
                    $name .= " alt='{$username}'/>".html_writer::link(new moodle_url('/user/view.php',
                            ['id' => $coursecontact['user']->id, 'course' => SITEID]),
                            " ".$username)."</div>";
                    $contentimages .= html_writer::tag('li', $name);
                }
                $contentimages .= html_writer::end_tag('ul');

                $contentimages .= html_writer::end_tag('div');
            } else {
                $image = $this->output->pix_icon(file_file_icon($file), $file->get_filename(), 'moodle');
                $filename = html_writer::tag('span', $image, ['class' => 'fp-icon']).
                    html_writer::tag('span', $file->get_filename(), ['class' => 'fp-filename']);
                $contentfiles .= html_writer::tag('span',
                    html_writer::link($url, $filename),
                    ['class' => 'coursefile fp-filename-icon text-break']);
            }
        }
        // If the course image is empty, add a teacher image.
        if (empty($contentimages)) {
            $contentimages .= html_writer::start_tag('ul', ['class' => 'teachers']);
            foreach ($course->get_course_contacts() as $coursecontact) {
                $rolenames = array_map(function ($role) {
                    return $role->displayname;
                }, $coursecontact['roles']);
                $namesrole = implode(", ", $rolenames);
                $user = \core_user::get_user($coursecontact['user']->id);
                $userpicture = new user_picture($user);
                $picture = $userpicture->get_url($this->page)->out(false);
                $username = format_string($coursecontact['username']);
                $name = " <div class='chip h6'><img src='{$picture}'";
                $name .= " class='border border-secondary' title='{$namesrole}' data-toggle='tooltip'";
                $name .= " alt='{$username}'/>".html_writer::link(new moodle_url('/user/view.php',
                        ['id' => $coursecontact['user']->id, 'course' => SITEID]),
                        " ".$username)."</div>";
                $contentimages .= html_writer::tag('li', $name);
            }
            $contentimages .= html_writer::end_tag('ul');
        }
        return $contentimages . $contentfiles;
    }
}

require_once($CFG->dirroot . "/blog/renderer.php");
/**
 * Educard blog renderer.
 *
 */
class theme_educard_core_blog_renderer extends core_blog_renderer {
    /**
     * Returns HTML to blog entry.
     *
     * @param blog_entry $entry blog edit.
     *
     */
    public function render_blog_entry(blog_entry $entry) {

        global $CFG;

        $syscontext = context_system::instance();

        $stredit = get_string('edit');
        $strdelete = get_string('delete');

        // Header.
        $mainclass = 'forumpost blog_entry blog clearfix educard-blog ';
        if ($entry->renderable->unassociatedentry) {
            $mainclass .= 'draft';
        } else {
            $mainclass .= $entry->publishstate;
        }
        $o = $this->output->container_start($mainclass, 'b' . $entry->id);

        // Post content.
        $o .= $this->output->container_start('row maincontent clearfix educard-blog-post');

        // Entry.
        $o .= $this->output->container_start('no-overflow content ');

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
        $o .= $this->output->container($blogtype, 'audience');

        // Attachments.
        $attachmentsoutputs = [];
        if ($entry->renderable->attachments) {
            foreach ($entry->renderable->attachments as $attachment) {
                $o .= $this->render($attachment, false);
            }
        }
        $o .= $this->output->container_start('educard-blog-header row header clearfix');

        // User picture.
        $o .= $this->output->container_start('left picture header');
        $o .= $this->output->user_picture($entry->renderable->user);
        $o .= $this->output->container_end();
        $o .= $this->output->container_start('topic starter header clearfix');
        // Post by.
        $by = new stdClass();
        $fullname = fullname($entry->renderable->user, has_capability('moodle/site:viewfullnames', $syscontext));
        $userurlparams = ['id' => $entry->renderable->user->id, 'course' => $this->page->course->id];
        $by->name = html_writer::link(new moodle_url('/user/view.php', $userurlparams), $fullname);
        $by->date = userdate($entry->created);
        $o .= $this->output->container( $by->name ." - ".$by->date, 'author');
        // Title.
        $titlelink = html_writer::link(new moodle_url('/blog/index.php',
                                                    ['entryid' => $entry->id]),
                                                    format_string($entry->subject));
        $o .= "<h4>";
        $o .= $this->output->container($titlelink, 'subject mt-2');
        $o .= "</h4>";
        // Adding external blog link.
        if (!empty($entry->renderable->externalblogtext)) {
            $o .= $this->output->container($entry->renderable->externalblogtext, 'externalblog');
        }
        // Closing subject tag and header tag.
        $o .= $this->output->container_end();
        $o .= $this->output->container_end();

        // Body.
        $o .= format_text($entry->summary, $entry->summaryformat, ['overflowdiv' => true]);
        if (!empty($entry->uniquehash)) {
            // Uniquehash is used as a link to an external blog.
            $url = clean_param($entry->uniquehash, PARAM_URL);
            if (!empty($url)) {
                $o .= $this->output->container_start('externalblog');
                $o .= html_writer::link($url, get_string('linktooriginalentry', 'blog'));
                $o .= $this->output->container_end();
            }
        }
        // Last modification.
        if ($entry->created != $entry->lastmodified) {
            $o .= "<div class= 'educard-blog-date'>";
            $o .= $this->output->container(get_string('modified').': '.userdate($entry->lastmodified));
            $o .= "</div>";
        }

        // Links to tags.
        $o .= $this->output->tag_list(core_tag_tag::get_item_tags('core', 'post', $entry->id));

        // Add associations.
        if (!empty($CFG->useblogassociations) && !empty($entry->renderable->blogassociations)) {

            // First find and show the associated course.
            $assocstr = '';
            $coursesarray = [];
            foreach ($entry->renderable->blogassociations as $assocrec) {
                if ($assocrec->contextlevel == CONTEXT_COURSE) {
                    $coursesarray[] = $this->output->action_icon($assocrec->url, $assocrec->icon, null, [], true);
                }
            }
            if (!empty($coursesarray)) {
                $assocstr .= get_string('associated', 'blog', get_string('course')) . ': ' . implode(', ', $coursesarray);
            }

            // Now show mod association.
            $modulesarray = [];
            foreach ($entry->renderable->blogassociations as $assocrec) {
                if ($assocrec->contextlevel == CONTEXT_MODULE) {
                    $str = get_string('associated', 'blog', $assocrec->type) . ': ';
                    $str .= $this->output->action_icon($assocrec->url, $assocrec->icon, null, [], true);
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
            $o .= $this->output->container($assocstr, 'tags btn btn-sm');
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
                                                        ['action' => 'edit', 'entryid' => $entry->id]),
                                                        $stredit, ['class' => 'btn btn-info btn-sm']);
            }
            $o .= html_writer::link(new moodle_url('/blog/edit.php',
                                                    ['action' => 'delete', 'entryid' => $entry->id]),
                                                    $strdelete, ['class' => 'btn btn-danger btn-sm']);
        }

        $entryurl = new moodle_url('/blog/index.php', ['entryid' => $entry->id]);
        $o .= html_writer::link($entryurl, get_string('permalink', 'blog'), ['class' => 'btn btn-primary btn-sm']);
        $o .= $this->output->container_end();

        // Comments.
        if (!empty($entry->renderable->comment)) {
            $o .= $entry->renderable->comment->output(true);
        }
        $o .= $this->output->container_end();

        // Closing maincontent div.
        $o .= $this->output->container('&nbsp;', 'side options');
        $o .= $this->output->container_end();

        $o .= $this->output->container_end();

        return $o;
    }
    /**
     * Renders an entry attachment
     *
     * Print link for non-images and returns images as HTML
     *
     * @param blog_entry_attachment $attachment
     * @return string List of attachments depending on the $return input
     */
    public function render_blog_entry_attachment(blog_entry_attachment $attachment) {

        $syscontext = context_system::instance();

        // Image attachments don't get printed as links.
        if (file_mimetype_in_typegroup($attachment->file->get_mimetype(), 'web_image')) {
            $attrs = ['src' => $attachment->url, 'alt' => ''];
            $o = html_writer::empty_tag('img', $attrs);
            $class = 'educard-blog-img attachedimages';
        } else {
            $image = $this->output->pix_icon(file_file_icon($attachment->file),
                                             $attachment->filename,
                                             'moodle',
                                             ['class' => 'icon']);
            $o = html_writer::link($attachment->url, $image);
            $o .= format_text(html_writer::link($attachment->url, $attachment->filename),
                              FORMAT_HTML,
                              ['context' => $syscontext]);
            $class = 'attachments';
        }

        return $this->output->container($o, $class);
    }
}
