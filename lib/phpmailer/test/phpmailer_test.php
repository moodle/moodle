<?php
/*******************
  Unit Test
  Type: phpmailer class
********************/

$INCLUDE_DIR = "";

require("phpunit.php");
require($INCLUDE_DIR . "class.phpmailer.php");
error_reporting(E_ALL);

/**
 * Performs authentication tests
 */
class phpmailerTest extends TestCase
{
    /**
     * Holds the default phpmailer instance.
     * @private
     * @type object
     */
    var $Mail = false;

    /**
     * Holds the SMTP mail host.
     * @public
     * @type string
     */
    var $Host = "";
    
    /**
     * Holds the change log.
     * @private
     * @type string array
     */
    var $ChangeLog = array();
    
     /**
     * Holds the note log.
     * @private
     * @type string array
     */
    var $NoteLog = array();   

    /**
     * Class constuctor.
     */
    function phpmailerTest($name) {
        /* must define this constructor */
        $this->TestCase( $name );
    }
    
    /**
     * Run before each test is started.
     */
    function setUp() {
        global $global_vars;
        global $INCLUDE_DIR;

        $this->Mail = new phpmailer();

        $this->Mail->Priority = 3;
        $this->Mail->Encoding = "8bit";
        $this->Mail->CharSet = "iso-8859-1";
        $this->Mail->From = "unit_test@phpmailer.sf.net";
        $this->Mail->FromName = "Unit Tester";
        $this->Mail->Sender = "";
        $this->Mail->Subject = "Unit Test";
        $this->Mail->Body = "";
        $this->Mail->AltBody = "";
        $this->Mail->WordWrap = 0;
        $this->Mail->Host = $global_vars["mail_host"];
        $this->Mail->Port = 25;
        $this->Mail->Helo = "localhost.localdomain";
        $this->Mail->SMTPAuth = false;
        $this->Mail->Username = "";
        $this->Mail->Password = "";
        $this->Mail->PluginDir = $INCLUDE_DIR;
		$this->Mail->AddReplyTo("no_reply@phpmailer.sf.net", "Reply Guy");

        if(strlen($this->Mail->Host) > 0)
            $this->Mail->Mailer = "smtp";
        else
        {
            $this->Mail->Mailer = "mail";
            $this->Sender = "unit_test@phpmailer.sf.net";
        }
        
        global $global_vars;
        $this->SetAddress($global_vars["mail_to"]);
        
        // This is where you might place additional To, Bcc, etc addresses
    }     

    /**
     * Run after each test is completed.
     */
    function tearDown() {
        // Clean global variables
        $this->Mail = false;
        $this->ChangeLog = array();
        $this->NoteLog = array();
    }


    /**
     * Build the body of the message in the appropriate format.
     * @private
     * @returns void
     */
    function BuildBody() {
        $this->CheckChanges();
        
        // Determine line endings for message        
        if($this->Mail->ContentType == "text/html" || strlen($this->Mail->AltBody) > 0)
        {
            $eol = "<br/>";
            $bullet = "<li>";
            $bullet_start = "<ul>";
            $bullet_end = "</ul>";
        }
        else
        {
            $eol = "\n";
            $bullet = " - ";
            $bullet_start = "";
            $bullet_end = "";
        }
        
        $ReportBody = "";
        
        $ReportBody .= "---------------------" . $eol;
        $ReportBody .= "Unit Test Information" . $eol;
        $ReportBody .= "---------------------" . $eol;
        $ReportBody .= "phpmailer version: " . $this->Mail->Version . $eol;
        $ReportBody .= "Content Type: " . $this->Mail->ContentType . $eol;
        
        if(strlen($this->Mail->Host) > 0)
            $ReportBody .= "Host: " . $this->Mail->Host . $eol;
        
        // If attachments then create an attachment list
        if(count($this->Mail->attachment) > 0)
        {
            $ReportBody .= "Attachments";
            $ReportBody .= $bullet_start;
            for($i = 0; $i < count($this->Mail->attachment); $i++)
            {
                $ReportBody .= $bullet . "Name: " . $this->Mail->attachment[$i][1] . ", ";
                $ReportBody .= "Encoding: " . $this->Mail->attachment[$i][3] . ", ";
                $ReportBody .= "Type: " . $this->Mail->attachment[$i][4] . $eol;
            }
            $ReportBody .= $bullet_end . $eol;
        }
        
        // If there are changes then list them
        if(count($this->ChangeLog) > 0)
        {
            $ReportBody .= "Changes" . $eol;
            $ReportBody .= "-------" . $eol;

            $ReportBody .= $bullet_start;
            for($i = 0; $i < count($this->ChangeLog); $i++)
            {
                $ReportBody .= $bullet . $this->ChangeLog[$i][0] . " was changed to [" . 
                               $this->ChangeLog[$i][1] . "]" . $eol;
            }
            $ReportBody .= $bullet_end . $eol . $eol;
        }
        
        // If there are notes then list them
        if(count($this->NoteLog) > 0)
        {
            $ReportBody .= "Notes" . $eol;
            $ReportBody .= "-----" . $eol;

            $ReportBody .= $bullet_start;
            for($i = 0; $i < count($this->NoteLog); $i++)
            {
                $ReportBody .= $bullet . $this->NoteLog[$i] . $eol;
            }
            $ReportBody .= $bullet_end;
        }
        
        // Re-attach the original body
        $this->Mail->Body .= $eol . $eol . $ReportBody;
    }
    
