<?php
    require_once('../../config.php');
    require_once('EvalKitOAuthConsumer.php');
    global $CFG, $USER;
    require_login();
    $config = get_config('blocks/evaluation_kit_sso');    
	$basestring = '';
	$config = get_config('blocks/evaluation_kit_sso');
	$popup = optional_param('isPopup', null, PARAM_INT);
	$cid = optional_param('cid', null, PARAM_INT);
	$pid = optional_param('pid', null, PARAM_INT);
	$roles = optional_param('roles', null, PARAM_TEXT);
	$config = get_config('blocks/evaluation_kit_sso');

		if (is_null($config->EvalKitaccounturl) 
		|| is_null($config->EvalKitconsumerkey) 
		|| is_null($config->EvalKitsharedsecretkey) 
		|| empty($config->EvalKitaccounturl) 
		|| empty($config->EvalKitconsumerkey) 
		|| empty($config->EvalKitsharedsecretkey)) 
		{
			// do nothing leave off widget, they can edit in edit mode
		}
		else
		{
			$accounturl = $config->EvalKitaccounturl;
			$consumerkey = $config->EvalKitconsumerkey;
			$sharedsecretkey = $config->EvalKitsharedsecretkey;
			try
			{
				$provider = new EvalKitOAuthConsumer($USER->username, $consumerkey, $sharedsecretkey, $accounturl.'/Login/OAuth1', 'GET', null, null, null);
				if($cid != null) {
        			$provider->addParameter('evalkit_course_id', $cid);
				}
				if($pid != null) {
					$provider->addParameter('evalkit_project_id', $pid);
				}
				if($roles != null) {
					$provider->addParameter("roles", urlencode($roles));
				}
				if ($popup!= null) {
					$provider->addParameter("evalkit_source", "33"); //32=OAuth Moodle, 33=popup
				}
				else {
					$provider->addParameter("evalkit_source", "32");
				}
				$provider->sign();
				$basestring = $provider->getbasestring();
			}
			catch(OAuthException2 $e)
			{
				$ret = '</script><!--EvaluationKIT Error: '.$e.'-->'; // tag trickery
				$ret .= '<script>alert("'.$e.'");'; // debug only
			}
		}
		if (empty($basestring)) 
		{
			echo '<p>'.get_string('BlockError', 'block_evaluation_kit_sso').'</p>';
		}
		else 
		{
			$ekurl = $accounturl.'/Login/OAuth1?'.$basestring;
			header("Location: ".$ekurl);
			die();
		}
?>