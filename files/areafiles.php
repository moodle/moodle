<?php // $Id$

    require('../config.php');
    require_once($CFG->libdir.'/filelib.php');

    $contextid  = required_param('contextid', PARAM_INT);
    $filearea   = required_param('filearea', PARAM_ALPHAEXT);
    $itemid     = required_param('itemid', PARAM_INT);

    $filepath   = optional_param('filepath', '/', PARAM_PATH);
    $filename   = optional_param('filename', '', PARAM_FILE);

    $newdirname = optional_param('newdirname', '', PARAM_FILE);
    $delete     = optional_param('delete', 0, PARAM_BOOL);

    if (!$context = get_context_instance_by_id($contextid)) {
        print_error('invalidcontext');
    }

    require_login();
    if (isguestuser()) {
        print_error('noguest');
    }

    $browser = get_file_browser();

    if (!$area_info = $browser->get_file_info($context, $filearea, $itemid, '/', null)) {
        error('Can not browse this area!'); // TODO: localise
    }

    if ($filename === '') {
        $filename = null;
    }

    $error = '';

    if ($filepath === '/' and is_null($filename)) {
        $file_info = $area_info;
    } else {
        if (!$file_info = $browser->get_file_info($context, $filearea, $itemid, $filepath, $filename)) {
            error('Can not browse this directory!'); // TODO: localise
        }
    }

/// process actions
    if ($file_info and $file_info->is_directory() and $file_info->is_writable() and $newdirname !== '' and data_submitted() and confirm_sesskey()) {
        if ($newdir_info = $file_info->create_directory($newdirname, $USER->id)) {
            $params = $newdir_info->get_params_rawencoded();
            $params = implode('&amp;', $params);
            redirect("areafiles.php?$params");
        } else {
            $error = "Could not create new dir"; // TODO: localise
        }
    }

    if ($file_info and $file_info->is_directory() and $file_info->is_writable() and isset($_FILES['newfile']) and data_submitted() and confirm_sesskey()) {
        $file = $_FILES['newfile'];
        $newfilename = clean_param($file['name'], PARAM_FILE);
        if (is_uploaded_file($_FILES['newfile']['tmp_name'])) {
            try {
                if ($newfile = $file_info->create_file_from_pathname($newfilename, $_FILES['newfile']['tmp_name'], $USER->id)) {
                    $params = $file_info->get_params_rawencoded();
                    $params = implode('&amp;', $params);
                    redirect("areafiles.php?$params");

                } else {
                    $error = "Could not create upload file"; // TODO: localise
                }
            } catch (file_exception $e) {
                $error = "Exception: Could not create upload file"; // TODO: localise
            }
        }
    }

    if ($file_info and $delete) {
        if (!data_submitted() or !confirm_sesskey()) {
            print_header();
            notify(get_string('deletecheckwarning').': '.$file_info->get_visible_name());
            $parent_info = $file_info->get_parent();

            $optionsno  = $parent_info->get_params();
            $optionsyes = $file_info->get_params();
            $optionsyes['delete'] = 1;
            $optionsyes['sesskey'] = sesskey();

            notice_yesno (get_string('deletecheckfiles'), 'areafiles.php', 'areafiles.php', $optionsyes, $optionsno, 'post', 'get');
            print_footer('empty');
            die;
        }

        if ($parent_info = $file_info->get_parent() and $parent_info->is_writable()) {
            if (!$file_info->delete()) {
                $error = "Could not delete file!"; // TODO: localise
            }
            $params = $parent_info->get_params_rawencoded();
            $params = implode('&amp;', $params);
            redirect("areafiles.php?$params", $error);
        }
    }

    print_header();

    if ($error !== '') {
        notify($error);
    }

    echo '<div class="areafiles">';
    displaydir($file_info);
    echo '</div>';

    if ($file_info and $file_info->is_directory() and $file_info->is_writable()) {

        echo '<form enctype="multipart/form-data" method="post" action="areafiles.php"><div>';
        echo '<input type="hidden" name="contextid" value="'.$contextid.'" />';
        echo '<input type="hidden" name="filearea" value="'.$filearea.'" />';
        echo '<input type="hidden" name="itemid" value="'.$itemid.'" />';
        echo '<input type="hidden" name="filepath" value="'.s($filepath).'" />';
        echo '<input type="hidden" name="filename" value="'.s($filename).'" />';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '<input name="newfile" type="file" />';
        echo '<input type="submit" value="'.get_string('uploadafile').'" />';
        echo '</div></form>';

        echo '<form action="areafiles.php" method="post"><div>';
        echo '<input type="hidden" name="contextid" value="'.$contextid.'" />';
        echo '<input type="hidden" name="filearea" value="'.$filearea.'" />';
        echo '<input type="hidden" name="itemid" value="'.$itemid.'" />';
        echo '<input type="hidden" name="filepath" value="'.s($filepath).'" />';
        echo '<input type="hidden" name="filename" value="'.s($filename).'" />';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '<input type="text" name="newdirname" value="" />';
        echo '<input type="submit" value="'.get_string('makeafolder').'" />';
        echo '</div></form>';
    }


    print_footer('empty');

