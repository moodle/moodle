<?php
////////////////////////////////////////////////////
// phpmailer - PHP email class
//
// Version 1.25, Created 07/02/2001
//
// Class for sending email using either
// sendmail, PHP mail(), or SMTP.  Methods are
// based upon the standard AspEmail(tm) classes.
//
// Author: Brent R. Matzelle <bmatzelle@yahoo.com>
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * phpmailer - PHP email transport class
 * @author Brent R. Matzelle
 */
class phpmailer
{
   /////////////////////////////////////////////////
   // PUBLIC VARIABLES
   /////////////////////////////////////////////////

   /**
    * Email priority (1 = High, 3 = Normal, 5 = low). Default value is 3.
    * @public
    * @type int
    */
   var $Priority         = 3;

   /**
    * Sets the CharSet of the message. Default value is "iso-8859-1".
    * @public
    * @type string
    */
   var $CharSet          = "iso-8859-1";

   /**
    * Sets the Content-type of the message. Default value is "text/plain".
    * @public
    * @type string
    */
   var $ContentType      = "text/plain";

   /**
    * Sets the Encoding of the message. Options for this are "8bit" (default),
    * "7bit", "binary", "base64", and "quoted-printable".
    * @public
    * @type string
    */
   var $Encoding         = "8bit";

   /**
    * Holds the most recent mailer error message. Default value is "".
    * @public
    * @type string
    */
   var $ErrorInfo        = "";

   /**
    * Sets the From email of the message. Default value is "root@localhost".
    * @public
    * @type string
    */
   var $From             = "root@localhost";

   /**
    * Sets the From name of the message. Default value is "Root User".
    * @public
    * @type string
    */
   var $FromName         = "Root User";

   /**
    * Sets the Sender email of the message. If not empty, will be sent via -f to sendmail
    * or as 'MAIL FROM' in smtp mode. Default value is "".
    * @public
    * @type string
    */
   var $Sender           = "";

   /**
    * Sets the Subject of the message. Default value is "".
    * @public
    * @type string
    */
   var $Subject          = "";

   /**
    * Sets the Body of the message. Default value is "".
    * @public
    * @type string
    */
   var $Body             = "";

   /**
    * Sets word wrapping on the message. Default value is false (off).
    * @public
    * @type string
    */
   var $WordWrap         = false;

   /**
    * Method to send mail: ("mail", "sendmail", or "smtp").
    * Default value is "mail".
    * @public
    * @type string
    */
   var $Mailer           = "mail";

   /**
    * Sets the path of the sendmail program. Default value is
    * "/usr/sbin/sendmail".
    * @public
    * @type string
    */
   var $Sendmail         = "/usr/sbin/sendmail";

   /**
    *  Turns Microsoft mail client headers on and off. Default value is false (off).
    *  @public
    *  @type bool
    */
   var $UseMSMailHeaders = false;

   /**
    *  Holds phpmailer version.
    *  @public
    *  @type string
    */
   var $Version       = "phpmailer [version 1.25]";


   /////////////////////////////////////////////////
   // SMTP VARIABLES
   /////////////////////////////////////////////////

   /**
    *  Sets the SMTP host. Default value is "localhost".
    *  @public
    *  @type string
    */
   var $Host        = "localhost";

   /**
    *  Sets the SMTP server port. Default value is 25.
    *  @public
    *  @type int
    */
   var $Port        = 25;

   /**
    *  Sets the CharSet of the message. Default value is "localhost.localdomain".
    *  @public
    *  @type string
    */
   var $Helo        = "localhost.localdomain";

   /**
    *  Sets the SMTP server timeout. Default value is 10.
    *  @public
    *  @type int
    */
   var $Timeout     = 10; // Socket timeout in sec.

   /**
    *  Sets SMTP class debugging on or off. Default value is false (off).
    *  @public
    *  @type bool
    */
   var $SMTPDebug   = false;


   /////////////////////////////////////////////////
   // PRIVATE VARIABLES
   /////////////////////////////////////////////////

   /**
    *  Holds all "To" addresses.
    *  @type array
    */
   var $to            = array();

   /**
    *  Holds all "CC" addresses.
    *  @type array
    */
   var $cc            = array();

   /**
    *  Holds all "BCC" addresses.
    *  @type array
    */
   var $bcc           = array();

