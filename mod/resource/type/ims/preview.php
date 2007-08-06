<?php // $Id$

    require_once('../../../../config.php');
    require_once('../../lib.php');
    require_once('resource.class.php');
    require_once('../../../../backup/lib.php');
    require_once('../../../../lib/filelib.php');
    require_once('../../../../lib/xmlize.php');

    require_once('repository_config.php');

    $directory = required_param ('directory', PARAM_PATH);
    $choose = optional_param ('choose', 'id_reference_value', PARAM_FILE);
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

/// Select direction
    if (get_string('thisdirection') == 'rtl') {
        $direction = ' dir="rtl"';
    } else {
        $direction = ' dir="ltr"';
    }

/// Conditional argument to pass to IMS JavaScript. Need to be global to retrieve it from our custom javascript! :-(
    global $jsarg;
    $jsarg = 'true';
/// Define $CFG->javascript to use our custom javascript. Save the original one to add it from ours. Global too! :-(
    global $standard_javascript;
    $standard_javascript = $CFG->javascript;  // Save original javascript file
    $CFG->javascript = $CFG->dirroot.'/mod/resource/type/ims/javascript.php';  //Use our custom IMS javascript code

/// The output here

/// moodle header
    print_header();
/// content - this produces everything else

/// adds side navigation bar if needed. must also adjust width of iframe to accomodate
    echo "<div id=\"ims-menudiv\">";
    preview_buttons($directory, $items['title'], $choose);
    echo preview_ims_generate_toc($items, $directory, 0, $page); echo "</div>";

    $fullurl = "$CFG->repositorywebroot/$directory/".$items[$page]->href;
/// prints iframe filled with $fullurl ;width:".$iframewidth." missing also height=\"420px\"
    echo "<iframe id=\"ims-contentframe\" name=\"ims-contentframe\" src=\"{$fullurl}\"></iframe>"; //Content frame
/// moodle footer
    echo "</div></div><script type=\"text/javascript\">resizeiframe($jsarg);</script></body></html>";

    /*** This function will generate the TOC file for the package
     *   from an specified parent to be used in the view of the IMS
     */
    function preview_ims_generate_toc($items, $directory, $page=0, $selected_page) {
        global $CFG;

        $contents = '';

    /// Configure links behaviour
        $fullurl = '?directory='.$directory.'&amp;page=';

    /// Iterate over items to build the menu
        $currlevel = 0;
        $currorder = 0;
        $endlevel  = 0;
        $openlielement = false;
        foreach ($items as $item) {
            if (!is_object($item)) {
                continue;
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
                    $openlielement = false;
                }
            /// End Level
                if ($item->level < $currlevel) {
                    $contents .= '</li>';
                    $contents .= '</ol>';
                }
            /// If we have some openlielement, just close it
                if ($openlielement) {
                    $contents .= '</li>';
                }
            /// Add item
                $contents .= '<li>';
                if (!empty($item->href)) {
                    if ($item->id == $selected_page) $contents .= '<div id="ims-toc-selected">';
                    $contents .= '<a href="'.$fullurl.$item->id.'" target="_parent">'.$item->title.'</a>';
                    if ($item->id == $selected_page) $contents .= '</div>';
                } else {
                    $contents .= $item->title;
                }
                $currlevel = $item->level;
                $openlielement = true;
                continue;
            }
        /// We have reached endlevel, exit
            if ($item->id > $page && $item->level <= $endlevel) {
                break;
            }
        }
    /// Close up to $endlevel
        for ($i=$currlevel;$i>$endlevel;$i--) {
            $contents .= '</li>';
            $contents .= '</ol>';
        }

        return $contents;
    }

    function preview_buttons($directory, $name, $choose='') {
        $strchoose = get_string('choose','resource');
        $strback = get_string('back','resource');

        $path = $directory;
        $arr = explode('/', $directory);
        array_pop($arr);
        $directory = implode('/', $arr);
        ?>
        <script type="text/javascript">
        //<![CDATA[
        function set_value(txt) {
            opener.document.getElementById('<?php echo $choose ?>').value = txt;
            window.close();
        }
        //]]>
        </script>
        <?php
        echo "<div id=\"ims_preview_buttons\" style=\"padding:10px;\">".
             "(<a href=\"finder.php?directory=$directory&amp;choose=$choose\">$strback</a>) ".
             "(<a onclick=\"return set_value('#$path')\" href=\"#\">$strchoose</a>)</div>";
    }

?>
