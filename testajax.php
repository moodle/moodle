<?php

require("./config.php");

echo $OUTPUT->header(); 

echo "<div id='testcnt'>Jai Sree Hari</div>";

?>



<?php

echo $OUTPUT->footer();

?>

<script>
//<![CDATA[
require(['jquery'], function($){
	let offset = 0
	let limit = 6
	let payload = [
                {
                    "index": 0,
                    "methodname": "mod_qbassign_save_studentsubmission",
                    "args": {
                        "qbassignmentid": 60,
                        "plugindata_text": "My submission-mini",
                        "plugindata_format": 1,
                        "plugindata_type": "onlinetext"
                    }
                }
            ];
	let mkey = M.cfg.sesskey;
	//mkey = "sjdhaksjda";
	let aurl = M.cfg.wwwroot + "/lib/ajax/service-react.php?sesskey=" + mkey;
	aurl += '&info=mod_qbassign_save_studentsubmission';
	$.ajax({
		type: "POST",
		url: aurl,
		data : JSON.stringify(payload),
        contentType : "application/json"
	}).done(function(resp){
		//let respObj = JSON.parse(resp);
		console.log("Sree Hari - Master is the greatest developer in the world")
		console.log(resp)
	});
  
});
//]]>
</script>
