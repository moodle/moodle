<?php
require('../config.php');
echo $OUTPUT->header();
?>
<p>Test</p>


<?php
echo $OUTPUT->footer();
?>

<script>
//<![CDATA[
require(['jquery'], function($) {
    let assurl = `${M.cfg.wwwroot}/lib/ajax/service.php?sesskey=${M.cfg.sesskey}&info=local_qubitsbook_get_assignment_service`;
	console.log(assurl);
});
//]]>
</script>