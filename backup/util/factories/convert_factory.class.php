<?php

abstract class convert_factory {
    /**
     * @static
     * @throws coding_exception
     * @param  $name The converter name
     * @param  $tempdir The temp directory to operate on
     * @return base_converter|plan_converter
     */
    public static function converter($name, $tempdir) {
        global $CFG;

        $name = clean_param($name, PARAM_SAFEDIR);

        $classfile = "$CFG->dirroot/backup/converter/$name/converter.class.php";
        $classname = "{$name}_converter";

        if (!file_exists($classfile)) {
            throw new coding_exception("Converter factory error: class file not found $classfile");
        }
        require_once($classfile);

        if (!class_exists($classname)) {
            throw new coding_exception("Converter factory error: class not found $classname");
        }
        return new $classname($tempdir);
    }

    /**
     * @static
     * @param string $tempdir The temp directory to operate on
     * @return array
     */
    public static function converters($tempdir) {
        global $CFG;

        $converters = array();
        $plugins    = get_list_of_plugins('backup/converter');
        foreach ($plugins as $name) {
            $converters[$name] = self::converter($name, $tempdir);
        }
        return $converters;
    }

    // @todo DELETE part of prototype code
    public static function activity_task(base_converter $converter, $name, array $data) {
        global $CFG;

        static $classmap = array();

        $convertname = $converter->get_name();

        if (!array_key_exists($convertname, $classmap)) {
            $classmap[$convertname] = array();
        }
        if (!array_key_exists($name, $classmap[$convertname])) {
            // @TODO include the class file and make sure class exists
            $classfile = "$CFG->dirroot/mod/$name/backup/$convertname/convert_{$name}_activity_task.class.php";
            $classname = "{$convertname}_{$name}_activity_task";

            if (!class_exists($classname)) {
                if (!file_exists($classfile)) {
                    throw new coding_exception("Conversion for $name for format $convertname not supported: class file not found $classfile");
                }
                require_once($classfile);

                if (!class_exists($classname)) {
                    throw new coding_exception("Conversion for $name for format $convertname not supported: class not found $classname");
                }
            }
            $classmap[$convertname][$name] = $classname;
        }
        $classname = $classmap[$convertname][$name];

        return new $classname($name, $data);
    }

    /**
     * Runs through all plugins of a specific type and instantiates
     * their task class.
     *
     * @static
     * @throws coding_exception
     * @param string $type The plugin type
     * @param string $format The convert format
     * @param string $extra Extra naming structure
     * @return array
     */
    public static function get_plugin_tasks($type, $format, $extra = NULL) {
        global $CFG; // REQUIRED by task file includes

        if (is_null($extra)) {
            $extra = $type;
        }
        $tasks   = array();
        $plugins = get_plugin_list($type);
        foreach ($plugins as $name => $dir) {
            $taskfile  = "$dir/backup/$format/convert_{$name}_{$extra}_task.class.php";
            $taskclass = "{$format}_{$name}_{$extra}_task";
            if (!file_exists($taskfile)) {
                continue;
            }
            require_once($taskfile);

            if (!class_exists($taskclass)) {
                throw new coding_exception("The class name should be $taskclass in $taskfile");
            }
            $tasks[] = new $taskclass("{$type}_$name");
        }
        return $tasks;
    }

    /**
     * This will add all of the plugin tasks to the converter's plan
     *
     * @static
     * @param plan_converter $converter The converter to add the plugin tasks to
     * @param string $type The plugin type
     * @param string $extra Extra naming structure
     * @return void
     */
    public static function build_plugin_tasks(plan_converter $converter, $type, $extra = NULL) {
        $tasks = self::get_plugin_tasks($type, $converter->get_name(), $extra);
        foreach ($tasks as $task) {
            $converter->get_plan()->add_task($task);
        }
    }
}