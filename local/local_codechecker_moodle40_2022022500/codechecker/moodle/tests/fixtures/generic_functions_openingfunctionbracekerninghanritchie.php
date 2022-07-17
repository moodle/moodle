<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// These are wrong, ONE space before curly bracket is mandatory.
// Functions.
function test01():int{
}

function test02():?int{
}

function test03():int   {
}

function test04():?int   {
}

// Methods.
class testbad {
    public function test01():int{
    }

    public function test02():?int{
    }

    public function test03():int   {
    }

    public function test04():?int   {
    }
}

// But any other space around is ok, in any combination.
// All together with and without nullable question mark.
// Functions.
function test05():int {
}

function test06():?int {
}

// One space everywhere, with and without nullable question marks.
function test07() : int {
}

function test08() : ? int {
}

// Multiple spaces everywhere,  with and without nullable question marks.
function test09()   :   int {
}

function test10()   :   ?   int {
}

function test11()   :?   int {
}

function test12()   :   ?int {
}

function test13():      ?int {
}

// Methods.
class testok {
    // All together with and without nullable question mark.
    public function test05():int {
    }

    public function test06():?int {
    }

    // One space everywhere, with and without nullable question marks.
    public function test07() : int {
    }

    public function test08() : ? int {
    }

    // Multiple spaces everywhere,  with and without nullable question marks.
    public function test09()   :   int {
    }

    public function test10()   :   ?   int {
    }

    public function test11()   :?   int {
    }

    public function test12()   :   ?int {
    }

    public function test13():      ?int {
    }
}
