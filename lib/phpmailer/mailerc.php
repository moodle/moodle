<?php
////////////////////////////////////////////////////
// mailerc - phpmailer client
//
// Version 0.07, Created 2001-01-03
//
// Client application for sending outgoing 
// messages from a file. 
//
// Author: Brent R. Matzelle <bmatzelle@yahoo.com>
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

require("class.phpmailer.php");

// Gather global server arg variables
if(!isset($_SERVER))
    $_SERVER = $HTTP_SERVER_VARS;
$cargv = $_SERVER["argv"];
$cargc = $_SERVER["argc"];

define("PROG_VERSION", "0.01");
define("PROG_NAME", $cargv[0]);
@set_time_limit(0); // unlimited

// Ignore warning messages
error_reporting(E_ERROR);

/**
 * mailerc - mailerc extension class
 * @author Brent R. Matzelle
 */
class mailerc extends phpmailer
{
    /**
     * Send an email from a file created with the
     * SendToQueue() function.
     * @public
     * @returns bool
     */
    function SendFromFile($filePath) {
        $qarray = array();
        $to_list = array();
        $header = "";
        $body = "";

        // Make sure is there and accessible
        if(!is_file($filePath))
        {
            $this->error_handler(sprintf("Cannot access: %s", $filePath));
            return false;
        }
        
        // upon getting header do not forget to gather the 
        // server info (date, recieved())
        $qarray = file($filePath);
        
        if(count($qarray) < 1)
        {
            $this->error_handler("Invalid queue file");
            return false;
        }

        // Create the header and the body (just set header)
        $header = $this->received();
        $header .= sprintf("Date: %s\r\n", $this->rfc_date());
        
        $msg_start = 0;
        for($i = 0; $i < count($qarray); $i++)
        {
            if($qarray[$i] == "----END PQM HEADER----\r\n")
            {
                $msg_start = $i + 1;
                break;
            }
        }
        
        for($i = $msg_start; $i < count($qarray); $i++)
            $body .= $qarray[$i];

        $this->Mailer = $this->qvar($qarray, "Mailer");
        if($this->Mailer == "sendmail")
        {
            $this->Sendmail = $this->qvar($qarray, "Sendmail");
            $this->Sender   = $this->qvar($qarray, "Sender");

            if(!$this->sendmail_send($header, $body))
                return false;
        }
        elseif($this->Mailer == "mail")
        {
            $this->Sender  = $this->qvar($qarray, "Sender");
            $this->Subject = $this->qvar($qarray, "Subject");

            $to_list = explode(";", $this->qvar($qarray, "to"));
            for($i = 0; $i < count($to_list); $i++)
                $this->AddAddress($to_list[0], "");

            // This might not work because of not sending 
            // both a header and body.
            if(!$this->mail_send($header, $body))
                return false;
        }
        elseif($this->Mailer == "smtp")
        {
            $this->Host     = $this->qvar($qarray, "Host");
            $this->Port     = $this->qvar($qarray, "Port");
            $this->Helo     = $this->qvar($qarray, "Helo");
            $this->Timeout  = $this->qvar($qarray, "Timeout");
            $this->SMTPAuth = (int)$this->qvar($qarray, "SMTPAuth");
            $this->Username = $this->qvar($qarray, "Username");
            $this->Password = $this->qvar($qarray, "Password");
            $this->From     = $this->qvar($qarray, "From");

            $to_addr = $this->qvar($qarray, "to");
            if(!empty($to_addr))
            {
                $to_list = explode(";", $to_addr);
                for($i = 0; $i < count($to_list); $i++)
                    $this->AddAddress($to_list[0], "");
            }

            $to_addr = $this->qvar($qarray, "cc");
            if(!empty($to_addr))
            {
                $to_list = explode(";", $to_addr);
                for($i = 0; $i < count($to_list); $i++)
                    $this->AddCC($to_list[0], "");
            }

            $to_addr = $this->qvar($qarray, "bcc");
            if(!empty($to_addr))
            {
                $to_list = explode(";", $to_addr);
                for($i = 0; $i < count($to_list); $i++)
                    $this->AddBCC($to_list[0], "");
            }

            if(!$this->smtp_send($header, $body))
                return false;
        }
        else
        {
            $this->error_handler(sprintf("%s mailer is not supported", $this->Mailer));
            return false;
        }
        
        return true;
    }
    
    /**
     * Return the given queue variable from the pqm header file. Returns 
     * an empty string if the data was not found.
     * @private
     * @return string
     */
    function qvar($qarray, $data) {
        $i = 0;
        $pqm_marker = "----END PQM HEADER----\n";

        while($qarray[$i] != $pqm_marker)
        {
            $item = explode(": ", $qarray[$i]);
            //echo $item[0] . "\n"; // debug
            if($item[0] == $data)
                return rtrim($item[1]);
            $i++;
        }

        return ""; // failure
    }
}

/**
 * Print out the program version information.
 * @private
 * @returns void
 */
function print_version()
{
   printf("mailerc %s - phpmailer client\n\n" .
     "This program is distributed in the hope that it will be useful,\n" .
     "but WITHOUT ANY WARRANTY; without even the implied warranty of \n" .
     "MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the \n" .
     "GNU Lesser General Public License for more details.\n\n" .
     "Written by: Brent R. Matzelle\n", PROG_VERSION);
}

/*
  Print out the help message to the console.
  @private
  @returns void
 */
function print_help()
{
  printf("mailerc %s, phpmailer queue daemon.\n", PROG_VERSION);
  printf("Usage: %s [OPTION] [FILE]\n", PROG_NAME);
  printf("
Options:
  -h,  --help                            print this help.
  -V,  --version                         print version information.
  -s,  --send                            send [FILE]\n");
}

/**
 * Sends a given message from a pqm (Phpmailer Queue Message) file.
 * @private
 * @returns bool
 */
function send_message($filePath)
{
    // Open the file and read the header contents and set 
    // another message.  Then run the phpmailer send file.
    $mail = new mailerc();
    if(!$mail->SendFromFile($filePath))
        printf("error: %s\n", $mail->ErrorInfo);
    else
        printf("success: sent\n");
}

/*
  Pseudo main()
 */
if($cargc < 1)
{
    print_version();
    exit;
}

switch($cargv[1])
{
    case "-h":
    case "--help":
        print_help();
        break;
    case "-V":
    case "--version":
        print_version();
        break;
    case "-s":
    case "--send":
        send_message($cargv[2]);
        break;
    default:
        print_help();
}

return 0;  // return success
?>
