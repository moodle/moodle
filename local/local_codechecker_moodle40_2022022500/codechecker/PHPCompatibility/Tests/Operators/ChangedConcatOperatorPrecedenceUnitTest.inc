<?php

// OK: Parentheses used, not an issue.
echo ("sum: " . $a) + $b;
echo "sum :" . ($a + $b);

// OK: Not an issue as both are left-associative.
echo $a + $b . 'text';

// OK: Lower precedence operator preventing an issue.
$b = 'test' . $a = 10 + 20;
$b = 'test' . $a & 10 + 20;

// OK: Not in the same statement.
$a = 'text' . 10;
echo $a + 20;
?>
<?= 'Text' . 'Text' ?><div><?= $a + 20 ?>
<?php

$array = array(
    1 => 'text' . 'text',
    2 => 1 + 2,
);

$short = [
    'text' . 'text',
    1 + 2,
];

echo 'text'.'text', 10 + 20;

switch ( $a . 's' ) :
    case 10 + 20:
        break;
    case 's' . 't':
        echo 10 + 20;
        break;
endswitch;

$b = $a . ${$b + 10};

if () {$a . $b} // Intentional parse error, missing semi-colon.
echo 10 + 10;

// OK: unary plus/minus.
echo $a . -10;
echo $a . +$b;

// OK: concat or plus/minus in various brackets.
echo $a[ '_' . $b ] + 20;
$a = array( 'a'.'b' ) + $anotherArray;
return basename('X'.$splited[count($splited) - 1], $suffix);

// OK: not the same nesting level.
$a = 'Text' . implode( '', array( 'a'.'b' ) + $anotherArray );

// Affected by PHP 7.4 changed concat precedence.
echo "sum: " . $a + $b;
$b = 'test' . $a * 10 + 20;
$b = 'test' . ${$a} * 10 + 20;

/*
 * Additional real-world test cases.
 * Source concat_results_top2000: https://gist.github.com/nikic/a4df3e8e18c7955c2c21cf6cdb4cbfaa
 */
// (BUG!) /home/nikic/package-analysis/sources/johnpbloch/wordpress-core/wp-admin/includes/class-wp-ajax-upgrader-skin.php:100
$this->errors->add( 'unknown_upgrade_error_' . $errors_count + 1, $string );

// (BUG!) /home/nikic/package-analysis/sources/microsoft/azure-storage/tests/Functional/File/FileServiceFunctionalTest.php:1005
$this->assertTrue(
    count($ret->getFiles()) + count($ret->getDirectories()) <= $options->getMaxResults(),
    'when NextMarker (\'' . $ret->getNextMarker() .
    '\')==\'\', Files length (' .
    count($ret->getFiles()) + count($ret->getDirectories()) .
        ') should be <= MaxResults (' .
        $options->getMaxResults() . ')'
);

// (BUG!) /home/nikic/package-analysis/sources/microsoft/azure-storage/tests/Functional/File/FileServiceFunctionalTest.php:1015
$this->assertEquals(
    $options->getMaxResults(),
    count($ret->getFiles()) + count($ret->getDirectories()),
    'when NextMarker (' . $ret->getNextMarker() .
        ')!=\'\', Files length (' .
        count($ret->getFiles()) + count($ret->getDirectories()) .
        ') should be == MaxResults (' .
        $options->getMaxResults() .')'
);

// (BUG!) /home/nikic/package-analysis/sources/sabre/vobject/lib/Recur/RRuleIterator.php:344
$this->currentDate = $this->currentDate->modify('+'.$this->interval - 1 .' days');

// (BUG!) /home/nikic/package-analysis/sources/sabre/vobject/lib/Recur/RRuleIterator.php:404
$this->currentDate = $this->currentDate->modify('+'.$this->interval - 1 .' weeks');
