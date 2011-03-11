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
}