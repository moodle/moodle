<?php //$Id$

define('BYTESERVING_BOUNDARY', 's1k2o3d4a5k6s7'); //unique string constant

function get_file_url($path, $options=null, $type='coursefile') {
    global $CFG, $HTTPSPAGEREQUIRED;

    $path = str_replace('//', '/', $path);
    $path = trim($path, '/'); // no leading and trailing slashes

    // type of file
    switch ($type) {
       case 'questionfile':
            $url = $CFG->wwwroot."/question/exportfile.php";
            break;
       case 'rssfile':
            $url = $CFG->wwwroot."/rss/file.php";
            break;
        case 'user':
            if (!empty($HTTPSPAGEREQUIRED)) {
                $wwwroot = $CFG->httpswwwroot;
            }
            else {
                $wwwroot = $CFG->wwwroot;
            }
            $url = $wwwroot."/user/pix.php";
            break;
        case 'usergroup':
            $url = $CFG->wwwroot."/user/pixgroup.php";
            break;
        case 'httpscoursefile':
            $url = $CFG->httpswwwroot."/file.php";
            break;
         case 'coursefile':
        default:
            $url = $CFG->wwwroot."/file.php";
    }

    if ($CFG->slasharguments) {
        $parts = explode('/', $path);
        foreach ($parts as $key => $part) {
        /// anchor dash character should not be encoded
            $subparts = explode('#', $part);
            $subparts = array_map('rawurlencode', $subparts);
            $parts[$key] = implode('#', $subparts);
        }
        $path  = implode('/', $parts);
        $ffurl = $url.'/'.$path;
        $separator = '?';
    } else {
        $path = rawurlencode('/'.$path);
        $ffurl = $url.'?file='.$path;
        $separator = '&amp;';
    }

    if ($options) {
        foreach ($options as $name=>$value) {
            $ffurl = $ffurl.$separator.$name.'='.$value;
            $separator = '&amp;';
        }
    }

    return $ffurl;
}

/**
 * Fetches content of file from Internet (using proxy if defined). Uses cURL extension if present.
 * Due to security concerns only downloads from http(s) sources are supported.
 *
 * @param string $url file url starting with http(s)://
 * @param array $headers http headers, null if none. If set, should be an
 *   associative array of header name => value pairs.
 * @param array $postdata array means use POST request with given parameters
 * @param bool $fullresponse return headers, responses, etc in a similar way snoopy does
 *   (if false, just returns content)
 * @param int $timeout timeout for complete download process including all file transfer
 *   (default 5 minutes)
 * @param int $connecttimeout timeout for connection to server; this is the timeout that
 *   usually happens if the remote server is completely down (default 20 seconds);
 *   may not work when using proxy
 * @param bool $skipcertverify If true, the peer's SSL certificate will not be checked. Only use this when already in a trusted location.
 * @return mixed false if request failed or content of the file as string if ok.
 */
