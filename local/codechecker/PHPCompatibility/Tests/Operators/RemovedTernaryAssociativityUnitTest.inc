<?php

return $a == 1 ? 'one'
     : $a == 2 ? 'two'
     : $a == 3 ? 'three'
     : $a == 4 ? 'four'
               : 'other'; // Deprecated.

return $a == 1 ? 'one'
     : ($a == 2 ? 'two'
     : ($a == 3 ? 'three'
     : ($a == 4 ? 'four'
               : 'other'))); // OK.

return ((($a == 1 ? 'one'
     : $a == 2) ? 'two'
     : $a == 3) ? 'three'
     : $a == 4) ? 'four'
               : 'other'; // OK.

return $a == 1 ? 'one'
     : ($a == 2 ? 'two'
     : $a == 3 ? 'three'
     : ($a == 4 ? 'four'
               : 'other')); // Deprecated, not all ternaries in parentheses.

return (($a == 1 ? 'one'
     : $a == 2) ? 'two'
     : $a == 3 ? 'three'
     : $a == 4) ? 'four'
               : 'other'; // Deprecated, not all ternaries in parentheses.

// In PHP 7.4 using nested ternaries without explicit parentheses will throw a deprecation warning. In PHP 8.0 it will become a compile-time error instead.
$d = 1 ? 2 : 3 ? 4 : 5;   // Deprecated.
$d = (1 ? 2 : 3) ? 4 : 5; // OK.
$d = 1 ? 2 : (3 ? 4 : 5); // OK.

// PHP 7.4: This also applies when mixing short and long ternary syntax:

$d = 1 ?: 2 ? 3 : 4;   // Deprecated.
$d = (1 ?: 2) ? 3 : 4; // OK.
$d = 1 ?: (2 ? 3 : 4); // OK.

$d = 1 ? 2 : 3 ?: 4;   // Deprecated.
$d = (1 ? 2 : 3) ?: 4; // OK.
$d = 1 ? 2 : (3 ?: 4); // OK.

// PHP 7.4: as an exception, explicit parenthesis are not required when combining two short ternaries:
$d = 1 ?: 2 ?: 3;   // OK.
$d = (1 ?: 2) ?: 3; // OK.
$d = 1 ?: (2 ?: 3); // OK.

// PHP 7.4: Parentheses are also not required when nesting into the middle operand, as this is always unambiguous and not affected by associativity:
$d = 1 ? 2 ? 3 : 4 : 5; // OK.
$d = 1 ? 2 ?: 3 : 4; // OK.

// More test cases.
$d = $a ? ( $x ? $y : $z ) : $b ? $c : $d; // Deprecated.
?>
<div class="<?php echo $a ? $b : $c ? $d : $e ? $f : $g ?>"></div><!-- Deprecated x2-->
<?php

$array = true ? array( $a ? $b : $c ) : array( $d ? $e : $f ); // OK.
$array = true ? [ $a ? $b : $c ] : [ $d ? $e : $f ]; // OK.

$closure = $a ? function() { return $a ? $b : $c; } : function() { return $d ? $e : $f; } ? function() { return $g ? $h : $i; } : function() { return $j ? $k : $l; } ; // Deprecated.

$closure = ( $a ? function() { return $a ? $b : $c; } : function() { return $d ? $e : $f; } ) ? function() { return $g ? $h : $i; } : function() { return $j ? $k : $l; } ; // OK.

// Logical operators have a lower precedence than ternary.
$logical = true ? true : false and false ? true : false;

/*
 * Additional real-world test cases.
 * Source ternary_top1000: https://gist.github.com/nikic/b6214f87b0e4a7c6fe26919ac849194f
 */
// (possibly okay) /home/nikic/package-analysis/sources/symfony/routing/Matcher/Dumper/CompiledUrlMatcherDumper.php:225
$compiledRoutes[$url][] = $this->compileRoute($route, $name, !$route->compile()->getHostVariables() ? $route->getHost() : $route->compile()->getHostRegex() ?: null, $hasTrailingSlash, false, $conditions);

// (BUG!) /home/nikic/package-analysis/sources/symfony/symfony/src/Symfony/Component/Messenger/DependencyInjection/MessengerPass.php:113
$messageLocation = isset($tag['handles']) ? 'declared in your tag attribute "handles"' : $r->implementsInterface(MessageSubscriberInterface::class) ? sprintf('returned by method "%s::getHandledMessages()"', $r->getName()) : sprintf('used as argument type in method "%s::%s()"', $r->getName(), $method);

// (BUG!) /home/nikic/package-analysis/sources/symfony/symfony/src/Symfony/Component/Messenger/DependencyInjection/MessengerPass.php:126
$messageLocation = isset($tag['handles']) ? 'declared in your tag attribute "handles"' : $r->implementsInterface(MessageSubscriberInterface::class) ? sprintf('returned by method "%s::getHandledMessages()"', $r->getName()) : sprintf('used as argument type in method "%s::%s()"', $r->getName(), $method);

// (possibly okay) /home/nikic/package-analysis/sources/ekino/newrelic-bundle/NewRelic/Config.php:32
$this->name = !empty($name) ? $name : \ini_get('newrelic.appname') ?: '';

// (possibly okay) /home/nikic/package-analysis/sources/ekino/newrelic-bundle/NewRelic/Config.php:34
$this->licenseKey = !empty($licenseKey) ? $licenseKey : \ini_get('newrelic.license') ?: '';

// (BUG!) /home/nikic/package-analysis/sources/kartik-v/yii2-grid/src/ColumnTrait.php:274
$curr = is_array($this->format) && isset($this->format[1]) ? $this->format[1] :
    isset($formatter->currencyCode) ? $formatter->currencyCode . ' ' : '';

// (BUG!) /home/nikic/package-analysis/sources/zendframework/zendframework1/library/Zend/Service/Console/Command.php:223
$handlerDescription = isset($handlerDescriptions[$hi]) ? $handlerDescriptions[$hi] : isset($handlerDescriptions[0]) ? $handlerDescriptions[0] : '';

// (BUG!) /home/nikic/package-analysis/sources/hoa/compiler/Bin/Pp.php:232
            printf(
                $format,
                $i,
                $token['namespace'],
                $token['token'],
                30 < $token['length']
                    ? mb_substr($token['value'], 0, 29) . '…'
                    : 'EOF' === $token['token']
                        ? str_repeat(' ', 30)
                        : $token['value'] .
                          str_repeat(' ', 30 - $token['length']),
                $token['offset']
            );

// (BUG!) /home/nikic/package-analysis/sources/jms/serializer/src/SerializationContext.php:152
return $this->initialType
    ? $this->initialType
    : $this->hasAttribute('initial_type') ? $this->getAttribute('initial_type') : null;

// (BUG!) /home/nikic/package-analysis/sources/psy/psysh/src/Formatter/SignatureFormatter.php:290
$value     = \is_array($value) ? 'array()' : \is_null($value) ? 'null' : \var_export($value, true);

// (BUG!) /home/nikic/package-analysis/sources/nesbot/carbon/src/Carbon/Lang/ga.php:69
return $number.($number === 1 ? 'd' : $number % 10 === 2 ? 'na' : 'mh');

// (BUG!) /home/nikic/package-analysis/sources/respect/validation/library/Rules/Cnh.php:59
$check = $dv2 < 0 ? $dv2 + 11 : $dv2 > 9 ? 0 : $dv2;
