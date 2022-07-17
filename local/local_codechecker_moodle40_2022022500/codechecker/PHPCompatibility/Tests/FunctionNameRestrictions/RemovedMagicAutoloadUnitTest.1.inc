<?php

function __autoload($someclass) {
    echo 'I am the autoloader - I am deprecated from PHP 7.2 onwards';
}

class fooclass {
    function __autoload($someclass) {
        echo 'I am the autoloader in a class (which makes no sense) - I am deprecated from PHP 7.2 onwards';
    }
}

interface foointerface {
    function __autoload($someclass);
}

trait footrait {
    function __autoload($someclass) {
        echo 'I am the autoloader in a trait (which makes no sense) - I am deprecated from PHP 7.2 onwards';
    }
}

fooanonclass(new class {
    function __autoload($someclass) {
        echo 'I am the autoloader in an anonymous class (which makes no sense) - I am deprecated from PHP 7.2 onwards';
    }
});
