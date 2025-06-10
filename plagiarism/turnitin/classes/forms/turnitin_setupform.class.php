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
 * Plugin setup form for plagiarism_turnitin component
 *
 * @package   plagiarism_turnitin
 * @copyright 2018 Turnitin
 * @author    David Winn <dwinn@turnitin.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/plagiarism/turnitin/lib.php');
require_once($CFG->libdir."/formslib.php");

class turnitin_setupform extends moodleform {

    // Define the form.
    public function definition() {
        global $DB, $CFG;

        $config = plagiarism_plugin_turnitin::plagiarism_turnitin_admin_config();

        $mform = $this->_form;

        $mform->disable_form_change_checker();

        $mform->addElement('header', 'config', get_string('turnitinconfig', 'plagiarism_turnitin'));
        $mform->addElement('html', get_string('tiiexplain', 'plagiarism_turnitin').'</br></br>');

        // Loop through all modules that support Plagiarism.
        $mods = array_keys(core_component::get_plugin_list('mod'));
        foreach ($mods as $mod) {
            if (plugin_supports('mod', $mod, FEATURE_PLAGIARISM)) {
                $mform->addElement('advcheckbox',
                    'plagiarism_turnitin_mod_'.$mod,
                    get_string('useturnitin_mod', 'plagiarism_turnitin', ucfirst($mod)),
                    '',
                    null,
                    array(0, 1)
                );
            }
        }

        $mform->addElement('header', 'plagiarism_turnitinconfig', get_string('tiiaccountconfig', 'plagiarism_turnitin'));
        $mform->setExpanded('plagiarism_turnitinconfig');

        $mform->addElement('text', 'plagiarism_turnitin_accountid', get_string('turnitinaccountid', 'plagiarism_turnitin'));
        $mform->setType('plagiarism_turnitin_accountid', PARAM_TEXT);

        $mform->addElement('passwordunmask', 'plagiarism_turnitin_secretkey', get_string('turnitinsecretkey', 'plagiarism_turnitin'));

        $options = array(
            'https://api.turnitin.com' => 'https://api.turnitin.com',
            'https://api.turnitinuk.com' => 'https://api.turnitinuk.com',
            'https://sandbox.turnitin.com' => 'https://sandbox.turnitin.com'
        );

        // Set $CFG->turnitinqa and add URLs to $CFG->turnitinqaurls array in config.php file for testing other environments.
        if (!empty($CFG->turnitinqa)) {
            foreach ($CFG->turnitinqaurls as $url) {
                $options[$url] = $url;
            }
        }

        $mform->addElement('select', 'plagiarism_turnitin_apiurl', get_string('turnitinapiurl', 'plagiarism_turnitin'), $options);

        $mform->addElement('button', 'connection_test', get_string("connecttest", 'plagiarism_turnitin'));

        $mform->addElement('header', 'plagiarism_debugginglogs', get_string('tiidebugginglogs', 'plagiarism_turnitin'));
        $mform->setExpanded('plagiarism_debugginglogs');

        $ynoptions = array(0 => get_string('no'), 1 => get_string('yes'));
        $diagnosticoptions = array(
            0 => get_string('diagnosticoptions_0', 'plagiarism_turnitin'),
            1 => get_string('diagnosticoptions_1', 'plagiarism_turnitin'),
            2 => get_string('diagnosticoptions_2', 'plagiarism_turnitin')
        );

        // Debugging and logging settings.
        $mform->addElement('select', 'plagiarism_turnitin_enablediagnostic', get_string('turnitindiagnostic', 'plagiarism_turnitin'), $diagnosticoptions);
        $mform->addElement('static', 'plagiarism_turnitin_enablediagnostic_desc', null, get_string('turnitindiagnostic_desc', 'plagiarism_turnitin'));

        $mform->addElement('header', 'plagiarism_accountsettings', get_string('tiiaccountsettings', 'plagiarism_turnitin'));
        $mform->setExpanded('plagiarism_accountsettings');

        $mform->addElement('html', '<div class="tii_checkagainstnote">'.get_string('tiiaccountsettings_desc', 'plagiarism_turnitin').'</div>');

        // Turnitin account settings.
        $mform->addElement('select', 'plagiarism_turnitin_usegrademark', get_string('turnitinusegrademark', 'plagiarism_turnitin'), $ynoptions);
        $mform->addElement('static', 'plagiarism_turnitin_usegrademark_desc', null, get_string('turnitinusegrademark_desc', 'plagiarism_turnitin'));
        $mform->setDefault('plagiarism_turnitin_usegrademark', 1);

        $mform->addElement('select', 'plagiarism_turnitin_enablepeermark', get_string('turnitinenablepeermark', 'plagiarism_turnitin'), $ynoptions);
        $mform->addElement('static', 'plagiarism_turnitin_enablepeermark_desc', null, get_string('turnitinenablepeermark_desc', 'plagiarism_turnitin'));
        $mform->setDefault('plagiarism_turnitin_enablepeermark', 1);

        $mform->addElement('select', 'plagiarism_turnitin_useanon', get_string('turnitinuseanon', 'plagiarism_turnitin'), $ynoptions);
        $mform->addElement('static', 'plagiarism_turnitin_useanon_desc', null, get_string('turnitinuseanon_desc', 'plagiarism_turnitin'));
        $mform->setDefault('plagiarism_turnitin_useanon', 0);

        $mform->addElement('select', 'plagiarism_turnitin_transmatch', get_string('transmatch', 'plagiarism_turnitin'), $ynoptions);
        $mform->addElement('static', 'plagiarism_turnitin_transmatch_desc', null, get_string('transmatch_desc', 'plagiarism_turnitin'));
        $mform->setDefault('plagiarism_turnitin_transmatch', 0);

        $repositoryoptions = array(
            PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_STANDARD => get_string('repositoryoptions_0', 'plagiarism_turnitin'),
            PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_EXPANDED => get_string('repositoryoptions_1', 'plagiarism_turnitin'),
            PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_STANDARD => get_string('repositoryoptions_2', 'plagiarism_turnitin'),
            PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_NO => get_string('repositoryoptions_3', 'plagiarism_turnitin'),
            PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_INSTITUTIONAL => get_string('repositoryoptions_4', 'plagiarism_turnitin')
        );

        $mform->addElement('select', 'plagiarism_turnitin_repositoryoption', get_string('turnitinrepositoryoptions', 'plagiarism_turnitin'), $repositoryoptions);
        $mform->addElement('static', 'plagiarism_turnitin_repositoryoption_desc', null, get_string('turnitinrepositoryoptions_desc', 'plagiarism_turnitin'));
        $mform->addHelpButton('plagiarism_turnitin_repositoryoption', 'turnitinrepositoryoptions', 'plagiarism_turnitin');
        $mform->setDefault('plagiarism_turnitin_repositoryoption', 0);

        // Miscellaneous settings.
        $mform->addElement('header', 'plagiarism_miscsettings', get_string('tiimiscsettings', 'plagiarism_turnitin'));
        $mform->setExpanded('plagiarism_miscsettings');

        $mform->addElement('textarea', 'plagiarism_turnitin_agreement', get_string("pp_agreement", "plagiarism_turnitin"), 'wrap="virtual" rows="10" cols="50"');
        $mform->addElement('static', 'plagiarism_turnitin_agreement_desc', null, get_string('pp_agreement_desc', 'plagiarism_turnitin'));

        // Student data privacy settings.
        $mform->addElement('header', 'plagiarism_privacy', get_string('studentdataprivacy', 'plagiarism_turnitin'));
        $mform->setExpanded('plagiarism_privacy');

        if ($DB->count_records('plagiarism_turnitin_users') > 0 AND isset($config->plagiarism_turnitin_enablepseudo)) {
            $enablepseudooptions = ($config->plagiarism_turnitin_enablepseudo == 1) ? array(1 => get_string('yes')) : array(0 => get_string('no'));
        } else if ($DB->count_records('plagiarism_turnitin_users') > 0) {
            $enablepseudooptions = array( 0 => get_string('no', 'plagiarism_turnitin'));
        } else {
            $enablepseudooptions = $ynoptions;
        }

        $mform->addElement('select', 'plagiarism_turnitin_enablepseudo', get_string('enablepseudo', 'plagiarism_turnitin'), $enablepseudooptions);
        $mform->addElement('static', 'plagiarism_turnitin_enablepseudo_desc', null, get_string('enablepseudo_desc', 'plagiarism_turnitin'));
        $mform->setDefault('plagiarism_turnitin_enablepseudo', 0);

        if (!empty($config->plagiarism_turnitin_enablepseudo)) {
            $mform->addElement('text', 'plagiarism_turnitin_pseudofirstname', get_string('pseudofirstname', 'plagiarism_turnitin'), array('class' => 'studentprivacy'));
            $mform->addElement('static', 'plagiarism_turnitin_pseudofirstname_desc', null,
                get_string('pseudofirstname_desc', 'plagiarism_turnitin'), array('class' => 'studentprivacy'));
            $mform->setType('plagiarism_turnitin_pseudofirstname', PARAM_TEXT);
            $mform->setDefault('plagiarism_turnitin_pseudofirstname', PLAGIARISM_TURNITIN_DEFAULT_PSEUDO_FIRSTNAME);

            $lnoptions = array( 0 => get_string('user') );

            $userprofiles = $DB->get_records('user_info_field');
            foreach ($userprofiles as $profile) {
                $lnoptions[$profile->id] = get_string('profilefield', 'admin').': '.$profile->name;
            }

            $mform->addElement('select', 'plagiarism_turnitin_pseudolastname', get_string('pseudolastname', 'plagiarism_turnitin'), $lnoptions, array('class' => 'studentprivacy'));
            $mform->addElement('static', 'plagiarism_turnitin_pseudolastname_desc', null,
                get_string('pseudolastname_desc', 'plagiarism_turnitin'), array('class' => 'studentprivacy'));
            $mform->setType('plagiarism_turnitin_pseudolastname', PARAM_TEXT);
            $mform->setDefault('plagiarism_turnitin_pseudolastname', 0);

            $mform->addElement('select', 'plagiarism_turnitin_lastnamegen', get_string('pseudolastnamegen', 'plagiarism_turnitin'), $ynoptions, array('class' => 'studentprivacy'));
            $mform->addElement('static', 'plagiarism_turnitin_lastnamegen_desc', null,
                get_string('pseudolastnamegen_desc', 'plagiarism_turnitin'), array('class' => 'studentprivacy'));
            $mform->setType('plagiarism_turnitin_lastnamegen', PARAM_TEXT);
            $mform->setDefault('plagiarism_turnitin_lastnamegen', 0);

            $mform->addElement('text', 'plagiarism_turnitin_pseudosalt', get_string('pseudoemailsalt', 'plagiarism_turnitin'), array('class' => 'studentprivacy'));
            $mform->addElement('static', 'plagiarism_turnitin_pseudosalt_desc', null,
                get_string('pseudoemailsalt_desc', 'plagiarism_turnitin'), array('class' => 'studentprivacy'));
            $mform->setType('plagiarism_turnitin_pseudosalt', PARAM_TEXT);

            $mform->addElement('text', 'plagiarism_turnitin_pseudoemaildomain', get_string('pseudoemaildomain', 'plagiarism_turnitin'), array('class' => 'studentprivacy'));
            $mform->addElement('static', 'plagiarism_turnitin_pseudoemaildomain_desc', null,
                get_string('pseudoemaildomain_desc', 'plagiarism_turnitin'), array('class' => 'studentprivacy'));
            $mform->setType('plagiarism_turnitin_pseudoemaildomain', PARAM_TEXT);
        }

        $this->add_action_buttons();
    }

