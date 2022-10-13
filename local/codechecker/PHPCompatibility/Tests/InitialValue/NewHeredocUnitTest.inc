<?php

// Static variables.
function foo() {
    static $bar = <<<LABEL
Nothing in here...
LABEL;
}

// Class properties/constants.
class foo
{
    const BAR = <<<FOOBAR
Constant example
FOOBAR;

    // Double quoted heredoc only introduced in PHP 5.3 and will be caught by another sniff.
    // Will still throw an error here too if the sniff is run on PHP >= 5.3.
    private $baz = <<<"FOOBAR"
Property example
FOOBAR;
}

// Anonymous class properties/constants.
$class = new class
{
    const BAR = <<<FOOBAR
Constant example
FOOBAR;

    protected static $baz = <<<FOOBAR
Property example
FOOBAR;
}

// Interface constants - interfaces cannot declare properties.
interface FooBar
{
    const BAR = <<<FOOBAR
Constant example
FOOBAR;
}

// Trait properties - traits cannot declare constants.
trait FooBar
{
    public $baz = <<<FOOBAR
Property example
FOOBAR;
}

const ONE = <<<"FOOBAR"
Global constant example
FOOBAR;

// This is a parse error no matter what, but that's not our concern.
// (`const` can only be used in class scope or global scope).
class SomeThing {
    function scoped() {
        const ONE = <<<FOOBAR
Property example
FOOBAR;
    }
}

/*
 * Test against false positives.
 */
// Pre-PHP 5.3 ordinary variable initialization with heredoc was already ok.
$var = <<<FOOBAR
Constant example
FOOBAR;

// Nowdoc was only introduced in PHP 5.3 and is sniffed for in a separate sniff.
$var = <<<'FOOBAR'
Constant example
FOOBAR;

$array = array( 'key' => <<<FOOBAR
Constant example
FOOBAR
);

/*
 * Test handling of multi-declarations.
 */
static $a = <<<EOD
Multi-declaration in static variable.
EOD
    , $b = <<<"EOT"
Multi-declaration in static variable.
EOT;

// Class properties/constants.
class foo
{
    const BAR = <<<FOOBAR
Multi-declaration class constant
FOOBAR
    , FOO = <<<FOOBAR
Multi-declaration class constant
FOOBAR;

    private $baz = <<<"FOOBAR"
Multi-declaration Property example
FOOBAR
    , $foy = <<<FOOBAR
Multi-declaration Property example
FOOBAR;
}

/*
 * Test handling of function parameter default values.
 */
function heredocDefault(string $a = <<<EOD
This is a function default value.
EOD
) {}

function heredocDefaults(
    $a = <<<EOD
This is a function default value.
EOD
    , $b = <<<"EOT"
This is a function default value.
EOT
) {}
