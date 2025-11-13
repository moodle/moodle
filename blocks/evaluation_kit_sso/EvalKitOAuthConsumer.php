<?php

class EvalKitOAuthConsumer
{
    public $consumerkey;
    public $secret;
    public $url;
    public $httpMethod;
    public $callbackUrl;
    public $username;
    public $params = array();
    public $courseuniqueid;
    public $coursecode;

    public function __construct($username, $consumerkey, $secret, $url, $httpMethod, $coursecode, $courseuniqueid, $callbackUrl)
    {
        $this->httpMethod = $httpMethod;
        $this->consumerkey = $consumerkey;
        $this->secret = $secret;
        $this->url = $url;
		$this->username = $username;
        $this->callbackUrl = $callbackUrl;
		$this->courseuniqueid = $courseuniqueid;
		$this->coursecode = $coursecode;
    }

    public function addParameter($tkey, $tvalue)
    {
		$this->params[$tkey] = $tvalue;
    }

    public function getbasestring()
    {
		$first = true;
        $param = '';
		ksort($this->params);
        foreach($this->params as $key=>$value) {
			if ($first) {
				$first = false;
			}
			else {
				$param.='&';
			}
			$param.=$key.'='.$value;
		}
        return $param;
    }
    
    public function sign()
    {
        $this->addParameter("oauth_consumer_key", $this->consumerkey);
        $this->addParameter("oauth_timestamp", time());
        $this->addParameter("oauth_signature_method", "HMAC-SHA1");
        $this->addParameter("oauth_version", "1.0");
        $this->addParameter("oauth_nonce", time());
        $this->addParameter("username", urlencode($this->username));
        
		$base = urlencode($this->httpMethod).'&'.urlencode($this->url).'&';
		// get params
		$first = true;
        $param = '';
		ksort($this->params);
        foreach($this->params as $key=>$value) {
			if (strtolower($key) == 'oauth_signature') {
				continue;
			}
			if ($first) {
				$first = false;
			}
			else {
				$param.='&';
			}
			$param.=$key.'='.$value;
		}
        $base.=urlencode($param);
        $tempsig = $this->getsignature($base);
        $this->addParameter("oauth_signature", $tempsig);
    }   

	function getsignature($base_string)
	{
	  $key = urlencode($this->secret).'&'.urlencode('');
	  if (function_exists('hash_hmac'))
	  {
		 $signature = base64_encode(hash_hmac("sha1", $base_string, $key, true));
	  }
	  else
	  {
		$blocksize = 64;
		$hashfunc = 'sha1';
		if (strlen($key) > $blocksize)
		{
			$key = pack('H*', $hashfunc($key));
		}
		$key = str_pad($key,$blocksize,chr(0x00));
		$ipad = str_repeat(chr(0x36),$blocksize);
		$opad = str_repeat(chr(0x5c),$blocksize);
		$hmac = pack('H*', $hashfunc(
					($key^$opad).pack(
						'H*',$hashfunc(
							($key^$ipad).$base_string
						)
					)
				)
			);
		$signature = base64_encode($hmac);
	  }
	  return urlencode($signature);
	}

