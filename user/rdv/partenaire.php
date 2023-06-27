<!DOCTYPE html>
<head>
    <meta charset="utf-8" />
    <title>Visioconférence partenaires</title> 
</head>
<?php
require_once(dirname(__FILE__) . '/../../config.php');
$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
$PAGE->set_url('/user/rdv/partenaire.php');
echo $OUTPUT->header();
$PAGE->set_heading("Visioconférence partenaires");
?>
<body>
<div class="container">
<iframe src="https://www.smartagenda.fr/pro/infans-partenaires/rendez-vous/"></iframe>
    </div>
<?php 
   echo $OUTPUT->footer(); 
?>
</body>


</html>
<style>

</style>