function download_file_content($url, $headers=null, $postdata=null, $fullresponse=false, $timeout=300, $connecttimeout=20, $skipcertverify=false) {
    global $CFG;

    // some extra security
    $newlines = array("\r", "\n");
    if (is_array($headers) ) {
        foreach ($headers as $key => $value) {
            $headers[$key] = str_replace($newlines, '', $value);
        }
    }
    $url = str_replace($newlines, '', $url);
    if (!preg_match('|^https?://|i', $url)) {
        if ($fullresponse) {
            $response = new object();
            $response->status        = 0;
            $response->headers       = array();
            $response->response_code = 'Invalid protocol specified in url';
            $response->results       = '';
            $response->error         = 'Invalid protocol specified in url';
            return $response;
        } else {
            return false;
        }
    }


    if (!extension_loaded('curl') or ($ch = curl_init($url)) === false) {
        require_once($CFG->libdir.'/snoopy/Snoopy.class.inc');
        $snoopy = new Snoopy();
        $snoopy->read_timeout = $timeout;
        $snoopy->_fp_timeout  = $connecttimeout;
        $snoopy->proxy_host   = $CFG->proxyhost;
        $snoopy->proxy_port   = $CFG->proxyport;
        if (!empty($CFG->proxyuser) and !empty($CFG->proxypassword)) {
            // this will probably fail, but let's try it anyway
            $snoopy->proxy_user     = $CFG->proxyuser;
            $snoopy->proxy_password = $CFG->proxypassword;
        }
        if (is_array($headers) ) {
            $client->rawheaders = $headers;
        }

        if (is_array($postdata)) {
            $fetch = @$snoopy->fetch($url, $postdata); // use more specific debug code bellow
        } else {
            $fetch = @$snoopy->fetch($url); // use more specific debug code bellow
        }

        if ($fetch) {
            if ($fullresponse) {
                //fix header line endings
                foreach ($snoopy->headers as $key=>$unused) {
                    $snoopy->headers[$key] = trim($snoopy->headers[$key]);
                }
                $response = new object();
                $response->status        = $snoopy->status;
                $response->headers       = $snoopy->headers;
                $response->response_code = trim($snoopy->response_code);
                $response->results       = $snoopy->results;
                $response->error         = $snoopy->error;
                return $response;

            } else if ($snoopy->status != 200) {
                debugging("Snoopy request for \"$url\" failed, http response code: ".$snoopy->response_code, DEBUG_ALL);
                return false;

            } else {
                return $snoopy->results;
            }
        } else {
            if ($fullresponse) {
                $response = new object();
                $response->status        = $snoopy->status;
                $response->headers       = array();
                $response->response_code = $snoopy->response_code;
                $response->results       = '';
                $response->error         = $snoopy->error;
                return $response;
            } else {
                debugging("Snoopy request for \"$url\" failed with: ".$snoopy->error, DEBUG_ALL);
                return false;
            }
        }
    }

    // set extra headers
    if (is_array($headers) ) {
        $headers2 = array();
        foreach ($headers as $key => $value) {
            $headers2[] = "$key: $value";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers2);
    }

    if ($skipcertverify) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }

    // use POST if requested
    if (is_array($postdata)) {
        foreach ($postdata as $k=>$v) {
            $postdata[$k] = urlencode($k).'='.urlencode($v);
        }
        $postdata = implode('&', $postdata);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connecttimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    if (!ini_get('open_basedir') and !ini_get('safe_mode')) {
        // TODO: add version test for '7.10.5'
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    }

    if (!empty($CFG->proxyhost)) {
        // SOCKS supported in PHP5 only
        if (!empty($CFG->proxytype) and ($CFG->proxytype == 'SOCKS5')) {
            if (defined('CURLPROXY_SOCKS5')) {
                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            } else {
                curl_close($ch);
                if ($fullresponse) {
                    $response = new object();
                    $response->status        = '0';
                    $response->headers       = array();
                    $response->response_code = 'SOCKS5 proxy is not supported in PHP4';
                    $response->results       = '';
                    $response->error         = 'SOCKS5 proxy is not supported in PHP4';
                    return $response;
                } else {
                    debugging("SOCKS5 proxy is not supported in PHP4.", DEBUG_ALL);
                    return false;
                }
            }
        }

        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, false);

        if (empty($CFG->proxyport)) {
            curl_setopt($ch, CURLOPT_PROXY, $CFG->proxyhost);
        } else {
            curl_setopt($ch, CURLOPT_PROXY, $CFG->proxyhost.':'.$CFG->proxyport);
        }

        if (!empty($CFG->proxyuser) and !empty($CFG->proxypassword)) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $CFG->proxyuser.':'.$CFG->proxypassword);
            if (defined('CURLOPT_PROXYAUTH')) {
                // any proxy authentication if PHP 5.1
                curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC | CURLAUTH_NTLM);
            }
        }
    }

    $data = curl_exec($ch);

    // try to detect encoding problems
    if ((curl_errno($ch) == 23 or curl_errno($ch) == 61) and defined('CURLOPT_ENCODING')) {
        curl_setopt($ch, CURLOPT_ENCODING, 'none');
        $data = curl_exec($ch);
    }

    if (curl_errno($ch)) {
        $error    = curl_error($ch);
        $error_no = curl_errno($ch);
        curl_close($ch);

        if ($fullresponse) {
            $response = new object();
            if ($error_no == 28) {
                $response->status    = '-100'; // mimic snoopy
            } else {
                $response->status    = '0';
            }
            $response->headers       = array();
            $response->response_code = $error;
            $response->results       = '';
            $response->error         = $error;
            return $response;
        } else {
            debugging("cURL request for \"$url\" failed with: $error ($error_no)", DEBUG_ALL);
            return false;
        }

    } else {
        $info = curl_getinfo($ch);
        curl_close($ch);

        if (empty($info['http_code'])) {
            // for security reasons we support only true http connections (Location: file:// exploit prevention)
            $response = new object();
            $response->status        = '0';
            $response->headers       = array();
            $response->response_code = 'Unknown cURL error';
            $response->results       = ''; // do NOT change this!
            $response->error         = 'Unknown cURL error';

        } else {
            // strip redirect headers and get headers array and content
            $data = explode("\r\n\r\n", $data, $info['redirect_count'] + 2);
            $results = array_pop($data);
            $headers = array_pop($data);
            $headers = explode("\r\n", trim($headers));

            $response = new object();;
            $response->status        = (string)$info['http_code'];
            $response->headers       = $headers;
            $response->response_code = $headers[0];
            $response->results       = $results;
            $response->error         = '';
        }

        if ($fullresponse) {
            return $response;
        } else if ($info['http_code'] != 200) {
            debugging("cURL request for \"$url\" failed, HTTP response code: ".$response->response_code, DEBUG_ALL);
            return false;
        } else {
            return $response->results;
        }
    }
}

/**
 * @return List of information about file types based on extensions.
 *   Associative array of extension (lower-case) to associative array
 *   from 'element name' to data. Current element names are 'type' and 'icon'.
 *   Unknown types should use the 'xxx' entry which includes defaults.
 */
