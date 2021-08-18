<?php

class sys_net_Socket {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->input = new sys_io_FileInput(null);
		$this->output = new sys_io_FileOutput(null);
		$this->protocol = "tcp";
	}}
	public function waitForRead() {
		sys_net_Socket::select(new _hx_array(array($this)), null, null, null);
	}
	public function setFastSend($b) {
		throw new HException("Not implemented");
	}
	public function setBlocking($b) {
		$r = stream_set_blocking($this->__s, $b);
		sys_net_Socket::checkError($r, 0, "Unable to block");
	}
	public function setTimeout($timeout) {
		$s = intval($timeout);
		$ms = intval(($timeout - $s) * 1000000);
		$r = stream_set_timeout($this->__s, $s, $ms);
		sys_net_Socket::checkError($r, 0, "Unable to set timeout");
	}
	public function host() {
		$r = stream_socket_get_name($this->__s, false);
		sys_net_Socket::checkError($r, 0, "Unable to retrieve the host name");
		return $this->hpOfString($r);
	}
	public function peer() {
		$r = stream_socket_get_name($this->__s, true);
		sys_net_Socket::checkError($r, 0, "Unable to retrieve the peer name");
		return $this->hpOfString($r);
	}
	public function hpOfString($s) {
		$parts = _hx_explode(":", $s);
		if($parts->length === 2) {
			return _hx_anonymous(array("host" => new sys_net_Host($parts[0]), "port" => Std::parseInt($parts[1])));
		} else {
			return _hx_anonymous(array("host" => new sys_net_Host(_hx_substr($parts[1], 2, null)), "port" => Std::parseInt($parts[2])));
		}
	}
	public function accept() {
		$r = stream_socket_accept($this->__s);
		sys_net_Socket::checkError($r, 0, "Unable to accept connections on socket");
		$s = new sys_net_Socket();
		$s->__s = $r;
		$s->assignHandler();
		return $s;
	}
	public function bind($host, $port) {
		$errs = null;
		$errn = null;
		$r = stream_socket_server($this->protocol . "://" . $host->_ip . ":" . _hx_string_rec($port, ""), $errn, $errs, (($this->protocol === "udp") ? STREAM_SERVER_BIND : STREAM_SERVER_BIND | STREAM_SERVER_LISTEN));
		sys_net_Socket::checkError($r, $errn, $errs);
		$this->__s = $r;
		$this->assignHandler();
	}
	public function shutdown($read, $write) {
		$r = null;
		if(function_exists("stream_socket_shutdown")) {
			$rw = (($read && $write) ? 2 : (($write) ? 1 : (($read) ? 0 : 2)));
			$r = stream_socket_shutdown($this->__s, $rw);
		} else {
			$r = fclose($this->__s);
		}
		sys_net_Socket::checkError($r, 0, "Unable to Shutdown");
	}
	public function listen($connections) {
		throw new HException("Not implemented");
	}
	public function connect($host, $port) {
		$errs = null;
		$errn = null;
		$r = stream_socket_client($this->protocol . "://" . $host->_ip . ":" . _hx_string_rec($port, ""), $errn, $errs);
		sys_net_Socket::checkError($r, $errn, $errs);
		$this->__s = $r;
		$this->assignHandler();
	}
	public function write($content) {
		fwrite($this->__s, $content);
		return;
	}
	public function read() {
		$b = "";
		while (!feof($this->__s)) $b .= fgets($this->__s);
		return $b;
	}
	public function close() {
		fclose($this->__s);
		{
			$this->input->__f = null;
			$this->output->__f = null;
		}
		$this->input->close();
		$this->output->close();
	}
	public function assignHandler() {
		$this->input->__f = $this->__s;
		$this->output->__f = $this->__s;
	}
	public $custom;
	public $output;
	public $input;
	public $protocol;
	public $__s;
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	static function checkError($r, $code, $msg) {
		if(!($r === false)) {
			return;
		}
		throw new HException(haxe_io_Error::Custom("Error [" . _hx_string_rec($code, "") . "]: " . $msg));
	}
	static function getType($isUdp) {
		return (($isUdp) ? SOCK_DGRAM : SOCK_STREAM);
	}
	static function getProtocol($protocol) {
		return getprotobyname($protocol);
	}
	static function select($read, $write, $others, $timeout = null) {
		throw new HException("Not implemented");
		return null;
	}
	function __toString() { return 'sys.net.Socket'; }
}