   /**
    *  Holds all "Reply-To" addresses.
    *  @type array
    */
   var $ReplyTo       = array();

   /**
    *  Holds all attachments.
    *  @type array
    */
   var $attachment    = array();

   /**
    *  Holds all custom headers.
    *  @type array
    */
   var $CustomHeader  = array();

   /**
    *  Holds the message boundary. Default is false.
    *  @type string
    */
   var $boundary      = false;

   /////////////////////////////////////////////////
   // VARIABLE METHODS
   /////////////////////////////////////////////////

   /**
    * Sets message type to HTML.  Returns void.
    * @public
    * @returns void
    */
   function IsHTML($bool) {
      if($bool == true)
         $this->ContentType = "text/html";
      else
         $this->ContentType = "text/plain";
   }

   /**
    * Sets Mailer to use SMTP.  Returns void.
    * @public
    * @returns void
    */
   function IsSMTP() {
      $this->Mailer = "smtp";
   }

   /**
    * Sets Mailer to use PHP mail() function.  Returns void.
    * @public
    * @returns void
    */
   function IsMail() {
      $this->Mailer = "mail";
   }

   /**
    * Sets Mailer to use $Sendmail program.  Returns void.
    * @public
    * @returns void
    */
   function IsSendmail() {
      $this->Mailer = "sendmail";
   }

   /**
    * Sets Mailer to use qmail MTA.  Returns void.
    * @public
    * @returns void
    */
   function IsQmail() {
      //$this->Sendmail = "/var/qmail/bin/qmail-inject";
      $this->Sendmail = "/var/qmail/bin/sendmail";
      $this->Mailer = "sendmail";
   }


   /////////////////////////////////////////////////
   // RECIPIENT METHODS
   /////////////////////////////////////////////////

   /**
    * Adds a "to" address.  Returns void.
    * @public
    * @returns void
    */
   function AddAddress($address, $name = "") {
      $cur = count($this->to);
      $this->to[$cur][0] = trim($address);
      $this->to[$cur][1] = $name;
   }

   /**
    * Adds a "Cc" address. Returns void.
    * @public
    * @returns void
    */
   function AddCC($address, $name = "") {
      $cur = count($this->cc);
      $this->cc[$cur][0] = trim($address);
      $this->cc[$cur][1] = $name;
   }

   /**
    * Adds a "Bcc" address. Note: this function works 
    * with the SMTP mailer on win32, not with the "mail" 
    * mailer.  This is a PHP bug that has been submitted 
    * on the Zend web site. The UNIX version of PHP 
    * functions correctly. 
    * Returns void.
    * @public
    * @returns void
    */
   function AddBCC($address, $name = "") {
      $cur = count($this->bcc);
      $this->bcc[$cur][0] = trim($address);
      $this->bcc[$cur][1] = $name;
   }

   /**
    * Adds a "Reply-to" address.  Returns void.
    * @public
    * @returns void
    */
   function AddReplyTo($address, $name = "") {
      $cur = count($this->ReplyTo);
      $this->ReplyTo[$cur][0] = trim($address);
      $this->ReplyTo[$cur][1] = $name;
   }


   /////////////////////////////////////////////////
   // MAIL SENDING METHODS
   /////////////////////////////////////////////////

