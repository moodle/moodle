<?PHP  // $Id$

// block.php - allows admin to edit all local configuration variables for a block

    require_once('../config.php');
    require_once($CFG->libdir.'/blocklib.php');

    require_login();

    if (!isadmin()) {
        error('Only an admin can use this page');
    }
    if (!$site = get_site()) {
        error("Site isn't defined!");
    }

    $blockid = required_param( 'block',PARAM_INT );
   
    if(($blockrecord = blocks_get_record($blockid)) === false) {
        error('This block does not exist');
    }

    $block = block_instance($blockrecord->name);
    if($block === false) {
        error('Problem in instantiating block object');
    }

    // Define the data we're going to silently include in the instance config form here,
    // so we can strip them from the submitted data BEFORE handling it.
    $hiddendata = array(
        'block' => $blockid,
        'sesskey' => $USER->sesskey
    );

    /// If data submitted, then process and store.

    if ($config = data_submitted()) {

        if (!confirm_sesskey()) {
             error(get_string('confirmsesskeybad', 'error'));
        }
        if(!$block->has_config()) {
            error('This block does not support global configuration');
        }
        $remove = array_keys($hiddendata);
        foreach($remove as $item) {
            unset($config->$item);
        }
        $block->config_save($config);
        redirect("$CFG->wwwroot/$CFG->admin/blocks.php", get_string("changessaved"), 1);
        exit;
    }

    /// Otherwise print the form.

    $stradmin = get_string('administration');
    $strconfiguration = get_string('configuration');
    $strmanageblocks = get_string('manageblocks');
    $strblockname = $block->get_title();

    print_header($site->shortname.': '.$strblockname.": $strconfiguration", $site->fullname,
                  "<a href=\"index.php\">$stradmin</a> -> ".
                  "<a href=\"configure.php\">$strconfiguration</a> -> ".
                  "<a href=\"blocks.php\">$strmanageblocks</a> -> ".$strblockname);

    print_heading($strblockname);

    print_simple_box('<center>'.get_string('configwarning', 'admin').'</center>', 'center', '50%');
    echo '<br />';

    echo '<form method="post" action="block.php">';
    echo '<p>';
    foreach($hiddendata as $name => $val) {
        echo '<input type="hidden" name="'. $name .'" value="'. $val .'" />';
    }
    echo '</p>';
    $block->config_print();
    echo '</form>';
    print_footer();

?>