function get_mimetypes_array() {
    return array (
        'xxx'  => array ('type'=>'document/unknown', 'icon'=>'unknown.gif'),
        '3gp'  => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
        'ai'   => array ('type'=>'application/postscript', 'icon'=>'image.gif'),
        'aif'  => array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
        'aiff' => array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
        'aifc' => array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
        'applescript'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'asc'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'asm'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'au'   => array ('type'=>'audio/au', 'icon'=>'audio.gif'),
        'avi'  => array ('type'=>'video/x-ms-wm', 'icon'=>'avi.gif'),
        'bmp'  => array ('type'=>'image/bmp', 'icon'=>'image.gif'),
        'c'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'cct'  => array ('type'=>'shockwave/director', 'icon'=>'flash.gif'),
        'cpp'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'cs'   => array ('type'=>'application/x-csh', 'icon'=>'text.gif'),
        'css'  => array ('type'=>'text/css', 'icon'=>'text.gif'),
        'csv'  => array ('type'=>'text/csv', 'icon'=>'excel.gif'),
        'dv'   => array ('type'=>'video/x-dv', 'icon'=>'video.gif'),
        'dmg'  => array ('type'=>'application/octet-stream', 'icon'=>'dmg.gif'),

        'doc'  => array ('type'=>'application/msword', 'icon'=>'word.gif'),
        'docx' => array ('type'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'icon'=>'docx.gif'),
        'docm' => array ('type'=>'application/vnd.ms-word.document.macroEnabled.12', 'icon'=>'docm.gif'),
        'dotx' => array ('type'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.template', 'icon'=>'dotx.gif'),
        'dotm' => array ('type'=>'application/vnd.ms-word.template.macroEnabled.12', 'icon'=>'dotm.gif'),

        'dcr'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'dif'  => array ('type'=>'video/x-dv', 'icon'=>'video.gif'),
        'dir'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'dxr'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'eps'  => array ('type'=>'application/postscript', 'icon'=>'pdf.gif'),
        'fdf'  => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
        'flv'  => array ('type'=>'video/x-flv', 'icon'=>'video.gif'),
        'gif'  => array ('type'=>'image/gif', 'icon'=>'image.gif'),
        'gtar' => array ('type'=>'application/x-gtar', 'icon'=>'zip.gif'),
        'tgz'  => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
        'gz'   => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
        'gzip' => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
        'h'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'hpp'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'hqx'  => array ('type'=>'application/mac-binhex40', 'icon'=>'zip.gif'),
        'htc'  => array ('type'=>'text/x-component', 'icon'=>'text.gif'),
        'html' => array ('type'=>'text/html', 'icon'=>'html.gif'),
        'xhtml'=> array ('type'=>'application/xhtml+xml', 'icon'=>'html.gif'),
        'htm'  => array ('type'=>'text/html', 'icon'=>'html.gif'),
        'ico'  => array ('type'=>'image/vnd.microsoft.icon', 'icon'=>'image.gif'),
        'ics'  => array ('type'=>'text/calendar', 'icon'=>'text.gif'),
        'isf'  => array ('type'=>'application/inspiration', 'icon'=>'isf.gif'),
        'ist'  => array ('type'=>'application/inspiration.template', 'icon'=>'isf.gif'),
        'java' => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'jcb'  => array ('type'=>'text/xml', 'icon'=>'jcb.gif'),
        'jcl'  => array ('type'=>'text/xml', 'icon'=>'jcl.gif'),
        'jcw'  => array ('type'=>'text/xml', 'icon'=>'jcw.gif'),
        'jmt'  => array ('type'=>'text/xml', 'icon'=>'jmt.gif'),
        'jmx'  => array ('type'=>'text/xml', 'icon'=>'jmx.gif'),
        'jpe'  => array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
        'jpeg' => array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
        'jpg'  => array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
        'jqz'  => array ('type'=>'text/xml', 'icon'=>'jqz.gif'),
        'js'   => array ('type'=>'application/x-javascript', 'icon'=>'text.gif'),
        'latex'=> array ('type'=>'application/x-latex', 'icon'=>'text.gif'),
        'm'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'mov'  => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
        'movie'=> array ('type'=>'video/x-sgi-movie', 'icon'=>'video.gif'),
        'm3u'  => array ('type'=>'audio/x-mpegurl', 'icon'=>'audio.gif'),
        'mp3'  => array ('type'=>'audio/mp3', 'icon'=>'audio.gif'),
        'mp4'  => array ('type'=>'video/mp4', 'icon'=>'video.gif'),
        'm4v'  => array ('type'=>'video/mp4', 'icon'=>'video.gif'),
        'm4a'  => array ('type'=>'audio/mp4', 'icon'=>'audio.gif'),
        'mpeg' => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),
        'mpe'  => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),
        'mpg'  => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),

        'odt'  => array ('type'=>'application/vnd.oasis.opendocument.text', 'icon'=>'odt.gif'),
        'ott'  => array ('type'=>'application/vnd.oasis.opendocument.text-template', 'icon'=>'odt.gif'),
        'oth'  => array ('type'=>'application/vnd.oasis.opendocument.text-web', 'icon'=>'odt.gif'),
        'odm'  => array ('type'=>'application/vnd.oasis.opendocument.text-master', 'icon'=>'odm.gif'),
        'odg'  => array ('type'=>'application/vnd.oasis.opendocument.graphics', 'icon'=>'odg.gif'),
        'otg'  => array ('type'=>'application/vnd.oasis.opendocument.graphics-template', 'icon'=>'odg.gif'),
        'odp'  => array ('type'=>'application/vnd.oasis.opendocument.presentation', 'icon'=>'odp.gif'),
        'otp'  => array ('type'=>'application/vnd.oasis.opendocument.presentation-template', 'icon'=>'odp.gif'),
        'ods'  => array ('type'=>'application/vnd.oasis.opendocument.spreadsheet', 'icon'=>'ods.gif'),
        'ots'  => array ('type'=>'application/vnd.oasis.opendocument.spreadsheet-template', 'icon'=>'ods.gif'),
        'odc'  => array ('type'=>'application/vnd.oasis.opendocument.chart', 'icon'=>'odc.gif'),
        'odf'  => array ('type'=>'application/vnd.oasis.opendocument.formula', 'icon'=>'odf.gif'),
        'odb'  => array ('type'=>'application/vnd.oasis.opendocument.database', 'icon'=>'odb.gif'),
        'odi'  => array ('type'=>'application/vnd.oasis.opendocument.image', 'icon'=>'odi.gif'),
        'ogg'  => array ('type'=>'audio/ogg', 'icon'=>'audio.gif'),
        'ogv'  => array ('type'=>'video/ogg', 'icon'=>'video.gif'),

        'pct'  => array ('type'=>'image/pict', 'icon'=>'image.gif'),
        'pdf'  => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
        'php'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'pic'  => array ('type'=>'image/pict', 'icon'=>'image.gif'),
        'pict' => array ('type'=>'image/pict', 'icon'=>'image.gif'),
        'png'  => array ('type'=>'image/png', 'icon'=>'image.gif'),

        'pps'  => array ('type'=>'application/vnd.ms-powerpoint', 'icon'=>'powerpoint.gif'),
        'ppt'  => array ('type'=>'application/vnd.ms-powerpoint', 'icon'=>'powerpoint.gif'),
        'pptx' => array ('type'=>'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'icon'=>'pptx.gif'),
        'pptm' => array ('type'=>'application/vnd.ms-powerpoint.presentation.macroEnabled.12', 'icon'=>'pptm.gif'),
        'potx' => array ('type'=>'application/vnd.openxmlformats-officedocument.presentationml.template', 'icon'=>'potx.gif'),
        'potm' => array ('type'=>'application/vnd.ms-powerpoint.template.macroEnabled.12', 'icon'=>'potm.gif'),
        'ppam' => array ('type'=>'application/vnd.ms-powerpoint.addin.macroEnabled.12', 'icon'=>'ppam.gif'),
        'ppsx' => array ('type'=>'application/vnd.openxmlformats-officedocument.presentationml.slideshow', 'icon'=>'ppsx.gif'),
        'ppsm' => array ('type'=>'application/vnd.ms-powerpoint.slideshow.macroEnabled.12', 'icon'=>'ppsm.gif'),

        'ps'   => array ('type'=>'application/postscript', 'icon'=>'pdf.gif'),
        'qt'   => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
        'ra'   => array ('type'=>'audio/x-realaudio-plugin', 'icon'=>'audio.gif'),
        'ram'  => array ('type'=>'audio/x-pn-realaudio-plugin', 'icon'=>'audio.gif'),
        'rhb'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
        'rm'   => array ('type'=>'audio/x-pn-realaudio-plugin', 'icon'=>'audio.gif'),
        'rtf'  => array ('type'=>'text/rtf', 'icon'=>'text.gif'),
        'rtx'  => array ('type'=>'text/richtext', 'icon'=>'text.gif'),
        'sh'   => array ('type'=>'application/x-sh', 'icon'=>'text.gif'),
        'sit'  => array ('type'=>'application/x-stuffit', 'icon'=>'zip.gif'),
        'smi'  => array ('type'=>'application/smil', 'icon'=>'text.gif'),
        'smil' => array ('type'=>'application/smil', 'icon'=>'text.gif'),
        'sqt'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
        'svg'  => array ('type'=>'image/svg+xml', 'icon'=>'image.gif'),
        'svgz' => array ('type'=>'image/svg+xml', 'icon'=>'image.gif'),
        'swa'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'swf'  => array ('type'=>'application/x-shockwave-flash', 'icon'=>'flash.gif'),
        'swfl' => array ('type'=>'application/x-shockwave-flash', 'icon'=>'flash.gif'),

        'sxw'  => array ('type'=>'application/vnd.sun.xml.writer', 'icon'=>'odt.gif'),
        'stw'  => array ('type'=>'application/vnd.sun.xml.writer.template', 'icon'=>'odt.gif'),
        'sxc'  => array ('type'=>'application/vnd.sun.xml.calc', 'icon'=>'odt.gif'),
        'stc'  => array ('type'=>'application/vnd.sun.xml.calc.template', 'icon'=>'odt.gif'),
        'sxd'  => array ('type'=>'application/vnd.sun.xml.draw', 'icon'=>'odt.gif'),
        'std'  => array ('type'=>'application/vnd.sun.xml.draw.template', 'icon'=>'odt.gif'),
        'sxi'  => array ('type'=>'application/vnd.sun.xml.impress', 'icon'=>'odt.gif'),
        'sti'  => array ('type'=>'application/vnd.sun.xml.impress.template', 'icon'=>'odt.gif'),
        'sxg'  => array ('type'=>'application/vnd.sun.xml.writer.global', 'icon'=>'odt.gif'),
        'sxm'  => array ('type'=>'application/vnd.sun.xml.math', 'icon'=>'odt.gif'),

        'tar'  => array ('type'=>'application/x-tar', 'icon'=>'zip.gif'),
        'tif'  => array ('type'=>'image/tiff', 'icon'=>'image.gif'),
        'tiff' => array ('type'=>'image/tiff', 'icon'=>'image.gif'),
        'tex'  => array ('type'=>'application/x-tex', 'icon'=>'text.gif'),
        'texi' => array ('type'=>'application/x-texinfo', 'icon'=>'text.gif'),
        'texinfo'  => array ('type'=>'application/x-texinfo', 'icon'=>'text.gif'),
        'tsv'  => array ('type'=>'text/tab-separated-values', 'icon'=>'text.gif'),
        'txt'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'wav'  => array ('type'=>'audio/wav', 'icon'=>'audio.gif'),
        'wmv'  => array ('type'=>'video/x-ms-wmv', 'icon'=>'avi.gif'),
        'asf'  => array ('type'=>'video/x-ms-asf', 'icon'=>'avi.gif'),
        'xdp'  => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
        'xfd'  => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
        'xfdf' => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),

        'xls'  => array ('type'=>'application/vnd.ms-excel', 'icon'=>'excel.gif'),
        'xlsx' => array ('type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'icon'=>'xlsx.gif'),
        'xlsm' => array ('type'=>'application/vnd.ms-excel.sheet.macroEnabled.12', 'icon'=>'xlsm.gif'),
        'xltx' => array ('type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.template', 'icon'=>'xltx.gif'),
        'xltm' => array ('type'=>'application/vnd.ms-excel.template.macroEnabled.12', 'icon'=>'xltm.gif'),
        'xlsb' => array ('type'=>'application/vnd.ms-excel.sheet.binary.macroEnabled.12', 'icon'=>'xlsb.gif'),
        'xlam' => array ('type'=>'application/vnd.ms-excel.addin.macroEnabled.12', 'icon'=>'xlam.gif'),

        'xml'  => array ('type'=>'application/xml', 'icon'=>'xml.gif'),
        'xsl'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
        'zip'  => array ('type'=>'application/zip', 'icon'=>'zip.gif')
    );
}

