<?php
require('../../config.php');
require_login();
$PAGE->set_pagelayout('thirdparty');
echo $OUTPUT->header();
?>
<script src="js/lib/grapes.min.js"></script>
<link rel="stylesheet" href="css/lib/grapes.min.css" />

<!-- Add Style and Script for Preset Webpage Builder -->
<script src="js/lib/grapesjs-preset-webpage.min.js"></script>
<link rel="stylesheet" href="css/lib/grapesjs-preset-webpage.min.css" />

<link rel="stylesheet" href="css/main.css" />

 <div id="editor">
      <p>Web Builder</p>
</div>

    <script type="text/javascript" src="js/main.js"></script>

<?php
echo $OUTPUT->footer();