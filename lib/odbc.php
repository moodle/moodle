<?php

/**
 * odbc.php - This is the ODBC Socket Server class PHP client class 
 * with sample usage at bottom.
 *
 * Released into the public domain for version 0.90 of ODBC Socket Server
 * {@link http://odbc.linuxbox.com/}
 * @author Team FXML
 * @copyright Copyright (c) 1999 Team FXML
 * @license http://odbc.linuxbox.com/ public domain
 * @package moodlecore
 */
 
 /**
 * ODBC Socket Server class
 */
class ODBCSocketServer {

   /**
    * Name of the host to connect to
    * @var string $sHostName 
    */
    var $sHostName;
   /**
    * Port to connect to
    * @var int $nPort 
    */
    var $nPort;
   /**
    * Connection string to use
    * @var string $sConnectionString 
    */
    var $sConnectionString;

    // 
    /** 
     * Function to parse the SQL
     *
     * @param string $sSQL The SQL statement to parse
     * @return string
     */
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
