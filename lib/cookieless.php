<?php  // $Id$
/**
* Enable cookieless sessions by including $CFG->usesid=true;
* in config.php.
* Based on code from php manual by Richard at postamble.co.uk
* Attempts to use cookies if cookies not present then uses session ids attached to all urls and forms to pass session id from page to page.
* If site is open to google, google is given guest access as usual and there are no sessions. No session ids will be attached to urls for googlebot.
* This doesn't require trans_sid to be turned on but this is recommended for better performance
* you should put :
* session.use_trans_sid = 1 
* in your php.ini file and make sure that you don't have a line like this in your php.ini
* session.use_only_cookies = 1
* @author Richard at postamble.co.uk and Jamie Pratt
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
*/
/**
* You won't call this function directly. This function is used to process 
* text buffered by php in an output buffer. All output is run through this function 
* before it is ouput.
* @param string $buffer is the output sent from php
* @return string the output sent to the browser
*/
function sid_ob_rewrite($buffer){
    $replacements = array(
        '/(<\s*(a|link|script|frame|area)\s[^>]*(href|src)\s*=\s*")([^"]*)(")/i',
        '/(<\s*(a|link|script|frame|area)\s[^>]*(href|src)\s*=\s*\')([^\']*)(\')/i');
 
    $buffer = preg_replace_callback($replacements, "sid_rewrite_link_tag", $buffer);
    $buffer = preg_replace('/<form\s[^>]*>/i',
        '\0<input type="hidden" name="' . session_name() . '" value="' . session_id() . '"/>', $buffer);
    
      return $buffer;
}
/**
* You won't call this function directly. This function is used to process 
* text buffered by php in an output buffer. All output is run through this function 
* before it is ouput.
* This function only processes absolute urls, it is used when we decide that 
* php is processing other urls itself but needs some help with internal absolute urls still.
* @param string $buffer is the output sent from php
* @return string the output sent to the browser
*/
function sid_ob_rewrite_absolute($buffer){
    $replacements = array(
        '/(<\s*(a|link|script|frame|area)\s[^>]*(href|src)\s*=\s*")((?:http|https)[^"]*)(")/i',
        '/(<\s*(a|link|script|frame|area)\s[^>]*(href|src)\s*=\s*\')((?:http|https)[^\']*)(\')/i');
 
    $buffer = preg_replace_callback($replacements, "sid_rewrite_link_tag", $buffer);
    $buffer = preg_replace('/<form\s[^>]*>/i',
        '\0<input type="hidden" name="' . session_name() . '" value="' . session_id() . '"/>', $buffer);
    return $buffer;
}
/**
* A function to process link, a and script tags found 
* by preg_replace_callback in {@link sid_ob_rewrite($buffer)}.
*/
function sid_rewrite_link_tag($matches){
    $url = $matches[4];
    $url=sid_process_url($url);
    return $matches[1]. $url.$matches[5];
}
/**
* You can call this function directly. This function is used to process 
* urls to add a moodle session id to the url for internal links.
* @param string $url is a url
* @return string the processed url
*/
function sid_process_url($url) {
    global $CFG;
    if ((preg_match('/^(http|https):/i', $url)) // absolute url
        &&  ((stripos($url, $CFG->wwwroot)!==0) && stripos($url, $CFG->httpswwwroot)!==0)) { // and not local one
        return $url; //don't attach sessid to non local urls
    }
    if ($url[0]=='#' || (stripos($url, 'javascript:')===0)) {
        return $url; //don't attach sessid to anchors
    }
    if (strpos($url, session_name())!==FALSE)
    {
        return $url; //don't attach sessid to url that already has one sessid
    }
    if (strpos($url, "?")===FALSE){
        $append="?".strip_tags(session_name() . '=' . session_id() );
    }    else {
        $append="&amp;".strip_tags(session_name() . '=' . session_id() );
    }
    //put sessid before any anchor
    $p = strpos($url, "#");
    if($p!==FALSE){
        $anch = substr($url, $p);
        $url = substr($url, 0, $p).$append.$anch ;
    } else  {
        $url .= $append ;
    }
    return $url;    
}


/**
* Call this function before there has been any output to the browser to
* buffer output and add session ids to all internal links.
*/
function sid_start_ob(){
    global $CFG;
    //don't attach sess id for bots

    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        if (!empty($CFG->opentogoogle)) {
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Googlebot') !== false ) {
                @ini_set('session.use_trans_sid', '0'); // try and turn off trans_sid
                $CFG->usesid=false;
                return;
            }
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'google.com') !== false ) {
                @ini_set('session.use_trans_sid', '0'); // try and turn off trans_sid
                $CFG->usesid=false;
                return;
            }
        }
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'W3C_Validator') !== false ) {
            @ini_set('session.use_trans_sid', '0'); // try and turn off trans_sid
            $CFG->usesid=false;
            return;
        }
    }
    @ini_set('session.use_trans_sid', '1'); // try and turn on trans_sid
    if (ini_get('session.use_trans_sid')!=0 ){ 
        // use trans sid as its available
        ini_set('url_rewriter.tags', 'a=href,area=href,script=src,link=href,' 
            . 'frame=src,form=fakeentry');
        ob_start('sid_ob_rewrite_absolute');
    }else{
        //rewrite all links ourselves
        ob_start('sid_ob_rewrite');
    }
}
?>
