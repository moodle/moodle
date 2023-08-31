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

    /** @var int|null the typeid or null if the instance form is being created for a manually configured tool instance.*/
    protected ?int $typeid;

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
        $this->typeid = optional_param('typeid', null, PARAM_INT);
        $this->type = optional_param('type', null, PARAM_ALPHA);

        // Only permit construction if the form deals with editing an existing instance (current->id not empty), or creating an
        // instance from a preconfigured tool type ($this->typeid not empty).
        global $PAGE;
        if ($PAGE->has_set_url() && str_contains($PAGE->url, '/course/modedit.php')) {
            if (empty($this->typeid) && empty($current->id)) {
                throw new moodle_exception('lti:addmanualinstanceprohibitederror', 'mod_lti');
            }
        }

        parent::__construct($current, $section, $cm, $course);
    }

    public function definition() {
        global $PAGE, $OUTPUT, $COURSE;

        if ($this->type) {
            component_callback("ltisource_$this->type", 'add_instance_hook');
        }

        // Since 'mod/lti:addmanualinstance' capability is deprecated, determining which users may have had access to the certain
        // form fields (the manual config fields) isn't straightforward. Users without 'mod/lti:addmanualinstance' would have only
        // been permitted to edit the basic instance fields (name, etc.), so care must be taken not to display the config fields to
        // these users. Users who can add/edit course tools (mod/lti:addcoursetool) are able to view tool information anyway, via
        // the tool definitions, so this capability is used as a replacement, to control access to these tool config fields.
        $canviewmanualconfig = has_capability('mod/lti:addcoursetool', $this->context);
        $manualinstance = empty($this->current->typeid) && empty($this->typeid);

        // Show configuration details only if not preset (when new) or user has the capabilities to do so (when editing).
        if ($this->_instance) {
            $showtypes = has_capability('mod/lti:addpreconfiguredinstance', $this->context);
            if ($manualinstance && !$canviewmanualconfig) {
                // If you cannot add a manual instance and this is already a manual instance, then
                // remove the 'types' selector.
                $showtypes = false;
            }
        } else {
            $showtypes = !$this->typeid;
        }

        // Determine whether this tool instance is using a tool which is not visible at the course level, but which does exist.
        // This indicates that the instance has either:
        // - Been configured manually, and was domain matched to a site tool in the past.
        // - Been configured using a preconfigured tool that is now no longer visible in the course.
        // In the case of the domain matched tool, tool URL will be set.
        $instancetypes = lti_get_types_for_add_instance();
        $matchestoolnotavailabletocourse = false;
        if (!$manualinstance && !empty($this->current->toolurl) && lti_get_type_config($this->current->typeid)) {
            // Type was found, so it's likely been domain matched.
            $matchestoolnotavailabletocourse = !in_array($this->current->typeid, array_keys($instancetypes));
        }

        $mform =& $this->_form;

        // Show the deprecation notice when displaying any manually configured instance, regardless of whether the user can view
        // the tool configuration details or not. They will still see locked privacy fields and should be told why that is.
        if ($manualinstance || $matchestoolnotavailabletocourse) {
            $mform->addElement('html', $OUTPUT->notification(
                get_string('editmanualinstancedeprecationwarning', 'mod_lti', get_docs_url('External_tool')),
                \core\output\notification::NOTIFY_WARNING, false));
        }

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('html', "<div data-attribute='dynamic-import' hidden aria-hidden='true' role='alert'></div>");
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('basicltiname', 'lti'), array('size' => '64'));
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
        $mform->setDefault('showtitlelaunch', true);
        $mform->addHelpButton('showtitlelaunch', 'display_name', 'lti');

        $mform->addElement('checkbox', 'showdescriptionlaunch', get_string('display_description', 'lti'));
        $mform->setAdvanced('showdescriptionlaunch');
        $mform->addHelpButton('showdescriptionlaunch', 'display_description', 'lti');

        // Tool settings.
        $toolproxy = array();
        // Array of tool type IDs that don't support ContentItemSelectionRequest.
        $noncontentitemtypes = [];

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
                $mform->addElement('hidden', 'typeid', $this->current->typeid);
                $mform->setType('typeid', PARAM_INT);

                $manualinstanceoption = $instancetypes[0]; // The 'Automatic, based on tool URL' option.
                $mform->addElement('static', 'typeiddisplayonly', get_string('external_tool_type', 'lti'),
                    $manualinstanceoption->name);
            } else {
                // To prevent the use of manually configured instances, existing instances which are using a preconfigured tool will
                // not display the option "Automatic, based on tool URL" in the preconfigured tools select. This prevents switching
                // from an instance configured using a preconfigured tool to an instance that is manually configured.
                unset($instancetypes[0]);

                $tooltypes = $mform->addElement('select', 'typeid', get_string('external_tool_type', 'lti'));
                if ($this->typeid) {
                    $mform->getElement('typeid')->setValue($this->typeid);
                }
                $mform->addHelpButton('typeid', 'external_tool_type', 'lti');

                foreach ($instancetypes as $id => $type) {
                    if (!empty($type->toolproxyid)) {
                        $toolproxy[] = $type->id;
                        $attributes = array('globalTool' => 1, 'toolproxy' => 1);
                        $enabledcapabilities = explode("\n", $type->enabledcapability);
                        if (!in_array('Result.autocreate', $enabledcapabilities) ||
                            in_array('BasicOutcome.url', $enabledcapabilities)) {
                            $attributes['nogrades'] = 1;
                        }
                        if (!in_array('Person.name.full', $enabledcapabilities) &&
                            !in_array('Person.name.family', $enabledcapabilities) &&
                            !in_array('Person.name.given', $enabledcapabilities)) {
                            $attributes['noname'] = 1;
                        }
                        if (!in_array('Person.email.primary', $enabledcapabilities)) {
                            $attributes['noemail'] = 1;
                        }
                    } else if ($type->course == $COURSE->id) {
                        $attributes = array('editable' => 1, 'courseTool' => 1, 'domain' => $type->tooldomain);
                    } else if ($id != 0) {
                        $attributes = array('globalTool' => 1, 'domain' => $type->tooldomain);
                    } else {
                        $attributes = array();
                    }

                    if ($id) {
                        $config = lti_get_type_config($id);
                        if (!empty($config['contentitem'])) {
                            $attributes['data-contentitem'] = 1;
                            $attributes['data-id'] = $id;
                        } else {
                            $noncontentitemtypes[] = $id;
                        }
                    }
                    $tooltypes->addOption($type->name, $id, $attributes);
                }
            }
        } else {
            $mform->addElement('hidden', 'typeid', $this->typeid);
            $mform->setType('typeid', PARAM_INT);
            if ($this->typeid) {
                $config = lti_get_type_config($this->typeid);
                if (!empty($config['contentitem'])) {
                    $mform->addElement('hidden', 'contentitem', 1);
                    $mform->setType('contentitem', PARAM_INT);
                }
            }
        }

        // Add button that launches the content-item selection dialogue.
        // Set contentitem URL.
        $contentitemurl = new moodle_url('/mod/lti/contentitem.php');
        $contentbuttonattributes = [
            'data-contentitemurl' => $contentitemurl->out(false)
        ];
        if (!$showtypes) {
            if (!$this->typeid || empty(lti_get_type_config($this->typeid)['contentitem'])) {
                $contentbuttonattributes['disabled'] = 'disabled';
            }
        }
        $contentbuttonlabel = get_string('selectcontent', 'lti');
        $contentbutton = $mform->addElement('button', 'selectcontent', $contentbuttonlabel, $contentbuttonattributes);
        // Disable select content button if the selected tool doesn't support content item or it's set to Automatic.
        if ($showtypes) {
            $allnoncontentitemtypes = $noncontentitemtypes;
            $allnoncontentitemtypes[] = '0'; // Add option value for "Automatic, based on tool URL".
            $mform->disabledIf('selectcontent', 'typeid', 'in', $allnoncontentitemtypes);

            // Always disable select content for legacy tool instances domain-matched to site tools.
            if ($matchestoolnotavailabletocourse) {
                $mform->disabledIf('selectcontent', 'typeid', 'in', [$this->current->typeid]);
            }
        }

        if ($canviewmanualconfig) {
            $mform->addElement('text', 'toolurl', get_string('launch_url', 'lti'), array('size' => '64'));
            $mform->setType('toolurl', PARAM_URL);
            $mform->addHelpButton('toolurl', 'launch_url', 'lti');
            $mform->hideIf('toolurl', 'typeid', 'in', $noncontentitemtypes);

            $mform->addElement('text', 'securetoolurl', get_string('secure_launch_url', 'lti'), array('size' => '64'));
            $mform->setType('securetoolurl', PARAM_URL);
            $mform->setAdvanced('securetoolurl');
            $mform->addHelpButton('securetoolurl', 'secure_launch_url', 'lti');
            $mform->hideIf('securetoolurl', 'typeid', 'in', $noncontentitemtypes);
        } else {
            // We still need those on page to support deep linking return, but hidden to avoid instructor modification.
            $mform->addElement('hidden', 'toolurl', '', array('id' => 'id_toolurl'));
            $mform->setType('toolurl', PARAM_URL);
            $mform->addElement('hidden', 'securetoolurl', '', array('id' => 'id_securetoolurl'));
            $mform->setType('securetoolurl', PARAM_URL);
        }

        $mform->addElement('hidden', 'urlmatchedtypeid', '', array('id' => 'id_urlmatchedtypeid'));
        $mform->setType('urlmatchedtypeid', PARAM_INT);

        $mform->addElement('hidden', 'lineitemresourceid', '', array( 'id' => 'id_lineitemresourceid' ));
        $mform->setType('lineitemresourceid', PARAM_TEXT);

        $mform->addElement('hidden', 'lineitemtag', '', array( 'id' => 'id_lineitemtag'));
        $mform->setType('lineitemtag', PARAM_TEXT);

        $mform->addElement('hidden', 'lineitemsubreviewurl', '', array( 'id' => 'id_lineitemsubreviewurl'));
        $mform->setType('lineitemsubreviewurl', PARAM_URL);

        $mform->addElement('hidden', 'lineitemsubreviewparams', '', array( 'id' => 'id_lineitemsubreviewparams'));
        $mform->setType('lineitemsubreviewparams', PARAM_TEXT);

        $launchoptions = array();
        $launchoptions[LTI_LAUNCH_CONTAINER_DEFAULT] = get_string('default', 'lti');
        $launchoptions[LTI_LAUNCH_CONTAINER_EMBED] = get_string('embed', 'lti');
        $launchoptions[LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS] = get_string('embed_no_blocks', 'lti');
        $launchoptions[LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW] = get_string('existing_window', 'lti');
        $launchoptions[LTI_LAUNCH_CONTAINER_WINDOW] = get_string('new_window', 'lti');

        $mform->addElement('select', 'launchcontainer', get_string('launchinpopup', 'lti'), $launchoptions);
        $mform->setDefault('launchcontainer', LTI_LAUNCH_CONTAINER_DEFAULT);
        $mform->addHelpButton('launchcontainer', 'launchinpopup', 'lti');
        $mform->setAdvanced('launchcontainer');

        if ($canviewmanualconfig) {
            $mform->addElement('text', 'resourcekey', get_string('resourcekey', 'lti'));
            $mform->setType('resourcekey', PARAM_TEXT);
            $mform->setAdvanced('resourcekey');
            $mform->addHelpButton('resourcekey', 'resourcekey', 'lti');
            $mform->setForceLtr('resourcekey');
            $mform->hideIf('resourcekey', 'typeid', 'in', $noncontentitemtypes);

            $mform->addElement('passwordunmask', 'password', get_string('password', 'lti'));
            $mform->setType('password', PARAM_TEXT);
            $mform->setAdvanced('password');
            $mform->addHelpButton('password', 'password', 'lti');
            $mform->hideIf('password', 'typeid', 'in', $noncontentitemtypes);

            $mform->addElement('textarea', 'instructorcustomparameters', get_string('custom', 'lti'), array('rows' => 4, 'cols' => 60));
            $mform->setType('instructorcustomparameters', PARAM_TEXT);
            $mform->setAdvanced('instructorcustomparameters');
            $mform->addHelpButton('instructorcustomparameters', 'custom', 'lti');
            $mform->setForceLtr('instructorcustomparameters');

            $mform->addElement('text', 'icon', get_string('icon_url', 'lti'), array('size' => '64'));
            $mform->setType('icon', PARAM_URL);
            $mform->setAdvanced('icon');
            $mform->addHelpButton('icon', 'icon_url', 'lti');
            $mform->hideIf('icon', 'typeid', 'in', $noncontentitemtypes);

            $mform->addElement('text', 'secureicon', get_string('secure_icon_url', 'lti'), array('size' => '64'));
            $mform->setType('secureicon', PARAM_URL);
            $mform->setAdvanced('secureicon');
            $mform->addHelpButton('secureicon', 'secure_icon_url', 'lti');
            $mform->hideIf('secureicon', 'typeid', 'in', $noncontentitemtypes);
        } else {
            // Keep those in the form to allow deep linking.
            $mform->addElement('hidden', 'resourcekey', '', array('id' => 'id_resourcekey'));
            $mform->setType('resourcekey', PARAM_TEXT);
            $mform->addElement('hidden', 'password', '', array('id' => 'id_password'));
            $mform->setType('password', PARAM_TEXT);
            $mform->addElement('hidden', 'instructorcustomparameters', '', array('id' => 'id_instructorcustomparameters'));
            $mform->setType('instructorcustomparameters', PARAM_TEXT);
            $mform->addElement('hidden', 'icon', '', array('id' => 'id_icon'));
            $mform->setType('icon', PARAM_URL);
            $mform->addElement('hidden', 'secureicon', '', array('id' => 'id_secureicon'));
            $mform->setType('secureicon', PARAM_URL);
        }

        // Add privacy preferences fieldset where users choose whether to send their data.
        $mform->addElement('header', 'privacy', get_string('privacy', 'lti'));

        $mform->addElement('advcheckbox', 'instructorchoicesendname', get_string('share_name', 'lti'));
        $mform->setDefault('instructorchoicesendname', '1');
        $mform->addHelpButton('instructorchoicesendname', 'share_name', 'lti');
        $mform->disabledIf('instructorchoicesendname', 'typeid', 'in', $toolproxy);

        $mform->addElement('advcheckbox', 'instructorchoicesendemailaddr', get_string('share_email', 'lti'));
        $mform->setDefault('instructorchoicesendemailaddr', '1');
        $mform->addHelpButton('instructorchoicesendemailaddr', 'share_email', 'lti');
        $mform->disabledIf('instructorchoicesendemailaddr', 'typeid', 'in', $toolproxy);

        $mform->addElement('advcheckbox', 'instructorchoiceacceptgrades', get_string('accept_grades', 'lti'));
        $mform->setDefault('instructorchoiceacceptgrades', '0');
        $mform->addHelpButton('instructorchoiceacceptgrades', 'accept_grades', 'lti');
        $mform->disabledIf('instructorchoiceacceptgrades', 'typeid', 'in', $toolproxy);

        // Add standard course module grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();
        $mform->setAdvanced('cmidnumber');

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();

        $editurl = new moodle_url('/mod/lti/instructor_edit_tool_type.php',
                array('sesskey' => sesskey(), 'course' => $COURSE->id));
        $ajaxurl = new moodle_url('/mod/lti/ajax.php');

        if (!empty($this->typeid)) {
            $mform->setAdvanced('typeid');
            $mform->setAdvanced('toolurl');
        }

        if ($manualinstance || $matchestoolnotavailabletocourse) {
            $mform->hardFreeze([
                'toolurl',
                'securetoolurl',
                'launchcontainer',
                'resourcekey',
                'password',
                'instructorcustomparameters',
                'icon',
                'secureicon',
                'instructorchoicesendname',
                'instructorchoicesendemailaddr',
                'instructorchoiceacceptgrades'
            ]);
        } else {
            // All these icon uses are incorrect. LTI JS needs updating to use AMD modules and templates so it can use
            // the mustache pix helper - until then LTI will have inconsistent icons.
            $jsinfo = (object)array(
                'edit_icon_url' => (string)$OUTPUT->image_url('t/edit'),
                'add_icon_url' => (string)$OUTPUT->image_url('t/add'),
                'delete_icon_url' => (string)$OUTPUT->image_url('t/delete'),
                'green_check_icon_url' => (string)$OUTPUT->image_url('i/valid'),
                'warning_icon_url' => (string)$OUTPUT->image_url('warning', 'lti'),
                'instructor_tool_type_edit_url' => $editurl->out(false),
                'ajax_url' => $ajaxurl->out(true),
                'courseId' => $COURSE->id
            );

            $module = array(
                'name' => 'mod_lti_edit',
                'fullpath' => '/mod/lti/mod_form.js',
                'requires' => array('base', 'io', 'querystring-stringify-simple', 'node', 'event', 'json-parse'),
                'strings' => array(
                    array('addtype', 'lti'),
                    array('edittype', 'lti'),
                    array('deletetype', 'lti'),
                    array('delete_confirmation', 'lti'),
                    array('cannot_edit', 'lti'),
                    array('cannot_delete', 'lti'),
                    array('global_tool_types', 'lti'),
                    array('course_tool_types', 'lti'),
                    array('using_tool_configuration', 'lti'),
                    array('using_tool_cartridge', 'lti'),
                    array('domain_mismatch', 'lti'),
                    array('custom_config', 'lti'),
                    array('tool_config_not_found', 'lti'),
                    array('tooltypeadded', 'lti'),
                    array('tooltypedeleted', 'lti'),
                    array('tooltypenotdeleted', 'lti'),
                    array('tooltypeupdated', 'lti'),
                    array('forced_help', 'lti')
                ),
            );
            $PAGE->requires->js_init_call('M.mod_lti.editor.init', array(json_encode($jsinfo)), true, $module);
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
