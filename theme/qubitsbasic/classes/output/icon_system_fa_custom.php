<?php

namespace theme_qubitsbasic\output;

defined('MOODLE_INTERNAL') || die();

class icon_system_fa_custom extends \core\output\icon_system_fontawesome {
    public function get_core_icon_map() {
        $map = parent::get_core_icon_map();
        $map['core:i/qnavicon'] = 'qicon-notifications';
        return $map;
    }
}