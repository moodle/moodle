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

namespace mod_data;

use core\output\checkbox_toggleall;
use html_writer;
use mod_data\manager;
use moodle_url;
use pix_icon;
use stdClass;
use user_picture;
use core_user;
use portfolio_add_button;
use data_portfolio_caller;
use comment;
use core_tag_tag;

/**
 * Class template for database activity
 *
 * @package    mod_data
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template {

    /** @var manager the current instance manager. */
    private $manager;

    /** @var stdClass the current instance record. */
    private $instance;

    /** @var string the template. */
    private $templatecontent;

    /** @var moodle_url the base url. */
    private $baseurl;

    /** @var string the current search if any. */
    private $search;

    /** @var bool if ratings must be added. */
    private $ratings;

    /** @var bool if comments must be added if not present in the template. */
    private $forcecomments;

    /** @var bool if the current user can manage entries. */
    private $canmanageentries = null;

    /** @var array if icons HTML. */
    private $icons = [];

    /** @var array All template tags (calculated in load_template_tags). */
    protected $tags = [];

    /**
     * Class contructor.
     *
     * See the add_options method for the available display options.
     *
     * @param manager $manager the current instance manager
     * @param string $templatecontent the template string to use
     * @param array $options an array of extra diplay options
     */
    public function __construct(manager $manager, string $templatecontent, array $options = []) {
        $this->manager = $manager;
        $this->instance = $manager->get_instance();
        $this->templatecontent = $templatecontent;

        $context = $manager->get_context();
        $this->canmanageentries = has_capability('mod/data:manageentries', $context);
        $this->icons = $this->get_icons();
        $this->add_options($options);
        $this->load_template_tags($templatecontent);
    }

    /**
     * Add extra display options.
     *
     * The extra options are:
     *  - page: the current pagination page
     *  - search: the current search text
     *  - baseurl: an alternative entry url (moodle_url)
     *  - comments: if comments must be added if not present
     *  - ratings: if ratings must be added
     *
     * @param array $options the array of options.
     */
    public function add_options(array $options = []) {
        $cm = $this->manager->get_coursemodule();
        $baseurl = $options['baseurl'] ?? new moodle_url('/mod/data/view.php', ['id' => $cm->id]);
        if (isset($options['page'])) {
            $baseurl->params([
                'page' => $options['page'],
            ]);
        }
        $this->baseurl = $baseurl;

        // Save options.
        $this->search = $options['search'] ?? null;
        $this->ratings = $options['ratings'] ?? false;
        $this->forcecomments = $options['comments'] ?? false;
    }

    /**
     * Scan the template tags.
     *
     * This method detects which tags are used in this template and store them
     * in the $this->tags attribute. This attribute will be used to determine
     * which replacements needs to be calculated.
     *
     * @param string $templatecontent the current template
     */
    protected function load_template_tags(string $templatecontent) {
        // Detect action tags.
        $pattern = '/##(?P<tags>\w+?)##/';
        $matches = [];
        preg_match_all($pattern, $templatecontent, $matches);
        if (!isset($matches['tags']) || empty($matches['tags'])) {
            return;
        }
        $this->tags = $matches['tags'];
    }

    /**
     * Generate the list of action icons.
     *
     * @return pix_icon[] icon name => pix_icon
     */
    protected function get_icons() {
        $attrs = ['class' => 'iconsmall'];
        return [
            'edit' => new pix_icon('t/edit', get_string('edit'), '', $attrs),
            'delete' => new pix_icon('t/delete', get_string('delete'), '', $attrs),
            'more' => new pix_icon('t/preview', get_string('more', 'data'), '', $attrs),
            'approve' => new pix_icon('t/approve', get_string('approve', 'data'), '', $attrs),
            'disapprove' => new pix_icon('t/block', get_string('disapprove', 'data'), '', $attrs),
        ];
    }

    /**
     * Return the parsed entry using a template.
     *
     * This method apply a template replacing all necessary tags.
     *
     * @param array $entries of entres to parse
     * @return string the entries outputs using the template
     */
    public function parse_entries(array $entries): string {
        if (empty($entries)) {
            return '';
        }
        $result = '';
        foreach ($entries as $entry) {
            $result .= $this->parse_entry($entry);
        }
        return $result;
    }

    /**
     * Parse a single entry.
     *
     * @param stdClass $entry the entry to parse
     * @return string the parsed entry
     */
    private function parse_entry(stdClass $entry): string {
        if (empty($this->templatecontent)) {
            return '';
        }
        $context = $this->manager->get_context();
        $canmanageentry = data_user_can_manage_entry($entry, $this->instance, $context);

        // Load all replacements for the entry.
        $fields = $this->get_fields_replacements($entry);
        $tags = $this->get_tags_replacements($entry, $canmanageentry);
        $replacements = array_merge($fields, $tags);

        $patterns = array_keys($replacements);
        $replacement = array_values($replacements);
        $result = str_ireplace($patterns, $replacement, $this->templatecontent);

        return $this->post_parse($result, $entry);
    }

    /**
     * Get all field replacements.
     *
     * @param stdClass $entry the entry object
     * @return array of pattern => replacement
     */
    private function get_fields_replacements(stdClass $entry): array {
        $result = [];
        $fields = $this->manager->get_fields();
        foreach ($fields as $field) {
            // Field value.
            $pattern = '[[' . $field->field->name . ']]';
            $result[$pattern] = highlight(
                $this->search,
                $field->display_browse_field($entry->id, $this->templatecontent)
            );
            // Field id.
            $pattern = '[[' . $field->field->name . '#id]]';
            $result[$pattern] = $field->field->id;
        }
        return $result;
    }

    /**
     * Get all standard tags replacements.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return array of pattern => replacement
     */
    private function get_tags_replacements(stdClass $entry, bool $canmanageentry): array {
        $result = [];
        foreach ($this->tags as $tagname) {
            $methodname = "get_tag_{$tagname}_replacement";
            if (method_exists($this, $methodname)) {
                $pattern = "##$tagname##";
                $replacement = $this->$methodname($entry, $canmanageentry);
                $result[$pattern] = $replacement;
            }
        }
        return $result;
    }

    /**
     * Add any extra information to the parsed entry.
     *
     * @param string $result the parsed template with the entry data
     * @param stdClass $entry the entry object
     * @return string the final parsed template
     */
    private function post_parse(string $result, stdClass $entry): string {
        if ($this->ratings) {
            $result .= data_print_ratings($this->instance, $entry, false);
        }
        if ($this->forcecomments && strpos($this->templatecontent, '##comments##') === false) {
            $result .= $this->get_tag_comments_replacement($entry, false);
        }
        return $result;
    }

    /**
     * Returns the ##edit## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_edit_replacement(stdClass $entry, bool $canmanageentry): string {
        global $OUTPUT;
        if (!$canmanageentry) {
            return '';
        }
        $backurl = new moodle_url($this->baseurl, [
            'rid' => $entry->id,
            'mode' => 'single',
        ]);
        $url = new moodle_url('/mod/data/edit.php', $this->baseurl->params());
        $url->params([
            'rid' => $entry->id,
            'sesskey' => sesskey(),
            'backto' => urlencode($backurl->out(false))
        ]);
        return html_writer::tag(
            'span',
            $OUTPUT->action_icon($url, $this->icons['edit']),
            ['class' => 'edit']
        );
    }

    /**
     * Returns the ##delete## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_delete_replacement(stdClass $entry, bool $canmanageentry): string {
        global $OUTPUT;
        if (!$canmanageentry) {
            return '';
        }
        $url = new moodle_url($this->baseurl, [
            'delete' => $entry->id,
            'sesskey' => sesskey(),
            'mode' => 'single',
        ]);

        return html_writer::tag(
            'span',
            $OUTPUT->action_icon($url, $this->icons['delete']),
            ['class' => 'delete']
        );
    }

    /**
     * Returns the ##more## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_more_replacement(stdClass $entry, bool $canmanageentry): string {
        global $OUTPUT;
        $url = new moodle_url($this->baseurl, [
            'rid' => $entry->id,
            'filter' => 1,
        ]);
        return html_writer::tag(
            'span',
            $OUTPUT->action_icon($url, $this->icons['more']),
            ['class' => 'more']
        );
    }

    /**
     * Returns the ##moreurl## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_moreurl_replacement(stdClass $entry, bool $canmanageentry): string {
        $url = new moodle_url($this->baseurl, [
            'rid' => $entry->id,
            'filter' => 1,
        ]);
        return $url->out(false);
    }

    /**
     * Returns the ##delcheck## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_delcheck_replacement(stdClass $entry, bool $canmanageentry): string {
        global $OUTPUT;
        if (!$this->canmanageentries) {
            return '';
        }
        $checkbox = new checkbox_toggleall('listview-entries', false, [
            'id' => "entry_{$entry->id}",
            'name' => 'delcheck[]',
            'classes' => 'recordcheckbox',
            'value' => $entry->id,
        ]);
        return $OUTPUT->render($checkbox);
    }

    /**
     * Returns the ##user## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_user_replacement(stdClass $entry, bool $canmanageentry): string {
        $cm = $this->manager->get_coursemodule();
        $url = new moodle_url('/user/view.php', [
            'id' => $entry->userid,
            'course' => $cm->course,
        ]);
        return html_writer::tag(
            'a',
            fullname($entry),
            ['href' => $url->out(false)]
        );
    }

    /**
     * Returns the ##userpicture## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_userpicture_replacement(stdClass $entry, bool $canmanageentry): string {
        global $OUTPUT;
        $cm = $this->manager->get_coursemodule();
        $user = user_picture::unalias($entry, null, 'userid');
        // If the record didn't come with user data, retrieve the user from database.
        if (!isset($user->picture)) {
            $user = core_user::get_user($entry->userid);
        }
        return $OUTPUT->user_picture($user, ['courseid' => $cm->course]);
    }

    /**
     * Returns the ##export## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_export_replacement(stdClass $entry, bool $canmanageentry): string {
        global $CFG;
        if (empty($CFG->enableportfolios)) {
            return '';
        }
        // Check the user can export the entry.
        $cm = $this->manager->get_coursemodule();
        $context = $this->manager->get_context();
        $canexportall = has_capability('mod/data:exportentry', $context);
        $canexportown = has_capability('mod/data:exportownentry', $context);
        if (!$canexportall && !(data_isowner($entry->id) && $canexportown)) {
            return '';
        }
        // Add the portfolio export button.
        require_once($CFG->libdir . '/portfoliolib.php');
        $button = new portfolio_add_button();
        $button->set_callback_options(
            'data_portfolio_caller',
            ['id' => $cm->id, 'recordid' => $entry->id],
            'mod_data'
        );
        $fields = $this->manager->get_fields();
        list($formats, $files) = data_portfolio_caller::formats($fields, $entry);
        $button->set_formats($formats);
        $result = $button->to_html(PORTFOLIO_ADD_ICON_LINK);
        if (is_null($result)) {
            $result = '';
        }
        return $result;
    }

    /**
     * Returns the ##timeadded## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_timeadded_replacement(stdClass $entry, bool $canmanageentry): string {
        return userdate($entry->timecreated);
    }

    /**
     * Returns the ##timemodified## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_timemodified_replacement(stdClass $entry, bool $canmanageentry): string {
        return userdate($entry->timemodified);
    }

    /**
     * Returns the ##approve## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_approve_replacement(stdClass $entry, bool $canmanageentry): string {
        global $OUTPUT;
        $context = $this->manager->get_context();
        if (!has_capability('mod/data:approve', $context) || !$this->instance->approval || $entry->approved) {
            return '';
        }
        $url = new moodle_url($this->baseurl, [
            'approve' => $entry->id,
            'sesskey' => sesskey(),
        ]);
        return html_writer::tag(
            'span',
            $OUTPUT->action_icon($url, $this->icons['approve']),
            ['class' => 'approve']
        );
    }

    /**
     * Returns the ##disapprove## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_disapprove_replacement(stdClass $entry, bool $canmanageentry): string {
        global $OUTPUT;
        $context = $this->manager->get_context();
        if (!has_capability('mod/data:approve', $context) || !$this->instance->approval || !$entry->approved) {
            return '';
        }
        $url = new moodle_url($this->baseurl, [
            'disapprove' => $entry->id,
            'sesskey' => sesskey(),
        ]);
        return html_writer::tag(
            'span',
            $OUTPUT->action_icon($url, $this->icons['disapprove']),
            ['class' => 'disapprove']
        );
    }

    /**
     * Returns the ##approvalstatus## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_approvalstatus_replacement(stdClass $entry, bool $canmanageentry): string {
        if (!$this->instance->approval) {
            return '';
        }
        return ($entry->approved) ? get_string('approved', 'data') : get_string('notapproved', 'data');
    }

    /**
     * Returns the ##approvalstatusclass## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_approvalstatusclass_replacement(stdClass $entry, bool $canmanageentry): string {
        if (!$this->instance->approval) {
            return '';
        }
        return ($entry->approved) ? 'approved' : 'notapproved';
    }

    /**
     * Returns the ##comments## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_comments_replacement(stdClass $entry, bool $canmanageentry): string {
        global $CFG;
        if (empty($CFG->usecomments) || empty($this->instance->comments)) {
            return '';
        }
        $context = $this->manager->get_context();
        require_once($CFG->dirroot  . '/comment/lib.php');
        list($context, $course, $cm) = get_context_info_array($context->id);
        $cmdata = (object)[
            'context' => $context,
            'course' => $course,
            'cm' => $cm,
            'area' => 'database_entry',
            'itemid' => $entry->id,
            'showcount' => true,
            'component' => 'mod_data',
        ];
        $comment = new comment($cmdata);
        return $comment->output(true);
    }

    /**
     * Returns the ##tags## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_tags_replacement(stdClass $entry, bool $canmanageentry): string {
        global $OUTPUT;
        if (!core_tag_tag::is_enabled('mod_data', 'data_records')) {
            return '';
        }
        return $OUTPUT->tag_list(
            core_tag_tag::get_item_tags('mod_data', 'data_records', $entry->id),
            '',
            'data-tags'
        );
    }

    /**
     * Returns the ##id## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_id_replacement(stdClass $entry, bool $canmanageentry): string {
        return (string) $entry->id;
    }

}
