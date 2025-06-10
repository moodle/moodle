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

$string['wq'] = 'Wiris Quizzes';
$string['pluginname'] = 'Wiris Quizzes';
$string['access_provider_enabled'] = 'Access control';
$string['access_provider_enabled_help'] = 'If enabled ony authenticated users can access Wiris services.';
$string['pluginnamesummary'] = '';
$string['wq_help'] = 'Generic Wiris Quizzes Help';
$string['editingwq'] = 'Editing a generic Wiris Quizzes question';
$string['addingwq'] = 'Adding a generic Wiris Quizzes question';
$string['cachedef_images'] = "Wiris Quizzes images.";
$string['error'] = 'ERROR';
$string['info_maintitle'] = "Wiris Quizzes test page";
$string['info_tableheader_test'] = "Test";
$string['info_tableheader_report'] = "Report";
$string['info_tableheader_status'] = "Status";
$string['info_disabled'] = "DISABLED";
$string['info_enabled'] = "ENABLED";
$string['info_enabled'] = "ENABLED";
$string['info_information'] = "For more information or if you have any doubt contact Wiris Support:";
$string['info_test1_name'] = "Wiris Quizzes version";
$string['info_test1_rt1'] = "Wiris Quizzes version is ";
$string['info_test1_rt2'] = "Impossible to find Wiris Quizzes version.";
$string['info_test2_name'] = "MathType filter version";
$string['info_test2_info'] = "Check MathType filter ";
$string['info_test2_infopage'] = "info page";
$string['info_test2_rt1'] = "MathType filter is properly installed.";
$string['info_test2_rt2'] = "Wiris Quizzes requires MathType filter 3.17.20 or greater. Your version is";
$string['info_test2_rt3'] = "Impossible to find MathType filter version file.";
$string['info_test3_name'] = "Moodle version";
$string['info_test3_rt1'] = "Your Moodle version is sufficiently new.";
$string['info_test3_rt2'] = "Your Moodle version is %s. Wiris Quizzes could not work correctly with Moodle version prior to 2011060313";
$string['info_test3_rt3'] = "Impossible to find Moodle version file.";
$string['info_test4_name'] = "Plugins";
$string['info_test4_pluginname1'] = "True/False question type";
$string['info_test4_pluginname2'] = "Short-Answer question type";
$string['info_test4_pluginname3'] = "Embedded answer (Cloze) question type";
$string['info_test4_pluginname4'] = "Multi Choice question type";
$string['info_test4_pluginname5'] = "Matching question type";
$string['info_test4_pluginname6'] = "Essay question type";
$string['info_test4_pluginname7'] = "Commons question type";
$string['info_test4_rt1'] = "The following Wiris Quizzes question types are installed:";
$string['info_test4_rt2'] = "The following Wiris Quizzes question types are missing:";
$string['info_test4_rt3'] = "Install";
$string['info_test5_name'] = "Database";
$string['info_test5_rt1'] = "All tables are present.";
$string['info_test5_rt2'] = "One or more of tables are missing.";
$string['info_test6_name'] = "Wiris Quizzes";
$string['info_test7_name'] = "Checking configuration";
$string['info_test8_name'] = "Checking if server is reachable";
$string['info_test8_rt1'] = "Connecting to %s at port %s";
$string['info_test9_name'] = "Wiris Quizzes service";
$string['info_test10_name'] = "Checking Wiris Quizzes functionality (variable)";
$string['info_test11_name'] = "Checking Wiris Quizzes functionality (plot)";
$string['info_test12_name'] = "Max server connections";
$string['info_test12_rt1'] = "There are currently %s active concurrent connections out of a maximum of %s. Greatest number of concurrent connections is %s.";
$string['info_test12_rt2'] = "Error with the maximum connections security system. See details: <br/><pre>%s<br/>%s</pre>";
$string['ok'] = 'OK';
$string['proxyurl'] = 'PROXY_URL:';
$string['cachedir'] = 'CACHE_DIR:';
$string['serviceurl'] = 'SERVICE_URL:';
$string['wqsummary'] = 'This adds a generic Wiris Quizzes question. Only for test purpose. It will be hide from here.';
$string['wirisquestionincorrect'] = 'Sorry! The system can not generate one of the questions of the quiz. <br />Maybe there is a temporary connection problem right now. <br />Maybe the question algorithm has a bug, and fails sometimes. <br />Maybe it will fail always. <br />Don\'t panic... <br />You can retry the quiz, without penalty, just clicking Continue. <br />You can also tell the Teachers that there is an issue with the question titled: \'{$a->questionname}\'';
$string['wirisquizzeserror'] = 'Sorry! There was an error in Wiris Quizzes.';
$string['failedtoloadwirisquizzesfromxml'] = 'Failed to load Wiris Quizzes XML definition for question id';
$string['connectionsettings'] = 'Connection settings';
$string['connectionsettings_text'] = '';
$string['quizzesserviceurl'] = 'Wiris Quizzes service URL';
$string['quizzesserviceurl_help'] = 'URL to connect to Wiris Quizzes service.';
$string['quizzeseditorurl'] = 'MathType service URL';
$string['quizzeseditorurl_help'] = 'URL where to load MathType.';
$string['quizzeshandurl'] = 'MathType Hand service URL';
$string['quizzeshandurl_help'] = 'URL where to load the Wiris HAND.';
$string['quizzeswirislauncherurl'] = 'Wiris CAS JNLP URL';
$string['quizzeswirislauncherurl_help'] = 'URL where to get the JNLP file for Wiris CAS app.';
$string['quizzeswirisurl'] = 'Wiris CAS applet service URL';
$string['quizzeswirisurl_help'] = 'URL where to load the Wiris CAS applet.';

$string['privacy:metadata:qtype_wq'] = 'Information about user\'s correct answer for a given Wiris Quizzes question type';
$string['privacy:metadata:qtype_wq:question'] = 'Wiris Quizzes question type id';
$string['privacy:metadata:qtype_wq:xml'] = 'Wiris Quizzes Question XML';

$string['auxiliar_text'] = 'Write an optional reasoning for your answer:';
