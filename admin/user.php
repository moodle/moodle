<?php

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/user/filters/lib.php');

    $delete       = optional_param('delete', 0, PARAM_INT);
    $confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
    $confirmuser  = optional_param('confirmuser', 0, PARAM_INT);
    $sort         = optional_param('sort', 'name', PARAM_ALPHANUM);
    $dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
    $page         = optional_param('page', 0, PARAM_INT);
    $perpage      = optional_param('perpage', 30, PARAM_INT);        // how many per page
    $ru           = optional_param('ru', '2', PARAM_INT);            // show remote users
    $lu           = optional_param('lu', '2', PARAM_INT);            // show local users
    $acl          = optional_param('acl', '0', PARAM_INT);           // id of user to tweak mnet ACL (requires $access)
    $suspend      = optional_param('suspend', 0, PARAM_INT);
    $unsuspend    = optional_param('unsuspend', 0, PARAM_INT);

    admin_externalpage_setup('editusers');

    $sitecontext = context_system::instance();
    $site = get_site();

    if (!has_capability('moodle/user:update', $sitecontext) and !has_capability('moodle/user:delete', $sitecontext)) {
        print_error('nopermissions', 'error', '', 'edit/delete users');
    }

    $stredit   = get_string('edit');
    $strdelete = get_string('delete');
    $strdeletecheck = get_string('deletecheck');
    $strshowallusers = get_string('showallusers');
    $strsuspend = get_string('suspenduser', 'admin');
    $strunsuspend = get_string('unsuspenduser', 'admin');
    $strconfirm = get_string('confirm');

    if (empty($CFG->loginhttps)) {
        $securewwwroot = $CFG->wwwroot;
    } else {
        $securewwwroot = str_replace('http:','https:',$CFG->wwwroot);
    }

    $returnurl = new moodle_url('/admin/user.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage, 'page'=>$page));

    if ($confirmuser and confirm_sesskey()) {
        require_capability('moodle/user:update', $sitecontext);
        if (!$user = $DB->get_record('user', array('id'=>$confirmuser, 'mnethostid'=>$CFG->mnet_localhost_id))) {
            print_error('nousers');
        }

        $auth = get_auth_plugin($user->auth);

        $result = $auth->user_confirm($user->username, $user->secret);

        if ($result == AUTH_CONFIRM_OK or $result == AUTH_CONFIRM_ALREADY) {
            redirect($returnurl);
        } else {
            echo $OUTPUT->header();
            redirect($returnurl, get_string('usernotconfirmed', '', fullname($user, true)));
        }

    } else if ($delete and confirm_sesskey()) {              // Delete a selected user, after confirmation
        require_capability('moodle/user:delete', $sitecontext);

        $user = $DB->get_record('user', array('id'=>$delete, 'mnethostid'=>$CFG->mnet_localhost_id), '*', MUST_EXIST);

        if (is_siteadmin($user->id)) {
            print_error('useradminodelete', 'error');
        }

        if ($confirm != md5($delete)) {
            echo $OUTPUT->header();
            $fullname = fullname($user, true);
            echo $OUTPUT->heading(get_string('deleteuser', 'admin'));
            $optionsyes = array('delete'=>$delete, 'confirm'=>md5($delete), 'sesskey'=>sesskey());
            echo $OUTPUT->confirm(get_string('deletecheckfull', '', "'$fullname'"), new moodle_url($returnurl, $optionsyes), $returnurl);
            echo $OUTPUT->footer();
            die;
        } else if (data_submitted() and !$user->deleted) {
            if (delete_user($user)) {
                session_gc(); // remove stale sessions
                redirect($returnurl);
            } else {
                session_gc(); // remove stale sessions
                echo $OUTPUT->header();
                echo $OUTPUT->notification($returnurl, get_string('deletednot', '', fullname($user, true)));
            }
        }
    } else if ($acl and confirm_sesskey()) {
        if (!has_capability('moodle/user:update', $sitecontext)) {
            print_error('nopermissions', 'error', '', 'modify the NMET access control list');
        }
        if (!$user = $DB->get_record('user', array('id'=>$acl))) {
            print_error('nousers', 'error');
        }
        if (!is_mnet_remote_user($user)) {
            print_error('usermustbemnet', 'error');
        }
        $accessctrl = strtolower(required_param('accessctrl', PARAM_ALPHA));
        if ($accessctrl != 'allow' and $accessctrl != 'deny') {
            print_error('invalidaccessparameter', 'error');
        }
        $aclrecord = $DB->get_record('mnet_sso_access_control', array('username'=>$user->username, 'mnet_host_id'=>$user->mnethostid));
        if (empty($aclrecord)) {
            $aclrecord = new stdClass();
            $aclrecord->mnet_host_id = $user->mnethostid;
            $aclrecord->username = $user->username;
            $aclrecord->accessctrl = $accessctrl;
            $DB->insert_record('mnet_sso_access_control', $aclrecord);
        } else {
            $aclrecord->accessctrl = $accessctrl;
            $DB->update_record('mnet_sso_access_control', $aclrecord);
        }
        $mnethosts = $DB->get_records('mnet_host', null, 'id', 'id,wwwroot,name');
        redirect($returnurl);

    } else if ($suspend and confirm_sesskey()) {
        require_capability('moodle/user:update', $sitecontext);

        if ($user = $DB->get_record('user', array('id'=>$suspend, 'mnethostid'=>$CFG->mnet_localhost_id, 'deleted'=>0))) {
            if (!is_siteadmin($user) and $USER->id != $user->id and $user->suspended != 1) {
                $user->suspended = 1;
                $user->timemodified = time();
                $DB->set_field('user', 'suspended', $user->suspended, array('id'=>$user->id));
                $DB->set_field('user', 'timemodified', $user->timemodified, array('id'=>$user->id));
                // force logout
                session_kill_user($user->id);
                events_trigger('user_updated', $user);
            }
        }
        redirect($returnurl);

    } else if ($unsuspend and confirm_sesskey()) {
        require_capability('moodle/user:update', $sitecontext);

        if ($user = $DB->get_record('user', array('id'=>$unsuspend, 'mnethostid'=>$CFG->mnet_localhost_id, 'deleted'=>0))) {
            if ($user->suspended != 0) {
                $user->suspended = 0;
                $user->timemodified = time();
                $DB->set_field('user', 'suspended', $user->suspended, array('id'=>$user->id));
                $DB->set_field('user', 'timemodified', $user->timemodified, array('id'=>$user->id));
                events_trigger('user_updated', $user);
            }
        }
        redirect($returnurl);
    }

    // create the user filter form
    $ufiltering = new user_filtering();
    echo $OUTPUT->header();

    // Carry on with the user listing
    $context = context_system::instance();
    $extracolumns = get_extra_user_fields($context);
    $columns = array_merge(array('firstname', 'lastname'), $extracolumns,
            array('city', 'country', 'lastaccess'));

    foreach ($columns as $column) {
        $string[$column] = get_user_field_name($column);
        if ($sort != $column) {
            $columnicon = "";
            if ($column == "lastaccess") {
                $columndir = "DESC";
            } else {
                $columndir = "ASC";
            }
        } else {
            $columndir = $dir == "ASC" ? "DESC":"ASC";
            if ($column == "lastaccess") {
                $columnicon = ($dir == "ASC") ? "sort_desc" : "sort_asc";
            } else {
                $columnicon = ($dir == "ASC") ? "sort_asc" : "sort_desc";
            }
            $columnicon = "<img class='iconsort' src=\"" . $OUTPUT->pix_url('t/' . $columnicon) . "\" alt=\"\" />";

        }
        $$column = "<a href=\"user.php?sort=$column&amp;dir=$columndir\">".$string[$column]."</a>$columnicon";
    }

    if ($sort == "name") {
        $sort = "firstname";
    }

    list($extrasql, $params) = $ufiltering->get_sql_filter();
    $users = get_users_listing($sort, $dir, $page*$perpage, $perpage, '', '', '',
            $extrasql, $params, $context);
    $usercount = get_users(false);
    $usersearchcount = get_users(false, '', false, null, "", '', '', '', '', '*', $extrasql, $params);

    if ($extrasql !== '') {
        echo $OUTPUT->heading("$usersearchcount / $usercount ".get_string('users'));
        $usercount = $usersearchcount;
    } else {
        echo $OUTPUT->heading("$usercount ".get_string('users'));
    }

    $strall = get_string('all');

    $baseurl = new moodle_url('/admin/user.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
    echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);

    flush();


    if (!$users) {
        $match = array();
        echo $OUTPUT->heading(get_string('nousersfound'));

        $table = NULL;

    } else {

        $countries = get_string_manager()->get_list_of_countries(false);
        if (empty($mnethosts)) {
            $mnethosts = $DB->get_records('mnet_host', null, 'id', 'id,wwwroot,name');
        }

        foreach ($users as $key => $user) {
            if (isset($countries[$user->country])) {
                $users[$key]->country = $countries[$user->country];
            }
        }
        if ($sort == "country") {  // Need to resort by full country name, not code
            foreach ($users as $user) {
                $susers[$user->id] = $user->country;
            }
            asort($susers);
            foreach ($susers as $key => $value) {
                $nusers[] = $users[$key];
            }
            $users = $nusers;
        }

        $override = new stdClass();
        $override->firstname = 'firstname';
        $override->lastname = 'lastname';
        $fullnamelanguage = get_string('fullnamedisplay', '', $override);
        if (($CFG->fullnamedisplay == 'firstname lastname') or
            ($CFG->fullnamedisplay == 'firstname') or
            ($CFG->fullnamedisplay == 'language' and $fullnamelanguage == 'firstname lastname' )) {
            $fullnamedisplay = "$firstname / $lastname";
        } else { // ($CFG->fullnamedisplay == 'language' and $fullnamelanguage == 'lastname firstname')
            $fullnamedisplay = "$lastname / $firstname";
        }

        $table = new html_table();
        $table->head = array ();
        $table->align = array();
        $table->head[] = $fullnamedisplay;
        $table->align[] = 'left';
        foreach ($extracolumns as $field) {
            $table->head[] = ${$field};
            $table->align[] = 'left';
        }
        $table->head[] = $city;
        $table->align[] = 'left';
        $table->head[] = $country;
        $table->align[] = 'left';
        $table->head[] = $lastaccess;
        $table->align[] = 'left';
        $table->head[] = get_string('edit');
        $table->align[] = 'center';
        $table->head[] = "";
        $table->align[] = 'center';

        $table->width = "95%";
        foreach ($users as $user) {
            if (isguestuser($user)) {
                continue; // do not display guest here
            }

            $buttons = array();
            $lastcolumn = '';

            // delete button
            if (has_capability('moodle/user:delete', $sitecontext)) {
                if (is_mnet_remote_user($user) or $user->id == $USER->id or is_siteadmin($user)) {
                    // no deleting of self, mnet accounts or admins allowed
                } else {
                    $buttons[] = html_writer::link(new moodle_url($returnurl, array('delete'=>$user->id, 'sesskey'=>sesskey())), html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/delete'), 'alt'=>$strdelete, 'class'=>'iconsmall')), array('title'=>$strdelete));
                }
            }

            // suspend button
            if (has_capability('moodle/user:update', $sitecontext)) {
                if (is_mnet_remote_user($user)) {
                    // mnet users have special access control, they can not be deleted the standard way or suspended
                    $accessctrl = 'allow';
                    if ($acl = $DB->get_record('mnet_sso_access_control', array('username'=>$user->username, 'mnet_host_id'=>$user->mnethostid))) {
                        $accessctrl = $acl->accessctrl;
                    }
                    $changeaccessto = ($accessctrl == 'deny' ? 'allow' : 'deny');
                    $buttons[] = " (<a href=\"?acl={$user->id}&amp;accessctrl=$changeaccessto&amp;sesskey=".sesskey()."\">".get_string($changeaccessto, 'mnet') . " access</a>)";

                } else {
                    if ($user->suspended) {
                        $buttons[] = html_writer::link(new moodle_url($returnurl, array('unsuspend'=>$user->id, 'sesskey'=>sesskey())), html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/show'), 'alt'=>$strunsuspend, 'class'=>'iconsmall')), array('title'=>$strunsuspend));
                    } else {
                        if ($user->id == $USER->id or is_siteadmin($user)) {
                            // no suspending of admins or self!
                        } else {
                            $buttons[] = html_writer::link(new moodle_url($returnurl, array('suspend'=>$user->id, 'sesskey'=>sesskey())), html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/hide'), 'alt'=>$strsuspend, 'class'=>'iconsmall')), array('title'=>$strsuspend));
                        }
                    }

                }
            }

            // edit button
            if (has_capability('moodle/user:update', $sitecontext)) {
                // prevent editing of admins by non-admins
                if (is_siteadmin($USER) or !is_siteadmin($user)) {
                    $buttons[] = html_writer::link(new moodle_url($securewwwroot.'/user/editadvanced.php', array('id'=>$user->id, 'course'=>$site->id)), html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/edit'), 'alt'=>$stredit, 'class'=>'iconsmall')), array('title'=>$stredit));
                }
            }

            // the last column - confirm or mnet info
            if (is_mnet_remote_user($user)) {
                // all mnet users are confirmed, let's print just the name of the host there
                if (isset($mnethosts[$user->mnethostid])) {
                    $lastcolumn = get_string($accessctrl, 'mnet').': '.$mnethosts[$user->mnethostid]->name;
                } else {
                    $lastcolumn = get_string($accessctrl, 'mnet');
                }

            } else if ($user->confirmed == 0) {
                if (has_capability('moodle/user:update', $sitecontext)) {
                    $lastcolumn = html_writer::link(new moodle_url($returnurl, array('confirmuser'=>$user->id, 'sesskey'=>sesskey())), $strconfirm);
                } else {
                    $lastcolumn = "<span class=\"dimmed_text\">".get_string('confirm')."</span>";
                }
            }

            if ($user->lastaccess) {
                $strlastaccess = format_time(time() - $user->lastaccess);
            } else {
                $strlastaccess = get_string('never');
            }
            $fullname = fullname($user, true);

            $row = array ();
            $row[] = "<a href=\"../user/view.php?id=$user->id&amp;course=$site->id\">$fullname</a>";
            foreach ($extracolumns as $field) {
                $row[] = $user->{$field};
            }
            $row[] = $user->city;
            $row[] = $user->country;
            $row[] = $strlastaccess;
            if ($user->suspended) {
                foreach ($row as $k=>$v) {
                    $row[$k] = html_writer::tag('span', $v, array('class'=>'usersuspended'));
                }
            }
            $row[] = implode(' ', $buttons);
            $row[] = $lastcolumn;
            $table->data[] = $row;
        }
    }

    // add filters
    $ufiltering->display_add();
    $ufiltering->display_active();

    if (has_capability('moodle/user:create', $sitecontext)) {
        echo $OUTPUT->heading('<a href="'.$securewwwroot.'/user/editadvanced.php?id=-1">'.get_string('addnewuser').'</a>');
    }
    if (!empty($table)) {
        echo html_writer::table($table);
        echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);
        if (has_capability('moodle/user:create', $sitecontext)) {
            echo $OUTPUT->heading('<a href="'.$securewwwroot.'/user/editadvanced.php?id=-1">'.get_string('addnewuser').'</a>');
        }
    }

    echo $OUTPUT->footer();



