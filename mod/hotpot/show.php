<?php

    require_once("../../config.php");
    require_once("lib.php");

    $params = new stdClass();
    $params->action = required_param('action', PARAM_ALPHA);
    $params->course = required_param('course', PARAM_INT);
    $params->reference = required_param('reference', PARAM_PATH);

    $PAGE->set_url('/mod/hotpot/show.php', array('action'=>$params->action, 'course'=>$params->course, 'reference'=>$params->reference));

    require_login($params->course);

    if (!has_capability('mod/hotpot:viewreport',get_context_instance(CONTEXT_COURSE, $params->course))) {
        print_error('nopermissiontoviewpage');
    }
    if (has_capability('mod/hotpot:viewreport', get_context_instance(CONTEXT_SYSTEM))) {
        $params->location = optional_param('location', HOTPOT_LOCATION_COURSEFILES, PARAM_INT);
    } else {
        $params->location = HOTPOT_LOCATION_COURSEFILES;
    }
    $title = get_string($params->action, 'hotpot').': '.$params->reference;
    $PAGE->set_title($title);
    $PAGE->set_heading($title);
    echo $OUTPUT->header();
    hotpot_print_show_links($params->course, $params->location, $params->reference);
?>
<script type="text/javascript">
//<![CDATA[
    // http://www.krikkit.net/howto_javascript_copy_clipboard.html
    function copy_contents(id) {
        if (id==null) {
            id = 'contents';
        }
        var obj = null;
        if (document.getElementById) {
            obj = document.getElementById(id);
        }
        if (obj && window.clipboardData) {
            window.clipboardData.setData("Text", obj.innerText);
            alert('<?php print_string('copiedtoclipboard', 'hotpot') ?>');
        }
    }
    document.write('<span class="helplink"> &nbsp; <a href="javascript:copy_contents()"><?php print_string('copytoclipboard', 'hotpot') ?></A></span>');
//]]>
</script>
<?php
    echo $OUTPUT->box_start("generalbox boxaligncenter boxwidthwide");
    if($hp = new hotpot_xml_quiz($params)) {
        print '<pre id="contents">';
        switch ($params->action) {
            case 'showxmlsource':
                print htmlspecialchars($hp->source);
                break;
            case 'showxmltree':
                if (isset($hp->xml)) {
                    print_r($hp->xml);
                }
                break;
            case 'showhtmlsource':
                print htmlspecialchars($hp->html);
                break;
            case 'showhtmlquiz':
                print $hp->html;
                break;
        }
        print '</pre>';
    } else {
        echo $OUTPUT->box("Could not open Hot Potatoes XML file", "errorboxcontent generalbox");
    }
    echo $OUTPUT->box_end();
    print '<br />';
    echo $OUTPUT->close_window_button();
?>
