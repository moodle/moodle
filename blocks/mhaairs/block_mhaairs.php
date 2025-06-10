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
 * This file contains the mhaairs block class.
 *
 * @package     block_mhaairs
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die();
global $CFG;
require_once($CFG->libdir.'/blocklib.php');
require_once($CFG->dirroot.'/blocks/mhaairs/lib.php');
require_once($CFG->dirroot.'/blocks/mhaairs/block_mhaairs_util.php');

/**
 * Class for the mhaairs-moodle integration.
 *
 * @package     block_mhaairs
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @copyright   2013-2014 Moodlerooms inc.
 * @author      Teresa Hardy <thardy@moodlerooms.com>
 * @author      Darko Miletic <dmiletic@moodlerooms.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mhaairs extends block_base {
    protected $customernumber;
    protected $sharedsecret;
    protected $displayservices;
    protected $displayhelplinks;
    private $servicedata;

    /**
     * Which page types this block may appear on.
     *
     * @return array page-type prefix => true/false.
     */
    public function applicable_formats() {
        return array(
            'all' => true,
            'mod' => false,
            'my' => false,
            'admin' => false,
            'tag' => false,
        );
    }

    /**
     * Initializes block title as the plugin name.
     *
     * @return void
     */
    public function init() {
        $this->title = get_string('pluginname', __CLASS__);
    }

    /**
     * Initializes block title as the plugin name.
     *
     * @return void
     */
    public function specialization() {
        global $CFG;

        // Customer number.
        if (!empty($CFG->block_mhaairs_customer_number)) {
            $this->customernumber = $CFG->block_mhaairs_customer_number;
        }

        // Shared secret.
        if (!empty($CFG->block_mhaairs_shared_secret)) {
            $this->sharedsecret = $CFG->block_mhaairs_shared_secret;
        }

        // Display services.
        if (!empty($CFG->block_mhaairs_display_services)) {
            $this->displayservices = $CFG->block_mhaairs_display_services;
        }

        // Display help links.
        $this->displayhelplinks = !empty($CFG->block_mhaairs_display_helplinks);
    }

    /**
     * Returns true to indicate that this block has a settings.php
     * file.
     *
     * @return bool Always true.
     */
    public function has_config() {
        return true;
    }

    /**
     * Returns the block display content.
     *
     * @return stdClass The block content object.
     */
    public function get_content() {

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        // Must be logged in to see the block.
        if (!isloggedin()) {
            return $this->content;
        }
        // Must be in a course context to see the block.
        $courselevel = ($this->page->context->contextlevel == CONTEXT_COURSE);
        $courseinstance = ($this->page->context->instanceid == $this->page->course->id);
        if (!$courselevel or !$courseinstance) {
            $this->content->text = 'not in course';
            return $this->content;
        }

        // Weather current user can see the block with incomplete config.
        $canmanipulateblock = has_capability('block/mhaairs:addinstance', $this->page->context);

        // Must have site configured.
        if (!$this->customernumber or !$this->sharedsecret or !$this->displayservices) {
            if ($canmanipulateblock) {
                // Set config warning in main content.
                $this->content->text = $this->get_warning_message('sitenotconfig');
            }
            return $this->content;
        }

        // MAIN CONTENT
        // Must have services enabled in the block level.
        if ($servicelinks = $this->get_service_links()) {
            $this->content->text = $servicelinks;
        } else {
            if ($canmanipulateblock) {
                // Set config warning in main content.
                $this->content->text = $this->get_warning_message('blocknotconfig');
            }
            return $this->content;
        }

        // FOOTER
        // Add help links to footer if applicable.
        //if ($helplinks = $this->get_help_links()) {
        //    foreach ($helplinks as $hlink) {
        //        $hlink = html_writer::tag('div', $hlink, array('class' => 'helplink'));
        //        $this->content->footer .= $hlink;
        //    }
        //}
        return $this->content;
    }

    /**
     * Returns a 'not configured' message.
     * This is used in the block content to alert users with
     * block management permission that the integration is yet configured.
     *
     * @return string HTML fragment.
     */
    public function get_warning_message($msgstr) {
        // Prepare configuration warning.
        $warning = html_writer::tag(
            'div',
             get_string($msgstr, __CLASS__),
             array('class' => 'block_mhaairs_warning')
         );
         return $warning;
    }

    /**
     * Initializes block title as the plugin name.
     *
     * @return void
     */
    public function set_phpunit_test_config(array $options) {
        // Must be in phpunit test mode.
        if (!PHPUNIT_TEST) {
            return;
        }

        // Site: customer number.
        if (!empty($options['block_mhaairs_customer_number'])) {
            $this->customernumber = $options['block_mhaairs_customer_number'];
        }

        // Site: shared secret.
        if (!empty($options['block_mhaairs_shared_secret'])) {
            $this->sharedsecret = $options['block_mhaairs_shared_secret'];
        }

        // Site: display services.
        if (!empty($options['block_mhaairs_display_services'])) {
            $this->displayservices = $options['block_mhaairs_display_services'];
        }

        // Service data.
        if (!empty($options['service_data'])) {
            $this->servicedata = $options['service_data'];
        }
    }

    /**
     * Returns the main part of the block display content.
     * In this version this contains service links.
     *
     * @return string HTML fragment.
     */
    protected function get_service_links() {
        $services = $this->get_service_data();
        if ($services === false) {
            return null;
        }
        $blocklinks = '';
        $imagealt = get_string('imagealt');
        $targetw = array('target' => '__mhaairs_service_window');
        $course = $this->page->course;
        foreach ($services as $aserv) {
            // Icon.
            $iconparams = array(
                'src' => $aserv['ServiceIconUrl'],
                'class' => 'serviceicon',
                'alt' => $imagealt
            );
            $icon = html_writer::tag('img', '', $iconparams);
            // Url.
            $urlparams = array(
                'serviceurl' => MHUtil::hex_encode($aserv['ServiceUrl']),
                'serviceid'  => MHUtil::hex_encode($aserv['ServiceID']),
                'courseid' => $course->id
            );
            $url = new moodle_url('/blocks/mhaairs/redirect.php', $urlparams);
            // Link.
            $link = html_writer::link($url, $aserv['ServiceName'], $targetw);

            $blocklinks .= html_writer::tag('div', $icon. $link, array('class' => 'servicelink'));
        }
        return $blocklinks;
    }

    /**
     * Returns a list of help links the user is permitted to see.
     *
     * @return array Array of HTML link fragments.
     */
    protected function get_help_links() {
        // Must be configured site level.
        if (!$this->displayhelplinks) {
            return array();
        }

        // Get the Help urls if enabled.
        $helpurls = block_mhaairs_connect::get_help_urls();
        if ($helpurls === false) {
            return array();
        }
        $helplinks = array();
        $context = $this->page->context;
        // Admin help link.
        $adminhelp = has_capability('block/mhaairs:viewadmindoc', $context);
        if ($adminhelp) {
            $targetw = array('target' => '__mhaairs_adminhelp_window');
            $adminhelplink = html_writer::link(
                $helpurls['AdminHelpUrl'],
                get_string('adminhelplabel', __CLASS__),
                $targetw
            );
            $helplinks[] = $adminhelplink;
        }
        // Teacher help link.
        $teacherhelp = has_capability('block/mhaairs:viewteacherdoc', $context);
        if ($teacherhelp) {
            $targetw = array('target' => '__mhaairs_teacherhelp_window');
            $instrhelplink = html_writer::link(
                $helpurls['InstructorHelpUrl'],
                get_string('instructorhelplabel', __CLASS__),
                $targetw);
            $helplinks[] = $instrhelplink;
        }
        return $helplinks;
    }

    /**
     * Returns list of services to display in the block content,
     * or false if no services are available.
     * For each services, returns:
     *  ServiceID       string id
     *  ServiceIconUrl  string url of an image
     *  ServiceName     string name
     *  ServiceUrl      string url
     *
     * @return array|false Array of arrays.
     */
    protected function get_service_data() {

        $result = false;

        // Some services must be configured site level.
        if (!$this->displayservices) {
            return $result;
        }

        // Initialize the display list with the services enabled in the site level.
        $displaylist = explode(',', $this->displayservices);

        // Limit to instructor selection in the instance, if any.
        // If the instance has not been configured, all services
        // enabled in the site level will be displayed.
        if (!empty($this->config)) {
            foreach ($displaylist as $key => $serviceid) {
                if (empty($this->config->$serviceid)) {
                    unset($displaylist[$key]);
                }
            }
        }

        // Empty display list means that block needs reconfiguration.
        if (empty($displaylist)) {
            return $result;
        }

        natcasesort($displaylist);

        // Get the data of all available services.
        $services = !empty($this->servicedata) ? $this->servicedata : block_mhaairs_connect::get_services();
        if ($services === false) {
            return $result;
        }
        // Collate service data for displayed services.
        $result = array();
        foreach ($displaylist as $serviceid) {
            foreach ($services['Tools'] as $vset) {
                if ($vset['ServiceID'] == $serviceid) {
                    $result[] = $vset;
                }
            }
        }
        return $result;
    }

}
