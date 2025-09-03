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

namespace core_course;

use core\component;
use core\context\course as context_course;
use core\exception\coding_exception;
use repository;
use stdClass;

/**
 * Stores all the information about the available dndupload handlers
 *
 * @package    core
 * @copyright  2012 Davo Smith
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dndupload_handler {
    /**
     * @var array A list of all registered mime types that can be dropped onto a course
     *            along with the modules that will handle them.
     */
    protected $types = [];

    /**
     * @var array  A list of the different file types (extensions) that different modules
     *             will handle.
     */
    protected $filehandlers = [];

    /**
     * @var context_course|null
     */
    protected $context = null;

    /**
     * Gather a list of dndupload handlers from the different mods
     *
     * @param object $course The course this is being added to (to check course_allowed_module() )
     * @param array|null $modnames An array of module names that are allowed in this course.
     */
    public function __construct($course, $modnames = null) {
        global $CFG, $PAGE;

        // Add some default types to handle.
        // Note: 'Files' type is hard-coded into the Javascript as this needs to be ...
        // ... treated a little differently.
        $this->register_type(
            identifier: 'url',
            datatransfertypes: ['url', 'text/uri-list', 'text/x-moz-url'],
            addmessage: get_string('addlinkhere', 'moodle'),
            namemessage: get_string('nameforlink', 'moodle'),
            handlermessage: get_string('whatforlink', 'moodle'),
            priority: 10,
        );
        $this->register_type(
            identifier: 'text/html',
            datatransfertypes: ['text/html'],
            addmessage: get_string('addpagehere', 'moodle'),
            namemessage: get_string('nameforpage', 'moodle'),
            handlermessage: get_string('whatforpage', 'moodle'),
            priority: 20,
        );
        $this->register_type(
            identifier: 'text',
            datatransfertypes: ['text', 'text/plain'],
            addmessage: get_string('addpagehere', 'moodle'),
            namemessage: get_string('nameforpage', 'moodle'),
            handlermessage: get_string('whatforpage', 'moodle'),
            priority: 30,
        );

        $this->context = context_course::instance($course->id);

        // Loop through all modules to find handlers.
        $mods = get_plugin_list_with_function('mod', 'dndupload_register');
        foreach ($mods as $component => $funcname) {
            [$modtype, $modname] = component::normalize_component($component);
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
                    $this->register_type(
                        identifier: $type['identifier'],
                        datatransfertypes: $type['datatransfertypes'],
                        addmessage: $type['addmessage'],
                        namemessage: $type['namemessage'],
                        handlermessage: $type['handlermessage'],
                        priority: $priority,
                    );
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
    protected function register_type($identifier, $datatransfertypes, $addmessage, $namemessage, $handlermessage, $priority = 100) {
        if ($this->is_known_type($identifier)) {
            throw new coding_exception("Type $identifier is already registered");
        }

        $add = (object) [
            'identifier' => $identifier,
            'datatransfertypes' => $datatransfertypes,
            'addmessage' => $addmessage,
            'namemessage' => $namemessage,
            'handlermessage' => $handlermessage,
            'priority' => $priority,
            'handlers' => [],
        ];

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

        $add = (object) [
            'type' => $type,
            'module' => $module,
            'message' => $message,
            'noname' => $noname ? 1 : 0,
        ];

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

        $add = (object) [
            'extension' => $extension,
            'module' => $module,
            'message' => $message,
        ];

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
        $types = [];
        foreach ($this->filehandlers as $handler) {
            if ($handler->module == $module) {
                if ($handler->extension == '*') {
                    return '*';
                } else {
                    // Prepending '.' as otherwise mimeinfo fails.
                    $types[] = '.' . $handler->extension;
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

        $this->load_repository();

        $ret = new stdClass();

        // Sort the types by priority.
        uasort($this->types, [$this, 'type_compare']);

        $ret->types = [];
        if (!empty($CFG->dndallowtextandlinks)) {
            foreach ($this->types as $type) {
                if (empty($type->handlers)) {
                    continue; // Skip any types without registered handlers.
                }
                $ret->types[] = $type;
            }
        }

        $ret->filehandlers = $this->filehandlers;
        $uploadrepo = repository::get_instances(['type' => 'upload', 'currentcontext' => $this->context]);
        if (empty($uploadrepo)) {
            $ret->filehandlers = []; // No upload repo => no file handlers.
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

    /**
     * Load the repository libraries.
     */
    private function load_repository(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/repository/lib.php");
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(dndupload_handler::class, \dndupload_handler::class);
