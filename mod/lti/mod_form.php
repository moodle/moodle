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
//
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu.

/**
 * This file defines the main lti configuration form
 *
 * @package mod_lti
 * @copyright  2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright  2009 Universitat Politecnica de Catalunya http://www.upc.edu
 * @author     Marc Alier
 * @author     Jordi Piguillem
 * @author     Nikolas Galanis
 * @author     Chris Scribner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/lti/locallib.php');

class mod_lti_mod_form extends moodleform_mod {

    /** @var int the tool typeid, or 0 if the instance form is being created for a manually configured tool instance.*/
    protected int $typeid;

    /** @var string|null type */
    protected ?string $type;

    /**
     * Constructor.
     *
     * Throws an exception if trying to init the form for a new manual instance of a tool, which is not supported in 4.3 onward.
     *
     * @param \stdClass $current the current form data.
     * @param string $section the section number.
     * @param \stdClass $cm the course module object.
     * @param \stdClass $course the course object.
     * @throws moodle_exception if trying to init the form for the creation of a manual instance, which is no longer supported.
     */
    public function __construct($current, $section, $cm, $course) {

        // Setup some of the pieces used to control display in the form definition() method.
        // Type ID parameter being passed when adding an preconfigured tool from activity chooser.
        $this->typeid = optional_param('typeid', 0, PARAM_INT);
        $this->type = optional_param('type', null, PARAM_ALPHA);

        // Only permit construction if the form deals with editing an existing instance (current->id not empty), or creating an
        // instance from a preconfigured tool type ($this->typeid not empty). Make an exception for callers, such as core_completion
        // which aren't instantiating the form with the expected data, by checking whether the page has been set up, which is the
        // case for normal uses.
        global $PAGE;
        if ($PAGE->has_set_url() && str_contains($PAGE->url, '/course/modedit.php')) {
            if (empty($this->typeid) && empty($current->id)) {
                throw new moodle_exception('lti:addmanualinstanceprohibitederror', 'mod_lti');
            }
        }

        parent::__construct($current, $section, $cm, $course);
    }

    /**
     * Defines the form for legacy instances. Here tool config is frozen because the manual configuration method is deprecated.
     *
     * @param array $instancetypes the array of options for the legacy 'preconfigured tools' select.
     * @return void
     */
    protected function legacy_instance_form_definition(array $instancetypes): void {
        global $OUTPUT;

        // The legacy form handles instances which are either entirely manually configured (current->typeid = 0), or which are
        // manually configured and have been domain-matched to a preconfigured tool (current->typeid != 0).
        $manualinstance = empty($this->current->typeid);
        $matchestoolnotavailabletocourse = !$manualinstance;
        $typeid = $manualinstance ? '0' : $this->current->typeid;

        // Since 'mod/lti:addmanualinstance' capability is deprecated, determining which users may have had access to the certain
        // form fields (the manual config fields) isn't straightforward. Users without 'mod/lti:addmanualinstance' would have only
        // been permitted to edit the basic instance fields (name, etc.), so care must be taken not to display the config fields to
        // these users. Users who can add/edit course tools (mod/lti:addcoursetool) are able to view tool information anyway, via
        // the tool definitions, so this capability is used as a replacement, to control access to these tool config fields.
        $canviewmanualconfig = has_capability('mod/lti:addcoursetool', $this->context);
        $showtypes = has_capability('mod/lti:addpreconfiguredinstance', $this->context);

        if ($manualinstance && !$canviewmanualconfig) {
            // If you cannot add a manual instance and this is already a manual instance, then remove the 'types' selector.
            $showtypes = false;
        }

        $mform =& $this->_form;

        // Show the deprecation notice, regardless of whether the user can view the tool configuration details or not.
        // They will still see locked privacy fields and should be informed as to why that is.
        $mform->addElement('html', $OUTPUT->notification(
            get_string('editmanualinstancedeprecationwarning', 'mod_lti', get_docs_url('External_tool')),
            \core\output\notification::NOTIFY_WARNING, false)
        );

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('html', "<div data-attribute='dynamic-import' hidden aria-hidden='true' role='alert'></div>");
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('basicltiname', 'lti'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the optional "intro" and "introformat" pair of fields.
        $this->standard_intro_elements(get_string('basicltiintro', 'lti'));
        $mform->setAdvanced('introeditor');

        // Display the label to the right of the checkbox so it looks better & matches rest of the form.
        if ($mform->elementExists('showdescription')) {
            $coursedesc = $mform->getElement('showdescription');
            if (!empty($coursedesc)) {
                $coursedesc->setText(' ' . $coursedesc->getLabel());
                $coursedesc->setLabel('&nbsp');
            }
        }

        $mform->setAdvanced('showdescription');

        $mform->addElement('checkbox', 'showtitlelaunch', get_string('display_name', 'lti'));
        $mform->setAdvanced('showtitlelaunch');
        $mform->addHelpButton('showtitlelaunch', 'display_name', 'lti');

        $mform->addElement('checkbox', 'showdescriptionlaunch', get_string('display_description', 'lti'));
        $mform->setAdvanced('showdescriptionlaunch');
        $mform->addHelpButton('showdescriptionlaunch', 'display_description', 'lti');

        if ($showtypes) {
            if ($manualinstance) {
                // Legacy, manually configured instances: only freeze the element (not hardFreeze) so that disabledIf() still works.
                // The data in the select is restricted so that only the current value is deemed valid, preventing DOM-edit changes,
                // which are possible with frozen elements.
                $tooltypes = $mform->addElement('select', 'typeid', get_string('external_tool_type', 'lti'));
                $mform->addHelpButton('typeid', 'external_tool_type', 'lti');
                $manualinstanceoption = $instancetypes[0]; // The 'Automatic, based on tool URL' option.
                $tooltypes->addOption($manualinstanceoption->name, 0, []);
                $mform->freeze('typeid');
            } else if ($matchestoolnotavailabletocourse) {
                // Legacy instances domain-matched to site tools: use a hidden field for typeid and a static visual element when
                // displaying these instances so that the string value of typeid is still visible when the element is frozen.
                // This gets around the fact that a frozen select without a selected option will display nothing.
                $mform->addElement('hidden', 'typeid', $typeid);
                $mform->setType('typeid', PARAM_INT);

                $manualinstanceoption = $instancetypes[0]; // The 'Automatic, based on tool URL' option.
                $mform->addElement('static', 'typeiddisplayonly', get_string('external_tool_type', 'lti'),
                    $manualinstanceoption->name);
            }
        } else {
            // Need to submit these still, but hidden to avoid instructor modification.
            $mform->addElement('hidden', 'typeid', $typeid);
            $mform->setType('typeid', PARAM_INT);
        }

        // Disable the content selection button unconditionally. Freeze/hardFreeze is unsuitable for buttons.
        $mform->addElement('button', 'selectcontent', get_string('selectcontent', 'lti'), ['disabled' => 'disabled']);
        $mform->disabledIf('selectcontent', 'typeid', 'eq', $typeid);

        if ($canviewmanualconfig) {
            $mform->addElement('text', 'toolurl', get_string('launch_url', 'lti'), ['size' => '64']);
            $mform->setType('toolurl', PARAM_URL);
            $mform->addHelpButton('toolurl', 'launch_url', 'lti');

            $mform->addElement('text', 'securetoolurl', get_string('secure_launch_url', 'lti'), ['size' => '64']);
            $mform->setType('securetoolurl', PARAM_URL);
            $mform->setAdvanced('securetoolurl');
            $mform->addHelpButton('securetoolurl', 'secure_launch_url', 'lti');
        } else {
            // Need to submit these still, but hidden to avoid instructor modification.
            $mform->addElement('hidden', 'toolurl', '', ['id' => 'id_toolurl']);
            $mform->setType('toolurl', PARAM_URL);
            $mform->addElement('hidden', 'securetoolurl', '', ['id' => 'id_securetoolurl']);
            $mform->setType('securetoolurl', PARAM_URL);
        }

        $mform->addElement('hidden', 'lineitemresourceid', '', ['id' => 'id_lineitemresourceid']);
        $mform->setType('lineitemresourceid', PARAM_TEXT);

        $mform->addElement('hidden', 'lineitemtag', '', ['id' => 'id_lineitemtag']);
        $mform->setType('lineitemtag', PARAM_TEXT);

        $mform->addElement('hidden', 'lineitemsubreviewurl', '', ['id' => 'id_lineitemsubreviewurl']);
        $mform->setType('lineitemsubreviewurl', PARAM_URL);

        $mform->addElement('hidden', 'lineitemsubreviewparams', '', ['id' => 'id_lineitemsubreviewparams']);
        $mform->setType('lineitemsubreviewparams', PARAM_TEXT);

        $launchoptions = [
            LTI_LAUNCH_CONTAINER_DEFAULT => get_string('default', 'lti'),
            LTI_LAUNCH_CONTAINER_EMBED => get_string('embed', 'lti'),
            LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS => get_string('embed_no_blocks', 'lti'),
            LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW => get_string('existing_window', 'lti'),
            LTI_LAUNCH_CONTAINER_WINDOW => get_string('new_window', 'lti')
        ];
        $mform->addElement('select', 'launchcontainer', get_string('launchinpopup', 'lti'), $launchoptions);
        $mform->addHelpButton('launchcontainer', 'launchinpopup', 'lti');
        $mform->setAdvanced('launchcontainer');

        if ($canviewmanualconfig) {
            $mform->addElement('text', 'resourcekey', get_string('resourcekey', 'lti'));
            $mform->setType('resourcekey', PARAM_TEXT);
            $mform->setAdvanced('resourcekey');
            $mform->addHelpButton('resourcekey', 'resourcekey', 'lti');
            $mform->setForceLtr('resourcekey');

            $mform->addElement('passwordunmask', 'password', get_string('password', 'lti'));
            $mform->setType('password', PARAM_TEXT);
            $mform->setAdvanced('password');
            $mform->addHelpButton('password', 'password', 'lti');

            $mform->addElement('textarea', 'instructorcustomparameters', get_string('custom', 'lti'), ['rows' => 4, 'cols' => 60]);
            $mform->setType('instructorcustomparameters', PARAM_TEXT);
            $mform->setAdvanced('instructorcustomparameters');
            $mform->addHelpButton('instructorcustomparameters', 'custom', 'lti');
            $mform->setForceLtr('instructorcustomparameters');

            $mform->addElement('text', 'icon', get_string('icon_url', 'lti'), ['size' => '64']);
            $mform->setType('icon', PARAM_URL);
            $mform->setAdvanced('icon');
            $mform->addHelpButton('icon', 'icon_url', 'lti');

            $mform->addElement('text', 'secureicon', get_string('secure_icon_url', 'lti'), ['size' => '64']);
            $mform->setType('secureicon', PARAM_URL);
            $mform->setAdvanced('secureicon');
            $mform->addHelpButton('secureicon', 'secure_icon_url', 'lti');
        } else {
            // Need to submit these still, but hidden to avoid instructor modification.
            $mform->addElement('hidden', 'resourcekey', '', ['id' => 'id_resourcekey']);
            $mform->setType('resourcekey', PARAM_TEXT);
            $mform->addElement('hidden', 'password', '', ['id' => 'id_password']);
            $mform->setType('password', PARAM_TEXT);
            $mform->addElement('hidden', 'instructorcustomparameters', '', ['id' => 'id_instructorcustomparameters']);
            $mform->setType('instructorcustomparameters', PARAM_TEXT);
            $mform->addElement('hidden', 'icon', '', ['id' => 'id_icon']);
            $mform->setType('icon', PARAM_URL);
            $mform->addElement('hidden', 'secureicon', '', ['id' => 'id_secureicon']);
            $mform->setType('secureicon', PARAM_URL);
        }

        // Add privacy preferences fieldset where users choose whether to send their data.
        $mform->addElement('header', 'privacy', get_string('privacy', 'lti'));

        $mform->addElement('advcheckbox', 'instructorchoicesendname', get_string('share_name', 'lti'));
        $mform->addHelpButton('instructorchoicesendname', 'share_name', 'lti');

        $mform->addElement('advcheckbox', 'instructorchoicesendemailaddr', get_string('share_email', 'lti'));
        $mform->addHelpButton('instructorchoicesendemailaddr', 'share_email', 'lti');

        $mform->addElement('advcheckbox', 'instructorchoiceacceptgrades', get_string('accept_grades', 'lti'));
        $mform->addHelpButton('instructorchoiceacceptgrades', 'accept_grades', 'lti');

        // Add standard course module grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();
        $mform->setAdvanced('cmidnumber');

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();

        $mform->hardFreeze([
            'toolurl',
            'securetoolurl',
            'launchcontainer',
            'instructorcustomparameters',
            'icon',
            'secureicon',
            'instructorchoicesendname',
            'instructorchoicesendemailaddr',
            'instructorchoiceacceptgrades'
        ]);
    }

    public function definition() {
        global $PAGE, $OUTPUT, $COURSE;

        if ($this->type) {
            component_callback("ltisource_$this->type", 'add_instance_hook');
        }

        // Determine whether this tool instance is a manually configure instance (now deprecated).
        $manualinstance = empty($this->current->typeid) && empty($this->typeid);

        // Determine whether this tool instance is using a domain-matched site tool which is not visible at the course level.
        // In such a case, the instance has a typeid (the site tool) and toolurl (the url used to domain match the site tool) set,
        // and the type still exists (is not deleted).
        $instancetypes = lti_get_types_for_add_instance();
        $matchestoolnotavailabletocourse = false;
        if (!$manualinstance && !empty($this->current->toolurl)) {
            if (lti_get_type_config($this->current->typeid)) {
                $matchestoolnotavailabletocourse = !in_array($this->current->typeid, array_keys($instancetypes));
            }
        }

        // Display the legacy form, presenting a read-only view of the configuration for unsupported (since 4.3) instances, which:
        // - Are manually configured instances (no longer supported. course tools should be configured and used instead).
        // - Are domain-matched to a hidden site level tool (no longer supported. to be replaced by URL-based course tool creation)
        // Instances based on preconfigured tools and which are not domain matched as above, are still valid and will be shown using
        // the non-legacy form.
        if ($manualinstance || $matchestoolnotavailabletocourse) {
            $this->legacy_instance_form_definition($instancetypes);
            return;
        }

        $tooltypeid = $this->current->typeid ?? $this->typeid;
        $tooltype = lti_get_type($tooltypeid);

        // Store the id of the tool type should it be linked to a tool proxy, to aid in disabling certain form elements.
        $toolproxytypeid = $tooltype->toolproxyid ? $tooltypeid : '';

        $issitetooltype = $tooltype->course == get_site()->id;

        $mform =& $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('html', "<div data-attribute='dynamic-import' hidden aria-hidden='true' role='alert'></div>");
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // For tools supporting content selection, add the 'Select content button'.
        $config = lti_get_type_config($tooltypeid);
        $supportscontentitemselection = !empty($config['contentitem']);

        if ($supportscontentitemselection) {
            $contentitemurl = new moodle_url('/mod/lti/contentitem.php');
            $contentbuttonattributes = [
                'data-contentitemurl' => $contentitemurl->out(false),
            ];

            // If this is an instance, was it created based on content selection in a prior-edit (need to infer since not stored).
            $iscontentitem = !empty($this->current->id)
                && (!empty($this->current->toolurl) || !empty($this->current->instructorcustomparameters)
                || !empty($this->current->secureicon) || !empty($this->current->icon));

            $selectcontentindicatorinner = $iscontentitem ?
                $OUTPUT->pix_icon('i/valid', get_string('contentselected', 'mod_lti'), 'moodle', ['class' => 'me-1'])
                . get_string('contentselected', 'mod_lti') : '';
            $selectcontentindicator = html_writer::div($selectcontentindicatorinner, '',
                ['aria-role' => 'status', 'id' => 'id_selectcontentindicator']);
            $selectcontentgrp = [
                $mform->createElement('button', 'selectcontent', get_string('selectcontent', 'mod_lti'), $contentbuttonattributes,
                    ['customclassoverride' => 'btn-secondary']),
                $mform->createElement('html', $selectcontentindicator),
            ];
            $mform->addGroup($selectcontentgrp, 'selectcontentgroup', get_string('content'), ' ', false);
        }

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('basicltiname', 'lti'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'server');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'server');

        // Show activity name when launched only applies to embedded type launches.
        if (in_array($config['launchcontainer'], [LTI_LAUNCH_CONTAINER_EMBED, LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS])) {
            $mform->addElement('checkbox', 'showtitlelaunch', get_string('display_name', 'lti'));
            $mform->setDefault('showtitlelaunch', true);
            $mform->addHelpButton('showtitlelaunch', 'display_name', 'lti');
        } else {
            // Include in the form anyway, so we retain the setting value in case the tool launch container is changed back.
            $mform->addElement('hidden', 'showtitlelaunch');
            $mform->setType('showtitlelaunch', PARAM_BOOL);
        }

        // Adding the optional "intro" and "introformat" pair of fields.
        $this->standard_intro_elements(get_string('basicltiintro', 'lti'));

        // Display the label to the right of the checkbox so it looks better & matches rest of the form.
        if ($mform->elementExists('showdescription')) {
            $coursedesc = $mform->getElement('showdescription');
            if (!empty($coursedesc)) {
                $coursedesc->setText(' ' . $coursedesc->getLabel());
                $coursedesc->setLabel('&nbsp');
            }
        }

        // Show activity description when launched only applies to embedded type launches.
        if (in_array($config['launchcontainer'], [LTI_LAUNCH_CONTAINER_EMBED, LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS])) {
            $mform->addElement('checkbox', 'showdescriptionlaunch', get_string('display_description', 'lti'));
            $mform->addHelpButton('showdescriptionlaunch', 'display_description', 'lti');
        } else {
            // Include in the form anyway, so we retain the setting value in case the tool launch container is changed back.
            $mform->addElement('hidden', 'showdescriptionlaunch');
            $mform->setType('showdescriptionlaunch', PARAM_BOOL);
        }

        // Tool settings.
        $mform->addElement('hidden', 'typeid', $tooltypeid, ['id' => 'hidden_typeid']);
        $mform->setType('typeid', PARAM_INT);
        if (!empty($config['contentitem'])) {
            $mform->addElement('hidden', 'contentitem', 1);
            $mform->setType('contentitem', PARAM_INT);
        }

        // Included to support deep linking return, but hidden to avoid instructor modification.
        $mform->addElement('hidden', 'toolurl', '', ['id' => 'id_toolurl']);
        $mform->setType('toolurl', PARAM_URL);
        $mform->addElement('hidden', 'securetoolurl', '', ['id' => 'id_securetoolurl']);
        $mform->setType('securetoolurl', PARAM_URL);

        $mform->addElement('hidden', 'urlmatchedtypeid', '', ['id' => 'id_urlmatchedtypeid']);
        $mform->setType('urlmatchedtypeid', PARAM_INT);

        $mform->addElement('hidden', 'lineitemresourceid', '', ['id' => 'id_lineitemresourceid']);
        $mform->setType('lineitemresourceid', PARAM_TEXT);

        $mform->addElement('hidden', 'lineitemtag', '', ['id' => 'id_lineitemtag']);
        $mform->setType('lineitemtag', PARAM_TEXT);

        $mform->addElement('hidden', 'lineitemsubreviewurl', '', ['id' => 'id_lineitemsubreviewurl']);
        $mform->setType('lineitemsubreviewurl', PARAM_URL);

        $mform->addElement('hidden', 'lineitemsubreviewparams', '', ['id' => 'id_lineitemsubreviewparams']);
        $mform->setType('lineitemsubreviewparams', PARAM_TEXT);

        // Launch container is set to 'LTI_LAUNCH_CONTAINER_DEFAULT', meaning it'll delegate to the tool's configuration.
        // Existing instances using values other than this can continue to use their existing value but cannot change it.
        $mform->addElement('hidden', 'launchcontainer', LTI_LAUNCH_CONTAINER_DEFAULT);
        $mform->setType('launchcontainer', PARAM_INT);

        // Included to support deep linking return, but hidden to avoid instructor modification.
        $mform->addElement('hidden', 'resourcekey', '', ['id' => 'id_resourcekey']);
        $mform->setType('resourcekey', PARAM_TEXT);
        $mform->addElement('hidden', 'password', '', ['id' => 'id_password']);
        $mform->setType('password', PARAM_TEXT);
        $mform->addElement('textarea', 'instructorcustomparameters', get_string('custom', 'lti'),
            ['rows' => 4, 'cols' => 60]);
        $mform->setType('instructorcustomparameters', PARAM_TEXT);
        $mform->setAdvanced('instructorcustomparameters');
        $mform->addHelpButton('instructorcustomparameters', 'custom', 'lti');
        $mform->setForceLtr('instructorcustomparameters');
        $mform->addElement('hidden', 'icon', '', ['id' => 'id_icon']);
        $mform->setType('icon', PARAM_URL);
        $mform->addElement('hidden', 'secureicon', '', ['id' => 'id_secureicon']);
        $mform->setType('secureicon', PARAM_URL);

        // Add standard course module grading elements, and show them if the tool type + instance config permits it.
        if (!empty($config['acceptgrades']) && in_array($config['acceptgrades'], [LTI_SETTING_ALWAYS, LTI_SETTING_DELEGATE])) {
            $elementnamesbeforegrading = $this->_form->_elementIndex;
            $this->standard_grading_coursemodule_elements();
            $elementnamesaftergrading = $this->_form->_elementIndex;

            // For all 'real' elements (not hidden or header) added as part of the standard grading elements, add a hideIf rule
            // making the element dependent on the 'accept grades from the tool' checkbox (instructorchoiceacceptgrades).
            $diff = array_diff($elementnamesaftergrading, $elementnamesbeforegrading);
            $diff = array_filter($diff, fn($key) => !in_array($this->_form->_elements[$key]->_type, ['hidden', 'header']));
            foreach ($diff as $gradeelementname => $gradeelementindex) {
                $mform->hideIf($gradeelementname, 'instructorchoiceacceptgrades', 'eq', 0);
            }

            // Extend the grade section with the 'accept grades from the tool' checkbox, allowing per-instance overrides of that
            // value according to the following rules:
            // - Site tools with 'acceptgrades' set to 'ALWAYS' do not permit overrides at the instance level; the checkbox is
            // omitted in such cases.
            // - Site tools with 'acceptgrades' set to 'DELEGATE' result in a checkbox that is defaulted to unchecked but which
            // permits overrides to 'yes/checked'.
            // - Course tools with 'acceptgrades' set to 'ALWAYS' result in a checkbox that is defaulted to checked but which
            // permits overrides to 'no/unchecked'.
            // - Course tools with 'acceptgrades' set to 'DELEGATE' result in a checkbox that is defaulted to unchecked but which
            // permits overrides to 'yes/checked'.
            if (($issitetooltype && $config['acceptgrades'] == LTI_SETTING_DELEGATE) || !$issitetooltype) {
                $mform->insertElementBefore(
                    $mform->createElement(
                        'advcheckbox',
                        'instructorchoiceacceptgrades',
                        get_string('accept_grades_from_tool', 'mod_lti', $tooltype->name)
                    ),
                    array_keys($diff)[0]
                );
                $acceptgradesdefault = !$issitetooltype && $config['acceptgrades'] == LTI_SETTING_ALWAYS ? '1' : '0';
                $mform->setDefault('instructorchoiceacceptgrades', $acceptgradesdefault);
                $mform->disabledIf('instructorchoiceacceptgrades', 'typeid', 'in', [$toolproxytypeid]); // LTI 2 only.
            }
        }

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();
        $mform->setAdvanced('cmidnumber');

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();

        if ($supportscontentitemselection) {
            $PAGE->requires->js_call_amd('mod_lti/mod_form', 'init', [$COURSE->id]);
        }
    }

    /**
     * Sets the current values handled by services in case of update.
     *
     * @param object $defaultvalues default values to populate the form with.
     */
    public function set_data($defaultvalues) {
        $services = lti_get_services();
        if (is_object($defaultvalues)) {
            foreach ($services as $service) {
                $service->set_instance_form_values( $defaultvalues );
            }
        }
        parent::set_data($defaultvalues);
    }
}
