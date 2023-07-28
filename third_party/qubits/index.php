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
	"page": "/Editor/[language]",
	"query": {
	  "language": "<?php echo $elanguage; ?>"
	},
	"buildId": "LtlwT_pwV8l8pbM_OcXTM",
	"isFallback": false,
	"gsp": true,
	"scriptLoader": []
  }
</script>

<?php

echo $OUTPUT->footer();

?>
