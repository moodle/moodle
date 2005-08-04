<?php //$Id$

function mimeinfo($element, $filename) {
    $mimeinfo = array (
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
        'dv'   => array ('type'=>'video/x-dv', 'icon'=>'video.gif'),
        'dmg'  => array ('type'=>'application/octet-stream', 'icon'=>'dmg.gif'),
        'doc'  => array ('type'=>'application/msword', 'icon'=>'word.gif'),
        'dcr'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'dif'  => array ('type'=>'video/x-dv', 'icon'=>'video.gif'),
        'dir'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'dxr'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
        'eps'  => array ('type'=>'application/postscript', 'icon'=>'pdf.gif'),
        'gif'  => array ('type'=>'image/gif', 'icon'=>'image.gif'),
        'gtar' => array ('type'=>'application/x-gtar', 'icon'=>'zip.gif'),
        'tgz'   => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
        'gz'   => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
        'gzip' => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
        'h'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'hpp'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'hqx'  => array ('type'=>'application/mac-binhex40', 'icon'=>'zip.gif'),
        'html' => array ('type'=>'text/html', 'icon'=>'html.gif'),
        'htm'  => array ('type'=>'text/html', 'icon'=>'html.gif'),
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
        'mpeg' => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),
        'mpe'  => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),
        'mpg'  => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),

        'odt'  => array ('type'=>'application/vnd.oasis.opendocument.text', 'icon'=>'odt.gif'),
        'ott'  => array ('type'=>'application/vnd.oasis.opendocument.text-template', 'icon'=>'odt.gif'),
        'oth'  => array ('type'=>'application/vnd.oasis.opendocument.text-web', 'icon'=>'odt.gif'),
        'odm'  => array ('type'=>'application/vnd.oasis.opendocument.text-master', 'icon'=>'odt.gif'),
        'odg'  => array ('type'=>'application/vnd.oasis.opendocument.graphics', 'icon'=>'odt.gif'),
        'otg'  => array ('type'=>'application/vnd.oasis.opendocument.graphics-template', 'icon'=>'odt.gif'),
        'odp'  => array ('type'=>'application/vnd.oasis.opendocument.presentation', 'icon'=>'odt.gif'),
        'otp'  => array ('type'=>'application/vnd.oasis.opendocument.presentation-template', 'icon'=>'odt.gif'),
        'ods'  => array ('type'=>'application/vnd.oasis.opendocument.spreadsheet', 'icon'=>'odt.gif'),
        'ots'  => array ('type'=>'application/vnd.oasis.opendocument.spreadsheet-template', 'icon'=>'odt.gif'),
        'odc'  => array ('type'=>'application/vnd.oasis.opendocument.chart', 'icon'=>'odt.gif'),
        'odf'  => array ('type'=>'application/vnd.oasis.opendocument.formula', 'icon'=>'odt.gif'),
        'odb'  => array ('type'=>'application/vnd.oasis.opendocument.database', 'icon'=>'odt.gif'),
        'odi'  => array ('type'=>'application/vnd.oasis.opendocument.image', 'icon'=>'odt.gif'),

        'pct'  => array ('type'=>'image/pict', 'icon'=>'image.gif'),
        'pdf'  => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
        'php'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
        'pic'  => array ('type'=>'image/pict', 'icon'=>'image.gif'),
        'pict' => array ('type'=>'image/pict', 'icon'=>'image.gif'),
        'png'  => array ('type'=>'image/png', 'icon'=>'image.gif'),
        'pps'  => array ('type'=>'application/vnd.ms-powerpoint', 'icon'=>'powerpoint.gif'),
        'ppt'  => array ('type'=>'application/vnd.ms-powerpoint', 'icon'=>'powerpoint.gif'),
        'ps'   => array ('type'=>'application/postscript', 'icon'=>'pdf.gif'),
        'qt'   => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
        'ra'   => array ('type'=>'audio/x-realaudio', 'icon'=>'audio.gif'),
        'ram'  => array ('type'=>'audio/x-pn-realaudio', 'icon'=>'audio.gif'),
        'rhb'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
        'rm'   => array ('type'=>'audio/x-pn-realaudio', 'icon'=>'audio.gif'),
        'rtf'  => array ('type'=>'text/rtf', 'icon'=>'text.gif'),
        'rtx'  => array ('type'=>'text/richtext', 'icon'=>'text.gif'),
        'sh'   => array ('type'=>'application/x-sh', 'icon'=>'text.gif'),
        'sit'  => array ('type'=>'application/x-stuffit', 'icon'=>'zip.gif'),
        'smi'  => array ('type'=>'application/smil', 'icon'=>'text.gif'),
        'smil' => array ('type'=>'application/smil', 'icon'=>'text.gif'),
        'sqt'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
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
        'xls'  => array ('type'=>'application/vnd.ms-excel', 'icon'=>'excel.gif'),
        'xml'  => array ('type'=>'application/xml', 'icon'=>'xml.gif'),
        'xsl'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
        'zip'  => array ('type'=>'application/zip', 'icon'=>'zip.gif')
    );

    if (eregi('\.([a-z0-9]+)$', $filename, $match)) {
        if (isset($mimeinfo[strtolower($match[1])][$element])) {
            return $mimeinfo[strtolower($match[1])][$element];
        } else {
            return $mimeinfo['xxx'][$element];   // By default
        }
    } else {
        return $mimeinfo['xxx'][$element];   // By default
    }
}

