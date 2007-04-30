<?PHP // $Id$
    
    require_once('../config.php');
    require_once($CFG->dirroot.'/my/pagelib.php');
    require_once($CFG->dirroot.'/lib/pagelib.php');
    require_once($CFG->dirroot.'/lib/blocklib.php');

    $pt  = optional_param('pt', null, PARAM_SAFEDIR); //alhanumeric and -

    $pagetypes = array(PAGE_MY_MOODLE => array('id' => PAGE_MY_MOODLE,
                                              'lib' => '/my/pagelib.php',
                                              'name' => get_string('mymoodle','admin')),
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
  
    require_capability('moodle/site:manageblocks', get_context_instance(CONTEXT_SYSTEM, SITEID));

    // first thing to do is print the dropdown menu

    $strtitle = get_string('stickyblocks','admin');
    $strheading = get_string('adminhelpstickyblocks');
    
    

    if (!empty($pt)) {

        require_once($CFG->dirroot.$pagetypes[$pt]['lib']);
        
        define('ADMIN_STICKYBLOCKS',$pt);
        
        $PAGE = page_create_object($pt, SITEID);
        $blocks = blocks_setup($PAGE,BLOCKS_PINNED_TRUE);
        $blocks_preferred_width = bounded_number(180, blocks_preferred_width($blocks[BLOCK_POS_LEFT]), 210);

        print_header($strtitle,$strtitle,'<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/index.php">'.
                     get_string('administration').'</a> -> '.$strtitle);
    
        echo '<table border="0" cellpadding="3" cellspacing="0" width="100%" id="layout-table">';
        echo '<tr valign="top">';

        echo '<td valign="top" style="width: '.$blocks_preferred_width.'px;" id="left-column">';
        
        blocks_print_group($PAGE, $blocks, BLOCK_POS_LEFT);
        echo '</td>';
        echo '<td valign="top" id="middle-column">';

    } else {
        require_once($CFG->libdir.'/adminlib.php');
        admin_externalpage_setup('stickyblocks');
        admin_externalpage_print_header();
    }


    print_box_start();
    print_heading($strheading);
    popup_form("$CFG->wwwroot/$CFG->admin/stickyblocks.php?pt=", $options, 'selecttype', $pt, 'choose', '', '', false, 'self', get_string('stickyblockspagetype','admin').': ');
    echo '<p>'.get_string('stickyblocksduplicatenotice','admin').'</p>';
    print_box_end();


    if (!empty($pt)) {
        echo '</td>';
        echo '<td valign="top" style="width: '.$blocks_preferred_width.'px;" id="left-column">';
        blocks_print_group($PAGE, $blocks, BLOCK_POS_RIGHT);
        echo '</td>';
        echo '</tr></table>';
        print_footer();
    } else {
        admin_externalpage_print_footer();
    }

?>
