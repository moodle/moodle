18 Nov 2009
Description of modifications to remove ereg related functions deprecated as of php 5.3. Patch below.

Index: class.t3lib_cs.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/typo3/class.t3lib_cs.php,v
retrieving revision 1.7
diff -u -r1.7 class.t3lib_cs.php
--- class.t3lib_cs.php	11 Aug 2006 09:48:35 -0000	1.7
+++ class.t3lib_cs.php	18 Nov 2009 06:53:50 -0000
@@ -781,8 +781,8 @@
 			$trans_tbl = array_flip(get_html_translation_table(HTML_ENTITIES));		// Getting them in iso-8859-1 - but thats ok since this is observed below.
 		}
 
-		$token = md5(microtime());
-		$parts = explode($token,ereg_replace('(&([#[:alnum:]]*);)',$token.'\2'.$token,$str));
+		$token = 'a'.md5(microtime());//token must start with a letter or preg_replace substitution won't work
+		$parts = explode($token,preg_replace('/(&([#[:alnum:]]*);)/',$token.'\2'.$token,$str));
 		foreach($parts as $k => $v)	{
 			if ($k%2)	{
 				if (substr($v,0,1)=='#')	{	// Dec or hex entities:
@@ -974,13 +974,13 @@
 
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
@@ -1084,7 +1084,7 @@
 
 				// accented Latin letters without "official" decomposition
 			$match = array();
-			if (ereg('^LATIN (SMALL|CAPITAL) LETTER ([A-Z]) WITH',$name,$match) && !$decomp)	{
+			if (preg_match('/^LATIN (SMALL|CAPITAL) LETTER ([A-Z]) WITH/',$name,$match) && !$decomp)	{
 				$c = ord($match[2]);
 				if ($match[1] == 'SMALL')	$c += 32;
 
@@ -1093,7 +1093,7 @@
 			}
 
 			$match = array();
-			if (ereg('(<.*>)? *(.+)',$decomp,$match))	{
+			if (preg_match('/(<.*>)? *(.+)/',$decomp,$match))	{
 				switch($match[1])	{
 					case '<circle>':	// add parenthesis as circle replacement, eg (1)
 						$match[2] = '0028 '.$match[2].' 0029';
@@ -1104,7 +1104,7 @@
 						break;
 
 					case '<compat>':	// ignore multi char decompositions that start with a space
-						if (ereg('^0020 ',$match[2]))	continue 2;
+						if (preg_match('/^0020 /',$match[2]))	continue 2;
 						break;
 
 						// ignore Arabic and vertical layout presentation decomposition
Index: class.t3lib_div.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/typo3/class.t3lib_div.php,v
retrieving revision 1.2
diff -u -r1.2 class.t3lib_div.php
--- class.t3lib_div.php	17 Oct 2005 15:48:29 -0000	1.2
+++ class.t3lib_div.php	18 Nov 2009 06:53:51 -0000
@@ -798,7 +798,7 @@
 	 * @return	array		Contains keys [path], [file], [filebody], [fileext], [realFileext]
 	 */
 	function split_fileref($fileref)	{
-		if (	ereg('(.*/)(.*)$',$fileref,$reg)	)	{
+		if (	preg_match('#(.*/)(.*)$#',$fileref,$reg)	)	{
 			$info['path'] = $reg[1];
 			$info['file'] = $reg[2];
 		} else {
@@ -806,7 +806,7 @@
 			$info['file'] = $fileref;
 		}
 		$reg='';
-		if (	ereg('(.*)\.([^\.]*$)',$info['file'],$reg)	)	{
+		if (	preg_match('/(.*)\.([^\.]*$)/',$info['file'],$reg)	)	{
 			$info['filebody'] = $reg[1];
 			$info['fileext'] = strtolower($reg[2]);
 			$info['realFileext'] = $reg[2];
@@ -882,7 +882,7 @@
 	 * @return	string
 	 */
 	function rm_endcomma($string)	{
-		return ereg_replace(',$','',$string);
+		return preg_replace('/,$/','',$string);
 	}
 
 	/**
@@ -896,7 +896,7 @@
 	 */
 	function danish_strtoupper($string)	{
 		$value = strtoupper($string);
-		return strtr($value, 'áéúíâêûôîæøåäöü', 'ÁÉÚÍÄËÜÖÏÆØÅÄÖÜ');
+		return strtr($value, 'ï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œ', 'ï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œï¿œ');
 	}
 
 	/**
@@ -904,13 +904,13 @@
 	 * Only known characters will be converted, so don't expect a result for any character.
 	 * (DEPRECIATED: Works only for western europe single-byte charsets! Use t3lib_cs::specCharsToASCII() instead!)
 	 *
-	 * ä => ae, Ö => Oe
+	 * ï¿œ => ae, ï¿œ => Oe
 	 *
 	 * @param	string		String to convert.
 	 * @return	string
 	 */
 	function convUmlauts($str)	{
-		$pat  = array (	'/ä/',	'/Ä/',	'/ö/',	'/Ö/',	'/ü/',	'/Ü/',	'/ß/',	'/å/',	'/Å/',	'/ø/',	'/Ø/',	'/æ/',	'/Æ/'	);
+		$pat  = array (	'/ï¿œ/',	'/ï¿œ/',	'/ï¿œ/',	'/ï¿œ/',	'/ï¿œ/',	'/ï¿œ/',	'/ï¿œ/',	'/ï¿œ/',	'/ï¿œ/',	'/ï¿œ/',	'/ï¿œ/',	'/ï¿œ/',	'/ï¿œ/'	);
 		$repl = array (	'ae',	'Ae',	'oe',	'Oe',	'ue',	'Ue',	'ss',	'aa',	'AA',	'oe',	'OE',	'ae',	'AE'	);
 		return preg_replace($pat,$repl,$str);
 	}
@@ -1022,7 +1022,7 @@
 	 * @see calcParenthesis()
 	 */
 	function calcPriority($string)	{
-		$string=ereg_replace('[[:space:]]*','',$string);	// removing all whitespace
+		$string=preg_replace('/[ ]*/','',$string);	// removing all whitespace
 		$string='+'.$string;	// Ensuring an operator for the first entrance
 		$qm='\*\/\+-^%';
 		$regex = '(['.$qm.'])(['.$qm.']?[0-9\.]*)';
@@ -1103,7 +1103,7 @@
 	 * @return	string		Converted result.
 	 */
 	function deHSCentities($str)	{
-		return ereg_replace('&amp;([#[:alnum:]]*;)','&\1',$str);
+		return preg_replace('/&amp;([#A-Za-z0-9]*;)/','&\1',$str);
 	}
 
 	/**
@@ -1154,7 +1154,7 @@
 	function validEmail($email)	{
 		$email = trim ($email);
 		if (strstr($email,' '))	 return FALSE;
-		return ereg('^[A-Za-z0-9\._-]+[@][A-Za-z0-9\._-]+[\.].[A-Za-z0-9]+$',$email) ? TRUE : FALSE;
+		return preg_match('/^[A-Za-z0-9\._-]+[@][A-Za-z0-9\._-]+[\.].[A-Za-z0-9]+$/',$email) ? TRUE : FALSE;
 	}
 
 	/**
@@ -1555,7 +1555,7 @@
 							$name = '';
 						}
 					} else {
-						if ($key = strtolower(ereg_replace('[^a-zA-Z0-9]','',$val)))	{
+						if ($key = strtolower(preg_replace('/[^a-zA-Z0-9]/','',$val)))	{
 							$attributes[$key] = '';
 							$name = $key;
 						}
@@ -1580,9 +1580,9 @@
 	 * @internal
 	 */
 	function split_tag_attributes($tag)	{
-		$tag_tmp = trim(eregi_replace ('^<[^[:space:]]*','',trim($tag)));
+		$tag_tmp = trim(preg_replace ('/^<[^[:space:]]*/i','',trim($tag)));
 			// Removes any > in the end of the string
-		$tag_tmp = trim(eregi_replace ('>$','',$tag_tmp));
+		$tag_tmp = trim(preg_replace ('/>$/i','',$tag_tmp));
 
 		while (strcmp($tag_tmp,''))	{	// Compared with empty string instead , 030102
 			$firstChar=substr($tag_tmp,0,1);
@@ -1653,7 +1653,7 @@
 	 * @param	boolean		Wrap script element in linebreaks? Default is TRUE.
 	 * @return	string		The wrapped JS code, ready to put into a XHTML page
 	 * @author	Ingmar Schlecht <ingmars@web.de>
-	 * @author	René Fritz <r.fritz@colorcube.de>
+	 * @author	Renï¿œ Fritz <r.fritz@colorcube.de>
 	 */
 	function wrapJS($string, $linebreak=TRUE) {
 		if(trim($string)) {
@@ -1813,7 +1813,7 @@
 				}
 
 					// The tag name is cleaned up so only alphanumeric chars (plus - and _) are in there and not longer than 100 chars either.
-				$tagName = substr(ereg_replace('[^[:alnum:]_-]','',$tagName),0,100);
+				$tagName = substr(preg_replace('/[^[:alnum:]_-]/','',$tagName),0,100);
 
 					// If the value is an array then we will call this function recursively:
 				if (is_array($v))	{
@@ -1902,7 +1902,7 @@
 			// What we do here fixes the problem but ONLY if the charset is utf-8, iso-8859-1 or us-ascii. That should work for most TYPO3 installations, in particular if people use utf-8 which we highly recommend.
 		if ((double)phpversion()>=5)	{
 			unset($ereg_result);
-			ereg('^[[:space:]]*<\?xml[^>]*encoding[[:space:]]*=[[:space:]]*"([^"]*)"',substr($string,0,200),$ereg_result);
+			preg_match('/^[[:space:]]*<\?xml[^>]*encoding[[:space:]]*=[[:space:]]*"([^"]*)"/',substr($string,0,200),$ereg_result);
 			$theCharset = $ereg_result[1] ? $ereg_result[1] : ($TYPO3_CONF_VARS['BE']['forceCharset'] ? $TYPO3_CONF_VARS['BE']['forceCharset'] : 'iso-8859-1');
 			xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, $theCharset);  // us-ascii / utf-8 / iso-8859-1
 		}
@@ -2042,7 +2042,7 @@
 	function xmlGetHeaderAttribs($xmlData)	{
 		$xmlHeader = substr(trim($xmlData),0,200);
 		$reg=array();
-		if (eregi('^<\?xml([^>]*)\?\>',$xmlHeader,$reg))	{
+		if (preg_match('/^<\?xml([^>]*)\?\>/i',$xmlHeader,$reg))	{
 			return t3lib_div::get_tag_attributes($reg[1]);
 		}
 	}
@@ -2163,7 +2163,7 @@
 							// Checking if the "subdir" is found:
 						$subdir = substr($fI['dirname'],strlen($dirName));
 						if ($subdir)	{
-							if (ereg('^[[:alnum:]_]+\/$',$subdir))	{
+							if (preg_match('#^[[:alnum:]_]+\/$#',$subdir))	{
 								$dirName.= $subdir;
 								if (!@is_dir($dirName))	{
 									t3lib_div::mkdir($dirName);
@@ -2191,7 +2191,7 @@
 	 * @return	boolean		TRUE if @mkdir went well!
 	 */
 	function mkdir($theNewFolder)	{
-		$theNewFolder = ereg_replace('\/$','',$theNewFolder);
+		$theNewFolder = preg_replace('#\/$#','',$theNewFolder);
 		if (mkdir($theNewFolder, octdec($GLOBALS['TYPO3_CONF_VARS']['BE']['folderCreateMask']))){
 			chmod($theNewFolder, octdec($GLOBALS['TYPO3_CONF_VARS']['BE']['folderCreateMask'])); //added this line, because the mode at 'mkdir' has a strange behaviour sometimes
 
@@ -2240,7 +2240,7 @@
 			// Initialize variabels:
 		$filearray = array();
 		$sortarray = array();
-		$path = ereg_replace('\/$','',$path);
+		$path = preg_replace('#\/$#','',$path);
 
 			// Find files+directories:
 		if (@is_dir($path))	{
@@ -2564,7 +2564,7 @@
 
 		$pString = t3lib_div::implodeArrayForUrl('',$params);
 
-		return $pString ? $parts.'?'.ereg_replace('^&','',$pString) : $parts;
+		return $pString ? $parts.'?'.preg_replace('/^&/','',$pString) : $parts;
 	}
 
 	/**
@@ -2673,7 +2673,7 @@
 			case 'REQUEST_URI':
 					// Typical application of REQUEST_URI is return urls, forms submitting to itself etc. Example: returnUrl='.rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI'))
 				if (!$_SERVER['REQUEST_URI'])	{	// This is for ISS/CGI which does not have the REQUEST_URI available.
-					return '/'.ereg_replace('^/','',t3lib_div::getIndpEnv('SCRIPT_NAME')).
+					return '/'.preg_replace('#^/#','',t3lib_div::getIndpEnv('SCRIPT_NAME')).
 						($_SERVER['QUERY_STRING']?'?'.$_SERVER['QUERY_STRING']:'');
 				} else return $_SERVER['REQUEST_URI'];
 			break;
@@ -2826,11 +2826,11 @@
 				break;
 				case 'msie':
 					$tmp = strstr($useragent,'MSIE');
-					$bInfo['VERSION'] = doubleval(ereg_replace('^[^0-9]*','',substr($tmp,4)));
+					$bInfo['VERSION'] = doubleval(preg_replace('/^[^0-9]*/','',substr($tmp,4)));
 				break;
 				case 'opera':
 					$tmp = strstr($useragent,'Opera');
-					$bInfo['VERSION'] = doubleval(ereg_replace('^[^0-9]*','',substr($tmp,5)));
+					$bInfo['VERSION'] = doubleval(preg_replace('/^[^0-9]*/','',substr($tmp,5)));
 				break;
 				case 'konqu':
 					$tmp = strstr($useragent,'Konqueror/');
@@ -2966,7 +2966,7 @@
 	 */
 	function verifyFilenameAgainstDenyPattern($filename)	{
 		if (strcmp($filename,'') && strcmp($GLOBALS['TYPO3_CONF_VARS']['BE']['fileDenyPattern'],''))	{
-			$result = eregi($GLOBALS['TYPO3_CONF_VARS']['BE']['fileDenyPattern'],$filename);
+			$result = preg_match('/'.$GLOBALS['TYPO3_CONF_VARS']['BE']['fileDenyPattern'].'/i',$filename);
 			if ($result)	return false;	// so if a matching filename is found, return false;
 		}
 		return true;
@@ -3126,7 +3126,7 @@
 	function readLLfile($fileRef,$langKey)	{
 		$file = t3lib_div::getFileAbsFileName($fileRef);
 		if ($file)	{
-			$baseFile = ereg_replace('\.(php|xml)$', '', $file);
+			$baseFile = preg_replace('#\.(php|xml)$#', '', $file);
 
 			if (@is_file($baseFile.'.xml'))	{
 				$LOCAL_LANG = t3lib_div::readLLXMLfile($baseFile.'.xml', $langKey);
@@ -3162,7 +3162,7 @@
 				// Cache file name:
 			$hashSource = substr($fileRef,strlen(PATH_site)).'|'.date('d-m-Y H:i:s',filemtime($fileRef));
 			$cacheFileName = PATH_site.'typo3temp/llxml/'.
-							#str_replace('_','',ereg_replace('^.*\/','',dirname($fileRef))).
+							#str_replace('_','',preg_replace('#^.*\/#','',dirname($fileRef))).
 							#'_'.basename($fileRef).
 							substr(basename($fileRef),10,15).
 							'_'.t3lib_div::shortMD5($hashSource).'.'.$langKey.'.'.$origCharset.'.cache';
@@ -3344,7 +3344,7 @@
 
 			// Check persistent object and if found, call directly and exit.
 		if (is_array($GLOBALS['T3_VAR']['callUserFunction'][$funcName]))	{
-			return call_user_method(
+			return call_user_func(
 						$GLOBALS['T3_VAR']['callUserFunction'][$funcName]['method'],
 						$GLOBALS['T3_VAR']['callUserFunction'][$funcName]['obj'],
 						$params,
@@ -3405,7 +3405,7 @@
 						);
 					}
 						// Call method:
-					$content = call_user_method(
+					$content = call_user_func(
 						$parts[1],
 						$classObj,
 						$params,
@@ -3520,7 +3520,7 @@
 	 * @param	string		Sub type like file extensions or similar. Defined by the service.
 	 * @param	mixed		List of service keys which should be exluded in the search for a service. Array or comma list.
 	 * @return	object		The service object or an array with error info's.
-	 * @author	René Fritz <r.fritz@colorcube.de>
+	 * @author	Renï¿œ Fritz <r.fritz@colorcube.de>
 	 */
 	function &makeInstanceService($serviceType, $serviceSubType='', $excludeServiceKeys=array())	{
 		global $T3_SERVICES, $T3_VAR, $TYPO3_CONF_VARS;
@@ -3606,7 +3606,7 @@
 
 				$message=t3lib_div::quoted_printable($message);
 
-				if (!$dontEncodeSubject)	$subject='=?'.$charset.'?Q?'.trim(t3lib_div::quoted_printable(ereg_replace('[[:space:]]','_',$subject),1000)).'?=';
+				if (!$dontEncodeSubject)	$subject='=?'.$charset.'?Q?'.trim(t3lib_div::quoted_printable(preg_replace('#/[[:space:]]/#','_',$subject),1000)).'?=';
 			break;
 			case '8bit':
 				$headers=trim($headers).chr(10).
@@ -3657,11 +3657,11 @@
 					$len+=3;
 				}
 			}
-			$newVal = ereg_replace(chr(32).'$','=20',$newVal);		// Replaces a possible SPACE-character at the end of a line
-			$newVal = ereg_replace(chr(9).'$','=09',$newVal);		// Replaces a possible TAB-character at the end of a line
+			$newVal = preg_replace('/'.chr(32).'$/','=20',$newVal);		// Replaces a possible SPACE-character at the end of a line
+			$newVal = preg_replace('/'.chr(9).'$/','=09',$newVal);		// Replaces a possible TAB-character at the end of a line
 			$newString.=$newVal.chr(13).chr(10);
 		}
-		return ereg_replace(chr(13).chr(10).'$','',$newString);
+		return preg_replace('/'.chr(13).chr(10).'$/','',$newString);
 	}
 
 	/**
@@ -3853,12 +3853,12 @@
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
