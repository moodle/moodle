<?PHP  // $Id$

// block.php - allows admin to edit all local configuration variables for a block

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    $adminroot = admin_get_root();
    admin_externalpage_setup('manageblocks', $adminroot);
    require_once($CFG->libdir.'/blocklib.php');

    $blockid = required_param('block', PARAM_INT);

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

    $strmanageblocks = get_string('manageblocks');
    $strblockname = $block->get_title();

    // $CFG->pagepath is used to generate the body and id attributes for the body tag
    // of the page. It is also used to generate the link to the Moodle Docs for this view.
    $CFG->pagepath = 'block/' . $block->name() . '/config';


    admin_externalpage_print_header($adminroot);

    print_heading($strblockname);

    print_simple_box(get_string('configwarning', 'admin'), 'center', '50%');
    echo '<br />';

    echo '<form method="post" action="block.php">';
    echo '<p>';
    foreach($hiddendata as $name => $val) {
        echo '<input type="hidden" name="'. $name .'" value="'. $val .'" />';
    }
    echo '</p>';
    $block->config_print();
    echo '</form>';
    admin_externalpage_print_footer($adminroot);

?>
