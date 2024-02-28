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
 * Class for providing quiz settings, to make setting up quiz form manageable.
 *
 * To make sure there are no inconsistencies between data sets, run tests in tests/phpunit/settings_provider_test.php.
 *
 * @package    quizaccess_seb
 * @author     Luca Bösch <luca.boesch@bfh.ch>
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quizaccess_seb;

use context_module;
use context_user;
use lang_string;
use stdClass;
use stored_file;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class for providing quiz settings, to make setting up quiz form manageable.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings_provider {

    /**
     * No SEB should be used.
     */
    const USE_SEB_NO = 0;

    /**
     * Use SEB and configure it manually.
     */
    const USE_SEB_CONFIG_MANUALLY = 1;

    /**
     * Use SEB config from pre configured template.
     */
    const USE_SEB_TEMPLATE = 2;

    /**
     * Use SEB config from uploaded config file.
     */
    const USE_SEB_UPLOAD_CONFIG = 3;

    /**
     * Use client config. Not SEB config is required.
     */
    const USE_SEB_CLIENT_CONFIG = 4;

    /**
     * Insert form element.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     * @param \HTML_QuickForm_element $element Element to insert.
     * @param string $before Insert element before.
     */
    protected static function insert_element(\mod_quiz_mod_form $quizform,
                                             \MoodleQuickForm $mform, \HTML_QuickForm_element $element, $before = 'security') {
        $mform->insertElementBefore($element, $before);
    }

    /**
     * Remove element from the form.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     * @param string $elementname Element name.
     */
    protected static function remove_element(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform, string  $elementname) {
        if ($mform->elementExists($elementname)) {
            $mform->removeElement($elementname);
            $mform->setDefault($elementname, null);
        }
    }

    /**
     * Add help button to the element.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     * @param string $elementname Element name.
     */
    protected static function add_help_button(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform, string $elementname) {
        if ($mform->elementExists($elementname)) {
            $mform->addHelpButton($elementname, $elementname, 'quizaccess_seb');
        }
    }

    /**
     * Set default value for the element.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     * @param string $elementname Element name.
     * @param mixed $value Default value.
     */
    protected static function set_default(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform, string  $elementname, $value) {
        $mform->setDefault($elementname, $value);
    }

    /**
     * Set element type.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     * @param string $elementname Element name.
     * @param string $type Type of the form element.
     */
    protected static function set_type(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform, string $elementname, string $type) {
        $mform->setType($elementname, $type);
    }

    /**
     * Freeze form element.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     * @param string $elementname Element name.
     */
    protected static function freeze_element(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform, string $elementname) {
        if ($mform->elementExists($elementname)) {
            $mform->freeze($elementname);
        }
    }

    /**
     * Add SEB header element to  the form.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     */
    protected static function add_seb_header_element(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform) {
        global  $OUTPUT;

        $element = $mform->createElement('header', 'seb', get_string('seb', 'quizaccess_seb'));
        self::insert_element($quizform, $mform, $element);

        // Display notification about locked settings.
        if (self::is_seb_settings_locked($quizform->get_instance())) {
            $notify = new \core\output\notification(
                get_string('settingsfrozen', 'quizaccess_seb'),
                \core\output\notification::NOTIFY_WARNING
            );

            $notifyelement = $mform->createElement('html', $OUTPUT->render($notify));
            self::insert_element($quizform, $mform, $notifyelement);
        }

        if (self::is_conflicting_permissions($quizform->get_context())) {
            $notify = new \core\output\notification(
                get_string('conflictingsettings', 'quizaccess_seb'),
                \core\output\notification::NOTIFY_WARNING
            );

            $notifyelement = $mform->createElement('html', $OUTPUT->render($notify));
            self::insert_element($quizform, $mform, $notifyelement);
        }
    }

    /**
     * Add SEB usage element with all available options.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     */
    protected static function add_seb_usage_options(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform) {
        $element = $mform->createElement(
            'select',
            'seb_requiresafeexambrowser',
            get_string('seb_requiresafeexambrowser', 'quizaccess_seb'),
            self::get_requiresafeexambrowser_options($quizform->get_context())
        );

        self::insert_element($quizform, $mform, $element);
        self::set_type($quizform, $mform, 'seb_requiresafeexambrowser', PARAM_INT);
        self::set_default($quizform, $mform, 'seb_requiresafeexambrowser', self::USE_SEB_NO);
        self::add_help_button($quizform, $mform, 'seb_requiresafeexambrowser');

        if (self::is_conflicting_permissions($quizform->get_context())) {
            self::freeze_element($quizform, $mform, 'seb_requiresafeexambrowser');
        }
    }

    /**
     * Add Templates element.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     */
    protected static function add_seb_templates(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform) {
        if (self::can_use_seb_template($quizform->get_context()) || self::is_conflicting_permissions($quizform->get_context())) {
            $element = $mform->createElement(
                'select',
                'seb_templateid',
                get_string('seb_templateid', 'quizaccess_seb'),
                self::get_template_options()
            );
        } else {
            $element = $mform->createElement('hidden', 'seb_templateid');
        }

        self::insert_element($quizform, $mform, $element);
        self::set_type($quizform, $mform, 'seb_templateid', PARAM_INT);
        self::set_default($quizform, $mform, 'seb_templateid', 0);
        self::add_help_button($quizform, $mform, 'seb_templateid');

        // In case if the user can't use templates, but the quiz is configured to use them,
        // we'd like to display template, but freeze it.
        if (self::is_conflicting_permissions($quizform->get_context())) {
            self::freeze_element($quizform, $mform, 'seb_templateid');
        }
    }

    /**
     * Add upload config file element.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     */
    protected static function add_seb_config_file(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform) {
        $itemid = 0;

        $draftitemid = 0;
        file_prepare_draft_area(
            $draftitemid,
            $quizform->get_context()->id,
            'quizaccess_seb',
            'filemanager_sebconfigfile',
            $itemid
        );

        if (self::can_upload_seb_file($quizform->get_context())) {
            $element = $mform->createElement(
                'filemanager',
                'filemanager_sebconfigfile',
                get_string('filemanager_sebconfigfile', 'quizaccess_seb'),
                null,
                self::get_filemanager_options()
            );
        } else {
            $element = $mform->createElement('hidden', 'filemanager_sebconfigfile');
        }

        self::insert_element($quizform, $mform, $element);
        self::set_type($quizform, $mform, 'filemanager_sebconfigfile', PARAM_RAW);
        self::set_default($quizform, $mform, 'filemanager_sebconfigfile', $draftitemid);
        self::add_help_button($quizform, $mform, 'filemanager_sebconfigfile');
    }

    /**
     * Add Show Safe Exam Browser download button.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     */
    protected static function add_seb_show_download_link(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform) {
        if (self::can_change_seb_showsebdownloadlink($quizform->get_context())) {
            $element = $mform->createElement('selectyesno',
                'seb_showsebdownloadlink',
                get_string('seb_showsebdownloadlink', 'quizaccess_seb')
            );
            self::insert_element($quizform, $mform, $element);
            self::set_type($quizform, $mform, 'seb_showsebdownloadlink', PARAM_BOOL);
            self::set_default($quizform, $mform, 'seb_showsebdownloadlink', 1);
            self::add_help_button($quizform, $mform, 'seb_showsebdownloadlink');
        }
    }

    /**
     * Add Allowed Browser Exam Keys setting.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     */
    protected static function add_seb_allowedbrowserexamkeys(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform) {
        if (self::can_change_seb_allowedbrowserexamkeys($quizform->get_context())) {
            $element = $mform->createElement('textarea',
                'seb_allowedbrowserexamkeys',
                get_string('seb_allowedbrowserexamkeys', 'quizaccess_seb')
            );
            self::insert_element($quizform, $mform, $element);
            self::set_type($quizform, $mform, 'seb_allowedbrowserexamkeys', PARAM_RAW);
            self::set_default($quizform, $mform, 'seb_allowedbrowserexamkeys', '');
            self::add_help_button($quizform, $mform, 'seb_allowedbrowserexamkeys');
        }
    }

    /**
     * Add SEB config elements.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     */
    protected static function add_seb_config_elements(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform) {
        $defaults = self::get_seb_config_element_defaults();
        $types = self::get_seb_config_element_types();

        foreach (self::get_seb_config_elements() as $name => $type) {
            if (!self::can_manage_seb_config_setting($name, $quizform->get_context())) {
                $type = 'hidden';
            }

            $element = $mform->createElement($type, $name, get_string($name, 'quizaccess_seb'));
            self::insert_element($quizform, $mform, $element);
            unset($element); // We need to make sure each &element only references the current element in loop.

            self::add_help_button($quizform, $mform, $name);

            if (isset($defaults[$name])) {
                self::set_default($quizform, $mform, $name, $defaults[$name]);
            }

            if (isset($types[$name])) {
                self::set_type($quizform, $mform, $name, $types[$name]);
            }
        }
    }

    /**
     * Add setting fields.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     */
    public static function add_seb_settings_fields(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform) {
        if (self::can_configure_seb($quizform->get_context())) {
            self::add_seb_header_element($quizform, $mform);
            self::add_seb_usage_options($quizform, $mform);
            self::add_seb_templates($quizform, $mform);
            self::add_seb_config_file($quizform, $mform);
            self::add_seb_show_download_link($quizform, $mform);
            self::add_seb_config_elements($quizform, $mform);
            self::add_seb_allowedbrowserexamkeys($quizform, $mform);
            self::hide_seb_elements($quizform, $mform);
            self::lock_seb_elements($quizform, $mform);
        }
    }

    /**
     * Hide SEB elements if required.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     */
    protected static function hide_seb_elements(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform) {
        foreach (self::get_quiz_hideifs() as $elname => $rules) {
            if ($mform->elementExists($elname)) {
                foreach ($rules as $hideif) {
                    $mform->hideIf(
                        $hideif->get_element(),
                        $hideif->get_dependantname(),
                        $hideif->get_condition(),
                        $hideif->get_dependantvalue()
                    );
                }
            }
        }
    }

    /**
     * Lock SEB elements if required.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     */
    protected static function lock_seb_elements(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform) {
        if (self::is_seb_settings_locked($quizform->get_instance()) || self::is_conflicting_permissions($quizform->get_context())) {
            // Freeze common quiz settings.
            self::freeze_element($quizform, $mform, 'seb_requiresafeexambrowser');
            self::freeze_element($quizform, $mform, 'seb_templateid');
            self::freeze_element($quizform, $mform, 'seb_showsebdownloadlink');
            self::freeze_element($quizform, $mform, 'seb_allowedbrowserexamkeys');

            $quizsettings = seb_quiz_settings::get_by_quiz_id((int) $quizform->get_instance());

            // If the file has been uploaded, then replace it with the link to download the file.
            if (!empty($quizsettings) && $quizsettings->get('requiresafeexambrowser') == self::USE_SEB_UPLOAD_CONFIG) {
                self::remove_element($quizform, $mform, 'filemanager_sebconfigfile');
                if ($link = self::get_uploaded_seb_file_download_link($quizform, $mform)) {
                    $element = $mform->createElement(
                        'static',
                        'filemanager_sebconfigfile',
                        get_string('filemanager_sebconfigfile', 'quizaccess_seb'),
                        $link
                    );
                    self::insert_element($quizform, $mform, $element, 'seb_showsebdownloadlink');
                }
            }

            // Remove template ID if not using template for this quiz.
            if (empty($quizsettings) || $quizsettings->get('requiresafeexambrowser') != self::USE_SEB_TEMPLATE) {
                $mform->removeElement('seb_templateid');
            }

            // Freeze all SEB specific settings.
            foreach (self::get_seb_config_elements() as $element => $type) {
                self::freeze_element($quizform, $mform, $element);
            }
        }
    }

    /**
     * Return uploaded SEB config file link.
     *
     * @param \mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param \MoodleQuickForm $mform the wrapped MoodleQuickForm.
     * @return string
     */
    protected static function get_uploaded_seb_file_download_link(\mod_quiz_mod_form $quizform, \MoodleQuickForm $mform): string {
        $link = '';
        $file = self::get_module_context_sebconfig_file($quizform->get_coursemodule()->id);

        if ($file) {
            $url = \moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename(),
                true
            );
            $link = \html_writer::link($url, get_string('downloadsebconfig', 'quizaccess_seb'));
        }

        return $link;
    }

    /**
     * Get the type of element for each of the form elements in quiz settings.
     *
     * Contains all setting elements. Array key is name of 'form element'/'database column (excluding prefix)'.
     *
     * @return array All quiz form elements to be added and their types.
     */
    public static function get_seb_config_elements(): array {
        return [
            'seb_linkquitseb' => 'text',
            'seb_userconfirmquit' => 'selectyesno',
            'seb_allowuserquitseb' => 'selectyesno',
            'seb_quitpassword' => 'passwordunmask',
            'seb_allowreloadinexam' => 'selectyesno',
            'seb_showsebtaskbar' => 'selectyesno',
            'seb_showreloadbutton' => 'selectyesno',
            'seb_showtime' => 'selectyesno',
            'seb_showkeyboardlayout' => 'selectyesno',
            'seb_showwificontrol' => 'selectyesno',
            'seb_enableaudiocontrol' => 'selectyesno',
            'seb_muteonstartup' => 'selectyesno',
            'seb_allowspellchecking' => 'selectyesno',
            'seb_activateurlfiltering' => 'selectyesno',
            'seb_filterembeddedcontent' => 'selectyesno',
            'seb_expressionsallowed' => 'textarea',
            'seb_regexallowed' => 'textarea',
            'seb_expressionsblocked' => 'textarea',
            'seb_regexblocked' => 'textarea',
        ];
    }


    /**
     * Get the types of the quiz settings elements.
     * @return array List of types for the setting elements.
     */
    public static function get_seb_config_element_types(): array {
        return [
            'seb_linkquitseb' => PARAM_RAW,
            'seb_userconfirmquit' => PARAM_BOOL,
            'seb_allowuserquitseb' => PARAM_BOOL,
            'seb_quitpassword' => PARAM_RAW,
            'seb_allowreloadinexam' => PARAM_BOOL,
            'seb_showsebtaskbar' => PARAM_BOOL,
            'seb_showreloadbutton' => PARAM_BOOL,
            'seb_showtime' => PARAM_BOOL,
            'seb_showkeyboardlayout' => PARAM_BOOL,
            'seb_showwificontrol' => PARAM_BOOL,
            'seb_enableaudiocontrol' => PARAM_BOOL,
            'seb_muteonstartup' => PARAM_BOOL,
            'seb_allowspellchecking' => PARAM_BOOL,
            'seb_activateurlfiltering' => PARAM_BOOL,
            'seb_filterembeddedcontent' => PARAM_BOOL,
            'seb_expressionsallowed' => PARAM_RAW,
            'seb_regexallowed' => PARAM_RAW,
            'seb_expressionsblocked' => PARAM_RAW,
            'seb_regexblocked' => PARAM_RAW,
        ];
    }

    /**
     * Check that we have conflicting permissions.
     *
     * In Some point we can have settings save by the person who use specific
     * type of SEB usage (e.g. use templates). But then another person who can't
     * use template (but still can update other settings) edit the same quiz. This is
     * conflict of permissions and we'd like to build the settings form having this in
     * mind.
     *
     * @param \context $context Context used with capability checking.
     *
     * @return bool
     */
    public static function is_conflicting_permissions(\context $context) {
        if ($context instanceof \context_course) {
            return false;
        }

        $settings = seb_quiz_settings::get_record(['cmid' => (int) $context->instanceid]);

        if (empty($settings)) {
            return false;
        }

        if (!self::can_use_seb_template($context) &&
            $settings->get('requiresafeexambrowser') == self::USE_SEB_TEMPLATE) {
            return true;
        }

        if (!self::can_upload_seb_file($context) &&
            $settings->get('requiresafeexambrowser') == self::USE_SEB_UPLOAD_CONFIG) {
            return true;
        }

        if (!self::can_configure_manually($context) &&
            $settings->get('requiresafeexambrowser') == self::USE_SEB_CONFIG_MANUALLY) {
            return true;
        }

        return false;
    }

    /**
     * Returns a list of all options of SEB usage.
     *
     * @param \context $context Context used with capability checking selection options.
     * @return array
     */
    public static function get_requiresafeexambrowser_options(\context $context): array {
        $options[self::USE_SEB_NO] = get_string('no');

        if (self::can_configure_manually($context) || self::is_conflicting_permissions($context)) {
            $options[self::USE_SEB_CONFIG_MANUALLY] = get_string('seb_use_manually', 'quizaccess_seb');
        }

        if (self::can_use_seb_template($context) || self::is_conflicting_permissions($context)) {
            if (!empty(self::get_template_options())) {
                $options[self::USE_SEB_TEMPLATE] = get_string('seb_use_template', 'quizaccess_seb');
            }
        }

        if (self::can_upload_seb_file($context) || self::is_conflicting_permissions($context)) {
            $options[self::USE_SEB_UPLOAD_CONFIG] = get_string('seb_use_upload', 'quizaccess_seb');
        }

        $options[self::USE_SEB_CLIENT_CONFIG] = get_string('seb_use_client', 'quizaccess_seb');

        return $options;
    }

    /**
     * Returns a list of templates.
     * @return array
     */
    protected static function get_template_options(): array {
        $templates = [];
        $records = template::get_records(['enabled' => 1], 'name');
        if ($records) {
            foreach ($records as $record) {
                $templates[$record->get('id')] = $record->get('name');
            }
        }

        return $templates;
    }

    /**
     * Returns a list of options for the file manager element.
     * @return array
     */
    public static function get_filemanager_options(): array {
        return [
            'subdirs' => 0,
            'maxfiles' => 1,
            'accepted_types' => ['.seb']
        ];
    }

    /**
     * Get the default values of the quiz settings.
     *
     * Array key is name of 'form element'/'database column (excluding prefix)'.
     *
     * @return array List of settings and their defaults.
     */
    public static function get_seb_config_element_defaults(): array {
        return [
            'seb_linkquitseb' => '',
            'seb_userconfirmquit' => 1,
            'seb_allowuserquitseb' => 1,
            'seb_quitpassword' => '',
            'seb_allowreloadinexam' => 1,
            'seb_showsebtaskbar' => 1,
            'seb_showreloadbutton' => 1,
            'seb_showtime' => 1,
            'seb_showkeyboardlayout' => 1,
            'seb_showwificontrol' => 0,
            'seb_enableaudiocontrol' => 0,
            'seb_muteonstartup' => 0,
            'seb_allowspellchecking' => 0,
            'seb_activateurlfiltering' => 0,
            'seb_filterembeddedcontent' => 0,
            'seb_expressionsallowed' => '',
            'seb_regexallowed' => '',
            'seb_expressionsblocked' => '',
            'seb_regexblocked' => '',
        ];
    }

    /**
     * Validate that if a file has been uploaded by current user, that it is a valid PLIST XML file.
     * This function is only called if requiresafeexambrowser == settings_provider::USE_SEB_UPLOAD_CONFIG.
     *
     * @param string $itemid Item ID of file in user draft file area.
     * @return void|lang_string
     */
    public static function validate_draftarea_configfile($itemid) {
        // When saving the settings, this value will be null.
        if (is_null($itemid)) {
            return;
        }
        // If there is a config file uploaded, make sure it is a PList XML file.
        $file = self::get_current_user_draft_file($itemid);

        // If we require an SEB config uploaded, and the file exists, parse it.
        if ($file) {
            if (!helper::is_valid_seb_config($file->get_content())) {
                return new lang_string('fileparsefailed', 'quizaccess_seb');
            }
        }

        // If we require an SEB config uploaded, and the file does not exist, error.
        if (!$file) {
            return new lang_string('filenotpresent', 'quizaccess_seb');
        }
    }

    /**
     * Try and get a file in the user draft filearea by itemid.
     *
     * @param string $itemid Item ID of the file.
     * @return stored_file|null Returns null if no file is found.
     */
    public static function get_current_user_draft_file(string $itemid): ?stored_file {
        global $USER;
        $context = context_user::instance($USER->id);
        $fs = get_file_storage();
        if (!$files = $fs->get_area_files($context->id, 'user', 'draft', $itemid, 'id DESC', false)) {
            return null;
        }
        return reset($files);
    }

    /**
     * Get the file that is stored in the course module file area.
     *
     * @param string $cmid The course module id which is used as an itemid reference.
     * @return stored_file|null Returns null if no file is found.
     */
    public static function get_module_context_sebconfig_file(string $cmid): ?stored_file {
        $fs = new \file_storage();
        $context = context_module::instance($cmid);

        if (!$files = $fs->get_area_files($context->id, 'quizaccess_seb', 'filemanager_sebconfigfile', 0,
            'id DESC', false)) {
            return null;
        }

        return reset($files);
    }

    /**
     * Saves filemanager_sebconfigfile files to the moodle storage backend.
     *
     * @param string $draftitemid The id of the draft area to use.
     * @param string $cmid The cmid of for the quiz.
     * @return bool Always true
     */
    public static function save_filemanager_sebconfigfile_draftarea(string $draftitemid, string $cmid): bool {
        if ($draftitemid) {
            $context = context_module::instance($cmid);
            file_save_draft_area_files($draftitemid, $context->id, 'quizaccess_seb', 'filemanager_sebconfigfile',
                0, []);
        }

        return true;
    }

    /**
     * Cleanup function to delete the saved config when it has not been specified.
     * This will be called when settings_provider::USE_SEB_UPLOAD_CONFIG is not true.
     *
     * @param string $cmid The cmid of for the quiz.
     * @return bool Always true or exception if error occurred
     */
    public static function delete_uploaded_config_file(string $cmid): bool {
        $file = self::get_module_context_sebconfig_file($cmid);

        if (!empty($file)) {
            return $file->delete();
        }

        return false;
    }

    /**
     * Check if the current user can configure SEB.
     *
     * @param \context $context Context to check access in.
     * @return bool
     */
    public static function can_configure_seb(\context $context): bool {
        return has_capability('quizaccess/seb:manage_seb_requiresafeexambrowser', $context);
    }

    /**
     * Check if the current user can use preconfigured templates.
     *
     * @param \context $context Context to check access in.
     * @return bool
     */
    public static function can_use_seb_template(\context $context): bool {
        return has_capability('quizaccess/seb:manage_seb_templateid', $context);
    }

    /**
     * Check if the current user can upload own SEB config file.
     *
     * @param \context $context Context to check access in.
     * @return bool
     */
    public static function can_upload_seb_file(\context $context): bool {
        return has_capability('quizaccess/seb:manage_filemanager_sebconfigfile', $context);
    }

    /**
     * Check if the current user can change Show Safe Exam Browser download button setting.
     *
     * @param \context $context Context to check access in.
     * @return bool
     */
    public static function can_change_seb_showsebdownloadlink(\context $context): bool {
        return has_capability('quizaccess/seb:manage_seb_showsebdownloadlink', $context);
    }

    /**
     * Check if the current user can change Allowed Browser Exam Keys setting.
     *
     * @param \context $context Context to check access in.
     * @return bool
     */
    public static function can_change_seb_allowedbrowserexamkeys(\context $context): bool {
        return has_capability('quizaccess/seb:manage_seb_allowedbrowserexamkeys', $context);
    }

    /**
     * Check if the current user can config SEB manually.
     *
     * @param \context $context Context to check access in.
     * @return bool
     */
    public static function can_configure_manually(\context $context): bool {
        foreach (self::get_seb_config_elements() as $name => $type) {
            if (self::can_manage_seb_config_setting($name, $context)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the current user can manage provided SEB setting.
     *
     * @param string $settingname Name of the setting.
     * @param \context $context Context to check access in.
     * @return bool
     */
    public static function can_manage_seb_config_setting(string $settingname, \context $context): bool {
        $capsttocheck = [];

        foreach (self::get_seb_settings_map() as $type => $settings) {
            $capsttocheck = self::build_config_capabilities_to_check($settingname, $settings);
            if (!empty($capsttocheck)) {
                break;
            }
        }

        foreach ($capsttocheck as $capability) {
            // Capability must exist.
            if (!$capinfo = get_capability_info($capability)) {
                throw new \coding_exception("Capability '{$capability}' was not found! This has to be fixed in code.");
            }
        }

        return has_all_capabilities($capsttocheck, $context);
    }

    /**
     * Helper method to build a list of capabilities to check.
     *
     * @param string $settingname Given setting name to build caps for.
     * @param array $settings A list of settings to go through.
     * @return array
     */
    protected static function build_config_capabilities_to_check(string $settingname, array $settings): array {
        $capsttocheck = [];

        foreach ($settings as $setting => $children) {
            if ($setting == $settingname) {
                $capsttocheck[$setting] = self::build_setting_capability_name($setting);
                break; // Found what we need exit the loop.
            }

            // Recursively check all children.
            $capsttocheck = self::build_config_capabilities_to_check($settingname, $children);
            if (!empty($capsttocheck)) {
                // Matching child found, add the parent capability to the list of caps to check.
                $capsttocheck[$setting] = self::build_setting_capability_name($setting);
                break; // Found what we need exit the loop.
            }
        }

        return $capsttocheck;
    }

    /**
     * Helper method to return a map of all settings.
     *
     * @return array
     */
    public static function get_seb_settings_map(): array {
        return [
            self::USE_SEB_NO => [

            ],
            self::USE_SEB_CONFIG_MANUALLY => [
                'seb_showsebdownloadlink' => [],
                'seb_linkquitseb' => [],
                'seb_userconfirmquit' => [],
                'seb_allowuserquitseb' => [
                    'seb_quitpassword' => []
                ],
                'seb_allowreloadinexam' => [],
                'seb_showsebtaskbar' => [
                    'seb_showreloadbutton' => [],
                    'seb_showtime' => [],
                    'seb_showkeyboardlayout' => [],
                    'seb_showwificontrol' => [],
                ],
                'seb_enableaudiocontrol' => [
                    'seb_muteonstartup' => [],
                ],
                'seb_allowspellchecking' => [],
                'seb_activateurlfiltering' => [
                    'seb_filterembeddedcontent' => [],
                    'seb_expressionsallowed' => [],
                    'seb_regexallowed' => [],
                    'seb_expressionsblocked' => [],
                    'seb_regexblocked' => [],
                ],
            ],
            self::USE_SEB_TEMPLATE => [
                'seb_templateid' => [],
                'seb_showsebdownloadlink' => [],
                'seb_allowuserquitseb' => [
                    'seb_quitpassword' => [],
                ],
            ],
            self::USE_SEB_UPLOAD_CONFIG => [
                'filemanager_sebconfigfile' => [],
                'seb_showsebdownloadlink' => [],
                'seb_allowedbrowserexamkeys' => [],
            ],
            self::USE_SEB_CLIENT_CONFIG => [
                'seb_showsebdownloadlink' => [],
                'seb_allowedbrowserexamkeys' => [],
            ],
        ];
    }

    /**
     * Get allowed settings for provided SEB usage type.
     *
     * @param int $requiresafeexambrowser SEB usage type.
     * @return array
     */
    private static function get_allowed_settings(int $requiresafeexambrowser): array {
        $result = [];
        $map = self::get_seb_settings_map();

        if (!key_exists($requiresafeexambrowser, $map)) {
            return $result;
        }

        return self::build_allowed_settings($map[$requiresafeexambrowser]);
    }

    /**
     * Recursive method to build a list of allowed settings.
     *
     * @param array $settings A list of settings from settings map.
     * @return array
     */
    private static function build_allowed_settings(array $settings): array {
        $result = [];

        foreach ($settings as $name => $children) {
            $result[] = $name;
            foreach ($children as $childname => $child) {
                $result[] = $childname;
                $result = array_merge($result, self::build_allowed_settings($child));
            }
        }

        return $result;
    }

    /**
     * Get the conditions that an element should be hid in the form. Expects matching using 'eq'.
     *
     * Array key is name of 'form element'/'database column (excluding prefix)'.
     * Values are instances of hideif_rule class.
     *
     * @return array List of rules per element.
     */
    public static function get_quiz_hideifs(): array {
        $hideifs = [];

        // We are building rules based on the settings map, that means children will be dependant on parent.
        // In most cases it's all pretty standard.
        // However it could be some specific cases for some fields, which will be overridden later.
        foreach (self::get_seb_settings_map() as $type => $settings) {
            foreach ($settings as $setting => $children) {
                $hideifs[$setting][] = new hideif_rule($setting, 'seb_requiresafeexambrowser', 'noteq', $type);

                foreach ($children as $childname => $child) {
                    $hideifs[$childname][] = new hideif_rule($childname, 'seb_requiresafeexambrowser', 'noteq', $type);
                    $hideifs[$childname][] = new hideif_rule($childname, $setting, 'eq', 0);
                }
            }
        }

        // Specific case for "Enable quitting of SEB". It should available for Manual and Template.
        $hideifs['seb_allowuserquitseb'] = [
            new hideif_rule('seb_allowuserquitseb', 'seb_requiresafeexambrowser', 'eq', self::USE_SEB_NO),
            new hideif_rule('seb_allowuserquitseb', 'seb_requiresafeexambrowser', 'eq', self::USE_SEB_CLIENT_CONFIG),
            new hideif_rule('seb_allowuserquitseb', 'seb_requiresafeexambrowser', 'eq', self::USE_SEB_UPLOAD_CONFIG),
        ];

        // Specific case for "Quit password". It should be available for Manual and Template. As it's parent.
        $hideifs['seb_quitpassword'] = [
            new hideif_rule('seb_quitpassword', 'seb_requiresafeexambrowser', 'eq', self::USE_SEB_NO),
            new hideif_rule('seb_quitpassword', 'seb_requiresafeexambrowser', 'eq', self::USE_SEB_CLIENT_CONFIG),
            new hideif_rule('seb_quitpassword', 'seb_requiresafeexambrowser', 'eq', self::USE_SEB_UPLOAD_CONFIG),
            new hideif_rule('seb_quitpassword', 'seb_allowuserquitseb', 'eq', 0),
        ];

        // Specific case for "Show Safe Exam Browser download button". It should be available for all cases, except No Seb.
        $hideifs['seb_showsebdownloadlink'] = [
            new hideif_rule('seb_showsebdownloadlink', 'seb_requiresafeexambrowser', 'eq', self::USE_SEB_NO)
        ];

        // Specific case for "Allowed Browser Exam Keys". It should be available for Template and Browser config.
        $hideifs['seb_allowedbrowserexamkeys'] = [
            new hideif_rule('seb_allowedbrowserexamkeys', 'seb_requiresafeexambrowser', 'eq', self::USE_SEB_NO),
            new hideif_rule('seb_allowedbrowserexamkeys', 'seb_requiresafeexambrowser', 'eq', self::USE_SEB_CONFIG_MANUALLY),
            new hideif_rule('seb_allowedbrowserexamkeys', 'seb_requiresafeexambrowser', 'eq', self::USE_SEB_TEMPLATE),
        ];

        return $hideifs;
    }

    /**
     * Build a capability name for the provided SEB setting.
     *
     * @param string $settingname Name of the setting.
     * @return string
     */
    public static function build_setting_capability_name(string $settingname): string {
        if (!key_exists($settingname, self::get_seb_config_elements())) {
            throw new \coding_exception('Incorrect SEB quiz setting ' . $settingname);
        }

        return 'quizaccess/seb:manage_' . $settingname;
    }

    /**
     * Check if settings is locked.
     *
     * @param int $quizid Quiz ID.
     * @return bool
     */
    public static function is_seb_settings_locked($quizid): bool {
        if (empty($quizid)) {
            return false;
        }

        return quiz_has_attempts($quizid);
    }

    /**
     * Filter a standard class by prefix.
     *
     * @param stdClass $settings Quiz settings object.
     * @return stdClass Filtered object.
     */
    private static function filter_by_prefix(\stdClass $settings): stdClass {
        $newsettings = new \stdClass();
        foreach ($settings as $name => $setting) {
            // Only add it, if not there.
            if (strpos($name, "seb_") === 0) {
                $newsettings->$name = $setting; // Add new key.
            }
        }
        return $newsettings;
    }

    /**
     * Filter settings based on the setting map. Set value of not allowed settings to null.
     *
     * @param stdClass $settings Quiz settings.
     * @return \stdClass
     */
    private static function filter_by_settings_map(stdClass $settings): stdClass {
        if (!isset($settings->seb_requiresafeexambrowser)) {
            return $settings;
        }

        $newsettings = new \stdClass();
        $newsettings->seb_requiresafeexambrowser = $settings->seb_requiresafeexambrowser;
        $allowedsettings = self::get_allowed_settings((int)$newsettings->seb_requiresafeexambrowser);
        unset($settings->seb_requiresafeexambrowser);

        foreach ($settings as $name => $value) {
            if (!in_array($name, $allowedsettings)) {
                $newsettings->$name = null;
            } else {
                $newsettings->$name = $value;
            }
        }

        return $newsettings;
    }

    /**
     * Filter quiz settings for this plugin only.
     *
     * @param stdClass $settings Quiz settings.
     * @return stdClass Filtered settings.
     */
    public static function filter_plugin_settings(stdClass $settings): stdClass {
        $settings = self::filter_by_prefix($settings);
        $settings = self::filter_by_settings_map($settings);

        return self::strip_all_prefixes($settings);
    }

    /**
     * Strip the seb_ prefix from each setting key.
     *
     * @param \stdClass $settings Object containing settings.
     * @return \stdClass The modified settings object.
     */
    private static function strip_all_prefixes(\stdClass $settings): stdClass {
        $newsettings = new \stdClass();
        foreach ($settings as $name => $setting) {
            $newname = preg_replace("/^seb_/", "", $name);
            $newsettings->$newname = $setting; // Add new key.
        }
        return $newsettings;
    }

    /**
     * Add prefix to string.
     *
     * @param string $name String to add prefix to.
     * @return string String with prefix.
     */
    public static function add_prefix(string $name): string {
        if (strpos($name, 'seb_') !== 0) {
            $name = 'seb_' . $name;
        }
        return $name;
    }
}
