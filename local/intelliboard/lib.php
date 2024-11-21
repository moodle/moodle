<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

use local_intelliboard\helpers\CustomMenuHelper;

// In versions before Moodle 2.9, the supported callbacks have _extends_ (not imperative mood) in their names. This was a consistency bug fixed in MDL-49643.
function local_intelliboard_extends_navigation(global_navigation $nav)
{
	global $CFG, $USER;

    local_intelliboard_init();
    $customMenu = new CustomMenuHelper("Intelliboard");
	$context = context_system::instance();
	if (isloggedin() and get_config('local_intelliboard', 't1') and has_capability('local/intelliboard:students', $context)) {
		$alt_name = get_config('local_intelliboard', 't0');
		$def_name = get_string('ts1', 'local_intelliboard');
		$name = ($alt_name) ? $alt_name : $def_name;


		$learner_menu = get_config('local_intelliboard', 'learner_menu');
        $learner_roles = get_config('local_intelliboard', 'filter11');
        $access = false;
        $roles = $learner_roles ? explode(',', $learner_roles) : [];

		if ($learner_menu) {
			if(!empty($roles)) {
                foreach ($roles as $role) {
                    if ($role and user_has_role_assignment($USER->id, $role)){
                        $access = true;
                        break;
                    }
                }
                if ($access) {
                    $nav->add($name, new moodle_url($CFG->wwwroot.'/local/intelliboard/student/index.php'));
                    $customMenu->add($name, new moodle_url($CFG->wwwroot.'/local/intelliboard/student/index.php'));
                }
			}
		} else {
			$nav->add($name, new moodle_url($CFG->wwwroot.'/local/intelliboard/student/index.php'));
			$customMenu->add($name, new moodle_url($CFG->wwwroot.'/local/intelliboard/student/index.php'));
		}
	}

	if(has_capability('local/intelliboard:view', $context) and get_config('local_intelliboard', 'ssomenu')){
		$nav->add(get_string('ianalytics', 'local_intelliboard'), new moodle_url($CFG->wwwroot.'/local/intelliboard/index.php?action=sso'));
		$customMenu->add(get_string('ianalytics', 'local_intelliboard'), new moodle_url($CFG->wwwroot.'/local/intelliboard/index.php?action=sso'));
	}
	if (isloggedin() and get_config('local_intelliboard', 'n10')){
	    //Check if user is enrolled to any courses with "instructor" role(s)
		$instructor_roles = get_config('local_intelliboard', 'filter10');
	    if (!empty($instructor_roles)) {
	    	$access = false;
		    $roles = explode(',', $instructor_roles);
		    if (!empty($roles)) {
			    foreach ($roles as $role) {
			    	if ($role and user_has_role_assignment($USER->id, $role)){
			    		$access = true;
			    		break;
			    	}
			    }
				if ($access) {
					$alt_name = get_config('local_intelliboard', 'n11');
					$def_name = get_string('n10', 'local_intelliboard');
					$name = ($alt_name) ? $alt_name : $def_name;
					$nav->add($name, new moodle_url($CFG->wwwroot.'/local/intelliboard/instructor/index.php'));
					$customMenu->add($name, new moodle_url($CFG->wwwroot.'/local/intelliboard/instructor/index.php'));
				}
			}
		}
	}
	$customMenu->setupMenu();
}
//call-back method to extend the navigation
function local_intelliboard_extend_navigation(global_navigation $nav)
{
	global $CFG, $DB, $USER, $PAGE;

    local_intelliboard_init();
    try {
        $customMenu = new CustomMenuHelper("Intelliboard");
		$mynode = $PAGE->navigation->find('myprofile', navigation_node::TYPE_ROOTNODE);
		$mynode->collapse = true;
		$mynode->make_inactive();
		$context = context_system::instance();

    if(has_capability('local/intelliboard:view', $context) and get_config('local_intelliboard', 'ssomenu')){
        $name = get_string('ianalytics', 'local_intelliboard');
        $url = new moodle_url($CFG->wwwroot.'/local/intelliboard/index.php?action=sso');
        $nav->add($name, $url);
        $customMenu->add($name, $url);
        $node = $mynode->add($name, $url, 0, null, 'intelliboard_admin', new pix_icon('i/pie_chart', '', 'local_intelliboard'));
        $node->showinflatnavigation = true;
    }

		if (isloggedin() and get_config('local_intelliboard', 't1')) {
			$alt_name = get_config('local_intelliboard', 't0');
			$def_name = get_string('ts1', 'local_intelliboard');
			$name = ($alt_name) ? $alt_name : $def_name;

			$learner_menu = get_config('local_intelliboard', 'learner_menu');
            $learner_roles = get_config('local_intelliboard', 'filter11');
            $access = false;
            $roles = $learner_roles ? explode(',', $learner_roles) : [];

			if ($learner_menu) {
				if(!empty($roles)) {
                    foreach ($roles as $role) {
                        if ($role and user_has_role_assignment($USER->id, $role)){
                            $access = true;
                            break;
                        }
                    }
                    if ($access) {
                        $url = new moodle_url($CFG->wwwroot.'/local/intelliboard/student/index.php');
                        $nav->add($name, $url);
                        $customMenu->add($name, $url);
                        $node = $mynode->add($name, $url, 0, null, 'intelliboard_student', new pix_icon('i/line_chart', '', 'local_intelliboard'));
                        $node->showinflatnavigation = true;
                    }
				}
			} elseif (has_capability('local/intelliboard:students', $context)) {
				$url = new moodle_url($CFG->wwwroot.'/local/intelliboard/student/index.php');
				$nav->add($name, $url);
				$customMenu->add($name, $url);
				$node = $mynode->add($name, $url, 0, null, 'intelliboard_student', new pix_icon('i/line_chart', '', 'local_intelliboard'));
				$node->showinflatnavigation = true;
			}
		}

		if (isloggedin() and get_config('local_intelliboard', 'n10')) {
		    //Check if user is enrolled to any courses with "instructor" role(s)
			$instructor_roles = get_config('local_intelliboard', 'filter10');
		    if (!empty($instructor_roles)) {
		    	$access = false;
			    $roles = explode(',', $instructor_roles);
			    if (!empty($roles)) {
				    foreach ($roles as $role) {
				    	if ($role and user_has_role_assignment($USER->id, $role)){
				    		$access = true;
				    		break;
				    	}
				    }
					if ($access) {
						$alt_name = get_config('local_intelliboard', 'n11');
						$def_name = get_string('n10', 'local_intelliboard');
						$name = ($alt_name) ? $alt_name : $def_name;
						$url = new moodle_url($CFG->wwwroot.'/local/intelliboard/instructor/index.php');
						$nav->add($name, $url);
						$customMenu->add($name, $url);

						$node = $mynode->add($name, $url, 0, null, 'intelliboard_instructor', new pix_icon('i/area_chart', '', 'local_intelliboard'));
						$node->showinflatnavigation = true;
					}
				}
			}
		}
		if (isloggedin() and get_config('local_intelliboard', 'competency_dashboard') and has_capability('local/intelliboard:competency', $context)) {
			$alt_name = get_config('local_intelliboard', 'a11');
			$def_name = get_string('a0', 'local_intelliboard');
			$name = ($alt_name) ? $alt_name : $def_name;
			$url = new moodle_url($CFG->wwwroot.'/local/intelliboard/competencies/index.php');
			$nav->add($name, $url);
			$customMenu->add($name, $url);

			$node = $mynode->add($name, $url, 0, null, 'intelliboard_competency', new pix_icon('i/bar_chart', '', 'local_intelliboard'));
			$node->showinflatnavigation = true;
		}

        // attendance
        if(isloggedin() and get_config('local_intelliboard', 'enableattendance')) {
            $coursenode = $nav->find($PAGE->course->id, navigation_node::TYPE_COURSE);

            if($coursenode === false OR !($PAGE->course->id > 1))  {
                // show attendance in site navigation
                $name = get_string('attendance', 'local_intelliboard');
                $url = new moodle_url('/local/intelliboard/attendance/index.php');
                $nav->add($name, $url);
                $customMenu->add($name, $url);

                $node = $mynode->add($name, $url, 0, null, 'intelliboard_attendance', new pix_icon('i/book', '', 'local_intelliboard'));
                $node->showinflatnavigation = true;
            } else {
                // show attendance in course navigation
                $name = get_string('attendance', 'local_intelliboard');
                $url = new moodle_url(
                    '/local/intelliboard/attendance/index.php',
                    ['course_id' => $PAGE->course->id]
                );
                $node = navigation_node::create(
                    $name,
                    $url,
                    navigation_node::TYPE_CUSTOM,
                    null,
                    'intelliboard_attendance',
                    new pix_icon('i/calendar', '', 'core')
                );
                $coursenode->add_node($node);
            }
        }
        $customMenu->setupMenu();
	} catch (Exception $e) {}
}
function local_intelliboard_extend_settings_navigation(settings_navigation $settingsnav, context $context)
{
    global $CFG;
    require_once($CFG->dirroot .'/local/intelliboard/locallib.php');
    require_once($CFG->dirroot .'/local/intelliboard/instructor/lib.php');

    $coursenode = $settingsnav->get('courseadmin');
    if ($coursenode && get_config('local_intelliboard', 'n19') && get_config('local_intelliboard', 'n10') && check_intelliboard_instructor_access()) {
        $cache = cache::make('local_intelliboard', 'reports_list');

        $reports = $cache->get('reports_list');
        if(!$reports){
            $params = array('do'=>'instructor','mode'=> 2);
            $intelliboard = intelliboard($params);

            if(isset($intelliboard->reports)){
                $reports = $intelliboard->reports;
                $cache->set('reports_list', $reports);
            }
        }

        $cat = $coursenode->add(get_string('intelliboard_reports', 'local_intelliboard'), null, navigation_node::TYPE_CONTAINER, null, 'intelliboard');

        if (is_array($reports)) {
            foreach ($reports as $key=>$report) {
                $cat->add(format_string($report->name), new moodle_url('/local/intelliboard/instructor/reports.php',array('id'=>format_string($key))), navigation_node::TYPE_CUSTOM);
            }
        }
    }
}