    /**
     * Check which default settings have been changed for the report.
     * @private
     * @returns void
     */
    function CheckChanges() {
        if($this->Mail->Priority != 3)
            $this->AddChange("Priority", $this->Mail->Priority);
        if($this->Mail->Encoding != "8bit")
            $this->AddChange("Encoding", $this->Mail->Encoding);
        if($this->Mail->CharSet != "iso-8859-1")
            $this->AddChange("CharSet", $this->Mail->CharSet);
        if($this->Mail->Sender != "")
            $this->AddChange("Sender", $this->Mail->Sender);
        if($this->Mail->WordWrap != 0)
            $this->AddChange("WordWrap", $this->Mail->WordWrap);
        if($this->Mail->Mailer != "mail")
            $this->AddChange("Mailer", $this->Mail->Mailer);
        if($this->Mail->Port != 25)
            $this->AddChange("Port", $this->Mail->Port);
        if($this->Mail->Helo != "localhost.localdomain")
            $this->AddChange("Helo", $this->Mail->Helo);
        if($this->Mail->SMTPAuth)
            $this->AddChange("SMTPAuth", "true");
    }
    
    /**
     * Adds a change entry.
     * @private
     * @returns void
     */
    function AddChange($sName, $sNewValue) {
        $cur = count($this->ChangeLog);
        $this->ChangeLog[$cur][0] = $sName;
        $this->ChangeLog[$cur][1] = $sNewValue;
    }
    
    /**
     * Adds a simple note to the message.
     * @public
     * @returns void
     */
    function AddNote($sValue) {
        $this->NoteLog[] = $sValue;
    }

    /**
     * Adds all of the addresses
     * @public
     * @returns void
     */
    function SetAddress($sAddress, $sName = "", $sType = "to") {
        switch($sType)
        {
            case "to":
                $this->Mail->AddAddress($sAddress, $sName);
                break;
            case "cc":
                $this->Mail->AddCC($sAddress, $sName);
                break;
            case "bcc":
                $this->Mail->AddBCC($sAddress, $sName);
                break;
        }
    }

    /////////////////////////////////////////////////
    // UNIT TESTS
    /////////////////////////////////////////////////

    /**
     * Try a plain message.
     */
    function test_WordWrap() {

        $this->Mail->WordWrap = 40;
        $my_body = "Here is the main body of this message.  It should " .
                   "be quite a few lines.  It should be wrapped at the " .
                   "40 characters.  Make sure that it is.";
        $nBodyLen = strlen($my_body);
        $my_body .= "\n\nThis is the above body length: " . $nBodyLen;

        $this->Mail->Body = $my_body;
        $this->Mail->Subject .= ": Wordwrap";

        $this->BuildBody();
        if(!$this->Mail->Send())
        {
            $this->assert(false, $this->Mail->ErrorInfo);
            return;
        }
    
        $this->assert(true);
    }

    /**
     * Try a plain message.
     */
    function test_Low_Priority() {
    
        $this->Mail->Priority = 5;
        $this->Mail->Body = "Here is the main body.  There should be " .
                            "a reply to address in this message.";
        $this->Mail->Subject .= ": Low Priority";
        $this->Mail->AddReplyTo("nobody@nobody.com", "Nobody (Unit Test)");

        $this->BuildBody();
        if(!$this->Mail->Send())
        {
            $this->assert(false, $this->Mail->ErrorInfo);
            return;
        }
    
        $this->assert(true);
    }

    /**
     * Simple plain file attachment test.
     */
    function test_Plain_FileAttachment() {

        $this->Mail->Body = "Here is the text body";
        $this->Mail->Subject .= ": Plain and FileAttachment";
        
        if(!$this->Mail->AddAttachment("phpmailer_test.php", "test_attach.txt"))
        {
            $this->assert(false, $this->Mail->ErrorInfo);
            return;
        }

        $this->BuildBody();
        if(!$this->Mail->Send())
        {
            $this->assert(false, $this->Mail->ErrorInfo);
            return;
        }
    
        $this->assert(true);
    }

    /**
     * Simple plain string attachment test.
     */
    function test_Plain_StringAttachment() {

        $this->Mail->Body = "Here is the text body";
        $this->Mail->Subject .= ": Plain and StringAttachment";
        
        $sAttachment = "These characters are the content of the " .
                       "string attachment.\nThis might be taken from a ".
                       "database or some other such thing. ";
        
        $this->Mail->AddStringAttachment($sAttachment, "string_attach.txt");

        $this->BuildBody();
        if(!$this->Mail->Send())
        {
            $this->assert(false, $this->Mail->ErrorInfo);
            return;
        }
    
        $this->assert(true);
    }

