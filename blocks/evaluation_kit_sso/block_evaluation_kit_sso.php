<?php
/**
* block_evaluation_kit_sso definition
*
* @package block_evaluation_kit_sso
*/
require_once('EvalKitOAuthConsumer.php');
require_once("Utils.php");

class block_evaluation_kit_sso extends block_base {
    function init() {
        $this->title = get_string('blockname', 'block_evaluation_kit_sso');
	    $this->content_type = BLOCK_TYPE_TEXT;
    }
    public function get_content() {

		global $CFG, $USER, $COURSE, $PAGE, $SITE;

		if ($this->content !== null ) {
			return $this->content;
		}
	
		$objUtils = new EvalKitUtils();
		$isUserStudent = $objUtils->isUserStudent($USER->id);

		$ret = '';
		$config = get_config('blocks/evaluation_kit_sso');
		if ($PAGE->user_is_editing() && !$isUserStudent)  
		{
			$ret = '<p>'.get_string('BlockEditMessage', 'block_evaluation_kit_sso').'</p>';
		}
		else 
		{
			/* turn off parent portlet*/
			$ret = '<!--EvaluationKIT 1-->';
			$ret .= '<div id="ek-widget">ek-widget</div>';
			$ret .= '<script>var evalkit_portlet = document.getElementById("ek-widget").parentNode.parentNode;if(evalkit_portlet !== null && evalkit_portlet.classList.contains("block_evaluation_kit_sso"));evalkit_portlet.style.display = "none";</script>';
		
			$config = get_config('blocks/evaluation_kit_sso');
			if ($USER->id > 0 && (
				is_null($config->EvalKitaccounturl) 
				|| is_null($config->EvalKitconsumerkey) 
				|| is_null($config->EvalKitsharedsecretkey) 
				|| empty($config->EvalKitaccounturl) 
				|| empty($config->EvalKitconsumerkey) 
				|| empty($config->EvalKitsharedsecretkey))
				) 
			{
				// do nothing leave off widget, they can edit in edit mode
			}
			else if ($USER->id > 0)
			{
				$accounturl = $config->EvalKitaccounturl;
				$consumerkey = $config->EvalKitconsumerkey;
				$sharedsecretkey = $config->EvalKitsharedsecretkey;
				$apppath = $CFG->wwwroot.'/blocks/evaluation_kit_sso/';
				$basestring = '';
				try
				{
					$coursecode = null;
					$courseuniqueid = null;
					$courseid = null;
					if ($SITE->shortname != $COURSE->shortname)
					{
						$courseid = $COURSE->id;
						$coursecode = $COURSE->shortname;
						$courseuniqueid = $COURSE->idnumber;
						if($courseuniqueid == null || empty($courseuniqueid)) 
						{
							$courseuniqueid = $coursecode;
						}
					}
					$provider = new EvalKitOAuthConsumer($USER->username, $consumerkey, $sharedsecretkey, $accounturl.'/UserIntegration/Settings/Moodle', 'GET', $coursecode, $courseuniqueid, null);
					if ($courseid!=null)
					{
						$provider->addParameter("course_id", $courseid);
					}		      
					if($courseuniqueid != null) {
						$provider->addParameter("course_unique_id", urlencode(str_replace(" ","",$courseuniqueid)));
					}
					if($coursecode != null) {
						$provider->addParameter("course_code", urlencode(str_replace(" ","",$coursecode)));
					}
					$provider->addParameter("evalkit_apppath", urlencode($apppath));
					$provider->addParameter("evalkit_source", "32");//32=OAuth Moodle, 33=popup
					$provider->sign();
					$basestring = $provider->getbasestring();
				}
				catch(OAuthException2 $e)
				{
					$ret = '<!--EvaluationKIT Error: '.$e.'-->'; // funny tag trickery
					$ret .= '<script>alert("'.$e.'");</script>'; // debug only
				}
				/* note: dont turn off while editing */
				$ret .= '<script>';
				$ret .= 'var evalkit_portlet = document.getElementById("ek-widget").parentNode.parentNode;if(evalkit_portlet !== null && evalkit_portlet.classList.contains("block_evaluation_kit_sso"));evalkit_portlet.style.display = "none";';
				$ret .= 'var evalkit_apppath = "'.$apppath.'";';
				$ret .= 'var evalkit_url = "'.$accounturl.'";';
				$ret .= 'var evalkit_courseid = "'.$COURSE->id.'";';
				$ret .= 'var evalkit_coursecode = "'.$COURSE->shortname.'";';
				$ret .= 'var evalkit_courseuniqueid = "'.$COURSE->idnumber.'";';
				$ret .= 'var evalkit_coursetitle = "";';
				$ret .= 'var evalkit_moduleid = "'.$this->instance->id.'";';
				$ret .= 'var evalkit_userid = "'.$USER->id.'";';
				$ret .= 'var evalkit_auth = "'.$basestring.'";';

				$ret .= 'document.write(unescape(\'%3Cscript async defer src="'.$accounturl.'/Scripts/Moodle/core.min.js" type="text/javascript"%3E%3C/script%3E\'));';
				$ret .= 'document.write(unescape(\'%3Clink async defer href="'.$accounturl.'/Scripts/Moodle/core.min.css" rel="stylesheet"%3E\'));';
				$ret .= '</script>';
			}
			$ret .= '<!--EvaluationKIT-->';
		}
        $this->content = new stdClass;
		$this->content->text = $ret;

        $this->content->footer = '';

        return $this->content;
    }
    function has_config() {
        return true;
    }
    function instance_allow_multiple() {
        return false;
    }
    function instance_allow_config() {
        return false;
    }
}