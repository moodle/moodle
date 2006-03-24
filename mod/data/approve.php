<?php // $Id$ 

    require_once('../../config.php');
    require_once('lib.php');

    require_login();

    $recordid = required_param('recordid',PARAM_INT);

    $page     = optional_param('page','0',PARAM_INT);
    $rid      = optional_param('rid','0', PARAM_INT);
    $search   = optional_param('search','',PARAM_ALPHA);
    $sort     = optional_param('sort','',PARAM_ALPHA);
    $order    = optional_param('order','',PARAM_ALPHA);

    if (! $record = get_record('data_records', 'id', $recordid)) {
        error('Record ID is incorrect');
    }
    if (! $data = get_record('data', 'id', $record->dataid)) {
        error('Data ID is incorrect');
    }
    if (! $course = get_record('course', 'id', $data->course)) {
        error('Course is misconfigured');
    }

    if (!isteacher($course->id)) {
        error(get_string('errormustbeteacher', 'data'));
    }

    if (confirm_sesskey()) {  /*  Approve it! */
        $newrecord->id = $record->id;
        $newrecord->approved = 1;
        update_record('data_records', $newrecord);
    }

    redirect('view.php?d='.$d.'&amp;approved=1&amp;page='.$page.'&amp;rid='.$rid.'&amp;search='.$search.'&amp;sort='.$sort.'&amp;order='.$order.'&amp;', get_string('recordapproved','data'));


?>