function local_intelliboard_user_details()
{
	$platform = "Unknown OS Platform";
	$browser = "Unknown Browser";

		try {
			$regexes = local_intelliboard_get_regexes();
			$agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	    foreach ($regexes->os_parsers as $regex) {
	        $flag = isset($regex->regex_flag) ? $regex->regex_flag : '';
	        if (preg_match('@' . $regex->regex . '@' . $flag, $agent, $matches)) {
	            $platform = (isset($regex->os_replacement))?str_replace('$1', $matches[1], $regex->os_replacement):$matches[1];
                if (isset($matches[2])) {
                    if (isset($regex->os_v1_replacement)) {
                        $platform .= ' ' . str_replace('$1', $matches[2], $regex->os_v1_replacement);
                    } else {
                        $platform .= ' ' . $matches[2];
                    }
                }
	            break;
	        }
	    }
	    foreach ($regexes->user_agent_parsers as $regex) {
	        $flag = isset($regex->regex_flag) ? $regex->regex_flag : '';
	        if (preg_match('@' . $regex->regex . '@' . $flag, $agent, $matches)) {
	            $browser = (isset($regex->family_replacement))?str_replace('$1', $matches[1], @$regex->family_replacement):$matches[1];
	            break;
	        }
	    }
		} catch (Exception $e) {}

	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}elseif (isset($_SERVER["HTTP_CLIENT_IP"])){
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	}else{
		$ip = $_SERVER["REMOTE_ADDR"];
	}
	$ip = ($ip) ? $ip : 0;
	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$userlang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	} else {
		$userlang = 'Unknown';
	}

	return array('useragent' => $browser, 'useros' => $platform, 'userip' => $ip, 'userlang' => $userlang);
}

