<?php // $Id$
    require('../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    $iplist = optional_param('list', '', PARAM_CLEAN);
    admin_externalpage_setup('ipblocker');

    if ($form = data_submitted()) {
        if (confirm_sesskey()) {
            $ips = explode("\n", $iplist); 
            $result = array();
            foreach($ips as $ip) {
                if(preg_match('#^(\d{1,3})(\.\d{1,3}){0,3}$#', $ip, $match) ||
                       preg_match('#^(\d{1,3})(\.\d{1,3}){0,3}(\/\d{1,2})$#', $ip, $match) ||
                       preg_match('#^(\d{1,3})(\.\d{1,3}){3}(-\d{1,3})$#', $ip, $match)) {
                    $result[] = $ip;
                }
            }
            set_config('blockedip', serialize($result));
        }
    }

    admin_externalpage_print_header();
    $iplist = unserialize(get_config(null, 'blockedip'));
    if(empty($iplist)) {
        $iplist = array();
    }
    $str = '';
    foreach($iplist as $ip){
        $str .= $ip."\n";
    }

    echo '<div style="text-align:center;">';
    echo '<form method="post">';
    echo '<h1>'.get_string('blockediplist', 'admin').'</h1>';
    print_textarea(false, 20, 50, 600, 400, "list", $str);
    echo '<p><input type="hidden" name="sesskey" value="'.sesskey().'" />';
    echo '<input type="submit" value="'.get_string('submit').'" />';
    echo helpbutton('blockip', 'Help');
    echo '</p>';
    echo '</form>';
    echo '</div>';

    admin_externalpage_print_footer();
?>
