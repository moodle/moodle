<?php
    require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/config.php');
    require_login();
    global $PAGE;
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('embedded');
    echo $OUTPUT->header();
    $requestQueryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : "";
    parse_str($requestQueryString, $params);
    $ltibrowseUrl = new moodle_url('ltibrowse.php', $params);
?>

<iframe allow="autoplay *; fullscreen *; encrypted-media *; camera *; microphone *;" id="kafIframe" src="<?php echo $ltibrowseUrl->out(); ?>" width="100%" height="600" style="border: 0;" allowfullscreen>
</iframe>
<script>
    var buttonJs = window.opener.buttonJs;

    function kaltura_atto_embed(data) {
        buttonJs.embedItem(buttonJs, data);
    }
</script>
