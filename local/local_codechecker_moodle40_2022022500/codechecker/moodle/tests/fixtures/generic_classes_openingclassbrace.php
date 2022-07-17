<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// ONE space before curly bracket is mandatory.
class test01{
}

class test02 extends test01{
}

class test03   {
}

class test04 extends test01   {
}

// New line curly bracket is a viloation
class test05
{
}

// These are correct.
class test06 {
}

class test07 {}

class test08 extends test06 {
}

class test09 extends test06 {}
