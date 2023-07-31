<?php
require('../../config.php');
$elanguage      = optional_param('language', '', PARAM_TEXT);
$PAGE->set_pagelayout('thirdparty');
echo $OUTPUT->header();

?>
<div id="__next"></div>

<script id="__NEXT_DATA__" type="application/json">
	{
		"props": {
			"pageProps": {},
			"__N_SSG": true
		},


		"page": "/pdf",
		"query": {
			"course": "DigiPro",
			"Level": "Level1",
			"pdf": "DPL01_U01_S02"
		},

		"buildId": "G7GxyPIfk8vrAIEzQ0oJE",
		"isFallback": false,
		"gsp": true,
		"scriptLoader": []
	}
</script>

<?php

echo $OUTPUT->footer();

?>