/// FILE FUNCTIONS ///////////////////////////////////////////////////////////

function displaydir($file_info) {
    global $CFG;

    $children = $file_info->get_children();
    $parent_info = $file_info->get_parent();

    $strname     = get_string('name');
    $strsize     = get_string('size');
    $strmodified = get_string('modified');
    $strfolder   = get_string('folder');
    $strfile     = get_string('file');
    $strdownload = get_string('download');
    $strdelete   = get_string('delete');
    $straction   = get_string('action');

    $parentwritable = $file_info->is_writable();

    $directory = $file_info->get_params();
    $directory = $directory['filepath'];

    if ($parent_info and $directory !== '/') {
        $params = $parent_info->get_params_rawencoded();
        $params = implode('&amp;', $params);

        echo '<div class="folder">';
        echo '<a href="areafiles.php?'.$params.'"><img src="'.$CFG->pixpath.'/f/parent.gif" class="icon" alt="" />&nbsp;'.get_string('parentfolder').'</a>';
        echo '</div>';
    }

    if ($children) {
        foreach ($children as $child_info) {
            $filename = $child_info->get_visible_name();
            $filesize = $child_info->get_filesize();
            $filesize = $filesize ? display_size($filesize) : '';

            $mimetype = $child_info->get_mimetype();

            $params = $child_info->get_params_rawencoded();
            $params = implode('&amp;', $params);

            if ($child_info->is_directory()) {

                echo '<div class="folder">';
                echo "<a href=\"areafiles.php?$params\"><img src=\"$CFG->pixpath/f/folder.gif\" class=\"icon\" alt=\"$strfolder\" />&nbsp;".s($filename)."</a>";
                if ($parentwritable) {
                    echo "<a href=\"areafiles.php?$params&amp;sesskey=".sesskey()."&amp;delete=1\"><img src=\"$CFG->pixpath/t/delete.gif\" class=\"iconsmall\" alt=\"$strdelete\" /></a>";
                }
                echo '</div>';

            } else {

                $icon = mimeinfo_from_type('icon', $mimetype);
                echo '<div class="file">';
                echo "<img src=\"$CFG->pixpath/f/$icon\" class=\"icon\" alt=\"$strfile\" />&nbsp;".s($filename)." ($filesize)";
                if ($viewurl = $child_info->get_url()) {
                    echo "&nbsp;".link_to_popup_window ($viewurl, "display",
                         "<img src=\"$CFG->pixpath/t/preview.gif\" class=\"iconsmall\" alt=\"$strfile\" />&nbsp;",
                         480, 640, get_string('viewfileinpopup'), null, true);
                }
                if ($parentwritable) {
                    echo "<a href=\"areafiles.php?$params&amp;sesskey=".sesskey()."&amp;delete=1\"><img src=\"$CFG->pixpath/t/delete.gif\" class=\"iconsmall\" alt=\"$strdelete\" /></a>";;
                }
                echo '</div>';
            }
        }
    }
}

?>
