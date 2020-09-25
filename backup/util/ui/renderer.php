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
 * This file contains backup and restore output renderers
 *
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_plan_builder.class.php');

/**
 * The primary renderer for the backup.
 *
 * Can be retrieved with the following code:
 * <?php
 * $renderer = $PAGE->get_renderer('core', 'backup');
 * ?>
 *
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_backup_renderer extends plugin_renderer_base {

    /**
     * Same site notification display.
     *
     * @var string
     */
    private $samesitenotification = '';

    /**
     * Renderers a progress bar for the backup or restore given the items that make it up.
     *
     * @param array $items An array of items
     * @return string
     */
    public function progress_bar(array $items) {
        foreach ($items as &$item) {
            $text = $item['text'];
            unset($item['text']);
            if (array_key_exists('link', $item)) {
                $link = $item['link'];
                unset($item['link']);
                $item = html_writer::link($link, $text, $item);
            } else {
                $item = html_writer::tag('span', $text, $item);
            }
        }
        return html_writer::tag('div', join(get_separator(), $items), array('class' => 'backup_progress clearfix'));
    }

    /**
     * The backup and restore pages may display a log (if any) in a scrolling box.
     *
     * @param string $loghtml Log content in HTML format
     * @return string HTML content that shows the log
     */
    public function log_display($loghtml) {
        $out = html_writer::start_div('backup_log');
        $out .= $this->output->heading(get_string('backuplog', 'backup'));
        $out .= html_writer::start_div('backup_log_contents');
        $out .= $loghtml;
        $out .= html_writer::end_div();
        $out .= html_writer::end_div();
        return $out;
    }

    /**
     * Set the same site backup notification.
     *
     */
    public function set_samesite_notification() {
        $this->samesitenotification = $this->output->notification(get_string('samesitenotification', 'backup'), 'info');
    }

    /**
     * Get the same site backup notification.
     *
     */
    public function get_samesite_notification() {
        return $this->samesitenotification;
    }

    /**
     * Prints a dependency notification
     *
     * @param string $message
     * @return string
     */
    public function dependency_notification($message) {
        return html_writer::tag('div', $message, array('class' => 'notification dependencies_enforced'));
    }

    /**
     * Displays the details of a backup file
     *
     * @param stdClass $details
     * @param moodle_url $nextstageurl
     * @return string
     */
    public function backup_details($details, $nextstageurl) {
        $yestick = $this->output->pix_icon('i/valid', get_string('yes'));
        $notick = $this->output->pix_icon('i/invalid', get_string('no'));

        $html  = html_writer::start_tag('div', array('class' => 'backup-restore'));

        $html .= html_writer::start_tag('div', ['class' => 'backup-section',
            'role' => 'table', 'aria-labelledby' => 'backupdetailsheader']);
        $html .= $this->output->heading(get_string('backupdetails', 'backup'), 2, 'header', 'backupdetailsheader');
        $html .= $this->backup_detail_pair(get_string('backuptype', 'backup'), get_string('backuptype'.$details->type, 'backup'));
        $html .= $this->backup_detail_pair(get_string('backupformat', 'backup'), get_string('backupformat'.$details->format, 'backup'));
        $html .= $this->backup_detail_pair(get_string('backupmode', 'backup'), get_string('backupmode'.$details->mode, 'backup'));
        $html .= $this->backup_detail_pair(get_string('backupdate', 'backup'), userdate($details->backup_date));
        $html .= $this->backup_detail_pair(get_string('moodleversion', 'backup'),
                html_writer::tag('span', $details->moodle_release, array('class' => 'moodle_release')).
                html_writer::tag('span', '['.$details->moodle_version.']', array('class' => 'moodle_version sub-detail')));
        $html .= $this->backup_detail_pair(get_string('backupversion', 'backup'),
                html_writer::tag('span', $details->backup_release, array('class' => 'moodle_release')).
                html_writer::tag('span', '['.$details->backup_version.']', array('class' => 'moodle_version sub-detail')));
        $html .= $this->backup_detail_pair(get_string('originalwwwroot', 'backup'),
                html_writer::tag('span', $details->original_wwwroot, array('class' => 'originalwwwroot')).
                html_writer::tag('span', '['.$details->original_site_identifier_hash.']', array('class' => 'sitehash sub-detail')));
        if (!empty($details->include_file_references_to_external_content)) {
            $message = '';
            if (backup_general_helper::backup_is_samesite($details)) {
                $message = $yestick . ' ' . get_string('filereferencessamesite', 'backup');
            } else {
                $message = $notick . ' ' . get_string('filereferencesnotsamesite', 'backup');
            }
            $html .= $this->backup_detail_pair(get_string('includefilereferences', 'backup'), $message);
        }

        $html .= html_writer::end_tag('div');

        $html .= html_writer::start_tag('div', ['class' => 'backup-section settings-section',
            'role' => 'table', 'aria-labelledby' => 'backupsettingsheader']);
        $html .= $this->output->heading(get_string('backupsettings', 'backup'), 2, 'header', 'backupsettingsheader');
        foreach ($details->root_settings as $label => $value) {
            if ($label == 'filename' or $label == 'user_files') {
                continue;
            }
            $html .= $this->backup_detail_pair(get_string('rootsetting'.str_replace('_', '', $label), 'backup'), $value ? $yestick : $notick);
        }
        $html .= html_writer::end_tag('div');

        if ($details->type === 'course') {
            $html .= html_writer::start_tag('div', ['class' => 'backup-section',
                    'role' => 'table', 'aria-labelledby' => 'backupcoursedetailsheader']);
            $html .= $this->output->heading(get_string('backupcoursedetails', 'backup'), 2, 'header', 'backupcoursedetailsheader');
            $html .= $this->backup_detail_pair(get_string('coursetitle', 'backup'), $details->course->title);
            $html .= $this->backup_detail_pair(get_string('courseid', 'backup'), $details->course->courseid);

            // Warning users about front page backups.
            if ($details->original_course_format === 'site') {
                $html .= $this->backup_detail_pair(get_string('type_format', 'plugin'), get_string('sitecourseformatwarning', 'backup'));
            }
            $html .= html_writer::start_tag('div', array('class' => 'backup-sub-section'));
            $html .= $this->output->heading(get_string('backupcoursesections', 'backup'), 3, array('class' => 'subheader'));
            foreach ($details->sections as $key => $section) {
                $included = $key.'_included';
                $userinfo = $key.'_userinfo';
                if ($section->settings[$included] && $section->settings[$userinfo]) {
                    $value = get_string('sectionincanduser', 'backup');
                } else if ($section->settings[$included]) {
                    $value = get_string('sectioninc', 'backup');
                } else {
                    continue;
                }
                $html .= $this->backup_detail_pair(get_string('backupcoursesection', 'backup', $section->title), $value);
                $table = null;
                foreach ($details->activities as $activitykey => $activity) {
                    if ($activity->sectionid != $section->sectionid) {
                        continue;
                    }
                    if (empty($table)) {
                        $table = new html_table();
                        $table->head = array(get_string('module', 'backup'), get_string('title', 'backup'), get_string('userinfo', 'backup'));
                        $table->colclasses = array('modulename', 'moduletitle', 'userinfoincluded');
                        $table->align = array('left', 'left', 'center');
                        $table->attributes = array('class' => 'activitytable generaltable');
                        $table->data = array();
                    }
                    $name = get_string('pluginname', $activity->modulename);
                    $icon = new image_icon('icon', '', $activity->modulename, ['class' => 'iconlarge icon-pre']);
                    $table->data[] = array(
                        $this->output->render($icon).$name,
                        $activity->title,
                        ($activity->settings[$activitykey.'_userinfo']) ? $yestick : $notick,
                    );
                }
                if (!empty($table)) {
                    $html .= $this->backup_detail_pair(get_string('sectionactivities', 'backup'), html_writer::table($table));
                }

            }
            $html .= html_writer::end_tag('div');
            $html .= html_writer::end_tag('div');
        }

        $html .= $this->continue_button($nextstageurl, 'post');
        $html .= html_writer::end_tag('div');

        return $html;
    }

    /**
     * Displays the general information about a backup file with non-standard format
     *
     * @param moodle_url $nextstageurl URL to send user to
     * @param array $details basic info about the file (format, type)
     * @return string HTML code to display
     */
    public function backup_details_nonstandard($nextstageurl, array $details) {

        $html  = html_writer::start_tag('div', array('class' => 'backup-restore nonstandardformat'));
        $html .= html_writer::start_tag('div', array('class' => 'backup-section'));
        $html .= $this->output->heading(get_string('backupdetails', 'backup'), 2, 'header');
        $html .= $this->output->box(get_string('backupdetailsnonstandardinfo', 'backup'), 'noticebox');
        $html .= $this->backup_detail_pair(
            get_string('backupformat', 'backup'),
            get_string('backupformat'.$details['format'], 'backup'));
        $html .= $this->backup_detail_pair(
            get_string('backuptype', 'backup'),
            get_string('backuptype'.$details['type'], 'backup'));
        $html .= html_writer::end_tag('div');
        $html .= $this->continue_button($nextstageurl, 'post');
        $html .= html_writer::end_tag('div');

        return $html;
    }

    /**
     * Displays the general information about a backup file with unknown format
     *
     * @param moodle_url $nextstageurl URL to send user to
     * @return string HTML code to display
     */
    public function backup_details_unknown(moodle_url $nextstageurl) {

        $html  = html_writer::start_div('unknownformat');
        $html .= $this->output->heading(get_string('errorinvalidformat', 'backup'), 2);
        $html .= $this->output->notification(get_string('errorinvalidformatinfo', 'backup'), 'notifyproblem');
        $html .= $this->continue_button($nextstageurl, 'post');
        $html .= html_writer::end_div();

        return $html;
    }

    /**
     * Displays a course selector for restore
     *
     * @param moodle_url $nextstageurl
     * @param bool $wholecourse true if we are restoring whole course (as with backup::TYPE_1COURSE), false otherwise
     * @param restore_category_search $categories
     * @param restore_course_search $courses
     * @param int $currentcourse
     * @return string
     */
    public function course_selector(moodle_url $nextstageurl, $wholecourse = true, restore_category_search $categories = null,
                                    restore_course_search $courses = null, $currentcourse = null) {
        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');

        // These variables are used to check if the form using this function was submitted.
        $target = optional_param('target', false, PARAM_INT);
        $targetid = optional_param('targetid', null, PARAM_INT);

        // Check if they submitted the form but did not provide all the data we need.
        $missingdata = false;
        if ($target and is_null($targetid)) {
            $missingdata = true;
        }

        $nextstageurl->param('sesskey', sesskey());

        $form = html_writer::start_tag('form', array('method' => 'post', 'action' => $nextstageurl->out_omit_querystring(),
            'class' => 'mform'));
        foreach ($nextstageurl->params() as $key => $value) {
            $form .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => $key, 'value' => $value));
        }

        $hasrestoreoption = false;

        $html  = html_writer::start_tag('div', array('class' => 'backup-course-selector backup-restore'));
        if ($wholecourse && !empty($categories) && ($categories->get_count() > 0 || $categories->get_search())) {
            // New course.
            $hasrestoreoption = true;
            $html .= $form;
            $html .= html_writer::start_tag('div', array('class' => 'bcs-new-course backup-section'));
            $html .= $this->output->heading(get_string('restoretonewcourse', 'backup'), 2, array('class' => 'header'));
            $html .= $this->backup_detail_input(get_string('restoretonewcourse', 'backup'), 'radio', 'target',
                backup::TARGET_NEW_COURSE, array('checked' => 'checked'));
            $selectacategoryhtml = $this->backup_detail_pair(get_string('selectacategory', 'backup'), $this->render($categories));
            // Display the category selection as required if the form was submitted but this data was not supplied.
            if ($missingdata && $target == backup::TARGET_NEW_COURSE) {
                $html .= html_writer::span(get_string('required'), 'error');
                $html .= html_writer::start_tag('fieldset', array('class' => 'error'));
                $html .= $selectacategoryhtml;
                $html .= html_writer::end_tag('fieldset');
            } else {
                $html .= $selectacategoryhtml;
            }
            $attrs = array('type' => 'submit', 'value' => get_string('continue'), 'class' => 'btn btn-primary');
            $html .= $this->backup_detail_pair('', html_writer::empty_tag('input', $attrs));
            $html .= html_writer::end_tag('div');
            $html .= html_writer::end_tag('form');
        }

        if ($wholecourse && !empty($currentcourse)) {
            // Current course.
            $hasrestoreoption = true;
            $html .= $form;
            $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'targetid', 'value' => $currentcourse));
            $html .= html_writer::start_tag('div', array('class' => 'bcs-current-course backup-section'));
            $html .= $this->output->heading(get_string('restoretocurrentcourse', 'backup'), 2, array('class' => 'header'));
            $html .= $this->backup_detail_input(get_string('restoretocurrentcourseadding', 'backup'), 'radio', 'target',
                backup::TARGET_CURRENT_ADDING, array('checked' => 'checked'));
            $html .= $this->backup_detail_input(get_string('restoretocurrentcoursedeleting', 'backup'), 'radio', 'target',
                backup::TARGET_CURRENT_DELETING);
            $attrs = array('type' => 'submit', 'value' => get_string('continue'), 'class' => 'btn btn-primary');
            $html .= $this->backup_detail_pair('', html_writer::empty_tag('input', $attrs));
            $html .= html_writer::end_tag('div');
            $html .= html_writer::end_tag('form');
        }

        // If we are restoring an activity, then include the current course.
        if (!$wholecourse) {
            $courses->invalidate_results(); // Clean list of courses.
            $courses->set_include_currentcourse();
        }
        if (!empty($courses) && ($courses->get_count() > 0 || $courses->get_search())) {
            // Existing course.
            $hasrestoreoption = true;
            $html .= $form;
            $html .= html_writer::start_tag('div', array('class' => 'bcs-existing-course backup-section'));
            $html .= $this->output->heading(get_string('restoretoexistingcourse', 'backup'), 2, array('class' => 'header'));
            if ($wholecourse) {
                $html .= $this->backup_detail_input(get_string('restoretoexistingcourseadding', 'backup'), 'radio', 'target',
                    backup::TARGET_EXISTING_ADDING, array('checked' => 'checked'));
                $html .= $this->backup_detail_input(get_string('restoretoexistingcoursedeleting', 'backup'), 'radio', 'target',
                    backup::TARGET_EXISTING_DELETING);
            } else {
                $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'target', 'value' => backup::TARGET_EXISTING_ADDING));
            }
            $selectacoursehtml = $this->backup_detail_pair(get_string('selectacourse', 'backup'), $this->render($courses));
            // Display the course selection as required if the form was submitted but this data was not supplied.
            if ($missingdata && $target == backup::TARGET_EXISTING_ADDING) {
                $html .= html_writer::span(get_string('required'), 'error');
                $html .= html_writer::start_tag('fieldset', array('class' => 'error'));
                $html .= $selectacoursehtml;
                $html .= html_writer::end_tag('fieldset');
            } else {
                $html .= $selectacoursehtml;
            }
            $attrs = array('type' => 'submit', 'value' => get_string('continue'), 'class' => 'btn btn-primary');
            $html .= $this->backup_detail_pair('', html_writer::empty_tag('input', $attrs));
            $html .= html_writer::end_tag('div');
            $html .= html_writer::end_tag('form');
        }

        if (!$hasrestoreoption) {
            echo $this->output->notification(get_string('norestoreoptions', 'backup'));
        }

        $html .= html_writer::end_tag('div');
        return $html;
    }

    /**
     * Displays the import course selector
     *
     * @param moodle_url $nextstageurl
     * @param import_course_search $courses
     * @return string
     */
    public function import_course_selector(moodle_url $nextstageurl, import_course_search $courses = null) {
        $html  = html_writer::start_tag('div', array('class' => 'import-course-selector backup-restore'));
        $html .= html_writer::start_tag('form', array('method' => 'post', 'action' => $nextstageurl->out_omit_querystring()));
        foreach ($nextstageurl->params() as $key => $value) {
            $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => $key, 'value' => $value));
        }
        // We only allow import adding for now. Enforce it here.
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'target', 'value' => backup::TARGET_CURRENT_ADDING));
        $html .= html_writer::start_tag('div', array('class' => 'ics-existing-course backup-section'));
        $html .= $this->output->heading(get_string('importdatafrom'), 2, array('class' => 'header'));
        $html .= $this->backup_detail_pair(get_string('selectacourse', 'backup'), $this->render($courses));
        $attrs = array('type' => 'submit', 'value' => get_string('continue'), 'class' => 'btn btn-primary');
        $html .= html_writer::start_tag('div', array('class' => 'mt-3'));
        $html .= $this->backup_detail_pair('', html_writer::empty_tag('input', $attrs));
        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('form');
        $html .= html_writer::end_tag('div');
        return $html;
    }

    /**
     * Creates a detailed pairing (key + value)
     *
     * @staticvar int $count
     * @param string $label
     * @param string $value
     * @return string
     */
    protected function backup_detail_pair($label, $value) {
        static $count = 0;
        $count ++;
        $html  = html_writer::start_tag('div', ['class' => 'detail-pair', 'role' => 'row']);
        $html .= html_writer::tag('div', $label, ['class' => 'detail-pair-label mb-2', 'role' => 'cell']);
        $html .= html_writer::tag('div', $value, ['class' => 'detail-pair-value pl-2', 'role' => 'cell']);
        $html .= html_writer::end_tag('div');
        return $html;
    }

    /**
     * Creates a unique id string by appending an incremental number to the prefix.
     *
     * @param string $prefix To be used as the left part of the id string.
     * @return string
     */
    protected function make_unique_id(string $prefix): string {
        static $count = 0;

        return $prefix . '-' . $count++;
    }

    /**
     * Created a detailed pairing with an input
     *
     * @param string $label
     * @param string $type
     * @param string $name
     * @param string $value
     * @param array $attributes
     * @param string|null $description
     * @return string
     */
    protected function backup_detail_input($label, $type, $name, $value, array $attributes = array(), $description = null) {
        if (!empty($description)) {
            $description = html_writer::tag('span', $description, array('class' => 'description'));
        } else {
            $description = '';
        }
        $id = $this->make_unique_id('detail-pair-value');
        return $this->backup_detail_pair(
            html_writer::label($label, $id),
            html_writer::empty_tag('input', $attributes + ['id' => $id, 'name' => $name, 'type' => $type, 'value' => $value]) .
                $description
        );
    }

    /**
     * Creates a detailed pairing with a select
     *
     * @param string $label
     * @param string $name
     * @param array $options
     * @param string $selected
     * @param bool $nothing
     * @param array $attributes
     * @param string|null $description
     * @return string
     */
    protected function backup_detail_select($label, $name, $options, $selected = '', $nothing = false, array $attributes = array(), $description = null) {
        if (!empty ($description)) {
            $description = html_writer::tag('span', $description, array('class' => 'description'));
        } else {
            $description = '';
        }
        return $this->backup_detail_pair($label, html_writer::select($options, $name, $selected, false, $attributes).$description);
    }

    /**
     * Displays precheck notices
     *
     * @param array $results
     * @return string
     */
    public function precheck_notices($results) {
        $output = html_writer::start_tag('div', array('class' => 'restore-precheck-notices'));
        if (array_key_exists('errors', $results)) {
            foreach ($results['errors'] as $error) {
                $output .= $this->output->notification($error);
            }
        }
        if (array_key_exists('warnings', $results)) {
            foreach ($results['warnings'] as $warning) {
                $output .= $this->output->notification($warning, 'notifyproblem');
            }
        }
        return $output.html_writer::end_tag('div');
    }

    /**
     * Displays substage buttons
     *
     * @param bool $haserrors
     * @return string
     */
    public function substage_buttons($haserrors) {
        $output  = html_writer::start_tag('div', array('continuebutton'));
        if (!$haserrors) {
            $attrs = array('type' => 'submit', 'value' => get_string('continue'), 'class' => 'btn btn-primary');
            $output .= html_writer::empty_tag('input', $attrs);
        }
        $attrs = array('type' => 'submit', 'name' => 'cancel', 'value' => get_string('cancel'), 'class' => 'btn btn-secondary');
        $output .= html_writer::empty_tag('input', $attrs);
        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Displays a role mapping interface
     *
     * @param array $rolemappings
     * @param array $roles
     * @return string
     */
    public function role_mappings($rolemappings, $roles) {
        $roles[0] = get_string('none');
        $output  = html_writer::start_tag('div', array('class' => 'restore-rolemappings'));
        $output .= $this->output->heading(get_string('restorerolemappings', 'backup'), 2);
        foreach ($rolemappings as $id => $mapping) {
            $label = $mapping->name;
            $name = 'mapping'.$id;
            $selected = $mapping->targetroleid;
            $output .= $this->backup_detail_select($label, $name, $roles, $mapping->targetroleid, false, array(), $mapping->description);
        }
        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Displays a continue button
     *
     * @param string|moodle_url $url
     * @param string $method
     * @return string
     */
    public function continue_button($url, $method = 'post') {
        if (!($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        if ($method != 'post') {
            $method = 'get';
        }
        $url->param('sesskey', sesskey());
        $button = new single_button($url, get_string('continue'), $method, true);
        $button->class = 'continuebutton';
        return $this->render($button);
    }
    /**
     * Print a backup files tree
     * @param array $options
     * @return string
     */
    public function backup_files_viewer(array $options = null) {
        $files = new backup_files_viewer($options);
        return $this->render($files);
    }

    /**
     * Generate the status indicator markup for display in the
     * backup restore file area UI.
     *
     * @param int $statuscode The status code of the backup.
     * @param string $backupid The backup record id.
     * @return string|boolean $status The status indicator for the operation.
     */
    public function get_status_display($statuscode, $backupid, $restoreid=null, $operation='backup') {
        if ($statuscode == backup::STATUS_AWAITING
            || $statuscode == backup::STATUS_EXECUTING
            || $statuscode == backup::STATUS_REQUIRE_CONV) {  // In progress.
            $progresssetup = array(
                'backupid' => $backupid,
                'restoreid' => $restoreid,
                'operation' => $operation,
                'width' => '100'
            );
            $status = $this->render_from_template('core/async_backup_progress', $progresssetup);
        } else if ($statuscode == backup::STATUS_FINISHED_ERR) { // Error.
            $icon = $this->output->render(new \pix_icon('i/delete', get_string('failed', 'backup')));
            $status = \html_writer::span($icon, 'action-icon');
        } else if ($statuscode == backup::STATUS_FINISHED_OK) { // Complete.
            $icon = $this->output->render(new \pix_icon('i/checked', get_string('successful', 'backup')));
            $status = \html_writer::span($icon, 'action-icon');
        }

        return $status;
    }

    /**
     * Displays a backup files viewer
     *
     * @global stdClass $USER
     * @param backup_files_viewer $viewer
     * @return string
     */
    public function render_backup_files_viewer(backup_files_viewer $viewer) {
        global $CFG;
        $files = $viewer->files;

        $async = async_helper::is_async_enabled();

        $tablehead = array(
                get_string('filename', 'backup'),
                get_string('time'),
                get_string('size'),
                get_string('download'),
                get_string('restore'));
        if ($async) {
            $tablehead[] = get_string('status', 'backup');
        }

        $table = new html_table();
        $table->attributes['class'] = 'backup-files-table generaltable';
        $table->head = $tablehead;
        $table->width = '100%';
        $table->data = array();

        // First add in progress asynchronous backups.
        // Only if asynchronous backups are enabled.
        // Also only render async status in correct area. Courese OR activity (not both).
        if ($async
                && (($viewer->filearea == 'course' && $viewer->currentcontext->contextlevel == CONTEXT_COURSE)
                || ($viewer->filearea == 'activity' && $viewer->currentcontext->contextlevel == CONTEXT_MODULE))
                ) {
                    $table->data = \async_helper::get_async_backups($this, $viewer->currentcontext->instanceid);
        }

        // Add completed backups.
        foreach ($files as $file) {
            if ($file->is_directory()) {
                continue;
            }
            $fileurl = moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                null,
                $file->get_filepath(),
                $file->get_filename(),
                true
            );
            $params = array();
            $params['action'] = 'choosebackupfile';
            $params['filename'] = $file->get_filename();
            $params['filepath'] = $file->get_filepath();
            $params['component'] = $file->get_component();
            $params['filearea'] = $file->get_filearea();
            $params['filecontextid'] = $file->get_contextid();
            $params['contextid'] = $viewer->currentcontext->id;
            $params['itemid'] = $file->get_itemid();
            $restoreurl = new moodle_url('/backup/restorefile.php', $params);
            $restorelink = html_writer::link($restoreurl, get_string('restore'));
            $downloadlink = html_writer::link($fileurl, get_string('download'));

            // Conditional display of the restore and download links, initially only for the 'automated' filearea.
            if ($params['filearea'] == 'automated') {
                if (!has_capability('moodle/restore:viewautomatedfilearea', $viewer->currentcontext)) {
                    $restorelink = '';
                }
                if (!can_download_from_backup_filearea($params['filearea'], $viewer->currentcontext)) {
                    $downloadlink = '';
                }
            }
            $tabledata = array(
                $file->get_filename(),
                userdate ($file->get_timemodified()),
                display_size ($file->get_filesize()),
                $downloadlink,
                $restorelink
            );
            if ($async) {
                $tabledata[] = $this->get_status_display(backup::STATUS_FINISHED_OK, null);
            }

            $table->data[] = $tabledata;
        }

        $html = html_writer::table($table);

        // For automated backups, the ability to manage backup files is controlled by the ability to download them.
        // All files must be from the same file area in a backup_files_viewer.
        $canmanagebackups = true;
        if ($viewer->filearea == 'automated') {
            if (!can_download_from_backup_filearea($viewer->filearea, $viewer->currentcontext)) {
                $canmanagebackups = false;
            }
        }

        if ($canmanagebackups) {
            $html .= $this->output->single_button(
                new moodle_url('/backup/backupfilesedit.php', array(
                        'currentcontext' => $viewer->currentcontext->id,
                        'contextid' => $viewer->filecontext->id,
                        'filearea' => $viewer->filearea,
                        'component' => $viewer->component,
                        'returnurl' => $this->page->url->out())
                ),
                get_string('managefiles', 'backup'),
                'post'
            );
        }

        return $html;
    }

    /**
     * Renders a restore course search object
     *
     * @param restore_course_search $component
     * @return string
     */
    public function render_restore_course_search(restore_course_search $component) {
        $output = html_writer::start_tag('div', array('class' => 'restore-course-search form-inline mb-1'));
        $output .= html_writer::start_tag('div', array('class' => 'rcs-results table-sm w-75'));

        $table = new html_table();
        $table->head = array('', get_string('shortnamecourse'), get_string('fullnamecourse'));
        $table->data = array();
        if ($component->get_count() !== 0) {
            foreach ($component->get_results() as $course) {
                $row = new html_table_row();
                $row->attributes['class'] = 'rcs-course';
                if (!$course->visible) {
                    $row->attributes['class'] .= ' dimmed';
                }
                $id = $this->make_unique_id('restore-course');
                $row->cells = [
                    html_writer::empty_tag('input', ['type' => 'radio', 'name' => 'targetid', 'value' => $course->id,
                        'id' => $id]),
                    html_writer::label(
                        format_string($course->shortname, true, ['context' => context_course::instance($course->id)]),
                        $id,
                        true,
                        ['class' => 'd-block']
                    ),
                    format_string($course->fullname, true, ['context' => context_course::instance($course->id)])
                ];
                $table->data[] = $row;
            }
            if ($component->has_more_results()) {
                $cell = new html_table_cell(get_string('moreresults', 'backup'));
                $cell->colspan = 3;
                $cell->attributes['class'] = 'notifyproblem';
                $row = new html_table_row(array($cell));
                $row->attributes['class'] = 'rcs-course';
                $table->data[] = $row;
            }
        } else {
            $cell = new html_table_cell(get_string('nomatchingcourses', 'backup'));
            $cell->colspan = 3;
            $cell->attributes['class'] = 'notifyproblem';
            $row = new html_table_row(array($cell));
            $row->attributes['class'] = 'rcs-course';
            $table->data[] = $row;
        }
        $output .= html_writer::table($table);
        $output .= html_writer::end_tag('div');

        $output .= html_writer::start_tag('div', array('class' => 'rcs-search'));
        $attrs = array(
            'type' => 'text',
            'name' => restore_course_search::$VAR_SEARCH,
            'value' => $component->get_search(),
            'aria-label' => get_string('searchcourses'),
            'placeholder' => get_string('searchcourses'),
            'class' => 'form-control'
        );
        $output .= html_writer::empty_tag('input', $attrs);
        $attrs = array(
            'type' => 'submit',
            'name' => 'searchcourses',
            'value' => get_string('search'),
            'class' => 'btn btn-secondary'
        );
        $output .= html_writer::empty_tag('input', $attrs);
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Renders an import course search object
     *
     * @param import_course_search $component
     * @return string
     */
    public function render_import_course_search(import_course_search $component) {
        $output = html_writer::start_tag('div', array('class' => 'import-course-search'));
        if ($component->get_count() === 0) {
            $output .= $this->output->notification(get_string('nomatchingcourses', 'backup'));

            $output .= html_writer::start_tag('div', array('class' => 'ics-search form-inline'));
            $attrs = array(
                'type' => 'text',
                'name' => restore_course_search::$VAR_SEARCH,
                'value' => $component->get_search(),
                'aria-label' => get_string('searchcourses'),
                'placeholder' => get_string('searchcourses'),
                'class' => 'form-control'
            );
            $output .= html_writer::empty_tag('input', $attrs);
            $attrs = array(
                'type' => 'submit',
                'name' => 'searchcourses',
                'value' => get_string('search'),
                'class' => 'btn btn-secondary ml-1'
            );
            $output .= html_writer::empty_tag('input', $attrs);
            $output .= html_writer::end_tag('div');

            $output .= html_writer::end_tag('div');
            return $output;
        }

        $countstr = '';
        if ($component->has_more_results()) {
            $countstr = get_string('morecoursesearchresults', 'backup', $component->get_count());
        } else {
            $countstr = get_string('totalcoursesearchresults', 'backup', $component->get_count());
        }

        $output .= html_writer::tag('div', $countstr, array('class' => 'ics-totalresults'));
        $output .= html_writer::start_tag('div', array('class' => 'ics-results'));

        $table = new html_table();
        $table->head = array('', get_string('shortnamecourse'), get_string('fullnamecourse'));
        $table->data = array();
        foreach ($component->get_results() as $course) {
            $row = new html_table_row();
            $row->attributes['class'] = 'ics-course';
            if (!$course->visible) {
                $row->attributes['class'] .= ' dimmed';
            }
            $id = $this->make_unique_id('import-course');
            $row->cells = [
                html_writer::empty_tag('input', ['type' => 'radio', 'name' => 'importid', 'value' => $course->id,
                    'id' => $id]),
                html_writer::label(
                    format_string($course->shortname, true, ['context' => context_course::instance($course->id)]),
                    $id,
                    true,
                    ['class' => 'd-block']
                ),
                format_string($course->fullname, true, ['context' => context_course::instance($course->id)])
            ];
            $table->data[] = $row;
        }
        if ($component->has_more_results()) {
            $cell = new html_table_cell(get_string('moreresults', 'backup'));
            $cell->colspan = 3;
            $cell->attributes['class'] = 'notifyproblem';
            $row = new html_table_row(array($cell));
            $row->attributes['class'] = 'rcs-course';
            $table->data[] = $row;
        }
        $output .= html_writer::table($table);
        $output .= html_writer::end_tag('div');

        $output .= html_writer::start_tag('div', array('class' => 'ics-search form-inline'));
        $attrs = array(
            'type' => 'text',
            'name' => restore_course_search::$VAR_SEARCH,
            'value' => $component->get_search(),
            'aria-label' => get_string('searchcourses'),
            'placeholder' => get_string('searchcourses'),
            'class' => 'form-control');
        $output .= html_writer::empty_tag('input', $attrs);
        $attrs = array(
            'type' => 'submit',
            'name' => 'searchcourses',
            'value' => get_string('search'),
            'class' => 'btn btn-secondary ml-1'
        );
        $output .= html_writer::empty_tag('input', $attrs);
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Renders a restore category search object
     *
     * @param restore_category_search $component
     * @return string
     */
    public function render_restore_category_search(restore_category_search $component) {
        $output = html_writer::start_tag('div', array('class' => 'restore-course-search form-inline mb-1'));
        $output .= html_writer::start_tag('div', array('class' => 'rcs-results table-sm w-75'));

        $table = new html_table();
        $table->head = array('', get_string('name'), get_string('description'));
        $table->data = array();

        if ($component->get_count() !== 0) {
            foreach ($component->get_results() as $category) {
                $row = new html_table_row();
                $row->attributes['class'] = 'rcs-course';
                if (!$category->visible) {
                    $row->attributes['class'] .= ' dimmed';
                }
                $context = context_coursecat::instance($category->id);
                $id = $this->make_unique_id('restore-category');
                $row->cells = [
                    html_writer::empty_tag('input', ['type' => 'radio', 'name' => 'targetid', 'value' => $category->id,
                        'id' => $id]),
                    html_writer::label(
                        format_string($category->name, true, ['context' => context_coursecat::instance($category->id)]),
                        $id,
                        true,
                        ['class' => 'd-block']
                    ),
                    format_text(file_rewrite_pluginfile_urls($category->description, 'pluginfile.php', $context->id,
                        'coursecat', 'description', null), $category->descriptionformat, ['overflowdiv' => true])
                ];
                $table->data[] = $row;
            }
            if ($component->has_more_results()) {
                $cell = new html_table_cell(get_string('moreresults', 'backup'));
                $cell->attributes['class'] = 'notifyproblem';
                $cell->colspan = 3;
                $row = new html_table_row(array($cell));
                $row->attributes['class'] = 'rcs-course';
                $table->data[] = $row;
            }
        } else {
            $cell = new html_table_cell(get_string('nomatchingcourses', 'backup'));
            $cell->colspan = 3;
            $cell->attributes['class'] = 'notifyproblem';
            $row = new html_table_row(array($cell));
            $row->attributes['class'] = 'rcs-course';
            $table->data[] = $row;
        }
        $output .= html_writer::table($table);
        $output .= html_writer::end_tag('div');

        $output .= html_writer::start_tag('div', array('class' => 'rcs-search'));
        $attrs = array(
            'type' => 'text',
            'name' => restore_category_search::$VAR_SEARCH,
            'value' => $component->get_search(),
            'aria-label' => get_string('searchcoursecategories'),
            'placeholder' => get_string('searchcoursecategories'),
            'class' => 'form-control'
        );
        $output .= html_writer::empty_tag('input', $attrs);
        $attrs = array(
            'type' => 'submit',
            'name' => 'searchcourses',
            'value' => get_string('search'),
            'class' => 'btn btn-secondary'
        );
        $output .= html_writer::empty_tag('input', $attrs);
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Get markup to render table for all of a users async
     * in progress restores.
     *
     * @param int $userid The Moodle user id.
     * @param \context $context The Moodle context for these restores.
     * @return string $html The table HTML.
     */
    public function restore_progress_viewer ($userid, $context) {
        $tablehead = array(get_string('course'), get_string('time'), get_string('status', 'backup'));

        $table = new html_table();
        $table->attributes['class'] = 'backup-files-table generaltable';
        $table->head = $tablehead;
        $tabledata = array();

        // Get all in progress async restores for this user.
        $restores = \async_helper::get_async_restores($userid);

        // For each backup get, new item name, time restore created and progress.
        foreach ($restores as $restore) {

            $restorename = \async_helper::get_restore_name($context);
            $timecreated = $restore->timecreated;
            $status = $this->get_status_display($restore->status, $restore->backupid, $restore->backupid, null, 'restore');

            $tablerow = array($restorename, userdate($timecreated), $status);
            $tabledata[] = $tablerow;
        }

        $table->data = $tabledata;
        $html = html_writer::table($table);

        return $html;
    }

    /**
     * Get markup to render table for all of a users course copies.
     *
     * @param int $userid The Moodle user id.
     * @param int $courseid The id of the course to get the backups for.
     * @return string $html The table HTML.
     */
    public function copy_progress_viewer(int $userid, int $courseid): string {
        $tablehead = array(
            get_string('copysource', 'backup'),
            get_string('copydest', 'backup'),
            get_string('time'),
            get_string('copyop', 'backup'),
            get_string('status', 'backup')
        );

        $table = new html_table();
        $table->attributes['class'] = 'backup-files-table generaltable';
        $table->head = $tablehead;

        $tabledata = array();

        // Get all in progress course copies for this user.
        $copies = \core_backup\copy\copy::get_copies($userid, $courseid);

        foreach ($copies as $copy) {
            $sourceurl = new \moodle_url('/course/view.php', array('id' => $copy->sourceid));

            $tablerow = array(
                html_writer::link($sourceurl, $copy->source),
                $copy->destination,
                userdate($copy->time),
                get_string($copy->operation),
                $this->get_status_display($copy->status, $copy->backupid, $copy->restoreid, $copy->operation)
            );
            $tabledata[] = $tablerow;
        }

        $table->data = $tabledata;
        $html = html_writer::table($table);

        return $html;
    }
}

/**
 * Data structure representing backup files viewer
 *
 * @copyright 2010 Dongsheng Cai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class backup_files_viewer implements renderable {

    /**
     * @var array
     */
    public $files;

    /**
     * @var context
     */
    public $filecontext;

    /**
     * @var string
     */
    public $component;

    /**
     * @var string
     */
    public $filearea;

    /**
     * @var context
     */
    public $currentcontext;

    /**
     * Constructor of backup_files_viewer class
     * @param array $options
     */
    public function __construct(array $options = null) {
        global $CFG, $USER;
        $fs = get_file_storage();
        $this->currentcontext = $options['currentcontext'];
        $this->filecontext    = $options['filecontext'];
        $this->component      = $options['component'];
        $this->filearea       = $options['filearea'];
        $files = $fs->get_area_files($this->filecontext->id, $this->component, $this->filearea, false, 'timecreated');
        $this->files = array_reverse($files);
    }
}