/**
 * Obtains information about a filetype based on its extension. Will
 * use a default if no information is present about that particular
 * extension.
 * @param string $element Desired information (usually 'icon'
 *   for icon filename or 'type' for MIME type)
 * @param string $filename Filename we're looking up
 * @return string Requested piece of information from array
 */
function mimeinfo($element, $filename) {
    static $mimeinfo = null;
    if (is_null($mimeinfo)) {
        $mimeinfo = get_mimetypes_array();
    }

    if (preg_match('/\.([a-zA-Z0-9]+)$/', $filename, $match)) {
        if (isset($mimeinfo[strtolower($match[1])][$element])) {
            return $mimeinfo[strtolower($match[1])][$element];
        } else {
            return $mimeinfo['xxx'][$element];   // By default
        }
    } else {
        return $mimeinfo['xxx'][$element];   // By default
    }
}

/**
 * Obtains information about a filetype based on the MIME type rather than
 * the other way around.
 * @param string $element Desired information (usually 'icon')
 * @param string $mimetype MIME type we're looking up
 * @return string Requested piece of information from array
 */
function mimeinfo_from_type($element, $mimetype) {
    static $mimeinfo;
    $mimeinfo=get_mimetypes_array();

    foreach($mimeinfo as $values) {
        if($values['type']==$mimetype) {
            if(isset($values[$element])) {
                return $values[$element];
            }
            break;
        }
    }
    return $mimeinfo['xxx'][$element]; // Default
}