    /**
     * Display the form, saving the contents of the output buffer overriding Moodle's
     * display function that prints to screen when called
     *
     * @return the form as an object to print to screen at our convenience
     */
    public function display() {
        ob_start();
        parent::display();
        $form = ob_get_contents();
        ob_end_clean();

        return $form;
    }

    /**
     * Save the plugin config data
     */
    public function save($data) {
        global $CFG;

        // Save whether the plugin is enabled for individual modules.
        $mods = array_keys(core_component::get_plugin_list('mod'));
        $pluginenabled = 0;
        foreach ($mods as $mod) {
            if (plugin_supports('mod', $mod, FEATURE_PLAGIARISM)) {
                $property = "plagiarism_turnitin_mod_" . $mod;
                ${ "plagiarism_turnitin_mod_" . "$mod" } = (!empty($data->$property)) ? $data->$property : 0;
                set_config('plagiarism_turnitin_mod_'.$mod, ${ "plagiarism_turnitin_mod_" . "$mod" }, 'plagiarism_turnitin');
                if (${ "plagiarism_turnitin_mod_" . "$mod" }) {
                    $pluginenabled = 1;
                }
            }
        }

        set_config('enabled', $pluginenabled, 'plagiarism_turnitin');
        // TODO: Remove turnitin_use completely when support for 3.8 is dropped.
        if ($CFG->branch < 39) {
            set_config('turnitin_use', $pluginenabled, 'plagiarism');
        }

        $properties = array("accountid", "secretkey", "apiurl", "enablediagnostic", "usegrademark", "enablepeermark",
            "useanon", "transmatch", "repositoryoption", "agreement", "enablepseudo", "pseudofirstname",
            "pseudolastname", "lastnamegen", "pseudosalt", "pseudoemaildomain");

        foreach ($properties as $property) {
            plagiarism_plugin_turnitin::plagiarism_set_config($data, "plagiarism_turnitin_".$property);
        }
    }
}
