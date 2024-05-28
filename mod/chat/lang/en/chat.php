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
 * Strings for component 'chat', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   mod_chat
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['activityoverview'] = 'You have upcoming chat sessions';
$string['ajax'] = 'Version using AJAX';
$string['autoscroll'] = 'Auto scroll';
$string['beep'] = 'Beep';
$string['bubble'] = 'Bubble';
$string['cantlogin'] = 'Could not log in to chat room!!';
$string['composemessage'] = 'Compose a message';
$string['configmethod'] = 'The AJAX chat method provide an AJAX-based chat interface which contacts the server regularly for updates. The normal chat method involves clients regularly contacting the server for updates. It requires no configuration and works everywhere, but can create a large load on the server if many users are chatting.  Using a server daemon requires shell access to Unix, but it results in a fast scalable chat environment.';
$string['confignormalupdatemode'] = 'Chatroom updates are normally served efficiently using the <em>Keep-Alive</em> feature of HTTP 1.1, but this is still quite heavy on the server. A more advanced method is to use the <em>Stream</em> strategy to feed updates to the users. Using <em>Stream</em> scales much better (similar to the chatd method) but may not be supported by your server.';
$string['configoldping'] = 'What is the maximum time that may pass before we detect that a user has disconnected (in seconds)? This is just an upper limit, as usually disconnects are detected very quickly. Lower values will be more demanding on your server. If you are using the normal method, <strong>never</strong> set this lower than 2 * chat_refresh_room.';
$string['configrefreshroom'] = 'How often should the chat room itself be refreshed? (in seconds).  Setting this low will make the chat room seem quicker, but it may place a higher load on your web server when many people are chatting. If you are using <em>Stream</em> updates, you can select higher refresh frequencies -- try with 2.';
$string['configrefreshuserlist'] = 'How often should the list of users be refreshed? (in seconds)';
$string['configserverhost'] = 'The hostname of the computer where the server daemon is';
$string['configserverip'] = 'The numerical IP address that matches the above hostname';
$string['configservermax'] = 'Max number of clients allowed';
$string['configserverport'] = 'Port to use on the server for the daemon';
$string['compact'] = 'Compact';
$string['coursetheme'] = 'Course theme';
$string['crontask'] = 'Background processing for chat module';
$string['currentchats'] = 'Active chat sessions';
$string['currentusers'] = 'Current users';
$string['deletesession'] = 'Delete this session';
$string['deletesessionsure'] = 'Are you sure you want to delete this session?';
$string['donotusechattime'] = 'Don\'t publish any chat times';
$string['enterchat'] = 'Enter the chat';
$string['errornousers'] = 'Could not find any users!';
$string['explaingeneralconfig'] = 'These settings are <strong>always</strong> used';
$string['explainmethoddaemon'] = 'These settings only have an effect if \'Chat server daemon\' is selected as chat method.';
$string['explainmethodnormal'] = 'These settings only have an effect if Normal is selected as chat method.';
$string['generalconfig'] = 'General configuration';
$string['chat:addinstance'] = 'Add a new chat';
$string['chat:deletelog'] = 'Delete chat logs';
$string['chat:exportparticipatedsession'] = 'Export chat session which you took part in';
$string['chat:exportsession'] = 'Export any chat session';
$string['chat:chat'] = 'Access a chat room';
$string['chatintro'] = 'Description';
$string['chatname'] = 'Name of this chat room';
$string['chat:readlog'] = 'View chat logs';
$string['chatreport'] = 'Chat sessions';
$string['chat:talk'] = 'Talk in a chat';
$string['chattime'] = 'Next chat time';
$string['nextchattime'] = 'Next chat time:';
$string['chat:view'] = 'View chat activity';
$string['entermessage'] = "Enter your message";
$string['eventmessagesent'] = 'Message sent';
$string['eventsessionsviewed'] = 'Sessions viewed';
$string['idle'] = 'Idle';
$string['indicator:cognitivedepth'] = 'Chat cognitive';
$string['indicator:cognitivedepth_help'] = 'This indicator is based on the cognitive depth reached by the student in a Chat activity.';
$string['indicator:cognitivedepthdef'] = 'Chat cognitive';
$string['indicator:cognitivedepthdef_help'] = 'The participant has reached this percentage of the cognitive engagement offered by the Chat activities during this analysis interval (Levels = No view, View, Submit, View feedback, Comment on feedback)';
$string['indicator:cognitivedepthdef_link'] = 'Learning_analytics_indicators#Cognitive_depth';
$string['indicator:socialbreadth'] = 'Chat social';
$string['indicator:socialbreadth_help'] = 'This indicator is based on the social breadth reached by the student in a Chat activity.';
$string['indicator:socialbreadthdef'] = 'Chat social';
$string['indicator:socialbreadthdef_help'] = 'The participant has reached this percentage of the social engagement offered by the Chat activities during this analysis interval (Levels = No participation, Participant alone, Participant with others)';
$string['indicator:socialbreadthdef_link'] = 'Learning_analytics_indicators#Social_breadth';
$string['inputarea'] = 'Input area';
$string['invalidid'] = 'Could not find that chat room!';
$string['list_all_sessions'] = 'List all sessions.';
$string['list_complete_sessions'] = 'List just complete sessions.';
$string['listing_all_sessions'] = 'Listing all sessions.';
$string['messagebeepseveryone'] = '{$a} beeps everyone!';
$string['messagebeepsyou'] = '{$a} has just beeped you!';
$string['messageenter'] = '{$a} has just entered this chat';
$string['messageexit'] = '{$a} has left this chat';
$string['messages'] = 'Messages';
$string['messageyoubeep'] = 'You beeped {$a}';
$string['method'] = 'Chat method';
$string['methoddaemon'] = 'Chat server daemon';
$string['methodnormal'] = 'Normal';
$string['methodajax'] = 'AJAX';
$string['modulename'] = 'Chat';
$string['modulename_help'] = 'The chat activity module enables participants to have text-based, real-time synchronous discussions.