function send_file($path, $filename, $lifetime=86400 , $filter=false, $pathisstring=false,$forcedownload=false) {

    $mimetype     = $forcedownload ? 'application/force-download' : mimeinfo('type', $filename);
    $lastmodified = $pathisstring ? time() : filemtime($path);
    $filesize     = $pathisstring ? strlen($path) : filesize($path);

    //IE compatibiltiy HACK!
    if(ini_get('zlib.output_compression')) {
        ini_set('zlib.output_compression', 'Off');
    }

    @header('Last-Modified: '. gmdate('D, d M Y H:i:s', $lastmodified) .' GMT');
    if ($lifetime > 0) {
        @header('Cache-control: max-age='.$lifetime);
        @header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .'GMT');
        @header('Pragma: ');
    } else {
        // This part is tricky, displaying of MS Office documents in IE needs
        // to store the file on disk, but no-cache may prevent it.
        // HTTPS:// sites might have problems with following code in IE, tweak it yourself if needed ;-)
        @header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=10');
        @header('Expires: '. gmdate('D, d M Y H:i:s', 0) .'GMT');
        @header('Pragma: no-cache');
    }
    @header('Accept-Ranges: none'); // Comment out if PDFs do not work...

    if ($forcedownload) {
        @header('Content-disposition: attachment; filename='.$filename);
    } else {
        @header('Content-disposition: inline; filename='.$filename);
    }

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
            readfile_chunked($path);
        }
    } else {     // Try to put the file through filters
        global $course;  // HACK!
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


if (!function_exists('file_get_contents')) {
   function file_get_contents($file) {
       $file = file($file);
       return !$file ? false : implode('', $file);
   }
}


function fulldelete($location) { 
    if (is_dir($location)) {
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

    } else {
        if (!unlink($location)) {
            return false;
        }
    }
    return true;
}

function readfile_chunked($filename,$retbytes=true) {
    $chunksize = 1*(1024*1024); // 1MB chunks
    $buffer = '';
    $cnt =0;// $handle = fopen($filename, 'rb');
    $handle = fopen($filename, 'rb');
    if ($handle === false) {
        return false;
    }
    
    while (!feof($handle)) {
        $buffer = fread($handle, $chunksize);
        echo $buffer;
        if ($retbytes) {
            $cnt += strlen($buffer);}
    }
    $status = fclose($handle);
    if ($retbytes && $status) {
        return $cnt; // return num. bytes delivered like readfile() does.
    }
    return $status;
}


?>
