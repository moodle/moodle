<?php
    require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/config.php');
    require_login();
    global $PAGE;
    $PAGE->set_context(context_system::instance()); 
    $PAGE->set_pagelayout('embedded');
    echo $OUTPUT->header();
    $requestQueryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : "";

?>

<iframe id="kafIframe" src="ltibrowse.php?<?php echo $requestQueryString; ?>" width="100%" height="600" style="border: 0;" allowfullscreen>
</iframe>
<div id="kalturaMediaSubmitButton"></div>
<script>
    var buttonJs = window.opener.buttonJs;
    var embedButton = Y.Node.create('<button></button>');
    embedButton.setAttribute('id', 'KalturaMediaSubmit');
    embedButton.setAttribute('disabled', 'disabled');
    embedButton.setHTML("<?php echo get_string('embedbuttontext', 'atto_kalturamedia'); ?>");
    embedButton.hide();
    Y.one("#kalturaMediaSubmitButton").append(embedButton);
    function kaltura_atto_embed_callback(data)
    {
        var button = Y.one('#KalturaMediaSubmit');
        for(param in data)
        {
            var attributeName = 'data-embedinfo-'+param;
            button.setAttribute(attributeName, data[param]);
        }
        button.removeAttribute('disabled');
        button.show();

        embedButton.on('click', buttonJs.embedItem, buttonJs, button._getDataAttributes());
    }

    function getEmbedInfo(data) {
        return embedInfo;
    }
</script>
