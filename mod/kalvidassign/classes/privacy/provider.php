<?php

namespace mod_kalvidassign_mod_form\privacy;

use core_privacy\local\metadata\collection;

defined('MOODLE_INTERNAL') || die();

class provider implements \core_privacy\local\metadata\provider {

    public static function get_metadata(collection $collection) : collection {

        // Here you will add more items into the collection.

        return $collection;
    }
}
