<?php
/*
 * Test functions which allow negative string offsets since PHP 7.1.
 */

// OK.
mb_ereg_search_setpos();
mb_ereg_search_setpos( 0 );
mb_ereg_search_setpos( /* Some comment. */ );
mb_ereg_search_setpos(
	// Some comment.
	0
	// phpcs:ignore Standard.Category.Sniff -- for reasons.
);
mb_ereg_search_setpos($position);
mb_ereg_search_setpos( -1 + 10 );
mb_ereg_search_setpos( 100 - 10 );
mb_ereg_search_setpos( 100- );

$content = file_get_contents( $filename, false, $context, 0, 100 );
$content = file_get_contents( $filename, false, $context, 100 );

$content = file_get_contents( $filename, false, $context, $offset ); // Not necessarily OK, but we can't determine it.

$content = file_get_contents( $filename, false, $context, ($previous_offset + $chunk_size) ); // Not necessarily OK, but we can't determine it.

// Not OK.
mb_ereg_search_setpos(
	// Some comment.
	-100
	// phpcs:ignore Standard.Category.Sniff -- for reasons.
);

MB_ereg_search_setpos( -     100);

$a = file_get_contents($filename, true, $context, -1024, 1024 );
$a = grapheme_extract($haystack, $size, $extract_type, -10 );
$a = grapheme_stripos($haystack, $needle, -1000 );
$a = grapheme_strpos($haystack, $needle, -3 );
$a = iconv_strpos($haystack, $needle, -052 );
$a = mb_strimwidth($str, -100, -10, '' );
$a = mb_stripos($haystack, $needle, -2 );
$a = mb_strpos($haystack, $needle, -5 );
$a = stripos($haystack, $needle, -42 );
$a = strpos($haystack, $needle, -30 );
$a = substr_count($haystack, $needle, -20, -10 );
$a = Substr_Count($haystack, $needle, -20, 10 );
$a = substr_count($haystack, $needle, 20, -+-+-10 );
