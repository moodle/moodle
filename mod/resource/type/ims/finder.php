<?php
    require_once('../../../../config.php');
    require_once('../../lib.php');
    require_once('resource.class.php');
    require_once('../../../../backup/lib.php');
    require_once('../../../../lib/filelib.php');
    require_once('../../../../lib/xmlize.php');
    
    require_once('repository_config.php');

/// Directory to browse, inside repository. Starts on ''.    
    $directory = optional_param ('directory', '', PARAM_PATH);
    $choose  = optional_param('choose', 'id_reference_value', PARAM_FILE);
    $name  = optional_param('name', 'id_name', PARAM_FILE);
    
/// Get the language strings needed
    $strdeployall = get_string('deployall','resource');
    $strpreview = get_string('preview','resource');
    $strchoose = get_string('choose','resource');
    $strdeploy = get_string('deploy','resource');
    $strnotdeployed = get_string('notdeployed','resource');
    $stremptyfolder = get_string('emptyfolder','resource');
    
/// Print header. Blank, nothing fancy. 
    print_header();
    
    $items = array();
/// Open $directory
    if (!($repository_dir = opendir("$CFG->repository/$directory"))) die("Can't open directory \"$CFG->repository/$directory\"");
    
/// Loops though dir building a list of all relevent entries. Ignores files.
/// Asks for deploy if admin user AND no serialized file found.
    while (false !== ($filename = readdir($repository_dir))) {
        if ($filename != '.' && $filename != '..' && is_dir("$CFG->repository/$directory/$filename")) {
            unset($item);
            $item->type = '';
            $item->name = 0;
            $item->path = "$directory/$filename";
            
        /// No manifest => normal, browsable directory.
            if (!file_exists("$CFG->repository/$item->path/imsmanifest.xml")) {
                $item->type = 'directory';
                $item->name = $filename;
            }
        /// Manifest, so IMS CP.
            else {
                if (file_exists("$CFG->repository/$item->path/moodle_inx.ser")) {
                    $item->type = 'deployed';
                    $index = ims_load_serialized_file("$CFG->repository/$item->path/moodle_inx.ser");
                    $item->name = $index['title'];
                }
                else {
                    $item->type = 'not deployed';
                    $item->name = $filename;
                }
            }
            $items[] = $item;   
        }   
    }
    closedir($repository_dir);

/// Prints the toolbar. 
    echo '<div id="ims_toolbar" style="padding:10px;">';
    ims_print_crumbtrail($directory, $choose);
    
/// If admin, add extra buttons - redeploy & help.
    if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
        echo " | (<a href=\"repository_deploy.php?file=$directory&amp;all=force\">$strdeployall</a>) ";
        helpbutton("deploy", get_string("deployall", "resource"), "resource", true);
    }
    echo '</div>';

/// Prints the file list from list generated above.
    echo '<div id="ims_filelist">';
    ?>
    <script type="text/javascript">
        //<![CDATA[
        function set_opener_value(field, txt, force) {
            if (opener.document.getElementById(field)) {
                if (force || !opener.document.getElementById(field).value) {
                    opener.document.getElementById(field).value = txt;
                }
            }
        }
        //]]>
    </script>
    <?php
    echo '<ul style="list-style:none;padding:10px;margin:0px;">';
    if ($items != array()) {

        foreach ($items as $item) {
            if ($item->type == 'deployed') {
                echo "<li><img src=\"images/ims.gif\" alt=\"IMS CP Package\" /> $item->name" .
                     "(<a onclick=\"set_opener_value('$choose', '#$item->path', true); set_opener_value('$name', '$item->name', false); window.close()\" href=\"#\">$strchoose</a>) " .
                     "(<a href=\"preview.php?directory=$item->path&amp;choose=$choose\">$strpreview</a>)</li>\n";
            }
            else if ($item->type == 'not deployed') {
            /// Only displays non-deployed IMS CP's if admin user.
                if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
                    echo "<li><img src=\"images/ims.gif\" alt=\"IMS CP Package\" /> <em>$item->path - $strnotdeployed</em> (<a href=\"repository_deploy.php?file=$item->path\">$strdeploy</a>)</li>\n";
                }
            }
            else if ($item->type == 'directory') {
                echo "<li><img src=\"images/dir.gif\" alt=\"IMS CP Package\" /> <a href=\"?directory=$item->path&amp;choose=$choose\">$item->name</a></li>\n";
            }
        }
    }
    else {
        echo "<li><em>$stremptyfolder</em></li>";
    }
    echo "</ul>";
    
/// Print footer and exit.
    echo "</div></div></div></body></html>";
    exit;
    
/// Generates the crumbtrial from $directory. Just splits up on '/'.
    function ims_print_crumbtrail($directory, $choose='') {
        $strrepository = get_string('repository','resource');
        
        $arr = explode('/', $directory);
        $last = array_pop($arr);
        if (trim($directory, '/') == '') {
            echo $strrepository;
            return;
        }
        else {
            $output = "<a href=\"?directory=&amp;choose=$choose\">$strrepository</a> &#187; ";
        }
        $itemdir = '';
        foreach ($arr as $item) {
            if ($item == '') continue;
            $itemdir .= '/'.$item;
            $output .= "<a href=\"?directory=$itemdir&amp;choose=$choose\">$item</a> &#187; ";
        }
        $output .= $last;
        echo $output;
    }
?>
