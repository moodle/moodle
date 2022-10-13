<?php

// These are ok.
preg_match_all('`[a-z]+`', $subject, $matches);
stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_SSLv23_CLIENT);

// These are not.
preg_match_all('`[a-z]+`', $subject);
stream_socket_enable_crypto($fp, true);

bcscale( 10 ); // OK.
bcscale(); // PHP 7.3+.

$ip = getenv('REMOTE_ADDR'); // OK.
$ip = getenv(); // PHP 7.1+

array_push($stack, "apple", "raspberry"); // OK.
array_push(...$stack); // PHP 7.3+

array_unshift($queue, "apple", "raspberry"); // OK.
array_unshift(...$queue); // PHP 7.3+

ftp_fget ($ftp_stream, $handle, $remote_file, $mode, $resumepos ); // OK.
ftp_fget( $ftp_stream, $handle, $remote_file ); // PHP 7.3+
ftp_fput($ftp_stream,$remote_file,$handle); // PHP 7.3+
ftp_get ($ftp_stream, $local_file, $remote_file); // PHP 7.3+
ftp_nb_fget( $ftp_stream,$handle, $remote_file); // PHP 7.3+
ftp_nb_fput($ftp_stream, $remote_file, $handle); // PHP 7.3+
ftp_nb_get( $ftp_stream, $local_file, $remote_file); // PHP 7.3+
ftp_nb_put( $ftp_stream, $remote_file,$local_file); // PHP 7.3+
ftp_put( $ftp_stream, $remote_file, $local_file); // PHP 7.3+
ftp_put ( $ftp_stream, $remote_file, $local_file,$mode,$startpos); // OK.

array_merge($arrays); // OK.
array_merge(); // PHP 7.4+
array_merge_recursive(); // PHP 7.4+
