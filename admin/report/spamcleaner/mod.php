<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

$keyword = optional_param('keyword', '', PARAM_RAW);
$autodetect = optional_param('autodetect', '', PARAM_RAW);
$reset = optional_param('reset', '', PARAM_RAW);
$id = optional_param('id', '', PARAM_INT);

// List of known spammy keywords, please add more here

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

// Print headers and things

print_heading(get_string('spamcleaner', 'admin'));
print_spamcleaner_javascript();

print_simple_box(get_string('spamcleanerintro', 'report_spamcleaner'));
    
?>

<div class="mdl-align">

<form method="post" action="report.php">
  <div>
    <input type="text" name="keyword" id="keyword_el" value="<?php p($keyword) ?>" /> 
    <input type="hidden" name="sesskey" value="<?php echo sesskey();?>" />
    <input type="submit" value="<?php echo get_string('spamsearch', 'report_spamcleaner')?>" />
  </div>
</form>
<p><?php echo get_string('spameg', 'report_spamcleaner');?></p>

<hr />

<form method="post"  action="report.php">
  <div>
    <input type="submit" name="autodetect" value="<?php echo get_string('spamauto', 'report_spamcleaner');?>" />
  </div>
</form>


</div>

<?php

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

    global $CFG, $USER, $DB; 

    if (!is_array($keywords)) {
        $keywords = array($keywords);    // Make it into an array
    }

    $like = sql_ilike();

    $keywordfull = array();
    foreach ($keywords as $keyword) {
        $keyword = addslashes($keyword);   // Just to be safe
        $keywordfull[] = " description $like '%$keyword%' ";
        $keywordfull2[] = " p.summary $like '%$keyword%' ";
    }
    $conditions = '( '.implode(' OR ', $keywordfull).' )';
    $conditions2 = '( '.implode(' OR ', $keywordfull2).' )';

    $sql = "SELECT * FROM {$CFG->prefix}user WHERE deleted = 0 AND id <> {$USER->id} AND $conditions";  // Exclude oneself
    $sql2= "SELECT u.*, p.summary FROM {$CFG->prefix}user AS u, {$CFG->prefix}post AS p WHERE $conditions2 AND u.deleted = 0 AND u.id=p.userid AND u.id <> {$USER->id}";
    $spamusers_desc = get_recordset_sql($sql);
    $spamusers_blog = get_recordset_sql($sql2);

    $keywordlist = implode(', ', $keywords);
    print_simple_box(get_string('spamresult', 'report_spamcleaner').s($keywordlist)).' ...';

    print_user_list(array($spamusers_desc, $spamusers_blog), $keywords);

}

function print_user_list($users_rs, $keywords) {
    global $CFG, $SESSION;

    // reset session everytime this function is called
    $SESSION->users_result = array();
    $count = 0;

    foreach ($users_rs as $key=>$rs) {
         while(!$rs->EOF) { 
            $user = (object)$rs->fields;
            if (!$count) {
                echo '<table border="1" width="100%" id="data-grid"><tr><th>&nbsp;</th><th>'.get_string('user','admin').'</th><th>'.get_string('spamdesc', 'report_spamcleaner').'</th><th>'.get_string('spamoperation', 'report_spamcleaner').'</th></tr>';
            }
            $rs->MoveNext();
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
    if (preg_match('#<img.*src=[\"\']('.$CFG->pixpath.')#', $user->description, $matches)
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

    $smalluserobject = new object;      // All we need to delete them later
    $smalluserobject->id = $user->id;
    $smalluserobject->email = $user->email;
    $smalluserobject->auth = $user->auth;
    $smalluserobject->firstname = $user->firstname;
    $smalluserobject->lastname = $user->lastname;

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

        $html .= '<td align="left">'.format_text($user->description, FORMAT_MOODLE).'</td>';
        $html .= '<td width="100px" align="center">';
        $html .= '<button onclick="del_user(this,'.$user->id.')">'.get_string('deleteuser', 'admin').'</button><br />';
        $html .= '<button onclick="ignore_user(this,'.$user->id.')">'.get_string('ignore', 'admin').'</button>';
        $html .= '</td>';
        $html .= '</tr>';
        return $html;
    } else {
        return null;
    }


}

function print_spamcleaner_javascript()  {
    global $CFG;

$sesskey = sesskey();

?>
<script type="text/javascript">
//<![CDATA[
var service_url = '<?php echo $CFG->wwwroot;?>/admin/report/spamcleaner/index.php';
function ajax(url, callback)
{
    method = 'POST';
    if (window.XMLHttpRequest) {
        var req = new XMLHttpRequest();
    } else {
        var MSXML = ['MSXML2.XMLHTTP.6.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP.5.0', 'MSXML2.XMLHTTP.4.0', 'MSXML2.XMLHTTP', 'Microsoft.XMLHTTP'];
        for(var n = 0; n < MSXML.length; n ++) {
            try {
                var req = new ActiveXObject(MSXML[n]);       
                break;
            } catch(e) {
            }
        }
    }
    with(req) {
        try {
            open(method, url, true);
            setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            send(null);           
        } catch(e) {
            alert(e);
        }
        onreadystatechange = function()
        {
            if (req.readyState == 4)
            {
                callback(req.responseText, req.status);
                delete(req);
            }
        }
    }
}
var row = null;
function delall_cb (text) {
    if(text){
        window.location.href=window.location.href;
    }
}
function init() {
    var remove_all = document.getElementById('removeall_btn');
    if(remove_all){
        remove_all.onclick = function(){
            var yes = confirm('<?php echo get_string('spamdeleteallconfirm', 'report_spamcleaner');?>');
            if(yes){
                ajax(service_url + '?delall=yes&sesskey=<?php echo $sesskey;?>', delall_cb);
            }
        }
    }
}
function del_cb (text) {
    if(row) {
        if(text){
            while(row.tagName != 'TR') {
                row = row.parentNode;
            }
            row.parentNode.removeChild(row);
            row = null;
        } else {
            alert('<?php echo get_string('spamcannotdelete', 'report_spamcleaner');?>');
        }
    }
}
function ignore_cb (text) {
        if(row) {
            if(text){
                while(row.tagName != 'TR') {
                    row = row.parentNode;
                }
                row.parentNode.removeChild(row);
                row = null;
            }
        }
}
function del_user(obj, id) {
    var yes = confirm('<?php echo get_string('spamdeleteconfirm', 'report_spamcleaner');?>');
    if(yes){
        row = obj;
        ajax(service_url + '?del=yes&sesskey=<?php echo $sesskey;?>&id='+id, del_cb);
    }
}
function ignore_user(obj, id) {
    row = obj;
    ajax(service_url + '?ignore=yes&sesskey=<?php echo $sesskey;?>&id='+id, ignore_cb);
}
window.onload = function(){
    init();
}
//]]>
</script>

<?php 
}
?>
