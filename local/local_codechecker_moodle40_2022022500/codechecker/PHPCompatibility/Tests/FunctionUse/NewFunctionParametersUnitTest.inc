<?php

// This is ok.
dirname( dirname( __FILE__ ) );

/*
 * These should all be flagged.
 * The examples use varying spacing to verify that the sniff
 * works with different spacing choices.
 */
array_filter( $array, $callback, ARRAY_FILTER_USE_BOTH );
array_slice($array, 0, 10, true );
array_unique( $array, SORT_NUMERIC );
assert( $assertion, $description );
base64_decode($str, true);
class_implements('not_loaded', true);
class_parents('not_loaded', true);
clearstatcache(true, 'somefile.txt'); // NB: 2 new params
copy($file, $newfile, $context);
curl_multi_info_read( $mh, $msgs_in_queue );
debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 3 ); // NB: 2 new params
debug_print_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 3 ); // NB: 2 new params
dirname('somewhere', 5);
dns_get_record("php.net", DNS_ANY, $authns, $addtl, true);
fgetcsv($handle, 1000, ',', '"', '`');
fputcsv( $handle, $fields, "," , '"', "`" );
file_get_contents('./people.txt', NULL, NULL, 20, 14); // NB: 2 new params
filter_input_array(INPUT_POST, $args, false);
filter_var_array($data, $args, false);
gettimeofday ( true );
get_html_translation_table(HTML_ENTITIES, ENT_QUOTES | ENT_HTML5, 'UTF-8');
get_loaded_extensions(true);
gzcompress('Compress me', 9, ZLIB_ENCODING_GZIP);
gzdeflate($compressed, 9, ZLIB_ENCODING_GZIP);
htmlentities($str, ENT_QUOTES, 'UTF-8', false);
htmlspecialchars('<a href="test">Test</a>', ENT_QUOTES, 'UTF-8', false);
http_build_query($data, '', '&amp;', PHP_QUERY_RFC1738); // NB: 2 new params
idn_to_ascii('t‰st.de', IDNA_CHECK_BIDI,  INTL_IDNA_VARIANT_UTS46, $result); // NB: 2 new params
idn_to_utf8($domain, IDNA_CHECK_BIDI,  INTL_IDNA_VARIANT_UTS46, $result); // NB: 2 new params
imagecolorset($im, $bg, 0, 0, 255, 0);
imagepng ( $image, $to, 7, PNG_ALL_FILTERS ); // NB: 2 new params
imagerotate($source, $degrees, 0, true);
imap_open("{localhost:143}INBOX", "user_id", "password", OP_READONLY, 3, array('DISABLE_AUTHENTICATOR'=>true)); // NB: 2 new params
imap_reopen($mbox, "{imap.example.org:143}INBOX.Sent", OP_READONLY, 3);
ini_get_all('pcre', false);
is_a($WF, 'WidgetFactory', true);
is_subclass_of($WFC, 'WidgetFactory', true);
iterator_to_array($iterator, true);
json_decode($json, false, 512, JSON_BIGINT_AS_STRING); // NB: 2 new params
json_encode($mixed, JSON_FORCE_OBJECT, 100); // NB: 2 new params
memory_get_peak_usage(true);
memory_get_usage(true);
mb_encode_numericentity($str, $convmap, "ISO-8859-1", true);
mb_strrpos( $haystack, $needle, 2, 'UTF-8' );
mssql_connect($server, 'sa', 'phpfi', true);
mysqli_commit( $link, $flags, $name ); // NB: 2 new params
mysqli_rollback( $link, $flags, $name ); // NB: 2 new params
nl2br("Welcome\r\nThis is my HTML document", false);
openssl_decrypt($data, 'aes-256-cbc', $encryption_key, OPENSSL_RAW_DATA, $iv, $tag, $aad);
openssl_encrypt($data, 'aes-256-cbc', $encryption_key, OPENSSL_RAW_DATA, $iv, $tag, $aad, $tag_length);
openssl_pkcs7_verify ( $filename , $flags , $outfilename , $cainfo , $extracerts , $content, $p7bfilename );
openssl_seal($data, $sealed, $ekeys, array($pk1, $pk2), 'RC4', 'iv');
openssl_verify($data, $signature, $public_key_res, OPENSSL_ALGO_SHA1);
parse_ini_file('sample.ini', true, INI_SCANNER_RAW);
parse_url($url, PHP_URL_PATH);
pg_lo_create($database, $id);
pg_lo_import($database, '/tmp/lob.dat', $id);
preg_replace( '`[A-Z]([a-z]+)`', '$1', $subject , -1 , $count );
preg_replace_callback( '`[A-Z]([a-z]+)`' , $callback , $subject , -1 , $count, $flags );
round(1.55, 1, PHP_ROUND_HALF_EVEN);
sem_acquire( $sem_identifier, true );
session_regenerate_id (true);
session_set_cookie_params($lifetime, $path, $domain, false, true );
session_set_save_handler( array($handler, 'open'), array($handler, 'close'), array($handler, 'read'), array($handler, 'write'), array($handler, 'destroy'), array($handler, 'gc'), array($handler, 'create_sid'), array($handler, 'validate_sid'), array($handler, 'update_timestamp') );
session_start(array('bla'));
setcookie('TestCookie', '', time() - 3600, '/~rasmus/', 'example.com', 1, true);
setrawcookie('TestCookie', '', time() - 3600, '/~rasmus/', 'example.com', 1, true);
simplexml_load_file( $filename, 'SimpleXMLElement', 0, '', true );
simplexml_load_string( $xmlString, 'SimpleXMLElement', 0, '', true );
spl_autoload_register($autoload_function,true,true);
stream_context_create($opts, $params);
stream_copy_to_stream($src, $dest1, 1024, 1024);
stream_get_contents( $handle, 1024, 1024 );
stream_wrapper_register("var", "VariableStream", STREAM_IS_URL);
stristr('a', 'bla', true);
strstr('a', 'bla', true);
str_word_count($str, 1, '‡·„Á3');
substr_count($text, 'is', 2, 3); // NB: 2 new params
sybase_connect('SYBASE', '', '', 'utf-8', '', true);
timezone_transitions_get( $dtzObject, $timestamp_begin, $timestamp_end ); // NB: 2 new params
timezone_identifiers_list( $what, $country); // NB: 2 new params
token_get_all('<?php echo; ?>',TOKEN_PARSE);
ucwords($foo, '|');
$data = unserialize($foo, ["allowed_classes" => false]);
get_defined_functions(true);
bcmod( '100', '10', '2' );
get_headers( $url, $format, $context );
getopt($options, $longopts, $optind);
pg_fetch_all( $result, $result_type );
pg_last_notice( $connection, $option );
pg_select ($connection, $table_name, $assoc_array, $options, $result_type);
unpack($format,$data,$offset);
openssl_open( $sealed_data, &$open_data, $env_key, $priv_key_id, $method, $iv);
php_uname ("a");
getenv($varname, true );
ldap_add( $link_identifier, $dn, $entry, $serverctrls );
ldap_compare( $link_identifier, $dn, $attribute, $value, $serverctrls );
ldap_delete( $link_identifier, $dn, $serverctrls );
ldap_list( $link_identifier, $base_dn, $filter, $attributes, $attrsonly, $sizelimit, $timelimit, $deref, $serverctrls );
ldap_mod_add( $link_identifier, $dn, $entry, $serverctrls );
ldap_mod_del( $link_identifier, $dn, $entry, $serverctrls );
ldap_mod_replace( $link_identifier, $dn, $entry, $serverctrls );
ldap_modify_batch( $link_identifier, $dn, $entry, $serverctrls );
ldap_parse_result( $link, $result, &$errcode, &$matcheddn, &$errmsg, &$referrals, &$serverctrls );
ldap_read( $link_identifier, $base_dn, $filter, $attributes, $attrsonly, $sizelimit, $timelimit, $deref, $serverctrls );
ldap_rename( $link_identifier, $dn, $newrdn, $newparent, $deleteoldrdn, $serverctrls );
ldap_search( $link_identifier, $base_dn, $filter, $attributes, $attrsonly, $sizelimit, $timelimit, $deref, $serverctrls );
preg_replace_callback_array($patterns_and_callbacks, $subject, $limit, $count, $flags);
