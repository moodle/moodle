<?php

class com_wiris_util_net_MimeTypes {
	public function __construct(){}
	static $JSON = "application/json";
	static $LATEX = "application/x-latex";
	static $XML = "application/xml";
	static $MTWEB_PARAMETERS = "application/vnd.wiris.mtweb-params+json";
	static $HAND_STROKES = "application/vnd.wiris.mtweb-strokes+json";
	function __toString() { return 'com.wiris.util.net.MimeTypes'; }
}
