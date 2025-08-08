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

use action_menu;
use action_menu_link_secondary;
use core\output\checkbox_toggleall;
use data_field_base;
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

    /** @var string the template name. */
    private $templatename;

    /** @var moodle_url the base url. */
    private $baseurl;

    /** @var string the current search if any. */
    private $search;

    /** @var bool if ratings must be added. */
    private $ratings;

    /** @var bool if comments must be added if not present in the template. */
    private $forcecomments;

    /** @var bool if show more option must be added. */
    private $showmore;

    /** @var bool if the current user can manage entries. */
    private $canmanageentries = null;

    /** @var array if icons HTML. */
    private $icons = [];

    /** @var array All template tags (calculated in load_template_tags). */
    protected $tags = [];

    /** @var array The mod_data fields. */
    protected $fields = [];

    /** @var array All fields that are not present in the template content. */
    protected $otherfields = [];

    /**
     * Class contructor.
     *
     * See the add_options method for the available display options.
     *
     * @param manager $manager the current instance manager
     * @param string $templatecontent the template string to use
     * @param array $options an array of extra diplay options
     * @param array $fields alternative array of fields (for preview presets)
     */
    public function __construct(manager $manager, string $templatecontent, array $options = [], ?array $fields = null) {
        $this->manager = $manager;
        $this->instance = $manager->get_instance();
        $this->templatecontent = $templatecontent;

        $context = $manager->get_context();
        $this->canmanageentries = has_capability('mod/data:manageentries', $context);
        $this->icons = $this->get_icons();
        $this->fields = $fields ?? $manager->get_fields();
        $this->add_options($options);
        $this->load_template_tags($templatecontent);
    }

    /**
     * Create a template class with the default template content.
     *
     * @param manager $manager the current instance manager.
     * @param string $templatename the template name.
     * @param bool $form whether the fields should be displayed as form instead of data.
     * @return self The template with the default content (to be displayed when no template is defined).
     */
    public static function create_default_template(
            manager $manager,
            string $templatename,
            bool $form = false
    ): self {
        $renderer = $manager->get_renderer();
        $content = '';
        switch ($templatename) {
            case 'addtemplate':
            case 'asearchtemplate':
            case 'listtemplate':
            case 'rsstemplate':
            case 'singletemplate':
                $template = new \mod_data\output\defaulttemplate($manager->get_fields(), $templatename, $form);
                $content = $renderer->render_defaulttemplate($template);
        }

        // Some templates have extra options.
        $options = self::get_default_display_options($templatename);

        return new self($manager, $content, $options);
    }

    /**
     * Get default options for templates.
     *
     * For instance, the list template supports the show more button.
     *
     * @param string $templatename the template name.
     * @return array an array of extra diplay options.
     */
    public static function get_default_display_options(string $templatename): array {
        $options = [];

        if ($templatename === 'singletemplate') {
            $options['comments'] = true;
            $options['ratings'] = true;
        }
        if ($templatename === 'listtemplate') {
            // The "Show more" button should be only displayed in the listtemplate.
            $options['showmore'] = true;
        }

        return $options;
    }

    /**
     * Return the raw template content.
     *
     * @return string the template content before parsing
     */
    public function get_template_content(): string {
        return $this->templatecontent;
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
        $this->showmore = $options['showmore'] ?? false;
        $this->templatename = $options['templatename'] ?? 'singletemplate';
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
        // Check if some tag require some extra template scan.
        foreach ($this->tags as $tagname) {
            $methodname = "preprocess_tag_{$tagname}";
            if (method_exists($this, $methodname)) {
                $this->$methodname($templatecontent);
            }
        }
    }

    /**
     * Check if a tag is present in the template.
     *
     * @param bool $tagname the tag to check (without ##)
     * @return bool if the tag is present
     */
    public function has_tag(string $tagname): bool {
        return in_array($tagname, $this->tags);
    }

    /**
     * Return the current template name.
     *
     * @return string the template name
     */
    public function get_template_name(): string {
        return $this->templatename;
    }

    /**
     * Generate the list of action icons.
     *
     * @return pix_icon[] icon name => pix_icon
     */
    protected function get_icons() {
        $attrs = ['class' => 'iconsmall dataicon'];
        return [
            'edit' => new pix_icon('t/editinline', get_string('edit'), '', $attrs),
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
        foreach ($this->fields as $field) {
            // Field value.
            $pattern = '[[' . $field->field->name . ']]';
            $result[$pattern] = highlight(
                $this->search,
                $field->display_browse_field($entry->id, $this->templatename)
            );
            // Other dynamic field information.
            $pattern = '[[' . $field->field->name . '#id]]';
            $result[$pattern] = $field->field->id;
            $pattern = '[[' . $field->field->name . '#name]]';
            $result[$pattern] = $field->field->name;
            $pattern = '[[' . $field->field->name . '#description]]';
            $result[$pattern] = $field->field->description;
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

        if (!$this->showmore) {
            return '';
        }

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
            'label' => get_string('selectfordeletion', 'data'),
            'labelclasses' => 'visually-hidden',
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
        return $OUTPUT->user_picture($user, ['courseid' => $cm->course, 'size' => 64]);
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
        list($formats, $files) = data_portfolio_caller::formats($this->fields, $entry);
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
        return html_writer::tag(
            'span',
            userdate($entry->timecreated, get_string('strftimedatemonthabbr', 'langconfig')),
            ['title' => userdate($entry->timecreated)]
        );
    }

    /**
     * Returns the ##timemodified## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_timemodified_replacement(stdClass $entry, bool $canmanageentry): string {
        return html_writer::tag(
            'span',
            userdate($entry->timemodified, get_string('strftimedatemonthabbr', 'langconfig')),
            ['title' => userdate($entry->timemodified)]
        );
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
        return ($entry->approved) ? '' : html_writer::div(get_string('notapproved', 'data'), 'mod-data-approval-status-badge');
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
        $comment = new \core_comment\manager($cmdata);
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

    /**
     * Prepare otherfield tag scanning the present template fields.
     *
     * @param string $templatecontent the template content
     */
    protected function preprocess_tag_otherfields(string $templatecontent) {
        $otherfields = [];
        $fields = $this->manager->get_fields();
        foreach ($fields as $field) {
            if (strpos($templatecontent, "[[" . $field->field->name . "]]") === false) {
                $otherfields[] = $field;
            }
        }
        $this->otherfields = $otherfields;
    }

    /**
     * Returns the ##otherfields## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_otherfields_replacement(stdClass $entry, bool $canmanageentry): string {
        global $OUTPUT;
        $fields = [];
        foreach ($this->otherfields as $field) {
            $fieldvalue = highlight(
                $this->search,
                $field->display_browse_field($entry->id, $this->templatename)
            );
            $fieldinfo = [
                'fieldname' => $field->field->name,
                'fieldcontent' => $fieldvalue,
            ];
            $fields[] = $fieldinfo;
        }
        return $OUTPUT->render_from_template('mod_data/fields_otherfields', ['fields' => $fields]);
    }

    /**
     * Returns the ##actionsmenu## tag replacement for an entry.
     *
     * @param stdClass $entry the entry object
     * @param bool $canmanageentry if the current user can manage this entry
     * @return string the tag replacement
     */
    protected function get_tag_actionsmenu_replacement(stdClass $entry, bool $canmanageentry): string {
        global $OUTPUT, $CFG;

        $actionmenu = new action_menu();
        $actionmenu->set_kebab_trigger();
        $actionmenu->set_action_label(get_string('actions'));
        $actionmenu->set_additional_classes('entry-actionsmenu');

        // Show more.
        if ($this->showmore) {
            $showmoreurl = new moodle_url($this->baseurl, [
                'rid' => $entry->id,
                'filter' => 1,
            ]);
            $actionmenu->add(new action_menu_link_secondary(
                $showmoreurl,
                null,
                get_string('showmore', 'mod_data')
            ));
        }

        if ($canmanageentry) {
            // Edit entry.
            $backurl = new moodle_url($this->baseurl, [
                'rid' => $entry->id,
                'mode' => 'single',
            ]);
            $editurl = new moodle_url('/mod/data/edit.php', $this->baseurl->params());
            $editurl->params([
                'rid' => $entry->id,
                'backto' => urlencode($backurl->out(false))
            ]);

            $actionmenu->add(new action_menu_link_secondary(
                $editurl,
                null,
                get_string('edit')
            ));

            // Delete entry.
            $deleteurl = new moodle_url($this->baseurl, [
                'delete' => $entry->id,
                'mode' => 'single',
            ]);

            $actionmenu->add(new action_menu_link_secondary(
                $deleteurl,
                null,
                get_string('delete')
            ));
        }

        // Approve/disapprove entry.
        $context = $this->manager->get_context();
        if (has_capability('mod/data:approve', $context) && $this->instance->approval) {
            if ($entry->approved) {
                $disapproveurl = new moodle_url($this->baseurl, [
                    'disapprove' => $entry->id,
                    'sesskey' => sesskey(),
                ]);
                $actionmenu->add(new action_menu_link_secondary(
                    $disapproveurl,
                    null,
                    get_string('disapprove', 'mod_data')
                ));
            } else {
                $approveurl = new moodle_url($this->baseurl, [
                    'approve' => $entry->id,
                    'sesskey' => sesskey(),
                ]);
                $actionmenu->add(new action_menu_link_secondary(
                    $approveurl,
                    null,
                    get_string('approve', 'mod_data')
                ));
            }
        }

        // Export entry to portfolio.
        if (!empty($CFG->enableportfolios)) {
            // Check the user can export the entry.
            $cm = $this->manager->get_coursemodule();
            $canexportall = has_capability('mod/data:exportentry', $context);
            $canexportown = has_capability('mod/data:exportownentry', $context);
            if ($canexportall || (data_isowner($entry->id) && $canexportown)) {
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
                $exporturl = $button->to_html(PORTFOLIO_ADD_MOODLE_URL);
                if (!is_null($exporturl)) {
                    $actionmenu->add(new action_menu_link_secondary(
                        $exporturl,
                        null,
                        get_string('addtoportfolio', 'portfolio')
                    ));
                }
            }
        }

        return $OUTPUT->render($actionmenu);
    }

    /**
     * Parse the template as if it was for add entry.
     *
     * This method is similar to the parse_entry but it uses the display_add_field method
     * instead of the display_browse_field.
     *
     * @param stdClass|null $processeddata the previous process data information.
     * @param int|null $entryid the possible entry id
     * @param stdClass|null $entrydata the entry data from a previous form or from a real entry
     * @return string the add entry HTML content
     */
    public function parse_add_entry(
        ?stdClass $processeddata = null,
        ?int $entryid = null,
        ?stdClass $entrydata = null
    ): string {
        global $OUTPUT;

        $manager = $this->manager;
        $renderer = $manager->get_renderer();
        $templatecontent = $this->templatecontent;

        if (!$processeddata) {
            $processeddata = (object)[
                'generalnotifications' => [],
                'fieldnotifications' => [],
            ];
        }

        $result = '';

        foreach ($processeddata->generalnotifications as $notification) {
            $result .= $renderer->notification($notification);
        }

        $possiblefields = $manager->get_fields();
        $patterns = [];
        $replacements = [];

        // Then we generate strings to replace.
        $otherfields = [];
        foreach ($possiblefields as $field) {
            $fieldinput = $this->get_field_input($processeddata, $field, $entryid, $entrydata);
            if (strpos($templatecontent, "[[" . $field->field->name . "]]") !== false) {
                // Replace the field tag.
                $patterns[] = "[[" . $field->field->name . "]]";
                $replacements[] = $fieldinput;
            } else {
                // Is in another fields.
                $otherfields[] = [
                    'fieldname' => $field->field->name,
                    'fieldcontent' => $fieldinput,
                ];
            }

            // Replace the field id tag.
            $patterns[] = "[[" . $field->field->name . "#id]]";
            $replacements[] = 'field_' . $field->field->id;
            $patterns[] = '[[' . $field->field->name . '#name]]';
            $replacements[] = $field->field->name;
            $patterns[] = '[[' . $field->field->name . '#description]]';
            $replacements[] = $field->field->description;
        }

        $patterns[] = "##otherfields##";
        if (!empty($otherfields)) {
            $replacements[] = $OUTPUT->render_from_template(
                'mod_data/fields_otherfields',
                ['fields' => $otherfields]
            );
        } else {
            $replacements[] = '';
        }

        if (core_tag_tag::is_enabled('mod_data', 'data_records')) {
            $patterns[] = "##tags##";
            $replacements[] = data_generate_tag_form($entryid);
        }

        $result .= str_ireplace($patterns, $replacements, $templatecontent);
        return $result;
    }

    /**
     * Return the input form html from a specific field.
     *
     * @param stdClass $processeddata the previous process data information.
     * @param data_field_base $field the field object
     * @param int|null $entryid the possible entry id
     * @param stdClass|null $entrydata the entry data from a previous form or from a real entry
     * @return string the add entry HTML content
     */
    private function get_field_input(
        stdClass $processeddata,
        data_field_base $field,
        ?int $entryid = null,
        ?stdClass $entrydata = null
    ): string {
        $renderer = $this->manager->get_renderer();
        $errors = '';
        $fieldnotifications = $processeddata->fieldnotifications[$field->field->name] ?? [];
        if (!empty($fieldnotifications)) {
            foreach ($fieldnotifications as $notification) {
                $errors .= $renderer->notification($notification);
            }
        }
        $fielddisplay = '';
        if ($field->type === 'unknown') {
            if ($this->canmanageentries) { // Display notification for users that can manage entries.
                $errors .= $renderer->notification(get_string(
                    'missingfieldtype',
                    'data',
                    (object)['name' => s($field->field->name)]
                ));
            }
        } else {
            $fielddisplay = $field->display_add_field($entryid, $entrydata);
        }
        return $errors . $fielddisplay;
    }
}
