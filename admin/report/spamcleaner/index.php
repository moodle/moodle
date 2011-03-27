<?php

/**
 * Spam Cleaner
 *
 * Helps an admin to clean up spam in Moodle
 *
 * @authors Dongsheng Cai, Martin Dougiamas, Amr Hourani
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
admin_externalpage_setup('reportspamcleaner');

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

if (!empty($ignore)) {
    unset($SESSION->users_result[$id]);
    echo json_encode(true);
    exit;
}

$PAGE->requires->js_init_call('M.report_spamcleaner.init', array(me()), true);
$strings = Array('spaminvalidresult','spamdeleteallconfirm','spamcannotdelete','spamdeleteconfirm');
$PAGE->requires->strings_for_js($strings, 'report_spamcleaner');

echo $OUTPUT->header();

// Print headers and things
echo $OUTPUT->box(get_string('spamcleanerintro', 'report_spamcleaner'));

echo $OUTPUT->box_start();     // The forms section at the top

?>

<div class="mdl-align">

<form method="post" action="index.php">
  <div>
    <input type="text" name="keyword" id="keyword_el" value="<?php p($keyword) ?>" />
    <input type="hidden" name="sesskey" value="<?php echo sesskey();?>" />
    <input type="submit" value="<?php echo get_string('spamsearch', 'report_spamcleaner')?>" />
  </div>
</form>
<p><?php echo get_string('spameg', 'report_spamcleaner');?></p>

<hr />

<form method="post"  action="index.php">
  <div>
    <input type="submit" name="autodetect" value="<?php echo get_string('spamauto', 'report_spamcleaner');?>" />
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
        $i++;
    }
    $conditions = '( '.implode(' OR ', $keywordfull).' )';
    $conditions2 = '( '.implode(' OR ', $keywordfull2).' )';

    $sql  = "SELECT * FROM {user} WHERE deleted = 0 AND id <> :userid AND $conditions";  // Exclude oneself
    $sql2 = "SELECT u.*, p.summary FROM {user} AS u, {post} AS p WHERE $conditions2 AND u.deleted = 0 AND u.id=p.userid AND u.id <> :userid";
    $spamusers_desc = $DB->get_recordset_sql($sql, $params);
    $spamusers_blog = $DB->get_recordset_sql($sql2, $params);

    $keywordlist = implode(', ', $keywords);
    echo $OUTPUT->box(get_string('spamresult', 'report_spamcleaner').s($keywordlist)).' ...';

    print_user_list(array($spamusers_desc, $spamusers_blog), $keywords);

}



function print_user_list($users_rs, $keywords) {
    global $CFG, $SESSION;

    // reset session everytime this function is called
    $SESSION->users_result = array();
    $count = 0;

    foreach ($users_rs as $rs){
        foreach ($rs as $user) {
            if (!$count) {
                echo '<table border="1" width="100%" id="data-grid"><tr><th>&nbsp;</th><th>'.get_string('user','admin').'</th><th>'.get_string('spamdesc', 'report_spamcleaner').'</th><th>'.get_string('spamoperation', 'report_spamcleaner').'</th></tr>';
            }
            $count++;
            filter_user($user, $keywords, $count);
        }
    }

    if (!$count) {
        echo get_string('spamcannotfinduser', 'report_spamcleaner');

    } else {
        echo '</table>';
        echo '<div class="mld-align">
              <button id="removeall_btn">'.get_string('spamdeleteall', 'report_spamcleaner').'</button>
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
        $user->description = '<h3>'.get_string('spamfromblog', 'report_spamcleaner').'</h3>'.$user->summary;
        unset($user->summary);
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
        $html .= '<button onclick="M.report_spamcleaner.del_user(this,'.$user->id.')">'.get_string('deleteuser', 'admin').'</button><br />';
        $html .= '<button onclick="M.report_spamcleaner.ignore_user(this,'.$user->id.')">'.get_string('ignore', 'admin').'</button>';
        $html .= '</td>';
        $html .= '</tr>';
        return $html;
    } else {
        return null;
    }


}

echo $OUTPUT->footer();
