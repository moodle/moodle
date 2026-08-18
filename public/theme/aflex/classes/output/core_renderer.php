<?php

namespace theme_aflex\output;

defined('MOODLE_INTERNAL') || die();

class core_renderer extends \theme_boost\output\core_renderer {
    public function standard_head_html() {
        $html = parent::standard_head_html();
        return str_replace('content="moodle, ', 'content="AFLEX, ', $html);
    }

    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        $url = $this->page->theme->setting_file_url('logo', 'logo');
        return $url ?: parent::get_logo_url($maxwidth, $maxheight);
    }

    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        $url = $this->page->theme->setting_file_url('logo', 'logo');
        return $url ?: parent::get_compact_logo_url($maxwidth, $maxheight);
    }

    public function favicon() {
        $url = $this->page->theme->setting_file_url('favicon', 'favicon');
        return $url ?: parent::favicon();
    }
}
