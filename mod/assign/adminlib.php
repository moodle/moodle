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
 * This file contains the classes for the admin settings of the assign module.
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

/**
 * Admin external page that displays a list of the installed submission plugins.
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_admin_page_manage_assign_plugins extends admin_externalpage {

    /** @var string the name of plugin subtype */
    private $subtype = '';

    /**
     * The constructor - calls parent constructor
     *
     * @param string $subtype
     */
    public function __construct($subtype) {
        $this->subtype = $subtype;
        $url = new moodle_url('/mod/assign/adminmanageplugins.php', array('subtype'=>$subtype));
        parent::__construct('manage' . $subtype . 'plugins',
                            get_string('manage' . $subtype . 'plugins', 'assign'),
                            $url);
    }

    /**
     * Search plugins for the specified string
     *
     * @param string $query The string to search for
     * @return array
     */
    public function search($query) {
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;

        foreach (core_component::get_plugin_list($this->subtype) as $name => $notused) {
            if (strpos(core_text::strtolower(get_string('pluginname', $this->subtype . '_' . $name)),
                    $query) !== false) {
                $found = true;
                break;
            }
        }
        if ($found) {
            $result = new stdClass();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }
}


/**
 * Class that handles the display and configuration of the list of submission plugins.
 *
 * @package   mod_assign
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_plugin_manager {

    /** @var object the url of the manage submission plugin page */
    private $pageurl;
    /** @var string any error from the current action */
    private $error = '';
    /** @var string either submission or feedback */
    private $subtype = '';

    /**
     * Constructor for this assignment plugin manager
     * @param string $subtype - either assignsubmission or assignfeedback
     */
    public function __construct($subtype) {
        $this->pageurl = new moodle_url('/mod/assign/adminmanageplugins.php', array('subtype'=>$subtype));
        $this->subtype = $subtype;
    }


    /**
     * Return a list of plugins sorted by the order defined in the admin interface
     *
     * @return array The list of plugins
     */
    public function get_sorted_plugins_list() {
        $names = core_component::get_plugin_list($this->subtype);

        $result = array();

        foreach ($names as $name => $path) {
            $idx = get_config($this->subtype . '_' . $name, 'sortorder');
            if (!$idx) {
                $idx = 0;
            }
            while (array_key_exists($idx, $result)) {
                $idx +=1;
            }
            $result[$idx] = $name;
        }
        ksort($result);

        return $result;
    }


    /**
     * Util function for writing an action icon link
     *
     * @param string $action URL parameter to include in the link
     * @param string $plugin URL parameter to include in the link
     * @param string $icon The key to the icon to use (e.g. 't/up')
     * @param string $alt The string description of the link used as the title and alt text
     * @return string The icon/link
     */
    private function format_icon_link($action, $plugin, $icon, $alt) {
        global $OUTPUT;

        $url = $this->pageurl;

        if ($action === 'delete') {
            $url = core_plugin_manager::instance()->get_uninstall_url($this->subtype.'_'.$plugin, 'manage');
            if (!$url) {
                return '&nbsp;';
            }
            return html_writer::link($url, get_string('uninstallplugin', 'core_admin'));
        }

        return $OUTPUT->action_icon(new moodle_url($url,
                array('action' => $action, 'plugin'=> $plugin, 'sesskey' => sesskey())),
                new pix_icon($icon, $alt, 'moodle', array('title' => $alt)),
                null, array('title' => $alt)) . ' ';
    }

    /**
     * Write the HTML for the submission plugins table.
     *
     * @return None
     */
    private function view_plugins_table() {
        global $OUTPUT, $CFG;
        require_once($CFG->libdir . '/tablelib.php');

        // Set up the table.
        $this->view_header();
        $table = new flexible_table($this->subtype . 'pluginsadminttable');
        $table->define_baseurl($this->pageurl);
        $table->define_columns(array('pluginname', 'version', 'hideshow', 'order',
                'settings', 'uninstall'));
        $table->define_headers(array(get_string($this->subtype . 'pluginname', 'assign'),
                get_string('version'), get_string('hideshow', 'assign'),
                get_string('order'), get_string('settings'), get_string('uninstallplugin', 'core_admin')));
        $table->set_attribute('id', $this->subtype . 'plugins');
        $table->set_attribute('class', 'admintable generaltable');
        $table->setup();

        $plugins = $this->get_sorted_plugins_list();
        $shortsubtype = substr($this->subtype, strlen('assign'));

        foreach ($plugins as $idx => $plugin) {
            $row = array();
            $class = '';

            $row[] = get_string('pluginname', $this->subtype . '_' . $plugin);
            $row[] = get_config($this->subtype . '_' . $plugin, 'version');

            $visible = !get_config($this->subtype . '_' . $plugin, 'disabled');

            if ($visible) {
                $row[] = $this->format_icon_link('hide', $plugin, 't/hide', get_string('disable'));
            } else {
                $row[] = $this->format_icon_link('show', $plugin, 't/show', get_string('enable'));
                $class = 'dimmed_text';
            }

            $movelinks = '';
            if (!$idx == 0) {
                $movelinks .= $this->format_icon_link('moveup', $plugin, 't/up', get_string('up'));
            } else {
                $movelinks .= $OUTPUT->spacer(array('width'=>16));
            }
            if ($idx != count($plugins) - 1) {
                $movelinks .= $this->format_icon_link('movedown', $plugin, 't/down', get_string('down'));
            }
            $row[] = $movelinks;

            $exists = file_exists($CFG->dirroot . '/mod/assign/' . $shortsubtype . '/' . $plugin . '/settings.php');
            if ($row[1] != '' && $exists) {
                $row[] = html_writer::link(new moodle_url('/admin/settings.php',
                        array('section' => $this->subtype . '_' . $plugin)), get_string('settings'));
            } else {
                $row[] = '&nbsp;';
            }

            $row[] = $this->format_icon_link('delete', $plugin, 't/delete', get_string('uninstallplugin', 'core_admin'));

            $table->add_data($row, $class);
        }

        $table->finish_output();
        $this->view_footer();
    }

    /**
     * Write the page header
     *
     * @return None
     */
    private function view_header() {
        global $OUTPUT;
        admin_externalpage_setup('manage' . $this->subtype . 'plugins');
        // Print the page heading.
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('manage' . $this->subtype . 'plugins', 'assign'));
    }

    /**
     * Write the page footer
     *
     * @return None
     */
    private function view_footer() {
        global $OUTPUT;
        echo $OUTPUT->footer();
    }

    /**
     * Check this user has permission to edit the list of installed plugins
     *
     * @return None
     */
    private function check_permissions() {
        // Check permissions.
        require_login();
        $systemcontext = context_system::instance();
        require_capability('moodle/site:config', $systemcontext);
    }

    /**
     * Hide this plugin.
     *
     * @param string $plugin - The plugin to hide
     * @return string The next page to display
     */
    public function hide_plugin($plugin) {
        $class = \core_plugin_manager::resolve_plugininfo_class($this->subtype);
        $class::enable_plugin($plugin, false);
        return 'view';
    }

    /**
     * Change the order of this plugin.
     *
     * @param string $plugintomove - The plugin to move
     * @param string $dir - up or down
     * @return string The next page to display
     */
    public function move_plugin($plugintomove, $dir) {
        // Get a list of the current plugins.
        $plugins = $this->get_sorted_plugins_list();

        $currentindex = 0;

        // Throw away the keys.
        $plugins = array_values($plugins);

        // Find this plugin in the list.
        foreach ($plugins as $key => $plugin) {
            if ($plugin == $plugintomove) {
                $currentindex = $key;
                break;
            }
        }

        // Make the switch.
        if ($dir == 'up') {
            if ($currentindex > 0) {
                $tempplugin = $plugins[$currentindex - 1];
                $plugins[$currentindex - 1] = $plugins[$currentindex];
                $plugins[$currentindex] = $tempplugin;
            }
        } else if ($dir == 'down') {
            if ($currentindex < (count($plugins) - 1)) {
                $tempplugin = $plugins[$currentindex + 1];
                $plugins[$currentindex + 1] = $plugins[$currentindex];
                $plugins[$currentindex] = $tempplugin;
            }
        }

        // Save the new normal order.
        foreach ($plugins as $key => $plugin) {
            set_config('sortorder', $key, $this->subtype . '_' . $plugin);
        }
        return 'view';
    }


    /**
     * Show this plugin.
     *
     * @param string $plugin - The plugin to show
     * @return string The next page to display
     */
    public function show_plugin($plugin) {
        $class = \core_plugin_manager::resolve_plugininfo_class($this->subtype);
        $class::enable_plugin($plugin, true);
        return 'view';
    }


    /**
     * This is the entry point for this controller class.
     *
     * @param string $action - The action to perform
     * @param string $plugin - Optional name of a plugin type to perform the action on
     * @return None
     */
    public function execute($action, $plugin) {
        if ($action == null) {
            $action = 'view';
        }

        $this->check_permissions();

        // Process.
        if ($action == 'hide' && $plugin != null) {
            $action = $this->hide_plugin($plugin);
        } else if ($action == 'show' && $plugin != null) {
            $action = $this->show_plugin($plugin);
        } else if ($action == 'moveup' && $plugin != null) {
            $action = $this->move_plugin($plugin, 'up');
        } else if ($action == 'movedown' && $plugin != null) {
            $action = $this->move_plugin($plugin, 'down');
        }

        // View.
        if ($action == 'view') {
            $this->view_plugins_table();
        }
    }
}
