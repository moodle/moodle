<?php 

require("./config.php");

echo $OUTPUT->header();

echo "<div id='testcnt'>TEST Page</div>";

?>



<?php

echo $OUTPUT->footer();

?>

<script>
//<![CDATA[
require(['jquery'], function($){
	let offset = 0;
	let limit = 6;
	let payload ={
                    "wstoken":"4dc910697d23e14c95c6085518482695",
                    "moodlewsrestformat":"json",
                    "wsfunction":"mod_qbassign_save_submission",
                    "qbassignmentid":45,
                    "plugindata":{"onlinetex_editor":{"text":"select * from table where name=mani","format":1,"itemid":1}}
                }
            
	let mkey = M.cfg.sesskey;
	//mkey = "sjdhaksjda";
	let aurl = M.cfg.wwwroot + "/lib/ajax/service-react.php?sesskey=" + mkey;
	aurl += '&info=mod_qbassign_save_submission';
	$.ajax({
		type: "POST",
		url: aurl,
		data : JSON.stringify(payload),
        contentType : "application/json"
	}).done(function(resp){
		//let respObj = JSON.parse(resp);
		//console.log("Sree Hari - Master is the greatest developer in the world")
		console.log(resp)
	});
  
});
//]]>
</script>
