<?php  // $Id$

function mimeinfo($element, $filename) {
    $mimeinfo = array (
        "xxx"  => array ("type"=>"document/unknown", "icon"=>"unknown.gif"),
        "3gp"  => array ("type"=>"video/quicktime", "icon"=>"video.gif"),
        "ai"   => array ("type"=>"application/postscript", "icon"=>"image.gif"),
        "aif"  => array ("type"=>"audio/x-aiff", "icon"=>"audio.gif"),
        "aiff" => array ("type"=>"audio/x-aiff", "icon"=>"audio.gif"),
        "aifc" => array ("type"=>"audio/x-aiff", "icon"=>"audio.gif"),
        "applescript"  => array ("type"=>"text/plain", "icon"=>"text.gif"),
        "asc"  => array ("type"=>"text/plain", "icon"=>"text.gif"),
        "au"   => array ("type"=>"audio/au", "icon"=>"audio.gif"),
        "avi"  => array ("type"=>"video/x-ms-wm", "icon"=>"avi.gif"),
        "bmp"  => array ("type"=>"image/bmp", "icon"=>"image.gif"),
        "cs"   => array ("type"=>"application/x-csh", "icon"=>"text.gif"),
        "css"  => array ("type"=>"text/css", "icon"=>"text.gif"),
        "dv"   => array ("type"=>"video/x-dv", "icon"=>"video.gif"),
        "doc"  => array ("type"=>"application/msword", "icon"=>"word.gif"),
        "dif"  => array ("type"=>"video/x-dv", "icon"=>"video.gif"),
        "eps"  => array ("type"=>"application/postscript", "icon"=>"pdf.gif"),
        "gif"  => array ("type"=>"image/gif", "icon"=>"image.gif"),
        "gtar" => array ("type"=>"application/x-gtar", "icon"=>"zip.gif"),
        "gz"   => array ("type"=>"application/g-zip", "icon"=>"zip.gif"),
        "gzip" => array ("type"=>"application/g-zip", "icon"=>"zip.gif"),
        "h"    => array ("type"=>"text/plain", "icon"=>"text.gif"),
        "hqx"  => array ("type"=>"application/mac-binhex40", "icon"=>"zip.gif"),
        "html" => array ("type"=>"text/html", "icon"=>"html.gif"),
        "htm"  => array ("type"=>"text/html", "icon"=>"html.gif"),
        "jpe"  => array ("type"=>"image/jpeg", "icon"=>"image.gif"),
        "jpeg" => array ("type"=>"image/jpeg", "icon"=>"image.gif"),
        "jpg"  => array ("type"=>"image/jpeg", "icon"=>"image.gif"),
        "js"   => array ("type"=>"application/x-javascript", "icon"=>"text.gif"),
        "latex"=> array ("type"=>"application/x-latex", "icon"=>"text.gif"),
        "m"    => array ("type"=>"text/plain", "icon"=>"text.gif"),
        "mov"  => array ("type"=>"video/quicktime", "icon"=>"video.gif"),
        "movie"=> array ("type"=>"video/x-sgi-movie", "icon"=>"video.gif"),
        "m3u"  => array ("type"=>"audio/x-mpegurl", "icon"=>"audio.gif"),
        "mp3"  => array ("type"=>"audio/mp3", "icon"=>"audio.gif"),
        "mp4"  => array ("type"=>"video/mp4", "icon"=>"video.gif"),
        "mpeg" => array ("type"=>"video/mpeg", "icon"=>"video.gif"),
        "mpe"  => array ("type"=>"video/mpeg", "icon"=>"video.gif"),
        "mpg"  => array ("type"=>"video/mpeg", "icon"=>"video.gif"),
        "pct"  => array ("type"=>"image/pict", "icon"=>"image.gif"),
        "pdf"  => array ("type"=>"application/pdf", "icon"=>"pdf.gif"),
        "php"  => array ("type"=>"text/plain", "icon"=>"text.gif"),
        "pic"  => array ("type"=>"image/pict", "icon"=>"image.gif"),
        "pict" => array ("type"=>"image/pict", "icon"=>"image.gif"),
        "png"  => array ("type"=>"image/png", "icon"=>"image.gif"),
        "pps"  => array ("type"=>"application/vnd.ms-powerpoint", "icon"=>"powerpoint.gif"),
        "ppt"  => array ("type"=>"application/vnd.ms-powerpoint", "icon"=>"powerpoint.gif"),
        "ps"   => array ("type"=>"application/postscript", "icon"=>"pdf.gif"),
        "qt"   => array ("type"=>"video/quicktime", "icon"=>"video.gif"),
        "ra"   => array ("type"=>"audio/x-realaudio", "icon"=>"audio.gif"),
        "ram"  => array ("type"=>"audio/x-pn-realaudio", "icon"=>"audio.gif"),
        "rm"   => array ("type"=>"audio/x-pn-realaudio", "icon"=>"audio.gif"),
        "rtf"  => array ("type"=>"text/rtf", "icon"=>"text.gif"),
        "rtx"  => array ("type"=>"text/richtext", "icon"=>"text.gif"),
        "sh"   => array ("type"=>"application/x-sh", "icon"=>"text.gif"),
        "sit"  => array ("type"=>"application/x-stuffit", "icon"=>"zip.gif"),
        "smi"  => array ("type"=>"application/smil", "icon"=>"text.gif"),
        "smil" => array ("type"=>"application/smil", "icon"=>"text.gif"),
        "swf"  => array ("type"=>"application/x-shockwave-flash", "icon"=>"flash.gif"),
        "tar"  => array ("type"=>"application/x-tar", "icon"=>"zip.gif"),
        "tif"  => array ("type"=>"image/tiff", "icon"=>"image.gif"),
        "tiff" => array ("type"=>"image/tiff", "icon"=>"image.gif"),
        "tex"  => array ("type"=>"application/x-tex", "icon"=>"text.gif"),
        "texi" => array ("type"=>"application/x-texinfo", "icon"=>"text.gif"),
        "texinfo"  => array ("type"=>"application/x-texinfo", "icon"=>"text.gif"),
        "tsv"  => array ("type"=>"text/tab-separated-values", "icon"=>"text.gif"),
        "txt"  => array ("type"=>"text/plain", "icon"=>"text.gif"),
        "wav"  => array ("type"=>"audio/wav", "icon"=>"audio.gif"),
        "wmv"  => array ("type"=>"video/x-ms-wmv", "icon"=>"avi.gif"),
        "asf"  => array ("type"=>"video/x-ms-asf", "icon"=>"avi.gif"),
        "xls"  => array ("type"=>"application/vnd.ms-excel", "icon"=>"excel.gif"),
        "xml"  => array ("type"=>"text/xml", "icon"=>"xml.gif"),
        "xsl"  => array ("type"=>"text/xml", "icon"=>"xml.gif"),
        "zip"  => array ("type"=>"application/zip", "icon"=>"zip.gif")
    );

    if (eregi("\.([a-z0-9]+)$", $filename, $match)) {
        if (isset($mimeinfo[strtolower($match[1])][$element])) {
            return $mimeinfo[strtolower($match[1])][$element];
        } else {
            return $mimeinfo["xxx"][$element];   // By default
        }
    } else {
        return $mimeinfo["xxx"][$element];   // By default
    }
}

