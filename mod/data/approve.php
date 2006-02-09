<?php

    require_once('../../config.php');
    require_once('lib.php');
    require_once($CFG->libdir.'/blocklib.php');

    require_once('pagelib.php');
    require_login();

    if (!isteacher()) {
        error(get_string('errormustbeteacher', 'data'));
    }

    if (confirm_sesskey()
        && ($recordid = required_param('recordid',PARAM_INT))
        && ($d = required_param('d',PARAM_INT))) {
        data_approve_record($recordid);
    }

    $page=optional_param('page','0',PARAM_INT);
    $rid = optional_param('rid','0', PARAM_INT);
    $search =optional_param('search','',PARAM_ALPHA);
    $sort= optional_param('sort','',PARAM_ALPHA);
    $order=optional_param('order','',PARAM_ALPHA);

    print_heading(get_string('recordapproved','data'));
    redirect('view.php?d='.$d.'&amp;approved=1&amp;page='.$page.'&amp;rid='.$rid.'&amp;search='.$search.'&amp;sort='.$sort.'&amp;order='.$order.'&amp;');


?>
