<?php

// Define path to Smarty files (don't forget trailing slash)
// and load library.  (you'll want to change this value)
//
// NOTE:  you can also simply add Smarty to your include path
define('SMARTY_DIR', '/home/kellan/projs/magpierss/scripts/Smarty/');
require_once(SMARTY_DIR.'Smarty.class.php');

// define path to Magpie files and load library
// (you'll want to change this value)
//
// NOTE: you can also simple add MagpieRSS to your include path
define('MAGPIE_DIR', '/home/kellan/projs/magpierss/');
require_once(MAGPIE_DIR.'rss_fetch.inc');
require_once(MAGPIE_DIR.'rss_utils.inc');


// optionally show lots of debugging info
# define('MAGPIE_DEBUG', 2);

// optionally flush cache quickly for debugging purposes, 
// don't do this on a live site
# define('MAGPIE_CACHE_AGE', 10);

// use cache?  default is yes.  see rss_fetch for other Magpie options
# define('MAGPIE_CACHE_ON', 1)

// setup template object
$smarty = new Smarty;
$smarty->compile_check = true;

// url of an rss file
$url = $_GET['rss_url'];


if ( $url ) {
	// assign a variable to smarty for use in the template
	$smarty->assign('rss_url', $url);
	
	// use MagpieRSS to fetch remote RSS file, and parse it
	$rss = fetch_rss( $url );
	
	// if fetch_rss returned false, we encountered an error
	if ( !$rss ) {
		$smarty->assign( 'error', magpie_error() );
	}
	$smarty->assign('rss', $rss );
	
	$item = $rss->items[0];
	$date = parse_w3cdtf( $item['dc']['date'] );
	$smarty->assign( 'date', $date );
}

// parse smarty template, and display using the variables we assigned
$smarty->display('simple.smarty');

?>
