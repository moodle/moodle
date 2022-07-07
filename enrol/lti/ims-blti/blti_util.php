<?php

require_once 'OAuth.php';

  // Replace this with some real function that pulls from the LMS.
  function getLMSDummyData() {
    $parms = array(
      "resource_link_id" => "120988f929-274612",
      "resource_link_title" => "Weekly Blog",
      "resource_link_description" => "Each student needs to reflect on the weekly reading.  These should be one paragraph long.",
      "user_id" => "292832126",
      "roles" => "Instructor",  // or Learner
      "lis_person_name_full" => 'Jane Q. Public',
      "lis_person_contact_email_primary" => "user@school.edu",
      "lis_person_sourcedid" => "school.edu:user",
      "context_id" => "456434513",
      "context_title" => "Design of Personal Environments",
      "context_label" => "SI182",
      );

    return $parms;
  }

  function validateDescriptor($descriptor)
  {
    $xml = new SimpleXMLElement($xmldata);
    if ( ! $xml ) {
       echo("Error parsing Descriptor XML\n");
       return;
    }
    $launch_url = $xml->secure_launch_url[0];
    if ( ! $launch_url ) $launch_url = $xml->launch_url[0];
    if ( $launch_url ) $launch_url = (string) $launch_url;
    return $launch_url;
  }

  // Parse a descriptor
  function launchInfo($xmldata) {
    $xml = new SimpleXMLElement($xmldata);
    if ( ! $xml ) {
       echo("Error parsing Descriptor XML\n");
       return;
    }
    $launch_url = $xml->secure_launch_url[0];
    if ( ! $launch_url ) $launch_url = $xml->launch_url[0];
    if ( $launch_url ) $launch_url = (string) $launch_url;
    $custom = array();
    if ( $xml->custom[0]->parameter )
    foreach ( $xml->custom[0]->parameter as $resource) {
      $key = (string) $resource['key'];
      $key = strtolower($key);
      $nk = "";
      for($i=0; $i < strlen($key); $i++) {
        $ch = substr($key,$i,1);
        if ( $ch >= "a" && $ch <= "z" ) $nk .= $ch;
        else if ( $ch >= "0" && $ch <= "9" ) $nk .= $ch;
        else $nk .= "_";
      }
      $value = (string) $resource;
      $custom["custom_".$nk] = $value;
    }
    return array("launch_url" => $launch_url, "custom" => $custom ) ;
  }

function signParameters($oldparms, $endpoint, $method, $oauth_consumer_key, $oauth_consumer_secret,
    $submit_text = false, $org_id = false, $org_desc = false)
{
    global $last_base_string;
    $parms = $oldparms;
    if ( ! isset($parms["lti_version"]) ) $parms["lti_version"] = "LTI-1p0";
    if ( ! isset($parms["lti_message_type"]) ) $parms["lti_message_type"] = "basic-lti-launch-request";
    if ( ! isset($parms["oauth_callback"]) ) $parms["oauth_callback"] = "about:blank";
    if ( $org_id ) $parms["tool_consumer_instance_guid"] = $org_id;
    if ( $org_desc ) $parms["tool_consumer_instance_description"] = $org_desc;
    if ( $submit_text ) $parms["ext_submit"] = $submit_text;

    $test_token = '';

    $hmac_method = new OAuthSignatureMethod_HMAC_SHA1();
    $test_consumer = new OAuthConsumer($oauth_consumer_key, $oauth_consumer_secret, NULL);

    $acc_req = OAuthRequest::from_consumer_and_token($test_consumer, $test_token, $method, $endpoint, $parms);

    $acc_req->sign_request($hmac_method, $test_consumer, $test_token);

    // Pass this back up "out of band" for debugging
    $last_base_string = $acc_req->get_signature_base_string();

    $newparms = $acc_req->get_parameters();

    return $newparms;
}

function signOnly($oldparms, $endpoint, $method, $oauth_consumer_key, $oauth_consumer_secret)
{
    global $last_base_string;
    $parms = $oldparms;

    $test_token = '';

    $hmac_method = new OAuthSignatureMethod_HMAC_SHA1();
    $test_consumer = new OAuthConsumer($oauth_consumer_key, $oauth_consumer_secret, NULL);

    $acc_req = OAuthRequest::from_consumer_and_token($test_consumer, $test_token, $method, $endpoint, $parms);
    $acc_req->sign_request($hmac_method, $test_consumer, $test_token);

    // Pass this back up "out of band" for debugging
    $last_base_string = $acc_req->get_signature_base_string();

    $newparms = $acc_req->get_parameters();

    return $newparms;
}

