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
 * Stores all the information about the available dndupload handlers
 *
 * @package    core
 * @copyright  2012 Davo Smith
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dndupload_handler {

    /**
     * @var array A list of all registered mime types that can be dropped onto a course
     *            along with the modules that will handle them.
     */
    protected $types = array();

    /**
     * @var array  A list of the different file types (extensions) that different modules
     *             will handle.
     */
    protected $filehandlers = array();

    /**
     * @var context_course|null
     */
    protected $context = null;

    /**
     * Gather a list of dndupload handlers from the different mods
     *
     * @param object $course The course this is being added to (to check course_allowed_module() )
     */
    public function __construct($course, $modnames = null) {
        global $CFG, $PAGE;

        // Add some default types to handle.
        // Note: 'Files' type is hard-coded into the Javascript as this needs to be ...
        // ... treated a little differently.
        $this->register_type('url', array('url', 'text/uri-list', 'text/x-moz-url'), get_string('addlinkhere', 'moodle'),
                        get_string('nameforlink', 'moodle'), get_string('whatforlink', 'moodle'), 10);
        $this->register_type('text/html', array('text/html'), get_string('addpagehere', 'moodle'),
                        get_string('nameforpage', 'moodle'), get_string('whatforpage', 'moodle'), 20);
        $this->register_type('text', array('text', 'text/plain'), get_string('addpagehere', 'moodle'),
                        get_string('nameforpage', 'moodle'), get_string('whatforpage', 'moodle'), 30);

        $this->context = context_course::instance($course->id);

        // Loop through all modules to find handlers.
        $mods = get_plugin_list_with_function('mod', 'dndupload_register');
        foreach ($mods as $component => $funcname) {
            list($modtype, $modname) = core_component::normalize_component($component);
            if ($modnames && !array_key_exists($modname, $modnames)) {
                continue; // Module is deactivated (hidden) at the site level.
            }
            if (!course_allowed_module($course, $modname)) {
                continue; // User does not have permission to add this module to the course.
            }
            $resp = $funcname();
            if (!$resp) {
                continue;
            }
            if (isset($resp['files'])) {
                foreach ($resp['files'] as $file) {
                    $this->register_file_handler($file['extension'], $modname, $file['message']);
                }
            }
            if (isset($resp['addtypes'])) {
                foreach ($resp['addtypes'] as $type) {
                    if (isset($type['priority'])) {
                        $priority = $type['priority'];
                    } else {
                        $priority = 100;
                    }
                    if (!isset($type['handlermessage'])) {
                        $type['handlermessage'] = '';
                    }
                    $this->register_type($type['identifier'], $type['datatransfertypes'],
                                    $type['addmessage'], $type['namemessage'], $type['handlermessage'], $priority);
                }
            }
            if (isset($resp['types'])) {
                foreach ($resp['types'] as $type) {
                    $noname = !empty($type['noname']);
                    $this->register_type_handler($type['identifier'], $modname, $type['message'], $noname);
                }
            }
            $PAGE->requires->string_for_js('pluginname', $modname);
        }
    }

    /**
     * Used to add a new mime type that can be drag and dropped onto a
     * course displayed in a browser window
     *
     * @param string $identifier The name that this type will be known as
     * @param array $datatransfertypes An array of the different types in the browser
     *                                 'dataTransfer.types' object that will map to this type
     * @param string $addmessage The message to display in the browser when this type is being
     *                           dragged onto the page
     * @param string $namemessage The message to pop up when asking for the name to give the
     *                            course module instance when it is created
     * @param string $handlermessage The message to pop up when asking which module should handle this type
     * @param int $priority Controls the order in which types are checked by the browser (mainly
     *                      needed to check for 'text' last as that is usually given as fallback)
     */
    protected function register_type($identifier, $datatransfertypes, $addmessage, $namemessage, $handlermessage, $priority=100) {
        if ($this->is_known_type($identifier)) {
            throw new coding_exception("Type $identifier is already registered");
        }

        $add = new stdClass;
        $add->identifier = $identifier;
        $add->datatransfertypes = $datatransfertypes;
        $add->addmessage = $addmessage;
        $add->namemessage = $namemessage;
        $add->handlermessage = $handlermessage;
        $add->priority = $priority;
        $add->handlers = array();

        $this->types[$identifier] = $add;
    }

    /**
     * Used to declare that a particular module will handle a particular type
     * of dropped data
     *
     * @param string $type The name of the type (as declared in register_type)
     * @param string $module The name of the module to handle this type
     * @param string $message The message to show the user if more than one handler is registered
     *                        for a type and the user needs to make a choice between them
     * @param bool $noname If true, the 'name' dialog should be disabled in the pop-up.
     * @throws coding_exception
     */
    protected function register_type_handler($type, $module, $message, $noname) {
        if (!$this->is_known_type($type)) {
            throw new coding_exception("Trying to add handler for unknown type $type");
        }

        $add = new stdClass;
        $add->type = $type;
        $add->module = $module;
        $add->message = $message;
        $add->noname = $noname ? 1 : 0;

        $this->types[$type]->handlers[] = $add;
    }

    /**
     * Used to declare that a particular module will handle a particular type
     * of dropped file
     *
     * @param string $extension The file extension to handle ('*' for all types)
     * @param string $module The name of the module to handle this type
     * @param string $message The message to show the user if more than one handler is registered
     *                        for a type and the user needs to make a choice between them
     */
    protected function register_file_handler($extension, $module, $message) {
        $extension = strtolower($extension);

        $add = new stdClass;
        $add->extension = $extension;
        $add->module = $module;
        $add->message = $message;

        $this->filehandlers[] = $add;
    }

    /**
     * Check to see if the type has been registered
     *
     * @param string $type The identifier of the type you are interested in
     * @return bool True if the type is registered
     */
    public function is_known_type($type) {
        return array_key_exists($type, $this->types);
    }

    /**
     * Check to see if the module in question has registered to handle the
     * type given
     *
     * @param string $module The name of the module
     * @param string $type The identifier of the type
     * @return bool True if the module has registered to handle that type
     */
    public function has_type_handler($module, $type) {
        if (!$this->is_known_type($type)) {
            throw new coding_exception("Checking for handler for unknown type $type");
        }
        foreach ($this->types[$type]->handlers as $handler) {
            if ($handler->module == $module) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check to see if the module in question has registered to handle files
     * with the given extension (or to handle all file types)
     *
     * @param string $module The name of the module
     * @param string $extension The extension of the uploaded file
     * @return bool True if the module has registered to handle files with
     *              that extension (or to handle all file types)
     */
    public function has_file_handler($module, $extension) {
        foreach ($this->filehandlers as $handler) {
            if ($handler->module == $module) {
                if ($handler->extension == '*' || $handler->extension == $extension) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Gets a list of the file types that are handled by a particular module
     *
     * @param string $module The name of the module to check
     * @return array of file extensions or string '*'
     */
    public function get_handled_file_types($module) {
        $types = array();
        foreach ($this->filehandlers as $handler) {
            if ($handler->module == $module) {
                if ($handler->extension == '*') {
                    return '*';
                } else {
                    // Prepending '.' as otherwise mimeinfo fails.
                    $types[] = '.'.$handler->extension;
                }
            }
        }
        return $types;
    }

    /**
     * Returns an object to pass onto the javascript code with data about all the
     * registered file / type handlers
     *
     * @return object Data to pass on to Javascript code
     */
    public function get_js_data() {
        global $CFG;

        $ret = new stdClass;

        // Sort the types by priority.
        uasort($this->types, array($this, 'type_compare'));

        $ret->types = array();
        if (!empty($CFG->dndallowtextandlinks)) {
            foreach ($this->types as $type) {
                if (empty($type->handlers)) {
                    continue; // Skip any types without registered handlers.
                }
                $ret->types[] = $type;
            }
        }

        $ret->filehandlers = $this->filehandlers;
        $uploadrepo = repository::get_instances(array('type' => 'upload', 'currentcontext' => $this->context));
        if (empty($uploadrepo)) {
            $ret->filehandlers = array(); // No upload repo => no file handlers.
        }

        return $ret;
    }

    /**
     * Comparison function used when sorting types by priority
     * @param object $type1 first type to compare
     * @param object $type2 second type to compare
     * @return integer -1 for $type1 < $type2; 1 for $type1 > $type2; 0 for equal
     */
    protected function type_compare($type1, $type2) {
        if ($type1->priority < $type2->priority) {
            return -1;
        }
        if ($type1->priority > $type2->priority) {
            return 1;
        }
        return 0;
    }

}
