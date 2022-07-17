<?php

/*
 * OK.
 */
$a = iconv($in_charset, $out_charset, $str); // OK: parameters not optional.
$a = iconv($in_charset); // OK: parameters not optional. Will throw fatal "Too few arguments passed" error.

$a = iconv_mime_decode_headers( $encoded_headers, $mode, $charset );
$a = iconv_mime_decode( $encoded_header, $mode, $charset );
$a = iconv_strlen($str, $charset);
$a = iconv_strpos ( $haystack, $needle, 0, 'UTF-8' );
$a = iconv_strrpos($haystack, $needle, $charset );
$a = iconv_substr($str, $offset, $length, ini_get("iconv.internal_encoding"));

$a = mb_check_encoding($var, $encoding);
$a = mb_chr($cp, $encoding);
$a = mb_convert_case( $str, MB_CASE_TITLE, mb_internal_encoding() );
$a = mb_convert_encoding( $str, $to_encoding, $from_encoding );
$a = mb_convert_kana( $str, $option, $encoding );
$a = mb_decode_numericentity($str, $convmap, $encoding, true);
$a = mb_encode_numericentity($str, $convmap, $encoding, true);
$a = mb_ord( $str, $encoding );
$a = mb_scrub( $str, $encoding );
$a = mb_strcut($str, $start, $length, $encoding);
$a = mb_stripos( $haystack, $needle, $offset, $encoding );
$a = mb_stristr( $haystack, $needle, true, $encoding );
$a = mb_strlen($str, $encoding);
$a = mb_strpos( $haystack, $needle, $offset, $encoding );
$a = mb_strrchr( $haystack, $needle, $part, $encoding );
$a = mb_strrichr( $haystack, $needle, $part, $encoding );
$a = mb_strripos( $haystack, $needle, $offset, $encoding );
$a = mb_strrpos( $haystack, $needle, $offset, $encoding );
$a = mb_strstr( $haystack, $needle, true, $encoding );
$a = mb_strtolower( $str, $encoding );
$a = mb_strtoupper( $str, $encoding );
$a = mb_strwidth( $str, $encoding );
$a = mb_substr_count( $haystack, $needle, $encoding );
$a = mb_substr($str, $start, $length, $encoding);

/*
 * Error: parameter not set.
 */
$a = iconv_mime_decode_headers( $encoded_headers, $mode );
$a = iconv_mime_decode( $encoded_header, $mode );
$a = Iconv_Strlen($str);
$a = iconv_strpos ( $haystack, $needle );
$a = iconv_strrpos($haystack, $needle );
$a = iconv_substr($str, $offset);

$a = mb_check_encoding($var);
$a = MB_chr($cp);
$a = mb_convert_case( $str, MB_CASE_UPPER );
$a = mb_convert_encoding( $str, $to_encoding );
$a = mb_convert_kana( $str, $option );
$a = mb_decode_numericentity($str, $convmap);
$a = mb_encode_numericentity($str, $convmap);
$a = mb_ord( $str );
$a = mb_scrub($str);
$a = mb_strcut($str, $start);
$a = mb_stripos( $haystack, $needle );
$a = mb_stristr( $haystack, $needle );
$a = mb_strlen($str);
$a = mb_strpos( $haystack, $needle );
$a = mb_strrchr( $haystack, $needle, $part );
$a = mb_strrichr( $haystack, $needle );
$a = mb_strripos( $haystack, $needle, $offset );
$a = mb_strrpos( $haystack, $needle );
$a = mb_strstr( $haystack, $needle, true );
$a = mb_strtolower($str);
$a = mb_strtoupper( $str );
$a = mb_strwidth( $str );
$a = mb_substr_count( $haystack, $needle );
$a = mb_substr($str, $start);

/*
 * iconv_mime_encode() specific tests.
 */
$a = iconv_mime_encode(
    $field_name,
    $field_value,
    array(
        'scheme'           => 'Q',
        'input-charset'    => 'ISO-8859-1',
        'output-charset'   => 'UTF-8',
        'line-length'      => 80,
        'line-break-chars' => "\n",
    )
); // OK. Parameter set and the expected array keys set as well.

$a = Iconv_Mime_Encode( $field_name, $field_value ); // Error: $preferences not set.
$a = ICONV_mime_encode( $field_name, $field_value, $preferences ); // Warning: unclear whether array keys are set or not.
$a = iconv_mime_encode(
    $field_name,
    $field_value,
    array(
        'scheme'           => 'Q',
        'input-charset'    => 'ISO-8859-1',
        'line-break-chars' => "\n",
    )
); // Error: 'output-charset' not set.

$a = iconv_mime_encode(
    $field_name,
    $field_value,
    [
        'output-charset'   => 'UTF-8',
        'line-length'      => 80,
    ]
); // Error: 'input-charset' not set.
