<? // $Id$

function mimeinfo($element, $filename) {
    $mimeinfo = array (
        "xxx"  => array ("type"=>"document/unknown", "icon"=>"unknown.gif"),
        "zip"  => array ("type"=>"application/zip", "icon"=>"zip.gif"),
        "jpeg" => array ("type"=>"image/jpeg", "icon"=>"image.gif"),
        "jpg"  => array ("type"=>"image/jpeg", "icon"=>"image.gif"),
        "gif"  => array ("type"=>"image/gif", "icon"=>"image.gif"),
        "png"  => array ("type"=>"image/png", "icon"=>"image.gif"),
        "bmp"  => array ("type"=>"image/bmp", "icon"=>"image.gif"),
        "html" => array ("type"=>"text/html", "icon"=>"html.gif"),
        "htm"  => array ("type"=>"text/html", "icon"=>"html.gif"),
        "txt"  => array ("type"=>"text/plain", "icon"=>"text.gif"),
        "php"  => array ("type"=>"text/plain", "icon"=>"text.gif"),
        "wav"  => array ("type"=>"audio/wav", "icon"=>"audio.gif"),
        "mp3"  => array ("type"=>"audio/mp3", "icon"=>"audio.gif"),
        "au"   => array ("type"=>"audio/au", "icon"=>"audio.gif"),
        "swf"  => array ("type"=>"application/x-shockwave-flash", "icon"=>"image.gif"),
        "pdf"  => array ("type"=>"application/pdf", "icon"=>"pdf.gif"),
        "doc"  => array ("type"=>"application/msword", "icon"=>"word.gif"),
        "ppt"  => array ("type"=>"application/vnd.ms-powerpoint", "icon"=>"powerpoint.gif"),
        "xls"  => array ("type"=>"application/vnd.ms-excel", "icon"=>"excel.gif")
    );

    if (eregi("\.([a-z0-9]+)$", $filename, $match)) {
        $result = $mimeinfo[strtolower($match[1])][$element];
    }

    if (!empty($result)) {
        return $result;
    } else {
        return $mimeinfo["xxx"][$element];   // By default
    }
}

?>
