<?php
require('../../config.php');
require_login();
$elanguage      = optional_param('language', '', PARAM_TEXT);
$clsname = 'page-editor-qubits-'.$elanguage;
$PAGE->set_pagelayout('thirdparty');
$PAGE->add_body_class($clsname);
echo $OUTPUT->header();
?>
<div id="__next"></div>
<?php if($elanguage=="webeditor") { ?>
<script id="__NEXT_DATA__" type="application/json">
	{
	"props": {
		"pageProps": {}
	},
	"page": "/CloudIDE",
	"query": {},
	"buildId": "Uax2aoFve0fGjmf5qSMjb",
	"nextExport": true,
	"autoExport": true,
	"isFallback": false,
	"scriptLoader": []
	}
</script>
<?php
} else { ?>
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

		"buildId": "Uax2aoFve0fGjmf5qSMjb",
		"isFallback": false,
		"gsp": true,
		"scriptLoader": []
	}
</script>
<?php }
echo $OUTPUT->footer();

?>