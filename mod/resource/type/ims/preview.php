<?php
    require_once('../../../../config.php');
    require_once('../../lib.php');
    require_once('resource.class.php');
    require_once('../../../../backup/lib.php');
    require_once('../../../../lib/filelib.php');
    require_once('../../../../lib/xmlize.php');
    
    require_once('repository_config.php');
    
    $directory = required_param ('directory', PARAM_PATH);
    $page = optional_param ('page', 0, PARAM_INT);

/// Calculate the path of the IMS CP to be displayed
    $deploydir = $CFG->repository . '/' . $directory;

/// Confirm that the IMS package has been deployed. Hash not generated
/// for repository ones.
    if (!file_exists($deploydir.'/moodle_inx.ser')) {
            $errortext = "Not Deployed";
            print_header();
            print_simple_box_start('center', '60%');
            echo '<p align="center">'.$errortext.'</p>';
            print_footer();
            exit;
    }               

/// Load serialized IMS CP index to memory only once.
    if (empty($items)) {
        if (!$items = ims_load_serialized_file($deploydir.'/moodle_inx.ser')) {
            error (get_string('errorreadingfile', 'error', 'moodle_inx.ser'));
        }
    }

/// fast forward to first non-index page
    while (empty($items[$page]->href)) $page++;
    
/// Select encoding
    $encoding = current_charset();

/// Select direction
    if (get_string('thisdirection') == 'rtl') {
        $direction = ' dir="rtl"';
    } else {
        $direction = ' dir="ltr"';
    }

/// The output here

    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">\n";
    echo "<html$direction>\n";
    echo '<head>';
    echo '<meta http-equiv="content-type" content="text/html; charset='.$encoding.'" />';
    echo "
    <script type=\"text/javascript\" language=\"javascript\" src=\"dummy.js\"></script>
    <script language=\"javascript\" type=\"text/javascript\">
        function resizeiframe () {
              var winWidth = 0, winHeight = 0;
              if( typeof( window.innerWidth ) == 'number' ) {
                //Non-IE
                winWidth = window.innerWidth;
                winHeight = window.innerHeight;
              } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
                //IE 6+ in 'standards compliant mode'
                winWidth = document.documentElement.clientWidth;
                winHeight = document.documentElement.clientHeight;
              } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
                //IE 4 compatible
                winWidth = document.body.clientWidth;
                winHeight = document.body.clientHeight;
              }

            document.getElementById('ims-preview-contentframe').style.width = (winWidth - 300)+'px';
            document.getElementById('ims-preview-contentframe').style.height = (winHeight)+'px';
            document.getElementById('ims-preview-menudiv').style.height = (winHeight)+'px';
            
        }
        
        window.onresize = resizeiframe;
        window.onload = resizeiframe;

    </script>
    <style type='text/css'>
        #ims-preview-menudiv {
            position:absolute;
            top:0px;
            left:0px;
            width:300px;
            height:100%;
            overflow:auto;
        }
        
        #ims-preview-contentframe {
            position:absolute;
            top:0px;
            left:300px;
            height:100%;
            border:0;
        }
    </style>
    ";
    echo "<title>Preview</title></head>\n";
/// moodle header
    print_header();
/// content - this produces everything else

/// adds side navigation bar if needed. must also adjust width of iframe to accomodate 
    echo "<div id=\"ims-preview-menudiv\">";  preview_buttons($directory, $items['title']); echo preview_ims_generate_toc($items, $directory); echo "</div>";
    
    $fullurl = "$CFG->repositorywebroot/$directory/".$items[$page]->href;
/// prints iframe filled with $fullurl ;width:".$iframewidth." missing also height=\"420px\"
    echo "<iframe id=\"ims-preview-contentframe\" name=\"ims-preview-contentframe\" src=\"{$fullurl}\"></iframe>"; //Content frame 
/// moodle footer
    echo "</div></div></body></html>";
    
    
    /*** This function will generate the TOC file for the package
     *   from an specified parent to be used in the view of the IMS
     */
    function preview_ims_generate_toc($items, $directory, $page=0) {
        global $CFG;

        $contents = '';

    /// Configure links behaviour
        $fullurl = '?directory='.$directory.'&page=';

    /// Iterate over items to build the menu
        $currlevel = 0;
        $currorder = 0;
        $endlevel  = 0;
        foreach ($items as $item) {
            if (!is_object($item)) {
                continue;
            }
        /// Convert text from UTF-8 to current charset if needed
            if (empty($CFG->unicodedb)) {
                $textlib = textlib_get_instance();
                $item->title = $textlib->convert($item->title, 'UTF-8', current_charset());
            }
        /// Skip pages until we arrive to $page
            if ($item->id < $page) {
                continue;
            }
        /// Arrive to page, we store its level
            if ($item->id == $page) {
                $endlevel = $item->level;
                continue;
            }
        /// We are after page and inside it (level > endlevel)
            if ($item->id > $page && $item->level > $endlevel) {
            /// Start Level 
                if ($item->level > $currlevel) {
                    $contents .= '<ol class="listlevel_'.$item->level.'">';
                }
            /// End Level
                if ($item->level < $currlevel) {
                    $contents .= '</ol>';
                }
            /// Add item
                $contents .= '<li>';
                if (!empty($item->href)) {
                    $contents .= '<a href="'.$fullurl.$item->id.'" target="_parent">'.$item->title.'</a>';
                } else {
                    $contents .= $item->title;
                }
                $contents .= '</li>';
                $currlevel = $item->level;
                continue;
            }
        /// We have reached endlevel, exit
            if ($item->id > $page && $item->level <= $endlevel) {
                break;
            }
        }
        $contents .= '</ol>';

        return $contents;
    }
    
    function preview_buttons($directory, $name) {
        $strchoose = get_string('choose','resource');
        $strback = get_string('back','resource');
        
        $path = $directory;
        $arr = explode('/', $directory);
        array_pop($arr);
        $directory = implode('/', $arr);
        echo "<div id=\"ims_preview_buttons\" style=\"padding:10px;\">
              (<a href='finder.php?directory=$directory'>$strback</a>) 
              (<a href=\"javascript:
                        opener.document.forms['form'].reference.value = '#$path'; 
                        opener.document.forms['form'].name.value = '$name'; 
                        window.close();
              \">$strchoose</a>)</div>";
    }
    
?>