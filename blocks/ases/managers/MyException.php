<?php
class MyException extends Exception {
    public function __construct($message, $code = 0, Exception $previous = null) {
        //se asigna los parametros a la clase padre (Exception)
        parent::__construct($message, $code, $previous);
    }
}
?>