    /**
     * Plain quoted-printable message.
     */
    function test_Quoted_Printable() {

        $this->Mail->Body = "Here is the main body";
        $this->Mail->Subject .= ": Plain and Quoted-printable";
        $this->Mail->Encoding = "quoted-printable";

        $this->BuildBody();
        if(!$this->Mail->Send())
        {
            $this->assert(false, $this->Mail->ErrorInfo);
            return;
        }
    
        $this->assert(true);
    }

    /**
     * Try a plain message.
     */
    function test_Html() {
    
        $this->Mail->IsHTML(true);
        $this->Mail->Subject .= ": HTML only";
        
        $this->Mail->Body = "This is a <b>test message</b> written in HTML. </br>" .
                            "Go to <a href=\"http://phpmailer.sourceforge.net/\">" .
                            "http://phpmailer.sourceforge.net/</a> for new versions of " .
                            "phpmailer.  <p/> Thank you!";

        $this->BuildBody();
        if(!$this->Mail->Send())
        {
            $this->assert(false, $this->Mail->ErrorInfo);
            return;
        }
    
        $this->assert(true);
    }

    /**
     * Simple HTML and attachment test
     */
    function test_HTML_Attachment() {

        $this->Mail->Body = "This is the <b>HTML</b> part of the email.";
        $this->Mail->Subject .= ": HTML and Attachment";
        $this->Mail->IsHTML(true);
        
        if(!$this->Mail->AddAttachment("phpmailer_test.php", "test_attach.txt"))
        {
            $this->assert(false, $this->Mail->ErrorInfo);
            return;
        }

        $this->BuildBody();
        if(!$this->Mail->Send())
        {
            $this->assert(false, $this->Mail->ErrorInfo);
            return;
        }
    
        $this->assert(true);
    }

    /**
     * An embedded attachment test.
     */
    function test_Embedded_Image() {

        $this->Mail->Body = "Embedded Image: <img alt=\"phpmailer\" src=\"cid:my-attach\">" .
                     "Here is an image!</a>";
        $this->Mail->Subject .= ": Embedded Image";
        $this->Mail->IsHTML(true);
        
        if(!$this->Mail->AddEmbeddedImage("rocks.png", "my-attach", "rocks.png"))
        {
            $this->assert(false, $this->Mail->ErrorInfo);
            return;
        }
        
        if($this->Mail->EmbeddedImageCount() < 0)
        {
            $this->assert(false, "Embedded image count below 1");
            return;
        }

        $this->BuildBody();
        if(!$this->Mail->Send())
        {
            $this->assert(false, $this->Mail->ErrorInfo);
            return;
        }
    
        $this->assert(true);
    }

    /**
     * Simple multipart/alternative test.
     */
    function test_AltBody() {

        $this->Mail->Body = "This is the <b>HTML</b> part of the email.";
        $this->Mail->AltBody = "This is the text part of the email.";
        $this->AddNote("This is a mulipart alternative email");
        $this->Mail->Subject .= ": AltBody";

        $this->BuildBody();
        if(!$this->Mail->Send())
        {
            $this->assert(false, $this->Mail->ErrorInfo);
            return;
        }
    
        $this->assert(true);
    }

    /**
     * Simple HTML and attachment test
     */
    function test_AltBody_Attachment() {

        $this->Mail->Body = "This is the <b>HTML</b> part of the email.";
        $this->Mail->AltBody = "This is the text part of the email.";
        $this->Mail->Subject .= ": AltBody and Attachment";
        $this->Mail->IsHTML(true);
        
        if(!$this->Mail->AddAttachment("phpmailer_test.php", "test_attach.txt"))
        {
            $this->assert(false, $this->Mail->ErrorInfo);
            return;
        }

        $this->BuildBody();
        if(!$this->Mail->Send())
        {
            $this->assert(false, $this->Mail->ErrorInfo);
            return;
        }
    
        $this->assert(true);
    }    
    
}  
 
/**
 * Create and run test instance.
 */
 
if(isset($HTTP_GET_VARS))
    $global_vars = $HTTP_GET_VARS;
else
    $global_vars = $_REQUEST;

if(isset($global_vars["submitted"]))
{
    echo "Test results:<br>";
    $suite = new TestSuite( "phpmailerTest" );
    
    $testRunner = new TestRunner;
    $testRunner->run($suite);
    echo "<hr noshade/>";
}

function get($sName) {
    global $global_vars;
    if(isset($global_vars[$sName]))
        return $global_vars[$sName];
    else
        return "";
}

?>

<html>
<body>
<h3>phpmailer Unit Test</h3>
By entering a SMTP hostname it will automatically perform tests with SMTP.

<form name="phpmailer_unit" action="phpmailer_test.php" method="get">
<input type="hidden" name="submitted" value="1"/>
To Address: <input type="text" size="50" name="mail_to" value="<?php echo get("mail_to"); ?>"/>
<br/>
Bcc Address: <input type="text" size="50" name="mail_bcc" value="<?php echo get("mail_bcc"); ?>"/>
<br/>
SMTP Hostname: <input type="text" size="50" name="mail_host" value="<?php echo get("mail_host"); ?>"/>
<p/>
<input type="submit" value="Run Test"/>

</form>
</body>
</html>
