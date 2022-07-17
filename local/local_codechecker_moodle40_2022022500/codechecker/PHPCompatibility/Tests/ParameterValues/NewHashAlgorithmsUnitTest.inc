<?php

/**
 * These should all be fine.
 */
hash_file("something"); // Not one of the targetted algorithms.
hash("1st param", "md2"); // Not the right parameter.
hash_init; // Not a function call.

/**
 * These should all be flagged.
 */
hash_file('md2');
hash_file('ripemd256');
hash_file("ripemd320");
hash_file( "salsa10" );

hash_hmac(     "salsa20"      );
hash_hmac_file("snefru256");
hash_init(   'sha224'  );

hash("joaat");
hash("fnv132", "2nd param", 3, false);
hash("fnv164");

hash_pbkdf2('gost-crypto');

hash_file('sha512/224');
hash_hmac_file('sha512/256');
hash_hmac('sha3-224');
hash_init('sha3-256');
hash_pbkdf2('sha3-384');
hash('sha3-512');
hash('crc32c');
