Description of Typo3 libraries (v 4.2.1) import into Moodle

skodak, stronk7



18 Nov 2009
Description of modifications to remove ereg related functions deprecated as of php 5.3. Patch below.

Index: class.t3lib_cs.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/typo3/class.t3lib_cs.php,v
retrieving revision 1.10
diff -u -r1.10 class.t3lib_cs.php
--- class.t3lib_cs.php	17 Nov 2009 01:36:35 -0000	1.10
+++ class.t3lib_cs.php	18 Nov 2009 05:51:54 -0000
@@ -987,13 +987,13 @@
 
 								// Detect type if not done yet: (Done on first real line)
 								// The "whitespaced" type is on the syntax 	"0x0A	0x000A	#LINE FEED" 	while 	"ms-token" is like 		"B9 = U+00B9 : SUPERSCRIPT ONE"
-							if (!$detectedType)		$detectedType = ereg('[[:space:]]*0x([[:alnum:]]*)[[:space:]]+0x([[:alnum:]]*)[[:space:]]+',$value) ? 'whitespaced' : 'ms-token';
+							if (!$detectedType)		$detectedType = preg_match('/[[:space:]]*0x([[:alnum:]]*)[[:space:]]+0x([[:alnum:]]*)[[:space:]]+/',$value) ? 'whitespaced' : 'ms-token';
 
 							if ($detectedType=='ms-token')	{
 								list($hexbyte,$utf8) = split('=|:',$value,3);
 							} elseif ($detectedType=='whitespaced')	{
 								$regA=array();
-								ereg('[[:space:]]*0x([[:alnum:]]*)[[:space:]]+0x([[:alnum:]]*)[[:space:]]+',$value,$regA);
+								preg_match('/[[:space:]]*0x([[:alnum:]]*)[[:space:]]+0x([[:alnum:]]*)[[:space:]]+/',$value,$regA);
 								$hexbyte = $regA[1];
 								$utf8 = 'U+'.$regA[2];
 							}
@@ -1097,7 +1097,7 @@
 
 				// accented Latin letters without "official" decomposition
 			$match = array();
-			if (ereg('^LATIN (SMALL|CAPITAL) LETTER ([A-Z]) WITH',$name,$match) && !$decomp)	{
+			if (preg_match('/^LATIN (SMALL|CAPITAL) LETTER ([A-Z]) WITH/',$name,$match) && !$decomp)	{
 				$c = ord($match[2]);
 				if ($match[1] == 'SMALL')	$c += 32;
 
@@ -1106,7 +1106,7 @@
 			}
 
 			$match = array();
-			if (ereg('(<.*>)? *(.+)',$decomp,$match))	{
+			if (preg_match('/(<.*>)? *(.+)/',$decomp,$match))	{
 				switch($match[1])	{
 					case '<circle>':	// add parenthesis as circle replacement, eg (1)
 						$match[2] = '0028 '.$match[2].' 0029';
@@ -1117,7 +1117,7 @@
 						break;
 
 					case '<compat>':	// ignore multi char decompositions that start with a space
-						if (ereg('^0020 ',$match[2]))	continue 2;
+						if (preg_match('/^0020 /',$match[2]))	continue 2;
 						break;
 
 						// ignore Arabic and vertical layout presentation decomposition
Index: class.t3lib_div.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/typo3/class.t3lib_div.php,v
retrieving revision 1.5
diff -u -r1.5 class.t3lib_div.php
--- class.t3lib_div.php	17 Nov 2009 01:36:35 -0000	1.5
+++ class.t3lib_div.php	18 Nov 2009 05:51:59 -0000
@@ -1063,7 +1063,7 @@
 	 */
 	public static function split_fileref($fileref)	{
 		$reg = array();
-		if (	ereg('(.*/)(.*)$',$fileref,$reg)	)	{
+		if (	preg_match('#(.*/)(.*)$#',$fileref,$reg)	)	{
 			$info['path'] = $reg[1];
 			$info['file'] = $reg[2];
 		} else {
@@ -1071,7 +1071,7 @@
 			$info['file'] = $fileref;
 		}
 		$reg='';
-		if (	ereg('(.*)\.([^\.]*$)',$info['file'],$reg)	)	{
+		if (	preg_match('#(.*)\.([^\.]*$)#',$info['file'],$reg)	)	{
 			$info['filebody'] = $reg[1];
 			$info['fileext'] = strtolower($reg[2]);
 			$info['realFileext'] = $reg[2];
@@ -1423,7 +1423,7 @@
 		if (strpos($email,' ') !== false) {
 			return false;
 		}
-		return ereg('^[A-Za-z0-9\._-]+[@][A-Za-z0-9\._-]+[\.].[A-Za-z0-9]+$',$email) ? TRUE : FALSE;
+		return preg_match('/^[A-Za-z0-9\._-]+[@][A-Za-z0-9\._-]+[\.].[A-Za-z0-9]+$/',$email) ? TRUE : FALSE;
 	}
 
 	/**
@@ -2713,7 +2713,7 @@
 							// Checking if the "subdir" is found:
 						$subdir = substr($fI['dirname'],strlen($dirName));
 						if ($subdir)	{
-							if (ereg('^[[:alnum:]_]+\/$',$subdir) || ereg('^[[:alnum:]_]+\/[[:alnum:]_]+\/$',$subdir))	{
+							if (preg_match('#^[[:alnum:]_]+\/$#',$subdir) || preg_match('#^[[:alnum:]_]+\/[[:alnum:]_]+\/$#',$subdir))	{
 								$dirName.= $subdir;
 								if (!@is_dir($dirName))	{
 									t3lib_div::mkdir_deep(PATH_site.'typo3temp/', $subdir);
@@ -3785,7 +3785,7 @@
 	 */
 	public static function verifyFilenameAgainstDenyPattern($filename)	{
 		if (strcmp($filename,'') && strcmp($GLOBALS['TYPO3_CONF_VARS']['BE']['fileDenyPattern'],''))	{
-			$result = eregi($GLOBALS['TYPO3_CONF_VARS']['BE']['fileDenyPattern'],$filename);
+			$result = preg_match('/'.$GLOBALS['TYPO3_CONF_VARS']['BE']['fileDenyPattern'].'/i',$filename);
 			if ($result)	return false;	// so if a matching filename is found, return false;
 		}
 		return true;
@@ -5103,12 +5103,12 @@
 			if($quoteActive > -1)	{
 				$paramsArr[$quoteActive] .= ' '.$v;
 				unset($paramsArr[$k]);
-				if(ereg('"$', $v))	{ $quoteActive = -1; }
+				if(preg_match('/"$/', $v))	{ $quoteActive = -1; }
 
 			} elseif(!trim($v))	{
 				unset($paramsArr[$k]);	// Remove empty elements
 
-			} elseif(ereg('^"', $v))	{
+			} elseif(preg_match('/^"/', $v))	{
 				$quoteActive = $k;
 			}
 		}