function send_file($path, $filename, $lifetime=86400 , $filter=false, $pathisstring=false) {

    $mimetype     = mimeinfo('type', $filename);
    $lastmodified = $pathisstring ? time() : filemtime($path);
    $filesize     = $pathisstring ? strlen($path) : filesize($path);

    @header('Last-Modified: '. gmdate("D, d M Y H:i:s", $lastmodified) .' GMT');
    if ($lifetime > 0) {
        @header('Cache-control: max-age='.$lifetime);
        @header('Expires: '. gmdate("D, d M Y H:i:s", time() + $lifetime) .'GMT');
        @header('Pragma: ');
    } else {
        // this part is tricky, displaying of MS Office documents in IE needs
        // to store the file on disk, but no-cache may prevent it
        @header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=10');
        @header('Expires: '. gmdate("D, d M Y H:i:s", 0) .'GMT');
        @header('Pragma: no-cache');
    }
    @header('Accept-Ranges: none'); // PDF compatibility
    @header('Content-disposition: inline; filename='.$filename);

    if (!$filter) {
        @header('Content-length: '.$filesize);
        if ($mimetype == 'text/plain') {
            @header('Content-type: text/plain; charset='.get_string('thischarset')); //add encoding
        } else {
            @header('Content-type: '.$mimetype);
        }
        if ($pathisstring) {
            echo $path;
        }else {
            readfile($path);
        }
    } else {     // Try to put the file through filters
        global $course;
        if (!empty($course->id)) {
            $courseid = $course->id;
        } else {
            $courseid = SITEID;
        }
        if ($mimetype == 'text/html') {
            $options->noclean = true;
            $text = $pathisstring ? $path : implode('', file($path));
            $output = format_text($text, FORMAT_HTML, $options, $courseid);

            @header('Content-length: '.strlen($output));
            @header('Content-type: text/html');
            echo $output;
        } else if ($mimetype == 'text/plain') {
            $options->newlines = false;
            $options->noclean = true;
            $text = htmlentities($pathisstring ? $path : implode('', file($path)));
            $output = '<pre>'. format_text($text, FORMAT_MOODLE, $options, $courseid) .'</pre>';

            @header('Content-length: '.strlen($output));
            @header('Content-type: text/html; charset='. get_string('thischarset')); //add encoding
            echo $output;
        } else {    // Just send it out raw
            @header('Content-length: '.$filesize);
            @header('Content-type: '.$mimetype);
            if ($pathisstring) {
                echo $path;
            }else {
                readfile($path);
            }
        }
    }
    die; //no more chars to output!!!
}

?>
