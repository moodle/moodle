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
 * ltixformslib.php - library of classes for creating lti forms in Moodle, based on PEAR QuickForms.
 *
 *
 * @package   core_ltix
 * @copyright 2023 TII
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
use core_ltix\ltix_helper;
global $CFG;

/**
 * Adds all the elements to a form for the lti module.
 */
function attach_lti_elements($mform, $context, $_instance, $current) {
    global $COURSE, $CFG, $DB, $OUTPUT, $PAGE;

    $mform->addElement('header', 'externaltool', get_string('externaltool', 'ltix'));

    $mform->addElement('checkbox', 'showtitlelaunch', get_string('display_name', 'core_ltix'));
    $mform->setAdvanced('showtitlelaunch');
    $mform->setDefault('showtitlelaunch', true);
    $mform->addHelpButton('showtitlelaunch', 'display_name', 'assign');

    $mform->addElement('checkbox', 'showdescriptionlaunch', get_string('display_description', 'core_ltix'));
    $mform->setAdvanced('showdescriptionlaunch');
    $mform->addHelpButton('showdescriptionlaunch', 'display_description', 'core_ltix');

    //Show type
    if ($type = optional_param('type', false, PARAM_ALPHA)) {
        component_callback("ltisource_$type", 'add_instance_hook');
    }

    // Type ID parameter being passed when adding an preconfigured tool from activity chooser.
    $typeid = optional_param('typeid', false, PARAM_INT);

    $showoptions = has_capability('moodle/ltix:addmanualinstance', $context);
    // Show configuration details only if not preset (when new) or user has the capabilities to do so (when editing).
    if ($_instance) {
        $showtypes = has_capability('moodle/ltix:addpreconfiguredinstance', $context);
        if (!$showoptions && $current->typeid == 0) {
            // If you cannot add a manual instance and this is already a manual instance, then
            // remove the 'types' selector.
            $showtypes = false;
        }
    } else {
        $showtypes = !$typeid;
    }

    // Tool settings.
    $toolproxy = array();
    // Array of tool type IDs that don't support ContentItemSelectionRequest.
    $noncontentitemtypes = [];

    if ($showtypes) {
        $tooltypes = $mform->addElement('select', 'typeid', get_string('external_tool_type', 'ltix'));
        if ($typeid) {
            $mform->getElement('typeid')->setValue($typeid);
        }
        $mform->addHelpButton('typeid', 'external_tool_type', 'ltix');

        foreach (ltix_helper::lti_get_types_for_add_instance() as $id => $type) {
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
                $config = ltix_helper::lti_get_type_config($id);
                if (!empty($config['contentitem'])) {
                    $attributes['data-contentitem'] = 1;
                    $attributes['data-id'] = $id;
                } else {
                    $noncontentitemtypes[] = $id;
                }
            }
            $tooltypes->addOption($type->name, $id, $attributes);
        }
    } else {
        $mform->addElement('hidden', 'typeid', $typeid);
        $mform->setType('typeid', PARAM_INT);
        if ($typeid) {
            $config = ltix_helper::lti_get_type_config($typeid);
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
        if (!$typeid || empty(ltix_helper::lti_get_type_config($typeid)['contentitem'])) {
            $contentbuttonattributes['disabled'] = 'disabled';
        }
    }
    $contentbuttonlabel = get_string('selectcontent', 'ltix');
    $contentbutton = $mform->addElement('button', 'selectcontent', $contentbuttonlabel, $contentbuttonattributes);
    // Disable select content button if the selected tool doesn't support content item or it's set to Automatic.
    if ($showtypes) {
        $allnoncontentitemtypes = $noncontentitemtypes;
        $allnoncontentitemtypes[] = '0'; // Add option value for "Automatic, based on tool URL".
        $mform->disabledIf('selectcontent', 'typeid', 'in', $allnoncontentitemtypes);
    }

    if ($showoptions) {
        $mform->addElement('text', 'toolurl', get_string('launch_url', 'ltix'), array('size' => '64'));
        $mform->setType('toolurl', PARAM_URL);
        $mform->addHelpButton('toolurl', 'launch_url', 'core_ltix');
        $mform->hideIf('toolurl', 'typeid', 'in', $noncontentitemtypes);

        $mform->addElement('text', 'securetoolurl', get_string('secure_launch_url', 'core_ltix'), array('size' => '64'));
        $mform->setType('securetoolurl', PARAM_URL);
        $mform->setAdvanced('securetoolurl');
        $mform->addHelpButton('securetoolurl', 'secure_launch_url', 'core_ltix');
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
    $launchoptions[LTI_LAUNCH_CONTAINER_DEFAULT] = get_string('default', 'core_ltix');
    $launchoptions[LTI_LAUNCH_CONTAINER_EMBED] = get_string('embed', 'core_ltix');
    $launchoptions[LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS] = get_string('embed_no_blocks', 'core_ltix');
    $launchoptions[LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW] = get_string('existing_window', 'core_ltix');
    $launchoptions[LTI_LAUNCH_CONTAINER_WINDOW] = get_string('new_window', 'core_ltix');

    $mform->addElement('select', 'launchcontainer', get_string('launchinpopup', 'core_ltix'), $launchoptions);
    $mform->setDefault('launchcontainer', LTI_LAUNCH_CONTAINER_DEFAULT);
    $mform->addHelpButton('launchcontainer', 'launchinpopup', 'core_ltix');
    $mform->setAdvanced('launchcontainer');

    if ($showoptions) {
        $mform->addElement('text', 'resourcekey', get_string('resourcekey', 'core_ltix'));
        $mform->setType('resourcekey', PARAM_TEXT);
        $mform->setAdvanced('resourcekey');
        $mform->addHelpButton('resourcekey', 'resourcekey', 'core_ltix');
        $mform->setForceLtr('resourcekey');
        $mform->hideIf('resourcekey', 'typeid', 'in', $noncontentitemtypes);

        $mform->addElement('passwordunmask', 'password', get_string('password', 'core_ltix'));
        $mform->setType('password', PARAM_TEXT);
        $mform->setAdvanced('password');
        $mform->addHelpButton('password', 'password', 'core_ltix');
        $mform->hideIf('password', 'typeid', 'in', $noncontentitemtypes);

        $mform->addElement('textarea', 'instructorcustomparameters', get_string('custom', 'core_ltix'), array('rows' => 4, 'cols' => 60));
        $mform->setType('instructorcustomparameters', PARAM_TEXT);
        $mform->setAdvanced('instructorcustomparameters');
        $mform->addHelpButton('instructorcustomparameters', 'custom', 'core_ltix');
        $mform->setForceLtr('instructorcustomparameters');

        $mform->addElement('text', 'icon', get_string('icon_url', 'ltix'), array('size' => '64'));
        $mform->setType('icon', PARAM_URL);
        $mform->setAdvanced('icon');
        $mform->addHelpButton('icon', 'icon_url', 'core_ltix');
        $mform->hideIf('icon', 'typeid', 'in', $noncontentitemtypes);

        $mform->addElement('text', 'secureicon', get_string('secure_icon_url', 'core_ltix'), array('size' => '64'));
        $mform->setType('secureicon', PARAM_URL);
        $mform->setAdvanced('secureicon');
        $mform->addHelpButton('secureicon', 'secure_icon_url', 'core_ltix');
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
    $mform->addElement('header', 'privacy', get_string('privacy', 'ltix'));

    $mform->addElement('advcheckbox', 'instructorchoicesendname', get_string('share_name', 'core_ltix'));
    $mform->setDefault('instructorchoicesendname', '1');
    $mform->addHelpButton('instructorchoicesendname', 'share_name', 'core_ltix');
    $mform->disabledIf('instructorchoicesendname', 'typeid', 'in', $toolproxy);

    $mform->addElement('advcheckbox', 'instructorchoicesendemailaddr', get_string('share_email', 'core_ltix'));
    $mform->setDefault('instructorchoicesendemailaddr', '1');
    $mform->addHelpButton('instructorchoicesendemailaddr', 'share_email', 'core_ltix');
    $mform->disabledIf('instructorchoicesendemailaddr', 'typeid', 'in', $toolproxy);

    $mform->addElement('advcheckbox', 'instructorchoiceacceptgrades', get_string('accept_grades', 'core_ltix'));
    $mform->setDefault('instructorchoiceacceptgrades', '0');
    $mform->addHelpButton('instructorchoiceacceptgrades', 'accept_grades', 'core_ltix');
    $mform->disabledIf('instructorchoiceacceptgrades', 'typeid', 'in', $toolproxy);

    // Add standard course module grading elements.
    //$this->standard_grading_coursemodule_elements();

    // Add standard elements, common to all modules.
    //$this->standard_coursemodule_elements();
    /*$mform->addElement('text', 'cmidnumber', get_string('idnumbermod'));
    $mform->setType('cmidnumber', PARAM_RAW);
    $mform->addHelpButton('cmidnumber', 'idnumbermod');*/
    $mform->setAdvanced('cmidnumber');

    // Add standard buttons, common to all modules.
    //$this->add_action_buttons();

    $editurl = new moodle_url('/ltix/instructor_edit_tool_type.php',
        array('sesskey' => sesskey(), 'course' => $COURSE->id));
    $ajaxurl = new moodle_url('/ltix/ajax.php');

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
        'name' => 'core_ltix_edit',
        'fullpath' => '/ltix/mod_form.js',
        'requires' => array('base', 'io', 'querystring-stringify-simple', 'node', 'event', 'json-parse'),
        'strings' => array(
            array('addtype', 'core_ltix'),
            array('edittype', 'core_ltix'),
            array('deletetype', 'core_ltix'),
            array('delete_confirmation', 'core_ltix'),
            array('cannot_edit', 'core_ltix'),
            array('cannot_delete', 'core_ltix'),
            array('global_tool_types', 'core_ltix'),
            array('course_tool_types', 'core_ltix'),
            array('using_tool_configuration', 'ltix'),
            array('using_tool_cartridge', 'core_ltix'),
            array('domain_mismatch', 'core_ltix'),
            array('custom_config', 'core_ltix'),
            array('tool_config_not_found', 'core_ltix'),
            array('tooltypeadded', 'core_ltix'),
            array('tooltypedeleted', 'core_ltix'),
            array('tooltypenotdeleted', 'core_ltix'),
            array('tooltypeupdated', 'core_ltix'),
            array('forced_help', 'core_ltix')
        ),
    );

    if (!empty($typeid)) {
        $mform->setAdvanced('typeid');
        $mform->setAdvanced('toolurl');
    }

    $PAGE->requires->js_init_call('M.mod_lti.editor.init', array(json_encode($jsinfo)), true, $module);
}




