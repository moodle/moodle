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
 * This file contains the main logic for the block_panopto package.
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2016 /With contributions from Spenser Jones (sjones@ambrose.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/lib/panopto_data.php');
require_once(dirname(__FILE__) . '/../../lib/accesslib.php');

/**
 * Base class for the Panopto block for Moodle.
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2015
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_panopto extends block_base {

    /**
     * ID of the div element containing the contents of the Panopto block.
     */
    const CONTENTID = 'block_panopto_content';

    /**
     * Name of the Panopto block. Should match the block's directory name on the server.
     *
     * @var string $blockname the name of the current block.
     */
    public $blockname = 'panopto';

    /**
     * Set system properties of plugin.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_panopto');
    }

    /**
     * Block has global config (display "Settings" link on blocks admin page).
     */
    public function has_config() {
        return true;
    }

    /**
     * Block has per-instance config (display edit icon in block header).
     */
    public function instance_allow_config() {
        return true;
    }

    /**
     * Save per-instance config in custom table instead of mdl_block_instance configdata column.
     *
     * @param array $data the data being set on Panopto
     * @param bool $nolongerused depcrecated variable
     */
    public function instance_config_save($data, $nolongerused = false) {

        // Add roles mapping.
        $publisherroles = (isset($data->publisher)) ? $data->publisher : [];
        $creatorroles = (isset($data->creator)) ? $data->creator : [];

        // Get the current role mappings set for the current course from the db.
        $mappings = \panopto_data::get_course_role_mappings($this->page->course->id);

        $oldcreators = array_diff($mappings['creator'], $creatorroles);
        $oldpublishers = array_diff($mappings['publisher'], $publisherroles);

        // Make sure the old unassigned roles get unset.
        \panopto_data::unset_course_role_permissions(
            $this->page->course->id,
            $oldpublishers,
            $oldcreators
        );

        \panopto_data::set_course_role_permissions(
            $this->page->course->id,
            $publisherroles,
            $creatorroles
        );

        if (!empty($data->course)) {

            // Only perform this chunk if we are remapping to a new folder.
            $panoptodata = new \panopto_data($this->page->course->id);

            if (strcasecmp($panoptodata->sessiongroupid, $data->course) != 0) {
                $oldsessionid = null;
                if (!empty($panoptodata->sessiongroupid)) {
                    $oldsessionid = $panoptodata->sessiongroupid;
                    $panoptodata->unprovision_course();
                }
                // Manually overwrite the sessiongroupid on this Panopto_Data instance,
                // so we can test provision the attempted new mapping.
                // If the provision fails do not allow it. Provision could fail if the user attempts to provision a personal folder.
                $panoptodata->sessiongroupid = $data->course;

                $provisioninginfo = $panoptodata->get_provisioning_info();
                $provisioneddata = $panoptodata->provision_course($provisioninginfo, false);
                if (isset($provisioneddata->Id) && !empty($provisioneddata->Id)) {
                    $panoptodata->update_folder_external_id_with_provider();
                    \panopto_data::set_panopto_course_id($this->page->course->id, $data->course);
                } else {
                    $panoptodata->sessiongroupid = $oldsessionid;
                    $provisioninginfo = $panoptodata->get_provisioning_info();
                    $provisioneddata = $panoptodata->provision_course($provisioninginfo, false);
                }
            }
        }
    }

    /**
     * Cron function to provision all valid courses at once.
     */
    public function cron() {
        return true;
    }

    /**
     * Generate HTML for block contents.
     */
    public function get_content() {
        global $COURSE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;

        // Initialize $this->content->text to an empty string here to avoid trying to append to it before
        // It has been initialized and throwing a warning. Bug 33163.
        $this->content->text = '';
        $this->content->footer = '';

        $params = ['id' => self::CONTENTID, 'courseid' => $COURSE->id];

        $this->page->requires->yui_module('moodle-block_panopto-asyncload',
                                    'M.block_panopto.asyncload.init',
                                    [$params],
                                    null,
                                    true);

        $this->content->text = html_writer::tag('div', "<font id='loading_text'>" .
            get_string('fetching_content', 'block_panopto') . '</font>', $params);

        $this->content->text .= '<script type="text/javascript">' .
                    // Function to pop up Panopto live note taker.
                    'function panopto_launchNotes(url) {' .
                        // Open empty notes window, then POST SSO form to it.
                        'var notesWindow = window.open("", "PanoptoNotes", ' .
                            '"width=500,height=800,resizable=1,scrollbars=0,status=0,location=0");' .
                        'document.SSO.action = url;' .
                        'document.SSO.target = "PanoptoNotes";' .
                        'document.SSO.submit();' .

                        // Ensure the new window is brought to the front of the z-order.
                        'notesWindow.focus();' .
                    '}' .

                    'function panopto_startSSO(linkElem) {' .
                        'document.SSO.action = linkElem.href;' .
                        'document.SSO.target = "_blank";' .
                        'document.SSO.submit();' .

                        // Cancel default link navigation.
                        'return false;' .
                    '}' .

                    'function panopto_toggleHiddenLectures() {' .
                        'var showAllToggle = document.getElementById("showAllToggle");' .
                        'var hiddenLecturesDiv = document.getElementById("hiddenLecturesDiv");' .

                        'if (hiddenLecturesDiv.style.display == "block") {' .
                            'hiddenLecturesDiv.style.display = "none";' .
                            'showAllToggle.innerHTML = "' . get_string('show_all', 'block_panopto') . '";' .
                        '} else {' .
                        'hiddenLecturesDiv.style.display = "block";' .
                        'showAllToggle.innerHTML = "' . get_string('show_less', 'block_panopto') . '";' .
                    '}' .
                '}' .
            '</script>';

        return $this->content;
    }

    /**
     * Which page types this block may appear on
     * @return array
     */
    public function applicable_formats() {
        // Since block is dealing with courses and enrollment's the only possible.
        // place where Panopto block can be used is the course.
        return ['course-view' => true];
    }

    /**
     * Allow more than one instance of the block on a page
     *
     * @return boolean
     */
    public function instance_allow_multiple() {
        return false;
    }
}
// End of block_panopto.php.
