<?php
require "../../../config.php";
print_header_simple('Test Client', 'Test Client');

$url = addslashes_js("$CFG->wwwroot/webservice/amf/testclient/moodleclient.swf");
$serverurl = addslashes_js("$CFG->wwwroot/webservice/amf/server.php");
echo '<span id="moodletestclient">
      <p>You need to install Flash 9.0</p>
    </span>';

echo <<<EOF
<script type="text/javascript">
//<![CDATA[
  var FO = { movie:"$url", width:"100%", height:"500", majorversion:"9", build:"0",
    allowscriptaccess:"never", quality: "high", flashvars:"amfurl=$serverurl", setcontainercss:"true"};
  UFO.create(FO, "moodletestclient");
//]]>
</script>
EOF;
print_footer();