<?php
 //This is the ODBC Socket Server class PHP client class with sample usage
 // at bottom.
 // (c) 1999 Team FXML
 // Released into the public domain for version 0.90 of ODBC Socket Server
 // http://odbc.linuxbox.com/

class ODBCSocketServer {
    var $sHostName; //name of the host to connect to
    var $nPort; //port to connect to
    var $sConnectionString; //connection string to use

    //function to parse the SQL 
    function ExecSQL($sSQL) {

        $fToOpen = fsockopen($this->sHostName, $this->nPort, &$errno, &$errstr, 30);
        if (!$fToOpen)
        {
            //contruct error string to return
            $sReturn = "<?xml version=\"1.0\"?>\r\n<result state=\"failure\">\r\n<error>$errstr</error>\r\n</result>\r\n";
        }
        else
        {
            //construct XML to send
            //search and replace HTML chars in SQL first
            $sSQL = HTMLSpecialChars($sSQL);
            $sSend = "<?xml version=\"1.0\"?>\r\n<request>\r\n<connectionstring>$this->sConnectionString</connectionstring>\r\n<sql>$sSQL</sql>\r\n</request>\r\n";
            //write request			
            fputs($fToOpen, $sSend);
            //now read response
            while (!feof($fToOpen))
            {
                $sReturn = $sReturn . fgets($fToOpen, 128);
            }
            fclose($fToOpen);
        }
        return $sReturn;
    }
}//class
?>
