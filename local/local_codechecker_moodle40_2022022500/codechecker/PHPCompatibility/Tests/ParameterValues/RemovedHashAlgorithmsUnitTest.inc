<?php

/**
 * These should all be fine.
 */
hash_file("something"); // Not one of the targetted algorithms.
hash("1st param", "salsa10"); // Not the right parameter.
hash_hmac; // Not a function call.

/**
 * These should all be flagged.
 */
hash_file("salsa10");
hash_file("salsa20");
hash_file('salsa10');
hash_file('salsa20');

hash_hmac_file("salsa10");
hash_hmac( "salsa20" );
hash_init(   'salsa10'  );

hash("salsa10");
hash("salsa10", "2nd param", 3, false);

hash_pbkdf2('salsa20');
