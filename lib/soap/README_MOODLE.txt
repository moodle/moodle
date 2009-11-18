18 Nov 2009
Description of modifications to remove ereg related functions deprecated as of php 5.3. Patch below.

Index: nusoap.php
===================================================================
RCS file: /cvsroot/moodle/moodle/lib/soap/nusoap.php,v
retrieving revision 1.2
diff -u -r1.2 nusoap.php
--- nusoap.php	3 Jan 2007 14:44:42 -0000	1.2
+++ nusoap.php	18 Nov 2009 06:48:29 -0000
@@ -503,7 +503,7 @@
 			case (is_array($val) || $type):
 				// detect if struct or array
 				$valueType = $this->isArraySimpleOrStruct($val);
-                if($valueType=='arraySimple' || ereg('^ArrayOf',$type)){
+                if($valueType=='arraySimple' || preg_match('/^ArrayOf/',$type)){
 					$i = 0;
 					if(is_array($val) && count($val)> 0){
 						foreach($val as $v){
@@ -698,7 +698,7 @@
 	*/
 	function expandQname($qname){
 		// get element prefix
-		if(strpos($qname,':') && !ereg('^http://',$qname)){
+		if(strpos($qname,':') && !preg_match('#^http://#',$qname)){
 			// get unqualified name
 			$name = substr(strstr($qname,':'),1);
 			// get ns prefix
@@ -827,6 +827,7 @@
 	$datestr = date('Y-m-d\TH:i:sO',$timestamp);
 	if($utc){
 		$eregStr =
+        '/'.
 		'([0-9]{4})-'.	// centuries & years CCYY-
 		'([0-9]{2})-'.	// months MM-
 		'([0-9]{2})'.	// days DD
@@ -834,9 +835,10 @@
 		'([0-9]{2}):'.	// hours hh:
 		'([0-9]{2}):'.	// minutes mm:
 		'([0-9]{2})(\.[0-9]*)?'. // seconds ss.ss...
-		'(Z|[+\-][0-9]{2}:?[0-9]{2})?'; // Z to indicate UTC, -/+HH:MM:SS.SS... for local tz's
+		'(Z|[+\-][0-9]{2}:?[0-9]{2})?'. // Z to indicate UTC, -/+HH:MM:SS.SS... for local tz's
+        '/';
 
-		if(ereg($eregStr,$datestr,$regs)){
+		if(preg_match($eregStr,$datestr,$regs)){
 			return sprintf('%04d-%02d-%02dT%02d:%02d:%02dZ',$regs[1],$regs[2],$regs[3],$regs[4],$regs[5],$regs[6]);
 		}
 		return false;
@@ -853,6 +855,7 @@
 */
 function iso8601_to_timestamp($datestr){
 	$eregStr =
+    '/'.
 	'([0-9]{4})-'.	// centuries & years CCYY-
 	'([0-9]{2})-'.	// months MM-
 	'([0-9]{2})'.	// days DD
@@ -860,8 +863,9 @@
 	'([0-9]{2}):'.	// hours hh:
 	'([0-9]{2}):'.	// minutes mm:
 	'([0-9]{2})(\.[0-9]+)?'. // seconds ss.ss...
-	'(Z|[+\-][0-9]{2}:?[0-9]{2})?'; // Z to indicate UTC, -/+HH:MM:SS.SS... for local tz's
-	if(ereg($eregStr,$datestr,$regs)){
+	'(Z|[+\-][0-9]{2}:?[0-9]{2})?'. // Z to indicate UTC, -/+HH:MM:SS.SS... for local tz's
+    '/';
+	if(preg_match($eregStr,$datestr,$regs)){
 		// not utc
 		if($regs[8] != 'Z'){
 			$op = substr($regs[8],0,1);
@@ -1171,7 +1175,7 @@
         if(count($attrs) > 0){
         	foreach($attrs as $k => $v){
                 // if ns declarations, add to class level array of valid namespaces
-				if(ereg("^xmlns",$k)){
+				if(preg_match("/^xmlns/",$k)){
                 	//$this->xdebug("$k: $v");
                 	//$this->xdebug('ns_prefix: '.$this->getPrefix($k));
                 	if($ns_prefix = substr(strrchr($k,':'),1)){
@@ -1281,7 +1285,7 @@
 					//                        minOccurs="0" maxOccurs="unbounded" />
 					//                </sequence>
 					//            </complexType>
-					if(isset($attrs['base']) && ereg(':Array$',$attrs['base'])){
+					if(isset($attrs['base']) && preg_match('/:Array$/',$attrs['base'])){
 						$this->xdebug('complexType is unusual array');
 						$this->complexTypes[$this->currentComplexType]['phpType'] = 'array';
 					} else {
@@ -1300,7 +1304,7 @@
 					//                        minOccurs="0" maxOccurs="unbounded" />
 					//                </sequence>
 					//            </complexType>
-					if(isset($attrs['base']) && ereg(':Array$',$attrs['base'])){
+					if(isset($attrs['base']) && preg_match('/:Array$/',$attrs['base'])){
 						$this->xdebug('complexType is unusual array');
 						$this->complexTypes[$this->currentComplexType]['phpType'] = 'array';
 					} else {
@@ -1698,7 +1702,7 @@
 		} elseif(isset($this->attributes[$type])){
 			$this->xdebug("in getTypeDef, found attribute $type");
 			return $this->attributes[$type];
-		} elseif (ereg('_ContainedType$', $type)) {
+		} elseif (preg_match('/_ContainedType$/', $type)) {
 			$this->xdebug("in getTypeDef, have an untyped element $type");
 			$typeDef['typeClass'] = 'simpleType';
 			$typeDef['phpType'] = 'scalar';
@@ -2041,7 +2045,7 @@
 	function soap_transport_http($url){
 		parent::nusoap_base();
 		$this->setURL($url);
-		ereg('\$Revisio' . 'n: ([^ ]+)', $this->revision, $rev);
+		preg_match('/\$Revisio' . 'n: ([^ ]+)/', $this->revision, $rev);
 		$this->outgoing_headers['User-Agent'] = $this->title.'/'.$this->version.' ('.$rev[1].')';
 		$this->debug('set User-Agent: ' . $this->outgoing_headers['User-Agent']);
 	}
@@ -2580,7 +2584,7 @@
 				}
 			}
 			// remove 100 header
-			if(isset($lb) && ereg('^HTTP/1.1 100',$data)){
+			if(isset($lb) && preg_match('/^HTTP/1.1 100/',$data)){
 				unset($lb);
 				$data = '';
 			}//
@@ -2733,7 +2737,7 @@
 		curl_close($this->ch);
 		
 		// remove 100 header(s)
-		while (ereg('^HTTP/1.1 100',$data)) {
+		while (preg_match('#^HTTP/1.1 100#',$data)) {
 			if ($pos = strpos($data,"\r\n\r\n")) {
 				$data = ltrim(substr($data,$pos));
 			} elseif($pos = strpos($data,"\n\n") ) {
@@ -3267,7 +3271,7 @@
 		}
 		$this->debug("In service, query string=$qs");
 
-		if (ereg('wsdl', $qs) ){
+		if (preg_match('/wsdl/', $qs) ){
 			$this->debug("In service, this is a request for WSDL");
 			if($this->externalWSDLURL){
               if (strpos($this->externalWSDLURL,"://")!==false) { // assume URL
@@ -3338,7 +3342,7 @@
 			// get the character encoding of the incoming request
 			if(isset($this->headers['content-type']) && strpos($this->headers['content-type'],'=')){
 				$enc = str_replace('"','',substr(strstr($this->headers["content-type"],'='),1));
-				if(eregi('^(ISO-8859-1|US-ASCII|UTF-8)$',$enc)){
+				if(preg_match('/^(ISO-8859-1|US-ASCII|UTF-8)$/i',$enc)){
 					$this->xml_encoding = strtoupper($enc);
 				} else {
 					$this->xml_encoding = 'US-ASCII';
@@ -3367,7 +3371,7 @@
 						$enc = substr(strstr($v, '='), 1);
 						$enc = str_replace('"', '', $enc);
 						$enc = str_replace('\\', '', $enc);
-						if (eregi('^(ISO-8859-1|US-ASCII|UTF-8)$', $enc)) {
+						if (preg_match('/^(ISO-8859-1|US-ASCII|UTF-8)$/i', $enc)) {
 							$this->xml_encoding = strtoupper($enc);
 						} else {
 							$this->xml_encoding = 'US-ASCII';
@@ -3401,7 +3405,7 @@
 						$enc = substr(strstr($v, '='), 1);
 						$enc = str_replace('"', '', $enc);
 						$enc = str_replace('\\', '', $enc);
-						if (eregi('^(ISO-8859-1|US-ASCII|UTF-8)$', $enc)) {
+						if (preg_match('/^(ISO-8859-1|US-ASCII|UTF-8)$/i', $enc)) {
 							$this->xml_encoding = strtoupper($enc);
 						} else {
 							$this->xml_encoding = 'US-ASCII';
@@ -3730,7 +3734,7 @@
         	$payload .= $this->getDebugAsXMLComment();
         }
 		$this->outgoing_headers[] = "Server: $this->title Server v$this->version";
-		ereg('\$Revisio' . 'n: ([^ ]+)', $this->revision, $rev);
+		preg_match('/\$Revisio' . 'n: ([^ ]+)/', $this->revision, $rev);
 		$this->outgoing_headers[] = "X-SOAP-Server: $this->title/$this->version (".$rev[1].")";
 		// Let the Web server decide about this
 		//$this->outgoing_headers[] = "Connection: Close\r\n";
@@ -3818,7 +3822,7 @@
 		if (strpos($headers['content-type'], '=')) {
 			$enc = str_replace('"', '', substr(strstr($headers["content-type"], '='), 1));
 			$this->debug('Got response encoding: ' . $enc);
-			if(eregi('^(ISO-8859-1|US-ASCII|UTF-8)$',$enc)){
+			if(preg_match('/^(ISO-8859-1|US-ASCII|UTF-8)$/i',$enc)){
 				$this->xml_encoding = strtoupper($enc);
 			} else {
 				$this->xml_encoding = 'US-ASCII';
@@ -4336,7 +4340,7 @@
             $this->currentSchema->schemaStartElement($parser, $name, $attrs);
             $this->appendDebug($this->currentSchema->getDebug());
             $this->currentSchema->clearDebug();
-        } elseif (ereg('schema$', $name)) {
+        } elseif (preg_match('/schema$/', $name)) {
         	$this->debug('Parsing WSDL schema');
             // $this->debug("startElement for $name ($attrs[name]). status = $this->status (".$this->getLocalPart($name).")");
             $this->status = 'schema';
@@ -4355,7 +4359,7 @@
             if (count($attrs) > 0) {
 				// register namespace declarations
                 foreach($attrs as $k => $v) {
-                    if (ereg("^xmlns", $k)) {
+                    if (preg_match("/^xmlns/", $k)) {
                         if ($ns_prefix = substr(strrchr($k, ':'), 1)) {
                             $this->namespaces[$ns_prefix] = $v;
                         } else {
@@ -4380,7 +4384,7 @@
                 $attrs = array();
             } 
             // get element prefix, namespace and name
-            if (ereg(':', $name)) {
+            if (preg_match('/:/', $name)) {
                 // get ns prefix
                 $prefix = substr($name, 0, strpos($name, ':')); 
                 // get ns
@@ -4545,7 +4549,7 @@
 	*/
 	function end_element($parser, $name){ 
 		// unset schema status
-		if (/*ereg('types$', $name) ||*/ ereg('schema$', $name)) {
+		if (/*preg_match('/types$/', $name) ||*/ preg_match('/schema$/', $name)) {
 			$this->status = "";
             $this->appendDebug($this->currentSchema->getDebug());
             $this->currentSchema->clearDebug();
@@ -5995,7 +5999,7 @@
 			$key_localpart = $this->getLocalPart($key);
 			// if ns declarations, add to class level array of valid namespaces
             if($key_prefix == 'xmlns'){
-				if(ereg('^http://www.w3.org/[0-9]{4}/XMLSchema$',$value)){
+				if(preg_match('#^http://www.w3.org/[0-9]{4}/XMLSchema$#',$value)){
 					$this->XMLSchemaVersion = $value;
 					$this->namespaces['xsd'] = $this->XMLSchemaVersion;
 					$this->namespaces['xsi'] = $this->XMLSchemaVersion.'-instance';
@@ -6031,8 +6035,8 @@
 				[5]    length    ::=    nextDimension* Digit+
 				[6]    nextDimension    ::=    Digit+ ','
 				*/
-				$expr = '([A-Za-z0-9_]+):([A-Za-z]+[A-Za-z0-9_]+)\[([0-9]+),?([0-9]*)\]';
-				if(ereg($expr,$value,$regs)){
+				$expr = '/([A-Za-z0-9_]+):([A-Za-z]+[A-Za-z0-9_]+)\[([0-9]+),?([0-9]*)\]';
+				if(preg_match($expr,$value,$regs)){
 					$this->message[$pos]['typePrefix'] = $regs[1];
 					$this->message[$pos]['arrayTypePrefix'] = $regs[1];
 	                if (isset($this->namespaces[$regs[1]])) {
@@ -6758,7 +6762,7 @@
 		// detect transport
 		switch(true){
 			// http(s)
-			case ereg('^http',$this->endpoint):
+			case preg_match('/^http/',$this->endpoint):
 				$this->debug('transporting via HTTP');
 				if($this->persistentConnection == true && is_object($this->persistentConnection)){
 					$http =& $this->persistentConnection;
@@ -6780,10 +6784,10 @@
 					$http->setEncoding($this->http_encoding);
 				}
 				$this->debug('sending message, length='.strlen($msg));
-				if(ereg('^http:',$this->endpoint)){
+				if(preg_match('/^http:/',$this->endpoint)){
 				//if(strpos($this->endpoint,'http:')){
 					$this->responseData = $http->send($msg,$timeout,$response_timeout,$this->cookies);
-				} elseif(ereg('^https',$this->endpoint)){
+				} elseif(preg_match('/^https/',$this->endpoint)){
 				//} elseif(strpos($this->endpoint,'https:')){
 					//if(phpversion() == '4.3.0-dev'){
 						//$response = $http->send($msg,$timeout,$response_timeout);
@@ -6841,7 +6845,7 @@
 		if (strpos($headers['content-type'], '=')) {
 			$enc = str_replace('"', '', substr(strstr($headers["content-type"], '='), 1));
 			$this->debug('Got response encoding: ' . $enc);
-			if(eregi('^(ISO-8859-1|US-ASCII|UTF-8)$',$enc)){
+			if(preg_match('/^(ISO-8859-1|US-ASCII|UTF-8)$/i',$enc)){
 				$this->xml_encoding = strtoupper($enc);
 			} else {
 				$this->xml_encoding = 'US-ASCII';
