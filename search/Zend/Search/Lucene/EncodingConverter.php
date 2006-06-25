<?php

class EncodingConverter {
  private $last_error,
          $in_encoding,
          $out_encoding;
          
  function __construct($in_encoding, $out_encoding) {
    $this->in_encoding = $in_encoding;
    $this->out_encoding = $out_encoding;
  } //constructor
  
  function handleError($err, $msg) {
    $this->last_error = $msg;
  } //handleError
  
  function convert($str) {
    $this->last_error = FALSE;
    
    set_error_handler(array(&$this, 'handleError'));
    $ret = iconv($this->in_encoding, $this->out_encoding, $str);
    restore_error_handler();
    
    return $ret;
  } //convert
  
  function getLastError() {
    return $this->last_error;
  } //getLastError
} //EncodingConverter

?>