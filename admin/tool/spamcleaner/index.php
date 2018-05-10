<?php

/**
 * Spam Cleaner
 *
 * Helps an admin to clean up spam in Moodle
 *
 * @author Dongsheng Cai
 * @author Martin Dougiamas
 * @author Amr Hourani
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

// List of known spammy keywords, please add more here

/////////////////////////////////////////////////////////////////////////////////

require_once('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');


// Configuration

$autokeywords = array(
                    "<img",
                    "fuck",
                    "casino",
                    "porn",
                    "xxx",
                    "cialis",
                    "viagra",
                    "poker",
                    "warcraft"
                );

$keyword = optional_param('keyword', '', PARAM_RAW);
$autodetect = optional_param('autodetect', '', PARAM_RAW);
$del = optional_param('del', '', PARAM_RAW);
$delall = optional_param('delall', '', PARAM_RAW);
$ignore = optional_param('ignore', '', PARAM_RAW);
$reset = optional_param('reset', '', PARAM_RAW);
$id = optional_param('id', '', PARAM_INT);

require_login();
admin_externalpage_setup('toolspamcleaner');

// Delete one user
if (!empty($del) && confirm_sesskey() && ($id != $USER->id)) {
    if (isset($SESSION->users_result[$id])) {
        $user = $SESSION->users_result[$id];
        if (delete_user($user)) {
            unset($SESSION->users_result[$id]);
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
    } else {
        echo json_encode(false);
    }
    exit;
}

// Delete lots of users
if (!empty($delall) && confirm_sesskey()) {
    if (!empty($SESSION->users_result)) {
        foreach ($SESSION->users_result as $userid => $user) {
            if ($userid != $USER->id) {
                if (delete_user($user)) {
                    unset($SESSION->users_result[$userid]);
                }
            }
        }
    }
    echo json_encode(true);
    exit;
}

if (!empty($ignore) && confirm_sesskey()) {
    unset($SESSION->users_result[$id]);
    echo json_encode(true);
    exit;
}

$PAGE->requires->js_init_call('M.tool_spamcleaner.init', array(me()), true);
$strings = Array('spaminvalidresult','spamdeleteallconfirm','spamcannotdelete','spamdeleteconfirm');
$PAGE->requires->strings_for_js($strings, 'tool_spamcleaner');

echo $OUTPUT->header();

// Print headers and things
echo $OUTPUT->box(get_string('spamcleanerintro', 'tool_spamcleaner'));

echo $OUTPUT->box_start();     // The forms section at the top

?>

<div class="mdl-align">

<form method="post" action="index.php" class="form-inline spamcleanerform">
  <div>
    <label class="accesshide" for="keyword_el"><?php print_string('spamkeyword', 'tool_spamcleaner') ?></label>
    <input type="text" class="form-control" name="keyword" id="keyword_el" value="<?php p($keyword) ?>" />
    <input type="hidden" name="sesskey" value="<?php echo sesskey();?>" />
    <input type="submit" class="btn btn-primary" value="<?php echo get_string('spamsearch', 'tool_spamcleaner')?>" />
  </div>
</form>
<p><?php echo get_string('spameg', 'tool_spamcleaner');?></p>

<hr />

<form method="post"  action="index.php">
  <div>
    <input type="submit" class="btn btn-primary" name="autodetect"
           value="<?php echo get_string('spamauto', 'tool_spamcleaner');?>" />
  </div>
</form>


</div>

<?php
echo $OUTPUT->box_end();

echo '<div id="result" class="mdl-align">';

// Print list of resulting profiles

if (!empty($keyword)) {               // Use the keyword(s) supplied by the user
    $keywords = explode(',', $keyword);
    foreach ($keywords as $key => $keyword) {
        $keywords[$key] = trim($keyword);
    }
    search_spammers($keywords);

} else if (!empty($autodetect)) {     // Use the inbuilt keyword list to detect users
    search_spammers($autokeywords);
}

echo '</div>';

/////////////////////////////////////////////////////////////////////////////////


///  Functions


function search_spammers($keywords) {

    global $CFG, $USER, $DB, $OUTPUT;

    if (!is_array($keywords)) {
        $keywords = array($keywords);    // Make it into an array
    }

    $params = array('userid'=>$USER->id);

    $keywordfull = array();
    $i = 0;
    foreach ($keywords as $keyword) {
        $keywordfull[] = $DB->sql_like('description', ':descpat'.$i, false);
        $params['descpat'.$i] = "%$keyword%";
        $keywordfull2[] = $DB->sql_like('p.summary', ':sumpat'.$i, false);
        $params['sumpat'.$i] = "%$keyword%";
        $keywordfull3[] = $DB->sql_like('p.subject', ':subpat'.$i, false);
        $params['subpat'.$i] = "%$keyword%";
        $keywordfull4[] = $DB->sql_like('c.content', ':contpat'.$i, false);
        $params['contpat'.$i] = "%$keyword%";
        $keywordfull5[] = $DB->sql_like('m.fullmessage', ':msgpat'.$i, false);
        $params['msgpat'.$i] = "%$keyword%";
        $keywordfull6[] = $DB->sql_like('fp.message', ':forumpostpat'.$i, false);
        $params['forumpostpat'.$i] = "%$keyword%";
        $keywordfull7[] = $DB->sql_like('fp.subject', ':forumpostsubpat'.$i, false);
        $params['forumpostsubpat'.$i] = "%$keyword%";
        $i++;
    }
    $conditions = '( '.implode(' OR ', $keywordfull).' )';
    $conditions2 = '( '.implode(' OR ', $keywordfull2).' )';
    $conditions3 = '( '.implode(' OR ', $keywordfull3).' )';
    $conditions4 = '( '.implode(' OR ', $keywordfull4).' )';
    $conditions5 = '( '.implode(' OR ', $keywordfull5).' )';
    $conditions6 = '( '.implode(' OR ', $keywordfull6).' )';
    $conditions7 = '( '.implode(' OR ', $keywordfull7).' )';

    $sql  = "SELECT *
               FROM {user}
              WHERE deleted = 0
                    AND id <> :userid
                    AND $conditions";  // Exclude oneself
    $sql2 = "SELECT u.*, p.summary
               FROM {user} u, {post} p
              WHERE $conditions2
                    AND u.deleted = 0
                    AND u.id=p.userid
                    AND u.id <> :userid";
    $sql3 = "SELECT u.*, p.subject AS postsubject
               FROM {user} u, {post} p
              WHERE $conditions3
                    AND u.deleted = 0
                    AND u.id=p.userid
                    AND u.id <> :userid";
    $sql4 = "SELECT u.*, c.content
               FROM {user} u, {comments} c
               WHERE $conditions4
                    AND u.deleted = 0
                    AND u.id=c.userid
                    AND u.id <> :userid";
    $sql5 = "SELECT u.*, m.fullmessage
               FROM {user} u, {message} m
              WHERE $conditions5
                    AND u.deleted = 0
                    AND u.id=m.useridfrom
                    AND u.id <> :userid";
    $sql6 = "SELECT u.*, fp.message
               FROM {user} u, {forum_posts} fp
              WHERE $conditions6
                    AND u.deleted = 0
                    AND u.id=fp.userid
                    AND u.id <> :userid";
    $sql7 = "SELECT u.*, fp.subject
               FROM {user} u, {forum_posts} fp
              WHERE $conditions7
                    AND u.deleted = 0
                    AND u.id=fp.userid
                    AND u.id <> :userid";

    $spamusers_desc = $DB->get_recordset_sql($sql, $params);
    $spamusers_blog = $DB->get_recordset_sql($sql2, $params);
    $spamusers_blogsub = $DB->get_recordset_sql($sql3, $params);
    $spamusers_comment = $DB->get_recordset_sql($sql4, $params);
    $spamusers_message = $DB->get_recordset_sql($sql5, $params);
    $spamusers_forumpost = $DB->get_recordset_sql($sql6, $params);
    $spamusers_forumpostsub = $DB->get_recordset_sql($sql7, $params);

    $keywordlist = implode(', ', $keywords);
    echo $OUTPUT->box(get_string('spamresult', 'tool_spamcleaner').s($keywordlist)).' ...';

    $recordsets = [
        $spamusers_desc,
        $spamusers_blog,
        $spamusers_blogsub,
        $spamusers_comment,
        $spamusers_message,
        $spamusers_forumpost,
        $spamusers_forumpostsub
    ];
    print_user_list($recordsets, $keywords);
    foreach ($recordsets as $rs) {
        $rs->close();
    }
}



function print_user_list($users_rs, $keywords) {
    global $CFG, $SESSION;

    // reset session everytime this function is called
    $SESSION->users_result = array();
    $count = 0;

    foreach ($users_rs as $rs){
        foreach ($rs as $user) {
            if (!$count) {
                echo '<table class="table table-bordered" border="1" width="100%" id="data-grid"><tr><th>&nbsp;</th>
                    <th>'.get_string('user', 'admin').'</th><th>'.get_string('spamdesc', 'tool_spamcleaner').'</th>
                    <th>'.get_string('spamoperation', 'tool_spamcleaner').'</th></tr>';
            }
            $count++;
            filter_user($user, $keywords, $count);
        }
    }

    if (!$count) {
        echo get_string('spamcannotfinduser', 'tool_spamcleaner');

    } else {
        echo '</table>';
        echo '<div class="mld-align">
              <button id="removeall_btn" class="btn btn-secondary">'.get_string('spamdeleteall', 'tool_spamcleaner').'</button>
              </div>';
    }
}
function filter_user($user, $keywords, $count) {
    global $CFG;
    $image_search = false;
    if (in_array('<img', $keywords)) {
        $image_search = true;
    }
    if (isset($user->summary)) {
        $user->description = '<h3>'.get_string('spamfromblog', 'tool_spamcleaner').'</h3>'.$user->summary;
        unset($user->summary);
    } else if (isset($user->postsubject)) {
        $user->description = '<h3>'.get_string('spamfromblog', 'tool_spamcleaner').'</h3>'.$user->postsubject;
        unset($user->postsubject);
    } else if (isset($user->content)) {
        $user->description = '<h3>'.get_string('spamfromcomments', 'tool_spamcleaner').'</h3>'.$user->content;
        unset($user->content);
    } else if (isset($user->fullmessage)) {
        $user->description = '<h3>'.get_string('spamfrommessages', 'tool_spamcleaner').'</h3>'.$user->fullmessage;
        unset($user->fullmessage);
    } else if (isset($user->message)) {
        $user->description = '<h3>'.get_string('spamfromforumpost', 'tool_spamcleaner').'</h3>'.$user->message;
        unset($user->message);
    } else if (isset($user->subject)) {
        $user->description = '<h3>'.get_string('spamfromforumpost', 'tool_spamcleaner').'</h3>'.$user->subject;
        unset($user->subject);
    }

    if (preg_match('#<img.*src=[\"\']('.$CFG->wwwroot.')#', $user->description, $matches)
        && $image_search) {
        $result = false;
        foreach ($keywords as $keyword) {
            if (preg_match('#'.$keyword.'#', $user->description)
                && ($keyword != '<img')) {
                $result = true;
            }
        }
        if ($result) {
            echo print_user_entry($user, $keywords, $count);
        } else {
            unset($user);
        }
    } else {
        echo print_user_entry($user, $keywords, $count);
    }
}


function print_user_entry($user, $keywords, $count) {

    global $SESSION, $CFG;

    $smalluserobject = new stdClass();      // All we need to delete them later
    $smalluserobject->id = $user->id;
    $smalluserobject->email = $user->email;
    $smalluserobject->auth = $user->auth;
    $smalluserobject->firstname = $user->firstname;
    $smalluserobject->lastname = $user->lastname;
    $smalluserobject->username = $user->username;

    if (empty($SESSION->users_result[$user->id])) {
        $SESSION->users_result[$user->id] = $smalluserobject;
        $html = '<tr valign="top" id="row-'.$user->id.'" class="result-row">';
        $html .= '<td width="10">'.$count.'</td>';
        $html .= '<td width="30%" align="left"><a href="'.$CFG->wwwroot."/user/view.php?course=1&amp;id=".$user->id.'" title="'.s($user->username).'">'.fullname($user).'</a>';

        $html .= "<ul>";
        $profile_set = array('city'=>true, 'country'=>true, 'email'=>true);
        foreach ($profile_set as $key=>$value) {
            if (isset($user->$key)){
                $html .= '<li>'.$user->$key.'</li>';
            }
        }
        $html .= "</ul>";
        $html .= '</td>';

        foreach ($keywords as $keyword) {
            $user->description = highlight($keyword, $user->description);
        }

        if (!isset($user->descriptionformat)) {
            $user->descriptionformat = FORMAT_MOODLE;
        }

        $html .= '<td align="left">'.format_text($user->description, $user->descriptionformat, array('overflowdiv'=>true)).'</td>';
        $html .= '<td width="100px" align="center">';
        $html .= '<button class="btn btn-primary" onclick="M.tool_spamcleaner.del_user(this,'.$user->id.')">'.
            get_string('deleteuser', 'admin').'</button><br />';
        $html .= '<button class="btn btn-secondary" onclick="M.tool_spamcleaner.ignore_user(this,'.$user->id.')">'.
            get_string('ignore', 'admin').'</button>';
        $html .= '</td>';
        $html .= '</tr>';
        return $html;
    } else {
        return null;
    }


}

echo $OUTPUT->footer();