/**
 * Get information about a filetype based on the icon file.
 * @param string $element Desired information (usually 'icon')
 * @param string $icon Icon file path.
 * @return string Requested piece of information from array
 */
function mimeinfo_from_icon($element, $icon) {
    static $mimeinfo;
    $mimeinfo=get_mimetypes_array();

    if (preg_match("/\/(.*)/", $icon, $matches)) {
        $icon = $matches[1];
    }
    $info = $mimeinfo['xxx'][$element]; // Default
    foreach($mimeinfo as $values) {
        if($values['icon']==$icon) {
            if(isset($values[$element])) {
                $info = $values[$element];
            }
            //No break, for example for 'excel.gif' we don't want 'csv'!
        }
    }
    return $info;
}

/**
 * Obtains descriptions for file types (e.g. 'Microsoft Word document') from the
 * mimetypes.php language file.
 * @param string $mimetype MIME type (can be obtained using the mimeinfo function)
 * @param bool $capitalise If true, capitalises first character of result
 * @return string Text description
 */
function get_mimetype_description($mimetype,$capitalise=false) {
    $result=get_string($mimetype,'mimetypes');
    // Surrounded by square brackets indicates that there isn't a string for that
    // (maybe there is a better way to find this out?)
    if(strpos($result,'[')===0) {
        $result=get_string('document/unknown','mimetypes');
    }
    if($capitalise) {
        $result=ucfirst($result);
    }
    return $result;
}

/**
 * Handles the sending of temporary file to user, download is forced.
 * File is deleted after abort or succesful sending.
 * @param string $path path to file, preferably from moodledata/temp/something; or content of file itself
 * @param string $filename proposed file name when saving file
 * @param bool $path is content of file
 */
function send_temp_file($path, $filename, $pathisstring=false) {
    global $CFG;

    // close session - not needed anymore
    @session_write_close();

    if (!$pathisstring) {
        if (!file_exists($path)) {
            header('HTTP/1.0 404 not found');
            error(get_string('filenotfound', 'error'), $CFG->wwwroot.'/');
        }
        // executed after normal finish or abort
        @register_shutdown_function('send_temp_file_finished', $path);
    }

    //IE compatibiltiy HACK!
    if (ini_get('zlib.output_compression')) {
        ini_set('zlib.output_compression', 'Off');
    }

    // if user is using IE, urlencode the filename so that multibyte file name will show up correctly on popup
    if (check_browser_version('MSIE')) {
        $filename = urlencode($filename);
    }

    $filesize = $pathisstring ? strlen($path) : filesize($path);

    @header('Content-Disposition: attachment; filename='.$filename);
    @header('Content-Length: '.$filesize);
    if (strpos($CFG->wwwroot, 'https://') === 0) { //https sites - watch out for IE! KB812935 and KB316431
        @header('Cache-Control: max-age=10');
        @header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        @header('Pragma: ');
    } else { //normal http - prevent caching at all cost
        @header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        @header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        @header('Pragma: no-cache');
    }
    @header('Accept-Ranges: none'); // Do not allow byteserving

    while (@ob_end_flush()); //flush the buffers - save memory and disable sid rewrite
    if ($pathisstring) {
        echo $path;
    } else {
        readfile_chunked($path);
    }

    die; //no more chars to output
}

/**
 * Internal callnack function used by send_temp_file()
 */
function send_temp_file_finished($path) {
    if (file_exists($path)) {
        @unlink($path);
    }
}

/**
 * Handles the sending of file data to the user's browser, including support for
 * byteranges etc.
 * @param string $path Path of file on disk (including real filename), or actual content of file as string
 * @param string $filename Filename to send
 * @param int $lifetime Number of seconds before the file should expire from caches (default 24 hours)
 * @param int $filter 0 (default)=no filtering, 1=all files, 2=html files only
 * @param bool $pathisstring If true (default false), $path is the content to send and not the pathname
 * @param bool $forcedownload If true (default false), forces download of file rather than view in browser/plugin
 * @param string $mimetype Include to specify the MIME type; leave blank to have it guess the type from $filename
 */