The chat may be a one-time activity or it may be repeated at the same time each day or each week. Chat sessions are saved and can be made available for everyone to view or restricted to users with the capability to view chat session logs.

Chats are especially useful when the group chatting is not able to meet face-to-face, such as

* Regular meetings of students participating in online courses to enable them to share experiences with others in the same course but in a different location
* A student temporarily unable to attend in person chatting with their teacher to catch up with work
* Students out on work experience getting together to discuss their experiences with each other and their teacher
* Younger children using chat at home in the evenings as a controlled (monitored) introduction to the world of social networking
* A question and answer session with an invited speaker in a different location
* Sessions to help students prepare for tests where the teacher, or other students, would pose sample questions';
$string['modulename_link'] = 'mod/chat/view';
$string['modulenameplural'] = 'Chats';
$string['neverdeletemessages'] = 'Never delete messages';
$string['no_complete_sessions_found'] = 'No complete sessions found.';
$string['noguests'] = 'The chat is not open to guests';
$string['nochat'] = 'No chat found';
$string['nomessages'] = 'No messages yet';
$string['normalkeepalive'] = 'KeepAlive';
$string['normalstream'] = 'Stream';
$string['noscheduledsession'] = 'No scheduled session';
$string['notallowenter'] = 'You are not allowed to enter the chat room.';
$string['notlogged'] = 'You are not logged in!';
$string['nopermissiontoseethechatlog'] = 'You don\'t have permission to see the chat logs.';
$string['oldping'] = 'Disconnect timeout';
$string['page-mod-chat-x'] = 'Any chat module page';
$string['pastchats'] = 'Past chat sessions';
$string['pastsessions'] = 'Past sessions';
$string['pluginadministration'] = 'Chat administration';
$string['pluginname'] = 'Chat';
$string['privacy:metadata:chat_messages_current'] = 'Current chat session. This data is temporary and is deleted after the chat session is deleted';
$string['privacy:metadata:chat_users'] = 'Keeps track of which users are in which chat rooms';
$string['privacy:metadata:chat_users:firstping'] = 'Time of the first access to chat room';
$string['privacy:metadata:chat_users:ip'] = 'User IP';
$string['privacy:metadata:chat_users:lang'] = 'User language';
$string['privacy:metadata:chat_users:lastmessageping'] = 'Time of the last message in this chat room';
$string['privacy:metadata:chat_users:lastping'] = 'Time of the last access to chat room';
$string['privacy:metadata:chat_users:userid'] = 'The user ID';
$string['privacy:metadata:chat_users:version'] = 'How user accessed the chat (sockets/basic/ajax/header_js)';
$string['privacy:metadata:messages'] = 'A record of the messages sent during a chat session';
$string['privacy:metadata:messages:issystem'] = 'Whether the message is a system-generated message';
$string['privacy:metadata:messages:message'] = 'The message';
$string['privacy:metadata:messages:timestamp'] = 'The time when the message was sent.';
$string['privacy:metadata:messages:userid'] = 'The user ID of the author of the message';
$string['refreshroom'] = 'Refresh room';
$string['refreshuserlist'] = 'Refresh user list';
$string['removemessages'] = 'All messages';
$string['repeatdaily'] = 'At the same time every day';
$string['repeatnone'] = 'No repeats - publish the specified time only';
$string['repeattimes'] = 'Repeat/publish session times';
$string['repeatweekly'] = 'At the same time every week';
$string['saidto'] = 'said to';
$string['savemessages'] = 'Save past sessions';
$string['seesession'] = 'See this session';
$string['search:activity'] = 'Chat - activity information';
$string['send'] = 'Send';
$string['sending'] = 'Sending';
$string['serverhost'] = 'Server name';
$string['serverip'] = 'Server ip';
$string['servermax'] = 'Max users';
$string['serverport'] = 'Server port';
$string['sessions'] = 'Chat sessions';
$string['sessionstartsin'] = 'The next chat session will start {$a} from now.';
$string['strftimemessage'] = '%H:%M';
$string['studentseereports'] = 'Everyone can view past sessions';
$string['studentseereports_help'] = 'If set to No, only users have mod/chat:readlog capability are able to see the chat logs';
$string['talk'] = 'Talk';
$string['updatemethod'] = 'Update method';
$string['updaterate'] = 'Update rate:';
$string['userlist'] = 'User list';
$string['usingchat'] = 'Using chat';
$string['usingchat_help'] = 'The chat module contains some features to make chatting a little nicer.

* Smilies - Any smiley faces (emoticons) that you can type elsewhere in Moodle can also be typed here, for example :-)
* Links - Website addresses will be turned into links automatically
* Emoting - You can start a line with "/me" or ":" to emote, for example if your name is Kim and you type ":laughs!" or "/me laughs!" then everyone will see "Kim laughs!"
* Beeps - You can send a sound to other participants by clicking the "beep" link next to their name. A useful shortcut to beep all the people in the chat at once is to type "beep all".
* HTML - If you know some HTML code, you can use it in your text to do things like insert images, play sounds or create different coloured text';
$string['viewreport'] = 'Past sessions';
