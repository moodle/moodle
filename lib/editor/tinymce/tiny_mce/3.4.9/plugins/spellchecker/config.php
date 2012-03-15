<?php
/**
 * config.php
 *
 * @package MCManager.includes
 */

require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))))).'/config.php'); // moodle hack
@error_reporting(E_ALL ^ E_NOTICE); // hide notices even if Moodle is configured to show them

	// General settings
	$config['general.engine'] = get_config('editor_tinymce', 'spellengine') ? get_config('editor_tinymce', 'spellengine') : 'GoogleSpell';
	//$config['general.engine'] = 'PSpell';
	//$config['general.engine'] = 'PSpellShell';
	//$config['general.remote_rpc_url'] = 'http://some.other.site/some/url/rpc.php';

	// GoogleSpell settings
	$config['GoogleSpell.proxyhost'] = isset($CFG->proxyhost) ? $CFG->proxyhost : '';
	$config['GoogleSpell.proxyport'] = isset($CFG->proxyport) ? $CFG->proxyport : '';
	$config['GoogleSpell.proxytype'] = isset($CFG->proxytype) ? $CFG->proxytype : 'HTML';
	$config['GoogleSpell.proxyuser'] = isset($CFG->proxyuser) ? $CFG->proxyuser : '';
	$config['GoogleSpell.proxypassword'] = isset($CFG->proxypassword) ? $CFG->proxypassword : '';

if ($config['general.engine'] === 'PSpell' or $config['general.engine'] === 'PSpellShell') { // moodlehack
	// PSpell settings
	$config['PSpell.mode'] = PSPELL_FAST;
	$config['PSpell.spelling'] = "";
	$config['PSpell.jargon'] = "";
	$config['PSpell.encoding'] = "";

	// PSpellShell settings
	$config['PSpellShell.mode'] = PSPELL_FAST;
	$config['PSpellShell.aspell'] = $CFG->aspellpath;
	$config['PSpellShell.tmp'] = '/tmp';

	// Windows PSpellShell settings
	//$config['PSpellShell.aspell'] = '"c:\Program Files\Aspell\bin\aspell.exe"';
	//$config['PSpellShell.tmp'] = 'c:/temp';
}
?>
