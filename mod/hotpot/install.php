<?
	require_once("../../config.php");
	require_login();
	if (!isadmin()) {
		print "Sorry, you must be a Moodle administrator to run the HotPot install script";
		exit;
	}
	require_once("version.php");
?>
<HTML>
<HEAD>
	<TITLE>Install the HotPot module</TITLE>
	<STYLE type="text/css">
	<!-- 
		TH {text-align:right; vertical-align:top}
		TD {text-align:left; vertical-align:top}
	-->
	</STYLE>
</HEAD>
<BODY>
<H2>Install the HotPot module (<? print $module->release ?>)</H2>
<FORM method="POST" action="">
<TABLE border="0" cellspacing="2" cellpadding="2">
	<TR>
		<TH>Language files:</TH>
		<TD>
			<SELECT name="lang">
				<OPTION value="1">Yes</OPTION>
				<OPTION value="0" <? print selected_no('lang') ?>>No</OPTION>
			</SELECT>
		</TD>
	</TR>
	<TR>
		<TH>HotPot file icons:</TH>
		<TD>
			<SELECT name="pix">
				<OPTION value="1">Yes</OPTION>
				<OPTION value="0" <? print selected_no('pix') ?>>No</OPTION>
			</SELECT>
		</TD>
	</TR>
	<TR>
		<TH>Quiz module add-on:</TH>
		<TD>
			<SELECT name="quiz">
				<OPTION value="1">Yes</OPTION>
				<OPTION value="0" <? print selected_no('quiz') ?>>No</OPTION>
			</SELECT>
			<FONT style="font-size:0.8em; font-weight:normal;">allows import from a HotPot XML file to Moodle's Quiz module</FONT>			
		</TD>
	</TR>
	<TR>
		<TH>&nbsp;</TH>
		<TD><INPUT type="submit" value="Install"></TD>
	</TR>
</TABLE>
</FORM>
<?php

if (count($_POST)) {
	print '<UL>';
	if (!empty($_POST['lang'])) {
		print '<LI>Installing language files ... </LI>';
		print '<UL>';
		copyr('lang', "$CFG->dirroot/lang");
		print '</UL>';
	}
	if (!empty($_POST['pix'])) {
		print '<LI>Installing file icons ... </LI>';
		print '<UL>';
		copyr('pix', "$CFG->dirroot/pix");
		print '</UL>';

		print '<LI>Updating mimetypes ... </LI>';
		print '<UL>';

		$comment = '// additional mimetypes for the "hotpot" module';
		$mimetypes = array(
			"jcb"  => "jcb.gif",
			"jcl"  => "jcl.gif",
			"jcw"  => "jcw.gif",
			"jmt"  => "jmt.gif",
			"jmx"  => "jmx.gif",
			"jqz"  => "jqz.gif",
			"rhb"  => "xml.gif",
			"sqt"  => "xml.gif",
		);

		$pattern = '/[\'"]('.implode('|', array_keys($mimetypes)).')[\'"]/';

		$slash = DIRECTORY_SEPARATOR;
		$filepath = "$CFG->dirroot{$slash}lib{$slash}filelib.php";
		if (file_exists($filepath)) {
			// Moodle 1.5+
		} else {
			// Moodle upto and including 1.4.4
			$filepath = "$CFG->dirroot{$slash}files{$slash}mimetypes.php";
		}
		if (is_readable($filepath) && ($handle=fopen($filepath, 'r'))) {

			$lines = file($filepath);
			fclose($handle);

			// remove previous hotpot comment and mimetypes
			$count = count($lines);
			for ($i=($count-1); $i>=0; $i--) {
				if ($lines[$i]==$comment || preg_match($pattern, $lines[$i])) {
					print "<LI>removed: ".htmlspecialchars($lines[$i])."</LI>\n";
					unset($lines[$i]);
				}
			}

			// write new hotpot comment and mimetypes
			if (is_writable($filepath) && ($handle=fopen($filepath, 'w'))) {
	
				$found = false;
				foreach ($lines as $line) {
					fwrite($handle, $line);
					if (!$found && is_numeric(strpos($line, '$mimeinfo = array ('))) {
						foreach ($mimetypes as $mimetype=>$icon) {
							$insert = "        '$mimetype'  => array ('type'=>'text/xml', 'icon'=>'$icon'),";
							fwrite($handle, "$insert\n");
							print "<LI>added: ".htmlspecialchars($insert)."</LI>\n";
						}
						$found = true;
					}
				}
				fclose($handle);
	
			} else {
				print "<LI>could not write to mimetypes file:<BR>$filepath</LI>";
			}
		} else {
			print "<LI>could not read mimetypes file:<BR>$filepath</LI>";
		}
		print '</UL>';
	}
	if (!empty($_POST['quiz'])) {
		print '<LI>Installing Quiz add-on ... </LI>';
		print '<UL>';
		copyr('quiz', "$CFG->dirroot/mod/quiz");
		print '</UL>';

		print '<LI>Updating Quiz language file ... </LI>';
		print '<UL>';

		$slash = DIRECTORY_SEPARATOR;
		$filepath = "$CFG->dirroot{$slash}lang{$slash}en{$slash}quiz.php";
		if (is_readable($filepath) && ($handle=fopen($filepath, 'r'))) {

			$lines = file($filepath);
			fclose($handle);

			// remove previous hotpot comment and mimetypes
			$count = count($lines);
			for ($i=($count-1); $i>=0; $i--) {
				if (is_numeric(strpos($lines[$i], '$'."string['hotpot']"))) {
					print "<LI>removed: ".htmlspecialchars($lines[$i])."</LI>\n";
					unset($lines[$i]);
				}
			}

			// write new hotpot comment and mimetypes
			if (is_writable($filepath) && ($handle=fopen($filepath, 'w'))) {
	
				$found = false;
				foreach ($lines as $line) {
					if (!$found && is_numeric(strpos($line, '?>'))) {
						$insert = '$'."string['hotpot'] = 'Hot Potatoes XML format'";
						fwrite($handle, "$insert\n");
						print "<LI>added: ".htmlspecialchars($insert)."</LI>\n";
						$found = true;
					}
					fwrite($handle, $line);
				}
				fclose($handle);
	
			} else {
				print "<LI>could not write to quiz language file:<BR>$filepath</LI>";
			}
		} else {
			print "<LI>could not read quiz language file:<BR>$filepath</LI>";
		}
		print '</UL>';
	}
	print '</UL>';
}

function selected_no($field) {
	return isset($_POST[$field]) && empty($_POST[$field]) ? ' SELECTED' : '';
}

function copyr($source, $dest) {
	// see http://aidan.dotgeek.org/lib/?file=function.copyr.php
	// by Aidan Lister <aidan@php.net>

	// Simple copy for a file
	if (is_file($source)) {
		print "<LI>$source ... ";
		$ok = copy($source, $dest);
		if ($ok) {
			print '[<FONT color="green">Success</FONT>]';
		} else {
			print '[<FONT color="red">Failure</FONT>]';
		}
		print "</LI>\n";
		return $ok;
	}
	
	// Make destination directory
	if (!is_dir($dest)) {
		mkdir($dest);
	}
	
	// Loop through the folder
	$dir = dir($source);
	while (false !== $entry = $dir->read()) {
		// Skip pointers
		if ($entry == '.' || $entry == '..') {
			continue;
		}
	
		// Deep copy directories
		if ($dest !== "$source/$entry") {
			copyr("$source/$entry", "$dest/$entry");
		}
	}
	
	// Clean up
	$dir->close();
	return true;
}

?>
