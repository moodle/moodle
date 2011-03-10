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
}