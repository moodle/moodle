<?php

// block.php - allows admin to edit all local configuration variables for a block

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    $blockid = required_param('block', PARAM_INT);

    if(!$blockrecord = blocks_get_record($blockid)) {
        print_error('blockdoesnotexist', 'error');
    }

    admin_externalpage_setup('blocksetting'.$blockrecord->name);

    $block = block_instance($blockrecord->name);
    if($block === false) {
        print_error('blockcannotinistantiate', 'error');
    }

    // Define the data we're going to silently include in the instance config form here,
    // so we can strip them from the submitted data BEFORE handling it.
    $hiddendata = array(
        'block' => $blockid,
        'sesskey' => sesskey()
    );

    /// If data submitted, then process and store.

    if ($config = data_submitted()) {

        if (!confirm_sesskey()) {
             print_error('confirmsesskeybad', 'error');
        }
        if(!$block->has_config()) {
            print_error('blockcannotconfig', 'error');
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

    echo $OUTPUT->header();

    echo $OUTPUT->heading($strblockname);

    echo $OUTPUT->notification('This block still uses an old-style config_global.html file. ' .
            'It must be updated by a developer to use a settings.php file.');

    echo $OUTPUT->box(get_string('configwarning', 'admin'), 'generalbox boxwidthnormal boxaligncenter');
    echo '<br />';

    echo '<form method="post" action="block.php">';
    echo '<p>';
    foreach($hiddendata as $name => $val) {
        echo '<input type="hidden" name="'. $name .'" value="'. $val .'" />';
    }
    echo '</p>';

    echo $OUTPUT->box_start();
    include($CFG->dirroot.'/blocks/'. $block->name() .'/config_global.html');
    echo $OUTPUT->box_end();

    echo '</form>';
    echo $OUTPUT->footer();