   /**
    * Creates message and assigns Mailer. If the message is 
    * not sent successfully then it returns false.  Returns bool.
    * @public
    * @returns bool
    */
   function Send() {
      if(count($this->to) < 1)
      {
         $this->error_handler("You must provide at least one recipient email address");
         return false;
      }

      $header = $this->create_header();
      if(!$body = $this->create_body())
         return false;

      // Choose the mailer
      if($this->Mailer == "sendmail")
      {
         if(!$this->sendmail_send($header, $body))
            return false;
      }
      elseif($this->Mailer == "mail")
      {
         if(!$this->mail_send($header, $body))
            return false;
      }
      elseif($this->Mailer == "smtp")
      {
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
    * Sends mail using the $Sendmail program.  Returns bool.
    * @private
    * @returns bool
    */
   function sendmail_send($header, $body) {
      if ($this->Sender != "")
         $sendmail = sprintf("%s -f %s -t", $this->Sendmail, $this->Sender);
      else
         $sendmail = sprintf("%s -t", $this->Sendmail);

      if(!@$mail = popen($sendmail, "w"))
      {
         $this->error_handler(sprintf("Could not execute %s", $this->Sendmail));
         return false;
      }

      fputs($mail, $header);
      fputs($mail, $body);
      pclose($mail);

      return true;
   }

   /**
    * Sends mail using the PHP mail() function.  Returns bool.
    * @private
    * @returns bool
    */
   function mail_send($header, $body) {
      //$to = substr($this->addr_append("To", $this->to), 4, -2);

      // Cannot add Bcc's to the $to
      $to = $this->to[0][0]; // no extra comma
      for($i = 1; $i < count($this->to); $i++)
         $to .= sprintf(",%s", $this->to[$i][0]);

      if ($this->Sender != "" && PHP_VERSION >= "4.0")
      {
         $old_from = ini_get("sendmail_from");
         ini_set("sendmail_from", $this->Sender);
      }

      if ($this->Sender != "" && PHP_VERSION >= "4.0.5")
      {
         // The fifth parameter to mail is only available in PHP >= 4.0.5
         $params = sprintf("-f %s", $this->Sender);
         $rt = @mail($to, $this->Subject, $body, $header, $params);
      }
      else
      {
         $rt = @mail($to, $this->Subject, $body, $header);
      }

      if (isset($old_from))
         ini_set("sendmail_from", $old_from);

      if(!$rt)
      {
         $this->error_handler("Could not instantiate mail()");
         return false;
      }

      return true;
   }

   /**
    * Sends mail via SMTP using PhpSMTP (Author:
    * Chris Ryan).  Returns bool.
    * @private
    * @returns bool
    */
   function smtp_send($header, $body) {
      // Include SMTP class code, but not twice
      //include_once("class.smtp.php"); // Load code only if asked

      $smtp = new SMTP;
      $smtp->do_debug = $this->SMTPDebug;

      // Try to connect to all SMTP servers
      $hosts = explode(";", $this->Host);
      $index = 0;
      $connection = false;

      // Retry while there is no connection
      while($index < count($hosts) && $connection == false)
      {
         if($smtp->Connect($hosts[$index], $this->Port, $this->Timeout))
            $connection = true;
         //printf("%s host could not connect<br>", $hosts[$index]); //debug only
         $index++;
      }
      if(!$connection)
      {
         $this->error_handler("SMTP Error: could not connect to SMTP host server(s)");
         return false;
      }

      $smtp->Hello($this->Helo);
      if ($this->Sender == "")
         $smtp->Mail(sprintf("<%s>", $this->From));
      else
         $smtp->Mail(sprintf("<%s>", $this->Sender));

      for($i = 0; $i < count($this->to); $i++)
         $smtp->Recipient(sprintf("<%s>", $this->to[$i][0]));
      for($i = 0; $i < count($this->cc); $i++)
         $smtp->Recipient(sprintf("<%s>", $this->cc[$i][0]));
      for($i = 0; $i < count($this->bcc); $i++)
         $smtp->Recipient(sprintf("<%s>", $this->bcc[$i][0]));

      if(!$smtp->Data(sprintf("%s%s", $header, $body)))
      {
         $this->error_handler("SMTP Error: Data not accepted");
         return false;
      }
      $smtp->Quit();

      return true;
   }


   /////////////////////////////////////////////////
   // MESSAGE CREATION METHODS
   /////////////////////////////////////////////////

   /**
    * Creates recipient headers.  Returns string.
    * @private
    * @returns string
    */
   function addr_append($type, $addr) {
      $addr_str = "";
      $addr_str .= sprintf("%s: %s <%s>", $type, $addr[0][1], $addr[0][0]);
      if(count($addr) > 1)
      {
         for($i = 1; $i < count($addr); $i++)
         {
            $addr_str .= sprintf(", %s <%s>", $addr[$i][1], $addr[$i][0]);
         }
         $addr_str .= "\r\n";
      }
      else
         $addr_str .= "\r\n";

      return($addr_str);
   }

   /**
    * Wraps message for use with mailers that don't
    * automatically perform wrapping and for quoted-printable.
    * Original written by philippe.  Returns string.
    * @private
    * @returns string
    */
   function wordwrap($message, $length, $qp_mode = false) {
      if ($qp_mode)
        $soft_break = " =\r\n";
      else
        $soft_break = "\r\n";

      $message = $this->fix_eol($message);
      if (substr($message, -1) == "\r\n")
        $message = substr($message, 0, -2);

      $line = explode("\r\n", $message);
      $message = "";
      for ($i=0 ;$i < count($line); $i++)
      {
         $line_part = explode(" ", trim($line[$i]));
         $buf = "";
         for ($e = 0; $e<count($line_part); $e++)
         {
            $word = $line_part[$e];
            if ($qp_mode and (strlen($word) > $length))
            {
               $space_left = $length - strlen($buf) - 1;
               if ($e != 0)
               {
                  if ($space_left > 20)
                  {
                     $len = $space_left;
                     if (substr($word, $len - 1, 1) == "=")
                        $len--;
                     elseif (substr($word, $len - 2, 1) == "=")
                        $len -= 2;
                     $part = substr($word, 0, $len);
                     $word = substr($word, $len);
                     $buf .= " " . $part;
                     $message .= $buf . "=\r\n";
                  }
                  else
                  {
                     $message .= $buf . $soft_break;
                  }
                  $buf = "";
               }
               while (strlen($word) > 0)
               {
                  $len = $length;
                  if (substr($word, $len - 1, 1) == "=")
                     $len--;
                  elseif (substr($word, $len - 2, 1) == "=")
                     $len -= 2;
                  $part = substr($word, 0, $len);
                  $word = substr($word, $len);

                  if (strlen($word) > 0)
                     $message .= $part . "=\r\n";
                  else
                     $buf = $part;
               }
            }
            else
            {
               $buf_o = $buf;
               if ($e == 0)
                  $buf .= $word;
               else
                  $buf .= " " . $word;
               if (strlen($buf) > $length and $buf_o != "")
               {
                  $message .= $buf_o . $soft_break;
                  $buf = $word;
               }
            }
         }
         $message .= $buf . "\r\n";
      }

      return ($message);
   }

   /**
    * Assembles message header.  Returns a string if successful
    * or false if unsuccessful.
    * @private
    * @returns string
    */
   function create_header() {
      $header = array();
      $header[] = sprintf("Date: %s\r\n", $this->rfc_date());

      // To be created automatically by mail()
      if($this->Mailer != "mail")
         $header[] = $this->addr_append("To", $this->to);

      $header[] = sprintf("From: %s <%s>\r\n", $this->FromName, trim($this->From));
      if(count($this->cc) > 0)
         $header[] = $this->addr_append("Cc", $this->cc);

      // sendmail and mail() extract Bcc from the header before sending
      if((($this->Mailer == "sendmail") || ($this->Mailer == "mail")) && (count($this->bcc) > 0))
         $header[] = $this->addr_append("Bcc", $this->bcc);

      if(count($this->ReplyTo) > 0)
         $header[] = $this->addr_append("Reply-to", $this->ReplyTo);

      // mail() sets the subject itself
      if($this->Mailer != "mail")
         $header[] = sprintf("Subject: %s\r\n", trim($this->Subject));

      $header[] = sprintf("X-Priority: %d\r\n", $this->Priority);
      $header[] = sprintf("X-Mailer: %s\r\n", $this->Version);
      $header[] = sprintf("Return-Path: %s\r\n", trim($this->From));

      // Add custom headers
      for($index = 0; $index < count($this->CustomHeader); $index++)
         $header[] = sprintf("%s\r\n", $this->CustomHeader[$index]);

      if($this->UseMSMailHeaders)
         $header[] = $this->AddMSMailHeaders();

      // Add all attachments
      if(count($this->attachment) > 0)
      {
         // Set message boundary
         $this->boundary = "_b" . md5(uniqid(time()));

         $header[] = sprintf("Content-Type: Multipart/Mixed; charset = \"%s\";\r\n", $this->CharSet);
         $header[] = sprintf(" boundary=\"Boundary-=%s\"\r\n", $this->boundary);
      }
      else
      {
         $header[] = sprintf("Content-Transfer-Encoding: %s\r\n", $this->Encoding);
         $header[] = sprintf("Content-Type: %s; charset = \"%s\";\r\n", $this->ContentType, $this->CharSet);
      }

      $header[] = "MIME-Version: 1.0\r\n";

      return(join("", $header));
   }

   /**
    * Assembles the message body.  Returns a string if successful
    * or false if unsuccessful.
    * @private
    * @returns string
    */
   function create_body() {
      // wordwrap the message body if set
      if($this->WordWrap)
         $this->Body = $this->wordwrap($this->Body, $this->WordWrap);

      $this->Body = $this->encode_string($this->Body, $this->Encoding);

      if(count($this->attachment) > 0)
      {
         if(!$body = $this->attach_all())
            return false;
      }
      else
         $body = $this->Body;

      return($body);
   }


   /////////////////////////////////////////////////
   // ATTACHMENT METHODS
   /////////////////////////////////////////////////

   /**
    * Checks if attachment is valid and then adds 
    * the attachment to the list. 
    * Returns false if the file was not found.
    * @public
    * @returns bool
    */
   function AddAttachment($path, $name = "", $encoding = "base64", $type = "application/octet-stream") {
      if(!@is_file($path))
      {
         $this->error_handler(sprintf("Could not find %s file on filesystem", $path));
         return false;
      }

      $filename = basename($path);
      if($name == "")
         $name = $filename;

      // Append to $attachment array
      $cur = count($this->attachment);
      $this->attachment[$cur][0] = $path;
      $this->attachment[$cur][1] = $filename;
      $this->attachment[$cur][2] = $name;
      $this->attachment[$cur][3] = $encoding;
      $this->attachment[$cur][4] = $type;

      return true;
   }

   /**
    * Attaches text and binary attachments to body.  Returns a
    * string if successful or false if unsuccessful.
    * @private
    * @returns string
    */
   function attach_all() {
      // Return text of body
      $mime = array();
      $mime[] = "This is a MIME message. If you are reading this text, you\r\n";
      $mime[] = "might want to consider changing to a mail reader that\r\n";
      $mime[] = "understands how to properly display MIME multipart messages.\r\n\r\n";
      $mime[] = sprintf("--Boundary-=%s\r\n", $this->boundary);
      $mime[] = sprintf("Content-Type: %s; charset = \"%s\";\r\n", $this->ContentType, $this->CharSet);
      $mime[] = sprintf("Content-Transfer-Encoding: %s\r\n\r\n", $this->Encoding);
      $mime[] = sprintf("%s\r\n", $this->Body);

      // Add all attachments
      for($i = 0; $i < count($this->attachment); $i++)
      {
         $path = $this->attachment[$i][0];
         $filename = $this->attachment[$i][1];
         $name = $this->attachment[$i][2];
         $encoding = $this->attachment[$i][3];
         $type = $this->attachment[$i][4];
         $mime[] = sprintf("--Boundary-=%s\r\n", $this->boundary);
         $mime[] = sprintf("Content-Type: %s;\r\n", $type);
         $mime[] = sprintf("name=\"%s\"\r\n", $name);
         $mime[] = sprintf("Content-Transfer-Encoding: %s\r\n", $encoding);
         $mime[] = sprintf("Content-Disposition: attachment; filename=\"%s\"\r\n\r\n", $name);
         if(!$mime[] = sprintf("%s\r\n\r\n", $this->encode_file($path, $encoding)))
            return false;
      }
      $mime[] = sprintf("\r\n--Boundary-=%s--\r\n", $this->boundary);

      return(join("", $mime));
   }

   /**
    * Encodes attachment in requested format.  Returns a
    * string if successful or false if unsuccessful.
    * @private
    * @returns string
    */
   function encode_file ($path, $encoding = "base64") {
      if(!@$fd = fopen($path, "r"))
      {
         $this->error_handler(sprintf("File Error: Could not open file %s", $path));
         return false;
      }
      $file = fread($fd, filesize($path));
      $encoded = $this->encode_string($file, $encoding);
      fclose($fd);

      return($encoded);
   }

   /**
    * Encodes string to requested format. Returns a
    * string if successful or false if unsuccessful.
    * @private
    * @returns string
    */
   function encode_string ($str, $encoding = "base64") {
      switch(strtolower($encoding)) {
         case "base64":
            // chunk_split is found in PHP >= 3.0.6
            $encoded = chunk_split(base64_encode($str));
            break;

         case "7bit":
         case "8bit":
            $encoded = $this->fix_eol($str);
            if (substr($encoded, -2) != "\r\n")
               $encoded .= "\r\n";
            break;

         case "binary":
            $encoded = $str;
            break;

         case "quoted-printable":
            $encoded = $this->encode_qp($str);
            break;

         default:
            $this->error_handler(sprintf("Unknown encoding: %s", $encoding));
            return false;
      }
      return($encoded);
   }

   /**
    * Encode string to quoted-printable.  Returns a string.
    * @private
    * @returns string
    */
   function encode_qp ($str) {
      $encoded = $this->fix_eol($str);
      if (substr($encoded, -2) != "\r\n")
         $encoded .= "\r\n";

      // Replace every high ascii, control and = characters
      $encoded = preg_replace("/([\001-\010\013\014\016-\037\075\177-\377])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
      // Replace every spaces and tabs when it's the last character on a line
      $encoded = preg_replace("/([\011\040])\r\n/e", "'='.sprintf('%02X', ord('\\1')).'\r\n'", $encoded);

      // Maximum line length of 76 characters before CRLF (74 + space + '=')
      $encoded = $this->WordWrap($encoded, 74, true);

      return $encoded;
   }

   /////////////////////////////////////////////////
   // MESSAGE RESET METHODS
   /////////////////////////////////////////////////

   /**
    * Clears all recipients assigned in the TO array.  Returns void.
    * @public
    * @returns void
    */
   function ClearAddresses() {
      $this->to = array();
   }

   /**
    * Clears all recipients assigned in the CC array.  Returns void.
    * @public
    * @returns void
    */
   function ClearCCs() {
      $this->cc = array();
   }

   /**
    * Clears all recipients assigned in the BCC array.  Returns void.
    * @public
    * @returns void
    */
   function ClearBCCs() {
      $this->bcc = array();
   }

   /**
    * Clears all recipients assigned in the ReplyTo array.  Returns void.
    * @public
    * @returns void
    */
   function ClearReplyTos() {
      $this->ReplyTo = array();
   }

   /**
    * Clears all recipients assigned in the TO, CC and BCC
    * array.  Returns void.
    * @public
    * @returns void
    */
   function ClearAllRecipients() {
      $this->to = array();
      $this->cc = array();
      $this->bcc = array();
   }

   /**
    * Clears all previously set attachments.  Returns void.
    * @public
    * @returns void
    */
   function ClearAttachments() {
      $this->attachment = array();
   }

   /**
    * Clears all custom headers.  Returns void.
    * @public
    * @returns void
    */
   function ClearCustomHeaders() {
      $this->CustomHeader = array();
   }


   /////////////////////////////////////////////////
   // MISCELLANEOUS METHODS
   /////////////////////////////////////////////////

   /**
    * Adds the error message to the error container.
    * Returns void.
    * @private
    * @returns void
    */
   function error_handler($msg) {
        $this->ErrorInfo = $msg;
   }
   
   /**
    * Returns the proper RFC 822 formatted date. Returns string.
    * @private
    * @returns string
    */
   function rfc_date() {
        $tz = date("Z");
        $tzs = ($tz < 0) ? "-" : "+";
        $tz = abs($tz);
        $tz = $tz/36 + $tz % 3600;
        $date = sprintf("%s %s%04d", date("D, j M Y H:i:s"), $tzs, $tz);
        return $date;
   }

   /**
    * Changes every end of line from CR or LF to CRLF.  Returns string.
    * @private
    * @returns string
    */
   function fix_eol($str) {
      $str = str_replace("\r\n", "\n", $str);
      $str = str_replace("\r", "\n", $str);
      $str = str_replace("\n", "\r\n", $str);
      return $str;
   }

   /**
    * Adds a custom header.  Returns void.
    * @public
    * @returns void
    */
   function AddCustomHeader($custom_header) {
      $this->CustomHeader[] = $custom_header;
   }

   /**
    * Adds all the Microsoft message headers.  Returns string.
    * @private
    * @returns string
    */
   function AddMSMailHeaders() {
      $MSHeader = "";
      if($this->Priority == 1)
         $MSPriority = "High";
      elseif($this->Priority == 5)
         $MSPriority = "Low";
      else
         $MSPriority = "Medium";

      $MSHeader .= sprintf("X-MSMail-Priority: %s\r\n", $MSPriority);
      $MSHeader .= sprintf("Importance: %s\r\n", $MSPriority);

      return($MSHeader);
   }

}
// End of class
?>