function local_intelliboard_get_regexes(){
    global $CFG;

    return json_decode(file_get_contents($CFG->dirroot .'/local/intelliboard/classes/regexes.json'));
}

function local_intelliboard_insert_tracking($ajaxRequest = false, $trackparameters = []) {
    global $CFG, $PAGE, $SITE, $DB, $USER;

		$enabled = get_config('local_intelliboard', 'enabled');
    $path = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    if (strpos($path,'cron.php') !== false) {
        return false;
    }

    if ($enabled and isloggedin() and !isguestuser()) {
				$version = get_config('local_intelliboard', 'version');
		    $inactivity = (int) get_config('local_intelliboard', 'inactivity');
		    $trackadmin = get_config('local_intelliboard', 'trackadmin');
		    $trackpoint = get_config('local_intelliboard', 'trackpoint');
		    $intelliboardMediaTrack = get_config('local_intelliboard', 'trackmedia');
		    $compresstrackingtype = get_config('local_intelliboard', 'compresstracking');
				$cachetrackconfig = \cache::make('local_intelliboard', 'track_config');
				$ajax = (int) get_config('local_intelliboard', 'ajax');
				$lastajaxtracking = get_user_preferences('last_intelliboard_ajax_tracking', 0);

				if (is_siteadmin() and !$trackadmin) {
            return false;
        }
				if ($ajaxRequest && (time() - $lastajaxtracking < $ajax)) {
						return true;
				} else {
						set_user_preference('last_intelliboard_ajax_tracking', time());
				}
        if (!empty($trackparameters['page']) && !empty($trackparameters['param']) && !empty($trackparameters['time'])) {
            $intelliboardPage = $trackparameters['page'];
            $intelliboardParam = $trackparameters['param'];
            $intelliboardTime = $trackparameters['time'];
        } else {
            $intelliboardPage = (isset($_COOKIE['intelliboardPage'])) ? clean_param($_COOKIE['intelliboardPage'], PARAM_ALPHANUMEXT) : '';
            $intelliboardParam = (isset($_COOKIE['intelliboardParam'])) ? clean_param($_COOKIE['intelliboardParam'], PARAM_INT) : 0;
            $intelliboardTime = (isset($_COOKIE['intelliboardTime'])) ? clean_param($_COOKIE['intelliboardTime'], PARAM_INT) : 0;
        }

        if (!empty($intelliboardPage) and !empty($intelliboardParam) and !empty($intelliboardTime)) {
            if ($compresstrackingtype > 0) {
                $storage = local_intelliboard\tools\compress_tracking::getStorage($compresstrackingtype);
                $storage->saveData($ajaxRequest, $intelliboardTime, $intelliboardPage, $intelliboardParam);
            } else {
                $userDetails = (object)local_intelliboard_user_details();
                if ($data = $DB->get_record('local_intelliboard_tracking', array('userid' => $USER->id, 'page' => $intelliboardPage, 'param' => $intelliboardParam), 'id, visits, timespend, lastaccess')) {
                    if ($intelliboardMediaTrack) {
                        if ($data->lastaccess <= (time() - $intelliboardTime)) {
                            $data->lastaccess = time();
                        } else {
                            $intelliboardTime = 0;
                        }
                    } else {
                        if (!$ajaxRequest) {
                            $data->visits = $data->visits + 1;
                            $data->lastaccess = time();
                        } else {
                            if ($data->lastaccess < strtotime('today')) {
                                $data->lastaccess = time();
                            } else {
                                unset($data->lastaccess);
                            }
                            unset($data->visits);
                        }
                    }
                    if ($intelliboardTime) {
                        $data->timespend = $data->timespend + $intelliboardTime;
                        $data->useragent = $userDetails->useragent;
                        $DB->update_record('local_intelliboard_tracking', $data);
                    }
                } else {
                    $courseid = 0;
                    if ($intelliboardPage == "module") {
                        $courseid = $DB->get_field_sql("SELECT c.id FROM {course} c, {course_modules} cm WHERE c.id = cm.course AND cm.id = $intelliboardParam");
                    } elseif ($intelliboardPage == "course") {
                        $courseid = $intelliboardParam;
                    }
                    $data = new stdClass();
                    $data->userid = $USER->id;
                    $data->courseid = $courseid;
                    $data->page = $intelliboardPage;
                    $data->param = $intelliboardParam;
                    $data->visits = 1;
                    $data->timespend = $intelliboardTime;
                    $data->firstaccess = time();
                    $data->lastaccess = time();
                    $data->useragent = $userDetails->useragent;
                    $data->useros = $userDetails->useros;
                    $data->userlang = $userDetails->userlang;
                    $data->userip = $userDetails->userip;
                    $data->id = $DB->insert_record('local_intelliboard_tracking', $data, true);
                }

                $tracklogs = get_config('local_intelliboard', 'tracklogs');
                $trackdetails = get_config('local_intelliboard', 'trackdetails');
                $tracktotals = get_config('local_intelliboard', 'tracktotals');

                if ($version >= 2016011300) {
                    $currentstamp = strtotime('today');
                    if ($data->id and $tracklogs) {
                        if ($log = $DB->get_record('local_intelliboard_logs', array('trackid' => $data->id, 'timepoint' => $currentstamp))) {
                            if (!$ajaxRequest) {
                                $log->visits = $log->visits + 1;
                            }
                            $log->timespend = $log->timespend + $intelliboardTime;
                            $DB->update_record('local_intelliboard_logs', $log);
                        } else {
                            $log = new stdClass();
                            $log->trackid = $data->id;
                            $log->visits = 1;
                            $log->timespend = $intelliboardTime;
                            $log->timepoint = $currentstamp;
                            $log->id = $DB->insert_record('local_intelliboard_logs', $log, true);
                        }

                        if ($version >= 2017072300 and isset($log->id) and $trackdetails) {
                            $currenthour = date('G');
                            if ($detail = $DB->get_record('local_intelliboard_details', array('logid' => $log->id, 'timepoint' => $currenthour))) {
                                if (!$ajaxRequest) {
                                    $detail->visits = $detail->visits + 1;
                                }
                                $detail->timespend = $detail->timespend + $intelliboardTime;
                                $DB->update_record('local_intelliboard_details', $detail);
                            } else {
                                $detail = new stdClass();
                                $detail->logid = $log->id;
                                $detail->visits = 1;
                                $detail->timespend = $intelliboardTime;
                                $detail->timepoint = $currenthour;
                                $detail->id = $DB->insert_record('local_intelliboard_details', $detail, true);
                            }
                        }
                    }
                }
            }
            $currentstamp = strtotime('today');
            $tracktotals = get_config('local_intelliboard', 'tracktotals');

            if (!empty($tracktotals)) {
                $sessions = false;
                $courses = false;

                if (!$ajaxRequest) {
                    if ($trackpoint != $currentstamp) {
                        set_config("trackpoint", $currentstamp, "local_intelliboard");
                        $cachetrackconfig->purge();
                    }

                    $cachekey = 'type_0_instanceid_' . $USER->id;
                    if (!$cachetrackconfig->has($cachekey)) {
                        $sessions = new stdClass();
                        $sessions->type = 0;
                        $sessions->instanceid = (int)$USER->id;
                        $sessions->timecreated = $currentstamp;
                        if (!$cachetrackconfig->set($cachekey, $sessions)){
                            // Something wrong.
                            error_log("Intelliboard tracking config: error save track to cache, key:{$cachekey}, data:" . json_encode($sessions));
                        }
                    }

                    $cachekey = 'type_1_instanceid_' . $intelliboardParam;
                    if ($intelliboardPage == 'course' and !$cachetrackconfig->has($cachekey)) {
                        $courses = new stdClass();
                        $courses->type = 1;
                        $courses->instanceid = (int)$intelliboardParam;
                        $courses->timecreated = $currentstamp;
                        if (!$cachetrackconfig->set($cachekey, $sessions)){
                            // Something wrong.
                            error_log("Intelliboard tracking config: error save track to cache, key:{$cachekey}, data:" . json_encode($courses));
                        }
                    }
                }

                if ($data = $DB->get_record('local_intelliboard_totals', array('timepoint' => $currentstamp))) {
                    if (!$ajaxRequest) {
                        $data->visits = $data->visits + 1;
                    }
                    if ($sessions) {
                        $data->sessions = $data->sessions + 1;
                    }
                    if ($courses) {
                        $data->courses = $data->courses + 1;
                    }
                    $data->timespend = $data->timespend + $intelliboardTime;
                    $DB->update_record('local_intelliboard_totals', $data);
                } else {
                    $data = new stdClass();
                    $data->sessions = 1;
                    $data->courses = ($courses) ? 1 : 0;
                    $data->visits = 1;
                    $data->timespend = $intelliboardTime;
                    $data->timepoint = $currentstamp;
                    $DB->insert_record('local_intelliboard_totals', $data);
                }
            }
        } else {
            $intelliboardTime = 0;
        }


        if ($ajaxRequest) {
            return ['time' => $intelliboardTime];
        }
        $pageurl = $PAGE->has_set_url() && !empty($PAGE->url->get_path()) ? $PAGE->url->get_path() : '';

        if (isset($PAGE->cm->id)) {
            $intelliboardPage = 'module';
            $intelliboardParam = $PAGE->cm->id;
        } elseif (isset($PAGE->course->id) and $SITE->id != $PAGE->course->id) {
            $intelliboardPage = 'course';
            $intelliboardParam = $PAGE->course->id;
        } elseif (strpos($pageurl, '/user/') !== false) {
            $intelliboardPage = 'user';
            $intelliboardParam = $USER->id;
        } elseif (strpos($pageurl, '/intelliboard/student/courses') !== false) {
            $intelliboardPage = 'local_intelliboard';
            $intelliboardParam = 1;
        } elseif (strpos($pageurl, '/intelliboard/student/grades') !== false) {
            $intelliboardPage = 'local_intelliboard';
            $intelliboardParam = 2;
        } elseif (strpos($pageurl, '/intelliboard/student/reports') !== false) {
            $intelliboardPage = 'local_intelliboard';
            $intelliboardParam = 3;
        } elseif (strpos($pageurl, '/intelliboard/student/monitors') !== false) {
            $intelliboardPage = 'local_intelliboard';
            $intelliboardParam = 4;
        } elseif (strpos($pageurl, '/intelliboard/student/') !== false) {
            $intelliboardPage = 'local_intelliboard';
            $intelliboardParam = 5;
        } elseif (strpos($pageurl, '/intelliboard/instructor/monitors') !== false) {
            $intelliboardPage = 'local_intelliboard';
            $intelliboardParam = 6;
        } elseif (strpos($pageurl, '/intelliboard/instructor/reports') !== false) {
            $intelliboardPage = 'local_intelliboard';
            $intelliboardParam = 7;
        } elseif (strpos($pageurl, '/intelliboard/instructor/courses') !== false) {
            $intelliboardPage = 'local_intelliboard';
            $intelliboardParam = 8;
        } elseif (strpos($pageurl, '/intelliboard/instructor/') !== false) {
            $intelliboardPage = 'local_intelliboard';
            $intelliboardParam = 9;
        } elseif (strpos($pageurl, '/intelliboard/competencies/') !== false) {
            $intelliboardPage = 'local_intelliboard';
            $intelliboardParam = 10;
        } elseif (strpos($pageurl, '/intelliboard/monitors') !== false) {
            $intelliboardPage = 'local_intelliboard';
            $intelliboardParam = 11;
        } elseif (strpos($pageurl, '/intelliboard/reports') !== false) {
            $intelliboardPage = 'local_intelliboard';
            $intelliboardParam = 12;
        } elseif (strpos($pageurl, '/intelliboard/') !== false) {
            $intelliboardPage = 'local_intelliboard';
            $intelliboardParam = 1;
        } elseif (strpos($pageurl, '/local/') !== false) {
            $start = strpos($pageurl, '/', strpos($pageurl, '/local/') + 1) + 1;
            $end = strpos($pageurl, '/', $start);
            $intelliboardPage = 'local_' . substr($pageurl, $start, ($end - $start));
            $intelliboardParam = 1;
        } else {
            $intelliboardPage = 'site';
            $intelliboardParam = 1;
        }
        $params = new stdClass();
        $params->intelliboardAjax = $ajax;
        $params->intelliboardAjaxUrl = $ajax ? "$CFG->wwwroot/local/intelliboard/ajax.php" : "";
        $params->intelliboardInactivity = $inactivity;
        $params->intelliboardPeriod = 1000;
        $params->intelliboardPage = $intelliboardPage;
        $params->intelliboardParam = $intelliboardParam;
        $params->intelliboardMediaTrack = $intelliboardMediaTrack;
        $params->intelliboardTime = 0;
        $params->intelliboardSSOLink = (get_config('local_intelliboard', 'ssomenu')) ? $CFG->wwwroot . '/local/intelliboard/index.php?action=sso' : false;

        $PAGE->requires->js('/local/intelliboard/module.js', false);
        $PAGE->requires->js_call_amd('local_intelliboard/tracking', 'trackActivityLabelClicks', [$params->intelliboardAjaxUrl]);
        $PAGE->requires->js_init_call('intelliboardInit', array($params), false);

        return true;
    }
}

function local_intelliboard_get_fontawesome_icon_map() {
    return array(
        'local_intelliboard:i/pie_chart' => 'fa-pie-chart',
        'local_intelliboard:i/line_chart' => 'fa-line-chart',
        'local_intelliboard:i/area_chart' => 'fa-area-chart',
        'local_intelliboard:i/bar_chart' => 'fa-bar-chart',
        'local_intelliboard:i/book' => 'fa-book',
    );
}

function local_intelliboard_init()
{
	$tracking = get_config('local_intelliboard', 'enabled');
	if ($tracking && !CLI_SCRIPT && !AJAX_SCRIPT) {
		local_intelliboard_insert_tracking();
	}
}
