<?php

/**
 * Abstract base token class that all others inherit from.
 */
class HTMLPurifier_Token {
    public $line; /**< Line number node was on in source document. Null if unknown. */
    
    /**
     * Lookup array of processing that this token is exempt from.
     * Currently, valid values are "ValidateAttributes" and
     * "MakeWellFormed_TagClosedError"
     */
    public $armor = array();
    
    public function __get($n) {
      if ($n === 'type') {
        trigger_error('Deprecated type property called; use instanceof', E_USER_NOTICE);
        switch (get_class($this)) {
          case 'HTMLPurifier_Token_Start': return 'start';
          case 'HTMLPurifier_Token_Empty': return 'empty';
          case 'HTMLPurifier_Token_End': return 'end';
          case 'HTMLPurifier_Token_Text': return 'text';
          case 'HTMLPurifier_Token_Comment': return 'comment';
          default: return null;
        }
      }
    }
}
