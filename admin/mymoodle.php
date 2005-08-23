<?
{
    require_once(dirname(dirname(__FILE__)).'/config.php');
    require_once($CFG->dirroot.'/my/pagelib.php');
    require_once($CFG->dirroot.'/lib/blocklib.php');

    require_login();
  
    if (!isadmin()) {
        error("Only the admin can use this page");
    }

    define('OVERRIDE_PAGE_TYPE',PAGE_MY_MOODLE);

    $PAGE = page_create_object(PAGE_MY_MOODLE,0);

    $blocks = blocks_setup($PAGE,BLOCKS_PINNED_TRUE);
    
    $strtitle = get_string('pinblocks','my');
    
    print_header($strtitle,$strtitle,'<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/index.php">'.
                 get_string('admin').'</a> -> '.$strtitle);
    
    echo '<table border="0" cellpadding="3" cellspacing="0" width="100%" id="layout-table">';
    echo '<tr valign="top">';


    $blocks_preferred_width = bounded_number(180, blocks_preferred_width($blocks[BLOCK_POS_LEFT]), 210);


    echo '<td style="vertical-align: top; width: '.$blocks_preferred_width.'px;" id="left-column">';
    blocks_print_group($PAGE, $blocks, BLOCK_POS_LEFT);
    echo '</td>';

    echo '<td valign="top" width="*" id="middle-column">';
    print_simple_box_start('center');
    print_heading($strtitle);
    print_string('pinblocksexplan','my');
    print_simple_box_end();
    echo '</td>';


    echo '<td style="vertical-align: top; width: '.$blocks_preferred_width.'px;" id="left-column">';
    blocks_print_group($PAGE, $blocks, BLOCK_POS_RIGHT);
    echo '</td>';
  
    echo '</tr></table>';

    print_footer();
    

}


?>