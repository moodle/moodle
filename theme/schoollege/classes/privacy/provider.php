<?php

namespace theme_schoollege\privacy;
 
class provider implements 
    \core_privacy\local\metadata\null_provider {
    public static function get_reason() : string {
        return 'privacy:metadata';
    }
}
