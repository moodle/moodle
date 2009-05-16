<?php

class textarea_texteditor extends texteditor {
    public function supported_by_browser() {
        return true;
    }

    public function get_supported_formats() {
        return array(FORMAT_HTML     => FORMAT_HTML,
                     FORMAT_MOODLE   => FORMAT_MOODLE,
                     FORMAT_PLAIN    => FORMAT_PLAIN,
                     FORMAT_MARKDOWN => FORMAT_MARKDOWN,
                    );
    }

    public function get_preferred_format() {
        return FORMAT_MOODLE;
    }

    public function supports_repositories() {
        return true;
    }

    public function get_editor_element_class() {
        return 'form-textarea-simple';
    }
    
    public function get_legacy_textarea_class() {
        return 'form-textarea-legacy';
    }

    public function header_js() {
        return '';
    }    
}


