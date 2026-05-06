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

namespace core_admin\setting\setting;

use core_admin\admin_search;

/**
 * Manage repository settings
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class managerepository extends \core_admin\setting {
/** @var string */
    private $baseurl;

    /**
     * calls parent::__construct with specific arguments
     */
    public function __construct() {
        global $CFG;
        parent::__construct('managerepository', get_string('manage', 'repository'), '', '');
        $this->baseurl = $CFG->wwwroot . '/' . $CFG->admin . '/repository.php?sesskey=' . sesskey();
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns s_managerepository
     *
     * @return string Always return 's_managerepository'
     */
    public function get_full_name() {
        return 's_managerepository';
    }

    /**
     * Always returns '' doesn't do anything
     */
    public function write_setting($data) {
        $url = $this->baseurl . '&amp;new=' . $data;
        return '';
    // TODO
    // Should not use redirect and exit here
    // Find a better way to do this.
    // redirect($url);
    // exit;
    }

    /**
     * Searches repository plugins for one that matches $query
     *
     * @param string $query The string to search for
     * @return bool true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $repositories= \core_component::get_plugin_list('repository');
        foreach ($repositories as $p => $dir) {
            if (strpos($p, $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
                return true;
            }
        }
        foreach (\repository::get_types() as $instance) {
            $title = $instance->get_typename();
            if (strpos(\core_text::strtolower($title), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_DISPLAY_NAME;
                return true;
            }
        }
        return false;
    }

    /**
     * Helper function that generates a moodle_url object
     * relevant to the repository
     */

    function repository_action_url($repository) {
        return new \moodle_url($this->baseurl, array('sesskey'=>sesskey(), 'repos'=>$repository));
    }

    /**
     * Builds XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string XHTML
     */
    public function output_html($data, $query='') {
        global $CFG, $USER, $OUTPUT;

        // Get strings that are used
        $strshow = get_string('on', 'repository');
        $strhide = get_string('off', 'repository');
        $strdelete = get_string('disabled', 'repository');

        $actionchoicesforexisting = array(
            'show' => $strshow,
            'hide' => $strhide,
            'delete' => $strdelete
        );

        $actionchoicesfornew = array(
            'newon' => $strshow,
            'newoff' => $strhide,
            'delete' => $strdelete
        );

        $return = '';
        $return .= $OUTPUT->box_start('generalbox');

        // Set strings that are used multiple times
        $settingsstr = get_string('settings');
        $disablestr = get_string('disable');

        // Table to list plug-ins
        $table = new \html_table();
        $table->head = array(get_string('name'), get_string('isactive', 'repository'), get_string('order'), $settingsstr);
        $table->align = array('left', 'center', 'center', 'center', 'center');
        $table->data = array();

        // Get list of used plug-ins
        $repositorytypes = \repository::get_types();
        if (!empty($repositorytypes)) {
            // Array to store plugins being used
            $alreadyplugins = array();
            $totalrepositorytypes = count($repositorytypes);
            $updowncount = 1;
            foreach ($repositorytypes as $i) {
                $settings = '';
                $typename = $i->get_typename();
                // Display edit link only if you can config the type or if it has multiple instances (e.g. has instance config)
                $typeoptionnames = \repository::static_function($typename, 'get_type_option_names');
                $instanceoptionnames = \repository::static_function($typename, 'get_instance_option_names');

                if (!empty($typeoptionnames) || !empty($instanceoptionnames)) {
                    // Calculate number of instances in order to display them for the Moodle administrator
                    if (!empty($instanceoptionnames)) {
                        $params = array();
                        $params['context'] = array(\context_system::instance());
                        $params['onlyvisible'] = false;
                        $params['type'] = $typename;
                        $admininstancenumber = count(\repository::static_function($typename, 'get_instances', $params));
                        // site instances
                        $admininstancenumbertext = get_string('instancesforsite', 'repository', $admininstancenumber);
                        $params['context'] = array();
                        $instances = \repository::static_function($typename, 'get_instances', $params);
                        $courseinstances = array();
                        $userinstances = array();

                        foreach ($instances as $instance) {
                            $repocontext = \context::instance_by_id($instance->instance->contextid);
                            if ($repocontext->contextlevel == CONTEXT_COURSE) {
                                $courseinstances[] = $instance;
                            } else if ($repocontext->contextlevel == CONTEXT_USER) {
                                $userinstances[] = $instance;
                            }
                        }
                        // course instances
                        $instancenumber = count($courseinstances);
                        $courseinstancenumbertext = get_string('instancesforcourses', 'repository', $instancenumber);

                        // user private instances
                        $instancenumber =  count($userinstances);
                        $userinstancenumbertext = get_string('instancesforusers', 'repository', $instancenumber);
                    } else {
                        $admininstancenumbertext = "";
                        $courseinstancenumbertext = "";
                        $userinstancenumbertext = "";
                    }

                    $settings .= '<a href="' . $this->baseurl . '&amp;action=edit&amp;repos=' . $typename . '">' . $settingsstr .'</a>';

                    $settings .= $OUTPUT->container_start('mdl-left');
                    $settings .= '<br/>';
                    $settings .= $admininstancenumbertext;
                    $settings .= '<br/>';
                    $settings .= $courseinstancenumbertext;
                    $settings .= '<br/>';
                    $settings .= $userinstancenumbertext;
                    $settings .= $OUTPUT->container_end();
                }
                // Get the current visibility
                if ($i->get_visible()) {
                    $currentaction = 'show';
                } else {
                    $currentaction = 'hide';
                }

                $select = new \single_select($this->repository_action_url($typename, 'repos'), 'action', $actionchoicesforexisting, $currentaction, null, 'applyto' . basename($typename));

                // Display up/down link
                $updown = '';
                // Should be done with CSS instead.
                $spacer = $OUTPUT->spacer(array('height' => 15, 'width' => 15, 'class' => 'smallicon'));

                if ($updowncount > 1) {
                    $updown .= "<a href=\"$this->baseurl&amp;action=moveup&amp;repos=".$typename."\">";
                    $updown .= $OUTPUT->pix_icon('t/up', get_string('moveup')) . '</a>&nbsp;';
                }
                else {
                    $updown .= $spacer;
                }
                if ($updowncount < $totalrepositorytypes) {
                    $updown .= "<a href=\"$this->baseurl&amp;action=movedown&amp;repos=".$typename."\">";
                    $updown .= $OUTPUT->pix_icon('t/down', get_string('movedown')) . '</a>&nbsp;';
                }
                else {
                    $updown .= $spacer;
                }

                $updowncount++;

                $table->data[] = array($i->get_readablename(), $OUTPUT->render($select), $updown, $settings);

                if (!in_array($typename, $alreadyplugins)) {
                    $alreadyplugins[] = $typename;
                }
            }
        }

        // Get all the plugins that exist on disk
        $plugins = \core_component::get_plugin_list('repository');
        if (!empty($plugins)) {
            foreach ($plugins as $plugin => $dir) {
                // Check that it has not already been listed
                if (!in_array($plugin, $alreadyplugins)) {
                    $select = new \single_select($this->repository_action_url($plugin, 'repos'), 'action', $actionchoicesfornew, 'delete', null, 'applyto' . basename($plugin));
                    $table->data[] = array(get_string('pluginname', 'repository_'.$plugin), $OUTPUT->render($select), '', '');
                }
            }
        }

        $return .= \html_writer::table($table);
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(managerepository::class, \admin_setting_managerepository::class);
