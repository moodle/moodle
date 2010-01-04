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
 * All workshop module renderers are defined here
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Workshop module renderer class
 *
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_mod_workshop_renderer extends moodle_renderer_base {

    /** the underlying renderer to use */
    protected $output;

    /** the page we are doing output for */
    protected $page;

    /**
     * Workshop renderer constructor
     *
     * @param mixed $page the page we are doing output for
     * @param mixed $output lower-level renderer, typically moodle_core_renderer
     * @access public
     * @return void
     */
    public function __construct($page, $output) {
        $this->page   = $page;
        $this->output = $output;
    }

    /**
     * Returns html code for a status message
     *
     * This should be replaced by a core system of displaying messages, as for example Mahara has.
     *
     * @param string $message to display
     * @return string html
     */
    public function status_message(stdClass $message) {
        if (empty($message->text)) {
            return '';
        }
        $sty = $message->sty ? $message->sty : 'info';

        $o = $this->output->output_tag('span', array(), $message->text);
        $closer = $this->output->output_tag('a', array('href' => $this->page->url->out()),
                    get_string('messageclose', 'workshop'));
        $o .= $this->output->container($closer, 'status-message-closer');
        if (isset($message->extra)) {
            $o .= $message->extra;
        }
        return $this->output->container($o, array('status-message', $sty));
    }

    /**
     * Wraps html code returned by the allocator init() method
     *
     * Supplied argument can be either integer status code or an array of string messages. Messages
     * in a array can have optional prefix or prefixes, using '::' as delimiter. Prefixes determine
     * the type of the message and may influence its visualisation.
     *
     * @param mixed $result int|array returned by init()
     * @return string html to be echoed
     */
    public function allocation_init_result($result='') {
        $msg = new stdClass();
        if ($result === 'WORKSHOP_ALLOCATION_RANDOM_ERROR') {
            $msg = (object)array('text' => get_string('randomallocationerror', 'workshop'), 'sty' => 'error');
        } else {
            $msg = (object)array('text' => get_string('randomallocationdone', 'workshop'), 'sty' => 'ok');
        }
        $o = $this->status_message($msg);
        if (is_array($result)) {
            $o .= $this->output->output_start_tag('ul', array('class' => 'allocation-init-results'));
            foreach ($result as $message) {
                $parts  = explode('::', $message);
                $text   = array_pop($parts);
                $class  = implode(' ', $parts);
                if (in_array('debug', $parts) && !debugging('', DEBUG_DEVELOPER)) {
                    // do not display allocation debugging messages
                    continue;
                }
                $o .= $this->output->output_tag('li', array('class' => $class), $text);
            }
            $o .= $this->output->output_end_tag('ul');
            $o .= $this->output->continue_button($this->page->url->out());
        }
        return $o;
    }

    /**
     * Displays the submission fulltext
     *
     * By default, this looks similar to a forum post.
     *
     * @param stdClass $submission     The submission record
     * @param bool     $showauthorname Should the author name be displayed
     * @param stdClass $author         If author's name should be displayed, this object contains the author data
     * @return string html to be echoed
     */
    public function submission_full(stdClass $submission, $showauthorname=false, stdClass $author=null) {
        global $CFG;

        $o  = '';    // output code
        $at = array('class' => 'submission-full');
        if (!$showauthorname || !$author) {
            $at['class'] .= ' anonymous';
        }
        $o .= $this->output->output_start_tag('div', $at);                                                      //+
        $o .= $this->output->output_start_tag('div', array('class' => 'header'));                               //++
        $o .= $this->output->heading(format_string($submission->title), 3, 'title');
        if ($showauthorname && $author) {
            $o .= $this->output->output_start_tag('div', array('class' => 'author'));                           //+++
            $userpic    = new user_picture();
            $userpic->user = $author;
            $userpic->courseid = $this->page->course->id;
            $userpic->url = true;
            $userpic->size = 64;
            $userpic    = $this->output->user_picture($userpic);
            $userurl    = new moodle_url($CFG->wwwroot . '/user/view.php',
                                            array('id' => $author->id, 'course' => $this->page->course->id));
            $a          = new stdClass();
            $a->name    = fullname($author);
            $a->url     = $userurl->out();
            $byfullname = get_string('byfullname', 'workshop', $a);
            $o .= $this->output->output_tag('div', array('class' => 'picture'), $userpic);
            $o .= $this->output->output_tag('div', array('class' => 'fullname'), $byfullname);
            $o .= $this->output->output_end_tag('div'); // end of author                                        //++
        }
        $created = get_string('userdatecreated', 'workshop', userdate($submission->timecreated));
        $o .= $this->output->output_tag('div', array('class' => 'userdate created'), $created);
        if ($submission->timemodified > $submission->timecreated) {
            $modified = get_string('userdatemodified', 'workshop', userdate($submission->timemodified));
            $o .= $this->output->output_tag('div', array('class' => 'userdate modified'), $modified);
        }
        $o .= $this->output->output_end_tag('div'); // end of header                                            //+

        $content = format_text($submission->content, $submission->contentformat);
        $content = file_rewrite_pluginfile_urls($content, 'pluginfile.php', $this->page->context->id,
                                                        'workshop_submission_content', $submission->id);
        $o .= $this->output->output_tag('div', array('class' => 'content'), $content);

        $o .= $this->submission_attachments($submission);

        $o .= $this->output->output_end_tag('div'); // end of submission-full                                   //

        return $o;
    }

    /**
     * Renders a list of files attached to the submission
     *
     * If format==html, then format a html string. If format==text, then format a text-only string.
     * Otherwise, returns html for non-images and html to display the image inline.
     *
     * @param stdClass $submission Submission record
     * @param string format        The format of the returned string
     * @return string              HTML code to be echoed
     */
    public function submission_attachments(stdClass $submission, $format=null) {
        global $CFG;
        require_once($CFG->libdir.'/filelib.php');

        $fs     = get_file_storage();
        $ctx    = $this->page->context;
        $files  = $fs->get_area_files($ctx->id, 'workshop_submission_attachment', $submission->id);

        $outputimgs     = "";   // images to be displayed inline
        $outputfiles    = "";   // list of attachment files

        foreach ($files as $file) {
            if ($file->is_directory()) {
                continue;
            }

            $filename   = $file->get_filename();
            $fileurl    = file_encode_url($CFG->wwwroot . '/pluginfile.php',
                                '/' . $ctx->id . '/workshop_submission_attachment/' . $submission->id . '/' . $filename, true);
            $type       = $file->get_mimetype();
            $type       = mimeinfo_from_type("type", $type);
            $icon       = new html_image();
            $icon->src  = $this->output->old_icon_url(file_mimetype_icon($type));
            $icon->set_classes('icon');
            $icon->alt  = $type;
            $image      = $this->output->image($icon);

            $linkhtml   = $this->output->link($fileurl, $image) . $this->output->link($fileurl, $filename);
            $linktxt    = "$filename [$fileurl]";

            if ($format == "html") {
                // this is the same as the code in the last else-branch
                $outputfiles .= $this->output->output_tag('li', array('class' => $type), $linkhtml);

            } else if ($format == "text") {
                $outputfiles .= $linktxt . "\n";

            } else {
                if (in_array($type, array('image/gif', 'image/jpeg', 'image/png'))) {
                    $preview        = new html_image();
                    $preview->src   = $fileurl;
                    $preview->set_classes('preview');
                    $preview        = $this->output->image($preview);
                    $preview        = $this->output->link($fileurl, $preview);
                    $outputimgs    .= $this->output->output_tag('div', array(), $preview);
                } else {
                    // this is the same as the code in html if-branch
                    $outputfiles .= $this->output->output_tag('li', array('class' => $type), $linkhtml);
                }
            }
        }

        if ($outputimgs) {
            $outputimgs = $this->output->output_tag('div', array('class' => 'images'), $outputimgs);
        }
        if ($format !== "text") {
            $outputfiles = $this->output->output_tag('ul', array('class' => 'files'), $outputfiles);
        }
        return $this->output->output_tag('div', array('class' => 'attachments'), $outputimgs . $outputfiles);
    }

}
