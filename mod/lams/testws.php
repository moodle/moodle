Testing web service...
<BR>
<?php
  require("../../config.php");
  require($CFG->dirroot.'/lib/nusoap/nusoap.php');

    $wsdl = "http://137.111.229.11:8080/lams/services/UserManagementService?wsdl";
    $s = new soapclient($wsdl,true,false,false,false,false,2,3);
    $server_id = "http://chalk:8080";
  $server_key = "3dPartyKEY";
  $datetime = "September 05, 2005 11:39 am";
  $username = "andrew";
  $password = "password";
  $email = "andrew@hotmail.com";
  $roles = "learner|staff|author";
  $fname = "Andrew";
  $lname = "Logue";
  $org_name = "org name";
  $org_desc = "org desc";
  $account = true;

    $plaintext = trim($datetime).trim($server_id).trim($server_key);
    $hashvalue = sha1(strtolower($plaintext));
    echo "hashvalue:".$hashvalue."<BR>";
    $parameters = array($server_id,$datetime,$hashvalue,$org_name,$org_desc,$account);
    $result = $s->call('createOrganisationForDemoServer',$parameters);
    if($s->getError()){
        echo "Error was:".$s->getError()."<BR>";
    }else{
        echo "orgId:".$result."<BR>";
        $plaintext1 = trim($datetime).trim($username).trim($server_id).trim($server_key);
        $hashvalue1 = sha1(strtolower($plaintext1));
        echo "hashvalue1:".$hashvalue1."<BR>";
        $parameters = array($server_id,$datetime,$hashvalue1,$username,$password,$email,$roles,$fname,$lname,$result);
        $result1 = $s->call('createUserForDemoServer',$parameters);
        if($s->getError()){
            echo "Error was:".$s->getError()."<BR>";
        }else{
            echo "userId:".$result1."<BR>";
        }
    }
    unset($s);

?>
<BR>
End

