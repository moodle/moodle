<?php

class com_wiris_quizzes_impl_MaxConnectionsHttpImpl extends com_wiris_quizzes_impl_HttpImpl {
	public function __construct($url, $listener) {
		if(!php_Boot::$skip_constructor) {
		parent::__construct($url,$listener);
		try {
			$this->max_connections = Std::parseInt(com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$MAXCONNECTIONS));
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$t = $_ex_;
			{
				$this->max_connections = 10;
			}
		}
	}}
	public function getConnectionSlot() {
		$p = new com_wiris_quizzes_impl_SharedVariables();
		$p->lockVariable(com_wiris_quizzes_impl_MaxConnectionsHttpImpl::$DATA_KEY_MAX_CONNECTIONS);
		$data = $p->getVariable(com_wiris_quizzes_impl_MaxConnectionsHttpImpl::$DATA_KEY_MAX_CONNECTIONS);
		$connections = null;
		if($data !== null) {
			try {
				$connections = haxe_Unserializer::run($data);
			}catch(Exception $»e) {
				$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
				$t = $_ex_;
				{
					$connections = null;
				}
			}
		}
		if($connections === null) {
			$connections = new _hx_array(array());
		}
		while($connections->length > $this->max_connections) {
			$connections->remove($connections[$connections->length - 1]);
		}
		$n = Math::floor(haxe_Timer::stamp());
		$this->current = $n;
		$this->slot = -1;
		$i = null;
		{
			$_g1 = 0; $_g = $connections->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$con = $connections[$i1];
				if($this->current - $con > com_wiris_quizzes_impl_MaxConnectionsHttpImpl::$CONNECTION_TIMEOUT || $con > $this->current + 1) {
					$this->slot = $i1;
					$connections[$i1] = $this->current;
					break;
				}
				unset($i1,$con);
			}
		}
		if($this->slot === -1 && $connections->length < $this->max_connections) {
			$this->slot = $connections->length;
			$connections->push($this->current);
		}
		if($this->slot !== -1) {
			$data = haxe_Serializer::run($connections);
			$p->setVariable(com_wiris_quizzes_impl_MaxConnectionsHttpImpl::$DATA_KEY_MAX_CONNECTIONS, $data);
		}
		$p->unlockVariable(com_wiris_quizzes_impl_MaxConnectionsHttpImpl::$DATA_KEY_MAX_CONNECTIONS);
		return $this->slot !== -1;
	}
	public function releaseConnectionSlot() {
		$p = new com_wiris_quizzes_impl_SharedVariables();
		$p->lockVariable(com_wiris_quizzes_impl_MaxConnectionsHttpImpl::$DATA_KEY_MAX_CONNECTIONS);
		$data = $p->getVariable(com_wiris_quizzes_impl_MaxConnectionsHttpImpl::$DATA_KEY_MAX_CONNECTIONS);
		$connections = haxe_Unserializer::run($data);
		if($connections[$this->slot] === $this->current) {
			$n = 0;
			$connections[$this->slot] = $n;
			$data = haxe_Serializer::run($connections);
			$p->setVariable(com_wiris_quizzes_impl_MaxConnectionsHttpImpl::$DATA_KEY_MAX_CONNECTIONS, $data);
		}
		$p->unlockVariable(com_wiris_quizzes_impl_MaxConnectionsHttpImpl::$DATA_KEY_MAX_CONNECTIONS);
	}
	public function request($post) {
		if($this->max_connections === -1) {
			parent::request($post);
		} else {
			if($this->getConnectionSlot()) {
				parent::request($post);
				$this->releaseConnectionSlot();
			} else {
				throw new HException("Too many concurrent connections.");
			}
		}
	}
	public $current;
	public $slot;
	public $max_connections;
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
	static $CONNECTION_TIMEOUT = 60;
	static $DATA_KEY_MAX_CONNECTIONS = "wiris_maxconnections";
	function __toString() { return 'com.wiris.quizzes.impl.MaxConnectionsHttpImpl'; }
}
