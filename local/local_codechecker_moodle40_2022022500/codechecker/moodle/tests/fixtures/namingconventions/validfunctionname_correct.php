<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

class class_with_correct_function_names {
    public function __construct() {
        echo 'hi';
    }

    public function __destruct() {
        echo 'hi';
    }

    public function __call() {
        echo 'hi';
    }

    public function __clone() {
        echo 'hi';
    }

    public function setUp() {
        echo 'hi';
    }
}

interface interface_with_correct_function_names {
    public function __construct() {
        echo 'hi';
    }

    public function __destruct() {
        echo 'hi';
    }

    public function __call() {
        echo 'hi';
    }
}

trait trait_with_correct_function_names {
    public function __construct() {
        echo 'hi';
    }

    public function __destruct() {
        echo 'hi';
    }

    public function __call() {
        echo 'hi';
    }
}

return new class {
    public function __construct() {
        echo 'hi';
    }

    public function __destruct() {
        echo 'hi';
    }

    public function __call() {
        echo 'hi';
    }
};
