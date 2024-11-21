<?php

namespace local_intelliboard;

require_once($CFG->dirroot . '/cache/locallib.php');

class ib_config_writer extends \cache_config_writer {
    public function config_save() {
        global $CFG;

        $cachefile = static::get_config_file_path();
        $directory = dirname($cachefile);
        if ($directory !== $CFG->dataroot && !file_exists($directory)) {
            $result = make_writable_directory($directory, false);
            if (!$result) {
                throw new \cache_exception('ex_configcannotsave', 'cache', '', null, 'Cannot create config directory. Check the permissions on your moodledata directory.');
            }
        }
        if (!file_exists($directory) || !is_writable($directory)) {
            throw new \cache_exception('ex_configcannotsave', 'cache', '', null, 'Config directory is not writable. Check the permissions on the moodledata/muc directory.');
        }

        $configuration = [];
        include($cachefile);

        if (isset($configuration['definitions']['local_intelliboard/tracking'])) {
            unset($configuration['definitions']['local_intelliboard/tracking']);
        }

        $content = "<?php defined('MOODLE_INTERNAL') || die();\n \$configuration = ".var_export($configuration, true).";";

        $handle = fopen($cachefile, 'w');
        fwrite($handle, $content);
        fflush($handle);
        fclose($handle);
        @chmod($cachefile, $CFG->filepermissions);
        // Tell PHP to recompile the script.
        \core_component::invalidate_opcode_php_cache($cachefile);
    }
}
