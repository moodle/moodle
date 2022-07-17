<?php

// OK.
implode();
implode($pieces);
implode(', ', $pieces);
implode( "$glue", $pieces );
implode(PHP_EOL, $pieces);

join();
join( $pieces );
join(', ' /*comment*/, $pieces);
join("$glue", $pieces);
join(DIRECTORY_SEPARATOR, $pieces);

// Undetermined, sniff stays silent.
implode($glue, $pieces);
implode( $settings['glue'], $pieces );
implode( $pieces, $settings['glue'] );
implode( $pieces, get_glue( 'type' ) );
implode( $pieces, $obj->get_glue() );
implode( $pieces, MY_GLUE );
implode( Custom\array_map( 'strtolower', $pieces ), $glue );
implode( $obj->array_map( 'strtolower', $pieces ), $glue );
implode( My_Class::array_map( 'strtolower', $pieces ), $glue );
implode( array_search( $glue_needle, $haystack ), $pieces );

// Not OK.
implode( $pieces, ', ' );
join($pieces, ' ');

implode(array('a', 'b'), $glue);
join( [1, 2] /*comment*/, $glue);

implode(array('a', 'b'), '-' . '|');
join([1, 2], "$glue");
implode($pieces, ('-' . '|'));
implode( $pieces, ( $type === 'a' ? '-' : '|') );

join ($pieces, <<<EOD
text
EOD
);

// Special cased functions.
implode( array_map( 'strtolower', $pieces ), $glue );
implode( array_value( $pieces ), $glue );
implode( explode( ',', $text ), $glue );
implode( compact( $piece1, $piece2, $piece3 ), $glue );

// Special cased PHP native constants.
implode( $pieces, PHP_EOL );
implode( $pieces, DIRECTORY_SEPARATOR );

// Issue #890 - Hardening for ternary as part of second param.
$defaults = [
    'class'    => implode(
        ' ',
        array_filter( [ 'a' => $type === 'a' ? '-' : '|', ] )
    ),
];

// Efficiency, second param doesn't even need to be examined here.
implode( ( $type === 'a' ? '-' : '|' ), (array) $pieces );

// Recognize array casts.
implode( $glue, (array) $object ); // OK.
implode( (array) $object, $glue ); // Error.
