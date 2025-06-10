<?php

class com_wiris_util_xml_SerializableImpl {
	public function __construct() { 
	}
	public function serialize() {
		$s = new com_wiris_util_xml_XmlSerializer();
		return $s->write($this);
	}
	public function newInstance() {
		return new com_wiris_util_xml_SerializableImpl();
	}
	public function onSerialize($s) {
	}
	function __toString() { return 'com.wiris.util.xml.SerializableImpl'; }
}
