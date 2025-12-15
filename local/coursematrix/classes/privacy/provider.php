<?php
namespace local_coursematrix\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\null_provider;

/**
 * Privacy provider for local_coursematrix.
 */
class provider implements null_provider {
    /**
     * Get the language string identifier with the component's language file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason() : string {
        return 'privacy:metadata';
    }
}
