<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     rss_date_parse
 * Purpose:  parse rss date into unix epoch
 * Input:    string: rss date
 *			 default_date:  default date if $rss_date is empty
 *
 * NOTE!!!  parse_w3cdtf provided by MagpieRSS's rss_utils.inc
 *          this file needs to be included somewhere in your script
 * -------------------------------------------------------------
 */
 
function smarty_modifier_rss_date_parse ($rss_date, $default_date=null)
{
	if($rss_date != '') {
    	return parse_w3cdtf( $rss_date );
	} elseif (isset($default_date) && $default_date != '') {		
    	return parse_w3cdtf( $default_date );
	} else {
		return;
	}
}




?>
