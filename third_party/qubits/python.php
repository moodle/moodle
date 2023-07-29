<?php
require('../../config.php');
$PAGE->set_pagelayout('thirdparty');
echo $OUTPUT->header();

?>
<p>Python Editor</p>
<div id="__next"></div>

 <script id="__NEXT_DATA__" type="application/json">
  {
	"props": {
	  "pageProps": {},
	  "__N_SSG": true
	},
	"page": "/Editor/[language]",
	"query": {
	  "language": "python"
	},
	"buildId": "BXl1ZtTDTb9tENltKnSWf",
	"isFallback": false,
	"gsp": true,
	"scriptLoader": []
  }
</script>

<?php

echo $OUTPUT->footer();

?>
