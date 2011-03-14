<?php
/**
 * General Convert Helper
 */
abstract class convert_helper {
    public static function generate_id($entropy) {
        return md5(time() . '-' . $entropy . '-' . random_string(20));
    }

    /**
     * @static
     * @throws coding_exception|restore_controller_exception
     * @param string $tempdir The directory to convert
     * @param string $format The current format, if already detected
     * @return void
     */
    public static function to_moodle2_format($tempdir, $format = NULL) {
        if (is_null($format)) {
            $format = backup_general_helper::detect_backup_format($tempdir);
        }
        while (!in_array($format, array(backup::FORMAT_MOODLE, backup::FORMAT_UNKNOWN))) {
            $converter = convert_factory::converter($format, $tempdir);

            if (!$converter->can_convert()) {
                throw new coding_exception('Converter detection failed, the loaded converter cannot convert this format');
            }
            $converter->convert();

            // Re-detect format
            $format = backup_general_helper::detect_backup_format($tempdir);
        }
        if ($format == backup::FORMAT_UNKNOWN) {
            throw new restore_controller_exception('cannot_convert_from_unknown_format');  // @todo Change exception class
        }
    }

   /**
    * Inserts an inforef into the conversion temp table
    */
    public static function set_inforef($contextid) {
        global $DB;


    }

    public static function get_inforef($contextid) {
    }

    /**
     * Converts a plain old php object (popo?) into a string...
     * Useful for debuging failed db inserts, or anything like that
     */
    public static function obj_to_readable($obj) {
        $mapper = function($field, $value) { return "$field=$value"; };
        $fields = get_object_vars($obj);

        return implode(", ", array_map($mapper, array_keys($fields), array_values($fields)));
    }

    /**
     * Generate an artificial context ID
     *
     * @static
     * @throws Exception
     * @param int $instance The moodle component instance ID, same value used for get_context_instance()
     * @param string $component The moodle component, like block_html, mod_quiz, etc
     * @param string $converterid The converter ID
     * @return int
     * @todo Add caching?
     * @todo Can we make the lookup faster?  Not taking advantage of indexes
     */
    public static function get_contextid($instance, $component = 'moodle', $converterid = NULL) {
        global $DB;

        // Attempt to retrieve the contextid
        $contextid = $DB->get_field('backup_ids_temp', 'id', array('itemid' => $instance, 'info' => $component));

        if (!empty($contextid)) {
            return $contextid;
        }

        $context = new stdClass;
        $context->itemid   = $instance;
        $context->itemname = 'context';
        $context->info     = $component;

        if (!is_null($converterid)) {
            $context->backupid = $converterid;
        }
        if ($id = $DB->insert('backup_ids_temp', $context)) {
            return $id;
        } else {
            $msg = self::obj_to_readable($context);
            throw new Exception(sprintf("Could not insert context record into temp table: %s", $msg));
        }
    }
}
