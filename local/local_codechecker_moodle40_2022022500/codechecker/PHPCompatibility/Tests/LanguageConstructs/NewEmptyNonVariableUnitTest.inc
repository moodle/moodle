<?php

// These are ok.
empty($variable);
empty( $variable );
empty($variable[1]);
empty($variable['offset']);
empty($variable[$offset]);
empty(stdClass::$property);
empty($myObject->property);
empty($_GET['var']);
empty(${$variable});
empty(${$variable}->property);
empty($variable /* this is fine*/ );

// These should be all flagged.
empty(($variable));
empty(array($variable));

empty(trim($name));
empty(str_replace('_', '', $variable));
empty($myObject->some_method($variable));
empty(${$variable}->some_method());

empty(SOME_CONSTANT);
empty(null);
empty(true);
empty(123);
empty(123.123);
empty('');
empty(array());
empty(new stdClass);

empty( (int) $variable );
empty( $variable + 0 );
empty( $variable . '' );
empty( $variableA && $variableB );
empty( $variableA > $variableB );
empty( $variableA = $variableB );
empty( $variableA === $variableB );

empty();
empty( /*comment*/ );


// Issue #210 + some extra variants - should all be ok as well.
empty(${$a . $b });
empty($a[$b . 'c']);
empty($a->{$b . 'c'});
empty($a[$b->c()]);
empty($a[$b->{$c}()]);
empty($a[$b + 2]);
empty($a->{$b + 2});
empty($a[$b && $c]);
empty($a->{$b && $c});
empty($a[(int) $b]);
empty($a->{(string) $c});
empty(a::${$b});
empty($a::${$b});
empty(a::${$b . 'c'});
empty($a::${$b . 'c'});


// Unclosed - live coding, don't examine.
empty(
