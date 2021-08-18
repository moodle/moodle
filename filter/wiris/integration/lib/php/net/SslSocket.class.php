<?php

class php_net_SslSocket extends sys_net_Socket {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct();
		$this->protocol = "ssl";
	}}
	public function connect($host, $port) {
		$errs = null;
		$errn = null;
		$r = stream_socket_client($this->protocol . "://" . $host->hostName . ":" . _hx_string_rec($port, ""), $errn, $errs);
		sys_net_Socket::checkError($r, $errn, $errs);
		$this->__s = $r;
		$this->assignHandler();
	}
	function __toString() { return 'php.net.SslSocket'; }
}
