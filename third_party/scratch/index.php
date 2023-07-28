<?php
require('../../config.php');
$PAGE->set_pagelayout('thirdparty');
echo $OUTPUT->header();
?>

<script type="text/javascript" src="lib.min.js"></script><script type="text/javascript" src="chunks/gui.js"></script>

<?php
$context = new stdClass;
echo $OUTPUT->render_from_template("local_qubitssite/scratch", $context);
echo $OUTPUT->footer();