function postLaunchHTML($newparms, $endpoint, $debug=false, $iframeattr=false) {
    global $last_base_string;
    $r = "<div id=\"ltiLaunchFormSubmitArea\">\n";
    if ( $iframeattr ) {
        $r = "<form action=\"".$endpoint."\" name=\"ltiLaunchForm\" id=\"ltiLaunchForm\" method=\"post\" target=\"basicltiLaunchFrame\" encType=\"application/x-www-form-urlencoded\">\n" ;
    } else {
        $r = "<form action=\"".$endpoint."\" name=\"ltiLaunchForm\" id=\"ltiLaunchForm\" method=\"post\" encType=\"application/x-www-form-urlencoded\">\n" ;
    }
    $submit_text = $newparms['ext_submit'];
    foreach($newparms as $key => $value ) {
        $key = htmlspecialchars($key);
        $value = htmlspecialchars($value);
        if ( $key == "ext_submit" ) {
            $r .= "<input type=\"submit\" name=\"";
        } else {
            $r .= "<input type=\"hidden\" name=\"";
        }
        $r .= $key;
        $r .= "\" value=\"";
        $r .= $value;
        $r .= "\"/>\n";
    }
    if ( $debug ) {
        $r .= "<script language=\"javascript\"> \n";
        $r .= "  //<![CDATA[ \n" ;
        $r .= "function basicltiDebugToggle() {\n";
        $r .= "    var ele = document.getElementById(\"basicltiDebug\");\n";
        $r .= "    if(ele.style.display == \"block\") {\n";
        $r .= "        ele.style.display = \"none\";\n";
        $r .= "    }\n";
        $r .= "    else {\n";
        $r .= "        ele.style.display = \"block\";\n";
        $r .= "    }\n";
        $r .= "} \n";
        $r .= "  //]]> \n" ;
        $r .= "</script>\n";
        $r .= "<a id=\"displayText\" href=\"javascript:basicltiDebugToggle();\">";
        $r .= get_stringIMS("toggle_debug_data","basiclti")."</a>\n";
        $r .= "<div id=\"basicltiDebug\" style=\"display:none\">\n";
        $r .=  "<b>".get_stringIMS("basiclti_endpoint","basiclti")."</b><br/>\n";
        $r .= $endpoint . "<br/>\n&nbsp;<br/>\n";
        $r .=  "<b>".get_stringIMS("basiclti_parameters","basiclti")."</b><br/>\n";
        foreach($newparms as $key => $value ) {
            $key = htmlspecialchars($key);
            $value = htmlspecialchars($value);
            $r .= "$key = $value<br/>\n";
        }
        $r .= "&nbsp;<br/>\n";
        $r .= "<p><b>".get_stringIMS("basiclti_base_string","basiclti")."</b><br/>\n".$last_base_string."</p>\n";
        $r .= "</div>\n";
    }
    $r .= "</form>\n";
    if ( $iframeattr ) {
        $r .= "<iframe name=\"basicltiLaunchFrame\"  id=\"basicltiLaunchFrame\" src=\"\"\n";
        $r .= $iframeattr . ">\n<p>".get_stringIMS("frames_required","basiclti")."</p>\n</iframe>\n";
    }
    if ( ! $debug ) {
        $ext_submit = "ext_submit";
        $ext_submit_text = $submit_text;
        $r .= " <script type=\"text/javascript\"> \n" .
            "  //<![CDATA[ \n" .
            "    document.getElementById(\"ltiLaunchForm\").style.display = \"none\";\n" .
            "    nei = document.createElement('input');\n" .
            "    nei.setAttribute('type', 'hidden');\n" .
            "    nei.setAttribute('name', '".$ext_submit."');\n" .
            "    nei.setAttribute('value', '".$ext_submit_text."');\n" .
            "    document.getElementById(\"ltiLaunchForm\").appendChild(nei);\n" .
            "    document.ltiLaunchForm.submit(); \n" .
            "  //]]> \n" .
            " </script> \n";
    }
    $r .= "</div>\n";
    return $r;
}

/* This is a bit of homage to Moodle's pattern of internationalisation */
function get_stringIMS($key,$bundle) {
    return $key;
}

function do_post_request($url, $data, $optional_headers = null)
{
  $params = array('http' => array(
              'method' => 'POST',
              'content' => $data
            ));

  if ($optional_headers !== null) {
     $header = $optional_headers . "\r\n";
  }
  // $header = $header . "Content-type: application/x-www-form-urlencoded\r\n";
  $params['http']['header'] = $header;
  $ctx = stream_context_create($params);
  $fp = @fopen($url, 'rb', false, $ctx);
  if (!$fp) {
    echo @stream_get_contents($fp);
    throw new Exception("Problem with $url, $php_errormsg");
  }
  $response = @stream_get_contents($fp);
  if ($response === false) {
    throw new Exception("Problem reading data from $url, $php_errormsg");
  }
  return $response;
}

