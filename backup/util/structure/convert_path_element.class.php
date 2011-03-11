<?php

class convert_path_element extends restore_path_element {
    public function get_processing_method() {
        return 'convert_' . $this->get_name();
    }
}