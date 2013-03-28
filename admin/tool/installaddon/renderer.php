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
 * Output rendering for the plugin.
 *
 * @package     tool_installaddon
 * @category    output
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Implements the plugin renderer
 *
 * @copyright 2013 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_installaddon_renderer extends plugin_renderer_base {

    /** @var tool_installaddon_installer */
    protected $installer = null;

    /** @var tool_installaddon_validator */
    protected $validator = null;

    /**
     * Sets the tool_installaddon_installer instance being used.
     *
     * @throws coding_exception if the installer has been already set
     * @param tool_installaddon_installer $installer
     */
    public function set_installer_instance(tool_installaddon_installer $installer) {
        if (is_null($this->installer)) {
            $this->installer = $installer;
        } else {
            throw new coding_exception('Attempting to reset the installer instance.');
        }
    }

    /**
     * Sets the tool_installaddon_validator instance being used.
     *
     * @throws coding_exception if the validator has been already set
     * @param tool_installaddon_validator $validator
     */
    public function set_validator_instance(tool_installaddon_validator $validator) {
        if (is_null($this->validator)) {
            $this->validator = $validator;
        } else {
            throw new coding_exception('Attempting to reset the validator instance.');
        }
    }

    /**
     * Defines the index page layout
     *
     * @return string
     */
    public function index_page() {

        if (is_null($this->installer)) {
            throw new coding_exception('Installer instance has not been set.');
        }

        $permcheckurl = new moodle_url('/admin/tool/installaddon/permcheck.php');
        $this->page->requires->yui_module('moodle-tool_installaddon-permcheck', 'M.tool_installaddon.permcheck.init',
            array(array('permcheckurl' => $permcheckurl->out())));
        $this->page->requires->strings_for_js(
            array('permcheckprogress', 'permcheckresultno', 'permcheckresultyes', 'permcheckerror'), 'tool_installaddon');

        $out = $this->output->header();
        $out .= $this->index_page_heading();
        $out .= $this->index_page_repository();
        $out .= $this->index_page_upload();
        $out .= $this->output->footer();

        return $out;
    }

    /**
     * Defines the validation results page layout
     *
     * @return string
     */
    public function validation_page() {

        if (is_null($this->installer)) {
            throw new coding_exception('Installer instance has not been set.');
        }

        if (is_null($this->validator)) {
            throw new coding_exception('Validator instance has not been set.');
        }

        $out = $this->output->header();
        $out .= $this->validation_page_heading();
        $out .= $this->validation_page_messages();
        $out .= $this->validation_page_continue();
        $out .= $this->output->footer();

        return $out;
    }

    /**
     * Inform the user about invalid remote installation request.
     *
     * @param moodle_url $continueurl
     * @return string
     */
    public function remote_request_invalid_page(moodle_url $continueurl) {

        $out = $this->output->header();
        $out .= $this->output->heading(get_string('installfromrepo', 'tool_installaddon'));
        $out .= $this->output->box(get_string('remoterequestinvalid', 'tool_installaddon'), 'generalbox', 'notice');
        $out .= $this->output->continue_button($continueurl, 'get');
        $out .= $this->output->footer();

        return $out;
    }

    /**
     * Inform the user that such plugin is already installed
     *
     * @param stdClass $data decoded request data
     * @param moodle_url $continueurl
     * @return string
     */
    public function remote_request_alreadyinstalled_page(stdClass $data, moodle_url $continueurl) {

        $out = $this->output->header();
        $out .= $this->output->heading(get_string('installfromrepo', 'tool_installaddon'));
        $out .= $this->output->box(get_string('remoterequestalreadyinstalled', 'tool_installaddon', $data), 'generalbox', 'notice');
        $out .= $this->output->continue_button($continueurl, 'get');
        $out .= $this->output->footer();

        return $out;
    }

    /**
     * Let the user confirm the remote installation request.
     *
     * @param stdClass $data decoded request data
     * @param moodle_url $continueurl
     * @param moodle_url $cancelurl
     * @return string
     */
    public function remote_request_confirm_page(stdClass $data, moodle_url $continueurl, moodle_url $cancelurl) {

        $out = $this->output->header();
        $out .= $this->output->heading(get_string('installfromrepo', 'tool_installaddon'));
        $out .= $this->output->confirm(get_string('remoterequestconfirm', 'tool_installaddon', $data), $continueurl, $cancelurl);
        $out .= $this->output->footer();

        return $out;
    }

    /**
     * Inform the user that the target plugin type location is not writable.
     *
     * @param stdClass $data decoded request data
     * @param string $plugintypepath full path to the plugin type location
     * @param moodle_url $continueurl to repeat the write permission check
     * @param moodle_url $cancelurl to cancel the installation
     * @return string
     */
    public function remote_request_permcheck_page(stdClass $data, $plugintypepath, moodle_url $continueurl, moodle_url $cancelurl) {

        $data->typepath = $plugintypepath;

        $out = $this->output->header();
        $out .= $this->output->heading(get_string('installfromrepo', 'tool_installaddon'));
        $out .= $this->output->confirm(get_string('remoterequestpermcheck', 'tool_installaddon', $data), $continueurl, $cancelurl);
        $out .= $this->output->footer();

        return $out;
    }

    /**
     * Inform the user about pluginfo service call exception
     *
     * This implementation does not actually use the passed exception. Custom renderers might want to
     * display additional data obtained via {@link get_exception_info()}. Also note, this method is called
     * in non-debugging mode only. If debugging is allowed at the site, default exception handler is triggered.
     *
     * @param stdClass $data decoded request data
     * @param tool_installaddon_pluginfo_exception $e thrown exception
     * @param moodle_url $continueurl
     * @return string
     */
    public function remote_request_pluginfo_exception(stdClass $data, tool_installaddon_pluginfo_exception $e, moodle_url $continueurl) {

        $out = $this->output->header();
        $out .= $this->output->heading(get_string('installfromrepo', 'tool_installaddon'));
        $out .= $this->output->box(get_string('remoterequestpluginfoexception', 'tool_installaddon', $data), 'generalbox', 'notice');
        $out .= $this->output->continue_button($continueurl, 'get');
        $out .= $this->output->footer();

        return $out;
    }

    /**
     * Inform the user about the installer exception
     *
     * This implementation does not actually use the passed exception. Custom renderers might want to
     * display additional data obtained via {@link get_exception_info()}. Also note, this method is called
     * in non-debugging mode only. If debugging is allowed at the site, default exception handler is triggered.
     *
     * @param tool_installaddon_installer_exception $e thrown exception
     * @param moodle_url $continueurl
     * @return string
     */
    public function installer_exception(tool_installaddon_installer_exception $e, moodle_url $continueurl) {

        $out = $this->output->header();
        $out .= $this->output->heading(get_string('installfromrepo', 'tool_installaddon'));
        $out .= $this->output->box(get_string('installexception', 'tool_installaddon'), 'generalbox', 'notice');
        $out .= $this->output->continue_button($continueurl, 'get');
        $out .= $this->output->footer();

        return $out;
    }

    // End of the external API /////////////////////////////////////////////////

    /**
     * Renders the index page heading
     *
     * @return string
     */
    protected function index_page_heading() {
        return $this->output->heading(get_string('pluginname', 'tool_installaddon'));
    }

    /**
     * Renders the widget for browsing the add-on repository
     *
     * @return string
     */
    protected function index_page_repository() {

        $url = $this->installer->get_addons_repository_url();

        $out = $this->box(
            $this->output->single_button($url, get_string('installfromrepo', 'tool_installaddon'), 'get').
            $this->output->help_icon('installfromrepo', 'tool_installaddon'),
            'generalbox', 'installfromrepobox'
        );

        return $out;
    }

    /**
     * Renders the widget for uploading the add-on ZIP package
     *
     * @return string
     */
    protected function index_page_upload() {

        $form = $this->installer->get_installfromzip_form();

        ob_start();
        $form->display();
        $out = ob_get_clean();

        $out = $this->box($out, 'generalbox', 'installfromzipbox');

        return $out;
    }

    /**
     * Renders the page title and the overall validation verdict
     *
     * @return string
     */
    protected function validation_page_heading() {

        $heading = $this->output->heading(get_string('validation', 'tool_installaddon'));

        if ($this->validator->get_result()) {
            $status = $this->output->container(
                html_writer::span(get_string('validationresult1', 'tool_installaddon'), 'verdict').
                    $this->output->help_icon('validationresult1', 'tool_installaddon'),
                array('validationresult', 'success')
            );
        } else {
            $status = $this->output->container(
                html_writer::span(get_string('validationresult0', 'tool_installaddon'), 'verdict').
                    $this->output->help_icon('validationresult0', 'tool_installaddon'),
                array('validationresult', 'failure')
            );
        }

        return $heading . $status;
    }

    /**
     * Renders validation log messages.
     *
     * @return string
     */
    protected function validation_page_messages() {

        $validator = $this->validator; // We need this to be able to use their constants.
        $messages = $validator->get_messages();

        if (empty($messages)) {
            return '';
        }

        $table = new html_table();
        $table->attributes['class'] = 'validationmessages generaltable';
        $table->head = array(
            get_string('validationresultstatus', 'tool_installaddon'),
            get_string('validationresultmsg', 'tool_installaddon'),
            get_string('validationresultinfo', 'tool_installaddon')
        );
        $table->colclasses = array('msgstatus', 'msgtext', 'msginfo');

        $stringman = get_string_manager();

        foreach ($messages as $message) {

            if ($message->level === $validator::DEBUG and !debugging()) {
                continue;
            }

            $msgstatus = get_string('validationmsglevel_'.$message->level, 'tool_installaddon');
            $msgtext = $msgtext = s($message->msgcode);
            if (is_null($message->addinfo)) {
                $msginfo = '';
            } else {
                $msginfo = html_writer::tag('pre', s(print_r($message->addinfo, true)));
            }
            $msghelp = '';

            // Replace the message code with the string if it is defined.
            if ($stringman->string_exists('validationmsg_'.$message->msgcode, 'tool_installaddon')) {
                $msgtext = get_string('validationmsg_'.$message->msgcode, 'tool_installaddon');
                // And check for the eventual help, too.
                if ($stringman->string_exists('validationmsg_'.$message->msgcode.'_help', 'tool_installaddon')) {
                    $msghelp = $this->output->help_icon('validationmsg_'.$message->msgcode, 'tool_installaddon');
                }
            }

            // Re-format the message info using a string if it is define.
            if (!is_null($message->addinfo) and $stringman->string_exists('validationmsg_'.$message->msgcode.'_info', 'tool_installaddon')) {
                $msginfo = get_string('validationmsg_'.$message->msgcode.'_info', 'tool_installaddon', $message->addinfo);
            }

            $row = new html_table_row(array($msgstatus, $msgtext.$msghelp, $msginfo));
            $row->attributes['class'] = 'level-'.$message->level.' '.$message->msgcode;

            $table->data[] = $row;
        }

        return html_writer::table($table);
    }

    /**
     * Renders widgets to continue from the validation results page
     *
     * @return string
     */
    protected function validation_page_continue() {

        $conturl = $this->validator->get_continue_url();
        if (is_null($conturl)) {
            $contbutton = '';
        } else {
            $contbutton = $this->output->single_button(
                $conturl, get_string('installaddon', 'tool_installaddon'), 'post',
                array('class' => 'singlebutton continuebutton'));
        }

        $cancelbutton = $this->output->single_button(
            new moodle_url('/admin/tool/installaddon/index.php'), get_string('cancel', 'core'), 'get',
            array('class' => 'singlebutton cancelbutton'));

        return $this->output->container($cancelbutton.$contbutton, 'postvalidationbuttons');
    }
}
