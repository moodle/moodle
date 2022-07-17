phpcs:set Squiz.WhiteSpace.OperatorSpacing ignoreNewlines true
<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// Array representations.
$array = array('test' => 'Test');
$array = array('test'=> 'Test');
$array = array('test' =>'Test');
$array = array('test'=>'Test');
$array = array('test'   => 'Test');
$array = array('test' =>   'Test');

// Various assignments representations.
$a= 10;
$a =10;
$a=10;
$a = 10;
$a =  10;
$a  = 10;

// Mathematical operations.
$c = $a+ 10;
$c = $a +10;
$c = $a+10;
$c = $a + 10;
$c = $a +  10;
$c = $a  + 10;

$c = $a- 10;
$c = $a -10;
$c = $a-10;
$c = $a - 10;
$c = $a -  10;
$c = $a  - 10;

$c = $a* 10;
$c = $a *10;
$c = $a*10;
$c = $a * 10;
$c = $a *  10;
$c = $a  * 10;

$c = $a/ 10;
$c = $a /10;
$c = $a/10;
$c = $a / 10;
$c = $a /  10;
$c = $a  / 10;

// Operators ending in new line instead of space.
$a = $c == 20 ?
    "Hottest nerd there!" : "Zombies are comming";
$a = $c == 20 ? "Hottest nerd there!" :
    "Zombies are comming";
$a = 200 -
    500;
$a = 200 +
    500;
$a = 200 /
    500;
$a = 200 *
    500;