function send_file($path, $filename, $lifetime = 'default' , $filter=0, $pathisstring=false, $forcedownload=false, $mimetype='') {
    global $CFG, $COURSE, $SESSION;

    // MDL-11789, apply $CFG->filelifetime here
    if ($lifetime === 'default') {
        if (!empty($CFG->filelifetime)) {
            $lifetime = $CFG->filelifetime;
        } else {
            $lifetime = 86400;
        }
    }

    // Use given MIME type if specified, otherwise guess it using mimeinfo.
    // IE, Konqueror and Opera open html file directly in browser from web even when directed to save it to disk :-O
    // only Firefox saves all files locally before opening when content-disposition: attachment stated
    $isFF         = check_browser_version('Firefox', '1.5'); // only FF > 1.5 properly tested
    $mimetype     = ($forcedownload and !$isFF) ? 'application/x-forcedownload' :
                         ($mimetype ? $mimetype : mimeinfo('type', $filename));

    // If the file is a Flash file and that the user flash player is outdated return a flash upgrader MDL-20841
    if (!empty($CFG->excludeoldflashclients) && $mimetype == 'application/x-shockwave-flash'&& !empty($SESSION->flashversion)) {
        $userplayerversion = explode('.', $SESSION->flashversion);
        $requiredplayerversion = explode('.', $CFG->excludeoldflashclients);
        if (($userplayerversion[0] <  $requiredplayerversion[0]) ||
            ($userplayerversion[0] == $requiredplayerversion[0] && $userplayerversion[1] < $requiredplayerversion[1]) ||
            ($userplayerversion[0] == $requiredplayerversion[0] && $userplayerversion[1] == $requiredplayerversion[1]
             && $userplayerversion[2] < $requiredplayerversion[2])) {
            $path = $CFG->dirroot."/lib/flashdetect/flashupgrade.swf";  // Alternate content asking user to upgrade Flash
            $filename = "flashupgrade.swf";
            $lifetime = 0;  // Do not cache
        }
    }

    $lastmodified = $pathisstring ? time() : filemtime($path);
    $filesize     = $pathisstring ? strlen($path) : filesize($path);

/* - MDL-13949
    //Adobe Acrobat Reader XSS prevention
    if ($mimetype=='application/pdf' or mimeinfo('type', $filename)=='application/pdf') {
        //please note that it prevents opening of pdfs in browser when http referer disabled
        //or file linked from another site; browser caching of pdfs is now disabled too
        if (!empty($_SERVER['HTTP_RANGE'])) {
            //already byteserving
            $lifetime = 1; // >0 needed for byteserving
        } else if (empty($_SERVER['HTTP_REFERER']) or strpos($_SERVER['HTTP_REFERER'], $CFG->wwwroot)!==0) {
            $mimetype = 'application/x-forcedownload';
            $forcedownload = true;
            $lifetime = 0;
        } else {
            $lifetime = 1; // >0 needed for byteserving
        }
    }
*/

    //IE compatibiltiy HACK!
    if (ini_get('zlib.output_compression')) {
        ini_set('zlib.output_compression', 'Off');
    }

    //try to disable automatic sid rewrite in cookieless mode
    @ini_set("session.use_trans_sid", "false");

    //do not put '@' before the next header to detect incorrect moodle configurations,
    //error should be better than "weird" empty lines for admins/users
    //TODO: should we remove all those @ before the header()? Are all of the values supported on all servers?
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', $lastmodified) .' GMT');

    // if user is using IE, urlencode the filename so that multibyte file name will show up correctly on popup
    if (check_browser_version('MSIE')) {
        $filename = rawurlencode($filename);
    }

    if ($forcedownload) {
        @header('Content-Disposition: attachment; filename="'.$filename.'"');
    } else {
        @header('Content-Disposition: inline; filename="'.$filename.'"');
    }

    if ($lifetime > 0) {
        @header('Cache-Control: max-age='.$lifetime);
        @header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
        @header('Pragma: ');

        if (empty($CFG->disablebyteserving) && !$pathisstring && $mimetype != 'text/plain' && $mimetype != 'text/html') {

            @header('Accept-Ranges: bytes');

            if (!empty($_SERVER['HTTP_RANGE']) && strpos($_SERVER['HTTP_RANGE'],'bytes=') !== FALSE) {
                // byteserving stuff - for acrobat reader and download accelerators
                // see: http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.35
                // inspired by: http://www.coneural.org/florian/papers/04_byteserving.php
                $ranges = false;
                if (preg_match_all('/(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $ranges, PREG_SET_ORDER)) {
                    foreach ($ranges as $key=>$value) {
                        if ($ranges[$key][1] == '') {
                            //suffix case
                            $ranges[$key][1] = $filesize - $ranges[$key][2];
                            $ranges[$key][2] = $filesize - 1;
                        } else if ($ranges[$key][2] == '' || $ranges[$key][2] > $filesize - 1) {
                            //fix range length
                            $ranges[$key][2] = $filesize - 1;
                        }
                        if ($ranges[$key][2] != '' && $ranges[$key][2] < $ranges[$key][1]) {
                            //invalid byte-range ==> ignore header
                            $ranges = false;
                            break;
                        }
                        //prepare multipart header
                        $ranges[$key][0] =  "\r\n--".BYTESERVING_BOUNDARY."\r\nContent-Type: $mimetype\r\n";
                        $ranges[$key][0] .= "Content-Range: bytes {$ranges[$key][1]}-{$ranges[$key][2]}/$filesize\r\n\r\n";
                    }
                } else {
                    $ranges = false;
                }
                if ($ranges) {
                    byteserving_send_file($path, $mimetype, $ranges);
                }
            }
        } else {
            /// Do not byteserve (disabled, strings, text and html files).
            @header('Accept-Ranges: none');
        }
    } else { // Do not cache files in proxies and browsers
        if (strpos($CFG->wwwroot, 'https://') === 0) { //https sites - watch out for IE! KB812935 and KB316431
            @header('Cache-Control: max-age=10');
            @header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
            @header('Pragma: ');
        } else { //normal http - prevent caching at all cost
            @header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
            @header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
            @header('Pragma: no-cache');
        }
        @header('Accept-Ranges: none'); // Do not allow byteserving when caching disabled
    }

    if (empty($filter)) {
        if ($mimetype == 'text/html' && !empty($CFG->usesid) && empty($_COOKIE['MoodleSession'.$CFG->sessioncookie])) {
            //cookieless mode - rewrite links
            @header('Content-Type: text/html');
            $path = $pathisstring ? $path : implode('', file($path));
            $path = sid_ob_rewrite($path);
            $filesize = strlen($path);
            $pathisstring = true;
        } else if ($mimetype == 'text/plain') {
            @header('Content-Type: Text/plain; charset=utf-8'); //add encoding
        } else {
            @header('Content-Type: '.$mimetype);
        }
        @header('Content-Length: '.$filesize);
        while (@ob_end_flush()); //flush the buffers - save memory and disable sid rewrite
        if ($pathisstring) {
            echo $path;
        } else {
            readfile_chunked($path);
        }
    } else {     // Try to put the file through filters
        if ($mimetype == 'text/html') {
            $options = new object();
            $options->noclean = true;
            $options->nocache = true; // temporary workaround for MDL-5136
            $text = $pathisstring ? $path : implode('', file($path));

            $text = file_modify_html_header($text);
            $output = format_text($text, FORMAT_HTML, $options, $COURSE->id);
            if (!empty($CFG->usesid) && empty($_COOKIE['MoodleSession'.$CFG->sessioncookie])) {
                //cookieless mode - rewrite links
                $output = sid_ob_rewrite($output);
            }

            @header('Content-Length: '.strlen($output));
            @header('Content-Type: text/html');
            while (@ob_end_flush()); //flush the buffers - save memory and disable sid rewrite
            echo $output;
        // only filter text if filter all files is selected
        } else if (($mimetype == 'text/plain') and ($filter == 1)) {
            $options = new object();
            $options->newlines = false;
            $options->noclean = true;
            $text = htmlentities($pathisstring ? $path : implode('', file($path)));
            $output = '<pre>'. format_text($text, FORMAT_MOODLE, $options, $COURSE->id) .'</pre>';
            if (!empty($CFG->usesid) && empty($_COOKIE['MoodleSession'.$CFG->sessioncookie])) {
                //cookieless mode - rewrite links
                $output = sid_ob_rewrite($output);
            }

            @header('Content-Length: '.strlen($output));
            @header('Content-Type: text/html; charset=utf-8'); //add encoding
            while (@ob_end_flush()); //flush the buffers - save memory and disable sid rewrite
            echo $output;
        } else {    // Just send it out raw
            @header('Content-Length: '.$filesize);
            @header('Content-Type: '.$mimetype);
            while (@ob_end_flush()); //flush the buffers - save memory and disable sid rewrite
            if ($pathisstring) {
                echo $path;
            }else {
                readfile_chunked($path);
            }
        }
    }
    die; //no more chars to output!!!
}

function get_records_csv($file, $table) {
    global $CFG, $db;

    if (!$metacolumns = $db->MetaColumns($CFG->prefix . $table)) {
        return false;
    }

    if(!($handle = @fopen($file, 'r'))) {
        error('get_records_csv failed to open '.$file);
    }

    $fieldnames = fgetcsv($handle, 4096);
    if(empty($fieldnames)) {
        fclose($handle);
        return false;
    }

    $columns = array();

    foreach($metacolumns as $metacolumn) {
        $ord = array_search($metacolumn->name, $fieldnames);
        if(is_int($ord)) {
            $columns[$metacolumn->name] = $ord;
        }
    }

    $rows = array();

    while (($data = fgetcsv($handle, 4096)) !== false) {
        $item = new stdClass;
        foreach($columns as $name => $ord) {
            $item->$name = $data[$ord];
        }
        $rows[] = $item;
    }

    fclose($handle);
    return $rows;
}

function put_records_csv($file, $records, $table = NULL) {
    global $CFG, $db;

    if (empty($records)) {
        return true;
    }

    $metacolumns = NULL;
    if ($table !== NULL && !$metacolumns = $db->MetaColumns($CFG->prefix . $table)) {
        return false;
    }

    echo "x";

    if(!($fp = @fopen($CFG->dataroot.'/temp/'.$file, 'w'))) {
        error('put_records_csv failed to open '.$file);
    }

    $proto = reset($records);
    if(is_object($proto)) {
        $fields_records = array_keys(get_object_vars($proto));
    }
    else if(is_array($proto)) {
        $fields_records = array_keys($proto);
    }
    else {
        return false;
    }
    echo "x";

    if(!empty($metacolumns)) {
        $fields_table = array_map(create_function('$a', 'return $a->name;'), $metacolumns);
        $fields = array_intersect($fields_records, $fields_table);
    }
    else {
        $fields = $fields_records;
    }

    fwrite($fp, implode(',', $fields));
    fwrite($fp, "\r\n");

    foreach($records as $record) {
        $array  = (array)$record;
        $values = array();
        foreach($fields as $field) {
            if(strpos($array[$field], ',')) {
                $values[] = '"'.str_replace('"', '\"', $array[$field]).'"';
            }
            else {
                $values[] = $array[$field];
            }
        }
        fwrite($fp, implode(',', $values)."\r\n");
    }

    fclose($fp);
    return true;
}


/**
 * Recursively delete the file or folder with path $location. That is,
 * if it is a file delete it. If it is a folder, delete all its content
 * then delete it. If $location does not exist to start, that is not
 * considered an error.
 *
 * @param $location the path to remove.
 */
function fulldelete($location) {
    if (is_dir($location) and !is_link($location)) {
        $currdir = opendir($location);
        while (false !== ($file = readdir($currdir))) {
            if ($file <> ".." && $file <> ".") {
                $fullfile = $location."/".$file;
                if (is_dir($fullfile)) {
                    if (!fulldelete($fullfile)) {
                        return false;
                    }
                } else {
                    if (!unlink($fullfile)) {
                        return false;
                    }
                }
            }
        }
        closedir($currdir);
        if (! rmdir($location)) {
            return false;
        }

    } else if (file_exists($location)) {
        if (!unlink($location)) {
            return false;
        }
    }
    return true;
}

/**
 * Improves memory consumptions and works around buggy readfile() in PHP 5.0.4 (2MB readfile limit).
 */
function readfile_chunked($filename, $retbytes=true) {
    $chunksize = 1*(1024*1024); // 1MB chunks - must be less than 2MB!
    $buffer = '';
    $cnt =0;
    $handle = fopen($filename, 'rb');
    if ($handle === false) {
        return false;
    }

    while (!feof($handle)) {
        @set_time_limit(60*60); //reset time limit to 60 min - should be enough for 1 MB chunk
        $buffer = fread($handle, $chunksize);
        echo $buffer;
        flush();
        if ($retbytes) {
            $cnt += strlen($buffer);
        }
    }
    $status = fclose($handle);
    if ($retbytes && $status) {
        return $cnt; // return num. bytes delivered like readfile() does.
    }
    return $status;
}

/**
 * Send requested byterange of file.
 */
function byteserving_send_file($filename, $mimetype, $ranges) {
    $chunksize = 1*(1024*1024); // 1MB chunks - must be less than 2MB!
    $handle = fopen($filename, 'rb');
    if ($handle === false) {
        die;
    }
    if (count($ranges) == 1) { //only one range requested
        $length = $ranges[0][2] - $ranges[0][1] + 1;
        @header('HTTP/1.1 206 Partial content');
        @header('Content-Length: '.$length);
        @header('Content-Range: bytes '.$ranges[0][1].'-'.$ranges[0][2].'/'.filesize($filename));
        @header('Content-Type: '.$mimetype);
        while (@ob_end_flush()); //flush the buffers - save memory and disable sid rewrite
        $buffer = '';
        fseek($handle, $ranges[0][1]);
        while (!feof($handle) && $length > 0) {
            @set_time_limit(60*60); //reset time limit to 60 min - should be enough for 1 MB chunk
            $buffer = fread($handle, ($chunksize < $length ? $chunksize : $length));
            echo $buffer;
            flush();
            $length -= strlen($buffer);
        }
        fclose($handle);
        die;
    } else { // multiple ranges requested - not tested much
        $totallength = 0;
        foreach($ranges as $range) {
            $totallength += strlen($range[0]) + $range[2] - $range[1] + 1;
        }
        $totallength += strlen("\r\n--".BYTESERVING_BOUNDARY."--\r\n");
        @header('HTTP/1.1 206 Partial content');
        @header('Content-Length: '.$totallength);
        @header('Content-Type: multipart/byteranges; boundary='.BYTESERVING_BOUNDARY);
        //TODO: check if "multipart/x-byteranges" is more compatible with current readers/browsers/servers
        while (@ob_end_flush()); //flush the buffers - save memory and disable sid rewrite
        foreach($ranges as $range) {
            $length = $range[2] - $range[1] + 1;
            echo $range[0];
            $buffer = '';
            fseek($handle, $range[1]);
            while (!feof($handle) && $length > 0) {
                @set_time_limit(60*60); //reset time limit to 60 min - should be enough for 1 MB chunk
                $buffer = fread($handle, ($chunksize < $length ? $chunksize : $length));
                echo $buffer;
                flush();
                $length -= strlen($buffer);
            }
        }
        echo "\r\n--".BYTESERVING_BOUNDARY."--\r\n";
        fclose($handle);
        die;
    }
}

/**
 * add includes (js and css) into uploaded files
 * before returning them, useful for themes and utf.js includes
 * @param string text - text to search and replace
 * @return string - text with added head includes
 */
function file_modify_html_header($text) {
    // first look for <head> tag
    global $CFG;

    $stylesheetshtml = '';
    foreach ($CFG->stylesheets as $stylesheet) {
        $stylesheetshtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />'."\n";
    }

    $filters = explode(",", $CFG->textfilters);
    if (in_array('filter/mediaplugin', $filters)) {
        // this script is needed by most media filter plugins.
        $ufo = "\n".'<script type="text/javascript" src="'.$CFG->wwwroot.'/lib/ufo.js"></script>'."\n";
    } else {
        $ufo = '';
    }

    preg_match('/\<head\>|\<HEAD\>/', $text, $matches);
    if ($matches) {
        $replacement = '<head>'.$ufo.$stylesheetshtml;
        $text = preg_replace('/\<head\>|\<HEAD\>/', $replacement, $text, 1);
        return $text;
    }

    // if not, look for <html> tag, and stick <head> right after
    preg_match('/\<html\>|\<HTML\>/', $text, $matches);
    if ($matches) {
        // replace <html> tag with <html><head>includes</head>
        $replacement = '<html>'."\n".'<head>'.$ufo.$stylesheetshtml.'</head>';
        $text = preg_replace('/\<html\>|\<HTML\>/', $replacement, $text, 1);
        return $text;
    }

    // if not, look for <body> tag, and stick <head> before body
    preg_match('/\<body\>|\<BODY\>/', $text, $matches);
    if ($matches) {
        $replacement = '<head>'.$ufo.$stylesheetshtml.'</head>'."\n".'<body>';
        $text = preg_replace('/\<body\>|\<BODY\>/', $replacement, $text, 1);
        return $text;
    }

    // if not, just stick a <head> tag at the beginning
    $text = '<head>'.$ufo.$stylesheetshtml.'</head>'."\n".$text;
    return $text;
}

?>
