<?
{
    
    require_once(dirname(dirname(__FILE__)).'/config.php');
    require_once($CFG->dirroot.'/my/pagelib.php');
    require_once($CFG->dirroot.'/lib/pagelib.php');
    require_once($CFG->dirroot.'/lib/blocklib.php');

    $pt  = optional_param('pt',null,PARAM_CLEAN);

    $pagetypes = array(PAGE_MY_MOODLE => array('id' => PAGE_MY_MOODLE,
                                              'lib' => '/my/pagelib.php',
                                              'name' => get_string('stickyblocksmymoodle','admin')),
                       PAGE_COURSE_VIEW => array('id' => PAGE_COURSE_VIEW, 
                                                'lib' => '/lib/pagelib.php',
                                                'name' => get_string('stickyblockscourseview','admin'))
                       // ... more?
                       );

    // for choose_from_menu
    $options = array();
    foreach ($pagetypes as $p) {
        $options[$p['id']] = $p['name'];
    }

    require_login();
  
    if (!isadmin()) {
        error("Only the admin can use this page");
    }

    // first thing to do is print the dropdown menu

    $strtitle = get_string('stickyblocks','admin');
    $strheading = get_string('adminhelpstickyblocks');
    
    print_header($strtitle,$strtitle,'<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/index.php">'.
                 get_string('admin').'</a> -> '.$strtitle);

    echo '<table border="0" cellpadding="3" cellspacing="0" width="100%" id="layout-table">';
    echo '<tr valign="top">';
    

    if (!empty($pt)) {
        require_once($CFG->dirroot.$pagetypes[$pt]['lib']);
        
        define('ADMIN_STICKYBLOCKS',$pt);
        
        $PAGE = page_create_object($pt);
        
        $blocks = blocks_setup($PAGE,BLOCKS_PINNED_TRUE);
        
        $blocks_preferred_width = bounded_number(180, blocks_preferred_width($blocks[BLOCK_POS_LEFT]), 210);
        echo '<td style="vertical-align: top; width: '.$blocks_preferred_width.'px;" id="left-column">';
        
        blocks_print_group($PAGE, $blocks, BLOCK_POS_LEFT);
    } else {
        echo '<td style="vertical-align: top;" id="left-column">';
    }
    echo '</td>';


    echo '<td valign="top" width="*" id="middle-column">';
    print_simple_box_start('center');
    print_heading($strheading);
    echo '<form method="post" action="'.$CFG->wwwroot.'/admin/stickyblocks.php">'
        .'<p align="center">'.get_string('stickyblockspagetype','admin').': ';
    choose_from_menu($options,'pt',$pt,'choose','this.form.submit();');
    echo '</p></form>';
    echo get_string('stickyblocksduplicatenotice','admin');
    print_simple_box_end();
    echo '</td>';


    if (!empty($pt)) {
        echo '<td style="vertical-align: top; width: '.$blocks_preferred_width.'px;" id="left-column">';
        blocks_print_group($PAGE, $blocks, BLOCK_POS_RIGHT);
    }
    echo '<td style="vertical-align: top;" id="left-column">';
    echo '</td>';
  
    echo '</tr></table>';

    print_footer();
    

}


?>