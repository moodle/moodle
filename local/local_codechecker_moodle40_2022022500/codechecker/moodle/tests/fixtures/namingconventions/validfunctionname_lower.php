<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

class class_with_correct_function_names {
    public function notUpperPlease() {
        echo 'hi';
    }
}

interface interface_with_correct_function_names {
    public function notUpperPlease() {
        echo 'hi';
    }

    function withoutScope() {
        echo 'hi';
    }
}

function notUpperPlease() {
    echo 'hi';
}
