<?php
/**
*	base include file for eclipse plugin 
*	@package	SimpleTest
*	@version	$Id$
*/
include_once "xml.php";
include_once "invoker.php";
include_once "socket.php";
class EclipseReporter extends XmlReporter {
	var $_port;
	function EclipseReporter($port){
		$this->_port = $port;
		$this->XmlReporter();
	}
	
	function &createInvoker(&$invoker){
		$eclinvoker = &new EclipseInvoker($invoker, $this->_port);
		return $eclinvoker;
	}
	
	function paintMethodStart($method) {
		parent::paintGroupStart($this->_group, $this->_size);
		parent::paintCaseStart($this->_case);
		parent::paintMethodStart($method);
	}
	
	function paintMethodEnd($method){
		parent::paintMethodEnd($method);
		parent::paintCaseEnd($this->_case);
		parent::paintGroupEnd($this->_group);
		
	}
	
	function paintCaseStart($case){
		$this->_case = $case;
	}
	
	function paintCaseEnd($case){
		$this->_case = "";
	}
	function paintGroupStart($group,$size){
		$this->_group = $group;
	}
	function paintGroupEnd($group){
		$this->_group = "";
	}
}

class EclipseInvoker extends SimpleInvokerDecorator{
	var $_port;
	function EclipseInvoker(&$invoker,$port) {
		$this->_port = $port;
		$this->SimpleInvokerDecorator($invoker);
	}
	
	function invoke($method) {
		ob_start();
		parent::invoke($method);
		$output = ob_get_contents();
		ob_end_clean();

		$sock = new SimpleSocket("127.0.0.1",$this->_port,5);
		$sock->write($output);
		$sock->close();
		echo $sock->getError();
	}
}
	
?>