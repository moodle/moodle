<?php
require_once(dirname(__FILE__). '/../../../config.php');
require_once("MyException.php");

class dateValidator {
    private $datastyle;
    
    
    function dateValidator(){
        global $DB;
        
        $datastyledb = $DB->get_record_sql('SHOW DATESTYLE');
 
        $datastyledb = preg_replace('/\s+/', '', $datastyledb->DateStyle);
        
        $datastyledb = explode(",", $datastyledb);
        
        foreach ($datastyledb as $value){
            if((strpos($value, "d")!== false) || (strpos($value, "D")!== false)){
                $this->datastyle = str_split($value);
                break;
            }
        }
        
    }
    
    function validateDateStyle($date){
        
        $tokens = preg_split("#(/|-)#", $date);

        $result1 = $this->validatetoken($this->datastyle[0], intval($tokens[0]));
        $result2 = $this->validatetoken($this->datastyle[1], intval($tokens[1]));
        $result3 = $this->validatetoken($this->datastyle[2], intval($tokens[2]));
        
        
        if(!$result1 || !$result2 || !$result3){
            throw new MyException("Error en el formato la fecha para ".$tokens[0]."/".$tokens[1]."/".$tokens[2].". el formato establecido es: ".$this->datastyle[0]."/".$this->datastyle[1]."/".$this->datastyle[2]." ó ".$this->datastyle[0]."-".$this->datastyle[1]."-".$this->datastyle[2]);
        }
    }
    
    function validatetoken($ds, $token){
        $valid = true;
        switch (true) {
            case ($ds == "d" || $ds == "D"):
                if ($token < 1 || $token > 31) $valid = false;
                break;
            
            case ($ds == "m" || $ds == "M"):
                if ($token < 1 || $token > 12) $valid = false;
                break;
            
            case ($ds == "y" || $ds == "M"):
                if ($token < 1970) $valid = false;
                break;
        }
        
        return $valid;
    }
    
    function getDateStyle(){
        return $this->datastyle;
    }
    
}

?>