    function normalizeURL($url)
	{
		$newUrl = "";
		$url = parse_url($url);	
		$defaultSchemes = array("http" => 80, "https" => 443);

		if(isset($url['scheme']))
		{
			$url['scheme'] = strtolower($url['scheme']);
			// Strip scheme default ports
			if(isset($defaultSchemes[$url['scheme']]) && isset($url['port']) && $defaultSchemes[$url['scheme']] == $url['port']) 
			{
				unset($url['port']);
			}
			$newUrl .= "{$url['scheme']}://";
		}

		if(isset($url['host']))
		{
			$url['host'] = strtolower($url['host']);
			// Seems like a valid domain, proper validation should be made in higher layers.
			if(preg_match("/[a-z]+\Z/", $url['host']))
			{
				if(preg_match("/^www\./", $url['host']) && gethostbyname($url['host']) == gethostbyname(str_replace("www.", "", $url['host'])))
				{
					$newUrl .= str_replace("www.", "", $url['host']);
				}				
				else
				{
					$newUrl .= $url['host'];
				}
			}
			else
			{
				$newUrl .= $url['host'];
			}
		}

		if(isset($url['port']))
		{
			$newUrl .= ":{$url['port']}";
		}

		if(isset($url['path']))
		{
			// Case normalization
			$url['path'] =  preg_replace_callback('/(%([0-9abcdef][0-9abcdef]))/e', function ($matches) {return "'%'.strtoupper('\\2')";} , $url['path']);

			//Strip duplicate slashes
			while(preg_match("/\/\//", $url['path']))
			{
				$url['path'] = preg_replace("/\/\//", "/", $url['path']);
			}

			/*
			 * Decode unreserved characters, http://www.apps.ietf.org/rfc/rfc3986.html#sec-2.3
			 * Heavily rewritten version of urlDecodeUnreservedChars() in Glen Scott's url-normalizer.
			 */

			$u = array();
			for ($o = 65; $o <= 90; $o++)
				$u[] = dechex($o);
			for ($o = 97; $o <= 122; $o++)
				$u[] = dechex($o);
			for ($o = 48; $o <= 57; $o++)
				$u[] = dechex($o);
			$chrs = array('-', '.', '_', '~');
			foreach($chrs as $chr)
			{
				$u[] = dechex(ord($chr));
			}
			$url['path'] = preg_replace_callback(array_map(create_function('$str', 'return "/%" . strtoupper($str) . "/x";'), $u),
			    create_function('$matches', 'return chr(hexdec($matches[0]));'), $url['path']);
			// Remove directory index
			$defaultIndexes = array("/default\.aspx/" => "default.aspx", "/default\.asp/"  => "default.asp",
			    "/index\.html/"   => "index.html",   "/index\.htm/"    => "index.htm",
				"/default\.html/" => "default.html", "/default\.htm/"  => "default.htm",
				"/index\.php/"    => "index.php",    "/index\.jsp/"    => "index.jsp");

			foreach($defaultIndexes as $index => $strip)
			{
				if(preg_match($index, $url['path']))
				{
					$url['path'] = str_replace($strip, "", $url['path']);
				}
			}
		
			/**
			 * Path segment normalization, http://www.apps.ietf.org/rfc/rfc3986.html#sec-5.2.4
			 * Heavily rewritten version of removeDotSegments() in Glen Scott's url-normalizer.
			 */
			$new_path = '';
			while(!empty($url['path']))
			{
				if(preg_match('!^(\.\./|\./)!x', $url['path']))
				{
					$url['path'] = preg_replace('!^(\.\./|\./)!x', '', $url['path']);
				}
				elseif(preg_match('!^(/\./)!x', $url['path'], $matches) || preg_match('!^(/\.)$!x', $url['path'], $matches))
				{
					$url['path'] = preg_replace("!^" . $matches[1] . "!", '/', $url['path']);
				}
				elseif(preg_match('!^(/\.\./|/\.\.)!x', $url['path'], $matches))
				{
					$url['path'] = preg_replace( '!^' . preg_quote( $matches[1], '!' ) . '!x', '/', $url['path'] );
					$new_path = preg_replace( '!/([^/]+)$!x', '', $new_path );
				}
				elseif(preg_match('!^(\.|\.\.)$!x', $url['path']))
				{
					$url['path'] = preg_replace('!^(\.|\.\.)$!x', '', $url['path']);
				}
				else
				{
					if(preg_match('!(/*[^/]*)!x', $url['path'], $matches))
					{
						$first_path_segment = $matches[1];
						$url['path'] = preg_replace( '/^' . preg_quote( $first_path_segment, '/' ) . '/', '', $url['path'], 1 );
						$new_path .= $first_path_segment;
					}
				}
			}
			$newUrl .= $new_path;
		}
		
		if(isset($url['fragment']))
		{
			unset($url['fragment']);
		}
		
		// Sort GET params alphabetically, not because the RFC requires it but because it's cool!
		if(isset($url['query']))
		{
			if(preg_match("/&/", $url['query']))
			{
				$s = explode("&", $url['query']);
				$url['query'] = "";
				sort($s);
				foreach($s as $z)
				{
					$url['query'] .= "{$z}&";
				}
				$url['query'] = preg_replace("/&\Z/", "", $url['query']);
			}
			$newUrl .= "?{$url['query']}";
		}
		return $newUrl;
	}
}