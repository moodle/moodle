<?php

// OK.
$hash = password_hash( $password, PASSWORD_DEFAULT, $options );
$hash = password_hash( $password, PASSWORD_BCRYPT, $options );
$hash = password_needs_rehash( $password, PASSWORD_ARGON2I, $options );
$hash = password_hash(
	$password,
	// comment.
	PASSWORD_ARGON2ID,
	$options
);

// Undetermined. Ignore.
$hash = password_hash( $password, $algo, $options );
$hash = password_hash( $password, $this->get_algo(), $options );
$hash = password_hash( $password, static::ALGO, $options );

// Not OK - error.
$hash = PassWord_hash( $password, null, $options );
$hash = password_hash( $password, +1, $options );
$hash = password_needs_rehash( $password, 2, $options );
$hash = password_hash( $password, 3, $options );
$hash = password_hash( $password, '2y', $options );
$hash = password_HASH( $password, "argon{$type}", $options );
$hash = password_needs_rehash( $password, 'argon2id', $options );
