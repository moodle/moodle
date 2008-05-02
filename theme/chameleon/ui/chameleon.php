<?php

   
if (!empty($THEME->chameleonenabled)) {
    $chameleon_isadmin = has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));
    $chameleon_isteacher = false;
    if ($COURSE->id == SITEID) {
        $chameleon_courseparam = '';
    } else {
        $chameleon_courseparam = '?id=' . $COURSE->id;
        $chameleon_isteacher = !empty($COURSE->theme) and has_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $COURSE->id));
    }
    
    if ($chameleon_isadmin or ($chameleon_isteacher and !empty($CFG->allowcoursethemes) and !empty($THEME->chameleonteachereditenabled))) { 
        // either we're an admin or we're a teacher and this is being used as the course theme
        // if we're on a page using a course theme edit that, otherwise edit the main chameleon theme
        // $chameleon_theme = (isset($CFG->coursetheme)) ? $CFG->coursetheme : $CFG->theme;
        $chameleon_theme = current_theme();
?>

<style type="text/css"> @import '<?php echo "$CFG->themewww/$chameleon_theme" ?>/ui/chameleon_ui.css'; </style>

<script type="text/javascript" src="<?php echo "$CFG->themewww/$chameleon_theme/ui/css_query.js" ?>"> </script>
<script type="text/javascript" src="<?php echo "$CFG->themewww/$chameleon_theme/ui/sarissa.js" ?>"> </script>
<script type="text/javascript" src="<?php echo "$CFG->themewww/$chameleon_theme/ui/chameleon_js.php$chameleon_courseparam" ?>"> </script>

<?php
    }
}
?>
