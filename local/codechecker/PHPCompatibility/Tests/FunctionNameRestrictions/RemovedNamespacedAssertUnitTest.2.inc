<?php

namespace AssertDeprecations;

function assert($something) {}

class fooclass {
    public function assert($something) {}
}

interface foointerface {
    public function assert($something);
}

trait footrait {
    public function assert($something) {}
}

fooanonclass(new class {
    private function assert($something) {}
});
